# CMS Like Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue.svg?style=flat-square)](https://php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen.svg?style=flat-square)](#)
[![Code Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen.svg?style=flat-square)](#)

A Symfony bundle that provides like/unlike functionality for CMS entities with logging and event dispatching.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Quick Start](#quick-start)
  - [1. Enable the Bundle](#1-enable-the-bundle)
  - [2. Database Schema](#2-database-schema)
  - [3. Basic Usage](#3-basic-usage)
  - [4. Repository Usage](#4-repository-usage)
- [API Reference](#api-reference)
  - [JSON-RPC Methods](#json-rpc-methods)
- [Configuration](#configuration)
  - [Service Registration](#service-registration)
  - [Event Configuration](#event-configuration)
  - [Database Configuration](#database-configuration)
- [Advanced Usage](#advanced-usage)
  - [Custom Like Logic](#custom-like-logic)
  - [Event Handling](#event-handling)
  - [Cross-Module Integration](#cross-module-integration)
- [Security](#security)
  - [Authentication Requirements](#authentication-requirements)
  - [Authorization Considerations](#authorization-considerations)
  - [Data Protection](#data-protection)
  - [Security Best Practices](#security-best-practices)
- [Events](#events)
- [Database Schema](#database-schema)
- [Requirements](#requirements)
- [License](#license)

## Features

- Like/unlike functionality for CMS entities
- Like log tracking with user and IP information
- JSON-RPC API endpoint for like operations
- Event dispatching for like actions
- Snowflake ID support for like logs
- Timestamp and blame tracking

## Installation

```bash
composer require tourze/cms-like-bundle
```

## Quick Start

### 1. Enable the Bundle

Add the bundle to your `config/bundles.php`:

```php
<?php

return [
    // ...
    Tourze\CmsLikeBundle\CmsLikeBundle::class => ['all' => true],
];
```

### 2. Database Schema

Run the migration to create the like log table:

```bash
php bin/console doctrine:migrations:migrate
```

### 3. Basic Usage

Use the JSON-RPC endpoint to like/unlike entities:

```javascript
// JSON-RPC request
{
    "jsonrpc": "2.0",
    "method": "LikeCmsEntity",
    "params": {
        "entityId": 123
    },
    "id": 1
}

// Response
{
    "jsonrpc": "2.0",
    "result": {
        "__message": "已点赞"
    },
    "id": 1
}
```

### 4. Repository Usage

```php
use Tourze\CmsLikeBundle\Repository\LikeLogRepository;

// Inject the repository
public function __construct(
    private LikeLogRepository $likeLogRepository
) {}

// Find like logs for a specific entity
$logs = $this->likeLogRepository->findBy(['entity' => $entity]);

// Check if user liked an entity
$likeLog = $this->likeLogRepository->findOneBy([
    'entity' => $entity,
    'user' => $user,
    'valid' => true
]);
```

## API Reference

### JSON-RPC Methods

#### LikeCmsEntity

Toggles like/unlike status for a CMS entity.

**Parameters:**
- `entityId` (int): The ID of the entity to like/unlike

**Returns:**
- `__message` (string): Success message indicating the action taken

**Security:** Requires authenticated user

## Configuration

## Service Registration

The bundle automatically registers its services. The key services are:

```yaml
# config/services.yaml (optional configuration)
when@dev:
    services:
        Tourze\CmsLikeBundle\Service\LikeService:
            public: true  # For debugging purposes
```

## Event Configuration

Configure event listeners for like actions:

```yaml
# config/services.yaml
services:
    App\EventListener\LikeEventListener:
        tags:
            - { name: kernel.event_listener, event: Tourze\CmsBundle\Event\LikeEntityEvent }
```

## Database Configuration

The bundle requires the following database configuration:

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

## Advanced Usage

## Custom Like Logic

Extend the LikeService to implement custom logic:

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

## Event Handling

Listen to like events for additional processing:

```php
use Tourze\CmsBundle\Event\LikeEntityEvent;
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
        // Update cached like counts
        // Send notifications
        // Update user statistics
    }
}
```

## Cross-Module Integration

Access other module services through proper service layer:

```php
use Tourze\CmsBundle\Service\EntityService;use Tourze\CmsLikeBundle\Service\LikeService;

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

## Security

## Authentication Requirements

The `LikeCmsEntity` JSON-RPC method requires authenticated users:

```php
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
class LikeCmsEntity extends LockableProcedure
```

## Authorization Considerations

- Only authenticated users can like/unlike content
- User sessions are validated before processing like operations
- IP addresses are logged for audit trails

## Data Protection

- User identifiers are stored securely
- IP addresses are hashed for privacy protection
- Like logs can be anonymized for GDPR compliance

## Security Best Practices

```php
// Implement rate limiting
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;

class LikeCmsEntity extends LockableProcedure
{
    // Automatic locking prevents rapid-fire requests
}

// Validate entity permissions
public function execute(): array
{
    $entity = $this->entityService->findEntityBy([
        'id' => $this->entityId,
        'state' => EntityState::PUBLISHED, // Only published content
    ]);
    
    if (null === $entity) {
        throw new ApiException('找不到文章');
    }
    // ... rest of implementation
}
```

## Events

The bundle dispatches `LikeEntityEvent` when a like action occurs:

```php
use Tourze\CmsBundle\Event\LikeEntityEvent;
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
        // Handle like event
        $entity = $event->getEntity();
        $user = $event->getSender();
        $message = $event->getMessage();
    }
}
```

## Database Schema

The bundle creates a `cms_like_log` table with the following columns:

- `id`: Snowflake ID
- `user_id`: Foreign key to user table
- `entity_id`: Foreign key to CMS entity table
- `valid`: Boolean flag indicating if the like is active
- `create_time`: Timestamp of creation (mapped to `createTime` property)
- `update_time`: Timestamp of last update (mapped to `updateTime` property)
- `created_by`: User who created the record
- `updated_by`: User who last updated the record
- `created_from_ip`: IP address where the record was created
- `updated_from_ip`: IP address where the record was last updated

## Requirements

- PHP 8.1+
- Symfony 6.4+
- Doctrine ORM 3.0+

## License

This bundle is released under the MIT license. See the [LICENSE](LICENSE) file for details.