<?php
session_start();
include("db.inc.php");

$SaveArr = array();
$RecipientsArr = $_POST['mailto'];
$SaveArr['senderid'] = $_SESSION['userid'];
$SaveArr['subject'] = pebkac($_POST['mailsubject'], 200, 'STRING');
$SaveArr['msgbody'] = pebkac(trim($_POST['mailbody']), 1000, 'STRING');
$SaveArr['threadid'] = pebkac($_POST['replyToThreadID']);
foreach($RecipientsArr AS $Ind => $Mailto)
{
	$SaveArr['receiverid'] = $Mailto;
	addMailboxItem($SaveArr);
}
header("Location: " . $_SERVER['HTTP_REFERER']);
?>