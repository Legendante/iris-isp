<?php
session_start();
include("db.inc.php");

$ComplexID = pebkac($_POST['ComplexID']); 
$ResidentArr = array();
$ResidentArr['customername'] = pebkac($_POST['customername'], 100, 'STRING');
$ResidentArr['customersurname'] = pebkac($_POST['customersurname'], 100, 'STRING');
$ResidentArr['idnumber'] = pebkac($_POST['idnumber'], 30, 'STRING');
$ResidentArr['email1'] = pebkac($_POST['email1'], 100, 'STRING');
$ResidentArr['cell1'] = pebkac($_POST['cell1'], 30, 'STRING');
$ResidentArr['tel1'] = pebkac($_POST['tel1'], 30, 'STRING');
$CustomerID = addCustomer($ResidentArr);
$UnitArr = array();
$UnitArr['customerid'] = $CustomerID;
$UnitArr['complexid'] = $ComplexID;
$UnitArr['packageid'] = (isset($_POST['packageid'])) ? pebkac($_POST['packageid']) : 0;
$UnitArr['unitnumber'] = pebkac($_POST['unitnum'], 100, 'STRING');
$UnitArr['unitowner'] = pebkac($_POST['unitowner']);
$UnitStatus = (isset($_POST['sitestatus'])) ? pebkac($_POST['sitestatus']) : 0;
$UnitID = addUnit($UnitArr);
if($UnitStatus != '')
{
	$StatusID = addUnitStatus($UnitID, $UnitStatus, $_SESSION['userid']);
	addComplexStatusComment($StatusID, "Customer Created", $_SESSION['userid']);
}
header("Location: complexresidents.php?cid=" . $ComplexID);
?>