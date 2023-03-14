<?php

namespace App\Repository;

use App\Entity\User;
use App\Service\Encryptors\EncryptorInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template T of object
 *
 * @method T|null find($id, $lockMode = null, $lockVersion = null)
 * @method T|null findOneBy(array $criteria, array $orderBy = null)
 * @method T[]    findAll()
 * @method T[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
abstract class AbstractRepository extends ServiceEntityRepository
{
    /**
     * @param class-string<T> $entityClass
     *
     * @psalm-param class-string<T> $entityClass
     */
    public function __construct(
        ManagerRegistry $registry,
        string $entityClass,
        private readonly EncryptorInterface $encryptor
    ) {
        parent::__construct($registry, $entityClass);
    }

    public function findEncryptedProperty(array $conditions): array
    {
        return $this->findEncryptedPropertyQuery($conditions)->getResult();
    }

    /**
     * @return T|null
     */
    public function findOneEncryptedProperty(array $conditions): ?object
    {
        return $this->findEncryptedPropertyQuery($conditions)->setMaxResults(1)->getOneOrNullResult();
    }

    public function findByCaseInsensitive(array $conditions): array
    {
        return $this->findByCaseInsensitiveQuery($conditions)->getResult();
    }

    /**
     * @return T|null
     */
    public function findOneByCaseInsensitive(array $conditions): ?object
    {
        return $this->findByCaseInsensitiveQuery($conditions)->setMaxResults(1)->getOneOrNullResult();
    }

    private function findEncryptedPropertyQuery(array $conditions): Query
    {
        $conditionString = [];
        $parameters = [];
        foreach ($conditions as $key => $value) {
            $conditionString[] = "o.$key = :$key";
            $parameters[$key] = $this->encryptor->encrypt((string) $value);
        }

        return $this->createQueryBuilder('o')
            ->where(implode(' AND ', $conditionString))
            ->setParameters($parameters)
            ->getQuery();
    }

    private function findByCaseInsensitiveQuery(array $conditions): Query
    {
        $conditionString = [];
        $parameters = [];
        foreach ($conditions as $k => $v) {
            $conditionString[] = "LOWER(o.$k) = :$k";
            $parameters[$k] = strtolower((string) $v);
        }

        return $this->createQueryBuilder('o')
            ->where(implode(' AND ', $conditionString))
            ->setParameters($parameters)
            ->getQuery();
    }
}
