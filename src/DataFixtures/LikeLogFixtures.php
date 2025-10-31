<?php

namespace Tourze\CmsLikeBundle\DataFixtures;

use CmsBundle\Entity\Entity;
use CmsBundle\Enum\EntityState;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Tourze\CmsLikeBundle\Entity\LikeLog;

class LikeLogFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建测试数据
        $testEntity = new Entity();
        $testEntity->setTitle('Test Entity');
        $testEntity->setState(EntityState::PUBLISHED);
        $manager->persist($testEntity);

        $likeLog = new LikeLog();
        $likeLog->setValid(true);
        $likeLog->setEntity($testEntity);

        $manager->persist($likeLog);
        $manager->flush();
    }
}
