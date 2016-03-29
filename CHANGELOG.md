# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.1.0 - 2016-03-29

### Added

- [#7](https://github.com/zendframework/zend-expressive-platesrenderer/pull/7)
  adds:
  - `Zend\Expressive\Plates\PlatesEngineFactory`, which will create and return a
    `League\Plates\Engine` instance. It introspects the `plates.extensions`
    configuration to optionally load extensions into the engine; that value must
    be an array of:
    - extension instances
    - string service names resolving to extension instances
    - string class names resolving to extension instances
  - `Zend\Expressive\Plates\Extension\UrlExtension`, which provides a wrapper
    around the `UrlHelper` and `ServerUrlHelper` from zend-expressive-helpers,
    as the functions `url($route = null, array $params = []) : string` and
    `serverurl($path = null) : string`, respectively.
  - `Zend\Expressive\Plates\Extension\UrlExtensionFactory`, which provides a
    factory for creating the `UrlExtension`.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#7](https://github.com/zendframework/zend-expressive-platesrenderer/pull/7)
  updates `PlatesRendererFactory` to use either the `League\Plates\Engine`
  service, if available, or the new `PlatesEngineFactory` to create the Plates
  engine instance. This also ensures the `url()` and `serverurl()` functions are
  registered by default.

## 1.0.0 - 2015-12-07

First stable release.

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.3.0 - 2015-12-02

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Now depends on [zendframework/zend-expressive-template](https://github.com/zendframework/zend-expressive-template)
  instead of zendframework/zend-expressive.

## 0.2.0 - 2015-10-20

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Updated zend-expressive to RC1.
- Added branch alias for dev-master, pointing to 1.0-dev.

## 0.1.0 - 2015-10-10

Initial release.

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.
