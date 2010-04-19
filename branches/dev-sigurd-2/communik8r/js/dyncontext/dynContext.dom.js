/**
* This file is part of the dynContext package (http://www.phpguru.org/)
*
* dynContext is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* dynContext is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with phpSQLiteAdmin; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
* 
* © Copyright 2005 Richard Heyes
*/

    __dynContext = new Array();

    /**
    * For hiding the submenus
    */
    function dynContext_hideAllSubMenus(menuObjName)
    {
        for (var i=0; i<__dynContext[menuObjName].subMenus.length; i++) {
            __dynContext[menuObjName].subMenus[i].hide();
        }
    }
    
    
    /**
    * Hides all menus when document is clicked
    */
    function dynContext_hideAllMenus()
    {
        for (i in __dynContext) {
            __dynContext[i].hide();
        }
    }

    document.addEventListener('click', dynContext_hideAllMenus, false);
    

    /**
    * Gets left coord of given element
    */
    function dynContext_GetLeft(element)
    {
        var curNode = element;
        var left    = 0;

        do {
            left += curNode.offsetLeft;
            curNode = curNode.offsetParent;

        } while(curNode.tagName.toLowerCase() != 'body');

        return left;
    }
    
    
    /**
    * Gets top coord of given element
    */
    function dynContext_GetTop(element)
    {
        var curNode = element;
        var top     = 0;

        do {
            top += curNode.offsetTop;
            curNode = curNode.offsetParent;

        } while(curNode.tagName.toLowerCase() != 'body');

        return top;
    }

/**
* Class: dynContext
*
* The top level menu object
*/
    function dynContext(objName, cssPath, width)
    {
        /**
        * Properties
        */
        this.subMenus       = new Array();
        this.menuItems      = new Array();
        this.objName        = objName;
        this.cssPath        = cssPath;
        this.width          = width;
        this.subMenuOffsetX = -6;
        this.subMenuOffsetY = -2;
        this.posX           = 0;
        this.posY           = 0;
        this.imagePath      = './images/';

        /**
        * Globals
        */
        __dynContext[this.objName] = this;
    }
    
    
    /**
    * Sets the image path
    */
    dynContext.prototype.setImagePath = function (path)
    {
        this.imagePath = path;
        
        // Set same image path on submenus
        if (this.subMenus.length) {
            for (i in this.subMenus) {
                this.subMenus[i].setImagePath(path);
            }
        }
    }


    /**
    * Adds a menuitem to the menu, be it whatever type
    */
    dynContext.prototype.addItem = function (newItem)
    {
        if (newItem.getType() == 'dynContext_menuItem' && newItem.hasSubMenu()) {
            this.subMenus.push(newItem.subMenu);
            newItem.subMenu.parentMenu = this;
            newItem.subMenu.setImagePath(this.imagePath);
        }

        this.menuItems.push(newItem);
        newItem.parentMenu = this;
    }
    
    
    /**
    * Shows the contextmenu
    */
    dynContext.prototype.show = function (event, posX, posY, posO)
    {
        // In relation to given object
        if (posO) {
            this.posX = dynContext_GetLeft(posO);
            this.posY = dynContext_GetTop(posO);
        } else {
            this.posX = 0;
            this.posY = 0;
        }

        this.posX += (typeof(posX) != 'undefined') ? Number(posX) : event.pageX;
        this.posY += (typeof(posY) != 'undefined') ? Number(posY) : event.pageY;

        /**
        * Handle menus being at the end of the screen
        */

        if (this.posX + this.width >= screen.width) {
            this.posX = this.posX - this.width;

        } else if (this.parentMenu && this.parentMenu.actualPosX + this.posX + this.width >= screen.width) {
            this.posX = (-1 * this.width) - this.subMenuOffsetX;
        }
            
        /**
        * Build and show the menu
        */
        this.buildMenu();
        this.popup.style.top    = this.posY   + 'px';
        this.popup.style.left   = this.posX   + 'px';
        this.popup.style.width  = this.width  + 'px';
        this.popup.style.visibility = 'visible';
    }


    /**
    * Hides the contextmenu
    */
    dynContext.prototype.hide = function ()
    {
        if (document.getElementById('dynContextDiv_' + this.objName)) {
            document.getElementById('dynContextDiv_' + this.objName).style.visibility = 'hidden';
            dynContext_hideAllSubMenus(this.objName);
        }
    }

    /**
    * Builds the content of the menu
    */
    dynContext.prototype.buildMenu = function ()
    {
        this.popup = document.createElement('div');
        this.popup.style.position = 'absolute';
        this.popup.className = 'dynContext';
        this.popup.id = 'dynContextDiv_' + this.objName;

        var html = ''
        for (i=0; i<this.menuItems.length; i++) {
            html += this.menuItems[i].toHTML(i);
        }

        this.popup.innerHTML = html;
        document.body.appendChild(this.popup);
    }


    /**
    * Builds the content of the menu
    */
    dynContext.prototype.reBuildMenu = function ()
    {
        this.buildMenu();
    }


    dynContext.prototype.buildAllMenus = function ()
    {
        this.buildMenu();
        for (var i = 0; i<this.subMenus.length; i++) {
            this.subMenus[i].buildAllMenus();
        }
    }
    
    
    /**
    * Dummy function to keep compatibility with msie version
    */
    dynContext.prototype.setAutoHeight = function ()
    {
    }
    
    
    /**
    * Dummy function to keep compatibility with msie version
    */
    dynContext.prototype.setStyle = function ()
    {
    }


