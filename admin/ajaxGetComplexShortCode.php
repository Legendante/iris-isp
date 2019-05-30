<?php
session_start();
include("db.inc.php");

$ComplexName = strtoupper(pebkac($_POST['cname'], 100, 'STRING'));
if(substr($ComplexName, 0, 4) == 'THE ')
	$ComplexName = trim(substr($ComplexName, 4));
$ComplexName = str_replace(" ", "", $ComplexName);
$NotFound = 1;
$Cnt = 0;

// A
// E
// I
// O
// U
// AE
// AI
// A0
// AU
// EI
// EO
// EU
// IO
// IU
// OU
// AEI
// AEO
// AEU
// EIO


// Bell
// BllA
while($NotFound == 1)
{
	switch($Cnt)
	{
		case 0:
			$ShortCode = substr($ComplexName, 0, 4);
			break;
		case 1:
			$TmpName = str_replace("A", "", $ComplexName);
			$ShortCode = substr($TmpName, 0, 4);
			break;
		case 2:
			$TmpName = str_replace("E", "", $ComplexName);
			$ShortCode = substr($TmpName, 0, 4);
			break;
		case 3:
			$TmpName = str_replace("I", "", $ComplexName);
			$ShortCode = substr($TmpName, 0, 4);
			break;
		case 4:
			$TmpName = str_replace("O", "", $ComplexName);
			$ShortCode = substr($TmpName, 0, 4);
			break;
		case 5:
			$TmpName = str_replace("U", "", $ComplexName);
			$ShortCode = substr($TmpName, 0, 4);
			break;
		default:
			$ShortCode = "";
			$NotFound = 0;
			break;
	}
	$CompRec = getComplexByShortCode($ShortCode);
	if(!isset($CompRec['complexid']))
	{
		$NotFound = 0;
	}
	$Cnt++;
}
echo $ShortCode;
?>