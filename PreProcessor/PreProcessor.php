<?php

namespace Doppy\PdfGeneratorBundle\PreProcessor;

class PreProcessor
{
    /**
     * @var PreProcessorInterface[]
     */
    protected $processors;

    /**
     * @param PreProcessorInterface $processor
     */
    public function addProcessor(PreProcessorInterface $processor)
    {
        $this->processors[] = $processor;
    }

    /**
     * Runs the configured preprocessors on the passed html.
     *
     * @param string $html
     *
     * @return string
     */
    public function process($html)
    {
        // get our document
        $htmlDocument = new \DOMDocument();
        $htmlDocument->loadHTML($html);

        // run processors
        $htmlDocument = $this->processDocument($htmlDocument);

        // return the html
        return $htmlDocument->saveHTML();
    }

    /**
     * Runs the configured preprocessors on the passed DomDocument.
     *
     * @param \DomDocument $domDocument
     *
     * @return \DomDocument
     */
    protected function processDocument(\DomDocument $domDocument)
    {
        foreach ($this->processors as $processor) {
            $domDocument = $processor->process($domDocument);
        }
        return $domDocument;
    }
}