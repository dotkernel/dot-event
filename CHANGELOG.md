# Changelog

## 5.0.0 - 2026-11-02

### Changed

- Minimum supported PHP version is now 8.3; PHP 8.6 is allowed.
- `Dot\Event\Factory\EventManagerFactory` now throws `Dot\Event\Exception\RuntimeException` (which extends `\RuntimeException`) when a configured listener does not implement `Dot\Event\DotEventListenerInterface`.
- Entries under the `dot-event` config key may now also be listener instances.
- The package now requires `psr/container` directly instead of `laminas/laminas-servicemanager`.

### Added

- `Dot\Event\Exception\ExceptionInterface` and `Dot\Event\Exception\RuntimeException`.

### Deprecated

- Instantiating a listener from its class name when the container has no service with that name. This now emits an `E_USER_DEPRECATED` notice and will be removed in the next major version: register your listeners in the container.

### Removed

- Support for PHP 8.2.
- The `psr/http-message` requirement, which the package never used. Projects that rely on it must require it directly.
- The `laminas/laminas-servicemanager` requirement. Projects that rely on it must require it directly.

### Fixed

- A `TypeError` is no longer thrown when a `dot-event` config entry is not a string; the listener validation error is thrown for invalid entries instead.

## 4.4.0 - 2025-10-31

### Added

- Issue #36: Support for PHP 8.4 and 8.5 (#38).

### Removed

- Issue #36: Support for PHP 8.1 (#38).

## 4.3.0 - 2025-03-04

### Changed

- Issue #32: Replaced Psalm with PHPStan (#33).

## 4.2.1 - 2025-01-28

### Changed

- Issue #30: Bumped Psalm to 6.x (#31).

## 4.2.0 - 2025-01-22

### Added

- Issue #26: Support for PHP 8.4 (#29).

## 4.1.0 - 2025-01-13

### Added

- Issue #27: Support for `laminas/laminas-servicemanager` 4.x (#28).

### Changed

- Updated `README.md` (#25).

## 4.0.3 - 2024-06-28

### Changed

- Issue #22: Marked the package as a wrapper on top of laminas-eventmanager (#23).
- Updated `overview.md` (#24).

## 4.0.2 - 2024-05-03

No changes since 4.0.1.

## 4.0.1 - 2024-05-03

### Fixed

- Renamed `docs/book/v4/instalation.md` to `installation.md` and fixed `configuration.md` (#21).

## 4.0.0 - 2024-04-25

### Added

- `Dot\Event\DotEventListenerInterface`.
- Documentation for version 4, and `SECURITY.md`.

### Changed

- Refactored the package: `EventManagerFactory` now attaches the listeners listed under the `dot-event` config key (#20).
- CI now uses the shared Laminas continuous integration workflow.

## 3.5.0 - 2025-10-31

### Added

- Issue #35: Support for PHP 8.4 and 8.5 (#37).

### Removed

- Issue #35: Support for PHP 8.1 (#37).

## 3.4.2 - 2023-11-29

### Added

- Support for PHP 8.3 (#17).

## 3.4.1 - 2023-08-24

### Added

- Codecov integration and badge (#13, #15).

### Changed

- Allowed `psr/http-message` `^1.0 || ^2.0` (#13).

## 3.4.0 - 2023-07-21

### Changed

- Code quality improvements, enhancements and tests (#11).

## 3.3.0 - 2023-03-29

### Removed

- `laminas/laminas-dependency-plugin` from `composer.json` (#8).

## 3.2.0 - 2022-04-01

### Changed

- Updated dependency versions.
- Updated tags in `README.md`.

## 3.1.0 - 2020-11-02

### Changed

- Composer 2 compatibility.
- Update composer.json to require version 2 of laminas/laminas-dependency-plugin to be Composer 2 compatible.

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 3.0.1 - 2020-04-23

### Changed

- License, `.gitignore` and composer keywords.

## 3.0.0 - 2020-04-10

### Changed

- Dependencies update.

## 2.0.0 - 2020-01-30

### Changed

- Laminas and Mezzio migration.

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Support

### Fixed

- Nothing

## 0.2.1 - 2018-05-04

### Changed

- dependency update

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 0.2.0 - 2017-03-15

### Changed

- Updated factories to typehint against PSR11 container

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 0.1.1 - 2017-03-11

### Added

- Updated php file headers doc blocks and package description

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 0.1.0 - 2017-03-07

Initial tagged release

### Added

- Everything

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing
