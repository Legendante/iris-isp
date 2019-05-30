<?php
include("header.inc.php");

$Vendors = getVendors();
$Speeds = getPackageSpeeds();
$ONTs = getONTTypes();
$VendorSpeeds = getVendorSpeeds();
$VendorONTs = getVendorONTs();
$Extras = getPackageExtras();
?>
<script>
$(document).ready(function()
{
	$("#vendorForm").validationEngine();
	$("#speedForm").validationEngine();
	$("#ontForm").validationEngine();
	$("#extraForm").validationEngine();
});


function editVendor(VendorID)
{
	$('#VendorID').val(VendorID);
	$('#vendorname').val($('#vendor_' + VendorID).val());
	$('#vendor-modal').modal("show");
}

function editSpeed(SpeedID)
{
	$('#speedname').val();
	$('input:checkbox[name^="speedvendor_"]').prop('checked', false);
	$('#SpeedID').val(SpeedID);
	$('#speedname').val($('#speed_' + SpeedID).val());
<?php
	foreach($Vendors AS $ID => $Rec)
	{
		echo "\t$('#speedprice_" . $ID . "').val($('#spcost_" . $ID . "_' + SpeedID).val());\n";
		echo "\tvar isChecked = ($('#spdvendor_" . $ID . "_' + SpeedID).val() == 1) ? true : false;\n";
		echo "\t$('#speedvendor_" . $ID . "').prop('checked', isChecked);\n";
	}
?>
	$('#speed-modal').modal("show");
}

function editONT(ONTID)
{
	$('#ontname').val();
	$('input:checkbox[name^="ontvendor_"]').prop('checked', false);
	$('#ONTID').val(ONTID);
	$('#ontname').val($('#ont_' + ONTID).val());
<?php
	foreach($Vendors AS $ID => $Rec)
	{
		echo "\t$('#ontprice_" . $ID . "').val($('#ontcost_" . $ID . "_' + ONTID).val());\n";
		echo "\tvar isChecked = ($('#ontvend_" . $ID . "_' + ONTID).val() == 1) ? true : false;\n";
		echo "\t$('#ontvendor_" . $ID . "').prop('checked', isChecked);\n";
	}
?>
	$('#ont-modal').modal("show");
}

function editExtra(ExtraID)
{
	$('#extraname').val();
	$('#extraprice').val();
	$('#ExtraID').val(ExtraID);
	$('#extraname').val($('#extra_' + ExtraID).val());
	$('#extraprice').val($('#extra_price_' + ExtraID).val());
	$('#extra-modal').modal("show");
}
</script>
<div class='row'>
	<div class='col-md-12'>
		<div class="panel panel-default">
			<div class="panel-heading"><h4>Vendors
				<button type='button' class='btn btn-xs pull-right' onclick='$("#VendorsPanel").toggleClass("hidden"); $("#VendorsPanelCHV").toggleClass("fa-chevron-down");'>
				<span class='fa fa-chevron-up' id='VendorsPanelCHV'></span></button>
				</h4>
			</div>
			<div class="panel-body" id="VendorsPanel">
				<div class='table'>
					<table class='table table-bordered'>
					<tr><th>Name</th><th width='100px'><button type='button' class='btn btn-success btn-sm' onclick='editVendor(0);'><i class='fa fa-plus'></i> Add</button></th></tr>
<?php
	foreach($Vendors AS $ID => $Rec)
	{
		echo "<tr>";
		echo "<td>" . $Rec;
		echo "<input type='hidden' name='vendor_" . $ID . "' id='vendor_" . $ID . "' value='" . $Rec . "'></td>";
		echo "<td><button type='button' class='btn btn-success btn-sm' onclick='editVendor(" . $ID . ");'><i class='fa fa-edit'></i> Edit</button></td>";
		echo "</tr>";
	}
