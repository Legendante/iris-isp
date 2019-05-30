<?php
session_start();
include("db.inc.php");

$ComplexID = pebkac($_POST['cid']);

$MapArr = getComplexUnitMap($ComplexID);
echo "<tr><th>&nbsp;</th><th>Description</th><th>&nbsp;</th><th><small>Bodycorp<br>Unit</small></th>";
echo "<th>&nbsp;</th><th>Description</th><th>&nbsp;</th><th><small>Bodycorp<br>Unit</small></th>";
echo "<th>&nbsp;</th><th>Description</th><th>&nbsp;</th><th><small>Bodycorp<br>Unit</small></th></tr>";
$cnt = 0;
foreach($MapArr AS $MapID => $MapRec)
{
	if($cnt == 0)
		echo "<tr>";
	if($MapRec['unitid'] != '')
		echo "<td class='text-success'><i class='fa fa-check'></i></td>";
	else
		echo "<td class='text-warning'><i class='fa fa-minus'></i></td>";
	echo "<td><input type='text' name='map_" . $MapRec['mapid'] . "' id='map_" . $MapRec['mapid'] . "' class='validate[required] form-control' value='" . $MapRec['unitdesc'] . "'></td>";
	if($MapRec['unitid'] == '')
		echo "<td class='text-success'><button type='button' class='btn btn-danger btn-xs' onclick='deleteMapUnit(" . $MapRec['mapid'] . ");'><i class='fa fa-times'></i></td>";
	else
		echo "<td class='text-success'>&nbsp;</td>";
	if($MapRec['hoaunit'] == 1)
		echo "<td align='center'><input type='checkbox' name='hoa_" . $MapRec['mapid'] . "' id='hoa_" . $MapRec['mapid'] . "' value='1' checked='checked' disabled='disabled'></td>";
	else
		echo "<td align='center'><input type='checkbox' name='hoa_" . $MapRec['mapid'] . "' id='hoa_" . $MapRec['mapid'] . "' value='1'></td>";
	
	if($cnt == 2)
	{
		echo "</tr>";
		$cnt = 0;
	}
	else
		$cnt++;
}

?>