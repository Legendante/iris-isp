<?php
include("db.inc.php");

$PackageID = pebkac($_POST["PackageID"]);
$SaveArr = array();
$SaveArr['packagename'] = pebkac($_POST["packagename"], 100, "STRING");
$SaveArr['packagegroupid'] = pebkac($_POST["packagegroup"]);
$SaveArr['packagetype'] = pebkac($_POST["packtype"]);
$SaveArr['vendorid'] = pebkac($_POST["vendorid"]);
if($PackageID == 0)
	$PackageID = addPackage($SaveArr);
else
	savePackage($PackageID, $SaveArr);

$SpeedPieceID = 0;
$SpeedArr = array();
$SpeedArr['packageid'] = $PackageID;
$SpeedArr['speedid'] = pebkac($_POST["speedid"]);
$SpeedArr['piecescost'] = pebkac($_POST["speedcost"], 10, "STRING");
$SpeedArr['piecesnummonths'] = pebkac($_POST["speedmonths"], 3);
$SpeedArr['piecescomms'] = pebkac($_POST["speedcommission"], 10, "STRING");
$SpeedArr['endcontinues'] = pebkac($_POST["speedcontinues"], 1);
$SpeedPiece = getPackageSpeedPiece($PackageID);
if(!isset($SpeedPiece['pieceid']))
	$SpeedPieceID = addPackagePiece($SpeedArr);
else
{
	$SpeedPieceID = $SpeedPiece['pieceid'];
	savePackagePiece($SpeedPieceID, $SpeedArr);
}

$ONTPieceID = 0;
$ONTArr = array();
$ONTArr['packageid'] = $PackageID;
$ONTArr['ontid'] = pebkac($_POST["ontid"]);
$ONTArr['piecescost'] = pebkac($_POST["ontcost"], 10, "STRING");
$ONTArr['piecesnummonths'] = pebkac($_POST["ontmonths"], 3);
$ONTArr['endcontinues'] = pebkac($_POST["ontcontinues"], 1);
$ONTArr['piecescomms'] = pebkac($_POST["ontcommission"], 10, "STRING");
$ONTPiece = getPackageONTPiece($PackageID);
if(!isset($ONTArr['pieceid']))
	$ONTPieceID = addPackagePiece($ONTArr);
else
{
	$ONTPieceID = $ONTArr['pieceid'];
	savePackagePiece($ONTPieceID, $ONTArr);
}

$ExtraArr = array();
foreach($_POST AS $Key => $Val)
{
	if(substr($Key, 0, 13) == 'extrapieceid_')
	{
		$XArr = explode("_", $Key);
		$XCnt = $XArr[1];
		$ExtraArr[$XCnt]['pieceid'] = $Val;
		$ExtraArr[$XCnt]['packageid'] = $PackageID;
	}
	if(substr($Key, 0, 8) == 'extraid_')
	{
		$XArr = explode("_", $Key);
		$XCnt = $XArr[1];
		$ExtraArr[$XCnt]['extraid'] = $Val;
	}
	if(substr($Key, 0, 10) == 'extracost_')
	{
		$XArr = explode("_", $Key);
		$XCnt = $XArr[1];
		$ExtraArr[$XCnt]['piecescost'] = $Val;
	}
	if(substr($Key, 0, 12) == 'extramonths_')
	{
		$XArr = explode("_", $Key);
		$XCnt = $XArr[1];
		$ExtraArr[$XCnt]['piecesnummonths'] = $Val;
	}
	if(substr($Key, 0, 11) == 'extracomms_')
	{
		$XArr = explode("_", $Key);
		$XCnt = $XArr[1];
		$ExtraArr[$XCnt]['piecescomms'] = $Val;
	}
	if(substr($Key, 0, 6) == 'extra_')
	{
		$XArr = explode("_", $Key);
		$XCnt = $XArr[1];
		$ExtraArr[$XCnt]['endcontinues'] = $Val;
	}
}
foreach($ExtraArr AS $Cnt => $PieceRec)
{
	$PieceID = $PieceRec['pieceid'];
	unset($PieceRec['pieceid']);
	
	if($PieceID == 0)
		$PieceID = addPackagePiece($PieceRec);
	else
		savePackagePiece($PieceID, $PieceRec);
}
header("Location: packageindex.php");
// print_r($ExtraArr);
/*
addPackagePiece($SaveArr)
savePackagePiece($ExtraID, $SaveArr)

extrapieceid_
pebkac($_POST["extraid_0"]
pebkac($_POST["extracost_0"]
pebkac($_POST["extramonths_0"]
pebkac($_POST["extracomms_0"]
pebkac($_POST["extra_0_continues"]
//*/
?>