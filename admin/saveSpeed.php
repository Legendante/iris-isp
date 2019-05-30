<?php
include("db.inc.php");

$SpeedID = pebkac($_POST['SpeedID'], 5);
$SaveArr = array();
$SaveArr['speedname'] = pebkac($_POST['speedname'], 100, 'STRING');
$VendorArr = array();
foreach($_POST AS $Key => $Val)
{
	if(substr($Key, 0, 12) == 'speedvendor_')
	{
		$ID = substr($Key, 12);
		$VendorArr[$ID]['active'] = $Val;
	}
	if(substr($Key, 0, 11) == 'speedprice_')
	{
		$ID = substr($Key, 11);
		$VendorArr[$ID]['price'] = $Val;
	}
}
if($SpeedID == 0)
	$SpeedID = addSpeed($SaveArr);
else
	saveSpeed($SpeedID, $SaveArr);
clearVendorSpeed($SpeedID);
foreach($VendorArr AS $VendorID => $VendorRec)
{
	if((isset($VendorRec['active'])) || ($VendorRec['price'] != ''))
	{
		$Active = (isset($VendorRec['active'])) ? $VendorRec['active'] : 0;
		$Active = !$Active;		// Got to remember we're dealing with a IS NOT ACTIVE true/false flag
		addVendorSpeed($SpeedID, $VendorID, $VendorRec['price'], $Active);
	}
}
header("Location: packageoptions.php");
?>