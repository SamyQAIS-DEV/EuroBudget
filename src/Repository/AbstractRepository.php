<?php

namespace App\Repository;

use App\Entity\User;
use App\Service\Encryptors\EncryptedPropertiesAccessor;
use App\Service\Encryptors\EncryptorInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template T of object
 *
 * @method T|null find($id, $lockMode = null, $lockVersion = null)
 * @method T[]    findAll()
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
        private readonly EncryptedPropertiesAccessor $encryptedPropertiesAccessor,
        private readonly EncryptorInterface $encryptor
    ) {
        parent::__construct($registry, $entityClass);
    }

    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        return $this->findByQuery($criteria)->getResult();
    }

    /**
     * @return T|null
     */
    public function findOneBy(array $criteria, ?array $orderBy = null): ?object
    {
        return $this->findByQuery($criteria)->setMaxResults(1)->getOneOrNullResult();
    }

    public function findByOr(array $criteria): array
    {
        return $this->findByQuery($criteria, 'OR')->getResult();
    }

    /**
     * @return T|null
     */
    public function findOneByOr(array $criteria, ?array $orderBy = null): ?object
    {
        return $this->findByQuery($criteria, 'OR')->setMaxResults(1)->getOneOrNullResult();
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

    private function findByQuery(array $criteria, $conditionType = 'AND'): Query
    {
        $encryptedProperties = $this->encryptedPropertiesAccessor->getProperties(new User());
        $criteriaString = [];
        $parameters = [];
        foreach ($criteria as $field => $value) {
            if (array_key_exists($field, $encryptedProperties) && $value) {
                $value = $this->encryptor->encrypt($value);
            }
            $criteriaString[] = "o.$field = :$field";
            $parameters[$field] = $value;
        }

        return $this->createQueryBuilder('o')
            ->where(implode(' ' . $conditionType . ' ', $criteriaString))
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
