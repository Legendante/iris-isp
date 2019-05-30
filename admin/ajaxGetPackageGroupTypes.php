<?php
include("db.inc.php");

$GroupID = pebkac($_POST['gid']);
$GroupTypes = getPackageGroupTypes($GroupID);
echo json_encode($GroupTypes);
?>