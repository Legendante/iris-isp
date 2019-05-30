<?php
session_start();
include("db.inc.php");
$UnreadCount = getUserUnreadMailboxItemCount($_SESSION['userid']);
echo json_encode(array("count" => $UnreadCount));
?>