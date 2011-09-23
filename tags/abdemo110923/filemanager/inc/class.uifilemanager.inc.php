<?php
	/***
	* phpGroupWare Filemanager
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
	 * Define UI_DEBUG
	 */
	define('UI_DEBUG',0);

	/**
	 * Filemanager GUI
	 * 
	 * @package filemanager
	 */
	class uifilemanager
	{
		var $public_functions = array
		(
			'index'		=> True,
			'indexhis'	 => True,
			'action'	=> True,
			'history'	=> True,
			'view'		=> True,
			'viewhis'	=> True,
			'edit'		=> True,
			'preferences'	=> True,
			'admin'		=> True,
			'edit_actions'	=> True,
			'compare'	=> True
		);

		var $bofilemanager;
		var $browser;
		var $maxperpage=20;
		//TODO use the API mime-icons ... still being implemented
		var $mime_ico = array
		(
			'application/pdf'		=> 'pdf',
			'application/postscript'	=> 'postscript',
			'application/msword'		=> 'word',
			'application/vnd.ms-excel'	=> 'excel',
			'application/vnd.ms-powerpoint'	=> 'ppt',
			'application/x-gzip'		=> 'tgz',
			'application/x-bzip'		=> 'tgz',
			'application/zip'		=> 'tgz',
			'application/x-debian-package'	=> 'deb',
			'application/x-rpm'		=> 'rpm',
			'application'			=> 'document',
			'application/octet-stream'	=> 'unknown',
			'audio'				=> 'sound',
			'audio/mpeg'			=> 'sound',
			'Directory'			=> 'folder',
			'exe'				=> 'exe',
			'image'				=> 'image',
			'text'				=> 'txt',
			'text/html'			=> 'html',
			'text/plain'			=> 'txt',
			'text/xml'			=> 'html',
			'text/x-vcalendar'		=> 'vcalendar',
			'text/calendar'			=> 'vcalendar',
			'text/x-vcard'			=> 'vcard',
			'text/x-tex'			=> 'tex',
			'unknown'			=> 'unknown',
			'video'				=> 'video',
			'message'			=> 'message'
		);

		function uifilemanager()
		{
			$this->action			= CreateObject('filemanager.uiaction_base');
			$this->bofilemanager	= $this->action->bofilemanager;
			$this->fileman			= $this->bofilemanager->fileman;
			$this->filehis			= $this->bofilemanager->filehis;
			$this->history_path	= $this->bofilemanager->history_path;
			$this->history_file	=	$this->bofilemanager->history_file;
			$this->path				= $this->bofilemanager->path;
			$this->homedir			= $this->bofilemanager->homedir;
			if(isset($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']))
			{
				$this->maxperpage= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			$this->check_access();
			$this->bofilemanager->f_update();

			$this->config = CreateObject('phpgwapi.config','filemanager');
			$this->config->read();
			if ($this->config->config_data)
			{
				$this->config_items = $this->config->config_data;
			}
		}
		
		function check_access()
		{
			$err = $this->bofilemanager->create_home_dir();
			if(strlen($err))
			{
				$error[] = $err;
			}

			if($this->bofilemanager->path != $this->bofilemanager->homedir && $this->bofilemanager->path != $this->bofilemanager->fakebase
				&& $this->bofilemanager->path != '/' && !$GLOBALS['phpgw']->vfs->acl_check(array(
				'string'=>$this->bofilemanager->path,
				'relatives' => Array(RELATIVE_NONE),
				'operation' => PHPGW_ACL_READ)))
			{
				$error[] = lang('you do not have access to %1',$this->bofilemanager->path);	
			}
			$this->bofilemanager->userinfo['working_id'] = $GLOBALS['phpgw']->vfs->working_id;
			$this->bofilemanager->userinfo['working_lid'] = $GLOBALS['phpgw']->accounts->id2lid($this->bofilemanager->userinfo['working_id']);

			
			// Verify path is real
			if($this->bofilemanager->debug)
			{
				echo 'DEBUG: ui.verify_path: PATH = '.$this->bofilemanager->path.'<br>'."\n";
				echo 'DEBUG: ui.verify_path: exists = '.$GLOBALS['phpgw']->vfs->file_exists(array(
					'string' => $this->bofilemanager->path,
					'relatives' => Array(RELATIVE_NONE))).'<br>'."\n";
			}
			
			if($this->bofilemanager->path != $this->bofilemanager->homedir &&
				$this->bofilemanager->path != '/' &&
				$this->bofilemanager->path != $this->bofilemanager->fakebase &&
				!$GLOBALS['phpgw']->vfs->file_exists(array(
					'string' => $this->bofilemanager->path,
					'relatives' => Array(RELATIVE_NONE)
					)))
			{
				$error[] = lang('directory %1 does not exist',$this->bofilemanager->path);
			}
			if(isset($error) && is_array($error))
			{
				if($this->bofilemanager->debug)
				{
					foreach($error as $key => $er)
					{
						echo 'DEBUG: ui.check_access: ' . $er ."\n";
					}
				}
			}
		}

		//Dispatches various file manager actions to the appropriate handler
		function action()
		{
			$params = get_var('params',array('POST','GET'));
			$show_upload_box = get_var('show_upload_boxes', 'GET');
			if($show_upload_box)
			{
				$GLOBALS['phpgw']->preferences->read();
				$GLOBALS['phpgw']->preferences->change('filemanager','show_upload_boxes',$show_upload_box);
				$GLOBALS['phpgw']->preferences->save_repository();
				$this->bofilemanager->show_upload_boxes = $show_upload_box;
			}

			if (UI_DEBUG)
			{
				echo 'uifilemanager -> Debug mode <br />';
				_debug_array($_POST);
			}
			$functions = Array
			(
				'rename',
				'delete',
				'go',
				'copy',
				'move',
				'download',
				'newdir',
				'newfile',
				'edit',
				'edit_comments',
				'apply_edit_comment',
				'apply_edit_name',
				'cancel',
				'upload',
				'execute',
				'update',
				'research',
				'compare',
				'next',
				'first',
				'last',
				'prev'
			);

			$bo_functions = array
			(
				'apply_edit_comment',
				'apply_edit_name',
				'copy',
				'delete',
				'move',
				'newdir',
				'newfile',
				'upload',
				'download',
				'execute',
				'update'
			);

			$link_data = array
			(
				'menuaction' => 'filemanager.uifilemanager.index',
				'path'			=> urlencode($this->bofilemanager->path)
			);
			$var_msg = '';
			if(is_array($params))
			{
				foreach($params as $true => $action)
				{
					$action = $true=='execute'?$true:$action;
					if($action != '' && in_array($action,$functions))
					{
						if(in_array($action,array('first','next','prev','last','research')))
						{
							$link_data['menuaction']='filemanager.uifilemanager.history';
							$link_data['vers'] =urlencode($this->bofilemanager->vers);
							$link_data['file']=urlencode($this->bofilemanager->file);
							$link_data['history_path']=urlencode($this->bofilemanager->path);
							$link_data['history_file']=urlencode($this->bofilemanager->file);
							$link_data['collapse']=urlencode($this->bofilemanager->collapse);
							$link_data['mime_type']=urlencode($this->bofilemanager->mime_type);
						}
						switch($action)
						{
							case 'rename':
								$link_data['rename_files'] = True;
								$edit = $this->bofilemanager->get_fileman();
								$this->bofilemanager->save_sessiondata($edit,'changes');

								if(!$this->bofilemanager->settings['name'])
								{
									$GLOBALS['phpgw']->preferences->read();
									$GLOBALS['phpgw']->preferences->change('filemanager','name','name');
									$GLOBALS['phpgw']->preferences->save_repository();
								}
								break;
							case 'research':
								$link_data['from_rev'] =urlencode($this->bofilemanager->from_rev);
								$link_data['to_rev'] =urlencode($this->bofilemanager->to_rev);
								break;
							case 'first':
								break;
							case 'next':
								$link_data['page']=urlencode($this->bofilemanager->page+1);
								break;
							case 'prev':
								$link_data['page']=urlencode($this->bofilemanager->page-1);
								break;
							case 'last':
								$link_data['page']=urlencode($this->bofilemanager->pages);
								break;
							case 'compare':
								$link_data['menuaction']	= 'filemanager.uifilemanager.compare';
								$link_data['file']=urlencode($this->bofilemanager->file);
								$link_data['collapse']=urlencode($this->bofilemanager->collapse);
								$link_data['page']=urlencode($this->bofilemanager->page);
								$link_data['pages']=urlencode($this->bofilemanager->pages);
								break;
							case 'edit_comments':
								$link_data['edit_comments'] = True;
								$edit = $this->bofilemanager->get_fileman();
								$this->bofilemanager->save_sessiondata($edit,'changes');
								
								if(!$this->bofilemanager->settings['comment'])
								{
									$GLOBALS['phpgw']->preferences->read();
									$GLOBALS['phpgw']->preferences->change('filemanager','comment','comment');
									$GLOBALS['phpgw']->preferences->save_repository();
								}
								break;
							case 'edit':
								$link_data['menuaction'] = 'filemanager.uiaction_edit.edit';
								break;
							case 'go':
								$link_data['path'] = urlencode($this->bofilemanager->todir);
								break;
							case 'cancel':
								$this->bofilemanager->unset_sessiondata();
								$var_msg = lang('action canceled');
								break;
							default:
								if(in_array($action,$bo_functions))
								{
									//echo ' bofunction: f_' . $action;
									$f_function = 'f_'.$action;
									$msg = $this->bofilemanager->$f_function();

									if($action == 'newfile' && !is_array($msg))
									{
										$link_data['menuaction']	= 'filemanager.uiaction_edit.edit';
										$link_data['edit_file']		= urlencode($this->bofilemanager->createfile);
									}
									elseif(is_array($msg))
									{
										$var_msg = implode("\n",$msg);
									}
								}
								break;
						}
					}
					elseif($action != '')
					{
						$var_msg = lang('unsupported action');
					}
				}
			}
			$link_data['msg'] = $var_msg;
			$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
		}

		function display_buttons($type = 'config')
		{
			$var = array();

			switch($type)
			{
				case 'config':
				case 'actions':
					$button = array
					(
						'type' 	=> 'submit',
						'name'	=> 'save',
						'value' => lang('save')
						//'caption' => $this->bofilemanager->build_help('save')
					);
					$var[] = array('height' => '50','valign' => 'bottom','widget' => $button);
					$button = array
					(
						'type' 	=> 'submit',
						'name' => 'cancel',
						'value' => lang('cancel')
						//'caption' => $this->bofilemanager->build_help('cancel')
					);
					if($type == 'actions')
					{
						$var[] = array('align' => 'right','colspan' => '2','height' => '50','valign' => 'bottom','widget' => $button);
					}
					else
					{
						$var[] = array('align' => 'right','height' => '50','valign' => 'bottom','widget' => $button);
					}
					break;
				case 'menu':
					$var['option'][] = array
					(
						'selected' => True,
						'caption' => lang('Menu -->')
					);

					if(isset($this->config_items['menu_disabled']) && is_array($this->config_items['menu_disabled']))
					{
						$disabled = $this->config_items['menu_disabled'];
					}
					else
					{
						$disabled = array();
					}

					$actions = array
					(
						'edit'			=> lang('edit'),
						'rename'		=> lang('rename'),
						'delete'		=> lang('delete'),
						'edit_comments'	=> lang('edit comments')
					);

					foreach($actions as $key => $trans)
					{
						if(!in_array($key,$disabled))
						{
							$var['option'][] = array
							(
								'value'		=> $key,
								'caption'	=> $trans
							);
						}
					}

					$var['option'][] = array
					(
						'disabled' => True,
						'caption' => '---------------'
					);
					$var['option'][] = array
					(
						'value' => 'move',
						'caption' => lang('Move To:')
					);

					$var['option'][] = array
					(
						'value' => 'copy',
						'caption' => lang('Copy To:')
					);
					$var['option'][] = array
					(
						'disabled' => True,
						'caption' => '---------------'
					);
					$var['option'][] = array
					(
						'value' => 'go',
						'caption' => lang('Go To:')
					);
					break;
				case 'dir_menu':
					$var['option'][] = array
					(
						'selected' => True,
						'caption' => lang('Choose Directory -->')
					);

					
					// First we get the directories in their home directory

					$dirs[] = Array
					(
						'directory' => $this->bofilemanager->fakebase,
						'name' => $this->bofilemanager->userinfo['account_lid']
					);

					$ls_array = $GLOBALS['phpgw']->vfs->ls(array(
					'string' => $this->bofilemanager->homedir,
					'relatives' => Array(RELATIVE_NONE),
					'checksubdirs'	=> True,
					'mime_type' => 'Directory'));

					//_debug_array($ls_array);
					reset($ls_array);
					while(list($num,$dir) = each($ls_array))
					{
						$dirs[] = $dir;
					}

					
					// Then we get the directories in their membership's home directories

					reset($this->bofilemanager->memberships);
					while(list($num,$group_array) = each($this->bofilemanager->memberships))
					{
						$dirs[] = Array(
						'directory' => $this->bofilemanager->fakebase,
						'name' => $GLOBALS['phpgw']->accounts->id2lid($group_array['account_id'])
						);

						$ls_array = $GLOBALS['phpgw']->vfs->ls(array(
						'string' => "{$this->bofilemanager->fakebase}/" . $GLOBALS['phpgw']->accounts->id2lid($group_array['account_id']),
						'relatives' => Array(RELATIVE_NONE),
						'checksubdirs'	=> True,
						'mime_type' => 'Directory'
						));
						while(list($num,$dir) = each($ls_array))
						{
							$dirs[] = $dir;
						}
					}

					//_debug_array($dirs);

					$dir_list = array();
					reset($dirs);
					while(list($num, $dir) = each($dirs))
					{
						if(!$dir['directory'])
						{
							continue;
						}
		
						// So we don't display //

						if($dir['directory'] != '/')
						{
							$dir['directory'] .= '/';
						}

						// No point in displaying the current directory, or a directory that doesn't exist
			
						if(($dir['directory'].$dir['name']) != $this->bofilemanager->path && $GLOBALS['phpgw']->vfs->file_exists(array(
							'string' => $dir['directory'].$dir['name'],
							'relatives' => Array(RELATIVE_NONE)
						)))
						{
							$var['option'][] = array('value'=> urlencode($dir['directory'].$dir['name']),
							'caption' => $dir['directory'].$dir['name']
							);
						}
					}
					break;
				default:
					if($this->bofilemanager->path != '/' && $this->bofilemanager->path != $this->bofilemanager->fakebase)
					{
						$var[]	= array('widget' => array('type'=>'submit',
											'name'=> 'download',
											'value' => lang('download'),
											'caption' => $this->bofilemanager->build_help('download')
											));
					}

					if($this->bofilemanager->settings['show_command_line'])
					{
						$var[] = array('widget' => array( 'type' => 'text' ,
												'name'=> 'command_line',
												'size' => '50',
												'caption' => $this->bofilemanager->build_help('command_line')
											));
						$var[] = array('widget' => array( 'type' => 'submit',
												'name' => 'execute',
												'value' => lang('execute'),
												'caption' => $this->bofilemanager->build_help('execute')
											));
						$var[] = array('widget' => array( 'type' => 'seperator' ));
					}
					break;
			}
			return $var;
		}

		function display_uploads()
		{
			for($i=0;$i<$this->bofilemanager->show_upload_boxes;++$i)
			{
					$var = array();
					$var[] = array('widget'	=> array('type' => 'file',
									'name'	=> 'upload_file[]',
								'maxlength'	=> '255'
								));

					$var[] = array('widget'	=> array('type' => 'text',
									'name'	=> 'upload_comment[]'
								));

					$var[] = array('widget' => array('type' => 'empty'));
					$table_rows[] = array('table_col' => $var);
			}
			$var = array();
			$var[] = array('widget'	=> array('type' => 'hidden',
							'name'	=> 'show_upload_boxes',
							'value' => $this->bofilemanager->show_upload_boxes
							));

			$table_rows[] = array('table_col' => $var);
			return array('table_row' => $table_rows);
		}

		function dirs_first($files_array)
		{
			$dirs	= array();
			$files	= array();
			$result	= array(); 

			for($i=0;$i!=count($files_array);$i++)
			{
				$file = $files_array[$i];
				if ($file['mime_type'] == 'Directory')
				{
					$dirs[] = $file;
				}
				else
				{
					$files[] = $file;
				}
			}
			return array_merge($dirs, $files);
		}

		function index()
		{
			$rename_files = get_var('rename_files','GET');
			$edit_comments = get_var('edit_comments','GET');

			$files_array = $this->bofilemanager->load_files();
			//_debug_array($files_array);
			$usage = 0;
			$files_array = $this->dirs_first($files_array);

			$file_attributes[] =  array('widget'=> array('type' => 'plain','caption' => lang('sort by')),
										'help' => array('widget' => array('type' => 'help','onClick' => $this->bofilemanager->build_help('sort_by'))));

			//_debug_array($this->bofilemanager->settings);

			$link_data = array
			(
				'menuaction'	=> 'filemanager.uifilemanager.index',
				'path'			=> $this->bofilemanager->path
			);

			foreach($this->bofilemanager->file_attributes as $attribute => $translation)
			{
				if (isset($this->bofilemanager->settings[$attribute]))
				{
					$link_data['sortby'] = $attribute;
					$file_attributes[] = array('widget'=> array('type' => 'link','caption' => lang($attribute),
																'href' =>  $GLOBALS['phpgw']->link('/index.php',$link_data)),
												'help'	=> array('widget' => array('type' => 'help','onClick' => $this->bofilemanager->build_help($attribute))));
				}
			}

			$file_output = array();
			for($i=0;$i<count($files_array);$i++)
			{
				$file = $files_array[$i];
				$file_output[$i]['checkbox'] = array('widget' => array( 'type' => 'checkbox','name' => 'fileman[]','value' => $file['name'],
																		'checked' => (isset($this->bofilemanager->changes[$file['name']]) && $this->bofilemanager->changes[$file['name']] == $file['name']?True:False)));
				@reset($this->bofilemanager->file_attributes);
				while(list($internal,$displayed) = each($this->bofilemanager->file_attributes))
				{
					if (isset($this->bofilemanager->settings[$internal]))
					{
						switch($internal)
						{
							case 'owner_id':
							case 'owner':
							case 'createdby_id':
							case 'modifiedby_id':
									$name = '';
									if(isset($file[$internal]))
									{
										$name = $GLOBALS['phpgw']->accounts->id2name($file[$internal]) ;
									}
									$file_output[$i][$internal] = $name ? $name: '';
									break;
							case 'created':
							case 'modified':
									//Convert ISO 8601 date format used by DAV into something people can read
									$file_output[$i][$internal] =  $this->bofilemanager->convert_date($file[$internal]);
									break;
							case 'name':
									if ($rename_files && $this->bofilemanager->changes[$file['name']] == $file['name'])
									{
										$file_output[$i][$internal] = array('widget' => array('type' => 'text',
																							'name' => 'changes['.$file['name'].']',
																							'value' => $file[$internal]));
									}
									else
									{
										$mime_parts = explode('/',$file['mime_type']);		
										$file_icon = isset($this->mime_ico[$file['mime_type']]) ? $this->mime_ico[$file['mime_type']] : '';
										if (!$file_icon)
										{
											$file_icon = ( $this->mime_ico[$mime_parts[0]]) ?  $this->mime_ico[$mime_parts[0]] :  $this->mime_ico['unknown'];
											if (strpos($file['name'],'.exe') !== false) $file_icon =  $this->mime_ico['exe'];
										}
										$file_output[$i]['name']['icon'] = array('widget' => array( 'type' => 'image',
																'src' => $GLOBALS['phpgw']->common->image('filemanager',$file_icon)));
										if ($file['mime_type']=='Directory')
										{
											$link_data['path'] = $this->bofilemanager->path . $this->bofilemanager->dispsep . $file['name'];
											$href = $GLOBALS['phpgw']->link('/index.php',$link_data);
											$onClick = '';
										}
										else
										{
											$href = '#';
											$onClick = "open_popup('" . $GLOBALS['phpgw']->link('/index.php',array(
																								'menuaction'	=> 'filemanager.ui'
																													.'filemanager.view',
																								'path' => urlencode($this->bofilemanager->path),
																								'file' => urlencode($file['name']))) . "','600','600');";
										}
										$file_output[$i]['name']['link'] = array('widget' => array('type' => 'link','caption' => $file['name'],
																									'href' => $href,'onClick' => $onClick));
										if($mime_parts[0] == 'text')
										{
											$link_data['menuaction']	= 'filemanager.uiaction_edit.edit';
											$link_data['edit_file']		= urlencode($file['name']);
											$link_data['path'] = $this->bofilemanager->path;
											$file_output[$i]['name']['edit'] = array('widget' => array( 'type' => 'image',
																'src' => $GLOBALS['phpgw']->common->image('filemanager','pencil'),
																'link' =>  $GLOBALS['phpgw']->link('/index.php',$link_data)));
										}
									}
									break;
							case 'comment':
									if ($edit_comments && $this->bofilemanager->changes[$file['name']] == $file['name'])
									{
										$file_output[$i][$internal] = array('widget' => array('type' => 'text',
																							'name' => 'changes['.$file['name'].']',
																							'value' => $file[$internal]));
									}
									else
									{
										$file_output[$i][$internal] = isset($file[$internal]) ? $file[$internal] : '';
									}
									break;
							case 'size':
								$file_output[$i][$internal] = $this->bofilemanager->borkb($file[$internal]);
								break;
							case 'version':
								if(isset($file[$internal]))
								{
									$file_output[$i][$internal] = array('widget' => array('type'	=> 'link',
																					'onClick'	=> "open_popup('" . $GLOBALS['phpgw']->link('/index.php',
																					array(
																						'menuaction' => 'filemanager.uifilemanager.history',
																						'file' => urlencode($file['name']),
																						'vers' => urlencode($file[$internal]),
																						'mime_type'=> urlencode($file['mime_type']),
																						'collapse' => urlencode('collapse'),
																						'history_path'=> urlencode($this->bofilemanager->path),
																						'history_file' => urlencode($file['name'])
																						)
																					).
																						"','1000','800');",
																					'href'		=> '#',
																					'caption'	=> $file[$internal]));
								}
								else
								{
									$file_output[$i][$internal] = '';
								}
								break;
							default:
									$file_output[$i][$internal] = $file[$internal];
						}
					}
				}
			}

			$free = False;
			if ($this->bofilemanager->quota != -1)
			{
				$free = $this->bofilemanager->borkb($this->bofilemanager->quota - $this->bofilemanager->get_size($this->homedir,True));	
			}

			$data = array
			(
				'summary' =>  array
				(
					'file_count'		=> $this->bofilemanager->count_files($this->path,False),
					'lang_files'		=> lang('files'),
					'lang_space'		=> lang('used space'),
					'lang_unused'		=> lang('unused space'),
					'usage'				=> $this->bofilemanager->borkb($this->bofilemanager->get_size($this->homedir,True)),
					'files_total'		=> $this->bofilemanager->count_files($this->homedir,True),
					'lang_files_total'	=> lang('files total'),
				),
				'files' => array
				(
						'file_attributes'	=> $file_attributes,
						'file'				=> $file_output
				),
				'form'	=> array('action'	=> $GLOBALS['phpgw']->link('/index.php',array(
													'menuaction' => 'filemanager.uifilemanager.action',
														'path' => urlencode($this->bofilemanager->path))),
														'id'	=> 'form_files',
														'name'	=> 'files',
														'method'	=> 'POST',
														'enctype'	=> 'multipart/form-data'),
				'error'		=> (isset($this->bofilemanager->errors) && is_array(unserialize(base64_decode($this->bofilemanager->errors)))?$GLOBALS['phpgw']->common->error_list(unserialize(base64_decode($this->bofilemanager->errors)),'Results'):''),
				'img_home'	=> array('widget' => array('type' => 'image',
														'src' => $GLOBALS['phpgw']->common->image('filemanager','folder_large'),
														'title' => lang('go to your home directory'),
														'link' => $GLOBALS['phpgw']->link('/index.php',Array(
																	'menuaction' => 'filemanager.uifilemanager.index',
																	'path' => urlencode($this->bofilemanager->homedir))))),
				'help_home'	=> array('widget' => array('type' => 'help','onClick' => $this->bofilemanager->build_help('home'))),
				'current_dir'	=> $this->bofilemanager->path,
				'help_dir'	=> array('widget' => array('type' => 'help','onClick' => $this->bofilemanager->build_help('current_dir'))),
				'img_dir'	=> array('widget' => array('type' => 'image',
											'src' => $GLOBALS['phpgw']->common->image('filemanager',($this->bofilemanager->homestr?'folder_home':'folder')),
											'title' => lang('current directory'))),
				'img_refresh'	=> array('widget' => array('type' => 'image',
											'src' => $GLOBALS['phpgw']->common->image('filemanager','reload'),
											'title' => lang('refresh'),
											'name'	=> 'params[update]',
											'value'	=> 'update')),
				'help_refresh'	=> array('widget' => array('type' => 'help','onClick' => $this->bofilemanager->build_help('refresh')))
					/*'add_moz_sidebar' => array
					(
						'url'			=> $GLOBALS['phpgw']->link('/index.php',array(
																						'menuaction'	=> 'filemanager.uifilemanager.index',
																						'path'			=> urlencode($this->bofilemanager->path)
																					)),
						'label'			=> lang('phpgroupware files'),
						'link_label'	=> lang('add mozilla/netscape sidebar tab')
					)*/
			);
			if($free === False)
			{
				$data['summary']['unused'] = $free;
			}

			if($this->bofilemanager->path != '/')
			{
				$data['img_up']	= array('widget' => array('type' => 'image',
											'src' => $GLOBALS['phpgw']->common->image('filemanager','up'),
											'title' => lang('up'),
											'link' => $GLOBALS['phpgw']->link('/index.php',Array(
													'menuaction'	=> 'filemanager.uifilemanager.index',
													'path'		=> urlencode($this->bofilemanager->lesspath)))));
				$data['help_up'] = array('widget' => array('type' => 'help','onClick' => $this->bofilemanager->build_help('up')));

				if($this->bofilemanager->path != $this->bofilemanager->fakebase && $this->bofilemanager->access_add)
				{
					if(isset($this->bofilemanager->settings['show_command_line']))
					{
						$data['command_line'] = array('widget' => array('type' => 'text',
							 									'name' => 'command_line',
												 				'maxlength' => '255',
												 				'size' => '25'));
						$data['execute'] = array('widget' => array('type' => 'submit',
							 									'name' => 'params[execute]',
												 				'value' => lang('execute')));
						$data['help_command_line']	= array('widget' => array('type' => 'help','onClick' => $this->bofilemanager->build_help('command_line')));
						$data['help_execute']		= array('widget' => array('type' => 'help','onClick' => $this->bofilemanager->build_help('execute')));
					}

					$data['create_folder']	= array('widget' => array('type' => 'text',
							 									'name' => 'createdir',
												 				'maxlength' => '255',
												 				'size' => '15'));
					$data['img_create_folder']	= array('widget' => array('type' => 'image',
																	'src' => $GLOBALS['phpgw']->common->image('filemanager','folder_new'),
																	'title' => lang('create folder'),
																	'name'	=> 'params[newdir]',
																	'value'	=> 'newdir'));
					$data['lang_create_folder']	= lang('create folder');
					$data['help_create_folder']	= array('widget' => array('type' => 'help','onClick' => $this->bofilemanager->build_help('create_folder')));
					$data['create_file']		= array('widget' => array('type' => 'text',
							 									'name' => 'createfile',
												 				'maxlength' => '255',
												 				'size' => '15'));
					$data['img_create_file']	= array('widget' => array('type' => 'image',
																	'src' => $GLOBALS['phpgw']->common->image('filemanager','filenew'),
																	'title' => lang('create file'),
																	'name'	=> 'params[newfile]',
																	'value'	=> 'newfile'));
					$data['lang_create_file']	= lang('create file');
					$data['help_create_file']	= array('widget' => array('type' => 'help','onClick' => $this->bofilemanager->build_help('create_file')));

					$data['lang_show']					= lang('show');
					$data['lang_upload_fields']			= lang('upload fields');
					$data['help_upload_file']			= array('widget' => array('type' => 'help','onClick' => $this->bofilemanager->build_help('upload_file')));
					$data['lang_file']					= lang('file');
					$data['lang_comment']				= lang('comment');
					$data['help_upload_comment']		= array('widget' => array('type' => 'help','onClick' => $this->bofilemanager->build_help('upload_comment')));
					$data['help_show_upload_fields']	= array('widget' => array('type' => 'help','onClick' => $this->bofilemanager->build_help('show_upload_fields')));

					foreach($this->bofilemanager->upload_boxes as $box)
					{
						$data['show_upload_boxes'][] = array('widget' => array('type' => 'link','caption' => $box,
																			'href' => $GLOBALS['phpgw']->link('/index.php',array(
																				'menuaction' => 'filemanager.uifilemanager.action',
																				'show_upload_boxes' => $box))));
					}
					$data['uploads']		= $this->display_uploads();
					$data['img_upload']	= array('widget' => array('type' => 'image',
											'src' => $GLOBALS['phpgw']->common->image('filemanager','1uparrow'),
											'title' => lang('upload files'),
											'name'	=> 'params[upload]',
											'value'	=> 'upload'));
					$data['help_upload_files']	= array('widget' => array('type' => 'help','onClick' => $this->bofilemanager->build_help('upload_files')));
			   		$data['lang_upload']	= lang('upload');

					$js		= "document.getElementById('file_menu').selectedIndex>5?(document.getElementById('menu_todir').disabled=false):document.getElementById('form_files').submit()"; 
					$js_dir	= "document.getElementById('form_files').submit()";

					$data['menu']			= array('widget' => array('type' => 'select','name' => 'params[menu]', 'id' => 'file_menu','onChange' => $js,'options' => $this->display_buttons('menu')));
					$data['help_menu']		= array('widget' => array('type' => 'help','onClick' => $this->bofilemanager->build_help('menu')));
					$data['dir_menu']		= array('widget' => array('type' => 'select','name' => 'todir', 'id' => 'menu_todir','onChange' => $js_dir,'disabled' => True,'options' => $this->display_buttons('dir_menu')));
					$data['help_dir_list']	= array('widget' => array('type' => 'help','onClick' => $this->bofilemanager->build_help('dir_list')));
					$data['img_dl']	= array('widget' => array('type' => 'image',
														'src' => $GLOBALS['phpgw']->common->image('filemanager','bottom'),
														'title' => lang('download files'),
														'name' => 'params[download]',
														'value' => 'download'));
					$data['help_dl']	= array('widget' => array('type' => 'help','onClick' => $this->bofilemanager->build_help('download')));
					$data['lang_dl']	= lang('download');
				}
			}

			/*if (strlen($this->bofilemanager->errors))
			{
				$data['errors'] = $this->bofilemanager->errors;
			}*/
			$msg = get_var('msg', 'GET', '');
			if(strlen($msg) > 0)
			{
				$data['msg'] = $msg;
			}

			if ($rename_files || $edit_comments)
			{
				$data['rename'] = array
				(
					'img_ok' => array('widget' => array('type' => 'image',
											'src' => $GLOBALS['phpgw']->common->image('filemanager','button_ok'),
											'title' => lang('apply changes'),
											'name'	=> 'params[apply_edit_' . ($rename_files?'name':'comment') . ']',
											'value'	=> 'apply_edit_' . ($rename_files?'name':'comment'))),
					'img_cancel' => array('widget' => array('type' => 'image',
											'src' => $GLOBALS['phpgw']->common->image('filemanager','button_cancel'),
											'title' => lang('cancel'),
											'name'	=> 'params[cancel]',
											'value'	=> 'cancel'))
				);
			}
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('index' => $data));
		}

		function indexhis()
		{
			$GLOBALS['phpgw_info']['flags']['noframework']=True;
			$link_data = array
			(
				'menuaction'	=> 'filemanager.uifilemanager.indexhis',
				'history_path'			=> $this->bofilemanager->history_path
			);
			foreach($this->bofilemanager->file_attributes as $attribute => $translation)
			{
				if (isset($this->bofilemanager->settings[$attribute]))
				{
					$file_attributes[] = array('widget'=> array('type' => 'label','caption' => lang($attribute)));
				}
			}

			$files_array=$this->bofilemanager->load_files(True);
			$files_array = $this->dirs_first($files_array);
			$file_output = array();
			for($i=0;$i<count($files_array);$i++)
			{
				$file = $files_array[$i];
				@reset($this->bofilemanager->file_attributes);
				while(list($internal,$displayed) = each($this->bofilemanager->file_attributes))
				{
					if (isset($this->bofilemanager->settings[$internal]))
					{
						switch($internal)
						{
							case 'owner_id':
							case 'owner':
							case 'createdby_id':
							case 'modifiedby_id':
									$name = '';
									if(isset($file[$internal]))
									{
										$name = $GLOBALS['phpgw']->accounts->id2name($file[$internal]) ;
									}
									$file_output[$i][$internal] = $name ? $name: '';
									break;
							case 'created':
							case 'modified':
									//Convert ISO 8601 date format used by DAV into something people can read
									$file_output[$i][$internal] =  $this->bofilemanager->convert_date($file[$internal]);
									break;
							case 'name':
									$mime_parts = explode('/',$file['mime_type']);
									$file_icon = isset($this->mime_ico[$file['mime_type']]) ? $this->mime_ico[$file['mime_type']] : '';
									if (!$file_icon)
									{
										$file_icon = ( $this->mime_ico[$mime_parts[0]]) ?  $this->mime_ico[$mime_parts[0]] :  $this->mime_ico['unknown'];
										if (strpos($file['name'],'.exe') !== false) $file_icon =  $this->mime_ico['exe'];
									}
									$file_output[$i]['name']['icon'] = array('widget' => array( 'type' => 'image',
															'src' => $GLOBALS['phpgw']->common->image('filemanager',$file_icon)));
									if ($file['mime_type']=='Directory')
									{
										$link_data['history_path'] = $this->bofilemanager->history_path. $this->bofilemanager->dispsep . $file['name'];
										$link_data['vers']= $this->bofilemanager-> vers;
										$href = $GLOBALS['phpgw']->link('/index.php',$link_data);
									}
									else
									{
										$href = '#';
										$onClick = "open_popup('" . $GLOBALS['phpgw']->link('/index.php',array(
																							'menuaction'	=> 'filemanager.ui'
																												.'filemanager.viewhis',
																							'vers' =>	urlencode($this->bofilemanager->vers),
																							'history_path' => urlencode($this->bofilemanager->history_path),
																							'history_file' => urlencode($file['name']))) . "','600','600');";
									}
									$file_output[$i]['name']['link'] = array('widget' => array('type' => 'link','caption' => $file['name'],																								'href' => $href,'onClick' => isset($onClick) ? $onClick : ''));
									break;
							case 'size':
								$file_output[$i][$internal] = $this->bofilemanager->borkb($file[$internal]);
								break;
							case 'version':
								$file_output[$i][$internal] = array('widget' => array('type'	=> 'label','caption'	=> $file[$internal]));
							default:
									$file_output[$i][$internal] = isset($file[$internal]) ? $file[$internal] : '';
						}
					}
				}
			}

			$data= array(
				'files' => array
				(
						'file_attributes'	=> $file_attributes,
						'file'				=>	 $file_output
				),
				'img_dir'	=> array('widget' => array('type' => 'image',
											'src' => $GLOBALS['phpgw']->common->image('filemanager',($this->bofilemanager->homestr?'folder_home':'folder')),
											'title' => lang('current directory'))),
				'current_dir'	=> $this->bofilemanager->history_path,
				'img_vers'	=> array('widget' => array('type' => 'image',
											'src' => $GLOBALS['phpgw']->common->image('filemanager','version')),
											'title' => lang('current version')),
				'current_vers' => $this->bofilemanager->vers,
				'img_home'	=> array('widget' => array('type' => 'image',
														'src' => $GLOBALS['phpgw']->common->image('filemanager','folder_large'),
														'title' => lang('go to your home directory'),
														'link' => $GLOBALS['phpgw']->link('/index.php',Array(
																	'menuaction' => 'filemanager.uifilemanager.indexhis',
																	'vers'		 => urlencode($this->bofilemanager->vers),
																	'history_path' => urlencode($this->bofilemanager->homedir))))),
				'lang_close'	=> lang('close window')
			);
			if($this->bofilemanager->path != '/')
			{
				$data['img_up']	= array('widget' => array('type' => 'image',
											'src' => $GLOBALS['phpgw']->common->image('filemanager','up'),
											'title' => lang('up'),
											'link' => $GLOBALS['phpgw']->link('/index.php',Array(
													'menuaction'	=> 'filemanager.uifilemanager.indexhis',
													'vers'		 => urlencode($this->bofilemanager->vers),
													'history_path'		=> urlencode($this->bofilemanager->historylesspath)))));
			}
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('index' => $data));
		}


		function compare()
		{
			$GLOBALS['phpgw_info']['flags']['noframework']=True;
			$this->bofilemanager->f_compare();
			$GLOBALS['phpgw']->xslttpl->set_var('compare', array('diff' => $this->bofilemanager->list_elements));
		}


		function edit_comments()
		{
			$edit=array();
			for ($i=0; $i!=count($this->bofilemanager->fileman);$i++)
			{
				$edit[$this->bofilemanager->fileman[$i]] = 'comment';
			}
			$this->index($edit);
		}

		function view()
		{
			$GLOBALS['phpgw_info']['flags']['noframework'] = True;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $GLOBALS['phpgw']->vfs->view(array('string' => $this->path.'/'. $this->bofilemanager->file,'relatives'	=> array (RELATIVE_NONE)))));
		}

		function viewhis()
		{
			$GLOBALS['phpgw_info']['flags']['noframework'] = True;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $GLOBALS['phpgw']->vfs->view(array('string' => $this->history_path.'/'. $this->bofilemanager->history_file,'rev' => $this->bofilemanager->vers,'relatives'	=> array (RELATIVE_NONE)))));
		}

		function history()
		{
			$GLOBALS['phpgw_info']['flags']['noframework'] = True;
			$file = $this->bofilemanager->path.$this->bofilemanager->dispsep.$this->bofilemanager->file;
			$from_rev=$this->bofilemanager->from_rev;
			$to_rev=$this->bofilemanager->to_rev;
			$vers= $this->bofilemanager->vers;
			$collapse=$this->bofilemanager->collapse;
			$mime_type=$this->bofilemanager->mime_type;
			$page=$this->bofilemanager->page;
			$pages=0;

	 			if($GLOBALS['phpgw']->vfs->file_exists(array('string' => $file,'relatives' => Array(RELATIVE_NONE))))
			{
				$col_headers = array
				(
					lang('')			 => '',
					lang('version')				=> 'version',
					lang('date')				=> 'created',
					lang('performed by')		=> 'owner_id',
					lang('operation')			=> 'comment'
				);
				foreach($col_headers as $label => $field)
				{
					$header[] = array('widget' => array('type' => 'label','caption' => $label));
				}
				$table_head = array('table_col' => $header);

				if($from_rev && $to_rev)
				{
						if(!is_numeric($from_rev) || $from_rev <0 || $from_rev>$vers)
						{
							$from_rev=1;
						}
						if(!is_numeric($to_rev) || $to_rev >$vers || $to_rev<0)
						{
							$to_rev=$vers;
						}

						if($from_rev>$to_rev)
						{
							$temp=$from_rev;
							$from_rev=$to_rev;
							$to_rev=$temp;
						}
				}
				elseif($from_rev)
				{
					if(!is_numeric($from_rev) || $from_rev <0 || $from_rev>$vers)
					{
						$from_rev=1;
					}
					$to_rev=$vers;
				}
				elseif($to_rev)
				{
					if(!is_numeric($to_rev) || $to_rev <0 || $to_rev>$vers)
					{
						$to_rev=$vers;
					}
					$from_rev=1;
				}

				$data = array(
					'string' => $file,
					'from_rev'=>$from_rev,
					'to_rev' => $to_rev,
					'vers' =>$vers,
					'relatives' => Array(RELATIVE_NONE)
				);

				if($collapse)
				{
					$data['collapse']='collapse';
				}
				$journal_array = $GLOBALS['phpgw']->vfs->get_journal($data);
				$revision=count($journal_array);
				 // Calculate the number of pages
				$pages = floor($revision / $this->maxperpage);
				if($revision % $this->maxperpage) $pages++;
				if($page > $pages)
				{
					$page= $pages;
				}
				else if($page<1)
				{
						$page=1;
				}
				$this->bofilemanager->save_page($page,$pages);
				if(is_array($journal_array))
				{
					@reset($journal_array);
					$startrevision=($page-1)*$this->maxperpage;
					$endrevision=$startrevision+$this->maxperpage;
					if($pages==0)
					{
						$startrevision=0;
						$endrevision=0;
					}
					for($i=$startrevision; $i<$endrevision;$i++)
					{
						if($i==$revision) break;
						$journal_entry=$journal_array[$i];
						$var = array();
						@reset($col_headers);
						foreach($col_headers as $label => $field)
						{
							switch($field)
							{
								case 'owner_id':
									$var[] = array('widget' => array('type' => 'label','caption' => $GLOBALS['phpgw']->common->grab_owner_name(isset($journal_entry[$field]) ? $journal_entry[$field] : '')));
									break;
								case 'created':
									$var[] = array('widget' => array('type' => 'label','caption' => $this->bofilemanager->convert_date($journal_entry[$field])));
									break;
								case '':
									$var[] = array('widget' => array('type' => 'checkbox','name'=>'filehis[]','value'=>$journal_entry['version'],'onClick' => 'checkCB(this)'));
									break;
								case 'version':
									if($mime_type=='Directory')
									{
										$href = '#';
										$onClick = "open_popup('" . $GLOBALS['phpgw']->link('/index.php',array(
																							'menuaction'	=> 'filemanager.ui'
																												.'filemanager.indexhis',
																							'path' => urlencode($this->bofilemanager->path),
																							'file' => urlencode($this->bofilemanager->file),
																							'history_path' => urlencode($this->bofilemanager->history_path),
																							'history_file' => urlencode($this->bofilemanager->history_file),
																							'vers' => urlencode($journal_entry[$field]))) . "','800','800');";
	 									}
									else
									{
										$href = '#';
										$onClick = "open_popup('" . $GLOBALS['phpgw']->link('/index.php',array(
																							'menuaction'	=> 'filemanager.ui'
																												.'filemanager.viewhis',
																							'path' => urlencode($this->bofilemanager->path),
																							'history_path' => urlencode($this->bofilemanager->history_path),
																							'file' => urlencode($this->bofilemanager->file),
																							'history_file' => urlencode($this->bofilemanager->history_file),
																							'vers' => urlencode($journal_entry[$field]))) . "','800','800');";
									}
									$var[] = array('widget' => array('type'	=> 'link','caption'	=> $journal_entry[$field],'href' => $href,'onClick'	=> $onClick));
									break;
								default:
									$var[] = array('widget' => array('type' => 'label','caption' => $journal_entry[$field]));
									break;
							}
						}
						$table_rows[] = array('table_col' => $var);
						$table_footer = array();
					}
				}
				$data = array
				(
					'form'	=> array('action'	=> $GLOBALS['phpgw']->link('/index.php',array(
													'menuaction' => 'filemanager.uifilemanager.action',
														'path' => urlencode($this->bofilemanager->path))),
														'id'	=> 'form_history',
														'name'	=> 'history',
														'method'	=> 'POST',
														'enctype'	=> 'multipart/form-data'),
					'error'		=> (isset($this->bofilemanager->errors) && is_array(unserialize(base64_decode($this->bofilemanager->errors)))?$GLOBALS['phpgw']->common->error_list(unserialize(base64_decode($this->bofilemanager->errors)),'Results'):''),
					'error'			=> '',
					'action_url'	=> '#',
					'title'			=> lang('history for %1',$file),
					'table'			=> array('width' => '100%','table_head' => $table_head ,'table_row' => $table_rows,'table_footer' => $table_footer),
					'lang_close'	=> lang('close window')
				);
				$data['lang_from_rev'] = lang('from rev');
				$data['from']	=	array('widget'	=> array('type' => 'text',
						 									'name' => 'from_rev',
											 				'maxlength' => '255',
											 				'size' => '15',
															'value' => $from_rev));
				$data['lang_to_rev']	= lang('to rev');

				$data['to']	= array('widget'	=> array('type' => 'text',
						 									'name' => 'to_rev',
											 				'maxlength' => '255',
															'size' => '15',
															'value' => $to_rev));
				$data['img_search'] =	array('widget' => array('type' => 'image',
																'src' => $GLOBALS['phpgw']->common->image('filemanager','reload'),
																'title' => lang('search'),
																'name'	=> 'params[research]',
																'value'	=> 'research'));
				$data['file']	= array('widget'	=> array('type' => 'hidden',
						'name'	=> 'file',
						'value' =>	$this->bofilemanager->file
						));
				$data['vers']	= array('widget'	=> array('type' => 'hidden',
						'name'	=> 'vers',
						'value' =>	$this->bofilemanager->vers
						));
				$data['mime_type']	= array('widget'	=> array('type' => 'hidden',
						'name'	=> 'mime_type',
						'value' =>	$this->bofilemanager->mime_type
						));
				$data['collapse'] = array('widget' => array('type' => 'checkbox','name'=>'collapse','value'=>'collapse','caption'=> 'collapse','checked' => (isset($this->bofilemanager->collapse)?True:False)));
				$data['img_compare'] = array('widget' => array('type' => 'image',
																	'src' => $GLOBALS['phpgw']->common->image('filemanager','button_ok'),
																	'title' => lang('Compare'),
																	'name'	=> 'params[compare]',
																	'value'	=> 'compare'));
				if($page>1)
				{
					$data['img_first'] = array('widget' => array('type' => 'image',
																'src' => $GLOBALS['phpgw']->common->image('filemanager','first-grey'),
																'title' => lang('first'),
																'name'	=> 'params[first]',
																'value'	=> 'first'));
					$data['img_prev'] = array('widget' => array('type' => 'image',
																'src' => $GLOBALS['phpgw']->common->image('filemanager','left-grey'),
																'title' => lang('previsous'),
																'name'	=> 'params[prev]',
																'value'	=> 'prev'));
				}
				if($page<$pages)
				{
					$data['img_next'] = array('widget' => array('type' => 'image',
																'src' => $GLOBALS['phpgw']->common->image('filemanager','right-grey'),
																'title' => lang('next'),
																'name'	=> 'params[next]',
																'value'	=> 'next'));
					$data['img_last'] = array('widget' => array('type' => 'image',
																'src' => $GLOBALS['phpgw']->common->image('filemanager','last-grey'),
																'title' => lang('last'),
																'name'	=> 'params[last]',
																'value'	=> 'last'));
				}
				if($pages>1)
				{
					$data['page'] = array('widget' => array('type' => 'label',
														 'caption'	=> lang('Page').$page.'/'.$pages));
				}
			}
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('history' => $data));
		}

		function view_file($file_array='')
		{
			if(is_array($file_array))
			{
				$this->bofilemanager->path = $file_array['path'];
				$this->bofilemanager->file = $file_array['file'];
			}
			$file = "{$this->bofilemanager->path}/{$this->bofilemanager->file}";
			if($GLOBALS['phpgw']->vfs->file_exists(array('string' => $file,'relatives' => Array(RELATIVE_NONE))))
			{
				$ls_array = $GLOBALS['phpgw']->vfs->ls(array('string' => $file,'relatives' => array (RELATIVE_ALL),'checksubdirs' => False,'nofiles' => True));
				$mime_type = $ls_array[0]['mime_type'];
				/*$mime_part = explode('/',$mime_type);
				$mime_type = ($mime_part=='text'?'application/octet-stream':$mime_type);
				echo $mime_type;
				exit;*/

				$browser = CreateObject('phpgwapi.browser');
				$browser->content_header($this->bofilemanager->file,$mime_type,$ls_array[0]['size']);

				echo $GLOBALS['phpgw']->vfs->read(array('string' => $file,'relatives' => Array(RELATIVE_NONE)));
				flush();
			}
			if(!is_array($file_array))
			{
				exit();
			}
		}

		function preferences()
		{
			/*
	   		To add an on/off preference, just add it here.  Key is internal name, value is displayed name
			*/
			$other_checkboxes = array
			(
				'viewinnewwin'		=> lang('View documents in new window'),
				'viewonserver'		=> lang('View documents on server (if available)'),
				'viewtextplain'		=> lang('Unknown MIME-type defaults to text/plain when viewing'),
				'dotdot'			=> lang('Show ..'),
				'dotfiles'			=> lang('Show dotfiles'),
				'show_help'			=> lang('Show help'),
				'show_command_line'	=> lang('Show command line (EXPERIMENTAL. DANGEROUS)')
			);

			if ($_POST['save'])
			{
				$values = $_POST['values'];

				//_debug_array($_POST);
		
				if(is_array($values))
				{
					$GLOBALS['phpgw']->preferences->read();
					$GLOBALS['phpgw']->preferences->delete('filemanager','');

					foreach($values as $key => $value)
					{
						$GLOBALS['phpgw']->preferences->change('filemanager',$key,($key == 'show_upload_boxes')?$value:$key);
					}
					$GLOBALS['phpgw']->preferences->save_repository();
				}
				$GLOBALS['phpgw']->redirect_link('/preferences/index.php');
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			if ($_POST['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/preferences/index.php');
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			//$GLOBALS['phpgw_info']['flags']['app_header'] = lang('filemanager') . ': ' . lang('preferences');

			$data = array();
			$table_head = array('table_col' => array('colspan' => '2','style' => 'font-weight: bold','widget' => array('type' => 'label','caption' => lang('display attributes'))));

			foreach($this->bofilemanager->file_attributes as $internal => $title)
			{
				$var = array();
				$var[] = array('width' => '90%','widget' => array('type' => 'label','caption' => $title));
				$var[] = array('widget' => array('type' => 'checkbox','name' => 'values[' . $internal . ']','value' => 'True','checked' => $GLOBALS['phpgw_info']['user']['preferences']['filemanager'][$internal]?True:False));
				$table_rows[] = array('table_col' => $var);
			}

			$var = array();
			$var[] = array('class' => 'th','width' => '90%','style' => 'font-weight: bold','widget' => array('type' => 'label','caption' => lang('other settings')));
			$var[] = array('class' => 'th','widget' => array('type' => 'empty'));
			$table_rows[] = array('table_col' => $var);

			reset ($other_checkboxes);
			foreach($other_checkboxes as $internal => $title)
			{
				$var = array();
				$var[] = array('width' => '90%','widget' => array('type' => 'label','caption' => $title));
				$var[] = array('widget' => array('type' => 'checkbox','name' => 'values[' . $internal . ']','value' => 'True','checked' => $GLOBALS['phpgw_info']['user']['preferences']['filemanager'][$internal]?True:False));
				$table_rows[] = array('table_col' => $var);
			}

			foreach($this->bofilemanager->upload_boxes as $internal)
			{
				$options[] = array('option' => array('value' => $internal,'caption' => $internal,'selected' => $GLOBALS['phpgw_info']['user']['preferences']['filemanager']['show_upload_boxes']==$internal?True:False));
			}
			$var = array();
			$var[] = array('width' => '90%','widget' => array('type' => 'label','caption' => lang('Default number of upload fields to show')));
			$var[] = array('widget' => array('type' => 'select','name' => 'values[show_upload_boxes]','options' => $options));
			$table_rows[] = array('table_col' => $var);
			$table_footer = array('table_col' => $this->display_buttons());

			$data = array
			(
				'error'			=> '',
				'action_url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'filemanager.uifilemanager.preferences')),
				'table'			=> array('width' => '50%','table_head' => $table_head ,'table_row' => $table_rows,'table_footer' => $table_footer)
			);
			//_debug_array($data);
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('config' => $data));
		}

		function admin()
		{
			$val	= $_POST['set_quota'];
			$values = $_POST['values'];

			// Check if the Values match the following pattern 0,1,10,unlimited

			if ($_POST['save'])
			{
				if (empty($val) || ereg("^[ 0-9]+(,[ 0-9]+)*$",$val))
				{
					$this->config->value('set_quota', '0,' . $val . ',unlimited');
				}
				else
				{
					$error[] = lang('erroneous input! check quota value!');
				}

				if ($values['script_path'])
				{
					$doc_root = get_var('DOCUMENT_ROOT',Array('GLOBAL','SERVER'));
					if (substr($values['script_path'],0,strlen($doc_root)) == $doc_root)
					{
						$error[] = lang('the directory to store additional action scripts must be outside of the webservers documentroot');
					}
					else
					{
						$this->config->value('script_path',$values['script_path']);
					}
				}
				$this->config->value('check_files',$values['check_files']);
				$this->config->value('check_interval',$values['check_interval']);
				$this->config->save_repository();

				if(!is_array($error))
				{
					$GLOBALS['phpgw']->redirect_link('/admin/index.php');
				}
			}
			elseif($_POST['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/admin/index.php');
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('filemanager') . ': ' . lang('admin');

			if (is_array($this->config_items))
			{
				$quota = $this->config_items['set_quota'];
			}

			if (!$quota)
			{
				$str .= '1,10';	
			}
			else
			{
				$quota_str = '';
				$tok = strtok ($quota,',');

				if ($tok=='0')
				{
					$tok = strtok (',');
				}
				while ($tok)
				{
					if (strstr($tok,'unlimited'))
					{
				   		$tok = strtok (',');
					}
					else
					{
						if ($quota_str=='')
						{
							$quota_str .= $tok;
						}
						else
						{
							$quota_str .= ','.$tok;
						}
				   		$tok = strtok (',');
					}
				}
				$str .= $quota_str;
			}

			$sstr = '0,';
			$estr .= ',' . lang('unlimited');

			$data = array();
			$table_head = array('table_col' => array('colspan' => '2','style' => 'font-weight: bold','widget' => array('type' => 'label','caption' => lang('edit quota'))));

			$var = array();
			$var[] = array('widget' => array('type' => 'label','width' => '80%','caption' => lang('Enter comma separated Quota value in MB')));
			$var[] = array('widget' => array('type' => 'text','name' => 'set_quota','value' => $str,'caption_start' => $sstr,'caption' => $estr));
			$table_rows[] = array('table_col' => $var);

			$var = array();
			$var[] = array('style' => 'font-weight: bold','colspan' => '2','widget' => array('type' => 'label','caption' => lang('user menu action scripts')));
			$table_rows[] = array('table_col' => $var);

			$var = array();
			$var[] = array('widget' => array('type' => 'label','caption' => lang('absolute path to directory for storing additional action scripts')));
			$var[] = array('widget' => array('type' => 'text','name' => 'values[script_path]','value' => $this->config_items['script_path']));
			$table_rows[] = array('table_col' => $var);

			$var = array();
			$var[] = array('style' => 'font-weight: bold','colspan' => '2','widget' => array('type' => 'label','caption' => lang('periodically check for new files')));
			$table_rows[] = array('table_col' => $var);

			$var = array();
			$var[] = array('widget' => array('type' => 'label','caption' => lang('enable periodically check for new files')));
			$var[] = array('widget' => array('type' => 'checkbox','name' => 'values[check_files]','value' => 'True', 'checked' => ($this->config_items['check_files']?True:False)));
			$table_rows[] = array('table_col' => $var);

			$var = array();
			$var[] = array('widget' => array('type' => 'label','caption' => lang('interval')));
			$var[] = array('widget' => array('type' => 'text','name' => 'values[check_interval]','value' => $this->config_items['check_interval'],'size' => 3,'caption' => lang('minutes')));
			$table_rows[] = array('table_col' => $var);

			$table_footer = array('table_col' => $this->display_buttons());

			if(is_array($error))
			{
				$errormsg = explode('<br />',$error);
			}

			$data = array
			(
				'error'			=> $errormsg,
				'action_url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'filemanager.uifilemanager.admin')),
				'table'			=> array('width' => '50%','table_head' => $table_head ,'table_row' => $table_rows,'table_footer' => $table_footer)
			);
			//_debug_array($data);
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('config' => $data));
		}

		function edit_actions()
		{
			if($this->config_items['user_scripts'])
			{
				$user_scripts = $this->config_items['user_scripts'];
				reset($user_scripts);
				//_debug_array($user_scripts);
			}

			if($_POST['save'])
			{
				$values = $_POST['values'];

				//_debug_array($values);
				//_debug_array($this->config->config_data);

				$this->config->value('menu_disabled',$values['menu_disabled']);

				if(strlen($values['scriptname'])>0 && strlen($values['scripttitle'])>0)
				{
					if(is_array($user_scripts))
					{
						$count = count($user_scripts);
						$user_scripts[$count] = array
						(
							'name'	=> $values['scriptname'],
							'title'	=> $values['scripttitle']
						);
					}
					else
					{
						$user_scripts[0] = array
						(
							'name'	=> $values['scriptname'],
							'title'	=> $values['scripttitle']
						);
					}
				}
				$this->config->value('user_scripts',$user_scripts);
				$this->config->save_repository();
				unset($this->config_items);
			}
			elseif($_GET['delete_script'])
			{
				$script_nr = intval($_GET['script']);
				unset($user_scripts[$script_nr]);
				reset($user_scripts);
				$i = 0;
				foreach($user_scripts as $key => $uscript)
				{
					$nscript[$i] = $uscript;
					++$i;
				}
				//_debug_array($nscript);
				$this->config->value('user_scripts',$nscript);
				$this->config->save_repository();
				unset($this->config_items);
			}

			if ($_POST['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/admin/index.php');
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('filemanager') . ': ' . lang('admin');

			if($_POST['save'] || $_GET['delete_script'])
			{
				$this->config->read();
				if ($this->config->config_data)
				{
					$this->config_items	= $this->config->config_data;
				}
			}

			$disabled = $this->config_items['menu_disabled'];
			$uscripts = $this->config_items['user_scripts'];

			//_debug_array($disabled);

			if(!is_array($disabled))
			{
				$disabled = array();
			}
			if(!is_array($uscripts))
			{
				$uscripts = array();
			}

			$data = array();
			$table_head = array('table_col' => array('colspan' => '3','style' => 'font-weight: bold','widget' => array('type' => 'label','caption' => lang('deactivate menu actions'))));

			$actions = array
			(
				'edit'			=> lang('Edit'),
				'rename'		=> lang('Rename'),
				'delete'		=> lang('Delete'),
				'edit_comments'	=> lang('Edit comments')
			);

			foreach($actions as $action => $trans)
			{
				$checked = False;
				if(in_array($action,$disabled))
				{
					$checked = True;
				}
				$var = array();
				$var[] = array('colspan' => '2','width' => '90%','widget' => array('type' => 'label','caption' => $trans));
				$var[] = array('align' => 'center','widget' => array('type' => 'checkbox','name' => 'values[menu_disabled][]','value' => $action, 'checked' => $checked));
				$table_rows[] = array('table_col' => $var);
			}

			$var = array();
			$var[] = array('class' => 'th','style' => 'font-weight: bold','colspan' => '3','widget' => array('type' => 'label','caption' => lang('additional menu actions')));
			$table_rows[] = array('table_col' => $var);

			$var = array();
			$var[] = array('widget' => array('type' => 'label','caption' => lang('script')));
			$var[] = array('widget' => array('type' => 'label','caption' => lang('user menu action')));
			$var[] = array('widget' => array('type' => 'empty'));
			$table_rows[] = array('table_col' => $var);

			reset($uscripts);
			$i = 0;
			foreach($uscripts as $uscript)
			{
				$delete_link = array('widget' => array( 'type'	=> 'image',
														'src'	=> $GLOBALS['phpgw']->common->image('phpgwapi','delete'),
														'link'	=>  $GLOBALS['phpgw']->link('/index.php','menuaction=filemanager.uifilemanager.edit_actions&delete_script=1&script=' . $i)));
				$var = array();
				$var[] = array('widget' => array('type' => 'label','caption' => $uscript['name']));
				$var[] = array('widget' => array('type' => 'label','caption' => $uscript['title']));
				$var[] = $delete_link;
				$table_rows[] = array('table_col' => $var);
				++$i;
			}

			$scripts = $this->bofilemanager->get_dirfiles($items['script_path']);
			$options[] = array('option' => array('value' => '','caption' => lang('select script')));
			if(is_array($scripts))
			{
				foreach($scripts as $null => $script)
				{
					$options[] = array('option' => array('value' => $script,'caption' => $script));
				}
			}

			$var = array();
			$var[] = array('widget' => array('type' => 'text','name' => 'values[scripttitle]'));
			$var[] = array('widget' => array('type' => 'select','name' => 'values[scriptname]','options' => $options));
			$var[] = array('widget' => array('type' => 'empty'));
			$table_rows[] = array('table_col' => $var);

			$table_footer = array('table_col' => $this->display_buttons('actions'));

			if(is_array($error))
			{
				$errormsg = explode('<br />',$error);
			}

			$data = array
			(
				'error'			=> $errormsg,
				'action_url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'filemanager.uifilemanager.edit_actions')),
				'table'			=> array('width' => '50%','table_head' => $table_head ,'table_row' => $table_rows,'table_footer' => $table_footer)
			);
			//_debug_array($data);
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('config' => $data));
		}
	}
?>
