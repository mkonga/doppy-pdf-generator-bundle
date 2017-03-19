<?php

namespace Doppy\PdfGeneratorBundle\DependencyInjection\CompilerPass;

use Doppy\UtilBundle\Helper\CompilerPass\BaseTagServiceCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class PreProcessorCompilerPass extends BaseTagServiceCompilerPass
{
    protected function handleTag(
        ContainerBuilder $containerBuilder,
        Definition $serviceDefinition,
        Reference $taggedServiceReference,
        $attributes
    )
    {
        $serviceDefinition->addMethodCall(
            'addProcessor',
            array(
                $taggedServiceReference
            )
        );
    }

    protected function getService(ContainerBuilder $containerBuilder)
    {
        return $containerBuilder->getDefinition('doppy_pdf_generator.pre_processor');
    }

    protected function getTaggedServices(ContainerBuilder $containerBuilder)
    {
        return $containerBuilder->findTaggedServiceIds('doppy_pdf_generator.pre_processor');
    }
}
