/**
 * ====================================================================
 * About
 * ====================================================================
 * Communik8r Menu Handler
 * @version 0.9.17.500
 * @author: Dave Hall skwashd at phpgroupware.org
 *
 * ====================================================================
 * Licence
 * ====================================================================
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 or
 * the GNU Lesser General Public License version 2.1 as published by
 * the Free Software Foundation (your choice of the two).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License or GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * or GNU Lesser General Public License along with this program; if not,
 * write to the Free Software Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 * or visit http://www.gnu.org
 *
 */
function Buttons(strHoldingDiv)
{
	this.oDiv = document.getElementById(strHoldingDiv);
	this.oXSLDoc = '';
	this.oXMLDoc = null;
	this.strHoldingDiv = strHoldingDiv;
	var self = this;

	/**
	 * Prepare to transform XML
	 * @internal load's onreadystatechange event handler
	 */
	this.transform = function()
	{
		if ( self.oXSLDoc.readyState == 4 )
		{
			var oXSLTProc = new XSLTProcessor();
			oXSLTProc.importStylesheet(self.oXSLDoc);
			Sarissa.updateContentFromURI(oApplication.strBaseURL + 'buttons' + oApplication.strGET, self.oDiv, oXSLTProc, self._addListeners());
		}
	}
	this.init();
}

/**
 * Handle button onClick Event
 *
 * @param Event e the click event
 */
Buttons.prototype.clicked = function(e)
{
	if(eventsLocked)
	{
		return false;
	}

	eventsLocked = true;

	if( !e ) //Fix Broken IE
	{
		var e = window.event;
	}

	if( e.target )
	{
		oTarget = e.target;
	}
	else if( e.srcElement )
	{
		oTarget = e.srcElement;
	}

	if( oTarget.id
			&& oTarget.id.indexOf('button_') == 0 
			&& oTarget.className.indexOf('inactive') == -1 )
	{
		strButton = oTarget.id.substr(7, oTarget.id.length);

		switch (strButton)
		{
			case 'new':
				{
					oApplication.new_message();
					break;
				}

			case 'refresh':
				{
					oApplication.refresh();
					break;
				}

			case 'reply':
				{
					oApplication.reply();
					break;
				}

			case 'reply_to_all':
				{
					oApplication.reply_to_all();
					break;
				}

			case 'forward':
				{
					oApplication.forward();
					break;
				}

			case 'print':
				{
					oApplication.print();
					break;
				}

			case 'delete':
				{
					oApplication.delete_msg(); //delete is a reserved word
					break;
				}

			default:
				{
					window.alert('clicked: ' + strButton);
					break;
				}
		}

		oTarget.blur();
		if( window.event ) //Crappy IE
		{
			e.cancelBubble = true;
		}
		else //W3C :)
		{
			e.stopPropagation();
		}
	}

	eventsLocked = false;
}

/**
 * Disable a button
 *
 * @internal will not work for "new" or "refresh"
 * @param object oBtn button to disable
 */
Buttons.prototype.disable = function(oBtn)
{
	if ( oBtn.id == 'button_new' || oBtn.id == 'button_refresh')
	{
		return false;
	}

	oBtn.disabled = true;
	addClassName(oBtn, 'inactive');
}

/**
 * Disable all buttons
 */
Buttons.prototype.disableAll = function()
{
	var oBtns = this.oDiv.getElementsByTagName('button');
	for( var i = 0; i < oBtns.length; i++ )
	{
		this.disable( oBtns.item(i) );
	}
}

/**
 * Enable a button
 *
 * @param object oBtn the button to activate
 */
Buttons.prototype.enable = function(oBtn)
{
	oBtn.disabled = false;
	removeClassName(oBtn, 'inactive');
}

/**
 * Enable all buttons
 */
Buttons.prototype.enableAll = function()
{
	var oBtns = this.oDiv.getElementsByTagName('button');
	for( var i = 0; i < oBtns.length; i++ )
	{
		this.enable( oBtns.item(i) );
	}
}

/**
 * @constructor
 */
Buttons.prototype.init = function()
{
	this.load();
}

/**
 * Load the XML for the buttons
 */
Buttons.prototype.load = function()
{
	if ( typeof(this.oXSLDoc) == 'object')
	{
		this.transform();
	}
	else
	{
		this.oXSLDoc = Sarissa.getDomDocument();
		this.oXSLDoc.onreadystatechange = this.transform;
		this.oXSLDoc.load(oApplication.strBaseURL + 'xsl/buttons');
	}
}

/**
 * Add Event listener to buttons
 */
Buttons.prototype._addListeners = function()
{
	this.oDiv = document.getElementById(this.strHoldingDiv);
	var oBtns = this.oDiv.getElementsByTagName('button');
	for( var i = 0; i < oBtns.length; i++ )
	{
		if( this.oDiv.addEventListener )
		{
			oBtns.item(i).addEventListener('click', self.clicked, false);
		}
		else if( this.oDiv.attachEvent )
		{
			oBtns.item(i).attachEvent('onclick', self.clicked);
		}
		self.disable( oBtns.item(i) );
	}
	eventsLocked = false;
	//oAccounts.load();
}
