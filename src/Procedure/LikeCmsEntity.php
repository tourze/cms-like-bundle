<?php

namespace Tourze\CmsLikeBundle\Procedure;

use CmsBundle\Entity\Entity;
use CmsBundle\Repository\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\CmsLikeBundle\Entity\LikeLog;
use Tourze\CmsLikeBundle\Repository\LikeLogRepository;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

#[MethodExpose(method: 'LikeCmsEntity')]
#[MethodTag(name: '内容管理')]
#[MethodDoc(summary: '点赞/取消点赞指定文章')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
#[Log]
class LikeCmsEntity extends LockableProcedure
{
    #[MethodParam(description: '文章ID')]
    public int $entityId;

    public function __construct(
        private readonly LikeLogRepository $likeLogRepository,
        private readonly Security $security,
        private readonly EntityManagerInterface $entityManager,
        private readonly EntityRepository $entityRepository,
    ) {
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function getMockResult(): ?array
    {
        return [
            '__message' => 0 === rand(0, 1) ? '已点赞' : '已取消点赞',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $entity = $this->entityRepository->find($this->entityId);
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

        return [
            '__message' => (true === $log->isValid()) ? '已点赞' : '已取消点赞',
        ];
    }
}
