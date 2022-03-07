
/* global lang, alertify */
var custom_tax_code;

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
		{//0
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
		{//1
			/**
			 * Hidden field for holding article id
			 */
			attrs: [{name: 'style', value: "display:none;"}],
			object: [
				{type: 'input', attrs: [
						{name: 'type', value: 'hidden'}
					]
				}
			], value: 'id'},
		{//2
			key: 'name',
			label: lang['article'],
			attrs: [{name: 'class', value: "align-middle"}],
		},
		{//3
			key: 'unit',
			label: lang['unit'],
			attrs: [{name: 'class', value: "unit align-middle"}],
		},
		{//4
			key: 'tax_code',
			label: lang['tax code'],
			attrs: [{name: 'class', value: "text-right align-middle"}],
		},
		{//5
			key: 'tax_percent',
			label: lang['percent'],
			attrs: [{name: 'class', value: "text-right align-middle"}],
		},
		{//6
			key: 'ex_tax_price',
			label: lang['unit cost'],
			attrs: [
				{name: 'class', value: "text-right align-middle"}
			]
		},
		{//7
			key: 'tax',
			label: lang['tax'],
			attrs: [{name: 'class', value: "text-right align-middle"}],
		},
		{//8
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
		{//9
			key: 'selected_quantity',
			label: lang['Selected'],
			attrs: [
				{name: 'class', value: "selected_quantity text-right align-middle"}
			]
		},
		{//10
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
		{//11
			key: 'selected_sum',
			label: lang['Sum'],
			attrs: [
				{name: 'class', value: "text-right align-middle selected_sum"}
			]
		},
		{//12
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
		{//13
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
		{//14
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

/**
 * Update resource item
 */
$("#articles_container").on("click", ".resource", function (event)
{
	event.preventDefault();

	/**
	 * Wait for the time-set to be removed before re-calculate
	 */
	setTimeout(function ()
	{
		alert('click');
//			post_handle_order_table();
	}, 500);
});


function set_mandatory(xTable)
{
	var xTableBody = xTable.childNodes[1];
	var mandatory = xTableBody.getElementsByClassName('mandatory');
	var tr;
	var unit;
	var quantity;
	var selected_quantity;
	var DateHelper = new DateFormatter();
	var _format = date_format + ' H:i';
	var from;
	var to;
	var timespan;
	var sum_hours = 0;
	var sum_days = 0;

	var datetime = $("#dates-container").find(".datetime");

	for (var j = 0; j < datetime.length; )
	{
		if (!datetime[j + 1].value)
		{
			j++;
			j++;
			continue;
		}

		from = DateHelper.parseDate(datetime[j].value, _format);
		to = DateHelper.parseDate(datetime[j + 1].value, _format);
		var timespan = Math.abs(to - from) / 36e5;

		sum_hours += Math.ceil(timespan);
		sum_days += Math.ceil(sum_hours / 24);

		j++;
		j++;
	}

	//Create and append select list
	var tax_code_select = document.createElement("select");
	tax_code_select.id = "tax_code_select";

	//Create and append the options
	for (var i = 0; i < tax_code_list.length; i++) {
		var option = document.createElement("option");
		option.value = tax_code_list[i].id;
		option.text = tax_code_list[i].name;
		tax_code_select.appendChild(option);
	}

	var tax_code_element;
	var unit_price_element;

	for (var i = 0; i < mandatory.length; i++)
	{
		tr = mandatory[i].parentNode.parentNode;
		tax_code_element = tr.childNodes[4];
		tax_code_element.id = 'tax_code_element_' + i;
		unit_price_element = tr.childNodes[6];

		$('#tax_code_element_' + i).on("click", function (event)
		{
			var tax_code = this;
			alertify.myAlert(tax_code_select)
			.set('onclose', function (closeEvent) {
				$(tax_code).html($('#tax_code_select').val());
				alertify.success('Ok');
		  });


		});
		$(unit_price_element).on("click", function (event)
		{
			alert('unit_price_element');
			event.preventDefault();
		});

		if (mandatory[i].value)
		{
			tr.classList.add("table-success");
			tr.childNodes[0].childNodes[0].setAttribute('style', 'display:none;');
			tr.childNodes[8].childNodes[0].setAttribute('style', 'display:none;');//quantity
			tr.childNodes[12].childNodes[0].setAttribute('style', 'display:none;');

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
	var price = parseFloat(tr.childNodes[6].innerText) + parseFloat(tr.childNodes[7].innerText);

	var parent_mapping_id = tr.getElementsByClassName('parent_mapping_id')[0].value;
	var selected_quantity = parseInt(quantity);
	/**
	 * target is the value for selected_articles[]
	 * <mapping_id>_<quantity>_<parent_mapping_id>
	 */
	var target = tr.childNodes[10].childNodes[0];
	target.value = id + '_' + selected_quantity + '_' + parent_mapping_id;

	var elem = tr.childNodes[9];

	elem.innerText = selected_quantity;

	var sum_cell = tr.childNodes[11]
	sum_cell.innerText = (selected_quantity * parseFloat(price)).toFixed(2);

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
	var quantity = element.parentNode.parentNode.childNodes[8].childNodes[0].value;
	var price = parseFloat(element.parentNode.parentNode.childNodes[6].innerText) + parseFloat(element.parentNode.parentNode.childNodes[7].innerText);

	var parent_mapping_id = tr.getElementsByClassName('parent_mapping_id')[0].value;

	/**
	 * set selected items
	 */
	var temp = element.parentNode.parentNode.childNodes[10].childNodes[0].value;

	var selected_quantity = 0;

	if (temp)
	{
		selected_quantity = parseInt(temp.split("_")[1]);
	}

	selected_quantity = selected_quantity + parseInt(quantity);

	/**
	 * Reset quantity
	 */
	element.parentNode.parentNode.childNodes[8].childNodes[0].value = 1;
	/**
	 * Reset button to disabled
	 */
	//element.parentNode.parentNode.childNodes[0].childNodes[0].setAttribute('disabled', true);
	element.parentNode.parentNode.childNodes[12].childNodes[0].removeAttribute('disabled');

	/**
	 * the value selected_articles[]
	 * <mapping_id>_<quantity>_<parent_mapping_id>
	 */
	var target = element.parentNode.parentNode.childNodes[10].childNodes[0];
	target.value = id + '_' + selected_quantity + '_' + parent_mapping_id;

	var elem = element.parentNode.parentNode.childNodes[9];

// add text
	elem.innerText = selected_quantity;

	var sum_cell = element.parentNode.parentNode.childNodes[11];
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
	for (var i = 0; i < selected_sum.length; i++)
	{
		if (selected_sum[i].innerHTML)
		{
			var cell = $(selected_sum[i]).parents().children()[12];
			$(cell).children()[0].removeAttribute('disabled');

			temp_total_sum = parseFloat(temp_total_sum) + parseFloat(selected_sum[i].innerHTML);
			selected_sum[i].innerHTML = parseFloat(selected_sum[i].innerHTML).toFixed(2);
		}
	}

	var tableFooter = document.createElement('tfoot');
	tableFooter.id = 'tfoot'
	var tableFooterTr = document.createElement('tr');
	var tableFooterTrTd = document.createElement('td');

	tableFooterTrTd.setAttribute('colspan', 9);
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
	element.parentNode.parentNode.childNodes[8].childNodes[0].value = 1;//quantity
	element.parentNode.parentNode.childNodes[9].innerText = '';
	element.parentNode.parentNode.childNodes[10].childNodes[0].value = '';
	element.parentNode.parentNode.childNodes[11].innerText = '';

	/**
	 * Reset button to disabled
	 */
//	element.parentNode.parentNode.childNodes[0].childNodes[0].setAttribute('disabled', true);
	element.parentNode.parentNode.childNodes[12].childNodes[0].setAttribute('disabled', true);

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


/**
 * Dialogs factory
 *
 * @name      {string}   Dialog name.
 * @Factory   {Function} Dialog factory function.
 * @transient {Boolean}  Indicates whether to create a singleton or transient dialog.
 * @base      {String}   The name of an existing dialog to inherit from.
 *
 * alertify.dialog(name, Factory, transient, base)
 *
 */

if (!alertify.myAlert)
{
	//define a new dialog
	alertify.dialog('myAlert', function factory()
	{
		return{
			main: function (tax_code_element)
			{
				this.setContent(tax_code_element);
			},
			setup: function ()
			{
				return {
					buttons: [

						/*button defintion*/
						{
							/* button label */
							text: 'OK',

							/*bind a keyboard key to the button: Enter */
							key: 13,

							/* indicate if closing the dialog should trigger this button action */
							invokeOnClose: false,

							/* custom button class name  */
							className: alertify.defaults.theme.ok,

							/* custom button attributes  */
							attrs: {attribute: 'value'},

							/* Defines the button scope, either primary (default) or auxiliary */
							scope: 'primary',

							/* The will conatin the button DOMElement once buttons are created */
							element: undefined
						}

					],
					focus: {element: 0},
					options:{
						onclose: function()
						{
//							console.log($('#tax_code_select'));

//							custom_tax_code = $('#tax_code_select').val();
						}
					}
				};
			},
			prepare: function ()
			{
		//		this.setContent(this.message);
			},
			hooks:{
				// triggered when the dialog is shown, this is seperate from user defined onshow
				onshow: function(){
				},
				// triggered when the dialog is closed, this is seperate from user defined onclose
				onclose: function(){
//console.log($(this).find('select'));
				},
				// triggered when a dialog option gets updated.
				// IMPORTANT: This will not be triggered for dialog custom settings updates ( use settingUpdated instead).
				onupdate: function(){
				}
			 }
		}
	});
}

