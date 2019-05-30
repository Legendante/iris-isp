<?php
session_start();
include("db.inc.php");
$selQry = "SELECT complexid, complexname, complexcode FROM `complexdetails` WHERE complexcode like '% %'";
$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
$retArr = array();
while($selData = mysqli_fetch_array($selRes))
{
	$ComplexName = strtoupper($selData['complexname']);
	if(substr($ComplexName, 0, 4) == 'THE ')
		$ComplexName = trim(substr($ComplexName, 4));
	$ComplexName = str_replace(" ", "", $ComplexName);
	$NotFound = 1;
	$Cnt = 0;
	while($NotFound == 1)
	{
		switch($Cnt)
		{
			case 0:
				$ShortCode = substr($ComplexName, 0, 4);
				break;
			case 1:
				$TmpName = str_replace("A", "", $ComplexName);
				$ShortCode = substr($TmpName, 0, 4);
				break;
			case 2:
				$TmpName = str_replace("E", "", $ComplexName);
				$ShortCode = substr($TmpName, 0, 4);
				break;
			case 3:
				$TmpName = str_replace("I", "", $ComplexName);
				$ShortCode = substr($TmpName, 0, 4);
				break;
			case 4:
				$TmpName = str_replace("O", "", $ComplexName);
				$ShortCode = substr($TmpName, 0, 4);
				break;
			case 5:
				$TmpName = str_replace("U", "", $ComplexName);
				$ShortCode = substr($TmpName, 0, 4);
				break;
			default:
				$ShortCode = "";
				$NotFound = 0;
				break;
		}
		$CompRec = getComplexByShortCode($ShortCode);
		if(!isset($CompRec['complexid']))
		{
			$NotFound = 0;
		}
		$Cnt++;
	}
	if($ShortCode != "")
	{
		$updQry = "UPDATE complexdetails SET complexcode = '" . $ShortCode . "' WHERE complexid = " . $selData['complexid'];
		$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
		echo "Changed code for " . $selData['complexname'] . " from '" . $selData['complexcode'] . "' to '" . $ShortCode . "'<br>\n";
	}
}
echo "Done";
?>