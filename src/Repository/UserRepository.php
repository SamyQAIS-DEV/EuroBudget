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

    public function findOneByRole(string $role): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%' . $role . '%')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
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

    /**
     * @param string $q
     * @return User[]
     */
    public function search(string $q): array
    {
        $search = [];
        foreach (explode(' ', $q) as $v) {
            $parts = str_replace('-', ' ', $v);
            $search[] = strtolower($v);
            $search[] = strtoupper($v);
            $search[] = ucfirst(strtolower($v));
            $search[] = str_replace(' ', '-', ucwords($parts));
            $search[] = $v;
        }
        $search = array_unique($search);

        if (count($search) > 5) {

            return $this->findBy(['firstname' => $search, 'lastname' => $search]);
        }

        return $this->findByOr(['firstname' => $search, 'lastname' => $search, 'email' => $search]);
    }
}
