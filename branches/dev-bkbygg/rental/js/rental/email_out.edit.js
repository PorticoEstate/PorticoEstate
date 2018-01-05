var oArgs = {
	menuaction: 'rental.uicomposite.index',
	type: 'all_composites',
	furnished_status: 4,
	has_contract: 'has_contract',
	is_active: 'active'
};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'composite_name', 'composite_id', 'composite_container', 'name');

$(window).on('load', function ()
{
	composite_id = $('#composite_id').val();
	if (composite_id)
	{
		composite_id_selection = composite_id;
	}
	$("#composite_name").on("autocompleteselect", function (event, ui)
	{
		var composite_id = ui.item.value;
//		if (composite_id != composite_id_selection)
//		{
		populateCandidates('composite', composite_id);
//		}
	});
});

function populateCandidates(type, composite_id)
{
	composite_id = composite_id || $('#composite_id').val();

	if (!composite_id)
	{
		return;
	}

	oArgs = {
		menuaction: 'rental.uiemail_out.get_candidates',
		type: type,
		id: composite_id
	};

	var requestUrl = phpGWLink('index.php', oArgs, true);
	JqueryPortico.updateinlineTableHelper(oTable1, requestUrl);

}

this.onActionsClick_candidates = function (type, ids)
{
//		console.log(ids);
	oArgs = {
		menuaction: 'rental.uiemail_out.set_candidates',
		id: $('#id').val()
	};

	var requestUrl = phpGWLink('index.php', oArgs, true);

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: requestUrl,
		data: {ids: ids, type: type},
		success: function (data)
		{
			if (data != null)
			{

			}
			oArgs = {
				menuaction: 'rental.uiemail_out.get_recipients',
				id: $('#id').val()
			};

			var requestUrl = phpGWLink('index.php', oArgs, true);
			JqueryPortico.updateinlineTableHelper(oTable2, requestUrl);
			oArgs = {
				menuaction: 'rental.uiemail_out.get_candidates',
				type: 'dummy',
				id: 0
			};

			var requestUrl = phpGWLink('index.php', oArgs, true);
			JqueryPortico.updateinlineTableHelper(oTable1, requestUrl);
		}
	});

}
this.onActionsClick_recipient = function (type, ids)
{
	oArgs = {
		menuaction: 'rental.uiemail_out.' + type,
		id: $('#id').val()
	};

	var requestUrl = phpGWLink('index.php', oArgs, true);

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: requestUrl,
		data: {ids: ids, type: type},
		success: function (data)
		{
			if (data != null)
			{

			}
			oArgs = {
				menuaction: 'rental.uiemail_out.get_recipients',
				id: $('#id').val()
			};

			var requestUrl = phpGWLink('index.php', oArgs, true);
			JqueryPortico.updateinlineTableHelper(oTable2, requestUrl);
		}
	});

}

function template_lookup()
{
	var oArgs = {menuaction: 'rental.uilookup.email_template'};
	var strURL = phpGWLink('index.php', oArgs);
	TINY.box.show({iframe: strURL, boxid: "frameless", width: 750, height: 450, fixed: false, maskid: "darkmask", maskopacity: 40, mask: true, animate: true, close: true});
}
