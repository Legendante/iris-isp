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
		// $this->SetFont('Arial','',10);
		// $this->SetXY(190, 10);
		// $this->Cell(10, 5, $this->page, 0, 0);
	}

	function Footer()
	{
		global $Complex;
		// if($this->page > 1)
		// {
			$IniLogo = "images/logo 251x58.png";
			$this->Image($IniLogo, 160, 280, 30);
			$this->SetXY(15, 282);
			$this->SetTextColor(187, 187, 187);
			$this->Cell(100, 5, "Letter of Engagement and Permission: " . $Complex['complexname'], 0, 0);
			$this->SetTextColor(0, 0, 0);
		// }
	}
}
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('P');
$pdf->SetFont('Arial','B',14);
$startY = 20;
$pdf->SetXY(15, $startY);
$pdf->Cell(180, 6, "Letter of Engagement and Permission", 0, 0, 'C');
$pdf->SetFont('Arial','',10);
$startY += 10;
$pdf->SetXY(15, $startY);
$LongText = "The " . $Complex['complexname'] . " Body Corporate hereby grants Company (Pty) Ltd permission to conduct a site survey and an uptake survey with the residents of the complex in order to ascertain the viability of building a fibre backbone through the complex and the installation of termination points into the residences.";
$pdf->MultiCell(180, 6, $LongText, 0);
$startY += 24;
$pdf->SetXY(15, $startY);
$LongText = "Company representatives will require access to the complex for the site survey while the uptake viability survey will take place in a format approved by the Body Corporate representatives during the initial meeting.";
$pdf->MultiCell(180, 6, $LongText, 0);
// $startY += 24;
// $pdf->SetXY(15, $startY);
// $LongText = "The site and uptake viability survey will begin on ________________________________ and will be concluded by _____________________. The results of the survey will be shared with the Body Corporate by no later than _________________________.";
// $pdf->MultiCell(180, 6, $LongText, 0);

$pdf->SetFont('Arial','B',10);
$startY += 48;
$pdf->SetXY(15, $startY);
$pdf->Cell(175, 5, "For the Body Corporate", 0, 0);
$pdf->SetFont('Arial','',10);

$startY += 10;
$pdf->SetXY(15, $startY);
$pdf->Cell(175, 5, "Signed this ____________ day of ______________________ 20_____ at _________________________", 0, 0);

$startY += 10;
$pdf->SetXY(15, $startY);
$pdf->Cell(175, 5, "Authorised signatory name: _____________________________________________", 0, 0);

$startY += 10;
$pdf->SetXY(15, $startY);
$pdf->Cell(175, 5, "Designation: _________________________________________________________", 0, 0);

$startY += 10;
$pdf->SetXY(15, $startY);
$pdf->Cell(175, 5, "Signature: ___________________________________________________________", 0, 0);

$pdf->SetFont('Arial','B',10);
$startY += 20;
$pdf->SetXY(15, $startY);
$pdf->Cell(175, 5, "For Company (Pty) Ltd", 0, 0);
$pdf->SetFont('Arial','',10);

$startY += 10;
$pdf->SetXY(15, $startY);
$pdf->Cell(175, 5, "Signed this ____________ day of ______________________ 20_____ at _________________________", 0, 0);

$startY += 10;
$pdf->SetXY(15, $startY);
$pdf->Cell(175, 5, "Authorised signatory name: _____________________________________________", 0, 0);

$startY += 10;
$pdf->SetXY(15, $startY);
$pdf->Cell(175, 5, "Designation: _________________________________________________________", 0, 0);

$startY += 10;
$pdf->SetXY(15, $startY);
$pdf->Cell(175, 5, "Signature: ___________________________________________________________", 0, 0);

$FileName = str_replace(" ", "_", $Complex['complexname']) . "_LOEP_" . date("Ymd") . ".pdf";
$pdf->Output($FileName, $OutPutType);
unset($pdf);
?>