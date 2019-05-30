<?php
include_once("db.inc.php");
include_once("iris.inc.php");

$Where = array();
$Where['speedid'] = pebkac($_POST['s']);
$Where['termnum'] = pebkac($_POST['t']);
$Where['ontid'] = pebkac($_POST['o']);
$PackageArr = getFibrePackages($Where);
$PackageArr = reset($PackageArr);
$VendorSpeeds = getPackageSpeeds();
$VendorONTs = getONTTypes();
$RetArr = array();
$RetArr['packageid'] = $PackageArr['packageid'];
$RetArr['monthlycost'] = $PackageArr['monthlycost'];
$RetArr['ontcost'] = $PackageArr['ontcost'];
$RetArr['connectcost'] = $PackageArr['connectcost'];
echo json_encode($RetArr);
?>