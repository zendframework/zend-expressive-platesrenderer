<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-platesrenderer for the canonical source repository
 * @copyright Copyright (c) 2016-2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-platesrenderer/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Plates\Extension;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Zend\Escaper\Escaper;
use Zend\Escaper\Exception\InvalidArgumentException;

class EscaperExtension implements ExtensionInterface
{
    /**
     * Register functions with the Plates engine.
     *
     * Registers:
     *
     * - escapeHtml($string) : string
     * - escapeHtmlAttr($string) : string
     * - escapeJs($string) : string
     * - escapeCss($string) : string
     * - escapeUrl($string) : string
     *
     * @param Engine $engine
     * @return void
     * @throws InvalidArgumentException
     */
    public function register(Engine $engine)
    {
        $escaper = new Escaper();

        $engine->registerFunction('escapeHtml', [$escaper, 'escapeHtml']);
        $engine->registerFunction('escapeHtmlAttr', [$escaper, 'escapeHtmlAttr']);
        $engine->registerFunction('escapeJs', [$escaper, 'escapeJs']);
        $engine->registerFunction('escapeCss', [$escaper, 'escapeCss']);
        $engine->registerFunction('escapeUrl', [$escaper, 'escapeUrl']);
    }
}
