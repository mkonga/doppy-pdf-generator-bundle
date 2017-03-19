<?php

namespace Doppy\PdfGeneratorBundle\PreProcessor;

use Doppy\PdfGeneratorBundle\FileLocator\FileLocator;

class SourceFilePreProcessor implements PreProcessorInterface
{
    /**
     * @var string[][]
     */
    protected $replace = array(
        'img'  => ['src'],
        'link' => ['href']
    );

    /**
     * @var FileLocator
     */
    protected $fileLocator;

    /**
     * SourceFilePreProcessor constructor.
     *
     * @param FileLocator $fileLocator
     */
    public function __construct(FileLocator $fileLocator)
    {
        $this->fileLocator = $fileLocator;
    }

    public function process($htmlDocument)
    {
        // loop through replace-actions
        foreach ($this->replace as $tag => $attributes) {
            $this->replaceForTag($htmlDocument, $tag, $attributes);
        }

        // return the html
        return $htmlDocument;
    }

    /**
     * @param \DOMDocument $htmlDocument
     * @param string       $tag
     * @param string[]     $attributes
     */
    protected function replaceForTag($htmlDocument, $tag, $attributes)
    {
        // find tags that might need some work
        $tags = $htmlDocument->getElementsByTagName($tag);
        foreach ($tags as $tagElement) {
            foreach ($attributes as $attribute) {
                $this->replaceForAttribute($tagElement, $attribute);
            }
        }
    }

    /**
     * Does an attempat at replacing the value of an attribute
     *
     * @param \DOMElement $tagElement
     * @param string $attribute
     */
    protected function replaceForAttribute($tagElement, $attribute) {
        if ($tagElement->hasAttribute($attribute)) {
            $replaceValue = $this->fileLocator->locate($tagElement->getAttribute($attribute));
            if ($replaceValue !== false) {
                $tagElement->setAttribute($attribute, $replaceValue);
            } else {
                $tagElement->removeAttribute($attribute);
            }
        }
    }
}
