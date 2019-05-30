<?php
include("header.inc.php");
$ComplexID = pebkac($_GET['cid'], 5);
$ComplexRec = getComplexByID($ComplexID);
$ImportRecord = array();
// $ImportRecord = getImportRecord($ComplexID);
$ComplextTypes = getComplexTypes();
$SiteStatusses = getSiteStatusses();
$ComplexGroups = getAllComplexGroups();
$SiteStatusses[0]['statusname'] = 'Unknown';
$SiteStatusses[0]['parentid'] = 0;
$SysUsers = getUsers();
$Agents = getAgents();
$Agents[0] = array("firstname" => "Unknown", "surname" => "User");
$HOACustomer = getCustomerByID($ComplexRec['customerid']);
$Precincts = getPrecincts();
$Suburbs = getSuburbs();
$Areas = getAreas();
$Cities = getCities();
$Provinces = getProvinces();
$Countries = getCountries();
$Vendors = getVendors();
$ManagingAgents = getManagingAgents();
$SecurityAgents = getSecurityCompanies();
$CompUnitCount = getComplexUnitMapCount($ComplexID);
$SOProcess = getSalesOperationsWorkflow();
$ComplexSOSteps = getComplexSalesOperationsSteps($ComplexID);
$ComplexNotes = getComplexNotes($ComplexID, 5);
$BCContacts = getBodyCorpContacts($ComplexID);
$CompFiles = getComplexFiles($ComplexID);
$MeetingTypes = getMeetingTypes();
$ComplexMeetings = getIncompleteMeetings($ComplexID);
$StatusCrumb = '<div class="row"><div class="col-md-12 text-center"><div class="btn-group btn-breadcrumb">';
$After = 0;
$CompStat = $SiteStatusses[$ComplexRec['statusid']];
$CompStatus = ($SiteStatusses[$ComplexRec['statusid']]['parentid'] == 0) ? $ComplexRec['statusid'] : $SiteStatusses[$ComplexRec['statusid']]['parentid'];
foreach($SiteStatusses AS $StatusID => $StatusRec)
{
	if($CompStatus == $StatusID)
	{
		$StatusName = $StatusRec['statusname'];
		if($SiteStatusses[$ComplexRec['statusid']]['parentid'] == $StatusID)
			$StatusName = "<small>" . $StatusName . "</small> <i class='fa fa-angle-right'></i> " . $SiteStatusses[$ComplexRec['statusid']]['statusname'];
		$StatusCrumb .= '<button type="button" class="btn btn-success">' . $StatusName . '</button>';
		$After = 1;
	}
	elseif(($After == 0) && ($StatusRec['parentid'] == 0))
		$StatusCrumb .= '<button type="button" class="btn btn-default">' . $StatusRec['statusname'] . '</button>';
	elseif(($After == 1) && ($StatusRec['parentid'] == 0))
		$StatusCrumb .= '<button type="button" class="btn btn-warning">' . $StatusRec['statusname'] . '</button>';
}
$StatusCrumb .= '</div></div></div>';
$KickoffDate = ((substr($ComplexRec['kickoff'], 0, 10) != '0000-00-00') && ($ComplexRec['kickoff'] != '')) ? substr($ComplexRec['kickoff'], 0, 10) : '';
$LandingPage = ($ComplexRec['subdomain'] != '') ? $ComplexRec['subdomain'] : '-';
// print_r($ComplexRec);
?>
<script>
$(document).ready(function()
{
	$("#complexForm").validationEngine();
	$("#commentForm").validationEngine();
	$("#mapForm").validationEngine();
	$("#kickoff").datepicker({"dateFormat": "yy-mm-dd"});
	$("#meetstartday").datepicker({"dateFormat": "yy-mm-dd"});
});

function loadBCContact(contactid)
{
	$("#BCCContactID").val(contactid);
	$("#contactname").val($("#bccont_fname_" + contactid).html());
	$("#contactsurname").val($("#bccont_sname_" + contactid).html());
	$("#contactemail").val($("#bccont_email_" + contactid).html());
	$("#contactcell").val($("#bccont_cell_" + contactid).html());
	$("#contacttel").val($("#bccont_tel_" + contactid).html());
	$("#designation").val($("#bccont_desig_" + contactid).html());
	$("#contactunit").val($("#bccont_unit_" + contactid).html());
	$("#bccontacts-modal").modal("show");
}
	
