
$(document).ready(function ()
{

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

function populateTableChkArticles(selection, resources, application_id, reservation_type, reservation_id)
{

	var oArgs = {
		menuaction: 'bookingfrontend.uiarticle_mapping.get_articles',
		sort: 'name',
		application_id: application_id,
		reservation_type: reservation_type,
		reservation_id: reservation_id
	};
	var url = phpGWLink('bookingfrontend/', oArgs, true);

	for (var r in resources)
	{
		url += '&resources[]=' + resources[r];
	}

	var container = 'articles_container';
	var colDefsRegulations = [
		{
			label: lang['Select'],
			attrs: [{name: 'class', value: "align-middle"}],
			object: [
				{
					type: 'button',
					attrs: [
						{name: 'type', value: 'button'},
						//		{name: 'disabled', value: true},
						{name: 'class', value: 'btn btn-success'},
						{name: 'onClick', value: 'add_to_bastet(this);'},
						{name: 'innerHTML', value: 'Legg til <i class="fas fa-shopping-basket"></i>'},
					]
				}
			]
		},
		{
			/**
			 * Hidden field for holding article id
			 */
			attrs: [{name: 'style', value: "display:none;"}],
			object: [
				{type: 'input', attrs: [
						{name: 'type', value: 'hidden'}
					]
				}
			],
			value: 'id'
		},
		{
			key: 'name',
			label: lang['article'],
			attrs: [{name: 'class', value: "align-middle"}],
		},
		{
			key: 'unit',
			label: lang['unit'],
			attrs: [{name: 'class', value: "unit align-middle"}],
		},
		{
			key: 'price',
			label: lang['unit cost'],
			attrs: [{name: 'class', value: "text-right align-middle"}],
		},
		{
			key: 'quantity',
			label: lang['quantity'],
			attrs: [{name: 'class', value: "align-middle"}],
			object: [
				{type: 'input', attrs: [
						{name: 'type', value: 'number'},
						{name: 'min', value: 1},
						{name: 'value', value: 1},
						{name: 'size', value: 3},
						{name: 'class', value: 'quantity form-control'},
					]
				}
			]},
		{
			key: 'selected_quantity',
			label: lang['Selected'],
			attrs: [{name: 'class', value: "selected_quantity text-right align-middle"}]
		},
		{
			label: 'hidden',
			attrs: [{name: 'style', value: "display:none;"}],
			object: [
				{type: 'input', attrs: [
						{name: 'type', value: 'text'},
						{name: 'name', value: 'selected_articles[]'}
					]
				}
			], value: 'selected_article_quantity'
		},
		{
			key: 'selected_sum',
			label: lang['Sum'],
			attrs: [
				{name: 'class', value: "text-right align-middle selected_sum"}
			]
		},
		{
			label: lang['Delete'],
			attrs: [{name: 'class', value: "align-middle"}],
			object: [
				{
					type: 'button',
					attrs: [
						{name: 'type', value: 'button'},
						{name: 'disabled', value: true},
						{name: 'class', value: 'btn btn-danger'},
						{name: 'onClick', value: 'empty_from_bastet(this);'},
						{name: 'innerHTML', value: 'Slett <i class="far fa-trash-alt"></i>'},
					]
				}
			]
		},
		{
			/**
			 * Hidden field for holding information on mandatory items
			 */
			attrs: [{name: 'style', value: "display:none;"}],
			object: [
				{type: 'input', attrs: [
						{name: 'type', value: 'hidden'},
						{name: 'class', value: "mandatory"}
					]
				}
			],
			value: 'mandatory'
		},
		{
			/**
			 * Hidden field for holding information on parent_mapping_id
			 */
			attrs: [{name: 'style', value: "display:none;"}],
			object: [
				{type: 'input', attrs: [
						{name: 'type', value: 'hidden'},
						{name: 'class', value: "parent_mapping_id"}
					]
				}
			],
			value: 'parent_mapping_id'
		}
	];

	populateTableArticles(url, container, colDefsRegulations);

}

var post_handle_order_table = function ()
{

	var tr = $('#articles_container').find('tr')[1];

	if (!tr || typeof (tr) == 'undefined')
	{
		return;
	}

	var xTable = tr.parentNode.parentNode;

	set_mandatory(xTable);
	set_sum(xTable);
};


function set_mandatory(xTable)
{
	var xTableBody = xTable.childNodes[1];
	var mandatory = xTableBody.getElementsByClassName('mandatory');
	var tr;
	var unit;
	var computed_quantity;
	var quantity;
	var selected_quantity;
	var DateHelper = new DateFormatter();
	var date;
	var _format = date_format + ' H:i';
	var from;
	var to;
	var timespan;
	var sum_hours = 0;
	var sum_days = 0;

//	var datetime = $("#dates-container").find(".datetime");
	var datetime = document.getElementsByClassName('datetime');

	for (var j = 0; j < datetime.length; )
	{
		if(!datetime[j + 1].value)
		{
			j++;
			j++;
			continue;
		}

		from = DateHelper.parseDate(datetime[j].value, _format);
		to = DateHelper.parseDate(datetime[j + 1].value, _format);
		var timespan = Math.abs(to - from) / 36e5;

		sum_hours += Math.ceil(timespan);
		sum_days += Math.floor(sum_hours/24);

		j++;
		j++;
	}

	console.log(sum_hours);

	for (var i = 0; i < mandatory.length; i++)
	{
		if (mandatory[i].value)
		{
			tr = mandatory[i].parentNode.parentNode;
			tr.classList.add("table-success");
			tr.childNodes[0].childNodes[0].setAttribute('style', 'display:none;');
			tr.childNodes[5].childNodes[0].setAttribute('style', 'display:none;');
			tr.childNodes[9].childNodes[0].setAttribute('style', 'display:none;');

			unit = tr.getElementsByClassName("unit")[0];

			if (unit.innerHTML == 'hour')
			{
				quantity = tr.getElementsByClassName("quantity")[0];
				selected_quantity = tr.getElementsByClassName("selected_quantity")[0];

				if (parseInt(selected_quantity.innerHTML) !== sum_hours)
				{
					tr.classList.remove("table-success");
					tr.classList.add("table-danger");
					selected_quantity.innerHTML = sum_hours;
					set_basket(tr, sum_hours);
				}
			}
			if (unit.innerHTML == 'day')
			{
				quantity = tr.getElementsByClassName("quantity")[0];
				selected_quantity = tr.getElementsByClassName("selected_quantity")[0];

				if (parseInt(selected_quantity.innerHTML) !== sum_days)
				{
					tr.classList.remove("table-success");
					tr.classList.add("table-danger");
					selected_quantity.innerHTML = sum_days;
					set_basket(tr, sum_days);
				}
			}
		}
	}
}


function set_basket(tr, quantity)
{
	var id = tr.childNodes[1].childNodes[0].value;
	var price = tr.childNodes[4].innerText;
	var parent_mapping_id = tr.getElementsByClassName('parent_mapping_id')[0].value;
	var selected_quantity = parseInt(quantity);
	/**
	 * target is the value for selected_articles[]
	 * <mapping_id>_<quantity>_<parent_mapping_id>
	 */
	var target = tr.childNodes[7].childNodes[0];
	target.value = id + '_' + selected_quantity + '_' + parent_mapping_id;

	var elem = tr.childNodes[6];

	elem.innerText = selected_quantity;

	var sum_cell = tr.childNodes[8]
	sum_cell.innerText = (selected_quantity * parseFloat(price)).toFixed(2);

	if(quantity !== 0)
	{
		tr.classList.remove("table-danger");
		tr.classList.add("table-success");
	}

	var xTable = tr.parentNode.parentNode;

	set_sum(xTable);
}

function add_to_bastet(element)
{
	var tr = element.parentNode.parentNode;
	if (tr.rowIndex == 1)
	{
		return;
	}

	tr.classList.add("table-success");

	var id = element.parentNode.parentNode.childNodes[1].childNodes[0].value;
	var quantity = element.parentNode.parentNode.childNodes[5].childNodes[0].value;
	var price = element.parentNode.parentNode.childNodes[4].innerText;
	var parent_mapping_id = tr.getElementsByClassName('parent_mapping_id')[0].value;

	/**
	 * set selected items
	 */
	var temp = element.parentNode.parentNode.childNodes[7].childNodes[0].value;

	var selected_quantity = 0;

	if (temp)
	{
		selected_quantity = parseInt(temp.split("_")[1]);
	}

	selected_quantity = selected_quantity + parseInt(quantity);

	/**
	 * Reset quantity
	 */
	element.parentNode.parentNode.childNodes[5].childNodes[0].value = 1;
	/**
	 * Reset button to disabled
	 */
	//element.parentNode.parentNode.childNodes[0].childNodes[0].setAttribute('disabled', true);
	element.parentNode.parentNode.childNodes[9].childNodes[0].removeAttribute('disabled');

	/**
	 * the value selected_articles[]
	 * <mapping_id>_<quantity>_<parent_mapping_id>
	 */
	var target = element.parentNode.parentNode.childNodes[7].childNodes[0];
	target.value = id + '_' + selected_quantity + '_' + parent_mapping_id;

	var elem = element.parentNode.parentNode.childNodes[6];

// add text
	elem.innerText = selected_quantity;

	var sum_cell = element.parentNode.parentNode.childNodes[8]
	sum_cell.innerText = (selected_quantity * parseFloat(price)).toFixed(2);

	var xTable = element.parentNode.parentNode.parentNode.parentNode;

	set_sum(xTable);
}

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
	var partial_sum = 0;
	for (var i = 0; i < selected_sum.length; i++)
	{
		if (selected_sum[i].innerHTML)
		{
			partial_sum = selected_sum[i].innerHTML.replaceAll(' ', '');
			var cell = $(selected_sum[i]).parents().children()[9];
			$(cell).children()[0].removeAttribute('disabled');

			temp_total_sum = parseFloat(temp_total_sum) + parseFloat(partial_sum);
			selected_sum[i].innerHTML = parseFloat(partial_sum).toFixed(2);
		}
	}

	var tableFooter = document.createElement('tfoot');
	tableFooter.id = 'tfoot'
	var tableFooterTr = document.createElement('tr');
	var tableFooterTrTd = document.createElement('td');

	tableFooterTrTd.setAttribute('colspan', 6);
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

function empty_from_bastet(element)
{
	var tr = element.parentNode.parentNode;
	tr.classList.remove("table-success");

	/**
	 * Reset quantity
	 */
	element.parentNode.parentNode.childNodes[6].innerText = '';
	element.parentNode.parentNode.childNodes[5].childNodes[0].value = 1;
	element.parentNode.parentNode.childNodes[8].innerText = '';
	element.parentNode.parentNode.childNodes[7].childNodes[0].value = '';

	/**
	 * Reset button to disabled
	 */
//	element.parentNode.parentNode.childNodes[0].childNodes[0].setAttribute('disabled', true);
	element.parentNode.parentNode.childNodes[9].childNodes[0].setAttribute('disabled', true);

	var xTableBody = element.parentNode.parentNode.parentNode;
	var selected_sum = xTableBody.getElementsByClassName('selected_sum');

	var temp_total_sum = 0;
	for (var i = 0; i < selected_sum.length; i++)
	{
		if (selected_sum[i].innerHTML)
		{
			temp_total_sum = parseFloat(temp_total_sum) + parseFloat(selected_sum[i].innerHTML);
		}
	}

	$('#sum_price_table').html(temp_total_sum.toFixed(2));

}


function populateTableArticles(url, container, colDefs)
{
	createTable(container, url, colDefs, '', 'table table-bordered table-hover table-sm table-responsive', null, post_handle_order_table);
}
