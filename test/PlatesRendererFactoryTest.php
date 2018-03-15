<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-platesrenderer for the canonical source repository
 * @copyright Copyright (c) 2015-2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-platesrenderer/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\Expressive\Plates;

use League\Plates\Engine as PlatesEngine;
use LogicException;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ProphecyInterface;
use Psr\Container\ContainerInterface;
use ReflectionProperty;
use Zend\Expressive\Helper\ServerUrlHelper;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Plates\Extension\EscaperExtension;
use Zend\Expressive\Plates\Extension\UrlExtension;
use Zend\Expressive\Plates\PlatesRenderer;
use Zend\Expressive\Plates\PlatesRendererFactory;
use Zend\Expressive\Template\TemplatePath;

use function restore_error_handler;
use function set_error_handler;
use function sprintf;

use const E_USER_WARNING;

class PlatesRendererFactoryTest extends TestCase
{
    /**
     * @var ContainerInterface|ProphecyInterface
     */
    private $container;

    /**
     * @var bool
     */
    private $errorCaught = false;

    public function setUp()
    {
        $this->errorCaught = false;
        $this->container = $this->prophesize(ContainerInterface::class);
    }

    public function configureEngineService()
    {
        $this->container->has(PlatesEngine::class)->willReturn(false);
        $this->container->has(UrlExtension::class)->willReturn(false);
        $this->container->has(EscaperExtension::class)->willReturn(false);
        $this->container->has(UrlHelper::class)->willReturn(true);
        $this->container->has(ServerUrlHelper::class)->willReturn(true);
        $this->container->get(UrlHelper::class)->willReturn($this->prophesize(UrlHelper::class)->reveal());
        $this->container->get(ServerUrlHelper::class)->willReturn($this->prophesize(ServerUrlHelper::class)->reveal());
    }

    public function fetchPlatesEngine(PlatesRenderer $plates)
    {
        $r = new ReflectionProperty($plates, 'template');
        $r->setAccessible(true);
        return $r->getValue($plates);
    }

    public function getConfigurationPaths()
    {
        return [
            'foo' => __DIR__ . '/TestAsset/bar',
            1 => __DIR__ . '/TestAsset/one',
            'bar' => [
                __DIR__ . '/TestAsset/baz',
                __DIR__ . '/TestAsset/bat',
            ],
            0 => [
                __DIR__ . '/TestAsset/two',
                __DIR__ . '/TestAsset/three',
            ],
        ];
    }

    public function assertPathsHasNamespace($namespace, array $paths, $message = null)
    {
        $message = $message ?: sprintf('Paths do not contain namespace %s', $namespace ?: 'null');

        $found = false;
        foreach ($paths as $path) {
            $this->assertInstanceOf(TemplatePath::class, $path, 'Non-TemplatePath found in paths list');
            if ($path->getNamespace() === $namespace) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, $message);
    }

    public function assertPathNamespaceCount($expected, $namespace, array $paths, $message = null)
    {
        $message = $message ?: sprintf('Did not find %d paths with namespace %s', $expected, $namespace ?: 'null');

        $count = 0;
        foreach ($paths as $path) {
            $this->assertInstanceOf(TemplatePath::class, $path, 'Non-TemplatePath found in paths list');
            if ($path->getNamespace() === $namespace) {
                $count += 1;
            }
        }
        $this->assertSame($expected, $count, $message);
    }

    public function assertPathNamespaceContains($expected, $namespace, array $paths, $message = null)
    {
        $message = $message ?: sprintf('Did not find path %s in namespace %s', $expected, $namespace ?: null);

        $found = [];
        foreach ($paths as $path) {
            $this->assertInstanceOf(TemplatePath::class, $path, 'Non-TemplatePath found in paths list');
            if ($path->getNamespace() === $namespace) {
                $found[] = $path->getPath();
            }
        }
        $this->assertContains($expected, $found, $message);
    }

