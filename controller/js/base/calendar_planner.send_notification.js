submitSendNotificationForm = function (e, form)
{
	e.preventDefault();

	var requestUrl = $(form).attr("action");
	$.ajax({
		type: 'POST',
		url: requestUrl,
		data: $(form).serialize(),
		success: function (data)
		{
			if (data)
			{
				var ok = data.ok || [];
				$(ok).each(function (i, obj)
				{
					$("#send_email_" + obj).attr('disabled', 'true');
					$("#send_email_" + obj).closest('tr').addClass('bg-success');
					$("#send_email_" + obj).hide();

					//	console.log(obj);

				});

				var error = data.error || [];
				$(error).each(function (i, obj)
				{
					$("#send_email_" + obj).closest('tr').addClass('bg-danger');
				});

			}
		}
	});
};

checkall = function ()
{
	var checkall_flag = $("#checkall_flag").attr('checkall_flag');
	if (checkall_flag == 1)
	{
		$(".mychecks").each(function ()
		{
			$(this).prop("checked", false);
		});

		document.getElementById("checkall_flag").setAttribute('checkall_flag', 0);
	}
	else
	{
		$(".mychecks").each(function ()
		{
			$(this).prop("checked", true);
		});

		document.getElementById("checkall_flag").setAttribute('checkall_flag', 1);
	}
};
