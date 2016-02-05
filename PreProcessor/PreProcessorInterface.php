<?php

namespace Doppy\PdfGeneratorBundle\PreProcessor;

interface PreProcessorInterface
{
    /**
     * Prepares the html for rendering by the PDF generator
     *
     * @param \DomDocument $htmlDocumentDocument
     *
     * @return \DomDocument
     */
    public function process($htmlDocumentDocument);
}