<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-event
 * @author: n3vrax
 * Date: 9/14/2016
 * Time: 1:13 AM
 */

namespace Dot\Event;

use Dot\Event\Factory\EventManagerAwareInitializer;
use Dot\Event\Factory\EventManagerFactory;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\SharedEventManager;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Class ConfigProvider
 * @package Dot\Event
 */
class ConfigProvider
{
    /**
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    /**
     * @return array
     */
    public function getDependencyConfig()
    {
        return [
            'invokables' => [
                SharedEventManagerInterface::class => SharedEventManager::class,
            ],

            'factories' => [
                EventManagerInterface::class => EventManagerFactory::class,
            ],
            'initializers' => [
                EventManagerAwareInitializer::class,
            ],
            'shared' => [
                EventManagerInterface::class => false,
            ]
        ];
    }
}
