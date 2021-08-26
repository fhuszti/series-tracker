<?php
namespace App\Command;

use App\Entity\Series;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
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

    /**
     * @throws TransportExceptionInterface
     */
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

        //On commence par récupérer toutes les séries dispo sur IMDb
        try {
            $output->writeln("Récupération des données sur l'API IMDb...");
            $response = $this->http->request('GET', self::IMDB_URL.$this->apiKey);

            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                $output->writeln("Échec de l'import des données : statut ".$statusCode);
                return Command::FAILURE;
            }

            $series = [];
            $result = $response->toArray();
            if (isset($result["items"]))
                $series = $result["items"];

            $output->writeln(["...données récupérées avec succès.", ""]);
        } catch (\Exception $e) {
            $output->writeln("Erreur lors de l'import des données : ".$e->getMessage());
            return Command::FAILURE;
        }

        $output->writeln("Préparation des entités...");
        $progressBar = new ProgressBar($output, count($series));
        $progressBar->start();
        //On boucle sur les résultats pour travailler nos données
        foreach ($series as $dataSeries) {
            //Si on a déjà la série en BDD, on va simplement la mettre à jour...
            $newSeries = $this->entityManager->getRepository(Series::class)->findOneBy(["imdbId" => $dataSeries["id"]]);
            //...sinon on la crée
            if (!$newSeries)
                $newSeries = new Series();

            $newSeries->setImdbId($dataSeries["id"]);
            $newSeries->setTitle($dataSeries["title"]);
            $newSeries->setFullTitle($dataSeries["fullTitle"]);
            $newSeries->setImdbRank($dataSeries["rank"]);
            $newSeries->setImdbRating($dataSeries["imDbRating"]);
            $newSeries->setYear($dataSeries["year"] ?? null);
            $newSeries->setImageUrl($dataSeries["image"] ?? null);

            $this->entityManager->persist($newSeries);
            $progressBar->advance();
        }
        $progressBar->finish();
        $output->writeln(["", "...enregistrement des données en BDD..."]);

        //On enregistre tous nos résultats en bdd
        $this->entityManager->flush();
        $output->writeln(["...toutes les données ont été enregistrées avec succès en BDD.", ""]);

        return Command::SUCCESS;
    }
}