/**
* Class: dynContext_menuItem
*
* This is an object representative of a regular menuitem
*/
    function dynContext_menuItem(text, action, image, disabled, subMenu)
    {
        this.text      = text     ? text          : '';
        this.action    = action   ? action + '()' : '';
        this.image     = image;
        this.imagetag  = image    ? '<img src="' + image + '" border="0" width="20" height="20" alt="' + this.text + '" hspace="3" align="absmiddle"/>' : '<img width="20" hspace="3" height="20" style="visibility: hidden" align="absmiddle">';
        this.disabled  = disabled ? disabled : false;
        this.subMenu   = subMenu  ? subMenu  : false;
    }

    /**
    * toHTML function that returns the HTML necessary to
    * write this entry to a document.
    */
    dynContext_menuItem.prototype.toHTML = function (index)
    {
        // Not disabled
        if (!this.disabled) {
            // Onmouseover event
            var onmouseover  = 'this.getElementsByTagName(\'div\')[1].style.MozOpacity = 1; this.className = \'row mouseover\'; dynContext_hideAllSubMenus(\'' + this.parentMenu.objName + '\'';
            onmouseover += (!this.subMenu ? ')' : '); ' + this.subMenu.objName + '.show(event, ' + (this.parentMenu.posX + this.parentMenu.width + this.parentMenu.subMenuOffsetX) + ', ' + (this.parentMenu.posY + this.parentMenu.subMenuOffsetY) + ' + this.offsetTop)');
    
            // Onclick event
            var onclick = this.subMenu ? 'event.cancelBubble = true; return false' : "dynContext_hideAllMenus(); " + this.action;

            // Disabled ?
            var disabled = '';

        // Disabled
        } else {
            var onmouseover = '';
            var onclick = 'event.cancelBubble = true; return false;';
            var disabled = 'style="-moz-opacity: 0.2"';
        }        

        // Arrow cell
        var arrowcell = !this.subMenu ? '&nbsp;': '<img src="' + this.parentMenu.imagePath + 'arrow.black.gif" alt="&gt;" hspace="7">';
    
        this.html = '<div class="row" onmouseover="' + onmouseover + '" ' + disabled + '\
                                      onmouseout="this.getElementsByTagName(\'div\')[1].style.MozOpacity = 0.4; ; this.className = \'row\'"\
                                      onselectstart="return false"\
                                      onclick="' + onclick + '" oncontextmenu="' + onclick + '; return false"' + disabled + '>\
                     <div class="arrow" style="display: inline; float: right">' + arrowcell + '</div>\
                     <div class="image" style="display: inline">' + this.imagetag + '</div>\
                     <div class="text"  style="display: inline">' + this.text + '</div>\
                     </div>';

        return this.html;
    }
    
    /**
    * Returns the type of this class
    */
    dynContext_menuItem.prototype.getType = function ()
    {
        return 'dynContext_menuItem';
    }
    
    /**
    * Returns whether this item has a submenu or not
    */
    dynContext_menuItem.prototype.hasSubMenu = function ()
    {
        return this.subMenu ? true : false;
    }
    
    /**
    * Toggles disabled status
    */
    dynContext_menuItem.prototype.toggleDisabled = function ()
    {
        this.setDisabled(!this.disabled);
    }

    /**
    * Sets the menuitem to be disabled
    */
    dynContext_menuItem.prototype.setDisabled = function (disabled)
    {
        this.disabled = disabled;
    }
    
    /**
    * Returns disabled status
    */
    dynContext_menuItem.prototype.isDisabled = function ()
    {
        return this.disabled;
    }


