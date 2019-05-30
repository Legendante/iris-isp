<?php
// define('FPDF_FONTPATH','font/');
// require('fpdf.php');

// class PDF extends FPDF
// {
	// public function Header()
	// {
		// $TopY = 30;
		// $this->SetDrawColor(77,4,140);
		// $this->SetLineWidth(0.5);
		// $this->Line(10, 13, 10, 285);
		// $this->SetLineWidth(0.3);
		// $this->SetDrawColor(140,145,146);
		// $this->Line(8, 275, 198, 275);
		// $this->SetLineWidth(0.1);
		// $this->SetDrawColor(0);
	// }

	// public function Footer()
	// {
		// $TopY = 276;
		// $this->SetFont('Arial','',8);
		// $this->SetXY(14, $TopY);
		// $this->SetTextColor(0);
		// $this->Cell(35, 5, "Company (PTY) Ltd.", 0);
		// $this->Cell(29, 5, "2016/123456/07", 0);
		// $TopY += 4;
		// $this->SetXY(15, $TopY);
		// $this->SetTextColor(77,4,140);
		// $this->Cell(3, 5, "(t)", 0, 0, 'C');
		// $this->SetTextColor(0);
		// $this->Cell(29, 5, "+27 (0) 11 123 4567", 0);
		// $this->SetTextColor(77,4,140);
		// $this->Cell(3, 5, "(w)", 0, 0, 'C');
		// $this->SetTextColor(0);
		// $this->Cell(28, 5, "www.domain.com", 0);
		// $this->SetTextColor(77,4,140);
		// $this->Cell(3, 5, "(e)", 0, 0, 'C');
		// $this->SetTextColor(0);
		// $this->Cell(29, 5, "hello@domain.com", 0);
		// $this->SetTextColor(77,4,140);
		// $this->Cell(3, 5, "(a)", 0, 0, 'C');
		// $this->SetTextColor(0);
		// $this->Cell(71, 5, "23 Skyways Business Park, Freda Rd, Randburg, 2188", 0);
		// $IniLogo = "images/brandicon.png";
		// $this->Image($IniLogo, 187, 276, 10);
	// }
// }

function createInvoice($SaveArr)
{
	global $dbCon;

	$selQry = 'SELECT MAX(invoicecnt) AS maxcnt FROM invoices WHERE customerid = ' . $SaveArr['customerid'];
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$selData = mysqli_fetch_array($selRes);
	$InvoiceCnt = 0;
	if($selData['maxcnt'] != '')
		$InvoiceCnt = $selData['maxcnt'];
	$InvoiceCnt++;
	$SaveArr['invoicecnt'] = $InvoiceCnt;

	$fieldA = array();
	$valueA = array();
	$cnt = 0;
	foreach($SaveArr AS $Field => $Value)
	{
		$fieldA[$cnt] = $Field;
		$valueA[$cnt] = '"' . $Value . '"';
		$cnt++;
	}
	if(!isset($SaveArr['invoicedate']))
	{
		$fieldA[$cnt] = 'invoicedate';
		$valueA[$cnt] = 'NOW()';
		$cnt++;
	}
	if(!isset($SaveArr['invstatus']))
	{
		$fieldA[$cnt] = 'invstatus';
		$valueA[$cnt] = '1';
		$cnt++;
	}
	$updQry = 'INSERT INTO invoices(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	// exit($updQry);
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function getRefererAmount($RefererID)
{
	global $dbCon;

	$selQry = 'SELECT SUM(prevatsalesprice) AS commsum FROM unitorderdetails WHERE historyserial = 0 AND customerid IN (SELECT customerid FROM customerdetails WHERE refererid = ' . $RefererID . ')';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$selData = mysqli_fetch_array($selRes);
	return $selData['commsum'];
}

function updateInvoice($InvoiceID, $SaveArr)
{
	global $dbCon;

	$updStr = '';
	foreach($SaveArr AS $Field => $Value)
	{
		if($updStr != '')
			$updStr .= ', ';
		$updStr .= $Field . ' = "' . $Value . '"';
	}

	$updQry = 'UPDATE invoices SET ' . $updStr . ' WHERE invoiceid = ' . $InvoiceID;
	$selRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}

function addInvoiceItem($SaveArr)
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
	$updQry = 'INSERT INTO invoiceitems(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	// exit($updQry);
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function getFundAccounts($AccountIDs = '')
{
	global $dbCon;

	$selQry = 'SELECT accountid, accountname, accountbalance FROM fundaccounts ';
	if($AccountIDs != '')
		$selQry .= 'WHERE accountid IN (' . $AccountIDs . ') ';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['accountid']]['accountid'] = $selData['accountid'];
		$retArr[$selData['accountid']]['accountname'] = $selData['accountname'];
		$retArr[$selData['accountid']]['accountbalance'] = $selData['accountbalance'];
	}
	return $retArr;
}

