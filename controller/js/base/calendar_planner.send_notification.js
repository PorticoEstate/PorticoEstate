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
					$("#send_email_" + obj).closest('tr').addClass('badge-success');
					$("#send_email_" + obj).hide();

				//	console.log(obj);

				});

				var error = data.error || [];
				$(error).each(function (i, obj)
				{
					$("#send_email_" + obj).closest('tr').addClass('badge-danger');
				});

			}
		}
	});
};
