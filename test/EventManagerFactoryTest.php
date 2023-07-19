<?php

declare(strict_types=1);

namespace DotTest\Event;

use Dot\Event\Factory\EventManagerFactory;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\SharedEventManagerInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class EventManagerFactoryTest extends TestCase
{
    private EventManagerFactory $eventManagerFactory;
    private ContainerInterface|MockObject $container;
    private SharedEventManagerInterface|MockObject $sharedEventManager;
    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->eventManagerFactory = new EventManagerFactory();
        $this->sharedEventManager  = $this->createMock(SharedEventManagerInterface::class);
        $this->container           = $this->createMock(ContainerInterface::class);
    }

    public function testInvokeWithSharedEventManager(): void
    {
        $container          = $this->container;
        $sharedEventManager = $this->sharedEventManager;
        $container->expects($this->once())
            ->method('has')
            ->with(SharedEventManagerInterface::class)
            ->willReturn(true);
        $container->expects($this->once())
            ->method('get')
            ->with(SharedEventManagerInterface::class)
            ->willReturn($sharedEventManager);
        $factory      = new EventManagerFactory();
        $eventManager = $factory->__invoke($container);
        $this->assertInstanceOf(EventManager::class, $eventManager);
    }

    public function testInvokeWithoutSharedEventManager(): void
    {
        $container = $this->container;
        $container->expects($this->once())
            ->method('has')
            ->with(SharedEventManagerInterface::class)
            ->willReturn(false);
        $factory      = $this->eventManagerFactory;
        $eventManager = $factory->__invoke($container);
        $this->assertInstanceOf(EventManager::class, $eventManager);
    }
}