function updateFundAccount($AccountID, $SaveArr)
{
	global $dbCon;

	$updStr = '';
	foreach($SaveArr AS $Field => $Value)
	{
		if($updStr != '')
			$updStr .= ', ';
		$updStr .= $Field . ' = "' . $Value . '"';
	}

	$updQry = 'UPDATE fundaccounts SET ' . $updStr . ' WHERE accountid = ' . $AccountID;
	$selRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}

function addFundAccount($SaveArr)
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
	$updQry = 'INSERT INTO fundaccounts(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	// exit($updQry);
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function addFundAccountTransaction($SaveArr)
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
	if(!isset($SaveArr['transactiondate']))
	{
		$fieldA[$cnt] = 'transactiondate';
		$valueA[$cnt] = 'NOW()';
		$cnt++;
	}
	
	
	$updQry = 'INSERT INTO fundaccounttransactions(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	// exit($updQry);
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function getSuppliers($SupplierIDs = '')
{
	global $dbCon;
	
	$selQry = 'SELECT supplierid, suppliername, suppliervatnum, supplierregnum, supplieraddress, supplieremail, suppliertel, supplierbalance FROM suppliers ';
	if($SupplierIDs != '')
		$selQry .= 'WHERE supplierid IN (' . $SupplierIDs . ') ';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['supplierid']]['supplierid'] = $selData['supplierid'];
		$retArr[$selData['supplierid']]['suppliername'] = $selData['suppliername'];
		$retArr[$selData['supplierid']]['suppliervatnum'] = $selData['suppliervatnum'];
		$retArr[$selData['supplierid']]['supplierregnum'] = $selData['supplierregnum'];
		$retArr[$selData['supplierid']]['supplieraddress'] = $selData['supplieraddress'];
		$retArr[$selData['supplierid']]['supplieremail'] = $selData['supplieremail'];
		$retArr[$selData['supplierid']]['suppliertel'] = $selData['suppliertel'];
		$retArr[$selData['supplierid']]['supplierbalance'] = $selData['supplierbalance'];
	}
	return $retArr;
}

function updateSupplier($SupplierID, $SaveArr)
{
	global $dbCon;

	$updStr = '';
	foreach($SaveArr AS $Field => $Value)
	{
		if($updStr != '')
			$updStr .= ', ';
		$updStr .= $Field . ' = "' . $Value . '"';
	}

	$updQry = 'UPDATE suppliers SET ' . $updStr . ' WHERE supplierid = ' . $SupplierID;
	$selRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}

function addSupplier($SaveArr)
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
	$updQry = 'INSERT INTO suppliers(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	// exit($updQry);
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}


function getInvoiceItemsByInvoiceID($InvoiceID)
{
	global $dbCon;

	$selQry = 'SELECT itemid, invoiceid, unitid, vattotal, nonvatotal, itemqty, itemdesc FROM invoiceitems WHERE invoiceid = ' . $InvoiceID;
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['itemid']]['itemid'] = $selData['itemid'];
		$retArr[$selData['itemid']]['invoiceid'] = $selData['invoiceid'];
		$retArr[$selData['itemid']]['unitid'] = $selData['unitid'];
		$retArr[$selData['itemid']]['vattotal'] = $selData['vattotal'];
		$retArr[$selData['itemid']]['nonvatotal'] = $selData['nonvatotal'];
		$retArr[$selData['itemid']]['itemqty'] = $selData['itemqty'];
		$retArr[$selData['itemid']]['itemdesc'] = $selData['itemdesc'];
	}
	return $retArr;
}

