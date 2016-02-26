<?php

namespace Doppy\PdfGeneratorBundle;

use Doppy\PdfGeneratorBundle\DependencyInjection\CompilerPass\FileLocatorCompilerPass;
use Doppy\PdfGeneratorBundle\DependencyInjection\CompilerPass\OddEvenPreProcessorCompilerPass;
use Doppy\PdfGeneratorBundle\DependencyInjection\CompilerPass\PreProcessorCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DoppyPdfGeneratorBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new OddEvenPreProcessorCompilerPass());
        $container->addCompilerPass(new PreProcessorCompilerPass());
        $container->addCompilerPass(new FileLocatorCompilerPass());
    }
}
