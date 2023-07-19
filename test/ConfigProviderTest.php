<?php

declare(strict_types=1);

namespace DotTest\Event;

use Dot\Event\ConfigProvider;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\SharedEventManager;
use Laminas\EventManager\SharedEventManagerInterface;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    protected array $config;

    protected function setup(): void
    {
        $this->config = (new ConfigProvider())();
    }

    public function testHasDependencies(): void
    {
        $this->assertArrayHasKey('dependencies', $this->config);
    }

    public function testDependenciesHasFactories(): void
    {
        $this->assertArrayHasKey('factories', $this->config['dependencies']);
        $this->assertArrayHasKey(SharedEventManager::class, $this->config['dependencies']['factories']);
        $this->assertArrayHasKey(EventManagerInterface::class, $this->config['dependencies']['factories']);
    }

    public function testDependenciesHasAliases(): void
    {
        $this->assertArrayHasKey('aliases', $this->config['dependencies']);
        $this->assertArrayHasKey(SharedEventManagerInterface::class, $this->config['dependencies']['aliases']);
    }

    public function testDependenciesHasShared(): void
    {
        $this->assertArrayHasKey('shared', $this->config['dependencies']);
        $this->assertArrayHasKey(EventManagerInterface::class, $this->config['dependencies']['shared']);
    }
}
