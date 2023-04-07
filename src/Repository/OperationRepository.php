<?php

namespace App\Repository;

use App\Entity\DepositAccount;
use App\Entity\Operation;
use App\Service\Encryptors\EncryptedPropertiesAccessor;
use App\Service\Encryptors\EncryptorInterface;
use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Operation>
 *
 * @method Operation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Operation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Operation[]    findAll()
 * @method Operation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OperationRepository extends AbstractRepository
{
    public function __construct(
        ManagerRegistry $registry,
        EncryptedPropertiesAccessor $encryptedPropertiesAccessor,
        private readonly EncryptorInterface $encryptor
    ) {
        parent::__construct($registry, Operation::class, $encryptedPropertiesAccessor, $encryptor);
    }

    /**
     * @param int $id
     * @return array{waitingOperationsNb: int, waitingAmount: float}
     * @throws NonUniqueResultException
     */
    public function findForRecap(int $id): array
    {
        return $this->findByDepositAccountIdQueryBuilder($id)
            ->select('COUNT(o.id) as waitingOperationsNb, SUM(o.amount) as waitingAmount')
            ->andWhere('o.past = false')
            ->getQuery()
            ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
            ->getOneOrNullResult();
    }

    /**
     * @return Operation[]
     */
    public function findForYearAndMonth(int $id, int $year, int $month): array
    {
        $start = new DateTimeImmutable("01-{$month}-{$year}");
        $end = $start->modify('+1 month');

        return $this->findByDepositAccountIdQueryBuilder($id)
            ->andWhere('o.date >= :start AND o.date < :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('o.date', 'DESC')
            ->addOrderBy('o.label', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findYearsMonths(int $id): array
    {
        $rows = $this->findByDepositAccountIdQueryBuilder($id)
            ->select('EXTRACT(MONTH FROM o.date) as month, EXTRACT(YEAR FROM o.date) as year, COUNT(o.id) as count')
            ->groupBy('month', 'year')
            ->orderBy('year', 'DESC')
            ->addOrderBy('month', 'DESC')
            ->getQuery()
            ->getResult();

        return array_map(fn (array $row) => [
            'path' => $row['year'] . '/' . str_pad((string) $row['month'], 2, '0', STR_PAD_LEFT),
            'count' => $row['count'],
        ], $rows);
    }

    public function countForYearAndMonth(int $id, int $year, int $month): int
    {
        $start = new DateTimeImmutable("01-{$month}-{$year}");
        $end = $start->modify('+1 month');

        return $this->findByDepositAccountIdQueryBuilder($id)
            ->select('COUNT(o.id)')
            ->andWhere('o.date >= :start AND o.date < :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function findByDepositAccountIdQueryBuilder(int $id): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.depositAccount', 'd')
            ->where('d.id = :id')
            ->setParameter('id', $id);
    }

    private function findByUserIdQueryBuilder(int $id): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.depositAccount', 'd')
            ->where('d.id = :id')
            ->setParameter('id', $id);
    }
}
