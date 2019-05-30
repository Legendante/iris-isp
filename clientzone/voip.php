<?php
session_start();
include_once("header.inc.php");
$Voip = getCustomerVoip($_SESSION['customerid']);
if(count($Voip) == 0)
{
	echo "<div id='packages'>";
	echo "<h3>Voice over IP settings</h3>";
	echo "<div class='row'><div class='col-md-12 col-sm-12'>You do not have VOIP configured on your account</div></div>";
	
	echo "</div>";
}
else
{
	
}
?>

<?php
include_once("footer.inc.php");
?>