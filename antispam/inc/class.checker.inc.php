<?php
/**************************************************************************\
* phpGroupWare - Antispam                                                  *
* http://www.phpgroupware.org                                              *
* This application written by:                                             *
*                             Marco Andriolo-Stagno <stagno@prosa.it>      *
*                             PROSA <http://www.prosa.it>                  *
* -------------------------------------------------------------------------*
* Funding for this program was provided by http://www.seeweb.com           *
* -------------------------------------------------------------------------*
*  This program is free software; you can redistribute it and/or modify it *
*  under the terms of the GNU General Public License as published by the   *
*  Free Software Foundation; either version 2 of the License, or (at your  *
*  option) any later version.                                              *
\**************************************************************************/

/* $Id$ */

	class checker
	{
		var $public_functions = array
		(
			'handle_settings'	=>	True,
			'default_settings'	=>	True,
			'edit_row'	=>	True,
			'delete_row'	=>	True,
			'update_db'	=>	True,
			'new_rule'	=>	True,
			'add_db'	=>	True,
			'update_settings'	=>	True
		);
    
		function handle_user_settings($id)
		{
			$this->login_id = $id;
			$this->handle_settings();
		}
    
		function default_settings()
		{
			$this->login_id = $GLOBALS['HTTP_GET_VARS']['id'];
			$this->handle_settings();
		}
    
		function row_action($action,$name,$pref_id,$login_id,$value,$type)
		{ 
			$tmparr = array
			(
			'menuaction' => 'antispam.checker.'.$action,
		      'pref_id' => $pref_id,
		      'login_id' => $login_id,
		      'value' => $value,
		      'type' => $type
			);
			return '<a href="'.$GLOBALS['phpgw']->link('/index.php',$tmparr).'"> '.$name.' </a>';
		}

		
		function handle_settings($nav=TRUE)
		{
			# if called from main_manager (admin module) 
			if (!isset($this->login_id))
				{$this->login_id = $GLOBALS['phpgw_info']['user']['account_lid'] ;}
			if ($nav!=FALSE)
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();
			}
          
			$this->db = $GLOBALS['phpgw']->db;
			$tpl = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
			$tpl->set_file(array('prefs_table' => 'preferences.tpl'));
			$tpl->set_block('prefs_table','display_user','write_display_user');
			$tpl->set_block('prefs_table','row_header','write_row_header');
			$tpl->set_block('prefs_table','row','write_row');
			$tpl->set_block('prefs_table','row_footer','write_row_footer');
			$tpl->set_block('prefs_table','params','write_params');

  
			# white/black list
			$query = 'SELECT username, preference, value, prefid FROM phpgw_antispam WHERE username = "'.$this->login_id.'" and (preference="whitelist_from" or preference="blacklist_from") order by preference,value'; 
			$this->db->query($query,__LINE__,__LINE__);
       
			$alternate = $GLOBALS['phpgw']->nextmatch = CreateObject('phpgwapi.nextmatchs');
			$tpl->set_var(array
			(
				'login_id'	=>	$this->login_id,
				'user'	=>	lang('user'),
				'td_color'	=>	$GLOBALS['phpgw_info']['theme']['th_bg'],
				'url_add'	=>	$GLOBALS['phpgw']->link('/index.php','menuaction=antispam.checker.new_rule'),
				't_address'	=>	lang('address'),
				't_type'	=>	lang('type'),
				't_edit'	=>	lang('edit'),
				't_delete'	=>	lang('delete'),
				'add'	=>	lang('add'),
				'genset'	=>	lang('genset'),
				'reqhits'	=>	lang('reqhits'),
				'reqhitstxt1'	=>	lang('reqhitstxt1'),
				'reqhitstxt2'	=>	lang('reqhitstxt2'),
				'rewrite'	=>	lang('rewrite'),
				'rewritetxt'	=>	lang('rewritetxt'),
				'report'	=>	lang('report'),
				'reporttxt'	=>	lang('reporttxt'),
				'dehtml'	=>	lang('dehtml'),
				'dehtmltxt'	=>	lang('dehtmltxt'),
				'shortreptxt'	=>	lang('shortreptxt'),
				'shortrep'	=>	lang('shortrep'),
				'updatesett'	=>	lang('updatesett')
			));
      
			$tpl->pparse('write_display_user','display_user');
			$tpl->pparse('write_row_header','row_header');
	  
			while ($this->db->next_record())
			{ 
				$bgcolor = $alternate->alternate_row_color($bgcolor);
				$row = $this->db->f('prefid');
				$value = $this->db->f('value');
	      
				if ($this->db->f('preference') == 'whitelist_from')
				{
					$type = lang('l_allow');
					$ttype = 'allow';
				}
				else
				{
					$type = lang('l_deny');
					$ttype = 'deny';
				}
	      
				$data = array
				(
					'type'	=>	$type,
					'value'	=>	$value,
					'bgcolor'	=>	$bgcolor,
					'edit'	=>	$this->row_action('edit_row',lang('edit'),$row,$this->login_id,$value,$ttype),
					'delete'	=>	$this->row_action('delete_row',lang('delete'),$row,$this->login_id,$value,$ttype),
				);
	      
				$tpl->set_var($data);
				$tpl->pparse('write_row','row'); 
			}
	  
			# general settings	  
			$query = 'SELECT value FROM phpgw_antispam WHERE username = "'.$this->login_id.'" and preference="required_hits"'; 
			$this->db->query($query,__LINE__,__LINE__);
			if (!$this->db->next_record())
			{$req_hits = '';}
			else
			{$req_hits = $this->db->f('value');}

			$query = 'SELECT value FROM phpgw_antispam WHERE username = "'.$this->login_id.'" and preference="rewrite_subject"';
			$this->db->query($query,__LINE__,__LINE__);
			$this->db->next_record();
			$res = $this->db->f('value');
			switch ($res)
			{
			 case '':
				$rewrite_subjectGLOB = 'checked' ;
				break;
			 case 1:
				$rewrite_subjectON = 'checked' ;
				break;
			 case 0:
				$rewrite_subjectOFF = 'checked' ;
				break;
			}


			$query = 'SELECT value FROM phpgw_antispam WHERE username = "'.$this->login_id.'" and preference="report_header"';
			$this->db->query($query,__LINE__,__LINE__);
			$this->db->next_record();
			$res = $this->db->f('value');
			switch ($res)
			{
			  case '':
				$report_headerGLOB = 'checked' ;
				break;
			 case 1:
				$report_headerON = 'checked' ;
				break;
			 case 0:
				$report_headerOFF = 'checked' ;
				break;
			 	
			}
			
			$query = 'SELECT value FROM phpgw_antispam WHERE username = "'.$this->login_id.'" and preference="defang_mime"';
			$this->db->query($query,__LINE__,__LINE__);
			$this->db->next_record();
			$res = $this->db->f('value');
			switch ($res)
			{
			 case '':
				$defang_mimeGLOB = 'checked' ;
				break;
			 case 1:
				$defang_mimeON = 'checked' ;
				break;
			 case 0:
				$defang_mimeOFF = 'checked' ;
				break;
			}
			

			$query = 'SELECT value FROM phpgw_antispam WHERE username = "'.$this->login_id.'" and preference="use_terse_report"';
			$this->db->query($query,__LINE__,__LINE__);
			$this->db->next_record();
			$res = $this->db->f('value');
			switch ($res)
			{
			 case '':
				$use_terse_reportGLOB = 'checked' ;
				break;
			 case 1:
				$use_terse_reportON = 'checked' ;
				break;
			 case 0:
				$use_terse_reportOFF = 'checked' ;
				break;
			}
			

			$vars = array
			(
				'req_hits'	=>	$req_hits,
			 
				'rewrite_subjectON'	=>	$rewrite_subjectON,
				'rewrite_subjectOFF'	=>	$rewrite_subjectOFF,
				'rewrite_subjectGLOB'	=>	$rewrite_subjectGLOB,
		
			 	'report_headerON'	=>	$report_headerON,
			 	'report_headerOFF'	=>	$report_headerOFF,
			 	'report_headerGLOB'	=>	$report_headerGLOB,
			 
			 	'defang_mimeON'	=>	$defang_mimeON,
			 	'defang_mimeOFF'	=>	$defang_mimeOFF,
			 	'defang_mimeGLOB'	=>	$defang_mimeGLOB,
			 
			 	'use_terse_reportON'	=>	$use_terse_reportON,
			 	'use_terse_reportOFF'	=>	$use_terse_reportOFF,
			 	'use_terse_reportGLOB'	=>	$use_terse_reportGLOB,
			 
				'update_settings'	=>	$GLOBALS['phpgw']->link('/index.php','menuaction=antispam.checker.update_settings'),
			 
			 	'l_on'	=> lang('on'),
			 	'l_off'	=>	lang('off'),
			 	'l_default'	=>	lang('default')
			);
	  
			$tpl->set_var($vars);
			$tpl->pparse('write_row_footer','row_footer');
			$tpl->pparse('write_params','params');
			return;
		}
    
    
		function delete_row()      
		{
			$this->db = $GLOBALS['phpgw']->db;
			$query = 'DELETE from phpgw_antispam where prefid='.$GLOBALS['HTTP_GET_VARS']['pref_id'];
			$this->db->query($query,__LINE__,__LINE__);	
  			$this->login_id = $GLOBALS['HTTP_GET_VARS']['login_id'];
  			$this->handle_settings(); 
		}
    

		function edit_row()
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$tpl = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
			$tpl->set_file(array('prefs_table' => 'preferences.tpl'));
			$tpl->set_block('prefs_table','edit_row','write_edit_row');      
			$tpl->set_var(array
			(
				'value'	=>	$GLOBALS['HTTP_GET_VARS']['value'],
				'pref_id'	=>	$GLOBALS['HTTP_GET_VARS']['pref_id'],	  
				'login_id'	=>	$GLOBALS['HTTP_GET_VARS']['login_id'],
				'edittxt'	=>	lang('edittxt'),
				'l_allow'	=>	lang('l_allow'),
				'l_deny'	=>	lang('l_deny')
			));
			
			if ($GLOBALS['HTTP_GET_VARS']['type'] == 'allow')
			{
		   		$tpl->set_var('allow','selected');
				$tpl->set_var('deny','');
			}
			else
			{
				$tpl->set_var('deny','selected');
				$tpl->set_var('allow','');
			}
			$tpl->set_var('url',$GLOBALS['phpgw']->link('/index.php','menuaction=antispam.checker.update_db'));
			$tpl->pparse('write_edit_row','edit_row');
		}

		function update_db()
		{
			$this->db = $GLOBALS['phpgw']->db;
   
			if ($GLOBALS['HTTP_POST_VARS']['type'] == 'allow')
			{$type = 'whitelist_from';}
			else
			{$type = 'blacklist_from';}
	
			$query = 'UPDATE phpgw_antispam SET preference="'.$type.'", value="'.$GLOBALS['HTTP_POST_VARS']['value'].'" WHERE prefid="'.$GLOBALS['HTTP_POST_VARS']['pref_id'].'"';
			$this->db->query($query,__LINE__,__LINE__);
	
			$this->login_id = $GLOBALS['HTTP_POST_VARS']['login_id'];
			$this->handle_settings(); 	
		}

		function new_rule()
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$tpl = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
			$tpl->set_file(array('prefs_table' => 'preferences.tpl'));
			$tpl->set_block('prefs_table','edit_row','write_edit_row');
			$tpl->set_var(array
			(
				'value'	=>	'',
				'login_id'	=>	$GLOBALS['HTTP_POST_VARS']['login_id'],
				'l_allow'	=>	lang('l_allow'),
				'l_deny'	=>	lang('l_deny'),
				'edittxt'	=>	lang('edittxt'),
				'url'	=>	$GLOBALS['phpgw']->link('/index.php','menuaction=antispam.checker.add_db'))
			); 

			$tpl->pparse('write_edit_row','edit_row');
		}
    
		function add_db()
		{
			$this->db = $GLOBALS['phpgw']->db;
			if ($GLOBALS['HTTP_POST_VARS']['type'] == 'allow')
			{$type = 'whitelist_from';}
			else
			{$type = 'blacklist_from';}

			$query = 'INSERT INTO phpgw_antispam (username,preference,value,prefid) VALUES ("'.$GLOBALS['HTTP_POST_VARS']['login_id'].'", "'.$type.'", "'.$GLOBALS['HTTP_POST_VARS']['value'].'","")';
	
			$this->db->query($query,__LINE__,__LINE__);
      
			$this->login_id = $GLOBALS['HTTP_POST_VARS']['login_id'];
			$this->handle_settings(); 	
		}

		function update_settings()
		{
			$this->db = $GLOBALS['phpgw']->db;
			$login_id = $GLOBALS['HTTP_POST_VARS']['login_id'];
			$query = 'DELETE FROM phpgw_antispam where (preference="rewrite_subject" or preference="report_header" or preference="defang_mime" or preference="use_terse_report") and username="'.$login_id.'"';
			$this->db->query($query,__LINE__,__LINE__);
			
			if ($GLOBALS['HTTP_POST_VARS']['rewrite_subject'] != '-1')
			{
				$rewrite_subject = $GLOBALS['HTTP_POST_VARS']['rewrite_subject'];
				$query = 'INSERT INTO phpgw_antispam (username,preference,value,prefid) VALUES ("'.$login_id.'", "rewrite_subject", "'.$rewrite_subject.'", "")'; 
				$this->db->query($query,__LINE__,__LINE__);
			}
	
			if ($GLOBALS['HTTP_POST_VARS']['report_header'] != '-1')
			{
				$report_header = $GLOBALS['HTTP_POST_VARS']['report_header'];
				$query = 'INSERT INTO phpgw_antispam (username,preference,value,prefid) VALUES ("'.$login_id.'", "report_header", "'.$report_header.'", "")';
				$this->db->query($query,__LINE__,__LINE__);
			}

			if ($GLOBALS['HTTP_POST_VARS']['defang_mime'] != '-1')
			{
				$defang_mime = $GLOBALS['HTTP_POST_VARS']['defang_mime'];
				$query = 'INSERT INTO phpgw_antispam (username,preference,value,prefid) VALUES ("'.$login_id.'", "defang_mime", "'.$defang_mime.'", "")';
				$this->db->query($query,__LINE__,__LINE__);
			}

			if ($GLOBALS['HTTP_POST_VARS']['use_terse_report'] != '-1')
			{
				$use_terse_report = $GLOBALS['HTTP_POST_VARS']['use_terse_report'];
				$query = 'INSERT INTO phpgw_antispam (username,preference,value,prefid) VALUES ("'.$login_id.'", "use_terse_report", "'.$use_terse_report.'", "")';
				$this->db->query($query,__LINE__,__LINE__);
			}

			if ($GLOBALS['HTTP_POST_VARS']['required_hits'] and $GLOBALS['HTTP_POST_VARS']['required_hits'] != '')
			{	
				$required_hits = $GLOBALS['HTTP_POST_VARS']['required_hits'];
			    				
				if (is_numeric($required_hits))	
				{
					$query = 'DELETE FROM phpgw_antispam where preference="required_hits" and username="'.$login_id.'"';
					$this->db->query($query,__LINE__,__LINE__);
					$query = 'INSERT INTO phpgw_antispam (username,preference,value,prefid) VALUES ("'.$login_id.'", "required_hits", "'.$required_hits.'", "") '; 
					$this->db->query($query,__LINE__,__LINE__);
				}
				else
				{ 
					print lang('not_valid');
				}
			}
						
			 
			$this->handle_user_settings($login_id); 	

		}
	}

?>
