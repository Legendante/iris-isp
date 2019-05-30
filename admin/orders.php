<?php
include("header.inc.php");
// $Orders = getUnitOrders();
$ONTs = getONTTypes();
$ONTs[0]['ontname'] = '-';
$PackageArr = getFibrePackages();
$SpeedCost = array();
foreach($PackageArr AS $PackageID => $PackRec)
{
	$SpeedCost[$PackRec['speedid']] = $PackRec['costprice'];
}
$ShowStatusses = array(1,2,4,5,6,7,8,9);
$VendorSpeeds = getPackageSpeeds();
$VendorSpeeds[0] = 'None';
ksort($VendorSpeeds);
$ComplexStepProgress = getAllComplexesMaxSalesOperationsSteps();
$VendorONTs = getONTTypes();
$PO_Orders = getPOOrders();
$NewOrderPOType = getPOTypeIDByName("New Order");
$OrderStatusses = getUnitOrderStatusses();
$OrderStatusses[0] = 'None';
// print_r($OrderStatusses);
$Orders = getComplexOrders();
$LiveComplexes = array();
$LiveOrders = array();
$LiveCount = 0;
$BuildComplexes = array();
$BuildOrders = array();
$BuildCount = array();
$UnitIDList = array();
$PackCounts = array();
$SpeedTotals = array();
$CompSpeedTotals = array();
$ValueTotals = array();
foreach($Orders AS $ComplexID => $UnitArr)
{
	$ComplexIDList[] = $ComplexID;
	foreach($UnitArr AS $UnitID => $OrderRec)
	{
		$UnitIDList[] = $UnitID;
		$CustomerIDList[] = $OrderRec['customerid'];
	}
}
$Units = getUnitsByIDList(implode(",", $UnitIDList));
$Complexes = getComplexesByIDList(implode(",", $ComplexIDList));
$Customers = getCustomersByIDList(implode(",", $CustomerIDList));
$SpeedCost[$PackRec['speedid']] = $PackRec['costprice'];
foreach($Orders AS $ComplexID => $UnitArr)
{
	$ValueTotals[$ComplexID] = array('MRP' => 0, 'IRP' => 0);
	if(!isset($PackCounts[$ComplexID]))
		$PackCounts[$ComplexID] = array();
	if($Complexes[$ComplexID]['statusid'] == 0)
	{
		// do nothing
	}
	elseif($Complexes[$ComplexID]['statusid'] == 47)
	{
		$AddComp = 0;
		foreach($UnitArr AS $UnitID => $OrderRec)
		{
			if(in_array($OrderRec['orderstatus'], $ShowStatusses))
			{
				$Speed = $OrderRec['speedid'];
				if($Speed > 0)
				{
					$AddComp++;
					if(!isset($PackCounts[$ComplexID][$Speed]))
						$PackCounts[$ComplexID][$Speed] = 0;
					$PackCounts[$ComplexID][$Speed]++;
					if(!isset($SpeedTotals[$Speed]))
					{
						//$SpeedTotals[$Speed] = 0;
						$SpeedTotals[$Speed] = array();
						$SpeedTotals[$Speed]['count'] = 0;
						$SpeedTotals[$Speed]['MRP'] = 0;
						$SpeedTotals[$Speed]['IRP'] = 0;
					}
					// $CompSpeedTotals[$Speed]++;
					$SpeedTotals[$Speed]['count'] += 1;
					$LiveCount++;
					$ValueTotals[$ComplexID]['MRP'] += $SpeedCost[$Speed];
					$ValueTotals[$ComplexID]['IRP'] += $OrderRec['prevatsalesprice'];
					$SpeedTotals[$Speed]['MRP'] = $SpeedCost[$Speed];
					if($OrderRec['prevatsalesprice'] > $SpeedTotals[$Speed]['IRP'])
						$SpeedTotals[$Speed]['IRP'] = $OrderRec['prevatsalesprice'];
				}
			}
		}
		if($AddComp > 0)
		{
			$LiveComplexes[$ComplexID] = $ComplexID;
			$LiveOrders[$ComplexID] = $UnitArr;
		}
	}
	else
	{
		$BuildComplexes[$ComplexID] = $ComplexID;
		$BuildOrders[$ComplexID] = $UnitArr;
		$BuildCount[$ComplexID] = 0;
		foreach($UnitArr AS $UnitID => $OrderRec)
		{
			$Speed = ($OrderRec['speedid'] != '') ? $OrderRec['speedid'] : 0;
			if(!isset($PackCounts[$ComplexID][$Speed]))
				$PackCounts[$ComplexID][$Speed] = 0;
			$PackCounts[$ComplexID][$Speed]++;
			if(!isset($SpeedTotals[$Speed]))
			{
				$SpeedTotals[$Speed] = array();
				$SpeedTotals[$Speed]['count'] = 0;
				$SpeedTotals[$Speed]['MRP'] = 0;
				$SpeedTotals[$Speed]['IRP'] = 0;
			}
			$SpeedTotals[$Speed]['count'] += 1;
			$BuildCount[$ComplexID]++;
			$ValueTotals[$ComplexID]['MRP'] += $SpeedCost[$Speed];
			$ValueTotals[$ComplexID]['IRP'] += $OrderRec['prevatsalesprice'];
			$SpeedTotals[$Speed]['MRP'] = $SpeedCost[$Speed];
			if($OrderRec['prevatsalesprice'] > $SpeedTotals[$Speed]['IRP'])
				$SpeedTotals[$Speed]['IRP'] = $OrderRec['prevatsalesprice'];
		}
	}
}
?>
<script>
$(document).ready(function()
{
	$("#invstart").datepicker({"dateFormat": "yy-mm-dd"});
	$("#proratadate").datepicker({"dateFormat": "yy-mm-dd"});
});

