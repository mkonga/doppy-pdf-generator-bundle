<?php

namespace Doppy\PdfGeneratorBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PreProcessorCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public function process(ContainerBuilder $container)
    {
        $preProcessorDefinition = $container->getDefinition('doppy_pdf_generator.pre_processor');
        $preProcessors          = $this->findAndSortTaggedServices('doppy_pdf_generator.pre_processor', $container);

        foreach ($preProcessors as $preProcessor) {
            $preProcessorDefinition->addMethodCall(
                'addProcessor',
                [$preProcessor]
            );
        }
    }
}
