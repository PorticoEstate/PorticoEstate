$(document).ready(function ()
{
	setTimeout(function ()
	{
		var article_cat_id = $('#filter_article_cat_id').val();

		var api = oTable.api();
		if(article_cat_id == 1)
		{
			api.column(1).visible(true);
		}
		else
		{
			api.column(1).visible(false);
		}

	}, 1000);

	$("#filter_article_cat_id").change(function ()
	{
		var api = oTable.api();
		if ($(this).val() == 1) //service
		{
			api.column(1).visible(true);
		}
		else
		{
			api.column(1).visible(false);
		}
	});

});