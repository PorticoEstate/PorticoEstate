
function show_order(element)
{
	var tr = element.parentNode.parentNode;

	tr.classList.add("table-success");

	var order_id = $(element).val();
	$("#order_details").show();
	populateTableChkorder(order_id);
}

function order_sum()
{

//	var table = $("#order_container").children('table');

	var table = document.getElementById("order_container").childNodes[0];

	var total_sum = 0;
	var row_sum;
	for (var i = 1, row; row = table.rows[i]; i++)
	{
		for (var j = 5, col; col = row.cells[j]; j++)
		{
			row_sum = col.innerText;
			total_sum = total_sum + parseFloat(row_sum);
		}
	}

	var tableFooter = document.getElementById('tfoot');
	if (tableFooter)
	{
		tableFooter.parentNode.removeChild(tableFooter);
	}

	tableFooter = document.createElement('tfoot');
	tableFooter.id = 'tfoot'
	var tableFooterTr = document.createElement('tr');
	var tableFooterTrTd = document.createElement('th');

	tableFooterTrTd.setAttribute('colspan', 5);
	tableFooterTrTd.innerHTML = "Sum:";
	tableFooterTr.appendChild(tableFooterTrTd);
	var tableFooterTrTd2 = document.createElement('th');
	tableFooterTrTd2.setAttribute('id', 'sum_price_table');
	tableFooterTrTd2.classList.add("text-right");

	tableFooterTrTd2.innerHTML = total_sum.toFixed(2);

	tableFooterTr.appendChild(tableFooterTrTd2);

	tableFooter.appendChild(tableFooterTr);
	table.appendChild(tableFooter);
}

function populateTableChkorder(order_id)
{
	var oArgs = {
		menuaction: 'booking.uiapplication.get_purchase_order',
		id: order_id,
	};
	var url = phpGWLink('index.php', oArgs, true);

	var container = 'order_container';

	var colDefsPurchase_order = [
		{key: 'name', label: lang['article']},
		{
			key: 'unit_price',
			label: lang['cost'],
			attrs: [
				{name: 'class', value: "text-right align-middle"}
			]
		},
		{key: 'quantity', label: lang['quantity'],
		attrs: [
			{name: 'class', value: "text-right align-middle"}
		]},
		{key: 'amount', label: lang['Sum'],
		attrs: [
			{name: 'class', value: "text-right align-middle"}
		]},
		{key: 'tax', label: lang['tax'],
		attrs: [
			{name: 'class', value: "text-right align-middle"}
		]},
		{key: 'sum', label: lang['Sum'],
		attrs: [
			{name: 'class', value: "text-right align-middle"}
		]}

	];

	createTable(container, url, colDefsPurchase_order, 'lines', 'pure-table pure-table-bordered', '', order_sum);
}
