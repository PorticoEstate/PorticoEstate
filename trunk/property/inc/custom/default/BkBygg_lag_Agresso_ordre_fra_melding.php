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
	if (isset($data['order_id']) && $data['order_id'] && isset($data['save']) && $data['save'] && isset($data['vendor_email'][0]) && $data['vendor_email'][0])
	{
		$exporter_ordre = new lag_agresso_ordre_fra_melding();
		$exporter_ordre->transfer($id);
	}

	class lag_agresso_ordre_fra_melding
	{

		function __construct()
		{
			
		}

		public function transfer( $id )
		{
			$_ticket = ExecMethod('property.sotts.read_single', $id);
//		_debug_array($_ticket);die();

			$contacts = CreateObject('property.sogeneric');
			$contacts->get_location_info('vendor', false);

			$custom = createObject('property.custom_fields');
			$vendor_data['attributes'] = $custom->find('property', '.vendor', 0, '', 'ASC', 'attrib_sort', true, true);

			$vendor_data = $contacts->read_single(array('id' => $_ticket['vendor_id']), $vendor_data);
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


			if (phpgw::get_var('on_behalf_of_assigned', 'bool') && isset($_ticket['assignedto_name']))
			{
				$user_name = $_ticket['assignedto_name'];
				$GLOBALS['phpgw']->preferences->set_account_id($_ticket['assignedto'], true);
				$GLOBALS['phpgw_info']['user']['preferences'] = $GLOBALS['phpgw']->preferences->data;
				$account_lid = $GLOBALS['phpgw']->accounts->id2lid($_ticket['assignedto']);
			}
			else
			{
				$user_name = $GLOBALS['phpgw_info']['user']['fullname'];
				$account_lid = $GLOBALS['phpgw_info']['user']['account_lid'];
			}
			//	$ressursnr = $GLOBALS['phpgw_info']['user']['preferences']['property']['ressursnr'];

			$buyer = array(
				'Name' => $user_name,
				'AddressInfo' => array(
					array(
						'Address' => $_ticket['address']
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
			if ($_ticket['location_data'])
			{
				$dim3 = isset($_ticket['location_data']['loc2']) && $_ticket['location_data']['loc2'] ? "{$_ticket['location_data']['loc1']}{$_ticket['location_data']['loc2']}" : "{$_ticket['location_data']['loc1']}01";
			}
			else
			{
				$dim3 = 9;
			}

			$dim6 = 9;

			if ($_ticket['order_dim1'])
			{
				$sogeneric = CreateObject('property.sogeneric', 'order_dim1');
				$sogeneric_data = $sogeneric->read_single(array('id' => $_ticket['order_dim1']));
				if ($sogeneric_data)
				{
					$dim6 = "{$_ticket['building_part']}{$sogeneric_data['num']}";
				}
			}

			$param = array(
				'dim0' => $_ticket['b_account_id'],			// Art
				'dim1' => $_ticket['ecodimb'],				// Ansvar
				'dim2' => $_ticket['service_id'] ? $_ticket['service_id'] : 9, // Tjeneste liste 30 stk, default 9
				'dim3' => $dim3,							// Objekt: eiendom + bygg: 6 siffer
				'dim4' => $_ticket['contract_id'],			// Kontrakt - frivillig / 9, 7 tegn - alfanumerisk
				'dim5' => $_ticket['external_project_id'],	// Prosjekt
				'dim6' => $dim6,							// Aktivitet - frivillig: bygningsdel, 3 siffer + bokstavkode
				'vendor_id' => $_ticket['vendor_id'],
				'vendor_name' => $vendor['name'],
				'vendor_address' => $vendor['address'],
				'order_id' => $_ticket['order_id'],
				'tax_code' => $_ticket['tax_code'],
				'buyer' => $buyer,
				'lines' => array(
					array(
						'unspsc_code' => $_ticket['unspsc_code'],
						'descr' => strip_tags($_ticket['order_descr'])
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
				$this->log_transfer( $id );
			}
		}

		private function log_transfer( $id )
		{
			$historylog = CreateObject('property.historylog', 'tts');
			$historylog->add('RM', $id, "Ordre overført til agresso");
			$now = time();
			$GLOBALS['phpgw']->db->query("UPDATE fm_tts_tickets SET order_sent = {$now} WHERE id = {$id}");
		}
	}