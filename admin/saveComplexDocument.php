<?php
session_start();
include("db.inc.php");

$ComplexID = pebkac($_POST["docComplexID"]);
$DocType = pebkac($_POST["doctype"]);
$strLen = strlen($ComplexID);
$rem = $strLen % 3;
$PadLength = 0;
if($rem > 0)
	$PadLength = $strLen + (3 - $rem);
$charStr = sprintf("%0" . $PadLength . "d", $ComplexID);
$retArr = str_split($charStr, 3);
$Path = 'uploads/complex/';
foreach($retArr AS $pName)
{
	$Path .= $pName . "/";
	if(!file_exists($Path))
	{
		if(!mkdir($Path))
		{
			logDBError("Failed to create complex upload directory", $Path, __FILE__, __FUNCTION__, __LINE__);
			exit("Failed to create complex upload directory");
		}
	}
}
$ExeFile = $ComplexID . time() . '.upl';
if(!move_uploaded_file($_FILES['complexfile']['tmp_name'], $Path . $ExeFile))
{
	logDBError("Failed to upload file", $Path, __FILE__, __FUNCTION__, __LINE__);
	exit("Failed to upload file");
}
$FileName = cleanFileName($_FILES['complexfile']['name']);
$SaveArr = array();
$SaveArr['userid'] = $_SESSION['userid'];
$SaveArr['complexid'] = $ComplexID;
$SaveArr['filename'] = $FileName;
$SaveArr['filepath'] = $Path . $ExeFile;
$SaveArr['doctype'] = $DocType;

saveComplexFile($SaveArr);
if($DocType == 1)
{
	$StepID = getSalesOperationsStepByName("Engagement letter returned");
	setComplexSalesOperationsStepsDate($ComplexID, $StepID['stepid']);
}
elseif($DocType == 2)
{
	$StepID = getSalesOperationsStepByName("MOU returned");
	setComplexSalesOperationsStepsDate($ComplexID, $StepID['stepid']);
}
elseif($DocType == 3)
{
	$StepID = getSalesOperationsStepByName("Site plan returned");
	setComplexSalesOperationsStepsDate($ComplexID, $StepID['stepid']);
}
header("Location: complex.php?cid=" . $ComplexID);
?>