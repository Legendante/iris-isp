<?php
session_start();
include_once("header.inc.php");
$OrderID = pebkac($_GET['o']);
// $OrderArr = getUnitOrderByID($OrderID);
$selQry = 'SELECT customerid, unitid, orderdate, packageid, termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, prevatsalesprice, prevatconnectcost FROM unitorderdetails ';
$selQry .= 'WHERE customerid = ' . $_SESSION['customerid'] . ' AND canceldate IS NULL AND historyserial = 0 AND orderstatus = 3';
$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
$OrderArr = mysqli_fetch_array($selRes);

$selQry = 'SELECT customerid, unitid, orderdate, packageid, termnum, speedid, ontid, orderstatus, monthlycost FROM unitorderdetails ';
$selQry .= 'WHERE customerid = ' . $_SESSION['customerid'] . ' AND canceldate IS NULL AND historyserial = 0 AND orderstatus IN (4,5,6,7,8,9)';
$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
$OrderChange = mysqli_fetch_array($selRes);

// print_r($OrderChange);

$PackageArr = getFibrePackages();
$VendorSpeeds = getPackageSpeeds();
$VendorSpeeds[0] = '-';
$VendorONTs = getONTTypes();
$Packages = array();
$Speeds = array();
$ONTs = array();
$Months = array();
$PackID = 0;
// $MonthCost = 0;
// $ONTCost = 0;
// $ConnCost = 0;
foreach($PackageArr AS $PackageID => $PackageRec)
{
	if($PackageRec['speedid'] > 0)
	{
		$Speeds[$PackageRec['speedid']]['id'] = $PackageRec['speedid'];
		$Speeds[$PackageRec['speedid']]['name'] = $PackageRec['packagename'];
		$Speeds[$PackageRec['speedid']]['speed'] = $VendorSpeeds[$PackageRec['speedid']];
		$Speeds[$PackageRec['speedid']]['cost'] = $PackageRec['monthlycost'];
		if($PackageRec['ontid'] > 0)
		{
			$ONTs[$PackageRec['ontid']] = $PackageRec['ontid'];
		}
		if($PackageRec['termnum'] > 0)
		{
			$Term = ($PackageRec['termnum'] == 1) ? "Month to month" : $PackageRec['termnum'] . " months";
			$Months[$PackageRec['termnum']] = $Term;
		}
	}
}
// $ConnCost  = $OrderArr['connectcost'];
// $MonthCost = $OrderArr['monthlycost'];
// $ONTCost = $OrderArr['ontcost'];
?>
<div id="packages">
	<div class="form-group"	id='packagearea'>
		<form method='POST' action='saveChangePackage.php'>
		<input type='hidden' name='packageid' id='packageid' value='<?php echo $PackID; ?>'>
		<input type='hidden' name='orderid' id='orderid' value='<?php echo $OrderID; ?>'>
		<table class='table table-hover'>
<?php
if(count($OrderChange) > 0)
{
	$Type = '';
	$NewSpeed = '';
	switch($OrderChange['orderstatus'])
	{
		case 4:
		case 5:
			$Type = 'Upgrade';
			$NewSpeed = "<small>New speed : " . $Speeds[$OrderChange['speedid']]['speed'] . "Mbps</small>";
			break;
		case 6:
		case 7:
			$Type = 'Downgrade';
			$NewSpeed = "<small>New speed : " . $Speeds[$OrderChange['speedid']]['speed'] . "Mbps</small>";
			break;
		case 8:
		case 9:
			$Type = 'Cancellation';
			break;
	}
	echo "<tr><th class='text-warning' colspan='5'><h3>*** " . $Type . " in progress *** " . $NewSpeed . "</h3></th></tr>";
}
?>
		<tr><th></th><th>Package</th><th>Speed</th><th>Cost</th><th>Router (ONT)</th></tr>
<?php
$cnt = 0;
foreach($Speeds AS $SpeedID => $SpeedRec)
{
	echo "<tr><td><input type='radio' name='packspeed' id='packspeed_" . $SpeedID . "' value='" . $SpeedID . "'";
	if($SpeedID == $OrderArr['speedid'])
		echo " checked='checked'";
	echo "></td>";
	echo "<td><label for='packspeed_" . $SpeedID . "'>" . $SpeedRec['name'] . "</label></td>";
	echo "<td>" . $SpeedRec['speed'] . " Mbps</td>";
	echo "<td>" . sprintf("R %0.2f p/m", $SpeedRec['cost']) . "</td>";
	if($cnt == 0)
	{
		echo "<td rowspan='" . count($Speeds) . "'>";
		foreach($VendorONTs AS $OntID => $ONTRec)
		{
			echo "<p><input type='radio' name='packont' id='packont_" . $OntID . "' value='" . $OntID . "'";
			if($OntID == $OrderArr['ontid'])
				echo " checked='checked'";
			echo "> <label for='packont_" . $OntID . "'>" . $ONTRec['ontname'] . "</label> ";
			if($ONTRec['ontcost'] > 0)
				echo sprintf("(extra R %0.2f p/m)", $ONTRec['ontcost']);
			echo "<p>";
		}
		echo "</select></td></tr>";
	}
	$cnt = 1;
	echo "</tr>";
}
?>
		<!-- <tr><td id='packdetails' colspan='4'>
			<table class='table'>
			<tr><th>Monthly Cost</th><th>ONT Cost <small>(Once off)</small></th><th>Connection Fee <small>(Once off)</small></th><th>Once off total</small></th></tr>
			<tr><td id='mthcost'>R <?php echo $MonthCost; ?></td><td id='ontcost'>R <?php echo $ONTCost; ?></td><td id='concost'>R <?php echo $ConnCost; ?></td><td id='totcost'>R <?php echo ($ConnCost + $ONTCost); ?></td></tr>
			</table>
		</td></tr> -->
		<tr><td colspan='5'><button type='submit' class='btn btn-success pull-right'><i class='fa fa-save'></i> Save</button></td></tr>
		<tr><td colspan='5'><p><em>* All packages are month to month</em></p></td></tr>
		</table>
		</form>
	</div>
</div>
<?php
include_once("footer.inc.php");
?>