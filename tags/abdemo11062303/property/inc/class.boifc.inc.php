<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
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
	* @subpackage ifc
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package hrm
	 */
	phpgw::import_class('phpgwapi.datetime');

	class boifc
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $allrows;

		/**
		 * @var object $custom reference to custom fields object
		 */
		protected $custom;

		var $public_functions = array
			(
				'read'			=> true,
				'read_single'	=> true,
				'save'			=> true,
				'delete'		=> true,
				'check_perms'	=> true
			);

		function boifc($session=false)
		{
	//		$this->so 			= CreateObject('property.soifc');
			$this->bocommon		= createObject('property.bocommon');
			$this->custom 		= createObject('property.custom_fields');
			$this->acl_location 	= '.ifc';

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start	= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query	= phpgw::get_var('query');
			$sort	= phpgw::get_var('sort');
			$order	= phpgw::get_var('order');
			$filter	= phpgw::get_var('filter', 'int');
			$cat_id	= phpgw::get_var('cat_id', 'int');
			$allrows= phpgw::get_var('allrows', 'bool');

			if ($start)
			{
				$this->start=$start;
			}
			else
			{
				$this->start=0;
			}

			if(array_key_exists('query',$_POST) || array_key_exists('query',$_GET))
			{
				$this->query = $query;
			}
			if(array_key_exists('filter',$_POST) || array_key_exists('filter',$_GET))
			{
				$this->filter = $filter;
			}
			if(array_key_exists('sort',$_POST) || array_key_exists('sort',$_GET))
			{
				$this->sort = $sort;
			}
			if(array_key_exists('order',$_POST) || array_key_exists('order',$_GET))
			{
				$this->order = $order;
			}
			if(array_key_exists('cat_id',$_POST) || array_key_exists('cat_id',$_GET))
			{
				$this->cat_id = $cat_id;
			}
			if ($allrows)
			{
				$this->allrows = $allrows;
			}

			switch($GLOBALS['phpgw_info']['server']['db_type'])
			{
			case 'mssql':
				$this->dateformat 		= "M d Y";
				$this->datetimeformat 	= "M d Y g:iA";
				break;
			case 'mysql':
				$this->dateformat 		= "Y-m-d";
				$this->datetimeformat 	= "Y-m-d G:i:s";
				break;
			case 'pgsql':
				$this->dateformat 		= "Y-m-d";
				$this->datetimeformat 	= "Y-m-d G:i:s";
				break;
			case 'postgres':
				$this->dateformat 		= "Y-m-d";
				$this->datetimeformat 	= "Y-m-d G:i:s";
				break;
			}
		}


		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','ifc_app',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','ifc_app');

			$this->start	= (isset($data['start'])?$data['start']:'');
			$this->query	= (isset($data['query'])?$data['query']:'');
			$this->filter	= (isset($data['filter'])?$data['filter']:'');
			$this->sort		= (isset($data['sort'])?$data['sort']:'');
			$this->order	= (isset($data['order'])?$data['order']:'');
			$this->cat_id	= (isset($data['cat_id'])?$data['cat_id']:'');
		}

		function check_perms($rights, $required)
		{
			return ($rights & $required);
		}

		function import($values='',$ifcfile='')
		{
			_debug_array($ifcfile);

/*			$xmltool		= CreateObject('phpgwapi.xmltool');
			$xmldata = file_get_contents($ifcfile);
			$xmltool->import_xml($xmldata);
			$xml = $xmltool->export_var();

_debug_array('hei');
 */
/*			$xmlparse = CreateObject('phpgwapi.parsexml');
			$xml = $xmlparse->GetXMLTree($ifcfile);
 */

//			_debug_array($this->xml_to_array($ifcfile));

			$xa = CreateObject('property.XmlToArray');
			$xa->get_attributes = true;
			$xml = $xa->parseFile($ifcfile);
			_debug_array($xml);

		}


		function read()
		{
			$ifc_info = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'cat_id'=>$this->cat_id,'allrows'=>$this->allrows,'filter'=>$this->filter));
			$this->total_records = $this->so->total_records;
			return $ifc_info;
		}

		/**
		 * Get list of records with dynamically allocated coulmns
		 *
		 * @return array Array with records.
		 */
		function read2()
		{
			$custom_attributes = $this->custom->find('property', $this->acl_location, 0, '', 'ASC', 'attrib_sort', true, true);

			$ifc_info = $this->so->read2(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'cat_id'=>$this->cat_id,'allrows'=>$this->allrows,'filter'=>$this->filter,
				'custom_attributes'=>$custom_attributes));
			$this->total_records = $this->so->total_records;
			$this->uicols	= $this->so->uicols;
			return $ifc_info;
		}

		function read_single($id='')
		{
			$values['attributes'] = $this->custom->find('property', $this->acl_location, 0, '', 'ASC', 'attrib_sort', true, true);

			if($id)
			{
				$values = $this->so->read_single($id,$values);
			}

			$values = $this->custom->prepare($values,$appname='property', $location=$this->acl_location);

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			if(isset($values['entry_date']) && $values['entry_date'])
			{
				$values['entry_date']	= $GLOBALS['phpgw']->common->show_date($values['entry_date'],$dateformat);
			}

			return $values;
		}

		function save($values,$values_attribute='')
		{
			if(is_array($values_attribute))
			{
				for ($i=0;$i<count($values_attribute);$i++)
				{
					if($values_attribute[$i]['datatype']=='CH' && $values_attribute[$i]['value'])
					{
	//					$values_attribute[$i]['value'] = serialize($values_attribute[$i]['value']);
						$values_attribute[$i]['value'] = ',' . implode(',', $values_attribute[$i]['value']) . ',';
					}
					if($values_attribute[$i]['datatype']=='R' && $values_attribute[$i]['value'])
					{
						$values_attribute[$i]['value'] = $values_attribute[$i]['value'][0];
					}

					if($values_attribute[$i]['datatype']=='N' && $values_attribute[$i]['value'])
					{
						$values_attribute[$i]['value'] = str_replace(",",".",$values_attribute[$i]['value']);
					}

					if($values_attribute[$i]['datatype']=='D' && $values_attribute[$i]['value'])
					{
						$values_attribute[$i]['value'] = date($this->dateformat,$this->date_to_timestamp($values_attribute[$i]['value']));
					}
				}
			}


			if (isset($values['ifc_id']) && $values['ifc_id'])
			{
				$receipt = $this->so->edit($values,$values_attribute);
			}
			else
			{
				$receipt = $this->so->add($values,$values_attribute);
			}

			$criteria = array
				(
					'appname'	=> 'property',
					'location'	=> $this->acl_location,
					'allrows'	=> true
				);

			$custom_functions = $GLOBALS['phpgw']->custom_functions->find($criteria);

			foreach ( $custom_functions as $entry )
			{
				// prevent path traversal
				if ( preg_match('/\.\./', $entry['file_name']) )
				{
					continue;
				}

				$file = PHPGW_SERVER_ROOT . "/property/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";
				if ( $entry['active'] && is_file($file) )
				{
					require_once $file;
				}
			}

			return $receipt;
		}

		function delete($id)
		{
			$this->so->delete($id);
		}

		function select_category_list($format='',$selected='')
		{
			switch($format)
			{
			case 'select':
				$GLOBALS['phpgw']->xslttpl->add_file(array('cat_select'));
				break;
			case 'filter':
				$GLOBALS['phpgw']->xslttpl->add_file(array('cat_filter'));
				break;
			}

			$categories= $this->so->select_category_list();

			while (is_array($categories) && list(,$category) = each($categories))
			{
				if ($category['id']==$selected)
				{
					$category_list[] = array
						(
							'cat_id'	=> $category['id'],
							'name'		=> $category['name'],
							'selected'	=> 'selected'
						);
				}
				else
				{
					$category_list[] = array
						(
							'cat_id'	=> $category['id'],
							'name'		=> $category['name'],
						);
				}
			}
			return $category_list;
		}

		/**
		 * Preserve attribute values from post in case of an error
		 *
		 * @param array $values_attribute attribute definition and values from posting
		 * @param array $values value set with
		 * @return array Array with attribute definition and values
		 */
		function preserve_attribute_values($values='',$values_attribute='')
		{
			return $this->bocommon->preserve_attribute_values($values,$values_attribute);
		}


		function date_to_timestamp($date)
		{
			if($date)
			{
				$date_array	= phpgwapi_datetime::date_array($date);
				$date	= mktime (8,0,0,$date_array['month'],$date_array['day'],$date_array['year']);
			}
			return $date;
		}
	}
