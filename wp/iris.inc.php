<?php
function saltAndPepper($Username, $Password)
{
	// global PASSTHEPEPPER;
	$RetArr = array();
	$RetArr[0] = substr(PASSTHEPEPPER, 3, 12);
	$RetArr[1] = substr(PASSTHEPEPPER, 8, 13);
	return substr($Password . $Username . $RetArr[0] . $RetArr[1], 0, 70);
}

function hashPassword($Username, $Password)
{
	$Peppered = saltAndPepper($Username, $Password);
	$options = array("cost" => 14);
	return password_hash($Peppered, PASSWORD_BCRYPT, $options);
}

function getComplexTypes()
{
	global $dbCon;

	$selQry = 'SELECT typeid, typename FROM complextypes';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['typeid']] = $selData['typename'];
	}
	return $retArr;
}

function getSiteStatusses()
{
	global $dbCon;

	$selQry = 'SELECT statusid, statusname, parentid, nextstatusid FROM sitestatusses';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['statusid']]['statusid'] = $selData['statusid'];
		$retArr[$selData['statusid']]['statusname'] = $selData['statusname'];
		$retArr[$selData['statusid']]['parentid'] = $selData['parentid'];
		$retArr[$selData['statusid']]['nextstatusid'] = $selData['nextstatusid'];
	}
	return $retArr;
}

function getUnitStatusses()
{
	global $dbCon;

	$selQry = 'SELECT statusid, statusname, parentid, nextstatusid FROM unitstatusses';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['statusid']]['statusid'] = $selData['statusid'];
		$retArr[$selData['statusid']]['statusname'] = $selData['statusname'];
		$retArr[$selData['statusid']]['parentid'] = $selData['parentid'];
		$retArr[$selData['statusid']]['nextstatusid'] = $selData['nextstatusid'];
	}
	return $retArr;
}

function getAllComplexCount($ComplexType = array())
{
	global $dbCon;
	$selQry = 'SELECT COUNT(complexid) AS compcount, COALESCE(statusid, 0) AS statusid ';
	$selQry .= 'FROM complexstatus WHERE historyserial = 0 ';
	$selQry .= 'GROUP BY statusid';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['statusid']] = $selData['compcount'];
	}
	return $retArr;
}

function getAllComplexes($StatusID = '', $ComplexType = array(), $Limit = '', $Offset = 0)
{
	global $dbCon;
	$StatArr = array();
	if($StatusID == 9999)
		$StatArr[] = 0;
	elseif($StatusID != '')
	{
		$Statusses = getSiteStatusses();
		foreach($Statusses AS $StatID => $StatRec)
		{
			if(($StatusID == $StatID) || ($StatRec['parentid'] == $StatusID))
				$StatArr[] = $StatID;
		}
	}
	$selQry = 'SELECT complexdetails.complexid, complexname, complexcode, complextype, latitude, longitude, unitnumber, streetaddress1, streetaddress2, streetaddress3, streetaddress4, streetaddress5, ';
	$selQry .= 'precinctid, suburbid, areaid, cityid, provinceid, countryid, numunits, dateregistered, vendorid, agentid, secagentid, maid, macontact, macell, maemail, seccompid, seccontact, secemail, seccell, customerid, ';
	$selQry .= 'complexstatusid, statusid, datechanged, userid AS statususer, groupid, kickoff, subdomain ';
	$selQry .= 'FROM complexdetails ';
	$selQry .= 'LEFT JOIN complexstatus ON complexstatus.complexid = complexdetails.complexid AND complexstatus.historyserial = 0 ';
	$WhereClause = '';
	if(count($StatArr) > 0)
		$WhereClause = ' WHERE statusid IN (' . implode(",", $StatArr) . ') ';
	if(count($ComplexType) > 0)
	{
		if($WhereClause == '')
			$WhereClause = ' WHERE complextype IN (' . implode(",", $ComplexType) . ') ';
		else
			$WhereClause .= ' AND complextype IN (' . implode(",", $ComplexType) . ') ';
	}
	$selQry .= $WhereClause;
	$selQry .= 'ORDER BY complexname';
	if($Limit != '')
		$selQry .= ' LIMIT ' . $Limit;
	if($Offset > 0)
		$selQry .= ' OFFSET ' . $Offset;
	// echo $selQry . "<Br>";
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['complexid']]['complexid'] = $selData['complexid'];
		$retArr[$selData['complexid']]['complexname'] = $selData['complexname'];
		$retArr[$selData['complexid']]['complexcode'] = $selData['complexcode'];
		$retArr[$selData['complexid']]['complextype'] = $selData['complextype'];
		$retArr[$selData['complexid']]['latitude'] = $selData['latitude'];
		$retArr[$selData['complexid']]['longitude'] = $selData['longitude'];
		$retArr[$selData['complexid']]['unitnumber'] = $selData['unitnumber'];
		$retArr[$selData['complexid']]['streetaddress1'] = $selData['streetaddress1'];
		$retArr[$selData['complexid']]['streetaddress2'] = $selData['streetaddress2'];
		$retArr[$selData['complexid']]['streetaddress3'] = $selData['streetaddress3'];
		$retArr[$selData['complexid']]['streetaddress4'] = $selData['streetaddress4'];
		$retArr[$selData['complexid']]['streetaddress5'] = $selData['streetaddress5'];
		$retArr[$selData['complexid']]['precinctid'] = $selData['precinctid'];
		$retArr[$selData['complexid']]['suburbid'] = $selData['suburbid'];
		$retArr[$selData['complexid']]['areaid'] = $selData['areaid'];
		$retArr[$selData['complexid']]['cityid'] = $selData['cityid'];
		$retArr[$selData['complexid']]['provinceid'] = $selData['provinceid'];
		$retArr[$selData['complexid']]['countryid'] = $selData['countryid'];
		$retArr[$selData['complexid']]['numunits'] = $selData['numunits'];
		$retArr[$selData['complexid']]['dateregistered'] = $selData['dateregistered'];
		$retArr[$selData['complexid']]['vendorid'] = $selData['vendorid'];
		$retArr[$selData['complexid']]['agentid'] = $selData['agentid'];
		$retArr[$selData['complexid']]['secagentid'] = $selData['secagentid'];
		$retArr[$selData['complexid']]['complexstatusid'] = $selData['complexstatusid'];
		$retArr[$selData['complexid']]['statusid'] = $selData['statusid'];
		$retArr[$selData['complexid']]['datechanged'] = $selData['datechanged'];
		$retArr[$selData['complexid']]['kickoff'] = $selData['kickoff'];
		$retArr[$selData['complexid']]['subdomain'] = $selData['subdomain'];
		$retArr[$selData['complexid']]['statususer'] = $selData['statususer'];
		$retArr[$selData['complexid']]['maid'] = $selData['maid'];
		$retArr[$selData['complexid']]['macontact'] = $selData['macontact'];
		$retArr[$selData['complexid']]['macell'] = $selData['macell'];
		$retArr[$selData['complexid']]['maemail'] = $selData['maemail'];
		$retArr[$selData['complexid']]['seccompid'] = $selData['seccompid'];
		$retArr[$selData['complexid']]['seccontact'] = $selData['seccontact'];
		$retArr[$selData['complexid']]['seccell'] = $selData['seccell'];
		$retArr[$selData['complexid']]['secemail'] = $selData['secemail'];
		$retArr[$selData['complexid']]['customerid'] = $selData['customerid'];
		$retArr[$selData['complexid']]['groupid'] = $selData['groupid'];
	}
	return $retArr;
}

function getAgentComplexCount($AgentID)
{
	global $dbCon;
	$StatArr = array();
	$selQry = 'SELECT COUNT(complexdetails.complexid) AS compcount, COALESCE(statusid, 0) AS statusid ';
	$selQry .= 'FROM complexdetails ';
	$selQry .= 'LEFT JOIN complexstatus ON complexstatus.complexid = complexdetails.complexid AND complexstatus.historyserial = 0 ';
	$selQry .= 'WHERE agentid = ' . $AgentID . ' ';
	$selQry .= 'GROUP BY statusid';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['statusid']] = $selData['compcount'];
	}
	return $retArr;
}

function getAgentComplexes($AgentID, $StatusID = '')
{
	global $dbCon;
	$StatArr = array();
	if($StatusID != '')
	{
		$Statusses = getSiteStatusses();
		foreach($Statusses AS $StatID => $StatRec)
		{
			if(($StatusID == $StatID) || ($StatRec['parentid'] == $StatusID))
				$StatArr[] = $StatID;
		}
	}

	$selQry = 'SELECT complexdetails.complexid, complexname, complexcode, complextype, latitude, longitude, unitnumber, streetaddress1, streetaddress2, streetaddress3, streetaddress4, streetaddress5, ';
	$selQry .= 'precinctid, suburbid, areaid, cityid, provinceid, countryid, numunits, dateregistered, vendorid, agentid, secagentid, maid, macontact, macell, maemail, seccompid, seccontact, secemail, seccell, customerid, ';
	$selQry .= 'complexstatusid, statusid, datechanged, userid AS statususer, groupid, kickoff, subdomain ';
	$selQry .= 'FROM complexdetails ';
	$selQry .= 'LEFT JOIN complexstatus ON complexstatus.complexid = complexdetails.complexid AND complexstatus.historyserial = 0 ';
	$selQry .= 'WHERE agentid = ' . $AgentID . ' ';
	if(count($StatArr) > 0)
		$selQry .= ' AND statusid IN (' . implode(",", $StatArr) . ') ';
	$selQry .= 'ORDER BY complexname';
	// echo $selQry . "<Br>";
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['complexid']]['complexid'] = $selData['complexid'];
		$retArr[$selData['complexid']]['complexname'] = $selData['complexname'];
		$retArr[$selData['complexid']]['complexcode'] = $selData['complexcode'];
		$retArr[$selData['complexid']]['complextype'] = $selData['complextype'];
		$retArr[$selData['complexid']]['latitude'] = $selData['latitude'];
		$retArr[$selData['complexid']]['longitude'] = $selData['longitude'];
		$retArr[$selData['complexid']]['unitnumber'] = $selData['unitnumber'];
		$retArr[$selData['complexid']]['streetaddress1'] = $selData['streetaddress1'];
		$retArr[$selData['complexid']]['streetaddress2'] = $selData['streetaddress2'];
		$retArr[$selData['complexid']]['streetaddress3'] = $selData['streetaddress3'];
		$retArr[$selData['complexid']]['streetaddress4'] = $selData['streetaddress4'];
		$retArr[$selData['complexid']]['streetaddress5'] = $selData['streetaddress5'];
		$retArr[$selData['complexid']]['precinctid'] = $selData['precinctid'];
		$retArr[$selData['complexid']]['suburbid'] = $selData['suburbid'];
		$retArr[$selData['complexid']]['areaid'] = $selData['areaid'];
		$retArr[$selData['complexid']]['cityid'] = $selData['cityid'];
		$retArr[$selData['complexid']]['provinceid'] = $selData['provinceid'];
		$retArr[$selData['complexid']]['countryid'] = $selData['countryid'];
		$retArr[$selData['complexid']]['numunits'] = $selData['numunits'];
		$retArr[$selData['complexid']]['dateregistered'] = $selData['dateregistered'];
		$retArr[$selData['complexid']]['vendorid'] = $selData['vendorid'];
		$retArr[$selData['complexid']]['agentid'] = $selData['agentid'];
		$retArr[$selData['complexid']]['secagentid'] = $selData['secagentid'];
		$retArr[$selData['complexid']]['complexstatusid'] = $selData['complexstatusid'];
		$retArr[$selData['complexid']]['statusid'] = $selData['statusid'];
		$retArr[$selData['complexid']]['datechanged'] = $selData['datechanged'];
		$retArr[$selData['complexid']]['kickoff'] = $selData['kickoff'];
		$retArr[$selData['complexid']]['subdomain'] = $selData['subdomain'];
		$retArr[$selData['complexid']]['statususer'] = $selData['statususer'];
		$retArr[$selData['complexid']]['maid'] = $selData['maid'];
		$retArr[$selData['complexid']]['macontact'] = $selData['macontact'];
		$retArr[$selData['complexid']]['macell'] = $selData['macell'];
		$retArr[$selData['complexid']]['maemail'] = $selData['maemail'];
		$retArr[$selData['complexid']]['seccompid'] = $selData['seccompid'];
		$retArr[$selData['complexid']]['seccontact'] = $selData['seccontact'];
		$retArr[$selData['complexid']]['seccell'] = $selData['seccell'];
		$retArr[$selData['complexid']]['secemail'] = $selData['secemail'];
		$retArr[$selData['complexid']]['customerid'] = $selData['customerid'];
		$retArr[$selData['complexid']]['groupid'] = $selData['groupid'];
	}
	return $retArr;
}

function getComplexByID($ComplexID)
{
	global $dbCon;

	$selQry = 'SELECT complexdetails.complexid, complexname, complexcode, complextype, latitude, longitude, unitnumber, streetaddress1, streetaddress2, streetaddress3, streetaddress4, streetaddress5, ';
	$selQry .= 'precinctid, suburbid, areaid, cityid, provinceid, countryid, numunits, dateregistered, vendorid, agentid, secagentid, maid, macontact, macell, maemail, seccompid, seccontact, secemail, seccell, customerid, ';
	$selQry .= 'complexstatusid, statusid, datechanged, userid AS statususer, groupid, kickoff, subdomain ';
	$selQry .= 'FROM complexdetails ';
	$selQry .= 'LEFT JOIN complexstatus ON complexstatus.complexid = complexdetails.complexid AND complexstatus.historyserial = 0 ';
	$selQry .= 'WHERE complexdetails.complexid = ' . $ComplexID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr['complexid'] = $selData['complexid'];
		$retArr['complexname'] = $selData['complexname'];
		$retArr['complexcode'] = $selData['complexcode'];
		$retArr['complextype'] = $selData['complextype'];
		$retArr['latitude'] = $selData['latitude'];
		$retArr['longitude'] = $selData['longitude'];
		$retArr['unitnumber'] = $selData['unitnumber'];
		$retArr['streetaddress1'] = $selData['streetaddress1'];
		$retArr['streetaddress2'] = $selData['streetaddress2'];
		$retArr['streetaddress3'] = $selData['streetaddress3'];
		$retArr['streetaddress4'] = $selData['streetaddress4'];
		$retArr['streetaddress5'] = $selData['streetaddress5'];
		$retArr['precinctid'] = $selData['precinctid'];
		$retArr['suburbid'] = $selData['suburbid'];
		$retArr['areaid'] = $selData['areaid'];
		$retArr['cityid'] = $selData['cityid'];
		$retArr['provinceid'] = $selData['provinceid'];
		$retArr['countryid'] = $selData['countryid'];
		$retArr['numunits'] = $selData['numunits'];
		$retArr['dateregistered'] = $selData['dateregistered'];
		$retArr['vendorid'] = $selData['vendorid'];
		$retArr['agentid'] = $selData['agentid'];
		$retArr['secagentid'] = $selData['secagentid'];
		$retArr['complexstatusid'] = $selData['complexstatusid'];
		$retArr['statusid'] = $selData['statusid'];
		$retArr['datechanged'] = $selData['datechanged'];
		$retArr['statususer'] = $selData['statususer'];
		$retArr['maid'] = $selData['maid'];
		$retArr['macontact'] = $selData['macontact'];
		$retArr['macell'] = $selData['macell'];
		$retArr['maemail'] = $selData['maemail'];
		$retArr['seccompid'] = $selData['seccompid'];
		$retArr['seccontact'] = $selData['seccontact'];
		$retArr['seccell'] = $selData['seccell'];
		$retArr['secemail'] = $selData['secemail'];
		$retArr['customerid'] = $selData['customerid'];
		$retArr['groupid'] = $selData['groupid'];
		$retArr['kickoff'] = $selData['kickoff'];
		$retArr['subdomain'] = $selData['subdomain'];
	}
	return $retArr;
}

