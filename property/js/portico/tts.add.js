var pendingList = 0;
var redirect_action;
var file_count = 0;

this.confirm_session = function (action)
{
	if (action == 'cancel')
	{
		window.location.href = phpGWLink('index.php', {menuaction: 'property.uitts.index'});
		return;
	}

	if (action == 'save' || action == 'apply')
	{
		conf = {
			modules: 'location, date, security, file',
			validateOnBlur: false,
			scrollToTopOnError: true,
			errorMessagePosition: 'top'
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
					JqueryPortico.lightboxlogin();//defined in common.js
				}
				else
				{
					var form = document.getElementById('form');
					$('<div id="spinner" class="d-flex align-items-center">')
					.append($('<strong>').text('Lagrer...'))
					.append($('<div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>')).insertAfter(form);
					window.scrollBy(0, 100); //

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
					if (action == 'apply')
					{
						var oArgs = {menuaction: 'property.uitts.view',
							id: id,
							tab: 'general'
						};
					}
					else
					{
						var oArgs = {menuaction: 'property.uitts.index'	};
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
					if(element)
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
			phpGWLink('index.php', {menuaction: 'property.uitts.handle_multi_upload_file', id: id})
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

set_tab = function ()
{
	//Dummy
};

window.on_location_updated = function (location_code)
{
	location_code = location_code || $("#loc1").val();

	var oArgs = {menuaction: 'property.uilocation.get_location_exception', location_code: location_code};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: requestUrl,
		success: function (data)
		{
			$("#message").html('');

			if (data != null)
			{
				var htmlString = '';
				var exceptions = data.location_exception;
				$.each(exceptions, function (k, v)
				{
					if(v.alert_vendor == 1)
					{
						htmlString += "<div class=\"error\">";
					}
					else
					{
						htmlString += "<div class=\"msg_good\">";
					}
					htmlString += v.severity + ": " + v.category_text;
					if(v.location_descr)
					{
						htmlString += "<br/>" + v.location_descr;
					}
					htmlString += '</div>';

				});
				$("#message").html(htmlString);
			}
		}
	});
};
