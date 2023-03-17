<?php

namespace App\Repository;

use App\Entity\LoginLinkToken;
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

//    /**
//     * @return LoginLinkToken[] Returns an array of LoginLinkToken objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LoginLinkToken
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
