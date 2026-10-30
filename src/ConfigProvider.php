<?php

declare(strict_types=1);

namespace Dot\Event;

use Dot\Event\Factory\EventManagerFactory;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerInterface;

class ConfigProvider
{
    /**
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
            'dot-event'    => [],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
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
