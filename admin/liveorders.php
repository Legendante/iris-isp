<?php
include("header.inc.php");

$Orders = getUnitOrders(3);
$VendorSpeeds = getPackageSpeeds();
$SpeedTotals = array();
foreach($VendorSpeeds AS $SpeedID => $Speed)
{
	$SpeedTotals[$SpeedID] = 0;
}
foreach($Orders AS $UnitID => $OrderRec)
{
	foreach($OrderRec['orders'] AS $OrderID => $Rec)
	{
		$SpeedTotals[$Rec['speedid']]++;
	}
}

foreach($VendorSpeeds AS $SpeedID => $Speed)
{
	echo $Speed . " " . $SpeedTotals[$SpeedID] . "<br>\n";
}

// $Customers = getCustomersByIDList(implode(",", $CustomerIDList));
// $Units = getUnitsByIDList(implode(",", $UnitIDList));
// foreach($Units AS $UnitID => $UnitRec)
// {
	// $ComplexIDList[] = $UnitRec['complexid'];
// }
// $Complexes = getComplexesByIDList(implode(",", $ComplexIDList));
// print_r($DoneOrder);
// foreach($DoneOrder AS $UnitID => $OrderRec)
// {
	// echo $Customers[$OrderRec['customerid']]['customername'] . " " . $Customers[$OrderRec['customerid']]['customersurname'] . " ";
	// echo $Units[$UnitID]['unitnumber'] . " " . $Complexes[$Units[$UnitID]['complexid']]['complexname'] . "<br>";
// }
include("footer.inc.php");
?>