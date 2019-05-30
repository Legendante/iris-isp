<?php
include("db.inc.php");
define('FPDF_FONTPATH','font/');
require('fpdf.php');

$ComplexID = pebkac($_GET['cid'], 5);
$Complex = getComplexByID($ComplexID);
$AgentID = $Complex['agentid'];
$AgentRec = getUserByID($AgentID);
$OutPutType = 'I';
$startY = 30;
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
		$this->Cell(100, 5, "Residents Letter", 0, 0);
		$this->SetTextColor(0, 0, 0);
		$this->SetFont('Arial','',10);
	}
}
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('P');
$pdf->SetFont('Arial','',10);
$pdf->SetXY(15, $startY);
$LongText = "Dear Owner / Tenant";
$pdf->MultiCell(180, 6, $LongText, 0);
$startY += 10;
$pdf->SetXY(15, $startY);
$LongText = "The Trustees of " . $Complex['complexname'] . ", engaged us (and its installation partner Initec), to complete a site survey and feasibility study into the installation of an efficient fibre network backbone.";
$pdf->MultiCell(180, 6, $LongText, 0);
$startY += 16;
$pdf->SetXY(15, $startY);
$KickOff = substr($Complex['kickoff'], 0, 10);
if(($KickOff != '') && ($KickOff != '0000-00-00'))
	$LongText = "With the site development plan now approved by the Trustees, we have committed to the installation with the intended start date of " . $KickOff . ". We expect the installation to take approximately six to eight weeks and we will keep all residents updated on the progress of the installation via dedicated communication channels that were approved by the trustees including; email/regular updates online via IRIS should you be registered to access this information/sms/whatsapp group.";
else
	$LongText = "With the site development plan now approved by the Trustees, we have committed to the installation of a fibre backbone into " . $Complex['complexname'] . ". We expect the installation to take approximately six to eight weeks and we will keep all residents updated on the progress of the installation via dedicated communication channels that were approved by the trustees including; email/regular updates online via IRIS should you be registered to access this information/sms/whatsapp group.";
$pdf->MultiCell(180, 6, $LongText, 0);
$startY += 34;
$pdf->SetXY(15, $startY);
$LongText = "As we finalise our arrangements to ensure we are able to complete the installation efficiently for you, we would like to recommend that you register for the termination point to be installed within your home. The termination point works like your existing internal Telkom ADSL / line connection point, and it is necessary to have this termination point should you wish to sign up for fibre internet access now or at a later stage.";
$pdf->MultiCell(180, 6, $LongText, 0);
$startY += 27;
$pdf->SetXY(15, $startY);
$LongText = "During the initial installation of the fibre network - when we bring the fibre connections from the street right into your home - these termination points are free of charge, so it's a good idea to have them installed. Remember that you are under no obligation to sign up for any fibre internet services to receive them free of charge during this primary installation phase. If however you choose not to install them at this time, and to rather do it at a later stage, there will be a cost to the resident for the installation of the termination point. Installing the termination point will also increase your home value instantly.";
$pdf->MultiCell(180, 6, $LongText, 0);
$startY += 39;
$pdf->SetXY(15, $startY);
$pdf->SetFont('Arial','B',10);
// $LongText = "Should you wish to sign up for fibre internet services with us, there is a once-off connection fee and a monthly access fee that is dependent on your choice of package. The uncapped, unshaped, symmetrical broadband package options, and further information on the installation process, are attached here. You can also visit our website at www.domain.com to learn more about the packages, to register for installation and to sign up for monthly fibre access.";
$WebAddress = "www.domain.com";
$LongText = "To register for a termination point (Free) or to sign up for fibre internet services, please visit our website at " . $WebAddress . "";
if($Complex['subdomain'] != '')
{
	$WebAddress = $Complex['subdomain'] . ".domain.com";
	$LongText = "To register for a termination point (Free) or to sign up for fibre internet services, please visit your complex landing page at " . $WebAddress . "";
}
$pdf->MultiCell(180, 6, $LongText, 0);
$pdf->SetFont('Arial','',10);
$startY += 16;
$pdf->SetXY(15, $startY);

$LongText = "Please feel free to contact " . $AgentRec['firstname'] . " " . $AgentRec['surname'] . " should you have any questions.";
$pdf->MultiCell(180, 6, $LongText, 0);

$pdf->SetFont('Arial','B',10);
$startY += 10;
$pdf->SetXY(15, $startY);
$LongText = $AgentRec['firstname'] . " " . $AgentRec['surname'];
$pdf->MultiCell(180, 6, $LongText, 0);
$pdf->SetFont('Arial','',10);
$startY += 6;
$pdf->SetXY(15, $startY);
$LongText = $AgentRec['cellnumber'];
$pdf->MultiCell(180, 6, $LongText, 0);
$startY += 6;
$pdf->SetXY(15, $startY);
$LongText = $AgentRec['username'];
$pdf->MultiCell(180, 6, $LongText, 0);

$FileName = str_replace(" ", "_", $Complex['complexname']) . "_ResIntroduction_" . date("Ymd") . ".pdf";
$pdf->Output($FileName, $OutPutType);
unset($pdf);
?>