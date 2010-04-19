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
/**
 * Menu Handler
 *
 * @param String strDiv the target element ID which will hold menu
 */
function Menu(strDiv)
{
	this.ojsMenu;
	this.oXML;
	this.strDIV = strDiv;
	var self = this;

	/**
	* Build the menu
	* @internal onreadystatechange Event Handler
	*/
	this._buildMenu = function()
	{
		if ( self.oXML.readyState != 4 )
		{
			return false; //nothing to see here folks, keep moving right along
		}
		
		if ( self.oXML.parseError != 0 )
		{
			return !alert('Invalid XML!');
		}
		
		var oMenuXML = self.oXML.getElementsByTagName('menu').item(0);
		var i = 0;
		var j = 0;
		
		self.ojsMenu = new jsDOMenuBar('static', self.strDIV);
		for ( i = 0; i < oMenuXML.childNodes.length; i++)
		{
			if ( oMenuXML.childNodes.item(i).nodeType == 1 )
			{
				var oSubMenu;
				if ( oMenuXML.childNodes.item(i).childNodes.length )
				{
					oSubMenu = new jsDOMenu(150, 'absolute');
					for ( j = 0; j < oMenuXML.childNodes.item(i).childNodes.length; j++)
					{
						if ( oMenuXML.childNodes.item(i).childNodes.item(j).nodeType == 1 
							&& oMenuXML.childNodes.item(i).childNodes.item(j).tagName == 'MenuItem')
						{
							oSubMenu.addMenuItem(new menuItem(oMenuXML.childNodes.item(i).childNodes.item(j).getAttribute('name'),
											oMenuXML.childNodes.item(i).childNodes.item(j).getAttribute('id'),
											'code:oMenu.catchClick("' + oMenuXML.childNodes.item(i).childNodes.item(j).getAttribute('id') + '")',
											true,
											'menu_item',
											'menu_item hilite',
											'menu_item'
											));
						}
						else if(oMenuXML.childNodes.item(i).childNodes.item(j).nodeType == 1 
							&& oMenuXML.childNodes.item(i).childNodes.item(j).tagName == 'MenuDiv')
						{
							oSubMenu.addMenuItem(new menuItem('-',
											'',
											'',
											false,
											'menu_item',
											'menu_item',
											'menu_item'
											));
						}
					}
				}
				var oTopMenu = new menuBarItem(oMenuXML.childNodes.item(i).getAttribute('name'),
								oSubMenu,
								oMenuXML.childNodes.item(i).getAttribute('id').toString(),
								true,
								'',
								'menu',
								'menu',
								'menu'
								);
				self.ojsMenu.addMenuBarItem(oTopMenu);
			}
		}
	}

	this.init();//get this show on the road
}

/**
* Initialize Object
*/
Menu.prototype.init = function()
{
	this.loadMenuXML();
}

/**
* Catch a menu click event
*
* @param String id of menu item which triggered event
*/
Menu.prototype.catchClick = function(strID)
{
	switch (strID.substr(5, strID.length) )
	{
		case 'about':
			alert("communik8r - http://communik8r.org\n(c) 2005 Dave Hall - skwashd@communik8r.org");
			break;

		case 'help':
			oApplication.showHelp();
			break;

		case 'new':
			oApplication.compose();
			break;

		case 'prefs':
			oApplication.showSettings();
			break;

		case 'print':
			oApplication.print();
			break;

		default:
			alert('clicked: ' + strID);
	}
}

/**
* Load the menu XML
*/
Menu.prototype.loadMenuXML = function()
{
	this.oXML = new Sarissa.getDomDocument();
	this.oXML.async = true; 
	this.oXML.onreadystatechange = this._buildMenu;
	this.oXML.load(oApplication.strBaseURL + '&section=menu');
	//this._buildMenu();
}
