<?php
include_once("../db.inc.php");
?>
<!DOCTYPE>
<html>
    <head>
    <title>Greencom precincts</title>
	<style>
	.customBox {
      background: yellow;
      border: 1px solid black;
      position: absolute;
    }
	</style>
<?php
$selQry = 'SELECT vendorid, vendorname FROM vendordetails ORDER BY vendorname';
$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
$VendorArr = array();
while($selData = mysqli_fetch_array($selRes))
{
	$VendorArr[$selData['vendorid']] = $selData['vendorname'];
}

$selQry = 'SELECT vendorid, precinctid FROM precinctpointvendors';
$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
$VendorPrecs = array();
while($selData = mysqli_fetch_array($selRes))
{
	$VendorPrecs[$selData['precinctid']][$selData['vendorid']] = 1;
}

$selQry = 'SELECT precinctid, pointstatus, isprecinct FROM precinctpointdetails WHERE precinctid > 0';
$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
$PrecinctRec = array();
while($selData = mysqli_fetch_array($selRes))
{
	$PrecinctRec[$selData['precinctid']]['precinctid'] = $selData['precinctid'];
	$PrecinctRec[$selData['precinctid']]['pointstatus'] = $selData['pointstatus'];
	$PrecinctRec[$selData['precinctid']]['isprecinct'] = $selData['isprecinct'];
}

$selQry = 'SELECT precinctid, precintname, polygondata  FROM precinctpoints WHERE pointdata IS NULL OR pointdata = ""';
$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
?>
<script type="text/javascript" src="../js/jquery-3.1.0.min.js"></script>
<script type="text/javascript">
var geocoder;
var labels = [];
var map;

function initMap() 
{
		geocoder = new google.maps.Geocoder();
		var infowindow = new google.maps.InfoWindow();
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 12,
          center: {lat: -26.02070904586248, lng: 28.00852867256502},
          mapTypeId: 'terrain'
        });
<?php
$Cnt = 0;
$ColArr = array(0 => "FF0000", 1 => "00FF00", 2 => "0000FF", 3 => "FF8C00", 4 => "B22222", 5 => "228B22", 6 => "DAA520");
$PrecinctArr = array();
while($selData = mysqli_fetch_array($selRes))
{
	$PrecinctArr[$selData['precinctid']] = $selData['precintname'];
	$pArr = explode(" ", $selData['polygondata']);
	$cnnt = 0;
	$coords = array();
	foreach($pArr AS $cnt => $PolyPoint)
	{
		$tmp = explode(",", $PolyPoint);
		$coords[$cnnt]['lat'] = $tmp[0];
		$coords[$cnnt]['lon'] = $tmp[1];
		$cnnt++;
	}
?>	
	var polygonCoords = [
<?php
$c = 0;
foreach ( $coords AS $i => $coord )
{
	if($c == 1)
		echo ",\n";
	echo "new google.maps.LatLng(" . $coord['lon'] . ", " . $coord['lat'] . ")\n";
	$c = 1;
}
?>
];
	var bounds = new google.maps.LatLngBounds();
	for (i = 0; i < polygonCoords.length; i++) 
	{
		bounds.extend(polygonCoords[i]);
	}
	 // construct the polygon
	 polygon<?php echo $selData['precinctid']; ?> = new google.maps.Polygon({
						   paths: polygonCoords,
						   strokeColor: "#<?php echo $ColArr[$Cnt]; ?>",
						   strokeOpacity: 0.8,
						   strokeWeight: 1,
						   fillColor: "#<?php echo $ColArr[$Cnt]; ?>",
						   fillOpacity: 0.35
	 });
	
	// console.log(bounds.getCenter());
	var latlng = new google.maps.LatLng({lat: <?php echo $coords[0]['lon']; ?>, lng: <?php echo $coords[0]['lat']; ?>});
	marker<?php echo $selData['precinctid']; ?> = new google.maps.Marker({
        position: bounds.getCenter(),
        map: map,
        title: "<?php echo $selData['precinctid'] . ": " . $selData['precintname']; ?>"
    });

	 // show polygon on the map
	 polygon<?php echo $selData['precinctid']; ?>.setMap(map);
<?php
$Cnt++;
if($Cnt == 7)
	$Cnt = 0;
}
?>
}  

function codeAddress() {
    var address = document.getElementById('address').value;
    geocoder.geocode( { componentRestrictions: { country: 'ZA' }, 'address': address}, function(results, status) {
      if (status == 'OK') {
        map.setCenter(results[0].geometry.location);
        var marker = new google.maps.Marker({
            map: map,
            position: results[0].geometry.location
        });
      } else {
        alert('Geocode was not successful for the following reason: ' + status);
      }
    });
  }


function hideShowPoly(PolyObj, MarkerObj, PrecID)
{
	if ($('#p_' + PrecID).is(':checked')) 
	{
		PolyObj.setMap(map);
		MarkerObj.setMap(map);
	}
	else
	{
		PolyObj.setMap(null);
		MarkerObj.setMap(null);
	}
}

