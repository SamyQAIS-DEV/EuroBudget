<?php

namespace App\Repository;

use App\Entity\DepositAccount;
use App\Entity\Operation;
use App\Entity\User;
use App\Service\Encryptors\EncryptedPropertiesAccessor;
use App\Service\Encryptors\EncryptorInterface;
use DateTimeImmutable;
use Doctrine\ORM\AbstractQuery;
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

    public function findLabelsFor(DepositAccount $depositAccount) {
        return $this->findLabelsForQueryBuilder($depositAccount)
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_SCALAR_COLUMN);
    }

    public function countFor(User $user): int
    {
        return $this->findByUserQueryBuilder($user)
            ->select('COUNT(d.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findForRecap(DepositAccount $depositAccount): array
    {
        return $this->findByDepositAccountQueryBuilder($depositAccount)
            ->select("COUNT(o.id) as waitingOperationsNb, SUM (CASE WHEN o.type = '-' THEN -o.amount ELSE o.amount END) as waitingAmount")
            ->andWhere('o.past = false')
            ->getQuery()
            ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
            ->getOneOrNullResult();
    }

    /**
     * @return Operation[]
     */
    public function findForYearAndMonth(DepositAccount $depositAccount, int $year, int $month): array
    {
        $start = new DateTimeImmutable("01-{$month}-{$year}");
        $end = $start->modify('+1 month');

        return $this->findByDepositAccountQueryBuilder($depositAccount)
            ->andWhere('o.date >= :start AND o.date < :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('o.date', 'DESC')
            ->addOrderBy('o.label', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findYearsMonths(DepositAccount $depositAccount): array
    {
        $rows = $this->findByDepositAccountQueryBuilder($depositAccount)
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

    public function countForYearAndMonth(DepositAccount $depositAccount, int $year, int $month): int
    {
        return $this->countForYearAndMonthQueryBuilder($depositAccount, $year, $month)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countFromInvoicesForYearAndMonth(DepositAccount $depositAccount, int $year, int $month): int
    {
        return $this->countForYearAndMonthQueryBuilder($depositAccount, $year, $month)
            ->andWhere('o.invoice IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function findLabelsForQueryBuilder(DepositAccount $depositAccount): QueryBuilder
    {
        return $this->findByDepositAccountQueryBuilder($depositAccount)
            ->select('DISTINCT o.label')
            ->orderBy('o.label', 'ASC');
    }

    private function countForYearAndMonthQueryBuilder(DepositAccount $depositAccount, int $year, int $month): QueryBuilder
    {
        $start = new DateTimeImmutable("01-{$month}-{$year}");
        $end = $start->modify('+1 month');

        return $this->findByDepositAccountQueryBuilder($depositAccount)
            ->select('COUNT(o.id)')
            ->andWhere('o.date >= :start AND o.date < :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end);
    }

    private function findByDepositAccountQueryBuilder(DepositAccount $depositAccount): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.depositAccount', 'd')
            ->where('d.id = :id')
            ->setParameter('id', $depositAccount->getId());
    }

    private function findByUserQueryBuilder(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.depositAccount', 'd')
            ->leftJoin('d.users', 'u')
            ->where('u.id = :id')
            ->setParameter('id', $user->getId())
            ->orderBy('d.title', 'DESC');
    }
}
