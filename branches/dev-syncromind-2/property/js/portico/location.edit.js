
var link_history = null;
var set_history_data = 0;

$(document).ready(function ()
{
	$('#category_id').change( function()
	{
		oTable0.dataTableSettings[0]['ajax']['data']['cat_id'] = $(this).val();
		oTable0.fnDraw();				
	});

	get_history_data = function ()
	{
		if (set_history_data === 0)
		{
			JqueryPortico.updateinlineTableHelper(oTable1, link_history);
			set_history_data = 1;
		}
	};
});
