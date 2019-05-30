<?php
include("header.inc.php");

$MailItems = getUserMailbox($_SESSION['userid']);
$MailCount = count($MailItems);
?>
<div class='row'>
	<div class='col-md-12'>
	<table class='table table-bordered'>
	<tr><th>Subject</th><th>From</th><th>Sent</th><th>Priority</th><th><button type='button' class='btn btn-primary pull-right'><i class='fa fa-pencil'></i></th></tr>
<?php
if($MailCount == 0)
	echo "<tr><td colspan='4'>No mailbox items</td></tr>";
else
{
	foreach($MailItems AS $MailID => $MailRec)
	{
		echo "<tr>";
		echo "<td>" . $MailRec['subject'] . "</td>";
		echo "<td>" . $MailRec['senderid'] . "</td>";
		echo "<td>" . $MailRec['sentwhen'] . "</td>";
		echo "<td>" . $MailRec['priority'] . "</td>";
		echo "</tr>";
	}
	echo "</table>";
}
?>
	</div>
</div>
