<?php
	/**
	* phpGroupWare - CATCH: An application for importing data from handhelds into property.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2009 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package catch
	* @subpackage catch
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

	/**
	 * Description
	 * @package demo
	 */

	class catch_uicatch
	{
		/**
		* @var ??? $grants ???
		*/
		private $grants;

		/**
		* @var ??? $start ???
		*/
		private $start;

		/**
		* @var ??? $query ???
		*/
		private $query;

		/**
		* @var ??? $sort ???
		*/
		private $sort;

		/**
		* @var ??? $order ???
		*/
		private $order;

		/**
		* @var object $cats categories object
		*/
		private $cats;

		/**
		* @var object $nextmatches paging handler
		*/
		private $nextmatches;

		/**
		* @var int $account reference to the current user id
		*/
		private $account;

		/**
		* @var object $bo business logic
		*/
		private $bo;

		/**
		* @var object $acl reference to global access control list manager
		*/
		private $acl;

		/**
		* @var string $acl_location the access control location
		*/
		private $acl_location;

		/**
		* @var bool $acl_read does the current user have read access to the current location
		*/
		private $acl_read;

		/**
		* @var bool $acl_add does the current user have add access to the current location
		*/
		private $acl_add;

		/**
		* @var bool $acl_edit does the current user have edit access to the current location
		*/
		private $acl_edit;

		/**
		* @var bool $allrows display all rows of result set?
		*/
		private $allrows;

		/**
		* @var int $cat_id the currently selected category
		*/
		private $cat_id;

		/**
		* @var bool $filter the current filter
		*/
		private $filter;

		/**
		* @var array $public_functions publicly available methods of the class
		*/
		public $public_functions = array
		(
			'index' 	=> true,
		);

		public function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->cats				= CreateObject('phpgwapi.categories');
			$this->nextmatches		= CreateObject('phpgwapi.nextmatchs');
			$this->account			=& $GLOBALS['phpgw_info']['user']['account_id'];
	//		$this->bo				= CreateObject('catch.bocatch',true);
			$this->acl 				=& $GLOBALS['phpgw']->acl;
	//		$this->acl_location 	= $this->bo->get_acl_location();
			$this->acl_read 		= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'demo');
			$this->acl_add 			= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'demo');
			$this->acl_edit 		= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'demo');
			$this->acl_delete 		= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'demo');

			$this->start			= $this->bo->start;
			$this->query			= $this->bo->query;
			$this->sort				= $this->bo->sort;
			$this->order			= $this->bo->order;
			$this->allrows			= $this->bo->allrows;
			$this->cat_id			= $this->bo->cat_id;
			$this->filter			= $this->bo->filter;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'catch';
		}

		private function save_sessiondata()
		{
			$data = array
			(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
			);
			$this->bo->save_sessiondata($data);
		}

		public function index()
		{
			$output	= self::get_output();

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::{$output}";

			if(!$this->acl_read)
			{
	//			$this->no_access();
	//			return;
			}
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false; // not really
			$GLOBALS['phpgw']->common->phpgw_header(true);
			echo '<b>Catch and release...Place holder for links to for various reports</b>';
		}

		/**
		* Get the output format
		*
		* @return string the output format - html, wml etc
		*/
		private static function get_output()
		{
			$output = phpgw::get_var('output', 'string', 'REQUEST', 'html');
			$GLOBALS['phpgw']->xslttpl->set_output($output);
			return $output;
		}
	}
