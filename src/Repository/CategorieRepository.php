<?php

namespace App\Repository;

use App\Entity\Categorie;
use App\Entity\Avoir;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Categorie>
 */
class CategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorie::class);
    }

    public function categOrdreAsc(): array
    {
        return $this->createQueryBuilder('c')
        ->orderBy('c.libelle', 'ASC')
        ->getQuery()
        ->getResult();
    }

    public function categDansLivret(int $id): array
    {
        $em = $this->getEntityManager();

        $subQb = $em->createQueryBuilder()
            ->select('IDENTITY(a.categorie)')
            ->from('App\Entity\Avoir', 'a')
            ->where('a.livret = :livretId');

        $qb = $this->createQueryBuilder('c')
            ->where('c.id IN (' . $subQb->getDQL() . ')')
            ->setParameter('livretId', $id)
            ->orderBy('c.libelle', 'ASC');

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Categorie[] Returns an array of Categorie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Categorie
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
