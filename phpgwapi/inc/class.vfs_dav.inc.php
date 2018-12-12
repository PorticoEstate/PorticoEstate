<?php
	/**
	* Virtual File System
	* @author Jason Wies <zone@phpgroupware.org>
	* @copyright Copyright (C) 2001-2003 Jason Wies, Johnathan Sim
	* @copyright Portions Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage vfs
	* @version $Id$
	*/

	/**
	* Enables debug output for this class
	*/
	define ('DEBUG', 0);
	/**
	* This generates a whole lotta output
	*/
	define ('TRACE', 0);
	/**
	* Enables some SQL debugging
	*/
	define ('DEBUG_SQL', 0);
	/**
	* Enables (LOTS) of debugging inside the HTTP class
	*/
	define ('DEBUG_DAV', 0);

	/**
	* DEBUG_LS is to debug ls only :)
	*/
	define ('DEBUG_LS', 0);

	/**
	* Virtual File System
	* 
	* @package phpgwapi
	* @subpackage vfs
	*/
	class phpgwapi_vfs extends phpgwapi_vfs_shared
	{
		/*
		* That's the history add to the path !
		*/
		var $svn_history_path = '/!svn/bc/';
		//This is a usefull add for svn case :
		//Usable only in this case !
		var $svn_repository_path = '';
		
		/*
		* the whole dav needed infos
		*/
		var $dav_user = '';
		var $dav_pwd = '';
		var $dav_host = '';
		var $dav_port = '80';
		var $dav_root = '';

		/*
		* Internal : the dav http client
		*/
		var $dav_client;

		//Only 3 attributes are missing : link_name / link_directory and version
		//These are DAV-native properties that have different names in VFS
		var $vfs_property_map = array(
			'creationdate' => 'created',
			'getlastmodified' => 'modified',
			'getcontentlength' => 'size',
			'getcontenttype' => 'mime_type',
			'description' => 'comment',
			'creator_id' => 'createdby_id',
			'contributor_id' => 'modifiedby_id',
			'publisher_id' => 'owner_id',
			'lockdiscovery' => 'locks',
			'source'	=>	'app',
			'getetag'	=> 'file_id',
		);
		
		/**
		 * constructor, sets up variables
		*
		 */

		function __construct()
		{
			parent::__construct();
			/*
			   File/dir attributes, each corresponding to a database field.  Useful for use in loops
			   If an attribute was added to the table, add it here and possibly add it to
			   set_attributes ()

			   set_attributes now uses this array().   07-Dec-01 skeeter
			*/

//			$this->attributes[] = '#NAMEHERE#';

			/* Dav properties */
			//First check if we are asked to use svn ...
			$use_svn = false;
			if(preg_match('/svn[s:][:\/]\//', $this->basedir))
			{
				$use_svn = true;
				//so the 's' is kept

				$this->basedir = preg_replace('/^svn/', 'http', $this->basedir);

			}
			
			if(!$this->dav_user)
			{
				$this->dav_user = isset($GLOBALS['phpgw_info']['user']['userid']) ? (string) $GLOBALS['phpgw_info']['user']['userid'] : '';
			}
			//FIXME pwd has to be clear text.
			if(!$this->dav_pwd)
			{
				$this->dav_pwd = isset($GLOBALS['phpgw_info']['user']['passwd']) ? (string) $GLOBALS['phpgw_info']['user']['passwd'] : '';
			}
			// For testing purpose:
//			$this->dav_user = 'www-data';
//			$this->dav_pwd = 'xxxxxx';


			$parsed_url = parse_url($this->basedir);
			$this->dav_host = $parsed_url['host'];
			$this->dav_port = isset($parsed_url['port']) ? $parsed_url['port'] : '';
			$this->dav_root = $parsed_url['scheme'].'://';
			$this->dav_root .= $this->dav_host;
			$this->dav_root .= (empty($this->dav_port)) ? '' : ':'.$this->dav_port;

			if($use_svn)
			{
				$this->dav_client = CreateObject('phpgwapi.http_svn_client');
				$this->svn_repository_path = $parsed_url['path'];
			}
			else
			{
				$this->dav_client = CreateObject('phpgwapi.http_dav_client');
			}
			$this->dav_client->set_credentials($this->dav_user,$this->dav_pwd);
			$this->dav_client->set_attributes($this->attributes,$this->vfs_property_map);
			$result = $this->dav_client->connect($this->dav_host,$this->dav_port,$parsed_url['scheme'] == 'https');
			if (DEBUG_DAV) 
			{
				echo '<b>DAV client debugging enabled!</b>';
				$this->dav_client->set_debug(DBGTRACE|DBGINDATA|DBGOUTDATA|DBGSOCK|DBGLOW);
			}
			if (!$result)
			{
				echo '<h2>Cannot connect to the file repository server!</h2>';
				die($this->dav_client->get_body());
			}
			//determine the supported DAV features
/*			$features = $this->dav_client->dav_features($this->repository);
			if (!$features || ! in_array( '1', $features) )
			{
				die("Error :: The specified file repository: $this->repository doesn't appear to support WebDAV! ");
			
			}
*/	

			register_shutdown_function(array(&$this, 'vfs_umount'));
			$this->debug('Constructed with debug enabled');
		}

		//TODO:  Get rid of this
		//A quick, temporary debug output function
		function debug($info,$debug=false) {
			if ($debug || DEBUG)
			{
				echo '<b> vfs_dav debug:<em> ';
				if (is_array($info))
				{
					print_r($info);
				}
				else
				{
					echo $info;
				}
				echo '</em></b><br>';
			}
		}

		/**
		 * Apaches mod_dav in particular requires that the path sent in a dav request NOT be a URI
		*
		 */
		function dav_path($uri) {
			//$this->debug('DAV path');
			$parsed = parse_url($uri);
			return $parsed['path'];
		}

		/**
		 * glues a parsed url (ie parsed using PHP's parse_url) back
		*
		 * 	together
		 * @param $url	The parsed url (its an array)
		 */
		function glue_url ($url){
			if (!is_array($url))
			{
				return false;
			}
			// scheme
			$uri = (!empty($url['scheme'])) ? $url['scheme'].'://' : '';
			// user & pass
			if (!empty($url['user']))
			{
				$uri .= $url['user'];
				if (!empty($url['pass']))
				{
					$uri .=':'.$url['pass'];
				}
				$uri .='@'; 
			}
			// host 
			$uri .= $url['host'];
			// port 
			$port = (!empty($url['port'])) ? ':'.$url['port'] : '';
			$uri .= $port; 
			//reposistory
			if(isset($url['repos']))
			{
				$uri .= $url['repos'];
			}
			//path his
			if(isset($url['pathhis']))
			{
				$uri .= $url['pathhis']; 
			}
			//revision
			if(isset($url['rev']))
			{
				$uri .= $url['rev'];
			}
			// path
			if(isset($url['path']))
			{
				$uri .= $url['path'];
			}
			// fragment or query
			if (isset($url['fragment']))
			{
				$uri .= '#'.$url['fragment'];
			} elseif (isset($url['query']))
			{
				$uri .= '?'.$url['query'];
			}
			return $uri;
		}

		function dav_host($uri) {
			//$this->debug('DAV path');
			$parsed = parse_url($uri);
			$parsed['path'] = '';
			$host = $this->glue_url($parsed);
			return $host;
		}

		function vfs_umount()
		{
			$this->dav_client->disconnect();
		}


		/**
		 * Add a journal entry after (or before) completing an operation,
		*
		 * 	  and increment the version number.  This function should be used internally only
		 * Note that state_one and state_two are ignored for some VFS_OPERATION's, for others
		 * 		 * they are required.  They are ignored for any "custom" operation
		 * 		 * The two operations that require state_two:
		 * 		 * operation		 * 	state_two
		 * 		 * VFS_OPERATION_COPIED	fake_full_path of copied to
		 * 		 * VFS_OPERATION_MOVED		 * fake_full_path of moved to

		 * 		 * If deleting, you must call add_journal () before you delete the entry from the database
		 * @param string File or directory to add entry for
		 * @param relatives Relativity array
		 * @param operation The operation that was performed.  Either a VFS_OPERATION define or
		 * 		 *   a non-integer descriptive text string
		 * @param state_one The first "state" of the file or directory.  Can be a file name, size,
		 * 		 *   location, whatever is appropriate for the specific operation
		 * @param state_two The second "state" of the file or directory
		 * @param incversion Boolean True/False.  Increment the version for the file?  Note that this is
		 * 		 *    handled automatically for the VFS_OPERATION defines.
		 * 		 *    i.e. VFS_OPERATION_EDITED would increment the version, VFS_OPERATION_COPIED
		 * 		 *    would not
		 * @return Boolean True/False
		 */
		function add_journal ($data) {
		//The journalling dont work :(  Ideally this will become "versioning"
			return True;
		}


		/*
		* Not done, but should be internal
		*/
		function flush_journal ($data)
		{
			return True;
		}


		/*
		* See vfs_shared
		*/
		function get_journal ($data)
		{
			$default_values = array
			(
				'relatives'	=> array (RELATIVE_CURRENT),
				'type'	=> False
			);

			$data = array_merge ($this->default_values ($data, $default_values), $data);
			$from_rev=isset($data['from_rev']) ? $data['from_rev'] : 0;
			$to_rev=isset($data['to_rev']) ? $data['to_rev'] : 0;
			$version=isset($data['vers']) ? $data['vers'] : 0; 
			$collapse=isset($data['collapse']) ? $data['collapse'] : false;
		
			if(!$from_rev || ! $to_rev) 
			{		
				$from_rev=0;
				$to_rev=$version;
			}

			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			if (!$this->acl_check (array(
					'string' => $p->fake_full_path,
					'relatives' => array ($p->mask)
				)))
			{
				return False;
			}
			$rarray=array();
			$result=$this->dav_client->getVersions($this->svn_repository_path.$p->fake_full_path,$from_rev,$to_rev);
			$rarray=$result;
			if(isset($collapse) && $collapse && count($rarray)>0)
			{
				$rarray=$this->dav_client->collapse($result);
			}
			$rrarray=array_reverse($rarray);
			return $rrarray;
		}

		/*
		* See vfs_shared
		* Additional note :
			"clean" values are run through vfs->clean_string () and
			are not parsed through dav_path in our case so contains the
			full URL of the file/dir, so are used mostly internally.
		*/
/*		function path_parts ($data)
		{
			//We need to overload the parent method because we need to adapt
			//the real_full_path and real_leading_dirs to avoid breaking things
			//We need URI in them and not URL !
			$obj = parent::path_parts($data);
			if( is_object($obj) )
			{
				$obj->real_full_path = $this->dav_path($obj->real_full_path);
				$obj->real_leading_dirs = $this->dav_path($obj->real_leading_dirs);
			}
			elseif ( is_array($obj) )
			{
				$obj['real_full_path'] = $this->dav_path($obj['real_full_path']);
				$obj['real_leading_dirs'] = $this->dav_path($obj['real_leading_dirs']);
			}
			return $obj;
		}
*/
		/*
		* See vfs_shared
		*/
		function acl_check ($data)
		{
			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT),
					'operation'	=> PHPGW_ACL_READ,
					'must_exist'	=> False,
					'checksubdirs'	=> False,
					'nofiles'	=> True
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			/* Accommodate special situations */
			if ($this->override_acl || $data['relatives'][0] == RELATIVE_USER_APP)
			{
				return True;
			}

			if (!isset($data['owner_id']) || !$data['owner_id'])
			{
				$p = $this->path_parts (array(
						'string'	=> $data['string'],
						'relatives'	=> array ($data['relatives'][0])
					)
				);

				/* Temporary, until we get symlink type files set up */
				if ($p->outside)
				{
					return True;
				}

				/* Read access is always allowed here, but nothing else is */
				if ($data['string'] == '/' || $data['string'] == $this->fakebase)
				{
					if ($data['operation'] == PHPGW_ACL_READ)
					{
						return True;
					}
					else
					{
						return False;
					}
				}

				/* If the file doesn't exist, we get ownership from the parent directory */
				if (!$this->file_exists (array(
						'string'	=> $p->fake_full_path,
						'relatives'	=> array ($p->mask)
					))
				)
				{
					if ($data['must_exist'])
					{
						return False;
					}

					$data['string'] = $p->fake_leading_dirs;
					$p2 = $this->path_parts (array(
							'string'	=> $data['string'],
							'relatives'	=> array ($p->mask)
						)
					);

					if (!$this->file_exists (array(
							'string'	=> $data['string'],
							'relatives'	=> array ($p->mask)
						))
					)
					{
						return False;
					}
				}
				else
				{
					$p2 = $p;
				}
				$prop =	$this->get_properties($p2->real_full_path,0,($p2->real_full_path != $p->real_full_path));
				$owner_id = -1;
				if ( $prop == False )
				{
					return False; // we get a 401
				}
				if ( is_array($prop[$p2->real_full_path]) )
				{
					if(isset($prop[$p2->real_full_path]['owner_id']))
					{
						$owner_id = $prop[$p2->real_full_path]['owner_id'];
					}
					else
					{
						$this->debug('No owner know, please check why !');
						$owner_id = 0;
					}
				}
			}
			else
			{
				$owner_id = $data['owner_id'];
			}
			if ($owner_id == -1) { _debug_array(debug_backtrace()); }
			/* This is correct.  The ACL currently doesn't handle undefined values correctly */
			if (!$owner_id)
			{
				$owner_id = 0;
			}

			$user_id = $GLOBALS['phpgw_info']['user']['account_id'];

			/* They always have access to their own files */
			if ($owner_id == $user_id)
			{
				return True;
			}

			/* Check if they're in the group */
			$memberships = $GLOBALS['phpgw']->accounts->membership ($user_id);
			$group_ok=0;
			if (is_array ($memberships))
			{
				//reset ($memberships);
				//while (list ($num, $group_array) = each ($memberships))
				foreach($memberships as $num => $group_array)
				{
					if ($owner_id == $group_array->id)
					{
						$group_ok = 1;
						break;
					}
				}
			}

			$acl = CreateObject ('phpgwapi.acl', $owner_id);
			$acl->set_account_id($owner_id);

			$rights = $acl->get_rights ($user_id);

			/* Add privileges from the groups this user belongs to */
			if (is_array ($memberships))
			{
				//reset ($memberships);
				//while (list ($num, $group_array) = each ($memberships))
				foreach($memberships as $num => $group_array)
				{
					$rights |= $acl->get_rights ($group_array->id);
				}
			}

			if ($rights & $data['operation'])
			{
				return True;
			}
			elseif (!$rights && $group_ok)
			{
				$conf = CreateObject('phpgwapi.config', 'phpgwapi');
				$conf->read();
				if ($conf->config_data['acl_default'] == 'grant')
				{
					return True;
				}
				else
				{
					return False;
				}
			}
			else
			{
				return False;
			}
		}

		/**
		* DAV (class 2) locking - sets an exclusive write lock
		*
		* @param string filename
		* @param relatives Relativity array
		* @result True if successfull 
		*/		
		function lock ($data)
		{
			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT),
					'timeout'	=> 'Infinite',
					'owner_lid' => $this->dav_user
				);

			$data = array_merge($this->default_values($data,$default_values),$data);

			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);
			if ( !$this->dav_client->lock($p->real_full_path, $data['owner_lid'], 0, $data['timeout']) )
			{
				$this->str_lock_arror = $this->dav_client->str_dav_error;
				return False;
			}
			else
			{
				return True;
			}	
		}

		/*
		@function lock_token
		@discussion retrieve a lock_token
		@param array $data : the traditional $data array (string => , relatives => )
		@param boolean $dontcareowner : (True) if False, we want that the 
		returned token is owned by the current user.
		@return mixed $token : False or the token string
		*/
		function lock_token ($data)
		{
			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT),
					'token' => ''
				);
			
			$data = array_merge ($this->default_values ($data, $default_values), $data);
			if ( isset($data['owner_lid']) )
			{
				if ( empty($data['owner_lid']) )
				{
					$data['owner_lid'] = $this->dav_user;
				}
				$dontcareowner = False;
			}
			else
			{
				$dontcareowner = True;
			}
			$p = $this->path_parts($data);
			$data['operation'] = PHPGW_ACL_READ | PHPGW_ACL_EDIT | PHPGW_ACL_ADD;
			if ( ! $this->acl_check($data) )
			{
				_debug_array('You don\' have the right to get this token !');
				return False;
			}
			$props = $this->get_properties($p->real_full_path,0);
			$locks = $props[$p->real_full_path]['locks']['activelock'];
			foreach ($locks as $lock)
			{
				if ( !$dontcareowner )
				{
					if ($lock['owner']['name'] == $data['owner_lid'] )
					{
						$token = @end($lock['locktoken']);
						return $token['full_name'];
					}
					else
					{
						continue;
					}
				}
				else
				{
					$token = @end($lock['locktoken']);
					return $token['full_name'];
				}
			}
			return False;
		}

		
		/**
		* DAV (class 2) unlocking - unsets the specified lock
		* @param string filename
		* @param relatives Relativity array
		* @param token	The token for the lock we wish to remove.
		* @return bool true if successfull
		*/		
		function unlock ($data)
		{
			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT),
					'content'	=> '',
					'token' => ''
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			if ( empty($data['token']) )
			{
				return False;
			}
			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);
			$this->remove_lock_override (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);
			if ( !$this->dav_client->unlock($p->real_full_path, $data['token']) )
			{
				$this->str_lock_error = $this->str_dav_error;
				return False;
			}
			else
			{
				return True;
			}
		}

		/**
		* Allows querying for optional features - esp optional DAV features like locking
		*
		* @internal	This should really check the server.  Unfortunately the overhead of doing 
		* this in every VFS instance is unacceptable (it essentially doubles the time for 
		* any request). Ideally we would store these features in the session perhaps?
		* 
		* @param option	The option you want to test for.  Options include 'LOCKING'
		*	'VIEW', 'VERSION-CONTROL (eventually) etc
		* @return true if the specified option is supported
		*/		
		function options($option)
		{
			switch ($option)
			{
			case 'LOCKING':
				return true;
			case 'VIEW':
				return true;
			default:
				return false;
			}
		}

		/*
		* See vfs_shared
		*/
		function read ($data)
		{

			/*If the user really wants to 'view' the file in the browser, it
			is much smarter simply to redirect them to the files web-accessable
			url */
/*			$app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			if ( ! $data['noview'] && ($app == 'phpwebhosting' || $app = 'filemanager' ))
			{
				$this->view($data);
			}	
*/			
			if (!is_array ($data))
			{
				$data = array ();
			}

			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT)
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			if (!$this->acl_check (array(
					'string'	=> $p->fake_full_path,
					'relatives'	=> array ($p->mask),
					'operation'	=> PHPGW_ACL_READ
				))
			)
			{
				return False;
			}
			if ($p->outside)
			{
						
				if (! $fp = fopen ($p->real_full_path, 'r')) 
				{
					return False;
				}
				$size=filesize($p->real_full_path);
				$buffer=fread($fp, $size);
				fclose ($fp);
				return $buffer;
			}
			else
			{
				$status=$this->dav_client->get($this->dav_path($p->real_full_path));
	$this->debug($this->dav_client->get_headers());
	
				if($status != 200) return False;
				$contents=$this->dav_client->get_body();
	$this->debug('Read:returning contents.  Status:'.$status);
				return $contents;
			}
		}

		/*
		* See vfs_shared
		* @discussion In the case of WebDAV, the file is web-accessible.  So instead
		* of reading it into memory and then dumping it back out again when someone
		* views a file, it makes much more sense to simply redirect, which is what 
		* this method does (its only called when reading from the file in the file manager,
		* when the variable "noview" isnt set to "true"
		*/
		function view($data)
		{	
		
			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT)
				);
			$data = array_merge ($this->default_values ($data, $default_values), $data);
			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);
			$parsed_url = parse_url($p->real_full_path);
			$parsed_url['user'] = $this->dav_user;
			if(isset($data['rev']))
			{
				$parsed_url['path'] = $p->fake_full_path;
				$parsed_url['pathhis'] =$this->svn_history_path;
				$parsed_url['repos'] = $this->svn_repository_path;
				$parsed_url['rev'] = $data['rev'];
			}
			//XXX Do we need to include the user password here ?
			//Do it a server config perhaps ?
			if ( isset($GLOBALS['phpgw_info']['server']['include_pwd']) && $GLOBALS['phpgw_info']['server']['include_pwd'] )
			{
				$parsed_url['pwd'] = $this->dav_pwd;
			}

			$location = $this->glue_url($parsed_url);

			header( 'Location: '.$location, true);
			$GLOBALS['phpgw']->common->phpgw_exit();
		}
		
		/*
		* See vfs_shared
		*/
		function write ($data)
		{
			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT),
					'content'	=> ''
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			if ($this->file_exists (array (
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				))
			)
			{
				$acl_operation = PHPGW_ACL_EDIT;
				$journal_operation = VFS_OPERATION_EDITED;
			}
			else
			{
				$acl_operation = PHPGW_ACL_ADD;
			}

			if (!$this->acl_check (array(
					'string'	=> $p->fake_full_path,
					'relatives'	=> array ($p->mask),
					'operation'	=> $acl_operation
				))
			)
			{
				return False;
			}

			//umask(000);

			
			$size=strlen($data['content']);
			if ($p->outside)
			{
				if (! $fp = fopen ($p->real_full_path, 'w')) 
				{
					return False;
				}
				$result = fwrite($fp, $data['content']);
				fclose ($fp);
				return $result;
			}
			else
			{
				$token = isset($this->override_locks[$p->real_full_path]) ? $this->override_locks[$p->real_full_path] : '';
				$status=$this->dav_client->put($this->dav_path($p->real_full_path),$data['content'],$token);
$this->debug('Put complete,  status: '.$status);
				if(intval($status) != 201 && intval($status) != 204) 
				{
$this->debug('The file was not created !');
					return False;
				}
				else
				{
$this->debug('The file was created !');
					/*
					   If 'string' doesn't exist, touch () creates both the file and the database entry
					   If 'string' does exist, touch () sets the modification time and modified by
					*/
					$this->touch (array(
							'string'	=> $p->fake_full_path,
							'relatives'	=> array ($p->mask)
						),
						true
					);

					$this->correct_attributes (array(
							'string'	=> $p->fake_full_path,
							'relatives'	=> array ($p->mask)
						)
					);
					
					return True;
				}
			}
		}

		/*
		* See vfs_shared
		*/
		function touch ($data,$_inwrite=false)
		{
			$default_values = array(
						'relatives'	=> array (RELATIVE_CURRENT)
						);
			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$account_id = $GLOBALS['phpgw_info']['user']['account_id'];
			$currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];

			$p = $this->path_parts (array(
							  'string'	=> $data['string'],
							  'relatives'	=> array ($data['relatives'][0])
							  )
						);
			umask (000);

			/*
			   PHP's touch function will automatically decide whether to
			   create the file or set the modification time
			*/
			if($p->outside)
			{
			  return @touch($p->real_full_path);
			}
			elseif ($this->file_exists (array(
					'string'	=> $p->fake_full_path,
					'relatives'	=> array ($p->mask)
				))
			)
			{
				$result =  $this->set_attributes (array(
						'string'	=> $p->fake_full_path,
						'relatives'	=> array ($p->mask),
						'attributes'	=> array(
									'modifiedby_id' => $account_id,
									'modified' => $this->now,
									'app' => $currentapp
						)));
			}
			else
			{
				if (!$this->acl_check (array(
						'string'	=> $p->fake_full_path,
						'relatives'	=> array ($p->mask),
						'operation'	=> PHPGW_ACL_ADD
					))
				) return False;
				if ( $_inwrite )
				{
					_debug_array($data);
					die('should be already created ! Please Fill a bug report !!');
				}
				$result = $this->write (array(
							  'string'	=> $data['string'],
							  'relatives'	=> array ($data['relatives'][0]),
							  'content' => ''
							  ));
				$this->set_attributes(array(
					'string'	=> $p->fake_full_path,
					'relatives'	=> array ($p->mask),
					'attributes'	=> array (
								'createdby_id' => $account_id,
								'created' => $this->now,
								'app' => $currentapp
							)));
			}

			return ($result);
		}

		/*
		* See vfs_shared
		*/
		function cp ($data)
		{
			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT, RELATIVE_CURRENT)
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$account_id = $GLOBALS['phpgw_info']['user']['account_id'];
$this->debug('cp : data :');
$this->debug($data);
			$f = $this->path_parts (array(
					'string'	=> $data['from'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			$t = $this->path_parts (array(
					'string'	=> $data['to'],
					'relatives'	=> array ($data['relatives'][1])
				)
			);

			if (!$this->acl_check (array(
					'string'	=> $f->fake_full_path,
					'relatives'	=> array ($f->mask),
					'operation'	=> PHPGW_ACL_READ
				))
			)
			{
$this->debug('cp : from forbidden by ACL !');
				return False;
			}

			if ($this->file_exists (array(
					'string'	=> $t->fake_full_path,
					'relatives'	=> array ($t->mask)
				))
			)
			{
				$remote_operation=PHPGW_ACL_EDIT;
			}
			else
			{
				$remote_operation=PHPGW_ACL_ADD;

			}
			if (!$this->acl_check (array(
							 'string'	=> $t->fake_full_path,
							 'relatives'	=> array ($t->mask),
							 'operation'	=> $remote_operation
							 ))
				)
			{
$this->debug('cp to forbidden by ACL');
				return False;
			}

			umask(000);

			if ($this->file_type (array(
					'string'	=> $f->fake_full_path,
					'relatives'	=> array ($f->mask)
				)) != 'Directory'
			)
			{
			  
				if ($f->outside && $t->outside)
				{
					return copy($f->real_full_path, $t->real_full_path);
				}
				elseif ($f->outside || $t->outside)
				{
				  	$content = $this->read(array(
						'string'	=> $f->fake_full_path,
						'noview' => true,
						'relatives'	=> array ($f->mask)
						)
					);
					$result = $this->write(array(
						'string'	=> $t->fake_full_path,
						'relatives'	=> array ($t->mask),
						'content' => $content
						)
					);
				}
				else 
				{
					$status=$this->dav_client->copy($this->dav_path($f->real_full_path), $t->real_full_path,True, 'Infinity', isset($this->override_locks[$t->real_full_path]) ? $this->override_locks[$t->real_full_path] : '');
					
					$result = $status == 204 || $status==201;
					if (!$result)
					{
$this->debug('cp : Failed : '.$status);
						return False;
					}
			 	 }
$this->debug('cp : from');
if(DEBUG) _debug_array($f);
$this->debug('cp : to');
if(DEBUG) _debug_array($t);
				 //Copy should copy the app too it's a dead property .
				$this->set_attributes(array(
					'string'	=> $t->fake_full_path,
					'relatives'	=> array ($t->mask),
					'attributes' => array (
								'owner_id' => $this->working_id,
								'createdby_id' => $account_id
							)
						)
					);
$this->debug('cp : success '.$result);
				return $result;

			}
			else if (!($f->outside || $t->outside)) 
			{
				//if the files are both on server, its just a depth=infinity copy
				$status=$this->dav_client->copy($this->dav_path($f->real_full_path), $t->real_full_path,True, 'infinity', $this->override_locks[$p->real_full_path]);
				if($status != 204 && $status!=201) 
				{
					return False;
				}
				else 
				{
					return True;
				}
			}
			else	/* It's a directory, and one of the files is local */
			{
				/* First, make the initial directory */
				if ($this->mkdir (array(
						'string'	=> $data['to'],
						'relatives'	=> array ($data['relatives'][1])
					)) === False
				)
				{
					return False;
				}

				/* Next, we create all the directories below the initial directory */
				$ls = $this->ls (array(
						'string'	=> $f->fake_full_path,
						'relatives'	=> array ($f->mask),
						'checksubdirs'	=> True,
						'mime_type'	=> 'Directory',
						'nofiles'	=> False
					)
				);

				//while (list ($num, $entry) = each ($ls))
				foreach($ls as $num => $entry)
				{
					$newdir =  str_ireplace ($f->fake_full_path, "{$t->fake_full_path}", $entry['directory']);
					if ($this->mkdir (array(
							'string'	=> $newdir.'/'.$entry['name'],
							'relatives'	=> array ($t->mask)
						)) === False
					)
					{
						return False;
					}
				}

				/* Lastly, we copy the files over */
				$ls = $this->ls (array(
						'string'	=> $f->fake_full_path,
						'relatives'	=> array ($f->mask)
					)
				);

				//while (list ($num, $entry) = each ($ls))
				foreach($ls as $num => $entry)
				{
					if ($entry['mime_type'] == 'Directory')
					{
						continue;
					}

					$newdir =  str_ireplace ($f->fake_full_path, "{$t->fake_full_path}", $entry['directory']);
					$this->cp (array(
							'from'	=> "$entry[directory]/$entry[name]",
							'to'	=> "$newdir/$entry[name]",
							'relatives'	=> array ($f->mask, $t->mask)
						)
					);
				}
			}

			return True;
		}

		/*
		* See vfs_shared
		*/
		function mv ($data)
		{
			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT, RELATIVE_CURRENT)
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$account_id = $GLOBALS['phpgw_info']['user']['account_id'];

			$f = $this->path_parts (array(
					'string'	=> $data['from'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			$t = $this->path_parts (array(
					'string'	=> $data['to'],
					'relatives'	=> array ($data['relatives'][1])
				)
			);

			if (!$this->acl_check (array(
					'string'	=> $f->fake_full_path,
					'relatives'	=> array ($f->mask),
					'operation'	=> PHPGW_ACL_READ
				))
				|| !$this->acl_check (array(
					'string'	=> $f->fake_full_path,
					'relatives'	=> array ($f->mask),
					'operation'	=> PHPGW_ACL_DELETE
				))
			)
			{
				return False;
			}

			if (!$this->acl_check (array(
					'string'	=> $t->fake_full_path,
					'relatives'	=> array ($t->mask),
					'operation'	=> PHPGW_ACL_ADD
				))
			)
			{
				return False;
			}

			if ($this->file_exists (array(
					'string'	=> $t->fake_full_path,
					'relatives'	=> array ($t->mask)
				))
			)
			{
				if (!$this->acl_check (array(
						'string'	=> $t->fake_full_path,
						'relatives'	=> array ($t->mask),
						'operation'	=> PHPGW_ACL_EDIT
					))
				)
				{
					return False;
				}
			}
			umask (000);

			/* We can't move directories into themselves */
			if (($this->file_type (array(
					'string'	=> $f->fake_full_path,
					'relatives'	=> array ($f->mask)
				) == 'Directory'))
				&& preg_match ('/^' . addcslashes($f->fake_full_path, '/'). '/', $t->fake_full_path)
			)
			{
				if (($t->fake_full_path == $f->fake_full_path) || substr ($t->fake_full_path, strlen ($f->fake_full_path), 1) == '/')
				{
					return False;
				}
			}

			if ($this->file_exists (array(
					'string'	=> $f->fake_full_path,
					'relatives'	=> array ($f->mask)
				))
			)
			{
				/* We get the listing now, because it will change after we update the database */
				$ls = $this->ls (array(
						'string'	=> $f->fake_full_path,
						'relatives'	=> array ($f->mask)
					)
				);

				if ($this->file_exists (array(
						'string'	=> $t->fake_full_path,
						'relatives'	=> array ($t->mask)
					))
				)
				{
					$this->rm (array(
							'string'	=> $t->fake_full_path,
							'relatives'	=> array ($t->mask)
						)
					);
				}

				$this->correct_attributes (array(
						'string'	=> $t->fake_full_path,
						'relatives'	=> array ($t->mask)
					)
				);
				
				if ($f->outside && $t->outside)
				{
					echo 'local';
					$result = rename ($f->real_full_path, $t->real_full_path);
				}
				else if ($f->outside || $t->outside) //if either file is local, read then write
				{
					$content = $this->read(array(
						'string'	=> $f->fake_full_path,
						'noview' => true,
						'relatives'	=> array ($f->mask)
						)
					);
					$result = $this->write(array(
						'string'	=> $t->fake_full_path,
						'relatives'	=> array ($t->mask),
						'content' => $content
						)
					);
					if ($result)
					{
						$result = $this->rm(array(
							'string'	=> $f->fake_full_path,
							'relatives'	=> array ($f->mask),
							'content' => $content
							)
						);
					}
				}
				else {  //we can do a server-side copy if both files are on the server
				$status=$this->dav_client->move($this->dav_path($f->real_full_path), $t->real_full_path,True, 'infinity', isset($this->override_locks[$p->real_full_path]) ? $this->override_locks[$p->real_full_path] : '');
					$result = ($status==201 || $status==204);
				}
				
				if ($result) $this->set_attributes(array(
						'string'	=> $t->fake_full_path,
						'relatives'	=> array ($t->mask),
						'attributes'	=> array (
									'modifiedby_id' => $account_id,
									'modified' => $this->now
								)));
				return $result;
			}
			else
			{
				return False;
			}

			$this->add_journal (array(
					'string'	=> $t->fake_full_path,
					'relatives'	=> array ($t->mask),
					'operation'	=> VFS_OPERATION_MOVED,
					'state_one'	=> $f->fake_full_path,
					'state_two'	=> $t->fake_full_path
				)
			);

			return True;
		}

		/*
		* See vfs_shared
		*/
		function rm ($data)
		{
			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT)
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);
			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);
			$this->debug("rm: $p->real_full_path");
			if (!$this->acl_check (array(
					'string'	=> $p->fake_full_path,
					'relatives'	=> array ($p->mask),
					'operation'	=> PHPGW_ACL_DELETE
				))
			)
			{
				return False;
			} 

/*this would become apparent soon enough anyway?
			if (!$this->file_exists (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				))
			) return False;
*/
			if ($this->file_type (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)) != 'Directory'
			)
			{
				if ($p->outside)
				{
					return unlink($p->real_full_path);
				}
				else
				{
					$rr=$this->dav_client->delete($this->dav_path($p->real_full_path), 0, isset($this->override_locks[$p->real_full_path]) ? $this->override_locks[$p->real_full_path] : '');
					return $rr == 204;	
				}
			}
			else
			{
				$ls = $this->ls (array(
						'string'	=> $p->fake_full_path,
						'relatives'	=> array ($p->mask)
					)
				);

				//while (list ($num, $entry) = each ($ls))
				foreach($ls as $num => $entry)
				{
					$this->rm (array(
							'string'	=> "$entry[directory]/$entry[name]",
							'relatives'	=> array ($p->mask)
						)
					);
				}

				/* If the directory is linked, we delete the placeholder directory */
				$ls_array = $this->ls (array(
						'string'	=> $p->fake_full_path,
						'relatives'	=> array ($p->mask),
						'checksubdirs'	=> False,
						'mime_type'	=> False,
						'nofiles'	=> True
					)
				);
				$link_info = $ls_array[0];

				if ($link_info['link_directory'] && $link_info['link_name'])
				{
					$path = $this->path_parts (array(
							'string'	=> $link_info['directory'] . '/' . $link_info['name'],
							'relatives'	=> array ($p->mask),
							'nolinks'	=> True
						)
					);
					$this->dav_client->delete($this->dav_path($path->real_full_path),0, $this->override_locks[$p->real_full_path]);
				}

				/* Last, we delete the directory itself */
				$this->add_journal (array(
						'string'	=> $p->fake_full_path,
						'relatives'	=> array ($p->mask),
						'operaton'	=> VFS_OPERATION_DELETED
					)
				);

				$this->dav_client->delete($this->dav_path($p->real_full_path).'/','Infinity', $this->override_locks[$p->real_full_path]);

				return True;
			}
		}

		/*
		* See vfs_shared
		*/
		function mkdir ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT)
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$account_id = $GLOBALS['phpgw_info']['user']['account_id'];
			$currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];

			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			if (!$this->acl_check (array(
					'string'	=> $p->fake_leading_dirs,
					'relatives'	=> array ($p->mask),
					'operation'	=> PHPGW_ACL_ADD)
				)
			)
			{
				return False;
			}

			/* We don't allow /'s in dir names, of course */
			if (preg_match ('/\//', $p->fake_name))
			{
				return False;
			}
			$lock = isset($this->override_locks[$p->real_full_path]) ? $this->override_locks[$p->real_full_path] : '';
			if ($p->outside)
			{
				if (file_exists($p->real_full_path))
				{
					if (!is_dir($p->real_full_path))
					{
						return False;
					}
				}
				elseif (!mkdir($p->real_full_path, 0777))
				{
					return False;
				}
			}
			else if($this->dav_client->mkcol($this->dav_path($p->real_full_path), $lock) != 201) 
			{
				return False;
			}
			

			if ($this->file_exists (array(
					'string'	=> $p->fake_full_path,
					'relatives' => array($p->mask)
				))
			)
			{
				$this->set_attributes(array(
					'string'	=> $p->fake_full_path,
					'relatives'	=> array ($p->mask),
					'attributes'	=> array (
								'createdby_id' => $account_id,
//								'size' => 4096,
//								'mime_type' => 'Directory',
								'created' => $this->now,
//								'deleteable' => 'Y',
								'app' => $currentapp
							),
					True
					)
				);

				$this->correct_attributes (array(
						'string'	=> $p->fake_full_path,
						'relatives'	=> array ($p->mask)
					),
					True
				);

				$this->add_journal (array(
						'string'	=> $p->fake_full_path,
						'relatives'	=> array ($p->mask),
						'operation'	=> VFS_OPERATION_CREATED
					)
				);

				/*Now we need to set access control for this dir.  Simply create an .htaccess
				file limiting access to this user, if we are creating this dir in the user's home dir*/
				$homedir = $this->fakebase.'/'.$this->dav_user; 
				if ( substr($p->fake_leading_dirs, 0, strlen($homedir)) == $homedir)
				{ 
					$conf = CreateObject('phpgwapi.config', 'phpgwapi');
					$conf->read();
					if ($conf->config_data['acl_default'] != 'grant')
					{
						$htaccess = 'require user '.$GLOBALS['phpgw_info']['user']['account_lid'];
						if ( ! $this->write(array(
								'string' =>  $p->fake_full_path.'/.htaccess',
								'content' => $htaccess,
								'relatives' => array(RELATIVE_NONE)
							)))
						{
							echo '<p><b>Unable to write .htaccess file</b></p></b>';
						};	
					}
				}
				return True;
			}
			else
			{
				return False;
			}
		}

		/*
		* See vfs_shared
		*/
		function make_link ($data)
		{
			return False; //This code certainly wont work anymore.  Does anything use it?
		/*
			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT, RELATIVE_CURRENT)
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$account_id = $GLOBALS['phpgw_info']['user']['account_id'];
			$currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];

			$vp = $this->path_parts (array(
					'string'	=> $data['vdir'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			$rp = $this->path_parts (array(
					'string'	=> $data['rdir'],
					'relatives'	=> array ($data['relatives'][1])
				)
			);

			if (!$this->acl_check (array(
					'string'	=> $vp->fake_full_path,
					'relatives'	=> array ($vp->mask),
					'operation'	=> PHPGW_ACL_ADD
				))
			) return False;

			if ($this->file_exists (array(
					'string'	=> $rp->real_full_path,
					'relatives'	=> array ($rp->mask)
			))) 
			{
				if (!is_dir($rp->real_full_path))
				{
					return False;
				}
			}
			elseif (!mkdir ($rp->real_full_path, 0770))
			{
				return False;
			}

			if (!$this->mkdir (array(
					'string'	=> $vp->fake_full_path,
					'relatives'	=> array ($vp->mask)
				))
			)return False;

			$size = $this->get_size (array(
					'string'	=> $rp->real_full_path,
					'relatives'	=> array ($rp->mask)
				)
			);

			$this->set_attributes(array(
					'string'	=> $vp->fake_full_path,
					'relatives'	=> array ($vp->mask),
					'attributes'	=> array (
								'link_directory' => $rp->real_leading_dirs,
								'link_name' => $rp->real_name,
								'size' => $size
							)
				)
			);

			$this->correct_attributes (array(
					'string'	=> $vp->fake_full_path,
					'relatives'	=> array ($vp->mask)
				)
			);

			return True;
	*/
		}

		/*
		* See vfs_shared
		*/
		function set_attributes ($data,$operation=PHPGW_ACL_EDIT,$is_dir=False)
		{
			if(!isset($data['attributes']))
			{
				_debug_array(debug_backtrace());
				_debug_array('Set_attributes with no attributes ??? what else ?');
				return false;
			}
			/*To get much benefit out of DAV properties we should use 
			some sensible XML namespace.  We will use the Dublin Core 
			metadata specification (http://dublincore.org/) here where 
			we can*/
			$p = $this->path_parts (array(
				'string'	=> $data['string'],
				'relatives'	=> array ($data['relatives'][0])
				));
			$dav_properties = array();
			$lid=''; $fname = ''; $lname='';
			if(!isset($data['attributes']['owner_id']) && isset($data['attributes']['createdby_id']))
			{
				$data['attributes']['owner_id'] = $data['attributes']['createdby_id'];
			}
			if (isset($data['attributes']['comment']))
			{
				$dav_properties['dc:description'] = $data['attributes']['comment'];
			}
			if (isset($data['attributes']['owner_id']) && $id=$data['attributes']['owner_id'])
			{
				$dav_properties['dc:publisher'] = (string) $GLOBALS['phpgw']->accounts->get($id);
				$dav_properties['publisher_id'] = $id;
			}
			if (isset($data['attributes']['createdby_id']) && $id=$data['attributes']['createdby_id'])
			{
				$dav_properties['dc:creator'] = (string) $GLOBALS['phpgw']->accounts->get($id);
				$dav_properties['creator_id'] = $id;
			}
			if (isset($data['attributes']['modifiedby_id']) && $id=$data['attributes']['modifiedby_id'])
			{
				$dav_properties['dc:contributor'] = (string) $GLOBALS['phpgw']->accounts->get($id);
				$dav_properties['contributor_id'] = $id;
			}
			if (isset($data['attributes']['app']) && $id=$data['attributes']['app'])
			{
				$dav_properties['dc:source'] = $id;
			}

			$xmlns = 'xmlns:dc="http://purl.org/dc/elements/1.1/"';
			if ( !$p->outside )
			{
				$lock = isset($this->override_locks[$p->real_full_path]) ? $this->override_locks[$p->real_full_path] : '';
				$this->dav_client->proppatch($this->dav_path($p->real_full_path), $dav_properties, $xmlns, $lock);
			}
			else
			{
				//What are we doing in this case ???
			}
			return True;
		}

		/*
		* See vfs_shared
		*/
		function file_type ($data)
		{
			$this->debug('file_type');
			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT)
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			if (!$this->acl_check (array(
					'string'	=> $p->fake_full_path,
					'relatives'	=> array ($p->mask),
					'operation'	=> PHPGW_ACL_READ,
					'must_exist'	=> True
				))
			) return False;

			if ($p->outside)
			{
			  if(is_dir($p->real_full_path)) return ('Directory');
			  else return $this->get_ext_mime_type(array('string' => $p->real_full_path));

			}
			$tmp_prop=$this->get_properties($p->real_full_path,0);