function getInvoices($InvoiceIDs = '')
{
	global $dbCon;

	$selQry = 'SELECT invoiceid, customerid, orderid, invoicedate, vattotal, nonvattotal, datestart, dateend, invoicecnt, invstatus, invtype, filepath, outstanding FROM invoices ';
	if($InvoiceIDs != '')
		$selQry .= 'WHERE invoiceid IN (' . $InvoiceIDs . ')';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['invoiceid']]['invoiceid'] = $selData['invoiceid'];
		$retArr[$selData['invoiceid']]['customerid'] = $selData['customerid'];
		$retArr[$selData['invoiceid']]['orderid'] = $selData['orderid'];
		$retArr[$selData['invoiceid']]['invoicedate'] = $selData['invoicedate'];
		$retArr[$selData['invoiceid']]['vattotal'] = $selData['vattotal'];
		$retArr[$selData['invoiceid']]['nonvattotal'] = $selData['nonvattotal'];
		$retArr[$selData['invoiceid']]['datestart'] = $selData['datestart'];
		$retArr[$selData['invoiceid']]['dateend'] = $selData['dateend'];
		$retArr[$selData['invoiceid']]['invoicecnt'] = $selData['invoicecnt'];
		$retArr[$selData['invoiceid']]['invstatus'] = $selData['invstatus'];
		$retArr[$selData['invoiceid']]['invtype'] = $selData['invtype'];
		$retArr[$selData['invoiceid']]['filepath'] = $selData['filepath'];
		$retArr[$selData['invoiceid']]['outstanding'] = $selData['outstanding'];
	}
	return $retArr;
}

function getInvoicesByStatus($Status)
{
	global $dbCon;

	$selQry = 'SELECT invoiceid, customerid, orderid, invoicedate, vattotal, nonvattotal, datestart, dateend, invoicecnt, invstatus, invtype, filepath, outstanding FROM invoices ';
	$selQry .= 'WHERE invstatus = ' . $Status . ' ORDER BY customerid, invoicedate ASC';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['invoiceid']]['invoiceid'] = $selData['invoiceid'];
		$retArr[$selData['invoiceid']]['customerid'] = $selData['customerid'];
		$retArr[$selData['invoiceid']]['orderid'] = $selData['orderid'];
		$retArr[$selData['invoiceid']]['invoicedate'] = $selData['invoicedate'];
		$retArr[$selData['invoiceid']]['vattotal'] = $selData['vattotal'];
		$retArr[$selData['invoiceid']]['nonvattotal'] = $selData['nonvattotal'];
		$retArr[$selData['invoiceid']]['datestart'] = $selData['datestart'];
		$retArr[$selData['invoiceid']]['dateend'] = $selData['dateend'];
		$retArr[$selData['invoiceid']]['invoicecnt'] = $selData['invoicecnt'];
		$retArr[$selData['invoiceid']]['invstatus'] = $selData['invstatus'];
		$retArr[$selData['invoiceid']]['invtype'] = $selData['invtype'];
		$retArr[$selData['invoiceid']]['filepath'] = $selData['filepath'];
		$retArr[$selData['invoiceid']]['outstanding'] = $selData['outstanding'];
	}
	return $retArr;
}

function getUnpaidInvoices()
{
	return getInvoicesByStatus(3);
}

function getPartialPaidInvoices()
{
	return getInvoicesByStatus(4);
}

function getUnsentInvoices()
{
	return getInvoicesByStatus(1);
}

function getInvoicesToEmail()
{
	return getInvoicesByStatus(2);
}

function getCustomerNumber($CustomerID)
{
	$CustomerRec = getCustomersByIDList($CustomerID);
	return 'CX' . substr($CustomerRec[$CustomerID]['dateregistered'], 2,2) . sprintf("%06d", $CustomerID);
}

function addCreditNote($SaveArr)
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
	if(!isset($SaveArr['creditdate']))
	{
		$fieldA[$cnt] = 'creditdate';
		$valueA[$cnt] = 'NOW()';
		$cnt++;
	}
	$updQry = 'INSERT INTO creditnotes(' . implode(",", $fieldA) . ') VALUES (' . implode(",", $valueA) . ')';
	// exit($updQry);
	$updRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
	return mysqli_insert_id($dbCon);
}

