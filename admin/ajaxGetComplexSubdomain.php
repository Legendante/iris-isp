<?php
session_start();
include("db.inc.php");

$SubD = pebkac($_POST['subd'], 50, 'STRING');
$Complex = getComplexBySubdomain($SubD);
if(!isset($Complex['complexid']))
	$Complex['complexid'] = 0;
echo json_encode($Complex);
?>