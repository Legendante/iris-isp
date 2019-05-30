<?php
include_once("header.inc.php");
?>
<script>
$(document).ready(function()
{
	console.log("Start validator");
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
				var statText = ui.item.status;
				if(statText == "")
					statText = "Unknown";
				VendorList = ui.item.vendorid;
				$('#complexid').val(ui.item.id);
				console.log("Vendor : " + VendorList);
				$('#compstatus').removeClass("text-success text-warning text-purple");
				if((ui.item.status == "Live") || (ui.item.status == "Building"))
					stat = "text-success";
				else if((ui.item.status == "Developing") || (ui.item.status == "Planning") || (ui.item.status == "Pending"))
					stat = "text-warning";
				else
					stat = "text-purple";
				$('#compstatus').addClass(stat).html(statText);
			}
			else
			{
				console.log("Nothing selected, input was " + this.value);
				$('#compstatus').addClass("text-purple").html("Unknown");
			}
		},
	});
	$("#registerFrm").validationEngine();
});
</script>
<form class="form-horizontal" id='registerFrm' method='POST' action='registerSite.php'>
<div id="about">
	<div class="container">
		<div class="row">
			<div class="col-md-12 col-sm-12">
				<h2>Register your complex</h2>
			</div>
			<div class="col-md-12 col-sm-12"><h3>Your details</h3></div>
			<div class="col-md-12 col-sm-12">
				<div class="row"><label class="col-md-3 control-label">Your name:</label><div class='col-md-9'><input type='text' name='custname' id='custname' class='form-control' value=''></div></div>
				<div class="row"><label class="col-md-3 control-label">Your number:</label><div class='col-md-9'><input type='text' name='custcell' id='custcell' class='form-control' value=''></div></div>
				<div class="row"><label class="col-md-3 control-label">Your email:</label><div class='col-md-9'><input type='text' name='custemail' id='custemail' class='form-control' value=''></div></div>
			</div>
			<div class="col-md-12 col-sm-12"><h3>Complex Details</h3></div>
			<div class="col-md-12 col-sm-12">
					<label class="col-sm-3 control-label"><i class='text-purple fa fa-exclamation-triangle pull-left'></i> Estate/Complex name:</label>
					<div class="col-sm-4"><input type="text" class="validate[required] form-control" name="complexName" id="complexName" placeholder="Complex Name">
					<input type='hidden' name='complexid' id='complexid' value=''>
					</div>
					<div class="col-sm-4">Complex status : <strong><span id='compstatus'></span></strong></div>
			</div>
			<div class="col-md-12 col-sm-12">
					<label class="col-sm-3 control-label">Number of units:</label><div class='col-md-2'><input type='text' class="form-control" name='numunits' id='numunits' value=''></div>
			</div>
			<div class="col-md-12 col-sm-12">
				<div class="row"><label class="col-sm-3 control-label">Estate/Complex address:</label><div class='col-md-9'><input type='text' name='address1' id='address1' class='form-control' value=''></div></div>
				<div class="row"><div class='col-md-3'></div><div class='col-md-9'><input type='text' name='address2' id='address2' class='form-control' value=''></div></div>
				<div class="row"><div class='col-md-3'></div><div class='col-md-9'><input type='text' name='address3' id='address3' class='form-control' value=''></div></div>
				<div class="row"><div class='col-md-3'></div><div class='col-md-9'><input type='text' name='address4' id='address4' class='form-control' value=''></div></div>
				<div class="row"><div class='col-md-3'></div><div class='col-md-9'><input type='text' name='address5' id='address5' class='form-control' value=''></div></div>
			</div>
			
			<div class="col-md-6 col-sm-12" style='border-right: 1px solid #5F00A9;'>
				<div class="col-md-12 col-sm-12"><h3>Body corporate/Home owners association details</h3></div>
				<div class="col-md-12 col-sm-12">
					<div class="row"><label class="col-md-3 control-label">Contact name:</label><div class='col-md-9'><input type='text' name='bcname' id='bcname' class='form-control' value=''></div></div>
					<div class="row"><label class="col-md-3 control-label">Contact number:</label><div class='col-md-9'><input type='text' name='bccell' id='bccell' class='form-control' value=''></div></div>
					<div class="row"><label class="col-md-3 control-label">Contact email:</label><div class='col-md-9'><input type='text' name='bcemail' id='bcemail' class='form-control' value=''></div></div>
				</div>
			</div>
			<div class="col-md-6 col-sm-12">
				<div class="col-md-12 col-sm-12"><h3>Managing Agent details</h3></div>
				<div class="col-md-12 col-sm-12">
					<div class="row"><label class="col-md-3 control-label">Contact name:</label><div class='col-md-9'><input type='text' name='macontact' id='macontact' class='form-control' value=''></div></div>
					<div class="row"><label class="col-md-3 control-label">Contact number:</label><div class='col-md-9'><input type='text' name='macell' id='macell' class='form-control' value=''></div></div>
					<div class="row"><label class="col-md-3 control-label">Contact email:</label><div class='col-md-9'><input type='text' name='maemail' id='maemail' class='form-control' value=''></div></div>
				</div>
			</div>
			<div class="form-group text-center">
				<button type='submit' class='btn btn-default'><i class='fa fa-send'></i> <strong>Submit</strong></button>
			</div>
		
		</div>
	</div>
</div>
</form>
<?php
include_once("footer.inc.php");
?>