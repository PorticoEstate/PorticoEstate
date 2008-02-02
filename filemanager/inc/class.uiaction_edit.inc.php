<?php
	/***
	* phpGroupWare Filemanager
	* @author Jonathon Sim <sim@zeald.com>
	* @author Bettina Gille <ceb@phpgroupware.org>
	* @copyright Copyright (C) 2000-2006 Free Software Foundation, Inc http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package filemanager
	* @version $Id$
	*/

	/**
	 * Define UIEDIT_DEBUG
	 */
	define('UIEDIT_DEBUG',0);

	/**
	 * Filemanager GUI edit action class
	 * 
	 * @package filemanager
	 */
	class uiaction_edit
	{
		// Lists the suported actions (human readable) indexed by their function name
		var $public_functions = array
		(
			'edit' => True
		);

		function uiaction_edit()
		{
			$this->action			= CreateObject('filemanager.uiaction_base');
			$this->bofilemanager	= $this->action->bofilemanager;
			$GLOBALS['phpgw']->xslttpl->add_file('edit');
		}

		function edit()
		{
			$edit_file = get_var('edit_file',array('GET','POST'));

			if (!strlen($edit_file))
			{
				$edit_file = $this->bofilemanager->fileman[0];
			}

			//_debug_array($this->bofilemanager->fileman);

			if(isset($_POST['cancel']) && $_POST['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction' => 'filemanager'.'.ui'.'filemanager'.'.index',
																	'path' => urlencode($this->bofilemanager->path)));
			}

			$data = array
			(
				'img_up'	=> array('widget' => array('type' => 'image',
											'src' => $GLOBALS['phpgw']->common->image('filemanager','up'),
											'title' => lang('up'),
											'link' => $GLOBALS['phpgw']->link('/index.php',Array(
													'menuaction'	=> 'filemanager'.'.ui'.'filemanager'.'.index',
													'path'		=> urlencode($this->bofilemanager->lesspath))))),
				'help_up'	=> array('widget' => array('type' => 'help','onClick' => $this->bofilemanager->build_help('up'))),
				'img_home'	=> array('widget' => array('type' => 'image',
											'src' => $GLOBALS['phpgw']->common->image('filemanager','folder_large'),
											'title' => lang('go to your home directory'),
											'link' => $GLOBALS['phpgw']->link('/index.php',Array(
													'menuaction'	=> 'filemanager'.'.ui'.'filemanager'.'.index',
													'path' => urlencode($this->bofilemanager->homedir))))),
				'help_home'		=> array('widget' => array('type' => 'help','onClick' => $this->bofilemanager->build_help('home'))),
				'current_dir'	=> $this->bofilemanager->path
			);

			if (get_var('edited',array('GET', 'POST')))
			{
				$content = get_var('edit_file_content', array('GET', 'POST'));
				if (get_magic_quotes_gpc()) //a thousand curses!
				{
					$content = stripslashes($content);
				}
			}
			else
			{
				$content = $GLOBALS['phpgw']->vfs->read (array ('string' => $edit_file));
			}

			if(isset($_POST['preview']))
			{
			$vars[]	= array('widget' => array('type' => 'image',
												'src' => $GLOBALS['phpgw']->common->image('filemanager','pencil'),
												'title' => lang('edit'),
												'name'	=> 'edit',
												'value'	=> 'edit'));
			}
			else
			{
				$vars[]	= array('widget' => array('type' => 'image',
												'src' => $GLOBALS['phpgw']->common->image('filemanager','preview'),
												'title' => lang('preview'),
												'name'	=> 'preview',
												'value'	=> 'preview'));
			}

			$vars[]	= array('widget' => array('type' => 'image',
												'src' => $GLOBALS['phpgw']->common->image('filemanager','filesave'),
												'title' => lang('save'),
												'name'	=> 'save',
												'value'	=> 'save'));
			$vars[]	= array('widget' => array('type' => 'image',
												'src' => $GLOBALS['phpgw']->common->image('filemanager','button_cancel'),
												'title' => lang('close'),
												'name'	=> 'cancel',
												'value'	=> 'cancel'));
			$data['nav_data'] = $vars;
			$data['lang_edit'] = isset($_POST['preview'])?lang('preview for'):lang('edit file');
			$data['filename'] = $edit_file;

			$vars = array();
			if(isset($_POST['preview']))
			{
				$vars['preview'] =  nl2br($content);
				$v[] = array('widget' => array('type' => 'hidden','name'=> 'edit_file_content','value'=> $content));
			}
			elseif (isset($_POST['save']))
			{
				if ($GLOBALS['phpgw']->vfs->write(array('string' => $this->bofilemanager->path.'/'.$edit_file ,'relatives' => array(RELATIVE_NONE),'content' => $content)))
				{
					$vars['output'] = lang('Saved %1',$this->bofilemanager->path.'/'.$edit_file);
				}
				else
				{
					$vars['output'] = lang('Could not save %1',$this->bofilemanager->path.'/'.$edit_file);
				}
			}

			if ($edit_file && $GLOBALS['phpgw']->vfs->file_exists (array('string' => $edit_file,'relatives'	=> array (RELATIVE_ALL))))
			{
					$v[] = array('widget' => array('type' => 'hidden','name'=> 'edited','value'=> 1));
					$v[] = array('widget' => array('type' => 'hidden','name' => 'edit_file','value' => $edit_file));
					$v[] = array('widget' => array('type'=> 'hidden','name'=> 'fileman[0]','value' => $this->bofilemanager->html_encode($edit_file,1)));

					$vars['form_data']		= $v;
					$vars['file_content']	=  $content;
			}

			$output = array
			(
				'form_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'filemanager'.'.uiaction_edit.edit',
																					'path' => urlencode($this->bofilemanager->path),'edit_file' => $edit_file)),
				'filemanager_nav'	=> $data,
				'filemanager_edit'	=> $vars
			);

			//_debug_array($output);
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('edit' => $output));
		}
	}
?>