function getComplexByShortCode($ComplexCode)
{
	global $dbCon;

	$selQry = 'SELECT complexdetails.complexid, complexname, complexcode, complextype, latitude, longitude, unitnumber, streetaddress1, streetaddress2, streetaddress3, streetaddress4, streetaddress5, ';
	$selQry .= 'precinctid, suburbid, areaid, cityid, provinceid, countryid, numunits, dateregistered, vendorid, agentid, secagentid, maid, macontact, macell, maemail, seccompid, seccontact, secemail, seccell, customerid, ';
	$selQry .= 'complexstatusid, statusid, datechanged, userid AS statususer, groupid, kickoff, subdomain ';
	$selQry .= 'FROM complexdetails ';
	$selQry .= 'LEFT JOIN complexstatus ON complexstatus.complexid = complexdetails.complexid AND complexstatus.historyserial = 0 ';
	$selQry .= 'WHERE complexdetails.complexcode = "' . $ComplexCode . '"';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr['complexid'] = $selData['complexid'];
		$retArr['complexname'] = $selData['complexname'];
		$retArr['complexcode'] = $selData['complexcode'];
		$retArr['complextype'] = $selData['complextype'];
		$retArr['latitude'] = $selData['latitude'];
		$retArr['longitude'] = $selData['longitude'];
		$retArr['unitnumber'] = $selData['unitnumber'];
		$retArr['streetaddress1'] = $selData['streetaddress1'];
		$retArr['streetaddress2'] = $selData['streetaddress2'];
		$retArr['streetaddress3'] = $selData['streetaddress3'];
		$retArr['streetaddress4'] = $selData['streetaddress4'];
		$retArr['streetaddress5'] = $selData['streetaddress5'];
		$retArr['precinctid'] = $selData['precinctid'];
		$retArr['suburbid'] = $selData['suburbid'];
		$retArr['areaid'] = $selData['areaid'];
		$retArr['cityid'] = $selData['cityid'];
		$retArr['provinceid'] = $selData['provinceid'];
		$retArr['countryid'] = $selData['countryid'];
		$retArr['numunits'] = $selData['numunits'];
		$retArr['dateregistered'] = $selData['dateregistered'];
		$retArr['vendorid'] = $selData['vendorid'];
		$retArr['agentid'] = $selData['agentid'];
		$retArr['secagentid'] = $selData['secagentid'];
		$retArr['complexstatusid'] = $selData['complexstatusid'];
		$retArr['statusid'] = $selData['statusid'];
		$retArr['datechanged'] = $selData['datechanged'];
		$retArr['statususer'] = $selData['statususer'];
		$retArr['maid'] = $selData['maid'];
		$retArr['macontact'] = $selData['macontact'];
		$retArr['macell'] = $selData['macell'];
		$retArr['maemail'] = $selData['maemail'];
		$retArr['seccompid'] = $selData['seccompid'];
		$retArr['seccontact'] = $selData['seccontact'];
		$retArr['seccell'] = $selData['seccell'];
		$retArr['secemail'] = $selData['secemail'];
		$retArr['customerid'] = $selData['customerid'];
		$retArr['groupid'] = $selData['groupid'];
		$retArr['kickoff'] = $selData['kickoff'];
		$retArr['subdomain'] = $selData['subdomain'];
	}
	return $retArr;
}

function getComplexBySubdomain($SubDomain)
{
	global $dbCon;

	$selQry = 'SELECT complexdetails.complexid, complexname, complexcode, complextype, latitude, longitude, unitnumber, streetaddress1, streetaddress2, streetaddress3, streetaddress4, streetaddress5, ';
	$selQry .= 'precinctid, suburbid, areaid, cityid, provinceid, countryid, numunits, dateregistered, vendorid, agentid, secagentid, maid, macontact, macell, maemail, seccompid, seccontact, secemail, seccell, customerid, ';
	$selQry .= 'complexstatusid, statusid, datechanged, userid AS statususer, groupid, kickoff, subdomain ';
	$selQry .= 'FROM complexdetails ';
	$selQry .= 'LEFT JOIN complexstatus ON complexstatus.complexid = complexdetails.complexid AND complexstatus.historyserial = 0 ';
	$selQry .= 'WHERE complexdetails.subdomain = "' . $SubDomain . '"';
	// echo $selQry . "<Br>";
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr['complexid'] = $selData['complexid'];
		$retArr['complexname'] = $selData['complexname'];
		$retArr['complexcode'] = $selData['complexcode'];
		$retArr['complextype'] = $selData['complextype'];
		$retArr['latitude'] = $selData['latitude'];
		$retArr['longitude'] = $selData['longitude'];
		$retArr['unitnumber'] = $selData['unitnumber'];
		$retArr['streetaddress1'] = $selData['streetaddress1'];
		$retArr['streetaddress2'] = $selData['streetaddress2'];
		$retArr['streetaddress3'] = $selData['streetaddress3'];
		$retArr['streetaddress4'] = $selData['streetaddress4'];
		$retArr['streetaddress5'] = $selData['streetaddress5'];
		$retArr['precinctid'] = $selData['precinctid'];
		$retArr['suburbid'] = $selData['suburbid'];
		$retArr['areaid'] = $selData['areaid'];
		$retArr['cityid'] = $selData['cityid'];
		$retArr['provinceid'] = $selData['provinceid'];
		$retArr['countryid'] = $selData['countryid'];
		$retArr['numunits'] = $selData['numunits'];
		$retArr['dateregistered'] = $selData['dateregistered'];
		$retArr['vendorid'] = $selData['vendorid'];
		$retArr['agentid'] = $selData['agentid'];
		$retArr['secagentid'] = $selData['secagentid'];
		$retArr['complexstatusid'] = $selData['complexstatusid'];
		$retArr['statusid'] = $selData['statusid'];
		$retArr['datechanged'] = $selData['datechanged'];
		$retArr['statususer'] = $selData['statususer'];
		$retArr['maid'] = $selData['maid'];
		$retArr['macontact'] = $selData['macontact'];
		$retArr['macell'] = $selData['macell'];
		$retArr['maemail'] = $selData['maemail'];
		$retArr['seccompid'] = $selData['seccompid'];
		$retArr['seccontact'] = $selData['seccontact'];
		$retArr['seccell'] = $selData['seccell'];
		$retArr['secemail'] = $selData['secemail'];
		$retArr['customerid'] = $selData['customerid'];
		$retArr['groupid'] = $selData['groupid'];
		$retArr['kickoff'] = $selData['kickoff'];
		$retArr['subdomain'] = $selData['subdomain'];
	}
	return $retArr;
}

function getComplexesByIDList($ComplexIDList)
{
	global $dbCon;

	$selQry = 'SELECT complexdetails.complexid, complexname, complexcode, complextype, latitude, longitude, unitnumber, streetaddress1, streetaddress2, streetaddress3, streetaddress4, streetaddress5, ';
	$selQry .= 'precinctid, suburbid, areaid, cityid, provinceid, countryid, numunits, dateregistered, vendorid, agentid, secagentid, maid, macontact, macell, maemail, seccompid, seccontact, secemail, seccell, customerid, ';
	$selQry .= 'complexstatusid, statusid, datechanged, userid AS statususer, groupid, kickoff, subdomain ';
	$selQry .= 'FROM complexdetails ';
	$selQry .= 'LEFT JOIN complexstatus ON complexstatus.complexid = complexdetails.complexid AND complexstatus.historyserial = 0 ';
	$selQry .= 'WHERE complexdetails.complexid IN (' . $ComplexIDList . ') ';
	$selQry .= 'ORDER BY complexname';
	// echo $selQry . "<Br>";
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['complexid']]['complexid'] = $selData['complexid'];
		$retArr[$selData['complexid']]['complexname'] = $selData['complexname'];
		$retArr[$selData['complexid']]['complexcode'] = $selData['complexcode'];
		$retArr[$selData['complexid']]['complextype'] = $selData['complextype'];
		$retArr[$selData['complexid']]['latitude'] = $selData['latitude'];
		$retArr[$selData['complexid']]['longitude'] = $selData['longitude'];
		$retArr[$selData['complexid']]['unitnumber'] = $selData['unitnumber'];
		$retArr[$selData['complexid']]['streetaddress1'] = $selData['streetaddress1'];
		$retArr[$selData['complexid']]['streetaddress2'] = $selData['streetaddress2'];
		$retArr[$selData['complexid']]['streetaddress3'] = $selData['streetaddress3'];
		$retArr[$selData['complexid']]['streetaddress4'] = $selData['streetaddress4'];
		$retArr[$selData['complexid']]['streetaddress5'] = $selData['streetaddress5'];
		$retArr[$selData['complexid']]['precinctid'] = $selData['precinctid'];
		$retArr[$selData['complexid']]['suburbid'] = $selData['suburbid'];
		$retArr[$selData['complexid']]['areaid'] = $selData['areaid'];
		$retArr[$selData['complexid']]['cityid'] = $selData['cityid'];
		$retArr[$selData['complexid']]['provinceid'] = $selData['provinceid'];
		$retArr[$selData['complexid']]['countryid'] = $selData['countryid'];
		$retArr[$selData['complexid']]['numunits'] = $selData['numunits'];
		$retArr[$selData['complexid']]['dateregistered'] = $selData['dateregistered'];
		$retArr[$selData['complexid']]['vendorid'] = $selData['vendorid'];
		$retArr[$selData['complexid']]['agentid'] = $selData['agentid'];
		$retArr[$selData['complexid']]['secagentid'] = $selData['secagentid'];
		$retArr[$selData['complexid']]['complexstatusid'] = $selData['complexstatusid'];
		$retArr[$selData['complexid']]['statusid'] = $selData['statusid'];
		$retArr[$selData['complexid']]['datechanged'] = $selData['datechanged'];
		$retArr[$selData['complexid']]['kickoff'] = $selData['kickoff'];
		$retArr[$selData['complexid']]['subdomain'] = $selData['subdomain'];
		$retArr[$selData['complexid']]['statususer'] = $selData['statususer'];
		$retArr[$selData['complexid']]['maid'] = $selData['maid'];
		$retArr[$selData['complexid']]['macontact'] = $selData['macontact'];
		$retArr[$selData['complexid']]['macell'] = $selData['macell'];
		$retArr[$selData['complexid']]['maemail'] = $selData['maemail'];
		$retArr[$selData['complexid']]['seccompid'] = $selData['seccompid'];
		$retArr[$selData['complexid']]['seccontact'] = $selData['seccontact'];
		$retArr[$selData['complexid']]['seccell'] = $selData['seccell'];
		$retArr[$selData['complexid']]['secemail'] = $selData['secemail'];
		$retArr[$selData['complexid']]['customerid'] = $selData['customerid'];
		$retArr[$selData['complexid']]['groupid'] = $selData['groupid'];
	}
	return $retArr;
}

