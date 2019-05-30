<?php
include("db.inc.php");

$PackageID = pebkac($_POST['pid']);
$PackageRec = getPackageDetails($PackageID);
$Pieces = getPackagePieces($PackageID);
$PackageRec['pieces'] = $Pieces;
echo json_encode($PackageRec);
?>