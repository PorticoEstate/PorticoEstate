$(document).ready(function ()
{
	$(window).scroll(function ()
	{
		if ($(this).scrollTop() > 100)
		{
			$('.scrollup').fadeIn();
		}
		else
		{
			$('.scrollup').fadeOut();
		}
	});

	$('.scrollup').click(function ()
	{
		$("html, body").animate({
			scrollTop: 0
		}, 600);
		return false;
	});


//	var test_for_child = $("#choose-child-on-component");
//
//	if(test_for_child)
//	{
//		if(test_for_child.val())
//		{
//			$("#view_cases").show();
//		}
//		else
//		{
//			$("#view_cases").hide();
//		}
//	}


	$(".add_picture_to_case_form").on("submit", function (e)
	{

		e.preventDefault();

		var thisForm = $(this);
//		var submitBnt = $(thisForm).find("button[type='submit']");
//		submitBnt.prop('disabled', true);

		$('<div id="spinner" class="text-center mt-2  ml-2">')
			.append($('<div class="spinner-border" role="status">')
				.append($('<span class="sr-only">Loading...</span>')))
			.insertAfter(thisForm);

		var oArgs = {menuaction: 'controller.uicase.add_case_image'};
		var requestUrl = phpGWLink('index.php', oArgs, true);

//		var requestUrl = $(thisForm).attr("action");

		var case_id = $("form").prev().find("input[name='case_id']").val();

		if (!case_id)
		{
			var case_id = $("#cache_case_id").val();
		}

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
						$('.custom-file-input').each(function (i, obj)
						{
							$(obj).val('');
							$(obj).siblings(".custom-file-label").removeClass("selected").html('Nytt bilde');
						});
						show_case_picture(case_id, thisForm);
//						submitBnt.prop('disabled', false);
					}
					else
					{
						alert(data.message);
					}
					var element = document.getElementById('spinner');
					if (element)
					{
						element.parentNode.removeChild(element);
					}
				}
			}
		});
	});

	$('#inspectObject').on('hidden.bs.modal', function (e)
	{
		if ($("#cache_case_id").val())
		{
			$('#set_completed_item').submit();
		}
	});

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

		var submitBnt = $(thisForm).find("input[type='submit']");
		$(submitBnt).prop("disabled", true);
		var spinner = '<div id="spinner" class="d-flex justify-content-center">  <div class="spinner-border" role="status"> <span class="sr-only"></span> </div></div>';
		$(spinner).insertBefore($(submitBnt));
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
								$(submitBnt).hide();
								$(submitBnt).prop("disabled", true);
								if (type == "control_item_type_2")
								{
									$(submitBnt).val('Lagre måling');
								}
								else
								{
									$(submitBnt).val('Lagre ny sak');
								}

								var element = document.getElementById('spinner');
								if (element)
								{
									element.parentNode.removeChild(element);
								}


//
//								$(submitBnt).addClass("case_saved");
//								$(submitBnt).attr("disabled", true);
							}, 500);

							$("#reset_form").val('Ny sak');
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

						var regulation_reference_text = $(thisForm).find("select[name='regulation_reference'] option:selected").val();
						$(clickRow).find(".case_info .regulation_reference").text(regulation_reference_text);

						var case_component_child = $(thisForm).find("select[name='component_child'] option:selected").text();
						$(clickRow).find(".case_info .case_component_child").empty().text(case_component_child);
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

function undo_completed(completed_id)
{
	var oArgs = {
		menuaction: 'controller.uicheck_list.undo_completed_item',
		completed_id: completed_id
	};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	$.ajax({
		type: 'POST',
		url: requestUrl,
		success: function (data)
		{
			if (data)
			{
				var status = data.status;
				if (status === 'ok')
				{
					location.reload();
				}
			}
		}
	});
}

function deleteValueFromRegulationReference(control_item_id)
{
	var regulation_value = $("#regulation_reference option:selected").val();

	var oArgs = {
		menuaction: 'controller.uicontrol_item.delete_regulation_reference',
		control_item_id: control_item_id
	};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	$.ajax({
		type: 'POST',
		url: requestUrl,
		data: {regulation_value: regulation_value},
		success: function (data)
		{
			if (data)
			{
				var status = data.status;
				if (status === 'ok')
				{
					$('#regulation_reference option:selected').remove();
				}
			}
		}
	});
}

/**
 * Open a prompt for input
 * @param {type} id
 * @param {type} input_text
 * @param {type} lang_new_value
 * @returns {undefined}
 */

function addNewValueToRegulationReference(control_item_id, input_text, lang_new_value)
{

	var oArgs = {
		menuaction: 'controller.uicase.add_regulation_option',
		control_item_id: control_item_id
	};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	//	var new_value = prompt(input_text, "");

	/*
	 * @title {String or DOMElement} The dialog title.
	 * @message {String or DOMElement} The dialog contents.
	 * @value {String} The default input value.
	 * @onok {Function} Invoked when the user clicks OK button.
	 * @oncancel {Function} Invoked when the user clicks Cancel button or closes the dialog.
	 *
	 * alertify.prompt(title, message, value, onok, oncancel);
	 *
	 */
	alertify.prompt(input_text, lang_new_value, ''
		, function (evt, value)
		{
			var new_value = value;
			if (new_value !== null && new_value !== "")
			{
				var xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function ()
				{
					if (this.readyState == 4 && this.status == 200)
					{
						var data = JSON.parse(this.responseText);

						if (data.status == 'ok' && data.choice_id)
						{
							alertify.success('You entered: ' + value);
							var select = document.getElementById('regulation_reference');
							var option = document.createElement("option");
							option.text = new_value;
							option.id = data.choice_id;
							select.add(option, select[1]);
							select.selectedIndex = "1";
						}
						else
						{
							alertify.error('Error');
						}
					}
				};
				xmlhttp.open("POST", requestUrl, true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				var params = 'new_value=' + new_value;
				xmlhttp.send(params);
			}
			else
			{
				alertify.error('Cancel');
			}
		}
	, function ()
	{
		alertify.error('Cancel')
	});

}