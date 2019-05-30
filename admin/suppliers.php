<?php
include("header.inc.php");
$Suppliers = getSuppliers();
?>
<script>
$(document).ready(function()
{
	$("#supplierForm").validationEngine();
});

function editSupplier(SupplierID)
{
	$('#SupplierID').val(SupplierID);
	$('#supname').val($('#sup_' + SupplierID).html());
	$('#supplier-modal').modal("show");
}
</script>
<div class='row'>
	<div class='col-md-12'>
		<div class="panel panel-default">
			<div class="panel-heading"><h4>Suppliers
				<button type='button' class='btn btn-xs pull-right' onclick='$("#Suppliers").toggleClass("hidden"); $("#SuppliersCHV").toggleClass("fa-chevron-down");'>
				<span class='fa fa-chevron-up' id='SuppliersCHV'></span></button>
				</h4>
			</div>
			<div class="panel-body" id="Suppliers">
				<div class='table'>
					<table class='table table-bordered'>
					<tr><th>ID</th><th>Name</th><th>Balance</th><th width='100px'><a href='supplierDetails.php' class='btn btn-success btn-sm'><i class='fa fa-plus'></i> Add</a></th></tr>
<?php
	foreach($Suppliers AS $ID => $Rec)
	{
		echo "<tr>";
		echo "<td>" . $ID . "</td>";
		echo "<td id='sup_" . $ID . "'>" . $Rec['suppliername'] . "</td>";
		echo "<td>" . $Rec['supplierbalance'] . "</td>";
		echo "<td><a href='supplierDetails.php?s=" . $ID . "' class='btn btn-success btn-sm'><i class='fa fa-edit'></i> Edit</a></td>";
		echo "</tr>";
	}
?>				
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class='modal fade' id='supplier-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<div class='modal-dialog modal-lg'>
		<form method='POST' action='saveSupplier.php' id='supplierForm'>
		<input type='hidden' name='SupplierID' id='SupplierID' value='0'>
		<div class='modal-content'>
			<div class='modal-header'><strong>Supplier Details<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body'>
				<div class='table'>
					<table class='table table-bordered'>
					<tr><th>Name</th><td colspan='3'><input type='text' name='supname' id='supname' class='validate[required] form-control' value='' maxlength='30'></td></tr>
					<tr><th>Reg Num</th><td><input type='text' name='regnum' id='regnum' class='form-control' value='' maxlength='30'></td>
					<th>Vat Num</th><td><input type='text' name='vatnum' id='vatnum' class='form-control' value='' maxlength='30'></td></tr>
					<tr><th>Email</th><td><input type='text' name='supemail' id='supemail' class='form-control' value='' maxlength='30'></td>
					<th>Tel</th><td><input type='text' name='suptel' id='suptel' class='form-control' value='' maxlength='30'></td></tr>
					<tr><th>Address</th><td colspan='3'><input type='text' name='supaddie' id='supaddie' class='form-control' value='' maxlength='30'></td></tr>
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