$this->debug('tmpprop: '.$p->real_full_path);
$this->debug($tmp_prop);
 			$mime_type=isset($tmp_prop[$p->real_full_path]['mime_type']) ? $tmp_prop[$p->real_full_path]['mime_type'] : '';
 			if ($mime_type == 'httpd/unix-directory' || (isset($tmp_prop[$p->real_full_path]['is_dir']) && $tmp_prop[$p->real_full_path]['is_dir'] == '1'))
			{
				$mime_type='Directory';
			}
$this->debug('file_type: Mime type : '.$mime_type);
			return $mime_type;
		}
 
		/*
		* See vfs_shared
		*/
		function file_exists ($data)
		{
			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT)
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);
			$this->debug('vfs->file_exists() data:'.$data['string']);
			$this->debug('vfs->file_exists() full_path:  '.$p->real_full_path);
			if ($p->outside)
			{
			  return file_exists($p->real_full_path);
			}
			
			$path = $p->real_full_path;
			
			//Even though this does full XML parsing on the output, because
			// it then caches the result this limits the amount of traffic to
			//the dav server (which makes it faster even over a local connection)
			$props = $this->get_properties($path,0);
			if($props === False)
			{
				$this->debug('found but denied');
				return True;
			}
			if (isset($props[$path]))
			{
				$this->debug('found');
				return True;
			}
			else
			{
				$this->debug('not found');
				return False;
			}
		}

		/**
		 * get directory listing or info about a single file
		*
		 * Note: The entries are not guaranteed to be returned in any logical order
		 * 		 * Note: The size for directories does not include subfiles/subdirectories.
		 * 		 *   If you need that, use $this->get_size ()
		 * @param string File or Directory
		 * @param relatives Relativity array
		 * @param checksubdirs Boolean, recursively list all sub directories as well?
		 * @param mime_type Only return entries matching MIME-type 'mime_type'.  Can be any MIME-type, "Directory" or "\ " for those without MIME types
		 * @param nofiles Boolean.  True means you want to return just the information about the directory $dir.  If $dir is a file, $nofiles is implied.  This is the equivalent of 'ls -ld $dir'
		 * @param orderby How to order results.  Note that this only works for directories inside the virtual root
		 * @return array of arrays.  Subarrays contain full info for each file/dir.
		 */
		function ls ($data)
		{
			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT),
					'checksubdirs'	=> True,
					'mime_type'	=> False,
					'nofiles'	=> False,
					'orderby'	=> 'directory'
				);
			$data = array_merge ($this->default_values ($data, $default_values), $data);
