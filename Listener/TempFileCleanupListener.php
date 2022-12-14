<?php

namespace Doppy\PdfGeneratorBundle\Listener;

class TempFileCleanupListener
{
    /**
     * @var string[]
     */
    protected $files = [];

    /**
     * Adds a temporary file to be deleted at the end of the request
     *
     * @param string|string[] $filename
     */
    public function addFile($filename)
    {
        $this->files[] = $filename;
    }

    /**
     * Removes all temp files at the end of the request
     */
    public function onKernelTerminate()
    {
        if (count($this->files) > 0) {
            $this->removeFiles($this->files);
        }
    }

    /**
     * Removes files that are no longer needed
     *
     * @param string[] $files
     */
    protected function removeFiles($files)
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($files));
        foreach ($iterator as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
}
