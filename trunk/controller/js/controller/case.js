$(document).ready(function() {

	// REGISTER CASE
	$(".frm_register_case").live("submit", function(e) {
		e.preventDefault();

		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		var type = $(thisForm).find("input[name='type']").val();
		var requestUrl = $(thisForm).attr("action");

		var location_code = $("#choose-building-on-property  option:selected").val();
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

		if (validate_status) {
			$.ajax({
				type: 'POST',
				url: requestUrl + "&" + $(thisForm).serialize(),
				success: function(data) {
					if (data) {
						var jsonObj = jQuery.parseJSON(data);

						if (jsonObj.status == "saved") {
							var submitBnt = $(thisForm).find("input[type='submit']");
							$(submitBnt).val("Lagret");

							clear_form(thisForm);

							// Changes text on save button back to original
							window.setTimeout(function() {
								if (type == "control_item_type_2")
								{
									$(submitBnt).val('Lagre måling');
								}
								else
								{
									$(submitBnt).val('Lagre sak');
								}

								$(submitBnt).addClass("not_active");
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
				success: function(data) {
					if (data != null) {
						var obj = jQuery.parseJSON(data);

						$.each(obj, function(i, control_group) {

							$.each(control_group, function(j, control_item)
							{
								if (typeof (control_item.components_at_location) != 'undefined')
								{
									if (control_group_id != control_item.control_group.id)
									{
										return;
									}

									htmlString = "";

									var component_options = control_item.components_at_location;
									$.each(component_options, function(k, options) {

										$.each(options, function(k, option) {

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
	$(".frm_update_case").live("submit", function(e) {
		e.preventDefault();

		var thisForm = $(this);
		var clickRow = $(this).closest("li");
		var checkItemRow = $(this).closest("li.check_item_case");
		var requestUrl = $(thisForm).attr("action");

		$.ajax({
			type: 'POST',
			url: requestUrl + "&" + $(thisForm).serialize(),
			success: function(data) {
				if (data) {
					var jsonObj = jQuery.parseJSON(data);

					if (jsonObj.status == "saved") {
						var type = $(thisForm).find("input[name=control_item_type]").val();

						if (type == "control_item_type_1") {
							var case_status = $(thisForm).find("select[name='case_status'] option:selected").text();

							$(clickRow).find(".case_info .case_status").empty().text(case_status);
						}
						else if (type == "control_item_type_2") {
							var case_status = $(thisForm).find("select[name='case_status'] option:selected").text();

							$(clickRow).find(".case_info .case_status").empty().text(case_status);

							var measurement_text = $(thisForm).find("input[name='measurement']").val();
							$(clickRow).find(".case_info .measurement").text(measurement_text);
						}
						else if (type == "control_item_type_3") {
							var case_status = $(thisForm).find("select[name='case_status'] option:selected").text();

							$(clickRow).find(".case_info .case_status").empty().text(case_status);

							var measurement_text = $(thisForm).find("select[name='measurement'] option:selected").val();
							$(clickRow).find(".case_info .measurement").text(measurement_text);
						}
						else if (type == "control_item_type_4") {
							var case_status = $(thisForm).find("select[name='case_status'] option:selected").text();

							$(clickRow).find(".case_info .case_status").empty().text(case_status);

							var measurement_text = $(thisForm).find("input:radio[name='measurement']:checked").val();
							$(clickRow).find(".case_info .measurement").text(measurement_text);
						}

						// Text from forms textarea
						var desc_text = $(thisForm).find("textarea").val();
						// Puts new text into description tag in case_info	    				   				
						$(clickRow).find(".case_info .case_descr").text(desc_text);

						$(clickRow).find(".case_info").show();
						$(clickRow).find(".frm_update_case").hide();
					}
				}
			}
		});
	});

	$("a.quick_edit_case").live("click", function(e) {
		e.preventDefault();
		//   console.log("sdfsdfsd");
		var clickRow = $(this).closest("li");

		$(clickRow).find(".case_info").hide();
		$(clickRow).find(".frm_update_case").show();

		return false;
	});

	$(".frm_update_case .cancel").live("click", function(e) {
		var clickRow = $(this).closest("li");


		$(clickRow).find(".case_info").show();
		$(clickRow).find(".frm_update_case").hide();

		return false;
	});

	// DELETE CASE
	$(".delete_case").live("click", function() {
		var clickElem = $(this);
		var clickRow = $(this).closest("li");
		var clickItem = $(this).closest("ul");
		var checkItemRow = $(this).parents("li.check_item_case");

		var url = $(clickElem).attr("href");

		// Sending request for deleting a control item list
		$.ajax({
			type: 'POST',
			url: url,
			success: function(data) {
				var obj = jQuery.parseJSON(data);

				if (obj.status == "deleted") {
					if ($(clickItem).children("li").length > 1) {
						$(clickRow).fadeOut(300, function() {
							$(clickRow).remove();
						});

						var next_row = $(clickRow).next();

						// Updating order numbers for rows below deleted row  
						while ($(next_row).length > 0) {
							update_order_nr_for_row(next_row, "-");
							next_row = $(next_row).next();
						}
					} else {
						$(checkItemRow).fadeOut(300, function() {
							$(checkItemRow).remove();
						});
					}
				}
			}
		});

		return false;
	});

	// CLOSE CASE
	$(".close_case").live("click", function() {
		var clickElem = $(this);
		var clickRow = $(this).closest("li");
		var clickItem = $(this).closest("ul");
		var checkItemRow = $(this).parents("li.check_item_case");

		var url = $(clickElem).attr("href");

		// Sending request for deleting a control item list
		$.ajax({
			type: 'POST',
			url: url,
			success: function(data) {
				var obj = jQuery.parseJSON(data);

				if (obj.status == "true") {
					if ($(clickItem).children("li").length > 1) {
						$(clickRow).fadeOut(300, function() {
							$(clickRow).remove();
						});

						var next_row = $(clickRow).next();

						// Updating order numbers for rows below deleted row  
						while ($(next_row).length > 0) {
							update_order_nr_for_row(next_row, "-");
							next_row = $(next_row).next();
						}
					} else {
						$(checkItemRow).fadeOut(300, function() {
							$(checkItemRow).remove();
						});
					}
				}
			}
		});

		return false;
	});

	// OPEN CASE
	$(".open_case").live("click", function() {
		var clickElem = $(this);
		var clickRow = $(this).closest("li");
		var clickItem = $(this).closest("ul");
		var checkItemRow = $(this).parents("li.check_item_case");

		var url = $(clickElem).attr("href");

		// Sending request for deleting a control item list
		$.ajax({
			type: 'POST',
			url: url,
			success: function(data) {
				var obj = jQuery.parseJSON(data);

				if (obj.status == "true") {
					if ($(clickItem).children("li").length > 1) {
						$(clickRow).fadeOut(300, function() {
							$(clickRow).remove();
						});

						var next_row = $(clickRow).next();

						// Updating order numbers for rows below deleted row  
						while ($(next_row).length > 0) {
							update_order_nr_for_row(next_row, "-");
							next_row = $(next_row).next();
						}
					} else {
						$(checkItemRow).fadeOut(300, function() {
							$(checkItemRow).remove();
						});
					}
				}
			}
		});

		return false;
	});

	$("#choose-building-on-property").change(function() {
		var location_code = $(this).val();
		var search = location.search.substring(1);
		var oArgs = search ? JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g, '":"') + '"}',
			function(key, value) {
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

	$(formObj).find(":input.required").each(function() {
		var thisInput = $(this);

		if ($(thisInput).val() == '')
		{
			if ($(thisInput).attr("type") == 'hidden')
			{
				$(formObj).prepend("<div class='input_error_msg'>Du må spesifisere lokalisering</div>");
			} else
			{
				$(thisInput).before("<div class='input_error_msg'>Du må fylle ut dette feltet</div>");
			}

			status = false;
		}
	});

	return status;
}

//Updates order number for hidden field and number in front of row
function update_order_nr_for_row(element, sign) {

	var span_order_nr = $(element).find("span.order_nr");
	var order_nr = $(span_order_nr).text();

	if (sign == "+")
		var updated_order_nr = parseInt(order_nr) + 1;
	else
		var updated_order_nr = parseInt(order_nr) - 1;

	// Updating order number in front of row
	$(span_order_nr).text(updated_order_nr);
}
