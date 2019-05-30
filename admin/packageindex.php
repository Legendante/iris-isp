<?php
include("header.inc.php");
$Speeds = getPackageSpeeds();
$ONTs = getONTTypes();
$ComplexTypes = getComplexTypes();
$Vendors = getVendors();
$PackageGroups = getPackageGroups();
$Packages = getPackages();
$Extras = getPackageExtras();
?>
<script>
$(document).ready(function()
{
	$("#packageForm").validationEngine();
	$("#groupForm").validationEngine();
});

var originalHTML = "";
function editPackageGroup(GroupID)
{
	$('#groupname').val();
	$('input:checkbox[name^="typeid"]').prop('checked', false);
	$('#GroupID').val(GroupID);
	$('#groupname').val($('#group_' + GroupID).val());
	var GroupTypes = getPackageGroupTypes(GroupID);
	$.each(GroupTypes, function (i, row)
	{
		$('#type_' + row).prop("checked", "checked");
	});
	$('#group-modal').modal("show");
}

function copyAddPackage(PackageID)
{
	editPackage(PackageID);
	$('#packagename').val('Copy - ' + $('#packagename').val());
	$('#PackageID').val(0);
}

function editPackage(PackageID)
{
	itemCnt = 0;
	if(originalHTML == "")
		originalHTML = $('#packageTable').html();
	$('#PackageID').val(PackageID);
	$('#packageTable').html(originalHTML);
	if(PackageID != 0)
	{
		var adate = new Date().getTime();
		$.ajax({async: false, type: "POST", url: "ajaxGetPackageDetails.php", dataType: "json",
			data: "dc=" + adate + "&pid=" + PackageID,
			success: function (feedback)
			{
				$('#packagename').val(feedback.packagename);
				$('#packagegroup').val(feedback.packagegroupid);
				$('#vendorid').val(feedback.vendorid);
				$.each(feedback.pieces, function (i, row)
				{
					if(row.speedid != 0)
					{
						$('#speedid').val(row.speedid);
						$('#speedcost').val(row.piecescost);
						$('#speedmonths').val(row.piecesnummonths);
						$('#speedcommission').val(row.piecescomms);
						$('#speedcontinues_' + row.endcontinues).prop("checked", "checked");
					}
					else if(row.ontid != 0)
					{
						$('#ontid').val(row.ontid);
						$('#ontcost').val(row.piecescost);
						$('#ontmonths').val(row.piecesnummonths);
						$('#ontcommission').val(row.piecescomms);
						$('#ontcontinues_' + row.endcontinues).prop("checked", "checked");
					}
					else if(row.extraid != 0)
					{
						var tmpCnt = itemCnt;
						addPackageRow();
						$("#extrapieceid_" + tmpCnt).val(row.pieceid);
						$("#extraid_" + tmpCnt).val(row.extraid);
						$("#extracost_" + tmpCnt).val(row.piecescost);
						$("#extramonths_" + tmpCnt).val(row.piecesnummonths);
						$("#extracomms_" + tmpCnt).val(row.piecescomms);
						$("#extra_" + tmpCnt + "_continues_" + row.endcontinues).prop("checked", "checked");
					}
				});
			},
			error: function(request, feedback, error)
			{
				alert("Request failed\n" + feedback + "\n" + error);
				return false;
			}
		});
	}
	$('#package-modal').modal("show");
}
var itemCnt = 0;
function addPackageRow()
{
	var HTMLStr = "<tr style='line-height: 2px; height: 2px;'><td colspan='6' class='bg-success' style='line-height: 2px; height: 2px;'>&nbsp;</td></tr>";
	HTMLStr += "<tr><th>Item</th><td>";
	HTMLStr += "<input type='hidden' name='extrapieceid_" + itemCnt + "' id='extrapieceid_" + itemCnt + "' value='0'>";
	HTMLStr += "<select name='extraid_" + itemCnt + "' id='extraid_" + itemCnt + "' class='form-control'>";
	HTMLStr += "<option value=''>-- Optional --</option>";
<?php
	foreach($Extras AS $ID => $Rec)
	{
		echo "HTMLStr += \"<option value='" . $ID . "'>" . $Rec['extraname'] . "</option>\";";
	}
?>				
	HTMLStr += "</select></td></tr>";
	HTMLStr += "<tr><th>Price</th><td><input type='text' name='extracost_" + itemCnt + "' id='extracost_" + itemCnt + "' class='validate[custom[number]] form-control' maxlength='10' value='' style='width: 100px;'></td>";
	HTMLStr += "<th># Months</th><td><input type='text' name='extramonths_" + itemCnt + "' id='extramonths_" + itemCnt + "' class='validate[min[1], custom[integer]] form-control' maxlength='10' value='' style='width: 100px;'></td>";
	HTMLStr += "<th>Comms</th><td><input type='text' name='extracomms_" + itemCnt + "' id='extracomms_" + itemCnt + "' class='validate[custom[number]] form-control' maxlength='10' value='' style='width: 100px;'></td></tr>";
	HTMLStr += "<tr><td colspan='2'>Continues after contract end?</td><td colspan='2'><input type='radio' name='extra_" + itemCnt + "_continues' id='extra_" + itemCnt + "_continues_1' value='1'><label for='extra_" + itemCnt + "_continues_1'>Yes</label> - ";
	HTMLStr += "<input type='radio' name='extra_" + itemCnt + "_continues' id='extra_" + itemCnt + "_continues_0' value='0' checked='checked'><label for='extra_" + itemCnt + "_continues_0'>No</label></td></tr>";
	itemCnt++;
	$('#packageTable').append(HTMLStr);
}
</script>
<div class='row'>
	<div class='col-md-12'>
		<div class="panel panel-default">
			<div class="panel-heading"><h4>Package Groups
				<button type='button' class='btn btn-xs pull-right' onclick='$("#ExtrasPanel").toggleClass("hidden"); $("#ExtrasPanelCHV").toggleClass("fa-chevron-down");'>
				<span class='fa fa-chevron-up' id='ExtrasPanelCHV'></span></button>
				</h4>
			</div>
			<div class="panel-body" id="ExtrasPanel">
				<div class='table'>
					<table class='table table-bordered'>
					<tr><th>Name</th><th width='100px'><button type='button' class='btn btn-success btn-sm' onclick='editPackageGroup(0);'><i class='fa fa-plus'></i> Add</button></th></tr>
