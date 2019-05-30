<?php
session_start();
include("db.inc.php");
include_once("header.inc.php");
include_once("iris.inc.php");
$ComplexID = (isset($_GET['ci'])) ? pebkac($_GET['ci']) : 0;
$Subdomain = (isset($_GET['dom'])) ? pebkac($_GET['dom'], 100, 'STRING') : '';
if($ComplexID > 0)
	$ComplexRec = getComplexByID($ComplexID);
elseif($Subdomain != '')
	$ComplexRec = getComplexBySubdomain($Subdomain);
if(count($ComplexRec) < 1)
{
?>
<div id="et-main-area">
	<div id="main-content">
		<div class="container">
			<div id="content-area" class="clearfix">
				<h2>Content not found :(</h2>
			</div>
			<div class="col-md-4 col-sm-4">Please check the address you are looking for</div> 
		</div>
	</div>
</div>
<?php
	include_once("footer.inc.php");
	exit();
}
$PackageArr = getFibrePackages();
$VendorSpeeds = getPackageSpeeds();
$VendorONTs = getONTTypes();
$Packages = array();
$Speeds = array();
$ONTs = array();
$Months = array();
$PackID = 0;
$MonthCost = 0;
$ONTCost = 0;
$ConnCost = 0;
$cnt = 0;
foreach($PackageArr AS $PackageID => $PackageRec)
{
	if($PackageRec['speedid'] > 0)
	{
		$Speeds[$PackageRec['speedid']]['id'] = $PackageRec['speedid'];
		$Speeds[$PackageRec['speedid']]['name'] = $PackageRec['packagename'];
		$Speeds[$PackageRec['speedid']]['speed'] = $VendorSpeeds[$PackageRec['speedid']];
		if(!isset($Speeds[$PackageRec['speedid']]['cost']) || ($Speeds[$PackageRec['speedid']]['cost'] < $PackageRec['monthlycost']))
			$Speeds[$PackageRec['speedid']]['cost'] = $PackageRec['monthlycost'];
		if($PackageRec['ontid'] > 0)
			$ONTs[$PackageRec['ontid']] = $PackageRec['ontid'];
		if($PackageRec['termnum'] > 0)
		{
			$Term = ($PackageRec['termnum'] == 1) ? "Month to month" : $PackageRec['termnum'] . " months";
			$Months[$PackageRec['termnum']] = $Term;
		}
		if($cnt == 0)
		{
			$PackID = $PackageRec['packageid'];
			$MonthCost = $PackageRec['monthlycost'];
			$ONTCost =  $PackageRec['ontcost'];
			$ConnCost = $PackageRec['connectcost'];
		}
		$cnt = 1;
	}
}
$ONTs = array_reverse($ONTs);
$ComplexID = $ComplexRec['complexid'];
$CompOrders = getComplexUnitPackages($ComplexID);
$UnitOrdered = array();
foreach($CompOrders AS $UnitID => $OrderRec)
{
	$UnitOrdered[$UnitID] = $UnitID;
}
$NumOrders = count($UnitOrdered);
$Residents = getComplexResidents($ComplexID);
$UnitOrdered = array();
foreach($Residents AS $UnitID => $ResRec)
{
	$UnitOrdered[$UnitID] = $ResRec['unitnumber'];
}
$NumUnits = getComplexUnitMapCount($ComplexID);
$IntPerc = ($NumOrders / $NumUnits) * 100;
$SiteStatusses = getSiteStatusses();

$CompStatus = ($SiteStatusses[$ComplexRec['statusid']]['parentid'] == 0) ? $ComplexRec['statusid'] : $SiteStatusses[$ComplexRec['statusid']]['parentid'];
$StatusName = $SiteStatusses[$CompStatus]['statusname'];
$ShowWhich = 0;
switch($CompStatus)
{
	case 50:	//	Customer Registered
		$ShowWhich = 6;
		break;
	case 27:	//	Far Off-Net
	case 28:	//	Off-Net
	case 29:	//	Monitoring
		$ShowWhich = 5;
		break;
	case 30: 	//	Developing
		$ShowWhich = 4;
		break;
	case 34:	// 	Planning
	case 42:	//	Pending
		$ShowWhich = 3;
		break;
	case 43:	//	Building
		$ShowWhich = 2;
		break;
	case 47:	// 	Live
		$ShowWhich = 1;
		break;
	default:
		$ShowWhich = 0;
		break;
}
?>
<script>
function loadPackage()
{
	var packspeed = $('input[name=packspeed]:checked').val();
	console.log($('input[name=packspeed]:checked').data("speedcost") + " " + $('input[name=packont]:checked').data('ontcost'));
	$('#mthcost').html('R ' + $('input[name=packspeed]:checked').data("speedcost"));
	$('#ontcost').html('R ' + $('input[name=packont]:checked').data('ontcost'));
}

$(document).ready(function()
{
	console.log("Start validator");
	$("input:radio[name=proptype]").change(function ()
	{
		if($(this).val() == 1)
			var which = 'fsh';
		else
			var which = 'com';
		$('#comwindow').hide();
		$('#fshwindow').hide();
		$('#' + which + 'window').show();
	});

	$("input:radio[name=ftpreq]").change(function () {
		if($(this).val() == 2)
		{
			$('#packageid').val(1);
			$('#fullorder').hide();
			$('#contractreminder').show();
		}
		else
		{
			$('#contractreminder').hide();
			$('#fullorder').show();
		}
	});
	
	$("#orderFrm").validationEngine();
	
});
</script>
<div id="et-main-area">
	<div id="main-content">
		<div class="container">
			<div id="content-area" class="clearfix">
		<div class="row">
			<div class="col-md-12 col-sm-12">
				<h2><?php echo $ComplexRec['complexname']; ?></h2>
			</div>
		</div>
<?php
if($ShowWhich == 0)	// We do not know this complex or the data is broken
{
?>

<?php
}
elseif($ShowWhich == 1)	// The complex is live
{
?>
<div class="row">
	<div class="col-md-4 col-sm-4" style='border: 0px solid #000000;'>
		<!-- <h4>CHECK COVERAGE IN YOUR AREA</h4> -->
		<h4><strong>Complex Status:</strong> <?php echo $StatusName; ?></h4>
		<!-- <h4><strong>Resident Interest:</strong> <?php echo sprintf("%0.2f", $IntPerc); ?>% - <?php echo $NumOrders; ?> out of <?php echo $NumUnits; ?></h4> -->
	</div> 
	<div class="col-md-8 col-sm-8" style='border: 0px solid #000000;'>
		<p><?php echo $ComplexRec['complexname']; ?> is <strong>Live</strong>!.</p>
		<p>If you have a fibre termination point already installed, you are ready for the great new world of super fast internet.</p>
		<p>If you do not have a fibre point in your residence yet, we can sort that our for you as well.<p>
		<p>If you want to place an order for one of our great fibre packages, please use the form below.</p>
	</div>
</div>
<?php
}
elseif($ShowWhich == 2)	// The complex is in build
{
?>
<div class="row">
	<div class="col-md-4 col-sm-4" style='border: 0px solid #000000;'>
		<!-- <h4>CHECK COVERAGE IN YOUR AREA</h4> -->
		<h4><strong>Complex Status:</strong> <?php echo $StatusName; ?></h4>
		<h4><strong>Resident Interest:</strong> <?php echo sprintf("%0.2f", $IntPerc); ?>% - <?php echo $NumOrders; ?> out of <?php echo $NumUnits; ?></h4>
	</div> 
	<div class="col-md-8 col-sm-8" style='border: 0px solid #000000;'>
		<p>We are busy building the fibre infrastructure into <?php echo $ComplexRec['complexname']; ?>.</p>
		<p>Very soon you will have access to the great new world of super fast internet.</p>
		<p>Remember, the infrastructure installation during the project phase is completely <strong>free</strong>. You are under no obligation to take a service</p>
		<p>If you only want a Termination point please select “I ONLY want a fibre termination point, but NO contract at this stage” under the fibre termination point of the order form</p>
		<p>If you want indicate your interest in having a termination point installed or place an order for one of our great fibre packages, please use the form below.</p>
	</div>
</div>
<?php
}
elseif($ShowWhich == 3)	// The complex is in planning (being built soon)
{
?>
<div class="row">
	<div class="col-md-4 col-sm-4" style='border: 0px solid #000000;'>
		<!-- <h4>CHECK COVERAGE IN YOUR AREA</h4> -->
		<h4><strong>Complex Status:</strong> <?php echo $StatusName; ?></h4>
		<h4><strong>Resident Interest:</strong> <?php echo sprintf("%0.2f", $IntPerc); ?>% - <?php echo $NumOrders; ?> out of <?php echo $NumUnits; ?></h4>
	</div> 
	<div class="col-md-8 col-sm-8" style='border: 0px solid #000000;'>
		<p>We are finalising the arrangements to put the fibre infrastructure into <?php echo $ComplexRec['complexname']; ?>.</p>
		<p>Plans are being drawn up and we're getting ready to build fibre right to your door</p>
		<p>Remember, the infrastructure installation during the project phase is completely <strong>free</strong>. You are under no obligation to take a service</p>
		<p>If you only want a Termination point please select “I ONLY want a fibre termination point, but NO contract at this stage” under the fibre termination point of the order form</p>
		<p>If you want indicate your interest in having a termination point installed or place an order for one of our great fibre packages so long, please use the form below.</p>
	</div>
</div>
<?php	
}
elseif($ShowWhich == 4)	// We're talking to the Body corporate
{
?>
<div class="row">
	<div class="col-md-4 col-sm-4" style='border: 0px solid #000000;'>
		<!-- <h4>CHECK COVERAGE IN YOUR AREA</h4> -->
		<h4><strong>Complex Status:</strong> <?php echo $StatusName; ?></h4>
		<h4><strong>Resident Interest:</strong> <?php echo sprintf("%0.2f", $IntPerc); ?>% - <?php echo $NumOrders; ?> out of <?php echo $NumUnits; ?></h4>
	</div> 
	<div class="col-md-8 col-sm-8" style='border: 0px solid #000000;'>
		<p>We've been in touch with the Body Corporate of <?php echo $ComplexRec['complexname']; ?>.</p>
		<p>Once all the I's are dotted and T's crossed, we will be building fibre infrastructure into <?php echo $ComplexRec['complexname']; ?>.</p>
		<p>Remember, the infrastructure installation during the project phase is completely <strong>free</strong>. You are under no obligation to take a service</p>
		<p>If you only want a Termination point please select “I ONLY want a fibre termination point, but NO contract at this stage” under the fibre termination point of the order form</p>
		<p>To indicate your interest in having fibre installed please use the form below.</p>
	</div>
</div>
<?php
}
elseif($ShowWhich == 5)	// We've not contacted the body corporate yet
{
?>

<?php
}
elseif($ShowWhich == 6)	// The complex was registered by a customer
{
?>

<?php
}
?>
<div class="container">
<div class="row">
<div class="col-md-12 col-sm-12" style='border: 0px solid #000000;'>
<form class="form-horizontal" id='orderFrm' method='POST' action='saveOrder.php'>
<input type='hidden' name='from' id='from' value='complexlanding'>
<input type='hidden' name='complexid' id='complexid' value='<?php echo $ComplexID; ?>'>
<div id='comwindow'>
	<div class="form-group"><label class="col-sm-3 control-label">Complex:</label><div class="col-sm-4"><h3><?php echo $ComplexRec['complexname']; ?></h3></div></div>
	<div class="form-group">
		<label class="col-sm-3 control-label"><i class='text-purple fa fa-exclamation-triangle pull-left'></i> Unit Number: </label>
		<div class="col-sm-8">
			<select name='unitnum' id='unitnum' class='validate[required] form-control'>
			<option value=''>-- Select Unit --</option>
<?php
$UnitMap = getComplexUnitMap($ComplexID);
foreach($UnitMap AS $MapID => $MapRec)
{
	if($MapRec['hoaunit'] == 0)
	{
		echo "<option value='" . $MapRec['unitdesc'] . "'>" . $MapRec['unitdesc'] . "</option>";
		// if(!in_array($MapRec['unitdesc'], $UnitOrdered))
			// echo "<option value='" . $MapRec['unitdesc'] . "'>" . $MapRec['unitdesc'] . "</option>";
		// else
			// echo "<option disabled='disabled'>" . $MapRec['unitdesc'] . " (Ordered)</option>";
	}
}
?>
			</select>
		</div>
	</div>
	<div class="form-group">
			<label class="col-sm-3 control-label"><i class='text-purple fa fa-exclamation-triangle pull-left'></i> Name and Surname:</label><div class="col-sm-4"><input type="text" class="validate[required] form-control" style='color: #000000;' name="regname" id="regname" placeholder="First Name"></div>
			<div class="col-sm-4"><input type="text" class="validate[required] form-control" name="regsurname" id="regsurname" placeholder="Surname"></div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label"><i class='text-purple fa fa-exclamation-triangle pull-left'></i> ID Number:</label><div class="col-sm-8"><input type="text" class="validate[required] form-control" name="regidnum" id="regidnum" placeholder="ID Number"></div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label"><i class='text-purple fa fa-exclamation-triangle pull-left'></i> Email:</label><div class="col-sm-8"><input type="text" class="validate[required, custom[email]] form-control" name="regemail" id="regemail" placeholder="Email"></div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label"><i class='text-purple fa fa-exclamation-triangle pull-left'></i> Cellphone Number:<p class="help-block">eg. 0821234567</p></label>
			<div class="col-sm-3"><input type="text" class="validate[required, custom[phone]] form-control" name="regcell" id="regcell" placeholder="Cellphone"></div>
			<label class="col-sm-2 control-label">Other Phone: <p class="help-block">eg. 0821234567</p></label>
			<div class="col-sm-3"><input type="text" class="validate[custom[phone]] form-control" name="regtel" id="regtel" placeholder="Other number"></div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label"><i class='text-purple fa fa-exclamation-triangle pull-left'></i> Owner or Tenant:</label>
				<div class="col-sm-8">
					<div class="radio">
						<label><input type="radio" name="owntenant" id="owntenant_o" value="1" class='validate[required] radio' checked='checked'> Owner</label><br>
						<label><input type="radio" name="owntenant" id="owntenant_t" value="2" class='validate[required] radio'> Tenant</label>
					</div>
				</div>
		</div>
		<h3>Fibre Termination Point<br>
		<small>Please specify if you already have a fibre termination point installed or still require one. If you select that you ONLY want a fibre termination point and NO contract at this stage, you will not be required to complete any data or voice package details.</small>
		</h3>
		<div class="form-group">
			<label class="col-sm-3 control-label"><i class='text-purple fa fa-exclamation-triangle pull-left'></i> Fibre Termination Point:</label>
			<div class="col-sm-8">
				<div class="radio">
<?php
if(($ShowWhich == 1) || ($ShowWhich == 0) || ($ShowWhich == 6))
	echo "<label><input type='radio' name='ftpreq' id='ftpreq_0' value='0' class='validate[required] radio'> I already have a fibre termination point installed in my unit</label><br>";
?>
					
					<label><input type="radio" name="ftpreq" id="ftpreq_1" value="1" checked='checked' class='validate[required] radio'> I want to Register for a Termination Point AND choose a Fibre package solution</label><br>
					<label><input type="radio" name="ftpreq" id="ftpreq_2" value="2" class='validate[required] radio'> I ONLY want a fibre termination point, but NO contract at this stage</label>
				</div>
			</div>
		</div>
		<div id="contractreminder">
		
		</div>
		<div id="fullorder">
			<h3>Data Package Selection - ONT Selection</h3>
			<div class="form-group"	id='packagearea'>
				<input type='hidden' name='packageid' id='packageid' value='<?php echo $PackID; ?>'>
				<input type='hidden' name='proptype' id='proptype' value='0'>
				<table class='table'>
				<tr><th></th><th>Package</th><th>Speed</th><th>Term</th><th>ONT</th></tr>
<?php
$cnt = 0;
foreach($Speeds AS $SpeedID => $SpeedRec)
{
	echo "<tr>";
	echo "<td><input type='radio' name='packspeed' id='packspeed_" . $SpeedID . "' value='" . $SpeedID . "' data-speedcost='" . $SpeedRec['cost'] . "' onchange='loadPackage();'";
	if($cnt == 0)
		echo " checked='checked'";
	echo "></td>";
	echo "<td><label for='packspeed_" . $SpeedID . "'>" . $SpeedRec['name'] . "</label></td>";
	echo "<td>" . $SpeedRec['speed'] . "</td>";
	if($cnt == 0)
	{
		echo "<td rowspan='" . count($Speeds) . "'>Month to month</td>";
		echo "<td rowspan='" . count($Speeds) . "'>";
		$cnt2 = 0;
		foreach($ONTs AS $OntID)
		{
			echo "<label><input type='radio' name='packont' id='packont_" . $OntID . "' value='" . $OntID . "' ";
			if($cnt2 == 0)
				echo "checked='checked' ";
			echo "onchange='loadPackage();' data-ontcost='" . $VendorONTs[$OntID]['ontcost'] . "'>" . $VendorONTs[$OntID]['ontname'] . "</label><br>";
			$cnt2++;
		}
		echo "</td></tr>";
	}
	$cnt = 1;
	echo "</tr>";
}
$ONTCost = "0.00";
?>
			<tr><td id='packdetails' colspan='4'>
				<table class='table'>
				<tr><th>Monthly Cost</th><th>ONT Cost <small>(Monthly)</small></th><th>Connection Fee <small>(Once off)</small></th> <!-- <th>Once off total</th> --></tr>
				<tr>
				<td id='mthcost'>R <?php echo $MonthCost; ?></td>
				<td id='ontcost'>R <?php echo $ONTCost; ?></td>
				<td id='concost'>R <?php echo $ConnCost; ?></td>
				<!-- <td id='totcost'>R <?php echo ($ConnCost + $ONTCost); ?></td> -->
				</tr>
				</table>
			</td></tr>
			</table>
			</div>
			<!-- <h3>VOIP Selection - Voice Over Internet Protocol</h3>
			<div class="form-group"	id='voiparea'>
			</div> -->
		</div>
		<div class="form-group">
			Once you have submitted your order, the consultant assigned to your complex or area will be in contact with you shortly to update you on the progress of your order and installation. 
			Installation time frames in a live complex are usually within 5 to 10 working days. Note that your order does start getting processed immediately once submitted.
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label"><i class='text-purple fa fa-exclamation-triangle pull-left'></i> Terms of Service:</label>
			<div class="col-sm-8">
				<div class="radio">
					<label><input type="checkbox" name="tandc" id="tandc" value="1" class="validate[required]"> I agree to the <a href='terms.php' target='_blank'>terms and conditions</a></label>
				</div>
			</div>
		</div>
		<div class="form-group text-center">
			<button type='submit' class='btn btn-success'><i class='fa fa-send'></i> Submit</button>
		</div>
</div>	
</form>
</div>
</div>
</div>

</div>
</div>
</div>
</div>
<?php
include_once("footer.inc.php");
?>