/**
 * Tabs class for handling HTML/CSS tabs
 *
 * Copyright (C) 2003 Dipl.-Inform. Kai Hofmann and probusiness AG
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with self.library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * Contact information:
 * Dipl.-Inform. Kai Hofmann
 * Arberger Heerstr. 92
 * 28307 Bremen
 * Germany
 *
 *
 * probusiness AG
 * Expo-Plaza-Nr. 1
 * 30539 Hannover
 * Germany
 *
 *
 * @version 1.0
 * @author hofmann@hofmann-int.de
 *
 * @argument nrTabs Number of Tabs to handle
 * @argument activeCSSclass CSS class name for active tabs (display:inline)
 * @argument inactiveCSSclass CSS class name for inactive tabs (display:none)
 * @argument HTMLtabID HTML ID name prefix that would be used with the tab number as tab name.
 * @argument HTMLtabcontentID HTML ID prefix for the tab content used with the tab number
 * @argument HTMLtabselectorID HTML ID prefix for a selectbox used to switch between the tabs
 * @argument HTMLtabradioID HTML ID prefix for radio button input fields used to switch between the tabs
 * @argument tabPageKey URL parameter name to use for setting/getting the actual tab
 * @argument callBack a callback function when the tab is changed - function must take 1 arg, the id of the new tab
 */
