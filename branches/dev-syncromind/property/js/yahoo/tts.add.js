	this.confirm_session = function(action)
	{
		var callback =
		{
			success: function(o)
			{
				var values = [];
				try
				{
					values = JSON.parse(o.responseText);
//					console.log(values);
				}
				catch (e)
				{
					return;
				}

				if(values['sessionExpired'] == true)
				{
					window.alert('sessionExpired - please log in');
					lightboxlogin();//defined i phpgwapi/templates/portico/js/base.js
				}
				else
				{
					document.getElementById(action).value = 1;
					document.form.submit();
				}

			},
			failure: function(o)
			{
				window.alert('failure - try again - once')
			},
			timeout: 5000
		};

		var oArgs = {menuaction:'property.bocommon.confirm_session'};
		var strURL = phpGWLink('index.php', oArgs, true);
		var request = YAHOO.util.Connect.asyncRequest('POST', strURL, callback);
	}
