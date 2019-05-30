<?php
session_start();
$ErrMsg = (!isset($_SESSION['errmsg'])) ? '' : $ErrMsg;
// echo "<div class='alert alert-danger alert-dismissible fade in' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button><strong>Error:</strong>  " . $ErrMsg . "</div>";
unset($_SESSION['errmsg']);
include_once("db.inc.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IRIS</title>
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="css/jquery-ui.min.css">
	<link rel="stylesheet" href="css/jquery-ui.theme.min.css">
	<link rel="stylesheet" href="css/jquery-ui.structure.min.css">
	<link rel="stylesheet" href="css/validationEngine.jquery.css"/>
	<link rel="stylesheet" href="css/font-awesome.css"/>
	<link rel="stylesheet" href="css/style.css"/>
	<script type="text/javascript" src="js/jquery-3.1.0.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/jquery.validationEngine-en.js"></script>
	<script type="text/javascript" src="js/jquery.validationEngine.js"></script>
	<style>
	.font-bold { font-weight: bold; }
	.label-as-badge { border-radius: 1em; }
	.btn-separator:after 
	{
		content: ' ';
		display: block;
		float: left;
		background: #ADADAD;
		margin: 0 10px;
		height: 34px;
		width: 1px;
	}
	</style>
</head>
<body>

	<div class="container">
	<nav class="navbar navbar-inverse" role="navigation" style="margin-bottom: 0">
		<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
			<a class="navbar-brand" href="index.php">IRIS</a></div>
			<div id="navbar" class="navbar-collapse collapse">
<?php
if((!isset($_SESSION['userid'])) || ($_SESSION['userid'] == ''))
{
?>
			<ul class="nav navbar-top-links navbar-nav">
			<li><a href="index.php">Home</a></li>
			<li><a href="">Services</a></li>
			<li><a href="">Our clients</a></li>
			<li><a href="">Who are we?</a></li>
			<li><a href=''>Contact Us</a></li>
			<li><a href=''>F.A.Q.</a></li>
			</ul>
<?php
}
else
{
	$MailToUserList = getUserOptionList();
	$UnreadCount = getUserUnreadMailboxItemCount($_SESSION['userid']);
?>
			<ul class="nav navbar-top-links navbar-nav">
			<li><a href="dashboard.php">Dashboard</a></li>
			<li>
				<a href='#' class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class='fa fa-building'></i> Complexes <span class="caret"></span></a>
				<ul class="dropdown-menu">
					<li><a href="complexindex.php">My Complexes</a></li>
					<li><a href="complexindex.php">All Complexes</a></li>
				</ul>
			</li>
			<li><a href='orders.php'><i class='fa fa-dollar'></i> Orders</a></li>
			<li><a href='#'><i class='fa fa-dollar'></i> Customers</a></li>
			<li>
				<a href='#' class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class='fa fa-shopping-cart'></i> Products <span class="caret"></span></a>
				<ul class="dropdown-menu">
					<li><a href='packageindex.php'><i class='fa fa-shopping-cart'></i> Products</a></li>
					<li><a href='packageoptions.php'><i class='fa fa-calculator'></i> Product Options</a></li>
				</ul>
			</li>
			<li>
				<a href='#' class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class='fa fa-bank'></i> Accounting <span class="caret"></span></a>
				<ul class="dropdown-menu">
					<li><a href='liveorders.php'><i class='fa fa-diamond'></i> Live Orders</a></li>
					<li><a href='unsentinvs.php'><i class='fa fa-question'></i> Unsent Invoices</a></li>
					<li><a href='unpaidinvs.php'><i class='fa fa-question'></i> Unpaid Invoices</a></li>
					<li role='separator' class='divider'></li>
					<li><a href='suppliers.php'><i class='fa fa-bank'></i> Suppliers</a></li>
					<li><a href='accounts.php'><i class='fa fa-bank'></i> Transaction Accounts</a></li>
				</ul>
			</li>
			<li>
				<a href='#' class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class='fa fa-cog'></i> Configuration <span class="caret"></span></a>
				<ul class="dropdown-menu">
					<li><a href='managents.php'><i class='fa fa-users'></i> Managing Agents</a></li>
					<li><a href='securecomps.php'><i class='fa fa-users'></i> Security Companies</a></li>
					<li><a href='userindex.php'><i class='fa fa-users'></i> System Users</a></li>
				</ul>
			</li>
			</ul>
			<ul class="nav navbar-top-links navbar-right">
				<!-- <li><a href='#' class='btn btn-warning btn-xs' title='Schedule' onclick='openInbox();'><i class='fa fa-calendar'></i></a></li>
				<li><a href='#' class='btn btn-success btn-xs' title='Inbox' onclick='openInbox();'><i class='fa fa-envelope-o'></i> <span class='label label-danger' id='unreadcounter'><?php echo $UnreadCount; ?></span></a></li> -->
				<li><a href="logout.php" class='btn btn-danger btn-xs' title='Logout <?php echo $_SESSION['username']; ?>'><i class='fa fa-power-off'></i></a></li>
			</ul>
<?php
}	
?>
	</div></div>
	</nav>
	</div>
	<div class="container" role="main">
		<div class='row'><div class='col-md-6'><h3>IRIS</h3></div>
			<div class='col-md-6'>
				<input type='text' class='form-control pull-right' name='superSearch' id='superSearch' placeholder='Super Search'>
				<!-- <div class='input-group'>
				
				<i class='fa fa-search'></i>
				</div> -->
			</div></div>