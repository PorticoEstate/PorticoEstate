<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003 -2018 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @subpackage location
	 * @version $Id$
	 */
	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('phpgwapi.jquery');

	class property_uigab extends phpgwapi_uicommon_jquery
	{

		private $receipt = array();
		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $part_of_town_id;
		var $sub;
		var $currentapp;
		var $custom;
		var $public_functions = array
			(
			'index' => true,
			'list_detail' => true,
			'query' => true,
			'query_detail' => true,
			'view' => true,
			'add' => true,
			'edit' => true,
			'save' => true,
			'delete' => true,
			'download' => true
		);

		function __construct()
		{
			parent::__construct();

			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::location::gabnr';

			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo = CreateObject('property.bogab', true);
			$this->bocommon = CreateObject('property.bocommon');
			$this->bolocation = CreateObject('property.bolocation');
			$this->custom = createObject('property.custom_fields');

			$this->config = CreateObject('phpgwapi.config', 'property');
			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->acl_location = '.location';
			$this->acl_read = $this->acl->check('.location', PHPGW_ACL_READ, 'property');
			$this->acl_add = $this->acl->check('.location', PHPGW_ACL_ADD, 'property');
			$this->acl_edit = $this->acl->check('.location', PHPGW_ACL_EDIT, 'property');
			$this->acl_delete = $this->acl->check('.location', PHPGW_ACL_DELETE, 'property');

			$this->start = $this->bo->start;
			$this->query = $this->bo->query;
			$this->sort = $this->bo->sort;
			$this->order = $this->bo->order;
			$this->filter = $this->bo->filter;
			$this->cat_id = $this->bo->cat_id;
			$this->allrows = $this->bo->allrows;
			$this->gab_insert_level = $this->bo->gab_insert_level;
		}

		private function _populate( $data = array() )
		{
			$gab_id = phpgw::get_var('gab_id');
			$location_code = phpgw::get_var('location_code');
			$values = (array)phpgw::get_var('values');
			$values_attribute = (array)phpgw::get_var('values_attribute');

			$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record', 'property');
			$values = $this->bocommon->collect_locationdata($values, $insert_record);

			$values['gab_id'] = $gab_id;

			$values['location_code'] = $location_code;

			if (!$values['location_code'] && !$values['location'])
			{
				$values['location_code'] = '0000-00';
//				$this->receipt['error'][] = array('msg' => lang('Please select a location !'));
			}

			if ((count($values['location']) < $this->gab_insert_level) && !$values['propagate'] && !$values['location_code'])
			{
				$values['location_code'] = '0000-00';
			//	$this->receipt['error'][] = array('msg' => lang('Either select propagate - or choose location level %1 !', $this->gab_insert_level));
			}

			if (isset($values_attribute) && is_array($values_attribute))
			{
				foreach ($values_attribute as &$attribute)
				{
					if ($attribute['nullable'] != 1 && (!$attribute['value'] && empty($values['extra'][$attribute['name']])))
					{
						$receipt['error'][] = array('msg' => lang('Please enter value for attribute %1', $attribute['input_text']));
					}

					if (isset($attribute['value']) && $attribute['value'] && $attribute['datatype'] == 'I' && !ctype_digit($attribute['value']))
					{
						$receipt['error'][] = array('msg' => lang('Please enter integer for attribute %1', $attribute['input_text']));
					}

					if (isset($attribute['value']) && $attribute['value'] && $attribute['datatype'] == 'V' && strlen($attribute['value']) > $attribute['precision'])
					{
						$receipt['error'][] = array('msg' => lang('Max length for attribute %1 is: %2', "\"{$attribute['input_text']}\"", $attribute['precision']));
						$attribute['value'] = substr($attribute['value'], 0, $attribute['precision']);
					}
				}
			}

			$values['attributes'] = $values_attribute;

			foreach ($data as $key => $original_value)
			{
				if ((!isset($values[$key]) || !$values[$key]) && $data[$key])
				{
					$values[$key] = $original_value;
				}
			}

			return $values;
		}


		function download()
		{
			$address = phpgw::get_var('address');
			$location_code = phpgw::get_var('location_code');
			$gaards_nr = phpgw::get_var('gaards_nr', 'string');
			$bruksnr = phpgw::get_var('bruksnr', 'int');
			$feste_nr = phpgw::get_var('feste_nr', 'int');
			$seksjons_nr = phpgw::get_var('seksjons_nr', 'int');

			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$columns = phpgw::get_var('columns');

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'allrows' => true,
				'location_code' => $location_code ? $location_code : $search['value'],
				'gaards_nr' => $gaards_nr,
				'bruksnr' => $bruksnr,
				'feste_nr' => $feste_nr,
				'seksjons_nr' => $seksjons_nr,
				'address' => $address
			);

			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 1, 'acl_location' => $this->acl_location));
			}

			$gab_list = $this->bo->read($params);
			$uicols = $this->get_uicols();
			$content = array();

			if (isset($gab_list) && is_array($gab_list))
			{
				$j = 0;
				foreach ($gab_list as $gab)
				{
					for ($i = 0; $i < count($uicols['name']); $i++)
					{
						if($uicols['input_type'][$i] == 'hidden')
						{
							continue;
						}

						if ($uicols['name'][$i] == 'gaards_nr')
						{
							$value_gaards_nr = substr($gab['gab_id'], 4, 5);
							$value = $value_gaards_nr;
						}
						else if ($uicols['name'][$i] == 'bruksnr')
						{
							$value_bruks_nr = substr($gab['gab_id'], 9, 4);
							$value = $value_bruks_nr;
						}
						else if ($uicols['name'][$i] == 'feste_nr')
						{
							$value_feste_nr = substr($gab['gab_id'], 13, 4);
							$value = $value_feste_nr;
						}
						else if ($uicols['name'][$i] == 'seksjons_nr')
						{
							$value_seksjons_nr = substr($gab['gab_id'], 17, 3);
							$value = $value_seksjons_nr;
						}
						else if ($uicols['name'][$i] == 'owner')
						{
							$value = $lang_yes_no[$gab[$uicols['name'][$i]]];
						}
						else
						{
							$value = isset($gab[$uicols['name'][$i]]) ? $gab[$uicols['name'][$i]] : '';
						}

						$content[$j][$uicols['name'][$i]] = $value;
					}

					$j++;
				}
			}

			$table_header = array();
			for ($i = 0; $i < count($uicols['name']); $i++)
			{
				if($uicols['input_type'][$i] != 'hidden')
				{
					$table_header['name'][] = $uicols['name'][$i];
					$table_header['descr'][] = $uicols['descr'][$i];
				}
			}

			$this->bocommon->download($content, $table_header['name'], $table_header['descr'], array());
		}

		function index()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 1, 'acl_location' => $this->acl_location));
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');
			$location_code = phpgw::get_var('location_code');

			$appname = lang('gab');
			$function_msg = lang('list gab');

			$data = array(
				'datatable_name' => $appname . ': ' . $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array(
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => 'property.uigab.index',
//						'location_code'	=> $location_code,
						'phpgw_return_as' => 'json'
					)),
					'new_item' => self::link(array(
						'menuaction' => 'property.uigab.add',
						'from' => 'index'
					)),
					'download' => self::link(array(
						'menuaction' => 'property.uigab.download',
						'export' => true,
						'allrows' => true
					)),
					'allrows' => true,
					'editor_action' => '',
					'query'		=> $location_code,
					'field' => array()
				)
			);

			$uicols = $this->get_uicols();

			$count_uicols_name = count($uicols['name']);

			for ($k = 0; $k < $count_uicols_name; $k++)
			{
				$params = array
					(
					'key' => $uicols['name'][$k],
					'label' => $uicols['descr'][$k],
					'sortable' => ($uicols['sortable'][$k]) ? true : false,
					'hidden' => ($uicols['input_type'][$k] == 'hidden') ? true : false,
					'className' => ($uicols['className'][$k]) ? $uicols['className'][$k] : ''
				);

				if ($uicols['formatter'][$k])
				{
					$params['formatter'] = $uicols['formatter'][$k];
				}

				array_push($data['datatable']['field'], $params);
			}

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'gab_id',
						'source' => 'gab_id'
					),
				)
			);

			if ($this->acl_read)
			{
				$data['datatable']['actions'][] = array
					(
					'my_name' => 'view',
					'text' => lang('view'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uigab.list_detail'
					)),
					'parameters' => json_encode($parameters)
				);
			}

			/* if($this->acl_add)
			  {
			  $data['datatable']['actions'][] = array
			  (
			  'my_name'			=> 'add',
			  'text' 			=> lang('add'),
			  'action'		=> $GLOBALS['phpgw']->link('/index.php',array
			  (
			  'menuaction'	=> 'property.uigab.add',
			  'from'			=> 'index'
			  ))
			  );
			  } */
			unset($parameters);

			$inputFilters = array(
				'gaards_nr' => lang('Gaards nr'),
				'bruksnr' => lang('Bruks nr'),
				'feste_nr' => lang('Feste nr'),
				'seksjons_nr' => lang('Seksjons nr'),
				'location_code' => lang('Location'),
				'address' => lang('Address')
			);

			if($location_code)
			{
				$set_location_code = <<<JS

					$( '#location_code').val('{$location_code}');
JS;
			}
			else
			{
				$set_location_code = '';
			}

			$code = "var inputFilters = " . json_encode($inputFilters);

			$lang_search = lang('search');

			$code .= <<<JS
				
				function initCompleteDatatable(oSettings, json, oTable) 
				{
					$('#datatable-container_filter').empty();
					$.each(inputFilters, function(i, val)
					{
						$('#datatable-container_filter').append('<input type="text" placeholder="{$lang_search} '+val+'" id="'+i+'" />');
					});

					{$set_location_code}

					var valuesInputFilter = {};

					$.each(inputFilters, function(i, val) 
					{
						valuesInputFilter[i] = '';
						$( '#' + i).on( 'keyup change', function () 
						{
							if ( $.trim($(this).val()) != $.trim(valuesInputFilter[i]) ) 
							{
								filterData(i, $(this).val());
								valuesInputFilter[i] = $(this).val();
							}
						});
					});
				};

