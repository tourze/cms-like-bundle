<?php

declare(strict_types=1);

namespace Tourze\CmsLikeBundle\Repository;

use CmsBundle\Entity\Entity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\CmsLikeBundle\Entity\LikeLog;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<LikeLog>
 */
#[AsRepository(entityClass: LikeLog::class)]
class LikeLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LikeLog::class);
    }

    /**
     * 查找用户对特定实体的点赞记录
     */
    public function findByUserAndEntity(UserInterface $user, Entity $entity): ?LikeLog
    {
        return $this->findOneBy([
            'user' => $user,
            'entity' => $entity,
        ]);
    }

    public function save(LikeLog $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LikeLog $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
