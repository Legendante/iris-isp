<?php
session_start();
include("db.inc.php");
$Users = getUsers();
$MailID = pebkac($_POST['mid']);
$MailItem = getMailItem($MailID);
markMailItemRead($MailID);
$ThreadItems = getItemsFromThread($MailItem['threadid'], $MailItem['senderid'], $MailItem['receiverid']);
$MailItem['fromtext'] = 'GABI';
if($MailItem['senderid'] != 0)
	$MailItem['fromtext'] = $Users[$MailItem['senderid']]['firstname'] . " " . $Users[$MailItem['senderid']]['surname'];
$MailItem['msgbody'] = nl2br($MailItem['msgbody']);
$Thread = "";
foreach($ThreadItems AS $ThreadMailID => $ThreadRec)
{
	if($ThreadRec['senderid'] == $_SESSION['userid'])
		$ThreadRecFrom = 'Me';
	else
		$ThreadRecFrom = $Users[$ThreadRec['senderid']]['firstname'] . " " . $Users[$ThreadRec['senderid']]['surname'];
	if($ThreadRec['receiverid'] == $_SESSION['userid'])
		$ThreadRecTo = 'Me';
	else
		$ThreadRecTo = $Users[$ThreadRec['receiverid']]['firstname'] . " " . $Users[$ThreadRec['receiverid']]['surname'];
	$Thread .= "<p><strong>To:</strong> " . $ThreadRecTo . "</p>";
	$Thread .= "<p><strong>From:</strong> " . $ThreadRecFrom . "</p>";
	$Thread .= "<p><strong>Sent:</strong> " . $ThreadRec['sentwhen'] . "</p>";
	$Thread .= "<p>" . $ThreadRec['msgbody'] . "</p><hr>";
}
$MailItem['thread'] = $Thread;
echo json_encode($MailItem);
?>