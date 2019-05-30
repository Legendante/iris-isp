<?php
session_start();
include("db.inc.php");

$SecID = pebkac($_POST['cid']);
$retArr = array();
$retArr = getManagingAgentByID($SecID);
echo json_encode($retArr);
?>