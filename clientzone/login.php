<?php
session_start();
include("../db.inc.php");

$Username = trim(pebkac($_POST['u'], 50, 'STRING'));
$Password = trim(pebkac($_POST['p'], 50, 'STRING'));
$selQry = 'SELECT customerid, customername, customersurname, email1, userpass FROM customerdetails WHERE LOWER(email1) = "' . strtolower($Username) . '"';
$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
$selData = mysqli_fetch_array($selRes);
if($selData['customerid'] != '')	// Success - Log them in
{
	// echo hashPassword($selData['username'], $Password) . "<br>";
	$HashPw = saltAndPepper($selData['email1'], $Password);
	// echo $HashPw . " - " . $selData['userpass'] . "<br>";
	// print_r($selData);
	if(password_verify($HashPw, $selData['userpass']))
	{
		$_SESSION['email'] = $selData['email1'];
		$_SESSION['firstname'] = $selData['customername'];
		$_SESSION['surname'] = $selData['customersurname'];
		$_SESSION['customerid'] = $selData['customerid'];
		header("Location: dashboard.php");
		exit();
	}
	else
		$_SESSION['errmsg'] = "Login failed";
}
else
	$_SESSION['errmsg'] = "Login failed";
header("Location: index.php");
?>