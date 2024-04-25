<?php

declare(strict_types=1);

namespace Dot\Event\Factory;

use Dot\Event\DotEventListenerInterface;
use Laminas\EventManager\EventManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

use function class_exists;
use function gettype;
use function is_object;
use function is_string;
use function sprintf;

class EventManagerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): EventManager
    {
        $eventManager = new EventManager();
        $config       = $container->get('config')['dot-event'] ?? [];

        foreach ($config as $listenerAggregate) {
            $listener = $this->getListener($container, $listenerAggregate);
            $listener->attach($eventManager);
        }

        return $eventManager;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getListener(
        ContainerInterface $container,
        string $listenerName
    ): DotEventListenerInterface {
        $listener = $listenerName;
        if ($container->has($listener)) {
            $listener = $container->get($listener);
        }

        if (is_string($listener) && class_exists($listener)) {
            $listener = new $listener();
        }

        if (! $listener instanceof DotEventListenerInterface) {
            throw new RuntimeException(sprintf(
                'Event listener must be an instance of %s, but %s was provided',
                DotEventListenerInterface::class,
                is_object($listener) ? $listener::class : gettype($listener)
            ));
        }

        return $listener;
    }
}
