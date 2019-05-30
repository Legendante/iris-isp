<?php
include("header.inc.php");

$selQry = 'SELECT complexid, complexname, subdomain FROM complexdetails ORDER BY complexname, subdomain';
$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
$retArr = array();
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Complex Name</th><th>DB Subdomain</th><th>Genned Subdomain</th></tr>";
$ChkArr = array();
while($selData = mysqli_fetch_array($selRes))
{
	$SubD = strtolower(str_replace("'", "", $selData['complexname']));
	$SubD = strtolower(str_replace("(", "", $SubD));
	$SubD = strtolower(str_replace(")", "", $SubD));
	$SubD = strtolower(str_replace(" ", "", $SubD));
	if(!isset($ChkArr[$SubD]))
		$ChkArr[$SubD] = 1;
	else
		$ChkArr[$SubD]++;
	echo "<tr>";
	echo "<td>" . $selData['complexid'] . "</td>";
	echo "<td>" . $selData['complexname'] . "</td>";
	echo "<td>" . $selData['subdomain'] . "</td>";
	echo "<td>" . $SubD . "</td>";
	echo "</tr>";
	if(($selData['subdomain'] == '') && ($ChkArr[$SubD] == 1))
	{
		$updQry = 'UPDATE complexdetails SET subdomain = "' . $SubD . '" WHERE complexid = ' . $selData['complexid'];
		$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	}
}
echo "</table>";
foreach($ChkArr AS $Sub => $Cnt)
{
	if($Cnt > 1)
		echo $Sub . "<br>";
}
?>