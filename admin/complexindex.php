<?php
set_time_limit(0);
include("header.inc.php");
$RefinedStatus = (isset($_GET['s'])) ? pebkac($_GET['s']) : '';
$PageNum = (isset($_GET['p'])) ? pebkac($_GET['p']) : 0;
$Agent = (isset($_GET['a'])) ? pebkac($_GET['a']) : 0;
$BuildType = (isset($_GET['b'])) ? pebkac($_GET['b']) : 0;
$NumPerPage = 100;
$PageOffset = $PageNum * $NumPerPage;
$ComplextTypes = getComplexTypes();
$ComplexGroups = getAllComplexGroups();
$Precincts = getPrecincts();
$Suburbs = getSuburbs();
$Areas = getAreas();
$Cities = getCities();
$Provinces = getProvinces();
$Countries = getCountries();
$TypeDispArr = array();
$TypeSelArr = array(0 => "Estates", 1 => "FSH", 2 => "Business", 3 => "Other");
foreach($ComplextTypes AS $TypeID => $TypeRec)
{
	switch($TypeRec)
	{
		case "Complex":
		case "Estate":
		case "Cluster":
		case "Single & Multi Dwelling":
		case "Mixed":
		case "Townhouses":
		case "Stack Units":
		case "Multi-dwelling units":
			$TypeDispArr['Estates'][] = $TypeID;
			break;
		case "Free Standing House":
			$TypeDispArr['FSH'][] = $TypeID;
			break;
		case "Shopping Centre":
		case "Business Park":
		case "Office Park":
		case "Office Block":
			$TypeDispArr['Business'][] = $TypeID;
			break;
		case "Street":
		case "Precinct":
		default:
			$TypeDispArr['Other'][] = $TypeID;
			break;
	}
}
$SiteStatusses = getSiteStatusses();
$Agents = getAgents();
$Vendors = getVendors();
$Vendors[0] = '-';
$StartsWith = array();
if($Agent == 0)
{
	$MyComplexes = getAllComplexes($RefinedStatus, $TypeDispArr[$TypeSelArr[$BuildType]], $NumPerPage, $PageOffset);
	$MyComplexCount = getAllComplexCount();
}
else
{
	$MyComplexes = getAgentComplexes($Agent, $RefinedStatus);
	$MyComplexCount = getAgentComplexCount($Agent);
}
if(!isset($MyComplexCount[0]))
	$MyComplexCount[0] = 0;
$CompCount = array(9999 => $MyComplexCount[0]);
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
$StatusCrumb = '<div class="row"><div class="col-md-12 text-center"><div class="btn-group btn-breadcrumb">';
$RecFound = 0;
if($RefinedStatus == '')
{
	$RecFound = $CompCountTotal;
	$StatusCrumb .= '<a href="complexindex.php" class="btn btn-success"><span class="badge">' . $CompCountTotal . '</span> <i class="fa fa-home"></i></a>';
}
else
{
	$RecFound = $CompCount[$RefinedStatus];
	$StatusCrumb .= '<a href="complexindex.php" class="btn btn-default"><span class="badge">' . $CompCountTotal . '</span> <i class="fa fa-home"></i></a>';
}
if($CompCount[9999] > 0)
{
	if($RefinedStatus == 9999)
		$StatusCrumb .= '<a href="complexindex.php?s=9999" class="btn btn-success"><span class="badge">' . $CompCount[9999] . '</span> <i class="fa fa-question"></i></a>';
	else
		$StatusCrumb .= '<a href="complexindex.php?s=9999" class="btn btn-default"><span class="badge">' . $CompCount[9999] . '</span> <i class="fa fa-question"></i></a>';
}
foreach($SiteStatusses AS $StatusID => $StatusRec)
{
	if($StatusRec['parentid'] == 0)
	{
		$StatCount = (isset($CompCount[$StatusID])) ? $CompCount[$StatusID] : 0;
		if($RefinedStatus == $StatusID)
			$StatusCrumb .= '<a href="complexindex.php?s=' . $StatusID . '" class="btn btn-success">' . $StatusRec['statusname'] . ' <span class="badge">' . $StatCount . '</span></a>';
		else
			$StatusCrumb .= '<a href="complexindex.php?s=' . $StatusID . '" class="btn btn-default">' . $StatusRec['statusname'] . ' <span class="badge">' . $StatCount . '</span></a>';
	}
}
$StatusCrumb .= '</div></div></div>';
$NumPages = ceil($RecFound / $NumPerPage); 
?>
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
</script>
<div class='row'>
	<div class='col-md-12'>
		<div class='table'>
			<table class='table table-bordered'>
			<tr><td colspan='9'><?php echo $StatusCrumb; ?></td></tr>
			<tr><td colspan='9'><div class="btn-group">
<?php
foreach($TypeSelArr AS $Key => $Text)
{
	echo '<a href="complexindex.php?b=' . $Key . '" class="btn btn-default">' . $Text . '</a>';
}
?>
			</div></td></tr>
			
			
			<tr><td colspan='9'>
			<small><?php echo $RecFound; ?> Records. <?php echo $NumPages; ?> pages.</small>
			<div class='row'>
				<div class='col-md-6'>
					<div class='btn-group'>
