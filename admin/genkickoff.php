<?php
include("db.inc.php");
define('FPDF_FONTPATH','font/');
require('fpdf.php');

$ComplexID = pebkac($_GET['cid'], 5);
$Complex = getComplexByID($ComplexID);
$OutPutType = 'I';
$startY = 18;
class PDF extends FPDF
{
	function Header()
	{
		global $startY;
		global $Complex;
		$IniLogo = "images/logo 658x150.png";
		$this->Image($IniLogo, 125, 12, 70);
	}

	function Footer()
	{
		global $Complex;
		$this->SetFont('Arial','',6);
		$IniLogo = "images/logo 251x58.png";
		$this->Image($IniLogo, 160, 280, 30);
		$this->SetXY(15, 282);
		$this->SetTextColor(187, 187, 187);
		$this->Cell(100, 5, "Kick-off Notification", 0, 0);
		$this->SetTextColor(0, 0, 0);
		$this->SetFont('Arial','',10);
	}
}
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('P');
$pdf->SetFont('Arial','B',18);
$pdf->SetXY(15, $startY);
$pdf->Cell(180, 6, "Kick-off Notification", 0);
$pdf->SetFont('Arial','',10);


$startY += 16;
$pdf->SetXY(15, $startY);
$LongText = "This serves as notification that Company intend on kicking off the installation of fibre infrastructure at " . $Complex['complexname'] . " on " . substr($Complex['kickoff'], 0, 10);
$pdf->MultiCell(180, 6, $LongText, 0);

$startY += 16;
$pdf->SetXY(15, $startY);
$LongText = "It is important to note that sometimes unforeseen factors could affect the build and installation timeline.\nExamples of these include among others:";
$pdf->MultiCell(180, 6, $LongText, 0);


$startY += 12;
$pdf->SetXY(16, $startY);
$pdf->Cell(3, 6, chr(127), 0);
$pdf->SetXY(19, $startY);
$LongText = "A delay in other projects due to unforeseen factors may result in kick off date being extended. The Body corporate/HOA will be notified of any extension on dates.";
$pdf->MultiCell(170, 6, $LongText, 0);

$startY += 12;
$pdf->SetXY(16, $startY);
$pdf->Cell(3, 6, chr(127), 0);
$pdf->SetXY(19, $startY);
$LongText = "Difficulty in gaining access to residences for the installation of termination points or additional residents signing up during the installation phase which require additional termination points to be installed.";
$pdf->MultiCell(170, 6, $LongText, 0);

$startY += 12;
$pdf->SetXY(16, $startY);
$pdf->Cell(3, 6, chr(127), 0);
$pdf->SetXY(19, $startY);
$LongText = "Delay on Live date may be caused by unsound infrastructure, resulting in replacing or installing new infrastructure into complex.";
$pdf->MultiCell(170, 6, $LongText, 0);

$startY += 12;
$pdf->SetXY(16, $startY);
$pdf->Cell(3, 6, chr(127), 0);
$pdf->SetXY(19, $startY);
$LongText = "Delay on Live date due to unavailable residents resulting in units not being able to receive installation (mainly in multi dwelling units). Units with orders will only then be installed.";
$pdf->MultiCell(170, 6, $LongText, 0);

$startY += 12;
$pdf->SetXY(16, $startY);
$pdf->Cell(3, 6, chr(127), 0);
$pdf->SetXY(19, $startY);
$LongText = "Inclement weather / environmental factors can cause delays. For reasons of health and safety, our teams are not permitted to work in bad weather. Heavy rains or other severe weather pose a temporary hazard to the build/infrastructure and our people";
$pdf->MultiCell(170, 6, $LongText, 0);

$startY += 22;
$pdf->SetXY(15, $startY);
$LongText = "Should the kick-off date need to be changed, the body corporate will be will be timeously notified. ";
$pdf->MultiCell(180, 6, $LongText, 0);

$pdf->SetFont('Arial','B',10);
$startY += 18;
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

$FileName = str_replace(" ", "_", $Complex['complexname']) . "_Kickoff_" . date("Ymd") . ".pdf";
$pdf->Output($FileName, $OutPutType);
unset($pdf);
?>