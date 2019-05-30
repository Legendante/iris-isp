<?php
include("header.inc.php");
$RefinedStatus = (isset($_GET['s'])) ? pebkac($_GET['s']) : '';
$ComplextTypes = getComplexTypes();
$SiteStatusses = getSiteStatusses();
$ComplexGroups = getAllComplexGroups();
$ComplexStepProgress = getAllComplexesMaxSalesOperationsSteps();
$SalesOpsSteps = getSalesOperationsWorkflow();
$Complexes = getAllComplexes();
$Meetings = getIncompleteMeetings();
$MeetingTypes = getMeetingTypes();
$MyMeetings = getMeetingsForAttendee($_SESSION['userid']);
$SalesOpsCount = array();
$SalesStepComplexes = array();
$SOComplexes = array();
$HiLightArr = array();
foreach($SalesOpsSteps AS $StepID => $StepRec)
{
	$SalesOpsCount[$StepID] = 0;
	$SalesStepComplexes[$StepID] = array();
}

foreach($ComplexStepProgress AS $ComplexID => $StepRec)
{
	$SalesOpsCount[$StepRec['stepid']]++;
	$SalesStepComplexes[$StepRec['stepid']][] = $ComplexID;
	$SOComplexes[$ComplexID] = $ComplexID;
	if($StepRec['datediff'] > 4)
		$HiLightArr[$StepRec['stepid']] = ' class="bg-warning"';
	if($StepRec['datediff'] > 7)
		$HiLightArr[$StepRec['stepid']] = ' class="bg-danger"';
}
$DocList = getComplexDocumentsByType('2,3', implode(",", $SOComplexes));
$DocListTypes = array();
$cnt = 0;
foreach($DocList AS $DocID => $DocRec)
{
	$DocListTypes[$DocRec['doctype']][] = $DocRec;
}
// print_r($DocListTypes);

$SysUsers = getUsers();
$Agents = getAgents();
$Vendors = getVendors();
$Vendors[0] = '-';
$MyComplexes = getAgentComplexes($_SESSION['userid'], $RefinedStatus);
$MyComplexCount = getAgentComplexCount($_SESSION['userid']);
$CompCount = array();
$CompCountTotal = 0;
foreach($SiteStatusses AS $StatusID => $StatusRec)
{
	$StatCount = (isset($MyComplexCount[$StatusID])) ? $MyComplexCount[$StatusID] : 0;
	$CompCountTotal += $StatCount;
	if($StatusRec['parentid'] == 0)
	{
		if(!isset($CompCount[$StatusID]))
			$CompCount[$StatusID] = 0;
		$CompCount[$StatusID] += $StatCount;
	}
	else
	{
		if(!isset($CompCount[$StatusRec['parentid']]))
			$CompCount[$StatusRec['parentid']] = 0;
		$CompCount[$StatusRec['parentid']] += $StatCount;
	}
}
$SiteStatusses = array_reverse($SiteStatusses, true);
$StatusCrumb = '<div class="row"><div class="col-md-12 text-center"><div class="btn-group btn-breadcrumb">';
if($RefinedStatus == '')
	$StatusCrumb .= '<a href="dashboard.php" class="btn btn-success"><span class="badge">' . $CompCountTotal . '</span> <i class="fa fa-home"></i></a>';
else
	$StatusCrumb .= '<a href="dashboard.php" class="btn btn-default"><span class="badge">' . $CompCountTotal . '</span> <i class="fa fa-home"></i></a>';
foreach($SiteStatusses AS $StatusID => $StatusRec)
{
	if($StatusRec['parentid'] == 0)
	{
		$StatCount = (isset($CompCount[$StatusID])) ? $CompCount[$StatusID] : 0;
		if($RefinedStatus == $StatusID)
			$StatusCrumb .= '<a href="dashboard.php?s=' . $StatusID . '" class="btn btn-success">' . $StatusRec['statusname'] . ' <span class="badge">' . $StatCount . '</span></a>';
		else
			$StatusCrumb .= '<a href="dashboard.php?s=' . $StatusID . '" class="btn btn-default">' . $StatusRec['statusname'] . ' <span class="badge">' . $StatCount . '</span></a>';
	}
}
$StatusCrumb .= '</div></div></div>';
?>
<style>
.nav-pills li 
{
	border: 1px solid #CCCCCC;
	border-bottom: 0;
}
</style>
<script>
$(document).ready(function()
{
	$("#complexForm").validationEngine();
});