/*			_debug_array($data);*/
			//Stupid "nofiles" fix" <= what this fix was supposed to do ??? Caeies
/*			if ($data['nofiles'])
			{
				$data['relatives'] = array (RELATIVE_NONE);
			}*/
			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);
		
			if ($data['checksubdirs']==False && preg_match('/.*\/$/', $data['string']) && $data['nofiles'] )
			{
$this->debug('Returning empty for'.$data['string'],DEBUG_LS);
				return array();
			}
			$dir = $p->fake_full_path;
$this->debug("ls'ing dir: $dir path: ".$p->real_full_path,DEBUG_LS);
			/* If they pass us a file or 'nofiles' is set, return the info for $dir only */
if (DEBUG_LS) _debug_array($data);
			if (((($type = $this->file_type (array(
					'string'	=> $dir,
					'relatives'	=> array ($p->mask)
				)) != 'Directory'))
				|| ($data['nofiles'])) && !$p->outside
			)
			{
$this->debug('ls branch 1 :'. $type,DEBUG_LS);
				$prop=$this->get_properties($p->real_full_path, 0, $type == 'Directory');
				if(!is_array($prop))
				{
					return array(); // Don't exist or access denied (check with file_exists)
				}
				$rarray = array ();

				$value = $prop[$p->real_full_path];
				$value['directory'] = $p->fake_leading_dirs;
				if(isset($value['is_dir'])) $value['mime_type']='Directory';
				$rarray[0] = $value;
if ( DEBUG_LS ) _debug_array($rarray);
$this->debug('ls returning 1:',DEBUG_LS);
				return $rarray;
			}

			//WIP - this should recurse using the same options the virtual part of ls () does
			/* If $dir is outside the virutal root, we have to check the file system manually */
			if ($p->outside)
			{
$this->debug('ls branch 2 (outside)',DEBUG_LS);
				if ($this->file_type (array(
						'string'	=> $p->fake_full_path,
						'relatives'	=> array ($p->mask)
					)) == 'Directory'
					&& !$data['nofiles']
				)
				{
					$dir_handle = opendir ($p->real_full_path);
					while ($filename = readdir ($dir_handle))
					{
						if ($filename == '.' || $filename == '..')
						{
							continue;
						}

						$rarray[] = $this->get_real_info (array(
								'string'	=> "{$p->real_full_path}/{$filename}",
								'relatives'	=> array ($p->mask)
							)
						);
					}
				}
				else
				{
					$rarray[] = $this->get_real_info (array(
							'string'	=> $p->real_full_path,
							'relatives'	=> array ($p->mask)
						)
					);
				}
$this->debug('ls returning 2:',DEBUG_LS);
				return $rarray;
			}
