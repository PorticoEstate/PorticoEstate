/* global lang, JqueryPortico, role, oTable0 */

$(document).ready(function ()
{

	$("#document_category").select2({
		placeholder: lang['document categories'],
		language: "no",
		width: '75%'
	});
	$("#branch").select2({
		placeholder: lang['branch'],
		language: "no",
		width: '75%'
	});
	$("#building_part").select2({
		placeholder: lang['building part'],
		language: "no",
		width: '75%'
	});

	if ($("#order_id").val())
	{
		get_order_info();
	}

});

this.onActionsClick_files = function (action, files)
{
	var numSelected = files.length;
	if (numSelected === 0)
	{
		alert('None selected');
		return false;
	}

	var order_id = $('#order_id').val();
	var document_category = $('#document_category option:selected').toArray().map(item => item.text);
	var branch = $('#branch option:selected').toArray().map(item => item.text);
	var building_part = $('#building_part option:selected').toArray().map(item => item.value);

	if (action !== 'delete_file')
	{
		if (!document_category.length && !branch.length && !building_part.length)
		{
			alert('ingenting valgt');
			return false;
		}
	}

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: phpGWLink('index.php', {menuaction: 'property.uiimport_documents.update_file_data'}, true),
		data: {files: files, document_category: document_category, branch: branch, building_part: building_part, action: action, order_id: order_id},
		success: function (data)
		{
			if (data !== null)
			{

			}
			var oArgs = {menuaction: 'property.uiimport_documents.get_files', order_id: order_id};
			var strURL = phpGWLink('index.php', oArgs, true);

			JqueryPortico.updateinlineTableHelper('datatable-container_0', strURL);
			$('.record').addClass('disabled');
			$("#toggle_select0").addClass('fa-toggle-off');
			$("#toggle_select0").removeClass('fa-toggle-on');
			$('#step_2_next').hide();
			$("#message0").hide();
			$('#step_2_view_all').hide();

		},
		error: function (data)
		{
			alert('feil');
		}
	});
};

this.refresh_files = function (show_all_columns)
{
//	var show_all = show_all_columns || false;

	var order_id = $("#order_id").val();
	var oArgs = {menuaction: 'property.uiimport_documents.get_files', order_id: order_id};
	var requestUrl = phpGWLink('index.php', oArgs, true);
	JqueryPortico.updateinlineTableHelper(oTable0, requestUrl);
	$($.fn.dataTable.tables(true)).DataTable().scroller.measure().columns.adjust()
		.fixedColumns().relayout().draw();

	$('#step_2_next').hide();
	$('#step_2_import_validate_next').hide();
	$("#message0").hide();
	$('#step_2_view_all').hide();
	$('#tab-content').responsiveTabs('disable', 2);
	$('.record').addClass('disabled');

//	var api = oTable0.api();
//	if(show_all)
//	{
//		api.column( 4 ).visible( true, false );
//		api.column( 5 ).visible( true, false );
//	}
//	else
//	{
//		api.column( 4 ).visible( false, false );
//		api.column( 5 ).visible( false, false );
//	}
};

this.local_DrawCallback0 = function (container)
{
	var api = $("#" + container).dataTable().api();
	if (api.rows().data().count() > 0)
	{
		$('#step_2_validate').show();
	}
};


this.get_order_info = function ()
{
	var order_id = $("#order_id").val();
//	alert(order_id);

	var oArgs = {menuaction: 'property.uiimport_documents.get_order_info', order_id: order_id};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: requestUrl,
		success: function (data)
		{
			if (data !== null)
			{
				if (data.error)
				{
					$("#order_info").hide();
					$("#message_step_1").text(data.error).show();
					$("#vendor_name").text('');
					$("#cadastral_unit").val('');
					$("#location_code").val('');
					$("#building_number").val('');
					$("#remark").val('');
					$("#get_order_info").show();
					$("#validate_step_1").hide();
				}
				else
				{
					$("#get_order_info").hide();
					$("#validate_step_1").show();
					$("#message_step_1").hide();
					$("#order_info").show();

					$('#fileupload').fileupload(
						'option',
						'url',
						phpGWLink('index.php', {menuaction: 'property.uiimport_documents.handle_import_files', order_id: order_id})
						);
				}

				$("#vendor_name").text(data.vendor_name);
				$("#cadastral_unit").val(data.cadastral_unit);
				$("#location_code").val(data.location_code);
				$("#building_number").val(data.building_number[0]);
				$("#remark").val(data.remark);
			}
			else
			{
			}

			refresh_files();
		}
	});
};

this.validate_step_1 = function ()
{
	var cadastral_unit = $("#cadastral_unit").val();
	var location_code = $("#location_code").val();
	var building_number = $("#building_number").val();
	var order_id = $("#order_id").val();

	var $html = [];

	if (!order_id)
	{
		$html.push(lang['Missing value'] + ': ' + lang['order id']);
	}
	if (!cadastral_unit)
	{
		$html.push(lang['Missing value'] + ': ' + lang['cadastral unit']);
	}
	if (!location_code)
	{
		$html.push(lang['Missing value'] + ': ' + lang['location code']);
	}
	if (!building_number)
	{
		$html.push(lang['Missing value'] + ': ' + lang['building number']);
	}

	if ($html.length > 0)
	{
		$("#message_step_1").html($html.join('<br/>')).show();
		$("#message_step_1").addClass('error');
	}
	else
	{
		$("#message_step_1").html('').hide();
		$("#message_step_1").removeClass('error');
		$('#tab-content').responsiveTabs('enable', 1);
		$('#tab-content').responsiveTabs('activate', 1);
		$($.fn.dataTable.tables(true)).DataTable().scroller.measure().columns.adjust()
			.fixedColumns().relayout().draw();

	}
};


