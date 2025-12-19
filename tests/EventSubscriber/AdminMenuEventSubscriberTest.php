<?php

declare(strict_types=1);

namespace Tourze\CmsLikeBundle\Tests\EventSubscriber;

use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tourze\CmsBundle\Entity\Entity;
use Tourze\CmsLikeBundle\Entity\LikeLog;
use Tourze\CmsLikeBundle\EventSubscriber\AdminMenuEventSubscriber;

/**
 * @internal
 */
#[CoversClass(AdminMenuEventSubscriber::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuEventSubscriberTest extends AbstractEventSubscriberTestCase
{
    private AdminMenuEventSubscriber $subscriber;
    private EventDispatcher $eventDispatcher;

    protected function onSetUp(): void
    {
        $this->subscriber = self::getService(AdminMenuEventSubscriber::class);
        $this->eventDispatcher = new EventDispatcher();
        $this->eventDispatcher->addSubscriber($this->subscriber);
    }

    public function testImplementsEventSubscriberInterface(): void
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->subscriber);
    }

    public function testGetSubscribedEvents(): void
    {
        $subscribedEvents = AdminMenuEventSubscriber::getSubscribedEvents();

        $this->assertIsArray($subscribedEvents);
        $this->assertArrayHasKey(BeforeEntityPersistedEvent::class, $subscribedEvents);
        $this->assertArrayHasKey(BeforeEntityUpdatedEvent::class, $subscribedEvents);

        $this->assertEquals('onBeforeEntityPersisted', $subscribedEvents[BeforeEntityPersistedEvent::class]);
        $this->assertEquals('onBeforeEntityUpdated', $subscribedEvents[BeforeEntityUpdatedEvent::class]);
    }

    public function testSubscriberHasRequiredMethods(): void
    {
        $this->assertTrue(method_exists($this->subscriber, 'onBeforeEntityPersisted'));
        $this->assertTrue(method_exists($this->subscriber, 'onBeforeEntityUpdated'));
    }

    public function testOnBeforeEntityPersistedWithLikeLog(): void
    {
        // Create a new LikeLog with default valid field (should be false)
        $likeLog = new LikeLog();
        $this->assertFalse($likeLog->isValid());

        // Set valid to null to test the event subscriber logic
        $likeLog->setValid(null);
        $this->assertNull($likeLog->isValid());

        // Create and dispatch the event
        $event = new BeforeEntityPersistedEvent($likeLog);
        $this->eventDispatcher->dispatch($event);

        // Verify that valid field is set to true
        $this->assertTrue($likeLog->isValid());
    }

    public function testOnBeforeEntityPersistedWithLikeLogWithExistingValidValue(): void
    {
        // Create a new LikeLog with existing valid value
        $likeLog = new LikeLog();
        $likeLog->setValid(false);
        $this->assertFalse($likeLog->isValid());

        // Create and dispatch the event
        $event = new BeforeEntityPersistedEvent($likeLog);
        $this->eventDispatcher->dispatch($event);

        // Verify that existing valid value is not changed
        $this->assertFalse($likeLog->isValid());
    }

    public function testOnBeforeEntityPersistedWithOtherEntity(): void
    {
        // Create a different entity
        $entity = new Entity();

        // Create and dispatch the event
        $event = new BeforeEntityPersistedEvent($entity);
        $this->eventDispatcher->dispatch($event);

        // Should not cause any errors, entity should remain unchanged
        $this->assertInstanceOf(Entity::class, $entity);
    }

    public function testOnBeforeEntityUpdatedWithLikeLog(): void
    {
        // Create a LikeLog entity
        $likeLog = new LikeLog();
        $likeLog->setValid(true);

        // Create and dispatch the event
        $event = new BeforeEntityUpdatedEvent($likeLog);
        $this->eventDispatcher->dispatch($event);

        // Should not cause any errors, method currently just returns
        $this->assertInstanceOf(LikeLog::class, $likeLog);
        $this->assertTrue($likeLog->isValid());
    }

    public function testOnBeforeEntityUpdatedWithOtherEntity(): void
    {
        // Create a different entity
        $entity = new Entity();

        // Create and dispatch the event
        $event = new BeforeEntityUpdatedEvent($entity);
        $this->eventDispatcher->dispatch($event);

        // Should not cause any errors, entity should remain unchanged
        $this->assertInstanceOf(Entity::class, $entity);
    }
}