?>				
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class='row'>
	<div class='col-md-12'>
		<div class="panel panel-default">
			<div class="panel-heading"><h4>Speeds
				<button type='button' class='btn btn-xs pull-right' onclick='$("#SpeedsPanel").toggleClass("hidden"); $("#SpeedsPanelCHV").toggleClass("fa-chevron-down");'>
				<span class='fa fa-chevron-up' id='SpeedsPanelCHV'></span></button>
				</h4>
			</div>
			<div class="panel-body" id="SpeedsPanel">
				<div class='table'>
					<table class='table table-bordered'>
					<tr><th rowspan='2'>Name</th><th colspan='<?php echo count($Vendors); ?>'>Vendors</th>
					<th rowspan='2' width='100px'><button type='button' class='btn btn-success btn-sm' onclick='editSpeed(0);'><i class='fa fa-plus'></i> Add</button></th></tr>
					<tr>
<?php
	foreach($Vendors AS $ID => $Rec)
	{
		echo "<th>" . $Rec . "</th>";
	}
?>			
					</tr>
<?php
	foreach($Speeds AS $ID => $Rec)
	{
		echo "<tr>";
		echo "<td>" . $Rec;
		echo "<input type='hidden' name='speed_" . $ID . "' id='speed_" . $ID . "' value='" . $Rec . "'></td>\n";
		foreach($Vendors AS $VendorID => $VendorRec)
		{
			if(isset($VendorSpeeds[$ID][$VendorID]))
			{
				if($VendorSpeeds[$ID][$VendorID]['isinactive'] == 1)
				{
					echo "<td class='text-danger'><i class='fa fa-times'></i> R " . $VendorSpeeds[$ID][$VendorID]['price'] . "\n";
					echo "<input type='hidden' name='spdvendor_" . $VendorID . "_" . $ID . "' id='spdvendor_" . $VendorID . "_" . $ID . "' value='0'>\n";
				}
				else
				{
					echo "<td class='text-success'><i class='fa fa-check'></i> R " . $VendorSpeeds[$ID][$VendorID]['price'] . "\n";
					echo "<input type='hidden' name='spdvendor_" . $VendorID . "_" . $ID . "' id='spdvendor_" . $VendorID . "_" . $ID . "' value='1'>\n";
				}
				echo "<input type='hidden' name='spcost_" . $VendorID . "_" . $ID . "' id='spcost_" . $VendorID . "_" . $ID . "' value='" . $VendorSpeeds[$ID][$VendorID]['price'] . "'></td>\n";
			}
			else
			{
				echo "<td class='text-danger'>";
				echo "<input type='hidden' name='spdvendor_" . $VendorID . "_" . $ID . "' id='spdvendor_" . $VendorID . "_" . $ID . "' value='0'>\n";
				echo "<i class='fa fa-times'></i></td>\n";
			}
		}
		echo "<td><button type='button' class='btn btn-success btn-sm' onclick='editSpeed(" . $ID . ");'><i class='fa fa-edit'></i> Edit</button></td>\n";
		echo "</tr>\n";
	}
?>				
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class='row'>
	<div class='col-md-12'>
		<div class="panel panel-default">
			<div class="panel-heading"><h4>ONT's
				<button type='button' class='btn btn-xs pull-right' onclick='$("#ONTsPanel").toggleClass("hidden"); $("#ONTsPanelCHV").toggleClass("fa-chevron-down");'>
				<span class='fa fa-chevron-up' id='ONTsPanelCHV'></span></button>
				</h4>
			</div>
			<div class="panel-body" id="ONTsPanel">
				<div class='table'>
					<table class='table table-bordered'>
					<tr><th rowspan='2'>Name</th><th colspan='<?php echo count($Vendors); ?>'>Vendors</th><th rowspan='2' width='100px'><button type='button' class='btn btn-success btn-sm' onclick='editONT(0);'><i class='fa fa-plus'></i> Add</button></th></tr>
					<tr>
<?php
	foreach($Vendors AS $ID => $Rec)
	{
		echo "<th>" . $Rec . "</th>";
	}
?>			
					
					</tr>
