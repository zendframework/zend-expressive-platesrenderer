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
        $engine->registerFunction('url', $this->urlHelper);
        $engine->registerFunction('serverurl', $this->serverUrlHelper);
        $engine->registerFunction('route', [$this->urlHelper, 'getRouteResult']);
    }

    /**
     * Get the RouteResult instance of UrlHelper, if any.
     *
     * @deprecated since 2.2.0; to be removed in 3.0.0. This method was originally
     *     used internally to back the route() Plates function; we now register
     *     the UrlHelper::getRouteResult callback directly.
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
     * @deprecated since 2.2.0; to be removed in 3.0.0. This method was originally
     *     used internally to back the url() Plates function; we now register
     *     UrlHelper instance directly, as it is callable.
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
     *
     * @deprecated since 2.2.0; to be removed in 3.0.0. This method was originally
     *     used internally to back the serverurl() Plates function; we now register
     *     the ServerUrl instance directly, as it is callable.
     */
    public function generateServerUrl(string $path = null) : string
    {
        return $this->serverUrlHelper->generate($path);
    }
}
