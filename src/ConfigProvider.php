<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-platesrenderer for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-platesrenderer/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Expressive\Plates;

use Zend\Expressive\Template\TemplateRendererInterface;

class ConfigProvider
{
    public function __invoke() : array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates' => $this->getTemplates(),
        ];
    }

    public function getDependencies() : array
    {
        return [
            'aliases' => [
                TemplateRendererInterface::class => PlatesRenderer::class,
            ],
            'factories' => [
                PlatesRenderer::class => PlatesRendererFactory::class,
            ],
        ];
    }

    public function getTemplates() : array
    {
        return [
            'extension' => 'phtml',
            'paths' => [],
        ];
    }
}
