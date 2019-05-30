$(document).ready(function()
{
	// checkPriorityMail();
	// var timeoutID = setInterval(function() { checkPriorityMail(); }, 30000);
	$('#priority-modal').on('hidden.bs.modal', function () 
	{
		pauseTimer = 0;
	});
	$( "#superSearch" ).autocomplete({
		source: "superSearch.php",
		minLength: 3,
		success: function (data) 
		{
			response($.map(data.employees, function (item) 
			{
				// return 
				// {
					var AC = new Object();
                    //autocomplete default values REQUIRED
					AC.id = item.id;
                    AC.label = item.value;
                    AC.value = item.value;
                    //extend values
                    AC.type = item.type;
                    return AC
				// };
			}));
		},
		select: function( event, ui ) 
		{
			if(ui.item)
			{
				if(ui.item.type == 'customer')
				{
					window.location = 'customer.php?cid=' + ui.item.id;
				}
				else if(ui.item.type == 'complex')
				{
					window.location = 'complex.php?cid=' + ui.item.id;
				}
			}
			else
			{
				console.log("Nothing selected, input was " + this.value);
				$('#compstatus').addClass("text-danger").html("Unknown");
			}
		},
	});
});	

var pauseTimer = 0;
function checkPriorityMail()
{
	var flaggy = 0;
	var adate = new Date().getTime();
	console.log(adate + " :Here");
	if(pauseTimer == 0)
	{
		$.ajax({async: false, type: "POST", url: "ajaxGetUnreadMailCount.php", dataType: "json",
			data: "dc=" + adate,
			success: function (feedback)
			{
				if(parseInt(feedback.count) > 0)
				{
					$('#unreadcounter').html(feedback.count);
					$('#unreadcounter').show();
				}
				else if(parseInt(feedback.count) == 0)
				{
					$('#unreadcounter').hide();
				}
			},
			error: function(request, feedback, error)
			{
				console.log("Request failed\n" + feedback + "\n" + error);
				return false;
			}
		});
		
		$('#mypriority').html('');
		$.ajax({async: false, type: "POST", url: "ajaxGetPriorityMail.php", dataType: "html",
			data: "dc=" + adate,
			success: function (feedback)
			{
				if(feedback != '')
				{
					flaggy = 1;
					pauseTimer = 1;
					$('#mypriority').append(feedback);
				}
			},
			error: function(request, feedback, error)
			{
				console.log("Request failed\n" + feedback + "\n" + error);
				return false;
			}
		});
		if(flaggy == 1)
			$("#priority-modal").modal("show");
	}
}

function openInbox()
{
	$('#replyToID').val('');
	$('#replyToThreadID').val('');
	var adate = new Date().getTime();
	$('#myinbox').html('');
	$.ajax({async: false, type: "POST", url: "ajaxGetInbox.php", dataType: "html",
		data: "dc=" + adate,
		success: function (feedback)
		{
			$('#myinbox').append(feedback);
		},
		error: function(request, feedback, error)
		{
			console.log("Request failed\n" + feedback + "\n" + error);
			return false;
		}
	});
	$("#inbox-modal").modal("show");
}

function openMailItem(mailid)
{
	$('#replyToMailID').val(mailid);
	$('#replyButton').show();
	var adate = new Date().getTime();
	$.ajax({async: false, type: "POST", url: "ajaxGetMailItem.php", dataType: "json",
		data: "dc=" + adate + "&mid=" + mailid,
		success: function (feedback)
		{
			$('#readfrom').html(feedback.fromtext);
			$('#readsubject').html(feedback.subject);
			$('#readbody').html(feedback.msgbody);
			$('#replyToUserID').val(feedback.senderid);
			$('#replyToThreadID').val(feedback.threadid);
			$('#readthread').html(feedback.thread);
			if(feedback.senderid == 0)
				$('#replyButton').hide();
		},
		error: function(request, feedback, error)
		{
			console.log("Request failed\n" + feedback + "\n" + error);
			return false;
		}
	});
	$('#mailrow_' + mailid).removeClass('font-bold bg-info');
	$('#mailitem-modal').modal("show");
}

function replyToMail()
{
	var repSubj = $('#readsubject').html();
	console.log(repSubj.substr(3));
	if(repSubj.substr(3) != 'Re:')
		repSubj = 'Re: ' + repSubj;
	var mailid = $('#replyToMailID').val();
	$('#mailto').val($('#replyToUserID').val());
	$('#mailsubject').val(repSubj);
	$("#mailsend-modal").modal("show");
}

function deleteMailItem(mailid)
{
	if(confirm("Are you sure you want to delete this message?"))
	{
		var adate = new Date().getTime();
		$.ajax({async: false, type: "POST", url: "ajaxDeleteMailItem.php", dataType: "html",
			data: "dc=" + adate + "&mid=" + mailid,
			success: function (feedback)
			{
				$('#mailrow_' + mailid).hide();
			},
				error: function(request, feedback, error)
				{
					console.log("Request failed\n" + feedback + "\n" + error);
					return false;
				}
			});
	}
}

function openSendMail()
{
	$("#mailsend-modal").modal("show");
}