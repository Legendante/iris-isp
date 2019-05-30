<?php
include_once("../db.inc.php");
$PID = $_POST['pid'];
$Name = mysqli_real_escape_string($dbCon, $_POST['name']);
$Status = $_POST['pstat'];
$YN = $_POST['pyn'];
$Vids = $_POST['vids'];
$VidArr = explode(",", $Vids);
$VidArr = array_unique($VidArr);
$updQry = 'UPDATE precinctpoints SET precintname = "' . $Name . '" WHERE precinctid = ' . $PID;
$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
$updQry = 'UPDATE precinctpointdetails SET pointstatus = ' . $Status . ', isprecinct = ' . $YN . ' WHERE precinctid = ' . $PID;
$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
$updQry = 'UPDATE precinctpointvendors SET precinctid = precinctid * -1 WHERE precinctid = ' . $PID;
$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
foreach($VidArr AS $Key => $Val)
{
	if($Val != '')
	{
		$updQry = 'INSERT INTO precinctpointvendors(precinctid, vendorid) VALUES (' . $PID . ', ' . $Val . ')';
		$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	}
}
echo "DONE";
?>