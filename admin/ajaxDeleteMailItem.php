<?php
session_start();
include("db.inc.php");
$MailID = pebkac($_POST['mid']);
deleteMailItem($MailID);
echo "";
?>