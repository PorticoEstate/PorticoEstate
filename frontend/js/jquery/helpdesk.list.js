var newTicket = function (oArgs)
{

	var sUrl = phpGWLink('index.php', oArgs);

	TINY.box.show({iframe: sUrl, boxid: 'frameless', width: 650, height: 500, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true,
		close: true,
		closejs: function ()
		{
			refresh_entity();
		}
	});
};

refresh_entity = function ()
{
	oTable0.fnDraw();
};