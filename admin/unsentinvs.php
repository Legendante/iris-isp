<?php
include("header.inc.php");

$Invs = getUnsentInvoices();
if(count($Invs) > 0)
{
	// print_r($Invs);
	$CustomerIDList = array();
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
	echo "<form method='POST' action='sendInvoices.php'>";
	echo "<table class='table table-bordered table-condensed table-hover'>";
	echo "<tr><th colspan='9'>Unsent Invoices</th></tr>";
	echo "<tr><td>#</td><th>ID</th><th>Customer</th><th>Unit</th><th>Invoice Date</th><th>Non-VAT Total</th><th>Total (VAT Incl.)</th><th>File</th><th></th></tr>";
	$Cnt = 1;
	foreach($Invs AS $InvoiceID => $InvRec)
	{
		echo "<tr>";
		echo "<td>" . $Cnt . "</td>";
		echo "<td>" . $InvRec['customerid'] . "</td>";
		echo "<td><a href='customer.php?cid=" . $InvRec['customerid'] . "'>" . $Customers[$InvRec['customerid']]['customername'] . " " . $Customers[$InvRec['customerid']]['customersurname'] . "</a></td>";
		echo "<td>" . $UnitArr[$InvRec['customerid']]['unitnumber'] . " " . $Complexes[$UnitArr[$InvRec['customerid']]['complexid']]['complexname'] . "</td>";
		echo "<td>" . substr($InvRec['invoicedate'], 0, 10) . "</td><td>R " . $InvRec['nonvattotal'] . "</td><td>R " . $InvRec['vattotal'] . "</td><td><a href='" . $InvRec['filepath'] . "'>PDF</a></td>";
		echo "<td><input type='checkbox' name='send[]' id='send_" . $InvoiceID . "' value='" . $InvoiceID . "'>";
		echo "<a href='deleteInvoice.php?s=1&i=" . $InvoiceID . "' title='Delete invoice' class='text-danger pull-right'><i class='fa fa-times'></i></a></td>";
		echo "</tr>";
		$Cnt++;
	}
	echo "<tr><th colspan='9'><button type='submit' class='btn btn-success pull-right'><i class='fa fa-send'></i> Send</button></th></tr>";
	echo "</table>";
	echo "</form>";
}
else
	echo "No invoices to show<br>";
// print_r($Invs);
include("footer.inc.php");
?>