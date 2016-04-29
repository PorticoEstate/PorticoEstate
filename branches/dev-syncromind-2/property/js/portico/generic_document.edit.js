
$(document).ready(function ()
{
	$('#location_id').change(function ()
	{
		filterRelations('location_id', $(this).val());
	});
	
	filterRelations('location_id', $('#location_id').val());
});

function filterRelations(param, value)
{
	oTable0.dataTableSettings[0]['ajax']['data'][param] = value;
	oTable0.fnDraw();
}