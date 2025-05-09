# FPDF-UTF8 library


This is an unofficial repository for the [FPDF-library](http://fpdf.org/fr/script/script92.php).  
The purpose is to be able to build automatic packages using [composer](http://packagist.org).

## Install

You can install this library by editing (or creating) your `composer.json`:

	{
		"require": {
			"cbaudry/fpdf-utf8": "*"
		}
	}

And run `composer update` or `composer install`.

Or you can simply run the `composer` command, `composer require cbaudry/fpdf-utf8`.

## Usage

### Namespacing

The namespacing has been changed to `CS\FPDF`.

### Caching

In this version you can set the cache folder yourself, so it will not put the cache files into the same directory as the font-files. Please note that if you do not set the cache folder, no caching is used and documents may be created a little bit slower.

	use CS\FPDF\PDF;

	$fpdf = new PDF();
	$fpdf->setFontPath('/font/unifont/');
	$fpdf->setCache('/cache/');
	$fpdf->AddPage();
	$fpdf->AddFont('OpenSans');
	$fpdf->SetFont('OpenSans');
	$fpdf->Write('This is a test PDF with OpenSans as font');
	$fpdf->Output(); 

### Exception

A custom exception class `FPDFException` has been created. For all exception error codes, please take a look at the `src/FPDF/Exception/FPDFException.php` file.

## About

### What is FPDF-UTF8?

FPDF-UTF8 is a version of FPDF, which supports UTF-8 and font-subsetting.  
This is a updated version (fork) of [FaimMedia's version](https://github.com/FaimMedia/fpdf-utf8), which is an updated version (fork) of [tFPDF](https://github.com/DocnetUK/tFPDF).

### What is FPDF?

FPDF is a PHP class which allows to generate PDF files with pure PHP, that is to say without using the PDFlib library. F from FPDF stands for Free: you may use it for any kind of usage and modify it to suit your needs.

FPDF has other advantages: high level functions. Here is a list of its main features:

* Choice of measure unit, page format and margins
* Page header and footer management
* Automatic page break
* Automatic line break and text justification
* Image support (JPEG, PNG, GIF and WebP)
* Colors
* Links
* TrueType, Type1 and encoding support
* Page compression
* EAN 13, 128 and Code 39 support (without extending the main class)
* PDF Encryption (without extending the main class)
* File Attachment
* Add [XMP data](https://www.adobe.com/products/xmp.html)

FPDF requires no extension, except zlib (to activate compression and handle png alpha chanel) and GD (for GIF support). It works and will be maintained with PHP 8 and above.

The tutorials will give you a quick start. The complete online documentation is here and download area is there. It is strongly advised to read the FAQ which lists the most common questions and issues.

A script section is available and provides some useful extensions (such as bookmarks, rotations, tables, barcodes...).

### What languages can I use?

The class can produce documents in many languages other than the Western European ones: Central European, Cyrillic, Greek, Baltic and Thai, provided you own TrueType or Type1 fonts with the desired character set. Chinese, Japanese and Korean are supported too. UTF-8 support is also available.

### What about performance?


Of course, the generation speed of the document is less than with PDFlib. However, the performance penalty keeps very reasonable and suits in most cases, unless your documents are particularly complex or heavy.

## Source attribution

The Code 128 and Code 39 barcode library code was taken directly from the FPDF website, where source attribution is vague at best, but mostly non-existent. If you need source attribution applying for your code included here, please raise an issue.

Based on [FPDF's Website](http://fpdf.org/) :

* EAN 128 has been shared by Roland Gautier (dir@persolaser.com)
* EAN 13 has been shared by Olivier (oliver@fpdf.org)
* Code 39 has been shared by Emmanuel Havet (ehavet@yahoo.fr)
* The base of this library has been release by Ian Back (ian@bpm1.com) under the LGPL licence
* File Attachment has been shared by Olivier (oliver@fpdf.org) and modified to be directly integrated within FPDF

