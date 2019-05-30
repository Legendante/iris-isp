<?php
// ComplexID > 59

session_start();
include("db.inc.php");

$selQry = 'SELECT `Site Name`,`Solution Type`,`SiteType`,`SiteType2`,`Latitude`,`Longitude`,`House No.`,`Street`,`Precinct`,`Area`,`City`,`Province`,`Total Units`,`Site Status`,`SiteTypeID`,`id`,`processed`,`sysid` ';
$selQry .= 'FROM ImportLive';
$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
$retArr = array();
while($selData = mysqli_fetch_array($selRes))
{
	$ImpID = $selData['id'];
	$ComplexName = strtoupper($selData['Site Name']);
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
	$ComplexArr = array();
	$ComplexArr['complexname'] = $selData['Site Name'];
	$ComplexArr['complexcode'] = $ShortCode;
	$ComplexArr['complextype'] = $selData['SiteTypeID'];
	$StatusID = 21;
	$ComplexArr['numunits'] = $selData['Total Units'];
	$ComplexArr['vendorid'] = 1;
	$ComplexArr['agentid'] = 3;
	$ComplexArr['secagentid'] = 2;
	$ComplexArr['streetaddress1'] = $selData['House No.'];
	$ComplexArr['streetaddress2'] = $selData['Street'];
	$ComplexArr['streetaddress3'] = $selData['Precinct'];
	$ComplexArr['streetaddress4'] = $selData['Area'];
	$ComplexArr['cityid'] = 6;
	$ComplexArr['provinceid'] = 1;
	$ComplexID = addComplex($ComplexArr);
	$updQry = 'UPDATE ImportLive SET sysid = ' . $ComplexID . ' WHERE id = ' . $ImpID;
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	$CustomerID = addCustomer(array('customername' => $ComplexArr['complexname'] . " Body Corporate"));
	saveComplex($ComplexID, array('customerid' => $CustomerID));
	addBilling(array('customerid' => $CustomerID, 'billingname' => $ComplexArr['complexname'] . " Body Corporate"));
	$ComplexStatusID = addComplexStatus($ComplexID, $StatusID, 2);
	addComplexStatusComment($ComplexStatusID, "Complex Imported", 2);
	for($i = 1; $i <= $ComplexArr['numunits']; $i++)
	{	
		addComplexUnitMap($ComplexID, $i);
	}
	echo "Imported " . $selData['Site Name'] . "<br>\n";
}
echo "Done";
?>