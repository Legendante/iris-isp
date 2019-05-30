<?php
include("header.inc.php");
$CustomerID = pebkac($_GET['cid'], 5);
$Users = getUsers();
$UnitID = (isset($_GET['uid'])) ? pebkac($_GET['uid'], 5) : 0;
$ImportRecord = array();
// $ImportRecord = getCustomerImportRecord($CustomerID);
$CustomerArr = getCustomerByID($CustomerID);
// print_r($CustomerArr);
$UnitArr = getCustomerUnits($CustomerID);
$BillingID = ($CustomerArr['billingid'] != '') ? $CustomerArr['billingid'] : 0;
$Speeds = getPackageSpeeds();
$Speeds[0] = '-';
$ONTs = getONTTypes();
$ONTs[0] = '-';
$Statusses = getUnitStatusses();
$Statusses[0] = array("statusname" => "-");
$PackageArr = getFibrePackages();
$VendorSpeeds = getPackageSpeeds();
$VendorONTs = getONTTypes();
$PackSpeeds = array();
foreach($PackageArr AS $PackID => $PackArr)
{
	$PackSpeeds[$PackArr['speedid']] = $PackArr['packagename'];
}
$CreditNotes = getCustomerCreditNotes($CustomerID);
?>
<script>
$(document).ready(function()
{
	$("#customerForm").validationEngine();
	$("#billingForm").validationEngine();
	$("#commentForm").validationEngine();
});
function showStatus(UnitID)
{
	$('#UnitID').val(UnitID);
	var adate = new Date().getTime();
	$.ajax({async: false, type: "POST", url: "ajaxGetUnitStatus.php", dataType: "json",
		data: "dc=" + adate + "&uid=" + UnitID,
		success: function (feedback)
		{
			$('#unitstatus').val(feedback.status);
		},
		error: function(request, feedback, error)
		{
			alert("Request failed\n" + feedback + "\n" + error);
			return false;
		}
	});
	$("#status-modal").modal("show");
}

function showHistory(UnitID)
{
	var adate = new Date().getTime();
	$.ajax({async: false, type: "POST", url: "ajaxGetUnitStatusHistory.php", dataType: "html",
		data: "dc=" + adate + "&uid=" + UnitID,
		success: function (feedback)
		{
			$('#historyTable').html(feedback);
		},
		error: function(request, feedback, error)
		{
			alert("Request failed\n" + feedback + "\n" + error);
			return false;
		}
	});
	$("#comment-modal").modal("show");
}
</script>
<form method='POST' action='saveCustomer.php' id='customerForm'>
<input type='hidden' name='CustomerID' id='CustomerID' value='<?php echo $CustomerID; ?>'>
<div class="panel panel-default">
	<div class="panel-heading"><h4>Customer Details
		<button type='button' class='btn btn-xs pull-right' onclick='$("#CustomerPanel").toggleClass("hidden"); $("#CustomerPanelCHV").toggleClass("fa-chevron-down");'>
		<span class='fa fa-chevron-up' id='CustomerPanelCHV'></span></button>
		
		<?php
if(count($ImportRecord) > 0)
	echo "<button type='button' class='btn btn-xs btn-warning pull-right' data-toggle='modal' data-target='#import-modal'><i class='fa fa-download'></i> Import Data</button>\n";
