<?php
session_start();
include("db.inc.php");

$ComplexID = pebkac($_POST['ComplexID']);
$ComplexArr = getComplexByID($ComplexID);
$CurrentMap = getComplexUnitMap($ComplexID);
$MapArr = array();
foreach($_POST AS $Key => $Val)
{
	if(substr($Key, 0, 4) == 'map_')
	{
		$ID = substr($Key, 4);
		$MapArr[$ID]['desc'] = $Val;
		// $MapArr[$ID]['hoa'] = 0;
		if(isset($_POST['hoa_' . $ID]))
			$MapArr[$ID]['hoa'] = $_POST['hoa_' . $ID];
	}
}
foreach($MapArr AS $MapID => $MapRec)
{
	$SaveArr = array("unitdesc" => $MapRec['desc']);
	if(isset($MapRec['hoa']))
	{
		if($CurrentMap[$MapID]['unitid'] == '')
		{
			$UnitArr = array();
			$UnitArr['customerid'] = $ComplexArr['customerid'];
			$UnitArr['complexid'] = $ComplexID;
			$UnitID = addUnit($UnitArr);
			$SaveArr = array();
			$SaveArr['customerid'] = $ComplexArr['customerid'];
			$SaveArr['complexid'] = $ComplexID;
			$SaveArr['unitid'] = $UnitID;
			$SaveArr['unitdesc'] = $MapRec['desc'];
			$SaveArr['hoaunit'] = $MapRec['hoa'];
		}
		else
		{
			$SaveArr['unitdesc'] = $MapRec['desc'];
			$SaveArr['hoaunit'] = $MapRec['hoa'];
		}
		saveComplexMapUnit($MapID, $SaveArr);
	}
	elseif($CurrentMap[$MapID]['customerid'] == '')
		saveComplexMapUnit($MapID, $SaveArr);
}
header("Location: complex.php?cid=" . $ComplexID);
?>