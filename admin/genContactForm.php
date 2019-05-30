<?php
include("db.inc.php");
define('FPDF_FONTPATH','font/');
require('fpdf.php');

$ComplexID = pebkac($_GET['cid'], 5);
$Complex = getComplexByID($ComplexID);
$OutPutType = 'I';

class PDF extends FPDF
{
	function Header()
	{
		global $startY;
		global $Complex;
	}

	function Footer()
	{
		global $Complex;
		$this->SetFont('Arial','',6);
		$IniLogo = "images/logo 251x58.png";
		$this->Image($IniLogo, 245, 190, 30);
		$this->SetXY(15, 190);
		$this->SetTextColor(187, 187, 187);
		$this->Cell(100, 5, "Complex Details Form: " . $Complex['complexname'], 0, 0);
		$this->SetTextColor(0, 0, 0);
		$this->SetFont('Arial','',10);
	}
}
$Cols = array();
$Cols[0]['width'] = 60;		// Name and surname
$Cols[0]['left'] = 15;
$Cols[1]['width'] = 60;		// Email
$Cols[1]['left'] = 75;
$Cols[2]['width'] = 40;		// Cell Number
$Cols[2]['left'] = 135;
$Cols[3]['width'] = 40;		// Tel number
$Cols[3]['left'] = 175;
$Cols[4]['width'] = 20;		// Unit
$Cols[4]['left'] = 215;
$Cols[5]['width'] = 50;		// Designation
$Cols[5]['left'] = 235;
$startY = 15;
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('L');
$pdf->SetFont('Arial','B',14);
$pdf->SetXY(15, $startY);
$pdf->Cell(260, 6, "Complex Details - " . $Complex['complexname'], 0, 0);
$pdf->SetFont('Arial','',10);
$startY += 10;
$pdf->SetXY(15, $startY);
$pdf->Cell(180, 6, "Number of units: __________", 0, 0);
$pdf->SetFont('Arial','B',10);
$startY += 10;
$pdf->SetXY(15, $startY);
$pdf->Cell(180, 6, "Body Corporate/Home Owners Association Contacts", 0, 0);
$pdf->SetFont('Arial','',10);
$startY += 6;
$pdf->SetXY($Cols[0]['left'], $startY);
$pdf->Cell($Cols[0]['width'], 6, "Name and Surname", 1, 0, 'C');
$pdf->SetXY($Cols[1]['left'], $startY);
$pdf->Cell($Cols[1]['width'], 6, "Email", 1, 0, 'C');
$pdf->SetXY($Cols[2]['left'], $startY);
$pdf->Cell($Cols[2]['width'], 6, "Cell Number", 1, 0, 'C');
$pdf->SetXY($Cols[3]['left'], $startY);
$pdf->Cell($Cols[3]['width'], 6, "Tel Number", 1, 0, 'C');
$pdf->SetXY($Cols[4]['left'], $startY);
$pdf->Cell($Cols[4]['width'], 6, "Unit", 1, 0, 'C');
$pdf->SetXY($Cols[5]['left'], $startY);
$pdf->Cell($Cols[5]['width'], 6, "Designation", 1, 0, 'C');
$startY += 6;
$pdf->SetXY($Cols[0]['left'], $startY);
$pdf->Cell($Cols[0]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[1]['left'], $startY);
$pdf->Cell($Cols[1]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[2]['left'], $startY);
$pdf->Cell($Cols[2]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[3]['left'], $startY);
$pdf->Cell($Cols[3]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[4]['left'], $startY);
$pdf->Cell($Cols[4]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[5]['left'], $startY);
$pdf->Cell($Cols[5]['width'], 9, "", 1, 0);
$startY += 9;
$pdf->SetXY($Cols[0]['left'], $startY);
$pdf->Cell($Cols[0]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[1]['left'], $startY);
$pdf->Cell($Cols[1]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[2]['left'], $startY);
$pdf->Cell($Cols[2]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[3]['left'], $startY);
$pdf->Cell($Cols[3]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[4]['left'], $startY);
$pdf->Cell($Cols[4]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[5]['left'], $startY);
$pdf->Cell($Cols[5]['width'], 9, "", 1, 0);
$startY += 9;
$pdf->SetXY($Cols[0]['left'], $startY);
$pdf->Cell($Cols[0]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[1]['left'], $startY);
$pdf->Cell($Cols[1]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[2]['left'], $startY);
$pdf->Cell($Cols[2]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[3]['left'], $startY);
$pdf->Cell($Cols[3]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[4]['left'], $startY);
$pdf->Cell($Cols[4]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[5]['left'], $startY);
$pdf->Cell($Cols[5]['width'], 9, "", 1, 0);
$startY += 9;
$pdf->SetXY($Cols[0]['left'], $startY);
$pdf->Cell($Cols[0]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[1]['left'], $startY);
$pdf->Cell($Cols[1]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[2]['left'], $startY);
$pdf->Cell($Cols[2]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[3]['left'], $startY);
$pdf->Cell($Cols[3]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[4]['left'], $startY);
$pdf->Cell($Cols[4]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[5]['left'], $startY);
$pdf->Cell($Cols[5]['width'], 9, "", 1, 0);
$startY += 9;
$pdf->SetXY($Cols[0]['left'], $startY);
$pdf->Cell($Cols[0]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[1]['left'], $startY);
$pdf->Cell($Cols[1]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[2]['left'], $startY);
$pdf->Cell($Cols[2]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[3]['left'], $startY);
$pdf->Cell($Cols[3]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[4]['left'], $startY);
$pdf->Cell($Cols[4]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[5]['left'], $startY);
$pdf->Cell($Cols[5]['width'], 9, "", 1, 0);

