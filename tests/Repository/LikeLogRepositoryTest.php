<?php

declare(strict_types=1);

namespace Tourze\CmsLikeBundle\Tests\Repository;

use BizUserBundle\Entity\BizUser;
use CmsBundle\Entity\Entity;
use CmsBundle\Enum\EntityState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CmsLikeBundle\Entity\LikeLog;
use Tourze\CmsLikeBundle\Repository\LikeLogRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(LikeLogRepository::class)]
#[RunTestsInSeparateProcesses]
final class LikeLogRepositoryTest extends AbstractRepositoryTestCase
{
    protected function createNewEntity(): object
    {
        $likeLog = new LikeLog();
        $likeLog->setValid(true);

        return $likeLog;
    }

    /**
     * @return ServiceEntityRepository<LikeLog>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        /** @var ServiceEntityRepository<LikeLog> */
        return self::getContainer()->get(LikeLogRepository::class);
    }

    protected function onSetUp(): void
    {
        // 设置测试环境
    }

    public function testFindByUserAndEntityWithExistingRecord(): void
    {
        /** @var LikeLogRepository $repository */
        $repository = $this->getRepository();

        // 创建测试用户
        $user = new BizUser();
        $user->setUsername('test_user_' . time());
        $user->setNickName('测试用户');
        $user->setPlainPassword('test_password');
        self::getEntityManager()->persist($user);

        // 创建测试实体
        $entity = new Entity();
        $entity->setTitle('测试实体');
        $entity->setState(EntityState::PUBLISHED);
        self::getEntityManager()->persist($entity);

        // 创建点赞记录
        $likeLog = new LikeLog();
        $likeLog->setUser($user);
        $likeLog->setEntity($entity);
        $likeLog->setValid(true);

        $repository->save($likeLog, true);

        // 测试查找方法
        $result = $repository->findByUserAndEntity($user, $entity);

        $this->assertInstanceOf(LikeLog::class, $result);
        $this->assertSame($user, $result->getUser());
        $this->assertSame($entity, $result->getEntity());
        $this->assertTrue($result->isValid());
    }

    public function testFindByUserAndEntityWithNonExistingRecord(): void
    {
        /** @var LikeLogRepository $repository */
        $repository = $this->getRepository();

        // 创建测试用户
        $user = new BizUser();
        $user->setUsername('test_user_2_' . time());
        $user->setNickName('测试用户2');
        $user->setPlainPassword('test_password');
        self::getEntityManager()->persist($user);

        // 创建测试实体
        $entity = new Entity();
        $entity->setTitle('测试实体2');
        $entity->setState(EntityState::PUBLISHED);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        // 测试查找不存在的记录
        $result = $repository->findByUserAndEntity($user, $entity);

        $this->assertNull($result);
    }

    public function testFindByUserAndEntityWithDifferentUsersSameEntity(): void
    {
        /** @var LikeLogRepository $repository */
        $repository = $this->getRepository();

        // 创建两个不同的测试用户
        $user1 = new BizUser();
        $user1->setUsername('test_user_1_' . time());
        $user1->setNickName('测试用户1');
        $user1->setPlainPassword('test_password');
        self::getEntityManager()->persist($user1);

        $user2 = new BizUser();
        $user2->setUsername('test_user_2_' . time());
        $user2->setNickName('测试用户2');
        $user2->setPlainPassword('test_password');
        self::getEntityManager()->persist($user2);

        // 创建测试实体
        $entity = new Entity();
        $entity->setTitle('测试实体3');
        $entity->setState(EntityState::PUBLISHED);
        self::getEntityManager()->persist($entity);

        // 为user1创建点赞记录
        $likeLog1 = new LikeLog();
        $likeLog1->setUser($user1);
        $likeLog1->setEntity($entity);
        $likeLog1->setValid(true);
        $repository->save($likeLog1, true);

        // 测试user1能找到记录
        $result1 = $repository->findByUserAndEntity($user1, $entity);
        $this->assertInstanceOf(LikeLog::class, $result1);
        $this->assertSame($user1, $result1->getUser());

        // 测试user2找不到记录
        $result2 = $repository->findByUserAndEntity($user2, $entity);
        $this->assertNull($result2);
    }
}
