<?php

namespace Doppy\PdfGeneratorBundle\DependencyInjection;

use Doppy\PdfGeneratorBundle\FileLocator\FileLocatorInterface;
use Doppy\PdfGeneratorBundle\PreProcessor\PreProcessorInterface;
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

        $this->loadPreProcessor($config['preprocessor'], $container);
        $this->loadTempFile($config['temp_file'], $container);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    private function loadPreProcessor($preProcessorConfig, ContainerBuilder $container)
    {
        // odd even configuration
        $oddEvenConfig = [];
        if (isset($preProcessorConfig['oddeven'])) {
            $oddEvenConfig = $preProcessorConfig['oddeven'];
        }
        $container->setParameter('doppy_pdf_generator.preprocessor.oddeven_config', $oddEvenConfig);
    }

    private function loadTempFile($tempfileConfig, ContainerBuilder $container)
    {
        // check config
        if ($tempfileConfig['path'] !== false) {
            if (!is_dir($tempfileConfig['path'])) {
                throw new \Exception(
                    sprintf('temp_file path ("%s") is not a directory', $tempfileConfig['path'])
                );
            }
            if (!is_writeable($tempfileConfig['path'])) {
                throw new \Exception(
                    sprintf('temp_file path is not writable', $tempfileConfig['path'])
                );
            }
        }

        // add parameters
        $container->setParameter('doppy_pdf_generator.temp_file.path', $tempfileConfig['path']);
        $container->setParameter('doppy_pdf_generator.temp_file.cleanup_on_terminate', $tempfileConfig['cleanup_on_terminate']);

        if ($tempfileConfig['cleanup_on_terminate']) {
            $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
            $loader->load('services.temp_file.cleanup.listener.yml');
        }

        // register interfaces for autowire
        $container->registerForAutoconfiguration(FileLocatorInterface::class)->addTag('doppy_pdf_generator.file_locator');
        $container->registerForAutoconfiguration(PreProcessorInterface::class)->addTag('doppy_pdf_generator.pre_processor');
    }
}
