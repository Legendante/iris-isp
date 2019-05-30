<?php
session_start();
include_once("db.inc.php");
include_once("header.inc.php");
include_once("iris.inc.php");
$PackageArr = getFibrePackages();
$VendorSpeeds = getPackageSpeeds();
$VendorONTs = getONTTypes();
$Packages = array();
$Speeds = array();
$ONTs = array();
$Months = array(1 => "Month to month");
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
		// if($PackageRec['termnum'] > 0)
		// {
			// $Term = ($PackageRec['termnum'] == 1) ? "Month to month" : $PackageRec['termnum'] . " months";
			// $Months[$PackageRec['termnum']] = $Term;
		// }
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
?>
<script>
var VendorList = '';
function getPackage(type)
{
	var adate = new Date().getTime();
	$.ajax({async: false, type: "POST", url: "ajaxGetPackages.php", dataType: "html",
		data: "dc=" + adate + "&vlist=" + VendorList + "&type=" + type,
		success: function (feedback)
		{
			if(type == 1)
				$('#packagearea').html(feedback);
			else if(type == 0)
				$('#voiparea').html(feedback);
		},
		error: function(request, feedback, error)
		{
			alert("Request failed\n" + feedback + "\n" + error);
			return false;
		}
	});
}

function loadPackage()
{
	var packspeed = $('input[name=packspeed]:checked').val();
	console.log($('input[name=packspeed]:checked').data("speedcost") + " " + $('input[name=packont]:checked').data('ontcost'));
	$('#mthcost').html('R ' + $('input[name=packspeed]:checked').data("speedcost"));
	$('#ontcost').html('R ' + $('input[name=packont]:checked').data('ontcost'));
	// var packterm = $('#packterm').val();
	// var packont = $('#packont').val();
	// var adate = new Date().getTime();
	/*$.ajax({async: false, type: "POST", url: "ajaxGetPackageDeets.php", dataType: "json",
		data: "dc=" + adate + "&s=" + packspeed + "&t=" + packterm + "&o=" + packont,
		success: function (feedback)
		{
			$('#packageid').val(feedback.packageid);
			$('#mthcost').html('R ' + feedback.monthlycost);
			$('#ontcost').html('R ' + feedback.ontcost);
			$('#concost').html('R ' + feedback.connectcost);
			var Tot = (parseFloat(feedback.ontcost) + parseFloat(feedback.connectcost)).toFixed(2);
			$('#totcost').html('R ' + Tot);
		},
		error: function(request, feedback, error)
		{
			alert("Request failed\n" + feedback + "\n" + error);
			return false;
		}
	});//*/
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
		// if(which == 'fsh')0
			// initMap();
	});
	
	$("input:radio[name=ftpreq]").change(function () {
		if($(this).val() == 2)
		{
			$('#packageid').val(1);
			$('#fullorder').hide();
		}
		else
			$('#fullorder').show();
	});
	
	$("#orderFrm").validationEngine();
	
});

function updateComplexStatus()
{
	var status = $('#complexid :selected').data("complexstatus");
	var txtStatus = '';
	switch(status)
	{
		case 47:
		case 48:
		case 49:
			txtStatus = 'Live';
			break;
		case 43:
		case 44:
		case 45:
		case 46:
			txtStatus = 'In build';
			break;
		case 34:
		case 35:
		case 36:
		case 37:
		case 38:
		case 39:
		case 40:
		case 41:
			txtStatus = 'Gathering interest';
			break;
		default: 
			txtStatus = 'Unknown';
			break;
	}
	$('#compstatus').html(txtStatus);
}
</script>
<div id="et-main-area">
	<div id="main-content">
		<div class="container">
			<div id="content-area" class="clearfix">
<div class='row'>
	<div class='col-md-12'>
	<form class="form-horizontal" id='orderFrm' method='POST' action='saveOrder.php'>
		<h2>Customer Details</h2>
		<div id='comwindow'>
			<div class="form-group">
				<label class="col-sm-3 control-label"><i class='text-purple fa fa-exclamation-triangle pull-left'></i> Complex:</label> <!-- <p class="help-block">Complex or Estate Name</p> -->
				<div class="col-sm-5"><select name='complexid' id='complexid' class="validate[required] form-control" onchange='updateComplexStatus();'>
				<option value=''>-- Select Complex --</option>
