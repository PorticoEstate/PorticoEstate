/**
 * Detect if browsertab is active - and update when revisit
 */
var vis = (function ()
{
	var stateKey, eventKey, keys = {
		hidden: "visibilitychange",
		webkitHidden: "webkitvisibilitychange",
		mozHidden: "mozvisibilitychange",
		msHidden: "msvisibilitychange"
	};
	for (stateKey in keys)
	{
		if (stateKey in document)
		{
			eventKey = keys[stateKey];
			break;
		}
	}
	return function (c)
	{
		if (c)

			document.addEventListener(eventKey, c);
		return !document[stateKey];
	}
})();

vis(function ()
{
	if (vis())
	{
		update_table();
	}
});



$(document).ready(function ()
{
	update_table();

	$("#location_name").focusout(function ()
	{
		if($("#location_code").val() && $(this).val() == false)
		{
			$("#location_code").val('');
			update_table('');
		}
	});

	$("#location_name").on("autocompleteselect", function (event, ui)
	{
		var location_code = ui.item.value;
//		alert($("#location_code").val());
//		$("#location_code").val(location_code);
		update_table(location_code);
//		if (location_code != location_code_selection)
//		{
//			building_id_selection = building_id;
//		}
	});
});

deselect_component = function ()
{
	$("[name='filter_component']").val('');
	update_table();
};
update_table = function (location_code)
{
	if(typeof(location_code) == 'undefined')
	{
		location_code = $("#location_code").val();
	}

	$("#receipt").html('');

	var report_type = $("#report_type").val();
	var user_id = $("#user_id").val();
	var custom_frontend = $("[name='custom_frontend']").val();
	var hide_total_hours = false;

	if (custom_frontend == 1)
	{
		$("#user_id").parent().hide();
		$("[for='user_id']").parent().hide();
	}
//console.log(user_id);
	if (user_id < 0 || custom_frontend == 1)
	{
		$("#entity_group_id").parent().hide();
		$("[for='entity_group_id']").parent().hide();
		$("#location_id").parent().hide();
		$("[for='location_id']").parent().hide();
		$("[name='all_items']").parent().hide();
		$("[for='all_items']").parent().hide();
		$("#org_unit_id").parent().hide();
		$("[for='org_unit_id']").parent().hide();
		$("[name='total_hours']").parent().hide();
		$("[for='total_hours']").parent().hide();
		hide_total_hours = true;
	}
	else
	{
		$("#entity_group_id").parent().show();
		$("[for='entity_group_id']").parent().show();
		$("#location_id").parent().show();
		$("[for='location_id']").parent().show();
		$("[name='all_items']").parent().show();
		$("[for='all_items']").parent().show();
		$("#org_unit_id").parent().show();
		$("[for='org_unit_id']").parent().show();
		$("[name='total_hours']").parent().show();
		$("[for='total_hours']").parent().show();
	}

	if (report_type != 'summary' && hide_total_hours == false)
	{
		$("[name='total_hours']").parent().show();
		$("[for='total_hours']").parent().show();
	}

	if (user_id == '')
	{
		$("[name='total_hours']").parent().hide();
		$("[for='total_hours']").parent().hide();
	}

	if (report_type == 'summary')
	{
		$("[name='all_items']").parent().hide();
		$("[for='all_items']").parent().hide();
		$("[name='status']").parent().hide();
		$("[for='status']").parent().hide();
	}

	var requestUrl = $("#queryForm").attr("action");
	requestUrl += '&phpgw_return_as=json' + "&" + $("#queryForm").serialize() + "&location_code=" + location_code ;

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: requestUrl,
		success: function (data)
		{
			if (data != null)
			{
				var components_data = data.components;
				var summary_data = data.summary;
				if (data.location_filter)
				{
					var obj = data.location_filter;
					var htmlString = "<option value=''>" + obj.length + " register funnet</option>";
					var entity_group_id = $("#entity_group_id").val();
					var location_id = $("#location_id").val();

					if (entity_group_id)
					{
						var selected = '';
						if (location_id == -1)
						{
							selected = ' selected';
						}
						htmlString += "<option value='-1'" + selected + ">Velg alle</option>";
					}

					$.each(obj, function (i)
					{
						var selected = '';
						if (obj[i].selected == 1)
						{
							selected = ' selected';
						}

						htmlString += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";

					});

					$("#location_id").html(htmlString);

				}


				var filter_options = data.location_filter_options;
				var return_location_id = data.return_location_id;
				var filtered_location_id = $("#filtered_location_id").val();
				var location_id = $("#location_id").val();
				if (filter_options == null)
				{
					$("#extra_row").hide();

				}
				if (filter_options !== null && filtered_location_id != return_location_id)
				{
					$("#filtered_location_id").val(location_id);
					$("#extra_row").show();
					$("#extra_filter").html('');
					var $extra_filter = $("#extra_filter");
					$.each(filter_options, function (key, filter)
					{
						var list = filter.list;
						$extra_filter.append($("<select></select>").attr("id", location_id + '_' + filter.name).text(location_id + '_' + filter.name));

						var custom_select = $("#" + location_id + '_' + filter.name);

						$.each(list, function (key, value)
						{
							custom_select.append($("<option></option>").attr("value", value.id).text(value.name));
						});

						$(document).on("change", "#" + location_id + '_' + filter.name, function ()
						{

							var custom_input = document.getElementById("custom_" + location_id + '_' + filter.name);

							if (custom_input != null)
							{
								$("#custom_" + location_id + '_' + filter.name).val($(this).val());
							}
							else
							{
								$('<input />').attr('type', 'hidden')
									.attr('id', 'custom_' + location_id + '_' + filter.name)
									.attr('name', 'custom_' + location_id + '_' + filter.name)
									.attr('value', $(this).val())
									.appendTo('#queryForm');
							}
							update_table();

						});

					});
				}

				if (components_data !== null)
				{
					$("#tbody").html(components_data.tbody);
					var time_sum = components_data.time_sum;
					var time_sum_actual = components_data.time_sum_actual;

					console.log(show_months);

//					if(show_months.length > 0)
//					{
//						for (i = 0; i < 13; i++)
//						{
//							$("#month" + i).hide();
//							$("#head" + i).hide();
//						}
//						for (i = 0; i < show_months.length; i++)
//						{
//							$("#month" + show_months[i]).show();
//							$("#head" + show_months[i]).show();
//						}
//						show_months = [];
//					}
//					else
					{
						var filter_months = data.filter_months;
						console.log(filter_months);
						for (i = 0; i < 13; i++)
						{
							$("#month" + i).hide();
							$("#head" + i).hide();
						}
						for (i = 0; i < filter_months.length; i++)
						{
							$("#month" + filter_months[i]).show();
							$("#head" + filter_months[i]).show();
						}
					}


					$("#checkall").html(components_data.checkall);
					$("#total_records").html(components_data.total_records);
					$("#control_text").html('type');
					$("#sum_text").html('Sum');
					$("#monthsum").html(time_sum[0] + '/' + time_sum_actual[0]);
					$("#month1").html(time_sum[1] + '/' + time_sum_actual[1]);
					$("#month2").html(time_sum[2] + '/' + time_sum_actual[2]);
					$("#month3").html(time_sum[3] + '/' + time_sum_actual[3]);
					$("#month4").html(time_sum[4] + '/' + time_sum_actual[4]);
					$("#month5").html(time_sum[5] + '/' + time_sum_actual[5]);
					$("#month6").html(time_sum[6] + '/' + time_sum_actual[6]);
					$("#month7").html(time_sum[7] + '/' + time_sum_actual[7]);
					$("#month8").html(time_sum[8] + '/' + time_sum_actual[8]);
					$("#month9").html(time_sum[9] + '/' + time_sum_actual[9]);
					$("#month10").html(time_sum[10] + '/' + time_sum_actual[10]);
					$("#month11").html(time_sum[11] + '/' + time_sum_actual[11]);
					$("#month12").html(time_sum[12] + '/' + time_sum_actual[12]);
				}


				if (summary_data !== null)
				{
					$("#status_summary").show();
					$("#components").hide();
					$("#status_summary").html(summary_data);
				}
				else
				{
					$("#status_summary").hide();
					$("#components").show();

				}
			}

		}
	});

};

