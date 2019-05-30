<?php
session_start();
include_once("../db.inc.php");
$SaveArr = array();
$SaveArr['customername'] = pebkac($_POST['custname'], 100, 'STRING');
$SaveArr['customersurname'] = pebkac($_POST['custsurname'], 100, 'STRING');
$SaveArr['cell1'] = pebkac($_POST['custcell'], 30, 'STRING');
$SaveArr['tel1'] = pebkac($_POST['custtel'], 30, 'STRING');
saveCustomer($_SESSION['customerid'], $SaveArr);
header("Location: dashboard.php");
?>