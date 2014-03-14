<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage entity
 	* @version $Id: class.cat_hooks.inc.php 11483 2013-11-24 19:54:40Z sigurdne $
	*/

	/**
	* hook management for categories
	* @package property
	*/
	class manual_cat_hooks
	{
		private $basedir;
		private $fakebase;

		function __construct()
		{
			$fakebase = '/manual';
			$this->vfs     = CreateObject('phpgwapi.vfs');
			$this->basedir = $this->vfs->basedir;
			$this->fakebase = $fakebase;
			$this->vfs->fakebase = $this->fakebase;

			if(!$this->vfs->file_exists(array(
				'string' => $this->fakebase,
				'relatives' => array(RELATIVE_NONE)
			)))
			{
				if (!$this->create_document_dir($this->fakebase))
				{
					throw new Exception("unable to create {$this->fakebase}");
				}
			}
		}


		/**
		 * Handle a new category being added, create location to hold ACL-data
		 */
		function cat_add($data)
		{
			if ( isset($data['cat_owner']) && $data['cat_owner'] != -1 )
			{
				return false; //nothing needed to be done, we only care about global cats
			}
			if($data['location_id'])
			{
				$dir = "{$this->fakebase}/{$data['cat_id']}";
				if (!$this->create_document_dir($dir))
				{
					throw new Exception("unable to create {$this->fakebase}");
				}
			}
		}

		/**
		 * Handle a category being deleted, remove the location 
		 */
		function cat_delete($data)
		{
			if ( isset($data['cat_owner']) && $data['cat_owner'] != -1 )
			{
				return false; //nothing needed to be done, we only care about global cats
			}
			if($data['location_id'])
			{
				$dir = "{$this->fakebase}/{$data['cat_id']}";

				$this->vfs->override_acl = 1;
				if(!$this->vfs->rm(array(
					'string' => $dir,
					'relatives' => array(
						RELATIVE_NONE
					)
				)))
				{
					$message = lang('failed to remove directory') . ' :'. $dir;
					phpgwapi_cache::message_set($message, 'error');
				}
				else
				{
					$message = lang('directory deleted') . ' :'. $dir;
					phpgwapi_cache::message_set($message, 'message');
				}
				$this->vfs->override_acl = 0;
			}
		}

		/**
		 * Handle a category being edited, update the location info
		 */
		function cat_edit($data)
		{
			if ( isset($data['cat_owner']) && $data['cat_owner'] != -1 )
			{
				return false; //nothing needed to be done, we only care about global cats
			}

//_debug_array($data);die();
			if($data['location_id'])
			{
				$dir = "{$this->fakebase}/{$data['cat_id']}";
				if(!$this->vfs->file_exists(array(
					'string' => $dir,
					'relatives' => array(RELATIVE_NONE)
				)))
				{
					if (!$this->create_document_dir($dir))
					{
						throw new Exception("unable to create {$dir}");
					}
				}
			}
		}

		/**
		 * Create catalog - starting with fakebase
		 *
		 * @param string $type part of path pointing to end target
		 *
		 * @return bool true on success.
		 */

		private function create_document_dir($dir)
		{
			$ok = false;
			$this->vfs->override_acl = 1;
			if(!$this->vfs->mkdir(array(
				'string' => $dir,
				'relatives' => array(
					RELATIVE_NONE
				)
			)))
			{
				$message = lang('failed to create directory') . ' :'. $dir;
				phpgwapi_cache::message_set($message, 'error');
			}
			else
			{
				$message = lang('directory created') . ' :'. $dir;
				phpgwapi_cache::message_set($message, 'message');
				$ok = true;
			}
			$this->vfs->override_acl = 0;
			return $ok;
		}
	}
