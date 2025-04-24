<?php
/**
 * TestPDF13
 *
 * Generates a Code 13 Barcode PDF document
 *
 * @author BAUDRY Charly
 */

use cbaudry\FPDF\PDF;

define('ROOT_PATH', realpath(__DIR__.'/..').'/');

require_once __DIR__ . '/../vendor/autoload.php';

$fpdf = new PDF();
$fpdf->setCachePath(ROOT_PATH.'cache/font/');
$fpdf->setFontPath(ROOT_PATH.'src/font/');
$fpdf->AddPage();
$fpdf->EAN13(5, 5, '9999999000016', 100, 50);

header('Content-Type: application/pdf; charset=UTF-8');
echo $fpdf->output();
