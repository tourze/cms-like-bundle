<?php

namespace Tourze\CmsLikeBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\CmsLikeBundle\Entity\LikeLog;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(LikeLog::class)]
final class LikeLogTest extends AbstractEntityTestCase
{
    protected function createEntity(): LikeLog
    {
        return new LikeLog();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'valid_true' => ['valid', true];
        yield 'valid_false' => ['valid', false];
    }

    public function testEntityCreation(): void
    {
        $entity = $this->createEntity();
        $this->assertInstanceOf(LikeLog::class, $entity);
    }

    public function testValidProperty(): void
    {
        $entity = $this->createEntity();
        $entity->setValid(true);
        $this->assertTrue($entity->isValid());
    }

    public function testToString(): void
    {
        $log = $this->createEntity();
        $this->assertEquals('', $log->__toString());
    }
}
