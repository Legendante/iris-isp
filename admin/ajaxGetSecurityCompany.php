<?php
session_start();
include("db.inc.php");

$SecID = pebkac($_POST['cid']);
$retArr = array();
$retArr = getSecurityCompanyByID($SecID);
echo json_encode($retArr);
?>