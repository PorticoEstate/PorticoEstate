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


this.preview = function (id)
{
	var oArgs = {menuaction: 'property.uiexternal_communication.view', id: id, preview_html: true};
	var strURL = phpGWLink('index.php', oArgs);
	Window1 = window.open(strURL, 'Search', "left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
};

$(document).ready(function ()
{
	var do_preview = $("#do_preview").val();

	if (do_preview)
	{
		preview(do_preview);
	}
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


$(window).on('load', function()
{

	$("#location_name").on("autocompleteselect", function (event, ui)
	{
		var location_code = ui.item.value;

		if (location_code !== location_code_selection)
		{
			location_code_selection = location_code;

			var temp = document.getElementById("new_note").value;
			if (temp)
			{
				temp = temp + "\n";
			}
			document.getElementById("new_note").value = temp + "Lokalisering: " + ui.item.label;
		}

		var vendor_id = $("#vendor_id").val();
		if (vendor_id && location_code)
		{
			get_other_orders(location_code, vendor_id);
			get_other_deviations(location_code, vendor_id);

		}
	});


});

$(document).ready(function ()
{

	//$("#datatable-container_2 tr").on("click", function (e)
	$("#datatable-container_2 tbody").on('click', 'tr', function ()
	{
		var order_id = $('td', this).eq(0).text();
		var temp = document.getElementById("new_note").value;
		if (temp)
		{
			temp = temp + "\n";
		}
		document.getElementById("new_note").value = temp + "Bestilling: " + order_id;

	});

	$("#type_id").change(function ()
	{
		var temp = document.getElementById("new_note").value;
		if (temp)
		{
			temp = temp + "\n";
		}
		document.getElementById("new_note").value = temp + "Type: " + $( "#type_id option:selected" ).text();;
	});

	$("#vendor_contract_id").change(function ()
	{
		var vendor_contract_id = $("#vendor_contract_id").val();
		if(!vendor_contract_id || vendor_contract_id == -1)
		{
			return;
		}

		var temp = document.getElementById("new_note").value;
		if (temp)
		{
			temp = temp + "\n";
		}
		document.getElementById("new_note").value = temp + "Kontrakt: " + vendor_contract_id;
	});


});


this.get_other_orders = function (location_code, vendor_id)
{
	var oArgs = {menuaction:'property.uiworkorder.get_other_orders',location_code:location_code,vendor_id:vendor_id};
	var strURL = phpGWLink('index.php', oArgs, true);
	JqueryPortico.updateinlineTableHelper('datatable-container_2', strURL);
};

this.get_other_deviations = function (location_code, vendor_id)
{
	var oArgs = {menuaction:'property.uiexternal_communication.get_other_deviations',location_code:location_code,vendor_id:vendor_id};
	var strURL = phpGWLink('index.php', oArgs, true);
	JqueryPortico.updateinlineTableHelper('datatable-container_3', strURL);
};

