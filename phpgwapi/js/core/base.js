/**
 * Generic sitemgr JS functions
 *
 * @author Dave Hall skwashd at phpgroupware.org
 * @license GPL
 */

/**
 * Emulate phpGW's link function
 *
 * @param String strURL target URL
 * @param Object oArgs Query String args as associate array object
 * @param bool bAsJSON ask that the request be returned as JSON (experimental feature)
 * @returns String URL
 */
function phpGWLink(strURL, oArgs, bAsJSON)
{
	var arURLParts = strBaseURL.split('?');
	var strNewURL = arURLParts[0] + strURL + '?';

	if (oArgs == null)
	{
		oArgs = new Object();
	}

	for (obj in oArgs)
	{
		strNewURL += obj + '=' + oArgs[obj] + '&';
	}
	strNewURL += arURLParts[1];

	if (bAsJSON)
	{
		strNewURL += '&phpgw_return_as=json';
	}
	return strNewURL;
}

/**
 * Disable a button
 */
function buttonDisable(buttonName, hide)
{
	document.getElementById(buttonName).disabled = true;
	if (hide)
	{
		document.getElementById(buttonName).style.display = 'none';
	}
}

/**
 * Enable a button
 */
function buttonEnable(buttonName, show)
{
	document.getElementById(buttonName).disabled = false;
	if (show)
	{
		document.getElementById(buttonName).style.display = 'inline';
	}
}

/**
 * Find the absolute horizontal position of an element
 *
 * @param Object obj HTML Element to find location of
 * @returns int the horizontal position in pixels
 */
function findPosX(obj)
{
	var curleft = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curleft += obj.offsetLeft
			obj = obj.offsetParent;
		}
	}
	else if (obj.x)
	{
		curleft += obj.x;
	}
	return curleft;
}

/**
 * Find the absolute vertical position of an element
 *
 * @param Object obj HTML Element to find location of
 * @returns int the vertical position in pixels
 */
function findPosY(obj)
{
	var curtop = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curtop += obj.offsetTop
			obj = obj.offsetParent;
		}
	}
	else if (obj.y)
	{
		curtop += obj.y;
	}
	return curtop;
}

/**
 * Get the height of the window
 *
 * @return int the window height in pixels
 */
function getWindowHeight()
{
	if (self.innerHeight) // all except Explorer
	{
		return self.innerHeight;
	}
	else if (document.documentElement && document.documentElement.clientHeight)	// Explorer 6 Strict Mode
	{
		return document.documentElement.clientHeight;
	}
	else if (document.body) // other Explorers
	{
		return document.body.clientHeight;
	}
	return 600;
}

/**
 * Get the width of the window
 *
 * @return int the window width in pixels
 */
function getWindowWidth()
{
	if (self.innerWidth) // all except Explorer
	{
		return self.innerWidth;
	}
	else if (document.documentElement && document.documentElement.clientWidth)	// Explorer 6 Strict Mode
	{
		return document.documentElement.clientWidth;
	}
	else if (document.body) // other Explorers
	{
		return document.body.clientWidth;
	}
	return 800;
}

/**
 * Move a HTML Element
 */
function moveObj(strID, iStartX, iStartY, iEndX, iEndY, iSteps)
{
	var oElm = document.getElementById(strID);
	if (iSteps == 0)
	{
		oElm.parentNode.removeChild(oElm);
	}

	var iInterval = 42; // Math.round(1000/24);
	if (oElm)
	{
		iX = iEndX + Math.round(((iStartX - iEndX) / 24) * iSteps);
		oElm.style.left = iX + 'px';

		iY = iEndY + Math.round(((iStartY - iEndY) / 24) * iSteps);
		oElm.style.top = iY + 'px';

		--iSteps;
		setTimeout('moveObj("' + strID + '", ' + iStartX + ', ' + iStartY + ', ' + iEndX + ', ' + iEndY + ', ' + iSteps + ')', iInterval);
	}
}

function addClassName(el, sClassName)
{
	var s = el.className;
	if (!s)
	{
		s = '';
	}
	var p = s.split(' ');
	var l = p.length;
	for (var i = 0; i < l; i++)
	{
		if (p[i] == sClassName)
		{
			return;
		}
	}
	p[p.length] = sClassName;
	el.className = p.join(' ');

}

