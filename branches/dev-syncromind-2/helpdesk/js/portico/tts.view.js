
var FormatterCenter = function (key, oData)
{

	return "<center>" + oData[key] + "</center>";
};

this.confirm_session = function (action)
{
	if (action == 'save' || action == 'apply')
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
	var sUrl = phpGWLink('index.php', fileuploader_action);
	TINY.box.show({iframe: sUrl, boxid: "frameless", width: 750, height: 450, fixed: false, maskid: "darkmask", maskopacity: 40, mask: true, animate: true, close: true}); //refresh_files is called after upload
};

this.refresh_files = function ()
{
	base_java_url['action'] = 'get_files';
	var oArgs = base_java_url;
	var strURL = phpGWLink('index.php', oArgs, true);
	JqueryPortico.updateinlineTableHelper(oTable2, strURL);
};

upload_canvas = function ()
{
	var canvas = document.getElementById("my_canvas");
	var image_data = canvas.toDataURL('image/png');
//	$('#pasted_image').val(image_data);
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
				if(data.status == 'ok')
				{
					var context = canvas.getContext('2d');
//					context.clearRect(0, 0, canvas.width, canvas.height);
					context.clearRect(0, 0, canvas.width, canvas.height);
					canvas.height = 100;
					canvas.width = 100;
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


	var CLIPBOARD = new CLIPBOARD_CLASS("my_canvas", true);

	/**
	 * image pasting into canvas
	 *
	 * @param string canvas_id canvas id
	 * @param boolean autoresize if canvas will be resized
	 */
	function CLIPBOARD_CLASS(canvas_id, autoresize)
	{
		var canvas = document.getElementById(canvas_id);
		var _self = this;
		var ctx = document.getElementById(canvas_id).getContext("2d");
		var ctrl_pressed = false;
		var reading_dom = false;
		var text_top = 15;
		var pasteCatcher;
		var paste_mode;

		//handlers
		document.addEventListener('keydown', function (e)
		{
			_self.on_keyboard_action(e);
		}, false); //firefox fix
		document.addEventListener('keyup', function (e)
		{
			_self.on_keyboardup_action(e);
		}, false); //firefox fix
		document.addEventListener('paste', function (e)
		{
			_self.paste_auto(e);
		}, false); //official paste handler

		//constructor - prepare
		this.init = function ()
		{
			//if using auto
			if (window.Clipboard)
			{
				return true;
			}

			pasteCatcher = document.createElement("div");
			pasteCatcher.setAttribute("id", "paste_ff");
			pasteCatcher.setAttribute("contenteditable", "");
			pasteCatcher.style.cssText = 'opacity:0;position:fixed;top:0px;left:0px;';
			pasteCatcher.style.marginLeft = "-20px";
			pasteCatcher.style.width = "10px";
			document.body.appendChild(pasteCatcher);
			document.getElementById('paste_ff').addEventListener('DOMSubtreeModified', function ()
			{
				if (paste_mode == 'auto' || ctrl_pressed == false)
				{
					return true;
				}
				//if paste handle failed - capture pasted object manually
				if (pasteCatcher.children.length == 1)
				{
					if (pasteCatcher.firstElementChild.src != undefined)
					{
						//image
						_self.paste_createImage(pasteCatcher.firstElementChild.src);
					}
				}
				//register cleanup after some time.
				setTimeout(function ()
				{
					pasteCatcher.innerHTML = '';
				}, 20);
			}, false);
		}();
		//default paste action
		this.paste_auto = function (e)
		{
			paste_mode = '';
			pasteCatcher.innerHTML = '';
			var plain_text_used = false;
			if (e.clipboardData)
			{
				var items = e.clipboardData.items;
				if (items)
				{
					paste_mode = 'auto';
					//access data directly
					for (var i = 0; i < items.length; i++)
					{
						if (items[i].type.indexOf("image") !== -1)
						{
							//image
							var blob = items[i].getAsFile();
							var URLObj = window.URL || window.webkitURL;
							var source = URLObj.createObjectURL(blob);
							this.paste_createImage(source);
						}
					}
					e.preventDefault();
				}
				else
				{
					//wait for DOMSubtreeModified event
					//https://bugzilla.mozilla.org/show_bug.cgi?id=891247
				}
			}
		};
		//on keyboard press -
		this.on_keyboard_action = function (event)
		{
			k = event.keyCode;
			//ctrl
			if (k == 17 || event.metaKey || event.ctrlKey)
			{
				if (ctrl_pressed == false)
				{
					ctrl_pressed = true;
				}
			}
			//c
			if (k == 86)
			{
				if (document.activeElement != undefined && document.activeElement.type == 'text')
				{
					//let user paste into some input
					return false;
				}

				if (ctrl_pressed == true && !window.Clipboard)
				{
					pasteCatcher.focus();
				}
			}
		};
		//on kaybord release
		this.on_keyboardup_action = function (event)
		{
			k = event.keyCode;
			//ctrl
			if (k == 17 || event.metaKey || event.ctrlKey || event.key == 'Meta')
			{
				ctrl_pressed = false;
			}
		};
		//draw image
		this.paste_createImage = function (source)
		{
			var pastedImage = new Image();
			pastedImage.onload = function ()
			{
				if (autoresize == true)
				{
					//resize canvas
					canvas.width = pastedImage.width;
					canvas.height = pastedImage.height;
				}
				else
				{
					//clear canvas
					ctx.clearRect(0, 0, canvas.width, canvas.height);
				}
				ctx.drawImage(pastedImage, 0, 0);
			};
			pastedImage.src = source;
			setTimeout(function ()
			{
				upload_canvas();
			}, 500);
		};
	}

});
