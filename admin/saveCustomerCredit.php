<?php
session_start();
include("db.inc.php");

$CustomerID = pebkac($_POST['CustomerID']);
$CredDays = pebkac($_POST['credDays']);
$Reason = pebkac($_POST['credReason'], 1000, 'STRING');
$Customer = getCustomerByID($CustomerID);
$Units = getCustomerUnits($CustomerID);
$UnitID = 0;
foreach($Units AS $UID => $UnitRec)
{
	$UnitID = $UID;
}
$Package = getUnitPackage($UnitID);
reset($Package);
$first_key = key($Package);
$SalesPrice = $Package[$first_key]['prevatsalesprice'];
$CostPerDay = round((($SalesPrice * 12) / 365), 2);
$CreditAmount = $CostPerDay * $CredDays;
$CreditArr = array();
$CreditArr['customerid'] = $CustomerID;
$CreditArr['creditby'] = $_SESSION['userid'];
$CreditArr['creditamount'] = $CreditAmount;
$CreditArr['creditdescription'] = $Reason;
addCreditNote($CreditArr);
$updQry = 'UPDATE customerdetails SET customerbalance = customerbalance - \'' . $CreditAmount . '\' WHERE customerid = ' . $CustomerID;
$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
header("Location: customer.php?cid=" . $CustomerID . "&uid=" . $UnitID);
?>