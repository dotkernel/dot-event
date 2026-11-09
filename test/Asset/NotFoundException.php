<?php

declare(strict_types=1);

namespace DotTest\Event\Asset;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class NotFoundException extends RuntimeException implements NotFoundExceptionInterface
{
}
