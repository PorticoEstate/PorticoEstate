
	$(document).ready(function () 
	{
		$('#type_id').change( function() 
		{
			filterDataLocations('type_id', $(this).val());
		});

		$('#search_option').change( function() 
		{
			filterDataLocations('search_option', $(this).val());
		});
		
		var valuesLocationSearch = {};
		$('#query').on( 'keyup change', function () 
		{
			if ( $.trim($(this).val()) != $.trim(valuesLocationSearch[i]) ) 
			{
				filterDataLocations('search', {'value': $(this).val()});
				valuesLocationSearch[i] = $(this).val();
			}
		});
		
		
		$('#contracts_search_option').change( function() 
		{
			filterDataContracts('search_option', $(this).val());
		});
		
		var valuesContractSearch = {};
		$('#contracts_query').on( 'keyup change', function () 
		{
			if ( $.trim($(this).val()) != $.trim(valuesContractSearch[i]) ) 
			{
				filterDataContracts('search', {'value': $(this).val()});
				valuesContractSearch[i] = $(this).val();
			}
		});
		
		$('#contract_status').change( function() 
		{
			filterDataContracts('contract_status', $(this).val());
		});
		
		$('#contract_type').change( function() 
		{
			filterDataContracts('contract_type', $(this).val());
		});
	});

	function filterDataLocations(param, value)
	{
		oTable1.dataTableSettings[1]['ajax']['data'][param] = value;
		oTable1.fnDraw();
	}
	
	function filterDataContracts(param, value)
	{
		oTable2.dataTableSettings[2]['ajax']['data'][param] = value;
		oTable2.fnDraw();
	}
	
	function formatterArea (key, oData) 
	{
		var amount = $.number( oData[key], decimalPlaces, decimalSeparator, thousandsSeparator ) + ' ' + area_suffix;
		return amount;
	}
	
	function formatterPrice (key, oData) 
	{
		var amount = $.number( oData[key], decimalPlaces, decimalSeparator, thousandsSeparator ) + ' ' + currency_suffix;
		return amount;
	}