function openUnitMap()
{
	var adate = new Date().getTime();
	$.ajax({async: false, type: "POST", url: "ajaxGetComplexUnitMap.php", dataType: "html",
		data: "dc=" + adate + "&cid=<?php echo $ComplexID; ?>",
		success: function (feedback)
		{
			$('#unitmap').html(feedback);
		},
		error: function(request, feedback, error)
		{
			alert("Request failed\n" + feedback + "\n" + error);
			return false;
		}
	});
	$("#unitmap-modal").modal("show");
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

function deleteMapUnit(MapID)
{
	var adate = new Date().getTime();
	$.ajax({async: false, type: "POST", url: "ajaxDeleteComplexUnitMap.php", dataType: "html",
		data: "dc=" + adate + "&mid=" + MapID,
		success: function (feedback)
		{
			openUnitMap();
		},
		error: function(request, feedback, error)
		{
			alert("Request failed\n" + feedback + "\n" + error);
			return false;
		}
	});
}

function addComplexMapUnit()
{
	var adate = new Date().getTime();
	var addnum = $('#addnumunits').val();
	$.ajax({async: false, type: "POST", url: "ajaxAddComplexUnitMap.php", dataType: "html",
		data: "dc=" + adate + "&cid=<?php echo $ComplexID; ?>&hmany=" + addnum,
		success: function (feedback)
		{
			openUnitMap();
		},
		error: function(request, feedback, error)
		{
			alert("Request failed\n" + feedback + "\n" + error);
			return false;
		}
	});
}

function openMeetComplete(meetingid)
{
	$('#CompMeetingID').val(meetingid);
	$("#completemeet-modal").modal("show");
}

function checkSubDomain()
{
	var adate = new Date().getTime();
	var subd = $('#subdomain').val();
	$.ajax({async: false, type: "POST", url: "ajaxGetComplexSubdomain.php", dataType: "json",
		data: "dc=" + adate + "&cid=<?php echo $ComplexID; ?>&subd=" + subd,
		success: function (feedback)
		{
			$('#subdomain').css('border', '0px solid #FF0000');
			if((feedback.complexid != 0) && (feedback.complexid != <?php echo $ComplexID; ?>))
				$('#subdomain').css('border', '1px solid #FF0000');
		},
		error: function(request, feedback, error)
		{
			alert("Request failed\n" + feedback + "\n" + error);
			return false;
		}
	});
}
</script>
<div class="panel panel-default">
	<div class="panel-body">
		<?php echo $StatusCrumb; ?>
		<div class='btn-group top5'>
			<a href='complexresidents.php?cid=<?php echo $ComplexID; ?>' class='btn btn-success'><i class='fa fa-users'></i> Complex Residents</a>
			<a href='' class='btn btn-default'><i class='fa fa-thumbs-o-up'></i> Interest Report</a>
			<div class="btn-group" role="group">
			<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Build Documents <span class="caret"></span></button>
			<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
				<li><a href='genproposal.php?cid=<?php echo $ComplexID; ?>' target='_blank'><i class='fa fa-file-pdf-o text-danger'></i> Proposal</a></li>
				<li><a href='genloep.php?cid=<?php echo $ComplexID; ?>' target='_blank'><i class='fa fa-file-pdf-o text-danger'></i> Letter of permission</a></li>
				<li><a href='genMOU.php?cid=<?php echo $ComplexID; ?>' target='_blank'><i class='fa fa-file-pdf-o text-danger'></i> MOU</a></li>
				<li><a href='genmarketingplan.php?cid=<?php echo $ComplexID; ?>' target='_blank'><i class='fa fa-file-pdf-o text-danger'></i> Marketing plan</a></li>
				<li><a href='genContactForm.php?cid=<?php echo $ComplexID; ?>' target='_blank'><i class='fa fa-file-pdf-o text-danger'></i> Complex Details Form</a></li>
				<li><a href='genIntro.php?cid=<?php echo $ComplexID; ?>' target='_blank'><i class='fa fa-file-pdf-o text-danger'></i> Residents Intro Letter</a></li>
				<li><a href='genPricingDoc.php?cid=<?php echo $ComplexID; ?>' target='_blank'><i class='fa fa-file-pdf-o text-danger'></i> Packages Doc</a></li>
				
				
				<li role="separator" class="divider"></li>
<?php
if(($ComplexRec['kickoff'] != '') && (substr($ComplexRec['kickoff'], 0, 10) != '0000-00-00'))
{
?>
				<li><a href='genkickoff.php?cid=<?php echo $ComplexID; ?>' target='_blank'><i class='fa fa-file-pdf-o text-danger'></i> Kick-off Notification</a></li>
<?php
}
else
{
?>
				<li class="disabled" title='Please enter a valid kick-off date'><a href='genkickoff.php?cid=<?php echo $ComplexID; ?>' target='_blank'><i class='fa fa-file-pdf-o text-danger'></i> Kick-off Notification</a></li>
<?php
}
?>	
			</ul>
			</div>
			<div class="btn-group" role="group">
			<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Set Meeting <span class="caret"></span></button>
			<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
<?php
foreach($MeetingTypes AS $TypeID => $Typename)
{
	echo "<li><a href='#' onclick='openMeetingModal(0, " . $TypeID . ")'><i class='fa fa-clock-o text-danger'></i> " . $Typename . "</a></li>";
}
?>
			</ul>
			</div>
		</div>
	</div>
</div>
<form method='POST' action='saveComplex.php' id='complexForm'>
<input type='hidden' name='ComplexID' id='ComplexID' value='<?php echo $ComplexID; ?>'>
<input type='hidden' name='BillingID' id='BillingID' value='<?php echo $HOACustomer['billingid']; ?>'>
<div class="panel panel-default">
	<div class="panel-heading"><strong>Complex Details
	<button type='button' class='btn btn-xs pull-right' onclick='$("#ComplexPanel").toggleClass("hidden"); $("#ComplexPanelCHV").toggleClass("fa-chevron-down");'>
	<span class='fa fa-chevron-up' id='ComplexPanelCHV'></span></button> 
<?php
if(count($ImportRecord) > 0)
	echo "<button type='button' class='btn btn-xs btn-warning pull-right' data-toggle='modal' data-target='#import-modal'><i class='fa fa-download'></i> Import Data</button>\n";
?>
		</strong>
	</div>
	<div class="panel-body" id="ComplexPanel">
		<div class="form-group form-group-sm">
			<div class='row'>
				<div class='col-md-2'>Complex:</div>
				<div class='col-md-5'><input type='text' name='complexname' id='complexname' maxlength='100' class='validate[required] form-control' value='<?php echo $ComplexRec['complexname']; ?>'></div>
				<div class='col-md-2'>Code:</div>
				<div class='col-md-3'><input type='text' name='complexcode' id='complexcode' maxlength='10' class='form-control' value='<?php echo $ComplexRec['complexcode']; ?>'></div>
			</div>
			<div class='row top5'>
				<!-- <div class='col-md-2'># Units:</div><div class='col-md-2'></div> -->
				<div class='col-md-2'># Units:</div><div class='col-md-2'>
					<input type='hidden' name='numunits' id='numunits' value='<?php echo $CompUnitCount; ?>'>
					<button type='button' class='btn btn-primary btn-block' onclick='openUnitMap();'><i class='fa fa-building'></i> <?php echo $CompUnitCount; ?></button></div>
				<div class='col-md-2'>Vendor:</div><div class='col-md-3'>
				<select name='vendorid' class="form-control">
				<option value=''>-- Select Vendor --</option>
<?php 
foreach($Vendors AS $VendorID => $VendorRec)
{
	echo "<option value='" . $VendorID. "'";
	if($VendorID == $ComplexRec['vendorid'])
		echo " selected='selected'";
	echo ">" . $VendorRec . "</option>";
}
?>	
						</select>
					</div>
				<div class='col-md-1'>Registered:</div><div class='col-md-2'><?php echo substr($ComplexRec['dateregistered'], 0, 10); ?></div>
			</div>
			<div class='row top5'>
				<div class='col-md-2'>Agent:</div><div class='col-md-2'>
					<select name='agentid' class="form-control">
					<option value=''>-- Select Agent --</option>
<?php 
$SelList = getUserOptionList($ComplexRec['agentid']);
echo $SelList;
?>	
					</select>
				</div>
				<div class='col-md-2'>Secondary Agent:</div><div class='col-md-2'>
					<select name='secagentid' class="form-control">
					<option value=''>-- Select Agent --</option>
<?php 
$SelList = getUserOptionList($ComplexRec['secagentid']);
echo $SelList;
?>	
					</select>
				</div>
				<div class='col-md-2'>Type:</div><div class='col-md-2'>
				
					<select name='complextype' class="validate[required] form-control">
					<option value=''>-- Select Type --</option>
<?php 
foreach($ComplextTypes AS $TypeID => $TypeRec)
{
	echo "<option value='" . $TypeID. "'";
	if($TypeID == $ComplexRec['complextype'])
		echo " selected='selected'";
	echo ">" . $TypeRec . "</option>";
}
?>	
					</select>
				</div>
			</div>
			<div class='row top5'>
				<div class='col-md-2'>Landing Page:</div>
					<div class='col-md-4 form-inline'><input type='text' name='subdomain' id='subdomain' class='form-control' style='width: 150px;' value='<?php echo $LandingPage; ?>' onchange='checkSubDomain();'>.domain.com</div>
			</div>
			
			
			<div class='row top5'>
				<div class='col-md-2'>Group:</div><div class='col-md-4'>
					<select name='complexgroup' class="form-control">
					<option value=''>-- Select Group --</option>
<?php 
foreach($ComplexGroups AS $GroupID => $GroupName)
{
	echo "<option value='" . $GroupID. "'";
	if($GroupID == $ComplexRec['groupid'])
		echo " selected='selected'";
	echo ">" . $GroupName . "</option>";
}
?>	
					</select>
				</div>
				<div class='col-md-2'>Kick-off:</div><div class='col-md-4'>
					<input type='text' name='kickoff' id='kickoff' maxlength='10' class='validate[custom[date]] form-control' value='<?php echo $KickoffDate; ?>'>
				</div>
			</div>
			<div class='row top5'>
				<div class='btn-group'>
				<button type='button' class='btn btn-primary' data-toggle="modal" data-target="#comment-modal"><i class='fa fa-comment'></i> Status History</button>
				<button type='button' class='btn btn-primary' data-toggle="modal" data-target="#status-modal"><i class='fa fa-share'></i> Change Status/Comment</button>
<?php 
if(count($ComplexSOSteps) == 0)
	echo "<a href='launchSOProc.php?cid=" . $ComplexID . "' class='btn btn-info'><i class='fa fa-file'></i> Initiate S/O Process</a>";
else
	echo "<button type='button' class='btn btn-info' data-toggle='modal' data-target='#SandOProcess-modal'><i class='fa fa-file'></i> Sales/Operations</button>";
?>
				<button type='button' class='btn btn-info' data-toggle="modal" data-target="#document-modal"><i class='fa fa-file'></i> Documents (<?php echo count($CompFiles); ?>)</button>
				<button type='submit' class='btn btn-success'><i class='fa fa-save'></i> Save</button>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
if(count($ComplexMeetings) > 0)
{
?>
	<div class="panel panel-default">
	<div class="panel-heading"><strong>Complex Meetings
		<button type='button' class='btn btn-xs pull-right' onclick='$("#MeetingPanel").toggleClass("hidden"); $("#MeetingPanelCHV").toggleClass("fa-chevron-down");'><i class='fa fa-chevron-up' id='MeetingPanelCHV'></i></button>
		</strong>
	</div>
	<div class="panel-body" id="MeetingPanel">
	<table class='table table-bordered table-condensed'>
	<tr><th>Meeting Type</th><th>Start Date</th><th>Start Time</th><th>End Time</th><th>Created By</th><th></th></tr>
<?php
foreach($ComplexMeetings AS $MeetingID => $MeetingRec)
{
	$StartDate = substr($MeetingRec['starttime'], 0, 10);
	$StartHr = substr($MeetingRec['starttime'], 11, 2);
	$StartMin = substr($MeetingRec['starttime'], 14, 2);
	$EndHr = substr($MeetingRec['endtime'], 11, 2);
	$EndMin = substr($MeetingRec['endtime'], 14, 2);
	echo "<tr>";
	echo "<td>" . $MeetingTypes[$MeetingRec['meetingtypeid']] . "</td>";
	echo "<td>" . date("l d M Y", strtotime($StartDate)) . "</td>";
	echo "<td>" . $StartHr . ":" . $StartMin . "</td>";
	echo "<td>" . $EndHr . ":" . $EndMin . "</td>";
	echo "<td>" . $SysUsers[$MeetingRec['setupuser']]['firstname'] . " " . $SysUsers[$MeetingRec['setupuser']]['surname'] . "</td>";
	echo "<td>";
	echo "<a href='#' onclick='openMeetingModal(" . $MeetingID . ", 0)'><i class='fa fa-pencil'></i> Edit</a> ";
	echo "<button type='button' class='btn btn-default btn-xs' onclick='openMeetComplete(" . $MeetingID . ");'>Complete</button>";
	echo "</div>";
	echo "</td>";
	echo "</tr>";
}
?>
	</table>
	</div>
</div>
<?php
}
?>
<div class="panel panel-default">
	<div class="panel-heading"><strong>Complex Notes
		<button type='button' class='btn btn-xs pull-right' onclick='$("#CompNotePanel").toggleClass("hidden"); $("#CompNotePanelCHV").toggleClass("fa-chevron-down");'>
		<i class='fa fa-chevron-up' id='CompNotePanelCHV'></i></button>
		
		<button type='button' class='btn btn-primary btn-xs pull-right' data-toggle="modal" data-target="#notes-modal">
		<i class='fa fa-pencil'></i> Add note</button>
		
		</strong>
	</div>
	<div class="panel-body" id="CompNotePanel">
		<div class='table'>
			<table class='table table-bordered table-condensed' style='font-size: 12px;'>
			<tr><th>User</th><th>Date</th><th width='70%'>Note</th></tr>
<?php
foreach($ComplexNotes AS $NoteID => $NoteRec)
{
	echo "<tr>";
	echo "<td>" . $SysUsers[$NoteRec['userid']]['firstname'] . " " . $SysUsers[$NoteRec['userid']]['surname'] . "</td>";
	echo "<td>" . substr($NoteRec['datechanged'], 0, 16) . "</td>"; 
	echo "<td>" . nl2br($NoteRec['commentary']) . "</td>";
	echo "</tr>";
}
?>
			</table>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading"><strong>Bodycorp/HOA Customer Details
		<button type='button' class='btn btn-xs pull-right' onclick='$("#CustomerPanel").toggleClass("hidden"); $("#CustomerPanelCHV").toggleClass("fa-chevron-down");'>
		<span class='fa fa-chevron-up' id='CustomerPanelCHV'></span></button>
		<button type='button' class='btn btn-primary btn-xs pull-right'  data-toggle='modal' data-target='#bccontacts-modal'><i class='fa fa-phone'></i> Add Contact</button>
		</strong>
	</div>
	<div class="panel-body" id="CustomerPanel">
		<div class='table'>
			<table class='table table-bordered table-condensed'>
			<tr><th width='20%'>Name</th><th width='20%'>Email</th><th width='10%'>Cell</th><th width='10%'>Tel</th><th width='10%'>Designation</th><th width='10%'>Unit</th><th width='10%'>&nbsp;</th></tr>
<?php
foreach($BCContacts AS $ContactID => $ContactRec)
{
	echo "<tr>";
	echo "<td><span id='bccont_fname_" . $ContactID . "'>" . $ContactRec['contactname'] . "</span> <span id='bccont_sname_" . $ContactID . "'>" . $ContactRec['contactsurname'] . "</span></td>";
	echo "<td id='bccont_email_" . $ContactID . "'>" . $ContactRec['contactemail'] . "</td>";
	echo "<td id='bccont_cell_" . $ContactID . "'>" . $ContactRec['contactcell'] . "</td>";
	echo "<td id='bccont_tel_" . $ContactID . "'>" . $ContactRec['contacttel'] . "</td>";
	echo "<td id='bccont_desig_" . $ContactID . "'>" . $ContactRec['designation'] . "</td>";
	echo "<td id='bccont_unit_" . $ContactID . "'>" . $ContactRec['unitnum'] . "</td>";
	echo "<td><button type='button' class='btn btn-default btn-xs pull-right' onclick='loadBCContact(" . $ContactID . ");'><i class='fa fa-pencil'></i> Edit</button></td>";
	echo "</tr>\n";
}
?>
			</table>
		</div>
		<div class="form-group form-group-sm">
			<div class='row top5'><div class='col-md-2'>Billing Customer Name:</div><div class='col-md-10'><input type='text' name='billingname' id='billingname' maxlength='100' class='validate[required] form-control' value='<?php echo $HOACustomer['billingname']; ?>'></div></div>
			<div class='row top5'>
				<div class='col-md-1'>Contact:</div><div class='col-md-3'><input type='text' name='billingcontact' id='billingcontact' maxlength='100' class='form-control' value='<?php echo $HOACustomer['billingcontact']; ?>'></div>
				<div class='col-md-1'>Email:</div><div class='col-md-3'><input type='text' name='billingemail' id='billingemail' maxlength='100' class='form-control' value='<?php echo $HOACustomer['billingemail']; ?>'></div>
				<div class='col-md-1'>Cell:</div><div class='col-md-3'><input type='text' name='billingcell' id='billingcell' maxlength='100' class='form-control' value='<?php echo $HOACustomer['billingcell']; ?>'></div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading"><strong>Managing Agent Details
		<button type='button' class='btn btn-xs pull-right' onclick='$("#ManagingPanel").toggleClass("hidden"); $("#ManagingPanelCHV").toggleClass("fa-chevron-down");'>
		<span class='fa fa-chevron-up' id='ManagingPanelCHV'></span></button>
		</strong>
	</div>
	<div class="panel-body" id="ManagingPanel">
		<div class="form-group form-group-sm">
			<div class='row top5'>
			<div class='col-md-1'>Agent:</div><div class='col-md-2'>
			
			<select name='maagentid' class="form-control">
				<option value=''>-- Select Agent --</option>
<?php 
foreach($ManagingAgents AS $AgentID => $AgentRec)
{
	echo "<option value='" . $AgentID. "'";
	if($AgentID == $ComplexRec['maid'])
		echo " selected='selected'";
	echo ">" . $AgentRec . "</option>";
}
?>	
				</select>
			</div>
			<div class='col-md-1'>Contact:</div><div class='col-md-2'><input type='text' name='macontact' id='macontact' maxlength='100' class='form-control' value='<?php echo $ComplexRec['macontact']; ?>'></div>
			<div class='col-md-1'>Email:</div><div class='col-md-2'><input type='text' name='maemail' id='maemail' maxlength='100' class='form-control' value='<?php echo $ComplexRec['maemail']; ?>'></div>
			<div class='col-md-1'>Cell:</div><div class='col-md-2'><input type='text' name='macell' id='macell' maxlength='100' class='form-control' value='<?php echo $ComplexRec['macell']; ?>'></div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading"><strong>Security Company details
		<button type='button' class='btn btn-xs pull-right' onclick='$("#SecurityPanel").toggleClass("hidden"); $("#SecurityPanelCHV").toggleClass("fa-chevron-down");'>
		<span class='fa fa-chevron-up' id='SecurityPanelCHV'></span></button>
		</strong>
	</div>
	<div class="panel-body" id="SecurityPanel">
		<div class="form-group form-group-sm">
			<div class='row top5'>
			<div class='col-md-1'>Agent:</div><div class='col-md-2'>
			
			<select name='seccompid' class="form-control">
				<option value=''>-- Select Agent --</option>
<?php 
foreach($SecurityAgents AS $AgentID => $AgentRec)
{
	echo "<option value='" . $AgentID. "'";
	if($AgentID == $ComplexRec['seccompid'])
		echo " selected='selected'";
	echo ">" . $AgentRec . "</option>";
}
?>	
				</select>
			</div>
			<div class='col-md-1'>Contact:</div><div class='col-md-2'><input type='text' name='seccontact' id='seccontact' maxlength='100' class='form-control' value='<?php echo $ComplexRec['seccontact']; ?>'></div>
			<div class='col-md-1'>Email:</div><div class='col-md-2'><input type='text' name='secemail' id='secemail' maxlength='100' class='form-control' value='<?php echo $ComplexRec['secemail']; ?>'></div>
			<div class='col-md-1'>Cell:</div><div class='col-md-2'><input type='text' name='seccell' id='seccell' maxlength='100' class='form-control' value='<?php echo $ComplexRec['seccell']; ?>'></div>
			</div>
		</div>
	</div>
</div>
<div class="panel panel-default">
	<div class="panel-heading"><strong>Address Details
		<button type='button' class='btn btn-xs pull-right' onclick='$("#AddressPanel").toggleClass("hidden"); $("#AddressPanelCHV").toggleClass("fa-chevron-down");'>
		<span class='fa fa-chevron-up' id='AddressPanelCHV'></span></button>
		</strong>
	</div>
	<div class="panel-body" id="AddressPanel">
		<div class="form-group form-group-sm">
			<div class='row'>
				<div class='col-md-6'>
					<div class='row top5'><div class='col-md-3'>Address:</div><div class='col-md-9'><input type='text' name='address1' id='address1' class='form-control' value='<?php echo $ComplexRec['streetaddress1']; ?>'></div></div>
					<div class='row top5'><div class='col-md-3'></div><div class='col-md-9'><input type='text' name='address2' id='address2' class='form-control' value='<?php echo $ComplexRec['streetaddress2']; ?>'></div></div>
					<div class='row top5'><div class='col-md-3'></div><div class='col-md-9'><input type='text' name='address3' id='address3' class='form-control' value='<?php echo $ComplexRec['streetaddress3']; ?>'></div></div>
					<div class='row top5'><div class='col-md-3'></div><div class='col-md-9'><input type='text' name='address4' id='address4' class='form-control' value='<?php echo $ComplexRec['streetaddress4']; ?>'></div></div>
					<div class='row top5'><div class='col-md-3'>Postal Code:</div><div class='col-md-4'><input type='text' name='address5' id='address5' class='form-control' value='<?php echo $ComplexRec['streetaddress5']; ?>'></div></div>
				</div>
				<div class='col-md-6'>
					<div class='row top5'><div class='col-md-3'>Precinct:</div><div class='col-md-9'>
					<select name='precinctid' class="form-control">
					<option value=''>-- Select --</option>
<?php
foreach($Precincts AS $Key => $Val)
{
	echo "<option value='" . $Key . "'";
	if($Key == $ComplexRec['precinctid'])
		echo " selected='selected'";
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
	if($Key == $ComplexRec['suburbid'])
		echo " selected='selected'";
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
	if($Key == $ComplexRec['areaid'])
		echo " selected='selected'";
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
	if($Key == $ComplexRec['cityid'])
		echo " selected='selected'";
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
	if($Key == $ComplexRec['provinceid'])
		echo " selected='selected'";
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
	if($Key == $ComplexRec['countryid'])
		echo " selected='selected'";
	echo ">" . $Val['countryname'] . "</option>";
}
?>
					</select>
					</div></div>
				</div>
			</div>
		</div>
	</div>
</div>
</form>

<div class='modal fade' id='status-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<form method='POST' action='saveComplexStatusComment.php' id='commentForm'>
	<input type='hidden' name='ComplexID' id='ComplexID' value='<?php echo $ComplexID; ?>'>
	<div class='modal-dialog modal-lg'>
		<div class='modal-content'>
			<div class='modal-header'><strong>Status Comment<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body'>
			<div class='row top5'><div class='col-md-3'>Status:</div><div class='col-md-9'>
					<select name='complexstatus' class="validate[required] form-control">
					<option value=''>-- Select Status --</option>
<?php 
foreach($SiteStatusses AS $StatusID => $StatusRec)
{
	$StatusName = $StatusRec['statusname'];
	if($StatusRec['parentid'] > 0)
		$StatusName = " -- " . $StatusName;
	echo "<option value='" . $StatusID. "'";
	if($ComplexRec['statusid'] == $StatusID)
		echo " selected='selected'";
	echo ">" . $StatusName . "</option>";
}
?>	
					</select>
				</div>
			</div>
			<div class='row top5'><div class='col-md-12'>Comment:</div></div>
			<div class='row top5'><div class='col-md-12'><textarea id='statuscomment' name='statuscomment' class='form-control'></textarea></div></div>
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
					<table class='table table-bordered'>
					<tr><th>Status</th><th>User</th><th>Date</th><th>Comment</th></tr>
<?php
$StatusHistory = getComplexStatusHistory($ComplexID);
$PrevStatus = 0;
foreach($StatusHistory AS $cnt => $HistRec)
{
	echo "<tr>";
	if($PrevStatus != $HistRec['statusid'])
	{
		echo "<td>" . $SiteStatusses[$HistRec['statusid']]['statusname'] . "</td>";
		if($HistRec['statususer'] == -112)
			echo "<td><i>System Import</i></td>";
		else
			echo "<td>" . $Agents[$HistRec['statususer']]['firstname'] . " " . $Agents[$HistRec['statususer']]['surname'] . "</td>";
		echo "<td>" . $HistRec['statusdate'] . "</td>";
		echo "<td>" . $HistRec['commentary'] . "</td>";
		$PrevStatus = $HistRec['statusid'];
	}
	else
	{
		echo "<td>&nbsp;</td>";
		if($HistRec['commentuser'] == -112)
			echo "<td><i>System Import</i></td>";
		else
			echo "<td>" . $Agents[$HistRec['commentuser']]['firstname'] . " " . $Agents[$HistRec['commentuser']]['surname'] . "</td>";
		echo "<td>" . $HistRec['commentdate'] . "</td>";
		echo "<td>" . $HistRec['commentary'] . "</td>";
	}
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

<div class='modal fade' id='meeting-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<div class='modal-dialog modal-lg'>
		<form method='POST' action='saveComplexMeeting.php' id='meetingForm'>
		<input type='hidden' name='MeetComplexID' id='MeetComplexID' value='<?php echo $ComplexID; ?>'>
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

<div class='modal fade' id='unitmap-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<div class='modal-dialog modal-lg'>
		<form method='POST' action='saveComplexUnitMap.php' id='mapForm'>
		<input type='hidden' name='ComplexID' id='ComplexID' value='<?php echo $ComplexID; ?>'>
		<input type='hidden' name='CustomerID' id='CustomerID' value='<?php echo $HOACustomer['billingid']; ?>'>
		<div class='modal-content'>
			<div class='modal-header'><strong><?php echo $ComplexRec['complexname']; ?> Units<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body'>
				<div class='table'>
					<table class='table table-bordered' id='unitmap'>
					
					</table>
				</div>
			</div>	
			<div class='modal-footer'>
				<input type='text' name='addnumunits' id='addnumunits' class='validate[custom[integer]] pull-left' size='2' value='1'><button type='button' class='btn btn-success pull-left' onclick='addComplexMapUnit();'><span class='fa fa-plus'></span> Add unit</button>
			
				<button type='button' class='btn btn-default' data-dismiss='modal'><span class='fa fa-times'></span> Close</button>
				<button type='submit' class='btn btn-success pull-right'><i class='fa fa-save'></i> Save</button>
			</div>
		</div>
		</form>
	</div>
</div>
<div class='modal fade' id='bccontacts-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<div class='modal-dialog modal-lg'>
		<form method='POST' action='saveBCContact.php' id='bccontactsForm'>
		<input type='hidden' name='BCCComplexID' id='BCCComplexID' value='<?php echo $ComplexID; ?>'>
		<input type='hidden' name='BCCContactID' id='BCCContactID' value='0'>
		<div class='modal-content'>
			<div class='modal-header'><strong>Bodycorp Contacts<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body'>
				<div class='table'>
					<table class='table table-bordered'>
					<tr>
						<td>
						<div class="form-inline">
							<div class="form-group">
							<input type='text' name='contactname' id='contactname' class='form-control input-sm' placeholder='Firstname'> 
							</div>
							<div class="form-group">
							<input type='text' name='contactsurname' id='contactsurname' class='form-control input-sm' placeholder='Lastname'>
							</div>
						</div>
						</td>
						<td><input type='text' name='contactemail' id='contactemail' class='form-control input-sm' placeholder='Email'></td>
						<td><input type='text' name='contactcell' id='contactcell' class='form-control input-sm' placeholder='Cell Number'></td>
						<td><input type='text' name='contacttel' id='contacttel' class='form-control input-sm' placeholder='Tel Number'></td>
						<td><button type='submit' class='btn btn-success btn-xs pull-right'><i class='fa fa-save'></i> Save</button></td>
					</tr>
					<tr><td>Designation: <input type='text' name='designation' id='designation' class='form-control input-sm' placeholder='Designation'></td>
						<td>Unit: <input type='text' name='contactunit' id='contactunit' class='form-control input-sm' placeholder='Unit'></td></tr>
					</table>
				</div>
			</div>	
			<div class='modal-footer'>
				<button type='button' class='btn btn-default btn-sm' data-dismiss='modal'><span class='fa fa-times'></span> Close</button>
			</div>
		</div>
		</form>
	</div>
</div>

<div class='modal fade' id='notes-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<div class='modal-dialog modal-lg'>
		<form method='POST' action='saveComplexNote.php' id='notesForm'>
		<input type='hidden' name='noteComplexID' id='noteComplexID' value='<?php echo $ComplexID; ?>'>
		<div class='modal-content'>
			<div class='modal-header'><strong>Add a complex note<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body'>
				<div class='row top5'>
					<div class='col-md-12'><textarea name='compnote' id='compnote' class='form-control'></textarea></div>
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
		<form method='POST' action='setMeetingComplete.php' id='completeMeetForm'>
		<input type='hidden' name='CompMeetingID' id='CompMeetingID' value=''>
		<input type='hidden' name='Origin' id='Origin' value='1'>
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

<div class='modal fade' id='document-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<div class='modal-dialog modal-lg'>
		<div class='modal-content'>
			<div class='modal-header'><strong>Complex Documents<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body'>
				<div class='row'>
					<div class='col-md-9'>
						<div class='table'>
							<table class='table table-bordered'>
							<tr><th colspan='5'><strong>Letter of engagement</strong></th></tr>
							<tr><th>Document</th><th>Uploaded</th><th>By</th><th colspan='2'></th></tr>
<?php
foreach($CompFiles AS $FileID => $FileRec)
{
	if($FileRec['doctype'] == 1)
	{
		echo "<tr>";
		echo "<td><a href='getFile.php?fid=" . $FileID . "'>" . $FileRec['filename'] . "</a></td>";
		echo "<td>" . $FileRec['uploadtime'] . "</td>";
		echo "<td>" . $Agents[$FileRec['userid']]['firstname'] . " " . $Agents[$FileRec['userid']]['surname'] . "</td>";
		echo "<td colspan='2'><a href='getFile.php?fid=" . $FileID . "'>Download</a></td>";
	}
}
?>							
							<tr><th colspan='5'><strong>Memorandum of understanding (MOU)</strong></th></tr>
							<tr><th>Document</th><th>Uploaded</th><th>By</th><th colspan='2'></th></tr>
<?php
foreach($CompFiles AS $FileID => $FileRec)
{
	if($FileRec['doctype'] == 2)
	{
		echo "<tr>";
		echo "<td><a href='getFile.php?fid=" . $FileID . "'>" . $FileRec['filename'] . "</a></td>";
		echo "<td>" . $FileRec['uploadtime'] . "</td>";
		echo "<td>" . $Agents[$FileRec['userid']]['firstname'] . " " . $Agents[$FileRec['userid']]['surname'] . "</td>";
		echo "<td colspan='2'><a href='getFile.php?fid=" . $FileID . "'>Download</a></td>";
	}
}
?>							
							<tr><th colspan='5'><strong>Site Plans</strong></th></tr>
							<tr><th>Document</th><th>Uploaded</th><th>By</th><th colspan='2'></th></tr>
<?php
foreach($CompFiles AS $FileID => $FileRec)
{
	if($FileRec['doctype'] == 3)
	{
		echo "<tr>";
		echo "<td><a href='getFile.php?fid=" . $FileID . "'>" . $FileRec['filename'] . "</a></td>";
		echo "<td>" . $FileRec['uploadtime'] . "</td>";
		echo "<td>" . $Agents[$FileRec['userid']]['firstname'] . " " . $Agents[$FileRec['userid']]['surname'] . "</td>";
		echo "<td><a href='getFile.php?fid=" . $FileID . "'>Download</a></td>";
		echo "<td><a href='genSitePlan.php?cid=" . $ComplexID . "&fid=" . $FileID . "' target='_blank'>Sign off copy</a></td>";
	}
}
?>							
							<tr><th colspan='5'><strong>Other</strong></th></tr>
							<tr><th>Document</th><th>Uploaded</th><th>By</th><th colspan='2'></th></tr>
<?php
foreach($CompFiles AS $FileID => $FileRec)
{
	if(($FileRec['doctype'] != 1) && ($FileRec['doctype'] != 2) && ($FileRec['doctype'] != 3))
	{
		echo "<tr>";
		echo "<td><a href='getFile.php?fid=" . $FileID . "'>" . $FileRec['filename'] . "</a></td>";
		echo "<td>" . $FileRec['uploadtime'] . "</td>";
		echo "<td>" . $Agents[$FileRec['userid']]['firstname'] . " " . $Agents[$FileRec['userid']]['surname'] . "</td>";
		echo "<td colspan='2'><a href='getFile.php?fid=" . $FileID . "'>Download</a></td>";
	}
}
?>							
							</table>
						</div>
					</div>
					<div class='col-md-3'>
					<form method='POST' action='saveComplexDocument.php' id='docForm' enctype="multipart/form-data">
						<input type='hidden' name='docComplexID' id='docComplexID' value='<?php echo $ComplexID; ?>'>
						<p><input type="file" name='complexfile' id='complexfile' class='file'  value='' data-filename-placement='inside'></p>
						<p>Doc Type: <select name='doctype' id='doctype'>
						<option value='0'>Other</option>
						<option value='1'>Letter of engagement</option>
						<option value='2'>MOU</option>
						<option value='3'>Site plan</option>
						</select></p>
						<p><button type='submit' class='btn btn-success pull-right'><i class='fa fa-save'></i> Upload</button></p>
					</div>
				</div>
			</div>	
			<div class='modal-footer'>
				<button type='button' class='btn btn-default' data-dismiss='modal'><span class='fa fa-times'></span> Close</button>
			</div>
		</div>
		</form>
	</div>
</div>
<div class='modal fade' id='SandOProcess-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<div class='modal-dialog modal-lg'>
		<input type='hidden' name='SOComplexID' id='SOComplexID' value='<?php echo $ComplexID; ?>'>
		<div class='modal-content'>
			<div class='modal-header'><strong>Sales and Operations Workflow<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body'>
				<div class='table'>
					<table class='table table-bordered'>
					<tr><th>Step</th><th>Date/Time</th></tr>
<?php
foreach($SOProcess AS $StepID => $StepRec)
{
	$TheVal = (isset($ComplexSOSteps[$StepID])) ? $ComplexSOSteps[$StepID] : "<em>Awaiting</em>";
	echo "<tr>";
	echo "<td>" . $StepRec['stepname'] . "</td>";
	echo "<td>" . $TheVal . "</td>";
	echo "</tr>";
}
?>
					</table>
				</div>
			</div>	
			<div class='modal-footer'>
				<button type='button' class='btn btn-default' data-dismiss='modal'><span class='fa fa-times'></span> Close</button>
<!--				<button type='submit' class='btn btn-success pull-right'><i class='fa fa-save'></i> Save</button> -->
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
					<table class='table table-bordered'>
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