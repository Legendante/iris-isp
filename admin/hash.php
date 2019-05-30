<?php
include_once("db.inc.php");
include_once("../iris.inc.php");
$Username = "jacques@legendante.com";
$Password = "jksicoe";
echo hashPassword($Username, $Password);
?>