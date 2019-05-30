<?php
?>
	</div>
<?php
if((isset($_SESSION['userid'])) && ($_SESSION['userid'] != ''))
{
?>
	<div class='modal fade' id='inbox-modal' tabindex='-1' role='dialog' aria-hidden='true'>
		<div class='modal-dialog modal-lg'>
			<div class='modal-content'>
				<div class='modal-header'><strong>My Inbox<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
				<div class='modal-body'>
					<table class='table table-bordered'>
					<thead><tr><th>Subject</th><th>From</th><th>Sent</th><th>Priority</th><th colspan='2'><button type='button' class='btn btn-primary pull-right' onclick='openSendMail();'><i class='fa fa-pencil'></i></button></th></tr></thead>
					<tbody id='myinbox'>
					
					</tbody>
					</table>
				</div>
				<div class='modal-footer'>
					<button type='button' class='btn btn-default btn-xs' data-dismiss='modal'><span class='fa fa-times'></span> Close</button>
				</div>
			</div>
		</div>
	</div>
	
	<div class='modal fade' id='priority-modal' tabindex='-1' role='dialog' aria-hidden='true'>
		<div class='modal-dialog modal-lg'>
			<div class='modal-content'>
				<div class='modal-header'><strong><i class='fa fa-exclamation'></i> Priority Messages<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
				<div class='modal-body'>
					<table class='table table-bordered'>
					<thead><tr><th>Subject</th><th>From</th><th>Sent</th><th>Priority</th><th colspan='2'><button type='button' class='btn btn-primary pull-right' onclick='openSendMail();'><i class='fa fa-pencil'></i></button></th></tr></thead>
					<tbody id='mypriority'>
					
					</tbody>
					</table>
				</div>
				<div class='modal-footer'>
					<button type='button' class='btn btn-default btn-xs' data-dismiss='modal'><span class='fa fa-times'></span> Close</button>
				</div>
			</div>
		</div>
	</div>
	
	<div class='modal fade' id='mailitem-modal' tabindex='-1' role='dialog' aria-hidden='true'>
		<div class='modal-dialog modal-lg'>
			<div class='modal-content'>
				<div class='modal-header'><strong>Message<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
				<div class='modal-body'>
					
					<input type='hidden' name='replyToUserID' id='replyToUserID' value=''>
					<table class='table table-bordered'>
					<tr><th>From:</th><td id='readfrom'></td></tr>
					<tr><th colspan='2'>Subject:</th></tr>
					<tr><td colspan='2' id='readsubject'></td></tr>
					<tr><th colspan='2'>Message:</th></tr>
					<tr><td colspan='2' id='readbody'></td></tr>
					<tr><th colspan='2'>Thread:</th></tr>
					<tr><td colspan='2' id='readthread'></td></tr>
					</table>
				</div>
				<div class='modal-footer'>
					<button type='button' class='btn btn-default btn-xs' data-dismiss='modal'><span class='fa fa-times'></span> Close</button>
					<button type='submit' class='btn btn-success' onclick='replyToMail();' id='replyButton'><span class='fa fa-send'></span> Reply</button>
				</div>
			</div>
		</div>
	</div>
	
	<div class='modal fade' id='mailsend-modal' tabindex='-1' role='dialog' aria-hidden='true'>
		<form method='POST' action='sendMail.php' id='mailSendForm'>
		<input type='hidden' name='replyToMailID' id='replyToMailID' value=''>
		<input type='hidden' name='replyToThreadID' id='replyToThreadID' value=''>
		<div class='modal-dialog modal-lg'>
			<div class='modal-content'>
				<div class='modal-header'><strong>Send Message<a href="#" class="close" data-dismiss="modal">&times;</a></strong></div>
				<div class='modal-body'>
					<table class='table table-bordered'>
					<tr><th>To:</th><td id='recipList'><select name='mailto[]' id='mailto'><option value=''>-- Select one --</option><?php echo $MailToUserList; ?></select>
					<button type='button' class='btn' onclick='addRecipient();'></button>
					</td></tr>
					<tr><th colspan='2'>Subject:</th></tr>
					<tr><td colspan='2'><input type='text' class='form-control' name='mailsubject' id='mailsubject'></td></tr>
					<tr><th colspan='2'>Message:</th></tr>
					<tr><td colspan='2'><textarea class='form-control' name='mailbody' id='mailbody'></textarea></td></tr>
					</table>
				</div>
				<div class='modal-footer'>
					<button type='button' class='btn btn-default btn-xs' data-dismiss='modal'><span class='fa fa-times'></span> Close</button>
					<button type='submit' class='btn btn-success'><span class='fa fa-send'></span> Send</button>
				</div>
			</div>
		</div>
		</form>
	</div>
	<script src="js/irisloggedin.js"></script>
	<script>
	function addRecipient()
{
	var addHTML = "<br><select name='mailto[]'><option value=''>-- Select one --</option><?php echo $MailToUserList; ?></select>";
	$('#recipList').append(addHTML);
	
}	
	</script>
<?php
}
?>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/iris.js"></script>
	</body>
</html>