add_from_master = function (myclass)
{
	var myRadio = $('input[name=master_component]');
	var master_component = myRadio.filter(':checked').val();

	if (!master_component)
	{
		alert('velg master');
		return;
	}

	var selected = new Array();

	$("." + myclass).each(function ()
	{
		if ($(this).prop("checked"))
		{
			selected.push($(this).val());
		}
	});

	oArgs = {menuaction: 'controller.uicomponent.add_controll_from_master'};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	$.ajax({
		type: 'POST',
		data: {master_component: master_component, target: selected},
		dataType: 'json',
		url: requestUrl,
		success: function (data)
		{
			if (data != null)
			{
//console.log(data);
				var message = data.message;

				htmlString = "";
				var msg_class = "msg_good";
				if (data.status == 'error')
				{
					msg_class = "error";
				}
				htmlString += "<div class=\"" + msg_class + "\">";
				htmlString += message;
				htmlString += '</div>';
				update_table();
				$("#receipt").html(htmlString);

			}
		}
	});

};

checkAll = function (myclass)
{
	$("." + myclass).each(function ()
	{
		if ($(this).prop("checked"))
		{
			$(this).prop("checked", false);
		}
		else
		{
			$(this).prop("checked", true);
		}
	});
};

var oArgs = {menuaction: 'property.bolocation.get_locations'};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'location_name', 'location_code', 'location_container');



perform_action = function(name, oArgs)
{

	if(name === 'save_check_list')
	{
		//nothing
		location.assign(phpGWLink('index.php', oArgs));
		return;
	}
	else if (name === 'submit_ok')
	{
		oArgs.menuaction = 'controller.uicheck_list.save_check_list';

		var confirm_msg = "Godkjenner du denne uten avvik?";

		if (confirm(confirm_msg) !== true)
		{
			return false;
		}

		var requestUrl = phpGWLink('index.php', oArgs, true);

		$.ajax({
			type: 'POST',
			data: {submit_ok: 1},
			dataType: 'json',
			url: requestUrl,
			success: function (data)
			{
				if (data !== null)
				{
					var message = data.message;

					if (data.status === 'error')
					{
						alert(message);
					}
					else
					{
						alert('Ok');
						update_table();
					}
				}
			}
		});
	}
	else if (name === 'submit_deviation')
	{
		oArgs.menuaction = 'controller.uicheck_list.save_check_list';
		oArgs.submit_deviation = 1;
		location.assign(phpGWLink('index.php', oArgs));
	}

};
