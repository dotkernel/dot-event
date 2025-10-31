# Usage

This tutorial explores various examples of dot-event usages.

To start using events, you will need the following things:

- An **Event**
- One or more **listeners** that will be registered on the application config (`ConfigProvider`)
- An instance of **EventManager** from the **container** so you can trigger the events

The listeners need to be registered in the `ConfigProvider`, under the `['dot-event']` key.

## Example

The below example will implement an event for update users.

Every event needs to extend `Dot\Event\Event`:

```php
class UserEvent extends Event
{
    public const EVENTS_PRE_UPDATE = 'pre.update.user';
    public const EVENTS_POST_UPDATE = 'post.update.user';

    public function __construct($name = null, $target = null, $params = [])
    {
        parent::__construct($name, $target, $params);
    }
    
}
```

We use the concept of listener aggregates because with this approach in a single class we can listen to multiple events.
If you pay attention, in the above event we have two events `pre.update.user` and `post.update.user`.

Every listener needs to extend `Dot\Event\ControllerEventListenerInterface`.
The interface defines two methods `attach()` and `detach()`.

```php
class UserEventListener implements DotEventListenerInterface
{
    use ListenerAggregateTrait;
    
    #[Dot\AnnotatedServices\Attribute\Inject(Logger::class)]
    public function __construct(private Logger $logger)
    {
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(UserUpdatedEvent::EVENTS_PRE_UPDATE, [$this, 'onUserPreUpdated']);
        $this->listeners[] = $events->attach(UserUpdatedEvent::EVENTS_POST_UPDATE, [$this, 'onUserPostUpdate']);
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

We register the listener in the `ConfigProvider`:

```php
'dot-event' => [
    UserEventListener::class
]
```

Every event can be triggered from an `Laminas\EventManager\EventManager` instance loaded from the container:

```php
class MyService
{
    #[Dot\AnnotatedServices\Attribute\Inject(EventManagerInterface::class)]
    public function __construct(private EventManagerInterface $eventManager) 
    {
    
    }
    
    public function update()
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

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(UserUpdatedEvent::EVENTS_POST_UPDATE, [$this, 'onUserPostUpdateSecond'], 1); // will run second
        $this->listeners[] = $events->attach(UserUpdatedEvent::EVENTS_POST_UPDATE, [$this, 'onUserPostUpdateFirst'], 2); // will run first
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
    
    public function update()
    {
        /** @var Laminas\EventManager\ResponseCollection $result */
        $result = $this->eventManager->triggerEvent(new UserEvent(UserEvent::EVENTS_POST_UPDATE, params: ['user' => $user]));
        $result->stopped(); // true or false if propagation is stopped
        $result->first(); // what last listener has returned (on multiple listeners it uses LIFO mode)
    }
}
```
