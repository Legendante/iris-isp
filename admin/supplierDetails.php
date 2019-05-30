<?php
include("header.inc.php");
$SupplierID = (isset($_GET['s'])) ? pebkac($_GET['s']) : 0;
$Suppliers = getSuppliers($SupplierID);
$Supplier = $Suppliers[$SupplierID];
?>
<form method='POST' action='saveSupplier.php' id='supplierForm'>
<input type='hidden' name='SupplierID' id='SupplierID' value='<?php echo $SupplierID; ?>'>
<div class='row'>
	<div class='col-md-12'>
		<div class='table'>
			<table class='table table-bordered'>
			<tr><th>Name</th><td colspan='3'><input type='text' name='supname' id='supname' class='validate[required] form-control' value='<?php echo $Supplier['suppliername']; ?>' maxlength='30'></td></tr>
			<tr><th>Reg Num</th><td><input type='text' name='regnum' id='regnum' class='form-control' value='<?php echo $Supplier['supplierregnum']; ?>' maxlength='30'></td>
			<th>Vat Num</th><td><input type='text' name='vatnum' id='vatnum' class='form-control' value='<?php echo $Supplier['suppliervatnum']; ?>' maxlength='30'></td></tr>
			<tr><th>Email</th><td><input type='text' name='supemail' id='supemail' class='form-control' value='<?php echo $Supplier['supplieremail']; ?>' maxlength='30'></td>
			<th>Tel</th><td><input type='text' name='suptel' id='suptel' class='form-control' value='<?php echo $Supplier['suppliertel']; ?>' maxlength='30'></td></tr>
			<tr><th>Address</th><td colspan='3'><input type='text' name='supaddie' id='supaddie' class='form-control' value='<?php echo $Supplier['supplieraddress']; ?>' maxlength='30'></td></tr>
			<tr><td colspan='4'><button type='submit' class='btn btn-success pull-right'><i class='fa fa-save'></i> Save</button></td></tr>
			</table>
		</div>
	</div>
</div>
</form>
<?php
include("footer.inc.php");
?>