<?php
session_start();
include("db.inc.php");
$SaveArr = array();
$ComplexID = (isset($_POST['MeetComplexID'])) ? pebkac($_POST['MeetComplexID']) : '';
$MeetingID = pebkac($_POST['MeetingID']);
$StartDate = pebkac($_POST['meetstartday'], 10, 'STRING');
$StartHr = pebkac($_POST['meetstarthour'], 2);
$StartMin = pebkac($_POST['meetstartmin'], 2);
$EndHr = pebkac($_POST['meetendhour'], 2);
$EndMin = pebkac($_POST['meetendmin'], 2);
$MeetType = pebkac($_POST['meettype'], 2);
$Attendees = $_POST['meetattend'];
$StartStr = $StartDate . " " . $StartHr . ":" . $StartMin;
$EndStr = $StartDate . " " . $EndHr . ":" . $EndMin;

$SaveArr['customerid'] = 0;
$SaveArr['meetingtypeid'] = $MeetType;
$SaveArr['completed'] = 0;
$SaveArr['starttime'] = $StartStr;
$SaveArr['endtime'] = $EndStr;
if($MeetingID == 0)
{
	$SaveArr['complexid'] = $ComplexID;
	$SaveArr['setupuser'] = $_SESSION['userid'];
	$MeetingID = addMeeting($SaveArr);
	if($MeetType == 1)
	{
		$StepID = getSalesOperationsStepByName("Bodycorp meeting");
		setComplexSalesOperationsStepsDate($ComplexID, $StepID['stepid']);
	}
	elseif($MeetType == 2)
	{
		$StepID = getSalesOperationsStepByName("Site survey");
		setComplexSalesOperationsStepsDate($ComplexID, $StepID['stepid']);
	}
}
else
	saveMeeting($MeetingID, $SaveArr);
deleteMeetingAttendees($MeetingID);
foreach($Attendees AS $AttID)
{
	$AttArr = array();
	$AttArr['meetingid'] = $MeetingID;
	$AttArr['userid'] = $AttID;
	$AttArr['attendance'] = 0;
	addMeetingAttendee($AttArr);
}
if(isset($_POST['fromDash']))
	header("Location: dashboard.php");
else
	header("Location: complex.php?cid=" . $ComplexID);
?>