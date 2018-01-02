var pageLayout;

$(document).ready(function ()
{
	// create page layout
	pageLayout = $('body').layout({
		stateManagement__enabled: true
		, defaults: {
		}
		, north: {
			size: "auto"
			, spacing_open: 0
			, closable: false
			, resizable: false
		}
		, west: {
			size: 250
			, spacing_closed: 22
			, togglerLength_closed: 140
			, togglerAlign_closed: "top"
			, togglerContent_closed: "M<BR>E<BR>N<BR>Y"
			, togglerTip_closed: "Open & Pin Contents"
			, sliderTip: "Slide Open Contents"
			, slideTrigger_open: "mouseover"
			, initClosed: false
		}
		, east: {
			initClosed: true
			, initHidden: true
			, spacing_closed: 0
				//	,   closable:				false
			, resizable: true
			, slidable: true

		}
		, south: {
			maxSize: 200
			, spacing_closed: 0			// HIDE resizer & toggler when 'closed'
			, spacing_open: 0
			, slidable: false		// REFERENCE - cannot slide if spacing_closed = 0
			, initClosed: false
			, resizable: false
		}

	});

	pageLayout.hide("east");
//
//		console.log(localStorage);
//		if (typeof(localStorage['pageLayout_west_closed']) != 'undefined' && localStorage['pageLayout_west_closed'] == 1)
//		{
//			pageLayout.show("west");
//		}

	/**
	 * Experimental : requires live update of js and css
	 * @param {type} requestUrl
	 */
	update_content = function (requestUrl)
	{

		window.location.href = requestUrl;
		return false;
		requestUrl += '&phpgw_return_as=stripped_html';
		$.ajax({
			type: 'GET',
			url: requestUrl,
			success: function (data)
			{
				if (data != null)
				{
					$("#center_content").html(data);
				}
			}
		});

	}

	$("#template_selector").change(function ()
	{

		var template = $(this).val();
		//user[template_set] = template;
		var oArgs = {appname: 'preferences', type: 'user'};
		var requestUrl = phpGWLink('preferences/preferences.php', oArgs, true);

		$.ajax({
			type: 'POST',
			dataType: 'json',
			data: {user: {template_set: template}, submit: true},
			url: requestUrl,
			success: function (data)
			{
		//		console.log(data);
				location.reload(true);
			}
		});

	});

});
