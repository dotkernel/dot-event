<?php

declare(strict_types=1);

namespace DotTest\Event;

use Dot\Event\ConfigProvider;
use Dot\Event\Factory\EventManagerFactory;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerInterface;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    /** @var array<string, mixed> */
    protected array $config;

    protected function setUp(): void
    {
        $this->config = (new ConfigProvider())();
    }

    public function testHasDependencies(): void
    {
        $this->assertArrayHasKey('dependencies', $this->config);
        $this->assertArrayHasKey('dot-event', $this->config);
        $this->assertSame([], $this->config['dot-event']);
    }

    public function testDependenciesHasFactories(): void
    {
        $this->assertArrayHasKey('factories', $this->config['dependencies']);
        $this->assertArrayHasKey(EventManager::class, $this->config['dependencies']['factories']);
        $this->assertSame(
            EventManagerFactory::class,
            $this->config['dependencies']['factories'][EventManager::class]
        );
    }

    public function testDependenciesHasAliases(): void
    {
        $this->assertArrayHasKey('aliases', $this->config['dependencies']);
        $this->assertArrayHasKey(EventManagerInterface::class, $this->config['dependencies']['aliases']);
        $this->assertSame(
            EventManager::class,
            $this->config['dependencies']['aliases'][EventManagerInterface::class]
        );
    }

    public function testEventManagerResolvesFromServiceManager(): void
    {
        $container = new ServiceManager($this->config['dependencies']);
        $container->setService('config', ['dot-event' => []]);

        $eventManager = $container->get(EventManagerInterface::class);

        $this->assertInstanceOf(EventManager::class, $eventManager);
        $this->assertSame($eventManager, $container->get(EventManager::class));
    }
}
