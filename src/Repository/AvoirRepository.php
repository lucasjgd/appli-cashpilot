<?php

namespace App\Repository;

use App\Entity\Avoir;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Types\Types;

/**
 * @extends ServiceEntityRepository<Avoir>
 */
class AvoirRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avoir::class);
    }

    public function findAvoir($livret, $categorie, \DateTimeInterface $moisAnnee): ?Avoir
    {
        return $this->createQueryBuilder('a')
            ->where('a.livret = :livret')
            ->andWhere('a.categorie = :categorie')
            ->andWhere('a.moisAnnee = :moisAnnee')
            ->setParameter('livret', $livret)
            ->setParameter('categorie', $categorie)
            ->setParameter('moisAnnee', $moisAnnee, Types::DATE_MUTABLE)
            ->getQuery()
            ->getOneOrNullResult();
    }



    //    /**
    //     * @return Avoir[] Returns an array of Avoir objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Avoir
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
