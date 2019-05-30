<?php
session_start();
include("db.inc.php");
$CustomerID = pebkac($_POST['CustUnitID']);
$UnitID = pebkac($_POST['PackUnitID']);
$SpeedID = pebkac($_POST['packspeed']);
$ONTID = pebkac($_POST['packont']);
$PeriodID = pebkac($_POST['packper']);
if($SpeedID == 0)
{
	$ONTID = 0;
	$PeriodID = 0;
}

$packQry = 'SELECT packageid, termnum, vendorid, connectcost, monthlycost, prevatsalesprice, prevatconnectcost FROM fibrepackages WHERE termnum IN (0,1) AND speedid = ' . $SpeedID . ' AND ontid = ' . $ONTID;
$updRes = mysqli_query($dbCon, $packQry) or logDBError(mysqli_error($dbCon), $packQry, __FILE__, __FUNCTION__, __LINE__);
$updData = mysqli_fetch_array($updRes);
$PackageID = $updData['packageid'];

$OrdQry = 'SELECT orderid FROM unitorderdetails WHERE unitid = ' . $UnitID . ' AND historyserial = 0';
$updRes = mysqli_query($dbCon, $OrdQry) or logDBError(mysqli_error($dbCon), $OrdQry, __FILE__, __FUNCTION__, __LINE__);
$updData = mysqli_fetch_array($updRes);
$OrderID = $updData['orderid'];

$OrderHistArr = array();
$OrderHistArr['orderid'] = $OrderID;
$OrderHistArr['eventdescr'] = "Order changed";
$OrderHistArr['eventcomment'] = "Changed an order";
$OrderHistArr['userid'] = $_SESSION['userid'];
addUnitOrderHistory($OrderHistArr);
// updateUnitPackageHistory($OrderID);
$insQry = 'UPDATE unitorderdetails SET historyserial = historyserial + 1 WHERE unitid = ' . $UnitID;
$updRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);

$Status = 1;
// $OrderID = addUnitPackage($UnitID, $PackageID, $CustomerID, $OrderArr['orderstatus']);
$ONTCost = (isset($VendorONTs[$ONTID]['ontcost'])) ? $VendorONTs[$ONTID]['ontcost'] : 0;
$insQry = 'INSERT INTO unitorderdetails(unitid, customerid, orderdate, packageid, termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, prevatsalesprice, orderstatus, historyserial) ';
$insQry .= 'SELECT ' . $UnitID . ', ' . $CustomerID . ', NOW(), packageid, termnum, ' . $SpeedID . ', ' . $ONTID . ', vendorid, ' . $ONTCost . ', connectcost, monthlycost, prevatsalesprice, ' . $Status . ', 0 ';
$insQry .= 'FROM fibrepackages WHERE packageid = ' . $PackageID;
// echo $insQry;
$updRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
header("Location: customer.php?cid=" . $CustomerID . "&uid=" . $UnitID);
?>