function addComplex($SaveArr)
{
	global $dbCon;

	$fieldA = array();
	$valueA = array();
	$cnt = 0;
	foreach($SaveArr AS $Field => $Value)
	{
		$fieldA[$cnt] = $Field;
		$valueA[$cnt] = '"' . $Value . '"';
		$cnt++;
	}
	$fieldA[$cnt] = 'dateregistered';
	$valueA[$cnt] = 'NOW()';
	$cnt++;
	$updQry = 'INSERT INTO complexdetails(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	// exit($updQry);
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function addCustomer($SaveArr)
{
	global $dbCon;

	$fieldA = array();
	$valueA = array();
	$cnt = 0;
	foreach($SaveArr AS $Field => $Value)
	{
		$fieldA[$cnt] = $Field;
		$valueA[$cnt] = '"' . $Value . '"';
		$cnt++;
	}
	if(!isset($SaveArr['dateregistered']))
	{
		$fieldA[$cnt] = 'dateregistered';
		$valueA[$cnt] = 'NOW()';
	}
	$updQry = 'INSERT INTO customerdetails(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	// exit($updQry);
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function addUnit($SaveArr)
{
	global $dbCon;

	$fieldA = array();
	$valueA = array();
	$cnt = 0;
	foreach($SaveArr AS $Field => $Value)
	{
		$fieldA[$cnt] = $Field;
		$valueA[$cnt] = '"' . $Value . '"';
		$cnt++;
	}
	$updQry = 'INSERT INTO customerunits(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	// exit($updQry);
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function addBilling($SaveArr)
{
	global $dbCon;

	$fieldA = array();
	$valueA = array();
	$cnt = 0;
	foreach($SaveArr AS $Field => $Value)
	{
		$fieldA[$cnt] = $Field;
		$valueA[$cnt] = '"' . $Value . '"';
		$cnt++;
	}
	$updQry = 'INSERT INTO customerbilling(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	// exit($updQry);
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function addUnitStatus($UnitID, $StatusID, $UserID)
{
	global $dbCon;

	$updQry = 'UPDATE unitstatus SET historyserial = historyserial + 1 WHERE unitid = ' . $UnitID;
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);

	$insQry = 'INSERT INTO unitstatus(unitid, statusid, datechanged, userid) VALUES ("' . $UnitID . '", "' . $StatusID . '", NOW(), "' . $UserID . '")';
	$updRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function addUnitStatusComment($StatusID, $Comment, $UserID)
{
	global $dbCon;

	$insQry = 'INSERT INTO unitstatuscomments(unitstatusid, datechanged, userid, commentary) VALUES ("' . $StatusID . '", NOW(), "' . $UserID . '", "' . $Comment . '")';
	// exit($insQry);
	$updRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
}

function getUnitStatusHistory($UnitID)
{
	global $dbCon;
	$selQry = 'SELECT unitstatus.unitstatusid, unitid, statusid, unitstatus.datechanged AS statusdate, unitstatus.userid AS statususer, ';
	$selQry .= 'unitstatuscomments.datechanged AS commentdate, unitstatuscomments.commentary, unitstatuscomments.userid AS commentuser ';
	$selQry .= 'FROM unitstatus ';
	$selQry .= 'LEFT JOIN unitstatuscomments ON unitstatuscomments.unitstatusid = unitstatus.unitstatusid ';
	$selQry .= 'WHERE unitid = ' . $UnitID . ' ';
	$selQry .= 'ORDER BY unitstatus.datechanged ASC, unitstatuscomments.datechanged ASC';
	// echo $selQry . "<br>";
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	$cnt = 0;
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$cnt]['unitstatusid'] = $selData['unitstatusid'];
		$retArr[$cnt]['unitid'] = $selData['unitid'];
		$retArr[$cnt]['statusid'] = $selData['statusid'];
		$retArr[$cnt]['statusdate'] = $selData['statusdate'];
		$retArr[$cnt]['statususer'] = $selData['statususer'];
		$retArr[$cnt]['commentdate'] = $selData['commentdate'];
		$retArr[$cnt]['commentary'] = $selData['commentary'];
		$retArr[$cnt]['commentuser'] = $selData['commentuser'];
		$cnt++;
	}
	return $retArr;
}

function addComplexStatus($ComplexID, $StatusID, $UserID)
{
	global $dbCon;

	$updQry = 'UPDATE complexstatus SET historyserial = historyserial + 1 WHERE complexid = ' . $ComplexID;
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);

	$insQry = 'INSERT INTO complexstatus(complexid, statusid, datechanged, userid) VALUES ("' . $ComplexID . '", "' . $StatusID . '", NOW(), "' . $UserID . '")';
	$updRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function addComplexStatusComment($StatusID, $Comment, $UserID)
{
	global $dbCon;

	$insQry = 'INSERT INTO complexstatuscomments(complexstatusid, datechanged, userid, commentary) VALUES ("' . $StatusID . '", NOW(), "' . $UserID . '", "' . $Comment . '")';
	// exit($insQry);
	$updRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
}

function getComplexStatusHistory($ComplexID)
{
	global $dbCon;
	$selQry = 'SELECT complexstatus.complexstatusid, complexid, statusid, complexstatus.datechanged AS statusdate, complexstatus.userid AS statususer, ';
	$selQry .= 'complexstatuscomments.datechanged AS commentdate, complexstatuscomments.commentary, complexstatuscomments.userid AS commentuser ';
	$selQry .= 'FROM complexstatus ';
	$selQry .= 'LEFT JOIN complexstatuscomments ON complexstatuscomments.complexstatusid = complexstatus.complexstatusid ';
	$selQry .= 'WHERE complexid = ' . $ComplexID . ' ';
	$selQry .= 'ORDER BY complexstatus.datechanged, complexstatuscomments.datechanged';
	// echo $selQry . "<br>";
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	$cnt = 0;
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$cnt]['complexstatusid'] = $selData['complexstatusid'];
		$retArr[$cnt]['complexid'] = $selData['complexid'];
		$retArr[$cnt]['statusid'] = $selData['statusid'];
		$retArr[$cnt]['statusdate'] = $selData['statusdate'];
		$retArr[$cnt]['statususer'] = $selData['statususer'];
		$retArr[$cnt]['commentdate'] = $selData['commentdate'];
		$retArr[$cnt]['commentary'] = $selData['commentary'];
		$retArr[$cnt]['commentuser'] = $selData['commentuser'];
		$cnt++;
	}
	return $retArr;
}

function saveComplex($ComplexID, $SaveArr)
{
	global $dbCon;

	$updStr = '';
	foreach($SaveArr AS $Field => $Value)
	{
		if($updStr != '')
			$updStr .= ', ';
		$updStr .= $Field . ' = "' . $Value . '"';
	}
	$updQry = 'UPDATE complexdetails SET ' . $updStr . ' WHERE complexid = ' . $ComplexID;
	// exit($updQry);
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}

function saveBilling($BillingID, $SaveArr)
{
	global $dbCon;

	$updStr = '';
	foreach($SaveArr AS $Field => $Value)
	{
		if($updStr != '')
			$updStr .= ', ';
		$updStr .= $Field . ' = "' . $Value . '"';
	}

	$updQry = 'UPDATE customerbilling SET ' . $updStr . ' WHERE billingid = ' . $BillingID;
	$selRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}

function saveCustomer($CustomerID, $SaveArr)
{
	global $dbCon;

	$updStr = '';
	foreach($SaveArr AS $Field => $Value)
	{
		if($updStr != '')
			$updStr .= ', ';
		$updStr .= $Field . ' = "' . $Value . '"';
	}

	$updQry = 'UPDATE customerdetails SET ' . $updStr . ' WHERE customerid = ' . $CustomerID;
	$selRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}

function getCustomerByID($CustomerID)
{
	global $dbCon;

	$selQry = 'SELECT customerdetails.customerid, customername, customersurname, idnumber, email1, email2, cell1, cell2, tel1, tel2, dateregistered, customernumber, ';
	$selQry .= 'billingid, billingname, billingcontact, billingemail, billingcell, unitnumber, streetaddress1, streetaddress2, streetaddress3, streetaddress4, streetaddress5 ';
	$selQry .= 'FROM customerdetails ';
	$selQry .= 'LEFT JOIN customerbilling ON customerbilling.customerid = customerdetails.customerid ';
	$selQry .= 'WHERE customerdetails.customerid = ' . $CustomerID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr['customerid'] = $selData['customerid'];
		$retArr['customername'] = $selData['customername'];
		$retArr['customersurname'] = $selData['customersurname'];
		$retArr['idnumber'] = $selData['idnumber'];
		$retArr['email1'] = $selData['email1'];
		$retArr['email2'] = $selData['email2'];
		$retArr['cell1'] = $selData['cell1'];
		$retArr['cell2'] = $selData['cell2'];
		$retArr['tel1'] = $selData['tel1'];
		$retArr['tel2'] = $selData['tel2'];
		$retArr['dateregistered'] = $selData['dateregistered'];
		$retArr['customernumber'] = $selData['customernumber'];
		$retArr['billingid'] = $selData['billingid'];
		$retArr['billingname'] = $selData['billingname'];
		$retArr['billingcontact'] = $selData['billingcontact'];
		$retArr['billingemail'] = $selData['billingemail'];
		$retArr['billingcell'] = $selData['billingcell'];
		$retArr['unitnumber'] = $selData['unitnumber'];
		$retArr['streetaddress1'] = $selData['streetaddress1'];
		$retArr['streetaddress2'] = $selData['streetaddress2'];
		$retArr['streetaddress3'] = $selData['streetaddress3'];
		$retArr['streetaddress4'] = $selData['streetaddress4'];
		$retArr['streetaddress5'] = $selData['streetaddress5'];
	}
	return $retArr;
}

function getCustomersByIDList($CustomerIDList)
{
	global $dbCon;

	$selQry = 'SELECT customerdetails.customerid, customername, customersurname, idnumber, email1, email2, cell1, cell2, tel1, tel2, dateregistered, customernumber, ';
	$selQry .= 'billingid, billingname, billingcontact, billingemail, billingcell, unitnumber, streetaddress1, streetaddress2, streetaddress3, streetaddress4, streetaddress5 ';
	$selQry .= 'FROM customerdetails ';
	$selQry .= 'LEFT JOIN customerbilling ON customerbilling.customerid = customerdetails.customerid ';
	$selQry .= 'WHERE customerdetails.customerid IN (' . $CustomerIDList . ')';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['customerid']]['customerid'] = $selData['customerid'];
		$retArr[$selData['customerid']]['customername'] = $selData['customername'];
		$retArr[$selData['customerid']]['customersurname'] = $selData['customersurname'];
		$retArr[$selData['customerid']]['idnumber'] = $selData['idnumber'];
		$retArr[$selData['customerid']]['email1'] = $selData['email1'];
		$retArr[$selData['customerid']]['email2'] = $selData['email2'];
		$retArr[$selData['customerid']]['cell1'] = $selData['cell1'];
		$retArr[$selData['customerid']]['cell2'] = $selData['cell2'];
		$retArr[$selData['customerid']]['tel1'] = $selData['tel1'];
		$retArr[$selData['customerid']]['tel2'] = $selData['tel2'];
		$retArr[$selData['customerid']]['dateregistered'] = $selData['dateregistered'];
		$retArr[$selData['customerid']]['customernumber'] = $selData['customernumber'];
		$retArr[$selData['customerid']]['billingid'] = $selData['billingid'];
		$retArr[$selData['customerid']]['billingname'] = $selData['billingname'];
		$retArr[$selData['customerid']]['billingcontact'] = $selData['billingcontact'];
		$retArr[$selData['customerid']]['billingemail'] = $selData['billingemail'];
		$retArr[$selData['customerid']]['billingcell'] = $selData['billingcell'];
		$retArr[$selData['customerid']]['unitnumber'] = $selData['unitnumber'];
		$retArr[$selData['customerid']]['streetaddress1'] = $selData['streetaddress1'];
		$retArr[$selData['customerid']]['streetaddress2'] = $selData['streetaddress2'];
		$retArr[$selData['customerid']]['streetaddress3'] = $selData['streetaddress3'];
		$retArr[$selData['customerid']]['streetaddress4'] = $selData['streetaddress4'];
		$retArr[$selData['customerid']]['streetaddress5'] = $selData['streetaddress5'];
	}
	return $retArr;
}

function getCustomerByEmail($Email)
{
	global $dbCon;

	$selQry = 'SELECT customerid FROM customerdetails WHERE email1 = "' . $Email . '"';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$selData = mysqli_fetch_array($selRes);
	return $selData['customerid'];
}

function getCustomerUnits($CustomerID)
{
	global $dbCon;

	$selQry = 'SELECT customerunits.unitid, customerid, complexid, unitnumber, unitowner, packageid, ';
	$selQry .= 'unitstatusid, statusid, datechanged, userid ';
	$selQry .= 'FROM customerunits ';
	$selQry .= 'LEFT JOIN unitstatus ON unitstatus.unitid = customerunits.unitid AND historyserial = 0 ';
	$selQry .= 'WHERE customerid = ' . $CustomerID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['unitid']]['unitid'] = $selData['unitid'];
		$retArr[$selData['unitid']]['customerid'] = $selData['customerid'];
		$retArr[$selData['unitid']]['complexid'] = $selData['complexid'];
		$retArr[$selData['unitid']]['unitnumber'] = $selData['unitnumber'];
		$retArr[$selData['unitid']]['unitowner'] = $selData['unitowner'];
		$retArr[$selData['unitid']]['packageid'] = $selData['packageid'];
		$retArr[$selData['unitid']]['unitstatusid'] = $selData['unitstatusid'];
		$retArr[$selData['unitid']]['statusid'] = $selData['statusid'];
		$retArr[$selData['unitid']]['datechanged'] = $selData['datechanged'];
		$retArr[$selData['unitid']]['userid'] = $selData['userid'];
	}
	return $retArr;
}

function getUnitByComplexAndUnitNum($ComplexID, $UnitNum)
{
	global $dbCon;

	$selQry = 'SELECT customerunits.unitid, customerid, complexid, unitnumber, unitowner, packageid ';
	$selQry .= 'FROM customerunits ';
	$selQry .= 'WHERE complexid = ' . $ComplexID . ' AND unitnumber = "' . $UnitNum . '"';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$selData = mysqli_fetch_array($selRes);
	return $selData['unitid'];
}

function getUnitByID($UnitID)
{
	global $dbCon;

	$selQry = 'SELECT customerunits.unitid, customerid, complexid, unitnumber, unitowner, packageid, ';
	$selQry .= 'unitstatusid, unitstatus.statusid, datechanged, userid ';
	$selQry .= 'FROM customerunits ';
	$selQry .= 'LEFT JOIN unitstatus ON unitstatus.unitid = customerunits.unitid AND historyserial = 0 ';
	$selQry .= 'WHERE customerunits.unitid = ' . $UnitID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr['unitid'] = $selData['unitid'];
		$retArr['customerid'] = $selData['customerid'];
		$retArr['complexid'] = $selData['complexid'];
		$retArr['unitnumber'] = $selData['unitnumber'];
		$retArr['unitowner'] = $selData['unitowner'];
		$retArr['packageid'] = $selData['packageid'];
		$retArr['unitstatusid'] = $selData['unitstatusid'];
		$retArr['statusid'] = $selData['statusid'];
		$retArr['datechanged'] = $selData['datechanged'];
		$retArr['userid'] = $selData['userid'];
	}
	return $retArr;
}

function getUnitsByIDList($UnitIDList)
{
	global $dbCon;

	$selQry = 'SELECT customerunits.unitid, customerid, complexid, unitnumber, unitowner, packageid, ';
	$selQry .= 'unitstatusid, unitstatus.statusid, datechanged, userid ';
	$selQry .= 'FROM customerunits ';
	$selQry .= 'LEFT JOIN unitstatus ON unitstatus.unitid = customerunits.unitid AND historyserial = 0 ';
	$selQry .= 'WHERE customerunits.unitid IN (' . $UnitIDList . ')';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['unitid']]['unitid'] = $selData['unitid'];
		$retArr[$selData['unitid']]['customerid'] = $selData['customerid'];
		$retArr[$selData['unitid']]['complexid'] = $selData['complexid'];
		$retArr[$selData['unitid']]['unitnumber'] = $selData['unitnumber'];
		$retArr[$selData['unitid']]['unitowner'] = $selData['unitowner'];
		$retArr[$selData['unitid']]['packageid'] = $selData['packageid'];
		$retArr[$selData['unitid']]['unitstatusid'] = $selData['unitstatusid'];
		$retArr[$selData['unitid']]['statusid'] = $selData['statusid'];
		$retArr[$selData['unitid']]['datechanged'] = $selData['datechanged'];
		$retArr[$selData['unitid']]['userid'] = $selData['userid'];
	}
		return $retArr;
}

