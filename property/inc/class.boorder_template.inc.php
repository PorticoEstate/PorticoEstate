<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2018 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @subpackage helpdesk
	 * @version $Id$
	 */
	phpgw::import_class('phpgwapi.bocommon');

	/**
	 * Description
	 * @package property
	 */
	class property_boorder_template  extends phpgwapi_bocommon
	{

		var $so, $historylog, $config, $bocommon, $preview_html, $dateformat, $currentapp;

		public function __construct( $currentapp = 'property' )
		{
			$this->currentapp	 = $currentapp ? $currentapp : $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so			 = createObject("{$this->currentapp}.soorder_template");
			$this->bocommon		 = createObject('property.bocommon');
			$this->config		 = CreateObject('phpgwapi.config', $this->currentapp)->read();
			$this->dateformat	 = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$this->fields = $this->so->get_fields();

		}

		public function read($params)
		{
			$values =  $this->so->read($params);
			foreach ($values['results'] as &$entry)
			{
				$entry['created_text'] = $GLOBALS['phpgw']->common->show_date($entry['created']);
				$entry['modified_text'] = $GLOBALS['phpgw']->common->show_date($entry['modified']);
			}
			return $values;
		}

		public function get_vendors( )
		{
			return $this->so->get_vendors();
		}

		public function store($object)
		{
		}

		function get_fields()
		{
			return $this->so->get_fields();
		}

		function read_single( $id )
		{
			return $this->so->read_single($id);
		}

		function save( $values )
		{

			if (empty($values['id']))
			{
				$action = 'add';
			}
			else
			{
				$action = 'edit';
			}

			if ($action == 'edit')
			{
				try
				{
					$receipt = $this->so->edit($values);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			else
			{
				try
				{
					$receipt		 = $this->so->add($values);
					$values['id']	 = $receipt['id'];
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}

			return $receipt;
		}

	}