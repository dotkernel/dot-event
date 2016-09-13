<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-event
 * @author: n3vrax
 * Date: 9/14/2016
 * Time: 1:12 AM
 */

namespace DotKernel\DotEvent\Factory;


use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Class EventManagerFactory
 * @package DotKernel\DotEvent\Factory
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