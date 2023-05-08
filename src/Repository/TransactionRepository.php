<?php

namespace App\Repository;

use App\Entity\Transaction;
use App\Entity\User;
use App\Service\Encryptors\EncryptedPropertiesAccessor;
use App\Service\Encryptors\EncryptorInterface;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Transaction>
 */
class TransactionRepository extends AbstractRepository
{
    public function __construct(
        ManagerRegistry $registry,
        EncryptedPropertiesAccessor $encryptedPropertiesAccessor,
        private readonly EncryptorInterface $encryptor
    ) {
        parent::__construct($registry, Transaction::class, $encryptedPropertiesAccessor, $encryptor);
    }

    /**
     * @return Transaction[]
     */
    public function findFor(User $user): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.author = :user')
            ->orderBy('t.createdAt', 'DESC')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function getMonthlyRevenues(): array
    {
        return $this->aggregateRevenus();
    }

    private function aggregateRevenus(): array
    {
        return array_reverse($this->createQueryBuilder('t')
            ->select(
                "EXTRACT(MONTH from t.createdAt) as date",
                "EXTRACT(YEAR_MONTH from t.createdAt) as fulldate",
                'SUM(t.price - t.tax - t.fee) as amount'
            )
            ->groupBy('fulldate', 'date')
//            ->where('t.refunded = false')
            ->orderBy('fulldate', 'DESC')
            ->getQuery()
            ->getResult());
    }

    public function getMonthlyReport(int $year): array
    {
        return $this->createQueryBuilder('t')
            ->select(
                't.method as method',
                'EXTRACT(MONTH FROM t.createdAt) as month',
                'ROUND(SUM(t.price) * 100) / 100 as price',
                'ROUND(SUM(t.tax) * 100) / 100 as tax',
                'ROUND(SUM(t.fee) * 100) / 100 as fee',
            )
            ->groupBy('month', 't.method')
//            ->where('t.refunded = false')
            ->andWhere('EXTRACT(YEAR FROM t.createdAt) = :year')
            ->setParameter('year', $year)
            ->orderBy('month', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
