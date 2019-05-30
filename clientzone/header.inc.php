<?php
include_once("../db.inc.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="keywords" content="">
	<meta name="description" content="">
	<title>Legendante Solutions</title>
	<!-- stylesheet css -->
	<link rel="stylesheet" href="/css2/bootstrap.min.css">
	<link rel="stylesheet" href="/css2/font-awesome.min.css">
	<link rel="stylesheet" href="/css2/nivo-lightbox.css">
	<link rel="stylesheet" href="/css2/nivo_themes/default/default.css">
	<link rel="stylesheet" href="/css2/style.css">
	<link rel="stylesheet" href="/css2/equal-height-columns.css" />
	<link rel="stylesheet" href="/css2/jquery-ui.min.css" />
	<link rel="stylesheet" href="/css2/validationEngine.jquery.css" />
	<!-- google web font css -->
	<link href='http://fonts.googleapis.com/css?family=Raleway:400,300,600,700' rel='stylesheet' type='text/css'>
	<script type="text/javascript" src="/js/jquery-3.1.0.min.js"></script>
	<script type="text/javascript" src="/js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="/js/jquery.validationEngine-en.js"></script>
	<script type="text/javascript" src="/js/jquery.validationEngine.js"></script>
</head>
<body>
<div class='container'>
	<div class='row'>
		<div class='col-md-6'><a href='index.php'><img src='images/logowhite.jpg' class='pull-right' style='margin-top: 15px;'></a></div>
		<div class='col-md-6'><h1>Client Zone</h1></div>
	</div>
</div>
<?php
if(isset($_SESSION['customerid']))
{
?>
<div class="navbar navbar-default" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="icon icon-bar"></span>
				<span class="icon icon-bar"></span>
				<span class="icon icon-bar"></span>
			</button>
			<strong>Welcome back <?php echo $_SESSION['firstname']; ?></strong>
		</div>
		<div class="collapse navbar-collapse">
			
			<ul class="nav navbar-nav navbar-right">
				<li><a href="dashboard.php#packages" class="smoothScroll">MY PACKAGES</a></li>
				<li><a href="dashboard.php#details" class="smoothScroll">MY DETAILS</a></li>
				<!-- <li><a href="index.php#pricing" class="smoothScroll">INVOICES</a></li>
				<li><a href="dashboard.php#logfault" class="smoothScroll">LOG A FAULT</a></li> -->
				<li><a href="logout.php" class="smoothScroll bg-danger"><i class='fa fa-close'></i> LOGOUT</a></li>
				<!-- <li><a href="faq.php" class="smoothScroll">F.A.Q's</a></li>
				<li><a href="about.php" class="smoothScroll">ABOUT</a></li> -->
			</ul>
		</div>
	</div>
</div>
<?php
}
?>
<!-- navigation -->
<!--<div class="navbar navbar-default navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="icon icon-bar"></span>
				<span class="icon icon-bar"></span>
				<span class="icon icon-bar"></span>
			</button>
			<a href="index.php" class="navbar-brand smoothScroll"><img src="images/logowhite.jpg"></a>
		</div>
		<div class="collapse navbar-collapse">
			<ul class="nav navbar-nav navbar-right">
				<li><a href="index.php#home" class="smoothScroll">HOME</a></li>
				<li><a href="index.php#aboutfibre" class="smoothScroll">ALL ABOUT FIBRE</a></li>
				<li><a href="index.php#pricing" class="smoothScroll">PACKAGES</a></li>
				<li><a href="index.php#whyus" class="smoothScroll">ESTATE/COMPLEX INSTALLATIONS</a></li>
				<li><a href="faq.php" class="smoothScroll">F.A.Q's</a></li>
				<li><a href="about.php" class="smoothScroll">ABOUT</a></li>
			</ul>
		</div>
	</div>
</div>-->
<div class='container'>