function getComplexShortCode()
{
	var shortval = $('#complexcode').val();
	if(shortval == '')
	{
		var adate = new Date().getTime();
		var compname = $('#complexname').val();
		$.ajax({async: false, type: "POST", url: "ajaxGetComplexShortCode.php", dataType: "html",
			data: "dc=" + adate + "&cname=" + compname,
			success: function (feedback)
			{
				$('#complexcode').val(feedback);
			},
			error: function(request, feedback, error)
			{
				alert("Request failed\n" + feedback + "\n" + error);
				return false;
			}
		});
	}
}

function openMeetingModal(meetingid, meetingtype)
{
	$('#meetstartday').val('');
	$('#meetstarthour').val('07');
	$('#meetstartmin').val('00');
	$('#meetendhour').val('08');
	$('#meetendmin').val('00');
	$('#meettype').val('1');
	$('input[name^=meetattend]').prop('checked', '');
	if(meetingid != 0)
	{
		// meetattend[]
		var adate = new Date().getTime();
		$.ajax({async: false, type: "POST", url: "ajaxGetMeetingDetails.php", dataType: "json",
			data: "dc=" + adate + "&mid=" + meetingid,
			success: function (feedback)
			{
				$('#MeetingID').val(meetingid);
				$('#meetstartday').val(feedback.meetstartday);
				$('#meetstarthour').val(feedback.meetstarthour);
				$('#meetstartmin').val(feedback.meetstartmin);
				$('#meetendhour').val(feedback.meetendhour);
				$('#meetendmin').val(feedback.meetendmin);
				$('#meettype').val(feedback.meettype);
				$.each(feedback.attendees,function(i,obj)
				{
					$('#meetusr_' + i).prop('checked', 'checked');
				});
			},
			error: function(request, feedback, error)
			{
				alert("Request failed\n" + feedback + "\n" + error);
				return false;
			}
		});
	}
	else
	{
		$('#meettype').val(meetingtype);
		$('#meetusr_<?php echo $_SESSION['userid']; ?>').prop('checked', 'checked');
	}
	$("#meeting-modal").modal("show");
}


function openMeetComplete(meetingid)
{
	$('#CompMeetingID').val(meetingid);
	$("#completemeet-modal").modal("show");
}
</script>
<div class="panel panel-default">
	<div class="panel-heading"><h4>Meetings
		<button type='button' class='btn btn-xs pull-right' onclick='$("#MeetingPanel").toggleClass("hidden"); $("#MeetingPanelCHV").toggleClass("fa-chevron-down");'>
		<span class='fa fa-chevron-up' id='MeetingPanelCHV'></span></button></h4>
	</div>
	<div class="panel-body" id='MeetingPanel'>
	<table class='table table-bordered'>
	<tr><th>Complex</th><th>Meeting Type</th><th>Start Date</th><th>Start Time</th><th>End Time</th><th>Created By</th><th></th></tr>
