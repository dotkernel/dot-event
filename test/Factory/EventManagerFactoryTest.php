<?php

declare(strict_types=1);

namespace DotTest\Event\Factory;

use Dot\Event\Factory\EventManagerFactory;
use Laminas\EventManager\EventManager;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class EventManagerFactoryTest extends TestCase
{
    private EventManagerFactory $eventManagerFactory;
    private ContainerInterface|MockObject $container;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->eventManagerFactory = new EventManagerFactory();
        $this->container           = $this->createMock(ContainerInterface::class);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testInvokeEventManager(): void
    {
        $container = $this->container;

        $container->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn([]);
        $factory      = new EventManagerFactory();
        $eventManager = $factory($container);
        $this->assertInstanceOf(EventManager::class, $eventManager);
    }
}
