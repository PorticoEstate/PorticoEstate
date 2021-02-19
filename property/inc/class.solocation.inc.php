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
	 * @subpackage location
	 * @version $Id$
	 */

	/**
	 * Description
	 * @package property
	 */
	class property_solocation
	{

		var $bocommon;
		var $total_records;
		protected $global_lock = false;

		function __construct( $bocommon = '' )
		{

			$this->account	= isset($GLOBALS['phpgw_info']['user']['account_id']) ? (int)$GLOBALS['phpgw_info']['user']['account_id'] : -1;

			$this->soadmin_location	 = CreateObject('property.soadmin_location');
			if (!$bocommon || !is_object($bocommon))
			{
				$this->bocommon = CreateObject('property.bocommon');
			}
			else
			{
				$this->bocommon = $bocommon;
			}
			$this->custom = createObject('property.custom_fields');

			$this->db		 = & $GLOBALS['phpgw']->db;
			$this->socommon	 = & $this->bocommon->socommon;

			$this->join		 = & $this->db->join;
			$this->left_join = & $this->db->left_join;
			$this->like		 = & $this->db->like;
		}

		function read_entity_to_link( $location_code, $exact = false )
		{
			$condition = $exact ? "= '{$location_code}'" : "{$this->like} '{$location_code}%'";

			$entity = array();

			$type_app = execMethod('property.soentity.get_type_app');

			foreach ($type_app as $type => $app)
			{
				if (!$GLOBALS['phpgw']->acl->check('run', PHPGW_ACL_READ, $app))
				{
					continue;
				}

				$sql = "SELECT * FROM fm_{$type}_category where loc_link=1";

				$this->db->query($sql, __LINE__, __FILE__);

				$category = array();
				while ($this->db->next_record())
				{
					$category[] = array
						(
						'entity_id'	 => $this->db->f('entity_id'),
						'cat_id'	 => $this->db->f('id'),
						'name'		 => $this->db->f('name'),
						'descr'		 => $this->db->f('descr'),
						'is_eav'	 => $this->db->f('is_eav')
					);
				}

				foreach ($category as $entry)
				{
					if ($entry['is_eav'])
					{
						$location_id = $GLOBALS['phpgw']->locations->get_id($app, ".{$type}.{$entry['entity_id']}.{$entry['cat_id']}");
						$this->db->query("SELECT id as bim_type FROM fm_bim_type WHERE location_id = {$location_id}", __LINE__, __FILE__);
						$this->db->next_record();
						$bim_type	 = (int)$this->db->f('bim_type');
						$sql		 = "SELECT count(*) as hits FROM fm_bim_item WHERE location_code {$condition} AND type = {$bim_type}";
					}
					else
					{
						$sql = "SELECT count(*) as hits FROM fm_{$type}_{$entry['entity_id']}_{$entry['cat_id']} WHERE location_code {$condition}";
					}

					$this->db->query($sql, __LINE__, __FILE__);
					$this->db->next_record();
					if ($this->db->f('hits'))
					{
						$entity['related'][] = array
							(
							'entity_link'	 => $GLOBALS['phpgw']->link('/index.php', array
								(
								'menuaction'	 => 'property.uientity.index',
								'entity_id'		 => $entry['entity_id'],
								'cat_id'		 => $entry['cat_id'],
								'location_code'	 => $location_code,
								'type'			 => $type
								)
							),
							'name'			 => $entry['name'] . ' [' . $this->db->f('hits') . ']',
							'descr'			 => $entry['descr']
						);
					}
				}
			}

			$sql = "SELECT count(*) as hits FROM fm_tts_tickets WHERE location_code {$condition}";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			if ($this->db->f('hits'))
			{
				$hits				 = $this->db->f('hits');
				$entity['related'][] = array
					(
					'entity_link'	 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uitts.index',
						'query'		 => $location_code, 'status_id'	 => 'all')),
					'name'			 => lang('Helpdesk') . " [{$hits}]",
					'descr'			 => lang('Helpdesk')
				);
			}

			$sql = "SELECT count(*) as hits FROM fm_request WHERE location_code {$condition}";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			if ($this->db->f('hits'))
			{
				$hits				 = $this->db->f('hits');
				$entity['related'][] = array
					(
					'entity_link'	 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uirequest.index',
						'query'		 => $location_code, 'status_id'	 => 'all')),
					'name'			 => lang('request') . " [{$hits}]",
					'descr'			 => lang('request')
				);
			}

			$sql = "SELECT count(*) as hits FROM fm_project WHERE location_code {$condition}";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			if ($this->db->f('hits'))
			{
				$hits				 = $this->db->f('hits');
				$entity['related'][] = array
					(
					'entity_link'	 => $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'property.uiproject.index',
						'query'			 => $location_code,
						'criteria_id'	 => 4, //criteria 4 is for location_code
						'status_id'		 => 'all',
						'filter_year'	 => 'all')
					),
					'name'			 => lang('project') . " [{$hits}]",
					'descr'			 => lang('project')
				);
			}

			$sql = "SELECT count(*) as hits FROM fm_gab_location WHERE location_code {$condition}";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			if ($this->db->f('hits'))
			{
				$hits			 = $this->db->f('hits');
				$entity['gab'][] = array
					(
					'entity_link'	 => $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'property.uigab.index',
						'location_code'	 => $location_code)),
					'name'			 => lang('gabnr') . " [{$hits}]",
					'descr'			 => lang('gab info')
				);
			}


			$sql	 = "SELECT DISTINCT fm_s_agreement.id FROM fm_s_agreement"
				. " {$this->join} fm_s_agreement_detail ON fm_s_agreement.id = fm_s_agreement_detail.agreement_id"
				. " WHERE location_code {$condition}"
				. " GROUP BY fm_s_agreement.id";
			$this->db->query($sql, __LINE__, __FILE__);
			$hits	 = $this->db->num_rows();

			if ($hits)
			{
				$entity['related'][] = array
					(
					'entity_link'	 => $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'property.uis_agreement.index',
						'location_code'	 => $location_code)),
					'name'			 => lang('service agreement') . " [{$hits}]",
					'descr'			 => lang('service agreement')
				);
			}

			return $entity;
		}

		function select_status_list( $type_id )
		{
			if (!$type_id)
			{
				return;
			}
			$location_id = $GLOBALS['phpgw']->locations->get_id('property', ".location.{$type_id}");

			$sql = "SELECT phpgw_cust_choice.id, phpgw_cust_choice.value FROM phpgw_cust_attribute"
				. " $this->join phpgw_cust_choice ON"
				. " phpgw_cust_attribute.location_id= phpgw_cust_choice.location_id AND"
				. " phpgw_cust_attribute.id= phpgw_cust_choice.attrib_id"
				. " WHERE phpgw_cust_attribute.column_name='status'"
				. " AND phpgw_cust_choice.location_id={$location_id} ORDER BY phpgw_cust_choice.id";

			$this->db->query($sql, __LINE__, __FILE__);

			$status = array();
			while ($this->db->next_record())
			{
				$status[] = array
					(
					'id'	 => $this->db->f('id'),
					'name'	 => $this->db->f('value', true)
				);
			}

			return $status;
		}

		function get_owner_type_list()
		{
			$this->db->query("SELECT id,descr FROM fm_owner_category  ORDER BY descr ");

			$owner_type = array();
			while ($this->db->next_record())
			{
				$owner_type[] = array(
					'id'	 => $this->db->f('id'),
					'name'	 => $this->db->f('descr', true)
				);
			}
			return $owner_type;
		}

		function get_owner_list()
		{
			$this->db->query("SELECT *  FROM fm_owner ORDER BY org_name ");
			$owners = array();
			while ($this->db->next_record())
			{
				$owners[] = array(
					'id'	 => $this->db->f('id'),
					'name'	 => $this->db->f('org_name', true)
				);
			}
			return $owners;
		}

		function check_location( $location_code, $type_id = 0 )
		{
			if (!$type_id)
			{
				$type_id = count(explode('-', $location_code));
			}
			$this->db->query("SELECT location_code FROM fm_location{$type_id} WHERE location_code='{$location_code}'");

			return $this->db->next_record();
		}

		function get_column_list( $location_id )
		{
			$sql = "SELECT * FROM phpgw_cust_attribute "
				. " WHERE location_id = {$location_id}"
				. " AND (custom = 1 OR column_name = 'category')"
				. " AND list IS NULL";

			$this->db->query($sql, __LINE__, __FILE__);
			$attribs = array();

			while ($this->db->next_record())
			{
				$id				 = $this->db->f('id');
				$attribs[$id]	 = array
					(
					'id'						 => $id,
					'group_id'					 => (int)$this->db->f('group_id'),
					'attrib_sort'				 => (int)$this->db->f('attrib_sort'),
					'list'						 => $this->db->f('list'),
					'lookup_form'				 => $this->db->f('lookup_form'),
					'entity_form'				 => $this->db->f('entity_form'),
					'column_name'				 => $this->db->f('column_name'),
					'name'						 => $this->db->f('column_name'),
					'size'						 => $this->db->f('size'),
					'precision'					 => $this->db->f('precision_'),
					'statustext'				 => $this->db->f('statustext', true),
					'input_text'				 => $this->db->f('input_text', true),
					'type_name'					 => $this->db->f('type'),
					'datatype'					 => $this->db->f('datatype'),
					'search'					 => $this->db->f('search'),
					'nullable'					 => ($this->db->f('nullable') == 'True'),
					'history'					 => $this->db->f('history'),
					'disabled'					 => $this->db->f('disabled'),
					'helpmsg'					 => !!$this->db->f('helpmsg'),
					'get_list_function'			 => $this->db->f('get_list_function'),
					'get_list_function_input'	 => $this->db->f('get_list_function_input') ? unserialize($this->db->f('get_list_function_input', true)) : '',
					'get_single_function'		 => $this->db->f('get_single_function'),
					'get_single_function_input'	 => $this->db->f('get_single_function_input') ? unserialize($this->db->f('get_single_function_input', true)) : '',
					'short_description'			 => $this->db->f('short_description'),
					'javascript_action'			 => $this->db->f('javascript_action', true),
					'default_value'				 => $this->db->f('default_value', true)
				);
			}
			return $attribs;
		}

		function read( $data )
		{
			$start					 = isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$filter					 = isset($data['filter']) && $data['filter'] ? $data['filter'] : 0;
			$query					 = isset($data['query']) ? $data['query'] : '';
			$sort					 = isset($data['sort']) && $data['sort'] ? $data['sort'] : 'ASC';
			$order					 = isset($data['order']) ? $data['order'] : '';
			$cat_id					 = isset($data['cat_id']) && $data['cat_id'] ? $data['cat_id'] : '';
			$type_id				 = isset($data['type_id']) ? $data['type_id'] : '';
			$lookup_tenant			 = isset($data['lookup_tenant']) ? $data['lookup_tenant'] : '';
			$district_id			 = isset($data['district_id']) ? $data['district_id'] : '';
			$allrows				 = isset($data['allrows']) ? $data['allrows'] : '';
			$lookup					 = isset($data['lookup']) ? $data['lookup'] : '';
			$status					 = isset($data['status']) ? $data['status'] : '';
			$part_of_town_id		 = isset($data['part_of_town_id']) ? $data['part_of_town_id'] : '';
			$dry_run				 = isset($data['dry_run']) ? $data['dry_run'] : '';
			$location_code			 = isset($data['location_code']) ? $data['location_code'] : '';
			$filter_role_on_contact	 = $data['filter_role_on_contact'] ? (int)$data['filter_role_on_contact'] : 0;
			$role_id				 = $data['role_id'] ? (int)$data['role_id'] : 0;
			$results				 = $data['results'] ? (int)$data['results'] : 0;
			$check_for_control		 = isset($data['check_for_control']) ? $data['check_for_control'] : false;
			$control_registered		 = isset($data['control_registered']) ? $data['control_registered'] : '';
			$control_id				 = isset($data['control_id']) && $data['control_id'] ? $data['control_id'] : 0;
			$location_id			 = isset($data['location_id']) && $data['location_id'] ? (int)$data['location_id'] : 0;
			$filter_item			 = isset($data['filter_item']) && $data['filter_item'] ? (array)$data['filter_item'] : array();
			$additional_fields		 = !empty($data['additional_fields']) ? (array)$data['additional_fields'] : array();

			if ($location_id && !$type_id)
			{
				$location_info = $GLOBALS['phpgw']->locations->get_name($location_id);

				if (substr($location_info['location'], 1, 8) == 'location')
				{
					$type_info	 = explode('.', $location_info['location']);
					$type_id	 = $type_info[2];
				}
			}


			if (!$type_id)
			{
				return array();
			}

			if ($order == 'undefined')
			{
				$order = '';
			}

			switch ($order)
			{
				case 'undefined':
					$order	 = '';
					break;
				case 'loc1':
					$order	 = 'fm_location1.loc1';
					break;
				default:
			}


			$sql = $this->socommon->fm_cache('sql_' . $type_id . '_lt' . $lookup_tenant . '_l' . $lookup . '_f' . !!$filter_role_on_contact);
			if (empty($location_id))
			{
				$location_id = $GLOBALS['phpgw']->locations->get_id('property', ".location.{$type_id}");
			}

//			$choice_table = 'phpgw_cust_choice';
			$attribute_table	 = 'phpgw_cust_attribute';
//			$attribute_filter = " custom = 1 AND location_id = {$location_id}";
			$attribute_filter	 = " location_id = {$location_id}";
//			$attribute_choice_filter = " location_id = {$location_id}";

			$cols_return = array();
			$uicols = array();
			if (!$sql)
			{
				$location_types = $this->soadmin_location->select_location_type();

				$cols					 = "fm_location{$type_id}.*";
				$cols_return[]			 = 'id';
				$uicols['input_type'][]	 = 'hidden';
				$uicols['name'][]		 = 'id';
				$uicols['descr'][]		 = 'dummy';
				$uicols['statustext'][]	 = 'dummy';
				$uicols['exchange'][]	 = false;
				$uicols['align'][]		 = '';
				$uicols['datatype'][]	 = 'I';
				$uicols['formatter'][]	 = '';

				$cols_return[]			 = 'location_code';
				$uicols['input_type'][]	 = 'hidden';
				$uicols['name'][]		 = 'location_code';
				$uicols['descr'][]		 = 'dummy';
				$uicols['statustext'][]	 = 'dummy';
				$uicols['exchange'][]	 = false;
				$uicols['align'][]		 = '';
				$uicols['datatype'][]	 = 'V';
				$uicols['formatter'][]	 = '';

				$cols_return[]			 = 'external_id';
				$uicols['input_type'][]	 = 'hidden';
				$uicols['name'][]		 = 'external_id';
				$uicols['descr'][]		 = 'dummy';
				$uicols['statustext'][]	 = 'dummy';
				$uicols['exchange'][]	 = false;
				$uicols['align'][]		 = '';
				$uicols['datatype'][]	 = 'V';
				$uicols['formatter'][]	 = '';

				for ($i = 0; $i < ($type_id); $i++)
				{
					$uicols['input_type'][]	 = 'text';
					$uicols['name'][]		 = "loc{$location_types[$i]['id']}";
					$uicols['descr'][]		 = $location_types[$i]['name'];
					$uicols['statustext'][]	 = $location_types[$i]['descr'];
					$uicols['exchange'][]	 = true;
					$uicols['align'][]		 = 'center';
					$uicols['datatype'][]	 = $i == ($type_id - 1 ) ? 'V' : 'link'; // correct the last one
					$uicols['formatter'][]	 = '';
					$cols					 .= ",fm_location{$type_id}.loc{$location_types[$i]['id']}";
					$cols_return[]			 = "loc{$location_types[$i]['id']}";
				}

				$list_info = $location_types[($type_id - 1)]['list_info'];

				for ($i = 1; $i < ($type_id + 1); $i++)
				{
					if (isset($list_info[$i]) && $list_info[$i])
					{
						$cols					 .= ",fm_location{$i}.loc{$i}_name";
						$cols_return[]			 = "loc{$i}_name";
						$uicols['input_type'][]	 = 'text';
						$uicols['name'][]		 = "loc{$i}_name";
						$uicols['descr'][]		 = $location_types[($i - 1)]['name'] . ' ' . lang('name');
						$uicols['statustext'][]	 = $location_types[($i - 1)]['name'] . ' ' . lang('name');
						$uicols['exchange'][]	 = true;
						$uicols['align'][]		 = 'left';
						$uicols['datatype'][]	 = 'V';
						$uicols['formatter'][]	 = '';
					}
				}

				$joinmethod	 = '';
				$paranthesis = '';
				for ($j = ($type_id - 1); $j > 0; $j--)
				{
					$joinmethod .= " {$this->join} fm_location{$j}";

					$paranthesis .= '(';

					$on = 'ON';
					for ($i = ($j); $i > 0; $i--)
					{
						$joinmethod	 .= " $on (fm_location" . ($j + 1) . ".loc{$i} = fm_location{$j}.loc{$i})";
						$on			 = 'AND';
						if ($i == 1)
						{
							$joinmethod .= ")";
						}
					}
				}

				//			$paranthesis .='(';
				//			$joinmethod .= " {$this->join} fm_part_of_town ON (fm_location1.part_of_town_id = fm_part_of_town.id))";

				$config = $this->soadmin_location->read_config('');

				//_debug_array($config);

				if ($lookup_tenant)
				{
					$cols					 .= ',fm_tenant.id as tenant_id';
					$cols_return[]			 = 'tenant_id';
					$uicols['input_type'][]	 = 'hidden';
					$uicols['name'][]		 = 'tenant_id';
					$uicols['descr'][]		 = 'dummy';
					$uicols['statustext'][]	 = 'dummy';
					$uicols['exchange'][]	 = true;
					$uicols['align'][]		 = '';
					$uicols['datatype'][]	 = 'I';
					$uicols['formatter'][]	 = '';

					$cols					 .= ',fm_tenant.last_name';
					$cols_return[]			 = 'last_name';
					$uicols['input_type'][]	 = 'text';
					$uicols['name'][]		 = 'last_name';
					$uicols['datatype'][]	 = 'V';
					$uicols['descr'][]		 = lang('last name');
					$uicols['statustext'][]	 = lang('last name');
					$uicols['exchange'][]	 = true;
					$uicols['align'][]		 = 'left';
					$uicols['datatype'][]	 = 'V';
					$uicols['formatter'][]	 = '';

					$cols					 .= ',fm_tenant.first_name';
					$cols_return[]			 = 'first_name';
					$uicols['input_type'][]	 = 'text';
					$uicols['name'][]		 = 'first_name';
					$uicols['datatype'][]	 = 'V';
					$uicols['descr'][]		 = lang('first name');
					$uicols['statustext'][]	 = lang('first name');
					$uicols['exchange'][]	 = true;
					$uicols['align'][]		 = 'left';
					$uicols['datatype'][]	 = 'V';
					$uicols['formatter'][]	 = '';

					$cols					 .= ',fm_tenant.contact_phone';
					$cols_return[]			 = 'contact_phone';
					$uicols['input_type'][]	 = 'text';
					$uicols['name'][]		 = 'contact_phone';
					$uicols['datatype'][]	 = 'V';
					$uicols['descr'][]		 = lang('contact phone');
					$uicols['statustext'][]	 = lang('contact phone');
					$uicols['exchange'][]	 = true;
					$uicols['align'][]		 = 'left';
					$uicols['datatype'][]	 = 'V';
					$uicols['formatter'][]	 = '';

					$sub_query_tenant = 1;
					$this->socommon->fm_cache('sub_query_tenant_' . $type_id . '_' . $lookup_tenant . '_' . $lookup, $sub_query_tenant);
				}

				$config_count = count($config);
				for ($i = 0; $i < $config_count; $i++)
				{
					if (($config[$i]['location_type'] <= $type_id) && ($config[$i]['f_key'] == 1))
					{
						if (!$lookup_tenant && $config[$i]['column_name'] == 'tenant_id')
						{

						}
						else
						{
							$joinmethod	 .= " {$this->left_join} {$config[$i]['reference_table']} ON ( fm_location{$config[$i]['location_type']}.{$config[$i]['column_name']} = {$config[$i]['reference_table']}.{$config[$i]['reference_id']}))";
							$paranthesis .= '(';
						}
					}

					if (($config[$i]['location_type'] <= $type_id) && ($config[$i]['query_value'] == 1))
					{

						if ($config[$i]['column_name'] == 'street_id')
						{

							$sub_query_street = 1;
							$this->socommon->fm_cache('sub_query_street_' . $type_id . '_' . $lookup_tenant . '_' . $lookup, $sub_query_street);

							//list address at sublevels beneath address-level
							if ($location_types[($type_id - 1)]['list_address'])
							{
								$cols					 .= ',fm_streetaddress.descr as street_name';
								$cols_return[]			 = 'street_name';
								$uicols['input_type'][]	 = 'text';
								$uicols['name'][]		 = 'street_name';
								$uicols['descr'][]		 = lang('street name');
								$uicols['statustext'][]	 = lang('street name');
								$uicols['exchange'][]	 = true;
								$uicols['align'][]		 = 'left';
								$uicols['datatype'][]	 = 'V';
								$uicols['formatter'][]	 = '';

								$cols					 .= ',street_number';
								$cols_return[]			 = 'street_number';
								$uicols['input_type'][]	 = 'text';
								$uicols['name'][]		 = 'street_number';
								$uicols['descr'][]		 = lang('street number');
								$uicols['statustext'][]	 = lang('street number');
								$uicols['exchange'][]	 = true;
								$uicols['align'][]		 = 'left';
								$uicols['datatype'][]	 = 'V';
								$uicols['formatter'][]	 = '';

								$cols					 .= ',fm_location' . $config[$i]['location_type'] . '.' . $config[$i]['column_name'];
								$cols_return[]			 = $config[$i]['column_name'];
								$uicols['input_type'][]	 = 'hidden';
								$uicols['name'][]		 = $config[$i]['column_name'];
								$uicols['descr'][]		 = lang($config[$i]['input_text']);
								$uicols['statustext'][]	 = lang($config[$i]['input_text']);
								$uicols['exchange'][]	 = true;
								$uicols['align'][]		 = '';
								$uicols['datatype'][]	 = '';
								$uicols['formatter'][]	 = '';
							}
						}
						else
						{
							$cols					 .= ',fm_location' . $config[$i]['location_type'] . '.' . $config[$i]['column_name'];
							$cols_return[]			 = $config[$i]['column_name'];
							$uicols['input_type'][]	 = 'hidden';
							$uicols['name'][]		 = $config[$i]['column_name'];
							$uicols['descr'][]		 = $config[$i]['input_text'];
							$uicols['statustext'][]	 = $config[$i]['input_text'];
							$uicols['exchange'][]	 = true;
							$uicols['align'][]		 = '';
							$uicols['datatype'][]	 = '';
							$uicols['formatter'][]	 = '';
						}
					}
				}

				$this->db->query("SELECT * FROM $attribute_table WHERE (list=1 OR lookup_form=1) AND $attribute_filter");
				while ($this->db->next_record())
				{
					$cols .= ",fm_location{$type_id}." . $this->db->f('column_name');
				}

				$cols .= ",fm_location{$type_id}_category.descr as category_text";

				$joinmethod	 .= " {$this->left_join} fm_location{$type_id}_category ON ( fm_location{$type_id}.category = fm_location{$type_id}_category.id))";
				$paranthesis .= '(';

				if ($filter_role_on_contact)
				{
					$joinmethod	 .= " {$this->join} fm_responsibility_contact ON ( fm_location{$type_id}.location_code = fm_responsibility_contact.location_code))";
					$paranthesis .= '(';
					$joinmethod	 .= " {$this->join} fm_responsibility_role ON ( fm_responsibility_contact.responsibility_role_id = fm_responsibility_role.id))";
					$paranthesis .= '(';

					$cols					 .= ',fm_responsibility_role.id as role_id';
					$cols_return[]			 = 'role_id';
					$uicols['input_type'][]	 = 'hidden';
					$uicols['name'][]		 = 'role_id';
					$uicols['descr'][]		 = 'dummy';
					$uicols['statustext'][]	 = 'dummy';
					$uicols['exchange'][]	 = false;
					$uicols['align'][]		 = '';
					$uicols['datatype'][]	 = '';
					$uicols['formatter'][]	 = '';
				}

				$from = " FROM {$paranthesis} fm_location{$type_id} ";

				$sql = "SELECT $cols $from $joinmethod";

				$this->socommon->fm_cache('sql_' . $type_id . '_lt' . $lookup_tenant . '_l' . $lookup . '_f' . !!$filter_role_on_contact, $sql);
				$this->socommon->fm_cache('uicols_' . $type_id . '_' . $lookup_tenant . '_' . $lookup . '_f' . !!$filter_role_on_contact, $uicols);
				$this->socommon->fm_cache('cols_return_' . $type_id . '_' . $lookup_tenant . '_' . $lookup . '_f' . !!$filter_role_on_contact, $cols_return);
			}
			else
			{
				$uicols		 = (array)$this->socommon->fm_cache('uicols_' . $type_id . '_' . $lookup_tenant . '_' . $lookup . '_f' . !!$filter_role_on_contact);
				$cols_return = (array)$this->socommon->fm_cache('cols_return_' . $type_id . '_' . $lookup_tenant . '_' . $lookup . '_f' . !!$filter_role_on_contact);

				$sub_query_tenant	 = $this->socommon->fm_cache('sub_query_tenant_' . $type_id . '_' . $lookup_tenant . '_' . $lookup);
				$sub_query_street	 = $this->socommon->fm_cache('sub_query_street_' . $type_id . '_' . $lookup_tenant . '_' . $lookup);
			}

			$items_with_control	 = array();
			$filtermethod		 = '';
			$where				 = 'WHERE';
			/**
			 * From old scheme: controller.uicontrol_register_to_location.index
			 */
			if (!$check_for_control && $control_registered && $control_id)
			{
				$sql			 .= "{$this->join} controller_control_location_list ON (fm_location{$type_id}.location_code = controller_control_location_list.location_code )";
				$filtermethod	 .= " $where  controller_control_location_list.control_id = $control_id";
				$where			 = 'AND';
			}// from new scheme controller.uicomponent.index
			else if ($check_for_control)
			{
				$sql_with_control = "SELECT DISTINCT fm_location{$type_id}.id as item_id"
					. " FROM fm_location{$type_id} {$this->join} controller_control_component_list"
					. " ON controller_control_component_list.component_id = fm_location{$type_id}.id"
					. " {$this->join} controller_control_serie"
					. " ON (controller_control_component_list.id = controller_control_serie.control_relation_id"
					. " AND controller_control_serie.control_relation_type = 'component'"
					. " AND controller_control_component_list.location_id = {$location_id}"
					. " AND controller_control_serie.enabled = 1)";

				if ($control_id)
				{
					$sql_with_control .= " WHERE control_id = {$control_id}";
				}

				$this->db->query($sql_with_control);
				$items_with_control = array_merge(array(-1), $filter_item);
				while ($this->db->next_record())
				{
					$items_with_control[] = (int)$this->db->f('item_id');
				}

				/**
				 * reset for later use
				 */
				$filter_item = array();

				if ($control_registered)
				{
					$filtermethod .= " $where fm_location{$type_id}.id IN (" . implode(',', $items_with_control) . ')';
				}
				else
				{
					$sql_without_control = "SELECT DISTINCT fm_location{$type_id}.id as item_id"
						. " FROM fm_location{$type_id} {$this->left_join} controller_control_component_list"
						. " ON controller_control_component_list.component_id = fm_location{$type_id}.id"
						. " {$this->left_join} controller_control_serie"
						. " ON (controller_control_component_list.id = controller_control_serie.control_relation_id"
						. " AND controller_control_serie.control_relation_type = 'component'"
						. " AND controller_control_component_list.location_id = {$location_id}"
						. " AND controller_control_serie.enabled = 1)"
						. " WHERE controller_control_component_list.location_id IS NULL";

					$this->db->query($sql_without_control);
					$items_without_control = array();
					while ($this->db->next_record())
					{
						$items_without_control[] = (int)$this->db->f('item_id');
					}

					$filtermethod .= " $where fm_location{$type_id}.id IN (" . implode(',', array_merge($items_without_control, $items_with_control)) . ')';
				}

				$where = 'AND';
			}
			//---------------------start custom user cols

			$user_columns	 = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['location_columns_' . $type_id . !!$lookup]) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['location_columns_' . $type_id . !!$lookup] : array();
			$_user_columns	 = array();
			foreach ($user_columns as $user_column_id)
			{
				if (ctype_digit($user_column_id))
				{
					$_user_columns[] = $user_column_id;
				}
			}
			$user_column_filter = '';
			if ($_user_columns)
			{
				$user_column_filter = " OR ($attribute_filter AND id IN (" . implode(',', $_user_columns) . '))';
			}

			$attribute_info = array();

			if($lookup_tenant)
			{
				$this->db->query("SELECT DISTINCT * FROM $attribute_table WHERE lookup_form=1 AND $attribute_filter $user_column_filter ORDER BY attrib_sort ASC");
			}
			else
			{
				$this->db->query("SELECT DISTINCT * FROM $attribute_table WHERE (list=1 OR lookup_form=1) AND $attribute_filter $user_column_filter ORDER BY attrib_sort ASC");
			}

				$i = count($uicols['name']);

				while ($this->db->next_record())
				{
					$attribute_info[] = array(
						'column_name'	 => $this->db->f('column_name'),
						'lookup_form'	 => $this->db->f('lookup_form'),
						'list'			 => $this->db->f('list'),
						'input_text'	 => $this->db->f('input_text', true),
						'statustext'	 => $this->db->f('statustext', true),
						'datatype'		 => $this->db->f('datatype'),
						'id'			 => $this->db->f('id')
					);
				}
			


			$locations			 = $this->soadmin_location->select_location_type();

			foreach ($attribute_info as $_attribute_info)
			{

				$input_type = 'text';
				if ($_attribute_info['lookup_form'] == 1 && $_attribute_info['list'] != 1)
				{
					$input_type	 = 'hidden';
					$exchange	 = true;
				}
				else if ($_attribute_info['lookup_form'] == 1)
				{
					$exchange = true;
				}
				else
				{
					$input_type	 = 'text';
					$exchange	 = false;
				}

				//hack...
				if($type_id < count($locations))
				{
					$exchange	 = false;
				}

				$uicols['input_type'][]			 = $input_type;
				$uicols['name'][]				 = $_attribute_info['column_name'];
				$uicols['descr'][]				 = $_attribute_info['input_text'];
				$uicols['statustext'][]			 = $_attribute_info['statustext'];
				$uicols['datatype'][$i]			 = $_attribute_info['datatype'];
				$uicols['formatter'][]			 = '';
				$uicols['exchange'][]			 = $exchange;
				$uicols['cols_return_extra'][$i] = array
					(
					'name'		 => $_attribute_info['column_name'],
					'datatype'	 => $_attribute_info['datatype'],
					'attrib_id'	 => $_attribute_info['id']
				);

				//TODO: move alignment to ui
				switch ($_attribute_info['datatype'])
				{
					case 'V':
					case 'C':
					case 'N':
						$uicols['align'][]	 = 'left';
						break;
					case 'D':
					case 'I':
						$uicols['align'][]	 = 'right';
						break;
					default:
						$uicols['align'][]	 = 'center';
				}

				if ($_attribute_info['column_name'] == 'category')
				{
					$i++;
					$cols							 .= ",fm_location{$type_id}_category.descr as category_text";
					$uicols['input_type'][]			 = 'text';
					$uicols['name'][]				 = 'category_text';
					$uicols['descr'][]				 = $_attribute_info['input_text'] . ' ' . lang('name');
					$uicols['statustext'][]			 = '';
					$uicols['datatype'][$i]			 = 'V';
					$uicols['formatter'][]			 = '';
					$uicols['exchange'][]			 = false;
					$uicols['align'][]				 = 'right';
					$uicols['cols_return_extra'][$i] = array();
				}
				$i++;
			}

			//---------------------end custom user cols

			$this->uicols = $uicols;


			if ($order && !$order == 'fm_location1.loc1')
			{
				$ordermethod = " ORDER BY {$order} {$sort}";
			}
			else
			{
				$ordermethod = " ORDER BY fm_location{$type_id}.loc1 {$sort}";

				if ($type_id > 1)
				{
					for ($i = 2; $i < ($type_id + 1); $i++)
					{
						$ordermethod .= ",fm_location{$type_id}.loc{$i} ASC";
					}
				}
			}


			$GLOBALS['phpgw']->config->read();
			if (isset($GLOBALS['phpgw']->config->config_data['acl_at_location']) && $GLOBALS['phpgw']->config->config_data['acl_at_location'])
			{
				$access_location = $this->bocommon->get_location_list(PHPGW_ACL_READ);
				$filtermethod	 = " WHERE fm_location{$type_id}.loc1 in ('" . implode("','", $access_location) . "')";
				$where			 = 'AND';
			}

			if ($cat_id)
			{
				if (is_array($cat_id))
				{
					foreach ($cat_id as &$_cat_id)
					{
						$_cat_id = $this->db->db_addslashes($_cat_id);
					}

					$filtermethod .= " {$where} fm_location{$type_id}.category IN ('" . implode("','", $cat_id) . "')";
				}
				else
				{
					$cat_id			 = $this->db->db_addslashes($cat_id);
					$filtermethod	 .= " {$where} fm_location{$type_id}.category='{$cat_id}'";
				}
				$where = 'AND';
			}
			else
			{
				$filtermethod	 .= " $where  (fm_location{$type_id}.category !='99' OR fm_location{$type_id}.category IS NULL)";
				$where			 = 'AND';
			}

			if ($filter_item)
			{
				$filtermethod	 .= " {$where} fm_location{$type_id}.id IN (" . implode(', ', $filter_item) . ')';
				$where			 = 'AND';
			}
			if ($filter > 0)
			{
				//cramirez.r@ccfirst.com 16/09/08 	validacion is added to avoid notice
				if (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['property_filter']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['property_filter'] == 'owner')
				//if($GLOBALS['phpgw_info']['user']['preferences']['property']['property_filter'] == 'owner')
				{
					$filtermethod .= " $where fm_owner.id='$filter' ";
				}
				else
				{
					$filtermethod .= " $where fm_owner.category='$filter' ";
				}
				$where = 'AND';
			}


			if ($status > 0)
			{
				$filtermethod	 .= " $where fm_location{$type_id}.status = {$status} ";
				$where			 = 'AND';
			}
			else
			{
				//				$filtermethod .= " $where fm_location" . ($type_id). ".status IS NULL ";
				//				$filtermethod .= " $where fm_location" . ($type_id). ".status !=2 ";
				//				$where= 'AND';
			}

			if ($district_id > 0)
			{
				$filtermethod	 .= " $where fm_part_of_town.district_id='$district_id' ";
				$where			 = 'AND';
			}

			if ($part_of_town_id > 0)
			{
				$filtermethod	 .= " $where fm_part_of_town.id='$part_of_town_id' ";
				$where			 = 'AND';
			}

			if ($location_code)
			{
				$filtermethod	 .= " {$where} fm_location{$type_id}.location_code $this->like '{$location_code}%'";
				$where			 = 'AND';
			}

			if ($filter_role_on_contact && $role_id)
			{
				$time			 = time() + 1;
				$filtermethod	 .= " {$where} active_from < {$time} AND (active_to > {$time} OR active_to = 0)"
					. " AND expired_on IS NULL AND fm_responsibility_role.id ={$role_id} AND contact_id = {$filter_role_on_contact}";
				$where			 = 'AND';
			}
			else if ($filter_role_on_contact)
			{
				$time			 = time() + 1;
				$filtermethod	 .= " {$where} active_from < {$time} AND (active_to > {$time} OR active_to = 0)"
					. " AND expired_on IS NULL AND contact_id = {$filter_role_on_contact}";
				$where			 = 'AND';
			}

			$_querymethod			 = array();
			$__querymethod			 = array();
			$_joinmethod_datatype	 = array();

			if ($query)
			{
				$query	 = $this->db->db_addslashes($query);
				$query	 = str_replace(",", '.', $query);
				if (stristr($query, '.'))
				{
					$query_part		 = explode(".", $query);
					$_querymethod[]	 = "(fm_location{$type_id}.loc1='{$query_part[0]}' AND fm_location{$type_id}.loc{$type_id}='{$query_part[1]}')";
				}
				//		else
				{
					$sub_query = '';

					if ($sub_query_tenant)
					{
						$sub_query .= " OR concat(fm_tenant.first_name || ' ' || fm_tenant.last_name) $this->like '$query%'";
						$sub_query .= " OR concat(fm_tenant.last_name || ' ' || fm_tenant.first_name) $this->like '$query%'";
						$sub_query .= " OR fm_tenant.contact_phone $this->like '%$query%'";
					}

					if ($sub_query_street)
					{
						$sub_query .= " OR concat(fm_streetaddress.descr || ' ' || street_number) $this->like '$query%'";
					}

					$query_name = '';
					for ($i = 1; $i < ($type_id + 1); $i++)
					{
						$query_name .= "OR loc{$i}_name {$this->like} '%{$query}%'";
					}

					if (!$criteria_id)
					{
						$_querymethod[] = " (fm_location{$type_id}.loc1 {$this->like} '%{$query}%' {$sub_query} OR fm_location{$type_id}.location_code {$this->like} '%{$query}%' {$query_name})";
					}
					else
					{
						$__querymethod = array("fm_location{$type_id}.loc1 = '-1'"); // block query waiting for criteria
					}

					$this->db->query("SELECT * FROM $attribute_table WHERE $attribute_filter AND search='1'");

					while ($this->db->next_record())
					{
						switch ($this->db->f('datatype'))
						{
							case 'V':
							case 'email':
							case 'CH':
								if (!$criteria_id)
								{
									$_querymethod[]	 = "fm_location{$type_id}." . $this->db->f('column_name') . " {$this->like} '%{$query}%'";
									$__querymethod	 = array(); // remove block
								}
								break;
							case 'R':
							case 'LB':
								if (!$criteria_id)
								{
									$_joinmethod_datatype[] = "{$this->join} phpgw_cust_choice ON (fm_location{$type_id}." . $this->db->f('column_name') . " = phpgw_cust_choice.id"
										. " AND phpgw_cust_choice.location_id =" . (int)$this->db->f('location_id')
										. " AND phpgw_cust_choice.attrib_id =" . (int)$this->db->f('id') . ')';

									$_querymethod[] = "(phpgw_cust_choice.location_id =" . (int)$this->db->f('location_id')
										. " AND phpgw_cust_choice.attrib_id =" . (int)$this->db->f('id')
										. " AND phpgw_cust_choice.value {$this->like} '%{$query}%')";

									$__querymethod = array(); // remove block
								}
								break;
							case 'I':
								if (ctype_digit($query) && !$criteria_id)
								{
									$_querymethod[]	 = "fm_location{$type_id}." . $this->db->f('column_name') . " = " . (int)$query;
									$__querymethod	 = array(); // remove block
								}
								break;
							case 'VENDOR':
								if ($criteria_id == 'vendor')
								{
									$_joinmethod_datatype[]	 = "{$this->join} fm_vendor ON (fm_location{$type_id}." . $this->db->f('column_name') . " = fm_vendor.id AND fm_vendor.org_name {$this->like} '%{$query}%') ";
									$__querymethod			 = array(); // remove block
								}
								break;
							case 'AB':
								if ($criteria_id == 'ab')
								{
									$_joinmethod_datatype[]	 = "{$this->join} phpgw_contact_person ON (fm_location{$type_id}." . $this->db->f('column_name') . " = pphpgw_contact_person.person_id AND (phpgw_contact_person.first_name {$this->like} '%{$query}%' OR phpgw_contact_person.last_name {$this->like} '%{$query}%'))";
									$__querymethod			 = array(); // remove block
								}
								break;
							case 'ABO':
								if ($criteria_id == 'abo')
								{
									$_joinmethod_datatype[]	 = "{$this->join} phpgw_contact_org ON (fm_location{$type_id}." . $this->db->f('column_name') . " = phpgw_contact_org.org_id AND phpgw_contact_org.name {$this->like} '%{$query}%')";
									$__querymethod			 = array(); // remove block
								}
								break;
							default:
								if (!$criteria_id)
								{
									$_querymethod[]	 = "fm_location{$type_id}." . $this->db->f('column_name') . " = '{$query}'";
									$__querymethod	 = array(); // remove block
								}
						}
					}
				}
				$where = 'AND';
			}

			foreach ($_joinmethod_datatype as $_joinmethod)
			{
				$sql .= $_joinmethod;
			}

			$querymethod = '';

			$_querymethod = array_merge($__querymethod, $_querymethod);
			if ($_querymethod)
			{
				$querymethod = " $where (" . implode(' OR ', $_querymethod) . ')';
				unset($_querymethod);
			}

			$sql .= "$filtermethod $querymethod";