$this->debug('ls branch 3',DEBUG_LS);
			/* $dir's not a file, is inside the virtual root, and they want to check subdirs */
			if(isset($data['rev']))
			{
				$parsed_url = parse_url($p->real_full_path);
				$parsed_url['path'] = $p->fake_full_path;
				$parsed_url['pathhis'] =$this->svn_history_path;
				$parsed_url['repos'] = $this->svn_repository_path;    
				$parsed_url['rev'] = $data['rev'];
				$location = $this->glue_url($parsed_url);
				$prop=$this->get_properties($location,1,true);
				unset($prop[$location]);				
			}
			else
			{
				$prop=$this->get_properties($p->real_full_path,1,true);
			}	
			if ( !is_array($prop) )
			{
				return array();
			}
			unset($prop[$p->real_full_path]);
			//make the key the 'orderby' attribute

			if (! ($data['orderby'] == 'directory'))
			{
				$tmp_prop = array();
				$id=0;
				foreach ( $prop as $key=>$value)
				{
					$id++;
					if(isset($value[$data['orderby']]))
					{
						$new_key =  substr($value[$data['orderby']].'        ',0, 8);
					}
					else
					{
						$new_key = substr($value['name'].'        ',0, 8);
					}
					$tmp_prop[strtolower($new_key).'_'.$id] = $value;
				}
			}
			else 
			{
				$tmp_prop = $prop;
			}
			
			ksort($tmp_prop);

