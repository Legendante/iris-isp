<?php
session_start();
include("db.inc.php");

// print_r($_POST);
// exit();
$MeetingID = pebkac($_POST['CompMeetingID']);
$Comment = pebkac($_POST['meetingcomment'], 1000, 'STRING');
$Origin = (isset($_POST['Origin'])) ? pebkac($_POST['Origin']) : 0;
$Meeting = getMeetingByID($MeetingID);
$ComplexID = $Meeting['complexid'];
$MeetType = $Meeting['meetingtypeid'];
$SaveArr = array();
$SaveArr['completed'] = 1;
$SaveArr['completeduser'] = $_SESSION['userid'];
saveMeeting($MeetingID, $SaveArr);
if($MeetType == 1)
{
	$StepID = getSalesOperationsStepByName("Bodycorp meeting feedback");
	setComplexSalesOperationsStepsDate($ComplexID, $StepID['stepid']);
}
elseif($MeetType == 2)
{
	$StepID = getSalesOperationsStepByName("Site survey feedback");
	setComplexSalesOperationsStepsDate($ComplexID, $StepID['stepid']);
}
if($Comment != '')
{
	$MeetingTypes = getMeetingTypes();
	$Comment = "Meeting Feedback\n" . $MeetingTypes[$Meeting['meetingtypeid']]  . "\nTime " . $Meeting['starttime'] . " to " . substr($Meeting['endtime'], 11,2) . "\n-- Feedback -- \n" . $Comment;
	$NoteArr = array();
	$NoteArr['complexid'] = $ComplexID;
	$NoteArr['commentary'] = $Comment;
	$NoteArr['userid'] = $_SESSION['userid'];
	addComplexNote($NoteArr);
}
if($Origin == 1)
	header("Location: complex.php?cid=" . $ComplexID);
else
	header("Location: dashboard.php");
?>