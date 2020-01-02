
var FormatterCenter = function (key, oData)
{

	return "<center>" + oData[key] + "</center>";
};

this.confirm_session = function (action)
{
	if (action == 'save' || action == 'apply' || action === 'external_communication')
	{
		conf = {
			modules: 'date, security, file',
			validateOnBlur: false,
			scrollToTopOnError: true,
			errorMessagePosition: 'top',
			language: validateLanguage
		};
		var test = $('form').isValid(validateLanguage, conf);
		if (!test)
		{
			return;
		}
	}

	if ($("#send_email").prop("checked") == true)
	{
		if (!confirm("Vil du sende epost?\n\"Cancel\" vil lagre posten uten varsling"))
		{
			$("#send_email").prop("checked", false);
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


function SmsCountKeyUp(maxChar)
{
	var msg = document.getElementsByName("values[response_text]")[0];
	var left = document.forms.form.charNumberLeftOutput;
	var smsLenLeft = maxChar - msg.value.length;
	if (smsLenLeft >= 0)
	{
		left.value = smsLenLeft;
	}
	else
	{
		var msgMaxLen = maxChar;
		left.value = 0;
		msg.value = msg.value.substring(0, msgMaxLen);
	}
}

function SmsCountKeyDown(maxChar)
{
	var msg = document.getElementsByName("values[response_text]")[0];
	var left = document.forms.form.charNumberLeftOutput;
	var smsLenLeft = maxChar - msg.value.length;
	if (smsLenLeft >= 0)
	{
		left.value = smsLenLeft;
	}
	else
	{
		var msgMaxLen = maxChar;
		left.value = 0;
		msg.value = msg.value.substring(0, msgMaxLen);
	}
}

this.fileuploader = function ()
{
	var sUrl = phpGWLink('index.php', multi_upload_parans);
	TINY.box.show({iframe: sUrl, boxid: 'frameless', width: 750, height: 450, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true,
		close: true,
		closejs: function ()
		{
			refresh_files()
		}
	});
};

this.refresh_files = function ()
{
	base_java_url['action'] = 'get_files';
	var oArgs = base_java_url;
	var strURL = phpGWLink('index.php', oArgs, true);
	JqueryPortico.updateinlineTableHelper(oTable2, strURL);
};

$(function ()
{
	$('#paste_image_data').pastableNonInputable();

	$('#paste_image_data').on('pasteImage', function (ev, data)
	{
//		console.log(data);
		$('<div class="preview_image">image: ' + data.width + ' x ' + data.height + '<img src="' + data.dataURL + '" ></div>').insertAfter(this);

		$('#pasted_image').val(data.dataURL);
		setTimeout(function ()
		{
			upload_canvas();
		}, 500);

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



upload_canvas = function ()
{
	var image_data = $('#pasted_image').val();

	var oArgs = {
		menuaction: 'helpdesk.uitts.upload_clip',
		id: $('#id').val()
	};
	var strURL = phpGWLink('index.php', oArgs, true);

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: strURL,
		data: {pasted_image: image_data},
		success: function (data)
		{
			if (data != null)
			{
				if (data.status == 'ok')
				{
					$('.preview_image').hide();
					refresh_files();
				}
				else
				{
					alert(data.message);
				}
			}
		},
		failure: function (o)
		{
		}
//		,timeout: 5000
	});

};

$(document).ready(function ()
{

	$("#publish_text").change(function ()
	{
		if ($(this).prop("checked") == true)
		{
			$("#send_email").prop("checked", true);
		}
		else
		{
			$("#send_email").prop("checked", false);
		}
	});

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

});

var oArgs = {
	menuaction: 'helpdesk.uitts.get_on_behalf_of',
	custom_method: true,
	method: 'get_on_behalf_of',
	acl_location: '.ticket'
};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'set_user_name', 'set_user_lid', 'set_user_container');

JqueryPortico.autocompleteHelper(phpGWLink('index.php',
{
	menuaction: 'helpdesk.uitts.get_on_behalf_of',
	custom_method: true,
	method: 'get_on_behalf_of',
	acl_location: '.ticket'
}, true),
	'set_notify_name', 'set_notify_lid', 'set_notify_container');


$(window).on('load', function ()
{
	$("#set_notify_name").on("autocompleteselect", function (event, ui)
	{
		var set_notify_lid = ui.item.value;

		if (set_notify_lid)
		{
			try
			{
				set_notify(set_notify_lid);
				JqueryPortico.updateinlineTableHelper('datatable-container_6');
			}
			catch (err)
			{
			}
		}
	});
});