function updateCreditNote($CreditID, $SaveArr)
{
	global $dbCon;

	$updStr = '';
	foreach($SaveArr AS $Field => $Value)
	{
		if($updStr != '')
			$updStr .= ', ';
		$updStr .= $Field . ' = "' . $Value . '"';
	}

	$updQry = 'UPDATE creditnotes SET ' . $updStr . ' WHERE creditid = ' . $CreditID;
	$selRes = mysqli_query($dbCon, $updQry) or logDBError(mysqli_error($dbCon), $updQry, __FILE__, __FUNCTION__, __LINE__);
}

function getCustomerCreditNotes($CustomerID, $Status = '')
{
	global $dbCon;
		
	$selQry = 'SELECT creditid, customerid, creditby, creditdate, creditamount, creditstatus, creditdescription FROM creditnotes ';
	$selQry .= 'WHERE customerid = ' . $CustomerID . ' ';
	if($Status != '')
		$selQry .= 'AND creditstatus = ' . $Status . ' ';
	$selQry .= 'ORDER BY creditdate ASC';
	$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
	$retArr = array();
	while($selData = mysqli_fetch_array($selRes))
	{
		$retArr[$selData['creditid']]['creditid'] = $selData['creditid'];
		$retArr[$selData['creditid']]['customerid'] = $selData['customerid'];
		$retArr[$selData['creditid']]['creditby'] = $selData['creditby'];
		$retArr[$selData['creditid']]['creditdate'] = $selData['creditdate'];
		$retArr[$selData['creditid']]['creditamount'] = $selData['creditamount'];
		$retArr[$selData['creditid']]['creditstatus'] = $selData['creditstatus'];
		$retArr[$selData['creditid']]['creditdescription'] = $selData['creditdescription'];
	}
	return $retArr;
}

