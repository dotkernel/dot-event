<?php

declare(strict_types=1);

namespace DotTest\Event\Factory;

use Dot\Event\DotEventListenerInterface;
use Dot\Event\Event;
use Dot\Event\Exception\RuntimeException;
use Dot\Event\Factory\EventManagerFactory;
use DotTest\Event\Asset\TestListener;
use Laminas\EventManager\EventManager;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use stdClass;

use function restore_error_handler;
use function set_error_handler;
use function sprintf;

use const E_USER_DEPRECATED;

class EventManagerFactoryTest extends TestCase
{
    private EventManagerFactory $eventManagerFactory;
    private ContainerInterface&MockObject $container;

    /**
     * @throws Exception
     */
    protected function setUp(): void
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
        $this->container->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn([]);

        $eventManager = ($this->eventManagerFactory)($this->container);

        $this->assertSame(EventManager::class, $eventManager::class);
    }

    public function testMissingDotEventConfigKey(): void
    {
        $this->container->method('get')
            ->with('config')
            ->willReturn(['other' => [TestListener::class]]);

        $eventManager = ($this->eventManagerFactory)($this->container);
        $event        = new Event(TestListener::EVENT_NAME);
        $eventManager->triggerEvent($event);

        $this->assertNull($event->getParam('handled'));
    }

    public function testListenerResolvedFromContainerIsAttached(): void
    {
        $this->container->method('has')
            ->with(TestListener::class)
            ->willReturn(true);
        $this->container->method('get')
            ->willReturnMap([
                ['config', ['dot-event' => [TestListener::class]]],
                [TestListener::class, new TestListener()],
            ]);

        $eventManager = ($this->eventManagerFactory)($this->container);
        $event        = new Event(TestListener::EVENT_NAME);
        $eventManager->triggerEvent($event);

        $this->assertTrue($event->getParam('handled'));
    }

    public function testListenerInstanceInConfigIsAttached(): void
    {
        $this->container->method('get')
            ->with('config')
            ->willReturn(['dot-event' => [new TestListener()]]);

        $eventManager = ($this->eventManagerFactory)($this->container);
        $event        = new Event(TestListener::EVENT_NAME);
        $eventManager->triggerEvent($event);

        $this->assertTrue($event->getParam('handled'));
    }

    public function testListenerInstantiatedFromClassNameIsDeprecated(): void
    {
        $this->container->method('has')
            ->with(TestListener::class)
            ->willReturn(false);
        $this->container->method('get')
            ->with('config')
            ->willReturn(['dot-event' => [TestListener::class]]);

        $deprecations = [];
        set_error_handler(
            static function (int $errno, string $errstr) use (&$deprecations): bool {
                $deprecations[] = $errstr;

                return true;
            },
            E_USER_DEPRECATED
        );

        try {
            $eventManager = ($this->eventManagerFactory)($this->container);
        } finally {
            restore_error_handler();
        }

        $this->assertCount(1, $deprecations);
        $this->assertStringContainsString(TestListener::class, $deprecations[0]);
        $this->assertStringContainsString('deprecated', $deprecations[0]);

        $event = new Event(TestListener::EVENT_NAME);
        $eventManager->triggerEvent($event);

        $this->assertTrue($event->getParam('handled'));
    }

    #[DataProvider('invalidListenerProvider')]
    public function testInvalidListenerThrows(mixed $listener, string $provided): void
    {
        $this->container->method('get')
            ->with('config')
            ->willReturn(['dot-event' => [$listener]]);

        try {
            ($this->eventManagerFactory)($this->container);
            $this->fail('Expected an exception for an invalid listener');
        } catch (RuntimeException $exception) {
            $this->assertSame(
                sprintf(
                    'Event listener must be an instance of %s, but %s was provided',
                    DotEventListenerInterface::class,
                    $provided
                ),
                $exception->getMessage()
            );
        }
    }

    /**
     * @return array<string, array{0: mixed, 1: string}>
     */
    public static function invalidListenerProvider(): array
    {
        return [
            'unknown class name' => ['Not\\A\\Listener', 'string'],
            'integer'            => [123, 'int'],
            'array'              => [[], 'array'],
            'object'             => [new stdClass(), 'stdClass'],
        ];
    }
}
