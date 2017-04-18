function set_tab(tab)
{
	$("#active_tab").val(tab);
	if (tab === 'application')
	{
		$("#submit_group_bottom").hide();
	}
	else
	{
		$("#submit_group_bottom").show();
	}
}

add_application = function (app, menuaction, vendor_id)
{
	if (!vendor_id)
	{
		return;
	}

	oArgs = {
		menuaction: menuaction,
		vendor_id: vendor_id
	};

	var requestUrl;
	if (app === 'eventplannerfrontend')
	{
		requestUrl = phpGWLink('eventplannerfrontend/', oArgs);
	}
	else
	{
		requestUrl = phpGWLink('index.php', oArgs);
	}
	window.location = requestUrl;
};
