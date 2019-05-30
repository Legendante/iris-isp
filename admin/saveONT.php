<?php
include("db.inc.php");

$ONTID = pebkac($_POST['ONTID'], 5);
$SaveArr = array();
$SaveArr['ontname'] = pebkac($_POST['ontname'], 100, 'STRING');
$VendorArr = array();
foreach($_POST AS $Key => $Val)
{
	if(substr($Key, 0, 10) == 'ontvendor_')
	{
		$ID = substr($Key, 10);
		$VendorArr[$ID]['active'] = $Val;
	}
	if(substr($Key, 0, 9) == 'ontprice_')
	{
		$ID = substr($Key, 9);
		$VendorArr[$ID]['price'] = $Val;
	}
}
if($ONTID == 0)
	$ONTID = addONT($SaveArr);
else
	saveONT($ONTID, $SaveArr);
clearVendorONT($ONTID);
foreach($VendorArr AS $VendorID => $VendorRec)
{
	if((isset($VendorRec['active'])) || ($VendorRec['price'] != ''))
	{
		$Active = (isset($VendorRec['active'])) ? $VendorRec['active'] : 0;
		$Active = !$Active;		// Got to remember we're dealing with a IS NOT ACTIVE true/false flag
		addVendorONT($ONTID, $VendorID, $VendorRec['price'], $Active);
	}
}
header("Location: packageoptions.php");
?>