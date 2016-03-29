<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @see       https://github.com/zendframework/zend-expressive-platesrenderer for the canonical source repository
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-platesrenderer/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\Expressive\Plates\Extension;

use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Expressive\Helper\ServerUrlHelper;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Plates\Exception\MissingHelperException;
use Zend\Expressive\Plates\Extension\UrlExtension;
use Zend\Expressive\Plates\Extension\UrlExtensionFactory;

class UrlExtensionFactoryTest extends TestCase
{
    public function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->urlHelper = $this->prophesize(UrlHelper::class);
        $this->serverUrlHelper = $this->prophesize(ServerUrlHelper::class);
    }

    public function testFactoryReturnsUrlExtensionInstanceWhenHelpersArePresent()
    {
        $this->container->has(UrlHelper::class)->willReturn(true);
        $this->container->get(UrlHelper::class)->willReturn($this->urlHelper->reveal());
        $this->container->has(ServerUrlHelper::class)->willReturn(true);
        $this->container->get(ServerUrlHelper::class)->willReturn($this->serverUrlHelper->reveal());

        $factory = new UrlExtensionFactory();
        $extension = $factory($this->container->reveal());
        $this->assertInstanceOf(UrlExtension::class, $extension);

        $this->assertAttributeSame($this->urlHelper->reveal(), 'urlHelper', $extension);
        $this->assertAttributeSame($this->serverUrlHelper->reveal(), 'serverUrlHelper', $extension);
    }

    public function testFactoryRaisesExceptionIfUrlHelperIsMissing()
    {
        $this->container->has(UrlHelper::class)->willReturn(false);
        $this->container->get(UrlHelper::class)->shouldNotBeCalled();
        $this->container->has(ServerUrlHelper::class)->shouldNotBeCalled();
        $this->container->get(ServerUrlHelper::class)->shouldNotBeCalled();

        $factory = new UrlExtensionFactory();

        $this->setExpectedException(MissingHelperException::class, UrlHelper::class);
        $factory($this->container->reveal());
    }

    public function testFactoryRaisesExceptionIfServerUrlHelperIsMissing()
    {
        $this->container->has(UrlHelper::class)->willReturn(true);
        $this->container->get(UrlHelper::class)->shouldNotBeCalled();
        $this->container->has(ServerUrlHelper::class)->willReturn(false);
        $this->container->get(ServerUrlHelper::class)->shouldNotBeCalled();

        $factory = new UrlExtensionFactory();

        $this->setExpectedException(MissingHelperException::class, ServerUrlHelper::class);
        $factory($this->container->reveal());
    }
}
