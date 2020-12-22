<?php
	/**
	* Setup detection
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Miles Lott <milosch@phpgroupware.org>
	* @copyright Portions Copyright (C) 2001-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage application
	* @version $Id$
	*/

	/**
	* Setup detection
	* 
	* @package phpgwapi
	* @subpackage application
	*/
	class setup_detection 
	{
		function get_versions()
		{
			$d = dir( realpath(dirname(__FILE__) . '/../..') );
			while ( $entry = $d->read() )
			{
				if ( $entry != 'setup' && is_dir(PHPGW_SERVER_ROOT . '/' . $entry))
				{
					$f = PHPGW_SERVER_ROOT . '/' . $entry . '/setup/setup.inc.php';
					if ( file_exists($f) )
					{
						require $f;
						$setup_info[$entry]['filename'] = $f;
					}
				}
			}
			$d->close();

			// _debug_array($setup_info);
			ksort($setup_info);
			return $setup_info;
		}

		function get_db_versions($setup_info=array())
		{
			$tname = Array();
			$GLOBALS['phpgw_setup']->db->Halt_On_Error = 'no';
			$tables = $GLOBALS['phpgw_setup']->db->table_names();
			$newapps = '';
			$oldapps = '';
			if(count($tables) > 0 && is_array($tables))
			{
				foreach($tables as $key => $val)
				{
					$tname[] = $val;
				}
				$tbl_exists = in_array('phpgw_applications',$tname);
			}

			if( (count($tables) > 0) && (is_array($tables)) && $tbl_exists )
			{
				/* one of these tables exists. checking for post/pre beta version */
				$GLOBALS['phpgw_setup']->db->query('SELECT * FROM phpgw_applications',__LINE__,__FILE__);
				while($GLOBALS['phpgw_setup']->db->next_record())
				{
					$setup_info[$GLOBALS['phpgw_setup']->db->f('app_name')]['currentver'] = $GLOBALS['phpgw_setup']->db->f('app_version');
					$setup_info[$GLOBALS['phpgw_setup']->db->f('app_name')]['enabled'] = $GLOBALS['phpgw_setup']->db->f('app_enabled');
				}
				/* This is to catch old setup installs that did not have phpgwapi listed as an app */
				$tmp = null;
				if ( isset($setup_info['phpgwapi']['version']) )
				{
					$tmp = $setup_info['phpgwapi']['version']; /* save the file version */
				}

				if(!$setup_info['phpgwapi']['currentver'])
				{
					$setup_info['phpgwapi']['currentver'] = $setup_info['admin']['currentver'];
					$setup_info['phpgwapi']['version'] = $setup_info['admin']['currentver'];
					$setup_info['phpgwapi']['enabled'] = $setup_info['admin']['enabled'];
					// _debug_array($setup_info['phpgwapi']);exit;
					// There seems to be a problem here.  If ['phpgwapi']['currentver'] is set,
					// The GLOBALS never gets set.
					$GLOBALS['setup_info'] = $setup_info;
					$GLOBALS['phpgw_setup']->register_app('phpgwapi');
				}
				else
				{
					$GLOBALS['setup_info'] = $setup_info;
				}
				$setup_info['phpgwapi']['version'] = $tmp; /* restore the file version */
			}
			// _debug_array($setup_info);
			return $setup_info;
		}

		/* app status values:
		U	Upgrade required/available
		R	upgrade in pRogress
		C	upgrade Completed successfully
		D	Dependency failure
		P	Post-install dependency failure
		F	upgrade Failed
		V	Version mismatch at end of upgrade (Not used, proposed only)
		M	Missing files at start of upgrade (Not used, proposed only)
		*/
		function compare_versions($setup_info)
		{
			foreach($setup_info as $key => $value)
			{
				//echo '<br>'.$value['name'].'STATUS: '.$value['status'];
				/* Only set this if it has not already failed to upgrade - Milosch */
				if ( !isset($value['status']) || (! ($value['status'] == 'F' || $value['status'] == 'C') ) )
				{
					//if ($setup_info[$key]['currentver'] > $setup_info[$key]['version'])
					if ( isset($value['currentver']) && isset($value['version'])
						&& $GLOBALS['phpgw_setup']->amorethanb($value['currentver'], $value['version']) )
					{
						$setup_info[$key]['status'] = 'V';
					}
					else if ( isset($value['currentver']) && isset($value['version'])
						&& $value['currentver'] == $value['version'])
					{
						$setup_info[$key]['status'] = 'C';
					}
					else if ( isset($value['currentver']) && isset($value['version'])
						&& $GLOBALS['phpgw_setup']->alessthanb($value['currentver'], $value['version']))
					{
						$setup_info[$key]['status'] = 'U';
					}
					else
					{
						$setup_info[$key]['status'] = 'U';
					}
				}
			}
			// _debug_array($setup_info);
			return $setup_info;
		}

		function check_depends($setup_info)
		{
			/* Run the list of apps */
			foreach($setup_info as $key => $value)
			{
				/* Does this app have any depends */
				if(isset($value['depends']))
				{
								/* If so find out which apps it depends on */
					foreach($value['depends'] as $depkey => $depvalue)
					{
						/* I set this to False until we find a compatible version of this app */
						$setup_info['depends'][$depkey]['status'] = False;
						/* Now we loop thru the versions looking for a compatible version */
										
						foreach($depvalue['versions'] as $depskey => $depsvalue)
						{
							if ( !isset($setup_info[$depvalue['appname']]['currentver']) )
							{
								$setup_info[$depvalue['appname']]['currentver'] = null; //deals with undefined index notice
							}

							$major = $GLOBALS['phpgw_setup']->get_major($setup_info[$depvalue['appname']]['currentver']);
							if ($major == $depsvalue)
							{
								$setup_info['depends'][$depkey]['status'] = True;
							}
							else	// check if majors are equal and minors greater or equal
							{
								//the @ is used below to work around some sloppy coding, we should not always assume version #s will be X.Y.Z.AAA
								$major_depsvalue = $GLOBALS['phpgw_setup']->get_major($depsvalue);				
								$depsvalue_arr = explode('.', $depsvalue);
								$minor_depsvalue = isset($depsvalue_arr[3]) ? $depsvalue_arr[3] : null;
		//						@list(,,,$minor_depsvalue) = explode('.', $depsvalue);
								$currentver_arr =  explode('.', $setup_info[$depvalue['appname']]['currentver']);
								$minor = isset($currentver_arr[3]) ? $currentver_arr[3] : null;
		//						@list(,,,$minor) = explode('.', $setup_info[$depsvalue['appname']]['currentver']);
								if ($major == $major_depsvalue && $minor <= $minor_depsvalue)
								{
									$setup_info['depends'][$depkey]['status'] = True;
								}
							}
						}
					}
					/*
					 Finally, we loop through the dependencies again to look for apps that still have a failure status
					 If we find one, we set the apps overall status as a dependency failure.
					*/
					foreach($value['depends'] as $depkey => $depvalue)
					{
						if ($setup_info['depends'][$depkey]['status'] == False)
						{
							/* Only set this if it has not already failed to upgrade - Milosch */
							if($setup_info[$key]['status'] != 'F')//&& $setup_info[$key]['status'] != 'C')
							{
								if($setup_info[$key]['status'] == 'C')
								{
									$setup_info[$key]['status'] = 'D';
								}
								else
								{
									$setup_info[$key]['status'] = 'P';
								}
							}
						}
					}
				}
			}
			return $setup_info;
		}

		/*
		 Called during the mass upgrade routine (Stage 1) to check for apps
		 that wish to be excluded from this process.
		*/
		function upgrade_exclude($setup_info)
		{
			foreach($setup_info as $key => $value)
			{
				if(isset($value['no_mass_update']) || !isset($value['enable']))
				{
					unset($setup_info[$key]);
				}
			}
			return $setup_info;
		}

		/*
		 Called during the first install.
		 only install core tables and the admin and preferences applications
		*/
		function base_install($setup_info)
		{
			$core_elements = array
			(
				'phpgwapi'		=> true,
				//if this isn't here, it can never be installed as it is part of the api - skwashd
				'notifywindow'	=> true ,
				'admin'			=> true,
				'preferences'	=> true
			);
			
			
			foreach ( array_keys($setup_info) as $key )
			{
				if ( !isset($core_elements[$key])
					|| !$core_elements[$key] )
				{
					unset($setup_info[$key]);
				}
			}
			return $setup_info;
		}


		function check_header()
		{
			if(!file_exists('../header.inc.php'))
			{
				$GLOBALS['phpgw_info']['setup']['header_msg'] = 'Stage One';
				return 1;
			}
			else
			{
				if (!isset($GLOBALS['phpgw_info']['server']['header_admin_password']))
				{
					$GLOBALS['phpgw_info']['setup']['header_msg'] = 'Stage One (No header admin password set)';
					return 2;
				}
				elseif (!isset($GLOBALS['phpgw_domain']))
				{
					$GLOBALS['phpgw_info']['setup']['header_msg'] = 'Stage One (Upgrade your header.inc.php)';
					return 3;
				}
				elseif ( isset($GLOBALS['phpgw_info']['server']['versions']['header']) 
					&& isset($GLOBALS['phpgw_info']['server']['versions']['current_header'])
					&& $GLOBALS['phpgw_info']['server']['versions']['header'] != $GLOBALS['phpgw_info']['server']['versions']['current_header'])
				{
					$GLOBALS['phpgw_info']['setup']['header_msg'] = 'Stage One (Upgrade your header.inc.php)';
					return 3;
				}
			}
			/* header.inc.php part settled. Moving to authentication */
			$GLOBALS['phpgw_info']['setup']['header_msg'] = 'Stage One (Completed)';
			return 10;
		}

		function check_db()
		{
			$setup_info = $GLOBALS['setup_info'];

			$GLOBALS['phpgw_setup']->db->Halt_On_Error = 'no';
			// _debug_array($setup_info);

			//error message supression
			if( isset($GLOBALS['DEBUG']) && $GLOBALS['DEBUG'] )
			{
				flush(); //push what we have
				ob_start(); //get the output
			}

			if(!isset($setup_info['phpgwapi']['currentver']))
			{
				try
				{
					$setup_info = $this->get_db_versions($setup_info);
				}
				catch(Exception $e)
				{
					return 1;
				}
			}

			// _debug_array($setup_info);
			if (isset($setup_info['phpgwapi']['currentver']))
			{
				if ( isset($setup_info['phpgwapi']['version'])
					&& $setup_info['phpgwapi']['currentver'] == $setup_info['phpgwapi']['version'])
				{
					$GLOBALS['phpgw_info']['setup']['header_msg'] = 'Stage 1 (Tables Complete)';
					return 10;
				}
				else
				{
					$GLOBALS['phpgw_info']['setup']['header_msg'] = 'Stage 1 (Tables need upgrading)';
					return 4;
				}
			}
			else
			{
				/* no tables, so checking if we can create them */
				$GLOBALS['phpgw_setup']->db->query('CREATE TABLE phpgw_testrights ( testfield varchar(5) NOT NULL )',__LINE__,__FILE__);
				
				if ( ob_get_contents() )
				{
					ob_end_clean();//dump the output
				}
				
				if( $GLOBALS['phpgw_setup']->db->link_id() 
					&& !isset($GLOBALS['phpgw_setup']->db->Errno) )
				{
					$GLOBALS['phpgw_setup']->db->query('DROP TABLE phpgw_testrights',__LINE__,__FILE__);
					$GLOBALS['phpgw_info']['setup']['header_msg'] = 'Stage 3 (Install Applications)';
					return 3;
				}
				else
				{
					$GLOBALS['phpgw_info']['setup']['header_msg'] = 'Stage 1 (Create Database)';
					return 1;
				}
			}
		}

		function check_config()
		{
			$GLOBALS['phpgw_setup']->db->Halt_On_Error = 'no';
			if( !isset($GLOBALS['phpgw_info']['setup']['stage']['db'])
				|| $GLOBALS['phpgw_info']['setup']['stage']['db'] != 10 )
			{
				return '';
			}

			$GLOBALS['phpgw_setup']->db->query("SELECT config_value FROM phpgw_config WHERE config_name='freshinstall'",__LINE__,__FILE__);
			$GLOBALS['phpgw_setup']->db->next_record();
			$configed = $GLOBALS['phpgw_setup']->db->f('config_value');
			if($configed)
			{
				$GLOBALS['phpgw_info']['setup']['header_msg'] = 'Stage 2 (Needs Configuration)';
				return 1;
			}
			else
			{
				$GLOBALS['phpgw_info']['setup']['header_msg'] = 'Stage 2 (Configuration OK)';
				return 10;
			}
		}

		function check_lang($check = true)
		{
			$GLOBALS['phpgw_setup']->db->Halt_On_Error = 'no';
			if ( $check 
				&& (!isset($GLOBALS['phpgw_info']['setup']['stage']['db'])
					|| $GLOBALS['phpgw_info']['setup']['stage']['db'] != 10 ) )
			{
				return '';
			}
			if (!$check)
			{
				if ( !isset($GLOBALS['setup_info']) || !is_array($GLOBALS['setup_info']) )
				{
					$GLOBALS['setup_info'] = array();
				}

				$GLOBALS['setup_info'] = $this->get_db_versions($GLOBALS['setup_info']);
			}

			$langtbl  = '';
			$languagestbl = '';

			$GLOBALS['phpgw_info']['setup']['installed_langs'] = array();

			$GLOBALS['phpgw_setup']->db->query("SELECT COUNT(DISTINCT lang) as langcount FROM phpgw_lang",__LINE__,__FILE__);
			$GLOBALS['phpgw_setup']->db->next_record();
			if($GLOBALS['phpgw_setup']->db->f('langcount') == 0)
			{
				$GLOBALS['phpgw_info']['setup']['header_msg'] = 'Stage 3 (No languages installed)';
				return 1;
			}
			else
			{
				$GLOBALS['phpgw_setup']->db->query($q = "SELECT DISTINCT lang FROM phpgw_lang",__LINE__,__FILE__);
				while($GLOBALS['phpgw_setup']->db->next_record())
				{
					$GLOBALS['phpgw_info']['setup']['installed_langs'][$GLOBALS['phpgw_setup']->db->f('lang')] = $GLOBALS['phpgw_setup']->db->f('lang');
				}
				foreach($GLOBALS['phpgw_info']['setup']['installed_langs'] as $key => $value)
				{
					$sql = "SELECT lang_name FROM phpgw_languages WHERE lang_id = '{$value}'";
					$GLOBALS['phpgw_setup']->db->query($sql,__LINE__,__FILE__);
					$GLOBALS['phpgw_setup']->db->next_record();
					$GLOBALS['phpgw_info']['setup']['installed_langs'][$value] = $GLOBALS['phpgw_setup']->db->f('lang_name');
				}
				$GLOBALS['phpgw_info']['setup']['header_msg'] = 'Stage 3 (Completed)';
				return 10;
			}
		}

		/**
		* Verify that all of an app's tables exist in the db
		* @param $appname
		* @param $any optional, set to True to see if any of the apps tables are installed
		*/
		function check_app_tables($appname,$any=False)
		{
			$none = 0;
			$setup_info = $GLOBALS['setup_info'];

			if (isset($setup_info[$appname]['tables']) 
				&& $setup_info[$appname]['tables'] )
			{
				/* Make a copy, else we send some callers into an infinite loop */
				$copy = $setup_info;
				$GLOBALS['phpgw_setup']->db->Halt_On_Error = 'no';
				$table_names = $GLOBALS['phpgw_setup']->db->table_names();
				$tables = Array();
				foreach($table_names as $key => $val)
				{
					$tables[] = $val;
				}
				foreach($copy[$appname]['tables'] as $key => $val)
				{
					if($GLOBALS['DEBUG'])
					{
						echo '<br>check_app_tables(): Checking: ' . $appname . ',table: ' . $val;
					}
					if(!in_array($val,$tables))
					{
						if($GLOBALS['DEBUG'])
						{
							echo '<br>check_app_tables(): ' . $val . ' missing!';
						}
						if(!$any)
						{
							return False;
						}
						else
						{
							$none++;
						}
					}
					else
					{
						if($any)
						{
							if($GLOBALS['DEBUG'])
							{
								echo '<br>check_app_tables(): Some tables installed';
							}
							return True;
						}
					}
				}
			}
			if($none && $any)
			{
				if($GLOBALS['DEBUG'])
				{
					echo '<br>check_app_tables(): No tables installed';
				}
				return False;
			}
			else
			{
				if($GLOBALS['DEBUG'])
				{
					echo '<br>check_app_tables(): All tables installed';
				}
				return True;
			}
		}
	}
