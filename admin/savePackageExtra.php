<?php
include("db.inc.php");

$ExtraID = pebkac($_POST['ExtraID']);
$SaveArr = array();
$SaveArr['extraname'] = pebkac($_POST['extraname'], 100, 'STRING');
$SaveArr['costprice'] = pebkac($_POST['extraprice'], 10, 'STRING');

if($ExtraID == 0)
	$ExtraID = addPackageExtra($SaveArr);
else
	savePackageExtra($ExtraID, $SaveArr);
header("Location: packageoptions.php");
?>