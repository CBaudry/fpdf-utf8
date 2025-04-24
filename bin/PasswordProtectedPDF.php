<?php
/**
 * PasswordProtectedPDF
 *
 * Generates a PDF that is password protected
 *
 * @author Tim Peters <info@faimmedia.nl>
 * @copyright FaimMedia B.V. 2018
 */

use cbaudry\FPDF\PDF,;

define('ROOT_PATH', realpath(__DIR__.'/..').'/');

require_once ROOT_PATH.'/vendor/autoload.php';

$password = '123';

$fpdf = new ProtectedPDF();
$fpdf->Protection->setProtection(user_password: $password);

$fpdf->AddFont('helvetica', '', '../helvetica.php');
$fpdf->SetFont('helvetica');
$fpdf->AddPage();
$fpdf->Cell(100, 5, 'This PDF-file is password protected', 0, 1);

header('Content-Type: application/pdf; charset=UTF-8');
echo $fpdf->output();
