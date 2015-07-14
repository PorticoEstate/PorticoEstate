
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
	});

	function filterData(param, value)
	{
		oTable1.dataTableSettings[1]['ajax']['data'][param] = value;
		oTable1.fnDraw();
	}