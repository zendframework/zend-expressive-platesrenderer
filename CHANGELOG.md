# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.1.0 - 2019-03-12

### Added

- [#36](https://github.com/zendframework/zend-expressive-platesrenderer/pull/36) adds the template function `route()`, which will return a
  `Zend\Expressive\Router\RouteResult` or `null`, based on the presence or
  absence of a `RouteResult` in the `Zend\Expressive\Helper\UrlHelper` instance
  composed. This can be used to get the matched route name and/or parameters.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.0.0 - 2018-03-15

### Added

- [#27](https://github.com/zendframework/zend-expressive-platesrenderer/pull/27) and
  [#32](https://github.com/zendframework/zend-expressive-platesrenderer/pull/32)
  add support for the zend-expressive-template v2 series,
  zend-expressive-router v3 series, and zend-expressive-helpers v5 series.

- [#29](https://github.com/zendframework/zend-expressive-platesrenderer/pull/29)
  adds a `ConfigProvider` class with default service wiring and configuration
  for the component. It also updates `composer.json` to add
  `extra.zf.config-provider` configuration to notify zend-component-installer
  of the shipped `ConfigProvider` class, allowing the plugin to inject the
  `ConfigProvider` in your application configuration during initial
  installation.

### Changed

- [#27](https://github.com/zendframework/zend-expressive-platesrenderer/pull/27)
  updates all classes to use scalar typehints and return typehints, including
  nullable types and void types, whenever possible, in order to improve
  reliability and predictability of operation.

- [#28](https://github.com/zendframework/zend-expressive-platesrenderer/pull/28)
  updates the package `ExceptionInterface` to extend from the
  `ExceptionInterface` provided in zend-expressive-template.

### Deprecated

- Nothing.

### Removed

- [#27](https://github.com/zendframework/zend-expressive-platesrenderer/pull/27)
  drops support for zend-expressive-template v1.

- [#27](https://github.com/zendframework/zend-expressive-platesrenderer/pull/27)
  drops support for PHP versions prior to PHP 7.1.

### Fixed

- Nothing.

## 1.4.0 - 2017-11-02

### Added

- [#22](https://github.com/zendframework/zend-expressive-platesrenderer/pull/22)
  adds `Zend\Expressive\PlatesRenderer\Extension\EscaperExtension`, as well as a
  factory for the extension. This extension provides context-specific escaper
  helpers to the Plates engine, based on zend-escaper: `escapeHtml()`,
  `escapeHtmlAttr()`, `escapeJs()`, `escapeCss()`, and `escapeUrl()`. These are
  registered by default with the Plates engine. If you wish to provide
  alternates, provide a `Zend\Expressive\PlatesRenderer\Extension\EscaperExtension` 
  service that provides the custom extension.

### Changed

- [#19](https://github.com/zendframework/zend-expressive-platesrenderer/pull/19)
  changes all factories to typehint against the PSR-11
  `Psr\Container\ContainerInterface` instead of
  `Interop\Container\ContainerInterface`. This change is backwards compatible
  for consumers, as container-interop interfaces extend from PSR-11 at this
  time. However, if you were _extending_ any of the factories, you will need to
  update your code to use the new typehints. (We expect very few extensions, as
  typically delegator factories will be used to modify instances returned by
  factories, or custom factories will be written.)

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.3.2 - 2017-11-01

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#25](https://github.com/zendframework/zend-expressive-platesrenderer/pull/25)
  modifies the `PlatesEngineFactory` such that it now skips injection of the
  `UrlExtension` if zend-expressive-helpers is not installed. This change allows
  usage of the package outside the Expressive ecosystem.

## 1.3.1 - 2017-03-14

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#17](https://github.com/zendframework/zend-expressive-platesrenderer/pull/17)
  fixes the default value of the `UrlExtension`'s `$fragmentIdentifier` to be
  `null` instead of an empty string.

## 1.3.0 - 2017-03-14

### Added

- [#18](https://github.com/zendframework/zend-expressive-platesrenderer/pull/18)
  adds support for zend-expressive-helpers 4.0.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.2.1 - 2017-03-02

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#15](https://github.com/zendframework/zend-expressive-platesrenderer/pull/15)
  updates the import statement for exceptions to point to the correct
  `Zend\Expressive\Router\Exception` namespace.

## 1.2.0 - 2017-01-11

### Added

- [#11](https://github.com/zendframework/zend-expressive-platesrenderer/pull/11)
  adds support for zend-expressive-helpers 3.0.0 (and, consequently,
  zend-expressive-router 2.0.0). Users may now pass additional arguments to the
  `url()` helper:

  ```php
  echo $this->url(
      $routeName,         // (optional) string route name; omit to use current matched route
      $routeParams,       // (optional) array of route parameter substitutions
      $queryParams,       // (optional) array of query string parameters to include
      $fragmentIdentifer, // (optional) string fragment to include
      $options,           // (optional) array of options; `router` array will be
                          // passed to the router, `reuse_result_params` can be
                          // passed to disable reuse of matched route parameters.
  );
  ```

  If you are still using the zend-expressive-helpers 2.2 series and/or
  zend-expressive-router 1.0 series, all parameters provided after the
  `$routeParams` will be ignored.

### Deprecated

- Nothing.

### Removed

- [#14](https://github.com/zendframework/zend-expressive-platesrenderer/pull/14)
  removes support for PHP 5.5.

### Fixed

- Nothing.

## 1.1.1 - 2017-01-11

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#9](https://github.com/zendframework/zend-expressive-platesrenderer/pull/9) and
  [#13](https://github.com/zendframework/zend-expressive-platesrenderer/pull/13)
  update the `PlatesEngineFactory` to ensure it raises a
  `Zend\Expressive\Plates\Exception\InvalidExtensionException` if a named
  service or an invokable extension class result in a non-`ExtensionInterface`
  instance.

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
