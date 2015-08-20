var link_not_included_composites = null;
var link_included_composites = null;
var set_composite_data = 0;

var link_not_included_parties = null;
var link_included_parties = null;
var set_parties_data = 0;

var link_not_included_price_items = null;
var link_included_price_items = null;
var set_price_data = 0;

$(document).ready(function()
{
	$("#date_start").change(function(){

		var date_start = $("#date_start").val();
		var billing_start = $("#billing_start_date").val();
		if(!billing_start)
		{
			$("#billing_start_date").val(date_start);
		}
	});

	$("#date_end").change(function(){

		var date_end = $("#date_end").val();
		var billing_end_date = $("#billing_end_date").val();
		if(!billing_end_date)
		{
			$("#billing_end_date").val(date_end);
		}
	});

	/******************************************************************************/
	
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
	
	get_composite_data = function()
	{
		if (set_composite_data  === 0)
		{
			oTable1.dataTableSettings[1]['oFeatures']['bServerSide'] = true;
			JqueryPortico.updateinlineTableHelper(oTable1, link_included_composites);
			
			oTable2.dataTableSettings[2]['oFeatures']['bServerSide'] = true;
			oTable2.dataTableSettings[2]['ajax'] = {url: link_not_included_composites, data: {}, type: 'GET'};
			JqueryPortico.updateinlineTableHelper(oTable2);

			set_composite_data = 1;
		}
	};

	get_parties_data = function()
	{
		if (set_parties_data  === 0)
		{
			oTable3.dataTableSettings[3]['oFeatures']['bServerSide'] = true;
			JqueryPortico.updateinlineTableHelper(oTable3, link_included_parties);
			
			oTable4.dataTableSettings[4]['oFeatures']['bServerSide'] = true;
			oTable4.dataTableSettings[4]['ajax'] = {url: link_not_included_parties, data: {}, type: 'GET'};
			JqueryPortico.updateinlineTableHelper(oTable4);

			set_parties_data = 1;
		}
	};
	
	get_price_data = function()
	{
		if (set_price_data  === 0)
		{
			oTable5.dataTableSettings[5]['oFeatures']['bServerSide'] = true;
			JqueryPortico.updateinlineTableHelper(oTable5, link_included_price_items);
			
			oTable6.dataTableSettings[6]['oFeatures']['bServerSide'] = true;
			oTable6.dataTableSettings[6]['ajax'] = {url: link_not_included_price_items, data: {}, type: 'GET'};
			JqueryPortico.updateinlineTableHelper(oTable6);

			set_price_data = 1;
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

/******************************************************************************/

addComposite = function(oArgs, parameters){
    
	var oTT = TableTools.fnGetInstance( 'datatable-container_2' );
	var selected = oTT.fnGetSelectedData();
	var nTable = 1;

	if (selected.length == 0){
		alert('None selected');
		return false;
	}

	var data = {};

	$.each(parameters.parameter, function( i, val ) {
		data[val.name] = {};
	});																	

	var n = 0;
	for ( var n = 0; n < selected.length; ++n )
	{
		$.each(parameters.parameter, function( i, val ) {
			data[val.name][n] = selected[n][val.source];
		});		
	}

	var requestUrl = phpGWLink('index.php', oArgs);

	JqueryPortico.execute_ajax(requestUrl, function(result){

		JqueryPortico.show_message(nTable, result);
		
		oTable1.fnDraw();
		oTable2.fnDraw();

	}, data, 'POST', 'JSON');
};

removeComposite = function(oArgs, parameters){
    
	var oTT = TableTools.fnGetInstance( 'datatable-container_1' );
	var selected = oTT.fnGetSelectedData();
	var nTable = 1;

	if (selected.length == 0){
		alert('None selected');
		return false;
	}

	var data = {};

	$.each(parameters.parameter, function( i, val ) {
		data[val.name] = {};
	});																	

	var n = 0;
	for ( var n = 0; n < selected.length; ++n )
	{
		$.each(parameters.parameter, function( i, val ) {
			data[val.name][n] = selected[n][val.source];
		});		
	}

	var requestUrl = phpGWLink('index.php', oArgs);

	JqueryPortico.execute_ajax(requestUrl, function(result){

		JqueryPortico.show_message(nTable, result);
		
		oTable1.fnDraw();
		oTable2.fnDraw();

	}, data, 'POST', 'JSON');
};


addParty = function(oArgs, parameters){
    
	var oTT = TableTools.fnGetInstance( 'datatable-container_4' );
	var selected = oTT.fnGetSelectedData();
	var nTable = 3;

	if (selected.length == 0){
		alert('None selected');
		return false;
	}

	var data = {};

	$.each(parameters.parameter, function( i, val ) {
		data[val.name] = {};
	});																	

	var n = 0;
	for ( var n = 0; n < selected.length; ++n )
	{
		$.each(parameters.parameter, function( i, val ) {
			data[val.name][n] = selected[n][val.source];
		});		
	}

	var requestUrl = phpGWLink('index.php', oArgs);

	JqueryPortico.execute_ajax(requestUrl, function(result){

		JqueryPortico.show_message(nTable, result);
		
		oTable3.fnDraw();
		oTable4.fnDraw();

	}, data, 'POST', 'JSON');
};

removeParty = function(oArgs, parameters){
    
	var oTT = TableTools.fnGetInstance( 'datatable-container_3' );
	var selected = oTT.fnGetSelectedData();
	var nTable = 3;

	if (selected.length == 0){
		alert('None selected');
		return false;
	}

	var data = {};

	$.each(parameters.parameter, function( i, val ) {
		data[val.name] = {};
	});																	

	var n = 0;
	for ( var n = 0; n < selected.length; ++n )
	{
		$.each(parameters.parameter, function( i, val ) {
			data[val.name][n] = selected[n][val.source];
		});		
	}

	var requestUrl = phpGWLink('index.php', oArgs);

	JqueryPortico.execute_ajax(requestUrl, function(result){

		JqueryPortico.show_message(nTable, result);
		
		oTable3.fnDraw();
		oTable4.fnDraw();

	}, data, 'POST', 'JSON');
};


addPrice = function(oArgs, parameters){
    
	var oTT = TableTools.fnGetInstance( 'datatable-container_6' );
	var selected = oTT.fnGetSelectedData();
	var nTable = 5;

	if (selected.length == 0){
		alert('None selected');
		return false;
	}

	var data = {};

	$.each(parameters.parameter, function( i, val ) {
		data[val.name] = {};
	});																	

	var n = 0;
	for ( var n = 0; n < selected.length; ++n )
	{
		$.each(parameters.parameter, function( i, val ) {
			data[val.name][n] = selected[n][val.source];
		});		
	}

	var requestUrl = phpGWLink('index.php', oArgs);

	JqueryPortico.execute_ajax(requestUrl, function(result){

		JqueryPortico.show_message(nTable, result);
		
		oTable5.fnDraw();
		oTable6.fnDraw();
		oTable0.fnDraw();

	}, data, 'POST', 'JSON');
};

removePrice = function(oArgs, parameters){
    
	var oTT = TableTools.fnGetInstance( 'datatable-container_5' );
	var selected = oTT.fnGetSelectedData();
	var nTable = 5;

	if (selected.length == 0){
		alert('None selected');
		return false;
	}

	var data = {};

	$.each(parameters.parameter, function( i, val ) {
		data[val.name] = {};
	});																	

	var n = 0;
	for ( var n = 0; n < selected.length; ++n )
	{
		$.each(parameters.parameter, function( i, val ) {
			data[val.name][n] = selected[n][val.source];
		});		
	}

	var requestUrl = phpGWLink('index.php', oArgs);

	JqueryPortico.execute_ajax(requestUrl, function(result){

		JqueryPortico.show_message(nTable, result);
		
		oTable5.fnDraw();
		oTable0.fnDraw();

	}, data, 'POST', 'JSON');
};
