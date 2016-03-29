<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @see       https://github.com/zendframework/zend-expressive-platesrenderer for the canonical source repository
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-platesrenderer/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Plates;

use Interop\Container\ContainerInterface;
use League\Plates\Engine as PlatesEngine;
use League\Plates\Extension\ExtensionInterface;

/**
 * Create and return a Plates engine instance.
 *
 * Optionally uses the service 'config', which should return an array. This
 * factory consumes the following structure:
 *
 * <code>
 * 'plates' => [
 *     'extensions' => [
 *         // extension instances, or
 *         // service names that return extension instances, or
 *         // class names of directly instantiable extensions.
 *     ]
 * ]
 * </code>
 *
 * By default, this factory attaches the Extension\UrlExtension to
 * the engine. You can override the functions that extension exposes
 * by providing an extension class in your extensions array, or providing
 * an alternative Zend\Expressive\Plates\Extension\UrlExtension service.
 */
class PlatesEngineFactory
{
    /**
     * @param ContainerInterface $container
     * @return PlatesEngine
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $config = isset($config['plates']) ? $config['plates'] : [];

        // Create the engine instance:
        $engine = new PlatesEngine();

        $this->injectUrlExtension($container, $engine);

        if (isset($config['extensions']) && is_array($config['extensions'])) {
            $this->injectExtensions($container, $engine, $config['extensions']);
        }

        return $engine;
    }

    /**
     * Inject the URL/ServerUrl extensions provided by this package.
     *
     * If a service by the name of the UrlExtension class exists, fetches
     * and loads it.
     *
     * Otherwise, instantiates the UrlExtensionFactory, and invokes it with
     * the container, loading the result into the engine.
     *
     * @param ContainerInterface $container
     * @param PlatesEngine $engine
     * @return void
     */
    private function injectUrlExtension(ContainerInterface $container, PlatesEngine $engine)
    {
        if ($container->has(Extension\UrlExtension::class)) {
            $engine->loadExtension($container->get(Extension\UrlExtension::class));
            return;
        }

        $extensionFactory = new Extension\UrlExtensionFactory();
        $engine->loadExtension($extensionFactory($container));
    }

    /**
     * Inject all configured extensions into the engine.
     * @param ContainerInterface $container
     * @param PlatesEngine $engine
     * @param array $extensions
     * @return void
     */
    private function injectExtensions(ContainerInterface $container, PlatesEngine $engine, array $extensions)
    {
        foreach ($extensions as $extension) {
            $this->injectExtension($container, $engine, $extension);
        }
    }

    /**
     * Inject an extension into the engine.
     *
     * Valid extension specifications include:
     *
     * - ExtensionInterface instances
     * - String service names that resolve to ExtensionInterface instances
     * - String class names that resolve to ExtensionInterface instances
     *
     * If anything else is provided, an exception is raised.
     *
     * @param ContainerInterface $container
     * @param PlatesEngine $engine
     * @param ExtensionInterface|string $extension
     * @return void
     * @throws Exception\InvalidExtensionException for non-string,
     *     non-extension $extension values.
     * @throws Exception\InvalidExtensionException for string $extension values
     *     that do not resolve to an extension instance.
     */
    private function injectExtension(ContainerInterface $container, PlatesEngine $engine, $extension)
    {
        if ($extension instanceof ExtensionInterface) {
            $engine->loadExtension($extension);
            return;
        }

        if (! is_string($extension)) {
            throw new Exception\InvalidExtensionException(sprintf(
                '%s expects extension instances, service names, or class names; received %s',
                __CLASS__,
                (is_object($extension) ? get_class($extension) : gettype($extension))
            ));
        }

        if ($container->has($extension)) {
            $engine->loadExtension($container->get($extension));
            return;
        }

        if (class_exists($extension)) {
            $engine->loadExtension(new $extension());
            return;
        }

        throw new Exception\InvalidExtensionException(sprintf(
            '%s expects extension service names or class names; "%s" does not resolve to either',
            __CLASS__,
            $extension
        ));
    }
}
