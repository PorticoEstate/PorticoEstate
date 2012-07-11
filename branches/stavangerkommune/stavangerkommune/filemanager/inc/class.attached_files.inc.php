<?php
	/***
	* Filemanager - Attached files
	*
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @author Lars Piepho <lpiepho@probusiness.de>
	* @copyright Copyright (C) 2005-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package filemanager
	* @version $Id$
	* @internal $Source$
	*/

	/**
	 * Filemanager attached files
	 * 
	 * @package filemanager
	 */
	class attached_files
	{
		var $public_functions = array
		(
			'show_file'		=> True,
			'save_file' 	=> True,
			'delete_file'	=> True,
			'get_file'		=> True
		);

		var $form_name;
		var $file;
		var $action_id;
		var $app;
		var $appdir;

		function attached_files()
		{
			$this->app			= (isset($_REQUEST['app'])?$_REQUEST['app']:$GLOBALS['phpgw_info']['flags']['currentapp']);
			$this->appdir		= $GLOBALS['phpgw_info']['server']['files_dir'] . '/' . $this->app;

			$this->form_name	= 'attachment';

			$this->file			= (isset($_REQUEST['file'])?$_REQUEST['file']:'');
			$this->action_id	= (isset($_REQUEST['action_id'])?$_REQUEST['action_id']:'');

			if (@!is_object($GLOBALS['phpgw']->vfs))
			{
				$GLOBALS['phpgw']->vfs = CreateObject ('phpgwapi.vfs');
			}
			$GLOBALS['phpgw']->vfs->override_acl = True;

			//Check if home/groupdirectory exists. If not, we create it
			if (!file_exists($this->appdir))
			{
				$GLOBALS['phpgw']->vfs->mkdir(array
				(
					'string'	=> $this->appdir,
					'relatives'	=> array (RELATIVE_ALL)
				));
			}
		}

		function show_file()
		{
			$ls_array = $GLOBALS['phpgw']->vfs->ls(array
			(
				'string'		=> $this->file,
				'relatives'		=> array (RELATIVE_ALL),
				'checksubdirs'	=> False,
				'nofiles'		=> True
			));

			if ($ls_array[0]['mime_type'])
			{
				$mime_type = $ls_array[0]['mime_type'];
			}
			elseif ($GLOBALS['settings']['viewtextplain'])
			{
				$mime_type = 'application/octet-stream;';
			}
			$filename = basename($this->file);
			header('Content-type: ' . $mime_type);
			header('Content-Disposition: attachment; filename=' . $filename);
			echo $GLOBALS['phpgw']->vfs->read(array
				(
					'string'	=> $this->file,
					'relatives'	=> array(RELATIVE_NONE)
				));
			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		function save_file($values = 0)
		{
			$action_id	= $values['action_id'];
			$comment	= (isset($values['comment']) && $values['comment'] ? $values['comment']:'');

			$GLOBALS['phpgw']->vfs->override_acl = 1;

			$attdir = $this->appdir . '/' . $action_id;

			if (!file_exists($attdir))
			{
				$GLOBALS['phpgw']->vfs->mkdir(array
				(
					'string' => $attdir,
					'relatives' => array (RELATIVE_ALL)
				));
			}

			if($GLOBALS['phpgw']->vfs->cp(array 
			(
				'from'		=> $_FILES[$this->form_name]['tmp_name'],
				'to'		=> $attdir . '/' . $_FILES[$this->form_name]['name'],
				'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL)
			)))
			{
				if(strlen($comment) > 0)
				{
					$GLOBALS['phpgw']->vfs->set_attributes(array(
					'string'		=> $_FILES[$this->form_name]['name'],
					'relatives'		=> array(RELATIVE_ALL),
					'attributes'	=> array(
						'size' => $_FILES[$this->form_name]['size'],
						'mime_type' => $_FILES[$this->form_name]['type'],
						'deleteable' => 'Y',
						'comment' => stripslashes($comment))));
				}
			}
			$GLOBALS['phpgw']->vfs->override_acl = 0;
		}

		function delete_file()
		{
			//$this->app			= $_GET['app'];
			//$this->appdir		= $GLOBALS['basedir'] . '/' . $this->app;

			if(!$this->file && file_exists($this->appdir . '/' . $this->action_id))
			{
				$GLOBALS['phpgw']->vfs->override_acl = 1;
				$GLOBALS['phpgw']->vfs->delete(array
				(
					'string'	=> $this->appdir . '/' . $this->action_id,
					'relatives'	=> array (RELATIVE_ALL)
				));
				$GLOBALS['phpgw']->vfs->override_acl = 0;
			}
			elseif($this->file)
			{
				$GLOBALS['phpgw']->vfs->override_acl = 1;
				$GLOBALS['phpgw']->vfs->rm(array
				(
					'string'	=> $this->appdir . '/' . $this->action_id . '/' . $this->file,
					'relatives' => array (RELATIVE_ALL)
				));
				$GLOBALS['phpgw']->vfs->override_acl = 0;

				Header('Location: ' . $_SERVER['HTTP_REFERER']);
				//$GLOBALS['phpgw']->redirect_link($referer);
			}
		}

		function get_files($action_id, $delete = False)
		{
			$att_link = '';
			$directory = '/' . $this->app . '/' . $action_id;

			$GLOBALS['phpgw']->db->query("SELECT name from phpgw_vfs where directory like '" . $directory . "' AND size > 0 AND mime_type NOT like 'journal-deleted'",__LINE__,__FILE__);

			while($GLOBALS['phpgw']->db->next_record() != '')
			{
				$attachment = $directory . '/' . $GLOBALS['phpgw']->db->f('name');

				$view_link = $GLOBALS['phpgw']->link('/index.php',array('menuaction'	=> 'filemanager.uiactions.show_file','file' => $attachment,'app' => $this->app));
				if($delete)
				{
					$del_link = $GLOBALS['phpgw']->link('/index.php',array
																	(
																		'menuaction'	=> 'filemanager.uiactions.delete_file',
																		'action_id'		=> $action_id,
																		'file'			=> basename($attachment),
																		'app'			=> $this->app));
					$del = '<a href="' . $del_link . '"><img src="' . $GLOBALS['phpgw']->common->image('phpgwapi','delete') . '" title="' . lang('delete') . '" border="0"></a>';
				}
				$att_link .= '<a href="' . $view_link . '" target="_blank">' . basename($attachment) . '</a>&nbsp&nbsp' . $del . '<br />';
			}
			return $att_link;
		}

		function file_exists($data)
		{
			return $GLOBALS['phpgw']->vfs->file_exists($data);
		}
	}
?>
