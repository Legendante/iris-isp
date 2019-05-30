<?php
include("header.inc.php");
$Accounts = getFundAccounts();
?>
<script>
$(document).ready(function()
{
	$("#accountForm").validationEngine();
});

function editAccount(AccountID)
{
	$('#AccountID').val(AccountID);
	$('#accountname').val($('#acc_' + AccountID).html());
	$('#account-modal').modal("show");
}
</script>
<div class='row'>
	<div class='col-md-12'>
		<div class="panel panel-default">
			<div class="panel-heading"><h4>Fund Accounts
				<button type='button' class='btn btn-xs pull-right' onclick='$("#FundAccounts").toggleClass("hidden"); $("#FundAccountsCHV").toggleClass("fa-chevron-down");'>
				<span class='fa fa-chevron-up' id='FundAccountsCHV'></span></button>
				</h4>
			</div>
			<div class="panel-body" id="FundAccounts">
				<div class='table'>
					<table class='table table-bordered'>
					<tr><th>ID</th><th>Name</th><th>Balance</th><th width='100px'><button type='button' class='btn btn-success btn-sm' onclick='editAccount(0);'><i class='fa fa-plus'></i> Add</button></th></tr>
<?php
	foreach($Accounts AS $ID => $Rec)
	{
		echo "<tr>";
		echo "<td>" . $ID . "</td>";
		echo "<td id='acc_" . $ID . "'>" . $Rec['accountname'] . "</td>";
		echo "<td>" . $Rec['accountbalance'] . "</td>";
		echo "<td><button type='button' class='btn btn-success btn-sm' onclick='editAccount(" . $ID . ");'><i class='fa fa-edit'></i> Edit</button></td>";
		echo "</tr>";
	}
?>				
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class='modal fade' id='account-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<div class='modal-dialog modal-lg'>
		<form method='POST' action='saveFundAccount.php' id='accountForm'>
		<input type='hidden' name='AccountID' id='AccountID' value='0'>
		<div class='modal-content'>
			<div class='modal-header'><strong>Fund Account Details<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body'>
				<div class='table'>
					<table class='table table-bordered'>
					<tr><th>Name</th><td><input type='text' name='accountname' id='accountname' class='validate[required] form-control' value='' maxlength='30'></td></tr>
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