# dot-event

Wrapper around the zend-eventmanager library. It add an event manager factory and initializes it with a SharedEventManager so that you can hook to events from anywhere in code.

## Installation

Run the following command in your project's root directory
```bash
$ composer require dotkernel/dot-event
```

This will install the zend-eventmanager package as dependency
Next, merge the `ConfigProvider` to your application's configuration.

## Usage

After enabling the module, anywhere you need an EventManager, you should get it from the service manager like
```php
$container->get(EventManagerInterface::class);
```

This will ensure that each EventManager will be injected a SharedEventManager.

For more on event managers, read the official zend-eventmanager documentation