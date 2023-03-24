var pendingList = 0;
var redirect_action;
var file_count = 0;

this.confirm_session = function (action)
{
	if (action == 'cancel')
	{
		window.location.href = phpGWLink('index.php', {menuaction: 'property.uidocument.index'});
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
//		$(this).prop('disabled', true);
	});

	var oArgs = {menuaction: 'property.bocommon.confirm_session'};
	var strURL = phpGWLink('index.php', oArgs, true);
	$.ajax({
		type: 'GET',
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
						var oArgs = {menuaction: 'property.uidocument.edit',
							id: id,
							tab: 'general'
						};
					}
					else
					{
						var oArgs = {menuaction: 'property.uidocument.index'};
					}

					redirect_action = phpGWLink('index.php', oArgs);
					if (pendingList === 0)
					{
						window.location.href = data.redirect_link.replace(/&amp;/g, "&");
						;
//						window.location.href = redirect_action;
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

set_tab = function (active_tab)
{
	conf = {
		//	modules: 'date, security, file',
		validateOnBlur: false,
		scrollToTopOnError: true,
		errorMessagePosition: 'top',
		validateHiddenInputs: true
	};
};


$(document).ready(function ()
{

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
			phpGWLink('index.php', {menuaction: 'property.uidocument.handle_multi_upload_file', id: id})
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
		limitConcurrentUploads: 4,
		maxChunkSize: 8388000,
		dataType: "json",
		add: function (e, data)
		{
			if (pendingList === 0)
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
					$('#document_name').val(data.files[0].name);
					$('#files_count').html(pendingList);
					$('#fileupload').prop('disabled', true);

				});
			}

		},
		progress: function (e, data)
		{
			var progress = parseInt((data.loaded / data.total) * 100, 10);
			data.context.css("background-position-x", 100 - progress + "%");
		},
		done: function (e, data)
		{
			var error = false;
			file_count++;

			var result = JSON.parse(data.result);

			if (result.files[0].error)
			{
				data.context
					.removeClass("file")
					.addClass("error")
					.append($('<span>').text(' Error: ' + result.files[0].error));
				error = true;

			}
			else
			{
				data.context
					.addClass("done");
			}

			if (file_count === pendingList && !result.files[0].error)
			{
				window.location.href = redirect_action;
			}

			var element = document.getElementById('spinner');
			if (element)
			{
				element.parentNode.removeChild(element);
			}

			if (error)
			{
				$('#fileupload').prop('disabled', false);
				$('#fileupload').val('');
				pendingList = 0;
				$('#files_count').html('');
			}

		}
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