<?php
	foreach($PackageGroups AS $ID => $Rec)
	{
		echo "<tr>";
		echo "<td>" . $Rec;
		echo "<input type='hidden' name='group_" . $ID . "' id='group_" . $ID . "' value='" . $Rec . "'></td>";
		echo "<td><button type='button' class='btn btn-success btn-sm' onclick='editPackageGroup(" . $ID . ");'><i class='fa fa-edit'></i> Edit</button></td>";
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
			<div class="panel-heading"><h4>Packages
				<button type='button' class='btn btn-xs pull-right' onclick='$("#ExtrasPanel").toggleClass("hidden"); $("#ExtrasPanelCHV").toggleClass("fa-chevron-down");'>
				<span class='fa fa-chevron-up' id='ExtrasPanelCHV'></span></button>
				</h4>
			</div>
			<div class="panel-body" id="ExtrasPanel">
				<div class='table'>
					<table class='table table-bordered'>
					<tr><th>Package Name</th><th>Group</th><th>Vendor</th><th>Type</th><th width='150px'><button type='button' class='btn btn-success btn-sm' onclick='editPackage(0);'><i class='fa fa-plus'></i> Add</button></th></tr>
<?php
foreach($Packages AS $PackageID => $Rec)
{
	$GroupName = ($Rec['packagegroupid'] == 0) ? "<i>None</i>" : $PackageGroups[$Rec['packagegroupid']];
	$VendorName = ($Rec['vendorid'] == 0) ? "<i>None</i>" : $Vendors[$Rec['vendorid']];
	$PackageType = ($Rec['packagetype'] == 0) ? "Add on" : "Sign-up";
	echo "<tr>";
	echo "<td>" . $Rec['packagename'] . "</td>";
	echo "<td>" . $GroupName . "</td>";
	echo "<td>" . $VendorName . "</td>";
	echo "<td>" . $PackageType . "</td>";
	echo "<td>";
	echo "<button type='button' class='btn btn-info btn-sm pull-left' onclick='copyAddPackage(" . $PackageID . ");'><i class='fa fa-copy'></i></button>";
	echo "<button type='button' class='btn btn-success btn-sm pull-right' onclick='editPackage(" . $PackageID . ");'><i class='fa fa-edit'></i> Edit</button>";
	echo "</td>";
	echo "</tr>";
}
?>
					
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class='modal fade' id='package-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<div class='modal-dialog modal-lg'>
		<form method='POST' action='savePackage.php' id='packageForm'>
		<input type='hidden' name='PackageID' id='PackageID' value=''>
		<div class='modal-content'>
			<div class='modal-header'><strong>Package Group Details<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body'>
				<div class='table'>
					<table class='table table-bordered' id='packageTable'>
					<tr><th>Name</th><td colspan='3'><input type='text' name='packagename' id='packagename' class='validate[required] form-control' value='' maxlength='100'></td>
					<th>Package Group</th><td><select name='packagegroup' id='packagegroup' class='validate[required] form-control'>
					<option value=''>-- Select One --</option>
<?php
	foreach($PackageGroups AS $ID => $Rec)
	{
		echo "<option value='" . $ID . "'>" . $Rec . "</option>";
	}
?>				
					
					</select></td></tr>
					<tr><th>Vendor</th><td colspan='3'><select name='vendorid' id='vendorid' class='form-control'>
					<option value=''>-- Optional --</option>
<?php
	foreach($Vendors AS $ID => $Rec)
	{
		echo "<option value='" . $ID . "'>" . $Rec . "</option>";
	}
?>				
					
					</select></td><td>Type:</td><td colspan='2'>
					<input type='radio' name='packtype' id='packtype_1' value='1'> <label for='packtype_1'>Sign-up</label><br>
					<input type='radio' name='packtype' id='packtype_0' value='0' checked='checked'> <label for='packtype_0'>Add-on</label>
					</td></tr>
					<tr><th>Speed</th><td><select name='speedid' id='speedid' class='form-control'>
					<option value=''>-- Optional --</option>
<?php
	foreach($Speeds AS $ID => $Rec)
	{
		echo "<option value='" . $ID . "'>" . $Rec . "</option>";
	}
?>				
					
					</select></td></tr>
					<tr><th>Price</th><td><input type='text' name='speedcost' id='speedcost' class='validate[custom[number]] form-control' maxlength='10' value='' style='width: 100px;'></td>
					<th># Months</th><td><input type='text' name='speedmonths' id='speedmonths' class='validate[min[1], custom[integer]] form-control' maxlength='10' value='' style='width: 100px;'></td>
					<th>Comms</th><td><input type='text' name='speedcommission' id='speedcommission' class='validate[custom[number]] form-control' maxlength='10' value='' style='width: 100px;'></td>
					</tr>
					<tr><td colspan='2'>Continues after term end?</td><td colspan='2'><input type='radio' name='speedcontinues' id='speedcontinues_1' value='1'><label for='speedcontinues_1'>Yes</label> - 
					<input type='radio' name='speedcontinues' id='speedcontinues_0' value='0' checked='checked'><label for='speedcontinues_0'>No</label></td>
					<td colspan='2'></td>
					</tr>
					<tr style='line-height: 2px; height: 2px;'><td colspan='6' class='bg-success' style='line-height: 2px; height: 2px;'>&nbsp;</td></tr>
					<tr><th>ONT</th><td colspan='2'><select name='ontid' id='ontid' class='form-control'>
					<option value=''>-- Optional --</option>
<?php
	foreach($ONTs AS $ID => $Rec)
	{
		echo "<option value='" . $ID . "'>" . $Rec . "</option>";
	}
?>				
					
					</select></td></tr>
					<tr><th>Price</th><td><input type='text' name='ontcost' id='ontcost' class='validate[custom[number]] form-control' maxlength='10' value='' style='width: 100px;'></td>
					<th># Months</th><td><input type='text' name='ontmonths' id='ontmonths' class='validate[min[1], custom[integer]] form-control' maxlength='10' value='' style='width: 100px;'></td>
					<th>Comms</th><td><input type='text' name='ontcommission' id='ontcommission' class='validate[custom[number]] form-control' maxlength='10' value='' style='width: 100px;'></td>
					</tr>
					<tr><td colspan='2'>Continues after term end?</td><td colspan='2'><input type='radio' name='ontcontinues' id='ontcontinues_1' value='1'><label for='ontcontinues_1'>Yes</label> - 
					<input type='radio' name='ontcontinues' id='ontcontinues_0' value='0' checked='checked'><label for='ontcontinues_0'>No</label></td>
					<td colspan='2'></td></tr>
					</table>
				</div>
			</div>	
			<div class='modal-footer'>
				<button type='button' class='btn btn-default pull-left' onclick='addPackageRow();'><span class='fa fa-plus'></span> Add Item</button>
				<button type='button' class='btn btn-default' data-dismiss='modal'><span class='fa fa-times'></span> Close</button>
				<button type='submit' class='btn btn-success pull-right'><i class='fa fa-save'></i> Save</button>
			</div>
		</div>
		</form>
	</div>
</div>

<div class='modal fade' id='group-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<div class='modal-dialog modal-lg'>
		<form method='POST' action='savePackageGroup.php' id='groupForm'>
		<input type='hidden' name='GroupID' id='GroupID' value=''>
		<div class='modal-content'>
			<div class='modal-header'><strong>Package Group Details<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body'>
				<div class='table'>
					<table class='table table-bordered'>
					<tr><th>Name</th><td><input type='text' name='groupname' id='groupname' class='form-control' value='' maxlength='100'></td></tr>
					<tr><th>Complex Types</th><td>
<?php
foreach($ComplexTypes AS $ID => $Rec)
{
	echo "<input type='checkbox' name='typeid[]' id='type_" . $ID . "' value='" . $ID . "'><label for='type_" . $ID . "'>" . $Rec . "</label><br>";
}
?>
					</td></tr>
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