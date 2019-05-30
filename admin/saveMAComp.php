<?php
session_start();
include("db.inc.php");

$CompID = pebkac($_POST['CompID']);
$SaveArr = array();
$SaveArr['agentname'] = pebkac($_POST['compname'], 100, 'STRING');
if($CompID == 0)
	$CompID = addManagingAgents($SaveArr);
else
	saveManagingAgents($CompID, $SaveArr);
header("Location: managents.php");
?>