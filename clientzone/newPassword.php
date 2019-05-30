<?php
session_start();
include("../db.inc.php");
$ResetID = pebkac($_GET['r']);
$CustHash = pebkac($_GET['s'], 64, 'STRING');
$KeyHash = pebkac($_GET['b'], 32, 'STRING');
$ResRec = getPasswordReset($ResetID);
if(!isset($ResRec['resetid']))
{
	$Output = "<p class='textbox bg-warning'><strong>I could not find a matching reset record</strong></p>";
}
else
{
	$Customer = getCustomerByID($ResRec['customerid']);
	$CustKey = $ResetID . "|" . $ResRec['customerid'] . "|" . $Customer['email1'];
	// echo $CustKey . "<Br>";
	$Key2 = $ResRec['randkey'] . $ResRec['timekey'];
	$CheckCustHash = hash("sha256", $CustKey);
	$CheckKeyHash = md5($Key2);
	if(($CustHash == $CheckCustHash) && ($KeyHash == $CheckKeyHash))
	{
		$_SESSION['resetcustomerid'] = $ResRec['customerid'];
		$_SESSION['resetid'] = $ResetID;
		$Output = "<p class='textbox'><strong>Reset Password</strong><br>Please enter a new password</p>";
		$Output .= '<div class="login">';
		$Output .= '<form method="post" action="setNewPassword.php">';
		$Output .= '	<input type="text" readonly="readonly" value="' . $Customer['email1'] . '">';
		$Output .= '	<input type="password" name="p" placeholder="New password" required="required" />';
		$Output .= '	<button type="submit" class="btn btn-primary btn-block btn-large">Update password</button>';
		$Output .= '</form>';
		$Output .= '</div>';
	}
	else
		$Output = "<p class='textbox bg-warning'><strong>Validity failure</strong><br>Invalid request made</p>";
}
include_once("header.inc.php");
?>
<style>
/* NOTE: The styles were added inline because Prefixfree needs access to your styles and they must be inlined if they are on local disk! */
@import url(http://fonts.googleapis.com/css?family=Open+Sans);
.btn 
{ 
	display: inline-block; 
	*display: inline; 
	*zoom: 1; 
	padding: 4px 10px 4px; 
	margin-bottom: 0; 
	font-size: 13px; 
	line-height: 18px; 
	color: #4D048C; 
	text-align: center;
	text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75); 
	vertical-align: middle; 
	background-color: #f5f5f5; 
	background-image: -moz-linear-gradient(top, #ffffff, #4D048C); 
	background-image: -ms-linear-gradient(top, #ffffff, #4D048C); 
	background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#4D048C)); 
	background-image: -webkit-linear-gradient(top, #ffffff, #4D048C); 
	background-image: -o-linear-gradient(top, #ffffff, #4D048C); 
	background-image: linear-gradient(top, #ffffff, #4D048C); 
	background-repeat: repeat-x; 
	filter: progid:dximagetransform.microsoft.gradient(startColorstr=#ffffff, endColorstr=#4D048C, GradientType=0); 
	border-color: #4D048C #4D048C #4D048C; 
	border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25); 
	border: 1px solid #4D048C; 
	-webkit-border-radius: 4px; 
	-moz-border-radius: 4px; 
	border-radius: 4px; 
	-webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05); 
	-moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05); 
	box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05); 
	cursor: pointer; 
	*margin-left: .3em; 
}
.btn:hover, .btn:active, .btn.active, .btn.disabled, .btn[disabled] { background-color: #e6e6e6; }
.btn-large { padding: 9px 14px; font-size: 15px; line-height: normal; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; }
.btn:hover { color: #333333; text-decoration: none; background-color: #e6e6e6; background-position: 0 -15px; -webkit-transition: background-position 0.1s linear; -moz-transition: background-position 0.1s linear; -ms-transition: background-position 0.1s linear; -o-transition: background-position 0.1s linear; transition: background-position 0.1s linear; }
.btn-primary, .btn-primary:hover { text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25); color: #ffffff; }
.btn-primary.active { color: rgba(255, 255, 255, 0.75); }
.btn-primary { background-color: #4D048C; background-image: -moz-linear-gradient(top, #6eb6de, #4D048C); background-image: -ms-linear-gradient(top, #6eb6de, #4D048C); background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#6eb6de), to(#4D048C)); background-image: -webkit-linear-gradient(top, #6eb6de, #4D048C); background-image: -o-linear-gradient(top, #6eb6de, #4D048C); background-image: linear-gradient(top, #6eb6de, #4D048C); background-repeat: repeat-x; filter: progid:dximagetransform.microsoft.gradient(startColorstr=#6eb6de, endColorstr=#4D048C, GradientType=0);  border: 1px solid #3762bc; text-shadow: 1px 1px 1px rgba(0,0,0,0.4); box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.5); }
.btn-primary:hover, .btn-primary:active, .btn-primary.active, .btn-primary.disabled, .btn-primary[disabled] { filter: none; background-color: #4D048C; }
.btn-block { width: 100%; display:block; }

* { -webkit-box-sizing:border-box; -moz-box-sizing:border-box; -ms-box-sizing:border-box; -o-box-sizing:border-box; box-sizing:border-box; }

html { width: 100%; height:100%; overflow:hidden; }
body 
{ 
width: 100%;
height:100%;
font-family: 'Open Sans', sans-serif;
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#3E1D6D', endColorstr='#092756',GradientType=1 );
}
.login { 
position: relative;
top: 50%;
left: 50%;
margin: 0px 0 0 -150px;
width:300px;
height:300px;
}
.login h1 { text-shadow: 0 0 10px rgba(0,0,0,0.3); letter-spacing:1px; text-align:center; }

input { 
width: 100%; 
margin-bottom: 10px; 
background: rgba(0,0,0,0.3);
border: none;
outline: none;
padding: 10px;
font-size: 13px;
color: #fff;
text-shadow: 1px 1px 1px rgba(0,0,0,0.3);
border: 1px solid rgba(0,0,0,0.3);
border-radius: 4px;
box-shadow: inset 0 -5px 45px rgba(100,100,100,0.2), 0 1px 1px rgba(255,255,255,0.2);
-webkit-transition: box-shadow .5s ease;
-moz-transition: box-shadow .5s ease;
-o-transition: box-shadow .5s ease;
-ms-transition: box-shadow .5s ease;
transition: box-shadow .5s ease;
}
input:focus { box-shadow: inset 0 -5px 45px rgba(100,100,100,0.4), 0 1px 1px rgba(255,255,255,0.2); }
.textbox
{
	position: relative;
	top: 50%;
	left: 50%;
	margin: 0px 0 0 -225px;
	width:450px;
	text-align: center;
}
.bg-warning 
{
	background-color: #fcf8e3;
	color: #8a6d3b;
}
</style>
<?php
echo $Output;
include_once("footer.inc.php");
?>