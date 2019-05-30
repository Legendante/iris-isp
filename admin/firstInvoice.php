<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("db.inc.php");
global $dbCon;


define('FPDF_FONTPATH','font/');
require('fpdf.php');

class PDF extends FPDF
{
	public function Header()
	{
		$TopY = 30;
		$this->SetDrawColor(77,4,140);
		$this->SetLineWidth(0.5);
		$this->Line(10, 13, 10, 285);
		$this->SetLineWidth(0.3);
		$this->SetDrawColor(140,145,146);
		$this->Line(8, 275, 198, 275);
		$this->SetLineWidth(0.1);
		$this->SetDrawColor(0);
	}

	public function Footer()
	{
		$TopY = 276;
		$this->SetFont('Arial','',8);
		$this->SetXY(14, $TopY);
		$this->SetTextColor(0);
		$this->Cell(35, 5, "Company (PTY) Ltd.", 0);
		$this->Cell(29, 5, "2016/123456/07", 0);
		$TopY += 4;
		$this->SetXY(15, $TopY);
		$this->SetTextColor(77,4,140);
		$this->Cell(3, 5, "(t)", 0, 0, 'C');
		$this->SetTextColor(0);
		$this->Cell(29, 5, "+27 (0) 11 123 4567", 0);
		$this->SetTextColor(77,4,140);
		$this->Cell(3, 5, "(w)", 0, 0, 'C');
		$this->SetTextColor(0);
		$this->Cell(28, 5, "www.domain.com", 0);
		$this->SetTextColor(77,4,140);
		$this->Cell(3, 5, "(e)", 0, 0, 'C');
		$this->SetTextColor(0);
		$this->Cell(29, 5, "hello@domain.com", 0);
		$this->SetTextColor(77,4,140);
		$this->Cell(3, 5, "(a)", 0, 0, 'C');
		$this->SetTextColor(0);
		$this->Cell(71, 5, "23 Skyways Business Park, Freda Rd, Randburg, 2188", 0);
		$IniLogo = "images/brandicon.png";
		$this->Image($IniLogo, 187, 276, 10);
	}
}