//			unset($tmp_prop[$p->real_full_path]);
			$rarray = array ();
			$datanew = $data;
			$datanew['relatives'][0]=RELATIVE_NONE;
			foreach($tmp_prop as $idx => $value)
			{
				if ( $data['checksubdirs'] && isset($value['is_dir']) && $value['is_dir'])
				{
					/* We have the choice between an infinite propfind 
					* or goind directory under directory : this is better 
					* since forbiden infinite recursion is a good idea 
					* to avoid DoS 
					*/
/*_debug_array($p->fake_full_path.$value['name'].'/');*/
//					$datanew['string'] = "{$p->fake_full_path}/{$value['name']};
					$datanew['string'] = preg_replace('#[/]+#','/', "{$p->fake_full_path}/{$value['name']}");
					$tmp = $this->ls($datanew);
					foreach($tmp as $f)
					{
						//If ls is working well, 
						//the returned ls match the values
						//except if the directory is protected
						if ( "{$f['directory']}/{$f['name']}" != "{$p->fake_full_path}/{$value['name']}" )
							$rarray[] = $f;
					}
				}
				if( $data['mime_type']=='' || $value['mime_type']==$data['mime_type'])
				{
					$value['directory'] = $p->fake_full_path;
					$rarray[] = $value;
				}
			}
