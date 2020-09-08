/* global html_editor, CKEDITOR */

var pendingList = 0;
var redirect_action;
var file_count = 0;

this.confirm_session = function (action)
{
	if (action == 'cancel')
	{
		window.location.href = phpGWLink('index.php', {menuaction: 'helpdesk.uitts.index', parent_cat_id: parent_cat_id});
		return;
	}

	if (action == 'save' || action == 'apply')
	{
		var conf = {
			modules: 'location, date, security, file',
			validateOnBlur: false,
			scrollToTopOnError: true,
			errorMessagePosition: 'top',
			validateHiddenInputs: true
		};
		var test = $('form').isValid(false, conf);
		if (!test)
		{
			return;
		}
	}
	/**
	 * Block doubleclick
	 */
	var send_buttons = $('.pure-button');
	$(send_buttons).each(function ()
	{
		$(this).prop('disabled', true);
	});

	var oArgs = {menuaction: 'property.bocommon.confirm_session'};
	var strURL = phpGWLink('index.php', oArgs, true);
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: strURL,
		success: function (data)
		{
			if (data != null)
			{
				if (data['sessionExpired'] == true)
				{
					window.alert('sessionExpired - please log in');
					JqueryPortico.lightboxlogin(); //defined in common.js
				}
				else
				{
					var form = document.getElementById('form');
					//				form.style.display = 'none';
//					$('<div id="spinner" class="d-flex align-items-center">')
//						.append($('<strong>').text('Lagrer...'))
//						.append($('<div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>'))
//						.insertAfter(form);

					$('<div id="spinner" class="text-center mt-2  ml-2">')
						.append($('<div class="spinner-border" role="status">')
							.append($('<span class="sr-only">Loading...</span>')))
						.insertAfter(form);

					window.scrollBy(0, 100); //

					document.getElementById(action).value = 1;
					try
					{
						validate_submit(action);
					}
					catch (e)
					{
						ajax_submit_form(action);
//						document.form.submit();
					}
				}
			}
		},
		failure: function (o)
		{
			window.alert('failure - try again - once');
		},
		timeout: 5000
	});
};
ajax_submit_form = function (action)
{
	var thisForm = $('#form');
	var requestUrl = $(thisForm).attr("action");
	var formdata = false;
	if (window.FormData)
	{
		try
		{
			formdata = new FormData(thisForm[0]);
			if (html_editor === 'ckeditor')
			{
				formdata.new_note = CKEDITOR.instances['new_note'].getData();
			}
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
		url: requestUrl + '&phpgw_return_as=json',
		data: formdata ? formdata : thisForm.serialize(),
		success: function (data, textStatus, jqXHR)
		{
			if (data)
			{
				if (data.status == "saved")
				{
					var id = data.id;
					if (action == 'apply')
					{
						var oArgs = {menuaction: 'helpdesk.uitts.view',
							parent_cat_id: data.parent_cat_id,
							id: id,
							tab: 'general'
						};
					}
					else
					{
						var oArgs = {menuaction: 'helpdesk.uitts.index',
							parent_cat_id: data.parent_cat_id
						};
					}

					redirect_action = phpGWLink('index.php', oArgs);
					if (pendingList === 0)
					{
						window.location.href = redirect_action;
					}
					else
					{
						sendAllFiles(id);
					}
				}
				else
				{
					var send_buttons = $('.pure-button');
					$(send_buttons).each(function ()
					{
						$(this).prop('disabled', false);
					});

					var element = document.getElementById('spinner');
					if (element)
					{
						element.parentNode.removeChild(element);
					}

					var error_message = '';
					$.each(data.message, function (index, error)
					{
						error_message += error.msg + "\n";
					});

					alert(error_message);
				}
			}
		}
	});
};

