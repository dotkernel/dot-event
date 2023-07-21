<?php

declare(strict_types=1);

namespace Dot\Event;

use ArrayAccess;

/**
 * @template TTarget of object|string|null
 * @template TParams of array|ArrayAccess|object
 * @extends \Laminas\EventManager\Event<TTarget, TParams>
 */
class Event extends \Laminas\EventManager\Event
{
}
