<?php
	$oProc->query("INSERT INTO phpgw_categories (cat_parent,cat_owner,cat_access,cat_appname,cat_name,cat_description,last_mod) VALUES (0,-1,'public','sitemgr','Default Website','This website has been added by setup',0)");
	$site_id = $oProc->m_odb->get_last_insert_id('phpgw_categories','cat_id');
	$oProc->query("UPDATE phpgw_categories SET cat_main = $site_id WHERE cat_id = $site_id",__LINE__,__FILE__);

	$oProc->query("select config_value FROM phpgw_config WHERE config_name='webserver_url'");
	$oProc->next_record();
	$siteurl = $oProc->f('config_value') . SEP . 'sitemgr' . SEP . 'sitemgr-site' . SEP;
	$sitedir = PHPGW_INCLUDE_ROOT . SEP . 'sitemgr' . SEP . 'sitemgr-site';
	$oProc->query("INSERT INTO phpgw_sitemgr_sites (site_id,site_name,site_url,site_dir,themesel,site_languages,home_page_id,anonymous_user,anonymous_passwd) VALUES ($site_id,'Default Website','$siteurl','$sitedir','phpgroupware','en',0,'anonymous','anonymous')");

	foreach (array('html','index','toc') as $module)
	{
		$oProc->query("INSERT INTO phpgw_sitemgr_modules (module_name) VALUES ('$module')",__LINE__,__FILE__);
		$module_id = $oProc->m_odb->get_last_insert_id('phpgw_sitemgr_modules','module_id');
		$oProc->query("INSERT INTO phpgw_sitemgr_active_modules (area,cat_id,module_id) VALUES ('__PAGE__',$site_id,$module_id)",__LINE__,__FILE__);
	}

