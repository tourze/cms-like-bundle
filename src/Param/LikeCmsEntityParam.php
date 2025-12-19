<?php

declare(strict_types=1);

namespace Tourze\CmsLikeBundle\Param;

use Symfony\Component\Validator\Constraints as Assert;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * LikeCmsEntity Procedure 的参数对象
 *
 * 用于点赞/取消点赞指定文章的请求参数
 */
final readonly class LikeCmsEntityParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '文章ID')]
        #[Assert\NotBlank]
        #[Assert\Positive]
        public int $entityId,
    ) {
    }
}
