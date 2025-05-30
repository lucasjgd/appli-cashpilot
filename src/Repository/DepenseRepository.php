<?php

namespace App\Repository;

use App\Entity\Depense;
use App\Entity\Recurrence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Depense>
 */
class DepenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Depense::class);
    }

    public function depensesDuMois(\DateTime $dateDebut, \DateTime $dateFin): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.dateDepense >= :debut')
            ->andWhere('d.dateDepense < :fin')
            ->setParameter('debut', $dateDebut)
            ->setParameter('fin', $dateFin)
            ->getQuery()
            ->getResult();
    }

    public function getSommeParCategorieParLivret(int $livretId): array
    {
        return $this->createQueryBuilder('d')
            ->select('c.libelle AS categorie, SUM(d.montantDepense) AS total')
            ->join('d.categorie', 'c')
            ->where('d.livret = :livretId')
            ->setParameter('livretId', $livretId)
            ->groupBy('c.libelle')
            ->getQuery()
            ->getResult();
    }


    //    /**
    //     * @return Depense[] Returns an array of Depense objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Depense
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
