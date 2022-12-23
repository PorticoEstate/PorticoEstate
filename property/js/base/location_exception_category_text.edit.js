$(document).ready(function ()
{

	$("#category_id").change(function ()
	{
		var category_id = $(this).val() || -1;
		var oArgs = {menuaction: 'property.uigeneric.get_list', type: 'location_exception_category_text'};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		$("#category_text_id").find("option:not(:first)").remove();

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			data: {
				type: 'location_exception_category_text',
				filter: {category_id: category_id},
				selected: $("#category_text_id").val(),
				mapping: {name: 'content'},
			},
			success: function (data)
			{
				var selected;
				if (data != null)
				{
					$.each(data, function (i, d)
					{
						selected = '';
						if (d.selected == 1)
						{
							selected = 'selected="selected"';
						}
						$('#category_text_id').append('<option value="' + d.id + '"' + selected + '>' + d.name + '</option>');
					});
				}
			}
		});
	});

});
