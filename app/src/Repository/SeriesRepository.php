<?php

namespace App\Repository;

use App\Entity\Series;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method Series|null find($id, $lockMode = null, $lockVersion = null)
 * @method Series|null findOneBy(array $criteria, array $orderBy = null)
 * @method Series[]    findAll()
 * @method Series[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Series::class);
    }

    /**
     * Compare les séries en bdd avec les IDs passés en paramètre et ressort les absents
     * @param array $rankedIds Un tableau d'IDs IMDb
     * @return array
     */
    public function findAbsentees(array $rankedIds): array
    {
        $qb = $this->createQueryBuilder('s');

        return $qb->andWhere($qb->expr()->notIn('s.imdbId', ':rankedIds'))
            ->setParameter('rankedIds', $rankedIds)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Trouve toutes les séries actuellement dans le top 250
     * @return array
     */
    public function findRanked(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere("s.imdbRank IS NOT NULL")
            ->orderBy('s.imdbRank', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Trouve toutes les séries qui ne sont plus dans le top 250
     * @return array
     */
    public function findUnranked(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere("s.imdbRank IS NULL")
            ->orderBy('s.title', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Series[] Returns an array of Series objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Series
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
