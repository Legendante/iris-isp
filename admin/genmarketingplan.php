<?php
include("db.inc.php");
define('FPDF_FONTPATH','font/');
require('fpdf.php');

$ComplexID = pebkac($_GET['cid'], 5);
$Complex = getComplexByID($ComplexID);
$OutPutType = 'I';
$startY = 15;
class PDF extends FPDF
{
	function Header()
	{
		global $startY;
		global $Complex;
		// if($this->page > 1)
		// {
			// $this->SetFont('Arial','',10);
			// $this->SetXY(190, 10);
			// $this->Cell(10, 5, $this->page, 0, 0);
		// }
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
			$this->Cell(100, 5, "Internal Marketing Plan: " . $Complex['complexname'], 0, 0);
			$this->SetTextColor(0, 0, 0);
		// }
	}
}
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('P');
$pdf->SetFont('Arial','B',18);
$pdf->SetXY(15, $startY);
$pdf->Cell(180, 6, "Internal Marketing Plan", 0, 0);
$pdf->SetFont('Arial','',14);
$startY += 10;
$pdf->SetXY(15, $startY);
$pdf->Cell(180, 6, "Complex/Estate Resident Marketing Options", 0, 0);
$startY += 10;
$pdf->SetXY(15, $startY);
$pdf->SetFont('Arial','',10);
$LongText = "Company is aware how frustrating it can be to be constantly bombarded and harassed by marketing messages from suppliers which is why we work closely with the Body Corporate to identify the best and most suitable approaches to sharing information with your residents. All marketing initiatives undertaken by Company will only be done with the approval of the representatives of the Body Corporate.";
$pdf->MultiCell(180, 6, $LongText, 0);
$startY += 28;
$pdf->SetXY(15, $startY);
$pdf->Cell(180, 6, "Company's marketing options include:", 0, 0);
$startY += 8;
$pdf->SetXY(20, $startY);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(50, 6, "1. EMAIL and or SMS notifications:", 0);
$startY += 6;
$pdf->SetFont('Arial','',10);
$pdf->SetXY(24, $startY);
$LongText = "Many Body Corporates have historically chosen to communicate directly with their residents, however this can become very time consuming for trustees.";
$pdf->MultiCell(170, 6, $LongText, 0);
$startY += 14;
$pdf->SetXY(24, $startY);
$LongText = "Company's online proprietary customer relations needs and support system, IRIS can seamlessly manage the entire complex communication process during this project, should the resident register their details on the site. Residents are assured of their right to privacy and as IRIS keeps a record of all resident contact, Company is able to provide the Body Corporate with confirmation that details are being safeguarded and correctly used.";
$pdf->MultiCell(170, 6, $LongText, 0);
$startY += 30;
$pdf->SetXY(20, $startY);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(50, 6, "2. Flyers", 0);
$startY += 6;
$pdf->SetFont('Arial','',10);
$pdf->SetXY(24, $startY);
$LongText = "Company can produce a physical flyer, in the form of a door hanger, which shares the relevant information with the residents. This format of flyer is neat but appropriately visible.";
$pdf->MultiCell(170, 6, $LongText, 0);
$startY += 12;
$pdf->SetXY(20, $startY);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(50, 6, "3. Temporary campaign awareness signage", 0);
$pdf->SetFont('Arial','',10);
$startY += 6;
$pdf->SetXY(24, $startY);
$LongText = "This neat, temporary campaign awareness signage is placed at the entrance to the complex";
$pdf->MultiCell(170, 6, $LongText, 0);
$startY += 6;
$pdf->SetXY(20, $startY);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(50, 6, "4. Onsite engagement events", 0);
$startY += 6;
$pdf->SetFont('Arial','',10);
$pdf->SetXY(24, $startY);
$LongText = "After initial awareness efforts have been implemented, Company staff will be available on Saturday mornings, to host information engagement events in an approved designated central point at the complex. This will allow us to answer any questions residents may have and to give progress updates on the build and installation.";
$pdf->MultiCell(170, 6, $LongText, 0);
$startY += 20;
$pdf->SetXY(15, $startY);
$LongText = "Please confirm below, which of the following marketing approaches Company may employ in the complex:";
$pdf->MultiCell(180, 6, $LongText, 0);
$pdf->SetFont('Arial','',10);
$startY += 8;
$pdf->SetXY(30, $startY);
$pdf->Cell(100, 6, "Email to residents by Body Corporate", 1);
$pdf->SetXY(130, $startY);
$pdf->Cell(20, 6, "Yes", 1, 0, 'C');
$pdf->SetXY(150, $startY);
$pdf->Cell(20, 6, "No", 1, 0, 'C');
$startY += 6;
$pdf->SetXY(30, $startY);
$pdf->Cell(100, 6, "SMS to residents by Body Corporate", 1);
$pdf->SetXY(130, $startY);
$pdf->Cell(20, 6, "Yes", 1, 0, 'C');
$pdf->SetXY(150, $startY);
$pdf->Cell(20, 6, "No", 1, 0, 'C');
$startY += 6;
$pdf->SetXY(30, $startY);
$pdf->Cell(100, 6, "IRIS Email management by Compay", 1);
$pdf->SetXY(130, $startY);
$pdf->Cell(20, 6, "Yes", 1, 0, 'C');
$pdf->SetXY(150, $startY);
$pdf->Cell(20, 6, "No", 1, 0, 'C');
$startY += 6;
$pdf->SetXY(30, $startY);
$pdf->Cell(100, 6, "IRIS SMS management by Company", 1);
$pdf->SetXY(130, $startY);
$pdf->Cell(20, 6, "Yes", 1, 0, 'C');
$pdf->SetXY(150, $startY);
$pdf->Cell(20, 6, "No", 1, 0, 'C');
$startY += 6;
$pdf->SetXY(30, $startY);
$pdf->Cell(100, 6, "Door Hanger Flyer by Company", 1);
$pdf->SetXY(130, $startY);
$pdf->Cell(20, 6, "Yes", 1, 0, 'C');
$pdf->SetXY(150, $startY);
$pdf->Cell(20, 6, "No", 1, 0, 'C');
$startY += 6;
$pdf->SetXY(30, $startY);
$pdf->Cell(100, 6, "Temporary Campaign Awareness Signage", 1);
$pdf->SetXY(130, $startY);
$pdf->Cell(20, 6, "Yes", 1, 0, 'C');
$pdf->SetXY(150, $startY);
$pdf->Cell(20, 6, "No", 1, 0, 'C');
$startY += 6;
$pdf->SetXY(30, $startY);
$pdf->Cell(100, 6, "Onsite engagement events on Saturday mornings", 1);
$pdf->SetXY(130, $startY);
$pdf->Cell(20, 6, "Yes", 1, 0, 'C');
$pdf->SetXY(150, $startY);
$pdf->Cell(20, 6, "No", 1, 0, 'C');
$startY += 12;
$LongText = "Should there be any other means of engagement which the Body Corporate might prefer, such as an introductory talk at a residents meeting, please let us know.";
$pdf->SetXY(15, $startY);
$pdf->MultiCell(180, 6, $LongText, 0);

$startY += 24;
$pdf->SetXY(15, $startY);
$pdf->Cell(90, 5, "Signatory: __________________________________", 0, 0);
$pdf->SetXY(105, $startY);
$pdf->Cell(90, 5, "Signature: __________________________________", 0, 0);

$FileName = str_replace(" ", "_", $Complex['complexname']) . "_Marketing_" . date("Ymd") . ".pdf";
$pdf->Output($FileName, $OutPutType);
unset($pdf);
?>