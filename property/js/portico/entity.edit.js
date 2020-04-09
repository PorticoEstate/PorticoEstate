/* global get_files_java_url */

this.fileuploader = function ()
{
	var sUrl = phpGWLink('index.php', multi_upload_parans);
	//TINY.box.show({iframe: sUrl, boxid: "frameless", width: 750, height: 450, fixed: false, maskid: "darkmask", maskopacity: 40, mask: true, animate: true, close: true}); //refresh_files is called after upload
	TINY.box.show({iframe: sUrl, boxid: 'frameless', width: 750, height: 450, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true,
		close: true,
		closejs: function ()
		{
			refresh_files()
		}
	});	
};

this.refresh_files = function ()
{
	var oArgs = get_files_java_url;
	var strURL = phpGWLink('index.php', oArgs, true);

	refresh_glider(strURL);

	oTable0.fnDraw();
}

this.showlightbox_add_inventory = function (location_id, id)
{
	var oArgs = {menuaction: 'property.uientity.add_inventory', location_id: location_id, id: id};
	var sUrl = phpGWLink('index.php', oArgs);

	TINY.box.show({iframe: sUrl, boxid: 'frameless', width: 650, height: 600, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true,
		close: true,
		closejs: function ()
		{
			refresh_inventory(location_id, id)
		}
	});
}

this.showlightbox_edit_inventory = function (location_id, id, inventory_id)
{
	var oArgs = {menuaction: 'property.uientity.edit_inventory', location_id: location_id, id: id, inventory_id: inventory_id};
	var sUrl = phpGWLink('index.php', oArgs);

	TINY.box.show({iframe: sUrl, boxid: 'frameless', width: 650, height: 600, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true,
		close: true,
		closejs: function ()
		{
			refresh_inventory(location_id, id)
		}
	});
}

this.showlightbox_show_calendar = function (location_id, id, inventory_id)
{
	var oArgs = {menuaction: 'property.uientity.inventory_calendar', location_id: location_id, id: id, inventory_id: inventory_id};
	var sUrl = phpGWLink('index.php', oArgs);

	TINY.box.show({iframe: sUrl, boxid: 'frameless', width: 650, height: 600, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true,
		close: true,
		closejs: function ()
		{
			refresh_inventory(location_id, id)
		}
	});
}

this.showlightbox_assigned_history = function (serie_id)
{
	var oArgs = {menuaction: 'property.uientity.get_assigned_history', serie_id: serie_id};
	var sUrl = phpGWLink('index.php', oArgs);

	TINY.box.show({iframe: sUrl, boxid: 'frameless', width: 400, height: 350, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true,
		close: true,
		closejs: false
	});
}

this.refresh_inventory = function (location_id, id)
{
	var oArgs = {menuaction: 'property.uientity.get_inventory', location_id: location_id, id: id};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	var api = oTable3.api();
	api.ajax.url(requestUrl).load();
}

this.onActionsClick = function (action)
{
	$("#controller_receipt").html("");
	if (action === 'add')
	{
		add_control();
	}

	var api = $('#datatable-container_4').dataTable().api();
	var selected = api.rows({selected: true}).data();

	var numSelected = selected.length;

	if (numSelected == 0)
	{
		alert('None selected');
		return false;
	}
	var ids = [];
	for (var n = 0; n < selected.length; ++n)
	{
		var aData = selected[n];
		ids.push(aData['serie_id']);
	}

	if (ids.length > 0)
	{
		var data = {ids: ids, action: action};
		data.repeat_interval = $("#repeat_interval").val();
		data.controle_time = $("#controle_time").val();
		data.service_time = $("#service_time").val();
		data.control_responsible = $("#control_responsible").val();
		data.control_start_date = $("#control_start_date").val();
		data.repeat_type = $("#repeat_type").val();

		var oArgs = {menuaction: 'property.controller_helper.update_control_serie', location_id : location_id, id: item_id };
		var requestUrl = phpGWLink('index.php', oArgs, true);
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			data: data,
			success: function (data)
			{
				if (data != null)
				{
					$("#controller_receipt").html(data.status + '::' + data.msg);
					if (data.status_kode == 'ok')
					{

					}
				}
			}
		});


		var oArgs2 = {menuaction: 'property.uientity.get_controls_at_component', location_id : location_id, id: item_id};
		var requestUrl2 = phpGWLink('index.php', oArgs2, true);
		JqueryPortico.updateinlineTableHelper('datatable-container_4', requestUrl2);
	}
}

