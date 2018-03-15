<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-platesrenderer for the canonical source repository
 * @copyright Copyright (c) 2016-2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-platesrenderer/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Expressive\Plates\Extension;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Helper\ServerUrlHelper;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Plates\Exception\MissingHelperException;

use function sprintf;

/**
 * Factory for creating a UrlExtension instance.
 */
class UrlExtensionFactory
{
    /**
     * @throws MissingHelperException if UrlHelper service is missing.
     * @throws MissingHelperException if ServerUrlHelper service is missing.
     */
    public function __invoke(ContainerInterface $container) : UrlExtension
    {
        if (! $container->has(UrlHelper::class)) {
            throw new MissingHelperException(sprintf(
                '%s requires that the %s service be present; not found',
                UrlExtension::class,
                UrlHelper::class
            ));
        }

        if (! $container->has(ServerUrlHelper::class)) {
            throw new MissingHelperException(sprintf(
                '%s requires that the %s service be present; not found',
                UrlExtension::class,
                ServerUrlHelper::class
            ));
        }

        return new UrlExtension(
            $container->get(UrlHelper::class),
            $container->get(ServerUrlHelper::class)
        );
    }
}
