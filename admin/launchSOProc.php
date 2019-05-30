<?php
include("db.inc.php");
$ComplexID = pebkac($_GET['cid']);
createComplexSalesOperationsSteps($ComplexID); 
$StepID = getSalesOperationsStepByName("Lead");
setComplexSalesOperationsStepsDate($ComplexID, $StepID['stepid']);
header("Location: complex.php?cid=" . $ComplexID);
?>