function parseURL(url)
{
	var parser = document.createElement('a'),
		searchObject = {},
		queries, split, i;
	// Let the browser do the work
	parser.href = url;
	// Convert query string to object
	queries = parser.search.replace(/^\?/, '').split('&');
	for (i = 0; i < queries.length; i++)
	{
		split = queries[i].split('=');
		searchObject[split[0]] = split[1];
	}
	return {
		protocol: parser.protocol,
		host: parser.host,
		hostname: parser.hostname,
		port: parser.port,
		pathname: parser.pathname,
		search: parser.search,
		searchObject: searchObject,
		hash: parser.hash
	};
}

add_control = function ()
{
	oArgs = {location_id:location_id, id: item_id};
	oArgs.menuaction = 'property.controller_helper.add_control';
	oArgs.control_id = $("#control_id").val();
	oArgs.control_responsible = $("#control_responsible").val();
	oArgs.control_start_date = $("#control_start_date").val();
	oArgs.repeat_type = $("#repeat_type").val();
	if (!oArgs.repeat_type)
	{
		alert('velg type serie');
		return;
	}

	oArgs.repeat_interval = $("#repeat_interval").val();
	oArgs.controle_time = $("#controle_time").val();
	oArgs.service_time = $("#service_time").val();
	var requestUrl = phpGWLink('index.php', oArgs, true);
//								alert(requestUrl);

	$("#controller_receipt").html("");

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: requestUrl,
		success: function (data)
		{
			if (data != null)
			{
				$("#controller_receipt").html(data.status + '::' + data.msg);
				if (data.status_kode == 'ok')
				{
					$("#control_id").val('');
					$("#control_name").val('');
					$("#control_responsible").val('');
					$("#control_responsible_user_name").val('');
					$("#control_start_date").val('');
				}
			}
		}
	});

	var oArgs2 = {menuaction: 'property.uientity.get_controls_at_component', location_id: location_id, id: item_id};
	var requestUrl2 = phpGWLink('index.php', oArgs2, true);
	JqueryPortico.updateinlineTableHelper('datatable-container_4', requestUrl2);
};


var documents = null;
var requestUrlDoc = null;
	
$(document).ready(function ()
{
	$('#doc_type').change( function()
	{
		paramsTable7['doc_type'] = $(this).val();
		oTable7.fnDraw();				
	});

	$("#workorder_cancel").on("submit", function (e)
	{
		if ($("#lean").val() == 0)
		{
			return;
		}
		e.preventDefault();
		parent.closeJS_remote();
//		parent.hide_popupBox();
	});

	var click_action_on_table = false;
	$("#check_lst_time_span").change(function ()
	{
		var oArgs = {menuaction: 'property.uientity.get_checklists', location_id: location_id, id: item_id, year: $(this).val()};
		var requestUrl = phpGWLink('index.php', oArgs, true);
		var _oTable = JqueryPortico.updateinlineTableHelper('datatable-container_5', requestUrl);

		oArgs = {menuaction: 'property.uientity.get_cases', location_id: location_id, id: item_id, year: $(this).val()};
		requestUrl = phpGWLink('index.php', oArgs, true);
		JqueryPortico.updateinlineTableHelper('datatable-container_6', requestUrl);

		if (click_action_on_table == false)
		{
			$(_oTable).on("click", function (e)
			{
				var aTrs = _oTable.fnGetNodes();
				for (var i = 0; i < aTrs.length; i++)
				{
					if ($(aTrs[i]).hasClass('selected'))
					{
						var check_list_id = $('td', aTrs[i]).eq(0).text();
						updateCaseTable(check_list_id);
					}
				}
			});
			click_action_on_table = true
		}

	});

	$("#datatable-container_5 tr").on("click", function (e)
	{
		var check_list_id = $('td', this).eq(0).text();
		updateCaseTable(check_list_id);
	});

});

function updateCaseTable(check_list_id)
{
	if (!check_list_id)
	{
		return;
	}
	var oArgs = {menuaction: 'property.uientity.get_cases_for_checklist', check_list_id: check_list_id};
	var requestUrl = phpGWLink('index.php', oArgs, true);
	JqueryPortico.updateinlineTableHelper('datatable-container_6', requestUrl);
}

function newDocument(oArgs)
{
	oArgs['doc_type'] = $('#doc_type').val();
	oArgs['from'] = 'property.uientity.edit';

	var requestUrl = phpGWLink('index.php', oArgs);

	window.open(requestUrl, '_self');
};