<?php
include("db.inc.php");
$ComplexID = pebkac($_GET['cid'], 5);
$FileID = pebkac($_GET['fid'], 5);
$FileRec = getComplexFileByID($FileID);
$Complex = getComplexByID($ComplexID);
$TheFile = $FileRec['filepath'];
$UserFileName = cleanFileName($FileRec['filename']);
$UserFileName = 'signoff_' . $UserFileName;
$ReadFile = '';
$startY = 35;
define('FPDF_FONTPATH','font/');
require('fpdf.php');
require('fpdi.php');

class PDF extends FPDI
{
	function Header()
	{
	}
	
	function Footer()
	{
		global $Complex;
		if($this->page != 2)
		{
			$IniLogo = "images/logo 251x58.png";
			$this->Image($IniLogo, 160, 280, 30);
			$this->SetTextColor(187, 187, 187);
			$this->SetFont('Arial','',10);
			$this->SetXY(15, 280);
			$this->Cell(200, 5, 'Site plans - ' . $Complex['complexname']);
			$this->SetTextColor(0, 0, 0);
		}
	}
}
$OutPutType = 'I';
$pdf = new PDF();
$pdf->addPage();
$pdf->SetFont('Arial','B',18);
$startY += 10;
$pdf->SetXY(15, $startY);
$pdf->Cell(180, 6, "Site Proposal", 0, 0, 'C');
$pdf->SetFont('Arial','',14);
$startY += 10;
$pdf->SetXY(15, $startY);
$pdf->Cell(180, 6, "for residential fibre installation", 0, 0, 'C');
$startY += 10;
$pdf->SetXY(15, $startY);
$pdf->Cell(180, 6, "provided by", 0, 0, 'C');
$startY += 10;
$pdf->SetXY(15, $startY);
$pdf->SetFont('Arial','B',18);
$pdf->Cell(180, 6, "Company (Pty) Ltd", 0, 0, 'C');
$startY += 10;
$pdf->SetXY(15, $startY);
$pdf->SetFont('Arial','',14);
$pdf->Cell(180, 6, "to", 0, 0, 'C');
$startY += 10;
$pdf->SetFont('Arial','B',18);
$pdf->SetXY(15, $startY);
$pdf->Cell(180, 6, $Complex['complexname'], 0, 0, 'C');
$IniLogo = "images/logo 658x150.png";
$startY += 50;
$pdf->Image($IniLogo, 65, $startY, 80);
$pageCount = $pdf->setSourceFile($FileRec['filepath']);
$tplIdx = $pdf->importPage(1);
$pdf->addPage();
$pdf->useTemplate($tplIdx);//, 10, 10, 90);
$pdf->addPage();
$pdf->SetFont('Arial'); 
$startY = 20;
$pdf->SetFont('Arial','B',14);
$pdf->SetXY(15, $startY);
$pdf->Cell(180, 6, "Site Plan Sign Off", 0, 0);
$pdf->SetFont('Arial','',10);
$startY += 10;
$pdf->SetXY(15, $startY);
$LongText = "Attached is the site plan for the installation of fibre infrastructure at " . $Complex['complexname'] . " for approval by the body corporate/home owner's association.";
$pdf->MultiCell(180, 6, $LongText, 0);
$pdf->SetFont('Arial','B',10);
$startY += 15;
$pdf->SetXY(15, $startY);
$pdf->Cell(6, 6, "Please note:", 0);
$pdf->SetFont('Arial','',10);
$startY += 8;
$pdf->SetXY(20, $startY);
$pdf->Cell(6, 6, chr(127), 0);
$pdf->SetXY(25, $startY);
$pdf->MultiCell(160, 6, "This gives Company the confirmation to use the attached site plans for planning and installation", 0);
$startY += 6;
$pdf->SetXY(20, $startY);
$pdf->Cell(6, 6, chr(127), 0);
$pdf->SetXY(25, $startY);
$pdf->MultiCell(160, 6, "Should there be a change to the site plans or the installation, the body corporate will be will be timeously notified.", 0);
$pdf->SetFont('Arial','B',10);
$startY += 20;
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
$pdf->Output($UserFileName, $OutPutType);
unset($pdf);
?>