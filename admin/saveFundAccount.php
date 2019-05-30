<?php
include("db.inc.php");

$AccountID = pebkac($_POST['AccountID']);
$AccountName = pebkac($_POST['accountname'], 30, 'STRING');
$SaveArr = array();
$SaveArr['accountname'] = $AccountName;
if($AccountID == 0)
	$AccountID = addFundAccount($SaveArr);
else
	updateFundAccount($AccountID, $SaveArr);
header("Location: accounts.php");
?>