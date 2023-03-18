<?php

namespace App\Repository;

use App\Entity\LoginLinkToken;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LoginLinkToken>
 *
 * @method LoginLinkToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method LoginLinkToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method LoginLinkToken[]    findAll()
 * @method LoginLinkToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoginLinkTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoginLinkToken::class);
    }

    public function cleanByUser(User $user): ?LoginLinkToken
    {
        $query = $this->createQueryBuilder('l')
            ->where('l.user = :user')
            ->setParameter('user', $user);

        $loginLink = $query->getQuery()->getOneOrNullResult();
        $query->delete(LoginLinkToken::class, 'l')->getQuery()->execute();

        return $loginLink;
    }

    public function getByUser(User $user): ?LoginLinkToken
    {
        return $this->createQueryBuilder('l')
            ->where('l.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function save(LoginLinkToken $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LoginLinkToken $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
