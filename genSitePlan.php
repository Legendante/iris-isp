<?php

$ReadFile = '';

define('FPDF_FONTPATH','font/');
require('fpdf.php');
require('fpdi.php');

class PDF extends FPDF
{
	function Header()
	{
		global $startY;
		global $Complex;
		$startY = 35;
		$MFLogo = "images/metrofibre.png";
		$this->Image($MFLogo, 35, 5, 135);
		
		$this->SetFont('Arial','B',12);
		$this->SetXY(5, $startY);
		$this->Cell(200, 10, "LETTER OF APPROVAL / HAPPY LETTER", 0, 10, 'C');
		$startY += 8;
		$this->SetFont('Arial','I',10);
		$this->SetXY(5, $startY);
		$this->Cell(200, 10, "(To be completed after installation is done)", 0, 10, 'C');
		$startY += 10;
		$this->SetFont('Arial', '', 8);
		$this->SetXY(35, $startY);
		$this->Cell(200, 10, "Estate Name: " . $Complex['complexname'], 0, 10);
	}
	
	function Footer()
	{
		$IniLogo = "images/initec.png";
		$this->Image($IniLogo, 38, 225, 135);
		$this->SetXY(5, 280);
		$this->Cell(200, 5, 'FIONA - Fibre Infrastructure and Optical Network Administrator', 0, 0, 'C');
	}
}
$OutPutType = 'I';
$pdf = new FPDI();

$pdf->addPage();
$pdf->SetFont('Arial'); 
$pdf->SetTextColor(255,0,0); 
$pdf->SetXY(25, 25); 
$pdf->Write(0, "This is just a simple text"); 

$pageCount = $pdf->setSourceFile('BarkleyMews.pdf');
$tplIdx = $pdf->importPage(1);

$pdf->addPage();
$pdf->useTemplate($tplIdx, 10, 10, 90);

$pdf->addPage();
$pdf->SetFont('Arial'); 
$pdf->SetTextColor(255,0,0); 
$pdf->SetXY(25, 25); 
$pdf->Write(0, "This is just a simple text"); 

$FileName = str_replace(" ", "_", $Complex['complexname']) . "_welcomes_" . date("Ymd") . ".pdf";
$pdf->Output($FileName, $OutPutType);
unset($pdf);
// $pdf->Output();

?>