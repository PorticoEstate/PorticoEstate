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
	class property_bogab
	{

		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $gab_insert_level;
		var $custom;
		var $public_functions = array
			(
			'read' => true,
			'read_single' => true,
			'save' => true,
			'delete' => true,
		);

		function __construct( )
		{
			$this->solocation = CreateObject('property.solocation');
			$this->so = CreateObject('property.sogab');
			$this->custom = createObject('property.custom_fields');
			$this->gab_insert_level = $this->so->gab_insert_level;

			$start = phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query = phpgw::get_var('query');
			$sort = phpgw::get_var('sort');
			$order = phpgw::get_var('order');
			$filter = phpgw::get_var('filter', 'int');
			$cat_id = phpgw::get_var('cat_id', 'int');
			$allrows = phpgw::get_var('allrows', 'bool');

			if ($start)
			{
				$this->start = $start;
			}
			else
			{
				$this->start = 0;
			}

			if (isset($query))
			{
				$this->query = $query;
			}
			if (!empty($filter))
			{
				$this->filter = $filter;
			}
			if (isset($sort))
			{
				$this->sort = $sort;
			}
			if (isset($order))
			{
				$this->order = $order;
			}
			if (isset($cat_id))
			{
				$this->cat_id = $cat_id;
			}
			if (isset($allrows))
			{
				$this->allrows = $allrows;
			}
		}

		function read( $data )
		{
			if ($data['allrows'])
			{
				$this->allrows = true;
			}

			$gab = $this->so->read(array
				(
					'start' => $data['start'],
					'sort' => $data['sort'],
					'order' => $data['order'],
					'allrows' => $data['allrows'],
					'cat_id' => $this->cat_id,
					'location_code' => $data['location_code'],
					'gaards_nr' => $data['gaards_nr'],
					'bruksnr' => $data['bruksnr'],
					'feste_nr' => $data['feste_nr'],
					'seksjons_nr' => $data['seksjons_nr'],
					'address' => $data['address'],
		//			'check_payments' => $data['check_payments']
				)
			);

			/*
			  foreach ($gab as &$_gab)
			  {
			  $location_data	= $this->solocation->read_single($_gab['location_code']);

			  if(isset($location_data['street_name']) && $location_data['street_name'])
			  {
			  $_gab['address'] = "{$location_data['street_name']} {$location_data['street_number']}";
			  }
			  elseif($location_data['loc2_name'])
			  {
			  $_gab['address'] = $location_data['loc2_name'];
			  }
			  elseif($location_data['loc1_name'])
			  {
			  $_gab['address'] = $location_data['loc1_name'];
			  }
			  }
			 */
			$this->total_records = $this->so->total_records;
			$this->payment_date = $this->so->payment_date;
			return $gab;
		}

		//nguerra@ccfirst.com $allrows - variable to display all records
		function read_detail( $data = '', $allrows = 0 )
		{
			$gab = $this->so->read_detail(array(
				'start' => $data['start'],
				'sort' => $data['sort'],
				'order' => $data['order'],
				'cat_id' => $this->cat_id,
				'gab_id' => $data['gab_id'],
				'allrows' => $allrows)
			);

			$this->total_records = $this->so->total_records;

			$this->uicols = $this->so->uicols;
			$cols_extra = $this->so->cols_extra;


			for ($i = 0; $i < count($gab); $i++)
			{
				$location_data = $this->solocation->read_single($gab[$i]['location_code']);

				for ($j = 0; $j < count($cols_extra); $j++)
				{
					$gab[$i][$cols_extra[$j]] = $location_data[$cols_extra[$j]];
				}
			}

			return $gab;
		}

		function read_single( $gab_id = '', $location_code = '' )
		{
			$values['attributes'] = $this->custom->find('property', '.location.gab', 0, '', 'ASC', 'attrib_sort', true, true);
			$values = $this->so->read_single($gab_id, $location_code, $values);
			$values = $this->custom->prepare($values, 'property', '.location.gab', $data['view']);

			if ($values['location_code'])
			{
				$values['location_data'] = $this->solocation->read_single($gab['location_code']);
			}

			return $values;
		}

		function save( $values )
		{
			if (!$values['location_code'])
			{
				//while (is_array($values['location']) && list(, $value) = each($values['location']))
                                foreach($values['location'] as $value)
				{
					if ($value)
					{
						$location[] = $value;
					}
				}

				$values['location_code'] = implode("-", $location);
			}

			if ($values['attributes'] && is_array($values['attributes']))
			{
				$values['attributes'] = $this->custom->convert_attribute_save($values['attributes']);
			}

			if ($values['action'] == 'edit')
			{
				$receipt = $this->so->edit($values);
			}
			else
			{
				$receipt = $this->so->add($values);
			}

			$receipt['location_code'] = $values['location_code'];
			return $receipt;
		}

		function delete( $gab_id = '', $location_code = '' )
		{
			$this->so->delete($gab_id, $location_code);
		}
	}