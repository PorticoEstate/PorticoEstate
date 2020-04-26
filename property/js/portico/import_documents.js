/* global lang */

$(document).ready(function ()
{


	$("#doument_type").select2({
		placeholder: lang['doument type'],
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


});

this.refresh_files = function ()
{
	var order_id = $("#order_id").val();
	var oArgs = {menuaction: 'property.uiimport_documents.get_files', order_id: order_id};
	var requestUrl = phpGWLink('index.php', oArgs, true);
	JqueryPortico.updateinlineTableHelper(oTable0, requestUrl);
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
					$("#fieldset_file_input").hide();
					$("#order_info").hide();
					$("#message").text(data.error).show();
					$("#vendor_name").text('');
					$("#cadastral_unit").val('');
					$("#location_code").val('');
					$("#building_number").val('');
					$("#remark").val('');

				}
				else
				{
					$("#message").hide();
					$("#order_info").show();
					$("#fieldset_file_input").show("slow");

					$('#fileupload').fileupload(
						'option',
						'url',
						phpGWLink('index.php', {menuaction: 'property.uiimport_documents.handle_import_files', order_id: order_id})
						);
				}

				$("#vendor_name").text(data.vendor_name);
				$("#cadastral_unit").val(data.cadastral_unit);
				$("#location_code").val(data.location_code);
				$("#building_number").val(data.building_number);
				$("#remark").val(data.remark);
			}
			else
			{
			}

			refresh_files();
		}
	});
};

this.validate_info = function ()
{
	var order_id = $("#order_id").val();

	var oArgs = {menuaction: 'property.uiimport_documents.validate_info', order_id: order_id};
	var requestUrl = phpGWLink('index.php', oArgs, true);
	JqueryPortico.updateinlineTableHelper('datatable-container_0', requestUrl);

	var cadastral_unit = $("#cadastral_unit").val();
	var location_code = $("#location_code").val();
	var building_number = $("#building_number").val();
	var remark = $("#remark").val();

	var $html = [];

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
		$("#validate_message").html($html.join('<br/>'));
		$("#validate_message").addClass('error');
	}
	else
	{
		$("#validate_message").html('');
		$("#validate_message").removeClass('error');

	}

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
};