<?php
foreach($Meetings AS $MeetingID => $MeetingRec)
{
	if(isset($MyMeetings[$MeetingID]))
	{
		$StartDate = substr($MeetingRec['starttime'], 0, 10);
		$StartHr = substr($MeetingRec['starttime'], 11, 2);
		$StartMin = substr($MeetingRec['starttime'], 14, 2);
		$EndHr = substr($MeetingRec['endtime'], 11, 2);
		$EndMin = substr($MeetingRec['endtime'], 14, 2);
		echo "<tr>";
		echo "<td><a href='complex.php?cid=" . $MeetingRec['complexid'] . "'>" . $Complexes[$MeetingRec['complexid']]['complexname'] . "</a></td>";
		echo "<td>" . $MeetingTypes[$MeetingRec['meetingtypeid']] . "</td>";
		echo "<td>" . date("l d M Y", strtotime($StartDate)) . "</td>";
		echo "<td>" . $StartHr . ":" . $StartMin . "</td>";
		echo "<td>" . $EndHr . ":" . $EndMin . "</td>";
		echo "<td>" . $SysUsers[$MeetingRec['setupuser']]['firstname'] . " " . $SysUsers[$MeetingRec['setupuser']]['surname'] . "</td>";
		echo "<td>";
		echo "<a href='#' onclick='openMeetingModal(" . $MeetingID . ", 0)'><i class='fa fa-pencil'></i> Edit</a> ";
		echo "<button type='button' class='btn btn-default btn-xs' onclick='openMeetComplete(" . $MeetingID . ");'>Complete</button>";
		echo "</td>";
		echo "</tr>";
	}
}
?>
	</table>
	</div>
</div>
<div class="panel panel-default">
	<div class="panel-heading"><h4>Lead to Build Workflow <small>(<?php echo count($SOComplexes); ?> complexes)</small>
	
		<button type='button' class='btn btn-primary btn-xs' data-toggle="modal" data-target="#sodocs-modal" style='margin-left: 10px;'><i class='fa fa-file'></i> Documents</button>
		<a href='excelSOWorkflow.php' class='btn btn-primary btn-xs' style='margin-left: 10px;'><i class='fa fa-file-excel-o'></i> S/O Summary Report</a>
	
		<button type='button' class='btn btn-xs pull-right' onclick='$("#SOWFPanel").toggleClass("hidden"); $("#SOWFPanelCHV").toggleClass("fa-chevron-down");'>
		<span class='fa fa-chevron-up' id='SOWFPanelCHV'></span></button></h4>
	</div>
	<div class="panel-body" id='SOWFPanel'>
			<ul  class="nav nav-pills">
<?php
$Active = 'class="active"';
foreach($SalesOpsSteps AS $StepID => $StepRec)
{
	$HiLite = '';
	
	if(isset($HiLightArr[$StepID]))
		$HiLite = $HiLightArr[$StepID];
	echo "<li " . $Active . "" . $HiLite . ">";
	if($SalesOpsCount[$StepID] == 0)
		echo "<a style='color: #888888;' href='#'>" . $StepRec['stepname'] . " (" . $SalesOpsCount[$StepID] . ")</a>";
	else
		echo "<a href='#SOStep_" . $StepID . "' data-toggle='tab'>" . $StepRec['stepname'] . " (" . $SalesOpsCount[$StepID] . ")</a>";
	echo "</li>\n";
	$Active = '';
}
?>
			</ul>
			<div class="tab-content">
<?php
$Active = ' active';
foreach($SalesOpsSteps AS $StepID => $StepRec)
{
	echo "<div class='tab-pane" . $Active . "' id='SOStep_" . $StepID . "'>";
	echo "<table class='table table-bordered'>";
	echo "<tr><th>ID</th><th>Complex</th><th>Date completed</th><th>Age</th></tr>\n";
	foreach($SalesStepComplexes[$StepID] AS $StepComplexID)
	{
		$HiLite = '';
		if($ComplexStepProgress[$StepComplexID]['datediff'] > 4)
			$HiLite = ' class="bg-warning"';
		if($ComplexStepProgress[$StepComplexID]['datediff'] > 7)
			$HiLite = ' class="bg-danger"';
		echo "<tr" . $HiLite . ">";
		echo "<td><a href='complex.php?cid=" . $StepComplexID . "'>" . $StepComplexID . "</a></td>";
		echo "<td><a href='complex.php?cid=" . $StepComplexID . "'>" . $Complexes[$StepComplexID]['complexname'] . "</a></td><td>" . substr($ComplexStepProgress[$StepComplexID]['datecompleted'], 0, 16) . "</td>";
		echo "<td>" . $ComplexStepProgress[$StepComplexID]['datediff'] . "</td>";
		echo "<td>";
		echo "<div class='btn-group' role='group'>";
		echo "<button class='btn btn-default dropdown-toggle btn-xs' type='button' id='dropdownMenu" . $StepComplexID . "' data-toggle='dropdown' aria-haspopup='true' aria-expanded='true'>Change Status <span class='caret'></span></button>";
		echo "<ul class='dropdown-menu' aria-labelledby='dropdownMenu" . $StepComplexID . "'>\n";
		foreach($SalesOpsSteps AS $DDStepID => $DDStepRec)
		{
			if($DDStepID == $StepID)
				echo "<li class='active'><a href='changeSOStatus.php?cid=" . $StepComplexID . "&sid=" . $DDStepID . "'>" . $DDStepRec['stepname'] . "</a></li>\n";
			else
				echo "<li><a href='changeSOStatus.php?cid=" . $StepComplexID . "&sid=" . $DDStepID . "'>" . $DDStepRec['stepname'] . "</a></li>\n";
		}
		echo "<li role='separator' class='divider'></li>";
		echo "<li class='bg-danger'><a href='changeSOStatus.php?cid=" . $StepComplexID . "&sid=" . $StepID . "&cancel=1'><strong>Cancel</strong></a></li>\n";
		echo "</ul>";
		echo "</div>\n";
		echo "</td>";
		echo "</tr>\n";
	}
	echo "</table>";
	echo "</div>\n";
	$Active = '';
}	
?>	
			</div>
	</div>
