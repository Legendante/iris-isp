<?php
session_start();
include("db.inc.php");

// print_r($_POST);
// exit();
$ComplexID = pebkac($_POST['ComplexID']); 
$StatusID = pebkac($_POST['complexstatus']); 
$Comment = trim(pebkac($_POST['statuscomment'], 1000, 'STRING')); 
$ComplexRec = getComplexByID($ComplexID);
$ComplexStatusID = $ComplexRec['complexstatusid'];
if($StatusID != $ComplexRec['statusid'])
{
	$ComplexStatusID = addComplexStatus($ComplexID, $StatusID, $_SESSION['userid']);
	if($StatusID == 47)
	{
		$CompOrders = getComplexUnitPackages($ComplexID);
		$SaveArr = array('orderstatus' => 3);
		foreach($CompOrders AS $UnitID => $UnitOrds)
		{
			foreach($UnitOrds AS $OrderID => $OrderRec)
			{
				if($OrderRec['orderstatus'] == 2)
					updateUnitPackage($OrderID, $SaveArr);
			}
		}
	}
}
if(($Comment != '') || ($StatusID != $ComplexRec['statusid']))
	addComplexStatusComment($ComplexStatusID, $Comment, $_SESSION['userid']);
header("Location: complex.php?cid=" . $ComplexID);
?>