/**
 * ====================================================================
 * About
 * ====================================================================
 * Communik8r Summary Pane Handler
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
function Summary(strTargetID)
{
	this.arInfo = null;
	this.bHideDeleted = true;
	this.strCurSelect = strCurSelection;
	this.strParentDivID = strTargetID;
	this.oTable = null;
	this.oXML = null;
	this.oXSL = null;
	var self = this;
	
	/**
	* @constructor
	*/
	this.init = function()
	{
		this.oTable = document.createElement('table');
		this.oTable.id = 'summary_tbl';
		document.getElementById(this.strParentDivID).appendChild(this.oTable);
		if ( this.strCurSelect )
		{
			this.setList(this.strCurSelect);
		}
	}

	/**
	* Click event handler
	*/
	this.clicked = function(e)
	{
		if(eventsLocked)
		{
			return false;
		}
		
		eventsLocked = true;

		activeElement = 'summary';
		
		if( window.event ) //Fix Broken IE
		{
			var e = window.event;
		}

		if ( e.currentTarget ) //DOM
		{
			oTarget = e.currentTarget;
		}
		else if ( e.srcElement ) //IE
		{
			var oTmp = e.srcElement;
			while ( ! ( oTmp.tagName
				&& oTmp.tagName.toUpperCase() == 'TR') )
			{
				oTmp = oTmp.parentNode;
			}
			oTarget = oTmp;
		}

		if( oTarget.tagName
			&& oTarget.tagName.toUpperCase() == 'TR' )
		{			
			if( window.event ) //Crappy IE
			{
				e.cancelBubble = true;
			}
			else //W3C :)
			{
				e.stopPropagation();
			}

			if ( self.strCurSelect == oTarget.id )
			{
				eventsLocked = false;
				return '';
			}
			
			/*
			if( self.strCurSelect )
			{
				removeClassName(document.getElementById(self.strCurSelect), 'hilite');
			}
			*/

			self.loadMessage(oTarget);
		}
		eventsLocked = false;
	}

	/**
	* Handle the [Page] Up|Down key press event
	*
	* @param string direction [up|down]
	* @param int was Page[Up|Down] key pressed ? 1 = yes
	*/
	this.scroll = function(direction, pageKey)
	{
		var iScroll = self.oTable.tBodies.item(0).rows.item(0).scrollHeight * (pageKey ? 7 : 1);
		
		if(iScroll < 0)
		{
			iScroll = 0;
		}
		else if( iScroll > iScroll.oTable.clientHeight )
		{
			iScroll = iScroll.oTable.clientHeight;
		}
		
		if(direction == 'up')
		{
			this.oTable.tBodies.item(0).scrollTop -= iScroll;
		}
		else
		{
			this.oTable.tBodies.item(0).scrollTop += iScroll;
		}
	}

	this._onUpdateNode = function()
	{
		self.oTable = document.getElementById('summary_tbl');
		
		self._addListeners();
		
		/*
		FIXME Doesn't work atm :(
		oMessageSorter =  new SortableTable(this.oTable, 
					['None', 'None', 'None', 'CaseInsensitiveString', 'CaseInsensitiveString', 'None'] );

		oMessageSorter.onsort = function ()
		{
			stripe(self.oTable.tBodies.item(0).rows);
		}
		*/
		stripe(self.oTable.tBodies.item(0).rows);

		var bFoundMsg = false;
		if ( (typeof(oCurMsgs) == 'object') )
		{
			if ( oCurMsgs[self.strCurSelect] )
			{
				var oElm = document.getElementById(self.strCurSelect + '_' + oCurMsgs[self.strCurSelect]);
				if ( oElm && oElm.id )
				{
					addClassName(oElm, 'hilite');
					if ( oElm.offsetTop )
					{
						self.oTable.tBodies.item(0).scrollTop = (oElm.offsetTop > 100 
											? oElm.offsetTop - 100 
											: 0);
					}
					else
					{
						self.oTable.tBodies.item(0).scrollTop = 0;
					}
					self.loadMessage(oElm);
					bFoundMsg = true;
				}
			}
		}
		
		if ( !bFoundMsg )
		{
			oMessage.loadMessage('');
		}

		//Stop the loading message coming and going too much
		oApplication.hideLoading();
	}

	/**
	* Refresh the message summary list
	*
	* Should only be called by oXSLDoc onreadystatechange
	*/
	this._transform = function()
	{
		if ( self.oXSLDoc && self.oXSLDoc.readyState == 4 )
		{			
			oTarget = document.getElementById(self.strParentDivID);
			var oXSLTProc = new XSLTProcessor();
			oXSLTProc.importStylesheet(self.oXSLDoc);
			oApplication.showLoading();
			Sarissa.updateContentFromURI(oApplication.strBaseURL + self.arInfo[0] + '/' + self.arInfo[1] + '/' + self.arInfo[2] + oApplication.strGET, 
							oTarget,
							oXSLTProc,
							self._onUpdateNode);
		}
	}

	/**
	* Add all the event listeners
	*/
	this._addListeners = function()
	{
		var oRows = self.oTable.tBodies.item(0).rows;

		for( var i = 0; i < oRows.length; i++ )
		{
			if( oRows.item(i).addEventListener )
			{
				oRows.item(i).addEventListener('click', this.clicked, false);
			}
			else if( oRows.item(i).attachEvent )
			{
				oRows.item(i).attachEvent('onclick', this.clicked);
			}
		}
	}
	this.init();
}

/**
* Delete a message
*
* @param string message row id
*/
Summary.prototype.delete_msg = function(strID)
{
	var oTarget = document.getElementById(strID);
	if ( typeof(oTarget) != 'undefined' && oTarget.id == strID ) //is valid
	{
		if ( this.bHideDeleted )
		{
			oTarget.parentNode.removeChild(oTarget);
			stripe(this.oTable.tBodies.item(0).rows);
		}
		else
		{
			addClassName(oTarget, 'deleted');
		}
	}
}

/**
* Get the contents of an account/mailbox, starts the REST process
*
* @access private
* @param array request components [0 - account id, 1 - mailbox/folder name]
*/
Summary.prototype.loadList = function(arListInfo)
{
	if ( arListInfo.length != 3 )
	{
		return false;
	}

	this.arInfo = arListInfo;

	if ( typeof(this.oXSLDoc) == 'object')
	{
		this._transform();
	}
	else
	{
		this.oXSLDoc = Sarissa.getDomDocument();
		this.oXSLDoc.onreadystatechange = this._transform;
	//	this.oXSLDoc.load(oApplication.strBaseURL + 'xsl/summary');
		this.oXSLDoc.load(strBaseURL + '&section=email&action=summary');
		alert(strBaseURL + '&section=email&action=summary');
	//	this.oXSLDoc.load(strAppURL + '/templates/base/summary.xsl');
	}
}

/**
* Loads a message - click event handler
*/
Summary.prototype.loadMessage = function(oTarget)
{
	//window.alert('clicked: ' + oTarget.id);
	if ( this.strCurSelect )
	{
		var oElm = document.getElementById(this.strCurSelect);
		if ( oElm )
		{
			removeClassName(oElm, 'hilite');
		}
	}
	addClassName(oTarget, 'hilite');

	oMessage.loadMessage(oTarget.id);

	this.strCurSelect = oTarget.id;
}

/**
* Set and update the contents of the active folder/mailbox
*/
Summary.prototype.setList = function( strNewSelection )
{
	if ( strNewSelection.split('_').length >= 3 ) // is it valid?
	{
		this.strCurSelect = strNewSelection;
		this.loadList( oApplication.id2ListInfo(strNewSelection) );
	}
}

