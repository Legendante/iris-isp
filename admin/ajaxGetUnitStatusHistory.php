<?php
session_start();
include("db.inc.php");
$UnitID = pebkac($_POST['uid']); 
$StatusHistory = getUnitStatusHistory($UnitID);
$Agents = getAgents();
$Statusses = getUnitStatusses();
$Statusses[0] = array("statusname" => "-");
$Agents[0] = array("firstname" => "Unknown", "surname" => "User");
echo "<tr><th>Status</th><th>User</th><th>Date</th><th>Comment</th></tr>\n";
$PrevStatus = 0;
foreach($StatusHistory AS $cnt => $HistRec)
{
	echo "<tr>";
	if($PrevStatus != $HistRec['statusid'])
	{
		echo "<td>" . $Statusses[$HistRec['statusid']]['statusname'] . "</td>";
		echo "<td>" . $Agents[$HistRec['statususer']]['firstname'] . " " . $Agents[$HistRec['statususer']]['surname'] . "</td>";
		echo "<td>" . $HistRec['statusdate'] . "</td>";
		echo "<td>" . $HistRec['commentary'] . "</td>";
		$PrevStatus = $HistRec['statusid'];
	}
	else
	{
		echo "<td>&nbsp;</td>";
		echo "<td>" . $Agents[$HistRec['commentuser']]['firstname'] . " " . $Agents[$HistRec['commentuser']]['surname'] . "</td>";
		echo "<td>" . $HistRec['commentdate'] . "</td>";
		echo "<td>" . $HistRec['commentary'] . "</td>";
	}
	echo "</tr>";
}
?>