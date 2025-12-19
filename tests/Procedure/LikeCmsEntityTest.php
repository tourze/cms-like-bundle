<?php

namespace Tourze\CmsLikeBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CmsLikeBundle\Procedure\LikeCmsEntity;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(LikeCmsEntity::class)]
#[RunTestsInSeparateProcesses]
final class LikeCmsEntityTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
        // 不需要额外的设置
    }

    public function testExecuteMethodExists(): void
    {
        $reflection = new \ReflectionClass(LikeCmsEntity::class);
        $this->assertTrue($reflection->hasMethod('execute'));
        $this->assertTrue($reflection->getMethod('execute')->isPublic());
    }
}