<?php
$selQry = 'SELECT complexdetails.complexid, complexname, streetaddress1, streetaddress2, statusid, vendorid FROM complexdetails ';
$selQry .= 'LEFT JOIN complexstatus ON complexstatus.complexid = complexdetails.complexid AND complexstatus.historyserial = 0 ';
$selQry .= 'WHERE showinresults = 1 ';
$selQry .= 'ORDER BY complexname';
// echo $selQry . "<Br>";
$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
$retArr = array();
$cnt = 0;
while($selData = mysqli_fetch_array($selRes))
{
	echo "<option value='" . $selData['complexid'] . "' data-complexstatus='" . $selData['statusid'] . "'>" . $selData['complexname'] . "</option>\n";
}
?>
				</select>
				</div>
				<div class="col-sm-4">Complex Status : <strong><span id='compstatus'></span></strong></div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><i class='text-purple fa fa-exclamation-triangle pull-left'></i> Unit Number: </label>
				<div class="col-sm-6"><input type="text" class="validate[required] form-control" name="unitnum" id="unitnum" value="" placeholder="Unit Number"/></div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label"><i class='text-purple fa fa-exclamation-triangle pull-left'></i> Name and Surname:</label>
			<div class="col-sm-3"><input type="text" class="validate[required] form-control" name="regname" id="regname" placeholder="First Name"></div>
			<div class="col-sm-3"><input type="text" class="validate[required] form-control" name="regsurname" id="regsurname" placeholder="Surname"></div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label"><i class='text-purple fa fa-exclamation-triangle pull-left'></i> ID Number:</label>
			<div class="col-sm-6"><input type="text" class="validate[required] form-control" name="regidnum" id="regidnum" placeholder="ID Number"></div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label"><i class='text-purple fa fa-exclamation-triangle pull-left'></i> Email:</label>
			<div class="col-sm-6"><input type="text" class="validate[required, custom[email]] form-control" name="regemail" id="regemail" placeholder="Email"></div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label"><i class='text-purple fa fa-exclamation-triangle pull-left'></i> Cellphone Number:<p class="help-block">eg. 0821234567</p></label>
			<div class="col-sm-2"><input type="text" class="validate[required, custom[phone]] form-control" name="regcell" id="regcell" placeholder="Cellphone"></div>
			<label class="col-sm-2 control-label">Other Phone: <p class="help-block">eg. 0821234567</p></label>
			<div class="col-sm-2"><input type="text" class="validate[custom[phone]] form-control" name="regtel" id="regtel" placeholder="Other number"></div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label"><i class='text-purple fa fa-exclamation-triangle pull-left'></i> Owner or Tenant:</label>
				<div class="col-sm-6">
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
			<div class="col-sm-6">
				<div class="radio">
					<label><input type="radio" name="ftpreq" id="ftpreq_0" value="0" checked='checked' class='validate[required] radio'> I already have a fibre termination point installed in my unit</label><br>
					<label><input type="radio" name="ftpreq" id="ftpreq_1" value="1" class='validate[required] radio'> I want to Register for a Termination Point AND choose a Fibre package solution</label><br>
					<label><input type="radio" name="ftpreq" id="ftpreq_2" value="2" class='validate[required] radio'> I ONLY want a fibre termination point, but NO contract at this stage</label>
				</div>
			</div>
		</div>
		<div id="fullorder">
			<h3>Data Package Selection - ONT Selection</h3>
			<div class="form-group"	id='packagearea'>
				<input type='hidden' name='packageid' id='packageid' value='<?php echo $PackID; ?>'>
				<input type='hidden' name='proptype' id='proptype' value='0'>
				<div class="col-sm-12">
				<div class='table'>
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
		// echo "<td rowspan='" . count($Speeds) . "'>";
		// foreach($Months AS $TermID => $Termname)
		// {
			// echo "<input type='radio' name='packterm' id='packterm_" . $TermID . " value='" . $TermID . "' onchange='loadPackage();'>" . $Termname . "<br>";
		// }
		// echo "</td>";
		// echo "<tr><th colspan='3'>Select the ont</th></tr>";
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
		/*echo "<td rowspan='" . count($Speeds) . "'><select name='packterm' id='packterm' onchange='loadPackage();'>";
		foreach($Months AS $TermID => $Termname)
		{
			if($TermID > 0)
				echo "<option value='" . $TermID . "'>" . $Termname . "</option>";
		}
		echo "</select></td>";
		// echo "<tr><th colspan='3'>Select the ont</th></tr>";
		echo "<td rowspan='" . count($Speeds) . "'><select name='packont' id='packont' onchange='loadPackage();'>";
		foreach($ONTs AS $OntID)
		{
			echo "<option value='" . $OntID . "'>" . $VendorONTs[$OntID] . "</option>";
		}
		echo "</select></td></tr>";//*/
	}
	$cnt = 1;
	echo "</tr>";
}
?>
			<tr><td id='packdetails' colspan='4'>
				<table class='table'>
				<tr><th>Monthly Cost</th><th>ONT Cost <small>(Monthly)</small></th><th>Connection Fee <small>(Once off)</small></th> <!-- <th>Once off total</small></th> --></tr>
				<tr>
				<td id='mthcost'>R <?php echo $MonthCost; ?></td>
				<td id='ontcost'>R 0</td>
				<td id='concost'>R 999</td>
				<!-- <td id='totcost'>R <?php echo $ConnCost; ?></td> -->
				</tr>
				</table>
			</td></tr>
			</table>
			</div>
			</div>
			</div>
			<!-- <h3>VOIP Selection - Voice Over Internet Protocol</h3>
			<div class="form-group"	id='voiparea'>
			
			</div> -->
		</div>
		<div class="form-group">
			<div class="col-sm-12">
			Installation time frames in a live complex are usually within 5 to 10 working days.
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label"><i class='text-purple fa fa-exclamation-triangle pull-left'></i> Terms of Service:</label>
			<div class="col-sm-6">
				<div class="radio">
					<label><input type="checkbox" name="tandc" id="tandc" value="1" class="validate[required]"> I agree to the <a href='/terms-and-conditions/' target='_blank'>terms and conditions</a></label>
				</div>
			</div>
		</div>
		<div class="form-group text-center">
			<button type='submit' class='btn btn-success'><i class='fa fa-send'></i> Submit</button>
		</div>
	</form>
	</div>
</div>
</div>
</div>
</div>
</div>
<?php
include_once("footer.inc.php");
?>