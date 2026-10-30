# Configuration

After installation, you need to register the `ConfigProvider` in your project by adding the below line to your configuration aggregator (usually: `config/config.php`):

```php
Dot\Event\ConfigProvider::class,
```

## What the ConfigProvider registers

- `Laminas\EventManager\EventManager`, built by `Dot\Event\Factory\EventManagerFactory`.
- `Laminas\EventManager\EventManagerInterface`, an alias of the above, so you can inject either one. Both resolve to the same shared instance.
- An empty `dot-event` key, where your application lists its event listeners (see [Usage](usage.md)).
