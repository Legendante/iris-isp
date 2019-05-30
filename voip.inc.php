<?php
function getCustomerVoip($CustomerID)
{
	global $dbCon;

	$selQry = 'SELECT id, customerid, telnumber, voipstatus FROM customervoip WHERE customerid = ' . $CustomerID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['id']]['id'] = $selData['id'];
		$retArr[$selData['id']]['customerid'] = $selData['customerid'];
		$retArr[$selData['id']]['telnumber'] = $selData['telnumber'];
		$retArr[$selData['id']]['voipstatus'] = $selData['voipstatus'];
	}
	return $retArr;
}
?>