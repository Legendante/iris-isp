<?php
include("db.inc.php");

$FileID = pebkac($_GET['fid'], 5);
$FileRec = getComplexFileByID($FileID);
$TheFile = $FileRec['filepath'];

if (file_exists($TheFile)) 
{
	$UserFileName = cleanFileName($FileRec['filename']);

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $UserFileName . '"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($TheFile));
    // ob_clean();
    flush();
    readfile($TheFile);
    exit;
}
else
	exit("The requested file was not found<br>" . $TheFile);
?>