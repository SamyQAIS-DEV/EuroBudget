<?php

namespace App\Repository;

use App\Entity\User;
use App\Service\Encryptors\EncryptedPropertiesAccessor;
use App\Service\Encryptors\EncryptorInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<User>
 */
class UserRepository extends AbstractRepository
{
    public function __construct(
        ManagerRegistry $registry,
        EncryptedPropertiesAccessor $encryptedPropertiesAccessor,
        private readonly EncryptorInterface $encryptor
    ) {
        parent::__construct($registry, User::class, $encryptedPropertiesAccessor, $encryptor);
    }

    public function findOneByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * Requête permettant de récupérer un utilisateur pour le login.
     */
    public function findForAuth(string $email): ?User
    {
        return $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->setMaxResults(1)
            ->setParameter('email', $this->encryptor->encrypt($email))
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Cherche un utilisateur pour l'oauth.
     */
    public function findForOauth(string $service, ?string $serviceId, ?string $email): ?User
    {
        if (null === $serviceId || null === $email) {
            return null;
        }

        return $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->orWhere("u.{$service}Id = :serviceId")
            ->setMaxResults(1)
            ->setParameters([
                'email' => $this->encryptor->encrypt($email),
                'serviceId' => $serviceId,
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