function Tabs(nrTabs,activeCSSclass,inactiveCSSclass,HTMLtabID,HTMLtabcontentID,HTMLtabselectorID,HTMLtabradioID,tabPageKey, callBack)
{
	this.nrTabs		= nrTabs;
	this.activeCSSclass	= activeCSSclass;
	this.inactiveCSSclass	= inactiveCSSclass;
	this.HTMLtabID		= HTMLtabID;
	this.HTMLtabcontentID	= HTMLtabcontentID;
	this.HTMLtabselectorID	= HTMLtabselectorID;
	this.HTMLtabradioID	= HTMLtabradioID;
	this.tabPageKey		= tabPageKey;
	this.callBack		= callBack;
	var self		= this;
	
	if (typeof(_tabs_prototype_called) == 'undefined')
	{
		_tabs_prototype_called		= true;
		Tabs.prototype.setActive	= setActive;
		Tabs.prototype.setInactive	= setInactive;
		Tabs.prototype.isActive		= isActive;
		Tabs.prototype.getActive	= getActive;
		Tabs.prototype.disableAll	= disableAll;
		Tabs.prototype.display		= display;
		Tabs.prototype.changeToActive	= changeToActive;
		Tabs.prototype.clicked		= clicked
		Tabs.prototype.init		= init;
		Tabs.prototype._replaceClass	= _replaceClass;
	}
	
	/**
	* Set tab as active
	*
	* @argument tabnr The tab number (1-nrTabs) of the tab that should be active
	*/
	function setActive(tabnr)
	{
		if ((tabnr > 0) && (tabnr <= self.nrTabs))
		{
			self._replaceClass( self.HTMLtabID + tabnr, self.inactiveCSSclass, self.activeCSSclass);
			self._replaceClass( self.HTMLtabcontentID + tabnr, self.inactiveCSSclass, self.activeCSSclass);
			if ( self.HTMLtabselectorID != null && self.HTMLtabselectorID != '' ) 
			{
				document.getElementById(self.HTMLtabselectorID).selectedIndex = tabnr-1;
			}
			if (  self.HTMLtabradioID != null && self.HTMLtabradioID != '' )
			{
				document.getElementById(self.HTMLtabradioID   + tabnr).checked = true;
			}
		}
	}
	
	/**
	* Set tab as inactive
	*
	* @argument tabnr The tab number (1-nrTabs) of the tab that should be inactive
	*/
	function setInactive(tabnr)
	{
		if ((tabnr > 0) && (tabnr <= self.nrTabs))
		{
			self._replaceClass( self.HTMLtabID + tabnr, self.activeCSSclass, self.inactiveCSSclass);
			self._replaceClass( self.HTMLtabcontentID + tabnr, self.activeCSSclass, self.inactiveCSSclass);
		}
	}
	
	/**
	* Test if tab is active
	*
	* @argument tabnr The tab number (1-nrTabs) of the tab that should be tested
	* @returns boolean - true if tab is active, false otherwise
	*/
	function isActive(tabnr)
	{
		return(document.getElementById(self.HTMLtabID + tabnr).className.search(/self.activeCSSclass/) != -1);
	}
	
	/**
	* Get the active tab number
	*
	* @returns Tab (1-nrTabs) that is currently active or 0 if non is active.
	*/
	function getActive()
	{
		for (var i = 1; i <= self.nrTabs; ++i)
		{
			if (self.isActive(i))
			{
				return i;
			}
		}
		return 0;
	}
	
	/**
	* Disable all tabs
	*/
	function disableAll()
	{
		for (var i = 1; i <= self.nrTabs; ++i)
		{
			self.setInactive(i);
		}
	}
	
	/**
	* Disable all tabs and then display the tab number given
	*
	* @argument tabnr Tab number to display
	*/
	function display(tabnr)
	{
		self.disableAll(self.nrTabs);
		self.setActive(tabnr);
	}
	
	/**
	* Loop over all tabs - switch off currently active tabs and display the new tab
	*
	* @argument tabnr Tab number to display
	*/
	function changeToActive(tabnr)
	{
		for (var i = 1; i <= self.nrTabs; ++i)
		{
			if (i == tabnr)
			{
				if (!self.isActive(i))
				{
					self.setActive(i);
				}
			}
			else
			{
				if (self.isActive(i))
				{
					self.setInactive(i);
				}
			}
		}
	}

	/**
	* Handle click event
	*
	* @argument e click event
	*/
	function clicked(e)
	{
		if ( !e ) //Fix Broken IE
		{
			var e = window.event;
		}
		
		if ( e.target )
		{
			var oTarget = e.target;
		}
		else if ( e.srcElement )
		{
			var oTarget = e.srcElement;
		}
		
		if ( oTarget.id && oTarget.id.indexOf(self.HTMLtabID) == 0 )
		{
			self.display(oTarget.id.substring(self.HTMLtabID.length));

			if ( window.event ) //Crappy IE
			{
				e.cancelBubble = true;
			}
			else //W3C :)
			{
				e.stopPropagation();
			}

			if ( self.callBack != null && self.callBack != '' )
			{
				self.callBack(oTarget);
			}
                }
	}
	
	/**
	* Get url parameter for first tab and display it.
	*/
	function init()
	{
		for ( i = 1; i <= self.nrTabs; ++i )
		{
			if ( document.getElementById )
			{
				var oTab = document.getElementById(self.HTMLtabID + i);
				if( oTab.addEventListener )
				{
					oTab.addEventListener('click', self.clicked, true);
				}
				else if( oTab.attachEvent )
				{
					oTab.attachEvent('onclick', self.clicked);
				}
			}
		}
		
		var tab = 0;
		var url = document.URL;
		var pos = url.indexOf("?");
		if (pos > -1)
		{
			var urlparams = url.substr(pos + 1,url.length - (pos + 1));
			var regexp = new RegExp('(^|&)' + self.tabPageKey + '=[0-9]{1,2}');
			var urlparamstart = urlparams.search(regexp);
			if (urlparamstart > -1)
			{
				urlparamstart = urlparamstart + ((urlparams[urlparamstart] == '&') ? 1 : 0);
				var urlparam = urlparams.substr(urlparamstart,urlparams.length - urlparamstart);
				pos = urlparam.indexOf("&");
				if (pos > -1)
				{
					urlparam = urlparam.substr(0,pos);
				}
				pos = urlparam.indexOf("=");
				if (pos > -1)
				{
					var urlparamvalue = urlparam.substr(pos + 1,urlparam.length - (pos + 1));
					tab = urlparamvalue;
				}
			}
			else
			{
				tab = 1;
			}
		}
		else
		{
			tab = 1;
		}
		if ((tab <= 0) || (tab > self.nrTabs))
		{
			tab = 1;
		}
		self.display(tab);
	}

	/**
	* Replace a CSS class with another
	*
	* @param string strID document element ID to replace class on
	* @param string strOldClass the old CSS class name to remove from ID
	* @param string strNewClass the new CSS class name to add to ID
	*/
	function _replaceClass(strID, strOldClass, strNewClass)
	{
		//document.getElementsByTagName('body').item(0).appendChild( document.createTextNode('_replaceClass(' + strID + ', ' + strOldClass + ', ' + strNewClass + ') called') );
		var oElm = document.getElementById(strID);
		if( oElm.className != '')
		{
			var arClasses = oElm.className.split(' ');
			var iCnt = arClasses.length;
			for(i = 0; i < iCnt; ++i)
			{
				if ( arClasses[i] == strOldClass )
				{
					arClasses[i] = strNewClass;
					break;
				}
			}
			oElm.className = arClasses.join(' ');
		}
		else
		{
			oElm.className = strNewClass;
		}
	}
}
