<?php
session_start();
include("db.inc.php");

$InvoiceID = pebkac($_GET['i']);
$Source = pebkac($_GET['s']);
$Invoice = getInvoices($InvoiceID);
$Invoice = $Invoice[$InvoiceID];
$CustomerID = $Invoice['customerid'];

$Units = getCustomerUnits($CustomerID);
$UnitID = 0;
foreach($Units AS $UID => $UnitRec)
{
	$UnitID = $UID;
}
$Package = getUnitPackage($UnitID);
foreach($Package AS $OrderID => $OrderRec)
{
	if($OrderRec['orderstatus'] == 3)
	{
		$SaveArr = array();
		$SaveArr['orderstatus'] = 2;
		updateUnitPackage($OrderID, $SaveArr);
	}
}
$UpdArr = array();
$UpdArr['invstatus'] = 10;
updateInvoice($InvoiceID, $UpdArr);
$updQry = 'UPDATE customerdetails SET customerbalance = customerbalance - \'' . $Invoice['vattotal'] . '\' WHERE customerid = ' . $CustomerID;
$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
if($Source == 2)
	header("Location: unpaidinvs.php");
else
	header("Location: unsentinvs.php");
?>