function removeClassName(el, sClassName)
{
	var s = el.className;
	var p = s.split(' ');
	var np = [];
	var l = p.length;
	var j = 0;
	for (var i = 0; i < l; i++)
	{
		if (p[i] != sClassName)
		{
			np[j++] = p[i];
		}
	}
	el.className = np.join(' ');
}

/**
 * Open a new window
 */
var phpgw_popup;
function openwindow(url, h, w)
{
	if (!h)
	{
		h = 700;
	}

	if (!w)
	{
		w = 600;
	}

	/*if ( phpgw_popup )
	{
		if ( phpgw_popup.closed )
		{
			phpgw_popup.stop;
			phpgw_popup.close;
		}
	}*/
	phpgw_popup = window.open(url, "pageWindow", "left=50,top=100,width=" + h + ",height=" + w + ",location=no,menubar=no,directories=no,toolbar=no,scrollbars=yes,resizable=yes,status=yes");
	if (phpgw_popup.opener == null)
	{
		phpgw_popup.opener = window;
	}
}

/**
 * Delete attribute value - if not used
 * @param {type} id
 * @param {type} location_id
 * @param {type} attribute_id
 * @returns {undefined}
 */
function deleteValueFromCustomAttribute(select_id, location_id, attribute_id)
{
	var choice_id = $("#" + select_id + " option:selected").val();

	if(!choice_id)
	{
		alert('None selected');
	}

	var oArgs = {
		menuaction: 'property.uiadmin_entity.delete_choice_value',
		location_id: location_id,
		attribute_id: attribute_id,
		choice_id: choice_id
	};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function ()
	{
		if (this.readyState == 4 && this.status == 200)
		{
			var data = JSON.parse(this.responseText);

			if (data.status == 'ok')
			{
				$("#" + select_id + " option:selected").remove();
			}
			else
			{
				alert(data.message);
			}
		}
	};
	xmlhttp.open("POST", requestUrl, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	var params = '';
	xmlhttp.send(params);
}

var alertify;

/**
 * Open a prompt for input
 * @param {type} id
 * @param {type} location_id
 * @param {type} attribute_id
 * @param {type} input_text
 * @param {type} lang_new_value
 * @returns {undefined}
 */

function addNewValueToCustomAttribute(id, location_id, attribute_id, input_text, lang_new_value)
{

	var oArgs = {
		menuaction: 'property.uiadmin_entity.add_choice_value',
		location_id: location_id,
		attribute_id: attribute_id
	};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	//	var new_value = prompt(input_text, "");

	/*
	 * @title {String or DOMElement} The dialog title.
	 * @message {String or DOMElement} The dialog contents.
	 * @value {String} The default input value.
	 * @onok {Function} Invoked when the user clicks OK button.
	 * @oncancel {Function} Invoked when the user clicks Cancel button or closes the dialog.
	 *
	 * alertify.prompt(title, message, value, onok, oncancel);
	 *
	 */
	alertify.prompt( input_text, lang_new_value, ''
               , function(evt, value)
			   {
					var new_value = value;
					if (new_value !== null && new_value !== "")
					{
						var xmlhttp = new XMLHttpRequest();
						xmlhttp.onreadystatechange = function ()
						{
							if (this.readyState == 4 && this.status == 200)
							{
								var data = JSON.parse(this.responseText);

								if (data.status == 'ok' && data.choice_id)
								{
									alertify.success('You entered: ' + value);
									var select = document.getElementById(id);
									var option = document.createElement("option");
									option.text = new_value;
									option.value = data.choice_id;
									select.add(option, select[1]);
									select.selectedIndex = "1";
								}
								else
								{
									alertify.error('Error');
								}
							}
						};
						xmlhttp.open("POST", requestUrl, true);
						xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
						var params = 'new_value=' + new_value;
						xmlhttp.send(params);
					}
					else
					{
						alertify.error('Cancel');
					}
				}
               , function() { alertify.error('Cancel') });

}