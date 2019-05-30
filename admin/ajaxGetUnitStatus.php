<?php
session_start();
include("db.inc.php");

$UnitID = pebkac($_POST['uid']); 
$UnitRec = getUnitByID($UnitID);
$retArr = array("status" => $UnitRec['statusid']);
echo json_encode($retArr);
?>