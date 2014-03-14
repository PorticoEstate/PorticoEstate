<?php
	/**
	* phpGroupWare - manual
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @subpackage admin
 	* @version $Id: class.bodocuments.inc.php 11483 2013-11-24 19:54:40Z sigurdne $
	*/

	/**
	 * Description
	 * @package manual
	 */

	class manual_bodocuments
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $location_info = array();
		var $appname;
		var $allrows;
		public $acl_location = '.documents';

		var $public_functions = array
		(
			'addfiles'		=> true
		);

		function __construct()
		{

		}

		public function addfiles()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;

			$acl 			= & $GLOBALS['phpgw']->acl;
			$acl_add 		= $acl->check($this->acl_location, PHPGW_ACL_ADD, 'manual');
			$acl_edit 		= $acl->check($this->acl_location, PHPGW_ACL_EDIT, 'manual');
			$cat_id			= phpgw::get_var('id', 'int');
			$check			= phpgw::get_var('check', 'bool');
			$fileuploader	= CreateObject('property.fileuploader');

			if(!$acl_add && !$acl_edit)
			{
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			if(!$cat_id)
			{
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$test = false;

			if ($test)
			{
				if (!empty($_FILES))
				{
					$tempFile = $_FILES['Filedata']['tmp_name'];
					$targetPath = "{$GLOBALS['phpgw_info']['server']['temp_dir']}/";
					$targetFile =  str_replace('//','/',$targetPath) . $_FILES['Filedata']['name'];
					move_uploaded_file($tempFile,$targetFile);
					echo str_replace($GLOBALS['phpgw_info']['server']['temp_dir'],'',$targetFile);
				}
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
	
			if($check)
			{
				$fileuploader->check($cat_id, '/manual');
			}
			else
			{
				$fileuploader->upload($cat_id, '/manual');
			}
		}

	}