function getComplexResidents($ComplexID)
{
	global $dbCon;

	$selQry = 'SELECT customerdetails.customerid, customername, customersurname, idnumber, email1, email2, cell1, cell2, tel1, tel2, dateregistered, customernumber, ';
	$selQry .= 'customerunits.unitid, complexid, unitnumber, unitowner, packageid, unitstatusid, statusid, datechanged, userid ';
	$selQry .= 'FROM customerdetails ';
	$selQry .= 'INNER JOIN customerunits ON customerunits.customerid = customerdetails.customerid ';
	$selQry .= 'LEFT JOIN unitstatus ON unitstatus.unitid = customerunits.unitid AND historyserial = 0 ';
	$selQry .= 'WHERE complexid = ' . $ComplexID . ' ';
	$selQry .= 'ORDER BY unitnumber * 1 ASC';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['unitid']]['unitid'] = $selData['unitid'];
		$retArr[$selData['unitid']]['customerid'] = $selData['customerid'];
		$retArr[$selData['unitid']]['customername'] = $selData['customername'];
		$retArr[$selData['unitid']]['customersurname'] = $selData['customersurname'];
		$retArr[$selData['unitid']]['idnumber'] = $selData['idnumber'];
		$retArr[$selData['unitid']]['email1'] = $selData['email1'];
		$retArr[$selData['unitid']]['email2'] = $selData['email2'];
		$retArr[$selData['unitid']]['cell1'] = $selData['cell1'];
		$retArr[$selData['unitid']]['cell2'] = $selData['cell2'];
		$retArr[$selData['unitid']]['tel1'] = $selData['tel1'];
		$retArr[$selData['unitid']]['tel2'] = $selData['tel2'];
		$retArr[$selData['unitid']]['packageid'] = $selData['packageid'];
		$retArr[$selData['unitid']]['dateregistered'] = $selData['dateregistered'];
		$retArr[$selData['unitid']]['customernumber'] = $selData['customernumber'];
		$retArr[$selData['unitid']]['complexid'] = $selData['complexid'];
		$retArr[$selData['unitid']]['unitnumber'] = $selData['unitnumber'];
		$retArr[$selData['unitid']]['unitowner'] = $selData['unitowner'];
		$retArr[$selData['unitid']]['unitstatusid'] = $selData['unitstatusid'];
		$retArr[$selData['unitid']]['statusid'] = $selData['statusid'];
		$retArr[$selData['unitid']]['datechanged'] = $selData['datechanged'];
		$retArr[$selData['unitid']]['userid'] = $selData['userid'];
	}
	return $retArr;
}

function getComplexResidentCounts()
{
	global $dbCon;

	$selQry = 'SELECT COUNT(customerunits.customerid) AS recount, complexid FROM customerunits GROUP BY complexid';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['complexid']] = $selData['recount'];
	}
	return $retArr;
}

function getUsers()
{
	global $dbCon;

	$selQry = 'SELECT userid, username, firstname, surname, cellnumber, telnumber, inactive FROM userdetails ORDER BY surname, firstname';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['userid']]['userid'] = $selData['userid'];
		$retArr[$selData['userid']]['username'] = $selData['username'];
		$retArr[$selData['userid']]['firstname'] = $selData['firstname'];
		$retArr[$selData['userid']]['surname'] = $selData['surname'];
		$retArr[$selData['userid']]['cellnumber'] = $selData['cellnumber'];
		$retArr[$selData['userid']]['telnumber'] = $selData['telnumber'];
		$retArr[$selData['userid']]['inactive'] = $selData['inactive'];
	}
	return $retArr;
}

function getUserByID($UserID)
{
	global $dbCon;

	$selQry = 'SELECT userid, username, firstname, surname, cellnumber, telnumber, inactive FROM userdetails WHERE userid = ' . $UserID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_fetch_array($selRes);
}

function getAgents()
{
	global $dbCon;

	$selQry = 'SELECT userid, username, firstname, surname, cellnumber, telnumber, inactive FROM userdetails ORDER BY surname, firstname';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['userid']]['userid'] = $selData['userid'];
		$retArr[$selData['userid']]['username'] = $selData['username'];
		$retArr[$selData['userid']]['firstname'] = $selData['firstname'];
		$retArr[$selData['userid']]['surname'] = $selData['surname'];
		$retArr[$selData['userid']]['cellnumber'] = $selData['cellnumber'];
		$retArr[$selData['userid']]['telnumber'] = $selData['telnumber'];
		$retArr[$selData['userid']]['inactive'] = $selData['inactive'];
	}
	return $retArr;
}

function getVendors()
{
	global $dbCon;

	$selQry = 'SELECT vendorid, vendorname FROM vendordetails ORDER BY vendorname';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['vendorid']] = $selData['vendorname'];
	}
	return $retArr;
}

function addVendor($SaveArr)
{
	global $dbCon;

	$fieldA = array();
	$valueA = array();
	$cnt = 0;
	foreach($SaveArr AS $Field => $Value)
	{
		$fieldA[$cnt] = $Field;
		$valueA[$cnt] = '"' . $Value . '"';
		$cnt++;
	}
	$updQry = 'INSERT INTO vendordetails(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	// exit($updQry);
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function saveVendor($VendorID, $SaveArr)
{
	global $dbCon;

	$updStr = '';
	foreach($SaveArr AS $Field => $Value)
	{
		if($updStr != '')
			$updStr .= ', ';
		$updStr .= $Field . ' = "' . $Value . '"';
	}

	$updQry = 'UPDATE vendordetails SET ' . $updStr . ' WHERE vendorid = ' . $VendorID;
	// exit($updQry);
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}

function getManagingAgents()
{
	global $dbCon;

	$selQry = 'SELECT maid, agentname FROM managingagentdetails ORDER BY agentname';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['maid']] = $selData['agentname'];
	}
	return $retArr;
}

function getManagingAgentByID($MAID)
{
	global $dbCon;

	$selQry = 'SELECT maid, agentname FROM managingagentdetails WHERE maid = ' . $MAID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_fetch_array($selRes);
}

function addManagingAgents($SaveArr)
{
	global $dbCon;

	$fieldA = array();
	$valueA = array();
	$cnt = 0;
	foreach($SaveArr AS $Field => $Value)
	{
		$fieldA[$cnt] = $Field;
		$valueA[$cnt] = '"' . $Value . '"';
		$cnt++;
	}
	$updQry = 'INSERT INTO managingagentdetails(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	// exit($updQry);
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function saveManagingAgents($MAID, $SaveArr)
{
	global $dbCon;

	$updStr = '';
	foreach($SaveArr AS $Field => $Value)
	{
		if($updStr != '')
			$updStr .= ', ';
		$updStr .= $Field . ' = "' . $Value . '"';
	}

	$updQry = 'UPDATE managingagentdetails SET ' . $updStr . ' WHERE maid = ' . $MAID;
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}

function getSecurityCompanies()
{
	global $dbCon;

	$selQry = 'SELECT secid, secname FROM securitycompanydetails ORDER BY secname';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['secid']] = $selData['secname'];
	}
	return $retArr;
}

function getSecurityCompanyByID($SecID)
{
	global $dbCon;

	$selQry = 'SELECT secid, secname FROM securitycompanydetails WHERE secid = ' . $SecID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_fetch_array($selRes);
}

function addSecurityCompanies($SaveArr)
{
	global $dbCon;

	$fieldA = array();
	$valueA = array();
	$cnt = 0;
	foreach($SaveArr AS $Field => $Value)
	{
		$fieldA[$cnt] = $Field;
		$valueA[$cnt] = '"' . $Value . '"';
		$cnt++;
	}
	$updQry = 'INSERT INTO securitycompanydetails(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	// exit($updQry);
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function saveSecurityCompanies($SecID, $SaveArr)
{
	global $dbCon;

	$updStr = '';
	foreach($SaveArr AS $Field => $Value)
	{
		if($updStr != '')
			$updStr .= ', ';
		$updStr .= $Field . ' = "' . $Value . '"';
	}

	$updQry = 'UPDATE securitycompanydetails SET ' . $updStr . ' WHERE secid = ' . $SecID;
	// exit($updQry);
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}


function getPackagesForVendorAndComplex($VendorID, $ComplexType)
{
	global $dbCon;

	$selQry = 'SELECT packageid, packagename, vendorid, complextype, packagetype, ontid, monthsterm, speedid, ontcost, installcost, devicefee, connectcost, monthlycost, prevatsalesprice, costprice, packagetype ';
	$selQry .= 'FROM packagedetails WHERE isinactive = 0 AND packagegroupid = 1 AND vendorid = ' . $VendorID . ' And complextype = ' . $ComplexType;
	// echo $selQry . "<br>";
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['packageid']]['packageid'] = $selData['packageid'];
		$retArr[$selData['packageid']]['packagename'] = $selData['packagename'];
		$retArr[$selData['packageid']]['vendorid'] = $selData['vendorid'];
		$retArr[$selData['packageid']]['complextype'] = $selData['complextype'];
		$retArr[$selData['packageid']]['ontid'] = $selData['ontid'];
		$retArr[$selData['packageid']]['monthsterm'] = $selData['monthsterm'];
		$retArr[$selData['packageid']]['speedid'] = $selData['speedid'];
		$retArr[$selData['packageid']]['ontcost'] = $selData['ontcost'];
		$retArr[$selData['packageid']]['installcost'] = $selData['installcost'];
		$retArr[$selData['packageid']]['devicefee'] = $selData['devicefee'];
		$retArr[$selData['packageid']]['connectcost'] = $selData['connectcost'];
		$retArr[$selData['packageid']]['monthlycost'] = $selData['monthlycost'];
		$retArr[$selData['packageid']]['prevatsalesprice'] = $selData['prevatsalesprice'];
		$retArr[$selData['packageid']]['costprice'] = $selData['costprice'];
		$retArr[$selData['packageid']]['packagetype'] = $selData['packagetype'];
	}
	return $retArr;
}

function getONTTypes()
{
	global $dbCon;

	$selQry = 'SELECT ontid, ontname FROM onttypes ORDER BY ontname';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['ontid']] = $selData['ontname'];
	}
	return $retArr;
}

function getPrecincts()
{
	global $dbCon;

	$selQry = 'SELECT precinctid, precinctname, precinctcode, suburbid, areaid, cityid, provinceid, countryid FROM precinctdetails';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['precinctid']]['precinctid'] = $selData['precinctid'];
		$retArr[$selData['precinctid']]['precinctname'] = $selData['precinctname'];
		$retArr[$selData['precinctid']]['precinctcode'] = $selData['precinctcode'];
		$retArr[$selData['precinctid']]['suburbid'] = $selData['suburbid'];
		$retArr[$selData['precinctid']]['areaid'] = $selData['areaid'];
		$retArr[$selData['precinctid']]['cityid'] = $selData['cityid'];
		$retArr[$selData['precinctid']]['provinceid'] = $selData['provinceid'];
		$retArr[$selData['precinctid']]['countryid'] = $selData['countryid'];
	}
	return $retArr;
}

function getSuburbs()
{
	global $dbCon;

	$selQry = 'SELECT suburbid, suburbname, suburbcode, areaid, cityid, provinceid, countryid FROM suburbdetails';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['suburbid']]['suburbid'] = $selData['suburbid'];
		$retArr[$selData['suburbid']]['suburbname'] = $selData['suburbname'];
		$retArr[$selData['suburbid']]['suburbcode'] = $selData['suburbcode'];
		$retArr[$selData['suburbid']]['areaid'] = $selData['areaid'];
		$retArr[$selData['suburbid']]['cityid'] = $selData['cityid'];
		$retArr[$selData['suburbid']]['provinceid'] = $selData['provinceid'];
		$retArr[$selData['suburbid']]['countryid'] = $selData['countryid'];
	}
	return $retArr;
}

function getAreas()
{
	global $dbCon;

	$selQry = 'SELECT areaid, areaname, areacode, cityid, provinceid, countryid FROM areadetails';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['areaid']]['areaid'] = $selData['areaid'];
		$retArr[$selData['areaid']]['areaname'] = $selData['areaname'];
		$retArr[$selData['areaid']]['areacode'] = $selData['areacode'];
		$retArr[$selData['areaid']]['cityid'] = $selData['cityid'];
		$retArr[$selData['areaid']]['provinceid'] = $selData['provinceid'];
		$retArr[$selData['areaid']]['countryid'] = $selData['countryid'];
	}
	return $retArr;
}

function getCities()
{
	global $dbCon;

	$selQry = 'SELECT cityid, cityname, citycode, provinceid, countryid FROM citydetails';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['cityid']]['cityid'] = $selData['cityid'];
		$retArr[$selData['cityid']]['cityname'] = $selData['cityname'];
		$retArr[$selData['cityid']]['citycode'] = $selData['citycode'];
		$retArr[$selData['cityid']]['provinceid'] = $selData['provinceid'];
		$retArr[$selData['cityid']]['countryid'] = $selData['countryid'];
	}
	return $retArr;
}

function getProvinces()
{
	global $dbCon;

	$selQry = 'SELECT provinceid, provincename, provincecode, countryid FROM provincedetails';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['provinceid']]['provinceid'] = $selData['provinceid'];
		$retArr[$selData['provinceid']]['provincename'] = $selData['provincename'];
		$retArr[$selData['provinceid']]['provincecode'] = $selData['provincecode'];
		$retArr[$selData['provinceid']]['countryid'] = $selData['countryid'];
	}
	return $retArr;
}

function getCountries()
{
	global $dbCon;

	$selQry = 'SELECT countryid, countryname, countrycode FROM countrydetails';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['countryid']]['countryid'] = $selData['countryid'];
		$retArr[$selData['countryid']]['countryname'] = $selData['countryname'];
		$retArr[$selData['countryid']]['countrycode'] = $selData['countrycode'];
	}
	return $retArr;
}

function addComplexUnitMap($ComplexID, $UnitDesc)
{
	global $dbCon;

	$insQry = 'INSERT INTO complexunitmap(complexid, unitdesc) VALUES ("' . $ComplexID . '", "' . $UnitDesc . '")';
	$insRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
}

