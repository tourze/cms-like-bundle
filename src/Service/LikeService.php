<?php

namespace Tourze\CmsLikeBundle\Service;

use Tourze\CmsLikeBundle\Entity\LikeLog;
use Tourze\CmsLikeBundle\Repository\LikeLogRepository;

/**
 * 点赞服务类
 *
 * 为其他模块提供点赞查询功能，避免直接调用 Repository
 */
readonly class LikeService
{
    public function __construct(
        private LikeLogRepository $likeLogRepository,
    ) {
    }

    /**
     * 通过条件查找点赞记录
     *
     * @param array<string, mixed> $criteria
     */
    public function findLikeLogBy(array $criteria): ?LikeLog
    {
        return $this->likeLogRepository->findOneBy($criteria);
    }

    /**
     * 查找多个点赞记录
     *
     * @param array<string, mixed>                           $criteria
     * @param array<string, 'ASC'|'asc'|'DESC'|'desc'>|null $orderBy
     *
     * @return LikeLog[]
     */
    public function findLikeLogsBy(array $criteria, ?array $orderBy = null): array
    {
        return $this->likeLogRepository->findBy($criteria, $orderBy);
    }

    /**
     * 统计点赞数量
     *
     * @param array<string, mixed> $criteria
     */
    public function countLikeLogs(array $criteria): int
    {
        return $this->likeLogRepository->count($criteria);
    }
}
