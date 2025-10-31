<?php

declare(strict_types=1);

namespace CmsBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

/**
 * 测试专用的Dashboard控制器
 */
#[AdminDashboard(routePath: '/test-admin', routeName: 'test_admin')]
class TestDashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return new Response('Test Dashboard');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Test Dashboard')
        ;
    }

    public function configureMenuItems(): iterable
    {
        return [];
    }
}