$(document).ready(function ()
{

	$('#group_id').attr("data-validation", "assigned").attr("data-validation-error-msg", lang['Please select a person or a group to handle the ticket !']);
	$('#user_id').attr("data-validation", "assigned").attr("data-validation-error-msg", lang['Please select a person or a group to handle the ticket !']);

	var conf_on_load = {
		modules: 'date, file',
		validateOnBlur: true,
		scrollToTopOnError: false,
		errorMessagePosition: 'inline',
		validateHiddenInputs: true,
	};

	setTimeout(function ()
	{
		$('form').isValid(false, conf_on_load, true);
	}, 500);

	formatFileSize = function (bytes)
	{
		if (typeof bytes !== 'number')
		{
			return '';
		}
		if (bytes >= 1000000000)
		{
			return (bytes / 1000000000).toFixed(2) + ' GB';
		}
		if (bytes >= 1000000)
		{
			return (bytes / 1000000).toFixed(2) + ' MB';
		}
		return (bytes / 1000).toFixed(2) + ' KB';
	};


	sendAllFiles = function (id)
	{

		$('#fileupload').fileupload(
			'option',
			'url',
			phpGWLink('index.php', {menuaction: 'helpdesk.uitts.handle_multi_upload_file', id: id})
			);

		$.each($('.start_file_upload'), function (index, file_start)
		{
			file_start.click();
		});
	};

	$('#fileupload').fileupload({
		dropZone: $('#drop-area'),
		uploadTemplateId: null,
		downloadTemplateId: null,
		autoUpload: false,
		add: function (e, data)
		{
			$.each(data.files, function (index, file)
			{
				var file_size = formatFileSize(file.size);

				data.context = $('<p class="file">')
					.append($('<span>').text(data.files[0].name + ' ' + file_size))
					.appendTo($(".content_upload_download"))
					.append($('<button type="button" class="start_file_upload" style="display:none">start</button>')
						.click(function ()
						{
							data.submit();
						}));

				pendingList++;

			});

		},
		progress: function (e, data)
		{
			var progress = parseInt((data.loaded / data.total) * 100, 10);
			data.context.css("background-position-x", 100 - progress + "%");
		},
		done: function (e, data)
		{
			file_count++;

			var result = JSON.parse(data.result);

			if (result.files[0].error)
			{
				data.context
					.removeClass("file")
					.addClass("error")
					.append($('<span>').text(' Error: ' + result.files[0].error));
			}
			else
			{
				data.context
					.addClass("done");
			}

			if (file_count === pendingList)
			{
				window.location.href = redirect_action;
			}

		},
		limitConcurrentUploads: 1,
		maxChunkSize: 8388000
	});

	$(document).bind('dragover', function (e)
	{
		var dropZone = $('#drop-area'),
			timeout = window.dropZoneTimeout;
		if (timeout)
		{
			clearTimeout(timeout);
		}
		else
		{
			dropZone.addClass('in');
		}
		var hoveredDropZone = $(e.target).closest(dropZone);
		dropZone.toggleClass('hover', hoveredDropZone.length);
		window.dropZoneTimeout = setTimeout(function ()
		{
			window.dropZoneTimeout = null;
			dropZone.removeClass('in hover');
		}, 100);
	});

	$(document).bind('drop dragover', function (e)
	{
		e.preventDefault();
	});

});


$.formUtils.addValidator({
	name: 'assigned',
	validatorFunction: function (value, $el, config, languaje, $form)
	{
		var v = false;
		var group_id = $('#group_id').val();
		var user_id = $('#user_id').val();
		if (group_id != "" || user_id != "")
		{
			v = true;
		}
		return v;
	},
	errorMessage: 'Assigned is required',
	errorMessageKey: ''
});


$.formUtils.addValidator({
	name: 'new_note',
	validatorFunction: function (value, $el, config, languaje, $form)
	{
		var element = document.getElementById('editor_error');
		if (element)
		{
			element.parentNode.removeChild(element);
		}
		var v = true;
		if (html_editor === 'summernote')
		{
			if ($('#new_note').summernote('isEmpty'))
			{
				$('<span id="editor_error" class="help-block form-error">').text('Angi detaljer').insertAfter($('.note-editor'));
				v = false;
			}
		}
		else if (html_editor === 'ckeditor')
		{
			var data = CKEDITOR.instances['new_note'].getData();

			document.getElementById("new_note").value = data;

			if (!data)
			{
				$('<span id="editor_error" class="help-block form-error">').text('Angi detaljer').insertAfter($('#new_note'));
				v = false;
			}
		}
		else
		{
			var data = document.getElementById("new_note").value;

			if (!data)
			{
				$('<span id="editor_error" class="help-block form-error">').text('Angi detaljer').insertAfter($('#new_note'));
				v = false;
			}

		}
		return v;
	},
	errorMessage: 'details are required',
	errorMessageKey: ''
});


$(function ()
{

	$('#paste_image_data').pastableNonInputable();

	$('#paste_image_data').on('pasteImage', function (ev, data)
	{
		$('<div style="margin: 1em 0 0 0;"  >image: ' + data.width + ' x ' + data.height + '<img src="' + data.dataURL + '" ></div>').insertAfter(this);
		$('<input type="hidden" name="pasted_image[]" value="' + data.dataURL + '"></input>').insertAfter(this);
		$('#pasted_image_is_blank').val(0);

	}).on('pasteImageError', function (ev, data)
	{
		alert('Oops: ' + data.message);
		if (data.url)
		{
			alert('But we got its url anyway:' + data.url)
		}
	}).on('pasteText', function (ev, data)
	{
		$('#paste_image_data').val('');
	});
});


