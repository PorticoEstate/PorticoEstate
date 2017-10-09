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
	
	if (!class_exists("lag_agresso_ordre_fra_workorder"))
	{
		class lag_agresso_ordre_fra_workorder
		{
			var $debug = false;

			public function __construct()
			{
				$this->cats = CreateObject('phpgwapi.categories', -1, 'property', '.project');
				$this->cats->supress_info = true;
				$config = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.invoice'));
				$this->debug = empty($config->config_data['export']['activate_transfer']) ? true : false;
			}

			public function transfer( $workorder )
			{
				$project = createObject('property.boproject')->read_single($workorder['project_id'], array(), true);

				if (!$this->debug && $workorder['order_sent'])
				{
					$transfer_time = $GLOBALS['phpgw']->common->show_date($workorder['order_sent']);
					phpgwapi_cache::message_set("Info: Ordre #{$workorder['id']} er allerede overført til Agresso {$transfer_time}");
					return 2;
				}

				$config = CreateObject('phpgwapi.config', 'property');
				$config->read();
				$approval_level = !empty($config->config_data['approval_level']) ? $config->config_data['approval_level'] : 'order';

				$approval_amount = 0;
				$price = 0;
				if($approval_level == 'project')
				{
					$approval_amount = ExecMethod('property.boworkorder.get_accumulated_budget_amount', $workorder['project_id']);
					$price = (float) ExecMethod('property.boworkorder.get_budget_amount', $workorder['id']);
				}
				else
				{
					$approval_amount = ExecMethod('property.boworkorder.get_budget_amount', $workorder['id']);
					$price = (float) $approval_amount;

				}

				try
				{
					$purchase_grant_ok = CreateObject('property.botts')->validate_purchase_grant( $workorder['ecodimb'], $approval_amount, $workorder['id']);
				}
				catch (Exception $ex)
				{
					throw $ex;
				}

				if (!$this->debug && !$purchase_grant_ok)
				{
					return 3;
				}

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


				if ($workorder['ecodimb'])
				{
					$dim1 = $workorder['ecodimb'];
				}
				else if ($project['ecodimb'])
				{
					$dim1 = $project['ecodimb'];
				}
				else
				{
					throw new Exception('Dimensjonen "Ansvar" mangler');
				}

				if ($workorder['location_code'])
				{
					$location_code = $workorder['location_code'];
					$location = explode('-', $location_code);
	//				$dim3 = isset($location[1]) && $location[1] ? "{$location[0]}{$location[1]}" : "{$location[0]}01";
					$dim3 = $location[0];

				}
				else if ($project['location_code'])
				{
					$location_code = $project['location_code'];
					$location = explode('-', $location_code);
	//				$dim3 = isset($location[1]) && $location[1] ? "{$location[0]}{$location[1]}" : "{$location[0]}01";
					$dim3 = $location[0];
				}
				else
				{
					$dim3 = 9;
				}

				if($dim3 == 9999)
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

				$address = mb_substr(htmlspecialchars($address, ENT_QUOTES, 'UTF-8', true), 0, 50);

				$buyer = array(
					'Name' => $user_name,
					'AddressInfo' => array(
						array(
							'Address' => htmlspecialchars_decode($address, ENT_QUOTES)
						)
					),
					'BuyerReferences' => array(
						array(
							'Responsible' => strtoupper($account_lid),
							'RequestedBy' => strtoupper($account_lid),
							'Accountable' => strtoupper($account_lid),
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
				switch ($tax_code)
				{
					case '0':
						$tax_code = '6A';
						break;
					case '75':
						$tax_code = '60';
						break;
					default:
						$tax_code = '6A';
						break;
				}

				$tjeneste = $workorder['service_id'] ? (int)$workorder['service_id'] : (int)$tjeneste;

				$GLOBALS['phpgw']->db->query("UPDATE fm_workorder SET service_id = {$tjeneste} WHERE id = {$workorder['id']}");

	//			_debug_array($location_info);die();

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

				/*
				P3: EBF Innkjøpsordre Portico : 45000000-45249999
				V3: EBF Varemotttak Portico   : 45500000-45749999
				P4: EBE Innkjøpsordre Portico : 45250000-45499999
				V4: EBE Varemotttak Portico   : 45750000-45999999
				*/

	//			$voucher_type = 'P4';

				if($workorder['id'] >= 45000000 && $workorder['id'] <= 45249999)
				{
					$voucher_type = 'P3';
				}
				else if ($workorder['id'] >= 45250000 && $workorder['id'] <= 45499999)
				{
					$voucher_type = 'P4';
				}
				else
				{
					throw new Exception("Ordrenummer '{$workorder['id']}' er utenfor serien:<br/>" . __FILE__ . '<br/>linje:' . __LINE__);
				}

				$param = array(
					'dim0' => $workorder['b_account_id'], // Art
					'dim1' => $dim1, // Ansvar
					'dim2' => $tjeneste, // Tjeneste liste 30 stk, default 9
					'dim3' => $dim3, // Objekt: eiendom + bygg: 6 siffer
					'dim4' => $workorder['contract_id'] == '-1' ? '' : $workorder['contract_id'], // Kontrakt - frivillig / 9, 7 tegn - alfanumerisk
					'dim5' => $project['external_project_id'], // Prosjekt
					'dim6' => $dim6, // Aktivitet - frivillig: bygningsdel, 3 siffer + bokstavkode
					'vendor_id' => $workorder['vendor_id'],
					'vendor_name' => $vendor['name'],
					'vendor_address' => mb_substr($vendor['address'], 0, 50),
					'order_id' => $workorder['id'],
					'tax_code' => $tax_code,
					'buyer' => $buyer,
					'lines' => array(
						array(
							'unspsc_code' => $workorder['unspsc_code'] ? $workorder['unspsc_code'] : 'UN-72000000',
	//						'descr' => strip_tags($workorder['descr'])
							'descr' => '',
							'price'	=> $price,
						)
					)
				);


				$exporter_ordre = new BkBygg_exporter_data_til_Agresso(array(
					'order_id' => $workorder['id'],
					'voucher_type' => $voucher_type
					)
				);
				$exporter_ordre->create_transfer_xml($param);

				$export_ok = $exporter_ordre->transfer($this->debug);

				if ($export_ok)
				{
					phpgwapi_cache::message_set("Ordre #{$workorder['id']} er overført");
					$this->log_transfer( $workorder['id'] );
				}
			}

			private function log_transfer( $id )
			{
				$historylog = CreateObject('property.historylog', 'workorder');
				$historylog->add('RM', $id, "Ordre overført til agresso");
				$now = time();
				$GLOBALS['phpgw']->db->query("UPDATE fm_workorder SET order_sent = {$now} WHERE id = {$id}");
			}
		}
	}

	if (!empty($transfer_action) && $transfer_action == 'workorder')
	{
		$exporter_ordre = new lag_agresso_ordre_fra_workorder();
		try
		{
			$exporter_ordre->transfer($workorder);
		}
		catch (Exception $exc)
		{
			phpgwapi_cache::message_set($exc->getMessage(), 'error');
		}
	}
