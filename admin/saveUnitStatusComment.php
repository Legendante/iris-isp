<?php
session_start();
include("db.inc.php");

$UnitID = pebkac($_POST['UnitID']); 
$StatusID = pebkac($_POST['unitstatus']); 
$Comment = trim(pebkac($_POST['statuscomment'], 1000, 'STRING')); 
$UnitRec = getUnitByID($UnitID);
$CustomerID = $UnitRec['customerid'];
$UnitStatusID = $UnitRec['unitstatusid'];
if($StatusID != $UnitRec['statusid'])
	$UnitStatusID = addUnitStatus($UnitID, $StatusID, $_SESSION['userid']);
if(($Comment != '') || ($StatusID != $UnitRec['statusid']))
	addUnitStatusComment($UnitStatusID, $Comment, $_SESSION['userid']);
header("Location: customer.php?cid=" . $CustomerID . "&uid=" . $UnitID);
?>