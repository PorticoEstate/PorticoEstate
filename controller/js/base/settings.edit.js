$(document).ready(function ()
{
	$("#control_area_id").change(function ()
	{
		var control_area_id = $(this).val();
		var oArgs = {menuaction: 'controller.uicontrol.get_controls_by_control_area'};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		//  	$("#hidden_control_area_id").val( control_area_id );
		//     var control_id_init = $("#hidden_control_id").val();
		var htmlString = "";
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl + "&control_area_id=" + control_area_id,
			success: function (data)
			{
				if (data != null)
				{
					htmlString = "<option>Velg kontroll</option>"
					var obj = JSON.parse(data);

					$.each(obj, function (i)
					{
						var selected = '';
						var title = obj[i].title;
						htmlString += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].title + "</option>";
					});

					$("#control_id").html(htmlString);
				}
				else
				{
					htmlString += "<option>Ingen kontroller</option>"
					$("#control_id").html(htmlString);
//						$("#hidden_control_id").val(-1); //reset
				}
			}
		});

	});


});



