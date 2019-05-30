<?php
include("db.inc.php");
$TransArr = array();
$InvoiceArr = array();
foreach($_POST AS $Key => $Val)
{
	if((substr($Key, 0, 4) == 'pay_') && ($Val != ''))
	{
		$Inv = substr($Key, 4);
		$InvoiceArr[$Inv] = $Inv;
		if(!isset($TransArr[$Inv]))
			$TransArr[$Inv]['acc'] = 0;
		$TransArr[$Inv]['amount'] = $Val;
	}
	elseif(substr($Key, 0, 4) == 'acc_')
	{
		$Inv = substr($Key, 4);
		$InvoiceArr[$Inv] = $Inv;
		if(!isset($TransArr[$Inv]))
			$TransArr[$Inv]['amount'] = 0;
		$TransArr[$Inv]['acc'] = $Val;
	}
}
$Invoices = getInvoices(implode(",", $InvoiceArr));
foreach($TransArr AS $InvoiceID => $InvRec)
{
	if($InvRec['amount'] > 0)
	{
		$CustomerID = $Invoices[$InvoiceID]['customerid'];
		$SaveArr = array();
		$InvOutstanding = $Invoices[$InvoiceID]['outstanding'];
		if($InvOutstanding > $InvRec['amount'])
		{
			$SaveArr['invstatus'] = 4;
			$SaveArr['outstanding'] = $InvOutstanding - $InvRec['amount'];
		}
		elseif($InvOutstanding <= $InvRec['amount'])
		{
			$SaveArr['invstatus'] = 5;
			$SaveArr['outstanding'] = 0;
		}
		// echo $InvOutstanding . " :: " . $InvRec['amount'] . "<br>";
		updateInvoice($InvoiceID, $SaveArr);
		$TransArr = array();
		$TransArr['accountid'] = $InvRec['acc'];
		$TransArr['customerid'] = $CustomerID;
		$TransArr['transactionamount'] = $InvRec['amount'];
		addFundAccountTransaction($TransArr);
		$FundAcc = getFundAccounts($InvRec['acc']);
		$FundArr = array();
		$FundArr['accountbalance'] = $FundAcc[$InvRec['acc']]['accountbalance'] + $InvRec['amount'];
		updateFundAccount($InvRec['acc'], $FundArr);
		$updQry = 'UPDATE customerdetails SET customerbalance = customerbalance - \'' . $InvRec['amount'] . '\' WHERE customerid = ' . $CustomerID;
		$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	}
}
header("Location: unpaidinvs.php");
?>