JqueryPortico.autocompleteHelper(phpGWLink('index.php',
{
	menuaction: 'helpdesk.uitts.get_on_behalf_of',
	custom_method: true, method: 'get_on_behalf_of',
	acl_location: '.ticket'
}, true),
	'set_on_behalf_of_name', 'set_on_behalf_of_lid', 'set_behalf_of_container');

JqueryPortico.autocompleteHelper(phpGWLink('index.php',
{
	menuaction: 'helpdesk.uitts.get_on_behalf_of',
	custom_method: true, method: 'get_on_behalf_of',
	acl_location: '.ticket'
}, true),
	'set_user_alternative_name', 'set_user_alternative_lid', 'set_user_alternative_container');

JqueryPortico.autocompleteHelper(phpGWLink('index.php',
{
	menuaction: 'helpdesk.uitts.get_on_behalf_of',
	custom_method: true, method: 'get_on_behalf_of',
	acl_location: '.ticket'
}, true),
	'set_notify_name', 'set_notify_lid', 'set_notify_container');

$(window).on('load', function ()
{
	$.fn.insertAtCaret = function (myValue)
	{
		myValue = myValue.trim();
//		CKEDITOR.instances['new_note'].insertText(myValue);
		CKEDITOR.instances['new_note'].insertHtml(myValue);
	};

	$("#set_on_behalf_of_name").on("autocompleteselect", function (event, ui)
	{
		//	console.log(ui);
		var on_behalf_of_lid = ui.item.value;
		try
		{
			if (html_editor === 'summernote')
			{
				$('textarea#new_note').summernote('insertText', "Saken gjelder: " + ui.item.label);
			}
			else if (html_editor === 'ckeditor')
			{
				var temp = document.getElementById("new_note").value;
				if (temp)
				{
					temp = temp + "<br/>";
				}
				CKEDITOR.instances['new_note'].insertText(temp + "Saken sendes til: " + ui.item.label);

			}
			else
			{
				var temp = document.getElementById("new_note").value;
				if (temp)
				{
					temp = temp + "\n";
				}
				document.getElementById("new_note").value = temp + "Saken gjelder: " + ui.item.label;
			}

			var conf = {
				modules: 'location, date, security, file',
				validateOnBlur: false,
				scrollToTopOnError: false
			};

			$('form').isValid(false, conf);
		}
		catch (err)
		{
		}


		/**
		 * Denne henter kandidater for saksbehandker - basert på epostlister fra Outlook
		 * - endres til å hende nærmeste leder
		 */
//		var selection = [];
//		if (on_behalf_of_lid)
//		{
//			populateTableChkAssignee(on_behalf_of_lid, selection);
//			try
//			{
//				get_user_info(on_behalf_of_lid);
//			}
//			catch (err)
//			{
//			}
//		}
	});

	$("#set_user_alternative_name").on("autocompleteselect", function (event, ui)
	{
		var set_user_alternative_lid = ui.item.value;
		try
		{
			if (html_editor === 'summernote')
			{
				$('textarea#new_note').summernote('insertText', "Saken sendes til: " + ui.item.label);
			}
			else if (html_editor === 'ckeditor')
			{
				var temp = document.getElementById("new_note").value;
				if (temp)
				{
					temp = temp + "<br/>";
				}
				CKEDITOR.instances['new_note'].insertText(temp + "Saken sendes til: " + ui.item.label);

			}
			else
			{
				var temp = document.getElementById("new_note").value;
				if (temp)
				{
					temp = temp + "\n";
				}
				document.getElementById("new_note").value = temp + "Saken sendes til: " + ui.item.label;
			}
		}
		catch (err)
		{
		}

		if (set_user_alternative_lid)
		{
			try
			{
				get_user_info(set_user_alternative_lid);
			}
			catch (err)
			{
			}
		}
	});
});
function populateTableChkAssignee(on_behalf_of_lid, selection)
{
	var oArgs = {
		menuaction: 'helpdesk.uitts.custom_ajax',
		method: 'get_reverse_assignee',
		acl_location: '.ticket',
		on_behalf_of_lid: on_behalf_of_lid
	};
	var requestUrl = phpGWLink('index.php', oArgs, true);
	var container = 'set_user_container';
	var colDefs =
	[{label: '',
			object: [{type: 'input', attrs: [
						{name: 'type', value: 'radio'},
						{name: 'name', value: 'values[set_user_id]'},
						{name: 'class', value: 'chkRegulations'}
					]}],
			value: 'id',
			checked: selection},
		{key: 'name', label: lang['Name']},
		{key: 'stilling', label: lang['stilling']},
		{key: 'office', label: lang['office']}
	];
	populateTableAssignee(requestUrl, container, colDefs);
}

function populateTableAssignee(requestUrl, container, colDefs)
{
	createTable(container, requestUrl, colDefs, 'results', 'pure-table pure-table-bordered pure-custom');
}
