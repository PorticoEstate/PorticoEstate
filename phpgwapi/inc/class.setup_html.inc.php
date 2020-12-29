<?php
	/**
	* Setup html
	* @author Tony Puglisi (Angles) <angles@phpgroupware.org>
	* @author Miles Lott <milosch@phpgroupware.org>
	* @copyright Portions Copyright (C) 2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage application
	* @version $Id$
	*/

	/**
	* Setup html
	*
	* @package phpgwapi
	* @subpackage application
	*/
	class setup_html
	{
		/**
		 * generate header.inc.php file output - NOT a generic html header function
		*
		 */
		function generate_header()
		{
			$GLOBALS['header_template']->set_file(array('header' => 'header.inc.php.template'));
			$GLOBALS['header_template']->set_block('header','domain','domain');
			$var = Array();

			$deletedomain = phpgw::get_var('deletedomain', 'string', 'POST');
			$domains = phpgw::get_var('domains', 'string', 'POST');
			if( !is_array($domains) )
			{
				$domains = array();
			}

			$setting = phpgw::get_var('setting', 'raw', 'POST');
			$settings = phpgw::get_var("settings", 'raw', 'POST');

			foreach($domains as $k => $v)
			{
				if(isset($deletedomain[$k]))
				{
					continue;
				}
				$dom = $settings[$k];
				$GLOBALS['header_template']->set_var('DB_DOMAIN',$v);
				foreach($dom as $x => $y)
				{
					if( ((isset($setting['enable_mcrypt']) && $setting['enable_mcrypt'] == 'True') || !empty($setting['enable_crypto'])) && ($x == 'db_pass' || $x == 'db_host' || $x == 'db_port' || $x == 'db_name' || $x == 'db_user' || $x == 'config_pass'))
					{
						$y = $GLOBALS['phpgw']->crypto->encrypt($y);
					}
					$GLOBALS['header_template']->set_var(strtoupper($x),$y);
				}
				$GLOBALS['header_template']->parse('domains','domain',True);
			}

			$GLOBALS['header_template']->set_var('domain','');

			if(!empty($setting) && is_array($setting))
			{
				foreach($setting as $k => $v)
				{
					if (((isset($setting['enable_mcrypt']) && $setting['enable_mcrypt'] == 'True')  || !empty($setting['enable_crypto']))&& $k == 'HEADER_ADMIN_PASSWORD')
					{
						$v = $GLOBALS['phpgw']->crypto->encrypt($v);
					}
					
					if( in_array( $k, array( 'server_root', 'include_root' ) )
						&& substr(PHP_OS, 0, 3) == 'WIN' )
					{
						$v = str_replace( '\\', '/', $v );
					}
					$var[strtoupper($k)] = $v;
				}
			}
			$GLOBALS['header_template']->set_var($var);
			return $GLOBALS['header_template']->parse('out','header');
		}

		function setup_tpl_dir($app_name='setup')
		{
			/* hack to get tpl dir */
			if (is_dir(PHPGW_SERVER_ROOT))
			{
				$srv_root = PHPGW_SERVER_ROOT . "/$app_name/";
			}
			else
			{
				$srv_root = '';
			}

			return "{$srv_root}/templates/base";
		}

		function show_header($title='',$nologoutbutton=False, $logoutfrom='config', $configdomain='')
		{
			$GLOBALS['setup_tpl']->set_var('lang_charset',lang('charset'));
			$style = array('th_bg'		=> '#486591',
					'th_text'	=> '#FFFFFF',
					'row_on'	=> '#DDDDDD',
					'row_off'	=> '#EEEEEE',
					'banner_bg'	=> '#4865F1',
					'msg'		=> '#FF0000',
					);
			$GLOBALS['setup_tpl']->set_var($style);
			if ($nologoutbutton)
			{
				$btn_logout = '&nbsp;';
			}
			else
			{
				$btn_logout = '<a href="index.php?FormLogout=' . $logoutfrom . '" class="link">' . lang('Logout').'</a>';
			}

			$GLOBALS['setup_tpl']->set_var('lang_version', lang('version'));
			$GLOBALS['setup_tpl']->set_var('lang_setup', lang('setup'));
			$GLOBALS['setup_tpl']->set_var('page_title',$title);
			if ($configdomain == '')
			{
				$GLOBALS['setup_tpl']->set_var('configdomain','');
			}
			else
			{
				$GLOBALS['setup_tpl']->set_var('configdomain',' - ' . lang('Domain') . ': ' . $configdomain);
			}

			$api_version = isset($GLOBALS['phpgw_info']['server']['versions']['phpgwapi']) ? $GLOBALS['phpgw_info']['server']['versions']['phpgwapi'] : '';
			
			$version = isset($GLOBALS['phpgw_info']['server']['versions']['system']) ? $GLOBALS['phpgw_info']['server']['versions']['system'] : $api_version;

			$GLOBALS['setup_tpl']->set_var('pgw_ver',$version);
			$GLOBALS['setup_tpl']->set_var('logoutbutton',$btn_logout);
			$GLOBALS['setup_tpl']->pparse('out','T_head');
			/* $setup_tpl->set_var('T_head',''); */
		}

		function show_footer()
		{
			$GLOBALS['setup_tpl']->pparse('out','T_footer');
			unset($GLOBALS['setup_tpl']);
		}

		function show_alert_msg($alert_word='Setup alert',$alert_msg='setup alert (generic)')
		{
			$GLOBALS['setup_tpl']->set_var('V_alert_word',$alert_word);
			$GLOBALS['setup_tpl']->set_var('V_alert_msg',$alert_msg);
			$GLOBALS['setup_tpl']->pparse('out','T_alert_msg');
		}

		function make_frm_btn_simple($pre_frm_blurb='',$frm_method='POST',$frm_action='',$input_type='submit',$input_value='',$post_frm_blurb='')
		{
			/* a simple form has simple components */
			$simple_form = $pre_frm_blurb  ."\n"
				. '<form method="' . $frm_method . '" action="' . $frm_action  . '">' . "\n"
				. '<input type="'  . $input_type . '" value="'  . $input_value . '">' . "\n"
				. '</form>' . "\n"
				. $post_frm_blurb . "\n";
			return $simple_form;
		}

		function make_href_link_simple($pre_link_blurb='',$href_link='',$href_text='default text',$post_link_blurb='')
		{
			/* a simple href link has simple components */
			$simple_link = $pre_link_blurb
				. '<a href="' . $href_link . '">' . $href_text . '</a> '
				. $post_link_blurb . "\n";
			return $simple_link;
		}

		function login_form()
		{
			/* begin use TEMPLATE login_main.tpl */
			$GLOBALS['setup_tpl']->set_var('ConfigLoginMSG',
				@$GLOBALS['phpgw_info']['setup']['ConfigLoginMSG']
				? $GLOBALS['phpgw_info']['setup']['ConfigLoginMSG'] : '&nbsp;');

			$GLOBALS['setup_tpl']->set_var('HeaderLoginMSG',
				@$GLOBALS['phpgw_info']['setup']['HeaderLoginMSG']
				? $GLOBALS['phpgw_info']['setup']['HeaderLoginMSG'] : '&nbsp;');

			if ($GLOBALS['phpgw_info']['setup']['stage']['header'] == '10')
			{
				/*
				 Begin use SUB-TEMPLATE login_stage_header,
				 fills V_login_stage_header used inside of login_main.tpl
				*/
				$GLOBALS['setup_tpl']->set_var('lang_select',lang_select());
				if (count($GLOBALS['phpgw_domain']) > 1)
				{
					$domains = '';
					foreach($GLOBALS['phpgw_domain'] as $domain => $data)
					{
						$domains .= "<option value=\"$domain\" ";
						if(isset($GLOBALS['phpgw_info']['setup']['LastDomain']) && $domain == $GLOBALS['phpgw_info']['setup']['LastDomain'])
						{
							$domains .= ' SELECTED';
						}
						elseif($domain == $_SERVER['SERVER_NAME'])
						{
							$domains .= ' SELECTED';
						}
						$domains .= ">$domain</option>\n";
					}
					$GLOBALS['setup_tpl']->set_var('domains',$domains);

					// use BLOCK B_multi_domain inside of login_stage_header
					$GLOBALS['setup_tpl']->parse('V_multi_domain','B_multi_domain');
					// in this case, the single domain block needs to be nothing
					$GLOBALS['setup_tpl']->set_var('V_single_domain','');
				}
				else
				{
					reset($GLOBALS['phpgw_domain']);
					//$default_domain = each($GLOBALS['phpgw_domain']);
					$default_domain = key($GLOBALS['phpgw_domain']);
					$GLOBALS['setup_tpl']->set_var('default_domain_zero',$default_domain);

					/* Use BLOCK B_single_domain inside of login_stage_header */
					$GLOBALS['setup_tpl']->parse('V_single_domain','B_single_domain');
					/* in this case, the multi domain block needs to be nothing */
					$GLOBALS['setup_tpl']->set_var('V_multi_domain','');
				}
				/*
				 End use SUB-TEMPLATE login_stage_header
				 put all this into V_login_stage_header for use inside login_main
				*/
				$GLOBALS['setup_tpl']->parse('V_login_stage_header','T_login_stage_header');
			}
			else
			{
				/* begin SKIP SUB-TEMPLATE login_stage_header */
				$GLOBALS['setup_tpl']->set_var('V_multi_domain','');
				$GLOBALS['setup_tpl']->set_var('V_single_domain','');
				$GLOBALS['setup_tpl']->set_var('V_login_stage_header','');
			}
			/*
			 end use TEMPLATE login_main.tpl
			 now out the login_main template
			*/
			$GLOBALS['setup_tpl']->pparse('out','T_login_main');
		}

		/**
		 * Get a list of available template sets
		 *
		 * @internal this doesn't appear to be called from anywhere - and duplicated code from phpgwapi_common
		 */
		function get_template_list()
		{
			return $GLOBALS['phpgw']->common->template_list();
		}

		/**
		 * Get a list of available old style themes
		 *
		 * @todo FIXME This is broken - themes aren't done this way any more
		 */
		function list_themes()
		{
			$dh = dir(PHPGW_SERVER_ROOT . '/phpgwapi/themes');
			while ($file = $dh->read())
			{
				if (preg_match("/\.theme$/i", $file))
				{
					$list[] = substr($file,0,strpos($file,'.'));
				}
			}
			$dh->close();
			reset ($list);
			return $list;
		}
	}
