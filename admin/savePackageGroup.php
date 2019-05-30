<?php
session_start();
include("db.inc.php");

$GroupID = pebkac($_POST['GroupID']);
$SaveArr = array();
$SaveArr['packagegroupname'] = pebkac($_POST['groupname'], 100, 'STRING');
$Types = $_POST['typeid'];
if($GroupID == 0)
	$GroupID = addPackageGroup($SaveArr);
else
	savePackageGroup($GroupID, $SaveArr);
clearPackageGroupTypes($GroupID);
foreach($Types AS $ind => $TypeID)
{
	addPackageGroupTypes($GroupID, $TypeID);
}
header("Location: packageindex.php");
?>