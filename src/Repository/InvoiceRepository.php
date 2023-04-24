<?php

namespace App\Repository;

use App\Entity\Invoice;
use App\Service\Encryptors\EncryptedPropertiesAccessor;
use App\Service\Encryptors\EncryptorInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Invoice>
 *
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends AbstractRepository
{
    public function __construct(
        ManagerRegistry $registry,
        EncryptedPropertiesAccessor $encryptedPropertiesAccessor,
        private readonly EncryptorInterface $encryptor
    ) {
        parent::__construct($registry, Invoice::class, $encryptedPropertiesAccessor, $encryptor);
    }

    /**
     * @return Invoice[]
     */
    public function findByDepositAccount(int $id, bool $onlyActive = false): array
    {
        $qb = $this->findByDepositAccountIdQueryBuilder($id)
            ->orderBy('i.label', 'ASC');

        if ($onlyActive) {
            $qb->andWhere('i.active = true');
        }

        return $qb->getQuery()
            ->getResult();
    }

    private function findByDepositAccountIdQueryBuilder(int $id): QueryBuilder
    {
        return $this->createQueryBuilder('i')
            ->innerJoin('i.depositAccount', 'd')
            ->where('d.id = :id')
            ->setParameter('id', $id);
    }
}
