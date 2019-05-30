<?php
include("header.inc.php");
$SystemPrivs = getSystemPrivileges();
$UserPrivs = getAllUserPrivileges();
$Users = getUsers();
?>
<script>
$(document).ready(function()
{
	$("#userForm").validationEngine();
});

function editUser(UserID)
{
	$("#usrpass").prop("disabled", "disabled");
	if(UserID == 0)
	{
		$("#usrpass").prop("disabled", "");
		var passw = getRandomPassword();
		$("#usrpass").val(passw);
	}
	$("input:checkbox[name^='priv']").prop("checked", "");
	$("#UserID").val(UserID);
	$("#fname").val($("#fname_" + UserID).val());
	$("#sname").val($("#sname_" + UserID).val());
	$("#email1").val($("#email_" + UserID).val());
	$("#cell1").val($("#cell_" + UserID).val());
	$("#tel1").val($("#tel_" + UserID).val());
	var stat = $("#status_" + UserID).val();
	$("#userstatus_" + stat).prop("checked", "checked");
	
	$("input:hidden[name^='priv_" + UserID + "_']").each(function ()
	{
		var thepriv = $(this).val();
		$("#priv_" + thepriv).prop("checked", "checked");
	});
	$("#user-modal").modal("show");
}
</script>
<div class='row'>
	<div class='col-md-12'>
		<div class="panel panel-default">
			<div class="panel-heading"><h4>Users
				<button type='button' class='btn btn-xs pull-right' onclick='$("#UsersPanel").toggleClass("hidden"); $("#UsersPanelCHV").toggleClass("fa-chevron-down");'>
				<span class='fa fa-chevron-up' id='UsersPanelCHV'></span></button>
				</h4>
			</div>
			<div class="panel-body" id="UsersPanel">
				<div class='table'>
					<table class='table table-bordered'>
					<tr><th rowspan='2'>Firstname</th><th rowspan='2'>Lastname</th><th rowspan='2'>Email</th><th rowspan='2'>Cell</th><th colspan='<?php echo count($SystemPrivs); ?>'>Privileges</th><th rowspan='2'>Active</th>
					<th width='100px' rowspan='2'><button type='button' class='btn btn-success btn-sm' onclick='editUser(0);'><i class='fa fa-plus'></i> Add</button></th></tr>
					<tr>
<?php
foreach($SystemPrivs AS $PrivID => $PrivName)
{
	echo "<td class='text-center'><small>" . $PrivName . "</small></td>";
}
?>
					</tr>
<?php
	foreach($Users AS $UserID => $UserRec)
	{
		$RowHi = ($UserRec['inactive'] == 1) ? " class='bg-danger'" : "";
		$RowHi = ($UserRec['inactive'] == 2) ? " class='bg-warning'" : $RowHi;
		echo "<tr" . $RowHi . ">";
		echo "<td>" . $UserRec['firstname'];
		echo "<input type='hidden' name='fname_" . $UserID . "' id='fname_" . $UserID . "' value='" . $UserRec['firstname'] . "'></td>\n";
		echo "<td>" . $UserRec['surname'];
		echo "<input type='hidden' name='sname_" . $UserID . "' id='sname_" . $UserID . "' value='" . $UserRec['surname'] . "'></td>\n";
		echo "<td>" . $UserRec['username'];
		echo "<input type='hidden' name='email_" . $UserID . "' id='email_" . $UserID . "' value='" . $UserRec['username'] . "'></td>\n";
		echo "<td>" . $UserRec['cellnumber'];
		echo "<input type='hidden' name='cell_" . $UserID . "' id='cell_" . $UserID . "' value='" . $UserRec['cellnumber'] . "'>\n";
		echo "<input type='hidden' name='tel_" . $UserID . "' id='tel_" . $UserID . "' value='" . $UserRec['telnumber'] . "'></td>\n";
		foreach($SystemPrivs AS $PrivID => $PrivName)
		{
			if(isset($UserPrivs[$UserID][$PrivID]))
			{
				echo "<th class='text-success text-center'><i class='fa fa-check'></i>";
				echo "<input type='hidden' name='priv_" . $UserID . "_" . $PrivID . "' id='priv_" . $UserID . "_" . $PrivID . "' value='" . $PrivID . "'>";
				echo "</th>\n";
			}
			else
			{
				echo "<th class='text-danger text-center'><i class='fa fa-times'></i>";
				echo "<input type='hidden' name='priv_" . $UserID . "_" . $PrivID . "' id='priv_" . $UserID . "_" . $PrivID . "' value='0'>";
				echo "</th>\n";
			}
		}
		if($UserRec['inactive'] == 0)
			echo "<th class='text-success text-center'><i class='fa fa-check'></i>\n";
		elseif($UserRec['inactive'] == 2)
			echo "<th class='text-warning text-center'><i class='fa fa-minus'></i> On leave\n";
		else
			echo "<th class='text-danger text-center'><i class='fa fa-times'></i>\n";
		echo "<input type='hidden' name='status_" . $UserID . "' id='status_" . $UserID . "' value='" . $UserRec['inactive'] . "'></th>";
		
		echo "<td><button type='button' class='btn btn-success btn-sm' onclick='editUser(" . $UserID . ");'><i class='fa fa-edit'></i> Edit</button></td>\n";
		echo "</tr>\n";
	}
