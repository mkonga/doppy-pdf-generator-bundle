<?php

namespace Doppy\PdfGeneratorBundle\PreProcessor;

class OddEvenPreProcessor implements PreProcessorInterface
{
    /**
     * @var string[][]
     */
    protected $tags = [];

    /**
     * OddEvenPreProcessor constructor.
     *
     * @param \string[][] $tags
     */
    public function __construct(array $tags)
    {
        $this->tags = $tags;
    }

    public function process($htmlDocument)
    {
        // loop through replace-actions
        foreach ($this->tags as $tag => $subTag) {
            $this->replaceForTag($htmlDocument, $tag, $subTag);
        }

        // return the html
        return $htmlDocument;
    }

    /**
     * @param \DOMDocument $htmlDocument
     * @param string       $tag
     * @param string       $subTag
     */
    protected function replaceForTag($htmlDocument, $tag, $subTag)
    {
        // find tags that might need some work
        $tags = $htmlDocument->getElementsByTagName($tag);
        foreach ($tags as $tagElement) {
            // reset odd
            $odd = true;

            foreach ($tagElement->childNodes as $childNode) {
                if ($childNode->nodeName == $subTag) {
                    $this->addClass($childNode, $odd);
                    $odd = !$odd;
                }
            }
        }
    }

    /**
     * @param \DomElement $element
     * @param bool        $odd
     */
    protected function addClass(\DomElement $element, $odd)
    {
        $class = '';
        if ($element->hasAttribute('class')) {
            $class = $element->getAttribute('class');
        }

        // add our class
        if ($odd) {
            $class .= ' odd';
        } else {
            $class .= ' even';
        }

        // set new class
        $element->setAttribute('class', trim($class));
    }
}
