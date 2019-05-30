<?php
session_start();
include("db.inc.php");
$CustomerID = pebkac($_GET['cid'], 5);
$UnitID = pebkac($_GET['uid'], 5);

$OrdQry = 'SELECT orderid FROM unitorderdetails WHERE unitid = ' . $UnitID . ' AND historyserial = 0';
$updRes = mysqli_query($dbCon, $OrdQry) or logDBError(mysqli_error($dbCon), $OrdQry, __FILE__, __FUNCTION__, __LINE__);
$updData = mysqli_fetch_array($updRes);
$OrderID = $updData['orderid'];

$OrderHistArr = array();
$OrderHistArr['orderid'] = $OrderID;
$OrderHistArr['eventdescr'] = "Order deleted";
$OrderHistArr['eventcomment'] = "Deleted an order";
$OrderHistArr['userid'] = $_SESSION['userid'];
addUnitOrderHistory($OrderHistArr);
$insQry = 'UPDATE unitorderdetails SET historyserial = historyserial + 1 WHERE unitid = ' . $UnitID;
$updRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
header("Location: orders.php");
?>