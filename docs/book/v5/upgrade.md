# Upgrading from v4 to v5

This page lists what you need to change in a project that uses `dotkernel/dot-event` v4 to move to v5.
The full list of changes is in the [changelog](https://github.com/dotkernel/dot-event/blob/5.0/CHANGELOG.md).

## Update the requirement

In your `composer.json`, set `"dotkernel/dot-event": "^5.0"` and run `composer update dotkernel/dot-event`.

## Required changes

### PHP version

v5 supports PHP 8.3, 8.4, 8.5 and 8.6.
PHP 8.2 is no longer supported, so upgrade PHP before you upgrade the package.

### `psr/http-message` is no longer installed

The package never used `psr/http-message`, so the requirement was removed.
If your project imports `Psr\Http\Message\*`, add `psr/http-message` to your own `composer.json`.

### `laminas/laminas-servicemanager` is no longer installed

The package now requires `psr/container` directly and no longer installs `laminas/laminas-servicemanager`.
Mezzio and Dotkernel applications already require it, so they need no action.
If your project relies on it without requiring it, add `laminas/laminas-servicemanager` to your own `composer.json`.

The `ConfigProvider` still registers its `factories` and `aliases` in the Laminas ServiceManager format, so your container must understand that format.

### Listeners that are neither services nor `DotEventListenerInterface`

`Dot\Event\Factory\EventManagerFactory` resolves each entry under the `dot-event` config key in this order:

1. An instance of `Dot\Event\DotEventListenerInterface` is used as is.
2. A string that is a container service is fetched from the container.
3. A string that is a class name is instantiated with `new`. This is deprecated.
4. Anything that is not a `Dot\Event\DotEventListenerInterface` after these steps, including a container service of another type, throws `Dot\Event\Exception\RuntimeException`.

What changes for you:

- A listener that does not implement `Dot\Event\DotEventListenerInterface` now throws `Dot\Event\Exception\RuntimeException`.
- The exception extends `\RuntimeException` and implements `Dot\Event\Exception\ExceptionInterface`, so existing `catch (\RuntimeException)` blocks still work.
- A non-string, non-listener entry no longer causes a `TypeError`; it throws the same `RuntimeException`.
- Fix the listeners that throw by implementing `Dot\Event\DotEventListenerInterface`, or remove them from the `dot-event` config key.

## Deprecations

### Creating listeners from their class name

If the container has no service with the configured class name, the factory still creates the listener with `new`.
This now triggers an `E_USER_DEPRECATED` notice and will be removed in the next major version.

To remove the notice, register each listener in the container, for example with a factory in your `ConfigProvider`:

```php
'dependencies' => [
    'factories' => [
        UserEventListener::class => UserEventListenerFactory::class,
    ],
],
'dot-event' => [
    UserEventListener::class,
],
```

Listeners are created when the `EventManager` is first resolved from the container, not when an event is triggered.

## New features

- `dot-event` entries may now be listener instances as well as class names or service names.
- `Dot\Event\Exception\ExceptionInterface` and `Dot\Event\Exception\RuntimeException` are new, so you can catch package errors specifically.

## Optional changes

These only affect development of the package itself and need no action in your project.

- The test suite runs on PHPUnit 12.
- Static analysis runs PHPStan at level 8.
- The GitHub Actions workflows were updated.

## FAQ

### Do I need to change my code?

Only if one of these applies:

- You run PHP 8.2 or older.
- You depend on `psr/http-message` or `laminas/laminas-servicemanager` without requiring them yourself.
- You have `dot-event` entries that are not listeners; they now throw `Dot\Event\Exception\RuntimeException`.

### Will my listeners still work?

Yes.
Listeners registered in the container keep working.
Listeners that were created from their class name still work, with a deprecation notice.

### How do I find the deprecated listeners?

Run your application with `E_USER_DEPRECATED` reporting enabled, or set an error handler that logs it.
Each notice names the class that was created with `new`.

### Do I have to switch to listener instances?

No.
Instances are an option, for listeners that you create yourself during configuration.
Registering listeners in the container is the supported way to remove the deprecation.

### Can I use v5 with another container than laminas-servicemanager?

Yes, any PSR-11 container works, as long as it supports the `factories` and `aliases` format of the `ConfigProvider`.
You can also register the `EventManager` yourself using `Dot\Event\Factory\EventManagerFactory`.
