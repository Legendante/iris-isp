<?php
include("header.inc.php");
$ComplexID = pebkac($_GET['cid'], 5);
$ComplexRec = getComplexByID($ComplexID);
$ComplextTypes = getComplexTypes();
$UnitStatusses = getUnitStatusses();
$OrderStatusses = getUnitOrderStatusses();
$OrderStatusses[0] = 'None';
$Agents = getAgents();
$Vendors = getVendors();
$Vendors[0] = '-';
$Residents = getComplexResidents($ComplexID);
$ComplexOrders = getComplexUnitPackages($ComplexID);
$Speeds = getPackageSpeeds();
$Speeds[0] = '-';
$ONTs = getONTTypes();
$ONTs[0] = '-';
$Packages = getFibrePackages();
$CompUnitCount = getComplexUnitMapCount($ComplexID);
// $Packages = getPackagesForVendorAndComplex($ComplexRec['vendorid'], $ComplexRec['complextype']);
$AgentName = ($ComplexRec['agentid'] > 0) ? $Agents[$ComplexRec['agentid']]['firstname'] . " " . $Agents[$ComplexRec['agentid']]['surname'] : '';
$SecAgentName = ($ComplexRec['secagentid'] > 0) ? $Agents[$ComplexRec['secagentid']]['firstname'] . " " . $Agents[$ComplexRec['secagentid']]['surname'] : '';
?>
<script>
$(document).ready(function()
{
	$("#residentForm").validationEngine();
});
</script>
<div class="panel panel-default">
	<div class="panel-heading"><h4>Complex Details
		<button type='button' class='btn btn-xs pull-right' onclick='$("#ComplexPanel").toggleClass("hidden"); $("#ComplexPanelCHV").toggleClass("fa-chevron-down");'>
		<span class='fa fa-chevron-up' id='ComplexPanelCHV'></span></button>
		</h4>
	</div>
	<div class="panel-body" id="ComplexPanel">
		<div class="form-group form-group-sm">
			<div class='row'>
				<div class='col-md-2'><strong>Complex:</strong></div><div class='col-md-6'><a href='complex.php?cid=<?php echo $ComplexID; ?>'><?php echo $ComplexRec['complexname']; ?></a></div>
				<div class='col-md-2'><strong>Code:</strong></div><div class='col-md-2'><?php echo $ComplexRec['complexcode']; ?></div>
			</div>
			<div class='row top5'>
				<div class='col-md-2'><strong># Units:</strong></div><div class='col-md-2'><?php echo $CompUnitCount; ?></div>
				<div class='col-md-2'><strong>Vendor:</strong></div><div class='col-md-2'><?php echo $Vendors[$ComplexRec['vendorid']]; ?></div>
				<div class='col-md-2'><strong>Registered:</strong></div><div class='col-md-2'><?php echo substr($ComplexRec['dateregistered'], 0, 10); ?></div>
			</div>
			<div class='row top5'>
				<div class='col-md-2'><strong>Agent:</strong></div><div class='col-md-2'><?php echo $AgentName; ?></div>
				<div class='col-md-2'><strong>Secondary Agent:</strong></div><div class='col-md-2'><?php echo $SecAgentName; ?></div>
				<div class='col-md-2'><strong>Type:</strong></div><div class='col-md-2'><?php echo $ComplextTypes[$ComplexRec['complextype']]; ?></div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading"><h4>Residents
		
		<button type='button' class='btn btn-xs pull-right' onclick='$("#ResidentsPanel").toggleClass("hidden"); $("#ResidentsPanelCHV").toggleClass("fa-chevron-down");'>
		<span class='fa fa-chevron-up' id='ResidentsPanelCHV'></span></button>
		<a href='excelComplexResidents.php?c=<?php echo $ComplexID; ?>' class='btn btn-success btn-xs pull-right'><span class='fa fa-file-excel-o' id=''></span></a>
		</h4>
	</div>
	<div class="panel-body" id="ResidentsPanel">
		<div class='table'>
			<table class='table table-bordered'>
			<tr><th>Unit #</th><th>Resident</th><th>Customer Number</th><th>Registered</th><th>Owner/Tenant</th><!-- <th>Status</th> --><th>Package</th>
				<th><button type='button' class='btn btn-success' onclick='$("#resident-modal").modal("show");'><i class='fa fa-plus'></i> Add Resident</button></th></tr>
