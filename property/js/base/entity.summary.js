var location_code_selection = "";
var html5QrcodeScanner;

$(document).ready(function ()
{
	JqueryPortico.autocompleteHelper(phpGWLink('index.php', {menuaction: 'property.bolocation.get_locations'}, true),
		'location_name', 'location_code', 'location_container');

});

$(document).ready(function ()
{

	$("#location_name").on("autocompleteselect", function (event, ui)
	{
		var location_code = ui.item.value;

		if (location_code !== location_code_selection)
		{
			location_code_selection = location_code;

		}

	});



	function onScanSuccess(decodedText, decodedResult)
	{
		// Handle on success condition with the decoded text or result.
		console.log(`Scan result: ${decodedText}`, decodedResult);

		document.getElementById("filter_location").value = decodedText;

		// ...
		html5QrcodeScanner.clear();
		// ^ this will stop the scanner (video feed) and clear the scan area.
	}

	function onScanError(errorMessage)
	{
		// handle on error condition, with error message
	}


	const element = document.getElementById("filter_location");
	element.addEventListener("click", function (event)
	{
		if (!this.value)
		{
			init_scanner(this);
		}
	}, {once: false});


	init_scanner = function ()
	{
		html5QrcodeScanner = new Html5QrcodeScanner(
			"reader_location", {fps: 10, qrbox: 250});
		html5QrcodeScanner.render(onScanSuccess, onScanError);

	};

	$("#btn_search").click(function ()
	{
		var entity_id = $("#field_entity_id").val();
		var type = $("#field_type").val();
		var qr_code = $("#filter_location ").val();

		if (!qr_code)
		{
			return;
		}

		var qr_code_infoURL = phpGWLink('index.php', {menuaction: 'property.uientity.get_items_per_qr', entity_id: entity_id, qr_code: qr_code}, true);

		var rqr_code_info = [{n: 'ResultSet'}, {n: 'Result'}];

		var colDefsItems_info = [
			{key: 'id', label: lang['Id'], formatter: genericLink},
			{key: 'register_name', label: lang['Register']},
			{key: 'address', label: lang['Address']},
			{key: 'location_code', label: lang['Location']}
		];

		var paginatorTableqr_code_info = new Array();
		paginatorTableqr_code_info.limit = 10;
		createPaginatorTable('qr_code_info_container', paginatorTableqr_code_info);


		const elements = document.getElementsByClassName('tablePaginator');

		createTable('qr_code_info_container', qr_code_infoURL, colDefsItems_info, rqr_code_info, 'pure-table pure-table-bordered', paginatorTableqr_code_info);
		while (elements.length > 1)
		{
			elements[0].parentNode.removeChild(elements[0]);
		}

	});



	$("#btn_search_").click(function ()
	{
		var entity_id = $("#field_entity_id").val();
		var type = $("#field_type").val();
		var qr_code = $("#filter_location ").val();

		if (!qr_code)
		{
			return;
		}

		alert(qr_code);

		var oArgs = {menuaction: 'property.uientity.get_items_per_qr', entity_id: entity_id};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		$.ajax({
			type: 'POST',
			url: requestUrl,
			data: {qr_code: qr_code},
			success: function (data)
			{
				if (data)
				{
					console.log(data);

				}

			},
			error: function (XMLHttpRequest, textStatus, errorThrown)
			{
				if (XMLHttpRequest.status === 401)
				{
					location.href = '/';
				}
			}
		});

		return false;
	});



});