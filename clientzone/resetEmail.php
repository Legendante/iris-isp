<?php
session_start();
include("../db.inc.php");

$Email = trim(pebkac($_POST['u'], 50, 'STRING'));

$selQry = 'SELECT customerid, customername, customersurname, email1 FROM customerdetails WHERE LOWER(email1) = "' . strtolower($Email) . '"';
$selRes = mysqli_query($dbCon, $selQry) or logDBError(mysqli_error($dbCon), $selQry, __FILE__, __FUNCTION__, __LINE__);
$selData = mysqli_fetch_array($selRes);
if($selData['customerid'] == '')
{
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

.bg-warning 
{
	position: relative;
	top: 50%;
	left: 50%;
	margin: 0px 0 0 -225px;
	width:450px;
	background-color: #fcf8e3;
	color: #8a6d3b;
	text-align: center;
}

</style>
	<p class='bg-warning'><strong>I could not find a record matching that email address.</strong></p>
	<div class="login">
	<h1>Reset Password</h1>
    <form method="post" action='resetEmail.php'>
    	<input type="text" name="u" placeholder="Email" required="required" />
		<button type="submit" class="btn btn-primary btn-block btn-large">Send reset request</button>
    </form>
</div>
<?php
	include_once("footer.inc.php");
	exit();
}
$RandKey = mt_rand(1000, 5000);
$TimeKey = time();
$SaveArr['customerid'] = $selData['customerid'];
$SaveArr['randkey'] = $RandKey;
$SaveArr['timekey'] = $TimeKey;
$ResetID = addPasswordReset($SaveArr);
$CustKey = $ResetID . "|" . $selData['customerid'] . "|" . $selData['email1'];
$SaveArr = array();
$Key2 = $RandKey . $TimeKey;
$CustHash = hash("sha256", $CustKey);
$KeyHash = md5($Key2);

$URL = 'http://clientzone.domain.com/newPassword.php?r=' . $ResetID . '&s=' . $CustHash . '&b=' . $KeyHash;
include_once("../class.phpmailer.php");
include_once("../class.pop3.php");
include_once("../class.smtp.php");

$mail = new PHPMailer;
//$mail->SMTPDebug = 3;                               											// Enable verbose debug output
$mail->isSMTP();                                      											// Set mailer to use SMTP
$mail->Host = $mailhost;  																		// Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               											// Enable SMTP authentication
$mail->Username = $mailusername;                							 					// SMTP username
$mail->Password = $mailpassword;                           										// SMTP password
$mail->SMTPSecure = 'tls';                            											// Enable TLS encryption, `ssl` also accepted
$mail->Port = $mailport;                                    									// TCP port to connect to
$mail->setFrom($mailusername, 'Online');
$mail->addAddress($selData['email1'], $selData['customername'] . ' ' . $selData['customersurname']);     								// Add a recipient
$mail->addReplyTo($mailusername, 'Online');
$mail->isHTML(true);                                  											// Set email format to HTML
$mail->Subject = 'Password reset request';
$PlaceHolderArr = array();
$PlaceHolderArr['[[Firstname]]'] = $selData['customername'];
$PlaceHolderArr['[[username]]'] = $selData['email1'];
$PlaceHolderArr['[[resetlink]]'] = $URL;
$HTMLContents = file_get_contents("../templates/passwordreset.html");
foreach($PlaceHolderArr AS $Needle => $Magnet)
{
	$HTMLContents = str_replace($Needle, $Magnet, $HTMLContents);
}
$mail->Body = $HTMLContents;
$TextContents = file_get_contents("../templates/passwordreset.txt");
foreach($PlaceHolderArr AS $Needle => $Magnet)
{
	$TextContents = str_replace($Needle, $Magnet, $TextContents);
}
$mail->AltBody = $TextContents;
if(!$mail->send()) 
	logDBError("Failed to send client email to '" . $Customer['email1'] . "' for order num " . $UnitPackageID, "Order mail send error", __FILE__, __FUNCTION__, __LINE__);
header("Location: resetSent.php");
?>