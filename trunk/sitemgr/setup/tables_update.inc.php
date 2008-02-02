<?php
	/**************************************************************************\
	* phpGroupWare                                                             *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	$test[] = '0.9.13.001';
	function sitemgr_upgrade0_9_13_001()
	{
		global $setup_info,$phpgw_setup;
		$setup_info['sitemgr']['currentver'] = '0.9.14.001';

		$phpgw_setup->oProc->AddColumn('phpgw_sitemgr_pages',
			'sort_order',array('type'=>int, 'precision'=>4));
		$phpgw_setup->oProc->AddColumn('phpgw_sitemgr_categories',
			'sort_order',array('type'=>int, 'precision'=>4));

		return $setup_info['sitemgr']['currentver'];
	}
	$test[] = '0.9.14.001';
	function sitemgr_upgrade0_9_14_001()
	{
		global $setup_info,$phpgw_setup;
		$setup_info['sitemgr']['currentver'] = '0.9.14.002';

		$phpgw_setup->oProc->AddColumn('phpgw_sitemgr_pages',
			'hide_page',array('type'=>int, 'precision'=>4));
		$phpgw_setup->oProc->AddColumn('phpgw_sitemgr_categories',
			'parent',array('type'=>int, 'precision'=>4));

		return $setup_info['sitemgr']['currentver'];
	}
	$test[] = '0.9.14.002';
	function sitemgr_upgrade0_9_14_002()
	{
		/******************************************************\
		* Purpose of this upgrade is to switch to phpgw        *
		* categories from the db categories.  So the           *
		* sql data will be moved to the cat stuff and the sql  *
		* categories table will be deleted.                    *
		*                                                      *
		* It would be nice if we could just run an UPDATE sql  *
		* query, but then you run the risk of this scenario:   *
		* old_cat_id = 5, new_cat_id = 2 --> update all pages  *
		* old_cat_id = 2, new_cat_id = 3 --> update all pages  *
		*  now all old_cat_id 5 pages are cat_id 3....         *
		\******************************************************/
		global $setup_info,$phpgw_setup;
		$setup_info['sitemgr']['currentver'] = '0.9.14.003';

		//$cat_db_so = CreateObject('sitemgr.Categories_db_SO');

		//$cat_db_so->convert_to_phpgwapi();

		// Finally, delete the categories table
		//$phpgw_setup->oProc->DropTable('phpgw_sitemgr_categories');
		
		// Turns out that convert_to_phpgwapi() must be run under 
		// the normal phpgw environment and not the setup env.
		// This upgrade routine has been moved to the main body 
		// of code.

		return $setup_info['sitemgr']['currentver'];
	}

	$test[] = '0.9.14.003';
	function sitemgr_upgrade0_9_14_003()
	{
		global $setup_info,$phpgw_setup;
		$setup_info['sitemgr']['currentver'] = '0.9.14.004';

		if (file_exists(PHPGW_SERVER_ROOT .'/sitemgr/setup/sitemgr_sitelang'))
		{
			$langfile = file(PHPGW_SERVER_ROOT . '/sitemgr/setup/sitemgr_sitelang');
			$lang = rtrim($langfile[0]);
			if (strlen($lang) != 2)
			{
				$lang = "en";
			}
		  }
		else
		  {
		    $lang = "en";
		  }

		echo 'Updating sitemgr to a multilingual architecture with existing site language ' . $lang;

		$db2 = $phpgw_setup->db;

		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_sitemgr_pages_lang',
			array(
				'fd' => array(
					'page_id' => array('type' => 'auto', 'nullable' => false),
					'lang' => array('type' => 'varchar', 'precision' => 2, 
						'nullable' => false),
					'title' => array('type' => 'varchar', 'precision' => 256),
					'subtitle' => array('type' => 'varchar', 
						'precision' => 256),
					'content' => array('type' => 'text')
				),
				'pk' => array('page_id','lang'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_sitemgr_categories_lang',
			array(
				'fd' => array(
					'cat_id' => array('type' => 'auto', 'nullable' => false),
					'lang' => array('type' => 'varchar', 'precision' => 2, 
						'nullable' => false),
					'name' => array('type' => 'varchar', 'precision' => 100),
					'description' => array('type' => 'varchar', 
						'precision' => 256)
				),
				'pk' => array('cat_id','lang'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);
		$GLOBALS['phpgw_setup']->oProc->query("select * from phpgw_categories where cat_appname='sitemgr'");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$cat_id = $GLOBALS['phpgw_setup']->oProc->f('cat_id');
			$name = $GLOBALS['phpgw_setup']->oProc->f('cat_name');
			$description = $GLOBALS['phpgw_setup']->oProc->f('cat_description');
			$db2->query("INSERT INTO phpgw_sitemgr_categories_lang (cat_id, lang, name, description) VALUES ($cat_id, '$lang', '$name', '$description')");
		}

		$GLOBALS['phpgw_setup']->oProc->query("select * from phpgw_sitemgr_pages");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$page_id = $GLOBALS['phpgw_setup']->oProc->f('page_id');
			$title = $GLOBALS['phpgw_setup']->oProc->f('title');
			$subtitle = $GLOBALS['phpgw_setup']->oProc->f('subtitle');
			$content =  $GLOBALS['phpgw_setup']->oProc->f('content');
		      
			$db2->query("INSERT INTO phpgw_sitemgr_pages_lang (page_id, lang, title, subtitle, content) VALUES ($page_id, '$lang', '$title', '$subtitle', '$content')");
		}
	  
		$newtbldef = array(
			'fd' => array(
				'page_id' => array('type' => 'auto', 'nullable' => false),
				'cat_id' => array('type' => 'int', 'precision' => 4),
				'sort_order' => array('type' => 'int', 'precision' => 4),
				'hide_page' => array('type' => 'int', 'precision' => 4),
				'name' => array('type' => 'varchar', 'precision' => 100),
				'subtitle' => array('type' => 'varchar', 'precision' => 256),
				'content' => array('type' => 'text')
			),
			'pk' => array('page_id'),
			'fk' => array(),
			'ix' => array('cat_id'),
			'uc' => array()
		);
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_sitemgr_pages',
			$newtbldef,'title');
		$newtbldef = array(
			'fd' => array(
				'page_id' => array('type' => 'auto', 'nullable' => false),
				'cat_id' => array('type' => 'int', 'precision' => 4),
				'sort_order' => array('type' => 'int', 'precision' => 4),
				'hide_page' => array('type' => 'int', 'precision' => 4),
				'name' => array('type' => 'varchar', 'precision' => 100),
				'content' => array('type' => 'text')
			),
			'pk' => array('page_id'),
			'fk' => array(),
			'ix' => array('cat_id'),
			'uc' => array()
		);
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_sitemgr_pages',
			$newtbldef,'subtitle');
		$newtbldef = array(
			'fd' => array(
				'page_id' => array('type' => 'auto', 'nullable' => false),
				'cat_id' => array('type' => 'int', 'precision' => 4),
				'sort_order' => array('type' => 'int', 'precision' => 4),
				'hide_page' => array('type' => 'int', 'precision' => 4),
				'name' => array('type' => 'varchar', 'precision' => 100)
			),
			'pk' => array('page_id'),
			'fk' => array(),
			'ix' => array('cat_id'),
			'uc' => array()
		);
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_sitemgr_pages',
			$newtbldef,'content');

		// try to set the sitelanguages preference. 
		// if it already exists do nothing
		$db2->query("SELECT pref_id FROM phpgw_sitemgr_preferences WHERE name='sitelanguages'");
		if ($db2->next_record())
		{
		}
		else
		{
			$db2->query("INSERT INTO phpgw_sitemgr_preferences (name, value) VALUES ('sitelanguages', '$lang')");
		}

		//internationalize the names for site-name, header and footer 
		//preferences
		$prefstochange = array('sitemgr-site-name','siteheader','sitefooter');
	  
		foreach ($prefstochange as $oldprefname)
		{
			$newprefname = $oldprefname . '-' . $lang;
			//echo "DEBUG: Changing $oldprefname to $newprefname. ";
			$db2->query("UPDATE phpgw_sitemgr_preferences SET name='$newprefname' where name='$oldprefname'");
		}

		return $setup_info['sitemgr']['currentver'];	  
	}	

	$test[] = '0.9.14.004';
	function sitemgr_upgrade0_9_14_004()
	{
		global $setup_info,$phpgw_setup;
		$setup_info['sitemgr']['currentver'] = '0.9.14.005';

		echo 'Fixing column names.';
		$phpgw_setup->oProc->RenameColumn('phpgw_sitemgr_blocks', 'position', 'pos');

		return $setup_info['sitemgr']['currentver'];                             
	}

	$test[] = '0.9.14.005';
	function sitemgr_upgrade0_9_14_005()
	{
		global $setup_info,$phpgw_setup;
		$setup_info['sitemgr']['currentver'] = '0.9.14.006';

		$phpgw_setup->oProc->AddColumn('phpgw_sitemgr_blocks',
			'description', array('type' => 'varchar', 'precision' => 256));
		$phpgw_setup->oProc->AddColumn('phpgw_sitemgr_blocks',
			'view', array('type' => 'int', 'precision' => 4));
		$phpgw_setup->oProc->AddColumn('phpgw_sitemgr_blocks',
			'actif', array('type' => 'int', 'precision' => 2));
		return $setup_info['sitemgr']['currentver'];
	}
	
	$test[] = '0.9.14.006';
	function sitemgr_upgrade0_9_14_006()
	{
		global $setup_info,$phpgw_setup;
		$setup_info['sitemgr']['currentver'] = '0.9.15.001';

		$phpgw_setup->oProc->DropTable('phpgw_sitemgr_blocks');
		$phpgw_setup->oProc->CreateTable('phpgw_sitemgr_modules',array(
			'fd' => array(
				'module_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
				'app_name' => array('type' => 'varchar', 'precision' => 25),
				'module_name' => array('type' => 'varchar', 'precision' => 25),
				'description' => array('type' => 'varchar', 'precision' => 255)
			),
			'pk' => array('module_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));
		$phpgw_setup->oProc->CreateTable('phpgw_sitemgr_content',array(
			'fd' => array(
				'block_id' => array('type' => 'auto', 'nullable' => false),
				'area' => array('type' => 'varchar', 'precision' => 50),
				//if page_id != NULL scope=page, elseif cat_id != NULL scope=category, else scope=site
				'cat_id' => array('type' => 'int', 'precision' => 4),
				'page_id' => array('type' => 'int', 'precision' => 4),
				'module_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'arguments' => array('type' => 'text'),
				'sort_order' => array('type' => 'int', 'precision' => 4),
				'view' => array('type' => 'int', 'precision' => 4),
				'actif' => array('type' => 'int', 'precision' => 2)
			),
			'pk' => array('block_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));
		$phpgw_setup->oProc->CreateTable('phpgw_sitemgr_content_lang',array(
			'fd' => array(
				'block_id' => array('type' => 'auto', 'nullable' => false),
				'lang' => array('type' => 'varchar', 'precision' => 2, 'nullable' => false),
				'arguments_lang' => array('type' => 'text'),
				'title' => array('type' => 'varchar', 'precision' => 255),
			),
			'pk' => array('block_id','lang'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));
		$phpgw_setup->oProc->CreateTable('phpgw_sitemgr_active_modules',array(
			'fd' => array(
				// area __PAGE__ stands for master list
				'area' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
				// cat_id 0 stands for site wide
				'cat_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'module_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false)
			),
			'pk' => array('area','cat_id','module_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));
		$phpgw_setup->oProc->CreateTable('phpgw_sitemgr_properties',array(
			'fd' => array(
				// area __PAGE__ stands for all areas
				'area' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
				// cat_id 0 stands for site wide 
				'cat_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false), 
				'module_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'properties' => array('type' => 'text')
			),
			'pk' => array('area','cat_id','module_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));

		//we register some standard modules so that the default site template works
		$db2 = $phpgw_setup->db;
		foreach (array('index','toc','html') as $module)
		{
			$db2->query("INSERT INTO phpgw_sitemgr_modules (app_name,module_name) VALUES ('sitemgr','$module')",__LINE__,__FILE__);
			$module_id = $db2->get_last_insert_id('phpgw_sitemgr_modules','module_id');
			$db2->query("INSERT INTO phpgw_sitemgr_active_modules (area,cat_id,module_id) VALUES ('__PAGE__',0,$module_id)",__LINE__,__FILE__);
		}

		//now to the difficult part: we try to put the old content field of phpgw_sitemgr_pages into the new phpgw_sitemgr_content table
		$db3 = $phpgw_setup->db;
		$GLOBALS['phpgw_setup']->oProc->query("select * from phpgw_sitemgr_pages",__LINE__,__FILE__);
		$emptyarray = serialize(array());
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$page_id = $GLOBALS['phpgw_setup']->oProc->f('page_id');
			$cat_id = $GLOBALS['phpgw_setup']->oProc->f('cat_id');
			//module_id is still the id of html module since it is the last inserted above
			$db2->query("INSERT INTO phpgw_sitemgr_content (area,cat_id,page_id,module_id,arguments,sort_order,view,actif) VALUES ('CENTER',$cat_id,$page_id,$module_id,'$emptyarray',0,0,1)",__LINE__,__FILE__);
			$block_id = $db2->get_last_insert_id('phpgw_sitemgr_content','block_id');
			$db2->query("select * from phpgw_sitemgr_pages_lang WHERE page_id = $page_id",__LINE__,__FILE__);
			while($db2->next_record())
			{
				$lang = $db2->f('lang');
				$content = $db2->db_addslashes(serialize(array('htmlcontent' => stripslashes($db2->f('content')))));
				$db3->query("INSERT INTO phpgw_sitemgr_content_lang (block_id,lang,arguments_lang,title) VALUES ($block_id,'$lang','$content','HTML')",__LINE__,__FILE__);
			}
		}
		//finally drop the content field
		$newtbldef = array(
			'fd' => array(
				'page_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'lang' => array('type' => 'varchar', 'precision' => 2, 'nullable' => false),
				'title' => array('type' => 'varchar', 'precision' => 255),
				'subtitle' => array('type' => 'varchar', 'precision' => 255)
			),
			'pk' => array('page_id','lang'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		);
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_sitemgr_pages_lang',$newtbldef,'content');
		return $setup_info['sitemgr']['currentver'];
	}

	$test[] = '0.9.15.001';
	function sitemgr_upgrade0_9_15_001()
	{
		global $setup_info,$phpgw_setup;
		$setup_info['sitemgr']['currentver'] = '0.9.15.002';

		echo 'Fixing column names.';
		$phpgw_setup->oProc->RenameColumn('phpgw_sitemgr_content', 'view', 'viewable');

		return $setup_info['sitemgr']['currentver'];                             
	}

	$test[] = '0.9.15.002';
	function sitemgr_upgrade0_9_15_002()
	{
		global $setup_info,$phpgw_setup;
		$setup_info['sitemgr']['currentver'] = '0.9.15.003';

		$newtbldef = array(
			'fd' => array(
				'module_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
				'module_name' => array('type' => 'varchar', 'precision' => 25),
				'description' => array('type' => 'varchar', 'precision' => 255)
			),
			'pk' => array('module_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		);

		$phpgw_setup->oProc->DropColumn('phpgw_sitemgr_modules',$newtbldef,'app_name');

		return $setup_info['sitemgr']['currentver'];                             
	}

 	$test[] = '0.9.15.003';
 	function sitemgr_upgrade0_9_15_003()
 	{
 		global $setup_info,$phpgw_setup;
 		$setup_info['sitemgr']['currentver'] = '0.9.15.004';

		$phpgw_setup->oProc->createtable('phpgw_sitemgr_sites',array(
			'fd' => array(
				'site_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'site_name' => array('type' => 'varchar', 'precision' => 255),
				'site_url' => array('type' => 'varchar', 'precision' => 255),
				'site_dir' => array('type' => 'varchar', 'precision' => 255),
				'themesel' => array('type' => 'varchar', 'precision' => 50),
				'site_languages' => array('type' => 'varchar', 'precision' => 50),
				'home_page_id' => array('type' => 'int', 'precision' => 4),
				'anonymous_user' => array('type' => 'varchar', 'precision' => 50),
				'anonymous_passwd' => array('type' => 'varchar', 'precision' => 50),
			),
			'pk' => array('site_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));

		$db2 = $phpgw_setup->db;

		//Create default site and hang all existing categories into it
		$phpgw_setup->oProc->query("INSERT INTO phpgw_categories (cat_parent,cat_owner,cat_access,cat_appname,cat_name,cat_description,last_mod) VALUES (0,-1,'public','sitemgr','Default Website','This website has been added by setup',0)");

		$phpgw_setup->oProc->query("SELECT cat_id FROM phpgw_categories WHERE cat_name='Default Website' AND cat_appname='sitemgr'");
		if ($phpgw_setup->oProc->next_record())
		{
			$site_id = $phpgw_setup->oProc->f('cat_id');
			$db2->query("UPDATE phpgw_categories SET cat_main = $site_id WHERE cat_appname = 'sitemgr'",__LINE__,__FILE__);
			$db2->query("UPDATE phpgw_categories SET cat_parent = $site_id WHERE cat_appname = 'sitemgr' AND cat_parent = 0 AND cat_id != $site_id",__LINE__,__FILE__);
			$db2->query("UPDATE phpgw_categories SET cat_level = cat_level +1 WHERE cat_appname = 'sitemgr' AND cat_id != $site_id",__LINE__,__FILE__);
			$db2->query("INSERT INTO phpgw_sitemgr_sites (site_id,site_name)  VALUES ($site_id,'Default Website')");
		}

		//insert values from old preferences table into new sites table
		$oldtonew = array(
			'sitemgr-site-url' => 'site_url',
			'sitemgr-site-dir' => 'site_dir',
			'themesel' => 'themesel',
			'sitelanguages' => 'site_languages',
			'home-page-id' => 'home_page_id',
			'anonymous-user' => 'anonymous_user',
			'anonymous-passwd' => 'anonymous_passwd'
		);
		foreach ($oldtonew as $old => $new)
		{
			$phpgw_setup->oProc->query("SELECT value from phpgw_sitemgr_preferences WHERE name = '$old'");
			if ($phpgw_setup->oProc->next_record())
			{
				$value = $phpgw_setup->oProc->f('value');
				$db2->query("UPDATE phpgw_sitemgr_sites SET $new = '$value' WHERE site_id = $site_id");
			}
		}

		//site names and headers
		$phpgw_setup->oProc->query("SELECT site_languages from phpgw_sitemgr_sites");
		if ($phpgw_setup->oProc->next_record())
		{
			$sitelanguages = $db2->f('site_languages');
			$sitelanguages = explode(',',$sitelanguages);
			$db2->query("SELECT module_id from phpgw_sitemgr_modules WHERE module_name='html'");
			$db2->next_record();
			$html_module = $db2->f('module_id');
			$emptyarray = serialize(array());
			$db2->query("INSERT INTO phpgw_sitemgr_content (area,cat_id,page_id,module_id,arguments,sort_order,viewable,actif) VALUES ('HEADER',$site_id,0,$html_module,'$emptyarray',0,0,1)",__LINE__,__FILE__);
			$headerblock = $db2->get_last_insert_id('phpgw_sitemgr_content','block_id');
			$db2->query("INSERT INTO phpgw_sitemgr_content (area,cat_id,page_id,module_id,arguments,sort_order,viewable,actif) VALUES ('FOOTER',$site_id,0,$html_module,'$emptyarray',0,0,1)",__LINE__,__FILE__);
			$footerblock = $db2->get_last_insert_id('phpgw_sitemgr_content','block_id');

			foreach ($sitelanguages as $lang)
			{
				$db2->query("SELECT value from phpgw_sitemgr_preferences WHERE name = 'sitemgr-site-name-$lang'");
				if ($db2->next_record())
				{
					$name_lang = $db2->f('value');
					$db2->query("INSERT INTO phpgw_sitemgr_categories_lang (cat_id,lang,name) VALUES ($site_id,'$lang','$name_lang')");
				}
				$db2->query("SELECT value from phpgw_sitemgr_preferences WHERE name = 'siteheader-$lang'");
				if ($db2->next_record())
				{
					$header_lang = $db2->f('value');
					$content = $db2->db_addslashes(serialize(array('htmlcontent' => stripslashes($header_lang))));
		
					$db2->query("INSERT INTO phpgw_sitemgr_content_lang (block_id,lang,arguments_lang,title) VALUES ($headerblock,'$lang','$content','Site header')",__LINE__,__FILE__);
				}
				$db2->query("SELECT value from phpgw_sitemgr_preferences WHERE name = 'sitefooter-$lang'");
				if ($db2->next_record())
				{
					$footer_lang = $db2->f('value');
					$content = $db2->db_addslashes(serialize(array('htmlcontent' => stripslashes($footer_lang))));
				
					$db2->query("INSERT INTO phpgw_sitemgr_content_lang (block_id,lang,arguments_lang,title) VALUES ($footerblock,'$lang','$content','Site footer')",__LINE__,__FILE__);
				}
			}
		}
 		$phpgw_setup->oProc->DropTable('phpgw_sitemgr_preferences');

 		return $setup_info['sitemgr']['currentver'];
	}

 	$test[] = '0.9.15.004';
 	function sitemgr_upgrade0_9_15_004()
 	{
 		global $setup_info,$phpgw_setup;
 		$setup_info['sitemgr']['currentver'] = '0.9.15.005';
 		$db2 = $phpgw_setup->db;
		$db3 = $phpgw_setup->db;

		//Create the field state for pages and categories and give all existing pages and categories published state (2)
		$phpgw_setup->oProc->AddColumn('phpgw_sitemgr_pages',
			'state',array('type'=>int, 'precision'=>2));
	
		$phpgw_setup->oProc->query("UPDATE phpgw_sitemgr_pages SET state = 2");

		$phpgw_setup->oProc->CreateTable('phpgw_sitemgr_categories_state',array(
			'fd' => array(
				'cat_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'state' => array('type' => 'int', 'precision' => 2)
			),
			'pk' => array('cat_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));

		$GLOBALS['phpgw_setup']->oProc->query("select cat_id from phpgw_categories where cat_appname='sitemgr' AND cat_level > 0");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$cat_id = $GLOBALS['phpgw_setup']->oProc->f('cat_id');
			$db2->query("INSERT INTO phpgw_sitemgr_categories_state (cat_id,state) VALUES ($cat_id,2)");
		}

		//rename table content blocks and table content_lang blocks_lang
		//and add the new tables content and content_lang
		$GLOBALS['phpgw_setup']->oProc->RenameTable('phpgw_sitemgr_content','phpgw_sitemgr_blocks');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('phpgw_sitemgr_content_lang','phpgw_sitemgr_blocks_lang');
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_sitemgr_content',array(
			'fd' => array(
				'version_id' => array('type' => 'auto', 'nullable' => false),
				'block_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'arguments' => array('type' => 'text'),
				'state' => array('type' => 'int', 'precision' => 2)
			),
			'pk' => array('version_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_sitemgr_content_lang',array(
			'fd' => array(
				'version_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'lang' => array('type' => 'varchar', 'precision' => 2, 'nullable' => false),
				'arguments_lang' => array('type' => 'text'),
			),
			'pk' => array('version_id','lang'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));

		//create rows in the new content tables from old content tables (where state=0(Draft) when inactive, state=2(Published) when active)
		$GLOBALS['phpgw_setup']->oProc->query("SELECT block_id,arguments,actif FROM phpgw_sitemgr_blocks");
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$block_id = $GLOBALS['phpgw_setup']->oProc->f('block_id');
			$arguments = $GLOBALS['phpgw_setup']->oProc->f('arguments');
			$state = $GLOBALS['phpgw_setup']->oProc->f('actif') ? 0 : 2;
			$db2->query("INSERT INTO phpgw_sitemgr_content (block_id,arguments,state) VALUES ($block_id,'$arguments',$state)");
			$version_id = $db2->get_last_insert_id('phpgw_sitemgr_content','version_id');
			$db2->query("SELECT lang,arguments_lang  FROM phpgw_sitemgr_blocks_lang WHERE block_id = $block_id");
			while ($db2->next_record())
			{
				$lang = $db2->f('lang');
				$arguments_lang = $db2->f('arguments_lang');
				$title = $db2->f('title');
				$db3->query("INSERT INTO phpgw_sitemgr_content_lang (version_id,lang,arguments_lang) VALUES ($version_id,'$lang','$arguments_lang')");
			}
		}

		//drop columns in tables blocks and blocks_lang
		$newtbldef = array(
			'fd' => array(
				'block_id' => array('type' => 'auto', 'nullable' => false),
				'area' => array('type' => 'varchar', 'precision' => 50),
				'cat_id' => array('type' => 'int', 'precision' => 4),
				'page_id' => array('type' => 'int', 'precision' => 4),
				'module_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'sort_order' => array('type' => 'int', 'precision' => 4),
				'viewable' => array('type' => 'int', 'precision' => 4),
				'actif' => array('type' => 'int', 'precision' => 2)
			),
			'pk' => array('block_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		);
		$phpgw_setup->oProc->DropColumn('phpgw_sitemgr_blocks',$newtbldef,'arguments');
		$newtbldef = array(
			'fd' => array(
				'block_id' => array('type' => 'auto', 'nullable' => false),
				'area' => array('type' => 'varchar', 'precision' => 50),
				'cat_id' => array('type' => 'int', 'precision' => 4),
				'page_id' => array('type' => 'int', 'precision' => 4),
				'module_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'sort_order' => array('type' => 'int', 'precision' => 4),
				'viewable' => array('type' => 'int', 'precision' => 4),
			),
			'pk' => array('block_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		);
		$phpgw_setup->oProc->DropColumn('phpgw_sitemgr_blocks',$newtbldef,'actif');
		$newtbldef = array(
			'fd' => array(
				'block_id' => array('type' => 'auto', 'nullable' => false),
				'lang' => array('type' => 'varchar', 'precision' => 2, 'nullable' => false),
				'title' => array('type' => 'varchar', 'precision' => 255),
			),
			'pk' => array('block_id','lang'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		);
		$phpgw_setup->oProc->DropColumn('phpgw_sitemgr_blocks_lang',$newtbldef,'arguments_lang');
		return $setup_info['sitemgr']['currentver'];
	}

?>
