<?php
session_start();
include_once("header.inc.php");
$ErrMsg = '';
$YayMsg = '';
if(isset($_SESSION['errmsg']))
{
	$ErrMsg = $_SESSION['errmsg'];
	unset($_SESSION['errmsg']);
}
if(isset($_SESSION['yaymsg']))
{
	$YayMsg = $_SESSION['yaymsg'];
	unset($_SESSION['yaymsg']);
}
$Units = getCustomerUnits($_SESSION['customerid']);
$CustomerRec = getCustomerByID($_SESSION['customerid']);
$PackageArr = getFibrePackages();
$VendorSpeeds = getPackageSpeeds();
$VendorSpeeds[0] = '-';
$VendorONTs = getONTTypes();
echo "<!-- \n";
print_r($CustomerRec);
echo "<hr>";
print_r($Units);
echo "\n-->\n";
if($ErrMsg != '')
{
	echo "<div id='errmsg' class='bg-danger'>";
	echo "<h3>Error</h3>";
	echo "<div class='row'>";
	echo "<div class='col-md-12 col-sm-12'>" . $ErrMsg . "</div>";
	echo "</div>";
	echo "</div>";
}
if($YayMsg != '')
{
	echo "<div id='yaymsg' class='bg-success'>";
	echo "<h3>Success</h3>";
	echo "<div class='row'>";
	echo "<div class='col-md-12 col-sm-12'>" . $YayMsg . "</div>";
	echo "</div>";
	echo "</div>";
}
?>
<script>
$(document).ready(function()
{
	$("#personalform").validationEngine();
	$("#billingform").validationEngine();
	$('#errmsg').delay(5000).fadeOut('slow');
	$('#yaymsg').delay(5000).fadeOut('slow');

});
</script>
<!-- <div class='container'>
All logged in and stuff<br>
<?php print_r($_SESSION); ?>
</div>-->
<div id="packages">
	<h3>My packages</h3>
	<div class='row'>
		<div class="col-md-9 col-sm-9">
			<div class='table'>
				<table class='table table-bordered table-condensed'>
				<tr><th>Unit</th><th>Package</th><th>Speed</th><th>Monthly Cost</th><th>Term</th><th></th></tr>
<?php
foreach($Units AS $UnitID => $UnitRec)
{
	$ComplexRec = getComplexByID($UnitRec['complexid']);
	$PackageRec = getUnitPackage($UnitID);
	foreach($PackageRec AS $OrderID => $OrderRec)
	{
		if($PackageArr[$UnitRec['packageid']]['termnum'] == 0)
			$Term = "-";
		else
			$Term = ($OrderRec['termnum'] == 1) ? "Month to month" : $OrderRec['termnum'] . " months";
		$Cost = ($OrderRec['monthlycost'] == 0) ? "-" : "R " . sprintf("%0.2d", $OrderRec['monthlycost']);
		echo "<tr>";
		echo "<td>" . $UnitRec['unitnumber'] . " " . $ComplexRec['complexname'] . "</td>";
		echo "<td>" . $PackageArr[$OrderRec['packageid']]['packagename'] . "</td>";
		echo "<td>" . $VendorSpeeds[$OrderRec['speedid']] . "</td>";
		echo "<td>" . $Cost . "</td>";
		echo "<td>" . $Term . "</td>";
		echo "<td><a href='changepackage.php?o=" . $OrderID . "' class='btn btn-primary'><i class='fa fa-arrows-v'></i> Change my package</a></td>";
		echo "</tr>";
	}
}
?>
				</table>
			</div>
		</div>
		<div class="col-md-3 col-sm-3">
			<a href='voip.php' class='btn btn-default'>Voice over IP Settings</a>
		</div>
	</div>
</div>
<div class="container">
	<div class="row">
		<div class="col-md-1 col-sm-1"></div>
		<div class="col-md-10 col-sm-10">
			<hr>
		</div>
		<div class="col-md-1 col-sm-1"></div>
	</div>
