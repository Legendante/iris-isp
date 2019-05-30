<?php
session_start();
include("db.inc.php");

$ContactArr = array();
$ComplexID = pebkac($_POST['BCCComplexID']);
$ContactID = pebkac($_POST['BCCContactID']);
$ContactArr['complexid'] = $ComplexID;
$ContactArr['contactname'] = pebkac($_POST['contactname'], 100, 'STRING');
$ContactArr['contactsurname'] = pebkac($_POST['contactsurname'], 100, 'STRING');
$ContactArr['contactemail'] = pebkac($_POST['contactemail'], 100, 'STRING');
$ContactArr['contactcell'] = pebkac($_POST['contactcell'], 30, 'STRING');
$ContactArr['contacttel'] = pebkac($_POST['contacttel'], 30, 'STRING');
$ContactArr['designation'] = pebkac($_POST['designation'], 50, 'STRING');
$ContactArr['unitnum'] = pebkac($_POST['contactunit'], 30, 'STRING');
$ContactArr['addedby'] = $_SESSION['userid'];
if($ContactID == 0)
	addBodyCorpContact($ContactArr);
else
	saveBodyCorpContact($ContactID, $ContactArr);
header("Location: complex.php?cid=" . $ComplexID);
?>