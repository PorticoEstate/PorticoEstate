//(c) Dave Hall 2005, GPL applies
var eventsLocked = true;
var activeElement = '';
var oApplication;
var oButtons;
var oAccounts;
var oSummary;
var oMessage;
var oScreenEvents;

/**
* Convert URL in text to links
*/
function autoLinks(strText)
{
	var regexp=/(https?\:\/\/[_.a-z0-9-]+\.[a-z0-9\/_:@=.+&\;\?,##%&~-]*[^.|\'|\# |!|\(|?|,| |>|<|\)])/ig;
	strText = strText.replace(regexp, '<a href="javascript:openHttpHref(\'$1\');">$1</a>');

	regexp=/(((ftp|irc|nntp)+\:\/\/)[_.a-z0-9-]+\.[a-z0-9\/_:@=.+&\;\?,##%&~-]*[^.|\'|\# |!|\(|?|,| |>|<|\)])/ig;
	strText = strText.replace(regexp, '<a href="$1" target="_blank">$1</a>');

	regexp=/([_.a-z0-9-]+@[_.a-z0-9-]+\.[a-z]{2,3})/ig;
	strText = strText.replace(regexp, '<a href="javascript:oApplication.compose(\'$1\')">$1</a>');
	
	return strText;
}

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

function hideFolderMenu()
{
	oFolderMenu.hideMenu();
}

//lifted from sortTable demo
function addClassName(el, sClassName)
{
	var s = el.className;
	if ( !s )
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

/**
* Opens a HTTP href in a way that stops session information disclosure
*/
function openHttpHref(strHref)
{
	//window.alert('escape(' + strHref + ") == \n" + escape(strHref));
	strHref = escape(strHref);
	window.open(oApplication.strBaseURL + '../redirect.php?go=' + strHref);
}

//lifted from sortTable demo
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
* Apply row_[on|off] CSS class to alternating table rows
*
* @param oRows the rows to "stripe"
*/
this.stripe = function(oRows)
{
	var l = oRows.length;
	for (var i = 0; i < l; i++)
	{
		removeClassName(oRows[i], i % 2 ? 'row_on' : 'row_off');
		addClassName(oRows[i], i % 2 ? 'row_off' : 'row_on');
	}
}

function screenEvents()
{
	var oThis = this;
	
	this.init = function()
	{
		this._addListeners();
	}
	
	this.keypressed = function(e)
	{
		if (!e) {
			key = window.event;
			e.which = e.keyCode;
		}
		
		switch (e.which)
		{
			case 27: //ESC
			{
				oThis.ESC();
				break;
			}
				
			case 33: //page up
			{
				oThis.pageUp();
				break;
			}
				
			case 34: //page down
			{
				oThis.pageDown();
				break;
			}
				
			case 38: // down
			{
				oThis.up();
				break;
			}
				
			case 40: // up
			{
				oThis.down();
				break;
			}
				
			case 46: //delete
			{
				oThis.del();
				break;
			}
				
			case 65: //a
			{
				if(e.shiftKey)
				{
					oThis.shiftA();
				}
				break;
			}
			
			case 67: //c
			{
				if( e.shiftKey )
				{
					oThis.shiftC();
				}
				break;
			}

			case 68: //d
			{
				if( e.shiftKey )
				{
					oThis.shiftD();
				}
				break;
			}

			case 69: //e
			{
				if( e.shiftKey )
				{
					oThis.shiftE();
				}
				break;
			}
			
			case 70: //f
			{
				if( e.shiftKey )
				{
					oThis.shiftF();
				}
				break;
			}
			
			case 72: //h
			{
				if( e.shiftKey )
				{
					oThis.shiftH();
				}
				break;
			}
			
			case 77: //m
			{
				if( e.shiftKey )
				{
					oThis.shiftM();
				}
				break;
			}
			
			case 78: //n
			{
				if( e.shiftKey )
				{
					oThis.shiftN();
				}
				break;
			}
			
			case 79: //o
			{
				if( e.shiftKey )
				{
					oThis.shiftO();
				}
				break;
			}

			case 80: //p
			{
				if( e.shiftKey )
				{
					oThis.shiftP();
				}
				break;
			}

			case 82: //r
			{
				if( e.shiftKey )
				{
					oThis.shiftR();
				}
				break;
			}
				
			case 83: //s
			{
				if( e.shiftKey )
				{
					oThis.shiftS();
				}
				break;
			}	
		}
	}

	this.ESC = function()
	{
		window.alert('[ESC] pressed');
	}

	this.shiftA = function()
	{
		window.alert('[shift] + a pressed')
	}

	this.shiftB = function()
	{
		window.alert('[shift] + b pressed')
	}
	
	this.shiftC = function()
	{
		window.alert('[shift] + c pressed')
	}
	
	this.shiftD = function()
	{
		window.alert('[shift] + d pressed')
	}
	
	this.shiftE = function()
	{
		window.alert('[shift] + e pressed')
	}
	
	this.shiftF = function()
	{
		window.alert('[shift] + f pressed')
	}
	
	this.shiftH = function()
	{
		window.location.reload();
		window.alert('[shift] + h pressed')
	}
	
	this.shiftM = function()
	{
		window.alert('[shift] + m pressed')
	}
	
	this.shiftN = function()
	{
		window.alert('[shift] + n pressed')
	}
	
	this.shiftO = function()
	{
		window.alert('[shift] + o pressed')
	}
	
	this.shiftP = function()
	{
		window.alert('[shift] + p pressed')
	}
	
	this.shiftS = function()
	{
		window.alert('[shift] + s pressed')
	}


	this.del = function()
	{
		window.alert('del pressed');
	}

	this.down = function (pageKey)
	{
		window.alert('Down pressed');
	}

	this.pageDown = function ()
	{
		window.alert('page down pressed');
	}

	this.pageUp = function()
	{
		window.alert('page up pressed');
	}

	this.up = function(pageKey)
	{
		window.alert('up pressed')
	}

	
	this._addListeners = function()
	{
		if(document.addEventListener)
		{
			document.addEventListener('keydown', this.keypressed, true);
		}
		else if(document.oTable.attachEvent)
		{
			this.oTable.attachEvent('onkeydown', this.keypressed);
		}
	}
}