</div>
<div id="details">
	<h3>My Details</h3>
	<div class='row'>
		<div class="col-md-6 col-sm-6">
			<h4>Personal Details</h4>
			<div class='table'>
				<form method='POST' action='savePersonal.php' id='personalform'>
				<table class='table table-bordered table-condensed'>
				<tr><th>Firstname: </th><td><input type='text' name='custname' id='custname' class='validate[required] form-control' value='<?php echo $CustomerRec['customername']; ?>'></td></tr>
				<tr><th>Surname: </th><td><input type='text' name='custsurname' id='custsurname' class='validate[required] form-control' value='<?php echo $CustomerRec['customersurname']; ?>'></td></tr>
				<tr><th>ID Number: </th><td><input type='text' class='form-control' value='<?php echo $CustomerRec['idnumber']; ?>' readonly='readonly'></td></tr>
				<tr><th>Cell: </th><td><input type='text' name='custcell' id='custcell' class='validate[groupRequired[contactnumbers]] form-control' value='<?php echo $CustomerRec['cell1']; ?>'></td></tr>
				<tr><th>Tel: </th><td><input type='text' name='custtel' id='custtel' class='validate[groupRequired[contactnumbers]] form-control' value='<?php echo $CustomerRec['tel1']; ?>'></td></tr>
				<tr><th colspan='2'><button type='submit' class='btn btn-primary pull-right'><i class='fa fa-save'></i> Save</button></td></tr>
				</table>
				</form>
			</div>
		</div>
		<div class="col-md-6 col-sm-6">
			<h4>Billing Details <small>If paying via a company etc.</small></h4>
			<div class='table'>
				<form method='POST' action='saveBilling.php' id='billingform'>
				<input type='hidden' name='billingid' id='billingid' value='<?php echo $CustomerRec['billingid']; ?>'>
				<table class='table table-bordered table-condensed'>
				<tr><th>Billing Name: </th><td><input type='text' name='billname' id='billname' class='validate[required] form-control' value='<?php echo $CustomerRec['billingname']; ?>'></td></tr>
				<tr><th>Billing Contact: </th><td><input type='text' name='billcontact' id='billcontact' class='validate[required] form-control' value='<?php echo $CustomerRec['billingcontact']; ?>'></td></tr>
				<tr><th>Billing Email: </th><td><input type='text' name='billemail' id='billemail' class='validate[required] form-control' value='<?php echo $CustomerRec['billingemail']; ?>'></td></tr>
				<tr><th>Billing Cell: </th><td><input type='text' name='billcell' id='billcell' class='form-control' value='<?php echo $CustomerRec['billingcell']; ?>'></td></tr>
				<tr><th colspan='2'><button type='submit' class='btn btn-primary pull-right'><i class='fa fa-save'></i> Save</button></td></tr>
				</table>
				</form>
			</div>
		</div>
	</div>
</div>
<div class="container">
	<div class="row">
		<div class="col-md-1 col-sm-1"></div>
		<div class="col-md-10 col-sm-10">
			<hr>
		</div>
		<div class="col-md-1 col-sm-1"></div>
	</div>
</div>
<div id="changepass">
	<h3>Change my password</h3>
	<div class='row'>
		<form method='POST' action='changePassword.php' id='changepassform'>
			<div class="col-md-2 col-sm-2">Old password:</div>
			<div class="col-md-2 col-sm-2"><input type='password' name='oldpass' id='oldpass' class='form-control' value='' placeholder='Old Password'></div>
			<div class="col-md-2 col-sm-2">New password:</div>
			<div class="col-md-2 col-sm-2"><input type='password' name='newpass1' id='newpass1' class='form-control' value='' placeholder='New password'></div>
			<div class="col-md-2 col-sm-2"><input type='password' name='newpass2' id='newpass2' class='form-control' value='' placeholder='Confirm new password'></div>
			<div class="col-md-2 col-sm-2"><button type='submit' class='btn btn-primary pull-right'><i class='fa fa-save'></i> Change password</button></div>
		</form>
	</div>
</div>
<!--
<div id="logfault">
	<h3>Log a fault</h3>
	<div class='row'>
		<div class="col-md-12 col-sm-12">
			<h4>Fault Details</h4>
			<div class='table'>
				<form method='POST' action='logfault.php' id='personalform'>
				<table class='table table-bordered table-condensed'>
				<tr><th>Firstname: </th><td><input type='text' name='' id='' class='form-control' value='<?php echo $CustomerRec['customername']; ?>'></td></tr>
				<tr><th>Surname: </th><td><input type='text' name='' id='' class='form-control' value='<?php echo $CustomerRec['customersurname']; ?>'></td></tr>
				<tr><th>ID Number: </th><td><input type='text' name='' id='' class='form-control' value='<?php echo $CustomerRec['idnumber']; ?>'></td></tr>
				<tr><th>Cell: </th><td><input type='text' name='' id='' class='form-control' value='<?php echo $CustomerRec['cell1']; ?>'></td></tr>
				<tr><th>Tel: </th><td><input type='text' name='' id='' class='form-control' value='<?php echo $CustomerRec['tel1']; ?>'></td></tr>
				<tr><th colspan='2'><button type='submit' class='btn btn-primary pull-right'><i class='fa fa-save'></i> Save</button></td></tr>
				</table>
				</form>
			</div>
		</div>
	</div>
</div> -->
<?php
include_once("footer.inc.php");
?>