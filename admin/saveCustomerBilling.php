<?php
include("db.inc.php");

$CustomerID = pebkac($_POST['CustomerID']); 
$BillingID = pebkac($_POST['BillingID']);
$BillingArr['customerid'] = $CustomerID;
$BillingArr['billingname'] = pebkac($_POST['billingname'], 100, 'STRING');
$BillingArr['billingcontact'] = pebkac($_POST['billingcontact'], 100, 'STRING');
$BillingArr['billingemail'] = pebkac($_POST['billingemail'], 100, 'STRING');
$BillingArr['billingcell'] = pebkac($_POST['billingcell'], 100, 'STRING');

if($BillingID == 0)
	addBilling($BillingArr);
else
	saveBilling($BillingID, $BillingArr);

header("Location: Customer.php?cid=" . $CustomerID);
?>