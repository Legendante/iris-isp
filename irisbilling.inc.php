<?php
function getActiveAccounts($InvoiceDate)
{
	global $dbCon;
	
	$selQry = 'SELECT unitpackagedetails.unitpackageid, unitpackagedetails.packageid, packagegroupid, packagename, packagetype, vendorid, isinactive, unitid, DATE(registerdate) AS registerdate, poorderid, tpoption, historyserial, ';
	$selQry .= 'customerid, DATE(contractstart) AS contractstart, DATE_FORMAT(contractstart, "%Y-%m-01") AS calcstart, DATE(contractend) AS contractend, DATE(contractexpires) AS contractexpires, ';
	$selQry .= 'TIMESTAMPDIFF(MONTH, DATE_FORMAT(contractstart, "%Y-%m-01"), "' . $InvoiceDate . '") AS contractdiff, ';
	$selQry .= 'unitpieceid, pieceid, speedid, ontid, extraid, piecesnummonths, piecescost, piecescomms, endcontinues, proratabilled ';
	$selQry .= 'FROM unitpackagedetails ';
	$selQry .= 'INNER JOIN unitpackagepieces ON unitpackagepieces.unitpackageid = unitpackagedetails.unitpackageid ';
	$selQry .= 'WHERE historyserial = 0 AND DATE(contractstart) <= "' . $InvoiceDate . '" ';
	$selQry .= 'AND (contractend IS NULL OR DATE(contractend) >= "' . $InvoiceDate . '") ';
	// echo $selQry;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['unitpackageid']]['unitpackageid'] = $selData['unitpackageid'];
		$retArr[$selData['unitpackageid']]['packageid'] = $selData['packageid'];
		$retArr[$selData['unitpackageid']]['packagegroupid'] = $selData['packagegroupid'];
		$retArr[$selData['unitpackageid']]['packagename'] = $selData['packagename'];
		$retArr[$selData['unitpackageid']]['packagetype'] = $selData['packagetype'];
		$retArr[$selData['unitpackageid']]['vendorid'] = $selData['vendorid'];
		$retArr[$selData['unitpackageid']]['isinactive'] = $selData['isinactive'];
		$retArr[$selData['unitpackageid']]['unitid'] = $selData['unitid'];
		$retArr[$selData['unitpackageid']]['registerdate'] = $selData['registerdate'];
		$retArr[$selData['unitpackageid']]['poorderid'] = $selData['poorderid'];
		$retArr[$selData['unitpackageid']]['tpoption'] = $selData['tpoption'];
		$retArr[$selData['unitpackageid']]['customerid'] = $selData['customerid'];
		$retArr[$selData['unitpackageid']]['contractstart'] = $selData['contractstart'];
		$retArr[$selData['unitpackageid']]['calcstart'] = $selData['calcstart'];
		$retArr[$selData['unitpackageid']]['contractend'] = $selData['contractend'];
		$retArr[$selData['unitpackageid']]['contractexpires'] = $selData['contractexpires'];
		$retArr[$selData['unitpackageid']]['contractdiff'] = $selData['contractdiff'];
		$retArr[$selData['unitpackageid']]['pieces'][$selData['unitpieceid']]['unitpieceid'] = $selData['unitpieceid'];
		$retArr[$selData['unitpackageid']]['pieces'][$selData['unitpieceid']]['pieceid'] = $selData['pieceid'];
		$retArr[$selData['unitpackageid']]['pieces'][$selData['unitpieceid']]['speedid'] = $selData['speedid'];
		$retArr[$selData['unitpackageid']]['pieces'][$selData['unitpieceid']]['ontid'] = $selData['ontid'];
		$retArr[$selData['unitpackageid']]['pieces'][$selData['unitpieceid']]['extraid'] = $selData['extraid'];
		$retArr[$selData['unitpackageid']]['pieces'][$selData['unitpieceid']]['piecesnummonths'] = $selData['piecesnummonths'];
		$retArr[$selData['unitpackageid']]['pieces'][$selData['unitpieceid']]['piecescost'] = $selData['piecescost'];
		$retArr[$selData['unitpackageid']]['pieces'][$selData['unitpieceid']]['piecescomms'] = $selData['piecescomms'];
		$retArr[$selData['unitpackageid']]['pieces'][$selData['unitpieceid']]['endcontinues'] = $selData['endcontinues'];
		$retArr[$selData['unitpackageid']]['pieces'][$selData['unitpieceid']]['proratabilled'] = $selData['proratabilled'];
	}
	return $retArr;
}
?>