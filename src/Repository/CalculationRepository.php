<?php

namespace App\Repository;

use App\Entity\Calculation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Calculation>
 */
class CalculationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Calculation::class);
    }

    public function getTop4(bool $excluded): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            SELECT c.*
            FROM calculation c, jsonb_array_elements(c.schedule::jsonb) AS schedule_item
            WHERE c.excluded = :val
            GROUP BY c.id
            ORDER BY SUM((schedule_item->>'interest')::numeric) DESC
            LIMIT 4
        ";

        $stmt = $conn->executeQuery($sql, ['val' => (int)$excluded]);

        return $stmt->fetchAllAssociative();
    }

    //    /**
    //     * @return Calculation[] Returns an array of Calculation objects
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

    //    public function findOneBySomeField($value): ?Calculation
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
