<?php
/**
 * @copyright: DotKernel
 * @library: dot-event
 * @author: n3vra
 * Date: 1/27/2017
 * Time: 4:03 PM
 */

declare(strict_types = 1);

namespace Dot\Event\Factory;

use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\Initializer\InitializerInterface;

/**
 * Class EventManagerAwareInitializer
 * @package Dot\Event\Factory
 */
class EventManagerAwareInitializer implements InitializerInterface
{
    /**
     * @param ContainerInterface $container
     * @param object $instance
     */
    public function __invoke(ContainerInterface $container, $instance)
    {
        if ($instance instanceof EventManagerAwareInterface) {
            $eventManager = $container->has(EventManagerInterface::class)
                ? $container->get(EventManagerInterface::class)
                : new EventManager();

            $instance->setEventManager($eventManager);
        }
    }
}
