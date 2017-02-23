<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-event
 * @author: n3vrax
 * Date: 9/14/2016
 * Time: 1:13 AM
 */

declare(strict_types = 1);

namespace Dot\Event;

use Dot\Event\Factory\EventManagerAwareInitializer;
use Dot\Event\Factory\EventManagerFactory;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\SharedEventManager;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\ServiceManager\Factory\InvokableFactory;

/**
 * Class ConfigProvider
 * @package Dot\Event
 */
class ConfigProvider
{
    /**
     * @return array
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    /**
     * @return array
     */
    public function getDependencyConfig(): array
    {
        return [
            'factories' => [
                SharedEventManager::class => InvokableFactory::class,
                EventManagerInterface::class => EventManagerFactory::class,
            ],
            'aliases' => [
                SharedEventManagerInterface::class => SharedEventManager::class,
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
