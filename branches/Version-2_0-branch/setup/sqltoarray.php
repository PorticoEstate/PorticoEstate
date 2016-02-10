<?php
	/**
	* Setup
	*
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package setup
	* @version $Id$
	*/

	$phpgw_info = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'noheader' => True,
		'nonavbar' => True,
		'currentapp' => 'home',
		'noapi' => True
	);
	
	/**
	 * Include setup functions
	 */
	include ('./inc/functions.inc.php');

	// Check header and authentication
	if (!$GLOBALS['phpgw_setup']->auth('Config'))
	{
		Header('Location: index.php');
		exit;
	}
	// Does not return unless user is authorized

	$tpl_root = $GLOBALS['phpgw_setup']->html->setup_tpl_dir('setup');
	$setup_tpl = CreateObject('phpgwapi.Template',$tpl_root);

	$download = phpgw::get_var('download','bool');
	$submit   = phpgw::get_var('submit','bool');
	$showall  = phpgw::get_var('showall','bool');
	$appname  = phpgw::get_var('appname','string');
	if ($download)
	{
		$setup_tpl->set_file(array(
			'sqlarr'   => 'arraydl.tpl'
		));
		$setup_tpl->set_var('idstring',"/* \$Id" . ": tables_current.inc.php" . ",v 1.0 " . @date('Y/m/d',time()) .  " username " . "Exp \$ */");
		$setup_tpl->set_block('sqlarr','sqlheader','sqlheader');
		$setup_tpl->set_block('sqlarr','sqlbody','sqlbody');
		$setup_tpl->set_block('sqlarr','sqlfooter','sqlfooter');
	}
	else
	{
		$setup_tpl->set_file(array(
			'T_head' => 'head.tpl',
			'T_footer' => 'footer.tpl',
			'T_alert_msg' => 'msg_alert_msg.tpl',
			'T_login_main' => 'login_main.tpl',
			'T_login_stage_header' => 'login_stage_header.tpl',
			'T_setup_main' => 'schema.tpl',
			'applist'  => 'applist.tpl',
			'sqlarr'   => 'sqltoarray.tpl',
			'T_head'   => 'head.tpl',
			'T_footer' => 'footer.tpl'
		));
		$setup_tpl->set_block('T_login_stage_header','B_multi_domain','V_multi_domain');
		$setup_tpl->set_block('T_login_stage_header','B_single_domain','V_single_domain');
		$setup_tpl->set_block('T_setup_main','header','header');
		$setup_tpl->set_block('applist','appheader','appheader');
		$setup_tpl->set_block('applist','appitem','appitem');
		$setup_tpl->set_block('applist','appfooter','appfooter');
		$setup_tpl->set_block('sqlarr','sqlheader','sqlheader');
		$setup_tpl->set_block('sqlarr','sqlbody','sqlbody');
		$setup_tpl->set_block('sqlarr','sqlfooter','sqlfooter');
	}

	$GLOBALS['phpgw_setup']->loaddb();

	/**
	 * Parse variables
	 * 
	 * @param string $table
	 * @param string $term
	 */
	function parse_vars($table,$term)
	{
		$GLOBALS['setup_tpl']->set_var('table', $table);
		$GLOBALS['setup_tpl']->set_var('term',$term);

		list($arr,$pk,$fk,$ix,$uc) = $GLOBALS['phpgw_setup']->process->sql_to_array($table);
		$GLOBALS['setup_tpl']->set_var('arr',$arr);
		if (count($pk) > 1)
		{
			$GLOBALS['setup_tpl']->set_var('pks', "'".implode("','",$pk)."'");
		}
		elseif($pk && !empty($pk))
		{
			$GLOBALS['setup_tpl']->set_var('pks', "'" . $pk[0] . "'");
		}
		else
		{
			$GLOBALS['setup_tpl']->set_var('pks','');
		}

		if (count($fk) > 1)
		{
			$GLOBALS['setup_tpl']->set_var('fks', "\n\t\t\t\t" . implode(",\n\t\t\t\t",$fk) );
		}
		elseif($fk && !empty($fk))
		{
			$GLOBALS['setup_tpl']->set_var('fks', $fk[0]);
		}
		else
		{
			$GLOBALS['setup_tpl']->set_var('fks','');
		}
		if (count($ix) > 1)
		{
			foreach($ix as $entry)
			{
				if(count($entry) > 1)
				{
					$ix_temp[] = "array('" . implode("','",$entry) . "')";
				}
				else
				{
					$ix_temp[] = "array('{$entry}')";
				}
			}
			$GLOBALS['setup_tpl']->set_var('ixs', implode(",",$ix_temp));
		}
		elseif($ix && !empty($ix))
		{
			$GLOBALS['setup_tpl']->set_var('ixs', "'{$ix[0]}'");
		}
		else
		{
			$GLOBALS['setup_tpl']->set_var('ixs','');
		}

		if (count($uc) > 1)
		{
			$GLOBALS['setup_tpl']->set_var('ucs', "'" . implode("','",$uc) . "'");
		}
		elseif($uc && !empty($uc))
		{
			$GLOBALS['setup_tpl']->set_var('ucs', "'" . $uc[0] . "'");
		}
		else
		{
			$GLOBALS['setup_tpl']->set_var('ucs','');
		}
	}

	/**
	 * 
	 * 
	 * @param string $template
	 * @return string
	 */
	function printout($template)
	{
		global $download,$appname,$table,$showall;
		$string = '';

		if ($download)
		{
			$GLOBALS['setup_tpl']->set_var('appname',$appname);
			$string = $GLOBALS['setup_tpl']->parse('out',$template);
		}
		else
		{
			$GLOBALS['setup_tpl']->set_var('appname',$appname);
			$GLOBALS['setup_tpl']->set_var('table',$table);
			$GLOBALS['setup_tpl']->set_var('lang_download','Download');
			$GLOBALS['setup_tpl']->set_var('showall',$showall);
			$GLOBALS['setup_tpl']->set_var('action_url','sqltoarray.php');
			$GLOBALS['setup_tpl']->pfp('out',$template);
		}
		return $string;
	}


	/**
	 * Download handler
	 * 
	 * @param string $dlstring
	 * @param string $fn
	 */
	function download_handler($dlstring,$fn='tables_current.inc.php')
	{
		//include( PHPGW_SERVER_ROOT . '/phpgwapi/inc/class.browser.inc.php');
		$b = CreateObject('phpgwapi.browser');
		$b->content_header($fn);
		echo $dlstring;
		exit;
	}

	if ($submit || $showall)
	{
		$dlstring = '';
		$term = '';

		if (!$download)
		{
			$GLOBALS['phpgw_setup']->html->show_header();
		}

		if ($showall)
		{
			$table = $appname = '';
		}

		if((!isset($table) || !$table) && !$appname)
		{
			$term = ',';
			$dlstring .= printout('sqlheader');

			$db = $GLOBALS['phpgw_setup']->db;
			$db->query('SHOW TABLES');
			while($db->next_record())
			{
				$table = $db->f(0);
				parse_vars($table,$term);
				$dlstring .= printout('sqlbody');
			}
			$dlstring .= printout('sqlfooter');

		}
		elseif($appname)
		{
			$dlstring .= printout('sqlheader');
			$term = ',';

			if(!isset($setup_info[$appname]['tables']) || !$setup_info[$appname]['tables'])
			{
				$f = PHPGW_SERVER_ROOT . '/' . $appname . '/setup/setup.inc.php';
				if (file_exists ($f)) 
				{
					/**
					 * Include existing file
					 */ 
				 	include($f); 
				 }
			}

			//$tables = explode(',',$setup_info[$appname]['tables']);
			$tables = $setup_info[$appname]['tables'];
			// $i = 1;
			while(list($key,$table) = @each($tables))
			{
				/*
				if($i == count($tables))
				{
					$term = '';
				}
				*/
				parse_vars($table,$term);
				$dlstring .= printout('sqlbody');
				// ++$i;
			}
			$dlstring .= printout('sqlfooter');
		}
		elseif($table)
		{
			$term = ';';
			parse_vars($table,$term);
			$dlstring .= printout('sqlheader');
			$dlstring .= printout('sqlbody');
			$dlstring .= printout('sqlfooter');
		}
		if ($download)
		{
			download_handler($dlstring);
		}
	}
	else
	{
		$GLOBALS['phpgw_setup']->html->show_header();

		$setup_tpl->set_var('action_url','sqltoarray.php');
		$setup_tpl->set_var('lang_submit','Show selected');
		$setup_tpl->set_var('lang_showall','Show all');
		$setup_tpl->set_var('title','SQL to schema_proc array util');
		$setup_tpl->set_var('lang_applist','Applications');
		$setup_tpl->set_var('select_to_download_file',lang('Select to download file'));
		$setup_tpl->pfp('out','appheader');

		$d = dir(PHPGW_SERVER_ROOT);
		while($entry=$d->read())
		{
			$f = PHPGW_SERVER_ROOT . '/' . $entry . '/setup/setup.inc.php';
			if (file_exists ($f)) { include($f); }
		}

		while (list($key,$data) = @each($setup_info))
		{
			if ($data['tables'] && $data['title'])
			{
				$setup_tpl->set_var('appname',$data['name']);
				$setup_tpl->set_var('apptitle',$data['title']);
				$setup_tpl->pfp('out','appitem');
			}
		}
		$setup_tpl->pfp('out','appfooter');
	}
