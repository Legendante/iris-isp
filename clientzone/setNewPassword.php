<?php
session_start();
include("../db.inc.php");
$ResetID = $_SESSION['resetcustomerid'];
$CustomerID = $_SESSION['resetcustomerid'];
$GenPass = pebkac($_POST['p'], 50, 'STRING');
$Customer = getCustomerByID($CustomerID);
$NewPass = hashPassword($Customer['email1'], $GenPass);
$CustomerArr = array('userpass' => $NewPass);
saveCustomer($CustomerID, $CustomerArr);
$_SESSION['email'] = $Customer['email1'];
$_SESSION['firstname'] = $Customer['customername'];
$_SESSION['surname'] = $Customer['customersurname'];
$_SESSION['customerid'] = $Customer['customerid'];
header("Location: dashboard.php");
?>