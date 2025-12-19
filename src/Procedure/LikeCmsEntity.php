<?php

declare(strict_types=1);

namespace Tourze\CmsLikeBundle\Procedure;

use Doctrine\ORM\EntityManagerInterface;
use Tourze\CmsBundle\Service\EntityService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\CmsLikeBundle\Entity\LikeLog;
use Tourze\CmsLikeBundle\Param\LikeCmsEntityParam;
use Tourze\CmsLikeBundle\Repository\LikeLogRepository;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

#[MethodExpose(method: 'LikeCmsEntity')]
#[MethodTag(name: '内容管理')]
#[MethodDoc(summary: '点赞/取消点赞指定文章')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
#[Log]
final class LikeCmsEntity extends LockableProcedure
{
    public function __construct(
        private readonly LikeLogRepository $likeLogRepository,
        private readonly Security $security,
        private readonly EntityManagerInterface $entityManager,
        private readonly EntityService $entityService,
    ) {
    }

    /**
     * @phpstan-param LikeCmsEntityParam $param
     */
    public function execute(LikeCmsEntityParam|RpcParamInterface $param): ArrayResult
    {
        $entity = $this->entityService->findEntityById($param->entityId);
        if (null === $entity) {
            throw new ApiException('找不到文章');
        }

        $user = $this->security->getUser();

        $log = $this->likeLogRepository->findOneBy([
            'entity' => $entity,
            'user' => $user,
        ]);
        if (null === $log) {
            $log = new LikeLog();
            $log->setEntity($entity);
            $log->setUser($user);
        }

        $log->setValid(!($log->isValid() ?? false));
        $this->entityManager->persist($log);
        $this->entityManager->flush();

        return new ArrayResult([
            '__message' => (true === $log->isValid()) ? '已点赞' : '已取消点赞',
        ]);
    }
}
