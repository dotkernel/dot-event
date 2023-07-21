<?php

declare(strict_types=1);

namespace Dot\Event\Factory;

use Laminas\EventManager\EventManager;
use Laminas\EventManager\SharedEventManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class EventManagerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): EventManager
    {
        $sharedEventManager = $container->has(SharedEventManagerInterface::class)
            ? $container->get(SharedEventManagerInterface::class)
            : null;

        return new EventManager($sharedEventManager);
    }
}
