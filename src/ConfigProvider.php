<?php
/**
 * @see https://github.com/dotkernel/dot-event/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-event/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Event;

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
            'shared' => [
                EventManagerInterface::class => false,
            ]
        ];
    }
}