function getProRata()
{
	var TheDate = $('#proratadate').val();
	var TheSpeed = $('#prorataspeed').val();
	var adate = new Date().getTime();
	$.ajax({async: false, type: "POST", url: "ajaxGetCalcProrata.php", dataType: "html",
		data: "dc=" + adate + "&d=" + TheDate + "&s=" + TheSpeed,
		success: function (feedback)
		{
			$("#prorataresult").html("R " + feedback);
		},
		error: function(request, feedback, error)
		{
			alert("Request failed\n" + feedback + "\n" + error);
			return false;
		}
	});
}

function genInvoices()
{
	var checkedValues = Array();
	$('input:checkbox[name^=inv_id]:checked').map(function () 
	{
		checkedValues.push($(this).val());
    });
	console.log(checkedValues);
	$('#inv_ids').val(checkedValues.join(","));
	$('#firstinv-modal').modal("show");
}
</script>
<table class='table table-condensed'><tr><th>Pro Rata Calculator</th><td><input type='text' name='proratadate' id='proratadate' value='' class='form-control'></td>
	<td><select name='prorataspeed' id='prorataspeed' class='form-control'>
<?php
foreach($VendorSpeeds AS $SpeedID => $SpeedTxt)
{
	echo "<option value='" . $SpeedID . "'>" . $SpeedTxt . "</option>";
}
?>
</select></td><td><button type='button' class='btn btn-success' onclick='getProRata();'><i class='fa fa-calculator'></i></button></td><td id='prorataresult'></td></tr></table>
<form method='POST' action='genpurchaseorder.php'>
<input type='hidden' name='POTypeID' id='POTypeID' value='<?php echo $NewOrderPOType; ?>'>
<ul class="nav nav-tabs">
<?php
$ActClass = ' class="active"';
foreach($BuildComplexes AS $ComplexID => $ComplexRec)
{
	echo "<li" . $ActClass . "><a href='#" . $ComplexID . "Panel' data-toggle='tab'><strong>" . $Complexes[$ComplexID]['complexname'] . " <span class='badge'>" . $BuildCount[$ComplexID] . "</span></strong></a></li>";
	$ActClass = "";
}
if(count($LiveComplexes) > 0)
{
	echo "<li><a href='#LivePanel' data-toggle='tab'><strong>Live Complexes <span class='badge'>" . $LiveCount . "</span></strong></a></li>";
}
?>
<li class='bg-warning'><a href='#TotalPanel' data-toggle='tab'><strong>Totals</strong></a></li>
<li class='bg-warning pull-right'><button type='submit' class='btn btn-success'>Generate Purchase Order</button></li>
</ul>
<div class="tab-content">
<?php
$ActClass = ' active';
foreach($BuildComplexes AS $ComplexID)
{
	$NumUnits = getComplexUnitMapCount($ComplexID);
	$NumUnits = ($NumUnits == 0) ? -1 : $NumUnits;
	echo "<div class='tab-pane" . $ActClass . "' id='" . $ComplexID . "Panel'>\n";
	echo "<h4>" . $Complexes[$ComplexID]['complexname'] . "</h4>";
	echo "<div class='table'>\n";
	echo "<table class='table table-bordered table-condensed'>\n";
	echo "<tr><td colspan='12'>";
	echo "<table class='table table-bordered table-condensed'>\n";
	echo "<tr>";
	$CompTotals = array('cost' => 0, 'sell' => 0);
	$PackCount = 0;
	foreach($VendorSpeeds AS $SpeedID => $SpeedTxt)
	{
		$cnt = 0;
		if(isset($PackCounts[$ComplexID][$SpeedID]))
			$cnt = $PackCounts[$ComplexID][$SpeedID];
		if($SpeedID > 0)
			$PackCount += $cnt;
		echo "<td><strong>" . $SpeedTxt . "</strong> " . $cnt . "</td>";
	}
	$PackPerc = ($PackCount / $NumUnits) * 100;;
	$IntPerc = ($BuildCount[$ComplexID] / $NumUnits) * 100;
	$txtPerc = sprintf("%0.2f", $IntPerc);
	echo "<td><strong>" . $BuildCount[$ComplexID] .  " / " . $NumUnits . " (" . $txtPerc . "%)</strong> (Packages: " . sprintf("%0.2f", $PackPerc) . "%)";
	echo "<p>MRP: R " . $ValueTotals[$ComplexID]['MRP'] . " - IRP: R" . $ValueTotals[$ComplexID]['IRP'] . " (Diff: " . ($ValueTotals[$ComplexID]['IRP'] - $ValueTotals[$ComplexID]['MRP']) . ")</p></td>";
	echo "</tr>";
	echo "</table>";
	echo "</td></tr>";
	echo "<tr><th>PO</th><th>Customer</th><th>Unit</th><th>ID</th><th>Package</th><th>Term</th><th>Speed</th><th>ONT</th><th>Order Date</th><th>Age</th><th>Status</th><th></th></tr>\n";
	$PrevNum = 0;
	foreach($BuildOrders[$ComplexID] AS $UnitID => $OrderRec)
	{
		$BGClass = '';
		$ShowCB = '';
		$OrderID = $OrderRec['orderid'];
		if(isset($PO_Orders[$OrderID]))
			$ShowCB = $PO_Orders[$OrderID]['po_id'];
		elseif($OrderRec['speedid'] != 0)
			$ShowCB = "<input type='checkbox' name='po_order[]' id='po_order_" . $OrderID . "' value='" . $OrderID . "' class='form-control'>";
		if($PrevNum == $Units[$UnitID]['unitnumber'])
			$BGClass = ' class="bg-danger"';
		$PrevNum = $Units[$UnitID]['unitnumber'];
		echo "<tr" . $BGClass . "><td>" . $ShowCB . "</td><td><a href='customer.php?cid=" . $OrderRec['customerid'] . "&uid=" . $UnitID . "'>";
		echo $Customers[$OrderRec['customerid']]['customername'] . " " . $Customers[$OrderRec['customerid']]['customersurname'] . "</a> " . $OrderRec['prevatsalesprice'] . "</td>";
		echo "<td>" . $Units[$UnitID]['unitnumber'] . " " . $Complexes[$Units[$UnitID]['complexid']]['complexname'] . "</td>"; //</tr>";
		if($OrderRec['speedid'] == 0)
			$LineSpeed = "-";
		else
			$LineSpeed = $VendorSpeeds[$OrderRec['speedid']];
		echo "<td>" . $OrderID . "</td>";
		echo "<td>" . $PackageArr[$OrderRec['packageid']]['packagename'] . "</td>";
		echo "<td>" . $OrderRec['termnum'] . "</td>";
		echo "<td>" . $LineSpeed . "</td>";
		echo "<td>" . $ONTs[$OrderRec['ontid']]['ontname'] . "</td>";
		echo "<td>" . $OrderRec['orderdate'] . "</td>";
		echo "<td>" . $OrderRec['datediff'] . "</td>";
		if($OrderRec['speedid'] == 0)
			echo "<td>-</td>";
		else
			echo "<td>" . $OrderStatusses[$OrderRec['orderstatus']] . "</td>";
		echo "<td><a href='removeOrder.php?cid=" . $OrderRec['customerid'] . "&uid=" . $UnitID . "' class='bg-danger text-danger' title='Delete Order'><i class='fa fa-times'></i></a></td>";
		echo "</tr>";
	}
	echo "</table>";
	echo "</div>";
	echo "</div>";
	$ActClass = '';
}
if(count($LiveComplexes) > 0)
{
	echo "<div class='tab-pane" . $ActClass . "' id='LivePanel'>\n";
	foreach($LiveComplexes AS $ComplexID)
	{
		echo "<h4>" . $Complexes[$ComplexID]['complexname'] . "</h4>";
		echo "<div class='table'>\n";
		echo "<table class='table table-bordered table-condensed'>\n";
		echo "<tr><th>PO</th><th>Customer</th><th>Unit</th><th>ID</th><th>Package</th><th>Term</th><th>Speed</th><th>ONT</th><th>Order Date</th><th>Age</th><th>Status</th><th></th></tr>\n";
		$PrevNum = 0;
		foreach($LiveOrders[$ComplexID] AS $UnitID => $OrderRec)
		{
			if(($OrderRec['speedid'] != 0) && (in_array($OrderRec['orderstatus'], $ShowStatusses)))
			{
				$BGClass = '';
				$ShowCB = '';
				$OrderID = $OrderRec['orderid'];
				if(isset($PO_Orders[$OrderID]))
					$ShowCB = $PO_Orders[$OrderID]['po_id'];
				else
					$ShowCB = "<input type='checkbox' name='po_order[]' id='po_order_" . $OrderID . "' value='" . $OrderID . "' class='form-control'>";
				if($PrevNum == $Units[$UnitID]['unitnumber'])
					$BGClass = ' class="bg-danger"';
				$PrevNum = $Units[$UnitID]['unitnumber'];
				echo "<tr" . $BGClass . "><td>" . $ShowCB . "</td><td><a href='customer.php?cid=" . $OrderRec['customerid'] . "&uid=" . $UnitID . "'>" . $Customers[$OrderRec['customerid']]['customername'] . " " . $Customers[$OrderRec['customerid']]['customersurname'] . "</a></td>";
				echo "<td>" . $Units[$UnitID]['unitnumber'] . " " . $Complexes[$Units[$UnitID]['complexid']]['complexname'] . "</td>"; //</tr>";
				if($OrderRec['speedid'] == 0)
					$LineSpeed = "-";
				else
					$LineSpeed = $VendorSpeeds[$OrderRec['speedid']];
				echo "<td>" . $OrderRec['customerid'] . "</td>";
				echo "<td>" . $PackageArr[$OrderRec['packageid']]['packagename'] . "</td>";
				echo "<td>" . $OrderRec['termnum'] . "</td>";
				echo "<td>" . $LineSpeed . "</td>";
				echo "<td>" . $ONTs[$OrderRec['ontid']]['ontname'] . "</td>";
				echo "<td>" . $OrderRec['orderdate'] . "</td>";
				echo "<td>" . $OrderRec['datediff'] . "</td>";
				if($OrderRec['orderstatus'] != 2)
					echo "<td>" . $OrderStatusses[$OrderRec['orderstatus']] . "</td>";
				else
					echo "<td><input type='checkbox' name='inv_id[]' value='" . $OrderID . "' id='inv_id_" . $OrderID . "'></td>";
				echo "<td><a href='removeOrder.php?cid=" . $OrderRec['customerid'] . "&uid=" . $UnitID . "' class='bg-danger text-danger' title='Delete Order'><i class='fa fa-times'></i></a></td>";
				echo "</tr>";
			}
		}
		echo "<tr><td colspan='11'><button type='button' class='btn btn-success pull-right' onclick='genInvoices();'>Generate First Invoices</button></td></tr>\n";
		echo "</table>";
		echo "</div>";
	}
	echo "</div>";
	$ActClass = '';
}
echo "<div class='tab-pane' id='TotalPanel'>\n";
echo "<table class='table table-bordered table-condensed'>\n";
echo "<tr><th>Speed</th><th>Count</th><th>MRP</th><th>IRP</th><th>Diff</th></tr>";
ksort($SpeedTotals);
print_r($SpeedTotals);
$Totals = array('Count' => 0, 'MRP' => 0, 'IRP' => 0, 'Diff' => 0);
foreach($SpeedTotals AS $Speed => $SpRec)
{
	$Count = $SpRec['count'];
	$MRP = $SpRec['MRP'] * $Count;
	$IRP = $SpRec['IRP'] * $Count;
	$Diff = $IRP - $MRP;
	$Totals['Count'] += $Count;
	$Totals['MRP'] += $MRP;
	$Totals['IRP'] += $IRP;
	$Totals['Diff'] += $Diff;
	echo "<tr><th>" . $VendorSpeeds[$Speed] . "</th><td>" . $Count . "</td><td>R " . sprintf("%0.2f", $MRP) . "</td><td>R " . sprintf("%0.2f", $IRP) . "</td><td>R " . sprintf("%0.2f", $Diff) . "</td></tr>";
}
echo "<tr><th>Totals</th><td>" . $Totals['Count'] . "</td><td>R " . sprintf("%0.2f", $Totals['MRP']) . "</td><td>R " . sprintf("%0.2f", $Totals['IRP']) . "</td><td>R " . sprintf("%0.2f", $Totals['Diff']) . "</td></tr>";
echo "</table>";
echo "</div>";
$MidMonth = date("Y-m-15");
$NextMonth = date('M Y', strtotime('+1 month', strtotime($MidMonth)));
?>
</div>
</form>
<div class='modal fade' id='firstinv-modal' tabindex='-1' role='dialog' aria-hidden='true'>
	<form method='POST' action='firstInvoice.php' id='firstInvForm'>
	<input type='hidden' name='inv_ids' id='inv_ids' value=''>
	<div class='modal-dialog modal-lg'>
		<div class='modal-content'>
			<div class='modal-header'><strong>First Invoice<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
			<div class='modal-body'>
				<p>Invoice Start Date : <input type='text' name='invstart' id='invstart' maxlength='10' class='validate[custom[date]] form-control' value=''></p>
				<p>Include <?php echo $NextMonth; ?> : <input type='checkbox' name='incNextMnt' id='incNextMnt' value='1'></p>
			</div>	
			<div class='modal-footer'>
				<button type='submit' class='btn btn-default btn-xs'><span class='fa fa-times'></span> Submit</button>
			</div>
		</div>
	</div>
	</form>
</div>
<?php
include("footer.inc.php");
?>