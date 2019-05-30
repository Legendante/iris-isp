<?php
include("header.inc.php");
$Speeds = getPackageSpeeds();
$ONTs = getONTTypes();
$ComplexTypes = getComplexTypes();
$Vendors = getVendors();
$Precincts = getPrecincts();
$Suburbs = getSuburbs();
$Areas = getAreas();
$Cities = getCities();
$Provinces = getProvinces();
$Countries = getCountries();
$ManagingAgents = getManagingAgents();
?>
<div class='row'>
	<div class='col-md-6'>
<div class="panel panel-default">
	<div class="panel-heading"><h4>Complex Types
		<button type='button' class='btn btn-xs pull-right' onclick='$("#ComplexTypesPanel").toggleClass("hidden"); $("#ComplexTypesPanelCHV").toggleClass("fa-chevron-down");'>
		<span class='fa fa-chevron-up' id='ComplexTypesPanelCHV'></span></button>
		</h4>
	</div>
	<div class="panel-body" id="ComplexTypesPanel">
		<div class='table'>
			<table class='table table-bordered'>
			<tr><th>ID</th><th>Name</th></tr>
<?php
	foreach($ComplexTypes AS $ID => $Rec)
	{
		echo "<tr>";
		echo "<td>" . $ID . "</td>";
		echo "<td>" . $Rec . "</td>";
		echo "</tr>";
	}
?>		
			</table>
		</div>
	</div>
</div>
	</div>
	<div class='col-md-6'>
<div class="panel panel-default">
	<div class="panel-heading"><h4>Managing Agents
		<button type='button' class='btn btn-xs pull-right' onclick='$("#ManagingAgentsPanel").toggleClass("hidden"); $("#ManagingAgentsPanelCHV").toggleClass("fa-chevron-down");'>
		<span class='fa fa-chevron-up' id='ManagingAgentsPanelCHV'></span></button>
		</h4>
	</div>
	<div class="panel-body" id="ManagingAgentsPanel">
		<div class='table'>
			<table class='table table-bordered'>
			<tr><th>ID</th><th>Name</th></tr>
<?php
	foreach($ManagingAgents AS $ID => $Rec)
	{
		echo "<tr>";
		echo "<td>" . $ID . "</td>";
		echo "<td>" . $Rec . "</td>";
		echo "</tr>";
	}
?>				
			</table>
		</div>
	</div>
</div>
	</div>
</div>
<div class='row'>
	<div class='col-md-6'>
<div class="panel panel-default">
	<div class="panel-heading"><h4>Countries
		<button type='button' class='btn btn-xs pull-right' onclick='$("#CountriesPanel").toggleClass("hidden"); $("#CountriesPanelCHV").toggleClass("fa-chevron-down");'>
		<span class='fa fa-chevron-up' id='CountriesPanelCHV'></span></button>
		</h4>
	</div>
	<div class="panel-body" id="CountriesPanel">
		<div class='table'>
			<table class='table table-bordered'>
			<tr><th>ID</th><th>Name</th><th>Code</th></tr>
<?php
	foreach($Countries AS $ID => $Rec)
	{
		echo "<tr>";
		echo "<td>" . $ID . "</td>";
		echo "<td>" . $Rec['countryname'] . "</td>";
		echo "<td>" . $Rec['countrycode'] . "</td>";
		echo "</tr>";
	}
?>				
			</table>
		</div>
	</div>
</div>
	</div>
	<div class='col-md-6'>
<div class="panel panel-default">
	<div class="panel-heading"><h4>Provinces
		<button type='button' class='btn btn-xs pull-right' onclick='$("#ProvincesPanel").toggleClass("hidden"); $("#ProvincesPanelCHV").toggleClass("fa-chevron-down");'>
		<span class='fa fa-chevron-up' id='ProvincesPanelCHV'></span></button>
		</h4>
	</div>
	<div class="panel-body" id="ProvincesPanel">
		<div class='table'>
			<table class='table table-bordered'>
			<tr><th>ID</th><th>Name</th><th>Code</th><th>Country ID</th></tr>
	<?php
	foreach($Provinces AS $ID => $Rec)
	{
		echo "<tr>";
		echo "<td>" . $ID . "</td>";
		echo "<td>" . $Rec['provincename'] . "</td>";
		echo "<td>" . $Rec['provincecode'] . "</td>";
		echo "<td>" . $Rec['countryid'] . "</td>";
		echo "</tr>";
	}
?>			
			</table>
		</div>
	</div>
</div>
	</div>
</div>
<div class='row'>
	<div class='col-md-6'>
<div class="panel panel-default">
	<div class="panel-heading"><h4>Cities
		<button type='button' class='btn btn-xs pull-right' onclick='$("#CitiesPanel").toggleClass("hidden"); $("#CitiesPanelCHV").toggleClass("fa-chevron-down");'>
		<span class='fa fa-chevron-up' id='CitiesPanelCHV'></span></button>
		</h4>
	</div>
	<div class="panel-body" id="CitiesPanel">
		<div class='table'>
			<table class='table table-bordered'>
			<tr><th>ID</th><th>Name</th><th>Code</th><th>Province ID</th><th>Country ID</th></tr>
