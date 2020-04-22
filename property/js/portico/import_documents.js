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
			if (data != null)
			{
				if (data.error)
				{
					$("#fieldset_file_input").hide();
					$("#message").text(data.error).show();
					$("#vendor_name").text('')
					$("#cadastral_unit").val('')
					$("#location_code").val('')
					$("#building_number").val('')
				}
				else
				{
					$("#message").hide();
					$("#fieldset_file_input").show("slow");

					$('#fileupload').fileupload(
						'option',
						'url',
						phpGWLink('index.php', {menuaction: 'property.uiimport_documents.handle_import_files', order_id: order_id})
						);
				}

				$("#vendor_name").text(data.vendor_name)
				$("#cadastral_unit").val(data.cadastral_unit)
				$("#location_code").val(data.location_code)
				$("#building_number").val(data.building_number)
			}
			else
			{
			}

			refresh_files();
		}
	});

};
