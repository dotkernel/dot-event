<?php

declare(strict_types=1);

namespace Dot\Event\Factory;

use Laminas\EventManager\EventManager;
use Laminas\EventManager\SharedEventManagerInterface;
use Psr\Container\ContainerInterface;

class EventManagerFactory
{
    public function __invoke(ContainerInterface $container): EventManager
    {
        $sharedEventManager = $container->has(SharedEventManagerInterface::class)
            ? $container->get(SharedEventManagerInterface::class)
            : null;

        return new EventManager($sharedEventManager);
    }
}