function getComplexUnitMap($ComplexID)
{
	global $dbCon;

	$selQry = 'SELECT mapid, complexid, unitid, customerid, unitdesc, hoaunit FROM complexunitmap WHERE complexid = ' . $ComplexID . ' ORDER BY hoaunit ASC, (unitdesc * -1) DESC';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['mapid']]['mapid'] = $selData['mapid'];
		$retArr[$selData['mapid']]['complexid'] = $selData['complexid'];
		$retArr[$selData['mapid']]['unitid'] = $selData['unitid'];
		$retArr[$selData['mapid']]['customerid'] = $selData['customerid'];
		$retArr[$selData['mapid']]['unitdesc'] = $selData['unitdesc'];
		$retArr[$selData['mapid']]['hoaunit'] = $selData['hoaunit'];
	}
	return $retArr;
}

function deleteComplexMapUnit($MapID)
{
	global $dbCon;

	$insQry = 'UPDATE complexunitmap SET complexid = complexid * -1 WHERE mapid = ' . $MapID;
	$insRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
}

function getComplexUnitMapCount($ComplexID)
{
	global $dbCon;

	$selQry = 'SELECT COUNT(*) AS mapcnt FROM complexunitmap WHERE complexid = ' . $ComplexID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$selData = mysqli_fetch_array($selRes);
	return $selData['mapcnt'];
}

function addComplexMapUnit($ComplexID)
{
	$CompCount = getComplexUnitMapCount($ComplexID) + 1;
	addComplexUnitMap($ComplexID, $CompCount);
}

function saveComplexMapUnit($MapID, $SaveArr)
{
	global $dbCon;

	$updStr = '';
	foreach($SaveArr AS $Field => $Value)
	{
		if($updStr != '')
			$updStr .= ', ';
		$updStr .= $Field . ' = "' . $Value . '"';
	}

	$updQry = 'UPDATE complexunitmap SET ' . $updStr . ' WHERE mapid = ' . $MapID;
	$selRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}

function getPackageSpeeds()
{
	global $dbCon;

	$selQry = 'SELECT speedid, speedname FROM packagespeeds ORDER BY speedname * 1';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['speedid']] = $selData['speedname'];
	}
	return $retArr;
}

function addSpeed($SaveArr)
{
	global $dbCon;

	$fieldA = array();
	$valueA = array();
	$cnt = 0;
	foreach($SaveArr AS $Field => $Value)
	{
		$fieldA[$cnt] = $Field;
		$valueA[$cnt] = '"' . $Value . '"';
		$cnt++;
	}
	$updQry = 'INSERT INTO packagespeeds(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function saveSpeed($SpeedID, $SaveArr)
{
	global $dbCon;

	$updStr = '';
	foreach($SaveArr AS $Field => $Value)
	{
		if($updStr != '')
			$updStr .= ', ';
		$updStr .= $Field . ' = "' . $Value . '"';
	}
	$updQry = 'UPDATE packagespeeds SET ' . $updStr . ' WHERE speedid = ' . $SpeedID;
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}

function getVendorSpeeds()
{
	global $dbCon;

	$selQry = 'SELECT speedid, vendorid, costprice, isinactive FROM vendorspeeds WHERE historyserial = 0';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['speedid']][$selData['vendorid']]['price'] = $selData['costprice'];
		$retArr[$selData['speedid']][$selData['vendorid']]['isinactive'] = $selData['isinactive'];
	}
	return $retArr;
}

function addVendorSpeed($SpeedID, $VendorID, $Price, $Active)
{
	global $dbCon;

	$selQry = 'INSERT INTO vendorspeeds(speedid, vendorid, costprice, isinactive) VALUES ("' . $SpeedID . '", "' . $VendorID . '", "' . $Price . '", "' . $Active . '")';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
}

function clearVendorSpeed($SpeedID)
{
	global $dbCon;

	$selQry = 'UPDATE vendorspeeds SET historyserial = historyserial + 1 WHERE speedid = ' . $SpeedID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
}

function getVendorONTs()
{
	global $dbCon;

	$selQry = 'SELECT ontid, vendorid, costprice, isinactive FROM vendoronts';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['ontid']][$selData['vendorid']]['price'] = $selData['costprice'];
		$retArr[$selData['ontid']][$selData['vendorid']]['isinactive'] = $selData['isinactive'];
	}
	return $retArr;
}

function addONT($SaveArr)
{
	global $dbCon;

	$fieldA = array();
	$valueA = array();
	$cnt = 0;
	foreach($SaveArr AS $Field => $Value)
	{
		$fieldA[$cnt] = $Field;
		$valueA[$cnt] = '"' . $Value . '"';
		$cnt++;
	}
	$updQry = 'INSERT INTO onttypes(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function saveONT($ONTID, $SaveArr)
{
	global $dbCon;

	$updStr = '';
	foreach($SaveArr AS $Field => $Value)
	{
		if($updStr != '')
			$updStr .= ', ';
		$updStr .= $Field . ' = "' . $Value . '"';
	}
	$updQry = 'UPDATE onttypes SET ' . $updStr . ' WHERE ontid = ' . $ONTID;
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}

function addVendorONT($ONTID, $VendorID, $Price, $Active)
{
	global $dbCon;

	$selQry = 'INSERT INTO vendoronts(ontid, vendorid, costprice, isinactive) VALUES ("' . $ONTID . '", "' . $VendorID . '", "' . $Price . '", "' . $Active . '")';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
}

function clearVendorONT($ONTID)
{
	global $dbCon;

	$selQry = 'UPDATE vendoronts SET historyserial = historyserial + 1 WHERE ontid = ' . $ONTID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
}

function getPackageExtras()
{
	global $dbCon;

	$selQry = 'SELECT extraid, extraname, costprice FROM packagelineextras';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['extraid']]['extraid'] = $selData['extraid'];
		$retArr[$selData['extraid']]['extraname'] = $selData['extraname'];
		$retArr[$selData['extraid']]['costprice'] = $selData['costprice'];
	}
	return $retArr;
}

function addPackageExtra($SaveArr)
{
	global $dbCon;

	$fieldA = array();
	$valueA = array();
	$cnt = 0;
	foreach($SaveArr AS $Field => $Value)
	{
		$fieldA[$cnt] = $Field;
		$valueA[$cnt] = '"' . $Value . '"';
		$cnt++;
	}
	$updQry = 'INSERT INTO packagelineextras(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function savePackageExtra($ExtraID, $SaveArr)
{
	global $dbCon;

	$updStr = '';
	foreach($SaveArr AS $Field => $Value)
	{
		if($updStr != '')
			$updStr .= ', ';
		$updStr .= $Field . ' = "' . $Value . '"';
	}
	$updQry = 'UPDATE packagelineextras SET ' . $updStr . ' WHERE extraid = ' . $ExtraID;
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}

function getPackageGroups()
{
	global $dbCon;

	$selQry = 'SELECT packagegroupid, packagegroupname FROM packagegroups';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['packagegroupid']] = $selData['packagegroupname'];
	}
	return $retArr;
}

function getPackageGroupTypes($GroupID)
{
	global $dbCon;

	$selQry = 'SELECT complextype FROM packagegroupcomplextypes WHERE packagegroupid = ' . $GroupID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array(0 => "");
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['complextype']] = $selData['complextype'];
	}
	return $retArr;
}

function getPackageGroupTypesForComplexType($ComplexTypeID)
{
	global $dbCon;

	$selQry = 'SELECT packagegroupid FROM packagegroupcomplextypes WHERE complextype = ' . $ComplexTypeID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array(0 => "");
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['packagegroupid']] = $selData['packagegroupid'];
	}
	return $retArr;
}

function clearPackageGroupTypes($GroupID)
{
	global $dbCon;

	$selQry = 'UPDATE packagegroupcomplextypes SET packagegroupid = packagegroupid * -1 WHERE packagegroupid = ' . $GroupID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
}

function addPackageGroupTypes($GroupID, $TypeID)
{
	global $dbCon;

	$selQry = 'INSERT INTO packagegroupcomplextypes(packagegroupid, complextype) VALUES ("' . $GroupID . '", "' . $TypeID . '")';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
}

function addPackageGroup($SaveArr)
{
	global $dbCon;

	$fieldA = array();
	$valueA = array();
	$cnt = 0;
	foreach($SaveArr AS $Field => $Value)
	{
		$fieldA[$cnt] = $Field;
		$valueA[$cnt] = '"' . $Value . '"';
		$cnt++;
	}
	$updQry = 'INSERT INTO packagegroups(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function savePackageGroup($GroupID, $SaveArr)
{
	global $dbCon;

	$updStr = '';
	foreach($SaveArr AS $Field => $Value)
	{
		if($updStr != '')
			$updStr .= ', ';
		$updStr .= $Field . ' = "' . $Value . '"';
	}
	$updQry = 'UPDATE packagegroups SET ' . $updStr . ' WHERE packagegroupid = ' . $GroupID;
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}

function getSystemPrivileges()
{
	global $dbCon;

	$selQry = 'SELECT privilegeid, privilegename FROM systemprivileges';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['privilegeid']] = $selData['privilegename'];
	}
	return $retArr;
}

function getAllUserPrivileges()
{
	global $dbCon;

	$selQry = 'SELECT userid, privilegeid FROM userprivileges';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['userid']][$selData['privilegeid']] = $selData['privilegeid'];
	}
	return $retArr;
}

function getUserPrivilege($UserID)
{
	global $dbCon;

	$selQry = 'SELECT userid, privilegeid FROM userprivileges WHERE userid = ' . $UserID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['privilegeid']] = $selData['privilegeid'];
	}
	return $retArr;
}

function addUser($SaveArr)
{
	global $dbCon;
	$SaveArr['userpass'] = hashPassword($SaveArr['username'], $SaveArr['userpass']);
	$fieldA = array();
	$valueA = array();
	$cnt = 0;
	foreach($SaveArr AS $Field => $Value)
	{
		$fieldA[$cnt] = $Field;
		$valueA[$cnt] = '"' . $Value . '"';
		$cnt++;
	}
	$fieldA[$cnt] = 'dateregistered';
	$valueA[$cnt] = 'NOW()';
	$cnt++;
	$fieldA[$cnt] = 'lastaction';
	$valueA[$cnt] = 'NOW()';
	$updQry = 'INSERT INTO userdetails(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function getPassword($Limit = 3)
{
	global $dbCon;
	$randQry = 'SELECT LOWER(theword) AS theword FROM wordlist ORDER BY RAND() LIMIT ' . $Limit;
	$randRes = mysqli_query($dbCon, $randQry) or logDBError(mysqli_error($dbCon), $randQry, __FILE__, __FUNCTION__, __LINE__);
	$GenPass = "";
	while($randData = mysqli_fetch_array($randRes))
	{
		$GenPass .= ucfirst($randData['theword']);
	}
	return $GenPass;
}

function saveUser($UserID, $SaveArr)
{
	global $dbCon;

	$updStr = '';
	foreach($SaveArr AS $Field => $Value)
	{
		if($updStr != '')
			$updStr .= ', ';
		$updStr .= $Field . ' = "' . $Value . '"';
	}
	$updQry = 'UPDATE userdetails SET ' . $updStr . ' WHERE userid = ' . $UserID;
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}

function clearUserPrivileges($UserID)
{
	global $dbCon;

	$selQry = 'UPDATE userprivileges SET userid = userid * -1 WHERE userid = ' . $UserID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
}

function addUserPrivilege($UserID, $PrivID)
{
	global $dbCon;

	$selQry = 'INSERT INTO userprivileges(userid, privilegeid) VALUES ("' . $UserID . '", "' . $PrivID . '")';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
}

function getPackages()
{
	global $dbCon;

	$selQry = 'SELECT packageid, packagegroupid, packagename, vendorid, isinactive, packagetype FROM packagedetails';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['packageid']]['packageid'] = $selData['packageid'];
		$retArr[$selData['packageid']]['packagegroupid'] = $selData['packagegroupid'];
		$retArr[$selData['packageid']]['packagename'] = $selData['packagename'];
		$retArr[$selData['packageid']]['vendorid'] = $selData['vendorid'];
		$retArr[$selData['packageid']]['isinactive'] = $selData['isinactive'];
		$retArr[$selData['packageid']]['packagetype'] = $selData['packagetype'];
	}
	return $retArr;
}

function getVendorPackages($VendorList)
{
	global $dbCon;

	$selQry = 'SELECT packagedetails.packageid, packagegroupid, packagename, vendorid, isinactive, pieceid, packagepieces.speedid, packagepieces.ontid, packagepieces.extraid, piecesnummonths, piecescost, endcontinues, piecescomms, packagetype ';
	$selQry .= 'FROM packagedetails ';
	$selQry .= 'INNER JOIN packagepieces ON packagepieces.packageid = packagedetails.packageid ';
	$selQry .= 'WHERE vendorid IN ("' . $VendorList . '") ORDER BY packageid, pieceid';
	// echo $selQry . "<Br>";
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['packageid']][0]['packageid'] = $selData['packageid'];
		$retArr[$selData['packageid']][0]['packagegroupid'] = $selData['packagegroupid'];
		$retArr[$selData['packageid']][0]['packagename'] = $selData['packagename'];
		$retArr[$selData['packageid']][0]['vendorid'] = $selData['vendorid'];
		$retArr[$selData['packageid']][0]['isinactive'] = $selData['isinactive'];
		$retArr[$selData['packageid']][0]['packagetype'] = $selData['packagetype'];
		$retArr[$selData['packageid']][$selData['pieceid']]['pieceid'] = $selData['pieceid'];
		$retArr[$selData['packageid']][$selData['pieceid']]['speedid'] = $selData['speedid'];
		$retArr[$selData['packageid']][$selData['pieceid']]['ontid'] = $selData['ontid'];
		$retArr[$selData['packageid']][$selData['pieceid']]['extraid'] = $selData['extraid'];
		$retArr[$selData['packageid']][$selData['pieceid']]['piecesnummonths'] = $selData['piecesnummonths'];
		$retArr[$selData['packageid']][$selData['pieceid']]['piecescost'] = $selData['piecescost'];
		$retArr[$selData['packageid']][$selData['pieceid']]['endcontinues'] = $selData['endcontinues'];
		$retArr[$selData['packageid']][$selData['pieceid']]['piecescomms'] = $selData['piecescomms'];
	}
	return $retArr;
}


