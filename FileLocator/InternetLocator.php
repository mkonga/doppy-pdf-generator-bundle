<?php

namespace Doppy\PdfGeneratorBundle\FileLocator;

use Doppy\PdfGeneratorBundle\TempFileGenerator\TempFileGenerator;
use Symfony\Component\Routing\RequestContext;

class InternetLocator implements FileLocatorInterface
{
    /**
     * @var TempFileGenerator
     */
    protected $tempFileGenerator;

    /**
     * @var RequestContext
     */
    protected $requestContext;

    /**
     * InternetLocator constructor.
     *
     * @param TempFileGenerator $tempFileGenerator
     */
    public function __construct(TempFileGenerator $tempFileGenerator, RequestContext $requestContext)
    {
        $this->tempFileGenerator = $tempFileGenerator;
        $this->requestContext    = $requestContext;
    }

    public function locate($source)
    {
        // try and match
        $source = $this->matches($source);
        if ($source !== false) {
            // open source
            $sourceHandler = @fopen($source, 'r');
            if ($sourceHandler === false) {
                // but that failed, probably does not exist
                return false;
            }

            // create tempFile
            $tempFile = $this->tempFileGenerator->getTempFileName('doppy_pdf_generator.internet_locator');

            // put the contents in it
            file_put_contents($tempFile, $sourceHandler);

            // return where it is located
            return $tempFile;
        }
        return false;
    }

    /**
     * Checks if the source can be handled by this Locator and returns the url to use if true
     *
     * @param string $source
     *
     * @return bool|string
     */
    protected function matches($source) {
        // is it a complete scheme?
        if ((substr($source, 0, 7) == 'http://') ||
            (substr($source, 0, 8) == 'https://')) {
            return $source;
        }

        // is it a scheme-less source?
        if (substr($source, 0, 2) == '//') {
            // prepend it what the requestContext knows
            return $this->requestContext->getScheme() . ':' . $source;
        }

        // it did not match
        return false;
    }
}
