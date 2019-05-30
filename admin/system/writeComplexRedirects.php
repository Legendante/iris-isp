<?php
include_once("../db.inc.php");

$FileContent = '<IfModule mod_expires.c>' . "\n";
$FileContent .= 'RewriteEngine on' . "\n";
$FileContent .= 'RewriteBase /' . "\n";

// $FileContent .= '# Enable expirations' . "\n";
// $FileContent .= 'ExpiresActive On' . "\n";
// $FileContent .= '# Default directive' . "\n";
// $FileContent .= 'ExpiresDefault "access plus 1 month"' . "\n";
// $FileContent .= '# My favicon' . "\n";
// $FileContent .= 'ExpiresByType image/x-icon "access plus 1 year"' . "\n";
// $FileContent .= '# Images' . "\n";
// $FileContent .= 'ExpiresByType image/gif "access plus 1 month"' . "\n";
// $FileContent .= 'ExpiresByType image/png "access plus 1 month"' . "\n";
// $FileContent .= 'ExpiresByType image/jpg "access plus 1 month"' . "\n";
// $FileContent .= 'ExpiresByType image/jpeg "access plus 1 month"' . "\n";
// $FileContent .= '# CSS' . "\n";
// $FileContent .= 'ExpiresByType text/css "access 1 month"' . "\n";
// $FileContent .= '# Javascript' . "\n";
// $FileContent .= 'ExpiresByType application/javascript "access plus 1 year"' . "\n";
// $FileContent .= '</IfModule>' . "\n\n";
// $FileContent .= 'Options +FollowSymlinks' . "\n";
// 
// $FileContent .= 'RewriteRule ^j/([0-9]+) jobdetails.php?gc=1&j=$1 [NC]' . "\n";
// $FileContent .= 'RewriteRule ^r/([0-9]+) recruiterdetails.php?cl=$1 [NC]' . "\n\n";

$ShortUrls = array();
// $ShortUrls = getClientShortURLList();

$selQry = 'SELECT complexdetails.complexid, subdomain FROM complexdetails WHERE subdomain != "" AND subdomain IS NOT NULL ORDER BY complexname';
// echo $selQry . "<Br>";
$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
$retArr = array();
while($selData = mysqli_fetch_array($selRes))
{
	$ShortUrls[$selData['complexid']] = $selData['subdomain'];
}

if(sizeof($ShortUrls) > 0)
{
	$FileContent .= '# System generated redirects -- START (' . date("D, d M Y H:i:s") . ")\n";
	foreach($ShortUrls AS $ClientID => $URL)
	{
		$URL = preg_replace("/[^a-zA-Z0-9]+/", "", $URL) . ".domain.com";
		$FileContent .= 'RewriteCond %{HTTP_HOST} ^' . $URL . '$ [NC]' . "\n";
		$FileContent .= 'RewriteRule ^(.*)$ http://www.domain.com/subhandler.php?ci=' . $ClientID . " [R=301,NC,L]\n";
	}
	$FileContent .= '# System generated redirects -- END' . "\n";
}

$FileContent .= 'RewriteRule ^index\.php$ - [L]' . "\n\n";
$FileContent .= 'RewriteCond %{REQUEST_FILENAME} !-f' . "\n\n";
$FileContent .= 'RewriteCond %{REQUEST_FILENAME} !-d' . "\n\n";
$FileContent .= 'RewriteRule . /index.php [L]' . "\n\n";
$FileContent .= '</IfModule>';

$HTFile = '../../.htaccess';
file_put_contents($HTFile, $FileContent);

$NewContent = file_get_contents($HTFile);
// include("../header.inc.php");
echo "<h3>Done</h3>";
echo "<textarea cols='100' rows='50'>" . $NewContent . "</textarea>";
// include_once("../footer.inc.php");
?>