/**
* Class: dynContext_checkItem
*
* This is an object representative of a checkbox style menuitem
*/
    function dynContext_checkItem(text, checked, action, disabled)
    {
        this.text      = text     ? text          : '';
        this.action    = action   ? action + '()' : '';
        this.checked   = checked  ? checked  : false;
        this.disabled  = disabled ? disabled : false;
    }

    // Inheritance
    dynContext_checkItem.prototype = new dynContext_menuItem();

    /**
    * toHTML method that returns a HTML representation
    * of the check item
    */
    dynContext_checkItem.prototype.toHTML = function (index)
    {
        if (!this.isDisabled()) {
            // Onclick event
            var onclick = this.parentMenu.objName + '.menuItems[' + index + '].toggleChecked(); ' + this.action;

            // Onmouseover event
            var onmouseover = "this.className = 'row mouseover'; dynContext_hideAllSubMenus('" + this.parentMenu.objName + "')";

            // Disabled ?
            var disabled = '';
        
        } else {
            var onclick  = '';
            var onmouseover = '';
            var disabled = 'style="-moz-opacity: 0.2"';
        }
        
        
        if (this.isChecked()) {
            var check = '<img src="' + this.parentMenu.imagePath + 'check.gif"  align="absmiddle" width="20" height="20" alt="O" hspace="3">';
        } else {
            check = '<img  align="absmiddle" width="20" height="20" alt="O" hspace="3" style="visibility: hidden">';
        }

        this.html = '<div class="row" onclick="dynContext_hideAllMenus(); ' + onclick + '"\
                                      onmouseover="' + onmouseover + '"\
                                      onmouseout="this.className = \'row\'" ' + disabled + '>\
                       <div class="check" style="display: inline">' + check + '</div>\
                       <div class="text" style="display: inline">' + this.text + '</div>\
                     </div>';

        return this.html;
    }

    /**
    * Returns the type of this class
    */
    dynContext_checkItem.prototype.getType = function ()
    {
        return 'dynContext_checkItem';
    }

    /**
    * Toggles current checked status
    */
    dynContext_checkItem.prototype.toggleChecked = function ()
    {
        this.setChecked(!this.checked);
    }

    /**
    * Sets item to checked or unchecked
    */
    dynContext_checkItem.prototype.setChecked = function (checked)
    {
        this.checked = checked;
    }

    /**
    * Returns checked status
    */
    dynContext_checkItem.prototype.isChecked = function ()
    {
        return this.checked;
    }


/**
* Class: dynContext_radioItem
*
* Adds a radio selection style item to the menu
*/
    function dynContext_radioItem(action, selected, menuItems)
    {
        this.action    = action ? action + '()' : '';
        this.selected  = selected;
        this.menuItems = menuItems;
    }

    /**
    * Inheritance
    */
    dynContext_separator.prototype = new dynContext_menuItem;

    /**
    * toHTML
    */
    dynContext_radioItem.prototype.toHTML = function (index)
    {
        this.html = '';
        var onmouseover = '';
        
        for (var i = 0; i<this.menuItems.length; i++) {
            
            if (this.menuItems[i].isDisabled()) {
                var onclick     = '';
                var disabled    = 'style="-moz-opacity: 0.2"';
                var onmouseover = '';
            
            } else {
                // Onclick event
                var onclick = this.parentMenu.objName + '.menuItems[' + index + '].setSelected(' + i + '); ' + this.action;
        
                // Disabled ?
                var disabled = '';
                
                // Onmouseover event
                var onmouseover = "this.className = 'row mouseover'";
            }
            
            if (i == this.selected) {
                var radio = '<img src="' + this.parentMenu.imagePath + 'radio.gif"  align="absmiddle" width="20" height="20" alt="O" hspace="3">';
            } else {
                var radio = '<img  align="absmiddle" width="20" height="20" alt="O" hspace="3" style="visibility: hidden">';
            }

            this.html += '<div class="row" onclick="dynContext_hideAllMenus(); ' + onclick + '"\
                                           onmouseover="' + onmouseover + '"\
                                           onmouseout="this.className = \'row\'" ' + disabled + '>\
                            <div class="radio">' + radio + '</div>\
                            <div class="text">' + this.menuItems[i].text + '</div>\
                          </div>';
        }
        
        return this.html;
    }
    
    /**
    * Returns type of object this is
    */
    dynContext_radioItem.prototype.getType = function ()
    {
        return 'dynContext_radioItem';
    }
    
    /**
    * Sets selected item
    */
    dynContext_radioItem.prototype.setSelected = function (selected)
    {
        this.selected  = selected;
        this.itemBuilt = false;
    }
    
    /**
    * Returns selected index
    */
    dynContext_radioItem.prototype.getSelected = function ()
    {
        return this.selected;
    }

/**
* Class: dynContext_separator
*
* Adds a separator to the context menu
*/
    function dynContext_separator()
    {
        // Nada!
    }

    /**
    * Inheritance
    */
    dynContext_separator.prototype = new dynContext_menuItem;
    
    /**
    * toHTML method that returns a HTML representation
    * of the separator
    */
    dynContext_separator.prototype.toHTML = function (index)
    {
        if (this.itemBuilt) {
            return this.html;
        }

        this.html = '<div class="separator"><hr></div>';

        this.itemBuilt = true;
        return this.html;
    }

    /**
    * Returns the type of this class
    */
    dynContext_separator.prototype.getType = function ()
    {
        return 'dynContext_separator';
    }