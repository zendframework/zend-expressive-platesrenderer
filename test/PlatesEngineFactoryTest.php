<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-platesrenderer for the canonical source repository
 * @copyright Copyright (c) 2016-2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-platesrenderer/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\Expressive\Plates;

use League\Plates\Engine as PlatesEngine;
use League\Plates\Extension\ExtensionInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ProphecyInterface;
use Psr\Container\ContainerInterface;
use stdClass;
use Zend\Expressive\Helper\ServerUrlHelper;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Plates\Exception\InvalidExtensionException;
use Zend\Expressive\Plates\Extension\EscaperExtension;
use Zend\Expressive\Plates\Extension\UrlExtension;
use Zend\Expressive\Plates\PlatesEngineFactory;

use function is_string;

class PlatesEngineFactoryTest extends TestCase
{
    /** @var ContainerInterface|ProphecyInterface */
    private $container;

    public function setUp()
    {
        TestAsset\TestExtension::$engine = null;
        $this->container = $this->prophesize(ContainerInterface::class);

        $this->container->has(UrlHelper::class)->willReturn(true);
        $this->container->get(UrlHelper::class)->willReturn(
            $this->prophesize(UrlHelper::class)->reveal()
        );

        $this->container->has(ServerUrlHelper::class)->willReturn(true);
        $this->container->get(ServerUrlHelper::class)->willReturn(
            $this->prophesize(ServerUrlHelper::class)->reveal()
        );

        $this->container->has(UrlExtension::class)->willReturn(false);
        $this->container->has(EscaperExtension::class)->willReturn(false);
    }

    public function testFactoryReturnsPlatesEngine()
    {
        $this->container->has('config')->willReturn(false);
        $factory = new PlatesEngineFactory();
        $engine = $factory($this->container->reveal());
        $this->assertInstanceOf(PlatesEngine::class, $engine);
        return $engine;
    }

    /**
     * @depends testFactoryReturnsPlatesEngine
     *
     * @param PlatesEngine $engine
     */
    public function testUrlExtensionIsRegisteredByDefault(PlatesEngine $engine)
    {
        $this->assertTrue($engine->doesFunctionExist('url'));
        $this->assertTrue($engine->doesFunctionExist('serverurl'));
    }

    /**
     * @depends testFactoryReturnsPlatesEngine
     *
     * @param PlatesEngine $engine
     */
    public function testEscaperExtensionIsRegisteredByDefault(PlatesEngine $engine)
    {
        $this->assertTrue($engine->doesFunctionExist('escapeHtml'));
        $this->assertTrue($engine->doesFunctionExist('escapeHtmlAttr'));
        $this->assertTrue($engine->doesFunctionExist('escapeJs'));
        $this->assertTrue($engine->doesFunctionExist('escapeCss'));
        $this->assertTrue($engine->doesFunctionExist('escapeUrl'));
    }

    /**
     * @depends testEscaperExtensionIsRegisteredByDefault
     */
    public function testEscaperExtensionIsRegisteredFromContainer()
    {
        $escaperExtension = new EscaperExtension();

        $this->container->has(EscaperExtension::class)->willReturn(true);
        $this->container->has('config')->willReturn(false);

        $this->container->get(EscaperExtension::class)->willReturn($escaperExtension);

        $factory = new PlatesEngineFactory();
        $engine = $factory($this->container->reveal());

        $this->assertTrue($engine->doesFunctionExist('escapeHtml'));
        $this->assertTrue($engine->doesFunctionExist('escapeHtmlAttr'));
        $this->assertTrue($engine->doesFunctionExist('escapeJs'));
        $this->assertTrue($engine->doesFunctionExist('escapeCss'));
        $this->assertTrue($engine->doesFunctionExist('escapeUrl'));
    }


