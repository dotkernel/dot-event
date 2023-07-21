<?php

declare(strict_types=1);

namespace Dot\Event;

use Dot\Event\Factory\EventManagerFactory;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\SharedEventManager;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\ServiceManager\Factory\InvokableFactory;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    public function getDependencyConfig(): array
    {
        return [
            'factories' => [
                SharedEventManager::class    => InvokableFactory::class,
                EventManagerInterface::class => EventManagerFactory::class,
            ],
            'aliases'   => [
                SharedEventManagerInterface::class => SharedEventManager::class,
            ],
            'shared'    => [
                EventManagerInterface::class => false,
            ],
        ];
    }
}
