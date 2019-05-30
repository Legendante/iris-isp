<?php
session_start();
include("db.inc.php");
$Users = getUsers();
$MailItems = getUserMailbox($_SESSION['userid']);
$MailCount = count($MailItems);
if($MailCount == 0)
	echo "<tr><td colspan='5'><strong>No mailbox items</strong></td></tr>";
else
{
	foreach($MailItems AS $MailID => $MailRec)
	{
		$Unread = '';
		$HiPriority = '';
		$GabiMsg = 'bg-primary text-primary';
		if($MailRec['openedwhen'] == '')
			$Unread = 'font-bold bg-info';
		if($MailRec['priority'] >= 5)
		{
			$HiPriority = 'bg-danger';
			$GabiMsg = '';
		}
		$FromUser = '<strong>G<small>ABI</small></strong>';
		
		if($MailRec['senderid'] != 0)
		{
			$FromUser = $Users[$MailRec['senderid']]['firstname'] . " " . $Users[$MailRec['senderid']]['surname'];
			$GabiMsg = '';
		}
		echo "<tr class='" . $Unread . " " . $GabiMsg . " " . $HiPriority . "' id='mailrow_" . $MailID . "'>";
		echo "<td onclick='openMailItem(" . $MailID . ");'>" . $MailRec['subject'] . "</td>";
		
		echo "<td>" . $FromUser . "</td>";
		echo "<td>" . $MailRec['sentwhen'] . "</td>";
		echo "<td>" . $MailRec['priority'] . "</td>";
		echo "<td><button type='button' class='btn btn-success btn-sm' onclick='openMailItem(" . $MailID . ");'><i class='fa fa-eye'></i> Read</button></td>";
		echo "<td><button type='button' class='btn btn-danger btn-sm' onclick='deleteMailItem(" . $MailID . ");'><i class='fa fa-times'></i> Delete</button></td>";
		echo "</tr>";
	}
	echo "";
}
?>