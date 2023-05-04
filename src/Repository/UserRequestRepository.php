<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserRequest;
use App\Service\Encryptors\EncryptedPropertiesAccessor;
use App\Service\Encryptors\EncryptorInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<UserRequest>
 */
class UserRequestRepository extends AbstractRepository
{
    public function __construct(
        ManagerRegistry $registry,
        EncryptedPropertiesAccessor $encryptedPropertiesAccessor,
        private readonly EncryptorInterface $encryptor
    ) {
        parent::__construct($registry, UserRequest::class, $encryptedPropertiesAccessor, $encryptor);
    }

    /**
     * @return UserRequest[]
     */
    public function findFor(User $user): array
    {
        return $this->findForQueryBuilder($user)
            ->getQuery()
            ->getResult();
    }

    private function findForQueryBuilder(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('ur')
            ->where('ur.target = :user')
            ->setParameter('user', $user)
            ->orderBy('ur.createdAt', 'DESC');
    }
}