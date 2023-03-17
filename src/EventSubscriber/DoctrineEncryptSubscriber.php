<?php

namespace App\EventSubscriber;

use App\Service\Encryptors\EntityEncryptor;
use Doctrine\Common\Proxy\Proxy;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;

class DoctrineEncryptSubscriber implements EventSubscriber
{
    public function __construct(private readonly EntityEncryptor $entityEncryptor)
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postLoad,
            Events::prePersist,
            Events::preUpdate,
            Events::postUpdate,
            Events::preFlush,
            Events::postFlush,
        ];
    }

    public function postLoad(PostLoadEventArgs $args)
    {
        $this->entityEncryptor->decryptAllEncryptedAttribute($args->getObject());
    }

    public function prePersist(PrePersistEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof Proxy && !$entity->__getInitializer()) {
            return;
        }
        $this->entityEncryptor->encryptAllEncryptedAttribute($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getObject();
        $this->entityEncryptor->encryptAllEncryptedAttribute($entity);
    }

    public function postUpdate(PostUpdateEventArgs $args)
    {
        $this->entityEncryptor->decryptAllEncryptedAttribute($args->getObject());
    }

    public function preFlush(PreFlushEventArgs $preFlushEventArgs)
    {
        $unitOfWork = $preFlushEventArgs->getObjectManager()->getUnitOfWork();
        foreach ($unitOfWork->getIdentityMap() as $className => $entities) {
            $class = $preFlushEventArgs->getObjectManager()->getClassMetadata($className);
            if ($class->isReadOnly) {
                continue;
            }

            foreach ($entities as $entity) {
                if (null === $entity || ($entity instanceof Proxy && !$entity->__getInitializer())) {
                    continue;
                }
                $this->entityEncryptor->encryptAllEncryptedAttribute($entity);
            }
        }
    }

    public function postFlush(PostFlushEventArgs $postFlushEventArgs)
    {
        $unitOfWork = $postFlushEventArgs->getObjectManager()->getUnitOfWork();
        foreach ($unitOfWork->getIdentityMap() as $entityMap) {
            foreach ($entityMap as $entity) {
                if (null !== $entity) {
                    $this->entityEncryptor->decryptAllEncryptedAttribute($entity);
                }
            }
        }
    }
}
