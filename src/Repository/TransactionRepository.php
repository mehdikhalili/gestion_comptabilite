<?php

namespace App\Repository;

use App\Entity\BankAccount;
use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\AST\BetweenExpression;
use Doctrine\ORM\Query\Parameter;
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
    public function findByDate(string $date_du, string $date_au): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.date >= :date_du')
            ->andWhere('t.date <= :date_au')
            ->setParameter('date_du', $date_du)
            ->setParameter('date_au', $date_au)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Transaction[] Returns an array of Transaction objects
     */
    public function findByBankAccount(BankAccount $bankAccount): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.bankAccount = :bankAccount')
            ->setParameter('bankAccount', $bankAccount)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        /* $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT t.*
                FROM `transaction` t, bank_account b
                WHERE t.bank_account_id = b.id
                AND t.bank_account_id = ?
                ORDER BY t.created_at DESC";
        $conn->prepare($sql);
        $result = $conn->executeQuery($sql, [$bankAccount]);
        return $result->fetchAllAssociative(); */
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
