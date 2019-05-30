<?php
include("db.inc.php");
define('FPDF_FONTPATH','font/');
require('fpdf.php');

$ComplexID = pebkac($_GET['cid'], 5);
$Complex = getComplexByID($ComplexID);
$OutPutType = 'I';
$startY = 35;
class PDF extends FPDF
{
	function Header()
	{
		global $startY;
		global $Complex;
		$IniLogo = "images/logo 251x58.png";
		$this->Image($IniLogo, 158, 15, 30);
	}

	function Footer()
	{
		global $Complex;
		$IniLogo = "images/logo 251x58.png";
		$this->Image($IniLogo, 158, 280, 30);
	}
}
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('P');
$startY = 15;
$pdf->SetFont('Arial','B',18);
$pdf->SetXY(20, $startY);
$pdf->Cell(180, 6, "Fibre Packages", 0, 0);
$IniLogo = "images/pricing953x653.png";
$pdf->Image($IniLogo, 19, 25, 170);
$FileName = str_replace(" ", "_", $Complex['complexname']) . "_Pricing_" . date("Ymd") . ".pdf";
$pdf->Output($FileName, $OutPutType);
unset($pdf);
?>