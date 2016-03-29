<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @see       https://github.com/zendframework/zend-expressive-platesrenderer for the canonical source repository
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-platesrenderer/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Plates\Extension;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Zend\Expressive\Helper\ServerUrlHelper;
use Zend\Expressive\Helper\UrlHelper;

class UrlExtension implements ExtensionInterface
{
    /**
     * @var ServerUrlHelper
     */
    private $serverUrlHelper;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @param UrlHelper $urlHelper
     * @param ServerUrlHelper $serverUrlHelper
     */
    public function __construct(UrlHelper $urlHelper, ServerUrlHelper $serverUrlHelper)
    {
        $this->urlHelper = $urlHelper;
        $this->serverUrlHelper = $serverUrlHelper;
    }

    /**
     * Register functions with the Plates engine.
     *
     * Registers:
     *
     * - url($route = null, array $params = []) : string
     * - serverurl($path = null) : string
     *
     * @param Engine $engine
     * @return void
     */
    public function register(Engine $engine)
    {
        $engine->registerFunction('url', [$this, 'generateUrl']);
        $engine->registerFunction('serverurl', [$this, 'generateServerUrl']);
    }

    /**
     * Generate a URL from either the currently matched route or the specfied route.
     *
     * @param null|string $route Name of route from which to generate URL.
     * @param array $params Route substitution parameters
     * @return string
     */
    public function generateUrl($route = null, array $params = [])
    {
        return $this->urlHelper->generate($route, $params);
    }

    /**
     * Generate a fully qualified URI, relative to $path.
     *
     * @param null|string $path
     * @return string
     */
    public function generateServerUrl($path = null)
    {
        return $this->serverUrlHelper->generate($path);
    }
}