function genInvoiceFile($InvoiceID)
{
	$Suburbs = getSuburbs();
	$OutPutType = 'F';
	$InvoiceRec = getInvoices($InvoiceID);
	$CustomerID = $InvoiceRec[$InvoiceID]['customerid'];
	$CustomerRec = getCustomersByIDList($CustomerID);
	$UnitRec = getCustomerUnits($CustomerID);
	$UnitRec = reset($UnitRec);
	$ComplexRec = getComplexByID($UnitRec['complexid']);
	$InvItems = getInvoiceItemsByInvoiceID($InvoiceID);
	$CustomerNum = getCustomerNumber($CustomerID);
	$SaveDir = 'uploads/invoices/' . date("Ymd") . '/';
	$FileName = $SaveDir . $CustomerNum . '_inv.pdf';

	if(!file_exists($SaveDir))
	{
		if(!mkdir($SaveDir))
			exit("Failed to create save directory: " . $SaveDir . "<br>" . getcwd());
	}

	$pdf = new PDF();
	$pdf->AliasNbPages();
	$pdf->AddPage('P');

	$TopY = 30;
	$IniLogo = "images/logo 658x150.png";
	$pdf->Image($IniLogo, 15, 13, 80);
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY(19, $TopY);
	$pdf->SetTextColor(77,4,140);
	$pdf->Cell(3, 5, "(w)", 0, 0, 'C');
	$pdf->SetTextColor(0);
	$pdf->Cell(28, 5, "www.domain.com", 0);
	$pdf->SetXY(60, $TopY);
	$pdf->SetTextColor(77,4,140);
	$pdf->Cell(3, 5, "(e)", 0, 0, 'C');
	$pdf->SetTextColor(0);
	$pdf->Cell(29, 5, "hello@domain.com", 0);
	$TopY = 16;
	$pdf->SetFont('Arial','',10);
	$pdf->SetXY(100, $TopY);
	$pdf->Cell(35, 5, "Company (PTY) Ltd.", 0);
	$TopY += 5;
	$pdf->SetXY(100, $TopY);
	$pdf->Cell(29, 5, "Reg No. 2016/515188/07", 0);
	$TopY += 5;
	$pdf->SetXY(100, $TopY);
	$pdf->Cell(29, 5, "Vat Reg No. 48 0027 6166", 0);
	$TopY = 16;
	$pdf->SetXY(150, $TopY);
	$pdf->Cell(40, 5, "23 Skyways Business Park,", 0);
	$TopY += 5;
	$pdf->SetXY(150, $TopY);
	$pdf->Cell(40, 5, "Freda Rd,", 0);
	$TopY += 5;
	$pdf->SetXY(150, $TopY);
	$pdf->Cell(40, 5, "Randburg, 2188", 0);
	$TopY += 12;
	$pdf->SetFont('Arial','B',18);
	$pdf->SetXY(100, $TopY);
	$pdf->Cell(100, 10, "TAX INVOICE", 1, 0, 'C');
	$TopY += 10;
	$pdf->SetXY(100, $TopY);
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(50, 6, "Invoice Date", 1, 0, 'R');
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY(150, $TopY);
	$pdf->Cell(50, 6, date("d M Y"), 1, 0, 'C');
	$pdf->SetFont('Arial','B',12);
	$TopY += 6;
	$pdf->SetXY(100, $TopY);
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(50, 6, "Invoice #", 1, 0, 'R');
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY(150, $TopY);
	$pdf->Cell(50, 6, $InvoiceRec[$InvoiceID]['invoicecnt'], 1, 0, 'C');
	$pdf->SetFont('Arial','B',12);
	$TopY = 65;
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY(15, $TopY);
	$pdf->Cell(50, 5, $CustomerRec[$CustomerID]['customername'] . " " . $CustomerRec[$CustomerID]['customersurname']);
	$pdf->SetFont('Arial','B',12);
	$pdf->SetXY(90, $TopY);
	$pdf->Cell(50, 6, "Customer No.", 0, 0, 'R');
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY(142, $TopY);
	$pdf->Cell(50, 6, $CustomerNum, 0, 0);
	$pdf->SetFont('Arial','',12);
	$TopY += 6;
	$pdf->SetXY(15, $TopY);
	$pdf->Cell(50, 5, $UnitRec['unitnumber'] . " " . $ComplexRec['complexname']);
	$pdf->SetFont('Arial','B',12);
	$pdf->SetXY(90, $TopY);
	$pdf->Cell(50, 6, "Date Registered:", 0, 0, 'R');
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY(142, $TopY);
	$pdf->Cell(50, 6, substr($CustomerRec[$CustomerID]['dateregistered'], 0, 10), 0, 0);
	$pdf->SetFont('Arial','',12);
	$TopY += 6;
	$pdf->SetXY(15, $TopY);
	$pdf->Cell(50, 5, $Suburbs[$ComplexRec['suburbid']]['suburbname'] . " " . $ComplexRec['streetaddress5'], 0, 0);
	$pdf->SetFont('Arial','B',12);
	$pdf->SetXY(90, $TopY);
	$pdf->Cell(50, 6, "Period:", 0, 0, 'R');
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY(142, $TopY);
	// $pdf->Cell(50, 6, date("d M Y", strtotime($InvoiceRec['datestart'])) . " - " . date("d M Y", strtotime($InvoiceRec['dateend'])), 0, 0);
	$pdf->Cell(50, 6, date("d M Y", strtotime($InvoiceRec[$InvoiceID]['datestart'])) . " - " . date("d M Y", strtotime($InvoiceRec[$InvoiceID]['dateend'])), 0, 0);
	$pdf->SetFont('Arial','',12);
	$TopY += 10;
	$pdf->SetFont('Arial','B',12);
	$pdf->SetXY(16, $TopY);
	$pdf->Cell(80, 10, "Description", 1, 0, 'C');
	$pdf->SetXY(96, $TopY);
	$pdf->Cell(30, 10, "Qty", 1, 0, 'C');
	$pdf->SetXY(126, $TopY);
	$pdf->Cell(30, 10, "Unit Cost", 1, 0, 'C');
	$pdf->SetXY(156, $TopY);
	$pdf->Cell(40, 10, "Sub Total", 1, 0, 'C');

	$SubTotal = 0;
	foreach($InvItems AS $ItemID => $ItemRec)
	{
		$TopY += 10;
		$pdf->SetFont('Arial','',12);
		$pdf->SetXY(16, $TopY);
		$pdf->Cell(110, 10, $ItemRec['itemdesc'], 1, 0, 'L');
		$pdf->SetXY(96, $TopY);
		$pdf->Cell(30, 10, $ItemRec['itemqty'], 1, 0, 'C');
		$pdf->SetXY(126, $TopY);
		$pdf->Cell(30, 10, "R " . sprintf("%0.2f", $ItemRec['nonvatotal']), 1, 0, 'C');
		$pdf->SetXY(156, $TopY);
		$pdf->Cell(40, 10, "R " . sprintf("%0.2f", ($ItemRec['itemqty'] * $ItemRec['nonvatotal'])), 1, 0, 'C');
		$SubTotal += $ItemRec['itemqty'] * $ItemRec['nonvatotal'];
	}
	$Vat = sprintf("%0.2f", $SubTotal * 0.14);
	$Total = sprintf("%0.2f", $SubTotal + $Vat);
	$TopY += 10;
	$pdf->SetXY(126, $TopY);
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(30, 10, "14% VAT", 1, 0, 'C');
	$pdf->SetXY(156, $TopY);
	$pdf->SetFont('Arial','',12);
	$pdf->Cell(40, 10, "R " . $Vat, 1, 0, 'C');
	$TopY += 10;
	$pdf->SetXY(126, $TopY);
	$pdf->SetFont('Arial','B',14);
	$pdf->Cell(30, 10, "Total", 1, 0, 'C');
	$pdf->SetXY(156, $TopY);
	$pdf->SetFont('Arial','',12);
	$pdf->Cell(40, 10, "R " . $Total, 1, 0, 'C');
	$TopY = 200;
	$RememberTop1 = $TopY;
	$pdf->SetXY(20, $TopY);
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(90, 6, "Direct Deposit", 1, 0, 'C');
	$pdf->SetFont('Arial','',12);
	$TopY += 6;
	$pdf->SetXY(20, $TopY);
	$pdf->Cell(40, 6, "Bank:", 1, 0, 'R');
	$pdf->SetXY(60, $TopY);
	$pdf->Cell(50, 6, "Bank", 1, 0, 'C');
	$TopY += 6;
	$pdf->SetXY(20, $TopY);
	$pdf->Cell(40, 6, "Account Name:", 1, 0, 'R');
	$pdf->SetXY(60, $TopY);
	$pdf->Cell(50, 6, "Company (PTY) Ltd", 1, 0, 'C');
	$TopY += 6;
	$pdf->SetXY(20, $TopY);
	$pdf->Cell(40, 6, "Account No:", 1, 0, 'R');
	$pdf->SetXY(60, $TopY);
	$pdf->Cell(50, 6, "12 345 6789", 1, 0, 'C');
	$TopY += 6;
	$pdf->SetXY(20, $TopY);
	$pdf->Cell(40, 6, "Branch Code:", 1, 0, 'R');
	$pdf->SetXY(60, $TopY);
	$pdf->Cell(50, 6, "123456", 1, 0, 'C');
	$TopY += 6;
	$pdf->SetXY(20, $TopY);
	$pdf->Cell(40, 6, "Reference:", 1, 0, 'R');
	$pdf->SetXY(60, $TopY);
	$pdf->Cell(50, 6, $CustomerNum, 1, 0, 'C');
	$TopY = $RememberTop1;
	$TopY += 3;
	$pdf->SetXY(120, $TopY);
	$pdf->Cell(70, 6, "Login to your customer zone", 0, 0, 'C');
	$TopY += 6;
	$pdf->SetXY(120, $TopY);
	$pdf->Cell(70, 6, "for more payment options", 0, 0, 'C');
	$TopY += 6;
	$pdf->SetXY(120, $TopY);
	$pdf->SetFont('Arial','U',12);
	$pdf->Cell(70, 6, "http://clientzone.domain.com/", 0, 0, 'C');
	$pdf->SetFont('Arial','',12);
	$TopY += 12;
	$pdf->SetXY(120, $TopY);
	$pdf->Cell(70, 6, "Your registered email address", 0, 0, 'C');
	$TopY += 6;
	$pdf->SetXY(120, $TopY);
	$pdf->SetFont('Arial','U',12);
	$pdf->Cell(70, 6, $CustomerRec[$CustomerID]['email1'], 0, 0, 'C');
	$pdf->SetFont('Arial','',10);
	$TopY += 12;
	$pdf->SetXY(15, $TopY);
	$LongText = "Please use your customer number, " . $CustomerNum . ", as the beneficiary payment reference. If you use the incorrect payment reference, your payment may not be allocated correctly which could lead to your account being suspended.";
	$pdf->MultiCell(180, 5, $LongText, 0);
	$pdf->Output($FileName, $OutPutType);
	unset($pdf);
	return $FileName;
}
?>