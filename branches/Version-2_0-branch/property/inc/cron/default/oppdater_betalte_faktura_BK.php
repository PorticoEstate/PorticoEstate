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
	 * @subpackage cron
	 * @version $Id: oppdater_betalte_faktura_BK.php 16075 2016-12-12 15:26:41Z sigurdne $
	 */
	/**
	 * Description
	 * example cron : /usr/local/bin/php -q /var/www/html/phpgroupware/property/inc/cron/cron.php default oppdater_betalte_faktura_BK
	 * @package property
	 */
	include_class('property', 'cron_parent', 'inc/cron/');
	phpgw::import_class('phpgwapi.datetime');

	class oppdater_betalte_faktura_BK extends property_cron_parent
	{

		public function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location = lang('property');
			$this->function_msg = 'oppdater bestillinger med grunnlag i betalte faktura';
		}

		function execute()
		{

			//curl -s -u portico:BgPor790gfol http://tjenester.usrv.ubergenkom.no/api/agresso/art
			//curl -s -u portico:BgPor790gfol http://tjenester.usrv.ubergenkom.no/api/agresso/ansvar?id=013000
			//curl -s -u portico:BgPor790gfol http://tjenester.usrv.ubergenkom.no/api/agresso/objekt?id=5001
			//curl -s -u portico:BgPor790gfol http://tjenester.usrv.ubergenkom.no/api/agresso/prosjekt?id=5001
			//curl -s -u portico:BgPor790gfol http://tjenester.usrv.ubergenkom.no/api/agresso/tjeneste?id=88010

			//curl -s -u portico:BgPor790gfol http://tjenester.usrv.ubergenkom.no/api/agresso/leverandorer?leverandorNr=722920
			if ($this->debug)
			{
			}

			try
			{
				$this->update_order();
			}
			catch (Exception $e)
			{
				$this->receipt['error'][] = array('msg' => $e->getMessage());
			}
		}

		private function update_order()
		{
			$config = CreateObject('phpgwapi.config', 'property')->read();
			$sql = "SELECT DISTINCT pmwrkord_code, external_voucher_id FROM fm_ecobilag";
			$this->db->query($sql, __LINE__, __FILE__);
			$vouchers = array();
			while ($this->db->next_record())
			{
				$vouchers[] = array
				(
					'order_id' => $this->db->f('pmwrkord_code'),
					'voucher_id' => $this->db->f('external_voucher_id')
				);
			}

			$socommon = CreateObject('property.socommon');
			$soworkorder = CreateObject('property.soworkorder');
			$sotts = CreateObject('property.sotts');
			$workorder_closed_status = !empty($config['workorder_closed_status']) ? $config['workorder_closed_status'] : false;

			if(!$workorder_closed_status)
			{
				throw new Exception('Order closed status not defined');
			}

			$vouchers_ok = array();
			foreach ($vouchers as $voucher)
			{
				if(!$this->check_payment($voucher['voucher_id']))
				{
					continue;
				}

				$ok = false;
				$order_type = $socommon->get_order_type($voucher['order_id']);
				switch ($order_type)
				{
					case 's_agreement':
						break;
					case 'workorder':
						$workorder = $soworkorder->read_single($voucher['order_id']);
						if($workorder['continuous'])
						{
							$ok = true;
						}
						else
						{
							$ok = $soworkorder->update_status(array('order_id' => $voucher['order_id'],'status' => $workorder_closed_status));
						}
						break;
					case 'ticket':
						$this->db->query("SELECT id FROM fm_tts_tickets WHERE order_id= '{$voucher['order_id']}'", __LINE__, __FILE__);
						$this->db->next_record();
						$ticket_id = $this->db->f('id');
						$ticket = array(
							'status' => 'C8' //Avsluttet og fakturert (C)
						);

						$ok = $sotts->update_status($ticket, $ticket_id);
						break;
					default:
						throw new Exception('Order type not supported');
				}

				if($ok)
				{
					$vouchers_ok = $voucher;
				}

			}
			unset($voucher);

			$metadata = $this->db->metadata('fm_ecobilag');
			$cols = array_keys($metadata);
			foreach ($vouchers_ok as $voucher)
			{
				$value_set = array();
				$this->db->query("SELECT * FROM fm_ecobilag WHERE external_voucher_id= '{$voucher['voucher_id']}'", __LINE__, __FILE__);
				$this->db->next_record();
				foreach ($cols as $col)
				{
					$value_set[$col] = $this->db->f($col);
				}
				$value_set['filnavn'] = date('d.m.Y-H:i:s', phpgwapi_datetime::user_localtime());

				$_cols = implode(',', array_keys($value_set));
				$values = $this->db->validate_insert(array_values($value_set));
				$this->db->query("INSERT INTO fm_ecobilagoverf ({$_cols}) VALUES ({$values})", __LINE__, __FILE__);
				$this->db->query("DELETE FROM fm_ecobilag WHERE external_voucher_id= '{$voucher['voucher_id']}'", __LINE__, __FILE__);
			}
		}

		function check_payment( $voucher_id )
		{
					//curl -s -u portico:BgPor790gfol http://tjenester.usrv.ubergenkom.no/api/agresso/tjeneste?id=88010

			return false;

		}

	}
