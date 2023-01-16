
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


	$("#part_of_town_id").multiselect({
		buttonClass: 'form-select',
		templates: {
		button: '<button type="button" class="multiselect dropdown-toggle" data-bs-toggle="dropdown"><span class="multiselect-selected-text"></span></button>',
		},
//	buttonWidth: 250,
		includeSelectAllOption: true,
		enableFiltering: true,
		enableCaseInsensitiveFiltering: true,
		onChange: function ($option)
		{
			// Check if the filter was used.
			var query = $("#part_of_town_id").find('li.multiselect-filter input').val();

			if (query)
			{
				$("#part_of_town_id").find('li.multiselect-filter input').val('').trigger('keydown');
			}
		},
		onDropdownHidden: function (event)
		{
//			console.log(event);
			$("#form").submit();
		}
	});

	$("#inspector_id").multiselect({
		buttonClass: 'form-select',
		templates: {
		button: '<button type="button" class="multiselect dropdown-toggle" data-bs-toggle="dropdown"><span class="multiselect-selected-text"></span></button>',
		},
//	buttonWidth: 250,
		includeSelectAllOption: true,
		enableFiltering: true,
		enableCaseInsensitiveFiltering: true,
		onChange: function ($option)
		{
			// Check if the filter was used.
			var query = $("#inspector_id").find('li.multiselect-filter input').val();

			if (query)
			{
				$("#inspector_id").find('li.multiselect-filter input').val('').trigger('keydown');
			}
		},
		onDropdownHidden: function (event)
		{
//			console.log(event);
			$("#form").submit();
		}
	});

//	$("#part_of_town_id").change(function ()
//	{
//		$("#form").submit();
//	});

	$(".btn-group").addClass('w-100');
//	$(".multiselect ").addClass('form-control');
//	$(".multiselect").removeClass('btn');
//	$(".multiselect").removeClass('btn-default');

});



