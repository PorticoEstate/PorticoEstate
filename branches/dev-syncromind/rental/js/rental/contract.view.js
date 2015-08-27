var link_included_composites = null;
var set_composite_data = 0;

var link_included_parties = null;
var set_parties_data = 0;

var link_included_price_items = null;
var set_price_data = 0;

var set_invoice_data = 0;

function formatterPrice (key, oData) 
{
	var amount = $.number( oData[key], decimalPlaces, decimalSeparator, thousandsSeparator ) + ' ' + currency_suffix;
	return amount;
}
	
$(document).ready(function()
{
	$('#invoice_id').change( function() 
	{
		oTable4.dataTableSettings[4]['ajax']['data']['invoice_id'] = $('#invoice_id').val();
		JqueryPortico.updateinlineTableHelper(oTable4);
	});	
	
	/******************************************************************************/
	
	$('#document_search_option').change( function() 
	{
		filterDataDocument('search_option', $(this).val());
	});

	var previous_document_query = '';
	$('#document_query').on( 'keyup change', function () 
	{
		if ( $.trim($(this).val()) != $.trim(previous_document_query) ) 
		{
			filterDataDocument('search', {'value': $(this).val()});
			previous_document_query = $(this).val();
		}
	});

	$('#document_type_search').change( function() 
	{
		filterDataDocument('document_type', $(this).val());
	});
	
	/******************************************************************************/
	
	get_composite_data = function()
	{
		if (set_composite_data  === 0)
		{
			oTable1.dataTableSettings[1]['oFeatures']['bServerSide'] = true;
			JqueryPortico.updateinlineTableHelper(oTable1, link_included_composites);

			set_composite_data = 1;
		}
	};

	get_parties_data = function()
	{
		if (set_parties_data  === 0)
		{
			oTable2.dataTableSettings[2]['oFeatures']['bServerSide'] = true;
			JqueryPortico.updateinlineTableHelper(oTable2, link_included_parties);

			set_parties_data = 1;
		}
	};
	
	get_price_data = function()
	{
		if (set_price_data  === 0)
		{
			oTable3.dataTableSettings[3]['oFeatures']['bServerSide'] = true;
			JqueryPortico.updateinlineTableHelper(oTable3, link_included_price_items);

			set_price_data = 1;
		}
	};
	
	initial_invoice_data = function()
	{
		if (set_invoice_data  === 0)
		{
			oTable4.dataTableSettings[4]['ajax']['data']['invoice_id'] = $('#invoice_id').val();
			JqueryPortico.updateinlineTableHelper(oTable4);

			set_invoice_data = 1;
		}
	};
});

/******************************************************************************/

function filterDataDocument(param, value)
{
	oTable5.dataTableSettings[5]['ajax']['data'][param] = value;
	oTable5.fnDraw();
}
