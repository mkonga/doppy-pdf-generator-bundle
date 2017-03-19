<?php

namespace Doppy\PdfGeneratorBundle\FileLocator;

class LocalWebLocator implements FileLocatorInterface
{
    /**
     * @var string
     */
    protected $webRoot;

    /**
     * @var string[]
     */
    protected $requiredPrefixes = [];

    /**
     * @param string $webRoot
     * @param string $requiredPrefixes
     */
    public function __construct($webRoot, $requiredPrefixes)
    {
        $this->webRoot          = realpath($webRoot);
        $this->requiredPrefixes = $requiredPrefixes;
    }

    public function locate($source)
    {
        // sanity check; the webRoot must exist
        if ($this->webRoot === false) {
            return false;
        }

        // does it match the required prefix?
        $source = $this->matchesRequiredPrefix($source);
        if ($source !== false) {
            // remove any trailing parameters
            $source = $this->stripTrailingParameters($source);

            // generate the new complete local path
            $complete = realpath($this->webRoot . '/' . $source);

            // check if the new complete path is actually within the original webroot ('/../')
            if (substr($complete, 0, strlen($this->webRoot)) != $this->webRoot) {
                return false;
            }

            // return what we found
            return $complete;
        }

        // seems we did not find it
        return false;
    }

    /**
     * Checks if a required prefix matches, and returns the source to use (stripped of prefix if requested)
     *
     * @param string $source
     *
     * @return string|false
     */
    protected function matchesRequiredPrefix($source)
    {
        // now look for the file
        foreach ($this->requiredPrefixes as $requiredPrefix) {
            if (substr($source, 0, strlen($requiredPrefix)) == $requiredPrefix) {
                return substr($source, strlen($requiredPrefix));
            }
        }
        return false;
    }

    /**
     * Checks if the source has a trailing ? or # and removes that part
     *
     * @param string $source
     *
     * @return string
     */
    protected function stripTrailingParameters($source)
    {
        foreach (['?', '#'] as $character) {
            if ($position = strpos($source, $character)) {
                return substr($source, 0, $position);
            }
        }
        return $source;
    }
}
