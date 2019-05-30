<?php
include("db.inc.php");
include("header.inc.php");
$ComplexTypes = getComplexTypes();
$Vendors = getVendors();
?>
<script>
$(document).ready(function () 
{
	// $('#questForm').validationEngine({'validationEventTrigger':'submit'});
	$("#unittype, #vendorid").change(function()
	{
		var typeid = $("#unittype").val();
		var vendorid = '';
		$("input:checkbox[name^='vendorid']:checked").each(function()
		{
			if(vendorid != '')
				vendorid += ",";
			vendorid += $(this).val();
		});
		if((typeid != '') && (vendorid != ''))
		{
			var adate = new Date().getTime();
			$.ajax({async: false, type: "POST", url: "ajaxGetRegisterSpeeds.php", dataType: "html",
				data: "dc=" + adate + "&cid=" + typeid + "&vid=" + vendorid,
				success: function (feedback)
				{
					// var optTxt = '';
					// $('#speedlist').html('');
					// $.each(feedback, function (i, row)
					// {
						// optTxt += '<input type="radio" name="speedtype" id="speedtype' + i + '" value="' + i + '"><label for="speedtype' + i + '">' + row.name;
						// if(row.cost > 0)
							// optTxt += ' (R ' + row.cost + ')';
						// optTxt += '</label><br>';
					// });
					// $('#speedlist').html(optTxt);
					$('#debugged').html(feedback);
				},
				error: function(request, feedback, error)
				{
					alert("Request failed\n" + feedback + "\n" + error);
					return false;
				}
			});
		}
		else
		{
			$('#speedtype').html('');
		}
	});
	
	$("input:radio[name=speedtype]").change(function()
	{
		var typeid = $("#unittype").val();
		var vendorid = $("#vendorid").val();
		var speedid = $("#speedtype").val();
	});
});
</script>
<div class='row'>
	<div class='col-md-1'>Unit Type:</div>
	<div class='col-md-2'><select name='unittype' id='unittype'>
	<option value=''>-- Select One --</option>
<?php
foreach($ComplexTypes AS $ID => $Rec)
{
	echo "<option value='" . $ID . "'>" . $Rec . "</option>";
}
?>
	</select>
	</div>
	<div class='col-md-1'>Network:</div>
	<div class='col-md-2'>
<?php
foreach($Vendors AS $ID => $Rec)
{
	echo "<p><input type='checkbox' name='vendorid[]' id='vendorid_" . $ID . "' value='" . $ID . "'><label for='vendorid_" . $ID . "'>" . $Rec . "</label></p>";
}
?>
	</div>
	<div class='col-md-1'>Speed:</div>
	<div class='col-md-2' id='speedlist'><input type="hidden" name="speedtype" value='0'></div>
	<div class='col-md-1'>ONT:</div>
	<div class='col-md-2' id='ontlist'></div>
</div>

<div class='row'>
	<div class='col-md-12' id='debugged'>Debug Data</div>
</div>