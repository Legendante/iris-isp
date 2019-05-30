<?php
session_start();
include("db.inc.php");
// include("iris.inc.php");
// include("iriscomms.inc.php");
// print_r($_POST);
// exit();
$VendorONTs = getONTTypes();
$PropertyType = pebkac($_POST['proptype']);
$PackageID = pebkac($_POST['packageid']);
$RequireTP = pebkac($_POST['ftpreq']);
$SpeedID = pebkac($_POST['packspeed']);
$ONTID = pebkac($_POST['packont']);
$OwnerOrTenant = pebkac($_POST['owntenant']);
$Email = pebkac($_POST['regemail'], 100, 'STRING');

if($RequireTP == 2)
{
	$SpeedID = 0;
	$ONTID = 0;
}

$packQry = 'SELECT packageid, termnum, vendorid, connectcost, monthlycost, prevatsalesprice FROM fibrepackages WHERE termnum IN (0,1) AND speedid = ' . $SpeedID . ' AND ontid = ' . $ONTID;
$updRes = mysqli_query($dbCon, $packQry) or logDBError(mysqli_error($dbCon), $packQry, __FILE__, __FUNCTION__, __LINE__);
$updData = mysqli_fetch_array($updRes);
$PackageID = $updData['packageid'];
// print_r($_POST);
// exit();
// if($RequireTP == 2)
	// $PackageID = 0; // Set it to the TP only package
