var location_code_selection = "";
var vendor_id = 0;
this.fetch_vendor_email = function ()
{
	if (document.getElementById('vendor_id').value)
	{
		base_java_url['vendor_id'] = document.getElementById('vendor_id').value;
	}

	if (document.getElementById('vendor_id').value != vendor_id)
	{
		base_java_url['action'] = 'get_vendor';
		base_java_url['field_name'] = 'mail_recipients';
		base_java_url['preselect'] = true;
		var oArgs = base_java_url;
		var strURL = phpGWLink('index.php', oArgs, true);
		JqueryPortico.updateinlineTableHelper(oTable1, strURL);
		vendor_id = document.getElementById('vendor_id').value;
	}
};

this.fetch_vendor_contract = function ()
{
	if (!document.getElementById('vendor_id').value)
	{
		return;
	}

	if ($("#vendor_id").val() != vendor_id)
	{
		var oArgs = {menuaction: 'property.uiworkorder.get_vendor_contract', vendor_id: $("#vendor_id").val()};
		var requestUrl = phpGWLink('index.php', oArgs, true);
		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function (data)
			{
				if (data != null)
				{
					if (data.sessionExpired)
					{
						alert('Sesjonen er utløpt - du må logge inn på nytt');
						return;
					}

					if(data.length > 0)
					{
						$("#vendor_contract_id").attr("data-validation", "required");
						htmlString = "<option value=''> kontrakter funnet</option>";
					}
					else
					{
						$("#vendor_contract_id").removeAttr("data-validation");
						htmlString = "<option value=''> kontrakter ikke funnet</option>";
					}

					var obj = data;

					$.each(obj, function (i)
					{
						htmlString += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
					});

					$("#vendor_contract_id").html(htmlString);
				}
			}
		});

	}
};

window.on_vendor_updated = function ()
{
	fetch_vendor_contract();
	fetch_vendor_email();

	var location_code = $("#location_code").val();
	var vendor_id = $("#vendor_id").val();

	get_other_orders(location_code, vendor_id);
	get_other_deviations(location_code, vendor_id);


};


$(document).ready(function ()
{
	$.formUtils.addValidator({
		name: 'category',
		validatorFunction: function (value, $el, config, languaje, $form)
		{
			var validatet_category = $('#validatet_category').val();
			if(validatet_category ==1)
			{
				return true;
			}
			else
			{
				return false;
			}
		},
		errorMessage: 'Ugyldig kategori',
		errorMessageKey: ''
	});

	$("#global_category_id").change(function ()
	{
		var oArgs = {menuaction: 'property.boworkorder.get_category', cat_id: $(this).val()};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function (data)
			{
				if (data != null)
				{
					if (data.active != 1 || data.is_node === false)
					{
						alert('Denne kan ikke velges');
						$('#global_category_id').prop('selectedIndex', 0);
						$('#validatet_category').val('');
					}
					else
					{
						$('#validatet_category').val(1);
					}
				}
			}
		});
	});
});

var oArgs = {menuaction: 'property.uitts.get_eco_service'};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'service_name', 'service_id', 'service_container');

var oArgs = {menuaction: 'property.uitts.get_ecodimb'};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'ecodimb_name', 'ecodimb', 'ecodimb_container');

var oArgs = {menuaction: 'property.uitts.get_b_account'};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'b_account_name', 'b_account_id', 'b_account_container');

var oArgs = {menuaction: 'property.uitts.get_external_project'};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'external_project_name', 'external_project_id', 'external_project_container');

var oArgs = {menuaction: 'property.uitts.get_unspsc_code'};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'unspsc_code_name', 'unspsc_code', 'unspsc_code_container');

