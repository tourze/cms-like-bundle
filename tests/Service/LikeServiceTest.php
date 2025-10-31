<?php

namespace Tourze\CmsLikeBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CmsLikeBundle\Entity\LikeLog;
use Tourze\CmsLikeBundle\Service\LikeService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(LikeService::class)]
#[RunTestsInSeparateProcesses]
final class LikeServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 空实现，测试不需要数据库
    }

    public function testServiceCanBeInstantiated(): void
    {
        // 从容器中获取服务
        $service = self::getService(LikeService::class);
        $this->assertInstanceOf(LikeService::class, $service);
    }

    public function testServiceHasCorrectDependencies(): void
    {
        // 从容器中获取服务
        $service = self::getService(LikeService::class);

        $reflection = new \ReflectionClass($service);

        // 检查是否有必要的属性
        $this->assertTrue($reflection->hasProperty('likeLogRepository'));
    }

    public function testFindLikeLogByMethodSignature(): void
    {
        // 从容器中获取服务
        $service = self::getService(LikeService::class);

        // 获取方法反射信息
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('findLikeLogBy');

        // 验证方法签名
        $this->assertEquals('findLikeLogBy', $method->getName());
        $this->assertTrue($method->isPublic());
        $this->assertEquals(1, $method->getNumberOfParameters());

        // 验证参数类型
        $parameters = $method->getParameters();
        $this->assertEquals('criteria', $parameters[0]->getName());
        $this->assertTrue($parameters[0]->getType() instanceof \ReflectionNamedType);
        $this->assertEquals('array', $parameters[0]->getType()->getName());

        // 验证返回类型
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertEquals(LikeLog::class, $returnType->getName());
        $this->assertTrue($returnType->allowsNull());
    }

    public function testFindLikeLogsByMethodSignature(): void
    {
        // 从容器中获取服务
        $service = self::getService(LikeService::class);

        // 获取方法反射信息
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('findLikeLogsBy');

        // 验证方法签名
        $this->assertEquals('findLikeLogsBy', $method->getName());
        $this->assertTrue($method->isPublic());
        $this->assertEquals(2, $method->getNumberOfParameters());

        // 验证参数类型
        $parameters = $method->getParameters();
        $this->assertEquals('criteria', $parameters[0]->getName());
        $this->assertTrue($parameters[0]->getType() instanceof \ReflectionNamedType);
        $this->assertEquals('array', $parameters[0]->getType()->getName());

        $this->assertEquals('orderBy', $parameters[1]->getName());
        $this->assertTrue($parameters[1]->getType() instanceof \ReflectionNamedType);
        $this->assertEquals('array', $parameters[1]->getType()->getName());
        $this->assertTrue($parameters[1]->isOptional());
        $this->assertTrue($parameters[1]->isDefaultValueAvailable());
        $this->assertNull($parameters[1]->getDefaultValue());

        // 验证返回类型
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertEquals('array', $returnType->getName());
    }

    public function testCountLikeLogsMethodSignature(): void
    {
        // 从容器中获取服务
        $service = self::getService(LikeService::class);

        // 获取方法反射信息
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('countLikeLogs');

        // 验证方法签名
        $this->assertEquals('countLikeLogs', $method->getName());
        $this->assertTrue($method->isPublic());
        $this->assertEquals(1, $method->getNumberOfParameters());

        // 验证参数类型
        $parameters = $method->getParameters();
        $this->assertEquals('criteria', $parameters[0]->getName());
        $this->assertTrue($parameters[0]->getType() instanceof \ReflectionNamedType);
        $this->assertEquals('array', $parameters[0]->getType()->getName());

        // 验证返回类型
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertEquals('int', $returnType->getName());
    }

    public function testAllPublicMethodsExistAndAreCallable(): void
    {
        // 从容器中获取服务
        $service = self::getService(LikeService::class);

        // 验证所有公共方法都存在且可调用
        $this->assertTrue(method_exists($service, 'findLikeLogBy'));
        $this->assertTrue(method_exists($service, 'findLikeLogsBy'));
        $this->assertTrue(method_exists($service, 'countLikeLogs'));
    }
}
