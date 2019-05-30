<?php
session_start();
include("db.inc.php");

$Username = "jacques@legendante.com";
$Password = "jksicoe123";

echo hashPassword($Username, $Password);
?>