$OrderIDs = $_POST['inv_ids'];
$StartDate = pebkac($_POST['invstart'], 10, 'STRING');
$IncNextMnt = (isset($_POST['incNextMnt'])) ? pebkac($_POST['incNextMnt']) : 0;
$Orders = getUnitOrdersByIDList($OrderIDs);
$ONTs = getONTTypes();
$VendorSpeeds = getPackageSpeeds();
$PackageArr = getFibrePackages();
$Customers = array();
$Items = array();
$CustomerIDs = array();
$UnitIDs = array();
foreach($Orders AS $OrderID => $OrderRec)
{
	$CustomerIDs[] = $OrderRec['customerid'];
	$UnitIDs[] = $OrderRec['unitid'];
	$Customers[$OrderRec['customerid']][$OrderID] = $OrderRec['unitid'];
	$Items[$OrderID]['prevatcost'] = $OrderRec['prevatsalesprice'];
	$Items[$OrderID]['connectcost'] = $OrderRec['prevatconnectcost'];
	$Items[$OrderID]['ontcost'] = $OrderRec['ontcost'];
	$Items[$OrderID]['speedid'] = $OrderRec['speedid'];
	$Items[$OrderID]['packageid'] = $OrderRec['packageid'];
}
$CustRecs = getCustomersByIDList(implode(",", $CustomerIDs));
$UnitRecs = getUnitsByIDList(implode(",", $UnitIDs));
$ComplexIDs = array();
foreach($UnitRecs AS $UnitID => $UnitRec)
{
	$ComplexIDs[] = $UnitRec['complexid'];
}
$Complexes = getComplexesByIDList(implode(",", $ComplexIDs));
$StartDay = date("d", strtotime($StartDate));
$EndDay = date("t", strtotime($StartDate));
$DateDiff = $EndDay - $StartDay + 1;
$GennedInvs = array();
foreach($Customers AS $CustomerID => $CustOrdRec)
{
	$Invoice = array();
	$Invoice['customerid'] = $CustomerID;
	$Invoice['datestart'] = $StartDate;
	$Invoice['dateend'] = date("Y-m-t", strtotime($StartDate));
	if(($IncNextMnt == 1) && ($StartDay > 1))
	{
		$MidMonth = date("Y-m-15");
		$Invoice['dateend'] = date('Y-m-t', strtotime('+1 month', strtotime($MidMonth)));
	}
	$InvoiceID = createInvoice($Invoice);
	$GennedInvs[] = $InvoiceID;
	$CustNum = date("y", strtotime($CustRecs[$CustomerID]['dateregistered'])) . sprintf("%06d", $CustomerID);
	$InvTotal = 0;
	$ReferAmount = getRefererAmount($CustomerID);
	foreach($CustOrdRec AS $OrderID => $UnitID)
	{
		$CostPerDay = round((($Items[$OrderID]['prevatcost'] * 12) / 365), 2);
		if($StartDay == 1)
			$SpeedCost = $Items[$OrderID]['prevatcost'];
		else
			$SpeedCost = round($CostPerDay * $DateDiff, 2);
		
		$ItemArr = array();
		$ItemArr['invoiceid'] = $InvoiceID;
		$ItemArr['unitid'] = $UnitID;
		$ItemArr['itemqty'] = 1;
		$ItemArr['nonvatotal'] = $SpeedCost;
		$InvTotal += $SpeedCost;
		$ItemArr['vattotal'] = $SpeedCost * 1.14;
		$Desc = $PackageArr[$Items[$OrderID]['packageid']]['packagename'] . " (" . $VendorSpeeds[$Items[$OrderID]['speedid']] . ")";
		$ItemArr['itemdesc'] = $Desc;
		addInvoiceItem($ItemArr);
		$ItemArr['nonvatotal'] = $Items[$OrderID]['connectcost'];
		$InvTotal += $Items[$OrderID]['connectcost'];
		$ItemArr['vattotal'] = $Items[$OrderID]['connectcost'] * 1.14;
		$Desc = "Connection Cost";
		$ItemArr['itemdesc'] = $Desc;
		addInvoiceItem($ItemArr);
		$ItemArr['nonvatotal'] = $Items[$OrderID]['ontcost'];
		$InvTotal += $Items[$OrderID]['ontcost'];
		$ItemArr['vattotal'] = $Items[$OrderID]['ontcost'] * 1.14;
		$Desc = "ONT Cost (Modem)";
		$ItemArr['itemdesc'] = $Desc;
		addInvoiceItem($ItemArr);
		$SaveArr = array('orderstatus' => 3);
		updateUnitPackage($OrderID, $SaveArr);
	}
	if(($IncNextMnt == 1) && ($StartDay > 1))
	{
		$SpeedCost = $Items[$OrderID]['prevatcost'];
		$ItemArr = array();
		$ItemArr['invoiceid'] = $InvoiceID;
		$ItemArr['unitid'] = $UnitID;
		$ItemArr['itemqty'] = 1;
		$ItemArr['nonvatotal'] = $SpeedCost;
		$InvTotal += $SpeedCost;
		$ItemArr['vattotal'] = $SpeedCost * 1.14;
		$Desc = $PackageArr[$Items[$OrderID]['packageid']]['packagename'] . " (" . $VendorSpeeds[$Items[$OrderID]['speedid']] . ")";
		$ItemArr['itemdesc'] = $Desc;
		addInvoiceItem($ItemArr);
	}
	$CreditNotes = getCustomerCreditNotes($CustomerID, 0);
	if(count($CreditNotes) > 0)
	{
		foreach($CreditNotes AS $CreditID => $CreditRec)
		{
			$ItemArr = array();
			$ItemArr['invoiceid'] = $InvoiceID;
			$ItemArr['unitid'] = $UnitID;
			$ItemArr['itemqty'] = 1;
			$ItemArr['nonvatotal'] = ($CreditRec['creditamount'] * -1);
			$ItemArr['itemdesc'] = "Credit (" . $CreditRec['creditdescription'] . ")";
			addInvoiceItem($ItemArr);
			$InvTotal -= $CreditRec['creditamount'];
			$CreditArr = array();
			$CreditArr['creditstatus'] = 1;
			updateCreditNote($CreditID, $CreditArr);
		}
	}
	if($ReferAmount > 0)
	{
		$ReferAmount = $ReferAmount * 0.05;
		$ItemArr = array();
		$ItemArr['invoiceid'] = $InvoiceID;
		$ItemArr['unitid'] = $UnitID;
		$ItemArr['itemqty'] = 1;
		$ItemArr['nonvatotal'] = ($ReferAmount * -1);
		$ItemArr['itemdesc'] = "Referal Rebate";
		addInvoiceItem($ItemArr);
		$InvTotal -= $ReferAmount;
	}
	$InvArr = array();
	$InvArr['nonvattotal'] = $InvTotal;
	$InvArr['vattotal'] = sprintf("%0.2f", $InvTotal * 1.14);
	$InvArr['outstanding'] = sprintf("%0.2f", $InvTotal * 1.14);
	updateInvoice($InvoiceID, $InvArr);
	$updQry = 'UPDATE customerdetails SET customerbalance = customerbalance + \'' . $InvArr['vattotal'] . '\' WHERE customerid = ' . $CustomerID;
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}
$Invs = getInvoices(implode(",", $GennedInvs));
foreach($Invs AS $InvID => $InvRec)
{
	$FileName = genInvoiceFile($InvID);
	$InvSaveArr = array();
	$InvSaveArr['filepath'] = $FileName;
	updateInvoice($InvID, $InvSaveArr);
}
header("Location: orders.php");
?>