JS;

			$GLOBALS['phpgw']->js->add_code('', $code, true);

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::add_javascript('property', 'portico', 'gab.index.js');
			self::render_template_xsl('datatable_jquery', $data);
		}


		/**
		 *
		 * @return array uicols
		 */
		private function get_uicols( )
		{
			$uicols = array(
				'input_type' => array('hidden', 'text', 'text', 'text', 'text',  'text',
					'text', 'text', 'link', 'link'),
				'name' => array('gab_id', 'gaards_nr', 'bruksnr', 'feste_nr', 'seksjons_nr',
					'owner', 'location_code', 'address', 'map', 'gab'),
				'formatter' => array('', '', '', '', '', '', 'linktToLocation', '', 'linktToMap', 'linktToGab'),
				'sortable' => array('', true, true, true, true, '', true, true, '', ''),
				'descr' => array('dummy', lang('Gaards nr'), lang('Bruks nr'), lang('Feste nr'),
					lang('Seksjons nr'), lang('Owner'), lang('Location'), lang('Address'),
					lang('Map'), lang('Gab')),
				'className' => array('', '', '', '', '', '', 'center', '', 'center', 'center')
			);

			$custom_fields = $this->bo->custom->find('property', '.location.gab', 0, '', 'ASC', 'attrib_sort', true, true);
			foreach ($custom_fields as $custom_field)
			{
				$uicols['input_type'][] = 'text';
				$uicols['name'][] = $custom_field['column_name'];
				$uicols['formatter'][] = $custom_field['formatter'];
				$uicols['sortable'][] = false;
				$uicols['descr'][] = $custom_field['input_text'];
				$uicols['className'][] = '';
			}

			return $uicols;
		}

		public function query()
		{
			$address = phpgw::get_var('address');
			$location_code = phpgw::get_var('location_code');
			$gaards_nr = phpgw::get_var('gaards_nr', 'string');
			$bruksnr = phpgw::get_var('bruksnr', 'int');
			$feste_nr = phpgw::get_var('feste_nr', 'int');
			$seksjons_nr = phpgw::get_var('seksjons_nr', 'int');

			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');
			$export = phpgw::get_var('export', 'bool');

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'allrows' => phpgw::get_var('length', 'int') == -1 || $export,
				'location_code' => $location_code ? $location_code : $search['value'],
				'gaards_nr' => $gaards_nr,
				'bruksnr' => $bruksnr,
				'feste_nr' => $feste_nr,
				'seksjons_nr' => $seksjons_nr,
				'address' => $address
			);

			$gab_list = $this->bo->read($params);

			$config = CreateObject('phpgwapi.config', 'property');

			$config->read_repository();

			$link_to_map = (isset($config->config_data['map_url']) ? $config->config_data['map_url'] : '');
			if ($link_to_map)
			{
				$text_map = lang('Map');
			}

			$link_to_gab = isset($config->config_data['gab_url']) ? $config->config_data['gab_url'] : '';
			$gab_url_paramtres = isset($config->config_data['gab_url_paramtres']) ? $config->config_data['gab_url_paramtres'] : 'type=eiendom&Gnr=__gaards_nr__&Bnr=__bruks_nr__&Fnr=__feste_nr__&Snr=__seksjons_nr__';

			if ($link_to_gab)
			{
				$text_gab = lang('GAB');
			}

			$uicols = $this->get_uicols();

			$lang_yes_no = array(
				'yes' => lang('yes'),
				'no' => lang('no'),
				'' => lang('no'),
			);

			$values = array();
			$j = 0;
			if (isset($gab_list) && is_array($gab_list))
			{
				foreach ($gab_list as $gab)
				{
					for ($i = 0; $i < count($uicols['name']); $i++)
					{
						if ($uicols['name'][$i] == 'gaards_nr')
						{
							$value_gaards_nr = substr($gab['gab_id'], 4, 5);
							$value = $value_gaards_nr;
						}
						else if ($uicols['name'][$i] == 'bruksnr')
						{
							$value_bruks_nr = substr($gab['gab_id'], 9, 4);
							$value = $value_bruks_nr;
						}
						else if ($uicols['name'][$i] == 'feste_nr')
						{
							$value_feste_nr = substr($gab['gab_id'], 13, 4);
							$value = $value_feste_nr;
						}
						else if ($uicols['name'][$i] == 'seksjons_nr')
						{
							$value_seksjons_nr = substr($gab['gab_id'], 17, 3);
							$value = $value_seksjons_nr;
						}
						else if ($uicols['name'][$i] == 'owner')
						{
							$value = $lang_yes_no[$gab[$uicols['name'][$i]]];
						}
						else if ($uicols['name'][$i] == 'location_code'  && !preg_match('/^0000/', $gab[$uicols['name'][$i]]))
						{
							$values[$j]['link_location'] = self::link(array('menuaction' => 'property.uilocation.view','location_code' => $gab[$uicols['name'][$i]]));
							$value = $gab[$uicols['name'][$i]];
						}
						else
						{
							$value = isset($gab[$uicols['name'][$i]]) ? $gab[$uicols['name'][$i]] : '';
						}

						if (isset($uicols['input_type']) && isset($uicols['input_type'][$i]) && $uicols['input_type'][$i] == 'link' && $uicols['name'][$i] == 'map')
						{
							$value_gaards_nr = substr($gab['gab_id'], 4, 5);
							$value_bruks_nr = substr($gab['gab_id'], 9, 4);
							$value_feste_nr = substr($gab['gab_id'], 13, 4);
							$link = phpgw::safe_redirect($link_to_map . '?maptype=Eiendomskart&gnr=' . (int)$value_gaards_nr . '&bnr=' . (int)$value_bruks_nr . '&fnr=' . (int)$value_feste_nr);

							$values[$j]['link_map'] = $link;
							$value = $text_map;
						}
						if (isset($uicols['input_type']) && isset($uicols['input_type'][$i]) && $uicols['input_type'][$i] == 'link' && $uicols['name'][$i] == 'gab')
						{
							$value_kommune_nr = substr($gab['gab_id'], 0, 4);
							$value_gaards_nr = substr($gab['gab_id'], 4, 5);
							$value_bruks_nr = substr($gab['gab_id'], 9, 4);
							$value_feste_nr = substr($gab['gab_id'], 13, 4);
							$value_seksjons_nr = substr($gab['gab_id'], 17, 3);

							$_param = str_replace(array
								(
								'__kommune_nr__',
								'__gaards_nr__',
								'__bruks_nr__',
								'__feste_nr__',
								'__seksjons_nr__'
								), array
								(
								$value_kommune_nr,
								(int)$value_gaards_nr,
								(int)$value_bruks_nr,
								(int)$value_feste_nr,
								(int)$value_seksjons_nr
								), $gab_url_paramtres);

							$link = phpgw::safe_redirect("$link_to_gab?{$_param}");

							$values[$j]['link_gab'] = $link;
							$value = $text_gab;
						}

						$values[$j][$uicols['name'][$i]] = $value;
					}

					$j++;
				}
			}


			if ($export)
			{
				return $values;
			}

			$result_data = array('results' => $values);
			$result_data['total_records'] = $this->bo->total_records;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		function list_detail()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 1, 'acl_location' => $this->acl_location));
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query_detail();
			}

			$gab_id = phpgw::get_var('gab_id');

			$top_toolbar = array
				(
				array
				(
					'type' => 'button',
					'id' => 'btn_add',
					'value' => lang('Add'),
					'url' => self::link(array
						(
						'menuaction' => 'property.uigab.add',
						'gab_id' => $gab_id,
						'from' => 'list_detail',
						'new' => true
					))
				),
				array
				(
					'type' => 'button',
					'id' => 'btn_cancel',
					'value' => lang('Cancel'),
					'url' => self::link(array
						(
						'menuaction' => 'property.uigab.index'
					))
				)
			);

			$gab_list = $this->bo->read_detail(array('gab_id' => $gab_id), true);

			$uicols = $this->bo->uicols;

			$count_uicols_name = count($uicols['name']);

			$detail_def = array();
			for ($k = 0; $k < $count_uicols_name; $k++)
			{
				$params = array
					(
					'key' => $uicols['name'][$k],
					'label' => $uicols['descr'][$k],
					'sortable' => ($uicols['sortable'][$k]) ? true : false,
					'hidden' => ($uicols['input_type'][$k] == 'hidden') ? true : false
				);

				if ($uicols['name'][$k] == 'gab_id')
				{
					$params['sortable'] = true;
				}

				if ($uicols['name'][$k] == 'address')
				{
					$params['sortable'] = true;
				}

				array_push($detail_def, $params);
			}

			$tabletools = array();

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'location_code',
						'source' => 'location_code'
					)
				)
			);

			if ($this->acl_read)
			{
				$tabletools[] = array
					(
					'my_name' => 'view',
					'text' => lang('view'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uigab.view',
						'gab_id' => $gab_id
					)),
					'parameters' => json_encode($parameters)
				);
			}

			if ($this->acl_edit)
			{
				$tabletools[] = array
					(
					'my_name' => 'edit',
					'text' => lang('edit'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uigab.edit',
						'from' => 'list_detail',
						'gab_id' => $gab_id
					)),
					'parameters' => json_encode($parameters)
				);
			}

			if ($this->acl_delete)
			{
				$tabletools[] = array
					(
					'my_name' => 'delete',
					'text' => lang('delete'),
					'confirm_msg' => lang('do you really want to delete this entry'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uigab.delete',
						'gab_id' => $gab_id
					)),
					'parameters' => json_encode($parameters)
				);
			}

			/* if($this->acl_add)
			  {
			  $tabletools[] = array
			  (
			  'my_name'			=> 'add',
			  'text' 			=> lang('add'),
			  'action'		=> $GLOBALS['phpgw']->link('/index.php',array
			  (
			  'menuaction'	=> 'property.uigab.edit',
			  'gab_id'		=>	$gab_id,
			  'from' 			=> 'list_detail',
			  'new'			=>	true
			  ))
			  );
			  } */

			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uigab.list_detail',
						'gab_id' => $gab_id, 'phpgw_return_as' => 'json'))),
				'ColumnDefs' => $detail_def,
				'data' => json_encode(array()),
				'tabletools' => $tabletools,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$appname = lang('gab');
			$function_msg = lang('list gab detail');

			$gaards_nr = substr($gab_id, 4, 5);
			$bruks_nr = substr($gab_id, 9, 4);
			$feste_nr = substr($gab_id, 13, 4);
			$seksjons_nr = substr($gab_id, 17, 3);

			$info = array();
			$info[0]['name'] = lang('gaards nr');
			$info[0]['value'] = $gaards_nr;
			$info[1]['name'] = lang('bruks nr');
			$info[1]['value'] = $bruks_nr;
			$info[2]['name'] = lang('Feste nr');
			$info[2]['value'] = $feste_nr;
			$info[3]['name'] = lang('Seksjons nr');
			$info[3]['value'] = $seksjons_nr;
			$info[4]['name'] = lang('owner');
			$info[4]['value'] = lang($gab_list[0]['owner']);

			$data = array
				(
				'datatable_def' => $datatable_def,
				'info' => $info,
				'top_toolbar' => $top_toolbar
			);

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl(array('gab', 'datatable_inline'), array('list_gab_detail' => $data));
		}

		public function query_detail()
		{
			$gab_id = phpgw::get_var('gab_id');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'dir' => $order[0]['dir'],
				'gab_id' => $gab_id
			);

			$values = $this->bo->read_detail($params, true);

			$result_data = array('results' => $values);

			$result_data['total_records'] = $this->bo->total_records;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		public function add()
		{
			$this->edit();
		}

		function edit( $values = array(), $mode = 'edit' )
		{
			if (!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 2, 'acl_location' => $this->acl_location));
			}

			$from = phpgw::get_var('from');
			$new = phpgw::get_var('new', 'bool');
			$gab_id = phpgw::get_var('gab_id');
			$location_code = phpgw::get_var('location_code');

			if (!$values && $location_code)
			{
				$values['location_data'] = $this->bolocation->read_single($location_code, $values['extra']);
			}

			if ($values['gab_id'])
			{
				$gab_id = $values['gab_id'];
				$location_code = $values['location_code'];
			}

