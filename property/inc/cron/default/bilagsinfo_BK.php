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
	 * @version $Id$
	 */
	/**
	 * Description
	 * example cron : /usr/local/bin/php -q /var/www/html/phpgroupware/property/inc/cron/cron.php default bilagsinfo_BK
	 * @package property
	 */
	include_class('property', 'cron_parent', 'inc/cron/');

	class bilagsinfo_BK extends property_cron_parent
	{

		var $b_accounts = array();

		public function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location	 = lang('property');
			$this->function_msg	 = 'Hent bilagsinformasjon fra Agresso - og oppdatere Portico';
			$this->db			 = & $GLOBALS['phpgw']->db;
			$this->join			 = & $this->db->join;
		}

		function execute()
		{
			$start = time();

			set_time_limit(900);

			require_once PHPGW_SERVER_ROOT . '/property/inc/soap_client/agresso/autoload.php';

			try
			{
				$this->update_order();
			}
			catch (Exception $e)
			{
				$this->receipt['error'][] = array('msg' => $e->getMessage());
			}

			$sql = "SELECT fm_b_account.id AS b_account_id FROM fm_b_account";// WHERE active = 1";

			$this->db->query($sql, __LINE__, __FILE__);

			$b_accounts = array();
			while ($this->db->next_record())
			{
				$b_accounts[] = $this->db->f('b_account_id');
			}

			$this->b_accounts = $b_accounts;

			$sql = "SELECT external_voucher_id AS bilagsnr"
				. " FROM fm_ecobilagoverf"
				. " WHERE overftid > '20170101'"
				. " AND external_voucher_id IS NOT NULL"
				. " AND external_updated IS NULL";

			$this->db->query($sql, __LINE__, __FILE__);

			$bilagserie = array();
			while ($this->db->next_record())
			{
				$bilagserie[] = $this->db->f('bilagsnr');
			}

			foreach ($bilagserie as $bilagsnr)
			{
				$bilag = $this->get_bilag($bilagsnr);
				if ($bilag)
				{
					$this->update_bilag($bilag, $bilagsnr);
					$this->receipt['message'][] = array('msg' => "{$bilagsnr} er oppdatert med data fra Argesso");
				}
			}

			$msg						 = 'Tidsbruk: ' . (time() - $start) . ' sekunder';
			$this->cron_log($msg, $cron);
			echo "$msg\n";
			$this->receipt['message'][]	 = array('msg' => $msg);
		}

		function cron_log( $receipt = '' )
		{

			$insert_values = array(
				$this->cron,
				date($this->db->datetime_format()),
				$this->function_name,
				$receipt
			);

			$insert_values = $this->db->validate_insert($insert_values);

			$sql = "INSERT INTO fm_cron_log (cron,cron_date,process,message) "
				. "VALUES ($insert_values)";
			$this->db->query($sql, __LINE__, __FILE__);
		}

		private function update_order()
		{
			$config		 = CreateObject('phpgwapi.config', 'property')->read();
			$sql		 = "SELECT DISTINCT pmwrkord_code, external_voucher_id FROM fm_ecobilag";
			$this->db->query($sql, __LINE__, __FILE__);
			$vouchers	 = array();
			while ($this->db->next_record())
			{
				$vouchers[] = array
					(
					'order_id'	 => $this->db->f('pmwrkord_code'),
					'voucher_id' => $this->db->f('external_voucher_id')
				);
			}

			$socommon				 = CreateObject('property.socommon');
			$soworkorder			 = CreateObject('property.soworkorder');
			$sotts					 = CreateObject('property.sotts');
			$workorder_closed_status = !empty($config['workorder_closed_status']) ? $config['workorder_closed_status'] : false;

			if (!$workorder_closed_status)
			{
				throw new Exception('Order closed status not defined');
			}

			$metadata	 = $this->db->metadata('fm_ecobilag');
			$cols		 = array_keys($metadata);

			foreach ($vouchers as $voucher)
			{
				if (!$this->get_bilag($voucher['voucher_id']))
				{
					$this->receipt['error'][] = array('msg' => "{$voucher['voucher_id']} er ikke betalt");
					continue;
				}

				$this->receipt['message'][] = array('msg' => "{$voucher['voucher_id']} er betalt");

				$ok			 = false;
				$order_type	 = $socommon->get_order_type($voucher['order_id']);
				switch ($order_type['type'])
				{
					case 's_agreement':
						break;
					case 'workorder':
						$workorder = $soworkorder->read_single($voucher['order_id']);
						if ($workorder['continuous'])
						{
							$ok = true;
						}
						else
						{
							$ok = $soworkorder->update_status(array('order_id'	 => $voucher['order_id'],
								'status'	 => $workorder_closed_status));
						}
						break;
					case 'ticket':
						$this->db->query("SELECT id, continuous FROM fm_tts_tickets WHERE order_id= '{$voucher['order_id']}'", __LINE__, __FILE__);
						$this->db->next_record();
						$ticket_id = $this->db->f('id');

						if ($this->db->f('continuous'))
						{
							$ok = true;
						}
						else
						{
							$ticket	 = array(
								'status' => 'C8' //Avsluttet og fakturert (C)
							);
							$ok		 = $sotts->update_status($ticket, $ticket_id);
						}
						break;
					default:
						throw new Exception('Order type not supported');
				}

				if ($ok)
				{
					_debug_array("Overfører {$voucher['voucher_id']} til historikk" . PHP_EOL);

					$this->db->transaction_begin();
					$value_set = array();
					$this->db->query("SELECT * FROM fm_ecobilag WHERE external_voucher_id= '{$voucher['voucher_id']}'", __LINE__, __FILE__);
					$this->db->next_record();
					foreach ($cols as $col)
					{
						$value_set[$col] = $this->db->f($col);
					}
					$value_set['filnavn']	 = date('d.m.Y-H:i:s', phpgwapi_datetime::user_localtime());
					$value_set['ordrebelop'] = $value_set['belop'];
					unset($value_set['pre_transfer']);

					$_cols						 = implode(',', array_keys($value_set));
					$values						 = $this->db->validate_insert(array_values($value_set));
					$this->db->query("INSERT INTO fm_ecobilagoverf ({$_cols}) VALUES ({$values})", __LINE__, __FILE__);
					$this->db->query("DELETE FROM fm_ecobilag WHERE external_voucher_id= '{$voucher['voucher_id']}'", __LINE__, __FILE__);
					$this->db->transaction_commit();
					$this->receipt['message'][]	 = array('msg' => "{$voucher['voucher_id']} er overført til historikk");
				}
			}
		}

		function update_bilag( $bilag, $bilagsnr )
		{
			$value_set	 = array
				(
				'periode'			 => $bilag[0]['period'],
				//			'pmwrkord_code' => $bilag[0]['order_id'],
				'mvakode'			 => 0,
				'netto_belop'		 => 0,
				'external_updated'	 => 1
			);
			$tax_code	 = 0;
			$netto_belop = 0;

			phpgwapi_cache::system_clear('property', "budget_order_{$bilag[0]['order_id']}");

			foreach ($bilag as $line)
			{
				if ($line['account'] == 2327010)
				{
					//				$value_set['belop'] = $line['amount'] * -1;
				}
				if (in_array($line['account'], $this->b_accounts))
				{
					$value_set['netto_belop']	 += $line['amount'];
					$value_set['mvakode']		 = $line['tax_code'] == '6A' ? 0 : $line['tax_code'];
				}
			}

			$value_set = $this->db->validate_update($value_set);
			$this->db->query("UPDATE fm_ecobilagoverf SET {$value_set} WHERE external_voucher_id = '{$bilagsnr}'", __LINE__, __FILE__);

			if ($this->debug)
			{
				_debug_array($value_set . PHP_EOL);
			}
		}

		function get_bilag_old( $bilagsnr )
		{
			static $first_connect	 = false;
			$username				 = 'WEBSER';
			$password				 = 'wser10';
			$client					 = 'BY';
			$TemplateId				 = '11176'; //Spørring bilag_Portico ordrer

			$service	 = new \QueryEngineV201101(array('trace' => 1));
			$Credentials = new \WSCredentials();
			$Credentials->setUsername($username);
			$Credentials->setPassword($password);
			$Credentials->setClient($client);

			echo "tester bilag {$bilagsnr}" . PHP_EOL;

			// Get the default settings for a template (templateId)
			try
			{
				$searchProp = $service->GetSearchCriteria(new \GetSearchCriteria($TemplateId, true, $Credentials));
				if (!$first_connect)
				{
					echo "SOAP HEADERS:\n" . $service->__getLastRequestHeaders() . PHP_EOL;
					echo "SOAP REQUEST:\n" . $service->__getLastRequest() . PHP_EOL;
				}
				$first_connect = true;
			}
			catch (SoapFault $fault)
			{
				$msg = "SOAP Fault:\n faultcode: {$fault->faultcode},\n faultstring: {$fault->faultstring}";
				echo $msg . PHP_EOL;
				trigger_error(nl2br($msg), E_USER_ERROR);
			}

			$searchProp->getGetSearchCriteriaResult()->getSearchCriteriaPropertiesList()->getSearchCriteriaProperties()[0]->setFromValue($bilagsnr)->setToValue($bilagsnr);
			$searchProp->getGetSearchCriteriaResult()->getSearchCriteriaPropertiesList()->getSearchCriteriaProperties()[2]->setFromValue('201701')->setToValue('201812');

			// Create the InputForTemplateResult class and set values
			$input									 = new InputForTemplateResult($TemplateId);
			$options								 = $service->GetTemplateResultOptions(new \GetTemplateResultOptions($Credentials));
			$options->RemoveHiddenColumns			 = true;
			$options->ShowDescriptions				 = true;
			$options->Aggregated					 = false;
			$options->OverrideAggregation			 = false;
			$options->CalculateFormulas				 = false;
			$options->FormatAlternativeBreakColumns	 = false;
			$options->FirstRecord					 = false;
			$options->LastRecord					 = false;

			$input->setTemplateResultOptions($options);
			// Get new values to SearchCriteria (if that’s what you want to do
			$input->setSearchCriteriaPropertiesList($searchProp->getGetSearchCriteriaResult()->getSearchCriteriaPropertiesList());
			//Retrieve result

			$result = $service->GetTemplateResultAsDataSet(new \GetTemplateResultAsDataSet($input, $Credentials));

			$data = $result->getGetTemplateResultAsDataSetResult()->getTemplateResult()->getAny();

			$xmlparse	 = CreateObject('property.XmlToArray');
			$xmlparse->setEncoding('utf-8');
			$xmlparse->setDecodesUTF8Automaticly(false);
			$var_result	 = $xmlparse->parse($data);

			if ($var_result)
			{
				//		if($this->debug)
				{
					_debug_array("Bilag {$bilagsnr} ER betalt" . PHP_EOL);
				}
				$ret = $var_result['Agresso'][0]['AgressoQE'];
			}
			else
			{
				//		if($this->debug)
				{
					_debug_array("Bilag {$bilagsnr} er IKKE betalt" . PHP_EOL);
				}
				$ret = array();
			}

			return $ret;
		}

		function get_bilag( $bilagsnr )
		{
			//Data, connection, auth
			$soapUser		 = "WEBSER";  //  username
			$soapPassword	 = "wser10"; // password
			$CLIENT			 = 'BY';

			// xml post structure
			$soap_request = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://services.agresso.com/QueryEngineService/QueryEngineV201101">
	<SOAP-ENV:Body>
		<ns1:GetTemplateResultAsDataSet>
			<ns1:input>
				<ns1:TemplateId>11176</ns1:TemplateId>
				<ns1:TemplateResultOptions>
					<ns1:ShowDescriptions>true</ns1:ShowDescriptions>
					<ns1:Aggregated>false</ns1:Aggregated>
					<ns1:OverrideAggregation>false</ns1:OverrideAggregation>
					<ns1:CalculateFormulas>false</ns1:CalculateFormulas>
					<ns1:FormatAlternativeBreakColumns>false</ns1:FormatAlternativeBreakColumns>
					<ns1:RemoveHiddenColumns>true</ns1:RemoveHiddenColumns>
					<ns1:FirstRecord>0</ns1:FirstRecord>
					<ns1:LastRecord>0</ns1:LastRecord>
				</ns1:TemplateResultOptions>
				<ns1:SearchCriteriaPropertiesList>
					<ns1:SearchCriteriaProperties>
						<ns1:ColumnName>voucher_no</ns1:ColumnName>
						<ns1:Description>Bilagsnr</ns1:Description>
						<ns1:RestrictionType>=</ns1:RestrictionType>
						<ns1:FromValue>{$bilagsnr}</ns1:FromValue>
						<ns1:ToValue>{$bilagsnr}</ns1:ToValue>
						<ns1:DataType>21</ns1:DataType>
						<ns1:DataLength>18</ns1:DataLength>
						<ns1:DataCase>0</ns1:DataCase>
						<ns1:IsParameter>true</ns1:IsParameter>
						<ns1:IsVisible>true</ns1:IsVisible>
						<ns1:IsPrompt>false</ns1:IsPrompt>
						<ns1:IsMandatory>false</ns1:IsMandatory>
						<ns1:CanBeOverridden>false</ns1:CanBeOverridden>
						<ns1:RelDateCrit></ns1:RelDateCrit>
					</ns1:SearchCriteriaProperties>
					<ns1:SearchCriteriaProperties>
						<ns1:ColumnName>order_id</ns1:ColumnName>
						<ns1:Description>Ordrenr</ns1:Description>
						<ns1:RestrictionType>&lt;&gt;</ns1:RestrictionType>
						<ns1:FromValue>40000000</ns1:FromValue>
						<ns1:ToValue>49999999</ns1:ToValue>
						<ns1:DataType>21</ns1:DataType>
						<ns1:DataLength>18</ns1:DataLength>
						<ns1:DataCase>0</ns1:DataCase>
						<ns1:IsParameter>true</ns1:IsParameter>
						<ns1:IsVisible>true</ns1:IsVisible>
						<ns1:IsPrompt>false</ns1:IsPrompt>
						<ns1:IsMandatory>false</ns1:IsMandatory>
						<ns1:CanBeOverridden>false</ns1:CanBeOverridden>
						<ns1:RelDateCrit></ns1:RelDateCrit>
					</ns1:SearchCriteriaProperties>
					<ns1:SearchCriteriaProperties>
						<ns1:ColumnName>period</ns1:ColumnName>
						<ns1:Description>Periode</ns1:Description>
						<ns1:RestrictionType>&lt;&gt;</ns1:RestrictionType>
						<ns1:FromValue>201701</ns1:FromValue>
						<ns1:ToValue>201812</ns1:ToValue>
						<ns1:DataType>3</ns1:DataType>
						<ns1:DataLength>6</ns1:DataLength>
						<ns1:DataCase>2</ns1:DataCase>
						<ns1:IsParameter>true</ns1:IsParameter>
						<ns1:IsVisible>true</ns1:IsVisible>
						<ns1:IsPrompt>false</ns1:IsPrompt>
						<ns1:IsMandatory>false</ns1:IsMandatory>
						<ns1:CanBeOverridden>false</ns1:CanBeOverridden>
						<ns1:RelDateCrit></ns1:RelDateCrit>
					</ns1:SearchCriteriaProperties>
					<ns1:SearchCriteriaProperties>
						<ns1:ColumnName>client</ns1:ColumnName>
						<ns1:Description>Firma</ns1:Description>
						<ns1:RestrictionType>=</ns1:RestrictionType>
						<ns1:FromValue>$CLIENT</ns1:FromValue>
						<ns1:ToValue></ns1:ToValue>
						<ns1:DataType>10</ns1:DataType>
						<ns1:DataLength>25</ns1:DataLength>
						<ns1:DataCase>2</ns1:DataCase>
						<ns1:IsParameter>true</ns1:IsParameter>
						<ns1:IsVisible>false</ns1:IsVisible>
						<ns1:IsPrompt>false</ns1:IsPrompt>
						<ns1:IsMandatory>false</ns1:IsMandatory>
						<ns1:CanBeOverridden>false</ns1:CanBeOverridden>
						<ns1:RelDateCrit></ns1:RelDateCrit>
					</ns1:SearchCriteriaProperties>
				</ns1:SearchCriteriaPropertiesList>
			</ns1:input>
			<ns1:credentials>
				<ns1:Username>{$soapUser}</ns1:Username>
				<ns1:Client>BY</ns1:Client>
				<ns1:Password>{$soapPassword}</ns1:Password>
			</ns1:credentials>
		</ns1:GetTemplateResultAsDataSet>
	</SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML;

			$headers = array(
				"Accept: text/xml",
				"Cache-Control: no-cache",
				"User-Agent: PHP-SOAP/7.1.15-1+ubuntu16.04.1+deb.sury.org+2",
				"Content-Type: text/xml; charset=utf-8",
				"SOAPAction: http://services.agresso.com/QueryEngineService/QueryEngineV201101/GetTemplateResultAsDataSet",
				"Content-length: " . strlen($soap_request)
			);

			//		$soapUrl = "http://10.19.14.242/agresso-webservices/service.svc?QueryEngineService/QueryEngineV201101"; // asmx URL of WSDL
			$soapUrl = "http://agrpweb.adm.bgo/UBW-webservices/service.svc?QueryEngineService/QueryEngineV201101";

			$ch = curl_init($soapUrl);

			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $soap_request);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			//		curl_setopt($ch, CURLOPT_VERBOSE, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);

			$response = curl_exec($ch);

			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			curl_close($ch);

			// converting
			$response1 = str_replace(array("<soap:Body>", "</soap:Body>"), "", $response);
			// convertingc to XML

			$xmlparse	 = CreateObject('property.XmlToArray');
			$xmlparse->setEncoding('utf-8');
			$xmlparse->setDecodesUTF8Automaticly(false);
			$var_result	 = $xmlparse->parse($response1);

			if (!empty($var_result['s:Body'][0]['GetTemplateResultAsDataSetResponse']['0']['GetTemplateResultAsDataSetResult'][0]['TemplateResult'][0]['diffgr:diffgram'][0]['Agresso'][0]['AgressoQE']))
			{
				//		if($this->debug)
				{
					_debug_array("Bilag {$bilagsnr} ER betalt" . PHP_EOL);
				}
				$ret = $var_result['s:Body'][0]['GetTemplateResultAsDataSetResponse']['0']['GetTemplateResultAsDataSetResult'][0]['TemplateResult'][0]['diffgr:diffgram'][0]['Agresso'][0]['AgressoQE'];
			}
			else
			{
				//		if($this->debug)
				{
					_debug_array("Bilag {$bilagsnr} er IKKE betalt" . PHP_EOL);
				}
				$ret = array();
			}

			return $ret;
		}
	}