<?php
	foreach($ONTs AS $ID => $Rec)
	{
		echo "<tr>";
		echo "<td>" . $Rec['ontname'];
		echo "<input type='hidden' name='ont_" . $ID . "' id='ont_" . $ID . "' value='" . $Rec['ontname'] . "'></td>\n";
		foreach($Vendors AS $VendorID => $VendorName)
		{
			if(isset($VendorONTs[$ID][$VendorID]))
			{
				if($VendorONTs[$ID][$VendorID]['isinactive'] == 1)
				{
					echo "<td class='text-danger'><i class='fa fa-times'></i> R " . $VendorONTs[$ID][$VendorID]['price'] . "\n";
					echo "<input type='hidden' name='ontvend_" . $VendorID . "_" . $ID . "' id='ontvend_" . $VendorID . "_" . $ID . "' value='0'>\n";
				}
				else
				{
					echo "<td class='text-success'><i class='fa fa-check'></i> R " . $VendorONTs[$ID][$VendorID]['price'] . "\n";
					echo "<input type='hidden' name='ontvend_" . $VendorID . "_" . $ID . "' id='ontvend_" . $VendorID . "_" . $ID . "' value='1'>\n";
				}
				echo "<input type='hidden' name='ontcost_" . $VendorID . "_" . $ID . "' id='ontcost_" . $VendorID . "_" . $ID . "' value='" . $VendorONTs[$ID][$VendorID]['price'] . "'></td>\n";
			}
			else
				echo "<td class='text-danger'><i class='fa fa-times'></i></th>\n";
		}
		echo "<td><button type='button' class='btn btn-success btn-sm' onclick='editONT(" . $ID . ");'><i class='fa fa-edit'></i> Edit</button></td>\n";
		echo "</tr>\n";
	}
?>				
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class='row'>
	<div class='col-md-12'>
		<div class="panel panel-default">
			<div class="panel-heading"><h4>Extra Options
				<button type='button' class='btn btn-xs pull-right' onclick='$("#ExtrasPanel").toggleClass("hidden"); $("#ExtrasPanelCHV").toggleClass("fa-chevron-down");'>
				<span class='fa fa-chevron-up' id='ExtrasPanelCHV'></span></button>
				</h4>
			</div>
			<div class="panel-body" id="ExtrasPanel">
				<div class='table'>
					<table class='table table-bordered'>
					<tr><th>Name</th><th>Cost price</th><th width='100px'><button type='button' class='btn btn-success btn-sm' onclick='editExtra(0);'><i class='fa fa-plus'></i> Add</button></th></tr>
<?php
	foreach($Extras AS $ID => $Rec)
	{
		echo "<tr>";
		echo "<td>" . $Rec['extraname'];
		echo "<input type='hidden' name='extra_" . $ID . "' id='extra_" . $ID . "' value='" . $Rec['extraname'] . "'></td>";
		echo "<td>" . $Rec['costprice'];
		echo "<input type='hidden' name='extra_price_" . $ID . "' id='extra_price_" . $ID . "' value='" . $Rec['costprice'] . "'></td>";
		echo "<td><button type='button' class='btn btn-success btn-sm' onclick='editExtra(" . $ID . ");'><i class='fa fa-edit'></i> Edit</button></td>";
		echo "</tr>";
	}
?>				
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class='modal fade' id='vendor-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<div class='modal-dialog modal-lg'>
		<form method='POST' action='saveVendor.php' id='vendorForm'>
		<input type='hidden' name='VendorID' id='VendorID' value=''>
		<div class='modal-content'>
			<div class='modal-header'><strong>Vendor Details<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body'>
				Vendor Name: <input type='text' name='vendorname' id='vendorname' class='validate[required] form-control' value='' maxlength='100'>
			</div>	
			<div class='modal-footer'>
				<button type='button' class='btn btn-default' data-dismiss='modal'><span class='fa fa-times'></span> Close</button>
				<button type='submit' class='btn btn-success pull-right'><i class='fa fa-save'></i> Save</button>
			</div>
		</div>
		</form>
	</div>
