
function searchUser()
{
	if ($.trim($('#username').val()) === '')
	{
		alert('enter username');
		return false;
	}

	var oArgs = {menuaction: 'frontend.uidelegates.search_user'};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	$('.loading').css({"visibility": "visible"});

	var data = {"username": document.getElementById('username').value};
	JqueryPortico.execute_ajax(requestUrl, function (result)
	{

		var error = false;
		document.getElementById('custom_message').innerHTML = '';

		if (typeof (result.message) !== 'undefined')
		{
			document.getElementById('custom_message').innerHTML = result.message.msg;
		}

		if (typeof (result.error) !== 'undefined')
		{
			document.getElementById('custom_message').innerHTML = result.error.msg;
			error = true;
		}

		if (typeof (result.data) !== 'undefined')
		{
			document.getElementById('username').value = result.data.username;
			document.getElementById('firstname').value = result.data.firstname;
			document.getElementById('lastname').value = result.data.lastname;
			if (typeof (result.data.email) !== 'undefined')
			{
				document.getElementById('email').value = result.data.email;
			}
			document.getElementById('account_id').value = result.data.account_id;

			if(typeof (result.data.username) !== 'undefined' && result.data.username && !error)
			{
				$("#add").show();
			}
			else
			{
				$("#add").hide();
			}
		}
		$('.loading').css({"visibility": "hidden"});

	}, data, "POST", "JSON");
}
