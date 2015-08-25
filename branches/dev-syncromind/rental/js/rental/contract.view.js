var link_not_included_composites = null;
var link_included_composites = null;
var set_composite_data = 0;

var link_not_included_parties = null;
var link_included_parties = null;
var set_parties_data = 0;

var link_not_included_price_items = null;
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

	$('#composite_search_options').change( function() 
	{
		filterDataComposite('search_option', $(this).val());
	});

	var previous_composite_query = '';
	$('#composite_query').on( 'keyup change', function () 
	{
		if ( $.trim($(this).val()) != $.trim(previous_composite_query) ) 
		{
			filterDataComposite('search', {'value': $(this).val()});
			previous_composite_query = $(this).val();
		}
	});

	$('#furnished_status').change( function() 
	{
		filterDataComposite('furnished_status', $(this).val());
	});

	$('#is_active').change( function() 
	{
		filterDataComposite('is_active', $(this).val());
	});
		
	$('#has_contract').change( function() 
	{
		filterDataComposite('has_contract', $(this).val());
	});
	
	/******************************************************************************/
	
	$('#party_search_options').change( function() 
	{
		filterDataParty('search_option', $(this).val());
	});

	var previous_party_query = '';
	$('#party_query').on( 'keyup change', function () 
	{
		if ( $.trim($(this).val()) != $.trim(previous_party_query) ) 
		{
			filterDataParty('search', {'value': $(this).val()});
			previous_party_query = $(this).val();
		}
	});

	$('#party_type').change( function() 
	{
		filterDataParty('party_type', $(this).val());
	});

	$('#active').change( function() 
	{
		filterDataParty('active', $(this).val());
	});	
	
	/******************************************************************************/
	
	$('#invoice_id').change( function() 
	{
		oTable7.dataTableSettings[7]['ajax']['data']['invoice_id'] = $('#invoice_id').val();
		JqueryPortico.updateinlineTableHelper(oTable7);
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
			oTable3.dataTableSettings[3]['oFeatures']['bServerSide'] = true;
			JqueryPortico.updateinlineTableHelper(oTable3, link_included_parties);

			set_parties_data = 1;
		}
	};
	
	get_price_data = function()
	{
		if (set_price_data  === 0)
		{
			oTable5.dataTableSettings[5]['oFeatures']['bServerSide'] = true;
			JqueryPortico.updateinlineTableHelper(oTable5, link_included_price_items);

			set_price_data = 1;
		}
	};
	
	initial_invoice_data = function()
	{
		if (set_invoice_data  === 0)
		{
			oTable7.dataTableSettings[7]['ajax']['data']['invoice_id'] = $('#invoice_id').val();
			JqueryPortico.updateinlineTableHelper(oTable7);

			set_invoice_data = 1;
		}
	};
});

/******************************************************************************/

function filterDataComposite(param, value)
{
	oTable2.dataTableSettings[2]['ajax']['data'][param] = value;
	oTable2.fnDraw();
}
	
function filterDataParty(param, value)
{
	oTable4.dataTableSettings[4]['ajax']['data'][param] = value;
	oTable4.fnDraw();
}

function filterDataDocument(param, value)
{
	oTable8.dataTableSettings[8]['ajax']['data'][param] = value;
	oTable8.fnDraw();
}
