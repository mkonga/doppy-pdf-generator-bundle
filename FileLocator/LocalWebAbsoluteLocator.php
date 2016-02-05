<?php

namespace Doppy\PdfGeneratorBundle\FileLocator;

use Symfony\Component\Routing\RequestContext;

class LocalWebAbsoluteLocator extends LocalWebLocator
{
    /**
     * @var RequestContext
     */
    protected $requestContext;

    public function __construct(RequestContext $requestContext, $webRoot)
    {
        parent::__construct($webRoot, []);
        $this->requestContext = $requestContext;
    }

    protected function getRequiredPrefixes()
    {
        return [
            $this->requestContext->getScheme() . '://' . $this->requestContext->getHost() . '/',
            '//' . $this->requestContext->getHost() . '/'
        ];
    }
}