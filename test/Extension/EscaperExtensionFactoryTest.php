<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-platesrenderer for the canonical source repository
 * @copyright Copyright (c) 2016-2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-platesrenderer/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\Expressive\Plates\Extension;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ProphecyInterface;
use Psr\Container\ContainerInterface;
use Zend\Escaper\Escaper;
use Zend\Escaper\Exception\InvalidArgumentException;
use Zend\Expressive\Helper\ServerUrlHelper;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Plates\Exception\MissingHelperException;
use Zend\Expressive\Plates\Extension\EscaperExtension;
use Zend\Expressive\Plates\Extension\EscaperExtensionFactory;
use Zend\Expressive\Plates\Extension\UrlExtension;
use Zend\Expressive\Plates\Extension\UrlExtensionFactory;

class EscaperExtensionFactoryTest extends TestCase
{
    /** @var ContainerInterface|ProphecyInterface */
    private $container;

    public function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
    }

    public function testFactoryWithoutConfig()
    {
        $this->container->has('config')->willReturn(false);

        $factory = new EscaperExtensionFactory();
        $extension = $factory($this->container->reveal());

        $this->assertInstanceOf(EscaperExtension::class, $extension);
    }

    public function testFactoryWithEmptyConfig()
    {
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn([]);

        $factory = new EscaperExtensionFactory();
        $extension = $factory($this->container->reveal());

        $this->assertInstanceOf(EscaperExtension::class, $extension);
    }

    public function testFactoryWithInvalidEncodingSetIn()
    {
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn([
            'plates' => [
                'encoding' => ''
            ]
        ]);

        $factory = new EscaperExtensionFactory();

        $this->expectException(InvalidArgumentException::class);
        $factory($this->container->reveal());
    }

    /**
     * @depends testFactoryWithInvalidEncodingSetIn
     */
    public function testFactoryWithValidEncodingSetIn()
    {
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn([
            'plates' => [
                'encoding' => 'iso-8859-1'
            ]
        ]);

        $factory = new EscaperExtensionFactory();
        $extension = $factory($this->container->reveal());

        $this->assertInstanceOf(EscaperExtension::class, $extension);
        $this->assertAttributeInstanceOf(Escaper::class, 'escaper', $extension);

        $class = new \ReflectionClass($extension);
        $escaper = $class->getProperty('escaper');
        $escaper->setAccessible(true);
        $escaper = $escaper->getValue($extension);

        $this->assertEquals('iso-8859-1', $escaper->getEncoding());
    }
}