    public function testFactoryCanRegisterConfiguredExtensions()
    {
        $extensionOne = $this->prophesize(ExtensionInterface::class);
        $extensionOne->register(Argument::type(PlatesEngine::class))->shouldBeCalled();

        $extensionTwo = $this->prophesize(ExtensionInterface::class);
        $extensionTwo->register(Argument::type(PlatesEngine::class))->shouldBeCalled();
        $this->container->has('ExtensionTwo')->willReturn(true);
        $this->container->get('ExtensionTwo')->willReturn($extensionTwo->reveal());

        $this->container->has(TestAsset\TestExtension::class)->willReturn(false);

        $config = [
            'plates' => [
                'extensions' => [
                    $extensionOne->reveal(),
                    'ExtensionTwo',
                    TestAsset\TestExtension::class,
                ],
            ],
        ];
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($config);

        $factory = new PlatesEngineFactory();
        $engine = $factory($this->container->reveal());
        $this->assertInstanceOf(PlatesEngine::class, $engine);

        // Test that the TestExtension was registered. The other two extensions
        // are verified via mocking.
        $this->assertSame($engine, TestAsset\TestExtension::$engine);
    }

    public function invalidExtensions()
    {
        return [
            'null' => [null],
            'true' => [true],
            'false' => [false],
            'zero' => [0],
            'int' => [1],
            'zero-float' => [0.0],
            'float' => [1.1],
            'non-class-string' => ['not-a-class'],
            'array' => [['not-an-extension']],
            'non-extension-object' => [(object) ['extension' => 'not-really']],
        ];
    }

    /**
     * @dataProvider invalidExtensions
     *
     * @param mixed $extension
     */
    public function testFactoryRaisesExceptionForInvalidExtensions($extension)
    {
        $config = [
            'plates' => [
                'extensions' => [
                    $extension,
                ],
            ],
        ];
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($config);

        if (is_string($extension)) {
            $this->container->has($extension)->willReturn(false);
        }

        $factory = new PlatesEngineFactory();
        $this->expectException(InvalidExtensionException::class);
        $factory($this->container->reveal());
    }

    public function testFactoryRaisesExceptionWhenAttemptingToInjectAnInvalidExtensionService()
    {
        $config = [
            'plates' => [
                'extensions' => [
                    'FooExtension',
                ],
            ],
        ];
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($config);

        $this->container->has('FooExtension')->willReturn(true);
        $this->container->get('FooExtension')->willReturn(new stdClass());

        $factory = new PlatesEngineFactory();
        $this->expectException(InvalidExtensionException::class);
        $this->expectExceptionMessage('ExtensionInterface');
        $factory($this->container->reveal());
    }

    public function testFactoryRaisesExceptionWhenNonServiceClassIsAnInvalidExtension()
    {
        $config = [
            'plates' => [
                'extensions' => [
                    stdClass::class,
                ],
            ],
        ];
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($config);

        $this->container->has(stdClass::class)->willReturn(false);

        $factory = new PlatesEngineFactory();
        $this->expectException(InvalidExtensionException::class);
        $this->expectExceptionMessage('ExtensionInterface');
        $factory($this->container->reveal());
    }

    public function provideHelpersToUnregister()
    {
        return [
            'url-only' => [[UrlHelper::class]],
            'server-url-only' => [[ServerUrlHelper::class]],
            'both' => [[ServerUrlHelper::class, UrlHelper::class]],
        ];
    }

    /**
     * @dataProvider provideHelpersToUnregister
     *
     * @param array $helpers
     */
    public function testUrlExtensionIsNotLoadedIfHelpersAreNotRegistered(array $helpers)
    {
        $this->container->has('config')->willReturn(false);
        foreach ($helpers as $helper) {
            $this->container->has($helper)->willReturn(false);
        }

        $factory = new PlatesEngineFactory();
        $engine = $factory($this->container->reveal());

        $this->assertFalse($engine->doesFunctionExist('url'));
        $this->assertFalse($engine->doesFunctionExist('serverurl'));
    }
}
