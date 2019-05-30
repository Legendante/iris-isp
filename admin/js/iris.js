function getRandomPassword()
{
	var adate = new Date().getTime();
	var feedbackval;
	$.ajax({async: false, type: "POST", url: "ajaxGetPassword.php", dataType: "html",
		data: "dc=" + adate,
		success: function (feedback)
		{
			feedbackval = feedback;
		},
		error: function(request, feedback, error)
		{
			console.log("Request failed\n" + feedback + "\n" + error);
			return false;
		}
	});
	return feedbackval;
}

function getPackageGroupTypes(GroupID)
{
	var adate = new Date().getTime();
	var feedbackval;
	$.ajax({async: false, type: "POST", url: "ajaxGetPackageGroupTypes.php", dataType: "json",
		data: "dc=" + adate + "&gid=" + GroupID,
		success: function (feedback)
		{
			feedbackval = feedback;
		},
		error: function(request, feedback, error)
		{
			console.log("Request failed\n" + feedback + "\n" + error);
			return false;
		}
	});
	return feedbackval;
}