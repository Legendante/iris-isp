<?php
session_start();
include("db.inc.php");

$CompID = pebkac($_POST['CompID']);
$SaveArr = array();
$SaveArr['secname'] = pebkac($_POST['compname'], 100, 'STRING');
if($CompID == 0)
	$CompID = addSecurityCompanies($SaveArr);
else
	saveSecurityCompanies($CompID, $SaveArr);
header("Location: securecomps.php");
?>