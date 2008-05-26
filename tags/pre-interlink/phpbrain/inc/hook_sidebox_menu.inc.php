<?php
    /**************************************************************************\
    * eGroupWare - Knowledge Base                                              *
    * http://www.egroupware.org                                                *
    * -----------------------------------------------                          *
    *  This program is free software; you can redistribute it and/or modify it *
    *  under the terms of the GNU General Public License as published by the   *
    *  Free Software Foundation; either version 2 of the License, or (at your  *
    *  option) any later version.                                              *
    \**************************************************************************/

	/* $Id$ */
{
	$menu_title = lang('%1 Menu', lang($appname));
	$file=array
	(
		array
		(
			'text'	=>'Main View',
			'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'phpbrain.uikb.index'))
		),
		
		array
		(
			'text'	=> 'New Article',
			'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'phpbrain.uikb.edit_article'))
		),

		array
		(
			'text'	=> 'Add Question',
			'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'phpbrain.uikb.add_question'))
		),

		array
		(
			'text'	=> 'Maintain Articles',
			'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'phpbrain.uikb.maintain_articles'))
		),

		array
		(
			'text'	=> 'Maintain Questions',
			'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'phpbrain.uikb.maintain_questions'))
		)
	);
	display_sidebox($appname,$menu_title,$file);

	if($GLOBALS['phpgw_info']['user']['apps']['preferences'])
	{
		$menu_title = lang('Preferences');
		$file = array
		(
			array
			(
				'text'	=> 'Preferences',
				//'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uisettings.index', 'appname' => $appname))
				'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname' => $appname))
			)

		/*
			),
			
			array
			(
				'text'	=> 'Edit Categories',
				'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uicategories.index', 'cats_app' => $appname, 'cats_level' => 1, 'global_cats' => 1))
			)
		*/
		);
		display_sidebox($appname,$menu_title,$file);
	}

	if($GLOBALS['phpgw_info']['user']['apps']['admin'])
	{
		$menu_title = lang('Administration');
		$file = array
		(
			array
			(
				'text'	=> 'Configuration',
				'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'phpbrain')),
			),
			
			array
			(
				'text'	=> 'Global Categories',
				'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'phpbrain'))
			)
		);
		display_sidebox($appname,$menu_title,$file);
	}
}
?>
