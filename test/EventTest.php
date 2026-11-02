<?php

declare(strict_types=1);

namespace DotTest\Event;

use Dot\Event\Event;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    public function testEventCreation(): void
    {
        $params = ['user' => 'dotkernel'];
        $event  = new Event('testEvent', $this, $params);

        $this->assertSame('testEvent', $event->getName());
        $this->assertSame($this, $event->getTarget());
        $this->assertSame($params, $event->getParams());
        $this->assertSame('dotkernel', $event->getParam('user'));
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
