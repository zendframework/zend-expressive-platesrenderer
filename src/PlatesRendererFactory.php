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
 * ]
 * </code>
 *
 * If the service League\Plates\Engine exists, that value will be used
 * for the PlatesEngine; otherwise, this factory invokes the PlatesEngineFactory
 * to create an instance.
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
        $config = isset($config['templates']) ? $config['templates'] : [];

        // Create the engine instance:
        $engine = $this->createEngine($container);

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
     * Create and return a Plates Engine instance.
     *
     * If the container has the League\Plates\Engine service, returns it.
     *
     * Otherwise, invokes the PlatesEngineFactory with the $container to create
     * and return the instance.
     *
     * @param ContainerInterface $container
     * @return PlatesEngine
     */
    private function createEngine(ContainerInterface $container)
    {
        if ($container->has(PlatesEngine::class)) {
            return $container->get(PlatesEngine::class);
        }

        $engineFactory = new PlatesEngineFactory();
        return $engineFactory($container);
    }
}
