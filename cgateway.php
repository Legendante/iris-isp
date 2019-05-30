<?php
include("db.inc.php");
include_once("header.inc.php");
$ComplexID = pebkac($_GET['c'], 5);
$ComplexRec = getComplexByID($ComplexID);
$CompOrders = getComplexOrders($ComplexID);
$SiteStatusses = getSiteStatusses();
$CompStatus = ($SiteStatusses[$ComplexRec['statusid']]['parentid'] == 0) ? $ComplexRec['statusid'] : $SiteStatusses[$ComplexRec['statusid']]['parentid'];
$StatusName = $SiteStatusses[$CompStatus]['statusname'];
?>
<!-- home section -->
<div id="aboutfibre">
	<div class="container">
		<div class="row">
			<div class="col-md-12 col-sm-12">
				<h2><?php echo $ComplexRec['complexname']; ?></h2>
			</div>
			<div class="col-md-4 col-sm-4">
				<!-- <h4>CHECK COVERAGE IN YOUR AREA</h4> -->
				<h4><small>Status:</small> <?php echo $StatusName; ?></h4>
				<h4><small>Interest:</small> <?php echo $StatusName; ?></h4>
			</div> 
 			<div class="col-md-8 col-sm-8">
				<p>More content in here</p>
			</div>
		</div>
	</div>
</div>
<?php
include_once("footer.inc.php");
?>