<?php

namespace App\Repository;

use App\Entity\DepositAccount;
use App\Entity\User;
use App\Service\Encryptors\EncryptedPropertiesAccessor;
use App\Service\Encryptors\EncryptorInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<DepositAccount>
 *
 * @method DepositAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method DepositAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method DepositAccount[]    findAll()
 * @method DepositAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepositAccountRepository extends AbstractRepository
{
    public function __construct(
        ManagerRegistry $registry,
        EncryptedPropertiesAccessor $encryptedPropertiesAccessor,
        private readonly EncryptorInterface $encryptor
    ) {
        parent::__construct($registry, DepositAccount::class, $encryptedPropertiesAccessor, $encryptor);
    }

    public function countFor(User $user): int
    {
        return $this->findForQueryBuilder($user)
            ->select('COUNT(d.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return DepositAccount[]
     */
    public function findFor(User $user): array
    {
        return $this->findForQueryBuilder($user)
            ->getQuery()
            ->getResult();
    }

    private function findForQueryBuilder(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('d')
            ->leftJoin('d.users', 'u')
            ->where('u.id = :id')
            ->setParameter('id', $user->getId())
            ->orderBy('d.title', 'DESC');
    }
}
