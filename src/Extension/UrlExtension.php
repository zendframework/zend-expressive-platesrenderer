<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-platesrenderer for the canonical source repository
 * @copyright Copyright (c) 2016-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-platesrenderer/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Expressive\Plates\Extension;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Zend\Expressive\Helper\ServerUrlHelper;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Router\RouteResult;

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
     */
    public function register(Engine $engine) : void
    {
        $engine->registerFunction('url', [$this, 'generateUrl']);
        $engine->registerFunction('serverurl', [$this, 'generateServerUrl']);
        $engine->registerFunction('route', [$this, 'getRouteResult']);
    }

    /**
     * Get the RouteResult instance of UrlHelper, if any.
     */
    public function getRouteResult() : ?RouteResult
    {
        return $this->urlHelper->getRouteResult();
    }

    /**
     * Generate a URL from either the currently matched route or the specfied route.
     *
     * @param array  $options Can have the following keys:
     *     - router (array): contains options to be passed to the router
     *     - reuse_result_params (bool): indicates if the current RouteResult
     *       parameters will be used, defaults to true
     * @return string
     */
    public function generateUrl(
        string $routeName = null,
        array $routeParams = [],
        array $queryParams = [],
        ?string $fragmentIdentifier = null,
        array $options = []
    ) {
        return $this->urlHelper->generate($routeName, $routeParams, $queryParams, $fragmentIdentifier, $options);
    }

    /**
     * Generate a fully qualified URI, relative to $path.
     */
    public function generateServerUrl(string $path = null) : string
    {
        return $this->serverUrlHelper->generate($path);
    }
}
