$(document).ready(function ()
{

	/*
	 Float Submit Button To Right Edge Of Window
	 Version 1.0
	 April 11, 2010

	 Will Bontrager
	 http://www.willmaster.com/
	 Copyright 2010 Bontrager Connection, LLC

	 Generated with customizations on February 23, 2016 at
	 http://www.willmaster.com/library/manage-forms/floating-submit-button.php

	 Bontrager Connection, LLC grants you
	 a royalty free license to use or modify
	 this software provided this notice appears
	 on all copies. This software is provided
	 "AS IS," without a warranty of any kind.
	 */

//*****************************//

	/** Five places to customize **/

// Place 1:
// The id value of the button.

	var ButtonId = "submitform";


// Place 2:
// The width of the button.

	var ButtonWidth = 60;


// Place 3:
// Left/Right location of button (specify "left" or "right").

	var ButtonLocation = "right";


// Place 4:
// How much space (in pixels) between button and window left/right edge.

	var SpaceBetweenButtonAndEdge = 30;


// Place 5:
// How much space (in pixels) between button and window top edge.

	var SpaceBetweenButtonAndTop = 100;


	/** No other customization required. **/

//************************************//

	TotalWidth = parseInt(ButtonWidth) + parseInt(SpaceBetweenButtonAndEdge);
	ButtonLocation = ButtonLocation.toLowerCase();
	ButtonLocation = ButtonLocation.substr(0, 1);
	var ButtonOnLeftEdge = (ButtonLocation == 'l') ? true : false;

	function AddButtonPlacementEvents(f)
	{
		var cache = window.onload;
		if (typeof window.onload != 'function')
		{
			window.onload = f;
		}
		else
		{
			window.onload = function ()
			{
				if (cache)
				{
					cache();
				}
				f();
			};
		}
		cache = window.onresize;
		if (typeof window.onresize != 'function')
		{
			window.onresize = f;
		}
		else
		{
			window.onresize = function ()
			{
				if (cache)
				{
					cache();
				}
				f();
			};
		}
	}

	function WindowHasScrollbar()
	{
		var ht = 0;
		if (document.all)
		{
			if (document.documentElement)
			{
				ht = document.documentElement.clientHeight;
			}
			else
			{
				ht = document.body.clientHeight;
			}
		}
		else
		{
			ht = window.innerHeight;
		}
		if (document.body.offsetHeight > ht)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function GlueButton(ledge)
	{
		var test = document.getElementById('add_sub_entry');

		var left_submit = 400;
		var left_cancel = 800;
		if(test != null )
		{
			$("#add_sub_entry").css({
				top: SpaceBetweenButtonAndTop + "px",
				left: (ledge - 80) + "px",
				display : "block",
				zIndex : "9999",
				position:'fixed'
			});

			ledge -= 140;
		}

		$("#submitform").css({
			top: SpaceBetweenButtonAndTop + "px",
			width : ButtonWidth + "px",
			left: (ledge - 100 )+ "px",
			display : "block",
			zIndex : "9999",
			position:'fixed'
		});

		$("#cancelform").css({
			top: SpaceBetweenButtonAndTop + "px",
			width : ButtonWidth + "px",
			left: (ledge -20)+ "px",
			display : "block",
			zIndex : "9999",
			position:'fixed'
		});


	}

	function PlaceTheButton()
	{
		if (ButtonOnLeftEdge)
		{
			GlueButton(SpaceBetweenButtonAndEdge);
			return;
		}
		if (document.documentElement && document.documentElement.clientWidth)
		{
			GlueButton(document.documentElement.clientWidth - TotalWidth);
		}
		else
		{
			if (navigator.userAgent.indexOf('MSIE') > 0)
			{
				GlueButton(document.body.clientWidth - TotalWidth + 19);
			}
			else
			{
				var scroll = WindowHasScrollbar() ? 0 : 15;
				if (typeof window.innerWidth == 'number')
				{
					GlueButton(window.innerWidth - TotalWidth - 15 + scroll);
				}
				else
				{
					GlueButton(document.body.clientWidth - TotalWidth + 15);
				}
			}
		}
	}

	AddButtonPlacementEvents(PlaceTheButton);
});
var sUrl_workorder = phpGWLink('index.php', {'menuaction': 'property.uiworkorder.edit'});
var sUrl_invoice = phpGWLink('index.php', {'menuaction': 'property.uiinvoice.index'});

formatLink = function (key, oData)
{
	return "<a href=" + sUrl_workorder + "&id=" + oData[key] + ">" + oData[key] + "</a>";
};

formatLink_voucher = function (key, oData)
{
	var voucher_out_id = oData['voucher_out_id'];
	if (voucher_out_id)
	{
		var voucher_id = voucher_out_id;
	}
	else
	{
		var voucher_id = Math.abs(oData[key]);
	}

	if (oData[key] > 0)
	{
		return "<a href=" + sUrl_invoice + "&query=" + oData[key] + "&voucher_id=" + oData[key] + "&user_lid=all>" + voucher_id + "</a>";
	}
	else
	{
		//oData[key] = -1 * oData[key];
		return "<a href=" + sUrl_invoice + "&voucher_id=" + Math.abs(oData[key]) + "&user_lid=all&paid=true>" + voucher_id + "</a>";
	}
};

//var oArgs_invoicehandler_2 = {menuaction:'property.uiinvoice2.index'};
var sUrl_invoicehandler_2 = phpGWLink('index.php', {menuaction: 'property.uiinvoice2.index'});

formatLink_invoicehandler_2 = function (key, oData)
{
	var voucher_out_id = oData['voucher_out_id'];
	if (voucher_out_id)
	{
		var voucher_id = voucher_out_id;
	}
	else
	{
		var voucher_id = Math.abs(oData[key]);
	}

	if (oData[key] > 0)
	{
		return "<a href=" + sUrl_invoicehandler_2 + "&voucher_id=" + oData[key] + ">" + voucher_id + "</a>";
	}
	else
	{
		//oData[key] = -1 * oData[key];
		return "<a href=" + sUrl_invoice + "&voucher_id=" + Math.abs(oData[key]) + "&user_lid=all&paid=true>" + voucher_id + "</a>";
	}
};

//var oArgs_project = {menuaction:'property.uiproject.edit'};
var sUrl_project = phpGWLink('index.php', {menuaction: 'property.uiproject.edit'});

var project_link = function (key, oData)
{
	if (oData[key] > 0)
	{
		return "<a href=" + sUrl_project + "&id=" + oData[key] + ">" + oData[key] + "</a>";
	}
};

function sum_columns_table_orders()
{
	var api = oTable1.api();
	// Remove the formatting to get integer data for summation
	var intVal = function (i)
	{
		return typeof i === 'string' ?
			i.replace(/[\$,]/g, '') * 1 :
			typeof i === 'number' ?
			i : 0;
	};

	var columns = ["3", "4", "6", "7", "8"];

	columns.forEach(function (col)
	{
		data = api.column(col, {page: 'current'}).data();
		pageTotal = data.length ?
			data.reduce(function (a, b)
			{
				return intVal(a) + intVal(b);
			}) : 0;

		pageTotal = $.number(pageTotal, 0, ',', '.');
		$(api.column(col).footer()).html(pageTotal);
	});
}

function sum_columns_table_invoice()
{
	var api = oTable2.api();
	// Remove the formatting to get integer data for summation
	var intVal = function (i)
	{
		return typeof i === 'string' ?
			i.replace(/[\$,]/g, '') * 1 :
			typeof i === 'number' ?
			i : 0;
	};

	var columns = ["4", "5"];

	columns.forEach(function (col)
	{
		data = api.column(col, {page: 'current'}).data();
		pageTotal = data.length ?
			data.reduce(function (a, b)
			{
				return intVal(a) + intVal(b);
			}) : 0;

		pageTotal = $.number(pageTotal, 2, ',', '.');
		$(api.column(col).footer()).html(pageTotal);
	});
}

$(document).ready(function ()
{

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
					if (data.active != 1)
					{
						alert('Denne kan ikke velges');
					}
				}
			}
		});
	});

	$("#order_time_span").change(function ()
	{
		var oArgs1 = {menuaction: 'property.uiproject.get_orders', project_id: project_id, year: $(this).val()};
		var requestUrl1 = phpGWLink('index.php', oArgs1, true);
		JqueryPortico.updateinlineTableHelper(oTable1, requestUrl1);

		var oArgs2 = {menuaction: 'property.uiproject.get_vouchers', project_id: project_id, year: $(this).val()};
		var requestUrl2 = phpGWLink('index.php', oArgs2, true);
		JqueryPortico.updateinlineTableHelper(oTable2, requestUrl2);
	});

	var api1 = oTable1.api();
	api1.on('draw', sum_columns_table_orders);

	var api2 = oTable2.api();
	api2.on('draw', sum_columns_table_invoice);

});