//			if ($gab_id && !$new)
			if ($gab_id)
			{
				$values = $this->bo->read_single($gab_id, $location_code);
			}

			if(empty($values['attributes']))
			{
				$values['attributes'] = $this->bo->custom->find('property', '.location.gab', 0, '', 'ASC', 'attrib_sort', true, true);
			}

			if ($values['location_code'])
			{
				$function_msg = lang('Edit gab');
				$action = 'edit';
				$lookup_type = 'view2';
			}
			else
			{
				$function_msg = lang('Add gab');
				$action = 'add';
				$lookup_type = 'form2';
			}

			if ($values['cat_id'])
			{
				$this->cat_id = $values['cat_id'];
			}

			if ($values['location_data'])
			{
				$type_id = count(explode('-', $values['location_code']));
			}
			else
			{
				$type_id = $this->gab_insert_level;
			}
			$location_data = $this->bolocation->initiate_ui_location(array(
				'values' => $values['location_data'],
				'type_id' => $type_id,
				'no_link' => false, // disable lookup links for location type less than type_id
				'tenant' => false,
				'lookup_type' => $lookup_type,
				'required_level' => 0
			));

			$link_data = array
				(
				'menuaction' => 'property.uigab.save',
				'gab_id' => $gab_id,
				'location_code' => $location_code,
				'from' => $from
			);

			$tabs = array();
			$tabs['generic'] = array('label' => lang('generic'), 'link' => '#generic');
			$active_tab = 'generic';

			$done_data = array('menuaction' => 'property.uigab.' . $from);
			if ($from == 'list_detail')
			{
				$done_data['gab_id'] = $gab_id;
			}

			$kommune_nr = substr($gab_id, 0, 4);
			if (!$kommune_nr > 0)
			{
				$this->config->read_repository();
				$kommune_nr = $this->config->config_data['default_municipal'];
			}

			if (isset($this->receipt) && is_array($this->receipt))
			{
				$msgbox_data = $this->bocommon->msgbox_data($this->receipt);
			}
			else
			{
				$msgbox_data = '';
			}

			$attributes_groups = $this->custom->get_attribute_groups('property', '.location.gab',  $values['attributes']);
