<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use App\Service\Encryptors\EncryptedPropertiesAccessor;
use App\Service\Encryptors\EncryptorInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Notification>
 */
class NotificationRepository extends AbstractRepository
{
    public function __construct(
        ManagerRegistry $registry,
        EncryptedPropertiesAccessor $encryptedPropertiesAccessor,
        private readonly EncryptorInterface $encryptor
    ) {
        parent::__construct($registry, Notification::class, $encryptedPropertiesAccessor, $encryptor);
    }

    /**
     * @return Notification[]
     */
    public function findRecentFor(User $user): array
    {
        return $this->findForQueryBuilder($user)
            ->setMaxResults(10)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return Notification[]
     */
    public function findFor(User $user): array
    {
        return $this->findForQueryBuilder($user)
            ->getQuery()
            ->getResult();
    }

    private function findForQueryBuilder(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('n')
            ->where('n.user = :user')
            ->setParameter('user', $user)
            ->orderBy('n.createdAt', 'DESC');
    }
}