?>

		
		</h4>
	</div>
	<div class="panel-body" id="CustomerPanel">
		<div class="form-group form-group-sm">
		<div class='row'>
			<div class='col-md-10'>
				<div class='row'>
					<div class='col-md-2'>Firstname:</div><div class='col-md-3'><input type='text' name='customername' id='customername' maxlength='100' class='validate[required] form-control' value='<?php echo $CustomerArr['customername']; ?>'></div>
					<div class='col-md-2'>Surname:</div><div class='col-md-3'><input type='text' name='customersurname' id='customersurname' maxlength='100' class='validate[required] form-control' value='<?php echo $CustomerArr['customersurname']; ?>'></div>
				</div>
				<div class='row'>
					<div class='col-md-2'>ID Number:</div><div class='col-md-3'><input type='text' name='idnumber' id='idnumber' maxlength='100' class='validate[required] form-control' value='<?php echo $CustomerArr['idnumber']; ?>'></div>
					<div class='col-md-2'>Date Registered:</div><div class='col-md-3'><?php echo $CustomerArr['dateregistered']; ?></div>
				</div>
				<div class='row'>
					<div class='col-md-2'>Email:</div><div class='col-md-2'><input type='text' name='email1' id='email1' maxlength='100' class='validate[custom[email],groupRequired[custcontact]] form-control' value='<?php echo $CustomerArr['email1']; ?>'></div>
					<div class='col-md-1'>Cell:</div><div class='col-md-2'><input type='text' name='cell1' id='cell1' maxlength='100' class='validate[custom[phone], groupRequired[custcontact]] form-control' value='<?php echo $CustomerArr['cell1']; ?>'></div>
					<div class='col-md-1'>Tel:</div><div class='col-md-2'><input type='text' name='tel1' id='tel1' maxlength='100' class='validate[custom[phone],groupRequired[custcontact]] form-control' value='<?php echo $CustomerArr['tel1']; ?>'></div>
				</div>
				<div class='row top5'>
					<div class='col-md-3'><button type='submit' class='btn btn-success pull-right'><i class='fa fa-save'></i> Save</button></div>
				</div>
			</div>
			<div class='col-md-2'>
				<div class="btn-group-vertical">
					Balance: <?php echo $CustomerArr['customerbalance']; ?>
					<button type='button' class='btn btn-danger' data-toggle="modal" data-target="#credit-modal">Credit Account</button>
					<button type='button' class='btn btn-warning' data-toggle="modal" data-target="#creditnotes-modal">Credit Notes</button>					
					<a href='monthlyInvoices.php?c=<?php echo $CustomerID; ?>' class='btn btn-success'>Generate Invoice</a>
				</div>
			</div>
		</div>
		</div>
	</div>
</div>
</form>
<div class="panel panel-default">
	<div class="panel-heading"><h4>Unit Details
		<button type='button' class='btn btn-xs pull-right' onclick='$("#UnitPanel").toggleClass("hidden"); $("#UnitPanelCHV").toggleClass("fa-chevron-down");'>
		<span class='fa fa-chevron-up' id='UnitPanelCHV'></span></button>
		</h4>
	</div>
	<div class="panel-body" id="UnitPanel">
		<div class='table'>
			<table class='table table-bordered'>
			<tr><th>Unit #</th><th>Complex</th><th>Owner/Tenant</th><th>Package</th><th>Status</th></tr>
<?php
$Packages = getFibrePackages();
$SetPack = 0;
$SetONT = 0;
$SetMnts = 0;
$SetOrderID = 0;
foreach($UnitArr AS $UnitID => $UnitRec)
{
	$UnitPack = getUnitPackage($UnitID);
	$Owner = ($UnitRec['unitowner'] == 0) ? 'Tenant' : 'Owner';
	$ComplexRec = getComplexByID($UnitRec['complexid']);
	// $Packages = getPackages(); // getPackagesForVendorAndComplex($ComplexRec['vendorid'], $ComplexRec['complextype']);
	$StatusID = ($UnitRec['statusid'] != '') ? $UnitRec['statusid'] : 0;
	echo "<tr>";
	echo "<td>" . $UnitRec['unitnumber'] . "</td>";
	echo "<td>" . $ComplexRec['complexname'] . "</td>";
	echo "<td>" . $Owner . "</td>";
	echo "<td>";
	// print_r($UnitPack);
	foreach($UnitPack AS $OrderID => $OrderRec)
	{
		echo $Packages[$OrderRec['packageid']]['packagename'];
		$SetPack = $OrderRec['speedid'];
		$SetONT = $OrderRec['ontid'];
		$SetMnts = $OrderRec['termnum'];
		$SetOrderID = $OrderID;
	}
	echo "<button type='button' class='btn btn-success btn-sm pull-right' data-toggle='modal' data-target='#package-modal'><i class='fa fa-dollar'></i> Change Package</button>";
	echo "</td>";
	echo "<td>" . $Statusses[$StatusID]['statusname'];
	echo "<button type='button' class='btn btn-primary btn-sm pull-right' onclick='showStatus(" . $UnitID . ");'><i class='fa fa-share'></i> Change/Comment</button>";
	echo "<button type='button' class='btn btn-primary btn-sm pull-right' onclick='showHistory(" . $UnitID . ");'><i class='fa fa-comment'></i> History</button>";
	echo "</td>";
	echo "</tr>";
}
?>
			</table>
		</div>
	</div>
</div>

