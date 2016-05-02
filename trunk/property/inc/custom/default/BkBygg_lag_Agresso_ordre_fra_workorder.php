<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation, Inc. http://www.fsf.org/
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
	/**
	 * Description
	 * @package property
	 */
	//if (false)
	if (true)
	{
		$exporter_ordre = new lag_agresso_ordre_fra_workorder();
		$exporter_ordre->transfer($project, $workorder);
	}

	class lag_agresso_ordre_fra_workorder
	{

		public function __construct()
		{
			$this->cats = CreateObject('phpgwapi.categories', -1, 'property', '.project');
			$this->cats->supress_info = true;
		}

		public function transfer( $project, $workorder )
		{
//	_debug_array($workorder);die();

			$contacts = CreateObject('property.sogeneric');
			$contacts->get_location_info('vendor', false);

			$custom = createObject('property.custom_fields');
			$vendor_data['attributes'] = $custom->find('property', '.vendor', 0, '', 'ASC', 'attrib_sort', true, true);

			$vendor_data = $contacts->read_single(array('id' => $workorder['vendor_id']), $vendor_data);
			if (is_array($vendor_data))
			{
				foreach ($vendor_data['attributes'] as $attribute)
				{
					if ($attribute['name'] == 'adresse')
					{
						$vendor['address'] = $attribute['value'];
					}
					if ($attribute['name'] == 'org_name')
					{
						$vendor['name'] = $attribute['value'];
					}
				}
			}
			unset($contacts);

			$GLOBALS['phpgw']->preferences->set_account_id($workorder['user_id'], true);

			$user_name = $GLOBALS['phpgw']->accounts->get($workorder['user_id'])->__toString();
			$account_lid = $GLOBALS['phpgw']->accounts->id2lid($workorder['user_id']);

			if ($workorder['location_code'])
			{
				$location_code = $workorder['location_code'];
				$location = explode('-', $location_code);
				$dim3 = isset($location[1]) && $location[1] ? "{$location[0]}{$location[1]}" : "{$location[0]}01";
			}
			else if ($project['location_code'])
			{
				$location_code = $project['location_code'];
				$location = explode('-', $location_code);
				$dim3 = isset($location[1]) && $location[1] ? "{$location[0]}{$location[1]}" : "{$location[0]}01";
			}
			else
			{
				$dim3 = 9;
			}

			$address_element = execMethod('property.botts.get_address_element', $location_code);
			$_address = array();
			foreach ($address_element as $entry)
			{
				$_address[] = "{$entry['text']}: {$entry['value']}";
			}

			$address = '';
			if ($_address)
			{
				$address = implode(', ', $_address);
			}

			$buyer = array(
				'Name' => $user_name,
				'AddressInfo' => array(
					array(
						'Address' => $address
					)
				),
				'BuyerReferences' => array(
					array(
						'Responsible' => $account_lid,
						'RequestedBy' => $account_lid,
						'Accountable' => $account_lid,
					)
				)
			);


			//EBF...

			$location_info = execMethod('property.bolocation.read_single', $location[0]);

			$tax_code = 0;
			$tjeneste = 9;
			if ($location_info['attributes'])
			{
				$_found = 0;
				foreach ($location_info['attributes'] as $key => $attribute)
				{
					if ($attribute['name'] == 'mva')
					{
						$tax_code = $attribute['value'];
						$_found ++;
					}
					if ($attribute['name'] == 'kostra_id')
					{
						$tjeneste = $attribute['value'];
						$_found ++;
					}
					if ($_found == 2)
					{
						break;
					}
				}
			}

			//Override from workorder
			$tax_code = $workorder['tax_code'] ? $workorder['tax_code'] : $tax_code;
			$tjeneste = $workorder['service_id'] ? $workorder['service_id'] : $tjeneste;

//			_debug_array($location_info);die();
			$config = CreateObject('phpgwapi.config', 'property');
			$config->read();

			$collect_building_part = false;
			if (isset($config->config_data['workorder_require_building_part']))
			{
				if ($config->config_data['workorder_require_building_part'] == 1)
				{
					$collect_building_part = true;
				}
			}

			if ($collect_building_part)
			{
				if ($workorder['order_dim1'])
				{
					$sogeneric = CreateObject('property.sogeneric', 'order_dim1');
					$sogeneric_data = $sogeneric->read_single(array('id' => $workorder['order_dim1']));
					if ($sogeneric_data)
					{
						$dim6 = "{$workorder['building_part']}{$sogeneric_data['num']}";
					}
				}
			}
			else
			{
				$category = $this->cats->return_single($workorder['cat_id']);
				$category_arr = explode('-', $category[0]['name']);
				$dim6 = (int)trim($category_arr[0]);
			}

			$param = array(
				'dim0' => $workorder['b_account_id'], // Art
				'dim1' => $workorder['ecodimb'], // Ansvar
				'dim2' => $tjeneste, // Tjeneste liste 30 stk, default 9
				'dim3' => $dim3, // Objekt: eiendom + bygg: 6 siffer
				'dim4' => $workorder['contract_id'], // Kontrakt - frivillig / 9, 7 tegn - alfanumerisk
				'dim5' => $project['external_project_id'], // Prosjekt
				'dim6' => $dim6, // Aktivitet - frivillig: bygningsdel, 3 siffer + bokstavkode
				'vendor_id' => $workorder['vendor_id'],
				'vendor_name' => $vendor['name'],
				'vendor_address' => $vendor['address'],
				'order_id' => $workorder['id'],
				'tax_code' => $tax_code,
				'buyer' => $buyer,
				'lines' => array(
					array(
						'unspsc_code' => $workorder['unspsc_code'],
						'descr' => strip_tags($workorder['descr'])
					)
				)
			);

			$exporter_ordre = new BkBygg_exporter_data_til_Agresso();
			$exporter_ordre->create_transfer_xml($param);
			$exporter_ordre->output();
			die();
			$export_ok = $exporter_ordre->transfer();
			if ($export_ok)
			{
				$this->log_transfer( $workorder['id'] );
			}
		}

		private function log_transfer( $id )
		{
			$historylog = CreateObject('property.historylog', 'workorder');
			$historylog->add('RM', $id, "Ordre overført til agresso");
			$GLOBALS['phpgw']->db->query("UPDATE fm_workorder SET order_sent = 1 WHERE id = {$id}");
		}
	}