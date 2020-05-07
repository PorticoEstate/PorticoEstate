/* global lang */

$(document).ready(function ()
{


	$("#document_category").select2({
		placeholder: lang['document categories'],
		language: "no",
		width: '50%'
	});
	$("#branch").select2({
		placeholder: lang['branch'],
		language: "no",
		width: '50%'
	});
	$("#building_part").select2({
		placeholder: lang['building part'],
		language: "no",
		width: '50%'
	});

	if ($("#order_id").val())
	{
		get_order_info();
	}

});

this.refresh_files = function ()
{
	var order_id = $("#order_id").val();
	var oArgs = {menuaction: 'property.uiimport_documents.get_files', order_id: order_id};
	var requestUrl = phpGWLink('index.php', oArgs, true);
	JqueryPortico.updateinlineTableHelper(oTable0, requestUrl);

	$('#step_2_next').hide();
	$("#message_step_2").hide();
	$('#step_2_view_all').hide();
	$('#tab-content').responsiveTabs('disable', 2);
	$('.record').addClass('disabled');
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
	}
};


this.validate_step_2 = function (next)
{
	var order_id = $("#order_id").val();

	var oArgs = {menuaction: 'property.uiimport_documents.validate_info', order_id: order_id};
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

	var allow_next = false;
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
					$("#message_step_2").hide();
					$('#step_2_next').show();
					allow_next = true;
					if (next && allow_next)
					{
						$('#tab-content').responsiveTabs('enable', 2);
						$('#tab-content').responsiveTabs('activate', 2);
					}
				}
				else
				{
					$('#step_2_next').hide();
					$('#step_2_import').hide();
					$("#message_step_2").html(lang['Missing info']).show();
					$("#message_step_2").addClass('error');
				}
			}
		}
	});

	$('#step_2_view_all').show();
	$(window).scrollTop(0);




};

this.step_2_import = function ()
{
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
			refresh_files();

		}
	});

};

