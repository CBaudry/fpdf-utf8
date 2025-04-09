<?php
/**
 * TestPDF39
 *
 * Generates a Code 39 Barcode PDF document
 *
 * @author Tim Peters <info@faimmedia.nl>
 * @copyright FaimMedia B.V. 2017
 */

use CS\FPDF\PDF;

define('ROOT_PATH', realpath(__DIR__.'/..').'/');

require_once __DIR__ . '/../vendor/autoload.php';

$fpdf = new PDF();
$fpdf->setCachePath(ROOT_PATH.'cache/font/');
$fpdf->setFontPath(ROOT_PATH.'src/font/');
$fpdf->AddPage();
$fpdf->Barcode->CODE_39(5, 5, '1234566700345');

header('Content-Type: application/pdf; charset=UTF-8');
echo $fpdf->output();
