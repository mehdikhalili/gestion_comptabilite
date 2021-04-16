<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function findByLibelle(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT libelle, SUM(debit) AS debit, SUM(credit) AS credit
                FROM `transaction`
                GROUP BY libelle
                ORDER BY libelle ASC";
        $result = $conn->executeQuery($sql);
        return $result->fetchAllAssociative();
    }

    /**
     * @return Transaction[] Returns an array of Transaction objects
     */
    public function findByLastAddedLimited()
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByLastAdded()
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Transaction[] Returns an array of Transaction objects
     */
    public function findByDate(string $date): array
    {
        /* $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT *
                FROM `transaction`
                WHERE DATE(created_at) = :val";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['val' => $date]);

        return $stmt->fetchAllAssociative(); */

        return $this->createQueryBuilder('t')
            ->where('DATE(t.createdAt) = :date')
            ->setParameter('date', $date)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Transaction[] Returns an array of Transaction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Transaction
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
