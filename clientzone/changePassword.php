<?php
session_start();
include_once("../db.inc.php");
$CustomerRec = getCustomerByID($_SESSION['customerid']);
$OldPass = pebkac($_POST['oldpass'], 100, 'STRING');
$NewPass = pebkac($_POST['newpass1'], 100, 'STRING');
$selQry = 'SELECT userpass FROM customerdetails WHERE customerid = ' . $_SESSION['customerid'];
$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
$selData = mysqli_fetch_array($selRes);
$HashPw = saltAndPepper($CustomerRec['email1'], $OldPass);
if(password_verify($HashPw, $selData['userpass']))
{
	$SaveArr = array();
	$SaveArr['userpass'] = hashPassword($CustomerRec['email1'], $NewPass);
	saveCustomer($_SESSION['customerid'], $SaveArr);
	$_SESSION['yaymsg'] = 'Password changed successfully';
}
else
	$_SESSION['errmsg'] = 'Incorrect password';
header("Location: dashboard.php");
?>