$pdf->SetFont('Arial','B',10);
$startY += 15;
$pdf->SetXY(15, $startY);
$pdf->Cell(180, 6, "Managing Agent", 0, 0);
$pdf->SetFont('Arial','',10);
$startY += 6;
$pdf->SetXY(15, $startY);
$pdf->Cell(180, 6, "Agency: ________________________________________________________________________________________________________________", 0, 0);
$startY += 6;
$pdf->SetXY($Cols[0]['left'], $startY);
$pdf->Cell($Cols[0]['width'], 6, "Name and Surname", 1, 0, 'C');
$pdf->SetXY($Cols[1]['left'], $startY);
$pdf->Cell($Cols[1]['width'], 6, "Email", 1, 0, 'C');
$pdf->SetXY($Cols[2]['left'], $startY);
$pdf->Cell($Cols[2]['width'], 6, "Cell Number", 1, 0, 'C');
$pdf->SetXY($Cols[3]['left'], $startY);
$pdf->Cell($Cols[3]['width'], 6, "Tel Number", 1, 0, 'C');

$startY += 6;
$pdf->SetXY($Cols[0]['left'], $startY);
$pdf->Cell($Cols[0]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[1]['left'], $startY);
$pdf->Cell($Cols[1]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[2]['left'], $startY);
$pdf->Cell($Cols[2]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[3]['left'], $startY);
$pdf->Cell($Cols[3]['width'], 9, "", 1, 0);

$pdf->SetFont('Arial','B',10);
$startY += 15;
$pdf->SetXY(15, $startY);
$pdf->Cell(180, 6, "Security Company", 0, 0);
$pdf->SetFont('Arial','',10);
$startY += 6;
$pdf->SetXY(15, $startY);
$pdf->Cell(180, 6, "Company: ________________________________________________________________________________________________________________", 0, 0);
$startY += 6;
$pdf->SetXY($Cols[0]['left'], $startY);
$pdf->Cell($Cols[0]['width'], 6, "Name and Surname", 1, 0, 'C');
$pdf->SetXY($Cols[1]['left'], $startY);
$pdf->Cell($Cols[1]['width'], 6, "Email", 1, 0, 'C');
$pdf->SetXY($Cols[2]['left'], $startY);
$pdf->Cell($Cols[2]['width'], 6, "Cell Number", 1, 0, 'C');
$pdf->SetXY($Cols[3]['left'], $startY);
$pdf->Cell($Cols[3]['width'], 6, "Tel Number", 1, 0, 'C');

$startY += 6;
$pdf->SetXY($Cols[0]['left'], $startY);
$pdf->Cell($Cols[0]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[1]['left'], $startY);
$pdf->Cell($Cols[1]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[2]['left'], $startY);
$pdf->Cell($Cols[2]['width'], 9, "", 1, 0);
$pdf->SetXY($Cols[3]['left'], $startY);
$pdf->Cell($Cols[3]['width'], 9, "", 1, 0);

$FileName = str_replace(" ", "_", $Complex['complexname']) . "_ComplexForm_" . date("Ymd") . ".pdf";
$pdf->Output($FileName, $OutPutType);
unset($pdf);
?>