<?php
	/**
	* Project Manager - attached_files
	*
	* @author Lars Piepho [lpiepho@probusiness.de]
	* @copyright Copyright (C) 2000-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package projects
	* @version $Id: class.attached_files.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	* $Source: /sources/phpgroupware/projects/inc/class.attached_files.inc.php,v $
	*/

	class attached_files
	{

		var $public_functions = array
		(
			'show_file'		=> true,
			'save_file' 	=> true,
			'delete_file'	=> true,
			'get_file'		=> true
		);
		var $file;
		var $vfs;
		var $project_id;

		function attached_files()
		{
			$this->file			= isset( $_REQUEST['file'] ) ? $_REQUEST['file'] : '';
			$this->project_id	= isset( $_REQUEST['project_id'] ) ? $_REQUEST['project_id'] : '';
			$this->vfs			= CreateObject('phpgwapi.vfs');
			$this->session		= $GLOBALS['phpgw']->session;
			$this->vfs->override_acl = true;
		}

		function show_file()
		{
			$ls_array = $this->vfs->ls(array
			(
				'string'		=> $this->file,
				'relatives'		=> array (RELATIVE_ALL),
				'checksubdirs'	=> False,
				'nofiles'		=> true
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
			echo $this->vfs->read(array
				(
					'string'	=> $this->file,
					'relatives'	=> array(RELATIVE_NONE)
				));
			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		function save_file($project_id, $source = '', $destination = '', $details = false)
		{
			//Check if home/groupdirectory exists. If not, we create it
			$basedir = '/projects';
			if (!file_exists($basedir))
			{
				$this->vfs->override_acl = 1;
				$this->vfs->mkdir( array
				(
					'string'	=> $basedir,
					'relatives'	=> array ( RELATIVE_ALL )
				));
				$this->vfs->override_acl = 0;
			}

			$attdir = $basedir . '/' . $project_id;

			if (!file_exists($attdir))
			{
				$this->vfs->override_acl = 1;
				$this->vfs->mkdir(array
				(
					'string' => $attdir,
					'relatives' => array (RELATIVE_ALL)
				));
				$this->vfs->override_acl = 0;
			}

			if(!$source || !$destination)
			{
				$source = $_FILES['attachment']['tmp_name'];
				$destination = $_FILES['attachment']['name'];
			}

			$this->vfs->override_acl = 1;
			$this->vfs->cp(array
			(
				'from'		=> $source,
				'to'		=> $attdir . '/' . $destination,
				'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL)
			));
			$this->vfs->override_acl = 0;

			if($details)
			{
				$GLOBALS['phpgw']->db->query("UPDATE phpgw_vfs SET comment='" . $details['comment'] . "', owner_id=" . $details['owner_id'] . " where name like '" . $destination . "' AND size > 0 AND mime_type NOT like 'journal-deleted'",__LINE__,__FILE__);
			}
		}

		function delete_file($project_id = true)
		{
			$basedir = '/projects';

			if(!$this->file && file_exists($basedir . '/' . $project_id))
			{
				$this->vfs->override_acl = 1;
				$this->vfs->delete(array
				(
					'string'	=> $basedir . '/' . $project_id,
					'relatives'	=> array (RELATIVE_ALL)
				));
				$this->vfs->override_acl = 0;
			}
			elseif($this->file)
			{
				$this->vfs->override_acl = 1;
				$this->vfs->rm(array
				(
					'string'	=> $basedir . '/' . $this->project_id . '/' . $this->file,
					'relatives' => array (RELATIVE_ALL)
				));
				$this->vfs->override_acl = 0;
				$clickhistory = $this->session->get_click_path_entry(1);
				$GLOBALS['phpgw']->redirect_link('/index.php',array
															(
																'menuaction' => $clickhistory['menuaction'],
																'project_id' => $clickhistory['get']['project_id'],
																'deleted'    => true
															));
			}
		}

		function get_files($project_id, $delete = false, $details = false, $user_id = false)
		{
			$GLOBALS['phpgw']->db->query("SELECT name,owner_id,comment from phpgw_vfs where directory like '/projects/" . $project_id . "' AND size > 0 AND mime_type NOT like 'journal-deleted'",__LINE__,__FILE__);
			$x = 0;

			if($user_id)
			{
				$user = $user_id;
			}
			else
			{
				$user = $GLOBALS['phpgw_info']['user']['account_id'];
			}

			while ($GLOBALS['phpgw']->db->next_record() != '')
			{
				$attachment[$x] =  '/projects/' . $project_id . '/' . $GLOBALS['phpgw']->db->f('name');
				//++$x;
				$owner[$x] = $GLOBALS['phpgw']->db->f('owner_id');
				$comment[$x] = $GLOBALS['phpgw']->db->f('comment');
			/*}

			for($i=0; $i<=$x-1; $i++)
			{*/
				$file = $GLOBALS['phpgw']->link('/index.php',array
															(
																'menuaction'	=> 'projects.attached_files.show_file',
																'file'			=> $attachment[$x]
															));
				/*
				if($details)
				{
					$details = '&nbsp;&nbsp;|&nbsp;&nbsp;' . $comment[$x];
				}
				*/
				if($delete)
				{
					$delFile = basename($attachment[$x]);
					$delLink = $GLOBALS['phpgw']->link('/index.php',array
																	(
																		'menuaction'	=> 'projects.attached_files.delete_file',
																		'project_id'	=> $project_id,
																		'file'			=> $delFile
																	));
					$del = '<a href="' . $delLink . '"><img src="' . $GLOBALS['phpgw']->common->image('phpgwapi','delete') . '" title="' . lang('delete') . '" border="0"></a>';
				}
				$attLink = '<a href="' . $file . '" target="_blank">' . basename($attachment[$x]) . '</a><br />';

				if($owner[$x]==$user)
				{
					$files[$x] = array(
										'link' => $attLink,
										'comment' => $comment[$x],
										'delLink' => $del);

					++$x;
				}
			}

			return ($files);
		}

		function file_exists($data)
		{
			return $this->vfs->file_exists($data);
		}
	}
?>
