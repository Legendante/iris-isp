<?php
session_start();
include("db.inc.php");

$Day = pebkac($_POST['d'], 10, 'STRING');
$SpeedID = pebkac($_POST['s']);
$PackageArr = getFibrePackages();
$Cost = 0;
foreach($PackageArr AS $PackageID => $PackRec)
{
	if(($PackRec['speedid'] == $SpeedID) && ($PackRec['termnum'] == 1))
		$Cost = $PackRec['prevatsalesprice'];
}
$StartDay = date("d", strtotime($Day));
$EndDay = date("t", strtotime($Day));
$DateDiff = $EndDay - $StartDay + 1;
$CostPerDay = round((($Cost * 12) / 365), 2);
if($StartDay == 1)
	$SpeedCost = $Items[$OrderID]['prevatcost'];
else
	$SpeedCost = round($CostPerDay * $DateDiff, 2);
echo $SpeedCost;
?>