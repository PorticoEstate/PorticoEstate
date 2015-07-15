
	$(document).ready(function () 
	{
		$('#type_id').change( function() 
		{
			filterData('type_id', $(this).val());
		});

		$('#search_option').change( function() 
		{
			filterData('search_option', $(this).val());
		});
		
		var valuesInputFilter = {};
		$('#query').on( 'keyup change', function () 
		{
			if ( $.trim($(this).val()) != $.trim(valuesInputFilter[i]) ) 
			{
				filterData('query', $(this).val());
				valuesInputFilter[i] = $(this).val();
			}
		});
	});

	function filterData(param, value)
	{
		oTable1.dataTableSettings[1]['ajax']['data'][param] = value;
		oTable1.fnDraw();
	}