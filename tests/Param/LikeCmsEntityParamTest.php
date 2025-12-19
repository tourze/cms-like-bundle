<?php

declare(strict_types=1);

namespace Tourze\CmsLikeBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Tourze\CmsLikeBundle\Param\LikeCmsEntityParam;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * LikeCmsEntityParam 单元测试
 *
 * @internal
 */
#[CoversClass(LikeCmsEntityParam::class)]
final class LikeCmsEntityParamTest extends TestCase
{
    public function testImplementsRpcParamInterface(): void
    {
        $param = new LikeCmsEntityParam(entityId: 123);

        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testConstructorWithValidParameter(): void
    {
        $param = new LikeCmsEntityParam(entityId: 456);

        $this->assertSame(456, $param->entityId);
    }

    public function testClassIsReadonly(): void
    {
        $reflection = new \ReflectionClass(LikeCmsEntityParam::class);

        $this->assertTrue($reflection->isReadOnly());
    }

    public function testClassIsFinal(): void
    {
        $reflection = new \ReflectionClass(LikeCmsEntityParam::class);

        $this->assertTrue($reflection->isFinal());
    }

    public function testPropertiesArePublicReadonly(): void
    {
        $reflection = new \ReflectionClass(LikeCmsEntityParam::class);

        $properties = ['entityId'];

        foreach ($properties as $propertyName) {
            $property = $reflection->getProperty($propertyName);
            $this->assertTrue($property->isPublic(), "{$propertyName} should be public");
            $this->assertTrue($property->isReadOnly(), "{$propertyName} should be readonly");
        }
    }

    public function testValidationFailsWhenEntityIdIsBlank(): void
    {
        $param = new LikeCmsEntityParam(entityId: 0);

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
        ;

        $violations = $validator->validate($param);

        $this->assertGreaterThan(0, count($violations));
        $this->assertSame('entityId', $violations->get(0)->getPropertyPath());
    }

    public function testValidationFailsWhenEntityIdIsNotPositive(): void
    {
        $param = new LikeCmsEntityParam(entityId: -1);

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
        ;

        $violations = $validator->validate($param);

        $this->assertGreaterThan(0, count($violations));
        $propertyPaths = array_map(fn ($v) => $v->getPropertyPath(), iterator_to_array($violations));
        $this->assertContains('entityId', $propertyPaths);
    }

    public function testValidationPassesWithValidEntityId(): void
    {
        $param = new LikeCmsEntityParam(entityId: 789);

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
        ;

        $violations = $validator->validate($param);

        $this->assertCount(0, $violations);
    }

    public function testHasMethodParamAttributes(): void
    {
        $reflection = new \ReflectionClass(LikeCmsEntityParam::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);

        $parameters = $constructor->getParameters();
        foreach ($parameters as $parameter) {
            $attrs = $parameter->getAttributes(MethodParam::class);
            $this->assertNotEmpty($attrs, "Parameter {$parameter->getName()} should have MethodParam attribute");
        }
    }

    public function testMethodParamAttributeDescription(): void
    {
        $reflection = new \ReflectionClass(LikeCmsEntityParam::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);

        $parameters = $constructor->getParameters();
        $entityIdParam = null;
        foreach ($parameters as $param) {
            if ('entityId' === $param->getName()) {
                $entityIdParam = $param;
                break;
            }
        }
        $this->assertNotNull($entityIdParam, 'Parameter entityId should exist');
        $methodParamAttr = $entityIdParam->getAttributes(MethodParam::class)[0]->newInstance();

        $this->assertSame('文章ID', $methodParamAttr->description);
    }
}