function addPackage($SaveArr)
{
	global $dbCon;

	$fieldA = array();
	$valueA = array();
	$cnt = 0;
	foreach($SaveArr AS $Field => $Value)
	{
		$fieldA[$cnt] = $Field;
		$valueA[$cnt] = '"' . $Value . '"';
		$cnt++;
	}
	$updQry = 'INSERT INTO packagedetails(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function savePackage($PackageID, $SaveArr)
{
	global $dbCon;

	$updStr = '';
	foreach($SaveArr AS $Field => $Value)
	{
		if($updStr != '')
			$updStr .= ', ';
		$updStr .= $Field . ' = "' . $Value . '"';
	}
	$updQry = 'UPDATE packagedetails SET ' . $updStr . ' WHERE packageid = ' . $PackageID;
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}

function getPackageDetails($PackageID)
{
	global $dbCon;

	$selQry = 'SELECT packageid, packagegroupid, packagename, vendorid, isinactive, packagetype FROM packagedetails WHERE packageid = ' . $PackageID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr['packageid'] = $selData['packageid'];
		$retArr['packagegroupid'] = $selData['packagegroupid'];
		$retArr['packagename'] = $selData['packagename'];
		$retArr['vendorid'] = $selData['vendorid'];
		$retArr['isinactive'] = $selData['isinactive'];
		$retArr['packagetype'] = $selData['packagetype'];
	}
	return $retArr;
}

function getPackagePieces($PackageID)
{
	global $dbCon;

	$selQry = 'SELECT pieceid, packageid, speedid, ontid, extraid, piecesnummonths, piecescost, endcontinues, piecescomms FROM packagepieces WHERE packageid = ' . $PackageID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['pieceid']]['pieceid'] = $selData['pieceid'];
		$retArr[$selData['pieceid']]['packageid'] = $selData['packageid'];
		$retArr[$selData['pieceid']]['speedid'] = $selData['speedid'];
		$retArr[$selData['pieceid']]['ontid'] = $selData['ontid'];
		$retArr[$selData['pieceid']]['extraid'] = $selData['extraid'];
		$retArr[$selData['pieceid']]['piecesnummonths'] = $selData['piecesnummonths'];
		$retArr[$selData['pieceid']]['piecescost'] = $selData['piecescost'];
		$retArr[$selData['pieceid']]['endcontinues'] = $selData['endcontinues'];
		$retArr[$selData['pieceid']]['piecescomms'] = $selData['piecescomms'];
	}
	return $retArr;
}

function getPackageSpeedPiece($PackageID)
{
	global $dbCon;

	$selQry = 'SELECT pieceid, packageid, speedid, ontid, extraid, piecesnummonths, piecescost, endcontinues, piecescomms FROM packagepieces WHERE packageid = ' . $PackageID . ' AND speedid != 0';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr['pieceid'] = $selData['pieceid'];
		$retArr['packageid'] = $selData['packageid'];
		$retArr['speedid'] = $selData['speedid'];
		$retArr['ontid'] = $selData['ontid'];
		$retArr['extraid'] = $selData['extraid'];
		$retArr['piecesnummonths'] = $selData['piecesnummonths'];
		$retArr['piecescost'] = $selData['piecescost'];
		$retArr['endcontinues'] = $selData['endcontinues'];
		$retArr['piecescomms'] = $selData['piecescomms'];
	}
	return $retArr;
}

function getPackageONTPiece($PackageID)
{
	global $dbCon;

	$selQry = 'SELECT pieceid, packageid, speedid, ontid, extraid, piecesnummonths, piecescost, endcontinues, piecescomms FROM packagepieces WHERE packageid = ' . $PackageID . ' AND ontid != 0';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr['pieceid'] = $selData['pieceid'];
		$retArr['packageid'] = $selData['packageid'];
		$retArr['speedid'] = $selData['speedid'];
		$retArr['ontid'] = $selData['ontid'];
		$retArr['extraid'] = $selData['extraid'];
		$retArr['piecesnummonths'] = $selData['piecesnummonths'];
		$retArr['piecescost'] = $selData['piecescost'];
		$retArr['endcontinues'] = $selData['endcontinues'];
		$retArr['piecescomms'] = $selData['piecescomms'];
	}
	return $retArr;
}

function addPackagePiece($SaveArr)
{
	global $dbCon;

	$fieldA = array();
	$valueA = array();
	$cnt = 0;
	foreach($SaveArr AS $Field => $Value)
	{
		$fieldA[$cnt] = $Field;
		$valueA[$cnt] = '"' . $Value . '"';
		$cnt++;
	}
	$updQry = 'INSERT INTO packagepieces(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function savePackagePiece($PieceID, $SaveArr)
{
	global $dbCon;

	$updStr = '';
	foreach($SaveArr AS $Field => $Value)
	{
		if($updStr != '')
			$updStr .= ', ';
		$updStr .= $Field . ' = "' . $Value . '"';
	}
	$updQry = 'UPDATE packagepieces SET ' . $updStr . ' WHERE pieceid = ' . $PieceID;
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}

function searchPackageOptions($ComplexType, $VendorID, $SpeedID = 0, $ONTID = 0, $NumMonths = 0)
{
	global $dbCon;

	$selQry = 'SELECT packagedetails.packageid, packagename, vendorid, pieceid, speedid, ontid, extraid, piecesnummonths, piecescost, packagedetails.packagegroupid, packagetype ';
	$selQry .= 'FROM packagedetails	';
	$selQry .= 'INNER JOIN packagegroupcomplextypes ON packagegroupcomplextypes.packagegroupid = packagedetails.packagegroupid AND complextype = ' . $ComplexType . ' ';
	$selQry .= 'INNER JOIN packagepieces ON packagepieces.packageid = packagedetails.packageid ';
	$selQry .= 'WHERE isinactive = 0 ';
	if($VendorID != '')
		$selQry .= ' AND packagedetails.vendorid IN (' . $VendorID . ') ';
	if($SpeedID != 0)
		$selQry .= ' AND packagepieces.speedid IN (0, ' . $SpeedID . ') ';
	if($ONTID != 0)
		$selQry .= ' AND packagepieces.ontid IN (0, ' . $ONTID . ') ';
	if($NumMonths != 0)
		$selQry .= ' AND packagepieces.piecesnummonths = ' . $SpeedID . ' ';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['packageid']][$selData['pieceid']]['packageid'] = $selData['packageid'];
		$retArr[$selData['packageid']][$selData['pieceid']]['packagegroupid'] = $selData['packagegroupid'];
		$retArr[$selData['packageid']][$selData['pieceid']]['packagename'] = $selData['packagename'];
		$retArr[$selData['packageid']][$selData['pieceid']]['vendorid'] = $selData['vendorid'];
		$retArr[$selData['packageid']][$selData['pieceid']]['pieceid'] = $selData['pieceid'];
		$retArr[$selData['packageid']][$selData['pieceid']]['speedid'] = $selData['speedid'];
		$retArr[$selData['packageid']][$selData['pieceid']]['ontid'] = $selData['ontid'];
		$retArr[$selData['packageid']][$selData['pieceid']]['extraid'] = $selData['extraid'];
		$retArr[$selData['packageid']][$selData['pieceid']]['piecesnummonths'] = $selData['piecesnummonths'];
		$retArr[$selData['packageid']][$selData['pieceid']]['piecescost'] = $selData['piecescost'];
		$retArr[$selData['packageid']][$selData['pieceid']]['packagetype'] = $selData['packagetype'];
	}
	return $retArr;
}

function getImportRecord($ComplexID)
{
	global $dbCon;
	$selQry = 'SELECT * FROM siteregisterexcel INNER JOIN excelcomplexidmap ON excelcomplexidmap.rowid = siteregisterexcel.rowid AND excelcomplexidmap.complexid = ' . $ComplexID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$selData = mysqli_fetch_array($selRes,MYSQLI_ASSOC);
	return $selData;
}

function getCustomerImportRecord($CustomerID)
{
	global $dbCon;
	$selQry = 'SELECT * FROM masterexcel INNER JOIN masterexcelcustomeridmap ON masterexcelcustomeridmap.rowid = masterexcel.rowid AND masterexcelcustomeridmap.customerid = ' . $CustomerID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$selData = mysqli_fetch_array($selRes,MYSQLI_ASSOC);
	return $selData;
}

function saveComplexFile($SaveArr)
{
	global $dbCon;

	$fieldA = array();
	$valueA = array();
	$cnt = 0;
	foreach($SaveArr AS $Field => $Value)
	{
		$fieldA[$cnt] = $Field;
		$valueA[$cnt] = '"' . $Value . '"';
		$cnt++;
	}
	if(!isset($SaveArr['uploadtime']))
	{
		$fieldA[$cnt] = 'uploadtime';
		$valueA[$cnt] = 'NOW()';
		$cnt++;
	}
	$updQry = 'INSERT INTO complexdocuments(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function getComplexFiles($ComplexID)
{
	global $dbCon;

	$selQry = 'SELECT documentid, complexid, userid, uploadtime, filename, filepath, doctype FROM complexdocuments WHERE complexid = ' . $ComplexID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['documentid']]['documentid'] = $selData['documentid'];
		$retArr[$selData['documentid']]['complexid'] = $selData['complexid'];
		$retArr[$selData['documentid']]['userid'] = $selData['userid'];
		$retArr[$selData['documentid']]['uploadtime'] = $selData['uploadtime'];
		$retArr[$selData['documentid']]['filename'] = $selData['filename'];
		$retArr[$selData['documentid']]['filepath'] = $selData['filepath'];
		$retArr[$selData['documentid']]['doctype'] = $selData['doctype'];
	}
	return $retArr;
}

function getComplexFileByID($FileID)
{
	global $dbCon;

	$selQry = 'SELECT documentid, complexid, userid, uploadtime, filename, filepath, doctype FROM complexdocuments WHERE documentid = ' . $FileID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_fetch_array($selRes);
}

function cleanFileName($fileName)
{
	$fileName = str_replace(" ", "_", $fileName);
	$fileName = str_replace("%", "_", $fileName);
	$fileName = str_replace("..", "_.", $fileName);
	$fileName = str_replace(",", "_", $fileName);
	$fileName = str_replace("|", "_", $fileName);
	$fileName = str_replace("/", "_.", $fileName);
	$fileName = str_replace("\\", "_.", $fileName);
	$fileName = str_replace("'", "_.", $fileName);
	$fileName = str_replace("\"", "_.", $fileName);
	return $fileName;
}

function addUnitPackage($UnitID, $PackageID, $CustomerID, $Status = 0) //, $TPOption = 0, $OrderID = 0)
{
	global $dbCon;

	// $PackRec = getPackageDetails($PackageID);
	// if($PackRec['packagetype'] == 1)
	// {
		// $updQry = 'UPDATE unitpackagedetails SET historyserial = historyserial + 1 WHERE unitid = ' . $UnitID . ' AND packagetype = 1';
	// }

	// $insQry = 'INSERT INTO unitpackagedetails(packageid, packagegroupid, packagename, packagetype, vendorid, isinactive, unitid, registerdate, tpoption, orderid) ';
	// $insQry .= 'SELECT packageid, packagegroupid, packagename, packagetype, vendorid, isinactive, ' . $UnitID . ', NOW(), ' . $TPOption . ', ' . $OrderID . ' FROM packagedetails WHERE packageid = ' . $PackageID;
	// $updRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
	// $UnitPackageID = mysqli_insert_id($dbCon);

	// $insQry = 'INSERT INTO unitpackagepieces(unitpackageid, pieceid, packageid, speedid, ontid, extraid, piecesnummonths, piecescost, piecescomms, endcontinues) ';
	// $insQry .= 'SELECT ' . $UnitPackageID . ', pieceid, packageid, speedid, ontid, extraid, piecesnummonths, piecescost, endcontinues, piecescomms FROM packagepieces WHERE packageid = ' . $PackageID;
	// $updRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);

	$insQry = 'INSERT INTO unitorderdetails(unitid, customerid, orderdate, packageid, termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, prevatsalesprice, orderstatus, historyserial) ';
	$insQry .= 'SELECT ' . $UnitID . ', ' . $CustomerID . ', NOW(), packageid, termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, prevatsalesprice, ' . $Status . ', 0 FROM fibrepackages WHERE packageid = ' . $PackageID;
	// echo $insQry;
	$updRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function updateUnitPackageHistory($OrderID)
{
	global $dbCon;
	
	$insQry = 'UPDATE unitorderdetails SET historyserial = historyserial + 1 WHERE orderid = ' . $OrderID;
	$updRes = mysqli_query($dbCon, $insQry) or logDBError(mysqli_error($dbCon), $insQry, __FILE__, __FUNCTION__, __LINE__);
}

function addUnitOrder($SaveArr)
{
	global $dbCon;

	$fieldA = array();
	$valueA = array();
	$cnt = 0;
	foreach($SaveArr AS $Field => $Value)
	{
		$fieldA[$cnt] = $Field;
		$valueA[$cnt] = '"' . $Value . '"';
		$cnt++;
	}
	if(!isset($SaveArr['orderdate']))
	{
		$fieldA[$cnt] = 'orderdate';
		$valueA[$cnt] = 'NOW()';
		$cnt++;
	}
	$updQry = 'INSERT INTO unitorders(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function addUnitOrderHistory($SaveArr)
{
	global $dbCon;

	$fieldA = array();
	$valueA = array();
	$cnt = 0;
	foreach($SaveArr AS $Field => $Value)
	{
		$fieldA[$cnt] = $Field;
		$valueA[$cnt] = '"' . $Value . '"';
		$cnt++;
	}
	if(!isset($SaveArr['eventdate']))
	{
		$fieldA[$cnt] = 'eventdate';
		$valueA[$cnt] = 'NOW()';
		$cnt++;
	}
	$updQry = 'INSERT INTO unitorderhistory(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

// function getUnitOrders()
// {
	// global $dbCon;

	// $selQry = 'SELECT orderid, unitid, orderdate, customerid, orderstatus, DATEDIFF(now(), orderdate) AS datediff FROM unitorders ORDER BY orderstatus, orderdate';
	// $selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	// $OrderArr = array();
	// while($selData = mysqli_fetch_array($selRes))
	// {
		// $OrderArr[$selData['orderid']]['orderid'] = $selData['orderid'];
		// $OrderArr[$selData['orderid']]['unitid'] = $selData['unitid'];
		// $OrderArr[$selData['orderid']]['orderdate'] = $selData['orderdate'];
		// $OrderArr[$selData['orderid']]['customerid'] = $selData['customerid'];
		// $OrderArr[$selData['orderid']]['orderstatus'] = $selData['orderstatus'];
		// $OrderArr[$selData['orderid']]['datediff'] = $selData['datediff'];

	// }
	// return $OrderArr;
// }

function getUnitOrdersByUnitID($UnitID)
{
	global $dbCon;

	$selQry = 'SELECT orderid, unitid, orderdate, customerid, orderstatus, DATEDIFF(now(), orderdate) AS datediff FROM unitorders WHERE unitid = ' . $UnitID . ' ORDER BY orderstatus, orderdate';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$OrderArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$OrderArr[$selData['orderid']]['orderid'] = $selData['orderid'];
		$OrderArr[$selData['orderid']]['unitid'] = $selData['unitid'];
		$OrderArr[$selData['orderid']]['orderdate'] = $selData['orderdate'];
		$OrderArr[$selData['orderid']]['customerid'] = $selData['customerid'];
		$OrderArr[$selData['orderid']]['orderstatus'] = $selData['orderstatus'];
		$OrderArr[$selData['orderid']]['datediff'] = $selData['datediff'];

	}
	return $OrderArr;
}

function getComplexOrders($ComplexID)
{
	global $dbCon;

	$selQry = 'SELECT orderid, unitorders.unitid, orderdate, unitorders.customerid, orderstatus, DATEDIFF(now(), orderdate) AS datediff ';
	$selQry .= 'FROM unitorders ';
	$selQry .= 'INNER JOIN customerunits ON customerunits.unitid = unitorders.unitid AND customerunits.complexid = ' . $ComplexID . ' ';
	$selQry .= 'ORDER BY orderstatus, orderdate';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$OrderArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$OrderArr[$selData['orderid']]['orderid'] = $selData['orderid'];
		$OrderArr[$selData['orderid']]['unitid'] = $selData['unitid'];
		$OrderArr[$selData['orderid']]['orderdate'] = $selData['orderdate'];
		$OrderArr[$selData['orderid']]['customerid'] = $selData['customerid'];
		$OrderArr[$selData['orderid']]['orderstatus'] = $selData['orderstatus'];
		$OrderArr[$selData['orderid']]['datediff'] = $selData['datediff'];

	}
	return $OrderArr;
}

function getUnitOrderHistoryByOrderIDList($OrderIDList)
{
	global $dbCon;

	$selQry = 'SELECT historyid, orderid, eventdate, eventdescr, eventcomment, userid FROM unitorderhistory WHERE orderid IN (' . $OrderIDList . ') ORDER BY orderid, eventdate';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$OrderArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$OrderArr[$selData['historyid']]['historyid'] = $selData['historyid'];
		$OrderArr[$selData['historyid']]['orderid'] = $selData['orderid'];
		$OrderArr[$selData['historyid']]['eventdate'] = $selData['eventdate'];
		$OrderArr[$selData['historyid']]['eventdescr'] = $selData['eventdescr'];
		$OrderArr[$selData['historyid']]['eventcomment'] = $selData['eventcomment'];
		$OrderArr[$selData['historyid']]['userid'] = $selData['userid'];
	}
	return $OrderArr;
}

