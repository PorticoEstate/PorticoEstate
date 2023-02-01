/* global oTable0 */

var newTicket = function (oArgs)
{
	var sUrl = phpGWLink('index.php', oArgs);

//	TINY.box.show({iframe: sUrl, boxid: 'frameless', width: 650, height: 500, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true,
//		close: true,
//		closejs: function ()
//		{
//			refresh_entity();
//		}
//	});


	const urlParams = new URLSearchParams(sUrl);

	if (urlParams.has('height'))
	{
		const height = urlParams.get('height');
		$("#iframepopupModal").attr('height', height);
	}

	$('#popupModal').on('shown.bs.modal', function (e)
	{
		$("#iframepopupModal").attr("src", sUrl);
	});

	$('#popupModal').on('hidden.bs.modal', function (e)
	{
		$("#iframepopupModal").attr("src", 'about:blank');
		refresh_entity();
	});

	var myModal = new bootstrap.Modal(document.getElementById('popupModal'), {
		keyboard: false
	});
	myModal.show();

};

refresh_entity = function ()
{
	oTable0.fnDraw();
};