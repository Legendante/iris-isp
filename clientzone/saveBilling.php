<?php
session_start();
include_once("../db.inc.php");
$SaveArr = array();
$BillingID = pebkac($_POST['billingid']);
$SaveArr['billingname'] = pebkac($_POST['billname'], 100, 'STRING');
$SaveArr['billingcontact'] = pebkac($_POST['billcontact'], 100, 'STRING');
$SaveArr['billingemail'] = pebkac($_POST['billemail'], 100, 'STRING');
$SaveArr['billingcell'] = pebkac($_POST['billcell'], 30, 'STRING');
if($BillingID == '')
{
	$SaveArr['customerid'] = $_SESSION['customerid'];
	$BillingID = addBilling($SaveArr);
}
else
	saveBilling($BillingID, $SaveArr);
// saveCustomer($_SESSION['customerid'], $SaveArr);
header("Location: dashboard.php");
?>