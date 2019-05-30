<?php

$PlaceHolderArr = array();
$PlaceHolderArr['[[Firstname]]'] = 'Jacques';
$PlaceHolderArr['[[username]]'] = 'Usernamehere';
$PlaceHolderArr['[[password]]'] = 'Passwordhere';
$PlaceHolderArr['[[packageorder]]'] = 'PackageOrder';
$HTMLContents = file_get_contents("templates/welcome.html");
foreach($PlaceHolderArr AS $Needle => $Magnet)
{
	$HTMLContents = str_replace($Needle, $Magnet, $HTMLContents);
}


echo $HTMLContents;
?>