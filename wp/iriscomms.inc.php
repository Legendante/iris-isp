<?php
function addMailboxItem($SaveArr)
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
	$fieldA[$cnt] = 'sentwhen';
	$valueA[$cnt] = 'NOW()';
	$cnt++;
	$updQry = 'INSERT INTO usermailbox(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	// exit($updQry);
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	$MailID = mysqli_insert_id($dbCon);
	if((!isset($SaveArr['threadid'])) || ($SaveArr['threadid'] == ''))
	{
		$updQry = 'UPDATE usermailbox SET threadid = mailid WHERE mailid = ' . $MailID;
		$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	}
	return $MailID;
}

function getUserMailbox($UserID)
{
	global $dbCon;
	
	$selQry = 'SELECT mailid, receiverid, senderid, sentwhen, openedwhen, subject, priority, msgbody, threadid, msgstatus FROM usermailbox WHERE receiverid = ' . $UserID . ' AND msgstatus = 0 ORDER BY priority DESC, sentwhen DESC';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['mailid']]['mailid'] = $selData['mailid'];
		$retArr[$selData['mailid']]['receiverid'] = $selData['receiverid'];
		$retArr[$selData['mailid']]['senderid'] = $selData['senderid'];
		$retArr[$selData['mailid']]['sentwhen'] = $selData['sentwhen'];
		$retArr[$selData['mailid']]['openedwhen'] = $selData['openedwhen'];
		$retArr[$selData['mailid']]['subject'] = $selData['subject'];
		$retArr[$selData['mailid']]['priority'] = $selData['priority'];
		$retArr[$selData['mailid']]['msgbody'] = $selData['msgbody'];
		$retArr[$selData['mailid']]['threadid'] = $selData['threadid'];
		$retArr[$selData['mailid']]['msgstatus'] = $selData['msgstatus'];
	}
	return $retArr;
}

function getMailItem($MailID)
{
	global $dbCon;
	
	$selQry = 'SELECT mailid, receiverid, senderid, sentwhen, openedwhen, subject, priority, msgbody, threadid, msgstatus FROM usermailbox WHERE mailid = ' . $MailID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr['mailid'] = $selData['mailid'];
		$retArr['receiverid'] = $selData['receiverid'];
		$retArr['senderid'] = $selData['senderid'];
		$retArr['sentwhen'] = $selData['sentwhen'];
		$retArr['openedwhen'] = $selData['openedwhen'];
		$retArr['subject'] = $selData['subject'];
		$retArr['priority'] = $selData['priority'];
		$retArr['msgbody'] = $selData['msgbody'];
		$retArr['threadid'] = $selData['threadid'];
		$retArr['msgstatus'] = $selData['msgstatus'];
	}
	return $retArr;
}

function getItemsFromThread($ThreadID, $SenderID, $ReceiverID, $ItemCount = 5)
{
	global $dbCon;
	
	$selQry = 'SELECT mailid, receiverid, senderid, sentwhen, openedwhen, subject, priority, msgbody, threadid, msgstatus FROM usermailbox ';
	$selQry .= 'WHERE threadid = ' . $ThreadID . ' AND receiverid IN (' . $SenderID . "," . $ReceiverID . ') AND senderid IN (' . $SenderID . "," . $ReceiverID . ') AND msgstatus = 0 ';
	$selQry .= 'ORDER BY priority DESC, sentwhen DESC';
	if($ItemCount != '')
		$selQry .= ' LIMIT ' . $ItemCount;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['mailid']]['mailid'] = $selData['mailid'];
		$retArr[$selData['mailid']]['receiverid'] = $selData['receiverid'];
		$retArr[$selData['mailid']]['senderid'] = $selData['senderid'];
		$retArr[$selData['mailid']]['sentwhen'] = $selData['sentwhen'];
		$retArr[$selData['mailid']]['openedwhen'] = $selData['openedwhen'];
		$retArr[$selData['mailid']]['subject'] = $selData['subject'];
		$retArr[$selData['mailid']]['priority'] = $selData['priority'];
		$retArr[$selData['mailid']]['msgbody'] = $selData['msgbody'];
		$retArr[$selData['mailid']]['threadid'] = $selData['threadid'];
		$retArr[$selData['mailid']]['msgstatus'] = $selData['msgstatus'];
	}
	return $retArr;
}

function getUserPriorityMailboxItems($UserID)
{
	global $dbCon;
	
	$selQry = 'SELECT mailid, receiverid, senderid, sentwhen, openedwhen, subject, priority, msgbody, threadid, msgstatus FROM usermailbox WHERE receiverid = ' . $UserID . ' AND priority >= 5 AND openedwhen IS NULL ORDER BY priority DESC, sentwhen';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['mailid']]['mailid'] = $selData['mailid'];
		$retArr[$selData['mailid']]['receiverid'] = $selData['receiverid'];
		$retArr[$selData['mailid']]['senderid'] = $selData['senderid'];
		$retArr[$selData['mailid']]['sentwhen'] = $selData['sentwhen'];
		$retArr[$selData['mailid']]['openedwhen'] = $selData['openedwhen'];
		$retArr[$selData['mailid']]['subject'] = $selData['subject'];
		$retArr[$selData['mailid']]['priority'] = $selData['priority'];
		$retArr[$selData['mailid']]['msgbody'] = $selData['msgbody'];
		$retArr[$selData['mailid']]['threadid'] = $selData['threadid'];
		$retArr[$selData['mailid']]['msgstatus'] = $selData['msgstatus'];
	}
	return $retArr;
}

function getUserUnreadMailboxItemCount($UserID)
{
	global $dbCon;
	
	$selQry = 'SELECT COUNT(mailid) AS mailcount FROM usermailbox WHERE receiverid = ' . $UserID . ' AND openedwhen IS NULL';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$selData = mysqli_fetch_array($selRes);
	return $selData['mailcount'];
}

function markMailItemRead($MailID)
{
	global $dbCon;
	
	$selQry = 'UPDATE usermailbox SET openedwhen = NOW() WHERE mailid = ' . $MailID . ' AND openedwhen IS NULL';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
}

function deleteMailItem($MailID)
{
	global $dbCon;
	
	$selQry = 'UPDATE usermailbox SET msgstatus = -99 WHERE mailid = ' . $MailID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
}
?>