</div>
<div class="panel panel-default">
	<div class="panel-heading"><h4>Complexes
		<button type='button' class='btn btn-xs pull-right' onclick='$("#CompPanel").toggleClass("hidden"); $("#CompPanelCHV").toggleClass("fa-chevron-down");'>
		<span class='fa fa-chevron-up' id='CompPanelCHV'></span></button></h4>
	</div>
	<div class="panel-body" id='CompPanel'>
		<div class='table'>
			<table class='table table-bordered'>
			<tr><td colspan='8'><?php echo $StatusCrumb; ?></td></tr>
			<tr class='bg-info text-info'><th>Complex Name</th><th>Code</th><th>Num Units</th><th>Type</th><th>Status</th><th>Vendor</th><th>Residents</th><th>
				<button type='button' class='btn btn-primary btn-xs' data-toggle="modal" data-target="#newcomplex-modal"><i class='fa fa-plus'></i> Add Complex</button>
			</th></tr>
<?php
foreach($MyComplexes AS $ComplexID => $CompRec)
{
	$HiLite = '';
	if(in_array($ComplexID, $SOComplexes))
		$HiLite = ' class="bg-info"';
	echo "<tr" . $HiLite . ">";
	echo "<td>" . $CompRec['complexname'] . "</td>";
	echo "<td>" . $CompRec['complexcode'] . "</td>";
	echo "<td>" . $CompRec['numunits'] . "</td>";
	echo "<td>" . $ComplextTypes[$CompRec['complextype']] . "</td>";
	$Status = $SiteStatusses[$CompRec['statusid']]['statusname'];
	if($SiteStatusses[$CompRec['statusid']]['parentid'] != 0)
	{
		$ParentStatus = $SiteStatusses[$SiteStatusses[$CompRec['statusid']]['parentid']]['statusname'];
		$Status = $ParentStatus . " <i class='fa fa-angle-right'></i> " . $Status;
	}
	echo "<td>" . $Status . "</td>";
	echo "<td>" . $Vendors[$CompRec['vendorid']] . "</td>";
	echo "<td><a href='complexresidents.php?cid=" . $ComplexID . "' class='btn btn-primary btn-xs'><i class='fa fa-users'></i> Residents</a></td>";
	echo "<td><a href='complex.php?cid=" . $ComplexID . "' class='btn btn-info btn-xs'><i class='fa fa-eye'></i> View</a></td>";
	echo "</tr>\n";
}
?>
			</table>
		</div>
	</div>
