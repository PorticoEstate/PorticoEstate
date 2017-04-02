var oArgs = {
	menuaction: 'helpdesk.bogeneric.get_autocomplete',
	type: 'email_recipient_set',
	active: 1
};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'recipient_set_name', 'recipient_set_id', 'recipient_set_container');


$(window).on('load', function ()
{
	recipient_set_id = $('#recipient_set_id').val();
	if (recipient_set_id)
	{
		recipient_set_id_selection = recipient_set_id;
	}
	$("#recipient_set_name").on("autocompleteselect", function (event, ui)
	{
		var recipient_set_id = ui.item.value;
//		if (recipient_set_id != recipient_set_id_selection)
//		{
		populateCandidates('recipient_set', recipient_set_id);
//		}
	});

	$.fn.insertAtCaret = function (myValue)
	{
		myValue = myValue.trim();
		CKEDITOR.instances['content'].insertText(myValue);
	};

});

function populateCandidates(type, recipient_set_id)
{
	recipient_set_id = recipient_set_id || $('#recipient_set_id').val();

	if (!recipient_set_id)
	{
		return;
	}

	oArgs = {
		menuaction: 'helpdesk.uiemail_out.get_candidates',
		type: type,
		set_id: recipient_set_id,
		id: $('#id').val()
	};

	var requestUrl = phpGWLink('index.php', oArgs, true);
	JqueryPortico.updateinlineTableHelper(oTable1, requestUrl);

}

this.onActionsClick_candidates = function (type, ids)
{
//		console.log(ids);
	oArgs = {
		menuaction: 'helpdesk.uiemail_out.set_candidates',
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
				menuaction: 'helpdesk.uiemail_out.get_recipients',
				id: $('#id').val()
			};

			var requestUrl = phpGWLink('index.php', oArgs, true);
			JqueryPortico.updateinlineTableHelper(oTable2, requestUrl);
			oArgs = {
				menuaction: 'helpdesk.uiemail_out.get_candidates',
				type: 'recipient_set',
				set_id: $('#recipient_set_id').val(),
				id: $('#id').val()
			};

			var requestUrl = phpGWLink('index.php', oArgs, true);
			JqueryPortico.updateinlineTableHelper(oTable1, requestUrl);
		}
	});

}
this.onActionsClick_recipient = function (type, ids)
{
	oArgs = {
		menuaction: 'helpdesk.uiemail_out.' + type,
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
				menuaction: 'helpdesk.uiemail_out.get_recipients',
				id: $('#id').val()
			};

			var requestUrl = phpGWLink('index.php', oArgs, true);
			JqueryPortico.updateinlineTableHelper(oTable2, requestUrl);
		}
	});

}

function template_lookup()
{
	var oArgs = {menuaction: 'helpdesk.uilookup.email_template'};
	var strURL = phpGWLink('index.php', oArgs);
	TINY.box.show({iframe: strURL, boxid: "frameless", width: 750, height: 450, fixed: false, maskid: "darkmask", maskopacity: 40, mask: true, animate: true, close: true});
}