function getAllComplexGroups()
{
	global $dbCon;

	$selQry = 'SELECT groupid, groupname FROM complexgroups';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['groupid']] = $selData['groupname'];
	}
	return $retArr;
}

function addRegisterCustomer($SaveArr)
{
	global $dbCon;

	$fieldA = array();
	$valueA = array();
	$cnt = 0;
	foreach($SaveArr AS $Field => $Value)
	{
		$fieldA[$cnt] = $Field;
		$valueA[$cnt] = '"' . $Value . '"';
		$cnt++;
	}
	$updQry = 'INSERT INTO registercustomers(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function getSalesOperationsWorkflow()
{
	global $dbCon;

	$selQry = 'SELECT stepid, stepname, steporder FROM salesoperationsprocess ORDER BY steporder';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['stepid']]['stepid'] = $selData['stepid'];
		$retArr[$selData['stepid']]['stepname'] = $selData['stepname'];
		$retArr[$selData['stepid']]['steporder'] = $selData['steporder'];
	}
	return $retArr;
}

function getSalesOperationsStepByName($StepName)
{
	global $dbCon;

	$selQry = 'SELECT stepid, stepname, steporder FROM salesoperationsprocess WHERE stepname = "' . $StepName . '"';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_fetch_array($selRes);
}

function createComplexSalesOperationsSteps($ComplexID)
{
	global $dbCon;

	$selQry = 'INSERT INTO salesoperationscomplex(complexid, stepid) SELECT ' . $ComplexID . ', stepid FROM salesoperationsprocess ORDER BY steporder';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
}

function setComplexSalesOperationsStepsDate($ComplexID, $StepID, $TheDate = '')
{
	global $dbCon;

	$TheDate = ($TheDate == '') ? 'NOW()' : '"' . $TheDate . '"';
	$updQry = 'UPDATE salesoperationscomplex SET datecompleted = ' . $TheDate . ' WHERE stepid = ' . $StepID . ' AND complexid = ' . $ComplexID;
	$selRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}

function getAllComplexesMaxSalesOperationsSteps()
{
	global $dbCon;

	$selQry = 'SELECT DISTINCT complexid FROM salesoperationscomplex WHERE stepid < 0';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$Inactives = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$Inactives[] = $selData['complexid'];
	}

	$retArr = array();
	$selQry = 'SELECT salesoperationscomplex.stepid, salesoperationscomplex.datecompleted, complexid, MAX(steporder) AS maxstep, DATEDIFF(NOW(), salesoperationscomplex.datecompleted) AS datediff FROM salesoperationscomplex ';
	$selQry .= 'INNER JOIN salesoperationsprocess ON salesoperationsprocess.stepid = salesoperationscomplex.stepid ';
	$selQry .= 'WHERE datecompleted IS NOT NULL ';
	if(count($Inactives) > 0)
		$selQry .= 'AND complexid NOT IN (' . implode(",", $Inactives) . ') ';
	$selQry .= 'GROUP BY complexid, salesoperationscomplex.stepid ORDER BY complexid, steporder';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);

	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['complexid']]['complexid'] = $selData['complexid'];
		$retArr[$selData['complexid']]['stepid'] = $selData['stepid'];
		$retArr[$selData['complexid']]['datecompleted'] = $selData['datecompleted'];
		$retArr[$selData['complexid']]['datediff'] = $selData['datediff'];
	}
	return $retArr;
}

function getActiveComplexesSalesOperations()
{
	global $dbCon;

	$selQry = 'SELECT DISTINCT complexid FROM salesoperationscomplex WHERE stepid < 0 AND complexid > 0';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$Inactives = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$Inactives[] = $selData['complexid'];
	}

	$selQry = 'SELECT salesoperationscomplex.stepid, salesoperationscomplex.datecompleted, complexid, DATEDIFF(NOW(), salesoperationscomplex.datecompleted) AS datediff FROM salesoperationscomplex ';
	$selQry .= 'WHERE complexid NOT IN (' . implode(",", $Inactives) . ') ';
	$selQry .= 'ORDER BY complexid, stepid';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['complexid']]['complexid'] = $selData['complexid'];
		$retArr[$selData['complexid']]['stepid'] = $selData['stepid'];
		$retArr[$selData['complexid']]['datecompleted'] = $selData['datecompleted'];
		$retArr[$selData['complexid']]['datediff'] = $selData['datediff'];
	}
	return $retArr;
}

function updateComplexSalesOperationStep($ComplexID, $StepID)
{
	global $dbCon;

	$selQry = 'UPDATE salesoperationscomplex SET datecompleted = NOW() WHERE complexid = ' . $ComplexID . ' AND stepid = ' . $StepID;
	// echo $selQry . "<Br>";
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);

}

function cancelComplexSalesOperationStep($ComplexID, $StepID)
{
	global $dbCon;

	$selQry = 'UPDATE salesoperationscomplex SET datecompleted = NOW(), stepid = stepid * -1 WHERE complexid = ' . $ComplexID . ' AND stepid = ' . $StepID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
}

function getComplexSalesOperationsSteps($ComplexID)
{
	global $dbCon;

	$selQry = 'SELECT stepid, datecompleted FROM salesoperationscomplex WHERE complexid = ' . $ComplexID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['stepid']] = $selData['datecompleted'];
	}
	return $retArr;
}

function addComplexNote($SaveArr)
{
	global $dbCon;

	$fieldA = array();
	$valueA = array();
	$cnt = 0;
	foreach($SaveArr AS $Field => $Value)
	{
		$fieldA[$cnt] = $Field;
		$valueA[$cnt] = '"' . $Value . '"';
		$cnt++;
	}
	if(!isset($SaveArr['datechanged']))
	{
		$fieldA[$cnt] = 'datechanged';
		$valueA[$cnt] = 'NOW()';
		$cnt++;
	}
	$updQry = 'INSERT INTO complexnotes(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function getComplexNotes($ComplexID, $RecLimit = '')
{
	global $dbCon;

	$selQry = 'SELECT noteid, userid, commentary, datechanged FROM complexnotes WHERE complexid = ' . $ComplexID . ' ORDER BY datechanged DESC';
	if($RecLimit != '')
		$selQry .= ' LIMIT ' . $RecLimit;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['noteid']]['noteid'] = $selData['noteid'];
		$retArr[$selData['noteid']]['userid'] = $selData['userid'];
		$retArr[$selData['noteid']]['commentary'] = $selData['commentary'];
		$retArr[$selData['noteid']]['datechanged'] = $selData['datechanged'];
	}
	return $retArr;
}

function getBodyCorpContacts($ComplexID)
{
	global $dbCon;

	$selQry = 'SELECT contactid, complexid, contactname, contactsurname, contactemail, contactcell, contacttel, addedby, addedwhen, designation, unitnum FROM bodycorpcontacts WHERE complexid = ' . $ComplexID . ' ORDER BY contactsurname, contactname';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['contactid']]['contactid'] = $selData['contactid'];
		$retArr[$selData['contactid']]['complexid'] = $selData['complexid'];
		$retArr[$selData['contactid']]['contactname'] = $selData['contactname'];
		$retArr[$selData['contactid']]['contactsurname'] = $selData['contactsurname'];
		$retArr[$selData['contactid']]['contactemail'] = $selData['contactemail'];
		$retArr[$selData['contactid']]['contactcell'] = $selData['contactcell'];
		$retArr[$selData['contactid']]['contacttel'] = $selData['contacttel'];
		$retArr[$selData['contactid']]['addedby'] = $selData['addedby'];
		$retArr[$selData['contactid']]['addedwhen'] = $selData['addedwhen'];
		$retArr[$selData['contactid']]['designation'] = $selData['designation'];
		$retArr[$selData['contactid']]['unitnum'] = $selData['unitnum'];
	}
	return $retArr;
}

function addBodyCorpContact($SaveArr)
{
	global $dbCon;

	$fieldA = array();
	$valueA = array();
	$cnt = 0;
	foreach($SaveArr AS $Field => $Value)
	{
		$fieldA[$cnt] = $Field;
		$valueA[$cnt] = '"' . $Value . '"';
		$cnt++;
	}
	$fieldA[$cnt] = 'addedwhen';
	$valueA[$cnt] = 'NOW()';
	$cnt++;
	$updQry = 'INSERT INTO bodycorpcontacts(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function saveBodyCorpContact($ContactID, $SaveArr)
{
	global $dbCon;

	$updStr = '';
	foreach($SaveArr AS $Field => $Value)
	{
		if($updStr != '')
			$updStr .= ', ';
		$updStr .= $Field . ' = "' . $Value . '"';
	}
	$updQry = 'UPDATE bodycorpcontacts SET ' . $updStr . ' WHERE contactid = ' . $ContactID;
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}

function getMeetingTypes()
{
	global $dbCon;

	$selQry = 'SELECT typeid, typename FROM meetingtypes ORDER BY typename';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['typeid']] = $selData['typename'];
	}
	return $retArr;
}

function addMeeting($SaveArr)
{
	global $dbCon;

	$fieldA = array();
	$valueA = array();
	$cnt = 0;
	foreach($SaveArr AS $Field => $Value)
	{
		$fieldA[$cnt] = $Field;
		$valueA[$cnt] = '"' . $Value . '"';
		$cnt++;
	}
	// $fieldA[$cnt] = 'addedwhen';
	// $valueA[$cnt] = 'NOW()';
	// $cnt++;
	$updQry = 'INSERT INTO meetingdetails(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function saveMeeting($MeetingID, $SaveArr)
{
	global $dbCon;

	$updStr = '';
	foreach($SaveArr AS $Field => $Value)
	{
		if($updStr != '')
			$updStr .= ', ';
		$updStr .= $Field . ' = "' . $Value . '"';
	}
	$updQry = 'UPDATE meetingdetails SET ' . $updStr . ' WHERE meetingid = ' . $MeetingID;
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}

function getMeetingByID($MeetingID)
{
	global $dbCon;

	$selQry = 'SELECT meetingid, complexid, customerid, meetingtypeid, completed, starttime, endtime, setupuser FROM meetingdetails WHERE meetingid = ' . $MeetingID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_fetch_array($selRes);
}

function getIncompleteMeetings($ComplexID = '')
{
	global $dbCon;

	$selQry = 'SELECT meetingid, complexid, customerid, meetingtypeid, completed, starttime, endtime, setupuser FROM meetingdetails WHERE completed = 0 ';
	if($ComplexID != '')
		$selQry .= 'AND complexid = ' . $ComplexID . ' ';
	$selQry .= 'ORDER BY starttime';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['meetingid']]['meetingid'] = $selData['meetingid'];
		$retArr[$selData['meetingid']]['complexid'] = $selData['complexid'];
		$retArr[$selData['meetingid']]['customerid'] = $selData['customerid'];
		$retArr[$selData['meetingid']]['meetingtypeid'] = $selData['meetingtypeid'];
		$retArr[$selData['meetingid']]['completed'] = $selData['completed'];
		$retArr[$selData['meetingid']]['starttime'] = $selData['starttime'];
		$retArr[$selData['meetingid']]['endtime'] = $selData['endtime'];
		$retArr[$selData['meetingid']]['setupuser'] = $selData['setupuser'];
	}
	return $retArr;
}

