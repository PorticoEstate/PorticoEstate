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
    * Hides all menus
    */
    function dynContext_hideAllMenus()
    {
        for (i in __dynContext) {
            __dynContext[i].hide();
        }
    }

/**
* Class: dynContext
*
* The top level menu object
*/
    function dynContext(objName, cssPath, width, height)
    {
        /**
        * Properties
        */
        this.handlerPrefix  = 'parent.';
        this.subMenus       = new Array();
        this.menuItems      = new Array();
        this.objName        = objName;
        this.cssPath        = cssPath;
        this.width          = width;
        this.height         = height;
        this.subMenuOffsetX = -8;
        this.subMenuOffsetY = -3;
        this.posX           = 0;
        this.posY           = 0;

        /**
        * Globals
        */
        __dynContext[this.objName] = this;
    }

    /**
    * Adds a menuitem to the menu, be it whatever type
    */
    dynContext.prototype.addItem = function (newItem)
    {
        if (newItem.getType() == 'dynContext_menuItem' && newItem.hasSubMenu()) {
            this.subMenus.push(newItem.subMenu);
            newItem.subMenu.parentMenu = this;
            newItem.subMenu.incrementPrefix();
        }
        this.menuItems.push(newItem);
        newItem.parentMenu = this;
    }
    
    
    /**
    * Sets the correct handlerPrefix when adding items
    */
    
    dynContext.prototype.incrementPrefix = function ()
    {
        this.handlerPrefix += 'parent.';
        
        if (this.subMenus.length) {
            for (i in this.subMenus) {
                this.subMenus[i].incrementPrefix();
            }
        }
    }

    /**
    * Sets the style sheet to be used. Also sets the menuBuilt variable to
    * false to ensure menu is rebuilt.
    */
    dynContext.prototype.setStyle = function (style)
    {
        if (this.cssPath !== style) {
            this.cssPath = style;
            
            for (var i=0; i<this.menuItems.length; i++) {
                if (this.menuItems[i].subMenu) {
                    this.menuItems[i].subMenu.setStyle(style);
                }
            }
        }
    }

    /**
    * 
    */
    dynContext.prototype.setAutoHeight = function (menuItem, imageMenuItem, separator, checkItem, radioItem)
    {
        menuItem = menuItem ? menuItem : 20;
        imageMenuItem = imageMenuItem ? imageMenuItem : 22;
        separator = separator ? separator : 15;
        checkItem = checkItem ? checkItem : 20;
        radioItem = radioItem ? radioItem : 20;

        var height = 4;
        for (var i=0; i<this.menuItems.length; i++) {
            switch (this.menuItems[i].getType()) {
                case 'dynContext_menuItem':
                    height += (this.menuItems[i].image ? imageMenuItem : menuItem);
                    break;

                case 'dynContext_separator':
                    height += separator;
                    break;

                case 'dynContext_checkItem':
                    height += checkItem;
                    break;

                case 'dynContext_radioItem':
                    height += (this.menuItems[i].menuItems.length * radioItem);
                    break;
            }
        }
        
        this.height = height;
    }

    /**
    * Shows the contextmenu
    */
    dynContext.prototype.show = function (posX, posY, posO)
    {
        if (this.popup && this.popup.isOpen) {
            return;
        }
        
        if (typeof posX == 'object') {
            posX = arguments[1];
            posY = arguments[2];
            posO = arguments[3];
        }

        this.posX = (typeof(posX) != 'undefined') ? Number(posX) : event.screenX;
        this.posY = (typeof(posY) != 'undefined') ? Number(posY) : event.screenY;

        /**
        * Handle menus being at the end of the screen
        */
        if (event && this.posX + this.width >= screen.width) {
            this.posX = this.posX - this.width;

        } else if (this.parentMenu && this.parentMenu.posX +  this.parentMenu.width + this.width >= screen.width) {
            this.posX = (-1 * this.width) - this.subMenuOffsetX;
        }
            
        /**
        * Build and show the menu
        */
        this.buildMenu();
        this.popup.show(this.posX, this.posY, this.width, this.height, posO);

        // Set some variables for the submenus to pick up.
        this.popup.document.popupObj   = this.popup;
        this.popup.document.topPopup   = event ? this.popup : this.topPopup;

        this.popup.document.subMenus  = new Array();
        eval('this.popup.document.' + this.objName + ' = this;');

        for (i=0; i<this.subMenus.length; i++) {
            this.popup.document.subMenus.push(this.subMenus[i]);
            this.subMenus[i].setWindow(this.popup.document.parentWindow);
            eval('this.popup.document.' + this.subMenus[i].objName + ' = this.subMenus[i]');
        }

        /**
        * For hiding the submenus
        */
        this.popup.document.hideAllSubMenus = function (subMenus, exception)
        {
            for (var i=0; i<subMenus.length; i++) {
                if (subMenus[i].objName !== exception) {
                    subMenus[i].hide();
                }
            }
        }
        
        /**
        * Go though doing mouseout on all rows
        */
        rows = this.popup.document.body.childNodes[0].childNodes[0].childNodes;
        for (var i=0; i<rows.length; i++) {
            if (rows[i].childNodes.length == 3) {
                rows[i].fireEvent('onmouseout');
            }
        }
    }

    /**
    * Hides the contextmenu
    */
    dynContext.prototype.hide = function ()
    {
        if (this.popup && this.popup.isOpen) {
            this.popup.hide();
        }
    }

    /**
    * Sets the window object to use for creating the popup
    */
    dynContext.prototype.setWindow = function (windowObj)
    {
        this.windowObj = windowObj;
    }
    
    /**
    * Gets the window object for passing to submenus
    */
    dynContext.prototype.getWindow = function ()
    {
        this.createPopup();
        return this.popup ? this.popup.document.parentWindow : window;
    }

    /**
    * Creates the popup object
    */
    dynContext.prototype.createPopup = function ()
    {
        //if (!this.popup) { // Fix bug in recent (latest) IE6 (maybe XP SP2?)
        this.popup = this.windowObj ? this.windowObj.createPopup() : window.createPopup();
        //}
    }

    /**
    * Builds the content of the menu
    */
    dynContext.prototype.buildMenu = function ()
    {
        this.createPopup();

        // Open document and write to it
        doc = this.popup.document.open('text/html');
        doc.write('<html><head><link rel="stylesheet" href="' + this.cssPath + '" media="screen" type="text/css" /></head><body class="dynContext"><table border="0" cellspacing="0" cellpadding="0" align="center" class="dynContext_table" style="table-layout: fixed">');
        for (i=0; i<this.menuItems.length; i++) {
            doc.write(this.menuItems[i].toHTML(i));
        }
        doc.write('</table></body></html>');
        doc.close();
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
            this.subMenus[i].setWindow(this.getWindow());
            this.subMenus[i].buildAllMenus();
        }
    }
    
    /**
    * Dummy function for api compatibility
    */
    dynContext.prototype.setImagePath = function (path)
    {
    }

