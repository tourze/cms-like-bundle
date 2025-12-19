<?php

declare(strict_types=1);

namespace Tourze\CmsLikeBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CmsLikeBundle\CmsLikeBundle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(CmsLikeBundle::class)]
#[RunTestsInSeparateProcesses]
final class CmsLikeBundleTest extends AbstractBundleTestCase
{
}
