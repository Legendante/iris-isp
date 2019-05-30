<?php
include_once("db.inc.php");
include_once("iris.inc.php");
$SrcTerm = strtolower($_GET['term']);
$SiteStatusses = getSiteStatusses();
// print_r($SiteStatusses);
$selQry = 'SELECT complexdetails.complexid, complexname, streetaddress1, streetaddress2, statusid, vendorid FROM complexdetails ';
$selQry .= 'LEFT JOIN complexstatus ON complexstatus.complexid = complexdetails.complexid AND complexstatus.historyserial = 0 ';
$selQry .= 'WHERE LOWER(complexname) LIKE "%' . $SrcTerm . '%" AND showinresults = 1 ';
$selQry .= 'ORDER BY complexname';
// echo $selQry . "<Br>";
$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
$retArr = array();
$cnt = 0;
while($selData = mysqli_fetch_array($selRes))
{
	$retStr = $selData['complexname'];
	if(trim($selData['streetaddress1']) != "")
		$retStr .= ", " . $selData['streetaddress1'];
	if(trim($selData['streetaddress2']) != "")
		$retStr .= ", " . $selData['streetaddress2'];
	$retArr[$cnt]['value'] = $retStr;
	$retArr[$cnt]['id'] = $selData['complexid'];
	$retArr[$cnt]['vendorid'] = $selData['vendorid'];
	if(isset($SiteStatusses[$selData['statusid']]))
	{
		if($SiteStatusses[$selData['statusid']]['parentid'] != 0)
			$retArr[$cnt]['status'] = $SiteStatusses[$SiteStatusses[$selData['statusid']]['parentid']]['statusname'];
		else
			$retArr[$cnt]['status'] = $SiteStatusses[$selData['statusid']]['statusname'];
	}
	else
		$retArr[$cnt]['status'] = "";
	$cnt++;
}
echo json_encode($retArr);
?>