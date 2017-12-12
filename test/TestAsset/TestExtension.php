<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-platesrenderer for the canonical source repository
 * @copyright Copyright (c) 2016-2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-platesrenderer/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\Expressive\Plates\TestAsset;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class TestExtension implements ExtensionInterface
{
    public static $engine;

    public function register(Engine $engine)
    {
        self::$engine = $engine;
    }
}
