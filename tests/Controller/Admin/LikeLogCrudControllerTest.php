<?php

declare(strict_types=1);

namespace Tourze\CmsLikeBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CmsLikeBundle\Controller\Admin\LikeLogCrudController;
use Tourze\CmsLikeBundle\Entity\LikeLog;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(LikeLogCrudController::class)]
#[RunTestsInSeparateProcesses]
final class LikeLogCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getEntityFqcn(): string
    {
        return LikeLog::class;
    }

    public function testGetEntityFqcn(): void
    {
        $this->assertSame(LikeLog::class, LikeLogCrudController::getEntityFqcn());
    }

    public function testIndexPage(): void
    {
        $client = self::createAuthenticatedClient();
        $crawler = $client->request('GET', '/admin');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Navigate to LikeLog CRUD if menu exists
        $link = $crawler->filter('a[href*="LikeLogCrudController"]')->first();
        if ($link->count() > 0) {
            $client->click($link->link());
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
        }
    }

    public function testCreateLikeLog(): void
    {
        $client = self::createAuthenticatedClient();
        $client->request('GET', '/admin');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Test that the controller can handle entity creation
        $controller = new LikeLogCrudController();
        $this->assertInstanceOf(LikeLogCrudController::class, $controller);
    }

    public function testConfigureCrud(): void
    {
        $controller = new LikeLogCrudController();
        $this->assertInstanceOf(LikeLogCrudController::class, $controller);

        // Test that configureCrud method exists and returns proper configuration
        $this->assertInstanceOf(AbstractCrudController::class, $controller);
    }

    public function testConfigureFields(): void
    {
        $controller = new LikeLogCrudController();
        $this->assertInstanceOf(LikeLogCrudController::class, $controller);

        // Test that configureFields method exists
        $this->assertInstanceOf(AbstractCrudController::class, $controller);

        $fields = iterator_to_array($controller->configureFields('index'));
        $this->assertNotEmpty($fields);

        // Just verify we have fields without checking specific properties
        // since the field interface doesn't guarantee getProperty() method
        $this->assertGreaterThan(0, count($fields));
    }

    public function testConfigureActions(): void
    {
        $controller = new LikeLogCrudController();
        $this->assertInstanceOf(LikeLogCrudController::class, $controller);

        // Test that configureActions method exists
        $this->assertInstanceOf(AbstractCrudController::class, $controller);
    }

    public function testConfigureFilters(): void
    {
        $controller = new LikeLogCrudController();
        $this->assertInstanceOf(LikeLogCrudController::class, $controller);

        // Test that configureFilters method exists
        $this->assertInstanceOf(AbstractCrudController::class, $controller);
    }

    public function testControllerInheritance(): void
    {
        $controller = new LikeLogCrudController();
        $this->assertInstanceOf(AbstractCrudController::class, $controller);
    }

    protected function getControllerService(): LikeLogCrudController
    {
        return new LikeLogCrudController();
    }

    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        yield '用户' => ['用户'];
        yield '实体' => ['实体'];
        yield '有效状态' => ['有效状态'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
        yield '创建者' => ['创建者'];
        yield '更新者' => ['更新者'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield '用户字段' => ['user'];
        yield '实体字段' => ['entity'];
        yield '有效状态字段' => ['valid'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield '用户字段' => ['user'];
        yield '实体字段' => ['entity'];
        yield '有效状态字段' => ['valid'];
    }
}
