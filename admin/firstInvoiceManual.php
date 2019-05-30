<?php
session_start();
include("db.inc.php");

$OrderID = $_GET['oid'];
$StartDate = pebkac($_POST['invstart'], 10, 'STRING');
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
	$Items[$OrderID]['connectcost'] = $OrderRec['connectcost'];
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
	$InvoiceID = createInvoice($Invoice);
	$GennedInvs[] = $InvoiceID;
	$CustNum = date("y", strtotime($CustRecs[$CustomerID]['dateregistered'])) . sprintf("%06d", $CustomerID);
	$InvTotal = 0;
	
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
		$SaveArr = array('orderstatus' => 4);
		updateUnitPackage($OrderID, $SaveArr);
	}
	$InvArr = array();
	$InvArr['nonvattotal'] = $InvTotal;
	$InvArr['vattotal'] = ($InvTotal * 1.14);
	updateInvoice($InvoiceID, $InvArr);
	$updQry = 'UPDATE customerdetails SET customerbalance += ' . $InvArr['vattotal'] . ' WHERE customerid = ' . $CustomerID;
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}
$Invs = getInvoices(implode(",", $GennedInvs));
foreach($Invs AS $InvID => $InvRec)
{
	$FileName = genInvoiceFile($InvID);
	$InvSaveArr = array();
	$InvSaveArr['filepath'] = $FileName;
	updateInvoice($InvoiceID, $InvSaveArr);
}
header("Location: orders.php");
?>