?>				
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class='modal fade' id='user-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<form method='POST' action='saveUser.php' id='userForm'>
	<input type='hidden' name='UserID' id='UserID' value=''>
	<div class='modal-dialog modal-lg'>
		<div class='modal-content'>
			<div class='modal-header'><strong>User Details<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body'>
				<div class='row top5'><div class='col-md-2'>Firstname:</div><div class='col-md-4'><input type='text' name='fname' id='fname' class='validate[required] form-control' maxlength='100'></div>
					<div class='col-md-2'>Surname:</div><div class='col-md-4'><input type='text' name='sname' id='sname' class='validate[required] form-control' maxlength='100'></div></div>
				<div class='row top5'><div class='col-md-2'>Email:</div><div class='col-md-4'><input type='text' name='email1' id='email1' class='validate[required, custom[email]] form-control' maxlength='100'></div>
					<div class='col-md-2'>Cell:</div><div class='col-md-4'><input type='text' name='cell1' id='cell1' class='validate[required, custom[phone]] form-control' maxlength='30'></div></div>
				<div class='row top5'><div class='col-md-2'>Tel:</div><div class='col-md-4'><input type='text' name='tel1' id='tel1' class='validate[custom[phone]] form-control' maxlength='30'></div>
					<div class='col-md-2'>Password:</div><div class='col-md-4'><input type='text' name='usrpass' id='usrpass' class='form-control' maxlength='30' disabled='disabled'></div>
				</div>
				<div class='row top5'><div class='col-md-2'>Status:</div><div class='col-md-2'>
					<input type='radio' id='userstatus_0' name='userstatus' value='0' checked='checked'><label for='userstatus_0' class='text-success'>Active <i class='fa fa-check'></i></label><br>
					<input type='radio' id='userstatus_2' name='userstatus' value='2'><label for='userstatus_2' class='text-warning'>On leave <i class='fa fa-minus'></i></label><br>
					<input type='radio' id='userstatus_1' name='userstatus' value='1'><label for='userstatus_1' class='text-danger'>Inctive <i class='fa fa-times'></i></label><br>
				</div></div>
				<div class='row top5'><div class='col-md-12'>
					<div class='table'>
					<table class='table table-bordered'>
					<tr><th>&nbsp;</th><th>Privilege</th></tr>
<?php
foreach($SystemPrivs AS $PrivID => $PrivName)
{
	echo "<tr><td><input type='checkbox' name='priv[]' id='priv_" . $PrivID . "' value='" . $PrivID . "'></td>";
	echo "<td><label for='priv_" . $PrivID . "'>" . $PrivName . "</label></td></tr>\n";
}
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