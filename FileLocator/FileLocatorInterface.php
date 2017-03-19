<?php

namespace Doppy\PdfGeneratorBundle\FileLocator;

interface FileLocatorInterface
{
    /**
     * attempts to locate a file based on the given source string.
     * If it knows how to locate it, it should return a string with the filename where it was stored locally.
     *
     * @param string $source
     *
     * @return string|bool
     */
    public function locate($source);
}