<?php
	foreach($Cities AS $ID => $Rec)
	{
		echo "<tr>";
		echo "<td>" . $ID . "</td>";
		echo "<td>" . $Rec['cityname'] . "</td>";
		echo "<td>" . $Rec['citycode'] . "</td>";
		echo "<td>" . $Rec['provinceid'] . "</td>";
		echo "<td>" . $Rec['countryid'] . "</td>";
		echo "</tr>";
	}
?>				
			</table>
		</div>
	</div>
</div>
	</div>
	<div class='col-md-6'>
<div class="panel panel-default">
	<div class="panel-heading"><h4>Areas
		<button type='button' class='btn btn-xs pull-right' onclick='$("#AreasPanel").toggleClass("hidden"); $("#AreasPanelCHV").toggleClass("fa-chevron-down");'>
		<span class='fa fa-chevron-up' id='AreasPanelCHV'></span></button>
		</h4>
	</div>
	<div class="panel-body" id="AreasPanel">
		<div class='table'>
			<table class='table table-bordered'>
			<tr><th>ID</th><th>Name</th><th>Code</th><th>City ID</th><th>Province ID</th><th>Country ID</th></tr>
<?php
	foreach($Areas AS $ID => $Rec)
	{
		echo "<tr>";
		echo "<td>" . $ID . "</td>";
		echo "<td>" . $Rec['areaname'] . "</td>";
		echo "<td>" . $Rec['areacode'] . "</td>";
		echo "<td>" . $Rec['cityid'] . "</td>";
		echo "<td>" . $Rec['provinceid'] . "</td>";
		echo "<td>" . $Rec['countryid'] . "</td>";
		echo "</tr>";
	}
?>				
			</table>
		</div>
	</div>
</div>
	</div>
</div>
<div class='row'>
	<div class='col-md-6'>
<div class="panel panel-default">
	<div class="panel-heading"><h4>Suburbs
		<button type='button' class='btn btn-xs pull-right' onclick='$("#SuburbsPanel").toggleClass("hidden"); $("#SuburbsPanelCHV").toggleClass("fa-chevron-down");'>
		<span class='fa fa-chevron-up' id='SuburbsPanelCHV'></span></button>
		</h4>
	</div>
	<div class="panel-body" id="SuburbsPanel">
		<div class='table'>
			<table class='table table-bordered'>
			<tr><th>ID</th><th>Name</th><th>Code</th><th>Area ID</th><th>City ID</th><th>Province ID</th><th>Country ID</th></tr>
<?php
	foreach($Suburbs AS $ID => $Rec)
	{
		echo "<tr>";
		echo "<td>" . $ID . "</td>";
		echo "<td>" . $Rec['suburbname'] . "</td>";
		echo "<td>" . $Rec['suburbcode'] . "</td>";
		echo "<td>" . $Rec['areaid'] . "</td>";
		echo "<td>" . $Rec['cityid'] . "</td>";
		echo "<td>" . $Rec['provinceid'] . "</td>";
		echo "<td>" . $Rec['countryid'] . "</td>";
		
		echo "</tr>";
	}
?>				
			</table>
		</div>
	</div>
</div>
	</div>
	<div class='col-md-6'>
<div class="panel panel-default">
	<div class="panel-heading"><h4>Precincts
		<button type='button' class='btn btn-xs pull-right' onclick='$("#PrecinctsPanel").toggleClass("hidden"); $("#PrecinctsPanelCHV").toggleClass("fa-chevron-down");'>
		<span class='fa fa-chevron-up' id='PrecinctsPanelCHV'></span></button>
		</h4>
	</div>
	<div class="panel-body" id="PrecinctsPanel">
		<div class='table'>
			<table class='table table-bordered'>
			<tr><th>ID</th><th>Name</th><th>Code</th><th>Suburb ID</th><th>Area ID</th><th>City ID</th><th>Province ID</th><th>Country ID</th></tr>
<?php
	foreach($Precincts AS $ID => $Rec)
	{
		echo "<tr>";
		echo "<td>" . $ID . "</td>";
		echo "<td>" . $Rec['precinctname'] . "</td>";
		echo "<td>" . $Rec['precinctcode'] . "</td>";
		echo "<td>" . $Rec['suburbid'] . "</td>";
		echo "<td>" . $Rec['areaid'] . "</td>";
		echo "<td>" . $Rec['cityid'] . "</td>";
		echo "<td>" . $Rec['provinceid'] . "</td>";
		echo "<td>" . $Rec['countryid'] . "</td>";
		echo "</tr>";
	}
?>				
			</table>
		</div>
	</div>
</div>
</div>
</div>
<?php
include("footer.inc.php");
?>