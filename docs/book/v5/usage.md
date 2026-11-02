# Usage

This tutorial explores various examples of dot-event usages.

To start using events, you will need the following things:

- An **Event**
- One or more **listeners** that will be registered in the application config
- An instance of **EventManager** from the **container** so you can trigger the events

The listeners need to be registered in your application's config (for example in your own module's `ConfigProvider`), under the `dot-event` key.

## Example

The below example will implement an event for update users.

Events should extend `Dot\Event\Event`:

```php
use Dot\Event\Event;

class UserEvent extends Event
{
    public const EVENTS_PRE_UPDATE = 'pre.update.user';
    public const EVENTS_POST_UPDATE = 'post.update.user';
}
```

We use the concept of listener aggregates because with this approach in a single class we can listen to multiple events.
If you pay attention, in the above event we have two events `pre.update.user` and `post.update.user`.

Every listener needs to implement `Dot\Event\DotEventListenerInterface`.
The interface extends `Laminas\EventManager\ListenerAggregateInterface`, so it requires two methods: `attach()` and `detach()`.

In the examples below, `User` and `Logger` stand for your own classes.

```php
use Dot\Event\DotEventListenerInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateTrait;

class UserEventListener implements DotEventListenerInterface
{
    use ListenerAggregateTrait;
    
    #[Dot\AnnotatedServices\Attribute\Inject(Logger::class)]
    public function __construct(private Logger $logger)
    {
    }

    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(UserEvent::EVENTS_PRE_UPDATE, [$this, 'onUserPreUpdated']);
        $this->listeners[] = $events->attach(UserEvent::EVENTS_POST_UPDATE, [$this, 'onUserPostUpdate']);
    }

    public function onUserPreUpdated(UserEvent $event)
    {
        $this->logger->info('The pre update event is triggered');
        
        //...more logic
   
    }

    public function onUserPostUpdate(UserEvent $event)
    {
        //...more logic
        
        $this->logger->info('The post update event is triggered');
    }

}
```

> The trait `Laminas\EventManager\ListenerAggregateTrait` can be used to help implementing `DotEventListenerInterface`.
> It defines the `$listeners` property, and `detach()` logic.

We register the listener in the application config, under the `dot-event` key:

```php
'dot-event' => [
    UserEventListener::class
]
```

Each entry is resolved in the following order:

1. If the entry is already an instance of `Dot\Event\DotEventListenerInterface`, it is used as is.
2. If the entry is a string and the container has a service with that name, the service is used. This is the recommended way, and the only way to give a listener constructor dependencies.
3. Otherwise, if the entry is the name of an existing class, the class is instantiated without arguments.
   > This fallback is **deprecated** and emits an `E_USER_DEPRECATED` notice. It will be removed in the next major version, so register your listeners in the container.
4. Otherwise, a `Dot\Event\Exception\RuntimeException` is thrown.

In every case, the resolved listener must implement `Dot\Event\DotEventListenerInterface`, otherwise the same exception is thrown. This includes entries that are not strings or listener instances, such as integers or arrays.

> Listener instances are convenient in tests or small scripts. In an application that caches its configuration, register listeners by class name and let the container create them, because objects cannot be stored in a cached config file.

> Listeners are created and attached when the `EventManager` is first resolved from the container, not when an event is triggered. Do not inject `EventManagerInterface` into a listener's constructor, since that creates a circular dependency.

Every event can be triggered from an `Laminas\EventManager\EventManager` instance loaded from the container:

```php
class MyService
{
    #[Dot\AnnotatedServices\Attribute\Inject(EventManagerInterface::class)]
    public function __construct(private EventManagerInterface $eventManager) 
    {
    
    }
    
    public function update(User $user)
    {
        $this->eventManager->triggerEvent(new UserEvent(UserEvent::EVENTS_PRE_UPDATE, params: ['user' => $user]));
        
        // ... logic of update
        
        $this->eventManager->triggerEvent(new UserEvent(UserEvent::EVENTS_POST_UPDATE, params: ['user' => $user]));
    }
}

```

> To inject classes from the container we use `Dot\AnnotatedServices\Attribute\Inject` from `dot-annotated-services` package, but you can use your own logic to get things from the container.

## Keep all in order

You can attach multiple listeners to the same event but with different logic.

All listeners are executed in the order in which they are attached. However, you can provide a priority value, and you can influence the order of the execution.

- Higher priority values execute earlier
- Lower (negative) priority values execute later

```php
class UserEventListener implements DotEventListenerInterface
{
    use ListenerAggregateTrait;
    
    #[Dot\AnnotatedServices\Attribute\Inject(Logger::class)]
    public function __construct(private Logger $logger)
    {
    }

    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(UserEvent::EVENTS_POST_UPDATE, [$this, 'onUserPostUpdateSecond'], 1); // will run second
        $this->listeners[] = $events->attach(UserEvent::EVENTS_POST_UPDATE, [$this, 'onUserPostUpdateFirst'], 2); // will run first
    }
    
    // ... callbacks

}
```

As you can notice, we attach the same event name to listeners, so once we trigger the event, both callbacks will run one after another.
But because we provide priority, the second attachment will run first because it has a higher priority.

## Short-circuiting the execution

Sometimes you have more listeners to an event, and you may want to stop the execution of the event if something is wrong with one of the listeners.

```php

    public function onUserPostUpdateFirst(UserEvent $event)
    {
      $event->stopPropagation();
    }

    public function onUserPostUpdateSecond(UserEvent $event)
    {
        // this will not execute
    }
```

## Returns

You can return whatever you want in a listener callback.
All events trigger return an instance of `Laminas\EventManager\ResponseCollection` so you can have information if the event has stopped, or if event returned some expected object.

```php
class MyService
{
    #[Dot\AnnotatedServices\Attribute\Inject(EventManagerInterface::class)]
    public function __construct(private EventManagerInterface $eventManager) 
    {
    }
    
    public function update(User $user)
    {
        /** @var Laminas\EventManager\ResponseCollection $result */
        $result = $this->eventManager->triggerEvent(new UserEvent(UserEvent::EVENTS_POST_UPDATE, params: ['user' => $user]));
        $result->stopped(); // true or false if propagation is stopped
        $result->first(); // what the first executed listener has returned
        $result->last(); // what the last executed listener has returned
    }
}
```

## Exceptions

Invalid listener configuration throws `Dot\Event\Exception\RuntimeException`. It extends `\RuntimeException` and implements `Dot\Event\Exception\ExceptionInterface`, so you can catch any of the three.