</div>
<div class='modal fade' id='newcomplex-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<form method='POST' action='saveNewComplex.php' id='complexForm'>
	<div class='modal-dialog modal-lg'>
		<div class='modal-content'>
			<div class='modal-header'><strong>New Complex<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body' id='schedulebody'>
					<div class='row'>
						<div class='col-md-2'>Complex:</div>
						<div class='col-md-5'><input type='text' name='complexname' id='complexname' maxlength='100' class='validate[required] form-control' value='' onchange='getComplexShortCode();'></div>
						<div class='col-md-2'>Code:</div>
						<div class='col-md-3'><input type='text' name='complexcode' id='complexcode' maxlength='10' class='form-control' value=''></div>
					</div>
					<div class='row top5'>
						<div class='col-md-2'># Units:</div><div class='col-md-2'><input type='text' name='numunits' id='numunits' maxlength='100' class='validate[required, min[1], custom[integer]] form-control' value=''></div>
						<div class='col-md-2'>Vendor:</div><div class='col-md-3'>
						<select name='vendorid' class="form-control">
						<option value=''>-- Select Vendor --</option>
<?php 
foreach($Vendors AS $VendorID => $VendorRec)
{
	echo "<option value='" . $VendorID. "'>" . $VendorRec . "</option>";
}
?>	
								</select>
							</div>
					</div>
			<div class='row top5'>
				<div class='col-md-2'>Agent:</div><div class='col-md-4'>
					<select name='agentid' class="form-control">
					<option value=''>-- Select Agent --</option>
<?php 
$SelList = getUserOptionList($_SESSION['userid']);
echo $SelList;
?>	
					</select>
				</div>
				<div class='col-md-2'>Secondary Agent:</div><div class='col-md-4'>
					<select name='secagentid' class="form-control">
					<option value=''>-- Select Agent --</option>
<?php
$SelList = getUserOptionList();
echo $SelList;
?>	
					</select>
				</div>
			</div>
			<div class='row top5'>
				<div class='col-md-2'>Status:</div><div class='col-md-4'>
				
					<select name='complexstatus' class="validate[required] form-control">
					<option value=''>-- Select Status --</option>
<?php 
foreach($SiteStatusses AS $StatusID => $StatusRec)
{
	echo "<option value='" . $StatusID. "'>" . $StatusRec['statusname'] . "</option>";
}
?>	
					</select>
				
				</div>
				<div class='col-md-2'>Type:</div><div class='col-md-4'>
				
					<select name='complextype' class="validate[required] form-control">
					<option value=''>-- Select Type --</option>
<?php 
foreach($ComplextTypes AS $TypeID => $TypeRec)
{
	echo "<option value='" . $TypeID. "'>" . $TypeRec . "</option>";
}
?>	
					</select>
				
				</div>
			</div>
			<div class='row top5'>
				<div class='col-md-2'>Group:</div><div class='col-md-4'>
				
					<select name='complexgroup' class="form-control">
					<option value=''>-- Select Group --</option>
<?php 
foreach($ComplexGroups AS $GroupID => $GroupName)
{
	echo "<option value='" . $GroupID. "'>" . $GroupName . "</option>";
}
?>	
					</select>
				
				</div>
			</div>
			
			<div class='row'>
				<div class='col-md-6'>
					<div class='row top5'><div class='col-md-3'>Address:</div><div class='col-md-9'><input type='text' name='address1' id='address1' class='form-control' value=''></div></div>
					<div class='row top5'><div class='col-md-3'></div><div class='col-md-9'><input type='text' name='address2' id='address2' class='form-control' value=''></div></div>
					<div class='row top5'><div class='col-md-3'></div><div class='col-md-9'><input type='text' name='address3' id='address3' class='form-control' value=''></div></div>
					<div class='row top5'><div class='col-md-3'></div><div class='col-md-9'><input type='text' name='address4' id='address4' class='form-control' value=''></div></div>
					<div class='row top5'><div class='col-md-3'></div><div class='col-md-9'><input type='text' name='address5' id='address5' class='form-control' value=''></div></div>
				</div>
				<div class='col-md-6'>
					<div class='row top5'><div class='col-md-3'>Precinct:</div><div class='col-md-9'>
					<select name='precinctid' class="form-control">
					<option value=''>-- Select --</option>
