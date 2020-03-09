$(document).ready(function ()
{
	var collapseOne = getCookie('collapseOne');
	if (collapseOne === 'true')
	{
		$("#collapseOne").addClass('show');
	}
	else
	{
		$("#collapseOne").removeClass('show');
	}

	$('#collapseOne').on('hidden.bs.collapse', function (e)
	{
		setCookie('collapseOne', false, 1);
	});
	$('#collapseOne').on('shown.bs.collapse', function (e)
	{
		setCookie('collapseOne', true, 1);
	});

	var collapseTwo = getCookie('collapseTwo');
	if (collapseTwo === 'true')
	{
		$("#collapseTwo").addClass('show');
	}
	else
	{
		$("#collapseTwo").removeClass('show');
	}

	$('#collapseTwo').on('hidden.bs.collapse', function (e)
	{
		setCookie('collapseTwo', false, 1);
	});
	$('#collapseTwo').on('shown.bs.collapse', function (e)
	{
		setCookie('collapseTwo', true, 1);
	});

	var collapseSubMenu = getCookie('collapseSubMenu');
	if (collapseSubMenu === 'true')
	{
		$("#collapseSubMenu").addClass('show');
	}
	else
	{
		$("#collapseSubMenu").removeClass('show');
	}

	$('#collapseSubMenu').on('hidden.bs.collapse', function (e)
	{
		setCookie('collapseSubMenu', false, 1);
	});
	$('#collapseSubMenu').on('shown.bs.collapse', function (e)
	{
		setCookie('collapseSubMenu', true, 1);
	});



	$("#myProfile_form").on("submit", function (e)
	{
		e.preventDefault();
		var thisForm = $(this);
		var requestUrl = $(thisForm).attr("action");
		$.ajax({
			type: 'POST',
			url: requestUrl,
			data: $(thisForm).serialize(),
			success: function (data)
			{
				if (data)
				{
					if (data.status == "saved")
					{
						var submitBnt = $(thisForm).find("input[type='submit']");
						$(submitBnt).val("Lagret");
					}
				}
			}
		});
	});
});


function setCookie(cname, cvalue, exdays)
{
	var d = new Date();
	d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
	var expires = "expires=" + d.toUTCString();
	document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname)
{
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for (var i = 0; i < ca.length; i++)
	{
		var c = ca[i];
		while (c.charAt(0) == ' ')
		{
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0)
		{
			return c.substring(name.length, c.length);
		}
	}
	return "";
}
