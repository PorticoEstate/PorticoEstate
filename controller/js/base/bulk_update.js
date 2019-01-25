$(document).ready(function ()
{
	$("#from").change(function ()
	{
		var oArgs1 = {menuaction: 'controller.uibulk_update.get_controller_serie', assigned_to: $(this).val(), results: -1};
		var strURL1 = phpGWLink('index.php', oArgs1, true);
		JqueryPortico.updateinlineTableHelper('datatable-container_0', strURL1);

		var oArgs2 = {menuaction: 'controller.uibulk_update.get_future_checklist', assigned_to: $(this).val(), results: -1};
		var strURL2 = phpGWLink('index.php', oArgs2, true);
		JqueryPortico.updateinlineTableHelper('datatable-container_1', strURL2);
	});
});

this.showlightbox_assigned_history = function (serie_id)
{
	var oArgs = {menuaction: 'property.uientity.get_assigned_history', serie_id: serie_id};
	var sUrl = phpGWLink('index.php', oArgs);

	TINY.box.show({iframe: sUrl, boxid: 'frameless', width: 400, height: 350, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true,
		close: true,
		closejs: false
	});
}
