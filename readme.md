# DoppyPDFGeneratorBundle

A symfony bundle for generating PDF files from HTML.
This is based on the [Flying Saucer Project][1]. The bundle is inspired by the [SpreadPDFGeneratorBundle][2] and expanded for personal use.

## Requirements

* Java (6 or later) to run the jar for generating the pdf's.

## Installation

Add requirement to composer:
````
composer require doppy/pdf-generator-bundle
````

Add bundles to Symfony:
````
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Doppy\PdfGeneratorBundle\DoppyPdfGeneratorBundle(),
        // ...
    );
````

## Usage

### Simple PDF generating

````
$htmlContent  = '......'; // implement your own logic.
$pdfGenerator = $this->getContainer()->get('doppy_pdf_generator.pdf_generator');
$pdfContent  = $pdfGenerator->generate($htmlContent);
````

* Pass an array of html-strings to create 1 pdf document from multiple files.
* To directly write to disk, pass the filename as a second parameter.

### Formatting

Rendering a normal html page as pdf will probably not give you the result you want. You will need additional elements and a bit of different css to get nice pages.

Some resources that can get you started:

* [Flying Saucer on Github][4]
* [CSS Paged Media Module][3]

## Features

### Pre Processing

The PdfGenerator uses a preprocesor to adjust your html before rendering the PDF.
For this your html is converted to a `\DomDocument` object, this results in the requirement that your html must not contain any errors.
 
You can add your own PreProcessor by creating a service which implements `\Doppy\PdfGeneratorBundle\PreProcessor\PreProcessorInterface`.
Then tag this service with `doppy_pdf_generator.pre_processor`. See the `services.yml` file in this bundle for an example.

If you want to ignore any PreProcessors configured, use the service `doppy_pdf_generator.pdf_file_generator` instead.

### File Locator PreProcessor

There is a single PreProcessor configured out of the box. This PreProcessor will convert any `<img src>` and `<link href>` attributes to make sure the path is a path to a file on local disk.
This is because the java is expecting all source files to be on disk.

The big advantage of this is that you can use assetic. Simply render your html using twig+assetic as you would for any html page.
The PreProcessor will make sure that all css-files and assets used will have the correct local path.

The existing PreProcessor supports the following paths:

* absolute path on local disk. (is not changed).
* remote paths (http://, https://, //). These files are downloaded and temporarily stored in your temp dir.
* path to your application webroot dir. (must start with "/") (path is changed to the actual absolute path).

It is a good idea to make sure all your source files are stored locally when rendering a pdf, as remote files will be downloaded each time you generate a pdf.

You can add your own File Locator by implementing `\Doppy\PdfGeneratorBundle\FileLocator\FileLocatorInterface`.
Then tag the service with `doppy_pdf_generator.file_locator`.  See the `services.yml` file in this bundle for an example.

### Odd Even PreProcessor

Because Flying Saucer does not support the nth-child selector, you are a bit stuck with more simple selectors.
To make it easy to create odd/even rows in tables, there is an OddEven Pre Processor, which adds classes to your elements.
Simply configure it as follows:

````
doppy_pdf_generator:
    preprocessor:
        oddeven:
            table: tr
            thead: tr
            tbody: tr
            tfoot: tr
````
This tells the preprocessor to add the class `odd` or `even` to `tr` elements under the four specified keys.
You can do this for any type of element. The class is only added to direct children that match the specified name.

If you leave out this config, the preprocessor is not used.

## Temp file generator

Some file locators need to store temporary files somewhere on disk, this can become quite a bit of files depending on what documents you are generatring and where files come from.
To keep things a bit more clean, it is possible to automatically cleanup timefiles when they are no longer needed.

You can specifify a path in the configuration where to create the tempfiles, like shown below.
When you don't configure this, the result of sys_get_temp_dir is used.

````
doppy_pdf_generator:
    temp_file:
        path: /your/path/
````

Using the default configuration, generated tempfiles will be removed on the terminate event. You can disable this using the configuration below.
You can also pass false as a second parameter to the generator to prevent that file from being cleaned up.
 
````
doppy_pdf_generator:
    temp_file:
        cleanup_on_terminate: false
````


[1]: http://code.google.com/p/flying-saucer/
[2]: https://github.com/stedekay/SpraedPDFGeneratorBundle/
[3]: https://www.w3.org/TR/css3-page/
[4]: https://github.com/flyingsaucerproject/flyingsaucer
