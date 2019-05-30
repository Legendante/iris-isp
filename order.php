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
	var packterm = $('#packterm').val();
	var packont = $('#packont').val();
	var adate = new Date().getTime();
	$.ajax({async: false, type: "POST", url: "ajaxGetPackageDeets.php", dataType: "json",
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
	});
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

	$( "#complexName" ).autocomplete({
		source: "srcComplexName.php",
		minLength: 3,
		success: function (data) 
		{
			response($.map(data.employees, function (item) {
				return 
				{
					var AC = new Object();
                    //autocomplete default values REQUIRED
					AC.id = item.id;
                    AC.label = item.value;
                    AC.value = item.value;
                    //extend values
                    AC.status = item.status;
					AC.vendorid = item.vendorid;
                    // return AC
				};
			}));
		},
		select: function( event, ui ) 
		{
			if(ui.item)
			{
				var stat = "";
				VendorList = ui.item.vendorid;
				$('#complexid').val(ui.item.id);
				// getPackage(1);
				// getPackage(0);
				console.log("Vendor : " + VendorList);
				$('#compstatus').removeClass("text-success text-warning text-purple");
				$('#ftpreq_0').attr('checked', 'checked');
				if((ui.item.status == "Live") || (ui.item.status == "Building"))
					stat = "text-success";
				else if((ui.item.status == "Developing") || (ui.item.status == "Planning") || (ui.item.status == "Pending"))
				{
					stat = "text-warning";
					$('#ftpreq_1').attr('checked', 'checked');
				}
				else
					stat = "text-purple";
				$('#compstatus').addClass(stat).html(ui.item.status);
			}
			else
			{
				console.log("Nothing selected, input was " + this.value);
				$('#compstatus').addClass("text-purple").html("Unknown");
			}
		},
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
</script>
<p></p>
<div id="about">
	<div class="container">
<div class='row'>
	<div class='col-md-12'>
	<form class="form-horizontal" id='orderFrm' method='POST' action='saveOrder.php'>
		<h2>Customer Details</h2>
<!--			<div class="form-group">
				<label class="col-sm-3 control-label"><i class='text-purple fa fa-exclamation-triangle pull-left'></i> I live in a:</label>
				<div class="col-sm-4"><input type='radio' name='proptype' id='proptype_0' value='0'><label for='proptype_0'> Complex</label></div>
				<div class="col-sm-4"><input type='radio' name='proptype' id='proptype_1' value='1'><label for='proptype_1'> Free Standing House</label></div>
			</div> -->
		<div id='comwindow'>
			<div class="form-group">
				<label class="col-sm-3 control-label"><i class='text-purple fa fa-exclamation-triangle pull-left'></i> Complex:<p class="help-block">Complex or Estate Name</p></label>
				<div class="col-sm-4"><input type="text" class="validate[required] form-control" name="complexName" id="complexName" placeholder="Complex Name">
				<input type='hidden' name='complexid' id='complexid' value=''>
				</div>
				<div class="col-sm-4">Complex Status : <strong><span id='compstatus'></span></strong></div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><i class='text-purple fa fa-exclamation-triangle pull-left'></i> Unit Number: </label>
				<div class="col-sm-8"><input type="text" class="validate[required] form-control" name="unitnum" id="unitnum" value="" placeholder="Unit Number"/></div>
			</div>
		</div>
		
		<!-- <div id='fshwindow' style='display: none;'> -->
			<!-- <div style='height: 500px; width: 100%; border: 1px solid #CCCCCC'>
				<input id="pac-input" class="controls" type="text" placeholder="Search for your address">
				<div id="map" style="height: 100%; width: 100%;"></div>
			</div> -->
			<!-- <div id="locationField">
				<input id="autocomplete" placeholder="Enter your address" onFocus="geolocate()" type="text"></input>
			</div> -->
<!--			<div class="form-group">
				<label class="col-sm-3 control-label"><i class='text-purple fa fa-exclamation-triangle pull-left'></i> House Address:</label>
				<div class="col-sm-3"></div><div class="col-sm-8"><input type="text" class="validate[required] form-control" name="street_number" id="street_number" placeholder="Street Address Line 1"></div>
				<!-- <div class="col-sm-6"><input type="text" class="form-control" name="route" id="route" placeholder="Street Address Line 1"></div> -->
<!--				<div class="col-sm-3"></div><div class="col-sm-8"><input type="text" class="validate[required] form-control" name="locality" id="locality" placeholder="City"></div>
				<div class="col-sm-3"></div><div class="col-sm-4"><input type="text" class="validate[required] form-control" name="administrative_area_level_1" id="administrative_area_level_1" placeholder="Region"></div>
				<!-- <div class="col-sm-3"><input type="text" class="form-control" name="fshaddress4" id="fshaddress4" placeholder="Region"></div> -->
<!--				<div class="col-sm-4"><input type="text" class="validate[required] form-control" name="postal_code" id="postal_code" placeholder="Postal/Zip Code"></div>
			</div>
		</div> -->
		
		<div class="form-group">
			<label class="col-sm-3 control-label"><i class='text-purple fa fa-exclamation-triangle pull-left'></i> Name and Surname:</label><div class="col-sm-4"><input type="text" class="validate[required] form-control" name="regname" id="regname" placeholder="First Name"></div>
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
					<label><input type="radio" name="ftpreq" id="ftpreq_0" value="0" checked='checked' class='validate[required] radio'> I already have a fibre termination point installed in my unit</label><br>
					<label><input type="radio" name="ftpreq" id="ftpreq_1" value="1" class='validate[required] radio'> I want to Register for a Termination Point AND choose a Fibre package solution</label><br>
					<label><input type="radio" name="ftpreq" id="ftpreq_2" value="2" class='validate[required] radio'> I ONLY want a fibre termination point, but NO contract at this stage</label>
				</div>
			</div>
		</div>
		<div id="fullorder">
			<!--	<h3>New Service or Upgrade<br>
			<small>Please specify if this is a new service request or if you wish to upgrade your existing service. Note that on a new service we charge a connection fee of R1140.</small>
			</h3>
		<div class="form-group">
				<label class="col-sm-3 control-label">New or Upgrade: </label>
				<div class="col-sm-8">
					<div class="radio">
						<label><input type="radio" name="neworup" id="neworup_0" value="0" checked='checked' class='validate[required] radio'> New Service</label><br>
						<label><input type="radio" name="neworup" id="neworup_1" value="1" class='validate[required] radio'> UPGRADE</label>
					</div>
				</div>
			</div> -->
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
	echo "<td><input type='radio' name='packspeed' id='packspeed_" . $SpeedID . "' value='" . $SpeedID . "' onchange='loadPackage();'";
	if($cnt == 0)
		echo " checked='checked'";
	echo "></td>";
	echo "<td><label for='packspeed_" . $SpeedID . "'>" . $SpeedRec['name'] . "</label></td>";
	echo "<td>" . $SpeedRec['speed'] . "</td>";
	if($cnt == 0)
	{
		echo "<td rowspan='" . count($Speeds) . "'><select name='packterm' id='packterm' onchange='loadPackage();'>";
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
		echo "</select></td></tr>";
	}
	$cnt = 1;
	echo "</tr>";
}
?>
			<tr><td id='packdetails' colspan='4'>
				<table class='table'>
				<tr><th>Monthly Cost</th><th>ONT Cost <small>(Once off)</small></th><th>Connection Fee <small>(Once off)</small></th><th>Once off total</small></th></tr>
				<tr>
				<td id='mthcost'>R <?php echo $MonthCost; ?></td>
				<td id='ontcost'>R <?php echo $ONTCost; ?></td>
				<td id='concost'>R <?php echo $ConnCost; ?></td>
				<td id='totcost'>R <?php echo ($ConnCost + $ONTCost); ?></td>
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
<?php
include_once("footer.inc.php");
?>