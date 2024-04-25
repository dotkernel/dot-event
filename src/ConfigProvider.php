<?php

declare(strict_types=1);

namespace Dot\Event;

use Dot\Event\Factory\EventManagerFactory;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerInterface;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
            'dot-event'    => [],
        ];
    }

    public function getDependencyConfig(): array
    {
        return [
            'factories' => [
                EventManager::class => EventManagerFactory::class,
            ],
            'aliases'   => [
                EventManagerInterface::class => EventManager::class,
            ],
        ];
    }
}
