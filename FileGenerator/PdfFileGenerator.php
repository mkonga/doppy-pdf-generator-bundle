<?php

namespace Doppy\PdfGeneratorBundle\FileGenerator;

use Doppy\UtilBundle\TempFileGenerator\TempFileGenerator;
use Symfony\Component\HttpKernel\Config\FileLocator;

class PdfFileGenerator
{
    /**
     * @var FileLocator
     */
    protected $fileLocator;

    /**
     * @var TempFileGenerator
     */
    protected $tempFileGenerator;

    /**
     * @param FileLocator       $fileLocator
     * @param TempFileGenerator $tempFileGenerator
     */
    function __construct(FileLocator $fileLocator, TempFileGenerator $tempFileGenerator)
    {
        $this->fileLocator       = $fileLocator;
        $this->tempFileGenerator = $tempFileGenerator;
    }

    /**
     * Generates a pdf
     *
     * @param string[]|string $html       html to generate the pdf from, can be an array, the parts will be merged into one pdf
     * @param string          $targetFile file where to write the pdf to; leave empty to return the content
     *
     * @param string          $encoding   set the html (input) and pdf (output) encoding, defaults to UTF-8
     *
     * @return bool|string
     */
    public function generatePdf($html, $targetFile = null, $encoding = 'UTF-8')
    {
        // make sure html is a array
        if (is_string($html)) {
            $html = [$html];
        }

        // prepare file containing all filenames
        $fileListFile   = $this->tempFileGenerator->getTempFileName('doppy_pdf_generator.files', true);

        foreach ($html as $content) {
            // put html in a file
            $htmlFile = $this->tempFileGenerator->getTempFileName('doppy_pdf_generator.html_content', true);
            file_put_contents($htmlFile, $content);

            // append filename to our list
            file_put_contents($fileListFile, $htmlFile . PHP_EOL, FILE_APPEND);
        }

        // prepare output file
        if (empty($targetFile)) {
            $pdfFile = $this->tempFileGenerator->getTempFileName('doppy_pdf_generator.pdf_output', true);
        } else {
            $pdfFile = $targetFile;
        }

        // generate the pdf
        $this->generate($fileListFile, $pdfFile, $encoding);

        // return result or not
        if (empty($targetFile)) {
            return file_get_contents($pdfFile);
        } else {
            return true;
        }
    }

    /**
     * @param        $htmlFile - the temporary html files the pdf is generated from
     * @param string $pdfFile  - the temporaray pdf file which the stream will be written to
     *
     * @param string $encoding - set the html (input) and pdf (output) encoding
     *
     * @return string
     */
    protected function generate($htmlFile, $pdfFile, $encoding)
    {
        // build command to call the pdf library
        $command = $this->getCommand($htmlFile, $pdfFile, $encoding);

        list($status, $stdout, $stderr) = $this->executeCommand($command);
        if ($status !== 0) {
            throw new \RuntimeException(sprintf(
                'The exit status code \'%s\' says something went wrong:' . "\n"
                . 'stderr: "%s"' . "\n"
                . 'stdout: "%s"' . "\n"
                . 'command: %s.',
                $status, $stderr, $stdout, $command
            ));
        }
    }

    /**
     * @param        $htmlFile - the temporary html file the pdf is generated from
     * @param string $pdfFile  - the temporaray pdf file which the stream will be written to
     *
     * @param string $encoding - set the html (input) and pdf (output) encoding
     *
     * @return string
     */
    protected function getCommand($htmlFile, $pdfFile, $encoding)
    {
        $resource = '@DoppyPdfGeneratorBundle/Resources/java/pdf-generator.jar';

        try {
            $path = $this->fileLocator->locate($resource);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(sprintf('Unable to load "%s"', $resource), 0, $e);
        }

        $command = 'java -Djava.awt.headless=true -jar ';
        $command .= '"' . $path . '"';
        $command .= ' --html "' . $htmlFile . '" --pdf "' . $pdfFile . '"';
        $command .= ' --encoding ' . $encoding;

        return $command;
    }

    /**
     * @param string $command - the command which will be executed to generate the pdf
     *
     * @return array
     */
    protected function executeCommand($command)
    {
        $stdout         = $stderr = $status = null;
        $pipes          = array();
        $descriptorspec = array(
            // stdout is a pipe that the child will write to
            1 => array('pipe', 'w'),
            // stderr is a pipe that the child will write to
            2 => array('pipe', 'w')
        );

        $process = proc_open($command, $descriptorspec, $pipes);

        if (is_resource($process)) {
            // $pipes now looks like this:
            // 0 => writeable handle connected to child stdin
            // 1 => readable handle connected to child stdout
            // 2 => readable handle connected to child stderr

            $stdout = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            // It is important that you close any pipes before calling
            // proc_close in order to avoid a deadlock
            $status = proc_close($process);
        }

        return array($status, $stdout, $stderr);
    }
}
