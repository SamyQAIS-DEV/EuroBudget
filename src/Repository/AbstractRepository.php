<?php

namespace App\Repository;

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

    /**
     * @return T[]
     */
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

    private function findByQuery(array $criteria, string $conditionType = 'AND'): Query
    {
        $encryptedProperties = $this->encryptedPropertiesAccessor->getProperties(new $this->_entityName());
        $criteriaString = [];
        $parameters = [];
        foreach ($criteria as $field => $value) {
            // TODO Refacto that
            if (array_key_exists($field, $encryptedProperties) && $value) {
                $values = [];
                if (is_string($value)) {
                    $value = $this->encryptor->encrypt($value);
                } else {
                    foreach ($value as $v) {
                        $values[] = $this->encryptor->encrypt($v);
                    }
                }
            }
            $criteriaString[] = is_string($value) ? "e.$field = :$field" : "e.$field IN (:$field)";
            $parameters[$field] = is_string($value) ? $value : $values;
        }

        $qb = $this->createQueryBuilder('e');
        if (count($criteriaString) > 0) {
            $qb->where(implode(' ' . $conditionType . ' ', $criteriaString))
                ->setParameters($parameters);
        }

        return $qb->getQuery();
    }

    private function findByCaseInsensitiveQuery(array $conditions): Query
    {
        $conditionString = [];
        $parameters = [];
        foreach ($conditions as $k => $v) {
            $conditionString[] = "LOWER(e.$k) = :$k";
            $parameters[$k] = strtolower((string) $v);
        }

        return $this->createQueryBuilder('e')
            ->where(implode(' AND ', $conditionString))
            ->setParameters($parameters)
            ->getQuery();
    }

    public function save(object $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(object $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