$CustomerID = getCustomerByEmail($Email); // Search for customer by email (and id number??)
$CustomerID = ($CustomerID == '') ? 0 : $CustomerID;
$Customer = array();
$Customer['customername'] = pebkac($_POST['regname'], 100, 'STRING');
$Customer['customersurname'] = pebkac($_POST['regsurname'], 100, 'STRING');
$Customer['idnumber'] = pebkac($_POST['regidnum'], 100, 'STRING');
$Customer['email1'] = pebkac($_POST['regemail'], 100, 'STRING');
$Customer['cell1'] = pebkac($_POST['regcell'], 100, 'STRING');
$Customer['tel1'] = pebkac($_POST['regtel'], 100, 'STRING');
if($CustomerID == 0)
{
	$GenPass = getPassword();
	$GenEmail = $Customer['email1'];
	$Customer['userpass'] = hashPassword($Customer['email1'], $GenPass);
	$CustomerID = addCustomer($Customer);
}
else
{
	header("Location: accountexists.php");
	exit();
}
if($PropertyType == 0)
{
	$Complex = array();
	$ComplexID = pebkac($_POST['complexid']); 
	$UnitNum = pebkac($_POST['unitnum'], 100, 'STRING');
	$ComplexRec = array();
	if($ComplexID == 0) // Could not identify complex. Make a plan
	{
		$ComplexArr = array();
		$ComplexArr['complexname'] = pebkac($_POST['complexname'], 100, 'STRING');
		$ComplexArr['showinresults'] = 0;
		$ComplexID = addComplex($ComplexArr);
		$ComplexRec['agentid'] = 0;
	}
	else
		$ComplexRec = getComplexByID($ComplexID);
	$UnitNumber = pebkac($_POST['unitnum'], 100, 'STRING');
}
else
{
	if($ComplexID == 0)
	{
		$ComplexArr = array();
		$ComplexArr['complexname'] = pebkac($_POST['street_number'] . " " . $_POST['locality'], 100, 'STRING');
		$ComplexArr['unitnumber'] = pebkac($_POST['street_number'], 100, 'STRING');
		$ComplexArr['streetaddress1'] = pebkac($_POST['locality'], 100, 'STRING');
		$ComplexArr['streetaddress2'] = pebkac($_POST['administrative_area_level_1'], 100, 'STRING');
		$ComplexArr['streetaddress5'] = pebkac($_POST['postal_code'], 100, 'STRING');
		$ComplexID = addComplex($ComplexArr);
		$ComplexRec['agentid'] = 0;
		$UnitNumber = $ComplexArr['unitnumber'];
	}
}
$UnitID = getUnitByComplexAndUnitNum($ComplexID, $UnitNumber);
if($UnitID == 0)
{
	$Unit = array();
	$Unit['unitowner'] = $OwnerOrTenant;
	$Unit['complexid'] = $ComplexID;
	$Unit['unitnumber'] = $UnitNumber;
	$Unit['packageid'] = $PackageID;
	$Unit['agentid'] = $ComplexRec['agentid'];
	$Unit['customerid'] = $CustomerID;
	if($RequireTP == 2)
		$Unit['tponly'] = 1;
	$UnitID = addUnit($Unit);
}
else	// Duplicate unit registration? Upgrade? New resident? Let's check and do stuff
{
	$UnitRec = getUnitByID($UnitID);
	if($UnitRec['customerid'] != $CustomerID)
	{
		$Unit = array();
		$Unit['unitowner'] = $OwnerOrTenant;
		$Unit['complexid'] = $ComplexID;
		$Unit['unitnumber'] = $UnitNumber;
		$Unit['packageid'] = $PackageID;
		$Unit['agentid'] = $ComplexRec['agentid'];
		$Unit['customerid'] = $CustomerID;
		if($RequireTP == 2)
			$Unit['tponly'] = 1;
		$OldUnitID = $UnitID;
		$UnitID = addUnit($Unit);
		$insQry = 'INSERT INTO unitduplicates(curunitid, newunitid, curcustomerid, newcustomerid, clashwhen) VALUES (' . $OldUnitID . ', ' . $UnitID . ', ' . $UnitRec['customerid'] . ', ' . $CustomerID . ', NOW())';
		$selRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
	}
}
// $OrderArr = array(); 
// $OrderArr['unitid'] = $UnitID;
// $OrderArr['customerid'] = $CustomerID;
$OrderStatus = 0;
if(isset($ComplexRec['statusid']))
{
	if(($ComplexRec['statusid'] == 21) || ($ComplexRec['statusid'] == 22) || ($ComplexRec['statusid'] == 23))	// Order placed in a live site.
	{
		$OrderStatus = 1;
		// JKS -- Need to send email explaining delivery procedure and timeline.
	}
}
// $OrderArr['orderstatus'] = $OrderStatus;
// $OrderID = addUnitOrder($OrderArr);
// $UnitPackageID = addUnitPackage($UnitID, $PackageID, $RequireTP, $OrderID);
// $UnitPackageID = addUnitPackage($UnitID, $PackageID, $CustomerID, $OrderStatus);
$Status = 1;
$ONTCost = (isset($VendorONTs[$ONTID]['ontcost'])) ? $VendorONTs[$ONTID]['ontcost'] : 0;
$insQry = 'INSERT INTO unitorderdetails(unitid, customerid, orderdate, packageid, termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, prevatsalesprice, orderstatus, historyserial) ';
$insQry .= 'SELECT ' . $UnitID . ', ' . $CustomerID . ', NOW(), packageid, termnum, ' . $SpeedID . ', ' . $ONTID . ', vendorid, ' . $ONTCost . ', connectcost, monthlycost, prevatsalesprice, ' . $Status . ', 0 ';
$insQry .= 'FROM fibrepackages WHERE packageid = ' . $PackageID;
// echo $insQry;
$updRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
$UnitPackageID = mysqli_insert_id($dbCon);

$OrderHistArr = array(); 
$OrderHistArr['orderid'] = $UnitPackageID;
$OrderHistArr['eventdescr'] = "Order placed";
$OrderHistArr['eventcomment'] = "Customer placed an order";
$OrderHistArr['userid'] = -10;
addUnitOrderHistory($OrderHistArr);
include_once("class.phpmailer.php");
include_once("class.pop3.php");
include_once("class.smtp.php");

$mail = new PHPMailer;
//$mail->SMTPDebug = 3;                               											// Enable verbose debug output
$mail->isSMTP();                                      											// Set mailer to use SMTP
$mail->Host = $mailhost;  																		// Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               											// Enable SMTP authentication
$mail->Username = $mailusername;                							 					// SMTP username
$mail->Password = $mailpassword;                           										// SMTP password
$mail->SMTPSecure = 'tls';                            											// Enable TLS encryption, `ssl` also accepted
$mail->Port = $mailport;                                    									// TCP port to connect to
$mail->setFrom($mailusername, 'Orders');
$mail->addAddress($Customer['email1'], $Customer['customername'] . ' ' . $Customer['customersurname']);     								// Add a recipient
$mail->addReplyTo($mailusername, 'Orders');
$mail->isHTML(true);                                  											// Set email format to HTML
$mail->Subject = 'Orders :: Thank you for your order';
$PlaceHolderArr = array();
$PlaceHolderArr['[[Firstname]]'] = $Customer['customername'];
$PlaceHolderArr['[[username]]'] = $GenEmail;
$PlaceHolderArr['[[password]]'] = $GenPass;