<?php
foreach($Residents AS $UnitID => $ResRec)
{
	$Owner = ($ResRec['unitowner'] == 0) ? 'T' : 'O';
	echo "<tr>";
	echo "<td>" . $ResRec['unitnumber'] . "</td>";
	echo "<td><a href='customer.php?cid=" . $ResRec['customerid'] . "&uid=" . $UnitID . "'>" . $ResRec['customername'] . " " . $ResRec['customersurname'] . "</a></td>";
	echo "<td>" . getCustomerNumber($ResRec['customerid']) . "</td>";
	echo "<td>" . $ResRec['dateregistered'] . "</td>";
	echo "<td>" . $Owner . "</td>";
	//echo "<td>" . $ResRec['statusid'] . "</td>";
	echo "<td>";
	if(isset($ComplexOrders[$UnitID]))
	{
		foreach($ComplexOrders[$UnitID] AS $OrderID => $OrderRec)
		{
			echo $Packages[$OrderRec['packageid']]['packagename'];
		}
	}
	echo "</td>";
	// echo "<td>" . $Packages[$ResRec['packageid']]['packagename'] . "</td>";
	echo "<td><a href='customer.php?cid=" . $ResRec['customerid'] . "&uid=" . $UnitID . "' class='btn btn-md'><i class='fa fa-eye'></i> View</a></td>";
	echo "</tr>";
}
?>
			</table>
		</div>
	</div>
</div>

<div class='modal fade' id='resident-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<form method='POST' action='saveComplexResident.php' id='residentForm'>
	<input type='hidden' name='ComplexID' id='ComplexID' value='<?php echo $ComplexID; ?>'>
	<div class='modal-dialog modal-lg'>
		<div class='modal-content'>
			<div class='modal-header'><strong>New Resident<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body'>
				<div class='row top5'>
					<div class='col-md-2'>Unit #:</div><div class='col-md-3'><input type='text' name='unitnum' id='unitnum' class='validate[required] form-control' maxlength='6'></div>
					<div class='col-md-6'>
						<input type='radio' name='unitowner' id='unitowner_0' maxlength='6' value='0' checked='checked'><label for='unitowner_0'>Tenant</label><br>
						<input type='radio' name='unitowner' id='unitowner_1' maxlength='6' value='1'><label for='unitowner_1'>Owner</label>
					</div>
				</div>
				<div class='row top5'><div class='col-md-2'>Firstname:</div><div class='col-md-4'><input type='text' name='customername' id='customername' class='validate[required] form-control' maxlength='100'></div>
					<div class='col-md-2'>Surname:</div><div class='col-md-4'><input type='text' name='customersurname' id='customersurname' class='validate[required] form-control' maxlength='100'></div></div>
				<div class='row top5'><div class='col-md-2'>ID/Passport Number:</div><div class='col-md-4'><input type='text' name='idnumber' id='idnumber' class='validate[required] form-control' maxlength='30'></div>
					<div class='col-md-2'>Status:</div><div class='col-md-4'>
						<select name='sitestatus' class="validate[required] form-control">
					<option value=''>-- Select Status --</option>
<?php 
foreach($UnitStatusses AS $StatusID => $StatusRec)
{
	echo "<option value='" . $StatusID. "'>" . $StatusRec['statusname'] . "</option>";
}
?>	
					</select>
					</div>
				</div>
				<div class='row top5'><div class='col-md-2'>Email:</div><div class='col-md-2'><input type='text' name='email1' id='email1' class='validate[custom[email]] form-control' maxlength='100'></div>
					<div class='col-md-2'>Cell:</div><div class='col-md-2'><input type='text' name='cell1' id='cell1' class='validate[custom[phone]] form-control' maxlength='30'></div>
					<div class='col-md-2'>Tel:</div><div class='col-md-2'><input type='text' name='tel1' id='tel1' class='validate[custom[phone]] form-control' maxlength='30'></div></div>
				<div class='row top5'><div class='col-md-12'>
					<div class='table'>
					<table class='table table-bordered'>
					<tr><th>&nbsp;</th><th>Name</th><th>Term</th><th>Speed</th><th>ONT</th><th>ONT Cost</th><th>Install Cost</th><th>Device Fee</th><th>Connection Cost</th><th>Monthly Cost</th></tr>
<?php
// foreach($Packages AS $PackageID => $PackRec)
// {
	// echo "<tr>";
	// echo "<td><input type='radio' name='packageid' id='packageid_" . $PackageID . "' value='" . $PackageID . "'></td>";
	// echo "<td><label for='packageid_" . $PackageID . "'>" . $PackRec['packagename'] . "</label></td>";
	// echo "<td>" . $PackRec['monthsterm'] . "</td>";
	// echo "<td>" . $Speeds[$PackRec['speedid']] . "</td>";
	// echo "<td>" . $ONTs[$PackRec['ontid']] . "</td>";
	// echo "<td>" . $PackRec['ontcost'] . "</td>";
	// echo "<td>" . $PackRec['installcost'] . "</td>";
	// echo "<td>" . $PackRec['devicefee'] . "</td>";
	// echo "<td>" . $PackRec['connectcost'] . "</td>";
	// echo "<td>" . $PackRec['monthlycost'] . "</td>";
	// echo "</tr>";
// }
?>
					</table>
					</div>
				</div></div>
				
			</div>
			<div class='modal-footer'>
				<button type='button' class='btn btn-default btn-xs' data-dismiss='modal'><span class='fa fa-times'></span> Close</button>
				<button type='submit' class='btn btn-success'><span class='fa fa-save'></span> Save</button>
			</div>
		</div>
	</div>
	</form>
</div>
<?php
include("footer.inc.php");
?>