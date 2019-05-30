<?php
include("header.inc.php");
$ErrMsg = (!isset($_SESSION['errmsg'])) ? '' : $ErrMsg;
if($ErrMsg != '')
	$ErrMsg = "<tr><th class='danger'>" . $ErrMsg . "</th></tr>";
?>
<div class='row'>
	<div class='col-md-7'>
		<div class='row'>
			<div class='col-md-12'>
			<h4>Login details</h4>
			<strong>Username:</strong> test@example.com<br>
			<strong>Password:</strong> Password<br>
			</div>
		</div>
	</div>
	<div class='col-md-5'>
		<div class='row'>
			<div class='col-md-12'>
				<div class='table-responsive'>
					<form method='POST' action='login.php' id='loginForm'>
					<table class='table table-bordered' border='1'>
					<tr><th>Login</th></tr>
					<?php echo $ErrMsg; ?>
					<tr><td>Username</td></tr>
					<tr><td><input type='text' name='username' id='username' class='validate[required] form-control'></td></tr>
					<tr><td>Password</td></tr>
					<tr><td><input type='password' name='userpass' id='userpass' class='validate[required] form-control'></td></tr>
					<tr><td><button class='btn btn-success push-right'><span class='glyphicon glyphicon-log-in'></span> Login</button></td></tr>
					</table>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
include("footer.inc.php");
?>