</div>
<div class='modal fade' id='speed-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<div class='modal-dialog modal-lg'>
		<form method='POST' action='saveSpeed.php' id='speedForm'>
		<input type='hidden' name='SpeedID' id='SpeedID' value=''>
		<div class='modal-content'>
			<div class='modal-header'><strong>Speed Details<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body'>
				<div class='table'>
					<table class='table table-bordered'>
					<tr><th rowspan='2'>Name</th><th colspan='<?php echo count($Vendors); ?>'>Vendor Cost Price</th></tr>
					<tr>
<?php
	foreach($Vendors AS $ID => $Rec)
	{
		echo "<th>" . $Rec . "</th>";
	}
?>			
					</tr>
					<tr><td><input type='text' name='speedname' id='speedname' class='validate[required] form-control' value='' maxlength='100'></td>
<?php
	foreach($Vendors AS $ID => $Rec)
	{
		echo "<td>";
		echo "<input type='checkbox' name='speedvendor_" . $ID . "' id='speedvendor_" . $ID . "' value='1'>\n";
		echo "<input type='text' name='speedprice_" . $ID . "' id='speedprice_" . $ID . "' class='form-control' value='' maxlength='8'></td>\n";
	}
?>						
					</tr>
					</table>
				</div>
			</div>	
			<div class='modal-footer'>
				<button type='button' class='btn btn-default' data-dismiss='modal'><span class='fa fa-times'></span> Close</button>
				<button type='submit' class='btn btn-success pull-right'><i class='fa fa-save'></i> Save</button>
			</div>
		</div>
		</form>
	</div>
</div>

<div class='modal fade' id='ont-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<div class='modal-dialog modal-lg'>
		<form method='POST' action='saveONT.php' id='ontForm'>
		<input type='hidden' name='ONTID' id='ONTID' value=''>
		<div class='modal-content'>
			<div class='modal-header'><strong>ONT Details<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body'>
				<div class='table'>
					<table class='table table-bordered'>
					<tr><th rowspan='2'>Name</th><th colspan='<?php echo count($Vendors); ?>'>Vendor Cost Price</th></tr>
					<tr>
<?php
	foreach($Vendors AS $ID => $Rec)
	{
		echo "<th>" . $Rec . "</th>";
	}
?>			
					</tr>
					<tr><td><input type='text' name='ontname' id='ontname' class='validate[required] form-control' value='' maxlength='100'></td>
<?php
	foreach($Vendors AS $ID => $Rec)
	{
		echo "<td>";
		echo "<input type='checkbox' name='ontvendor_" . $ID . "' id='ontvendor_" . $ID . "' value='1'>\n";
		echo "<input type='text' name='ontprice_" . $ID . "' id='ontprice_" . $ID . "' class='form-control' value='' maxlength='8'></td>\n";
	}
?>						
					</tr>
					</table>
				</div>
			</div>	
			<div class='modal-footer'>
				<button type='button' class='btn btn-default' data-dismiss='modal'><span class='fa fa-times'></span> Close</button>
				<button type='submit' class='btn btn-success pull-right'><i class='fa fa-save'></i> Save</button>
			</div>
		</div>
		</form>
	</div>
</div>

<div class='modal fade' id='extra-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<div class='modal-dialog modal-lg'>
		<form method='POST' action='savePackageExtra.php' id='extraForm'>
		<input type='hidden' name='ExtraID' id='ExtraID' value=''>
		<div class='modal-content'>
			<div class='modal-header'><strong>Extra Details<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body'>
				<div class='table'>
					<table class='table table-bordered'>
					<tr><th>Name</th><td><input type='text' name='extraname' id='extraname' class='validate[required] form-control' value='' maxlength='100'></td></tr>
					<tr><th>Cost Price</th><td><input type='text' name='extraprice' id='extraprice' class='form-control' value='' maxlength='100'></td></tr>
					</table>
				</div>
			</div>	
			<div class='modal-footer'>
				<button type='button' class='btn btn-default' data-dismiss='modal'><span class='fa fa-times'></span> Close</button>
				<button type='submit' class='btn btn-success pull-right'><i class='fa fa-save'></i> Save</button>
			</div>
		</div>
		</form>
	</div>
</div>
<?php
include("footer.inc.php");
?>