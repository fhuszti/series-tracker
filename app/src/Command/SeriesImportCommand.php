<?php
namespace App\Command;

use App\Entity\Series;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SeriesImportCommand extends Command
{
    const IMDB_URL = "https://imdb-api.com/fr/API/Top250TVs/";

    protected static $defaultName = 'import:series';

    private string $apiKey;

    private EntityManagerInterface $entityManager;

    private HttpClientInterface $http;

    public function __construct(string $apiKey, EntityManagerInterface $entityManager, HttpClientInterface $http)
    {
        $this->apiKey = $apiKey;
        $this->entityManager = $entityManager;
        $this->http = $http;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Importe les séries.')
            ->setHelp("Cette commande permet d'importer les séries de l'API IMDb à la base de données locale.");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            '==================================',
            'Import des séries à partir de IMDb',
            '==================================',
            '',
        ]);

        if (empty($this->apiKey)) {
            $output->writeln("Clé API IMDb manquante. Veuillez l'ajouter à votre .env.local et réessayer.");
            return Command::FAILURE;
        }

        //On commence par récupérer toutes les séries du top 250 sur IMDb
        try {
            $output->writeln("Récupération des données sur l'API IMDb...");
            $response = $this->http->request('GET', self::IMDB_URL.$this->apiKey);

            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                $output->writeln("Échec de l'import des données : statut ".$statusCode);
                return Command::FAILURE;
            }

            $seriesList = [];
            $result = $response->toArray();
            if (isset($result["items"]))
                $seriesList = $result["items"];

            $output->writeln(["...données récupérées avec succès.", ""]);
        } catch (\Exception $e) {
            $output->writeln("Erreur lors de l'import des données : ".$e->getMessage());
            return Command::FAILURE;
        }

        //On va retenir la liste des IDs des séries du top afin de pouvoir la comparer ensuite avec ceux déjà en bdd.
        //Toute série présente en bdd et pas dans le top actuellement importé est une série anciennement top qui ne l'est plus.
        //On lui retirera donc son ranking.
        $doneIds = [];

        $output->writeln("Préparation des entités...");
        $progressBarTop = new ProgressBar($output, count($seriesList));
        $progressBarTop->start();
        //On boucle sur les résultats pour travailler nos données
        foreach ($seriesList as $dataSeries) {
            //Si on a déjà la série en BDD, on va simplement la mettre à jour...
            $newSeries = $this->entityManager->getRepository(Series::class)->findOneBy(["imdbId" => $dataSeries["id"]]);
            //...sinon on la crée
            if (!$newSeries)
                $newSeries = new Series();

            $newSeries->setImdbId($dataSeries["id"]);
            $newSeries->setTitle($dataSeries["title"]);
            $newSeries->setFullTitle($dataSeries["fullTitle"]);
            $newSeries->setImdbRank((int) $dataSeries["rank"]);
            $newSeries->setImdbRating((float) $dataSeries["imDbRating"]);
            $newSeries->setYear((int) $dataSeries["year"] ?? null);
            $newSeries->setImageUrl($dataSeries["image"] ?? null);

            //et on ajoute l'ID de la série traitée à la liste qu'on tient à jour
            $doneIds[] = $dataSeries["id"];

            $this->entityManager->persist($newSeries);
            $progressBarTop->advance();
        }
        $progressBarTop->finish();
        $output->writeln(["", "", "...suppression du classement des séries n'apparaissant plus dans le top 250..."]);

        $seriesOutOfTop = $this->entityManager->getRepository(Series::class)->findAbsentees($doneIds);
        $progressBarOutOfTop = new ProgressBar($output, count($seriesOutOfTop));
        $progressBarOutOfTop->start();
        //On retire toutes les séries concernées du classement
        /** @var Series $seriesList */
        foreach ($seriesOutOfTop as $series) {
            $series->setImdbRank(null);
            $this->entityManager->persist($series);
            $progressBarOutOfTop->advance();
        }
        $progressBarOutOfTop->finish();

        //On enregistre tous nos résultats en bdd
        $output->writeln(["", "", "...enregistrement des données en BDD..."]);
        $this->entityManager->flush();
        $output->writeln(["...toutes les données ont été enregistrées avec succès en BDD.", ""]);

        return Command::SUCCESS;
    }
}
