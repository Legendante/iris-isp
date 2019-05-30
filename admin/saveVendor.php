<?php
include("db.inc.php");

$VendorID = pebkac($_POST['VendorID'], 5);
$SaveArr = array();
$SaveArr['vendorname'] = pebkac($_POST['vendorname'], 100, 'STRING');
if($VendorID == 0)
	$VendorID = addVendor($SaveArr);
else
	saveVendor($VendorID, $SaveArr);
header("Location: packageoptions.php");
?>