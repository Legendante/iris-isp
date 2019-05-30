<?php
include_once("db.inc.php");
include_once("iris.inc.php");
$VendorList = pebkac($_POST['vlist'], 30, 'STRING');
$PackageType = pebkac($_POST['type']);
$Speeds = getPackageSpeeds();
$ONTs = getONTTypes();
$Packages = getVendorPackages($VendorList);
$PackageArr = array();
foreach($Packages AS $PackageID => $PieceRec)
{
	if($PieceRec[0]['packagetype'] == $PackageType)
	{
		$PackageArr[$PackageID] = array();
		$PackageArr[$PackageID]['packagename'] = $PieceRec[0]['packagename'];
		foreach($PieceRec AS $PieceID => $Piece)
		{
			if(!isset($PackageArr[$PackageID]['oncecost']))
				$PackageArr[$PackageID]['oncecost'] = 0;
			if(isset($Piece['piecesnummonths']) && ($Piece['piecesnummonths'] > 1))
			{
				$PackageArr[$PackageID]['nummonths'] = $Piece['piecesnummonths'];
				$PackageArr[$PackageID]['monthcost'] = $Piece['piecescost'];
			}
			if(isset($Piece['piecesnummonths']) && ($Piece['piecesnummonths'] == 1))
				$PackageArr[$PackageID]['oncecost'] += $Piece['piecescost'];
			if(!isset($PackageArr[$PackageID]))
				$PackageArr[$PackageID] = array('speedid' => '', 'ont' => '');
			if((isset($Piece['speedid'])) && ($Piece['speedid'] != 0))
				$PackageArr[$PackageID]['speed'] = $Piece['speedid'];
			if((isset($Piece['ontid'])) && ($Piece['ontid'] != 0))
				$PackageArr[$PackageID]['ont'] = $Piece['ontid'];
			if((isset($Piece['extraid'])) && ($Piece['extraid'] != 0))
				$PackageArr[$PackageID][$PieceID]['extra'] = $Piece['extraid'];
		}
	}
}
echo "<table class='table table-bordered'>";
echo "<tr><th></th><th>Package</th><th>Contract Period</th><th>Speed</th><th>ONT</th><th>Installation Cost</th><th>Monthly Cost</th></tr>";
foreach($PackageArr AS $PackageID => $PackRec)
{
	$OnceCost = ($PackRec['oncecost'] > 0) ? "R " . $PackRec['oncecost'] : "-";
	echo "<tr>";
	echo "<td><input type='radio' name='packageid' id='packageid_" . $PackageID . "' value='" . $PackageID . "' class='validate[required] radio'></td>";
	echo "<td><label id='packageid_" . $PackageID . "'>" . $PackRec['packagename'] . "</label></td>";
	echo "<td>" . $PackRec['nummonths'] . "</td>";
	echo "<td>" . $Speeds[$PackRec['speed']] . "</td>";
	echo "<td>" . $ONTs[$PackRec['ont']] . "</td>";
	echo "<td>" . $OnceCost . "</td>";
	echo "<td>R " . $PackRec['monthcost'] . "</td>";
	echo "</tr>";
}
echo "</table>";
// print_r($PackageArr);
?>