<form method='POST' action='saveCustomerBilling.php' id='billingForm'>
<input type='hidden' name='CustomerID' id='CustomerID' value='<?php echo $CustomerID; ?>'>
<input type='hidden' name='BillingID' id='BillingID' value='<?php echo $BillingID; ?>'>
<div class="panel panel-default">
	<div class="panel-heading"><h4>Billing Details
		<button type='button' class='btn btn-xs pull-right' onclick='$("#BillingPanel").toggleClass("hidden"); $("#BillingPanelCHV").toggleClass("fa-chevron-down");'>
		<span class='fa fa-chevron-up' id='BillingPanelCHV'></span></button>
		</h4>
	</div>
	<div class="panel-body" id="BillingPanel">
		<div class="form-group form-group-sm">
			<div class='row'>
				<div class='col-md-1'>Contact:</div><div class='col-md-3'><input type='text' name='billingcontact' id='billingcontact' maxlength='100' class='form-control' value='<?php echo $CustomerArr['billingcontact']; ?>'></div>
				<div class='col-md-1'>Email:</div><div class='col-md-3'><input type='text' name='billingemail' id='billingemail' maxlength='100' class='validate[custom[email]] form-control' value='<?php echo $CustomerArr['billingemail']; ?>'></div>
				<div class='col-md-1'>Cell:</div><div class='col-md-3'><input type='text' name='billingcell' id='billingcell' maxlength='100' class='validate[custom[phone]] form-control' value='<?php echo $CustomerArr['billingcell']; ?>'></div>
			</div>
			<div class='row top5'>
				<div class='col-md-3'><button type='submit' class='btn btn-success pull-right'><i class='fa fa-save'></i> Save</button></div>
			</div>
		</div>
	</div>
</div>
</form>

<div class='modal fade' id='status-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<form method='POST' action='saveUnitStatusComment.php' id='commentForm'>
	<input type='hidden' name='UnitID' id='UnitID' value=''>
	<div class='modal-dialog modal-lg'>
		<div class='modal-content'>
			<div class='modal-header'><strong>Status Comment<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body'>
			<div class='row top5'><div class='col-md-3'>Status:</div><div class='col-md-9'>
					<select name='unitstatus' id='unitstatus' class="validate[required] form-control">
					<option value=''>-- Select Status --</option>
<?php 
foreach($Statusses AS $StatusID => $StatusRec)
{
	$StatusName = $StatusRec['statusname'];
	if($StatusRec['parentid'] > 0)
		$StatusName = " -- " . $StatusName;
	echo "<option value='" . $StatusID. "'>" . $StatusName . "</option>";
}
?>	
					</select>
				</div>
			</div>
			<div class='row top5'><div class='col-md-12'>Comment:</div></div>
			<div class='row top5'><div class='col-md-12'><textarea id='statuscomment' name='statuscomment' class='validate[required] form-control'></textarea></div></div>
			</div>
			<div class='modal-footer'>
				<button type='button' class='btn btn-default btn-xs' data-dismiss='modal'><span class='fa fa-times'></span> Close</button>
				<button type='submit' class='btn btn-success'><span class='fa fa-save'></span> Save</button>
			</div>
		</div>
	</div>
	</form>
</div>

<div class='modal fade' id='package-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<form method='POST' action='saveUnitPackage.php' id='commentForm'>
	<input type='hidden' name='CustUnitID' id='CustUnitID' value='<?php echo $CustomerID; ?>'>
	<input type='hidden' name='PackUnitID' id='PackUnitID' value='<?php echo $UnitID; ?>'>
	<div class='modal-dialog modal-lg'>
		<div class='modal-content'>
			<div class='modal-header'><strong>Change package<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body'>
				<div class='table'>
					<table class='table table-condensed'>
					<tr><td>
						<table class='table table-bordered'>
						<tr><th colspan='2'>Speed</th></tr>
<?php
foreach($Speeds AS $SpeedID => $SpeedRec)
{
	echo "<tr><td><input type='radio' name='packspeed' id='packspeed_" . $SpeedID . "'";
	if($SetPack == $SpeedID)
		echo " checked='checked'";
	echo " value='" . $SpeedID . "'></td><td><label for='packspeed_" . $SpeedID . "'>" . $PackSpeeds[$SpeedID] . "</label></td></tr>";
}
?>

						</table>
					</td><td>
						<table class='table table-bordered'>
						<tr><th colspan='2'>ONT</th></tr>
<?php
foreach($VendorONTs AS $ONTID => $ONTRec)
{
	echo "<tr><td><input type='radio' name='packont' id='packont_" . $ONTID . "'";
	if($SetONT == $ONTID)
		echo " checked='checked'";
	echo " value='" . $ONTID . "'></td><td><label for='packont_" . $ONTID . "'>" . $ONTRec['ontname'] . "</label></td></tr>";
}
?>
						</table>
					</td><td>
						<table class='table table-bordered'>
						<tr><th colspan='2'>Period</th></tr>
