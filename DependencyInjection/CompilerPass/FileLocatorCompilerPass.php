<?php

namespace Doppy\PdfGeneratorBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FileLocatorCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public function process(ContainerBuilder $container)
    {
        $fileLocatorDefinition = $container->getDefinition('doppy_pdf_generator.file_locator');
        $fileLocators = $this->findAndSortTaggedServices('doppy_pdf_generator.file_locator', $container);

        foreach ($fileLocators as $fileLocator) {
            $fileLocatorDefinition->addMethodCall(
                'addLocator',
                [$fileLocator]
            );
        }
    }
}
