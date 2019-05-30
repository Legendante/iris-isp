<?php
session_start();
include("db.inc.php");

$SrcTerm = $_GET['term'];

// $SrcTerm = 'ham';
$retArr = array();
$searchQry = 'SELECT customerdetails.customerid, customername, customersurname, complexdetails.complexid, complexdetails.complexname ';
$searchQry .= 'FROM customerdetails ';
$searchQry .= 'LEFT JOIN customerunits ON customerunits.customerid = customerdetails.customerid ';
$searchQry .= 'LEFT JOIN complexdetails ON complexdetails.complexid = customerunits.complexid OR complexdetails.customerid = customerdetails.customerid ';
$searchQry .= 'WHERE LOWER(customername) LIKE "%' . $SrcTerm . '%" OR ';
$searchQry .= 'LOWER(customersurname) LIKE "%' . $SrcTerm . '%" ';
// $searchQry .= 'LOWER(idnumber) LIKE "%' . $SrcTerm . '%" OR ';
// $searchQry .= 'LOWER(email1) LIKE "%' . $SrcTerm . '%" OR ';
// $searchQry .= 'LOWER(cell1) LIKE "%' . $SrcTerm . '%" OR '; 
// $searchQry .= 'LOWER(complexname) LIKE "%' . $SrcTerm . '%"'; 
// $searchQry .= 'WHERE MATCH (complexname) ';
// $searchQry .= 'AGAINST ("Ham" IN NATURAL LANGUAGE MODE)';
// echo $searchQry . "<br>";

$selRes = mysqli_query($dbCon, $searchQry) or logDBError(mysqli_error($dbCon), $searchQry, __FILE__, __FUNCTION__, __LINE__);
$cnt = 0;
while($selData = mysqli_fetch_array($selRes))
{
	$ComplexName = ($selData['complexname'] != '') ? ' [' . $selData['complexname'] . ']' : '';
	$retArr[$cnt]['type'] = 'customer';
	$retArr[$cnt]['id'] = $selData['customerid'];
	$retArr[$cnt]['value'] = $selData['customername'] . ' ' . $selData['customersurname'] . $ComplexName;
	$cnt++;
}
$searchQry = 'SELECT complexid, complexname FROM complexdetails WHERE LOWER(complexname) LIKE "%' . $SrcTerm . '%"';
// echo $searchQry . "<br>";
$selRes = mysqli_query($dbCon, $searchQry) or logDBError(mysqli_error($dbCon), $searchQry, __FILE__, __FUNCTION__, __LINE__);
while($selData = mysqli_fetch_array($selRes))
{
	$retArr[$cnt]['type'] = 'complex';
	$retArr[$cnt]['id'] = $selData['complexid'];
	$retArr[$cnt]['value'] = $selData['complexname'] . " [Complex]";
	$cnt++;
}
echo json_encode($retArr);
?>