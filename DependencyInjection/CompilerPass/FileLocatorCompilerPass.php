<?php

namespace Doppy\PdfGeneratorBundle\DependencyInjection\CompilerPass;

use Doppy\UtilBundle\Helper\CompilerPass\TaggedServicesTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class FileLocatorCompilerPass implements CompilerPassInterface
{
    use TaggedServicesTrait;

    public function process(ContainerBuilder $container)
    {
        $this->processTaggedServices($container, 'doppy_pdf_generator.file_locator', 'addLocator');
    }
}