/**
* Class: dynContext_menuItem
*
* This is an object representative of a regular menuitem
*/
    function dynContext_menuItem(text, action, image, disabled, subMenu)
    {
        this.text      = text     ? text : '';
        this.action    = action   ? this.parentMenu.handlerPrefix + action + '()' : '';
        this.image     = image;
        this.imagetag  = image    ? '<img src="' + image + '" border="0" alt="' + this.text + '" hspace="3" />' : '<span style="width: 30px">&nbsp;</span>';
        this.disabled  = disabled ? disabled : false;
        this.subMenu   = subMenu  ? subMenu  : false;

        this.itemBuilt = false;  // These are inherited by checkItem and radioItem
        this.html      = '';     // These are inherited by checkItem and radioItem
    }

    /**
    * toHTML function that returns the HTML necessary to
    * write this entry to a document.
    */
    dynContext_menuItem.prototype.toHTML = function (index)
    {
        if (this.itemBuilt) {
            return this.html;			
        }

        // Onmouseover event
        var onmouseover  = 'this.childNodes[0].className = \'dynContext_cell_mouseover ' + (this.image ? 'dynContext_imagecell_mouseover' : 'dynContext_imagecell_empty_mouseover') + '\';  this.childNodes[1].className = \'dynContext_cell_mouseover dynContext_textcell_mouseover\'; this.childNodes[2].className = \'dynContext_cell_mouseover dynContext_arrowcell_mouseover\';  hideAllSubMenus(subMenus';
        onmouseover += (!this.subMenu ? ')' : ', \'' + this.subMenu.objName + '\'); ' + this.subMenu.objName + '.topPopup = topPopup; ' + this.subMenu.objName + '.show(' + this.parentMenu.width + ' + ' + this.parentMenu.subMenuOffsetX + ', this.offsetTop + ' + this.parentMenu.subMenuOffsetY + ', this.offsetParent)');
        
        // Onclick event
        var onclick = this.subMenu ? '' : this.action + "; this.fireEvent('onmouseout'); topPopup.hide()";
        
        // Onmousedown event
        var onmousedown = !this.subMenu ? '' : this.subMenu.objName + '.show(' + this.parentMenu.width + ' + ' + this.parentMenu.subMenuOffsetX + ', this.offsetTop + ' + this.parentMenu.subMenuOffsetY + ', this.offsetParent)';

        // Arrow cell
        var arrowcell = !this.subMenu ? '&nbsp;': '4';
        
        // Disabled ?
        var disabled = this.disabled ? 'disabled="disabled"' : '';
        
        this.html = '<tr onmouseover="' + onmouseover + '" \
                         onmouseout="this.childNodes[0].className = \'dynContext_cell dynContext_imagecell\';  this.childNodes[1].className = \'dynContext_cell dynContext_textcell\'; this.childNodes[2].className = \'dynContext_cell dynContext_arrowcell\'" \
                         onselectstart="return false" \
                         onclick="' + onclick + '" oncontextmenu="' + onclick + '; return false" \
                         onmousedown="' + onmousedown + '" ' + disabled + '> \
                         <td class="dynContext_cell dynContext_imagecell" nowrap="nowrap">' + this.imagetag + '</td> \
                         <td class="dynContext_cell dynContext_textcell" nowrap="nowrap">' + this.text + '</td> \
                         <td class="dynContext_cell dynContext_arrowcell" nowrap="nowrap">' + arrowcell + '</td> \
                     </tr>';

        this.itemBuilt = true;
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
        this.disabled  = disabled;
        this.itemBuilt = false;
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
        this.action    = action   ? this.parentMenu.handlerPrefix + action + '()' : '';
        this.text      = text     ? text     : '';
        this.checked   = checked  ? checked  : false;
        this.disabled  = disabled ? disabled : false;
    }

    // Inheritance
    dynContext_checkItem.prototype = new dynContext_menuItem();

    /**
    * toHTML method that returns a HTML representation
    * of the separator
    */
    dynContext_checkItem.prototype.toHTML = function (index)
    {
        if (this.itemBuilt) {
            return this.html;
        }

        // Onclick event
        var onclick = this.parentMenu.objName + '.menuItems[' + index + '].toggleChecked(); topPopup.hide(); ' + this.action;

        // Disabled ?
        var disabled = this.isDisabled() ? 'disabled="disabled"' : '';
        
        this.html =  '<tr onmouseover="this.childNodes[0].className = \'dynContext_cell_mouseover dynContext_checkcell_mouseover\';  this.childNodes[1].className = \'dynContext_cell_mouseover dynContext_textcell_mouseover\'; this.childNodes[2].className = \'dynContext_cell_mouseover dynContext_arrowcell_mouseover\'; hideAllSubMenus(subMenus)" \
                         onmouseout="this.childNodes[0].className = \'dynContext_cell dynContext_checkcell\';  this.childNodes[1].className = \'dynContext_cell dynContext_textcell\'; this.childNodes[2].className = \'dynContext_cell dynContext_arrowcell\'" \
                         onselectstart="return false" \
                         onclick="' + onclick + '" oncontextmenu="' + onclick + '" ' + disabled + '> \
                         <td class="dynContext_cell dynContext_checkcell" nowrap="nowrap">' + (this.isChecked() ? '<span style="position: relative; top: 2px">a</span>' : '<span style="visibility: hidden">a</span>') + '</td> \
                         <td class="dynContext_cell dynContext_textcell" nowrap="nowrap">' + this.text + '</td> \
                         <td class="dynContext_cell dynContext_arrowcell" nowrap="nowrap">&nbsp;</td> \
                     </tr>';

        this.itemBuilt = true;
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
        this.itemBuilt = false;
        this.checked   = checked;
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
        this.action    = action ? this.parentMenu.handlerPrefix + action + '()' : '';
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
        if (this.itemBuilt) {
            return this.html;
        }

        this.html = '';

        for (var i = 0; i<this.menuItems.length; i++) {
            // Onclick event
            var onclick = this.parentMenu.objName + '.menuItems[' + index + '].setSelected(' + i + '); ' + this.action + '; topPopup.hide()';
    
            // Disabled ?
            var disabled = this.menuItems[i].isDisabled() ? 'disabled="disabled"' : '';
            
            this.html += '<tr onmouseover="this.childNodes[0].className = \'dynContext_cell_mouseover dynContext_checkcell_mouseover\';  this.childNodes[1].className = \'dynContext_cell_mouseover dynContext_textcell_mouseover\'; this.childNodes[2].className = \'dynContext_cell_mouseover dynContext_arrowcell_mouseover\'; hideAllSubMenus(subMenus)" \
                             onmouseout="this.childNodes[0].className = \'dynContext_cell dynContext_checkcell\';  this.childNodes[1].className = \'dynContext_cell dynContext_textcell\'; this.childNodes[2].className = \'dynContext_cell dynContext_arrowcell\'" \
                             onselectstart="return false" \
                             onclick="' + onclick + '" oncontextmenu="' + onclick + '" ' + disabled + '> \
                             <td class="dynContext_cell dynContext_checkcell" nowrap="nowrap">' + (this.selected == i ? '<span style="position: relative; top: 2px">h</span>' : '<span style="position: relative; top: 2px; visibility: hidden">h</span>') + '</td> \
                             <td class="dynContext_cell dynContext_textcell" nowrap="nowrap">' + this.menuItems[i].text + '</td> \
                             <td class="dynContext_cell dynContext_arrowcell" nowrap="nowrap">&nbsp;</td> \
                         </tr>';
        }
        
        this.itemBuilt = true;
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

        this.html = '<tr><td colspan="3" class="dynContext_separatorcell"><hr class="dynContext_separator"></td></tr>';

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