<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-event
 * @author: n3vrax
 * Date: 9/14/2016
 * Time: 1:13 AM
 */

namespace DotKernel\DotEvent;


use DotKernel\DotEvent\Factory\EventManagerFactory;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\SharedEventManager;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Class ConfigProvider
 * @package DotKernel\DotEvent
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

            'shared' => [
                EventManagerInterface::class => false,
            ]
        ];
    }
}