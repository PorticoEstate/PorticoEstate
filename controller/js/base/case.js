$(document).ready(function ()
{
	$(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('.scrollup').fadeIn();
        } else {
            $('.scrollup').fadeOut();
        }
    });

    $('.scrollup').click(function () {
        $("html, body").animate({
            scrollTop: 0
        }, 600);
        return false;
    });

	$("#choose-child-on-component").change(function ()
	{
		$("#submit_update_component").hide();
		$("#component_picture_file").val('');

		if ($(this).val())
		{
			show_component_information($(this).val());
			show_component_picture();
			$("#new_picture").show();
		}
		else
		{
			$('#equipment_picture_container').html('');
			$("#new_picture").hide();
			$("#form_new_component_2").html('');

		}
	});

	show_component_picture = function ()
	{
		var component = $("#choose-child-on-component").val();
		var oArgs = {menuaction: 'controller.uicase.get_image', component: component};
		var ImageUrl = phpGWLink('index.php', oArgs, true);

		$.ajax({
			cache: false,
			contentType: false,
			processData: false,
			type: 'GET',
			url: ImageUrl + '&dry_run=1',
			success: function (data, textStatus, jqXHR)
			{
				if (data)
				{
					if (data.status == "200")
					{
						$('#equipment_picture_container').html('<img alt="Bilde" id="equipment_picture" src="' + ImageUrl + '" style="width:100%;max-width:300px"/>');
					}
					else
					{
						$('#equipment_picture_container').html(data.message);
					}
				}
			}
		});
	};

	show_case_picture = function (case_id, form)
	{
		var oArgs = {menuaction: 'controller.uicase.get_case_image', case_id: case_id};
		var ImageUrl = phpGWLink('index.php', oArgs, true);

		$.ajax({
			cache: false,
			contentType: false,
			processData: false,
			type: 'GET',
			url: ImageUrl + '&dry_run=1',
			success: function (data, textStatus, jqXHR)
			{
				if (data)
				{
					var picture_container = $(form).find("div[name='picture_container']");

					if (data.status == "200")
					{
						$(picture_container).append('<br><img alt="Bilde" src="' + ImageUrl + '&file_id=' + data.file_id + '" style="width:100%;max-width:300px"/>');
					}
					else
					{
						$(picture_container).append('<br>' + data.message);
					}
				}
			}
		});
	};

	show_picture_submit = function ()
	{
		$("#submit_update_component").show();

	};


	// REGISTER PICTURE TO CHILD COMPONENT
	$("#frm_add_picture").on("submit", function (e)
	{
		e.preventDefault();

		var thisForm = $(this);
		var requestUrl = $(thisForm).attr("action");


		var formdata = false;
		if (window.FormData)
		{
			formdata = new FormData(thisForm[0]);
		}

		$.ajax({
			cache: false,
			contentType: false,
			processData: false,
			type: 'POST',
			url: requestUrl,
			data: formdata ? formdata : thisForm.serialize(),
			success: function (data, textStatus, jqXHR)
			{
				if (data)
				{
					if (data.status == "saved")
					{
						$("#submit_update_component").hide();
						$("#component_picture_file").val('');
						show_component_picture();

					}
					else
					{
						alert(data.message);
					}
				}
			}
		});
	});

	$(".add_picture_to_case_form").on("submit", function (e)
	{
		e.preventDefault();

		var thisForm = $(this);

		var oArgs = {menuaction: 'controller.uicase.add_case_image'};
		var requestUrl = phpGWLink('index.php', oArgs, true);

//		var requestUrl = $(thisForm).attr("action");

//		var case_id = $("form").prev().find("input[name='case_id']").val();

		var case_id = $("#cache_case_id").val();

		var formdata = false;
		if (window.FormData)
		{
			try
			{
				formdata = new FormData(thisForm[0]);
			}
			catch (e)
			{

			}
		}

		$.ajax({
			cache: false,
			contentType: false,
			processData: false,
			type: 'POST',
			url: requestUrl + '&case_id=' + case_id,
			data: formdata ? formdata : thisForm.serialize(),
			success: function (data, textStatus, jqXHR)
			{
				if (data)
				{
					if (data.status == "saved")
					{
						$("#case_picture_file").val('');
						show_case_picture(case_id, thisForm);
					}
					else
					{
						alert(data.message);
					}
				}
			}
		});
	});


	// REGISTER NEW CHILD COMPONENT
	show_component_information = function (component)
	{
		var component_arr = component.split('_');
		var oArgs = {
			menuaction: 'controller.uicase.edit_component_child',
			location_id: component_arr[0],
			component_id: component_arr[1],
			get_info: 1
		};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		$.ajax({
			type: 'POST',
			url: requestUrl,
			success: function (data)
			{
				if (data)
				{
					$("#form_new_component_2").html(data);
				}
			}
		});
	};

	get_edit_form = function ()
	{
		var component = $("#choose-child-on-component").val();

		if (!component)
		{
			alert('komponent ikke valgt');
			return false;
		}

		var parent_location_id = $('input[name=parent_location_id]')[0];
		var parent_component_id = $('input[name=parent_component_id]')[0];

		var component_arr = component.split('_');
		var oArgs = {
			menuaction: 'controller.uicase.edit_component_child',
			location_id: component_arr[0],
			component_id: component_arr[1],
			parent_location_id: $(parent_location_id).val(),
			parent_component_id: $(parent_component_id).val(),
			get_edit_form: 1
		};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		$.ajax({
			type: 'POST',
			url: requestUrl,
			success: function (data)
			{
				if (data)
				{
					$("#form_new_component_2").html(data);
				}
			}
		});
	};


	// REGISTER NEW CHILD COMPONENT
	$(".form_new_component").on("submit", function (e)
	{
		e.preventDefault();

		var thisForm = $(this);
		var requestUrl = $(thisForm).attr("action");
		$.ajax({
			type: 'POST',
			url: requestUrl,
			data: $(thisForm).serialize(),
			success: function (data)
			{
				if (data)
				{
					$("#form_new_component_2").html(data);
				}
			}
		});

	});

	resetForm = function (form)
	{
		clear_form(form);
		$(form).find("input[type='submit']").removeAttr('disabled');
		$(form).find("input[type='submit']").removeClass("case_saved");
		var picture_container = $(form).next('div').find("div[name='picture_container']");
		picture_container.html('');
		var add_picture_to_case_container = $(form).next('.add_picture_to_case');
		$(add_picture_to_case_container).hide();
	};

	// REGISTER CASE
	$(".frm_register_case").on("submit", function (e)
	{
		e.preventDefault();

		var thisForm = $(this);
		var requestUrl = $(thisForm).attr("action");
		var location_code = $("#choose-building-on-property  option:selected").val();
		var component_child = $("#choose-child-on-component  option:selected").val();

		$(thisForm).find("input[name=location_code]").val(location_code);

		var control_group_id = $(thisForm).find("input[name=control_group_id]").val();
		var component = $("#component_at_control_group_" + control_group_id).val();

		if (typeof (component) != 'undefined')
		{
			var component_arr = component.split("_");
			var component_location_id = component_arr[0];
			var component_id = component_arr[1];
			$(thisForm).find("input[name=component_location_id]").val(component_location_id);
			$(thisForm).find("input[name=component_id]").val(component_id);
		}

		var validate_status = validate_form(thisForm);

		if (validate_status)
		{
			$.ajax({
				type: 'POST',
				url: requestUrl + "&component_child=" + component_child,
				data: $(thisForm).serialize(),
				success: function (data)
				{
					if (data)
					{
						var jsonObj = JSON.parse(data);

						if (jsonObj.status == "saved")
						{
							var type = $(thisForm).find("input[name='type']").val();
							var submitBnt = $(thisForm).find("input[type='submit']");
							$(submitBnt).val("Lagret");

							var add_picture_to_case_container = thisForm.next('.add_picture_to_case');
							$(add_picture_to_case_container).show();

//							clear_form(thisForm);

//							var selects = $(thisForm).find("select");
//							var  select = null;
//							for(var i = 0, len = selects.length; i < len; i++)
//							{
//								select = selects[i];
//								console.log(select);
//
//								$.each(select, function (i, option)
//								{
//									if(i==0)
//									{
//										$(option).attr('selected', true);
//									}
//									else
//									{
//										$(option).removeAttr('selected');
//									}
//								});
//							}


//							 $(thisForm).find("input[name='case_id']").val(jsonObj.case_id);
							$("#cache_case_id").val(jsonObj.case_id);

							// Changes text on save button back to original
							window.setTimeout(function ()
							{
								if (type == "control_item_type_2")
								{
									$(submitBnt).val('Lagre måling');
								}
								else
								{
									$(submitBnt).val('Lagre ny sak');
								}

								$(submitBnt).addClass("case_saved");
								$(submitBnt).attr("disabled", true);
							}, 1000);

							/*
							 $(thisForm).delay(1500).slideUp(500, function(){
							 $(thisForm).parents("ul.expand_list").find("h4 img").attr("src", "controller/images/arrow_right.png");
							 });
							 */

						}
					}
				}
			});

			var check_list_id = $(thisForm).find("input[name=check_list_id]").val();
			var oArgs = {menuaction: 'controller.uicase.get_case_data_ajax', check_list_id: check_list_id, location_code: location_code};
			var requestUrl = phpGWLink('index.php', oArgs, true);

			var htmlString = "";

			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: requestUrl,
				success: function (data)
				{
					if (data != null)
					{
//						var obj = JSON.parse(data);
						//returned as json
						var obj = data;

						$.each(obj, function (i, control_group)
						{

							$.each(control_group, function (j, control_item)
							{
								if (typeof (control_item.components_at_location) != 'undefined')
								{
									if (control_group_id != control_item.control_group.id)
									{
										return;
									}

									htmlString = "";

									var component_options = control_item.components_at_location;
									$.each(component_options, function (k, options)
									{

										$.each(options, function (k, option)
										{

											var selected = '';

											if (option.id == component_id)
											{
												selected = ' selected';
											}

											htmlString += "<option value='" + component_location_id + '_' + option.id + "'" + selected + ">" + option.id + ' - ' + option['short_description'] + "</option>";

										});
									});

									$("#component_at_control_group_" + control_group_id).html(htmlString);
								}
							});
						});
					}
				}
			});
		}
	});

	// UPDATE CASE
	$(".frm_update_case").on("submit", function (e)
	{
		e.preventDefault();

		var thisForm = $(this);
		var clickRow = $(this).closest("li");
		var checkItemRow = $(this).closest("li.check_item_case");
		var requestUrl = $(thisForm).attr("action");

		$.ajax({
			type: 'POST',
			url: requestUrl + "&" + $(thisForm).serialize(),
			success: function (data)
			{
				if (data)
				{
					var jsonObj = JSON.parse(data);

					if (jsonObj.status == "saved")
					{
						var type = $(thisForm).find("input[name=control_item_type]").val();

						if (type == "control_item_type_1")
						{
							var case_status = $(thisForm).find("select[name='case_status'] option:selected").text();

							$(clickRow).find(".case_info .case_status").empty().text(case_status);
						}
						else if (type == "control_item_type_2")
						{
							var case_status = $(thisForm).find("select[name='case_status'] option:selected").text();

							$(clickRow).find(".case_info .case_status").empty().text(case_status);

							var measurement_text = $(thisForm).find("input[name='measurement']").val();
							$(clickRow).find(".case_info .measurement").text(measurement_text);
						}
						else if (type == "control_item_type_3")
						{
							var case_status = $(thisForm).find("select[name='case_status'] option:selected").text();

							$(clickRow).find(".case_info .case_status").empty().text(case_status);

							var measurement_text = $(thisForm).find("select[name='measurement'] option:selected").val();
							$(clickRow).find(".case_info .measurement").text(measurement_text);
						}
						else if (type == "control_item_type_4")
						{
							var case_status = $(thisForm).find("select[name='case_status'] option:selected").text();

							$(clickRow).find(".case_info .case_status").empty().text(case_status);

							var measurement_text = $(thisForm).find("input:radio[name='measurement']:checked").val();
							$(clickRow).find(".case_info .measurement").text(measurement_text);
						}
						else if (type == "control_item_type_5")
						{
							var case_status = $(thisForm).find("select[name='case_status'] option:selected").text();

							$(clickRow).find(".case_info .case_status").empty().text(case_status);

							var measurement_text = '';
							$(thisForm).find("input:checkbox[name='measurement[]']:checked").each(function ()
							{
								measurement_text += "<br>" + $(this).val();
							});

							$(clickRow).find(".case_info .measurement").html(measurement_text);
						}

						var case_condition_degree = $(thisForm).find("select[name='condition_degree'] option:selected").text();
						$(clickRow).find(".case_info .case_condition_degree").empty().text(case_condition_degree);
						var case_consequence = $(thisForm).find("select[name='consequence'] option:selected").text();
						$(clickRow).find(".case_info .case_consequence").empty().text(case_consequence);

						// Text from forms textarea
						var desc_text = $(thisForm).find("textarea[name='case_descr']").val();
						var proposed_counter_measure_text = $(thisForm).find("textarea[name='proposed_counter_measure']").val();
						// Puts new text into description tag in case_info
						$(clickRow).find(".case_info .case_descr").text(desc_text);
						$(clickRow).find(".case_info .proposed_counter_measure").text(proposed_counter_measure_text);

						$(clickRow).find(".case_info").show();
						$(clickRow).find(".frm_update_case").hide();
					}
				}
			}
		});
	});

	$("a.quick_edit_case").on("click", function (e)
	{
		e.preventDefault();
		//   console.log("sdfsdfsd");
		var clickRow = $(this).closest("li");

		$(clickRow).find(".case_info").hide();
		$(clickRow).find(".frm_update_case").show();

		return false;
	});

	$(".frm_update_case .cancel").on("click", function (e)
	{
		var clickRow = $(this).closest("li");


		$(clickRow).find(".case_info").show();
		$(clickRow).find(".frm_update_case").hide();

		return false;
	});

	// DELETE CASE
	$(".delete_case").on("click", function ()
	{
		var clickElem = $(this);
		var clickRow = $(this).closest("li");
		var clickItem = $(this).closest("ul");
		var checkItemRow = $(this).parents("li.check_item_case");

		var url = $(clickElem).attr("href");

		// Sending request for deleting a control item list
		$.ajax({
			type: 'POST',
			url: url,
			success: function (data)
			{
				var obj = JSON.parse(data);

				if (obj.status == "deleted")
				{
					if ($(clickItem).children("li").length > 1)
					{
						$(clickRow).fadeOut(300, function ()
						{
							$(clickRow).remove();
						});

						var next_row = $(clickRow).next();

						// Updating order numbers for rows below deleted row
						while ($(next_row).length > 0)
						{
							update_order_nr_for_row(next_row, "-");
							next_row = $(next_row).next();
						}
					}
					else
					{
						$(checkItemRow).fadeOut(300, function ()
						{
							$(checkItemRow).remove();
						});
					}
				}
			}
		});

		return false;
	});

	// CLOSE CASE
	$(".close_case").on("click", function ()
	{
		var clickElem = $(this);
		var clickRow = $(this).closest("li");
		var clickItem = $(this).closest("ul");
		var checkItemRow = $(this).parents("li.check_item_case");

		var url = $(clickElem).attr("href");

		// Sending request for deleting a control item list
		$.ajax({
			type: 'POST',
			url: url,
			success: function (data)
			{
				var obj = JSON.parse(data);

				if (obj.status == "true")
				{
					if ($(clickItem).children("li").length > 1)
					{
						$(clickRow).fadeOut(300, function ()
						{
							$(clickRow).remove();
						});

						var next_row = $(clickRow).next();

						// Updating order numbers for rows below deleted row
						while ($(next_row).length > 0)
						{
							update_order_nr_for_row(next_row, "-");
							next_row = $(next_row).next();
						}
					}
					else
					{
						$(checkItemRow).fadeOut(300, function ()
						{
							$(checkItemRow).remove();
						});
					}
				}
			}
		});

		return false;
	});

	// OPEN CASE
	$(".open_case").on("click", function ()
	{
		var clickElem = $(this);
		var clickRow = $(this).closest("li");
		var clickItem = $(this).closest("ul");
		var checkItemRow = $(this).parents("li.check_item_case");

		var url = $(clickElem).attr("href");

		// Sending request for deleting a control item list
		$.ajax({
			type: 'POST',
			url: url,
			success: function (data)
			{
				var obj = JSON.parse(data);

				if (obj.status == "true")
				{
					if ($(clickItem).children("li").length > 1)
					{
						$(clickRow).fadeOut(300, function ()
						{
							$(clickRow).remove();
						});

						var next_row = $(clickRow).next();

						// Updating order numbers for rows below deleted row
						while ($(next_row).length > 0)
						{
							update_order_nr_for_row(next_row, "-");
							next_row = $(next_row).next();
						}
					}
					else
					{
						$(checkItemRow).fadeOut(300, function ()
						{
							$(checkItemRow).remove();
						});
					}
				}
			}
		});

		return false;
	});

	$("#choose-building-on-property").change(function ()
	{
		var location_code = $(this).val();
		var search = location.search.substring(1);
		var oArgs = search ? JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g, '":"') + '"}',
			function (key, value)
			{
				return key === "" ? value : decodeURIComponent(value)
			}) : {}

		oArgs.location_code = location_code;
		delete oArgs.click_history;
		var reloadPageUrl = phpGWLink('index.php', oArgs);
		//var reloadPageUrl = location.pathname + location.search + "&location_code=" + location_code;
		location.href = reloadPageUrl;
	});


	/*
	 $("#choose-building-on-property.view-cases").change(function () {
	 var location_code = $(this).val();

	 var reloadPageUrl = location.pathname + location.search + "&location_code=" + location_code;
	 alert(reloadPageUrl);
	 location.href = reloadPageUrl;
	 });
	 */
});

function validate_form(formObj)
{
	var status = true;

	$(formObj).find(".input_error_msg").remove();

	$(formObj).find(":input.required").each(function ()
	{
		var thisInput = $(this);

		if ($(thisInput).val() == '')
		{
			if ($(thisInput).attr("type") == 'hidden')
			{
				$(formObj).prepend("<div class='input_error_msg'>Du må spesifisere lokalisering</div>");
			}
			else
			{
				$(thisInput).before("<div class='input_error_msg'>Du må fylle ut dette feltet</div>");
			}

			status = false;
		}
	});

	return status;
}

//Updates order number for hidden field and number in front of row
function update_order_nr_for_row(element, sign)
{

	var span_order_nr = $(element).find("span.order_nr");
	var order_nr = $(span_order_nr).text();

	if (sign == "+")
		var updated_order_nr = parseInt(order_nr) + 1;
	else
		var updated_order_nr = parseInt(order_nr) - 1;

	// Updating order number in front of row
	$(span_order_nr).text(updated_order_nr);
}
