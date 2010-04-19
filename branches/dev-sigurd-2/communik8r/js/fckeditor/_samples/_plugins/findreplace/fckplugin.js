/*
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2004 Frederico Caldeira Knabben
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 * 
 * For further information visit:
 * 		http://www.fckeditor.net/
 * 
 * File Name: fckplugin.js
 * 	This is the sample plugin definition file.
 * 
 * Version:  2.0 RC3
 * Modified: 2004-11-22 11:20:10
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
 */

// Register the related commands.
FCKCommands.RegisterCommand( 'My_Find'		, new FCKDialogCommand( FCKLang['DlgMyFindTitle']	, FCKLang['DlgMyFindTitle']		, FCKConfig.PluginsPath + 'FindReplace/find.html'	, 340, 170 ) ) ;
FCKCommands.RegisterCommand( 'My_Replace'	, new FCKDialogCommand( FCKLang['DlgMyReplaceTitle'], FCKLang['DlgMyReplaceTitle']	, FCKConfig.PluginsPath + 'FindReplace/replace.html', 340, 200 ) ) ;

// Create the "Find" toolbar button.
var oFindItem		= new FCKToolbarButton( 'My_Find', FCKLang['DlgMyFindTitle'] ) ;
oFindItem.IconPath	= FCKConfig.PluginsPath + 'FindReplace/find.gif' ;

FCKToolbarItems.RegisterItem( 'My_Find', oFindItem ) ;			// 'My_Find' is the name used in the Toolbar config.

// Create the "Replace" toolbar button.
var oReplaceItem		= new FCKToolbarButton( 'My_Replace', FCKLang['DlgMyReplaceTitle'] ) ;
oReplaceItem.IconPath	= FCKConfig.PluginsPath + 'FindReplace/replace.gif' ;

FCKToolbarItems.RegisterItem( 'My_Replace', oReplaceItem ) ;	// 'My_Replace' is the name used in the Toolbar config.