//			_debug_array($sql);
			$values = array();
//			$this->db->query('SELECT count(*) AS cnt ' . substr($sql,strripos($sql,' from')),__LINE__,__FILE__);
//			$this->db->next_record();
//			$this->total_records = $this->db->f('cnt');

			$cache_info = phpgwapi_cache::session_get('property', "location{$type_id}_listing_metadata");

			if (!isset($cache_info['sql_hash']) || $cache_info['sql_hash'] != md5($sql))
			{
				$cache_info = array();
			}

			if (!$cache_info)
			{
				$this->db->query('SELECT count(*) AS cnt ' . substr($sql, strripos($sql, ' from')), __LINE__, __FILE__);
				$this->db->next_record();

				$cache_info = array
					(
					'total_records'	 => $this->db->f('cnt'),
					'sql_hash'		 => md5($sql)
				);
				phpgwapi_cache::session_set('property', "location{$type_id}_listing_metadata", $cache_info);
			}

			$this->total_records = $cache_info['total_records'];

			//cramirez.r@ccfirst.com 23/07/08 avoid retrieve data in first time, only render definition for headers (var myColumnDefs)
			if ($dry_run)
			{
				return array();
			}
//			_debug_array($sql);
			if (!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod, $start, __LINE__, __FILE__, $results);
			}
			else
			{

				$_fetch_single = false;
				/*
				  if($this->total_records > 200)
				  {
				  $_fetch_single = true;
				  }
				  else
				  {
				  $_fetch_single = false;
				  }
				 */
				$this->db->query($sql . $ordermethod, __LINE__, __FILE__, false, $_fetch_single);
				unset($_fetch_single);
			}

			$j = 0;

			$location_count = $type_id - 1;

			$cols_return = $uicols['name'];

			if ($additional_fields)
			{
				foreach ($additional_fields as $additional_field)
				{
					if (!in_array($additional_field, $cols_return))
					{
						$cols_return[]			 = $additional_field;
						$uicols['datatype'][]	 = '';
					}
				}
			}

			$dataset = array();
			while ($this->db->next_record())
			{
				foreach ($cols_return as $key => $field)
				{
					$dataset[$j][$field] = array
						(
						'value'		 => $this->db->f($field, true),
						'datatype'	 => $uicols['datatype'][$key],
						'attrib_id'	 => isset($uicols['cols_return_extra'][$key]['attrib_id']) ? $uicols['cols_return_extra'][$key]['attrib_id'] : ''
					);
				}
				$j++;
			}

			$this->db->set_fetch_single(false);

			$values = $this->custom->translate_value($dataset, $location_id, $location_count);

			if ($check_for_control)
			{
				foreach ($values as &$value)
				{
					$value['has_control'] = !!in_array($value['id'], $items_with_control);
				}
			}

			return $values;
		}

		function generate_sql( $type_id, $cols = '', $cols_return = '', $uicols = '', $read_single = '' )
		{
			$joinmethod = " fm_location{$type_id}";

			$location_types = $this->soadmin_location->select_location_type();

			//		$cols .= "fm_location" . ($type_id) .".location_code";
			$cols_return[] = 'location_code';
			for ($i = 0; $i < ($type_id); $i++)
			{
				$uicols['input_type'][]	 = 'text';
				$uicols['name'][]		 = 'loc' . $location_types[$i]['id'];
				$uicols['descr'][]		 = $location_types[$i]['name'];
				$uicols['statustext'][]	 = $location_types[$i]['descr'];
				//			$cols 						.= ",fm_location" . ($type_id) .".loc" . $location_types[$i]['id'];
				$cols_return[]			 = 'loc' . $location_types[$i]['id'];
			}


			if ($type_id != 1)
			{
//				$cols.= ',fm_location1.loc1_name as loc1_name';
//				$cols_return[] 				= 'loc1_name';
				$uicols['input_type'][]	 = 'text';
				$uicols['name'][]		 = 'loc1_name';
				$uicols['descr'][]		 = lang('Property Name');
				$uicols['statustext'][]	 = lang('Property Name');
			}

			$paranthesis = '';
			for ($j = ($type_id - 1); $j > 0; $j--)
			{
				$joinmethod .= " $this->join fm_location" . ($j);

				$paranthesis .= '(';

				$on = 'ON';
				for ($i = ($j); $i > 0; $i--)
				{
					$joinmethod	 .= " {$on} (fm_location" . ($j + 1) . ".loc{$i}  = fm_location{$j}.loc{$i})";
					$on			 = 'AND';
					if ($i == 1)
					{
						$joinmethod .= ")";
					}
				}
			}

			$config = $this->soadmin_location->read_config('');

			$config_count = count($config);
			for ($i = 0; $i < $config_count; $i++)
			{
				if (($config[$i]['location_type'] <= $type_id) && ($config[$i]['f_key'] == 1))
				{
					if ($config[$i]['column_name'] == 'tenant_id' || $config[$i]['column_name'] == 'street_id')
					{
						$join = $this->left_join;
					}
					else
					{
						$join = $this->join;
					}

					$joinmethod .= " $join  " . $config[$i]['reference_table'] . " ON ( fm_location" . $config[$i]['location_type'] . "." . $config[$i]['column_name'] . "=" . $config[$i]['reference_table'] . "." . $config[$i]['reference_id'] . "))";

					$paranthesis .= '(';
				}

				if ($config[$i]['location_type'] <= $type_id)
				{

					if ($config[$i]['column_name'] == 'street_id')
					{
						$cols					 .= ',fm_streetaddress.descr as street_name';
						$cols_return[]			 = 'street_name';
						$uicols['input_type'][]	 = 'text';
						$uicols['name'][]		 = 'street_name';
						$uicols['descr'][]		 = lang('street name');
						$uicols['statustext'][]	 = lang('street name');

						$cols					 .= ',street_number';
						$cols_return[]			 = 'street_number';
						$uicols['input_type'][]	 = 'text';
						$uicols['name'][]		 = 'street_number';
						$uicols['descr'][]		 = lang('street number');
						$uicols['statustext'][]	 = lang('street number');

						$cols					 .= ',fm_location' . $config[$i]['location_type'] . '.' . $config[$i]['column_name'];
						$cols_return[]			 = $config[$i]['column_name'];
						$uicols['input_type'][]	 = 'hidden';
						$uicols['name'][]		 = $config[$i]['column_name'];
						$uicols['descr'][]		 = lang($config[$i]['input_text']);
						$uicols['statustext'][]	 = lang($config[$i]['input_text']);
					}
					else if ($config[$i]['column_name'] == 'tenant_id')
					{
						$cols					 .= ',fm_tenant.id as tenant_id';
						$cols_return[]			 = 'tenant_id';
						$uicols['input_type'][]	 = 'hidden';
						$uicols['name'][]		 = 'tenant_id';
						$uicols['descr'][]		 = 'dummy';
						$uicols['statustext'][]	 = 'dummy';

						$cols					 .= ',fm_tenant.last_name as last_name';
						$cols_return[]			 = 'last_name';
						$uicols['input_type'][]	 = 'text';
						$uicols['name'][]		 = 'last_name';
						$uicols['descr'][]		 = lang('last name');
						$uicols['statustext'][]	 = lang('last name');

						$cols					 .= ',fm_tenant.first_name as first_name';
						$cols_return[]			 = 'first_name';
						$uicols['input_type'][]	 = 'text';
						$uicols['name'][]		 = 'first_name';
						$uicols['descr'][]		 = lang('first name');
						$uicols['statustext'][]	 = lang('first name');

						$cols					 .= ',contact_phone';
						$cols_return[]			 = 'contact_phone';
						$uicols['input_type'][]	 = 'text';
						$uicols['name'][]		 = 'contact_phone';
						$uicols['descr'][]		 = lang('contact phone');
						$uicols['statustext'][]	 = lang('contact phone');
					}
					else
					{
						$cols					 .= ',fm_location' . $config[$i]['location_type'] . '.' . $config[$i]['column_name'];
						$cols_return[]			 = $config[$i]['column_name'];
						$uicols['input_type'][]	 = 'text';
						$uicols['name'][]		 = $config[$i]['column_name'];
						$uicols['descr'][]		 = $config[$i]['input_text'];
						$uicols['statustext'][]	 = $config[$i]['input_text'];
					}
				}
			}

			$custom = createObject('property.custom_fields');

			$fm_location_cols = $custom->find('property', '.location.' . $type_id, 0, '', '', '', true);
			//_debug_array($fm_location_cols);

			if ($read_single)
			{
				$cols .= ",fm_location{$type_id}.*";
				foreach ($fm_location_cols as $location_col)
				{
					if ($location_col['lookup_form'] == 1)
					{
						$cols_return[] = $location_col['column_name'];
					}
				}
			}
			else
			{
				foreach ($fm_location_cols as $location_col)
				{
					if ($location_col['list'] == 1)
					{
						$cols					 .= ",fm_location" . ($type_id) . "." . $location_col['column_name'];
						$cols_return[]			 = $location_col['column_name'];
						$uicols['input_type'][]	 = 'text';
						$uicols['name'][]		 = $location_col['column_name'];
						$uicols['descr'][]		 = $location_col['input_text'];
						$uicols['statustext'][]	 = $location_col['statustext'];
					}
				}
			}


			$cols			 .= ',district_id';
			$cols_return[]	 = 'district_id';

			$this->uicols		 = $uicols;
			$this->cols_return	 = $cols_return;

			$from = " FROM $paranthesis ";

			$sql = "SELECT $cols $from $joinmethod";

			$this->socommon->fm_cache('sql_single_' . $type_id, $sql);
			$this->socommon->fm_cache('uicols_single_' . $type_id, $uicols);
			$this->socommon->fm_cache('cols_return_single_' . $type_id, $cols_return);

			return $sql;
		}

		function read_single( $location_code = '', $values = array(), $skip_attribs = false )
		{
			//cache result
			static $location = array();

			$location_array	 = explode('-', $location_code);
			$type_id		 = count($location_array);

			$location_id = !empty($values['location_id']) ? (int)$values['location_id'] : null;

			/**
			 * Override
			 */
			if ($location_id)
			{
				$location_info = $GLOBALS['phpgw']->locations->get_name($location_id);

				if (substr($location_info['location'], 1, 8) == 'location')
				{
					$type_info	 = explode('.', $location_info['location']);
					$type_id	 = $type_info[2];
				}
				$id = !empty($values['id']) ? (int)$values['id'] : null;
				if (!$id && !$location_code)
				{
					$type_id = false;
				}
			}

			if (!$type_id)
			{
				return;
			}

			if (isset($location[$location_code]))
			{
				return $location[$location_code];
			}

			if (!$skip_attribs && (!isset($values['attributes']) || !$values['attributes']))
			{
				$values['attributes'] = $this->custom->find('property', '.location.' . $type_id, 0, '', 'ASC', 'attrib_sort', true, true);
			}
			$metadata	 = $this->db->metadata("fm_location{$type_id}");
			//In case old version of property-app
			$cols		 = '';
			$cols_return = array();

			if (isset($metadata['id']))
			{
				$cols			 = "fm_location{$type_id}.id,";
				$cols_return[]	 = 'id';
			}

			$cols			 .= "fm_location{$type_id}.external_id";
			$cols_return[]	 = 'external_id';

			$cols			 .= ",fm_location{$type_id}.category as cat_id";
			$cols			 .= ",fm_location{$type_id}_category.descr as category_name";
			$cols_return[]	 = 'cat_id';
			$cols_return[]	 = 'category_name';

			for ($i = 1; $i < ($type_id); $i++)
			{
				$cols			 .= ',fm_location' . $i . '.loc' . $i . '_name';
				$cols_return[]	 = 'loc' . $i . '_name';
			}
			$cols_return[] = 'loc' . $type_id . '_name';

			//	$cols.= 'fm_location' . $type_id . '.change_type,';
			$cols_return[]			 = 'change_type';
			$uicols['input_type'][]	 = 'text';
			$uicols['name'][]		 = 'loc' . $type_id . '_name';
			$uicols['descr'][]		 = lang('name');
			$uicols['statustext'][]	 = lang('name');

			//	$cols.= 'fm_location' . $type_id .'.remark as remark,';
			$cols_return[]			 = 'remark';
			$uicols['input_type'][]	 = 'text';
			$uicols['name'][]		 = 'descr';
			$uicols['descr'][]		 = lang('remark');
			$uicols['statustext'][]	 = lang('remark');

			$sql = $this->socommon->fm_cache('sql_single_' . $type_id);

			if (!$sql)
			{
				$sql = $this->generate_sql($type_id, $cols, $cols_return, $uicols, true);
			}
			else
			{

				$this->uicols		 = $this->socommon->fm_cache('uicols_single_' . $type_id);
				$this->cols_return	 = $this->socommon->fm_cache('cols_return_single_' . $type_id);
			}

			//FIXME: Make sure all locations are linked to a valid category
			$sql .= " {$this->left_join} fm_location{$type_id}_category ON (fm_location{$type_id}.category = fm_location{$type_id}_category.id)";

			if ($location_code)
			{
				$sql .= " WHERE fm_location{$type_id}.location_code='{$location_code}' ";
			}
			else
			{
				$sql .= " WHERE fm_location{$type_id}.id=" . (int)$id;
			}

			$this->db->query($sql, __LINE__, __FILE__);

//			_debug_array($sql);
			$cols_return = $this->cols_return;

			if ($this->db->next_record())
			{
				foreach ($cols_return as $col)
				{
					$values[$col] = $this->db->f($col, true);
				}

				if (isset($values['attributes']) && is_array($values['attributes']))
				{
					foreach ($values['attributes'] as &$attr)
					{
						$attr['value'] = $this->db->f($attr['column_name'], true);
						if ($attr['lookup_form'])
						{
							$values[$attr['column_name']] = $attr['value'];
						}
					}
				}

				if (!$location_code)
				{
					$location_code = $this->db->f('location_code');
				}
			}

			/**
			 * Loop on add...
			 */
//			if(empty($values['id']))
//			{
//				//In case old version of property-app
//				if(isset($metadata['id']))
//				{
//					$this->update_location();
//				}
//			}

			$location[$location_code] = $values;
			return $values;
		}

		function add( $location, $values_attribute = array(), $type_id = '' )
		{
			$receipt = array();
			foreach ($location as $input_name => $value)
			{
				if ($value)
				{
					if ($input_name == 'cat_id')
					{
						$input_name = 'category';
					}
					$cols[]	 = $input_name;
					$vals[]	 = $value;
				}
			}

			if (isset($values_attribute) AND is_array($values_attribute))
			{
				foreach ($values_attribute as $entry)
				{
					if ($entry['value'])
					{
						if ($entry['datatype'] == 'C' || $entry['datatype'] == 'T' || $entry['datatype'] == 'V' || $entry['datatype'] == 'link')
						{
							$entry['value'] = $this->db->db_addslashes($entry['value']);
						}

						$cols[]	 = $entry['name'];
						$vals[]	 = $entry['value'];
					}
				}
			}

			$cols[]	 = 'user_id';
			$cols[]	 = 'entry_date';
			$cols[]	 = 'modified_by';
			$cols[]	 = 'modified_on';
			$vals[]	 = $this->account;
			$vals[]	 = time();
			$vals[]	 = $this->account;
			$vals[]	 = date($this->db->datetime_format(), time());

			$cols	 = implode(",", $cols);
			$vals	 = "'" . implode("','", $vals) . "'";

			$this->db->transaction_begin();
			$sql = "INSERT INTO fm_location{$type_id} ($cols) VALUES ($vals)";

			//echo $sql;
			$this->db->query($sql, __LINE__, __FILE__);

			$sql = "INSERT INTO fm_locations (level, location_code, loc1) VALUES ({$type_id}, '{$location['location_code']}', '{$location['loc1']}')";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->update_location_name($location['location_code']);

			$id = $this->get_item_id($location['location_code']);
			$this->db->query("UPDATE fm_location{$type_id} SET id = {$id} WHERE location_code = '{$location['location_code']}'", __LINE__, __FILE__);

			$this->db->transaction_commit();
			$receipt['message'][] = array('msg' => lang('Location %1 has been saved', $location['location_code']));

			$GLOBALS['phpgw']->config->read();
			// Keep it at level 1 for the moment
			if (isset($GLOBALS['phpgw']->config->config_data['acl_at_location']) && $GLOBALS['phpgw']->config->config_data['acl_at_location'] && $type_id == 1)
			{
				$acl_location = ".location.{$type_id}." . str_replace("-", '.', $location['location_code']);
				if (!$GLOBALS['phpgw']->locations->get_id('property', $acl_location))
				{
					$GLOBALS['phpgw']->locations->add($acl_location, $location["loc{$type_id}_name"], 'property');
				}
			}

			$receipt['location_code'] = $location['location_code'];
			return $receipt;
		}

		function edit( $location, $values_attribute = array(), $type_id = '' )
		{
			$receipt = array();

			if (is_array($location))
			{
				foreach ($location as $input_name => $value)
				{
					if ($value)
					{
						if ($input_name == 'cat_id')
						{
							$input_name = 'category';
						}
						$value_set[$input_name] = $this->db->db_addslashes($value);
					}
				}
			}

			if (isset($values_attribute) AND is_array($values_attribute))
			{
				foreach ($values_attribute as $entry)
				{
					if ($entry['datatype'] == 'C' || $entry['datatype'] == 'T' || $entry['datatype'] == 'V' || $entry['datatype'] == 'link')
					{
						$entry['value'] = $this->db->db_addslashes($entry['value']);
					}
					$value_set[$entry['name']] = $entry['value'];
				}
			}

			$value_set['modified_by']	 = $this->account;
			$value_set['modified_on']	 = date($this->db->datetime_format(), time());

			$value_set = $this->db->validate_update($value_set);

			$sql = "SELECT * FROM fm_location{$type_id} WHERE location_code ='{$location['location_code']}'";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();

			$metadata = $this->db->metadata('fm_location' . $type_id);
			if (isset($this->db->adodb))
			{
				$i = 0;
				foreach ($metadata as $key => $val)
				{
					$metadata_temp[$i]['name'] = $key;
					$i++;
				}
				$metadata = $metadata_temp;
				unset($metadata_temp);
			}

			for ($i = 0; $i < count($metadata); $i++)
			{
				$cols[] = $metadata[$i]['name'];

				if (ctype_digit($this->db->f($metadata[$i]['name'])))
				{
					$vals[] = $this->db->f($metadata[$i]['name']);
				}
				else
				{
					$vals[] = $this->db->db_addslashes($this->db->f($metadata[$i]['name'], true));
				}
			}

			$cols[]	 = 'exp_date';
			$vals[]	 = date($this->db->datetime_format(), time());

			$cols	 = implode(",", $cols);
			$vals	 = $this->db->validate_insert($vals);

			$this->db->transaction_begin();
			$sql = "INSERT INTO fm_location{$type_id}_history ($cols) VALUES ($vals)";
			$this->db->query($sql, __LINE__, __FILE__);

			$sql = "UPDATE fm_location{$type_id} SET {$value_set} WHERE location_code='{$location['location_code']}'";
			$this->db->query($sql, __LINE__, __FILE__);

			$this->update_location_name($location['location_code']);

			$this->db->transaction_commit();
			$receipt['message'][]		 = array('msg' => lang('Location %1 has been edited', $location['location_code']));
			$receipt['location_code']	 = $location['location_code'];
			return $receipt;
		}

		function delete( $location_code )
		{
			$location_array	 = explode('-', $location_code);
			$type_id		 = count($location_array);
			$this->db->transaction_begin();
			$this->db->query("DELETE FROM fm_location$type_id WHERE location_code='{$location_code}'", __LINE__, __FILE__);
			$this->db->query("DELETE FROM fm_locations WHERE location_code='{$location_code}'", __LINE__, __FILE__);
			$this->db->transaction_commit();
		}

		function update_cat()
		{
			$location_types = $this->soadmin_location->select_location_type();

			$m = count($location_types);

			$this->db->transaction_begin();

			$this->db->query("UPDATE fm_location" . $m . " set	status= 2  WHERE category='99'", __LINE__, __FILE__);

			for ($type_id = $m; $type_id > 1; $type_id--)
			{
				$parent_table = 'fm_location' . ($type_id - 1);

				$joinmethod .= " $this->join $parent_table";

				$paranthesis .= '(';

				$on = 'ON';
				for ($i = ($type_id - 1); $i > 0; $i--)
				{
					$joinmethod	 .= " $on (fm_location" . ($type_id) . ".loc" . ($i) . ' = ' . $parent_table . ".loc" . ($i) . ")";
					$on			 = 'AND';
					if ($i == 1)
					{
						$joinmethod .= ")";
					}
				}

				$sql = "SELECT $parent_table.location_code ,count(*) as count_99  FROM $paranthesis fm_location$type_id $joinmethod where fm_location$type_id.status=2 group by $parent_table.location_code ";
				$this->db->query($sql, __LINE__, __FILE__);

				while ($this->db->next_record())
				{
					$outdated[$this->db->f('location_code')]['count_99'] = $this->db->f('count_99');
				}

				$sql = "SELECT $parent_table.location_code ,count(*) as count_all  FROM $paranthesis fm_location$type_id $joinmethod group by $parent_table.location_code ";
				$this->db->query($sql, __LINE__, __FILE__);
				while ($this->db->next_record())
				{
					if ($outdated[$this->db->f('location_code')]['count_99'] == $this->db->f('count_all'))
					{
						$update[] = array('location_code' => $this->db->f('location_code'));
					}
				}

				$j = 0;
				for ($i = 0; $i < count($update); $i++)
				{

					$sql = "SELECT status  FROM $parent_table WHERE location_code= '" . $update[$i]['location_code'] . "'";

					$this->db->query($sql, __LINE__, __FILE__);
					$this->db->next_record();

					if ($this->db->f('status') != 2)
					{
						$j++;
					}

					$this->db->query("UPDATE fm_location" . ($type_id - 1) . " SET"
						. " status= 2,"
						. " category = '99'"
						. " WHERE location_code= '{$update[$i]['location_code']}'", __LINE__, __FILE__);
				}

				$receipt['message'][] = array('msg' => lang('%1 location %2 has been updated to not active of %3 already not active', $j, $location_types[($type_id - 2)]['descr'], count($update)));

				unset($outdated);
				unset($update);
				unset($joinmethod);
				unset($paranthesis);
			}

			$this->db->transaction_commit();

			return $receipt;
		}

		function update_location()
		{
			if ($this->db->get_transaction())
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}
			set_time_limit(0);

			$this->db->query('SELECT max(id) as levels FROM fm_location_type');
			$this->db->next_record();
			$levels = $this->db->f('levels');

			//perform an update on all location_codes on all levels to make sure they are consistent and unique
			/* 			$locations = array();
			  for ($level = 1; $level < ($levels + 1); $level++)
			  {
			  $sql = "SELECT * from fm_location{$level}";
			  $this->db->query($sql, __LINE__, __FILE__);
			  $i = 0;
			  while ($this->db->next_record())
			  {
			  $location_code = array();
			  $where = 'WHERE';
			  $locations[$level][$i]['condition'] = '';
			  for ($j = 1; $j < ($level + 1); $j++)
			  {
			  $loc = $this->db->f("loc{$j}");
			  $location_code[] = $loc;
			  $locations[$level][$i]['condition'] .= "$where loc{$j}='{$loc}'";
			  $where = 'AND';
			  }
			  $locations[$level][$i]['new_values']['location_code'] = implode('-', $location_code);
			  $i++;
			  }
			  }

			  foreach ($locations as $level => $location_at_level)
			  {
			  foreach ($location_at_level as $location)
			  {
			  $sql = "UPDATE fm_location{$level} SET location_code = '{$location['new_values']['location_code']}' {$location['condition']}";
			  $this->db->query($sql, __LINE__, __FILE__);
			  }
			  }
			 */
			$locations = array();
			for ($i = 1; $i < ($levels + 1); $i++)
			{
				$this->db->query("SELECT fm_location{$i}.location_code, fm_location{$i}.loc1 FROM fm_location{$i} {$this->left_join} fm_locations ON fm_location{$i}.location_code = fm_locations.location_code WHERE fm_locations.location_code IS NULL");
				while ($this->db->next_record())
				{
					$locations[] = array
						(
						'level'			 => $i,
						'location_code'	 => $this->db->f('location_code'),
						'loc1'			 => $this->db->f('loc1')
					);
				}
			}

			$receipt = array();
			foreach ($locations as $location)
			{
				$this->db->query("INSERT INTO fm_locations (level, location_code, loc1) VALUES ({$location['level']}, '{$location['location_code']}', '{$location['loc1']}')");

				$receipt['message'][] = array('msg' => lang('location %1 added at level %2', $location['location_code'], $location['level']));
			}
			unset($location);

			// Check ACL-location - currently only level 1
			$GLOBALS['phpgw']->config->read();
			if (isset($GLOBALS['phpgw']->config->config_data['acl_at_location']) && $GLOBALS['phpgw']->config->config_data['acl_at_location'])
			{
				$level			 = 1;
				$acl_locations	 = array();
				$this->db->query("SELECT location_code, loc{$level}_name FROM fm_location{$level}");
				while ($this->db->next_record())
				{
					$acl_locations[] = array
						(
						'id'	 => ".location.{$level}." . str_replace("-", '.', $this->db->f('location_code')),
						'name'	 => $this->db->f("loc{$level}_name", true)
					);
				}

				foreach ($acl_locations as $acl_location)
				{
					if (!$GLOBALS['phpgw']->locations->get_id('property', $acl_location['id']))
					{
						$GLOBALS['phpgw']->locations->add($acl_location['id'], $acl_location['name'], 'property');
						$receipt['message'][] = array('msg' => lang('%1 added as ACL-location %2', $acl_location['name'], $acl_location['id']));
					}
				}
			}

			$this->db->query("SELECT location_code FROM fm_locations WHERE name IS NULL");

			$locations = array();

			while ($this->db->next_record())
			{
				$locations[] = $this->db->f('location_code');
			}

			foreach ($locations as $location_code)
			{
				$this->update_location_name($location_code);
			}


			$locations = array();
			for ($i = 1; $i < ($levels + 1); $i++)
			{
				$this->db->query("SELECT fm_location{$i}.location_code, fm_locations.id FROM fm_location{$i} {$this->join} fm_locations ON fm_location{$i}.location_code = fm_locations.location_code WHERE fm_location{$i}.id IS NULL");
				while ($this->db->next_record())
				{
					$locations[] = array(
						'level'			 => $i,
						'location_code'	 => $this->db->f('location_code'),
						'id'			 => (int)$this->db->f('id')
					);
				}
			}

			foreach ($locations as $location)
			{
				$this->db->query("UPDATE fm_location{$location['level']} SET id = {$location['id']} WHERE location_code = '{$location['location_code']}'");
			}

			if (!$this->global_lock)
			{
				if ($this->db->transaction_commit())
				{
					return $receipt;
				}
				else
				{
					return $receipt['error'][] = array('msg' => lang('update failed'));
				}
			}
		}

		function update_location_name( $location_code )
		{
			$location_array = $this->read_single($location_code);

			$location_arr	 = explode('-', $location_code);
			$loc_name_arr	 = array();
			$i				 = 1;
			foreach ($location_arr as $_part)
			{
				$loc_name_arr[] = $location_array["loc{$i}_name"];
				$i++;
			}
			$name = $this->db->db_addslashes(implode(', ', $loc_name_arr));
			$this->db->query("UPDATE fm_locations SET name = '{$name}' WHERE location_code = '{$location_code}'");
		}

		function read_summary( $data = '' )
		{
			if (is_array($data))
			{
				$filter			 = isset($data['filter']) && $data['filter'] ? $data['filter'] : 0;
				$type_id		 = isset($data['type_id']) && $data['type_id'] ? (int)$data['type_id'] : 0;
				$district_id	 = isset($data['district_id']) && $data['district_id'] ? (int)$data['district_id'] : 0;
				$part_of_town_id = isset($data['part_of_town_id']) && $data['part_of_town_id'] ? (int)$data['part_of_town_id'] : 0;
				$dry_run		= !empty($data['dry_run']);
			}

			$location_types = $this->soadmin_location->select_location_type();

			if (!$type_id)
			{
				$type_id = count($location_types);
			}

			$entity_table	 = "fm_location{$type_id}";
			$cols_return	 = array();
			$paranthesis	 = '';

			$cols = "count(*) as count, $entity_table.category, {$entity_table}_category.descr as type";

			$groupmethod = " GROUP by $entity_table.category , {$entity_table}_category.descr";

			$uicols['name'][]		 = 'type';
			$uicols['descr'][]		 = lang('type');
			$uicols['input_type'][]	 = 'text';

			$filtermethod	 = '';
			$where			 = 'WHERE';
			if ($district_id > 0)
			{
				$uicols['name'][]		 = 'district_id';
				$uicols['descr'][]		 = lang('district_id');
				$uicols['input_type'][]	 = 'text';
				$cols					 .= ", fm_part_of_town.district_id as district_id";
				$groupmethod			 .= " ,fm_part_of_town.district_id";
				$filtermethod			 .= " $where fm_part_of_town.district_id = {$district_id}";
				$where					 = 'AND';
			}

			if ($part_of_town_id > 0)
			{
				$uicols['name'][]		 = 'part_of_town';
				$uicols['descr'][]		 = lang('part of town');
				$uicols['input_type'][]	 = 'text';
				$groupmethod			 .= " ,fm_part_of_town.name";
				$cols					 .= ", fm_part_of_town.name as part_of_town";
				$filtermethod			 .= " $where fm_part_of_town.id = {$part_of_town_id}";
				$where					 = 'AND';
			}

			if ($filter > 0)
			{
				if ($GLOBALS['phpgw_info']['user']['preferences']['property']['property_filter'] == 'owner')
				{
					$filtermethod .= " $where fm_owner.id='$filter' ";
				}
				else
				{
					$filtermethod .= " $where fm_owner.category='$filter' ";
				}
				$where = 'AND';
			}

			foreach ($location_types as $location_type)
			{
				$uicols['name'][]		 = "count_{$location_type['id']}";
				$uicols['descr'][]		 = lang('count') . " {$location_type['name']}";
				$uicols['input_type'][]	 = 'text';
			}

			$this->uicols = $uicols;

			if($dry_run)
			{
				return array();
			}

			$joinmethod	 = "{$this->join} fm_part_of_town ON (fm_location1.part_of_town_id = fm_part_of_town.id))";
			$paranthesis .= '(';
			$joinmethod	 .= " {$this->join} fm_owner ON (fm_location1.owner_id = fm_owner.id))";
			$paranthesis .= '(';

			$_level = 2;
			for ($i = 1; $i < $type_id; $i++)
			{
				$joinmethod	 .= " {$this->join} fm_location{$_level}";
				$paranthesis .= '(';
				$on			 = 'ON';
				for ($k = ($_level - 1); $k > 0; $k--)
				{
					$joinmethod	 .= " $on (fm_location{$_level}.loc{$k} = fm_location" . ($_level - 1) . ".loc{$k})";
					$on			 = 'AND';
					if ($k == 1)
					{
						$joinmethod .= ")";
					}
				}
				$_level ++;
			}

			$joinmethod	 .= " {$this->join} {$entity_table}_category ON ($entity_table.category = {$entity_table}_category.id))";
			$paranthesis .= '(';

			$sql_base = "$paranthesis fm_location1 $joinmethod";

			$cols = "count(*) as count, fm_location{$type_id}.category, fm_location{$type_id}_category.descr as type";

			$sql = "SELECT $cols FROM $paranthesis fm_location1 $joinmethod";
			_debug_array($sql . $filtermethod . $groupmethod);
			$this->db->query($sql . $filtermethod . $groupmethod . " ORDER BY $entity_table.category", __LINE__, __FILE__);

			$values = array();
			while ($this->db->next_record())
			{
				$cat_id = $this->db->f('category');
				$values[] = array
				(
					'cat_id'		 => $cat_id,
					"count_{$type_id}"			 => $this->db->f('count'),
					'type'			 => '[' . $cat_id . '] ' . $this->db->f('type'),
					'part_of_town'	 => $this->db->f('part_of_town'),
					'district_id'	 => $this->db->f('district_id')
				);
				unset($cat_id);
			}

			foreach ($values as  &$value)
			{
				$cat_id = $value['cat_id'];
				for ($i = ( $type_id - 1 ); $i > 0; $i--)
				{
					$filtermethod2 = " {$where} fm_location{$type_id}.category = '{$cat_id}'";
					$sql = "SELECT count(*) as count FROM "
						. "(SELECT DISTINCT fm_location{$i}.location_code"
						. " FROM {$sql_base} {$filtermethod} {$filtermethod2}) as t";
					$this->db->query($sql, __LINE__, __FILE__);
					while ($this->db->next_record())
					{
						$value["count_{$i}"] = $this->db->f('count');
					}
				}
			}

//			_debug_array($values);
			return $values;
		}

		function check_history( $location_code = '' )
		{
			$location_array	 = explode('-', $location_code);
			$type_id		 = count($location_array);

			if (!$type_id)
			{
				return false;
			}

			$table = 'fm_location' . $type_id . '_history';

			$sql = "SELECT count(*) AS cnt FROM $table WHERE location_code='$location_code'";

			$this->db->query($sql, __LINE__, __FILE__);

			$this->db->next_record();

			if ($this->db->f('cnt') > 0)
			{
				return true;
			}
			else
			{
				return;
			}
		}

		/**
		 * Get complete copies of older versions of the record
		 * @param string $location_code
		 * @param bool $dry_run
		 * @return array $values
		 */
		function get_history( $location_code, $dry_run = false )
		{
			$this->uicols	 = array();
			$location_array	 = explode('-', $location_code);
			$type_id		 = count($location_array);
			$contacts		 = CreateObject('phpgwapi.contacts');

			if (!$type_id)
			{
				return;
			}

			$table = "fm_location{$type_id}_history";

			$table_category		 = "fm_location{$type_id}_category";
			$location_id		 = $GLOBALS['phpgw']->locations->get_id('property', ".location.{$type_id}");
			$choice_table		 = 'phpgw_cust_choice';
			$attribute_table	 = 'phpgw_cust_attribute';
			$attribute_filter	 = "location_id = {$location_id}";

			$sql = "SELECT column_name,datatype,input_text,id AS attrib_id FROM {$attribute_table} WHERE {$attribute_filter}";

			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$attrib[] = array(
					'column_name'	 => $this->db->f('column_name'),
					'input_text'	 => $this->db->f('input_text'),
					'datatype'		 => $this->db->f('datatype'),
					'attrib_id'		 => $this->db->f('attrib_id')
				);

				$this->uicols['input_type'][]	 = 'text';
				$this->uicols['name'][]			 = $this->db->f('column_name');
				$this->uicols['descr'][]		 = $this->db->f('input_text');
			}

			$this->uicols['input_type'][]	 = 'text';
			$this->uicols['name'][]			 = 'exp_date';
			$this->uicols['descr'][]		 = lang('exp date');

			if ($dry_run)
			{
				return;
			}

			$attrib[] = array
				(
				'column_name'	 => 'exp_date',
				'input_text'	 => 'exp date',
				'datatype'		 => 'D',
				'attrib_id'		 => 0
			);

			$sql = "SELECT {$table}.*, {$table_category}.descr as category"
				. " FROM {$table} {$this->left_join} {$table_category} ON {$table}.category = {$table_category}.id"
				. " WHERE location_code='{$location_code}' ORDER BY exp_date DESC";
			$this->db->query($sql, __LINE__, __FILE__);

			$j			 = 0;
			$cols_return = $uicols['name'];
			$dataset	 = array();
			while ($this->db->next_record())
			{
				foreach ($attrib as $key => $field)
				{
					$dataset[$j][$field['column_name']] = array
						(
						'value'		 => $this->db->f($field['column_name']),
						'datatype'	 => $field['datatype'],
						'attrib_id'	 => $field['attrib_id']
					);
				}
				$j++;
			}

			$values = $this->custom->translate_value($dataset, $location_id);

			return $values;
		}

		function get_tenant_location( $tenant_id = '' )
		{
			$location_code = '';

			$location_level = $this->soadmin_location->read_config_single('tenant_id');

			$this->db->query("SELECT location_code FROM fm_location{$location_level} WHERE tenant_id='" . $tenant_id . "'", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$location_code[] = $this->db->f('location_code');
			}
			if (count($location_code) == 1)
			{
				return $location_code[0];
			}
			else
			{
				return $location_code;
			}
		}

		function get_item_id( $location_code )
		{
			$this->db->query("SELECT id FROM fm_locations WHERE location_code='{$location_code}'", __LINE__, __FILE__);
			$this->db->next_record();
			return (int)$this->db->f('id');
		}

		public function get_location_code( $id )
		{
			$sql = "SELECT location_code FROM fm_locations WHERE id = '{$id}'";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			return $this->db->f('location_code');
		}

		function get_children( $criteria = '' )
		{
			$join_method	 = '';
			$filtermethod	 = '';
			if (is_array($criteria))
			{
				$location_code	 = $criteria['location_code'];
				$child_level	 = $criteria['child_level'];
				$id_field		 = 'location_code';
				$field_name		 = $criteria['field_name'];
				$part_of_town_id = $criteria['part_of_town_id'];

				if ($part_of_town_id)
				{
					$join_method	 = "{$this->join} fm_part_of_town ON fm_part_of_town.id = fm_location1.part_of_town_id";
					$filtermethod	 = 'AND fm_part_of_town.id =' . (int)$part_of_town_id;
				}
			}
			else
			{
				$location_code = $criteria;
				if (!$location_code)
				{
					$level = 0;
				}
				else
				{
					$level = count(explode('-', $location_code));
				}

				$child_level = $level + 1;
				$id_field	 = "loc{$child_level}";
				$field_name	 = "loc{$child_level}_name";
			}

			$location_types = $this->soadmin_location->select_location_type();

			$values = array();

			if ($child_level > count($location_types))
			{
				return $values;
			}

			$location_id = $GLOBALS['phpgw']->locations->get_id('property', ".location.{$child_level}");

			$this->db->query("SELECT $id_field AS id, {$field_name} AS name, id AS item_id, location_code"
			. " FROM fm_location{$child_level} {$join_method} WHERE location_code {$this->like} '{$location_code}%' {$filtermethod} ORDER BY {$field_name} ASC", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$location_code	 = $this->db->f('location_code', true);
				$name			 = $this->db->f('name', true);

				$values[]	 = array
				(
					'id'			 => $this->db->f('id'),
					'name'			 => $name ? $name : $location_code,
					'item_id'		 => $this->db->f('item_id'),
					'location_id'	 =>$location_id,
				);
			}
			return $values;
		}

		/**
		 * Get location by name
		 *
		 * @param array   $data array with level and name to search for
		 *
		 * @return array array of hits
		 */
		public function get_locations_by_name( $data )
		{
			$level = isset($data['level']) && $data['level'] ? $data['level'] : 1;

			$location_name	 = isset($data['location_name']) && $data['location_name'] ? $data['location_name'] : '';
			$values			 = array();
			if ($location_name)
			{
				$this->db->query("SELECT loc{$level}_name as name, location_code"
				. " FROM fm_location{$level}"
				. " WHERE loc{$level}_name {$this->like} '{$location_name}%'"
				. " OR location_code {$this->like} '{$location_name}%'", __LINE__, __FILE__);
				while ($this->db->next_record())
				{
					$values[] = array
						(
						'name'			 => $this->db->f('name', true),
						'location_code'	 => $this->db->f('location_code'),
					);
				}
			}
			return $values;
		}

		public function get_booking_part_of_towns()
		{
			$values	 = array();
			$sql	 = "SELECT DISTINCT fm_part_of_town.id, fm_part_of_town.name FROM"
				. " bb_building {$this->join} fm_locations ON bb_building.location_code = fm_locations.location_code"
				. " {$this->join} fm_location1 ON fm_locations.loc1 = fm_location1.loc1"
				. " {$this->join} fm_part_of_town ON fm_location1.part_of_town_id = fm_part_of_town.id ORDER BY name ASC";
			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$values[] = array(
					'id'	 => $this->db->f('id'),
					'name'	 => $this->db->f('name', true)
				);
			}
			return $values;
		}

		public function get_district_name( $location_code )
		{
			$location_code = $this->db->db_addslashes($location_code);

			$sql			 = "SELECT fm_district.descr"
				. " FROM fm_locations"
				. " {$this->join} fm_location1 ON (fm_location1.loc1 = fm_locations.loc1)"
				. " {$this->join} fm_part_of_town ON (fm_location1.part_of_town_id = fm_part_of_town.id)"
				. " {$this->join} fm_district ON (fm_part_of_town.district_id = fm_district.id)"
				. " WHERE fm_locations.location_code = '{$location_code}'";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			$district_name	 = $this->db->f('descr', true);

			return $district_name;
		}

		public function get_part_of_town( $location_code )
		{
			$location_code = $this->db->db_addslashes($location_code);

			$sql = "SELECT fm_district.descr AS district_name,"
				. " fm_part_of_town.name AS part_of_town,"
				. " fm_locations.name AS location_name"
				. " FROM fm_locations"
				. " {$this->join} fm_location1 ON (fm_location1.loc1 = fm_locations.loc1)"
				. " {$this->join} fm_part_of_town ON (fm_location1.part_of_town_id = fm_part_of_town.id)"
				. " {$this->join} fm_district ON (fm_part_of_town.district_id = fm_district.id)"
				. " WHERE fm_locations.location_code = '{$location_code}'";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();

			$values = array
			(
				'district_name'	 => $this->db->f('district_name', true),
				'part_of_town' => $this->db->f('part_of_town', true),
				'location_name'	 => $this->db->f('location_name', true)
			);

			return $values;
		}



		/**
		 * Get location by location_code
		 *
		 * @param string   location_code
		 *
		 * @return array array of hits
		 */
		public function get_locations( $location_code )
		{

			$config = $this->soadmin_location->read_config('');
			$location_types = $this->soadmin_location->select_location_type();

			$__config = array();
			foreach ($config as $_config)
			{
				$__config[$_config['column_name']] = $_config['location_type'];
			}

			$location_levels = count($location_types);

			$location_arr	 = explode('-', $location_code);
			$current_level	 = count($location_arr);
			$next_level		 = $current_level + 1;

			$filtermethod1	 = '';
			$filtermethod2	 = '';
			$text_search	 = false;
			$limit_search	 = 10;
			if (ctype_digit($location_arr[0]))
			{
				$filtermethod1	 = "WHERE location_code {$this->like} '{$location_code}%' AND category !='99'";
				$filtermethod2	 = $filtermethod1;
			}
			else
			{
				$limit_search	 = 100;
				$text_search	 = true;
				$current_level	 = 1;
				$next_level		 = 2;
				$last_level		 = 4;
				$query			 = $this->db->db_addslashes($location_arr[0]);
				$filtermethod1	 = "WHERE loc{$current_level}_name {$this->like} '%{$query}%' AND category !='99'";
				$filtermethod2	 = "WHERE loc{$next_level}_name {$this->like} '%{$query}%' AND category !='99'";
			}

			$metadata1 = $this->db->metadata("fm_location{$current_level}");

			if (!$metadata1)
			{
				return array();
			}


			if($__config['street_id'] == $current_level)
			{
				$name_field = "fm_streetaddress.descr || ' ' || fm_location{$current_level}.street_number";
				$sql = "SELECT location_code, {$name_field} AS name"
					. " FROM fm_location{$current_level}"
					. " {$this->join} fm_streetaddress ON (fm_location{$current_level}.street_id = fm_streetaddress.id)"
					. " {$filtermethod1}";
			}
			else
			{
				$name_field = "loc{$current_level}_name";
				$sql = "SELECT location_code, {$name_field} AS name"
					. " FROM fm_location{$current_level} {$filtermethod1}";
			}


			$metadata2 = $this->db->metadata("fm_location{$next_level}");

			if ($metadata2 && !$text_search)
			{
				if($__config['street_id'] == $next_level)
				{
					$name_field = "fm_streetaddress.descr || ' ' || fm_location{$next_level}.street_number";
					$sql .= " UNION"
						. " SELECT location_code, $name_field AS name"
						. " FROM fm_location{$next_level}"
						. " {$this->join} fm_streetaddress ON (fm_location{$next_level}.street_id = fm_streetaddress.id)"
						. " {$filtermethod2}";
				}
				else
				{
					$name_field = "loc{$next_level}_name";
					$sql .= " UNION"
						. " SELECT location_code, $name_field AS name"
						. " FROM fm_location{$next_level} {$filtermethod2}";
				}

			}
			else if ($text_search)
			{
				$_sql = array();
				$j = 1;
				for ($i = 0; $i < $location_levels; $i++)
				{
					$metadata = $this->db->metadata("fm_location{$j}");

					$name_field = "loc{$j}_name";

					if($__config['street_id'] == $j)
					{
						$name_field = "fm_streetaddress.descr || ' ' || fm_location{$j}.street_number";
						if(isset($metadata['etasje']))
						{
							$name_field .= " || fm_location{$j}.etasje";
						}

						$_sql[]= " SELECT location_code, {$name_field} AS name"
								. " FROM fm_location{$j} {$this->join} fm_streetaddress ON (fm_location{$j}.street_id = fm_streetaddress.id)"
								. " WHERE {$name_field} {$this->like} '%{$query}%' AND category !='99'";
					}
					else
					{
						$_sql[]= " SELECT location_code, {$name_field} AS name"
								. " FROM fm_location{$j}"
								. " WHERE {$name_field} {$this->like} '%{$query}%' AND category !='99'";
					}
	
					$j++;
				}
				$sql = implode(" UNION ", $_sql);

			}

			$sql .= " ORDER BY location_code";

			$values = array();
			if ($location_code)
			{
				$this->db->limit_query($sql, 0, __LINE__, __FILE__, $limit_search);
				while ($this->db->next_record())
				{
					$_location_code = $this->db->f('location_code');

					$values[] = array(
						'id'	 => $_location_code,
						'name'	 => $_location_code . '::' . $this->db->f('name', true)
					);
				}
			}
			return $values;
		}

		function get_delivery_address( $loc1 = '' )
		{
			$loc1				 = $this->db->db_addslashes($loc1);
			$sql				 = "SELECT delivery_address FROM fm_location1 WHERE loc1 = '$loc1'";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			$delivery_address	 = $this->db->f('delivery_address', true);

			if ($delivery_address)
			{
				return $delivery_address;
			}

			$sql				 = "SELECT fm_part_of_town.delivery_address FROM fm_location1"
				. " {$this->join} fm_part_of_town ON (fm_location1.part_of_town_id = fm_part_of_town.id)"
				. "WHERE loc1 = '$loc1'";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			$delivery_address	 = $this->db->f('delivery_address', true);

			if ($delivery_address)
			{
				return $delivery_address;
			}

			$sql				 = "SELECT fm_district.delivery_address FROM fm_location1 "
				. " {$this->join} fm_part_of_town ON (fm_location1.part_of_town_id = fm_part_of_town.id)"
				. " {$this->join} fm_district ON (fm_part_of_town.district_id = fm_district.id)"
				. " WHERE loc1 = '$loc1'";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			$delivery_address	 = $this->db->f('delivery_address', true);

			return $delivery_address;
		}

		function get_location_data( $location_code = '' )
		{
			$locaction_code = $this->db->db_addslashes($location_code);

			$location_arr	 = explode('-', $location_code);
			$current_level	 = count($location_arr);

			$values = array();

			if (!$locaction_code)
			{
				return $values;
			}

			for ($i = 1; $i <= $current_level; $i++)
			{
				$values['location']["loc{$i}"] = $location_arr[($i - 1)];
			}

			$values['location_name'] = $this->get_location_address($location_code);
			return $values;
		}

		function get_location_address( $location_code = '' )
		{
			$locaction_code = $this->db->db_addslashes($location_code);

			$location_arr	 = explode('-', $location_code);
			$current_level	 = count($location_arr);


			$sql			 = "SELECT location_type AS location_level FROM fm_location_config WHERE column_name = 'street_id'";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			$location_level	 = $this->db->f('location_level');

			if ($location_level > $current_level)
			{
				$sql	 = "SELECT loc{$current_level}_name AS address FROM fm_location{$current_level} WHERE location_code = '{$location_code}'";
				$this->db->query($sql, __LINE__, __FILE__);
				$this->db->next_record();
				$address = $this->db->f('address', true);
				return $address;
			}

			$search_location_arr = array();

			for ($i = 0; $i < $location_level; $i++)
			{
				$search_location_arr[] = $location_arr[$i];
			}

			$search_location_code = implode('-', $search_location_arr);

			$sql	 = "SELECT fm_streetaddress.descr || ' ' || fm_location{$location_level}.street_number AS address FROM fm_location{$location_level}"
				. " {$this->join} fm_streetaddress ON fm_location{$location_level}.street_id = fm_streetaddress.id"
				. " WHERE location_code = '{$search_location_code}'";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			$address = $this->db->f('address', true);
			return $address;
		}

		function get_location_exception( $location_code = '', $alert_vendor = false )
		{

			if (!$location_code)
			{
				return array();
			}

			$location_arr	 = explode('-', $location_code);
			$exceptions		 = array();
			$now			 = time();
			$_location_arr	 = array();
			foreach ($location_arr as $loc)
			{
				$_location_arr[] = $this->db->db_addslashes($loc);

				$_location_code = implode('-', $_location_arr);

				$sql = "SELECT DISTINCT fm_location_exception_severity.name AS severity,"
					. " fm_location_exception_category.name AS category,"
					. " content AS category_text, descr as location_descr, alert_vendor"
					. " FROM fm_location_exception"
					. " {$this->join} fm_location_exception_severity ON fm_location_exception.severity_id = fm_location_exception_severity.id"
					. " {$this->join} fm_location_exception_category ON fm_location_exception.category_id = fm_location_exception_category.id"
					. " {$this->left_join} fm_location_exception_category_text ON fm_location_exception.category_text_id = fm_location_exception_category_text.id"
					. " WHERE location_code = '{$_location_code}'"
					. " AND start_date < $now AND (end_date IS NULL  OR end_date = 0 OR end_date > $now)";


				if ($alert_vendor)
				{
					$sql .= ' AND alert_vendor = 1';
				}

				$this->db->query($sql, __LINE__, __FILE__);

				while ($this->db->next_record())
				{
					$exceptions[] = array(
						'severity'		 => $this->db->f('severity', true),
						'category'		 => $this->db->f('category', true),
						'category_text'	 => $this->db->f('category_text', true),
						'location_descr' => $this->db->f('location_descr', true),
						'alert_vendor'	 => $this->db->f('alert_vendor'),
					);
				}
			}

			return $exceptions;
		}

		function edit_field( $data )
		{
			$id			 = (int)$data['id'];
			$field_name	 = $data['field_name'];
			$value		 = $this->db->db_addslashes($data['value']);

			if ($field_name != 'contact_phone')
			{
				return;
			}

			$this->db->query("SELECT tenant_id FROM fm_location4"
				. " WHERE id = {$id}", __LINE__, __FILE__);
			$this->db->next_record();
			$tenant_id = $this->db->f('tenant_id');

			return $this->db->query("UPDATE fm_tenant SET contact_phone = '{$value}'"
					. " WHERE id = {$tenant_id}", __LINE__, __FILE__);
		}

		function get_zip_info($location_code)
		{
			if (!$location_code)
			{
				return array();
			}

			$location_arr	 = explode('-', $location_code);
			$zip_info = array();

			$this->db->query("SELECT fm_zip_code.id AS zip_code, fm_zip_code.name AS city"
				. " FROM fm_zip_code $this->join fm_location1 ON fm_zip_code.id = fm_location1.zip_code"
				. " WHERE loc1 = '{$location_arr[0]}'", __LINE__, __FILE__);
			if($this->db->next_record())
			{
				$zip_info['zip_code'] = $this->db->f('zip_code');
				$zip_info['city'] = $this->db->f('city', true);
			}
			return $zip_info;
		}
	}