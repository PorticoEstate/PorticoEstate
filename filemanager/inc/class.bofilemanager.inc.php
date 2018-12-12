<?php
	/***
	 * Filemanager
	 * @author Jason Wies (Zone)
	 * @author Mark A Peters <skeeter@phpgroupware.org>
	 * @author Jonathon Sim <sim@zeald.com>
	 * @author Bettina Gille <ceb@phpgroupware.org>
	 * @copyright Portions Copyright (C) 2000-2005 Free Software Foundation, Inc http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package filemanager
	 * @version $Id$
	 * @internal Based on phpWebhosting
	 */

	/**
	 * Filemanager business object class
	 * 
	 * @package filemanager
	 */
	class bofilemanager
	{
		var $access_add = false;
		var $basedir;
		var $fakebase;
		var $settings;
		var $filesdir;
		var $hostname;
		var $userinfo = array();
		var $homedir;
		var $homestr = False;
		var $file_attributes;
		var $help_info;

		var $errors;

		var $download;
		var $createdir;
		var $createfile;

		var $params = array();

		var $fileman = array();
		var $changes = array();
		var $upload_comment = array();
		var $upload_file = array();
		var $filehis = array();
		var $file;
		var $path;
		var $vers;
		var $mime_type;
		var $from_rev;
		var $to_rev;
		var $collapse;
		var $page=1;
		var $pages=0;
		var $svnpath;
		var $history_path;
		var $history_file;
		var $list_elements=array();

		var $dispsep;
		var $sortby = 'name';

		var $show_upload_boxes = 5;
		var $upload_boxes = array();
		var $memberships = array();
		var $now;
		var $matches;
		var $quota = 0;
		var $command_line;

		var $go = false;

		//var $debug = true;
		var $debug = false;

		var $public_functions = array
			(
			 'f_download'	=> true,
			 'load_files'	=> true
			);

		function __construct()
		{
			if ( !isset($GLOBALS['phpgw']->vfs) || !is_object($GLOBALS['phpgw']->vfs) )
			{
				$GLOBALS['phpgw']->vfs = CreateObject ('phpgwapi.vfs');
			}
			$to_decode = Array
			(
					/*
					   Decode
					   'var'	when	  'avar' == 'value'
					   or
					   'var'	when	  'var'  is set
					 */
					'path',
					'file',
					'todir',
					'sortby',
					'fileman',
					'upload_file',
					'upload_comment',
					'upload_name',
					'upload',
					'changes',
					'download',
					'createfile',
					'createdir',
					'params',
					'command_line',
					'vers',
					'mime_type',
					'from_rev',
					'to_rev',
					'collapse',
					'filehis',
					'page',
					'pages',
					'history_path',
					'history_file'
			);

			foreach ( $to_decode as $decode_me )
			{
				$this->initialize_vars($decode_me);
			}

			if(count($this->fileman))
			{
				$this->save_sessiondata();
			}
			else
			{
				$this->read_sessiondata('fileman');
			}
			
			if(count($this->filehis))
			{
				$this->save_sessiondata($this->filehis,'filehis');
			}
			else
			{
				$this->read_sessiondata('filehis');
			}

			if($this->page > 1 || (!isset($this->params['next']) && !isset($this->params['prev']) && !isset($this->params['compare']) && !isset($this->params['last'])))
			{
				$this->save_page($this->page,$this->pages);
			}
			else if(isset($this->params['next']) || isset($this->params['prev']) || isset($this->params['compare']) || isset($this->params['last']))
			{
				$this->read_page();
			}

			$this->basedir = $GLOBALS['phpgw']->vfs->basedir;
			$this->fakebase = $GLOBALS['phpgw']->vfs->fakebase;
			$this->settings = isset($GLOBALS['phpgw_info']['user']['preferences']['filemanager']) ? $GLOBALS['phpgw_info']['user']['preferences']['filemanager'] : array();
			if(stristr($this->basedir,PHPGW_SERVER_ROOT))
			{
				$this->filesdir = substr($this->basedir,strlen(PHPGW_SERVER_ROOT));
			}
			else
			{
				$this->filesdir = '';
			}

			$this->svnpath					= isset($GLOBALS['phpgw_info']['server']['svn_dir']) ? $GLOBALS['phpgw_info']['server']['svn_dir'] : '';
			$this->hostname					= $GLOBALS['phpgw_info']['server']['webserver_url'].$this->filesdir;
			$this->userinfo['account_id']	= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->userinfo['account_lid']	= $GLOBALS['phpgw_info']['user']['account_lid'];
			$this->userinfo['hdspace']		= 10000000000;
			$this->homedir					= "{$this->fakebase}/{$this->userinfo['account_lid']}";

			if(!defined('NULL'))
			{
				define('NULL','');
			}

			$this->file_attributes = array
				(
				 'name'			=> lang('Filename'),
				 'deletable'		=> lang('Deletable'),
				 'mime_type'		=> lang('MIME Type'),
				 'size'			=> lang('Size'),
				 'created'		=> lang('Created'),
				 'modified'		=> lang('Modified'),
				 'owner'			=> lang('Owner'),
				 'createdby_id'	=> lang('Created by'),
				 'modifiedby_id'	=> lang('Modified by'),
				 'app'			=> lang('Application'),
				 'comment'		=> lang('Comment'),
				 'version'		=> lang('Version')
				);
			asort($this->file_attributes);

			$this->upload_boxes = array(1,5,10,15,20,25,30);

			$this->show_upload_boxes = isset($this->settings['show_upload_boxes']) ? $this->settings['show_upload_boxes'] : 5 ;
			if($this->go)
			{
				$this->path = $this->todir;
			}

			if($this->debug)
			{
				echo 'DEBUG: bo.bofilemanager: PATH = '.$this->path.'<br>'."\n";
			}

			if(!$this->path)
			{
				$this->path = $GLOBALS['phpgw']->vfs->pwd();
				if (!$this->path || $GLOBALS['phpgw']->vfs->pwd(array('full' => False)) == '')
				{
					$this->path = $this->homedir;
				}
			}
			$GLOBALS['phpgw']->vfs->cd(array('relative' => False,'relatives' => array(RELATIVE_NONE)));
			$GLOBALS['phpgw']->vfs->cd(array('string' => $this->path,'relative' => False,'relatives' => array(RELATIVE_NONE)));

			$this->pwd = $GLOBALS['phpgw']->vfs->pwd();

			if (!$this->cwd = substr($this->path,strlen($this->homedir) + 1))
			{
				$this->cwd = '/';
			}
			else
			{
				$this->cwd = substr($this->pwd,strrpos($this->pwd,'/')+1);
			}

			/* This just prevents // in some cases */
			if($this->path == '/')
			{
				$this->dispsep = '';
			}
			else
			{
				$this->dispsep = '/';
			}

			if (!($this->lesspath = substr($this->path,0,strrpos($this->path, '/'))))
			{
				$this->lesspath = '/';
			}

			if (!($this->historylesspath = substr($this->history_path,0,strrpos($this->history_path, '/'))))
			{
				$this->historylesspath = '/';
			}
			if(substr($this->path,0,strlen($this->homedir)) == $this->homedir)
			{
				$this->homestr = True;
			}

			$this->now = date('Y-m-d');

			if($this->debug)
			{
				echo '<b>Filemanager debug:</b><br>'
					. 'path: '.$this->path.'<br>'
					. 'cwd: '.$this->cwd.'<br>'
					. 'lesspath: '.$this->lesspath.'<br>'
					. 'fakebase: '.$this->fakebase.'<br>'
					. 'homedir: '.$this->homedir.'<p>'
					. '<b>phpGW debug:</b><br>'
					. 'real cabsolutepath: '.$GLOBALS['phpgw']->vfs->getabsolutepath(array(
								'string' => False, 
								'fake' => False
								)).'<br>'
					. 'fake getabsolutepath: '.$GLOBALS['phpgw']->vfs->getabsolutepath().'<br>'
					. 'appsession: ' . trim($GLOBALS['phpgw']->session->appsession('vfs','')) . '<br>'
					. 'pwd: '.$GLOBALS['phpgw']->vfs->pwd().'<br>';
			}

			/*	
				Get their memberships to be used throughout the script
			 */

			$groups = $GLOBALS['phpgw']->accounts->membership($this->userinfo['account_id']);

			if(!is_array($groups))
			{
				$groups = array();
			}

			/*
			   Don't list directories for groups that don't have access
			 */
			foreach($groups as $idx => $group)
			{
				if ($GLOBALS['phpgw']->vfs->acl_check(array('owner_id' => $group->id,'operation' => PHPGW_ACL_READ)))
				{
					$applications = CreateObject('phpgwapi.applications', $group->id);
					$apps = $applications->read_account_specific();
					if(!$apps['filemanager'])
					{
						unset($groups[$idx]);
					}
					unset($applications);
				}
				else
				{
					unset($groups[$idx]);
				}
			}
			reset($groups);
			$this->memberships = $groups;
			//_debug_array($this->memberships);

			/*
			   We determine if they're in their home directory or a group's directory,
			   and set the VFS working_id appropriately
			 */
			if((preg_match('+^'.$this->fakebase.'\/(.*)(\/|$)+U',$this->path,$this->matches)) && $this->matches[1] != $this->userinfo['account_lid'])
			{
				$GLOBALS['phpgw']->vfs->working_id = $GLOBALS['phpgw']->accounts->name2id($this->matches[1]);
			}
			else
			{
				$GLOBALS['phpgw']->vfs->working_id = $this->userinfo['account_id'];
			}

			//XXX Caeies: well, doing this when the path doesn't exist, is ... problematic at least for DAV ...
			if ($GLOBALS['phpgw']->vfs->acl_check(array('string' => $this->path,'relatives' => array (RELATIVE_NONE),'operation' => PHPGW_ACL_ADD)))
			{
				$this->access_add = true;
			}

			$account = $GLOBALS['phpgw']->accounts->get($this->userinfo['account_id']);

			if ( $account->quota == -1 )
			{
				$this->quota = -1;
			}
			else
			{
				$this->quota = $account->quota * 1024 * 1024;
			}
		}

		function initialize_vars($name)
		{
			$var = '';
			if ( isset($_FILES[$name]))
			{
				$var = $_FILES[$name];
			}
			else if (isset($_REQUEST[$name]) )
			{
				$var = $_REQUEST[$name];
			}

			if($this->debug)
			{
				echo "<!-- {$name} = {$var} -->\n";
			}

			if(isset($this->$name) && is_array($this->$name) && $var)
			{
				//_debug_array($var);

				$temp = Array();
				foreach ( $var as $varkey => $varvalue )
				{
					if(is_int($varkey))
					{
						$temp[$varkey] = urldecode($varvalue);
					}
					else
					{
						$temp[urldecode($varkey)] = $varvalue;
					}
				}
			}
			elseif($var)
			{
				$temp = urldecode($var);
			}

			if(isset($temp))
			{
				$this->$name = $temp;
				//_debug_array($this->$name);
			}
		}

		function save_page($page=0,$pages=0)
		{
			$this->page=$page;
			$this->pages=$pages;
			$data = array
			(
				'page'	=> serialize($this->page),
				'pages'	=> serialize($this->pages),	
			);
			$GLOBALS['phpgw']->session->appsession('session_data','filemanager_page',$data);
		}

		function read_page()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','filemanager_page');
			$this->pages=unserialize($data['pages']);
			$this->page=unserialize($data['page']);
		}

		function save_sessiondata($values = 0,$type = 'changes')
		{
			if(is_array($values))
			{
				switch($type)
				{
					case 'fileman':
						$this->fileman = $values;
						break;
					case 'filehis':
						$this->filehis=$values;
						break;
					default:
						$this->changes = $values;
				}
			}
			$data = array
				(
					'fileman'	=> serialize($this->fileman),
					'filehis'	=> serialize($this->filehis),
					'changes'	=> serialize($this->changes)
				);

			$GLOBALS['phpgw']->session->appsession('session_data','filemanager',$data);
		}

		function read_sessiondata($type='fileman')
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','filemanager');
			switch($type)
			{
				case 'fileman':
					$this->fileman = !empty($data['fileman']) ? unserialize($data['fileman']) : '';
					$this->changes = !empty($data['changes']) ? unserialize($data['changes']) : '';
					break;
				default:
					$this->filehis = !empty($data['filehis']) ? unserialize($data['filehis']) : '';
			}
		}

		function get_fileman()
		{
			$edit=array();
			$cnt_fileman = count($this->fileman);
			for ($i=0; $i < $cnt_fileman; ++$i)
			{
				$edit[$this->fileman[$i]] = $this->fileman[$i];
			}
			return $edit;
		}

		function unset_sessiondata()
		{
			$GLOBALS['phpgw']->session->appsession('session_data','filemanager','');
		}

		function create_home_dir()
		{
			/*
			   If their home directory doesn't exist, we create it
			   Same for group directories
			 */
			$error = '';
			if($this->debug)
			{
				echo 'DEBUG: bo.create_home_dir: PATH = '.$this->path.'<br>'."\n";
				echo 'DEBUG: bo.create_home_dir: PATH = '.urlencode($this->path).'<br>'."\n";
				echo 'DEBUG: bo.create_home_dir: HOMEDIR = '.urlencode($this->homedir).'<br>'."\n";
			}

			if(($this->path == $this->homedir) && !$GLOBALS['phpgw']->vfs->file_exists(array('string' => $this->homedir,'relatives' => Array(RELATIVE_NONE))))
			{
				$GLOBALS['phpgw']->vfs->override_acl = 1;
				if (!$GLOBALS['phpgw']->vfs->mkdir(array('string' => $this->homedir,'relatives' => array(RELATIVE_NONE))))
				{
					$error = lang('failed to create directory %1',$this->homedir);
				}
				$GLOBALS['phpgw']->vfs->override_acl = 0;
			}
			elseif(preg_match("|^{$this->fakebase}\/(.*)$|U", $this->path, $this->matches))
			{
				if (!$GLOBALS['phpgw']->vfs->file_exists(array('string' => $this->path,'relatives' => array(RELATIVE_NONE))))
				{
					$group_id = (int) $GLOBALS['phpgw']->accounts->name2id($this->matches[1]);
					$GLOBALS['phpgw']->vfs->working_id = $group_id;
					$GLOBALS['phpgw']->vfs->override_acl = 1;

					if (!$GLOBALS['phpgw']->vfs->mkdir(array('string' => $this->path,'relatives' => array(RELATIVE_NONE))))
					{
						$error = lang('failed to create directory %1',$this->homedir);
					}
					$GLOBALS['phpgw']->vfs->override_acl = 0;

					if($this->debug)
					{
						echo 'DEBUG: ui.create_home_dir: PATH = '.$this->bofilemanager->path.'<br>'."\n";
						echo 'DEBUG: ui.create_home_dir(): matches[1] = '.$this->bofilemanager->matches[1].'<br>'."\n";
					}

					if($group_id)
					{
						$GLOBALS['phpgw']->vfs->set_attributes(array(
									'string' => $this->path,
									'relatives' => array(RELATIVE_NONE),
									'attributes' => array('owner_id' => $group_id, 'createdby_id' => $group_id)));
					}
					$GLOBALS['phpgw']->vfs->working_id = $this->userinfo['account_id'];
					$GLOBALS['phpgw']->vfs->override_acl = 0;
				}
			}
			if(strlen($error))
			{
				return $error;
			}
		}

		function load_files($history=False)
		{
			/*
			   Read in file info from database to use in the rest of the script
			   $fakebase is a special directory.  In that directory, we list the user's
			   home directory and the directories for the groups they're in
			 */
			if($history && $this->history_file)
			{
					$this->historylesspath=$this->history_path;
					$this->history_path=$this->history_path.$this->dispsep.$this->history_file;
			}

			if (($this->path == $this->fakebase && !$history) || $this->history_path == $this->fakebase)
			{
				//echo 'path: ' . $this->path . "\n";
				//echo 'fake: ' . $this->fakebase;

				if (!$GLOBALS['phpgw']->vfs->file_exists(array('string' => $this->homedir,'relatives' => array(RELATIVE_NONE))))
				{
					$GLOBALS['phpgw']->vfs->mkdir(array('string' => $this->homedir,'relatives' => array(RELATIVE_NONE)));
				}
				$data	=	array(
									'string' => $this->homedir,
									'relatives' =>Array(RELATIVE_NONE),
									'checksubdirs' => False,
									'nofiles' => True
								);
				if($history)
				{
					$data['rev'] = $this->vers;
				}
				$ls_array = $GLOBALS['phpgw']->vfs->ls($data);

				$this->files_array[] = $ls_array[0];
				foreach ( $this->memberships as $group_array )
				{
					if ( !$GLOBALS['phpgw']->vfs->file_exists(array('string' => "{$this->fakebase}/{$group_array['account_name']}", 'relatives' => array(RELATIVE_NONE) ) ) )
					{
						//We want to create it, so overide acl :
						$GLOBALS['phpgw']->vfs->override_acl=true;
						if($GLOBALS['phpgw']->vfs->mkdir(array
								(
								 'string'	=> "{$this->fakebase}/{$group_array['account_name']}",
								 'relatives'	=> array(RELATIVE_NONE)
								)))
						{

							$GLOBALS['phpgw']->vfs->set_attributes(array
								(
								 'string'	=> "{$this->fakebase}/{$group_array['account_name']}",
								 'relatives' => array(RELATIVE_NONE),
								 'attributes'=> array('owner_id' => $group_array['account_id'], 'createdby_id' => $group_array['account_id'])
								));
						}
						$GLOBALS['phpgw']->vfs->override_acl=false;
					}
					$data= array
								(
									'string'	=> "{$this->fakebase}/{$group_array['account_name']}",
									'relatives'	=> array(RELATIVE_NONE),
									'checksubdirs'	=> false,
									'nofiles'	=> true
								);
					if($history)
					{
						$data['rev']=$this->vers;
					}
					$ls_array = $GLOBALS['phpgw']->vfs->ls($data);
					$this->files_array[] = $ls_array[0];
				}
			}
			else
			{
				//echo 'path: ' . $this->path . "\n";
				//echo 'fake: ' . $this->fakebase;
				$data=array
							(
								'string'	=> $this->path,
								'rev'		=> $this->vers,
								'relatives'	=> array(RELATIVE_NONE),
								'checksubdirs'	=> false,
								'orderby'	=>$this->sortby
							);
				if($history)
				{
					$data['rev'] = $this->vers;
					$data['string'] = $this->history_path;
				}
				$ls_array = $GLOBALS['phpgw']->vfs->ls($data);

				//echo '<pre>' . print_r($ls_array, true) . '</pre>';
				if ($this->debug)
				{
					echo "# of files found in '{$this->path}' : " . count($ls_array) . "<br>\n";
				}

				foreach ( $ls_array as $file_array )
				{
					$this->files_array[] = $file_array;
					if ($this->debug)
					{
						echo 'Filename: '.$file_array['name'].'<br>'."\n";
					}
				}
			}
			if(!isset($this->files_array) || !is_array($this->files_array))
			{
				$this->files_array = array();
			}
			return $this->files_array;
		}

		function convert_date($data)
		{
			if($data && $data != '0000-00-00')
			{
				$year = substr($data,0,4);
				$month = substr($data,5,2);
				$day = substr($data,8,2);
				$datetime = mktime(0,0,0,$month,$day,$year);
				$data = date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],$datetime);
			}
			else
			{
				$data = '';
			}
			return $data;
		}

		function f_go()
		{
			$this->path = $this->todir;
			return True;
		}

		function f_apply_edit_comment()
		{
			for ($i=0;$i<count($this->fileman);++$i)
			{
				$file = $this->fileman[$i];
				if (!$GLOBALS['phpgw']->vfs->set_attributes(array('string' => $file,'relatives' => array (RELATIVE_ALL),
								'attributes' => array('comment' => stripslashes ($this->changes[$file])))))
				{
					$result[] = lang('failed to change comment for %1', $file);
				}
				else
				{
					$result[] = lang('changed comment for %1', $file);
				}
			}
			return is_array($result)?$result:True;
		}

		function f_apply_edit_name()
		{
			//_debug_array($this->changes);
			//while (list ($from, $to) = each ($this->changes))
			foreach($this->changes as $from => $to)
			{
				if ($badchar = $this->bad_chars($to,True,True))
				{
					$result[] = lang('file names cannot contain %1', $badchar);
				}

				if (preg_match ("/\//", $to) || ereg ("/\\\\/", $to))
				{
					//echo $GLOBALS['phpgw']->common->error_list (array ("File names cannot contain \\ or /"));
					$result[] = lang('file names cannot contain \\ or /');
				}
				elseif (!$GLOBALS['phpgw']->vfs->mv(array('from' => $from,'to' => $to)))
				{
					//echo $GLOBALS['phpgw']->common->error_list (array ('Could not rename '.$disppath.'/'.$from.' to '.$disppath.'/'.$to));
					$result[] = lang('could not rename %1 to %2', $this->path.'/'.$from, $this->path.'/'.$to);
				}
				else
				{
					$result[] = lang('renamed %1 to %2', $this->path.'/'.$this->path.'/'.$from, $to);
				}
			}
			return is_array($result)?$result:True;
		}

		function f_delete()
		{
			for($i=0;$i<count($this->fileman);++$i)
			{
				if($this->fileman[$i])
				{
					$ls_array = $GLOBALS['phpgw']->vfs->ls(array(
								'string' => "{$this->path}/{$this->fileman[$i]}",
								'relatives' => array(RELATIVE_NONE),
								'checksubdirs' =>False,
								'nofiles' => True));

					$fileinfo = $ls_array[0];

					if($fileinfo)
					{
						if($fileinfo['mime_type'] == 'Directory')
						{
							$mime_type = $fileinfo['mime_type'];
						}
						else
						{
							$mime_type = 'File';
						}
						if($GLOBALS['phpgw']->vfs->delete(array(
										'string' => "{$this->path}/{$this->fileman[$i]}",
										'relatives' => Array(RELATIVE_USER_NONE))))
						{
							$result[] = lang('deleted %1', "{$this->path}/{$this->fileman[$i]}");
						}
						else
						{
							$result[] = lang('could not delete %1', "{$this->path}/{$this->fileman[$i]}");
						}
					}
					else
					{
						$result[] = lang('%1 does not exist', "{$this->path}/{$this->fileman[$i]}");
					}
				}
			}
			return is_array($result)?$result:True;
		}

		function f_copy()
		{
			for($i=0;$i<count($this->fileman);++$i)
			{
				if($this->fileman[$i])
				{
					if(!$this->check_quota($this->fileman[$i]))
					{
						$result[] = lang('Could not copy %1 to %2 quota exceeded', "{$this->todir}/{$this->fileman[$i]}", "{$this->path}/{$this->fileman[$i]}");
					}
					elseif($GLOBALS['phpgw']->vfs->cp(array(
									'from' => "{$this->path}/{$this->fileman[$i]}",
									'to' => "{$this->todir}/{$this->fileman[$i]}",
									'relatives' => array(RELATIVE_NONE,RELATIVE_NONE))))
					{
						$result[] = lang('file %1 copied to %2'. "{$this->todir}/{$this->fileman[$i]}", "{$this->path}/{$this->fileman[$i]}");
					}
					else
					{					
						$result[] = lang('could not copy %1 to %2', "{$this->todir}/{$this->fileman[$i]}", "{$this->path}/{$this->fileman[$i]}");
					}
				}
			}
			return is_array($result)?$result:True;
		}

		function f_move()
		{
			for($i=0;$i<count($this->fileman);++$i)
			{
				if($this->fileman[$i])
				{
					if($GLOBALS['phpgw']->vfs->mv(array(
									'from' => "{$this->path}/{$this->fileman[$i]}",
									'to' => "{$this->todir}/{$this->fileman[$i]}",
									'relatives' => array(RELATIVE_NONE,RELATIVE_NONE))))
					{
						$result[] = lang('file %1 moved to %2', "{$this->path}/{$this->fileman[$i]}", "{$this->todir}/{$this->fileman[$i]}");
					}
					else
					{					
						$result[] = lang('could not move %1 to %2', "{$this->path}/{$this->fileman[$i]}", "{$this->todir}/{$this->fileman[$i]}");
					}
				}
			}
			return is_array($result) ? $result : true;
		}

		function f_download()
		{
			/* JSON hack around */
			if ( (!isset($this->fileman) || !is_array($this->fileman) || !count($this->fileman) ) 
					&& (isset($this->file) && strlen($this->file) ) )
			{
				$this->fileman = array($this->file);
			}

			foreach ( $this->fileman as $file )
			{
				if ( $GLOBALS['phpgw']->vfs->file_exists(array('string' => "{$this->path}/$file",'relatives' => Array(RELATIVE_NONE) ) ) )
				{
					execmethod('filemanager.uifilemanager.view_file',
							Array(
								'path' => $this->path,
								'file' => $file
								)
							);
					$result[] = lang('file downloaded: %1', "{$this->path}/$file");
				}
				else
				{
					$result[] = lang('file does not exist: %1', "{$this->path}/$file");
				}
			}
			return is_array($result)?$result:True;
		}

		function f_newdir()
		{
			if ($this->params['newdir'] && $this->createdir)
			{
				if ($badchar = $this->bad_chars($this->createdir,True,True))
				{
					$result[] = lang('directory names cannot contain "%1"', $badchar);
					return $result;
				}

				if (substr($this->createdir,strlen($this->createdir)-1,1) == ' ' || substr($this->createdir,0,1) == ' ')
				{
					$result[] = lang('cannot create directory because it begins or ends in a space');
					return $result;
				}
				
				$dir_to_check = array(
						'string' => "{$this->path}/{$this->createdir}/",
						'relatives' => array(RELATIVE_NONE),
						'checksubdirs' => False,
						'nofiles' => True);
				$ls_array = $GLOBALS['phpgw']->vfs->ls($dir_to_check);

				$fileinfo = (is_array($ls_array) && isset($ls_array[0])) ? $ls_array[0] : array();
				if (is_array($fileinfo) && isset($fileinfo['name']))
				{
					if ($fileinfo['mime_type'] != 'Directory')
					{
						$result[] = lang('%1 already exists as a file', $fileinfo['name']);
					}
					else
					{
						$result[] = lang('directory %1 already exists', $fileinfo['name']);
					}
				}
				else
				{
					if ($GLOBALS['phpgw']->vfs->mkdir(array(
									'string' => "{$this->path}/{$this->createdir}",
									'relatives' => Array(RELATIVE_NONE))))
					{
						$this->path = "{$this->path}/{$this->createdir}";
						$result[] = lang('created directory %1', $this->path);
					}
					else
					{
						$result[] = lang('could not create %1', "{$this->path}/{$this->createdir}");
					}
				}
			}
			return is_array($result)?$result:True;
		}

		function f_newfile()
		{
			//echo "newfile: ".$this->params['newfile'] ." createfile: ".$this->createfile;
			//die();
			$result = array();
			if ($this->params['newfile'] && $this->createfile)
			{
				if($badchar = $this->bad_chars($this->createfile,True,True))
				{
					$result[] = lang('file names cannot contain %1, file %2', $badchar, "{$this->path}/{$this->createfile}");
					return $result;
				}
				if($GLOBALS['phpgw']->vfs->file_exists(array(
								'string' => $this->createfile,
								'relatives' => array(RELATIVE_ALL))))
				{
					$result[] = lang('file %1 already exists. Please edit it or delete it first', "{$this->path}/{$this->createfile}");
					return $result;
				}
				if(!$GLOBALS['phpgw']->vfs->touch(array(
								'string' => $this->createfile,
								'relatives' => Array(RELATIVE_ALL))))
				{
					$result[] = lang('file %1 could not be created', "{$this->path}/{$this->createfile}");
				}
			}
			else
			{
				$result[] = lang('filename not provided');
			}
			return count($result)?$result:True;
		}

		function f_upload()
		{
			for ($i = 0;$i<$this->show_upload_boxes;++$i)
			{
				if(!$this->check_quota($this->upload_file['size'][$i]))
				{
					$result[] = lang('could not upload %1 quota exceeded',$this->upload_file['name'][$i]);
				}
				elseif($badchar = $this->bad_chars($this->upload_file['name'][$i],True,True))
				{
					$result[] = lang('file names cannot contain %1, file %2',$badchar,$this->upload_file['name'][$i]);
					//echo $GLOBALS['phpgw']->common->error_list (array (html_encode ('File names cannot contain "'.$badchar.'"', 1)));
				}
				else
				{
					/*
					   Check to see if the file exists in the database, and get its info at the same time
					 */

					$ls_array = $GLOBALS['phpgw']->vfs->ls(array(
								'string'	=> $this->upload_file['name'][$i],
								'relatives'	=> array (RELATIVE_ALL),
								'checksubdirs'	=> False,
								'nofiles'	=> True));

					$fileinfo = $ls_array[0];

					if ($fileinfo['name'])
					{
						if ($fileinfo['mime_type'] == 'Directory')
						{
							$result[] = lang('cannot replace %1 because it is a directory', $fileinfo['name']);
						}
					}

					if ($this->upload_file['size'][$i] > 0)
					{
						if ($fileinfo['name'] && $fileinfo['deleteable'] != 'N')
						{
							if($GLOBALS['phpgw']->vfs->cp(array(
											'from'	=> $this->upload_file['tmp_name'][$i],
											'to'	=> $this->upload_file['name'][$i],
											'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))))
							{
								$GLOBALS['phpgw']->vfs->set_attributes(array(
											'string'	=> $this->upload_file['name'][$i],
											'relatives'	=> array(RELATIVE_ALL),
											'attributes'	=> array(
												'owner_id' => $GLOBALS['userinfo']['account_id'],
												'modifiedby_id' => $GLOBALS['userinfo']['account_id'],
												'modified' => $now,
												'size' => $this->upload_file['size'][$i],
												'mime_type' => $this->upload_file['type'][$i],
												'deleteable' => 'Y',
												'comment' => stripslashes ($upload_comment[$i]))));
								$result[] = lang('replaced %1 (%2 bytes)',$this->path.'/'.$this->upload_file['name'][$i],$this->upload_file['size'][$i]);
							}
							else
							{
								$result[] = lang( 'failed to upload file: %1',$this->upload_file['name'][$i]);
							}
						}
						else
						{
							if($GLOBALS['phpgw']->vfs->cp(array(
											'from'	=> $this->upload_file['tmp_name'][$i],
											'to'	=> $this->upload_file['name'][$i],
											'relatives'	=> array(RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))))
							{
								$GLOBALS['phpgw']->vfs->set_attributes(array(
											'string'	=> $this->upload_file['name'][$i],
											'relatives'	=> array(RELATIVE_ALL),
											'attributes'	=> array (
												'mime_type' => $this->upload_file['type'][$i],
												'comment' => stripslashes ($this->upload_comment[$i]))));
								$result[] = lang('created %1 (%2 bytes)',$this->path.'/'.$this->upload_file['name'][$i] , $this->upload_file['size'][$i]);
							}
							else
							{
								$result[] = lang('failed to upload file: %1',$this->upload_file['name'][$i]);
							}
						}
					}
					elseif ($this->upload_file['name'][$i])
					{
						$GLOBALS['phpgw']->vfs->touch(array(
									'string'	=> $this->upload_file['name'][$i],
									'relatives'	=> array(RELATIVE_ALL)));

						$GLOBALS['phpgw']->vfs->set_attributes(array(
									'string'	=> $this->upload_file['name'][$i],
									'relatives'	=> array(RELATIVE_ALL),
									'attributes'	=> array(
										'mime_type' => $this->upload_file['type'][$i],
										'comment' => $this->upload_comment[$i])));

						$result[] = ' Created '.$this->path.'/'.$this->upload_file['name'][$i].' '. $this->file_size[$i];
					}
				}
			}
			return is_array($result)?$result:True;
		}

		/**TODO : xslt-ise this */
		function build_help($help_option,$text = '')
		{
			if(isset($this->settings['show_help']))
			{
				$link = $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'filemanager.uiaction_base.help','help_name'	=> urlencode($help_option)));

				if(strlen($text)>0)
				{
					$help = '<a href="' . $link . '">' . $text . '</a>';
				}
				else
				{
					$help = "open_popup('" . $link . "','250','250')";
				}
				return $help;
			}
			else
			{
				return '';
			}
		}

		function load_help_info()
		{
			$this->help_info = array
				(
				 'up' => 'The Up button takes you to the directory above the current directory. For example, if you are in */home/jdoe/mydir*, the Up button would take you to */home/jdoe*.',
				 'current_dir' => 'The name of the directory you are currently in.',
				 'home' => 'The Home button takes you to your personal home directory.',
				 'sort_by' => 'Click on any of the column headers to sort the list by that column.',
				 'name' => 'The name of the file or directory.',
				 'mime_type' => "The MIME-type of the file. Examples include text/plain, text/html, image/jpeg. The special MIME-type Directory is used for directories.",
				 'size' => "The size of the file or directory in the most convenient units: bytes (B), kilobytes (KB), megabytes (MB), gigabytes (GB).  Sizes for directories include subfiles and subdirectories.",
				 'created' => 'When the file or directory was created.',
				 'modified' => 'When the file or directory was last modified.',
				 'owner' => 'The owner of the file or directory. This can be a user or group name.',
				 'createdby_id' => 'Displays who created the file or directory.',
				 'modifiedby_id' => 'Displays who last modified the file or directory.',
				 'application' => "The application associated with the file or directory.  Usually the application used to create it.  A blank application field is ok.",
				 'comment' => "The comment for the file or directory.  Comments can be set when creating the file or directory, and created or edited any time thereafter.",
				 'version' => "The current version for the file or directory.  Clicking on the version number will display a list of changes made to the file or directory.",
				 'edit' => "Edit the text of the selected file(s). You can select more than one file; this is useful when you want to copy part of one file into another. Clicking Preview will show you a preview of the file. Click Save to save your changes.",
				 'rename' => "Rename the selected file(s). You can select as many files or directories as you want.  You are presented with a text field to enter the new name of each file or directory.",
				 'delete' => "Delete the selected file(s). You can select as many files or directories as you want.  When deleting directories, the entire directory and all of its contents are deleted.  You will not be prompted to make sure you want to delete the file(s); make sure you really want to delete them before clicking Delete.",
				 'edit_comments' => "Create a comment for a file or directory, or edit an existing comment.  You can select as many files or directories as you want.",
				 'go_to' => 'The Go to button takes you to the directory selected in the drop down [dir_list|Directory List].',
				 'copy_to' => 'This will copy all selected files and directories to the directory selected in the drop down [dir_list|Directory List].',
				 'move_to' => 'This will move all selected files and directories to the directory selected in the drop down [dir_list|Directory List].',
				 'dir_list' => 'The Directory List contains a list of all directories you have (at least) read access to. Selecting [go_to|Go to]/[copy_to|Copy to]/[move_to|Move to] from the left Menu will perform the selected action on that directory. For example, if you select */home/somegroup/reports* from the Directory List, and choose [copy_to|Copy to] from the left Menu, all selected files and directories will be copied to */home/somegroup/reports*.',
				 'download' => 'Download the first selected file to your local computer. You can only download one file at a time. Directories cannot be downloaded, only files.',
				 'create_folder' => "Creates a directory (folder == directory).  The name of the directory is specified in the text box next to the Create Folder button.",
				 'create_file' => "Creates a file in the current directory.  The name of the file is specified in the text box next to the Create File button.  After clicking the Create File button you will be presented with the [edit|Edit] screen, where you may edit the file you just created.  If you do not with to make any changes to the file at this time, simply click the Save button and the file will be saved as an empty file.",
				 'command_line' => 'Enter a Unix-style command line here, which will be executed when the [execute|Execute] button is pressed. If you do not know what this is, you probably should turn the option off in the Preferences.',
				 'execute' => 'Clicking the Execute button will execute the Unix-style [command_line|command line] specified in the text box above. If you do not know what this is, you probably should turn the option off in the Preferences.',
				 'update' => "Sync the database with the filesystem for the current directory. This is useful if you use another interface to access the same files.  Any new files or directories in the current directory will be read in, and the attributes for the other files will be updated to reflect any changes to the filesystem.  Update is run automatically every few page loads (currently every 20 page loads as of this writing, but that may have changed by now).",
				 'file_stats' => 'Various statistics on the number and size of the files in the current directory. In some situations, these reflect different statistics. For example, when in / or the base directory.',
				 'upload_file' => 'The full path of the local file to upload. You can type it in or use the Browse.. button to select it. The file will be uploaded to the current directory. You cannot upload directories, only files.',
				 'upload_comment' => 'The inital comment to use for the newly uploaded file. Totally optional and completely arbitrary. You can [edit_comments|create or edit the comment] at any time in the future.',
				 'upload_files' => 'This will upload the files listed in the input boxes and store them in the current directory.',
				 'show_upload_fields' => 'This setting determines how many fields for uploading files will be shown at once. You can change the default number that will be shown by clicking one of the numbers or in the preferences section.',
				 'refresh'	=> 'Clicking the button refreshes the current window.',
				 'menu'	=> ''
					 );
		}

		function borkb ($size, $enclosed = false, $return = false)
		{
			if ( !(int) $size )
			{
				$size = 0;
			}

			if ($size < 1024)
			{
				$rstring = $size . 'B';
			}
			elseif ($size < 1024*1024)
			{
				$rstring = round($size/1024, 1) . 'KB';
			}
			else if ($size < (1024*1024*1024))
			{
				$rstring = round($size/(1024*1024), 1) . 'MB';
			}
			else if ($size < (1024*1024*1024*1024))
			{
				$rstring = round($size/(1024*1024*1024),3) . 'GB';
			}
			else
			{
				$rstring = round($size/(1024*1024*1024*1024),3) . 'TB';
			}

			if ( $enclosed )
			{
				$rstring = "({$rstring})";
			}

			return $rstring;
		}

		/**
		 * Check for and return the first unwanted character
		 */
		function bad_chars($string,$all = True,$return = 0)
		{
			$rstring = '';
			if($all)
			{
				if (preg_match("-([\\/<>\'\"\&])-", $string, $badchars))
				{
					$rstring = $badchars[1];
				}
			}
			else
			{
				if (preg_match("-([\\/<>])-", $string, $badchars))
				{
					$rstring = $badchars[1];
				}
			}
			return $rstring;
		}

		/**
		 * Match character in string using ord ().
		 */
		function ord_match($string, $charnum)
		{
			for ($i=0;$i<strlen($string);$i++)
			{
				$character = ord(substr($string,$i,1));

				if ($character == $charnum)
				{
					return True;
				}
			}
			return False;
		}

		/**
		 * Decide whether to echo or return.  Used by HTML functions
		 */
		function eor($rstring, $return)
		{
			if($return)
			{
				return $rstring;
			}
			else
			{
				html_text($rstring."\n");
				return '';
			}
		}

		/**
		 * URL encode a string
		 * First check if its a query string, then if its just a URL, then just encodes it all
		 * Note: this is a hack.  It was made to work with form actions, form values, and links only,
		 * but should be able to handle any normal query string or URL
		 */

		function string_encode($string,$return = False)
		{
			if (preg_match("/=(.*)(&|$)/U",$string))
			{
				$rstring = preg_replace("/=(.*)(&|$)/Ue","'='.rawurlencode(base64_encode ('\\1')).'\\2'",$string);
			}
			elseif (preg_match("/^{$this->hostname}/",$string))
			{
				$rstring = str_ireplace("{$this->hostname}/",'',$string);
				$rstring = preg_replace("/(.*)(\/|$)/Ue","rawurlencode (base64_encode ('\\1')).'\\2'",$rstring);
				$rstring = $this->hostname.'/'.$rstring;
			}
			else
			{
				$rstring = rawurlencode($string);

				/* Terrible hack, decodes all /'s back to normal */  
				$rstring = preg_replace("/%2F/",'/',$rstring);
			}

			return($this->eor($rstring,$return));
		}

		function string_decode($string,$return = False)
		{
			$rstring = rawurldecode($string);

			return($this->eor($rstring,$return));
		}

		/**
		 * HTML encode a string
		 * This should be used with anything in an HTML tag that might contain < or >
		 */
		function html_encode($string,$return)
		{
			return($this->eor(htmlspecialchars($string),$return));
		}

		/**
		 * Returns the amount of file(s) in a certain Path
		 * either recursive or non recursive
		 */
		function count_files($path,$subdirs)
		{
			$ls_array = $GLOBALS['phpgw']->vfs->ls(array(
						'string'	=> $path,
						'relatives'	=> array (RELATIVE_NONE),
						'checksubdirs'	=> $subdirs));

			$ls_dirs = $GLOBALS['phpgw']->vfs->ls(array(
						'string'	=> $path,
						'relatives'	=> array (RELATIVE_NONE),
						'checksubdirs'	=> $subdirs,
						'mime_type' => 'Directory'));
			return (count($ls_array)-count($ls_dirs));
		}

		/**
		 * Return the size of either a File or a Directory
		 */
		function get_size($files,$homedir)
		{
			if($homedir)
			{
				$size = $GLOBALS['phpgw']->vfs->get_size(array(
							'string'	=> $this->homedir,
							'checksubdirs'	=> True,
							'relatives'	=> array (RELATIVE_NONE)));
			}
			elseif(isset($files['mime_type']) && $files['mime_type']=='Directory')
			{
				$size = $GLOBALS['phpgw']->vfs->get_size(array(
							'string'	=> $files['directory'] . '/' . $files['name'],
							'checksubdirs'	=> True,
							'relatives'	=> array (RELATIVE_NONE)));
			}
			else
			{
				$size = $GLOBALS['phpgw']->vfs->get_size(array(
							'string'	=> $files['directory'] . '/' . $files['name'],
							'checksubdirs'	=> False,
							'relatives'	=> array(RELATIVE_NONE)));
			}
			return $size;
		}

		function check_quota($file,$size = 0)
		{
			$size_homedir	= $this->get_size($this->homedir, True);
			$size_file	= $size ? (int)$size : $this->get_size(array('directory' => $this->path, 'name' => $file),False);
_debug_Array($this->quota);
			if($this->quota == -1)
			{
				return true;
			}
			elseif(($this->quota - $size_homedir - $size_file) > 0)
			{
				return true;
			}
			return false;
		}

		function f_execute()
		{
			if ($this->params['execute'] && $this->command_line != '')
			{
				if ($command = $GLOBALS['phpgw']->vfs->command_line(array('command_line' => stripslashes($this->command_line))))
				{
					$result[] = lang('Command sucessfully run');

					if ($command != 1 && strlen ($command) > 0)
					{
						$resutl[] = $command;
					}
				}
				else
				{
					$result[] = lang('Error running command');
				}
			}
			return is_array($result)?$result:False;
		}

		function f_update()
		{
			/* Update if they request it, or one out of 20 page loads */
			srand((double)microtime() * 1000000);
			if((isset($this->params['update']) && $this->params['update']) || rand(0,19) == 4)
			{
				/*
				* CAEIES VERY ANGRY : SEE BELOW
				* $dir_files = $this->get_dirfiles();
				* $sql_files = $this->get_sqlfiles();
				* $this->sync_files($dir_files,$sql_files);
				*/
				$GLOBALS['phpgw']->vfs->update_real(array('string' => $this->path,'relatives' => array(RELATIVE_NONE)));
			}
		}

		function f_compare()
		{
			$file=$this->basedir.$this->path.$this->dispsep.$this->file;
			$path=array();
			$table=array();
			if(count($this->filehis)>1)
			{
					if($this->filehis[0]> $this->filehis[1])
					{
						$temp=$this->filehis[0];
						$this->filehis[0]=$this->filehis[1];
						$this->filehis[1]=$temp;
					}
			}
			else
			{
				return array();
			}

			for($i=0;$i<count($this->filehis);++$i)
			{
				if($this->filehis[$i])
				{
					$path[$i]=$file."@".$this->filehis[$i];
				}
			}
			if(isset($this->svnpath) && !empty($this->svnpath))
			{
				$cmd=$this->svnpath." diff --non-interactive ".$path[0] ." ".$path[1];
			}
			else
			{
				$cmd="svn diff --non-interactive ".$path[0]." ".$path[1];
			}
			$diff=popen($cmd,"r");
			$indiffproper = false;
			$indiff = false;
			$getLine = true;
			$node = null;
			$table_rows=array();
			while (!feof($diff))
			{
				if ($getLine)
				{
					$line = fgets($diff);
				}
				$getLine = true;
				if ($indiff)
				{
					if ($indiffproper)
					{
						$var = array();
						if ($line[0] == " " || $line[0] == "+" || $line[0] == "-" || $line[0] == "\\")
						{
							$subline='';
							switch ($line[0])
							{
								case "\\":
									break;
								case " ":
									$subline = htmlentities(rtrim(substr($line, 1)));
									$var[] = array('widget' => array('type' => 'label','caption' =>$subline),'class'=>'diff');
									break;
								case "+":
									$subline = htmlentities(rtrim(substr($line, 1)));
									$var[] = array('widget' => array('type' => 'label','caption' =>$subline),'class'=>'diffadded');
									break;
								case "-":
									$subline = htmlentities(rtrim(substr($line, 1)));
									$var[] = array('widget' => array('type' => 'label','caption' =>$subline),'class'=>'diffdeleted');
									break;
							}
							if(!empty($subline))
							{
								$table_rows[] = array('table_col' => $var);
							}
							continue;
						}
						else
						{
							$indiffproper = false;
							$table[] = array('tablediff' => array('table_row' => $table_rows),'class' => 'diff');
							unset($table_rows);
							$getLine = false;
							continue;
						}
					}//end indiffproper
					// Check for the start of a new diff area
					if (!strncmp($line, "@@", 2))
					{
						$pos = strpos($line, "+");
						$posline = substr($line, $pos);
						$var=array();
						sscanf($posline, "+%d,%d", $sline, $eline);
						// Check that this isn't a file deletion
						if ($sline == 0 && $eline == 0)
						{
							$line = fgets($diff);
							//ignoring this line
							while ($line[0] == " " || $line[0] == "+" || $line[0] == "-")
							{
								$line = fgets($diff);
							}
							$getLine = false;
							$var[] = array('widget' => array('type' => 'label','caption' =>lang('File deleted')));
						}
						else
						{
							$var[] = array('widget' => array('type' => 'label','caption' =>$line));
							$indiffproper = true;
						}
						$table_rows[] = array('table_col' => $var);
						$table[] = array('tablediff' => array('table_row' => $table_rows));
						unset($var);
						unset($table_rows);
						continue;
					}
					else
					{
						$indiff = false;
					}
				}
				// Check for a new node entry
				if (strncmp(trim($line), "Index: ", 7) == 0)
				{
					// End the current node
					$var=array();
					$node = trim($line);
					$node = substr($node, 7);
					$var[] = array('widget' => array('type' => 'label','caption' => $node),'class' => 'newpath');
					$table_rows[] = array('table_col' => $var);
					unset($var);
					$line = fgets($diff);
					// Check for a file addition
					$line = fgets($diff);
					if (strpos($line, "(revision 0)"))
					{
						$var[] = array('widget' => array('type' => 'label','caption' => lang('File added')));
						$table_rows[] = array('table_col' => $var);
						unset($var);
					}
					if (strncmp(trim($line), "Cannot display:", 15) == 0)
					{
						$var[] = array('widget' => array('type' => 'label','caption' => $line));
						$table_rows[] = array('table_col' => $var);
						unset($var);
						continue;
					}
					// Skip second file info
					$line = fgets($diff);
					$indiff = true;
					$table_rows[] = array('table_col' => isset($var) ? $var : array());
					$table[] = array('tablediff' => array('table_row' => $table_rows));
					unset($table_rows);
					continue;
				}
			}
			$this->list_elements=$table;
		}

		/*
		* CAEIES VERY ANGRY
		* That's a pure non - sense
		* - First : what happens If we are using DAV and not SQL ??? => all files erased on a rand() ...
		* - Second : vfs->update_real should do this work : if it doesn't work, correct it !!!!

		function get_dirfiles($basedir = '')
		{
			if($basedir = '')
			{
				$basedir = $this->basedir . $this->path;
			}

			$files	= array();
			if(is_dir($basedir))
			{
				$dir	= opendir($basedir);
				$i		= 0;
				while ($file = readdir($dir)) 
				{
					if ($file != '.' && $file != '..') 
					{
						$files[$i] = $file;
						++$i;
					}
				}
				closedir($dir);
			}
			return $files;
		}

		function get_sqlfiles()
		{
			$ls_array = $GLOBALS['phpgw']->vfs->ls(array(
						'string'		=> $this->path,
						'relatives'		=> array (RELATIVE_NONE),
						'checksubdirs'	=> False,
						'nofiles'		=> False));

			$files	= array();
			if(is_array($ls_array))
			{
				$i = 0;
				foreach($ls_array as $key => $file)
				{
					$files[$i] = $file['name'];
					++$i;
				}
			}
			return $files;
		}

		function sync_files($dir_files,$sql_files)
		{
			if (is_array($sql_files))
			{
				$array_difference = array_diff($sql_files,$dir_files);
				sort($array_difference);
				for($i=0;$i<count($array_difference);++$i)
				{
					//echo $array_difference[$i];
					@$GLOBALS['phpgw']->vfs->delete(array('string' => $array_difference[$i]));
				}
			}
		}
		*/
	}
