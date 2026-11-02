<?php

declare(strict_types=1);

namespace Dot\Event\Factory;

use Dot\Event\DotEventListenerInterface;
use Dot\Event\Exception\RuntimeException;
use Laminas\EventManager\EventManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function class_exists;
use function get_debug_type;
use function is_string;
use function sprintf;
use function trigger_error;

use const E_USER_DEPRECATED;

class EventManagerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RuntimeException
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
     * @throws RuntimeException
     */
    private function getListener(ContainerInterface $container, mixed $listener): DotEventListenerInterface
    {
        if (is_string($listener)) {
            if ($container->has($listener)) {
                $listener = $container->get($listener);
            } elseif (class_exists($listener)) {
                trigger_error(
                    sprintf(
                        'Instantiating event listener "%s" without a container entry is deprecated;'
                        . ' register it in the container. This fallback will be removed in the next major version.',
                        $listener
                    ),
                    E_USER_DEPRECATED
                );
                $listener = new $listener();
            }
        }

        if (! $listener instanceof DotEventListenerInterface) {
            throw new RuntimeException(sprintf(
                'Event listener must be an instance of %s, but %s was provided',
                DotEventListenerInterface::class,
                get_debug_type($listener)
            ));
        }

        return $listener;
    }
}
