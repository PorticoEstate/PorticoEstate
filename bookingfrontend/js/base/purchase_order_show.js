
/* global template_set, lang, initialSelection */

$(document).ready(function ()
{
	function populateTableChkArticles(selection, resources, application_id, reservation_type, reservation_id)
	{
		const queryString = window.location.search;
		const urlParams = new URLSearchParams(queryString);
		const menuaction = urlParams.get('menuaction');

		var oArgs;
		var url;

		/*
		 * Frontend
		 */
		if(menuaction.search("bookingfrontend") > 0)
		{
			oArgs = {
				menuaction: 'bookingfrontend.uiarticle_mapping.get_articles',
				sort: 'name',
				application_id: application_id,
				reservation_type: reservation_type,
				reservation_id: reservation_id,
				collection: 1
			};
			url = phpGWLink('bookingfrontend/', oArgs, true);

		}//Backend
		else
		{
			oArgs = {
				menuaction: 'booking.uiarticle_mapping.get_articles',
				sort: 'name',
				application_id: application_id,
				reservation_type: reservation_type,
				reservation_id: reservation_id,
				collection: 1
			};
			url = phpGWLink('index.php', oArgs, true);
		}



		for (var r in resources)
		{
			url += '&resources[]=' + resources[r];
		}

		var container = 'articles_container';
		var colDefsRegulations = [
			{
				key: 'name',
				label: lang['article'],
				attrs: [
					{name: 'style', value: "text-align:left;"}
				]
			},
			{
				key: 'lang_unit',
				label: lang['unit'],
				attrs: [
					{name: 'style', value: "text-align:center;"}
				]
			},
			{
				key: 'ex_tax_price',
				label: lang['unit cost'],
				attrs: [
					{name: 'style', value: "text-align:right;"}
				]
			},
			{
				key: 'tax',
				label: lang['tax'],
				attrs: [
					{name: 'style', value: "text-align:right;"}
				]
			},
			{
				key: 'selected_quantity',
				label: lang['quantity'],
				attrs: [
					{name: 'style', value: "text-align:right;"}
				]
			},
			{
				key: 'selected_sum',
				label: lang['Sum'],
				attrs: [
					{name: 'class', value: "selected_sum"},
					{name: 'style', value: "text-align:right;"}
				]
			}

		];

		populateTableArticles(url, container, colDefsRegulations);

	}
	var resources = initialSelection;
	if (resources.length > 0)
	{
		if (typeof (application_id) === 'undefined')
		{
			application_id = '';
		}
		if (typeof (reservation_type) === 'undefined')
		{
			reservation_type = '';
		}
		if (typeof (reservation_id) === 'undefined')
		{
			reservation_id = '';
		}

		populateTableChkArticles([
		], resources, application_id, reservation_type, reservation_id);

	}

});

var post_handle_order_table = function ()
{

	var tr = $('#articles_container').find('tr')[1];

	if (!tr || typeof (tr) === 'undefined')
	{
		return;
	}

	var xTable = tr.parentNode.parentNode;

	set_sum(xTable);
};



function set_sum(xTable)
{
	var tableFooter = document.getElementById('tfoot');
	if (tableFooter)
	{
		tableFooter.parentNode.removeChild(tableFooter);
	}
	var xTableBody = xTable.childNodes[1];
	var selected_sum = xTableBody.getElementsByClassName('selected_sum');

	var temp_total_sum = 0;
	for (var i = 0; i < selected_sum.length; i++)
	{
		if (selected_sum[i].innerHTML)
		{
			temp_total_sum = parseFloat(temp_total_sum) + parseFloat(selected_sum[i].innerHTML);
			selected_sum[i].innerHTML = parseFloat(selected_sum[i].innerHTML).toFixed(2);
		}
	}

	var tableFooter = document.createElement('tfoot');
	tableFooter.id = 'tfoot';
	var tableFooterTr = document.createElement('tr');
	var tableFooterTrTd = document.createElement('td');

	tableFooterTrTd.setAttribute('colspan', 5);
	tableFooterTrTd.innerHTML = "Sum:";
	tableFooterTr.appendChild(tableFooterTrTd);
	var tableFooterTrTd2 = document.createElement('td');
	tableFooterTrTd2.setAttribute('id', 'sum_price_table');
	tableFooterTrTd2.classList.add("text-right");

	tableFooterTrTd2.innerHTML = temp_total_sum.toFixed(2);

	tableFooterTr.appendChild(tableFooterTrTd2);

	tableFooter.appendChild(tableFooterTr);
	xTable.appendChild(tableFooter);

}


function populateTableArticles(url, container, colDefs)
{
	var table_class = '';
	if (template_set === 'bootstrap')
	{
		table_class = 'table table-bordered table-hover table-sm table-responsive';
	}
	else if (template_set === 'bookingfrontend')
	{
		table_class = 'table table-striped';
	}
	else
	{
		table_class = 'pure-table pure-table-bordered';
	}

	createTable(container, url, colDefs, '', table_class, null, post_handle_order_table);
}
