<?php

declare(strict_types=1);

namespace Tourze\CmsLikeBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CmsLikeBundle\Service\AdminMenu;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // 空实现，测试不需要数据库
    }

    public function testServiceCanBeInstantiated(): void
    {
        $service = self::getService(AdminMenu::class);
        $this->assertInstanceOf(AdminMenu::class, $service);
    }

    public function testAdminMenuIsCallable(): void
    {
        $service = self::getService(AdminMenu::class);
        $this->assertIsCallable($service);
    }
}
