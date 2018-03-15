<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-platesrenderer for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-platesrenderer/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\Expressive\Plates\Extension;

use League\Plates\Engine;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Zend\Escaper\Escaper;
use Zend\Expressive\Plates\Extension\EscaperExtension;

use function is_array;

class EscaperExtensionTest extends TestCase
{
    public function testRegistersEscaperFunctionsWithEngine()
    {
        $extension = new EscaperExtension();

        $engine = $this->prophesize(Engine::class);
        $engine
            ->registerFunction('escapeHtml', Argument::that(function ($argument) {
                return is_array($argument) && $argument[0] instanceof Escaper && $argument[1] === 'escapeHtml';
            }))->shouldBeCalled();
        $engine
            ->registerFunction('escapeHtmlAttr', Argument::that(function ($argument) {
                return is_array($argument) && $argument[0] instanceof Escaper && $argument[1] === 'escapeHtmlAttr';
            }))->shouldBeCalled();
        $engine
            ->registerFunction('escapeJs', Argument::that(function ($argument) {
                return is_array($argument) && $argument[0] instanceof Escaper && $argument[1] === 'escapeJs';
            }))->shouldBeCalled();
        $engine
            ->registerFunction('escapeCss', Argument::that(function ($argument) {
                return is_array($argument) && $argument[0] instanceof Escaper && $argument[1] === 'escapeCss';
            }))->shouldBeCalled();
        $engine
            ->registerFunction('escapeUrl', Argument::that(function ($argument) {
                return is_array($argument) && $argument[0] instanceof Escaper && $argument[1] === 'escapeUrl';
            }))->shouldBeCalled();

        $extension->register($engine->reveal());
    }
}
