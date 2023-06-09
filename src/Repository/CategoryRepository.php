<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\DepositAccount;
use App\Service\Encryptors\EncryptedPropertiesAccessor;
use App\Service\Encryptors\EncryptorInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Category>
 */
class CategoryRepository extends AbstractRepository
{
    public function __construct(
        ManagerRegistry $registry,
        EncryptedPropertiesAccessor $encryptedPropertiesAccessor,
        private readonly EncryptorInterface $encryptor
    ) {
        parent::__construct($registry, Category::class, $encryptedPropertiesAccessor, $encryptor);
    }

    /**
     * @return Category[]
     */
    public function findByDepositAccount(DepositAccount $depositAccount): array
    {
        return $this->findByDepositAccountQueryBuilder($depositAccount)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    private function findByDepositAccountQueryBuilder(DepositAccount $depositAccount): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.depositAccount', 'd')
            ->where('d.id = :id')
            ->setParameter('id', $depositAccount->getId());
    }
}
