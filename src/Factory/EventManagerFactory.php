<?php
/**
 * @see https://github.com/dotkernel/dot-event/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-event/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Event\Factory;

use Psr\Container\ContainerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Class EventManagerFactory
 * @package Dot\Event\Factory
 */
class EventManagerFactory
{
    /**
     * @param ContainerInterface $container
     * @return EventManager
     */
    public function __invoke(ContainerInterface $container)
    {
        $sharedEventManager = $container->has(SharedEventManagerInterface::class)
            ? $container->get(SharedEventManagerInterface::class)
            : null;

        return new EventManager($sharedEventManager);
    }
}
