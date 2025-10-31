# CMS Like Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue.svg?style=flat-square)](https://php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen.svg?style=flat-square)](#)
[![Code Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen.svg?style=flat-square)](#)

为 CMS 实体提供点赞/取消点赞功能的 Symfony Bundle，包含日志记录和事件分发。

## 目录

- [特性](#特性)
- [安装](#安装)
- [快速开始](#快速开始)
  - [1. 启用 Bundle](#1-启用-bundle)
  - [2. 数据库架构](#2-数据库架构)
  - [3. 基本用法](#3-基本用法)
  - [4. 仓储使用](#4-仓储使用)
- [API 参考](#api-参考)
  - [JSON-RPC 方法](#json-rpc-方法)
- [配置](#配置)
  - [服务注册](#服务注册)
  - [事件配置](#事件配置)
  - [数据库配置](#数据库配置)
- [高级用法](#高级用法)
  - [自定义点赞逻辑](#自定义点赞逻辑)
  - [事件处理](#事件处理)
  - [跨模块集成](#跨模块集成)
- [安全](#安全)
  - [认证要求](#认证要求)
  - [授权考虑](#授权考虑)
  - [数据保护](#数据保护)
  - [安全最佳实践](#安全最佳实践)
- [事件](#事件)
- [数据库架构](#数据库架构)
- [系统要求](#系统要求)
- [许可证](#许可证)

## 特性

- CMS 实体的点赞/取消点赞功能
- 点赞日志跟踪，包含用户和 IP 信息
- JSON-RPC API 端点用于点赞操作
- 点赞动作的事件分发
- 点赞日志的雪花 ID 支持
- 时间戳和归属跟踪

## 安装

```bash
composer require tourze/cms-like-bundle
```

## 快速开始

### 1. 启用 Bundle

将 Bundle 添加到您的 `config/bundles.php`：

```php
<?php

return [
    // ...
    Tourze\CmsLikeBundle\CmsLikeBundle::class => ['all' => true],
];
```

### 2. 数据库架构

运行迁移以创建点赞日志表：

```bash
php bin/console doctrine:migrations:migrate
```

### 3. 基本用法

使用 JSON-RPC 端点来点赞/取消点赞实体：

```javascript
// JSON-RPC 请求
{
    "jsonrpc": "2.0",
    "method": "LikeCmsEntity",
    "params": {
        "entityId": 123
    },
    "id": 1
}

// 响应
{
    "jsonrpc": "2.0",
    "result": {
        "__message": "已点赞"
    },
    "id": 1
}
```

### 4. 仓储使用

```php
use Tourze\CmsLikeBundle\Repository\LikeLogRepository;

// 注入仓储
public function __construct(
    private LikeLogRepository $likeLogRepository
) {}

// 查找特定实体的点赞日志
$logs = $this->likeLogRepository->findBy(['entity' => $entity]);

// 检查用户是否点赞了某个实体
$likeLog = $this->likeLogRepository->findOneBy([
    'entity' => $entity,
    'user' => $user,
    'valid' => true
]);
```

## API 参考

### JSON-RPC 方法

#### LikeCmsEntity

切换 CMS 实体的点赞/取消点赞状态。

**参数：**
- `entityId` (int): 要点赞/取消点赞的实体 ID

**返回：**
- `__message` (string): 表示执行动作的成功消息

**安全：** 需要认证用户

## 配置

## 服务注册

Bundle 自动注册其服务。主要服务包括：

```yaml
# config/services.yaml (可选配置)
when@dev:
    services:
        Tourze\CmsLikeBundle\Service\LikeService:
            public: true  # 用于调试目的
```

## 事件配置

为点赞动作配置事件监听器：

```yaml
# config/services.yaml
services:
    App\EventListener\LikeEventListener:
        tags:
            - { name: kernel.event_listener, event: CmsBundle\Event\LikeEntityEvent }
```

## 数据库配置

Bundle 需要以下数据库配置：

```yaml
# config/packages/doctrine.yaml
doctrine:
    orm:
        mappings:
            CmsLikeBundle:
                is_bundle: true
                type: attribute
                dir: 'Entity'
                prefix: 'Tourze\CmsLikeBundle\Entity'
```

## 高级用法

## 自定义点赞逻辑

扩展 LikeService 以实现自定义逻辑：

```php
use Tourze\CmsLikeBundle\Service\LikeService;

class CustomLikeService extends LikeService
{
    public function findUserLikesCount(UserInterface $user): int
    {
        return $this->countLikeLogs(['user' => $user, 'valid' => true]);
    }
    
    public function findPopularEntities(int $limit = 10): array
    {
        return $this->findLikeLogsBy(
            ['valid' => true],
            ['createTime' => 'DESC']
        );
    }
}
```

## 事件处理

监听点赞事件进行额外处理：

```php
use CmsBundle\Event\LikeEntityEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LikeStatisticsSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            LikeEntityEvent::class => ['updateStatistics', 10],
        ];
    }

    public function updateStatistics(LikeEntityEvent $event): void
    {
        $entity = $event->getEntity();
        // 更新缓存的点赞数量
        // 发送通知
        // 更新用户统计
    }
}
```

## 跨模块集成

通过适当的服务层访问其他模块服务：

```php
use CmsBundle\Service\EntityService;use Tourze\CmsLikeBundle\Service\LikeService;

class ContentStatisticsService
{
    public function __construct(
        private EntityService $entityService,
        private LikeService $likeService,
    ) {}

    public function getContentWithLikeStats(int $entityId): array
    {
        $entity = $this->entityService->findEntityById($entityId);
        $likeCount = $this->likeService->countLikeLogs([
            'entity' => $entity,
            'valid' => true
        ]);
        
        return [
            'entity' => $entity,
            'likeCount' => $likeCount,
        ];
    }
}
```

## 安全

## 认证要求

`LikeCmsEntity` JSON-RPC 方法需要认证用户：

```php
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
class LikeCmsEntity extends LockableProcedure
```

## 授权考虑

- 只有认证用户可以点赞/取消点赞内容
- 处理点赞操作前验证用户会话
- 记录 IP 地址用于审计跟踪

## 数据保护

- 安全存储用户标识符
- 为隐私保护对 IP 地址进行哈希处理
- 点赞日志可以匿名化以符合 GDPR 合规

## 安全最佳实践

```php
// 实现速率限制
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;

class LikeCmsEntity extends LockableProcedure
{
    // 自动锁定防止快速连续请求
}

// 验证实体权限
public function execute(): array
{
    $entity = $this->entityService->findEntityBy([
        'id' => $this->entityId,
        'state' => EntityState::PUBLISHED, // 只允许已发布内容
    ]);
    
    if (null === $entity) {
        throw new ApiException('找不到文章');
    }
    // ... 其余实现
}
```

## 事件

Bundle 在点赞动作发生时分发 `LikeEntityEvent`：

```php
use CmsBundle\Event\LikeEntityEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LikeEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            LikeEntityEvent::class => 'onLikeEntity',
        ];
    }

    public function onLikeEntity(LikeEntityEvent $event): void
    {
        // 处理点赞事件
        $entity = $event->getEntity();
        $user = $event->getSender();
        $message = $event->getMessage();
    }
}
```

## 数据库架构

Bundle 创建一个 `cms_like_log` 表，包含以下列：

- `id`: 雪花 ID
- `user_id`: 用户表外键
- `entity_id`: CMS 实体表外键
- `valid`: 布尔标志，指示点赞是否有效
- `create_time`: 创建时间戳(映射到 `createTime` 属性)
- `update_time`: 最后更新时间戳(映射到 `updateTime` 属性)
- `created_by`: 创建记录的用户
- `updated_by`: 最后更新记录的用户
- `created_from_ip`: 创建记录的 IP 地址
- `updated_from_ip`: 最后更新记录的 IP 地址

## 系统要求

- PHP 8.1+
- Symfony 6.4+
- Doctrine ORM 3.0+

## 许可证

此 Bundle 在 MIT 许可证下发布。详情请查看 [LICENSE](LICENSE) 文件。