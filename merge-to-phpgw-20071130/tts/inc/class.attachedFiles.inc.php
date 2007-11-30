<?php
	/**
	* Trouble Ticket System - Attached files
	*
	* @author Lars Piepho <lpiepho@probusiness.de>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package tts
	* @version $Id: class.attachedFiles.inc.php 17413 2006-10-14 05:39:42Z skwashd $
	*/


	/* 
	 * FIXME This is crap and should be removed.  phpgwapi.browser handles a lot of it and the rest should be in botts
	 */
	

	/**
	* Attached files
	* 
	* @package tts
	*/	
	class attachedFiles
	{

		var $public_functions = array(
			'show_file' => True
		);
		var $file;
		var $vfs;

		function attachedFiles()
		{		
		 $this->file = $_REQUEST['file'];
		 		  
		 $GLOBALS['phpgw_info']['flags'] = array
		(
			'currentapp'	=> 'tts',
			'noheader'	=> True,
			'nofooter'	=> True,
			'enable_vfs_class'	=> True,
			'enable_browser_class'	=> True
		);
		
		$this->vfs = CreateObject('phpgwapi.vfs');
		
		}

		function show_file()
		{			
			$ls_array = $this->vfs->ls(array (
			'string'	=> $this->file,
			'relatives'	=> array (RELATIVE_ALL),
			'checksubdirs'	=> False,
			'nofiles'	=> True));

			if ($ls_array[0]['mime_type'])
			{
				$mime_type = $ls_array[0]['mime_type'];
			}
			elseif ($GLOBALS['settings']['viewtextplain'])
			{
				$mime_type = 'text/plain';
			}
			$filename = basename($this->file);
			header('Content-type: ' . $mime_type);
			header('Content-Disposition: attachment; filename=' . $filename);
			echo $this->vfs->read (array (
					'string'	=> $this->file,
					'relatives'	=> array (RELATIVE_NONE)));
			$GLOBALS['phpgw']->common->phpgw_exit ();
		}
		
	}
?>