$PackageArr = getFibrePackages(array('packageid' => $PackageID));
$VendorSpeeds = getPackageSpeeds();
// $VendorONTs = getONTTypes();
$PackageTbl = "<table border='1' cellpadding='0' cellspacing='0' width='100%' style='border: 0; height: 0; border-top: 1px solid rgba(0, 0, 0, 0.1); border-bottom: 1px solid rgba(77, 4, 140, 0.7);'>";
$PackageTbl .= "<tr><th>Package</th><th>Speed</th><th>Term</th><th>ONT</th><th>Monthly Cost</th><th>ONT Cost</th><th>Connection Fee</th></tr>";
$PackageTbl .= "<tr><td>" . $PackageArr[$PackageID]['packagename'] . "</td>";
$PackageTbl .= "<td>" . $VendorSpeeds[$PackageArr[$PackageID]['speedid']] . "</td>";
$Term = ($PackageArr[$PackageID]['termnum'] == 1) ? "Month to month" : $PackageArr[$PackageID]['termnum'] . " months";
$PackageTbl .= "<td>" . $Term . "</td>";
$PackageTbl .= "<td>" . $VendorONTs[$PackageArr[$PackageID]['ontid']]['ontname'] . "</td>";
$PackageTbl .= "<td>R " . sprintf("%0.2d", $PackageArr[$PackageID]['monthlycost']) . "</td>";
$PackageTbl .= "<td>R " . sprintf("%0.2d", $PackageArr[$PackageID]['ontcost']) . "</td>";
$PackageTbl .= "<td>R " . sprintf("%0.2d", $PackageArr[$PackageID]['connectcost']) . "</td>";
$PackageTbl .= "</tr></table>";
$PlaceHolderArr['[[packageorder]]'] = $PackageTbl;
$HTMLContents = file_get_contents("templates/welcome.html");
foreach($PlaceHolderArr AS $Needle => $Magnet)
{
	$HTMLContents = str_replace($Needle, $Magnet, $HTMLContents);
}
$mail->Body = $HTMLContents;
$PackageTbl = "Package: " . $PackageArr[$PackageID]['packagename'] . "\r\n";
$PackageTbl .= "Speed: " . $VendorSpeeds[$PackageArr[$PackageID]['speedid']] . "\r\n";
$Term = ($PackageArr[$PackageID]['termnum'] == 1) ? "Month to month" : $PackageArr[$PackageID]['termnum'] . " months";
$PackageTbl .= "Term: " . $Term . "\r\n";
$PackageTbl .= "ONT: " . $VendorONTs[$PackageArr[$PackageID]['ontid']]['ontname'] . "\r\n";
$PackageTbl .= "Monthly Cost: R " . sprintf("%0.2d", $PackageArr[$PackageID]['monthlycost']) . "\r\n";
$PackageTbl .= "ONT Cost: R " . sprintf("%0.2d", $PackageArr[$PackageID]['ontcost']) . "\r\n";
$PackageTbl .= "Connection Fee: R " . sprintf("%0.2d", $PackageArr[$PackageID]['connectcost']) . "\r\n";
$PlaceHolderArr['[[packageorder]]'] = $PackageTbl;
$TextContents = file_get_contents("templates/welcome.txt");
foreach($PlaceHolderArr AS $Needle => $Magnet)
{
	$TextContents = str_replace($Needle, $Magnet, $TextContents);
}
$mail->AltBody = $TextContents;
if(!$mail->send()) 
	logDBError("Failed to send client email to '" . $Customer['email1'] . "' for order num " . $UnitPackageID, "Order mail send error", __FILE__, __FUNCTION__, __LINE__);
// logDBError("Failed to send client email to '" . $Customer['email1'] . "' for order num " . $UnitPackageID, $mail->getError(), __FILE__, __FUNCTION__, __LINE__);
// Mail em - JKS
header("Location: thankyou.php");
?>