function addMeetingAttendee($SaveArr)
{
	global $dbCon;

	$fieldA = array();
	$valueA = array();
	$cnt = 0;
	foreach($SaveArr AS $Field => $Value)
	{
		$fieldA[$cnt] = $Field;
		$valueA[$cnt] = '"' . $Value . '"';
		$cnt++;
	}
	// $fieldA[$cnt] = 'addedwhen';
	// $valueA[$cnt] = 'NOW()';
	// $cnt++;
	$updQry = 'INSERT INTO meetingdiary(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function saveMeetingAttendee($DiaryID, $SaveArr)
{
	global $dbCon;

	$updStr = '';
	foreach($SaveArr AS $Field => $Value)
	{
		if($updStr != '')
			$updStr .= ', ';
		$updStr .= $Field . ' = "' . $Value . '"';
	}
	$updQry = 'UPDATE meetingdiary SET ' . $updStr . ' WHERE diaryid = ' . $DiaryID;
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}

function deleteMeetingAttendees($MeetingID)
{
	global $dbCon;

	$updQry = 'DELETE FROM meetingdiary WHERE meetingid = ' . $MeetingID;
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}

function getMeetingsForAttendee($Attendee)
{
	global $dbCon;

	$selQry = 'SELECT meetingid, userid, attendance FROM meetingdiary WHERE userid = ' . $Attendee;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['meetingid']]['userid'] = $selData['userid'];
		$retArr[$selData['meetingid']]['attendance'] = $selData['attendance'];
		$retArr[$selData['meetingid']]['meetingid'] = $selData['meetingid'];
	}
	return $retArr;
}

function getAttendeesForMeetings($MeetingID)
{
	global $dbCon;

	$selQry = 'SELECT meetingid, userid, attendance FROM meetingdiary WHERE meetingid = ' . $MeetingID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['userid']]['userid'] = $selData['userid'];
		$retArr[$selData['userid']]['attendance'] = $selData['attendance'];
		$retArr[$selData['userid']]['meetingid'] = $selData['meetingid'];
	}
	return $retArr;
}

function getFibrePackages($WhereCriteria = '')
{
	global $dbCon;

	$selQry = 'SELECT packageid, packagename, termnum, speedid, ontid, vendorid, ontcost, connectcost, monthlycost, prevatsalesprice, costprice, networkconnectcost, networkontcost FROM fibrepackages ';
	if(($WhereCriteria != '') && (is_array($WhereCriteria)))
	{
		$WhereArr = array();
		foreach($WhereCriteria AS $Field => $Value)
		{
			$WhereArr[] = $Field . ' = ' . $Value;
		}
		$selQry .= ' WHERE ' . implode(" AND ", $WhereArr);
	}
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['packageid']]['packageid'] = $selData['packageid'];
		$retArr[$selData['packageid']]['packagename'] = $selData['packagename'];
		$retArr[$selData['packageid']]['termnum'] = $selData['termnum'];
		$retArr[$selData['packageid']]['speedid'] = $selData['speedid'];
		$retArr[$selData['packageid']]['ontid'] = $selData['ontid'];
		$retArr[$selData['packageid']]['vendorid'] = $selData['vendorid'];
		$retArr[$selData['packageid']]['ontcost'] = $selData['ontcost'];
		$retArr[$selData['packageid']]['connectcost'] = $selData['connectcost'];
		$retArr[$selData['packageid']]['monthlycost'] = $selData['monthlycost'];
		$retArr[$selData['packageid']]['prevatsalesprice'] = $selData['prevatsalesprice'];
		$retArr[$selData['packageid']]['costprice'] = $selData['costprice'];
		$retArr[$selData['packageid']]['networkconnectcost'] = $selData['networkconnectcost'];
		$retArr[$selData['packageid']]['networkontcost'] = $selData['networkontcost'];
	}
	return $retArr;
}

function getUnitOrders()
{
	global $dbCon;

	$selQry = 'SELECT orderid, unitorderdetails.unitid, unitorderdetails.customerid, orderdate, unitorderdetails.packageid, termnum, speedid, ontid, vendorid, ontcost, installcost, connectcost, monthlycost, prevatsalesprice, DATEDIFF(NOW(), orderdate) AS datediff, orderstatus ';
	$selQry .= 'FROM unitorderdetails ';
	$selQry .= 'INNER JOIN customerunits ON customerunits.unitid = unitorderdetails.unitid ';
	$selQry .= 'WHERE historyserial = 0';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['unitid']]['orderid'] = $selData['orderid'];
		$retArr[$selData['unitid']]['unitid'] = $selData['unitid'];
		$retArr[$selData['unitid']]['customerid'] = $selData['customerid'];
		// $retArr[$selData['unitid']]['orderdate'] = $selData['orderdate'];
		$retArr[$selData['unitid']]['orders'][$selData['orderid']]['packageid'] = $selData['packageid'];
		$retArr[$selData['unitid']]['orders'][$selData['orderid']]['termnum'] = $selData['termnum'];
		$retArr[$selData['unitid']]['orders'][$selData['orderid']]['speedid'] = $selData['speedid'];
		$retArr[$selData['unitid']]['orders'][$selData['orderid']]['ontid'] = $selData['ontid'];
		$retArr[$selData['unitid']]['orders'][$selData['orderid']]['vendorid'] = $selData['vendorid'];
		$retArr[$selData['unitid']]['orders'][$selData['orderid']]['ontcost'] = $selData['ontcost'];
		$retArr[$selData['unitid']]['orders'][$selData['orderid']]['installcost'] = $selData['installcost'];
		$retArr[$selData['unitid']]['orders'][$selData['orderid']]['connectcost'] = $selData['connectcost'];
		$retArr[$selData['unitid']]['orders'][$selData['orderid']]['prevatsalesprice'] = $selData['prevatsalesprice'];
		$retArr[$selData['unitid']]['orders'][$selData['orderid']]['orderdate'] = $selData['orderdate'];
		$retArr[$selData['unitid']]['orders'][$selData['orderid']]['datediff'] = $selData['datediff'];
		$retArr[$selData['unitid']]['orders'][$selData['orderid']]['orderstatus'] = $selData['orderstatus'];
	}
	return $retArr;
}

function getNonPOOrders()
{
	global $dbCon;

	$selQry = 'SELECT orderid, unitorderdetails.unitid, unitorderdetails.customerid, orderdate, unitorderdetails.packageid, termnum, speedid, ontid, vendorid, ontcost, installcost, connectcost, monthlycost, prevatsalesprice, DATEDIFF(NOW(), orderdate) AS datediff, orderstatus ';
	$selQry .= 'FROM unitorderdetails ';
	$selQry .= 'INNER JOIN customerunits ON customerunits.unitid = unitorderdetails.unitid ';
	$selQry .= 'WHERE historyserial = 0';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['unitid']]['orderid'] = $selData['orderid'];
		$retArr[$selData['unitid']]['unitid'] = $selData['unitid'];
		$retArr[$selData['unitid']]['customerid'] = $selData['customerid'];
		// $retArr[$selData['unitid']]['orderdate'] = $selData['orderdate'];
		$retArr[$selData['unitid']]['orders'][$selData['orderid']]['packageid'] = $selData['packageid'];
		$retArr[$selData['unitid']]['orders'][$selData['orderid']]['termnum'] = $selData['termnum'];
		$retArr[$selData['unitid']]['orders'][$selData['orderid']]['speedid'] = $selData['speedid'];
		$retArr[$selData['unitid']]['orders'][$selData['orderid']]['ontid'] = $selData['ontid'];
		$retArr[$selData['unitid']]['orders'][$selData['orderid']]['vendorid'] = $selData['vendorid'];
		$retArr[$selData['unitid']]['orders'][$selData['orderid']]['ontcost'] = $selData['ontcost'];
		$retArr[$selData['unitid']]['orders'][$selData['orderid']]['installcost'] = $selData['installcost'];
		$retArr[$selData['unitid']]['orders'][$selData['orderid']]['connectcost'] = $selData['connectcost'];
		$retArr[$selData['unitid']]['orders'][$selData['orderid']]['monthlycost'] = $selData['monthlycost'];
		$retArr[$selData['unitid']]['orders'][$selData['orderid']]['prevatsalesprice'] = $selData['prevatsalesprice'];
		$retArr[$selData['unitid']]['orders'][$selData['orderid']]['orderdate'] = $selData['orderdate'];
		$retArr[$selData['unitid']]['orders'][$selData['orderid']]['datediff'] = $selData['datediff'];
		$retArr[$selData['unitid']]['orders'][$selData['orderid']]['orderstatus'] = $selData['orderstatus'];
	}
	return $retArr;
}

function getComplexUnitPackages($ComplexID)
{
	global $dbCon;

	$selQry = 'SELECT orderid, unitorderdetails.unitid, unitorderdetails.customerid, orderdate, unitorderdetails.packageid, termnum, speedid, ontid, vendorid, ontcost, installcost, connectcost, monthlycost, prevatsalesprice, orderstatus ';
	$selQry .= 'FROM unitorderdetails ';
	$selQry .= 'INNER JOIN customerunits ON customerunits.unitid = unitorderdetails.unitid AND customerunits.complexid = ' . $ComplexID . ' ';
	$selQry .= 'WHERE historyserial = 0';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['unitid']][$selData['orderid']]['orderid'] = $selData['orderid'];
		$retArr[$selData['unitid']][$selData['orderid']]['unitid'] = $selData['unitid'];
		$retArr[$selData['unitid']][$selData['orderid']]['customerid'] = $selData['customerid'];
		$retArr[$selData['unitid']][$selData['orderid']]['orderdate'] = $selData['orderdate'];
		$retArr[$selData['unitid']][$selData['orderid']]['packageid'] = $selData['packageid'];
		$retArr[$selData['unitid']][$selData['orderid']]['termnum'] = $selData['termnum'];
		$retArr[$selData['unitid']][$selData['orderid']]['speedid'] = $selData['speedid'];
		$retArr[$selData['unitid']][$selData['orderid']]['ontid'] = $selData['ontid'];
		$retArr[$selData['unitid']][$selData['orderid']]['vendorid'] = $selData['vendorid'];
		$retArr[$selData['unitid']][$selData['orderid']]['ontcost'] = $selData['ontcost'];
		$retArr[$selData['unitid']][$selData['orderid']]['installcost'] = $selData['installcost'];
		$retArr[$selData['unitid']][$selData['orderid']]['connectcost'] = $selData['connectcost'];
		$retArr[$selData['unitid']][$selData['orderid']]['monthlycost'] = $selData['monthlycost'];
		$retArr[$selData['unitid']][$selData['orderid']]['prevatsalesprice'] = $selData['prevatsalesprice'];
		$retArr[$selData['unitid']][$selData['orderid']]['orderstatus'] = $selData['orderstatus'];
	}
	return $retArr;
}

function getUnitPackage($UnitID)
{
	global $dbCon;

	$selQry = 'SELECT orderid, unitid, customerid, orderdate, packageid, termnum, speedid, ontid, vendorid, ontcost, installcost, connectcost, monthlycost, prevatsalesprice, orderstatus FROM unitorderdetails WHERE historyserial = 0 AND unitid = ' . $UnitID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['orderid']]['orderid'] = $selData['orderid'];
		$retArr[$selData['orderid']]['unitid'] = $selData['unitid'];
		$retArr[$selData['orderid']]['customerid'] = $selData['customerid'];
		$retArr[$selData['orderid']]['orderdate'] = $selData['orderdate'];
		$retArr[$selData['orderid']]['packageid'] = $selData['packageid'];
		$retArr[$selData['orderid']]['termnum'] = $selData['termnum'];
		$retArr[$selData['orderid']]['speedid'] = $selData['speedid'];
		$retArr[$selData['orderid']]['ontid'] = $selData['ontid'];
		$retArr[$selData['orderid']]['vendorid'] = $selData['vendorid'];
		$retArr[$selData['orderid']]['ontcost'] = $selData['ontcost'];
		$retArr[$selData['orderid']]['installcost'] = $selData['installcost'];
		$retArr[$selData['orderid']]['connectcost'] = $selData['connectcost'];
		$retArr[$selData['orderid']]['monthlycost'] = $selData['monthlycost'];
		$retArr[$selData['orderid']]['prevatsalesprice'] = $selData['prevatsalesprice'];
		$retArr[$selData['orderid']]['orderstatus'] = $selData['orderstatus'];
	}
	return $retArr;
}

function getUnitOrderByID($OrderID)
{
	global $dbCon;

	$selQry = 'SELECT orderid, unitid, customerid, orderdate, packageid, termnum, speedid, ontid, vendorid, ontcost, installcost, connectcost, monthlycost, prevatsalesprice, orderstatus FROM unitorderdetails WHERE orderid = ' . $OrderID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr['orderid'] = $selData['orderid'];
		$retArr['unitid'] = $selData['unitid'];
		$retArr['customerid'] = $selData['customerid'];
		$retArr['orderdate'] = $selData['orderdate'];
		$retArr['packageid'] = $selData['packageid'];
		$retArr['termnum'] = $selData['termnum'];
		$retArr['speedid'] = $selData['speedid'];
		$retArr['ontid'] = $selData['ontid'];
		$retArr['vendorid'] = $selData['vendorid'];
		$retArr['ontcost'] = $selData['ontcost'];
		$retArr['installcost'] = $selData['installcost'];
		$retArr['connectcost'] = $selData['connectcost'];
		$retArr['monthlycost'] = $selData['monthlycost'];
		$retArr['prevatsalesprice'] = $selData['prevatsalesprice'];
		$retArr['orderstatus'] = $selData['orderstatus'];
	}
	return $retArr;
}

function getComplexDocumentsByType($DocTypes, $ComplexIDs = '')
{
	global $dbCon;

	$selQry = 'SELECT documentid, complexid, userid, uploadtime, filename, filepath, doctype FROM complexdocuments WHERE doctype IN (' . $DocTypes . ') ';
	if($ComplexIDs != '')
		$selQry .= 'AND complexid IN (' . $ComplexIDs . ')';
	
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['documentid']]['documentid'] = $selData['documentid'];
		$retArr[$selData['documentid']]['complexid'] = $selData['complexid'];
		$retArr[$selData['documentid']]['userid'] = $selData['userid'];
		$retArr[$selData['documentid']]['uploadtime'] = $selData['uploadtime'];
		$retArr[$selData['documentid']]['filename'] = $selData['filename'];
		$retArr[$selData['documentid']]['filepath'] = $selData['filepath'];
		$retArr[$selData['documentid']]['doctype'] = $selData['doctype'];
	}
	return $retArr;
}
?>