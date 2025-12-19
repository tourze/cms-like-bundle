<?php

declare(strict_types=1);

namespace Tourze\CmsLikeBundle\EventSubscriber;

use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tourze\CmsLikeBundle\Entity\LikeLog;

final class AdminMenuEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => 'onBeforeEntityPersisted',
            BeforeEntityUpdatedEvent::class => 'onBeforeEntityUpdated',
        ];
    }

    /**
     * @param BeforeEntityPersistedEvent<LikeLog> $event
     */
    public function onBeforeEntityPersisted(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof LikeLog)) {
            return;
        }

        // 在创建时设置默认值
        if (null === $entity->isValid()) {
            $entity->setValid(true);
        }
    }

    /**
     * @param BeforeEntityUpdatedEvent<LikeLog> $event
     */
    public function onBeforeEntityUpdated(BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof LikeLog)) {
            return;
        }

        // 在更新时的逻辑处理
        // 可以添加额外的业务逻辑
    }
}
