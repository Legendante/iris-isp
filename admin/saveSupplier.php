<?php
include("db.inc.php");
$SupplierID = pebkac($_POST['SupplierID']);
$SaveArr = array();
$SaveArr['suppliername'] = pebkac($_POST['supname'], 100, 'STRING');
$SaveArr['supplierregnum'] = pebkac($_POST['regnum'], 20, 'STRING');
$SaveArr['suppliervatnum'] = pebkac($_POST['vatnum'], 20, 'STRING');
$SaveArr['supplieremail'] = pebkac($_POST['supemail'], 50, 'STRING');
$SaveArr['suppliertel'] = pebkac($_POST['suptel'], 20, 'STRING');
$SaveArr['supplieraddress'] = pebkac($_POST['supaddie'], 200, 'STRING');
if($SupplierID == 0)
	$SupplierID = addSupplier($SaveArr);
else
	updateSupplier($SupplierID, $SaveArr);
header("Location: supplierDetails.php?s=" . $SupplierID);
?>