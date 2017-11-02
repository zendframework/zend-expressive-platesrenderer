<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-platesrenderer for the canonical source repository
 * @copyright Copyright (c) 2016-2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-platesrenderer/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Plates\Extension;

use Psr\Container\ContainerInterface;
use Zend\Escaper\Exception\InvalidArgumentException;

/**
 * Factory for creating a EscaperExtension instance.
 *
 * Optionally uses the service 'config', which should return an array. This
 * factory consumes the following structure:
 *
 * <code>
 * 'plates' => [
 *     'encoding' => 'global encoding value, if not set then will fallback to UTF-8'
 * ]
 * </code>
 */
class EscaperExtensionFactory
{
    /**
     * @param ContainerInterface $container
     * @return EscaperExtension
     * @throws InvalidArgumentException
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $config = isset($config['plates']) ? $config['plates'] : [];

        $encoding = null;

        // Set encoding
        if (isset($config['encoding'])) {
            $encoding = $config['encoding'];
        }

        // Create new EscaperExtension instance
        return new EscaperExtension($encoding);
    }
}
