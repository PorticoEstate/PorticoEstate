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
	/**
	 * Description of lag_agresso_varemottak
	 *
	 * @author Sigurd Nes
	 */

	if (!class_exists("lag_agresso_varemottak"))
	{
		class lag_agresso_varemottak
		{

			private $acl_location;
			private $values;
			private $ordered_amount = 1;
			var $debug = true;

			function __construct( $acl_location, $id )
			{
				switch ($acl_location)
				{
					case '.ticket':
						$this->acl_location = $acl_location;
						$this->values = ExecMethod('property.sotts.read_single', $id);
						$this->ordered_amount = $this->_get_ordered_ticket_amount($id);
						break;
					default:
						$this->acl_location = '.project.workorder';
						$this->values = ExecMethod('property.soworkorder.read_single', $id);
						$this->values['order_id'] = $id;
						$this->ordered_amount = $this->_get_ordered_workorder_amount($id);
						break;
				}
			}

			private function _get_ordered_ticket_amount($id)
			{
				$amount = 0;
				$budgets = ExecMethod('property.botts.get_budgets',$id);
				foreach ($budgets as $budget)
				{

					$amount += $budget['amount'];
				}
				return $amount ? $amount : 1;
			}

			private function _get_ordered_workorder_amount($id)
			{
				return ExecMethod('property.boworkorder.get_budget_amount',$id);
			}

			public function transfer( $id, $received_amount )
			{
				$values = $this->values;
	//		_debug_array($values);die();

				/*
				P3: EBF Innkjøpsordre Portico : 45000000-45249999
				V3: EBF Varemotttak Portico   : 45500000-45749999
				P4: EBE Innkjøpsordre Portico : 45250000-45499999
				V4: EBE Varemotttak Portico   : 45750000-45999999
				*/

				$voucher_type = 'P4';

				if($values['order_id'] >= 45000000 && $values['order_id'] <= 45249999)
				{
					$voucher_type = 'V3';
				}
				else if ($values['order_id'] >= 45250000 && $values['order_id'] <= 45499999)
				{
					$voucher_type = 'V4';
				}
				else
				{
					throw new Exception("Ordrenummer '{$values['order_id']}' er utenfor serien:<br/>" . __FILE__ . '<br/>linje:' . __LINE__);
				}

				if(empty($this->values['continuous']))
				{
					$quantity = 1; // closing the order
				}
				else if($this->ordered_amount)
				{
					$quantity = $received_amount/$this->ordered_amount;
				}
				else //should not happen, but just in case...
				{
					$quantity = 0.8;
				}

				$param = array(
					'voucher_type'	=> $voucher_type,
					'order_id' => $values['order_id'],
					'lines' => array(
						array(
							'UnitCode' => 'STK',
							'Quantity' => $quantity,
						)
					)
				);

				$exporter_varemottak = new BkBygg_exporter_varemottak_til_Agresso(array(
					'order_id' => $values['order_id'],
					'voucher_type' => $voucher_type
					));
				$exporter_varemottak->create_transfer_xml($param);

				$export_ok = $exporter_varemottak->transfer($this->debug);
				if ($export_ok)
				{
					$this->log_transfer( $id, $received_amount );
				}
				return $export_ok;
			}

			private function log_transfer( $id, $received_amount )
			{
				$id = (int)$id;
				$received_amount = (int)$received_amount;
				switch ($this->acl_location)
				{
					case '.ticket':
						$historylog = CreateObject('property.historylog', 'tts');
						$table = 'fm_tts_tickets';
						break;
					default:
						$historylog = CreateObject('property.historylog', 'workorder');
						$table = 'fm_workorder';
						break;
				}
				$historylog->add('RM', $id, "Varemottak: {$received_amount} overført til agresso");
				$now = time();
				$GLOBALS['phpgw']->db->query("UPDATE {$table} SET order_received = {$now}, order_received_amount = order_received_amount + {$received_amount} WHERE id = {$id}");
			}
		}
	}

	if (!class_exists("BkBygg_exporter_varemottak_til_Agresso"))
	{
		class BkBygg_exporter_varemottak_til_Agresso extends BkBygg_exporter_data_til_Agresso
		{

			var $transfer_xml;
			var $connection;
			var $order_id;
			var $voucher_type;
			var $batch_id;

			public function __construct( $param )
			{
				parent::__construct($param);
			}

			public function create_transfer_xml( $param )
			{
				$Orders = array();
				$Detail = array();
				$i = 1;
				foreach ($param['lines'] as $line)
				{
					$Detail[] = array(
						'LineNo' => $i,
						'Status' => 'N',
						'UnitCode' => $line['UnitCode'],
						'Quantity' => $line['Quantity'],
					);
					$i++;
				}

				$Orders['Order'][] = array(
					'OrderNo' => $param['order_id'],
					'VoucherType' => $param['voucher_type'],
					'TransType' => 51,
					'Details' => array('Detail' => $Detail)
				);

	//			_debug_array($Orders);
	//			die();

				$root_attributes = array(
					'Version' => "542",
					'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
					'xsi:noNamespaceSchemaLocation' => "http://services.agresso.com/schema/ABWOrder/2004/07/02/ABWOrder.xsd"
				);
				$xml_creator = new xml_creator('ABWOrder', $root_attributes);
				$xml_creator->fromArray($Orders);
				$this->transfer_xml = $xml_creator->getDocument();
				return $this->transfer_xml;
			}

			protected function create_file_name( $ref = '' )
			{
				if (!$this->batchid)
				{
					throw new Exception('BkBygg_exporter_data_til_Agresso::create_file_name() Mangler referanse');
				}
				$voucher_type = $this->voucher_type;
				$order_id = $this->order_id;
				if (!$voucher_type)
				{
					throw new Exception('BkBygg_exporter_varemottak_til_Agresso::create_file_name() Mangler bilagstype');
				}

				$fil_katalog = $this->config->config_data['export']['path'];

				$filename = "{$fil_katalog}/{$voucher_type}_varemottak_{$order_id}_{$this->batchid}.xml";

				//Sjekk om filen eksisterer
				if (file_exists($filename))
				{
					unlink($filename);
				}

				return $filename;
			}
		}
	}

	if (!empty($id) && !empty($acl_location) && isset($transfer_action) && $transfer_action == 'receive_order')
	{
		$exporter_varemottak = new lag_agresso_varemottak($acl_location, $id);
		$result = $exporter_varemottak->transfer($id, $received_amount);
	}
