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

    public function findDepensesRecurrentesDuJour(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
        SELECT d.id AS depense_id, 
               r.id AS recurrence_id, 
               r.frequence, 
               d.date_depense AS date_depense, 
               r.date_debut, 
               r.date_fin, 
               d.montant_depense, 
               d.description_depense, 
               d.categorie_id, 
               d.livret_id
        FROM depense d
        JOIN recurrence r ON d.id = r.depense_id
        WHERE DATE_ADD(d.date_depense, INTERVAL r.frequence DAY) = CURRENT_DATE()
        AND CURRENT_DATE() BETWEEN r.date_debut AND r.date_fin
    ";

        return $conn->executeQuery($sql)->fetchAllAssociative();
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
