
/* global enable_add_case */

downloadComponents = function (parent_location_id, parent_id, location_id)
{
	var oArgs = {
		menuaction: 'property.uientity.download',
		parent_location_id: parent_location_id,
		parent_id: parent_id,
		location_id: location_id,
		export: 1
	};
	var requestUrl = phpGWLink('index.php', oArgs);
	window.location.href = requestUrl;
};


$(document).ready(function ()
{

	// EDIT COMPONENT
	show_parent_component_information = function (location_id, component_id)
	{
		var x = document.getElementById("form_parent_component_2");

		var y = document.getElementById("new_picture_parent");
		if (x.style.display === "block")
		{
			x.style.display = "none";
			y.style.display = "none";
		}
		else
		{
			x.style.display = "block";
			y.style.display = "block";
		}

		var oArgs = {
			menuaction: 'controller.uicase.edit_parent_component',
			location_id: location_id,
			component_id: component_id,
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
					$("#form_parent_component_2").html(data.html);
					var script = document.createElement("script");
					script.textContent = data.lookup_functions;
					document.head.appendChild(script);
					show_component_parent_picture(location_id + '_' + component_id);
				}
			}
		});
	};

	get_parent_component_edit_form = function ()
	{
		var location_id = $('input[name=location_id]')[0];
		var component_id = $('input[name=component_id]')[0];

		var oArgs = {
			menuaction: 'controller.uicase.edit_parent_component',
			location_id: $(location_id).val(),
			component_id: $(component_id).val(),
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
					$("#form_parent_component_2").html(data.html);
					var script = document.createElement("script");
					script.textContent = data.lookup_functions;
					document.head.appendChild(script);
				}
			}
		});
	};

	remove_component_form = function (form)
	{
		var edit_parent = $(form).find("input[name=edit_parent]").val();

		if (edit_parent == 1)
		{
			$("#form_parent_component_2").html('');
			$("#form_parent_component_2").hide();
		}
		else
		{
			$("#form_new_component_2").html('');
			document.getElementById("choose-child-on-component").selectedIndex = "0";
			$('#equipment_picture_container').html('');
		}
	};


	submitComponentForm = function (e, form)
	{
		var edit_parent = $(form).find("input[name=edit_parent]").val();

		e.preventDefault();
		var requestUrl = $(form).attr("action");

		var inputs = $("select, input"), input = null, flag = true;
		for (var i = 0, len = inputs.length; i < len; i++)
		{
			input = inputs[i];

			if ($(input).attr("data-validation") == "required")
			{
				if (!$(input).val())
				{
					$(input).addClass('error');
					$(input).attr("style", 'border-color: rgb(185, 74, 72);');
					$(input).focus();
					flag = false;
				}
				else
				{
					$(input).removeClass('error');
					$(input).removeAttr("style");
					$(input).addClass('valid');
				}
			}
		}

		if (!flag)
		{
			return false;
		}
		var submitBnt = $('#submit_component_form');
		$(submitBnt).prop("disabled", true);
		var spinner = '<div id="spinner" class="d-flex justify-content-center">  <div class="spinner-border" role="status"> <span class="sr-only"></span> </div></div>';
		$(spinner).insertBefore($(submitBnt));

		$.ajax({
			type: 'POST',
			url: requestUrl,
			data: $(form).serialize(),
			success: function (data)
			{
				$(submitBnt).prop("disabled", false);

				if (data.status == "saved")
				{
					$("#choose-child-on-component").empty();

					var component_children = data.component_children;

					$.each(component_children, function (i, val)
					{
						$('#choose-child-on-component').append($('<option>', {
							value: val.location_id ? val.location_id + '_' + val.id : '',
							text: val.short_description,
							selected: val.selected === 1 ? true : null
						}));
					});

					$("#item_string").val($('#choose-child-on-component').val());
					$("#inspection_title").html($("#choose-child-on-component option:selected").text());
				}
				if (edit_parent == 1)
				{
					$("#form_parent_component_2").html(data.message);
					$("#form_parent_component_2").hide(2000);
				}
				else
				{
					$("#form_new_component_2").html(data.message);
					$("#new_picture").show();
//					$("#view_cases").hide();
				}
			}
		});

		return false;
	};

	$("#choose-child-on-component").change(function ()
	{
		$("#submit_update_component").hide();
		$("#component_picture_file").val('');

		if ($(this).val())
		{
			show_component_information($(this).val());
			show_component_picture();
			$("#new_picture").show();
			$("#view_cases").show();
			$("#inspection_title").html($("#choose-child-on-component option:selected").text());
			$("#item_string").val($(this).val());
		}
		else
		{
			$('#equipment_picture_container').html('');
			$("#new_picture").hide();
			$("#form_new_component_2").html('');
			$("#view_cases").hide();
			$("#perform_control_on_selected").remove();

		}
	});

	show_component_parent_picture = function (component)
	{
		var d = new Date();
		var n = d.getTime();// to forse refrech cache
		var oArgs = {menuaction: 'controller.uicase.get_image', component: component, n: n};
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
						$('#equipment_parent_picture_container').html('<img alt="Bilde" id="equipment_parent_picture" src="' + ImageUrl + '" style="width:100%;max-width:300px"  class="img-fluid"/>');
						$('#equipment_parent_picture_container').show();
					}
					else
					{
						$('#equipment_parent_picture_container').html(data.message);
					}
				}
			}
		});
	};
	show_component_picture = function ()
	{
		var d = new Date();
		var n = d.getTime();// to forse refrech cache
		var component = $("#choose-child-on-component").val();
		var oArgs = {menuaction: 'controller.uicase.get_image', component: component, n: n};
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

		var form = $('#component_picture_file').closest('form');
		form.submit();

//		$("#submit_update_component").show();

	};
	show_picture_parent_submit = function ()
	{
//		$("#submit_update_component_parent").show();

	};

	// REGISTER PICTURE TO PARENT COMPONENT
	$("#frm_add_picture_parent").on("submit", function (e)
	{
		e.preventDefault();

		var thisForm = $(this);
		var requestUrl = $(thisForm).attr("action");
		var component = $(thisForm).find("input[name='component']").val();

		requestUrl += '&component=' + component;

		$('<div id="spinner" class="text-center mt-2  ml-2">')
			.append($('<div class="spinner-border" role="status">')
				.append($('<span class="sr-only">Loading...</span>')))
			.insertAfter(thisForm);

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
						$("#submit_update_component_parent").hide();
						$("#component_parent_picture_file").val('');
						show_component_parent_picture(component);
					}
					else
					{
						alert(data.message);
					}
				}
				var element = document.getElementById('spinner');
				if (element)
				{
					element.parentNode.removeChild(element);
				}

			}
		});
	});

	// REGISTER PICTURE TO CHILD COMPONENT
	$("#frm_add_picture").on("submit", function (e)
	{
		e.preventDefault();

		var thisForm = $(this);
		var requestUrl = $(thisForm).attr("action");

		$('<div id="spinner" class="text-center mt-2  ml-2">')
			.append($('<div class="spinner-border" role="status">')
				.append($('<span class="sr-only">Loading...</span>')))
			.insertAfter(thisForm);

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
				var element = document.getElementById('spinner');
				if (element)
				{
					element.parentNode.removeChild(element);
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

		if (typeof (enable_add_case) !== 'undefined' && enable_add_case === true)
		{
			oArgs.enable_add_case = true;
		}

		var requestUrl = phpGWLink('index.php', oArgs, true);

		$.ajax({
			type: 'POST',
			url: requestUrl,
			success: function (data)
			{
				if (data)
				{
					$("#form_new_component_2").html(data.html);
					var script = document.createElement("script");
					script.textContent = data.lookup_functions;
					document.head.appendChild(script);
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

		var submitBnt = $('#submit_component_form');

		$(submitBnt).prop("disabled", true);
		var spinner = '<div id="spinner" class="d-flex justify-content-center">  <div class="spinner-border" role="status"> <span class="sr-only"></span> </div></div>';
		$(spinner).insertBefore($(submitBnt));

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
					$("#form_new_component_2").html(data.html);
					var script = document.createElement("script");
					script.textContent = data.lookup_functions;
					document.head.appendChild(script);
				}
			}
		});
	};


	// REGISTER NEW CHILD COMPONENT
	$(".form_new_component").on("submit", function (e)
	{

		e.preventDefault();
		$('#equipment_picture_container').html('');
		$("#new_picture").hide();
		document.getElementById("choose-child-on-component").selectedIndex = "0";

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
					$("#form_new_component_2").html(data.html);
					var script = document.createElement("script");
					script.textContent = data.lookup_functions;
					document.head.appendChild(script);
				}
			}
		});

	});

	resetForm = function (form)
	{
		clear_form(form);
		$("#cache_case_id").val('');
		$(form).find("input[type='submit']").show();
		$(form).find("input[type='submit']").removeAttr('disabled');
		$(form).find("input[type='submit']").removeClass("case_saved");
		var picture_container = $(form).next('div').find("div[name='picture_container']");
		picture_container.html('');
		var add_picture_to_case_container = $(form).next('.add_picture_to_case');
		$(add_picture_to_case_container).hide();

		$("#reset_form").val('Tøm skjema');

	};

	// Add the following code if you want the name of the file appear on select
	$(".custom-file-input").on("change", function ()
	{
		var fileName = $(this).val().split("\\").pop();
		$(this).siblings(".custom-file-label").addClass("selected").html(fileName);
		var form = $(this).closest('form');
		form.submit();
	});


});