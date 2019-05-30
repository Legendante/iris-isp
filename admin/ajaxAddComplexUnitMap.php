<?php
session_start();
include("db.inc.php");

$ComplexID = pebkac($_POST['cid']);
$HowMany = pebkac($_POST['hmany']);
for($i = 0; $i < $HowMany; $i++)
{
	// echo $i . "<br>";
	addComplexMapUnit($ComplexID);
}
?>