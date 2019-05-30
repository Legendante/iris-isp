<?php
include("db.inc.php");

$ComplexArr = array();
// $ComplexID = pebkac($_POST['ComplexID']); 
// pebkac($_POST['complexname'], 100, 'STRING');

$ComplexName = strtoupper($_POST['complexName']);
if(substr($ComplexName, 0, 4) == 'THE ')
	$ComplexName = trim(substr($ComplexName, 4));
$ComplexName = str_replace(" ", "", $ComplexName);
$NotFound = 1;
$Cnt = 0;
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
$ComplexArr['complexname'] = $ComplexName;
$ComplexArr['complexcode'] = $ShortCode;
$ComplexArr['complextype'] = 1;
$ComplexArr['numunits'] = pebkac($_POST['numunits']);
// $ComplexArr['vendorid'] = pebkac($_POST['vendorid']);
// $ComplexArr['agentid'] = pebkac($_POST['agentid']);
// $ComplexArr['secagentid'] = pebkac($_POST['secagentid']);
$ComplexArr['macontact'] = pebkac($_POST['macontact'], 100, 'STRING');
// $ComplexArr['maid'] = pebkac($_POST['maagentid']);
$ComplexArr['maemail'] = pebkac($_POST['maemail'], 100, 'STRING');
$ComplexArr['macell'] = pebkac($_POST['macell'], 100, 'STRING');
$ComplexArr['streetaddress1'] = pebkac($_POST['address1'], 100, 'STRING');
$ComplexArr['streetaddress2'] = pebkac($_POST['address2'], 100, 'STRING');
$ComplexArr['streetaddress3'] = pebkac($_POST['address3'], 100, 'STRING');
$ComplexArr['streetaddress4'] = pebkac($_POST['address4'], 100, 'STRING');
$ComplexArr['streetaddress5'] = pebkac($_POST['address5'], 100, 'STRING');
$StatusID = 24;
// $ComplexArr['precinctid'] = pebkac($_POST['precinctid']);
// $ComplexArr['suburbid'] = pebkac($_POST['suburbid']);
// $ComplexArr['areaid'] = pebkac($_POST['areaid']);
// $ComplexArr['cityid'] = pebkac($_POST['cityid']);
// $ComplexArr['provinceid'] = pebkac($_POST['provinceid']);
// $ComplexArr['countryid'] = pebkac($_POST['countryid']);
// $ComplexArr['groupid'] = pebkac($_POST['complexgroup']);
$ComplexID = addComplex($ComplexArr);
$ComplexStatusID = addComplexStatus($ComplexID, $StatusID, 0);
$RegisterArr = array();
$RegisterArr['customername'] = pebkac($_POST['custname'], 100, 'STRING');
$RegisterArr['customercell'] = pebkac($_POST['custcell'], 100, 'STRING');
$RegisterArr['customeremail'] = pebkac($_POST['custemail'], 100, 'STRING');
$RegisterArr['complexid'] = $ComplexID;
addRegisterCustomer($RegisterArr);
$CustomerID = addCustomer(array('customername' => $ComplexArr['complexname'] . " Body Corporate"));
saveComplex($ComplexID, array('customerid' => $CustomerID));
$BillingID = addBilling(array('customerid' => $CustomerID, 'billingname' => $ComplexArr['complexname'] . " Body Corporate"));
// $BillingArr['billingname'] = pebkac($_POST['billingname'], 100, 'STRING');
$BillingArr['billingcontact'] = pebkac($_POST['bcname'], 100, 'STRING');
$BillingArr['billingemail'] = pebkac($_POST['bcemail'], 100, 'STRING');
$BillingArr['billingcell'] = pebkac($_POST['bccell'], 100, 'STRING');
saveBilling($BillingID, $BillingArr);
header("Location: complexRegisterThanks.php");
?>