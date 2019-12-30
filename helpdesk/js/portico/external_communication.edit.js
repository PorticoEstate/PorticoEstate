var vendor_id = 0;
this.fetch_vendor_email = function ()
{
	if (document.getElementById('vendor_id').value)
	{
		base_java_url['vendor_id'] = document.getElementById('vendor_id').value;
	}

	if (document.getElementById('vendor_id').value != vendor_id)
	{
		base_java_url['action'] = 'get_vendor';
		base_java_url['field_name'] = 'mail_recipients';
		var oArgs = base_java_url;
		var strURL = phpGWLink('index.php', oArgs, true);
		JqueryPortico.updateinlineTableHelper(oTable1, strURL);
		vendor_id = document.getElementById('vendor_id').value;
	}
};

window.on_vendor_updated = function ()
{
	fetch_vendor_email();
};


this.preview = function (id)
{
	var oArgs = {menuaction: 'helpdesk.uiexternal_communication.view', id: id, preview_html: true};
	var strURL = phpGWLink('index.php', oArgs);
	Window1 = window.open(strURL, 'Search', "left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
};

$(window).on('load', function ()
{
	$.fn.insertAtCaret = function (myValue)
	{
		myValue = myValue.trim();
		CKEDITOR.instances['new_note'].insertText(myValue);
	};

});

$(document).ready(function ()
{
	var do_preview = $("#do_preview").val();

	if (do_preview)
	{
		preview(do_preview);
	}
});

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
		id: $('#ticket_id').val()
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
