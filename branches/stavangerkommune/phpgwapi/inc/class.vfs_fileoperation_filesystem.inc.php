<?php
	/**
	* Fileoperation
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2014 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License v2 or later
	* @package phpgwapi
	* @subpackage vfs
	* @version $Id: class.vfs_fileoperation_filesystem.inc.php 11651 2014-02-02 17:03:26Z sigurdne $
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

	class phpgwapi_vfs_fileoperation_filesystem
	{
		public function __construct()
		{

		}

		public function filesize($path_parts)
		{
			$path = $path_parts->real_full_path;
			return filesize($path);
		}

		public function read($path_parts)
		{
			$path = $path_parts->real_full_path;
			$filesize = filesize($path);
			$contents = false;
			if( $filesize  > 0 && $fp = fopen($path, 'rb'))
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


		public function write($path_parts, $content)
		{
			$write_ok = false;
			if($fp = fopen($path_parts->real_full_path, 'wb'))
			{
				fwrite($fp, $content, strlen($content));
				fclose($fp);
				$write_ok = true;
			}
			return $write_ok;
		}


		public function touch($path_parts)
		{
			return @touch($path_parts->real_full_path);
		}

		public function rename($from, $to)
		{
			return rename($from->real_full_path, $to->real_full_path);
		}

		/*
		*Deletes a file
		*/
		public function unlink($path_parts)
		{
			return unlink($path_parts->real_full_path);
		}

		/*
		*Removes directory
		*/
		public function rmdir($path_parts)
		{
			return rmdir($path_parts->real_full_path);
		}

		public function check_target_directory($path_parts)
		{
			return file_exists($path_parts->real_leading_dirs);
		}

		public function auto_create_home($basedir)
		{
			if(!file_exists($basedir.'/home'))
 			{
  				@mkdir($basedir.'/home', 0770);
 			}
		}

		public function file_exists($path_parts)
		{
			return file_exists($path_parts->real_full_path);
		}

		public function is_dir($path_parts)
		{
			return is_dir($path_parts->real_full_path);
		}

		public function mkdir($path_parts)
		{
			return mkdir($path_parts->real_full_path, 0770);
		}



	}
