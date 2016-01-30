<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @see       https://github.com/zendframework/zend-expressive for the canonical source repository
 * @copyright Copyright (c) 2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Plates;

use Interop\Container\ContainerInterface;
use League\Plates\Engine as PlatesEngine;
use League\Plates\Extension as PlatesExtension;

/**
 * Create and return a Plates template instance.
 *
 * Optionally uses the service 'config', which should return an array. This
 * factory consumes the following structure:
 *
 * <code>
 * 'templates' => [
 *     'extension' => 'file extension used by templates; defaults to html',
 *     'paths' => [
 *         // namespace / path pairs
 *         //
 *         // Numeric namespaces imply the default/main namespace. Paths may be
 *         // strings or arrays of string paths to associate with the namespace.
 *     ],
 * ],
 * 'plates' => [
 *     'assets_path' => 'path to assets',
 *     'extensions' => [
 *          // extension service names or instances
 *     ]
 * ]
 * </code>
 */
class PlatesRendererFactory
{
    /**
     * @param ContainerInterface $container
     * @return PlatesRenderer
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $config = $this->mergeConfig($config);

        // Create the engine instance:
        $engine = new PlatesEngine();

        // Add user defined extensions
        $extensions = (isset($config['extensions']) && is_array($config['extensions']))
            ? $config['extensions']
            : [];
        $this->injectExtensions($engine, $container, $extensions);

        // Set file extension
        if (isset($config['extension'])) {
            $engine->setFileExtension($config['extension']);
        }

        // Inject engine
        $plates = new PlatesRenderer($engine);

        // Add template paths
        $allPaths = isset($config['paths']) && is_array($config['paths']) ? $config['paths'] : [];
        foreach ($allPaths as $namespace => $paths) {
            $namespace = is_numeric($namespace) ? null : $namespace;
            foreach ((array) $paths as $path) {
                $plates->addPath($path, $namespace);
            }
        }

        return $plates;
    }

    /**
     * Merge expressive templating config with plates config.
     *
     * Pulls the `templates` and `plates` top-level keys from the configuration,
     * if present, and then retuns the merged result, with those from the plates
     * array having precedence.
     *
     * @param array|ArrayObject $config
     * @return array
     * @throws Exception\InvalidConfigException if a non-array, non-ArrayObject
     *     $config is received.
     */
    private function mergeConfig($config)
    {
        $config = $config instanceof ArrayObject ? $config->getArrayCopy() : $config;

        if (! is_array($config)) {
            throw new Exception\InvalidConfigException(sprintf(
                'config service MUST be an array or ArrayObject; received %s',
                is_object($config) ? get_class($config) : gettype($config)
            ));
        }

        $expressiveConfig = (isset($config['templates']) && is_array($config['templates']))
            ? $config['templates']
            : [];
        $platesConfig = (isset($config['plates']) && is_array($config['plates']))
            ? $config['plates']
            : [];

        return array_replace_recursive($expressiveConfig, $platesConfig);
    }

    private function injectExtensions(PlatesEngine $engine, ContainerInterface $container, array $extensions)
    {
        foreach ($extensions as $extension) {
            // Load the extension from the container
            if (is_string($extension) && $container->has($extension)) {
                $extension = $container->get($extension);
            }

            if (! $extension instanceof PlatesExtension\ExtensionInterface) {
                throw new Exception\InvalidExtensionException(sprintf(
                    'Plates extension must be an instance of League\Plates\Extension\ExtensionInterface; "%s" given,',
                    is_object($extension) ? get_class($extension) : gettype($extension)
                ));
            }

            $engine->loadExtension($extension);
        }
    }
}
