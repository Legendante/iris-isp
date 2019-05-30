<?php
include("db.inc.php");
$ComplexArr = array();
$ComplexID = pebkac($_POST['ComplexID']); 
$ComplexArr['complexname'] = pebkac($_POST['complexname'], 100, 'STRING');
$ComplexArr['complexcode'] = pebkac($_POST['complexcode'], 10, 'STRING');
$ComplexArr['complextype'] = pebkac($_POST['complextype']);
$ComplexArr['numunits'] = pebkac($_POST['numunits']);
$ComplexArr['vendorid'] = pebkac($_POST['vendorid']);
$ComplexArr['agentid'] = pebkac($_POST['agentid']);
$ComplexArr['secagentid'] = pebkac($_POST['secagentid']);
$ComplexArr['macontact'] = pebkac($_POST['macontact'], 100, 'STRING');
$ComplexArr['maid'] = pebkac($_POST['maagentid']);
$ComplexArr['maemail'] = pebkac($_POST['maemail'], 100, 'STRING');
$ComplexArr['macell'] = pebkac($_POST['macell'], 100, 'STRING');
$ComplexArr['seccontact'] = pebkac($_POST['seccontact'], 100, 'STRING');
$ComplexArr['seccompid'] = pebkac($_POST['seccompid']);
$ComplexArr['secemail'] = pebkac($_POST['secemail'], 100, 'STRING');
$ComplexArr['seccell'] = pebkac($_POST['seccell'], 100, 'STRING');
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
$ComplexArr['groupid'] = pebkac($_POST['complexgroup']);
$ComplexArr['kickoff'] = pebkac($_POST['kickoff'], 10, 'STRING');
// $ComplexArr['showinresults'] = 1;
if(($_POST['subdomain'] != '-') && ($_POST['subdomain'] != ''))
{
	$SubD = pebkac($_POST['subdomain'], 50, 'STRING');
	$SubDComplex = getComplexBySubdomain($SubD);
	if((!isset($SubDComplex['complexid'])) || ($SubDComplex['complexid'] == ''))
		$ComplexArr['subdomain'] = $SubD;
}
saveComplex($ComplexID, $ComplexArr);

$BillingID = pebkac($_POST['BillingID']);
$BillingArr['billingname'] = pebkac($_POST['billingname'], 100, 'STRING');
$BillingArr['billingcontact'] = pebkac($_POST['billingcontact'], 100, 'STRING');
$BillingArr['billingemail'] = pebkac($_POST['billingemail'], 100, 'STRING');
$BillingArr['billingcell'] = pebkac($_POST['billingcell'], 100, 'STRING');

saveBilling($BillingID, $BillingArr);

header("Location: complex.php?cid=" . $ComplexID);
?>