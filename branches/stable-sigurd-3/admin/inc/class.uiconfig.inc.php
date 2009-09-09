<?php
  /**************************************************************************\
  * phpGroupWare - Admin config                                              *
  * Written by Miles Lott <milosch@phpgroupware.org>                         *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

	class admin_uiconfig
	{
		public $public_functions = array('index' => True);

		function index()
		{
			$errors = '';
			$referer = phpgw::get_var('referer', 'url', 'GET');

			if($referer)
			{
				$_redir = $referer;
				$GLOBALS['phpgw']->session->appsession('session_data','admin_config',$referer);
			}
			else
			{
				$referer = $GLOBALS['phpgw']->session->appsession('session_data','admin_config');
				if($referer == -1)
				{
					$referer = '';
				}
				$_redir  = $referer ? $referer : $GLOBALS['phpgw']->link('/admin/index.php');
			}
						
			$appname = phpgw::get_var('appname', 'string', 'GET');
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "admin::{$appname}::index";

			$GLOBALS['phpgw_info']['apps']['manual']['app'] = $appname; // override the appname fetched from the referer for the manual.

			switch($appname)
			{
				case 'admin':
				//case 'preferences':
				//$appname = 'preferences';
				case 'addressbook':
				case 'calendar':
				case 'email':
				case 'nntp':
					/*
					Other special apps can go here for now, e.g.:
					case 'bogusappname':
					*/
					$config_appname = 'phpgwapi';
					break;
				case 'phpgwapi':
				case '':
					/* This keeps the admin from getting into what is a setup-only config */
					Header('Location: ' . $_redir);
					break;
				default:
					$config_appname = $appname;
					break;
			}

			$t =& $GLOBALS['phpgw']->template;
			$t->set_root($GLOBALS['phpgw']->common->get_tpl_dir($appname));

			$t->set_file(array('config' => 'config.tpl'));
			$t->set_block('config','body','body');

			$c = CreateObject('phpgwapi.config',$config_appname);
			$c->read();

			if ($c->config_data)
			{
				$current_config = $c->config_data;
			}

			if ( isset($_POST['cancel']) && $_POST['cancel'] )
			{
				Header('Location: ' . str_replace('&amp;', '&', $_redir) );
			}

			$errors = '';
			if ( isset($_POST['submit']) && $_POST['submit'] )
			{
				/* Load hook file with functions to validate each config (one/none/all) */
				$GLOBALS['phpgw']->hooks->single('config_validate',$appname);

				while (list($key,$config) = each($_POST['newsettings']))
				{
					if ($config)
					{
						if(isset($GLOBALS['phpgw_info']['server']['found_validation_hook']) && $GLOBALS['phpgw_info']['server']['found_validation_hook'] && function_exists($key))
						{
							call_user_func($key,$config);
							if($GLOBALS['config_error'])
							{
								$errors .= lang($GLOBALS['config_error']) . '&nbsp;';
								$GLOBALS['config_error'] = False;
							}
							else
							{
								$c->config_data[$key] = $config;
							}
						}
						else
						{
							$c->config_data[$key] = $config;
						}
					}
					else
					{
						/* don't erase passwords, since we also don't print them */
						if(!ereg('passwd',$key) && !ereg('password',$key) && !ereg('root_pw',$key))
						{
							unset($c->config_data[$key]);
						}
					}
				}
				if(isset($GLOBALS['phpgw_info']['server']['found_validation_hook']) && $GLOBALS['phpgw_info']['server']['found_validation_hook'] && function_exists('final_validation'))
				{
					final_validation($newsettings);
					if($GLOBALS['config_error'])
					{
						$errors .= lang($GLOBALS['config_error']) . '&nbsp;';
						$GLOBALS['config_error'] = False;
					}
					unset($GLOBALS['phpgw_info']['server']['found_validation_hook']);
				}

				$c->save_repository(True);

				if(!$errors)
				{
					$GLOBALS['phpgw']->session->appsession('session_data','admin_config',-1);
					Header('Location: ' . $_redir);
					$GLOBALS['phpgw_info']['flags']['nodisplay'] = true;
					exit;
				}
			}

			if(isset($errors) && $errors)
			{
				$t->set_var(array
				(
					'error'			=> lang('Error: %1', $errors),
					'error_class'	=> 'error'
				));
				unset($errors);
				unset($GLOBALS['config_error']);
			}
			else
			{
				$t->set_var(array
				(
					'error'			=> '',
					'error_class'	=> ''
				));
			}


			$t->set_var(array
			(
				'action_url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'admin.uiconfig.index', 'appname'=> $appname) ),
				'lang_cancel'	=> lang('cancel'),
				'lang_submit'	=> lang('save'),
				'title'			=> lang('Site Configuration'),
			));

	//		$t->unknown_regexp = 'loose';
			$vars = $t->get_undefined('body');
	//		$t->unknown_regexp = '';

			$GLOBALS['phpgw']->hooks->single('config',$appname);

			if(is_array($vars))
			{
				foreach ( $vars as $value )
				{
					$valarray = explode('_', $value);
					$type = $valarray[0];
					$new = $newval = '';

					while($chunk = next($valarray))
					{
						$new[] = $chunk;
					}
					$newval = implode(' ',$new);

					switch($type)
					{
						case 'lang':
							$t->set_var($value,$GLOBALS['phpgw']->translation->translate($newval, array(),false, $appname));
							break;
						case 'value':
							$newval = ereg_replace(' ','_',$newval);
							/* Don't show passwords in the form */
							if ( !isset($current_config[$newval]) || ereg('passwd',$value) || ereg('password',$value) || ereg('root_pw',$value))
							{
								$t->set_var($value,'');
							}
							else
							{
								$t->set_var($value,(isset($current_config[$newval])?$current_config[$newval]:''));
							}
							break;
						case 'checked':
							/* '+' is used as a delimiter for the check value */
							list($newvalue,$check) = split('\+',$newval);
							$newval = ereg_replace(' ','_',$newvalue);
							if($current_config[$newval] == $check)
							{
								$t->set_var($value, ' checked');
							}
							else
							{
								$t->set_var($value, '');
							}
							break;
						case 'selected':
							$configs = array();
							$config  = '';
							$newvals = explode(' ',$newval);
							$setting = end($newvals);
							for ($i=0;$i<(count($newvals) - 1); $i++)
							{
								$configs[] = $newvals[$i];
							}
							$config = implode('_',$configs);
							/* echo $config . '=' . $current_config[$config]; */
							if ( isset($current_config[$config]) 
								&& $current_config[$config] == $setting)
							{
								$t->set_var($value,' selected');
							}
							else
							{
								$t->set_var($value,'');
							}
							break;
						case 'hook':
							$newval = ereg_replace(' ','_',$newval);
							if(function_exists($newval))
							{
								$t->set_var($value,$newval($current_config));
							}
							else
							{
								$t->set_var($value,'');
							}
							break;
						default:
							$t->set_var($value,'');
							break;
					}
				}
			}
			$GLOBALS['phpgw']->common->phpgw_header(true);
			$t->pfp('out','config');
		}
	}