<?php
foreach($Precincts AS $Key => $Val)
{
	echo "<option value='" . $Key . "'";
	echo ">" . $Val['precinctname'] . "</option>";
}
?>
					</select>
					</div></div>
					<div class='row top5'><div class='col-md-3'>Suburb:</div><div class='col-md-9'>
					<select name='suburbid' class="form-control">
					<option value=''>-- Select --</option>
<?php
foreach($Suburbs AS $Key => $Val)
{
	echo "<option value='" . $Key . "'";
	echo ">" . $Val['suburbname'] . "</option>";
}
?>
					</select>
					</div></div>
					<div class='row top5'><div class='col-md-3'>Area:</div><div class='col-md-9'>
					<select name='areaid' class="form-control">
					<option value=''>-- Select --</option>
<?php
foreach($Areas AS $Key => $Val)
{
	echo "<option value='" . $Key . "'";
	echo ">" . $Val['areaname'] . "</option>";
}
?>
					</select>
					</div></div>
					<div class='row top5'><div class='col-md-3'>City:</div><div class='col-md-9'>
					<select name='cityid' class="form-control">
					<option value=''>-- Select --</option>
<?php
foreach($Cities AS $Key => $Val)
{
	echo "<option value='" . $Key . "'";
	echo ">" . $Val['cityname'] . "</option>";
}
?>
					</select>
					</div></div>
					<div class='row top5'><div class='col-md-3'>Province:</div><div class='col-md-9'>
					<select name='provinceid' class="form-control">
					<option value=''>-- Select --</option>
<?php
foreach($Provinces AS $Key => $Val)
{
	echo "<option value='" . $Key . "'";
	echo ">" . $Val['provincename'] . "</option>";
}
?>
					</select>
					</div></div>
					<div class='row top5'><div class='col-md-3'>Country:</div><div class='col-md-9'>
					<select name='countryid' class="form-control">
					<option value=''>-- Select --</option>
<?php
foreach($Countries AS $Key => $Val)
{
	echo "<option value='" . $Key . "'";
	echo ">" . $Val['countryname'] . "</option>";
}
?>
					</select>
					</div></div>
				</div>
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

<div class='modal fade' id='meeting-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<div class='modal-dialog modal-lg'>
		<form method='POST' action='saveComplexMeeting.php' id='meetingForm'>
		<input type='hidden' name='fromDash' id='fromDash' value='1'>
		<input type='hidden' name='MeetingID' id='MeetingID' value='0'>
		<div class='modal-content'>
			<div class='modal-header'><strong>Set Meeting<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body'>
				<div class='table'>
					<table class='table table-bordered'>
					<tr><td>Start:</td><td>
						<div class="form-inline">
							<div class="form-group">
							<input type='text' name='meetstartday' id='meetstartday' maxlength='10' class='validate[custom[date]] form-control' value=''>
							</div>
							<div class="form-group">
							<select name='meetstarthour' id='meetstarthour' class='form-control'>
								<option value='07'>7</option>
								<option value='08'>8</option>
								<option value='09'>9</option>
								<option value='10'>10</option>
								<option value='11'>11</option>
								<option value='12'>12</option>
								<option value='13'>13</option>
								<option value='14'>14</option>
								<option value='15'>15</option>
								<option value='16'>16</option>
								<option value='17'>17</option>
								<option value='18'>18</option>
								<option value='19'>19</option>
								<option value='20'>20</option>
							</select>:
							</div>
							<div class="form-group">
							<select name='meetstartmin' id='meetstartmin' class='form-control'>
								<option value='00'>00</option>
								<option value='15'>15</option>
								<option value='30'>30</option>
								<option value='45'>45</option>
							</select>
							</div>
						</div>
					</td><td>End:</td><td>
						<div class="form-inline">
							<div class="form-group">
							<select name='meetendhour' id='meetendhour' class='form-control'>
								<option value='07'>7</option>
								<option value='08'>8</option>
								<option value='09'>9</option>
								<option value='10'>10</option>
								<option value='11'>11</option>
								<option value='12'>12</option>
								<option value='13'>13</option>
								<option value='14'>14</option>
								<option value='15'>15</option>
								<option value='16'>16</option>
								<option value='17'>17</option>
								<option value='18'>18</option>
								<option value='19'>19</option>
								<option value='20'>20</option>
							</select>:
							</div>
							<div class="form-group">
							<select name='meetendmin' id='meetendmin' class='form-control'>
								<option value='00'>00</option>
								<option value='15'>15</option>
								<option value='30'>30</option>
								<option value='45'>45</option>
							</select>
							</div>
						</div>
					</td></tr>
					<tr><td>Meeting Type:</td><td>
					<select name='meettype' id='meettype' class='form-control'>
