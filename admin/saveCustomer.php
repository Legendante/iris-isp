<?php
include("db.inc.php");

$CustomerArr = array();
$CustomerID = pebkac($_POST['CustomerID']); 
$CustomerArr['customername'] = pebkac($_POST['customername'], 100, 'STRING');
$CustomerArr['customersurname'] = pebkac($_POST['customersurname'], 100, 'STRING');
$CustomerArr['idnumber'] = pebkac($_POST['idnumber'], 30, 'STRING');
$CustomerArr['email1'] = pebkac($_POST['email1'], 30, 'STRING');
$CustomerArr['cell1'] = pebkac($_POST['cell1'], 30, 'STRING');
$CustomerArr['tel1'] = pebkac($_POST['tel1'], 30, 'STRING');
saveCustomer($CustomerID, $CustomerArr);

if(isset($_POST['BillingID']))
{
	$BillingID = pebkac($_POST['BillingID']);
	$BillingArr['billingname'] = pebkac($_POST['billingname'], 100, 'STRING');
	$BillingArr['billingcontact'] = pebkac($_POST['billingcontact'], 100, 'STRING');
	$BillingArr['billingemail'] = pebkac($_POST['billingemail'], 100, 'STRING');
	$BillingArr['billingcell'] = pebkac($_POST['billingcell'], 100, 'STRING');

	saveBilling($BillingID, $BillingArr);
}
header("Location: customer.php?cid=" . $CustomerID);
?>