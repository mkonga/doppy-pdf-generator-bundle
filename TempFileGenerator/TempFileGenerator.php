<?php

namespace Doppy\PdfGeneratorBundle\TempFileGenerator;

use Doppy\PdfGeneratorBundle\Exception\TempFileGenerationException;
use Doppy\PdfGeneratorBundle\Listener\TempFileCleanupListener;

class TempFileGenerator
{
    /**
     * @var TempFileCleanupListener
     */
    protected $cleanupListener;

    /**
     * @var string
     */
    protected $path;

    /**
     * InternetLocator constructor.
     *
     * @param null                    $path
     * @param TempFileCleanupListener $cleanupListener
     */
    public function __construct($path = null, TempFileCleanupListener $cleanupListener = null)
    {
        $this->path            = $path;
        $this->cleanupListener = $cleanupListener;
    }

    /**
     * Returns the path for a tempfile. The system temp dir will be used as base location.
     *
     * @param string $prefix           prefix of the filename
     * @param bool   $removeOnShutDown set to true if the file is to be delete on shutdown
     *
     * @return string
     */
    public function getTempFileName($prefix = '', $removeOnShutDown = true)
    {
        // attempt to generate tempfile
        $tempFile = tempnam($this->getTempDir(), $prefix);

        // check if that worked
        if ($tempFile === false) {
            throw new TempFileGenerationException('Unable to generate tempfile');
        }

        // maybe remove it on terminate
        if (($removeOnShutDown) && ($this->cleanupListener)) {
            $this->cleanupListener->addFile($tempFile);
        }

        // return result
        return $tempFile;
    }

    /**
     * @return string
     */
    protected function getTempDir()
    {
        if ($this->path === false) {
            $this->path = sys_get_temp_dir();
        }
        return $this->path;
    }
}
