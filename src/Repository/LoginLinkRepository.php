<?php

namespace App\Repository;

use App\Entity\LoginLink;
use App\Entity\User;
use App\Service\Encryptors\EncryptedPropertiesAccessor;
use App\Service\Encryptors\EncryptorInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<LoginLink>
 *
 * @method LoginLink|null find($id, $lockMode = null, $lockVersion = null)
 * @method LoginLink|null findOneBy(array $criteria, array $orderBy = null)
 * @method LoginLink[]    findAll()
 * @method LoginLink[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoginLinkRepository extends AbstractRepository
{
    public function __construct(
        ManagerRegistry $registry,
        EncryptedPropertiesAccessor $encryptedPropertiesAccessor,
        private readonly EncryptorInterface $encryptor
    ) {
        parent::__construct($registry, LoginLink::class, $encryptedPropertiesAccessor, $encryptor);
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
