<?php
function resizeImage($ImgWidth, $ImgHeight, $MaxWidth = 100, $MaxHeight = 100)
{
	$HeightRatio = $ImgHeight / $MaxHeight;
	$WidthRatio = $ImgWidth / $MaxWidth;
	$UseRatio = $WidthRatio;
	if($HeightRatio > $WidthRatio)
		$UseRatio = $HeightRatio;
	$retArr = array('width' => 0, 'height' => 0);
	if(($ImgWidth > $MaxWidth) || ($ImgHeight > $MaxHeight)) // Both height and width is too much
	{
		$retArr['width'] = floor($ImgWidth / $UseRatio);
		$retArr['height'] = floor($ImgHeight / $UseRatio);
	}
	else
	{
		$retArr['width'] = $ImgWidth;
		$retArr['height'] = $ImgHeight;
	}
	return $retArr;
}


function createPOOrder($TypeID)
{
	global $dbCon;

	$selQry = 'INSERT INTO purchaseorders(po_created, po_type) VALUES (NOW(), ' . $TypeID . ')';
	// echo $selQry . "<Br>";
	// exit();
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function getPOTypes()
{
	global $dbCon;

	$selQry = 'SELECT typeid, typename FROM purchaseordertypes';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['typeid']] = $selData['typename'];
	}
	return $retArr;
}

function getPOTypeIDByName($TypeName)
{
	global $dbCon;

	$selQry = 'SELECT typeid FROM purchaseordertypes WHERE typename = "' . $TypeName . '"';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$ret = mysqli_fetch_array($selRes);
	return $ret['typeid'];
}

function addOrderToPO($POID, $OrderID)
{
	global $dbCon;

	$selQry = 'INSERT INTO purchaseorderorders(po_id, orderid, created) VALUES (' . $POID . ', ' . $OrderID . ', NOW())';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
}

function getPOOrders()
{
	global $dbCon;

	$selQry = 'SELECT po_id, orderid, created FROM purchaseorderorders';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['orderid']]['orderid'] = $selData['orderid'];
		$retArr[$selData['orderid']]['po_id'] = $selData['po_id'];
		$retArr[$selData['orderid']]['created'] = $selData['created'];
	}
	return $retArr;
}
?>