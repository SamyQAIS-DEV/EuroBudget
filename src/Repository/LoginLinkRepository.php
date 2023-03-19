<?php

namespace App\Repository;

use App\Entity\LoginLink;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LoginLink>
 *
 * @method LoginLink|null find($id, $lockMode = null, $lockVersion = null)
 * @method LoginLink|null findOneBy(array $criteria, array $orderBy = null)
 * @method LoginLink[]    findAll()
 * @method LoginLink[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoginLinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoginLink::class);
    }

    public function cleanFor(User $user): ?LoginLink
    {
        $query = $this->createQueryBuilder('l')
            ->where('l.user = :user')
            ->setParameter('user', $user);

        $loginLink = $query->getQuery()->getOneOrNullResult();
        $query->delete(LoginLink::class, 'l')->getQuery()->execute();

        return $loginLink;
    }
}
