<?php

declare(strict_types=1);

namespace DotTest\Event\Asset;

use Dot\Event\DotEventListenerInterface;
use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateTrait;

class TestListener implements DotEventListenerInterface
{
    use ListenerAggregateTrait;

    public const EVENT_NAME = 'test.event';

    /**
     * @param int $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(self::EVENT_NAME, [$this, 'onTestEvent'], $priority);
    }

    /**
     * @param EventInterface<object|string|null, array<string, mixed>> $event
     */
    public function onTestEvent(EventInterface $event): void
    {
        $event->setParam('handled', true);
    }
}
