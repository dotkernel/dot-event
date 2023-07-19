<?php

declare(strict_types=1);

namespace DotTest\Event;

use Dot\Event\Event;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    public function testEventCreation(): void
    {
        $event = new Event('testEvent', $this);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function testEventPropagation(): void
    {
        $event = new Event('testEvent', $this);
        // By default, events should be propagating
        $this->assertFalse($event->propagationIsStopped());
        // Test that you can stop event propagation
        $event->stopPropagation(true);
        $this->assertTrue($event->propagationIsStopped());
    }
}