<?php
$ContractPerid = array(1 => "Monthly",12 => "12 Months");
foreach($ContractPerid AS $PeriodID => $Period)
{
	echo "<tr><td><input type='radio' name='packper' id='packper_" . $PeriodID . "'";
	if($SetMnts == $PeriodID)
		echo " checked='checked'";
	echo " value='" . $PeriodID . "'></td><td><label for='packper_" . $PeriodID . "'>" . $Period . "</label></td></tr>";
}
?>						
						</table>
					</td></tr>
					</table>
				</div>
			</div>	
			<div class='modal-footer'>
				<button type='button' class='btn btn-default btn-xs' data-dismiss='modal'><span class='fa fa-times'></span> Close</button>
				<button type='submit' class='btn btn-success'><span class='fa fa-save'></span> Save</button>
			</div>
		</div>
	</div>
	</form>
</div>
<div class='modal fade' id='comment-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<div class='modal-dialog modal-lg'>
		<div class='modal-content'>
			<div class='modal-header'><strong>Status History<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body'>
				<div class='table'>
					<table class='table table-bordered' id='historyTable'>
					

					</table>
				</div>
			</div>	
			<div class='modal-footer'>
				<button type='button' class='btn btn-default btn-xs' data-dismiss='modal'><span class='fa fa-times'></span> Close</button>
			</div>
		</div>
	</div>
</div>
<div class='modal fade' id='credit-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<form method='POST' action='saveCustomerCredit.php' id='commentForm'>
	<input type='hidden' name='CustomerID' id='CustomerID' value='<?php echo $CustomerID; ?>'>
	<div class='modal-dialog modal-sm'>
		<div class='modal-content'>
			<div class='modal-header'><strong>Credit Customer<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body'>
				<div class='row'>
					<div class='col-md-6'>Number of days:</div>
					<div class='col-md-4'><input type='text' name='credDays' id='credDays' value='' class='form-control'></div>
				</div>
				<div class='row'><div class='col-md-6'>Reason:</div></div>
				<div class='row'><div class='col-md-12'><textarea name='credReason' id='credReason' class='form-control'></textarea></div></div>
			</div>
			<div class='modal-footer'>
				<button type='button' class='btn btn-default btn-xs' data-dismiss='modal'><span class='fa fa-times'></span> Close</button>
				<button type='submit' class='btn btn-success'><span class='fa fa-save'></span> Save</button>
			</div>
		</div>
	</div>
	</form>
</div>

<div class='modal fade' id='creditnotes-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<div class='modal-dialog modal-lg'>
		<div class='modal-content'>
			<div class='modal-header'><strong>Customer Notes<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body'>
				<div class='table'>
				<table class='table table-bordered table-condensed'>
				<tr><th>Date</th><th>User</th><th>Amount</th><th>Status</th><th>Reason</th></tr>
<?php
foreach($CreditNotes AS $CreditID => $CreditRec)
{
	echo "<tr>";
	echo "<td>" . $CreditRec['creditdate'] . "</td>"; 
	echo "<td>" . $Users[$CreditRec['creditby']]['firstname'] . " " . $Users[$CreditRec['creditby']]['surname'] . "</td>"; 
	echo "<td>R " . $CreditRec['creditamount'] . "</td>"; 
	echo "<td>" . $CreditRec['creditstatus'] . "</td>"; 
	echo "<td>" . $CreditRec['creditdescription'] . "</td>"; 
	echo "</tr>";
}
?>
				</table>
				</div>
			</div>
			<div class='modal-footer'>
				<button type='button' class='btn btn-default btn-xs' data-dismiss='modal'><span class='fa fa-times'></span> Close</button>
			</div>
		</div>
	</div>
</div>
<?php
if(count($ImportRecord) > 0)
{
?>
<div class='modal fade' id='import-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<div class='modal-dialog modal-lg'>
		<div class='modal-content'>
			<div class='modal-header'><strong>Import Data<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body'>
				<div class='table'>
					<table class='table table-bordered' id='unitmap'>
					<tr><th>Field</th><th>Value</th></tr>
<?php
foreach($ImportRecord AS $Key => $Val)
{
	echo "<tr><td>" . $Key . "</td><td>" . $Val . "</td></tr>\n";
}
?>				
					</table>
				</div>
			</div>	
			<div class='modal-footer'>
				<button type='button' class='btn btn-default' data-dismiss='modal'><span class='fa fa-times'></span> Close</button>
			</div>
		</div>
	</div>
</div>
<?php	
}
include("footer.inc.php");
?>