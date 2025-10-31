<?php

declare(strict_types=1);

namespace Tourze\CmsLikeBundle\Service;

use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\CmsLikeBundle\Entity\LikeLog;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

/**
 * 点赞管理后台菜单提供者
 */
#[Autoconfigure(public: true)]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('内容管理')) {
            $item->addChild('内容管理');
        }

        $contentMenu = $item->getChild('内容管理');
        if (null === $contentMenu) {
            return;
        }

        // 添加点赞记录管理子菜单
        $contentMenu->addChild('点赞记录')
            ->setUri($this->linkGenerator->getCurdListPage(LikeLog::class))
            ->setAttribute('icon', 'fas fa-heart')
        ;
    }
}
