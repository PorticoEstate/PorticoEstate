<?php
	/**
	* Fileoperation
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2014 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License v2 or later
	* @package phpgwapi
	* @subpackage vfs
	* @version $Id: class.acl.inc.php 11567 2013-12-23 12:49:00Z sigurdne $
	*/

	/*
		This program is free software: you can redistribute it and/or modify
		it under the terms of the GNU Lesser General Public License as published by
		the Free Software Foundation, either version 2 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU Lesser General Public License for more details.

		You should have received a copy of the GNU Lesser General Public License
		along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	class phpgwapi_vfs_fileoperation_braArkiv
	{
		public function __construct()
		{

		}

		public function filesize($path_parts)
		{
			$path = $path_parts->real_full_path;
			return filesize($path);
		}

		public function fread($path_parts)
		{
			$path = $path_parts->real_full_path;
			$contents = null;
			if( $filesize = $this->filesize($path) > 0 && $fp = fopen($path, 'rb'))
			{
				$contents = fread($fp, $filesize);
				fclose ($fp);
			}
			return $contents;
		}

		//not relevant to braArkiv
		public function copy($from, $to)
		{
			return copy($from->real_full_path, $to->real_full_path);
		}

	}