<?php
foreach($MeetingTypes AS $TypeID => $Typename)
{
	echo "<option value='" . $TypeID . "'>" . $Typename . "</option>";
}
?>
						</select>
					</td></tr>
					<tr><td>Attendees:</td><td colspan='3'>
						<table class='table table-bordered table-condensed'>
<?php
$cnt = 0;
foreach($SysUsers AS $UsrID => $UsrRec)
{
	if($cnt == 0)
		echo "<tr>";
	echo "<td><input type='checkbox' name='meetattend[]' value='" . $UsrID . "' id='meetusr_" . $UsrID . "'> <label for='meetusr_" . $UsrID . "'>" . $UsrRec['firstname'] . " " . $UsrRec['surname'] . "</label></td>";
	$cnt++;
	if($cnt == 3)
	{
		echo "</tr>";
		$cnt = 0;
	}
}
?>
						</table>
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

<div class='modal fade' id='completemeet-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<div class='modal-dialog modal-lg'>
		<form method='POST' action='setMeetingComplete.php' id='mapForm'>
		<input type='hidden' name='CompMeetingID' id='CompMeetingID' value=''>
		<div class='modal-content'>
			<div class='modal-header'><strong>Meeting Feedback<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body'>
				<div class='row top5'><div class='col-md-12'><textarea id='meetingcomment' name='meetingcomment' class='form-control'></textarea></div></div>
			</div>	
			<div class='modal-footer'>
				<button type='button' class='btn btn-default' data-dismiss='modal'><span class='fa fa-times'></span> Close</button>
				<button type='submit' class='btn btn-success pull-right'><i class='fa fa-save'></i> Save</button>
			</div>
		</div>
		</form>
	</div>
</div>


<div class='modal fade' id='sodocs-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<div class='modal-dialog modal-lg'>
		<div class='modal-content'>
			<div class='modal-header'><strong>S/O Documents Received<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body'>
				<table class='table table-bordered'>
<?php
foreach($DocListTypes AS $DocType => $DocTypeRec)
{
	$DocTypeHeading = '';
	switch($DocType)
	{
		case 1:
			$DocTypeHeading = 'Letter of engagement';
			break;
		case 2:
			$DocTypeHeading = 'MOU';
			break;
		case 3:
			$DocTypeHeading = 'Site Plans';
			break;
	}
	foreach($DocTypeRec AS $cnt => $DocRec)
	{
		if($cnt == 0)
		{
			echo "<tr><th colspan='4' class='bg-primary'>" . $DocTypeHeading . "</th></tr>";
			echo "<tr><th>Complex</th><th>Document</th><th>Uploaded</th><th>By</th></tr>";
		}
		echo "<tr><td>" . $Complexes[$DocRec['complexid']]['complexname'] . "</td><td><a href='getFile.php?fid=" . $DocRec['documentid'] . "'>" . $DocRec['filename'] . "</a></td><td>" . $DocRec['uploadtime'] . "</td>";
		$DocUser = (isset($Agents[$DocRec['userid']])) ? $Agents[$DocRec['userid']]['firstname'] . " " . $Agents[$DocRec['userid']]['surname'] : '-';
		echo "<td>" . $DocUser . "</td></tr>";
	}
}
?>
				</table>
			</div>	
			<div class='modal-footer'>
				<button type='button' class='btn btn-default' data-dismiss='modal'><span class='fa fa-times'></span> Close</button>
			</div>
		</div>
	</div>
</div>


<?php
include("footer.inc.php");
?>