this.validate_step_2 = function (sub_step)
{
	var order_id = $("#order_id").val();

	var oArgs = {menuaction: 'property.uiimport_documents.validate_info', order_id: order_id, sub_step: sub_step};
	var requestUrl = phpGWLink('index.php', oArgs, true);
	JqueryPortico.updateinlineTableHelper('datatable-container_0', requestUrl);

	var cadastral_unit = $("#cadastral_unit").val();
	var location_code = $("#location_code").val();
	var building_number = $("#building_number").val();
	var remark = $("#remark").val();

	$.ajax({
		type: 'POST',
		dataType: 'json',
		data: {cadastral_unit: cadastral_unit, location_code: location_code, building_number: building_number, remark: remark, action: 'set_tag', order_id: order_id},
		url: phpGWLink('index.php', {menuaction: 'property.uiimport_documents.update_file_data'}, true),
		success: function (data)
		{
			if (data != null)
			{
			}
		}
	});

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: requestUrl,
		success: function (data)
		{
			if (data != null)
			{
				if (data.recordsTotal === 0)
				{
					//$("#message0").hide();
					$("#message0").removeClass('error');
					$("#message0").addClass('msg_good');
					$("#message0").html('Ok').show();

					if (role === 'manager')
					{
						$('#step_2_import').show();
					}
					else
					{
						$('#step_2_next').show();
					}

					if (sub_step === 1)
					{
						$('#tab-content').responsiveTabs('enable', 2);
						$('#tab-content').responsiveTabs('activate', 2);
					}
					else if (sub_step === 2)
					{
						$('#step_2_validate').hide(500);
						$('#step_2_view_all').hide(500);
						$('#step_2_import').hide(500);
						$('#step_2_import_validate').hide(500);
						$('#step_2_import_validate_next').show(500);
					}
				}
				else
				{
					if (sub_step === 2)
					{
						$("#message0").html('Filer gjenstår').show();
					}
					else
					{
						$("#message0").html(lang['Missing info']).show();
						$('#step_2_next').hide();
						$('#step_2_import').hide();
					}
					$("#message0").removeClass('msg_good');
					$("#message0").addClass('error');
				}
			}
		}
	});

	$('#step_2_view_all').show();
	$(window).scrollTop(0);

};

this.step_2_import = function ()
{

	$("#step_2_import").prop("disabled", true);
	$("#step_2_validate").prop("disabled", true);
	$("#step_2_view_all").prop("disabled", true);

	$('.processing-import').show();
	$("#message0").hide();

	var order_id = $("#order_id").val();

	var oArgs = {menuaction: 'property.uiimport_documents.step_2_import', order_id: order_id};
	var requestUrl = phpGWLink('index.php', oArgs, true);


	$.ajax({
		type: 'POST',
		dataType: 'json',
		data: {},
		url: requestUrl,
		success: function (data)
		{
			if (data != null)
			{
				console.log(data);
			}
			refresh_files(true);
			$("#step_2_import").prop("disabled", false);
			$("#step_2_validate").prop("disabled", false);
			$("#step_2_view_all").prop("disabled", false);
			$('#step_2_import_validate').show();
			$('.processing-import').hide();
		}
	});
//	setTimeout(get_progress, 1000 /*1 second*/);

};

this.get_progress = function ()
{
	$.get(phpGWLink('index.php', {menuaction: 'property.uiimport_documents.get_progress'}, true), function (response)
	{
		console.log(response);
		if (!response.success)
		{
//			alert('Cannot find progress');
			return;
		}
		if (response.done)
		{
//			alert('Done!');
		}
		else
		{
//			alert('Progress at ' + response.precent + '%');
			setTimeout(get_progress, 1000 /*1 second*/);
		}

	});

};


this.move_to_step_3 = function ()
{
	$('#tab-content').responsiveTabs('enable', 2);
	$('#tab-content').responsiveTabs('activate', 2);

};

this.step_3_clean_up = function ()
{
	$('.processing-import').show();
	$("#step_3_clean_up").prop("disabled", true);
	var order_id = $("#order_id").val();

	var oArgs = {menuaction: 'property.uiimport_documents.step_3_clean_up', order_id: order_id};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	$.ajax({
		type: 'POST',
		dataType: 'json',
		data: {},
		url: requestUrl,
		success: function (data)
		{
			$('.processing-import').hide();
			if(data.status == 'ok')
			{
				$("#message_step_3").addClass('msg_good');
				$("#message_step_3").removeClass('error');
				$("#message_step_3").html('Filer slettet: ' + data.number_of_files).show();

				window.setTimeout(function ()
				{
					$("#message_step_3").html('Du blir videresendt til oversikten');
				}, 2000);

				window.setTimeout(function ()
				{
					window.location.href = phpGWLink('index.php', {menuaction: 'property.uiimport_documents.index'});
				}, 4000);
			}
			else
			{
				$("#message_step_3").html('Noe gikk feil med slettingen: ' + data.path_dir).show();
				$("#message_step_3").removeClass('msg_good');
				$("#message_step_3").addClass('error');
			}

		}
	});


};