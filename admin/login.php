<?php
session_start();
include("db.inc.php");

$Username = trim(pebkac($_POST['username'], 50, 'STRING'));
$Password = trim(pebkac($_POST['userpass'], 50, 'STRING'));
// echo $Password . "<br>";
$selQry = 'SELECT userid, username, userpass, firstname, surname, customerid FROM userdetails WHERE inactive IN (0,2) AND LOWER(username) = "' . strtolower($Username) . '"';
$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
$selData = mysqli_fetch_array($selRes);
if($selData['userid'] != '')	// Success - Log them in
{
	// echo hashPassword($selData['username'], $Password) . "<br>";
	$HashPw = saltAndPepper($selData['username'], $Password);
	// echo $HashPw . " - " . $selData['userpass'] . "<br>";
	// print_r($selData);
	if(password_verify($HashPw, $selData['userpass']))
	{
		// exit("<br>Verified");
		$_SESSION['userid'] = $selData['userid'];
		$_SESSION['username'] = $selData['username'];
		$_SESSION['firstname'] = $selData['firstname'];
		$_SESSION['surname'] = $selData['surname'];
		$_SESSION['customerid'] = $selData['customerid'];
		header("Location: dashboard.php");
		exit();
	}
	else
		$_SESSION['errmsg'] = "Login failed";
}
else
	$_SESSION['errmsg'] = "Login failed";
// print_r($_SESSION);
// exit();
header("Location: index.php");
?>