function check_and_submit_valid_session()
{
	var oArgs = {menuaction: 'property.bocommon.confirm_session'};
	var strURL = phpGWLink('index.php', oArgs, true);

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: strURL,
		success: function (data)
		{
			if (data != null)
			{
				if (data['sessionExpired'] == true)
				{
					window.alert('sessionExpired - please log in');
					JqueryPortico.lightboxlogin();//defined in common.js
				}
				else
				{
					document.getElementsByName("save")[0].value = 1;
					document.form.submit();
				}
			}
		},
		failure: function (o)
		{
			window.alert('failure - try again - once');
		},
		timeout: 5000
	});
}

this.validate_form = function ()
{
	conf = {
		modules: 'location, date, security, file',
		validateOnBlur: false,
		scrollToTopOnError: true,
		errorMessagePosition: 'top',
		language: validateLanguage
	};
	return $('form').isValid(validateLanguage, conf);
}

JqueryPortico.FormatterClosed = function (key, oData)
{
	return "<div align=\"center\">" + oData['closed'] + oData['closed_orig'] + "</div>";
};

JqueryPortico.FormatterActive = function (key, oData)
{
	return "<div align=\"center\">" + oData['active'] + oData['active_orig'] + "</div>";
};

function set_tab(tab)
{
	$("#project_tab").val(tab);
}

$(document).ready(function ()
{
	$('form[name=form]').submit(function (e)
	{
		e.preventDefault();

		if (!validate_form())
		{
			return;
		}
		check_and_submit_valid_session();
	});
});

var oArgs = {menuaction: 'property.uiproject.get_external_project'};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'external_project_name', 'external_project_id', 'external_project_container');
