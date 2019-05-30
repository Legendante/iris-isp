<?php
session_start();
include("db.inc.php");

$MapID = pebkac($_POST['mid']);
deleteComplexMapUnit($MapID);
?>