<?php
$StartP = $PageNum - 6;
$AddToE = 0;
if($StartP < 0)
{
	$AddToE = abs($StartP);
	$StartP = 0;
}	
$EndP = ($PageNum + 6) + $AddToE;
$EndP = ($EndP >= $NumPages) ? $NumPages : $EndP;
// echo $PageNum . ' ' . $NumPages . " ;; " . $EndP . "<BR>";

if($PageNum > 6)
	echo "<a href='complexindex.php?p=0&s=" . $RefinedStatus . "' class='btn btn-success btn-sm'><i class='fa fa-fast-backward'></i></a>";
for($i = $StartP; $i < $EndP; $i++)
{
	if($i == $PageNum)
		echo "<a href='complexindex.php?p=" . $i . "&s=" . $RefinedStatus . "' class='btn btn-success btn-sm'>" . ($i + 1) . "</a>";
	else
		echo "<a href='complexindex.php?p=" . $i . "&s=" . $RefinedStatus . "' class='btn btn-default btn-sm'>" . ($i + 1) . "</a>";
}
if($PageNum <= ($NumPages - 6))
echo "<a href='complexindex.php?p=" . $NumPages . "&s=" . $RefinedStatus . "' class='btn btn-success btn-sm'><i class='fa fa-fast-forward'></i></a>";
?>					
					</div>
				</div>
				<div class='col-md-6'>
					<form method='GET' action='complexindex.php' class="form-inline">
					<button type='submit' class='btn btn-default btn-sm pull-right'><i class='fa fa-search'></i> Fetch</button>
					<select name='a' class="form-control pull-right">
					<option value=''>-- All Agents --</option>
<?php 
$SelList = getUserOptionList($Agent);
echo $SelList;
?>	
					</select>
					</form>
				</div>
			</div>
			</td></tr>
			<tr class='bg-success text-success'><th>Complex Name</th><th>Code</th><th>Num Units</th><th>Agent</th><th>Type</th><th>Status</th><th>Vendor</th><th>Residents</th><th>
				<button type='button' class='btn btn-success' onclick='$("#newcomplex-modal").modal("show");'><i class='fa fa-plus'></i> Add Complex</button>
			</th></tr>
<?php
foreach($MyComplexes AS $ComplexID => $CompRec)
{
	echo "<tr>";
	echo "<td>" . $CompRec['complexname'] . "</td>";
	echo "<td>" . $CompRec['complexcode'] . "</td>";
	echo "<td>" . $CompRec['numunits'] . "</td>";
	if(($CompRec['agentid'] == '') || ($CompRec['agentid'] == 0))
		echo "<td class='bg-danger'><strong>Unknown</strong></td>";
	else
	{
		$AgentName = $Agents[$CompRec['agentid']]['firstname'] . " " . $Agents[$CompRec['agentid']]['surname'];
		if($Agents[$CompRec['agentid']]['inactive'] == 2)
			$AgentName .= " - ** On leave **";
		if($Agents[$CompRec['agentid']]['inactive'] == 1)
			echo "<td class='bg-danger'><strong>" . $AgentName . "</strong></td>";
		else
			echo "<td>" . $AgentName . "</td>";
	}
	
	if(!isset($ComplextTypes[$CompRec['complextype']]))
		echo "<td class='bg-danger'><strong>Unknown</strong></td>";
	else
		echo "<td>" . $ComplextTypes[$CompRec['complextype']] . "</td>";
	$Status = (isset($SiteStatusses[$CompRec['statusid']]['statusname'])) ? $SiteStatusses[$CompRec['statusid']]['statusname'] : 'Unknown';
	if((isset($SiteStatusses[$CompRec['statusid']])) && ($SiteStatusses[$CompRec['statusid']]['parentid'] != 0))
	{
		$ParentStatus = $SiteStatusses[$SiteStatusses[$CompRec['statusid']]['parentid']]['statusname'];
		$Status = $ParentStatus . " <i class='fa fa-angle-right'></i> " . $Status;
	}
	if(!isset($SiteStatusses[$CompRec['statusid']]['statusname']))
		echo "<td class='bg-danger'><strong>Unknown</strong></td>";
	else
		echo "<td>" . $Status . "</td>";
	$VendorName = isset($Vendors[$CompRec['vendorid']]) ? $Vendors[$CompRec['vendorid']] : 'Unknown';
	if(!isset($Vendors[$CompRec['vendorid']]))
		echo "<td class='bg-danger'><strong>Unknown</strong></td>";
	else
		echo "<td>" . $Vendors[$CompRec['vendorid']] . "</td>";
	echo "<td><a href='complexresidents.php?cid=" . $ComplexID . "' class='btn btn-success'><i class='fa fa-users'></i> Residents</a></td>";
	echo "<td><a href='complex.php?cid=" . $ComplexID . "' class='btn btn-info btn-md'><i class='fa fa-eye'></i> View</a></td>";
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
<?php
include("footer.inc.php");
?>