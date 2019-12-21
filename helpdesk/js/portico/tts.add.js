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
//					var canvas = document.getElementById("my_canvas");
//					var image_data = canvas.toDataURL('image/png');
//					$('#pasted_image').val(image_data);

					try
					{
						validate_submit();
					}
					catch (e)
					{
						document.form.submit();
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

	document.getElementById('file_input').onchange = function ()
	{
		$('.files_to_upload').remove();

		var file;
		var file_size;
		if (this.files.length > 1)
		{
			for (var i = 0; i < this.files.length; i++)
			{
				file = this.files[i];
				file_size = file.size / (1024 * 1024);
				$('<div class="files_to_upload">File: ' + file.name + ' size: ' + file_size.toFixed(2) + ' MB</div>').insertAfter(this);
			}
		}
	};



// ************************ Drag and drop ***************** //
	let dropArea = document.getElementById("drop-area")
	let fileInput = document.getElementById('file_input');
// Prevent default drag behaviors
		;
	['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
		dropArea.addEventListener(eventName, preventDefaults, false)
		document.body.addEventListener(eventName, preventDefaults, false)
	})

// Highlight drop area when item is dragged over it
		;
	['dragenter', 'dragover'].forEach(eventName => {
		dropArea.addEventListener(eventName, highlight, false)
	})

		;
	['dragleave', 'drop'].forEach(eventName => {
		dropArea.addEventListener(eventName, unhighlight, false)
	})

// Handle dropped files
	dropArea.addEventListener('drop', handleDrop, false)

	function preventDefaults(e)
	{
		e.preventDefault()
		e.stopPropagation()
	}

	function highlight(e)
	{
		dropArea.classList.add('highlight')
	}

	function unhighlight(e)
	{
		dropArea.classList.remove('active')
	}

	function handleDrop(e)
	{
		var dt = e.dataTransfer
		var files = dt.files

		handleFiles(files)
	}

	function handleFiles(files)
	{
		fileInput.files = files;

		files = [...files]
	
		$('.files_to_upload').remove();

		var file;
		var file_size;
		if (files.length > 1)
		{
			for (var i = 0; i < files.length; i++)
			{
				file = files[i];
				file_size = file.size / (1024 * 1024);
				$('<div class="files_to_upload">File: ' + file.name + ' size: ' + file_size.toFixed(2) + ' MB</div>').insertAfter(fileInput);
			}
		}
	}

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
