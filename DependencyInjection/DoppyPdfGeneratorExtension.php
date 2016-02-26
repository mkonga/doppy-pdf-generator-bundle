<?php

namespace Doppy\PdfGeneratorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class DoppyPdfGeneratorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        // odd even configuration
        $oddEvenConfig = [];
        if (isset($config['preprocessor']['oddeven'])) {
            $oddEvenConfig = $config['preprocessor']['oddeven'];
        }
        $container->setParameter('doppy_pdf_generator.preprocessor.oddeven_config', $oddEvenConfig);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}
