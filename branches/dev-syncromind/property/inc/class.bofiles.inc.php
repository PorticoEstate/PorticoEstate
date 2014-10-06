<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2007,2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	 * @package phpgroupware
	 * @subpackage property
	 * @category core
 	 * @version $Id$
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/*
	 * phpGroupWare::property file handler class
	 *
	 * @package phpgroupware
	 * @subpackage property
	 * @category core
	 */

	class property_bofiles
	{
		/**
		* @var string $fakebase Fake base directory.
		*/
		var $fakebase = '/property';
		var $rootdir;

		/**
		 * constructor
		 *
		 * @param string $fakebase fakebase
		 *
		 * @return
		 */

		function __construct($fakebase='/property')
		{
			$this->vfs     = CreateObject('phpgwapi.vfs');
			$this->rootdir = $this->vfs->basedir;
			if($fakebase)
			{
				$this->fakebase = $fakebase;
			}
			$this->vfs->fakebase = $this->fakebase;
		}

		/**
		 * Set the account id used for cron jobs where there is no user-session
		 *
		 * @param integer $account_id the account id to use - 0 = current user
		 *
		 * @return null
		 */
		public function set_account_id($account_id = 0)
		{
			if($account_id)
			{
				$this->vfs->working_id = $account_id;
			}
		}

		/**
		 * Create catalog - starting with fakebase
		 *
		 * @param string $type part of path pointing to end target
		 *
		 * @return array Array with result on the action(failed/success) for each catalog down the path
		 */

		function create_document_dir($type)
		{
			$receipt = array();

			if(!$this->vfs->file_exists(array(
				'string' => $this->fakebase,
				'relatives' => array(RELATIVE_NONE)
			)))
			{
				$this->vfs->override_acl = 1;
				if(!$this->vfs->mkdir(array(
					'string' => $this->fakebase,
					'relatives' => array(
						RELATIVE_NONE
					)
				)))
				{
					$receipt['error'][] = array('msg'=>lang('failed to create directory') . ' :'. $this->fakebase);
				}
				else
				{
					$receipt['message'][] = array('msg'=>lang('directory created') . ' :'. $this->fakebase);
				}
				$this->vfs->override_acl = 0;
			}

			$type_part = explode('/',$type);

			$catalog = '';
			foreach($type_part as $entry)
			{
				$catalog .= "/{$entry}";

				if(!$this->vfs->file_exists(array(
					'string' => "{$this->fakebase}{$catalog}",
					'relatives' => array(RELATIVE_NONE)
				)))
				{
					$this->vfs->override_acl = 1;
					if(!$this->vfs->mkdir(array(
						'string' => "{$this->fakebase}{$catalog}",
						'relatives' => array(
							RELATIVE_NONE
						)
					)))
					{
						$receipt['error'][] = array('msg'=>lang('failed to create directory') . ' :'. $this->fakebase . $catalog);
					}
					else
					{
						$receipt['message'][] = array('msg'=>lang('directory created') . ' :'. $this->fakebase . $catalog);
					}
					$this->vfs->override_acl = 0;
				}
			}
			//_debug_array($receipt);
			return $receipt;
		}

		/**
		 * Delete Files
		 *
		 * @param string $path   part of path where to look for files
		 * @param array  $values array holding information of selected files
		 *
		 * @return array Array with result on the action(failed/success) for each file
		 */

		function delete_file($path, $values)
		{
			$receipt = array();

			foreach ($values['file_action'] as $file_name)
			{
				$file_name = html_entity_decode($file_name);
				
				$file = "{$this->fakebase}{$path}{$file_name}";

				if($this->vfs->file_exists(array(
					'string' => $file,
					'relatives' => array(RELATIVE_NONE)
				)))
				{
					$this->vfs->override_acl = 1;

					if(!$this->vfs->rm(array(
						'string' => $file,
						'relatives' => array(
							RELATIVE_NONE
						)
					)))
					{
						$receipt['error'][] = array('msg'=>lang('failed to delete file') . ' :'. $this->fakebase . $path . $file_name);
					}
					else
					{
						$receipt['message'][] = array('msg'=>lang('file deleted') . ' :'. $this->fakebase . $path . $file_name);
					}
					$this->vfs->override_acl = 0;
				}
			}
			return $receipt;
		}

		/**
		 * View File - echo (or download) to browser.
		 *
		 * @param string $type part of path where to look for files
		 * @param string $file optional filename
		 *
		 * @return null
		 */

		function view_file($type = '', $file = '', $jasper = '')
		{
			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			if(!$file)
			{
				$file_name = html_entity_decode(urldecode(phpgw::get_var('file_name')));
				$id        = phpgw::get_var('id');
				$file      = "{$this->fakebase}/{$type}/{$id}/{$file_name}";
			}

			// prevent path traversal
			if ( preg_match('/\.\./', $file) )
			{
				return false;
			}

			if($this->vfs->file_exists(array(
				'string' => $file,
				'relatives' => array(RELATIVE_NONE)
			)))
			{
				$ls_array = $this->vfs->ls(array(
					'string'		=>  $file,
					'relatives' 	=> array(RELATIVE_NONE),
					'checksubdirs'	=> false,
					'nofiles'		=> true
				));

				if(!$jasper)
				{
					$this->vfs->override_acl = 1;

					$document = $this->vfs->read(array(
						'string' 	=> $file,
						'relatives' => array(RELATIVE_NONE)));

					$this->vfs->override_acl = 0;

					$browser = CreateObject('phpgwapi.browser');
					$browser->content_header($ls_array[0]['name'],$ls_array[0]['mime_type'],$ls_array[0]['size']);
					echo $document;
				}
				else //Execute the jasper report
				{
					$output_type = 'PDF';

					$report_source		= "{$this->rootdir}{$file}";
					$jasper_wrapper		= CreateObject('phpgwapi.jasper_wrapper');
					try
					{
						$jasper_wrapper->execute('', $output_type, $report_source);
					}
					catch(Exception $e)
					{
						$error = $e->getMessage();
						//FIXME Do something clever with the error
						echo "<H1>{$error}</H1>";
					}
				}
			}
		}

		/**
		 * Get attachments
		 *
		 * @param string $path   part of path where to look for files
		 * @param array  $values array holding information of selected files
		 *
		 * @return array Array with filecontent
		 */
		function get_attachments($path, $values)
		{
			$mime_magic = createObject('phpgwapi.mime_magic');
			$attachments = array();
			foreach ($values as $file_name)
			{
				$file = "{$this->fakebase}{$path}{$file_name}";

				if($this->vfs->file_exists(array(
					'string' => $file,
					'relatives' => array(RELATIVE_NONE))))
				{
					$mime       = $mime_magic->filename2mime($file_name);

					$attachments[] = array
						(
							'file' => "{$GLOBALS['phpgw_info']['server']['files_dir']}{$file}",
							'name' => $file_name,
							'type' => $mime
						);
				}
			}
			return $attachments;
		}
	}
