<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
session_start();
include("../db.inc.php");
$OrderID = pebkac($_POST['orderid']);
$PackageID = pebkac($_POST['packageid']);
$SpeedID = pebkac($_POST['packspeed']);
// $TermID = pebkac($_POST['packterm']);
$ONTID = pebkac($_POST['packont']);
// $OrderArr = getUnitOrderByID($OrderID);
// $UnitID = $OrderArr['unitid'];
$VendorSpeeds = getPackageSpeeds();
// STEP BY STEP
// STEP 1 : DETERMINE IF CUSTOMER HAS EXISTING "INACTIVE" ORDERS AND SET THEM HISTORICAL
$insQry = 'UPDATE unitorderdetails SET historyserial = historyserial + 1 WHERE customerid = ' . $_SESSION['customerid'] . ' AND orderstatus != 3'; //NOT IN (3, 10)'; // "Remove" any orders that have not been completed yet.
$updRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);

// STEP 2 : GET "ACTIVE" ORDER DETAILS
$selQry = 'SELECT orderid, customerid, unitid, orderdate, packageid, termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, prevatsalesprice, prevatconnectcost FROM unitorderdetails ';
$selQry .= 'WHERE customerid = ' . $_SESSION['customerid'] . ' AND canceldate IS NULL AND historyserial = 0 AND orderstatus = 3';
$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
$ActiveOrder = mysqli_fetch_array($selRes);
$OrderID = $ActiveOrder['orderid'];
$UnitID = $ActiveOrder['unitid'];
// STEP 3 : DETERMINE IF NEW ORDER IS UPGRADE OR DOWNGRADE
$packQry = 'SELECT packageid, speedid, termnum, vendorid, connectcost, monthlycost, prevatsalesprice, prevatconnectcost FROM fibrepackages WHERE termnum IN (0,1) AND speedid = ' . $SpeedID . ' AND ontid = ' . $ONTID;
$updRes = mysqli_query($dbCon, $packQry) or logDBError(mysqli_error($dbCon), $packQry, __FILE__, __FUNCTION__, __LINE__);
$NewOrder = mysqli_fetch_array($updRes);
$PackageID = $NewOrder['packageid'];
// echo $VendorSpeeds[$ActiveOrder['speedid']] . " :: " . $VendorSpeeds[$NewOrder['speedid']] . "<br>";
if($VendorSpeeds[$ActiveOrder['speedid']] <= $VendorSpeeds[$NewOrder['speedid']]) // It's an upgrade
	$Status = 4;
else
	$Status = 6;
// STEP 4 : CREATE NEW ORDER RECORD
$ONTCost = (isset($VendorONTs[$ONTID]['ontcost'])) ? $VendorONTs[$ONTID]['ontcost'] : 0;
$insQry = 'INSERT INTO unitorderdetails(unitid, customerid, orderdate, packageid, termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, prevatsalesprice, orderstatus, prevatconnectcost, historyserial) ';
$insQry .= 'SELECT ' . $UnitID . ', ' . $_SESSION['customerid'] . ', NOW(), packageid, termnum, ' . $SpeedID . ', ' . $ONTID . ', vendorid, ' . $ONTCost . ', connectcost, monthlycost, prevatsalesprice, ' . $Status . ', prevatconnectcost, 0 ';
$insQry .= 'FROM fibrepackages WHERE packageid = ' . $PackageID;
$updRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
$NewOrderID = mysqli_insert_id($dbCon);
// STEP 5 : LOG ORDER CHANGE EVENT
$OrderHistArr = array();
$OrderHistArr['orderid'] = $OrderID;
$OrderHistArr['eventdescr'] = "Order changed";
$OrderHistArr['eventcomment'] = "Customer changed an order. Old Order = " . $OrderID . ", New Order = " . $NewOrderID;
$OrderHistArr['userid'] = $_SESSION['customerid'];
addUnitOrderHistory($OrderHistArr);
// exit();
header("Location: changepackage.php?o=" . $OrderID);
?>