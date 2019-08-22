
function keepAlive()
{
	var oArgs = {keep_alive: true};
	var keep_alive_url = phpGWLink('home.php', oArgs, true);

	$.ajax({
		cache: false,
		contentType: false,
		processData: false,
		type: 'GET',
		url: keep_alive_url,
		success: function (data, textStatus, jqXHR)
		{
			if (data)
			{
				if ( data.status !== 200)
				{
					//something...
				}
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown)
		{
			clearInterval(refreshIntervalId);
			alert('expired');
		}
	});
}

var refreshIntervalId = setInterval(keepAlive, 600000);  //My session expires at 10 minutes