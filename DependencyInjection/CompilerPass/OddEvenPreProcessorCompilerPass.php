<?php

namespace Doppy\PdfGeneratorBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class OddEvenPreProcessorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // remove the odd_even preprocessor when it is not configured
        if ((!$container->hasParameter('doppy_pdf_generator.preprocessor.oddeven_config')) ||
            (count($container->getParameter('doppy_pdf_generator.preprocessor.oddeven_config')) == 0)
        ) {
            $container->removeDefinition('doppy_pdf_generator.pre_processor.oddeven');
        }
    }
}
