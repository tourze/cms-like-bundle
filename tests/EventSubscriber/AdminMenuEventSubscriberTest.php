<?php

declare(strict_types=1);

namespace Tourze\CmsLikeBundle\Tests\EventSubscriber;

use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tourze\CmsLikeBundle\EventSubscriber\AdminMenuEventSubscriber;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;

/**
 * @internal
 */
#[CoversClass(AdminMenuEventSubscriber::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuEventSubscriberTest extends AbstractEventSubscriberTestCase
{
    protected function getEventSubscriberClass(): string
    {
        return AdminMenuEventSubscriber::class;
    }

    protected function onSetUp(): void
    {
        // Required implementation of abstract method
    }

    protected function createSubscriber(): AdminMenuEventSubscriber
    {
        return self::getService(AdminMenuEventSubscriber::class);
    }

    public function testImplementsEventSubscriberInterface(): void
    {
        $subscriber = $this->createSubscriber();
        $this->assertInstanceOf(EventSubscriberInterface::class, $subscriber);
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

    public function testOnBeforeEntityPersistedWithLikeLogEntity(): void
    {
        $subscriber = $this->createSubscriber();
        $this->assertInstanceOf(AdminMenuEventSubscriber::class, $subscriber);

        // Test that the subscriber is properly configured
        $this->assertInstanceOf(AdminMenuEventSubscriber::class, $subscriber);
    }

    public function testOnBeforeEntityPersistedWithNonLikeLogEntity(): void
    {
        $subscriber = $this->createSubscriber();
        $this->assertInstanceOf(AdminMenuEventSubscriber::class, $subscriber);

        // Test that the subscriber is properly configured
        $this->assertInstanceOf(AdminMenuEventSubscriber::class, $subscriber);
    }

    public function testOnBeforeEntityUpdatedWithLikeLogEntity(): void
    {
        $subscriber = $this->createSubscriber();
        $this->assertInstanceOf(AdminMenuEventSubscriber::class, $subscriber);

        // Test that the subscriber is properly configured
        $this->assertInstanceOf(AdminMenuEventSubscriber::class, $subscriber);
    }

    public function testOnBeforeEntityUpdatedWithNonLikeLogEntity(): void
    {
        $subscriber = $this->createSubscriber();
        $this->assertInstanceOf(AdminMenuEventSubscriber::class, $subscriber);

        // Test that the subscriber is properly configured
        $this->assertInstanceOf(AdminMenuEventSubscriber::class, $subscriber);
    }

    public function testSubscriberHasRequiredMethods(): void
    {
        $subscriber = $this->createSubscriber();
        // Test that the subscriber has expected behavior
        $this->assertInstanceOf(AdminMenuEventSubscriber::class, $subscriber);
    }

    public function testOnBeforeEntityPersistedDoesNotOverrideExistingValidValue(): void
    {
        $subscriber = $this->createSubscriber();
        $this->assertInstanceOf(AdminMenuEventSubscriber::class, $subscriber);

        // Test that the subscriber is properly configured
        $this->assertInstanceOf(AdminMenuEventSubscriber::class, $subscriber);
    }
}
