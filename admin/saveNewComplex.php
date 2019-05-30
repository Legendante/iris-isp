<?php
session_start();
include("db.inc.php");

$ComplexArr = array();
$ComplexArr['complexname'] = pebkac($_POST['complexname'], 100, 'STRING');
$ComplexArr['complexcode'] = pebkac($_POST['complexcode'], 10, 'STRING');
$ComplexArr['complextype'] = pebkac($_POST['complextype']);
$StatusID = (isset($_POST['complexstatus'])) ? pebkac($_POST['complexstatus']) : 1;
$ComplexArr['numunits'] = pebkac($_POST['numunits']);
$ComplexArr['vendorid'] = pebkac($_POST['vendorid']);
$ComplexArr['agentid'] = pebkac($_POST['agentid']);
$ComplexArr['secagentid'] = pebkac($_POST['secagentid']);
$ComplexArr['groupid'] = pebkac($_POST['complexgroup']);
$ComplexArr['streetaddress1'] = pebkac($_POST['address1'], 100, 'STRING');
$ComplexArr['streetaddress2'] = pebkac($_POST['address2'], 100, 'STRING');
$ComplexArr['streetaddress3'] = pebkac($_POST['address3'], 100, 'STRING');
$ComplexArr['streetaddress4'] = pebkac($_POST['address4'], 100, 'STRING');
$ComplexArr['streetaddress5'] = pebkac($_POST['address5'], 100, 'STRING');
$ComplexArr['precinctid'] = pebkac($_POST['precinctid']);
$ComplexArr['suburbid'] = pebkac($_POST['suburbid']);
$ComplexArr['areaid'] = pebkac($_POST['areaid']);
$ComplexArr['cityid'] = pebkac($_POST['cityid']);
$ComplexArr['provinceid'] = pebkac($_POST['provinceid']);
$ComplexArr['countryid'] = pebkac($_POST['countryid']);
$ComplexArr['showinresults'] = 1;
$ComplexID = addComplex($ComplexArr);
$CustomerID = addCustomer(array('customername' => $ComplexArr['complexname'] . " Body Corporate"));
saveComplex($ComplexID, array('customerid' => $CustomerID));
addBilling(array('customerid' => $CustomerID, 'billingname' => $ComplexArr['complexname'] . " Body Corporate"));
$ComplexStatusID = addComplexStatus($ComplexID, $StatusID, $_SESSION['userid']);
addComplexStatusComment($ComplexStatusID, "Complex Inserted", $_SESSION['userid']);

for($i = 1; $i <= $ComplexArr['numunits']; $i++)
{	
	addComplexUnitMap($ComplexID, $i);
}
header("Location: complex.php?cid=" . $ComplexID);
?>