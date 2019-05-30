<?php
include("header.inc.php");
$CompArr = getManagingAgents();
?>
<script>
$(document).ready(function()
{
	$("#secForm").validationEngine();
});

function openEditWindow(compid)
{
	$('#CompID').val(compid);
	if(compid != 0)
	{
		var adate = new Date().getTime();
		$.ajax({async: false, type: "POST", url: "ajaxGetMACompany.php", dataType: "json",
			data: "dc=" + adate + "&cid=" + compid,
			success: function (feedback)
			{
				$('#compname').val(feedback.agentname);
			},
			error: function(request, feedback, error)
			{
				alert("Request failed\n" + feedback + "\n" + error);
				return false;
			}
		});
	}
	$("#company-modal").modal("show");
}
</script>
<div class="panel panel-default">
	<div class="panel-heading"><h4>Managing Agents
		<button type='button' class='btn btn-xs pull-right' onclick='$("#CompPanel").toggleClass("hidden"); $("#CompPanelCHV").toggleClass("fa-chevron-down");'>
		<span class='fa fa-chevron-up' id='CompPanelCHV'></span></button></h4>
	</div>
	<div class="panel-body" id='CompPanel'>
		<div class='table'>
			<table class='table table-bordered'>
			<tr><th>Name <button type='button' class='btn btn-success btn-xs pull-right' onclick='openEditWindow(0);'><i class='fa fa-plus'></i> Add Company</button></th></tr>
<?php
foreach($CompArr AS $CompID => $CompRec)
{
	echo "<tr><td>" . $CompRec . "<button type='button' class='btn btn-success btn-xs pull-right' onclick='openEditWindow(" . $CompID . ");'><i class='fa fa-edit'></i> Edit</button></td></tr>";
}
?>
			</table>
		</div>
	</div>
</div>

<div class='modal fade' id='company-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<form method='POST' action='saveMAComp.php' id='secForm'>
	<input type='hidden' name='CompID' id='CompID' value='0'>
	<div class='modal-dialog modal-lg'>
		<div class='modal-content'>
			<div class='modal-header'><strong>Managing Agent<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body' id='schedulebody'>
					<div class='row'>
						<div class='col-md-2'>Company name:</div>
						<div class='col-md-5'><input type='text' name='compname' id='compname' maxlength='100' class='validate[required] form-control' value=''></div>
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


<?php
include("footer.inc.php");
?>