if ( DEBUG_LS ) { _debug_array($rarray); }
$this->debug('ls:returning 3:' .$data['string'], DEBUG_LS);
			return $rarray;
		}

		/* Below are helpers */

		/**
		*  * @access : Private
		*  * @param string $uri : the $p->real_full_path of the file/dir
		*  * @param mixed $scope : 0 = the uri itself, 1 = the uri and one-level
		*  * under it, Infinity = the uri and all the sub-tree (not always ok 
		*  * depends on WebServer configuration (DavDepthInfinity on/off)
		*  * @param boolean $is_dir : If you know that uri is a directory put
		*  * it to True, so only one propfind is needed against some kind of
		*  * Dav implementation (if not you will get a 301 Moved Permanently, 
		*  * The function follow it)
		*  * @param string $prop_name : a property name (first level !);
		*  * @return mixed $props : the full array of needed properties or 
		*  * an array corresponding to the requested $prop_name if $scope == 0 
		*  * => the value itself, else an array indexed by path and containing 
		*  * only the prop_name
		*  * : This is a helper for the dav_properties. WARNING :
		*  * NO ACL CHECKS ARE DONE !!!
		 */
		function get_properties($uri, $scope = 0 , $is_dir = False, $prop_name = '#ALL#')
		{
			$prop = array();
			$ret = $this->dav_client->get_properties($prop, $this->dav_path($uri), $scope, $is_dir);
			if ( !$ret || !is_array($prop))
			{
				//Return False if we get a 401 or True if it's a 404
				return $ret;
			}
if(DEBUG)			_debug_array($prop);
			$proptmp = array();
			
			foreach($prop as $key => $arr)
			{
				$proptmp[$this->dav_root.$key] = $arr;
			}
			unset($prop);
			$prop = $proptmp;
			unset($proptmp);
			if ( $prop_name == '#ALL#' )
			{
				return $prop;
			}
			$ret = array();
			if ( !empty($prop_name) )
			{
				if ( !$scope )
				{
					return $prop[key($prop)][$prop_name];
				}
				else
				{
					foreach( $prop as $path => $arr )
					{
						$ret[$path] = $arr[$prop_name];
					}
				}
			}
			return $ret;
		}

		/* Since we are always dealing with real info, this just calls ls */
		function get_real_info ($data){
			if (!is_array ($data))
			{
				$data = array ();
			}

			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT)
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			if (is_dir ($p->real_full_path))
			{
				$mime_type = 'Directory';
			}
			else
			{
				$mime_type = $this->get_ext_mime_type (array(
						'string'	=> $p->fake_name
					)
				);

				if($mime_type)
				{
				}
			}

			$size = filesize ($p->real_full_path);
			$rarray = array(
				'directory' => $p->fake_leading_dirs,
				'name' => $p->fake_name,
				'size' => $size,
				'mime_type' => $mime_type
			);

			return ($rarray);
		}

	}

