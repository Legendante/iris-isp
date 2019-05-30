<?php
function getUserOptionList($SelUserID = '')
{
	$Users = getUsers();
	$RetStr = '';
	foreach($Users AS $UserID => $UserRec)
	{
		$RetStr .= "<option value='" . $UserID. "'";
		if($UserID == $SelUserID)
			$RetStr .= " selected='selected'";
		$RetStr .= ">" . $UserRec['firstname'] . " " . $UserRec['surname'];
		if($UserRec['inactive'] == 2)
			$RetStr .= " - ** On leave **";
		elseif($UserRec['inactive'] == 1)
			$RetStr .= " - ** Deactivated **";
		$RetStr .= "</option>";
	}
	return $RetStr;
}
?>