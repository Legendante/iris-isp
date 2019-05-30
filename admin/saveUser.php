<?php
include("db.inc.php");

$UserID = $_POST['UserID'];
$SaveArr = array();
$SaveArr['firstname'] = trim(pebkac($_POST['fname'], 100, 'STRING'));
$SaveArr['surname'] = trim(pebkac($_POST['sname'], 100, 'STRING'));
$SaveArr['cellnumber'] = trim(pebkac($_POST['cell1'], 30, 'STRING'));
$SaveArr['telnumber'] = trim(pebkac($_POST['tel1'], 30, 'STRING'));
$SaveArr['inactive'] = trim(pebkac($_POST['userstatus'], 1));
if($UserID == 0)
{
	$SaveArr['username'] = trim(pebkac($_POST['email1'], 100, 'STRING'));
	$SaveArr['userpass'] = trim(pebkac($_POST['usrpass'], 100, 'STRING'));
	$UserID = addUser($SaveArr);
}
else
	saveUser($UserID, $SaveArr);
$PrivArr = $_POST['priv'];
clearUserPrivileges($UserID);
foreach($PrivArr AS $ind => $PrivID)
{
	addUserPrivilege($UserID, $PrivID);
}
header("Location: userindex.php");
?>