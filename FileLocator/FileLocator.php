<?php

namespace Doppy\PdfGeneratorBundle\FileLocator;

class FileLocator implements FileLocatorInterface
{
    /**
     * @var FileLocatorInterface[]
     */
    protected $locators;

    /**
     * @param FileLocatorInterface $fileLocator
     */
    public function addLocator(FileLocatorInterface $fileLocator)
    {
        $this->locators[] = $fileLocator;
    }

    public function locate($source)
    {
        foreach ($this->locators as $locator) {
            $locateResult = $locator->locate($source);
            if ($locateResult !== false) {
                return $locateResult;
            }
        }
        return false;
    }
}