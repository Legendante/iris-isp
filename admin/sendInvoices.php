<?php
session_start();
include("db.inc.php");
include_once("../class.phpmailer.php");
include_once("../class.pop3.php");
include_once("../class.smtp.php");
$SendInvs = $_POST['send'];
$Invoices = getInvoices(implode(",", $SendInvs));
// print_r($Invoices);
foreach($Invoices AS $InvID => $InvRec)
{
	$Customer = getCustomerByID($InvRec['customerid']);
	$FirstName = $Customer['customername'];
	$Email = $Customer['email1'];
	// $Email = 'jacques@legendante.com'; // Remember to remove this before going live
	$Attachment = $InvRec['filepath'];
	// $HTMLTemplate = file_get_contents("invoiceing/templates/firstinvoice.html");
	// $TextTemplate = file_get_contents("invoiceing/templates/firstinvoice.txt");
	$HTMLTemplate = file_get_contents("invoiceing/templates/monthlyinvoice.html");
	$TextTemplate = file_get_contents("invoiceing/templates/monthlyinvoice.txt");
	$HTMLTemplate = str_replace("[[#[FIRSTNAME]#]]", $FirstName, $HTMLTemplate);
	$TextTemplate = str_replace("[[#[FIRSTNAME]#]]", $FirstName, $TextTemplate);
	$mail = new PHPMailer;
	// $mail->SMTPDebug = 3;                               											// Enable verbose debug output
	$mail->isSMTP();                                      											// Set mailer to use SMTP
	$mail->Host = $mailhost;  																		// Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               											// Enable SMTP authentication
	$mail->Username = $mailusername;                							 					// SMTP username
	$mail->Password = $mailpassword;                           										// SMTP password
	$mail->SMTPSecure = 'tls';                            											// Enable TLS encryption, `ssl` also accepted
	$mail->Port = $mailport;                                    									// TCP port to connect to
	$mail->setFrom('accounts@domain.com', 'Accounts');
	//$mail->addBCC($EmArr['email'], $EmArr['firstname'] . ' ' . $EmArr['lastname']);     		// Add a recipient
	$mail->addReplyTo('accounts@domain.com', 'Accounts');
	$mail->isHTML(true);
	$mail->addAddress(trim($Email), $FirstName . ' ' . $Customer['customersurname']);
	$mail->AddCC('accounts@domain.com', 'Accounts');
	$mail->Subject = 'Invoice';
	$mail->Body = $HTMLTemplate;
	$mail->AltBody = $TextTemplate;
	$mail->AddAttachment($Attachment);
	if(!$mail->send()) 
		logDBError("Failed to send invoice email to '" . $Email . "'", "Fault mail send error", __FILE__, __FUNCTION__, __LINE__);
	$InvSaveArr = array();
	$InvSaveArr['invstatus'] = 3;
	updateInvoice($InvID, $InvSaveArr);
}
header("Location: unsentinvs.php");
?>