    public function testCallingFactoryWithNoConfigReturnsPlatesInstance()
    {
        $this->container->has('config')->willReturn(false);
        $this->configureEngineService();
        $factory = new PlatesRendererFactory();
        $plates = $factory($this->container->reveal());
        $this->assertInstanceOf(PlatesRenderer::class, $plates);
        return $plates;
    }

    /**
     * @depends testCallingFactoryWithNoConfigReturnsPlatesInstance
     *
     * @param PlatesRenderer $plates
     */
    public function testUnconfiguredPlatesInstanceContainsNoPaths(PlatesRenderer $plates)
    {
        $paths = $plates->getPaths();
        $this->assertInternalType('array', $paths);
        $this->assertEmpty($paths);
    }

    public function testConfiguresTemplateSuffix()
    {
        $config = [
            'templates' => [
                'extension' => 'html',
            ],
        ];
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($config);
        $this->configureEngineService();
        $factory = new PlatesRendererFactory();
        $plates = $factory($this->container->reveal());

        $engine = $this->fetchPlatesEngine($plates);
        $r = new ReflectionProperty($engine, 'fileExtension');
        $r->setAccessible(true);
        $extension = $r->getValue($engine);
        $this->assertAttributeSame($config['templates']['extension'], 'fileExtension', $extension);
    }

    public function testExceptionIsRaisedIfMultiplePathsSpecifyDefaultNamespace()
    {
        $config = [
            'templates' => [
                'paths' => [
                    0 => __DIR__ . '/TestAsset/bar',
                    1 => __DIR__ . '/TestAsset/baz',
                ]
            ],
        ];
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($config);
        $this->configureEngineService();
        $factory = new PlatesRendererFactory();

        set_error_handler(function ($errno, $errstr) {
            $this->errorCaught = true;
        }, E_USER_WARNING);
        $factory($this->container->reveal());
        restore_error_handler();
        $this->assertTrue($this->errorCaught, 'Did not detect duplicate path for default namespace');
    }

    public function testExceptionIsRaisedIfMultiplePathsInSameNamespace()
    {
        $config = [
            'templates' => [
                'paths' => [
                    'bar' => [
                        __DIR__ . '/TestAsset/baz',
                        __DIR__ . '/TestAsset/bat',
                    ],
                ],
            ],
        ];
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($config);
        $this->configureEngineService();
        $factory = new PlatesRendererFactory();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('already being used');
        $factory($this->container->reveal());
    }

    public function testConfiguresPaths()
    {
        $config = [
            'templates' => [
                'paths' => [
                    'foo' => __DIR__ . '/TestAsset/bar',
                    1 => __DIR__ . '/TestAsset/one',
                    'bar' => __DIR__ . '/TestAsset/baz',
                ],
            ],
        ];
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($config);
        $this->configureEngineService();
        $factory = new PlatesRendererFactory();
        $plates = $factory($this->container->reveal());

        $paths = $plates->getPaths();
        $this->assertPathsHasNamespace('foo', $paths);
        $this->assertPathsHasNamespace('bar', $paths);
        $this->assertPathsHasNamespace(null, $paths);

        $this->assertPathNamespaceCount(1, 'foo', $paths);
        $this->assertPathNamespaceCount(1, 'bar', $paths);
        $this->assertPathNamespaceCount(1, null, $paths);

        $this->assertPathNamespaceContains(__DIR__ . '/TestAsset/bar', 'foo', $paths);
        $this->assertPathNamespaceContains(__DIR__ . '/TestAsset/baz', 'bar', $paths);
        $this->assertPathNamespaceContains(__DIR__ . '/TestAsset/one', null, $paths);
    }

    public function testWillPullPlatesEngineFromContainerIfPresent()
    {
        $engine = $this->prophesize(PlatesEngine::class);
        $this->container->has(PlatesEngine::class)->willReturn(true);
        $this->container->get(PlatesEngine::class)->willReturn($engine->reveal());

        $this->container->has('config')->willReturn(false);

        $factory = new PlatesRendererFactory();
        $renderer = $factory($this->container->reveal());
        $this->assertAttributeSame($engine->reveal(), 'template', $renderer);
    }
}
