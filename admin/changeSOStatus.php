<?php
include("db.inc.php");
$ComplexID = pebkac($_GET['cid']);
$StepID = pebkac($_GET['sid']);
$Cancel = (isset($_GET['cancel'])) ? pebkac($_GET['cancel']) : 0;
if($Cancel == 1)
	cancelComplexSalesOperationStep($ComplexID, $StepID);
else
	updateComplexSalesOperationStep($ComplexID, $StepID);
header("Location: dashboard.php");
?>