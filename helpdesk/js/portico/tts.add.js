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
			language: validateLanguage,
			validateHiddenInputs: true,
		};
		var test = $('form').isValid(validateLanguage, conf);
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

	var form = document.getElementById('form');
//	form.style.display = 'none';
//	var processing = document.createElement('span');
//	processing.appendChild(document.createTextNode('processing ...'));
//	form.parentNode.insertBefore(processing, form);

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
					JqueryPortico.lightboxlogin();//defined in common.js
				}
				else
				{
					document.getElementById(action).value = 1;

					try
					{
						validate_submit();
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
					sendAllFiles(id);

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

					var redirect_action = phpGWLink('index.php', oArgs);
//					window.location.href = redirect_action;
				}
				else
				{
					alert(data.message);
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
		errorMessagePosition: 'inline'
	};

	setTimeout(function ()
	{
		$('form').isValid(validateLanguage, conf_on_load, true);
	}, 500);

	var oArgs = {menuaction: 'helpdesk.uitts.handle_multi_upload_file'};
	var multi_upload_action = phpGWLink('index.php', oArgs, true);

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


	var pendingList = [];

	sendAllFiles = function (id)
	{
		pendingList.forEach(function (data)
		{
			data.formData = {id: id};
			data.submit().done(function (data, status)
			{

				$.each(data.files, function (index, file)
				{
					if (typeof file.error != 'undefined' && file.error)
					{
						alert(file.name + ': ' + file.error);
					}
				});

//				console.log(data);
			});

		});
		pendingList = [];
	};


	$('#fileupload').fileupload({
		dropZone: $('#drop-area'),
		uploadTemplateId: null,
		downloadTemplateId: null,
		// Uncomment the following to send cross-domain cookies:
		//xhrFields: {withCredentials: true},
		url: multi_upload_action,
		autoUpload: false,
		add: function (e, data)
		{
			$.each(data.files, function (index, file)
			{
				var row = $('<div class="template-upload">' +
					'<div class="table-cell">' +
					'<div class="name">' + file.name + '</div>' +
					'<div class="error"></div>' +
					'</div>' +
					'<div class="table-cell">' +
					'<div class="size">Processing...</div>' +
					'</div>' +
					'<div class="table-cell">' +
					'<div class="progress" style="width: 100px;"></div>' +
					'</div>' +
					'</div>');

				var file_size = formatFileSize(file.size);

				row.find('.size').text(file_size);
				if (file.error)
				{
					row.find('.error').text(file.error);
				}

				data.context = row.appendTo($(".content_upload_download"));
			});


			pendingList.push(data);
		},
		limitConcurrentUploads: 4,
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



	var Allowed_Methods = [];
	$(function ()
	{
		'use strict';

		$('#fileupload')
			.bind('fileuploadcompleted', function (e, data)
			{
				$("#files-count").html(data.result.num_files);
			});

		$('#fileupload')
			.bind('fileuploaddestroyed', function (e, data)
			{
				var n = 0;
				$(".template-download").each(function (i)
				{
					n++;
				});
				$("#files-count").html(n);
			});
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
	$("#set_on_behalf_of_name").on("autocompleteselect", function (event, ui)
	{
		//	console.log(ui);
		var on_behalf_of_lid = ui.item.value;
		try
		{
			var temp = document.getElementById("new_note").value;
			if (temp)
			{
				temp = temp + "\n";
			}
			document.getElementById("new_note").value = temp + "Saken gjelder: " + ui.item.label;

			var conf = {
				modules: 'location, date, security, file',
				validateOnBlur: false,
				scrollToTopOnError: false,
				//		errorMessagePosition: 'top',
				language: validateLanguage
			};

			$('form').isValid(validateLanguage, conf);
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
			var temp = document.getElementById("new_note").value;
			if (temp)
			{
				temp = temp + "\n";
			}
			document.getElementById("new_note").value = temp + "Saken sendes til: " + ui.item.label;
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
