<?php

declare(strict_types=1);

namespace DotTest\Event\Exception;

use Dot\Event\Exception\ExceptionInterface;
use Dot\Event\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

use function class_implements;
use function get_parent_class;

class RuntimeExceptionTest extends TestCase
{
    public function testExtendsNativeRuntimeException(): void
    {
        $this->assertSame(\RuntimeException::class, get_parent_class(RuntimeException::class));
    }

    public function testImplementsPackageExceptionInterface(): void
    {
        $this->assertContains(ExceptionInterface::class, class_implements(RuntimeException::class));
    }
}
