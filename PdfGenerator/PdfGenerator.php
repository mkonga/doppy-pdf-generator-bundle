<?php

namespace Doppy\PdfGeneratorBundle\PdfGenerator;

use Doppy\PdfGeneratorBundle\FileGenerator\PdfFileGenerator;
use Doppy\PdfGeneratorBundle\PreProcessor\PreProcessor;

class PdfGenerator
{
    /**
     * @var PdfFileGenerator
     */
    protected $pdfFileGenerator;

    /**
     * @var PreProcessor
     */
    private $preProcessor;

    /**
     * PdfGenerator constructor.
     *
     * @param PdfFileGenerator $pdfFileGenerator
     * @param PreProcessor     $preProcessor
     */
    public function __construct(PdfFileGenerator $pdfFileGenerator, PreProcessor $preProcessor)
    {
        $this->pdfFileGenerator = $pdfFileGenerator;
        $this->preProcessor     = $preProcessor;
    }

    /**
     * @param string[]|string $html       html to generate the pdf from
     * @param string          $targetFile file where to write the pdf to; leave empty to return the content
     *
     * @param string          $encoding   set the html (input) and pdf (output) encoding, defaults to UTF-8
     *
     * @return string
     */
    public function generatePdf($html, $targetFile = null, $encoding = 'UTF-8')
    {
        // make sure we got an array
        if (!is_array($html)) {
            $html = [$html];
        }

        // run the pre-processors
        foreach ($html as $key => $current) {
            $html[$key] = $this->preProcessor->process($current);
        }

        // generate the pdf
        return $this->pdfFileGenerator->generatePdf($html, $targetFile, $encoding);
    }
}