function savePoint(Point)
{
	var adate = new Date().getTime();
	var name = $('#pname_' + Point).val();
	var status = $('#pstatus_' + Point).val();
	var pyn = $("input:radio[name=pyn_" + Point + "]:checked").val();
	var v_ids = '';
	$("input:checkbox[name^='v_" + Point + "_']:checked").each(function() 
	{
		v_ids += $(this).val() + ",";
	});
	
	$.ajax({async: false, type: "POST", url: "ajaxSavePoint.php", dataType: "html",
		data: "dc=" + adate + "&pid=" + Point + "&name=" + name + "&pstat=" + status + "&pyn=" + pyn + "&vids=" + v_ids,
		success: function (feedback)
		{
			if(feedback != 'DONE')
				alert("Something broke: " + feedback);
		},
		error: function(request, feedback, error)
		{
			alert("Request failed\n" + feedback + "\n" + error);
			return false;
		}
	});
}
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC_XVJrxLArAn0vFXoBDsDQNzG44IJhLjs&callback=initMap"></script>
<table><tr><td width='510px' valign='top'>
<div style="margin:auto;  width: 500px; ">
    <div id="map" style="height: 500px;"></div>
</div>
</td><td valign='top'>
<input type='text' name='address' id='address' style='width: 300px;'><input type='button' onclick='codeAddress();' value='Check address'><br>
<div style='height: 600px; overflow-y: scroll'>
	<table border='1'>
	<th>&nbsp;</th><th>Name</th><th>Status</th><th>Is GC Precinct?</th><th>Vendors</th><th>&nbsp;</th></tr>
	<?php
	foreach($PrecinctArr AS $precinctID => $PrecArr)
	{
		$StatChecked = array(1 => "", 2 => "", 3 => "", 4 => "", 5 => "");
		$StatChecked[$PrecinctRec[$precinctID]['pointstatus']] = " selected='selected'";
		$YNChecked = array(0 => "", 1 => "");
		$YNChecked[$PrecinctRec[$precinctID]['isprecinct']] = " checked='checked'";
		echo "<tr id='row_" . $precinctID . "'>";
		echo "<td><input type='checkbox' name='p_" . $precinctID . "' id='p_" . $precinctID . "' onclick='hideShowPoly(polygon" . $precinctID . ", marker" . $precinctID . ", \"" . $precinctID . "\");' checked='checked'></td>";
		echo "<td>" . $precinctID . ": <input type='text' name='pname_" . $precinctID . "' id='pname_" . $precinctID . "' value='" . $PrecArr . "'></td>\n";
		
		echo "<td><select name='pstatus_" . $precinctID . "' id='pstatus_" . $precinctID . "'>";
		// -- 0 = unchecked, 1 = live, 2 = development, 3 = in planning, 4 = interest, 5 = future
		echo "<option value=''>- Select -</option>";
		echo "<option value='1'" . $StatChecked[1] . ">Live</option>";
		echo "<option value='2'" . $StatChecked[2] . ">In development</option>";
		echo "<option value='3'" . $StatChecked[3] . ">In planning</option>";
		echo "<option value='4'" . $StatChecked[4] . ">Gathering Interest</option>";
		echo "<option value='5'" . $StatChecked[5] . ">Future</option>";
		echo "</select></td>\n";
		echo "<td>";
		// <select name='pyn_" . $precinctID . "' id='pyn_" . $precinctID . "'>";
		// -- 0 = unchecked, 1 = live, 2 = development, 3 = in planning, 4 = interest, 5 = future
		// echo "<option value=''>- Select -</option>";
		echo "<input type='radio' name='pyn_" . $precinctID . "' id='pyn_" . $precinctID . "_1' value='1'" . $YNChecked[1] . "><label for='pyn_" . $precinctID . "_1'>Yes</label><br>";
		echo "<input type='radio' name='pyn_" . $precinctID . "' id='pyn_" . $precinctID . "_0' value='0'" . $YNChecked[0] . "><label for='pyn_" . $precinctID . "_0'>No</label><br>";
		// echo "<option value='0'" . $YNChecked[0] . ">No</option>";
		// echo "</select>"'
		echo "</td>\n";
		echo "<td>";
		foreach($VendorArr AS $VID => $VName)
		{
			echo "<input type='checkbox' name='v_" . $precinctID . "_" . $VID . "' id='v_" . $precinctID . "_" . $VID . "' value='" . $VID . "'";
			if(isset($VendorPrecs[$precinctID][$VID]))
				echo " checked='checked'";
			echo "><label for='v_" . $precinctID . "_" . $VID . "'>" . $VName . "</label><br>";
		}
		echo "</td>";
		echo "<td><button onclick='savePoint(" . $precinctID . ");'>Save</button></td>\n";
		echo "</tr>";
	}
	?>
	</table>
</div>
</td></tr>
</table>