<?php
session_start();
include("db.inc.php");

$MeetingID = pebkac($_POST['mid']);
$retArr = array();
$meetingArr = getMeetingByID($MeetingID);
$StartDate = substr($meetingArr['starttime'], 0, 10);
$StartHr = substr($meetingArr['starttime'], 11, 2);
$StartMin = substr($meetingArr['starttime'], 14, 2);
$EndHr = substr($meetingArr['endtime'], 11, 2);
$EndMin = substr($meetingArr['endtime'], 14, 2);
$retArr['meetstartday'] = $StartDate;
$retArr['meetstarthour'] = $StartHr;
$retArr['meetstartmin'] = $StartMin;
$retArr['meetendhour'] = $EndHr;
$retArr['meetendmin'] = $EndMin;
$retArr['meettype'] = $meetingArr['meetingtypeid'];
$Attendees = getAttendeesForMeetings($MeetingID);
$retArr['attendees'] = $Attendees;
echo json_encode($retArr);
?>