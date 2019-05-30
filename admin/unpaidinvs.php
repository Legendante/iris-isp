<?php
include("header.inc.php");

$Invs = getUnpaidInvoices();
$LookCnt = 0;
echo "<form method='POST' action='logPayments.php'>";
if(count($Invs) > 0)
{
	$LookCnt = 1;
	$Accounts = getFundAccounts();
	$AccDDStr = '';
	foreach($Accounts AS $ID => $Rec)
	{
		$AccDDStr .= "<option value='" . $ID . "'>" . $Rec['accountname'] . "</option>\n";
	}
	// print_r($Invs);
	$CustomerIDList = array();
	$NonVatTotal = 0;
	$VatTotal = 0;
	$UnitArr = array();
	$ComplexIDArr = array();
	foreach($Invs AS $InvoiceID => $Inv)
	{
		$CustomerIDList[] = $Inv['customerid'];
		$Units = getCustomerUnits($Inv['customerid']);
		$UnitID = 0;
		foreach($Units AS $UID => $UnitRec)
		{
			$UnitID = $UID;
			$UnitArr[$Inv['customerid']] = $UnitRec;
			$ComplexIDArr[$UnitRec['complexid']] = $UnitRec['complexid'];
		}
	}
	$Complexes = getComplexesByIDList(implode(",", $ComplexIDArr));
	$Customers = getCustomersByIDList(implode(",", $CustomerIDList));
	// print_r($Invs);
	echo "<table class='table table-bordered table-condensed table-hover'>";
	echo "<tr><th colspan='8'>Unpaid Invoices</th><th colspan='3'><a href='excelUnpaidInvs.php' class='pull-right'><i class='fa fa-file-excel-o'></i></a></th></tr>";
	echo "<tr><th>ID</th><th>Customer</th><th>Unit</th><th>Balance</th><th>Invoice Date</th><th>Non-VAT Total</th><th>Total (VAT Incl.)</th><th>Outstanding</th><th>File</th><th>Payment Amount</th><th></th></tr>";
	foreach($Invs AS $InvoiceID => $InvRec)
	{
		
		echo "<tr><td>" . $InvRec['customerid'] . "</td>";
		echo "<td><a href='customer.php?cid=" . $InvRec['customerid'] . "'>" . $Customers[$InvRec['customerid']]['customername'] . " " . $Customers[$InvRec['customerid']]['customersurname'] . "</a></td>";
		echo "<td>" . $UnitArr[$InvRec['customerid']]['unitnumber'] . " " . $Complexes[$UnitArr[$InvRec['customerid']]['complexid']]['complexname'] . "</td>";
		echo "<td>" . $Customers[$InvRec['customerid']]['customerbalance'] . "</td>";
		echo "<td>" . substr($InvRec['invoicedate'], 0, 10) . "</td><td>R " . $InvRec['nonvattotal'] . "</td><td>R " . $InvRec['vattotal'] . "</td>";
		echo "<td>R " . $InvRec['outstanding'] . "</td>";
		echo "<td><a href='" . $InvRec['filepath'] . "'>PDF</a></td>";
		echo "<td><input type='text' name='pay_" . $InvoiceID . "' id='pay_" . $InvoiceID . "' value='' class='form-control'>";
		echo "<select name='acc_" . $InvoiceID . "' id='acc_" . $InvoiceID . "' class='form-control'>" . $AccDDStr . "</select>";
		echo "</td>";
		echo "<td><a href='deleteInvoice.php?s=2&i=" . $InvoiceID . "' title='Delete invoice' class='text-danger pull-right'><i class='fa fa-times'></i></a></td>";
		echo "</tr>";
		$NonVatTotal += $InvRec['nonvattotal'];
		$VatTotal += $InvRec['vattotal'];
	}
	echo "<tr><th colspan='4'>Totals</th><td>R " . $NonVatTotal . "</td><td>R " . $VatTotal . "</td>";
	echo "<td colspan='2'><button type='submit' class='btn btn-success pull-right'><i class='fa fa-save'> Save payments</i></button></td></tr>";
	echo "</table>";
}
$Invs = getPartialPaidInvoices();
if(count($Invs) > 0)
{
	$LookCnt = 1;
	$Accounts = getFundAccounts();
	$AccDDStr = '';
	foreach($Accounts AS $ID => $Rec)
	{
		$AccDDStr .= "<option value='" . $ID . "'>" . $Rec['accountname'] . "</option>\n";
	}
	// print_r($Invs);
	$CustomerIDList = array();
	$NonVatTotal = 0;
	$VatTotal = 0;
	$UnitArr = array();
	$ComplexIDArr = array();
	foreach($Invs AS $InvoiceID => $Inv)
	{
		$CustomerIDList[] = $Inv['customerid'];
		$Units = getCustomerUnits($Inv['customerid']);
		$UnitID = 0;
		foreach($Units AS $UID => $UnitRec)
		{
			$UnitID = $UID;
			$UnitArr[$Inv['customerid']] = $UnitRec;
			$ComplexIDArr[$UnitRec['complexid']] = $UnitRec['complexid'];
		}
	}
	$Complexes = getComplexesByIDList(implode(",", $ComplexIDArr));
	$Customers = getCustomersByIDList(implode(",", $CustomerIDList));
	// print_r($Invs);
	echo "<table class='table table-bordered table-condensed table-hover'>";
	echo "<tr><th colspan='8'>Partially Paid Invoices</th></tr>";
	echo "<tr><th>ID</th><th>Customer</th><th>Unit</th><th>Invoice Date</th><th>Non-VAT Total</th><th>Total (VAT Incl.)</th><th>Outstanding</th><th>File</th><th>Payment Amount</th><th></th></tr>";
	foreach($Invs AS $InvoiceID => $InvRec)
	{
		
		echo "<tr><td>" . $InvRec['customerid'] . "</td>";
		echo "<td><a href='customer.php?cid=" . $InvRec['customerid'] . "'>" . $Customers[$InvRec['customerid']]['customername'] . " " . $Customers[$InvRec['customerid']]['customersurname'] . "</a></td>";
		echo "<td>" . $UnitArr[$InvRec['customerid']]['unitnumber'] . " " . $Complexes[$UnitArr[$InvRec['customerid']]['complexid']]['complexname'] . "</td>";
		echo "<td>" . substr($InvRec['invoicedate'], 0, 10) . "</td><td>R " . $InvRec['nonvattotal'] . "</td><td>R " . $InvRec['vattotal'] . "</td>";
		echo "<td>R " . $InvRec['outstanding'] . "</td>";
		echo "<td><a href='" . $InvRec['filepath'] . "'>PDF</a></td>";
		echo "<td><input type='text' name='pay_" . $InvoiceID . "' id='pay_" . $InvoiceID . "' value='' class='form-control'>";
		echo "<select name='acc_" . $InvoiceID . "' id='acc_" . $InvoiceID . "' class='form-control'>" . $AccDDStr . "</select>";
		echo "</td>";
		echo "<td><a href='deleteInvoice.php?s=2&i=" . $InvoiceID . "' title='Delete invoice' class='text-danger pull-right'><i class='fa fa-times'></i></a></td>";
		echo "</tr>";
		$NonVatTotal += $InvRec['nonvattotal'];
		$VatTotal += $InvRec['vattotal'];
	}
	echo "<tr><th colspan='4'>Totals</th><td>R " . $NonVatTotal . "</td><td>R " . $VatTotal . "</td>";
	echo "<td colspan='2'><button type='submit' class='btn btn-success pull-right'><i class='fa fa-save'> Save payments</i></button></td></tr>";
	echo "</table>";
}
echo "</form>";
if($LookCnt == 0)
	echo "No invoices to show<br>";
// print_r($Invs);
include("footer.inc.php");
?>