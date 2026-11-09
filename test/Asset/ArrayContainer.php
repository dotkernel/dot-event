<?php

declare(strict_types=1);

namespace DotTest\Event\Asset;

use Psr\Container\ContainerInterface;

use function array_key_exists;
use function is_callable;
use function is_string;
use function sprintf;

final class ArrayContainer implements ContainerInterface
{
    /** @var array<string, mixed> */
    private array $services;

    /**
     * @param array<string, mixed> $dependencies
     * @param array<string, mixed> $services
     */
    public function __construct(private readonly array $dependencies, array $services = [])
    {
        $this->services = $services;
    }

    /**
     * @param string $id
     */
    public function get($id): mixed
    {
        $id = $this->resolveAlias($id);

        if (array_key_exists($id, $this->services)) {
            return $this->services[$id];
        }

        $factory = $this->dependencies['factories'][$id] ?? null;
        if (! is_string($factory)) {
            throw new NotFoundException(sprintf('Service "%s" not found', $id));
        }

        $instance = new $factory();
        if (! is_callable($instance)) {
            throw new NotFoundException(sprintf('Factory for "%s" is not invokable', $id));
        }

        return $this->services[$id] = $instance($this, $id);
    }

    /**
     * @param string $id
     */
    public function has($id): bool
    {
        $id = $this->resolveAlias($id);

        return array_key_exists($id, $this->services) || isset($this->dependencies['factories'][$id]);
    }

    private function resolveAlias(string $id): string
    {
        return $this->dependencies['aliases'][$id] ?? $id;
    }
}