//				_debug_array($attributes_groups);
			$attributes_general = array();
			$i = -1;
			$attributes = array();

			$active_tab = phpgw::get_var('active_tab');

			foreach ($attributes_groups as $_key => $group)
			{
				if (!isset($group['attributes']))
				{
					$group['attributes'] = array(array());
				}
				if (!$group['level'])
				{
					$_tab_name = str_replace(array(' ', '/', '?', '.', '*', '(', ')', '[', ']'), '_', $group['name']);
					$tabs[$_tab_name] = array('label' => $group['name'], 'link' => "#{$_tab_name}",
						'disable' => 0);
					$group['link'] = $_tab_name;
					$attributes[] = $group;
					$i ++;
				}
				else
				{
					$attributes[$i]['attributes'][] = array
						(
						'datatype' => 'section',
						'descr' => '<H' . ($group['level'] + 1) . "> {$group['descr']} </H" . ($group['level'] + 1) . '>',
						'level' => $group['level'],
					);
					$attributes[$i]['attributes'] = array_merge($attributes[$i]['attributes'], $group['attributes']);
				}
				unset($_tab_name);

			}
			unset($attributes_groups);

			$data = array
			(
				'attributes_group' => $attributes,
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'value_owner' => $values['owner'],
				'lang_owner' => lang('owner'),
				'kommune_nr' => $kommune_nr,
				'gaards_nr' => substr($gab_id, 4, 5),
				'bruks_nr' => substr($gab_id, 9, 4),
				'feste_nr' => substr($gab_id, 13, 4),
				'seksjons_nr' => substr($gab_id, 17, 3),
				'lang_kommune_nr' => lang('kommune nr'),
				'lang_gaards_nr' => lang('gaards nr'),
				'lang_bruksnr' => lang('bruks nr'),
				'lang_feste_nr' => lang('Feste nr'),
				'lang_seksjons_nr' => lang('Seksjons nr'),
				'action' => $action,
				'lookup_type' => $lookup_type,
				'location_data2' => $location_data,
				'form_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action' => $GLOBALS['phpgw']->link('/index.php', $done_data),
				'lang_save' => lang('save'),
				'lang_done' => lang('done'),
				'lang_propagate' => lang('propagate'),
				'lang_propagate_statustext' => lang('check to inherit from this location'),
				'lang_remark_statustext' => lang('Enter a remark for this entity'),
				'lang_remark' => lang('remark'),
				'value_remark' => $values['remark'],
				'lang_done_statustext' => lang('Back to the list'),
				'lang_save_statustext' => lang('Save the gab'),
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator' => phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'))
			);

			$appname = lang('gab');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$attribute_template = 'attributes_form';
			if($mode == 'view')
			{
				$attribute_template = 'attributes_view';
			}

			self::render_template_xsl(array('gab', $attribute_template), array('edit' => $data));
		}

		public function save()
		{
			if (!$_POST)
			{
				return $this->edit();
			}

			$gab_id = phpgw::get_var('gab_id');
			$location_code = phpgw::get_var('location_code');

			/*
			 * Overrides with incoming data from POST
			 */
			$data = $this->bo->read_single($gab_id, $location_code);
			$data = $this->_populate($data);

			if ($this->receipt['error'])
			{
				$this->edit();
			}
			else
			{
				try
				{
					$receipt = $this->bo->save($data);
					$location_code = $data['location_code'] = $receipt['location_code'];
					$gab_id = $data['gab_id'] = $receipt['gab_id'];
					$this->receipt = $receipt;
				}
				catch (Exception $e)
				{
					if ($e)
					{
						phpgwapi_cache::message_set($e->getMessage(), 'error');
					}
				}

				if ($location_code)
				{
					self::message_set($receipt);
					self::redirect(array('menuaction' => 'property.uigab.edit', 'gab_id' => $gab_id,
						'location_code' => $location_code,
						'from' => phpgw::get_var('from')));
				}

				$this->edit($data);
				return;
			}
		}

		function delete()
		{
			if (!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 8, 'acl_location' => $this->acl_location));
			}

			$gab_id = phpgw::get_var('gab_id');
			$location_code = phpgw::get_var('location_code');
			$confirm = phpgw::get_var('confirm', 'bool', 'POST');

			//cramirez add JsonCod for Delete
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$this->bo->delete($gab_id, $location_code);
				return "gab_id " . $gab_id . " " . lang("has been deleted");
			}

			$link_data = array
				(
				'menuaction' => 'property.uigab.list_detail',
				'gab_id' => $gab_id
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($gab_id, $location_code);
				$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
				'done_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'delete_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uigab.delete',
					'gab_id' => $gab_id, 'location_code' => $location_code)),
				'lang_confirm_msg' => lang('do you really want to delete this entry'),
				'lang_yes' => lang('yes'),
				'lang_yes_statustext' => lang('Delete the entry'),
				'lang_no_statustext' => lang('Back to the list'),
				'lang_no' => lang('no')
			);

			$appname = lang('gab');
			$function_msg = lang('delete gab at:') . ' ' . $location_code;

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('delete' => $data));
		}

		function view()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 1, 'acl_location' => $this->acl_location));
			}

			$gab_id = phpgw::get_var('gab_id');
			$location_code = phpgw::get_var('location_code');

			$GLOBALS['phpgw']->xslttpl->add_file(array('gab'));

			//_debug_array($values);


			if ($gab_id && !$new)
			{
				$values = $this->bo->read_single($gab_id, $location_code);
			}

			$function_msg = lang('View gab');
			$location_type = 'view';


			$location_data = $this->bolocation->initiate_ui_location(array(
				'values' => $values['location_data'],
				'type_id' => count(explode('-', $values['location_code'])),
				'no_link' => false, // disable lookup links for location type less than type_id
				'tenant' => false,
				'lookup_type' => 'view'
			));

			$tabs = array();
			$tabs['generic'] = array('label' => lang('generic'), 'link' => '#generic');
			$active_tab = 'generic';

			$data = array
				(
				'kommune_nr' => substr($gab_id, 0, 4),
				'gaards_nr' => substr($gab_id, 4, 5),
				'bruks_nr' => substr($gab_id, 9, 4),
				'feste_nr' => substr($gab_id, 13, 4),
				'seksjons_nr' => substr($gab_id, 17, 3),
				'value_owner' => lang($values['owner']),
				'lang_owner' => lang('owner'),
				'lang_kommune_nr' => lang('kommune nr'),
				'lang_gaards_nr' => lang('gaards nr'),
				'lang_bruksnr' => lang('bruks nr'),
				'lang_feste_nr' => lang('Feste nr'),
				'lang_seksjons_nr' => lang('Seksjons nr'),
				'location_type' => $location_type,
				'location_data' => $location_data,
				'done_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uigab.list_detail',
					'gab_id' => $gab_id)),
				'lang_save' => lang('save'),
				'lang_done' => lang('done'),
				'lang_remark' => lang('remark'),
				'value_remark' => $values['remark'],
				'lang_done_statustext' => lang('Back to the list'),
				'edit_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uigab.edit',
					'from' => 'list_detail', 'gab_id' => $gab_id, 'location_code' => $location_code)),
				'lang_edit_statustext' => lang('Edit this entry'),
				'lang_edit' => lang('Edit'),
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab)
			);

			$appname = lang('gab');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('view' => $data));
		}
	}