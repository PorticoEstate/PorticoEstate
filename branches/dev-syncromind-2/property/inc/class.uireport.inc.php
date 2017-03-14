<?php
	/**
	 * phpGroupWare - property: a part of a Facilities Management System.
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
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/
	 * @package property
	 * @subpackage logistic
	 * @version $Id: class.uireport.inc.php 14913 2017-03-10 12:27:37Z sigurdne $
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('phpgwapi.jquery');

	class property_uireport extends phpgwapi_uicommon_jquery
	{
		private $receipt = array();

		public $public_functions = array(
			'query' => true,
			'index' => true,
			'download' => true,
			'get_views' => true
		);

		public function __construct()
		{
			parent::__construct();
			
			//$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->bo = CreateObject('property.boreport', true);
		}

		public function download()
		{
			return;
		}

		private function _get_filters()
		{
			$combos = array();

			$views = $this->bo->get_views();;
			foreach ($views as $view)
			{
				$list[] = array('id' => $view['name'], 'name' => $view['name']);
			}
				
			$default_value = array('id' => '', 'name' => lang('Select'));
			array_unshift($list, $default_value);

			$combos[] = array('type' => 'filter',
				'name' => 'view',
				'text' => lang('Views'),
				'list' => $list
			);
			
			return $combos;
		}
		
		/**
		 * Prepare UI
		 * @return void
		 */
		public function index()
		{
			
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}
						
			$appname = lang('report');
			$function_msg = lang('list');
			
			$data = array(
				'datatable_name' => $appname,
				'form' => array(
					'toolbar' => array(
						'item' => array(),
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'property.uireport.index',
						'phpgw_return_as' => 'json')),
					'download' => self::link(array('menuaction' => 'property.uireport.download',
						'export' => true, 'allrows' => true)),
					'new_item' => self::link(array('menuaction' => 'property.uireport.add')),
					'allrows' => true,
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'sortable' => true,
							'formatter' => 'JqueryPortico.formatLink'
						),
						array(
							'key' => 'name',
							'label' => lang('Name'),
							'sortable' => true,
							'editor' => true
						)
					)
				),
			);
			
			$filters = $this->_get_filters();

			foreach ($filters as $filter)
			{
				$data['form']['toolbar']['item'][] = $filter;
			}
			
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl('datatable_jquery', $data);
		}

		/**
		 * Fetch data from $this->bo based on parametres
		 * @return array
		 */
		public function query()
		{
			$result_data = array('results' => array());

			$result_data['total_records'] = 0;
			$result_data['draw'] = 1;
			
			return $this->jquery_results($result_data);
		}
	}