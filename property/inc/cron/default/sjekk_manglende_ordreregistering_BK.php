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
	 * example cron : /usr/local/bin/php -q /var/www/html/phpgroupware/property/inc/cron/cron.php default sjekk_manglende_ordreregistering_BK
	 * @package property
	 */
	include_class('property', 'cron_parent', 'inc/cron/');

	class sjekk_manglende_ordreregistering_BK extends property_cron_parent
	{
		var $b_accounts = array();

		public function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location = lang('property');
			$this->function_msg = 'Sjekk manglende ordreregistering i Agresso fra Portico';
			$this->db = & $GLOBALS['phpgw']->db;
			$this->join = & $this->db->join;
		}

		function execute()
		{
			$start = time();

			set_time_limit(900);

			require_once PHPGW_SERVER_ROOT . '/property/inc/soap_client/agresso/autoload.php';

			$sql = "SELECT fm_workorder.id AS order_id, fm_workorder.status,"
				. " to_char(to_timestamp(fm_workorder.entry_date ),'DD/MM-YYYY') as date,"
				. " to_char(to_timestamp(fm_workorder.entry_date ),'YYYYMMDD') as sorteringsdato,"
				. " fm_workorder.account_id as kostnadsart,"
				. " to_char(to_timestamp(fm_workorder.order_sent ),'DD/MM-YYYY') as overfort_dato, "
				. " fm_workorder.combined_cost as budget,"
				. " account_firstname, account_lastname"
				. " FROM fm_workorder JOIN fm_workorder_status ON fm_workorder.status = fm_workorder_status.id"
				. " JOIN phpgw_accounts ON fm_workorder.user_id = phpgw_accounts.account_id"
				. " WHERE (order_sent IS NOT NULL OR canceled IS NULL)"
				. " AND fm_workorder.id > 45000000"
				. " AND verified_transfered IS NULL"
				. " ORDER BY sorteringsdato , fm_workorder.id";

			$this->db->query($sql, __LINE__, __FILE__);

			$orderserie = array();
			while ($this->db->next_record())
			{
				$orderserie[] = array(
					'order_id' => $this->db->f('order_id'),
					'status' => $this->db->f('status'),
					'date' => $this->db->f('date'),
					'kostnadsart' => $this->db->f('kostnadsart'),
					'overfort_dato' => $this->db->f('overfort_dato'),
					'budget' => $this->db->f('budget'),
					'account_lastname' => $this->db->f('account_lastname'),
					'account_firstname' => $this->db->f('account_firstname'),
					);
			}

			$html =<<<HTML
			<!DOCTYPE html>
			<html>
				<head>
					<style>
					table, th, td {
						border: 1px solid black;
					}
					th, td {
						padding: 10px;
					}
					th {
						text-align: left;
					}
					</style>
				</head>
				<body>
					<table>
					 <caption>Manglende ordreregistering i Agresso fra Portico</caption>
					<tr>
						<th>Ordre</th>
						<th>Dato</th>
						<th>Bestillingssum</th>
						<th>Bestiller</th>
						<th>#</th>
					</tr>
HTML;
			$i = 0;
			foreach ($orderserie as $entry)
			{
				$order_id = $entry['order_id'];
				$order = $this->get_order($order_id);
				if($order)
				{
					$this->db->query("UPDATE fm_workorder SET verified_transfered = 1 WHERE id = '{$order_id}'", __LINE__, __FILE__);
					$this->receipt['message'][] = array('msg' => "{$order_id} er oppdatert som overført til Argesso");
				}
				else
				{
					$this->receipt['error'][] = array('msg' => "Ordre: {$order_id}; dato: {$entry['date']}; budsjett:{$entry['budget']}; bestiller: {$entry['account_lastname']}, {$entry['account_firstname']};  er ikke registrert i Agresso");

					$i++;

					$order_link = '<a href ="' . $GLOBALS['phpgw']->link('/index.php', array(
						'menuaction' => 'property.uiworkorder.edit',
						'id' => $order_id), false, true) . "\">{$order_id}</a>";

					$html .=<<<HTML

					<tr>
						<td>{$order_link}</td>
						<td>{$entry['date']}</td>
						<td>{$entry['budget']}</td>
						<td>{$entry['account_lastname']}, {$entry['account_firstname']}</td>
						<td>{$i}</td>
					</tr>
HTML;
				}
			}

			$html .=<<<HTML
					</table>
				</body>
			</html>
HTML;

			$subject = 'Manglende ordreregistering i Agresso fra Portico';

			$toarray = array('hc483@bergen.kommune.no');
			$to = implode(';', $toarray);

			try
			{
				$rc = CreateObject('phpgwapi.send')->msg('email', $to, $subject, $html, '', $cc='', $bcc='', 'hc483@bergen.kommune.no', 'Ikke svar', 'html');
			}
			catch (Exception $e)
			{
				$this->receipt['error'][] = array('msg' => $e->getMessage());
			}


			$msg = 'Tidsbruk: ' . (time() - $start) . ' sekunder';
			$this->cron_log($msg, $cron);
			echo "$msg\n";
			$this->receipt['message'][] = array('msg' => $msg);

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



		function get_order_old($order_id)
		{
			static $first_connect = false;
			$username = 'WEBSER';
			$password = 'wser10';
			$client = 'BY';
			$TemplateId = '10771'; //Spørring bilag_Portico ordrer

			$service = new \QueryEngineV201101(array('trace' => 1));
			$Credentials = new \WSCredentials();
			$Credentials->setUsername($username);
			$Credentials->setPassword($password);
			$Credentials->setClient($client);

			echo "tester ordre {$order_id}". PHP_EOL;

			// Get the default settings for a template (templateId)
			try
			{
				$searchProp = $service->GetSearchCriteria(new \GetSearchCriteria($TemplateId, true, $Credentials));
				if(!$first_connect)
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
			$searchProp->getGetSearchCriteriaResult()->getSearchCriteriaPropertiesList()->getSearchCriteriaProperties()[4]->setFromValue($order_id)->setToValue($order_id);
			$searchProp->getGetSearchCriteriaResult()->getSearchCriteriaPropertiesList()->getSearchCriteriaProperties()[6]->setFromValue('201701')->setToValue('209912');
//			_debug_array($searchProp->getGetSearchCriteriaResult()->getSearchCriteriaPropertiesList()->getSearchCriteriaProperties());

			// Create the InputForTemplateResult class and set values
			$input = new InputForTemplateResult($TemplateId);
			$options = $service->GetTemplateResultOptions(new \GetTemplateResultOptions($Credentials));
			$options->RemoveHiddenColumns = true;
			$options->ShowDescriptions = true;
			$options->Aggregated = false;
			$options->OverrideAggregation= false;
			$options->CalculateFormulas= false;
			$options->FormatAlternativeBreakColumns= false;
			$options->FirstRecord= false;
			$options->LastRecord= false;

			$input->setTemplateResultOptions($options);
			// Get new values to SearchCriteria (if that’s what you want to do
			$input->setSearchCriteriaPropertiesList($searchProp->getGetSearchCriteriaResult()->getSearchCriteriaPropertiesList());
			//Retrieve result

			$result = $service->GetTemplateResultAsDataSet(new \GetTemplateResultAsDataSet($input, $Credentials));

			$data = $result->getGetTemplateResultAsDataSetResult()->getTemplateResult()->getAny();
//			echo "SOAP HEADERS:\n" . $service->__getLastRequestHeaders() . PHP_EOL;
//			echo "SOAP REQUEST:\n" . $service->__getLastRequest() . PHP_EOL;

			$xmlparse = CreateObject('property.XmlToArray');
			$xmlparse->setEncoding('utf-8');
			$xmlparse->setDecodesUTF8Automaticly(false);
			$var_result = $xmlparse->parse($data);

			if($var_result)
			{
		//		if($this->debug)
				{
					_debug_array("Ordre {$order_id} ER registrert" . PHP_EOL);
				}
				$ret = $var_result['Agresso'][0]['AgressoQE'];
			}
			else
			{
		//		if($this->debug)
				{
					_debug_array("Ordre {$order_id} er IKKE registrert" . PHP_EOL);
				}
				$ret = array();
			}

			return $ret;

		}

		function get_order( $order_id )
		{
			//Data, connection, auth
			$soapUser = "WEBSER";  //  username
			$soapPassword = "wser10"; // password
			$CLIENT = 'BY';
			$TemplateId = '10771'; //Spørring bilag_Portico ordrer

			// xml post structure

			$soap_request = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
		<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://services.agresso.com/QueryEngineService/QueryEngineV201101">
			<SOAP-ENV:Body>
				<ns1:GetTemplateResultAsDataSet>
					<ns1:input>
						<ns1:TemplateId>{$TemplateId}</ns1:TemplateId>
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
								<ns1:ColumnName>dim_2</ns1:ColumnName>
								<ns1:Description>Tjeneste</ns1:Description>
								<ns1:RestrictionType>=</ns1:RestrictionType>
								<ns1:FromValue></ns1:FromValue>
								<ns1:ToValue></ns1:ToValue>
								<ns1:DataType>10</ns1:DataType>
								<ns1:DataLength>25</ns1:DataLength>
								<ns1:DataCase>2</ns1:DataCase>
								<ns1:IsParameter>true</ns1:IsParameter>
								<ns1:IsVisible>true</ns1:IsVisible>
								<ns1:IsPrompt>true</ns1:IsPrompt>
								<ns1:IsMandatory>false</ns1:IsMandatory>
								<ns1:CanBeOverridden>true</ns1:CanBeOverridden>
								<ns1:RelDateCrit></ns1:RelDateCrit>
							</ns1:SearchCriteriaProperties>
							<ns1:SearchCriteriaProperties>
								<ns1:ColumnName>dim_1</ns1:ColumnName>
								<ns1:Description>Ansvar</ns1:Description>
								<ns1:RestrictionType>=</ns1:RestrictionType>
								<ns1:FromValue></ns1:FromValue>
								<ns1:ToValue></ns1:ToValue>
								<ns1:DataType>10</ns1:DataType>
								<ns1:DataLength>25</ns1:DataLength>
								<ns1:DataCase>2</ns1:DataCase>
								<ns1:IsParameter>true</ns1:IsParameter>
								<ns1:IsVisible>true</ns1:IsVisible>
								<ns1:IsPrompt>true</ns1:IsPrompt>
								<ns1:IsMandatory>false</ns1:IsMandatory>
								<ns1:CanBeOverridden>true</ns1:CanBeOverridden>
								<ns1:RelDateCrit></ns1:RelDateCrit>
							</ns1:SearchCriteriaProperties>
							<ns1:SearchCriteriaProperties>
								<ns1:ColumnName>apar_id</ns1:ColumnName>
								<ns1:Description>Leverandørnr</ns1:Description>
								<ns1:RestrictionType>=</ns1:RestrictionType>
								<ns1:FromValue></ns1:FromValue>
								<ns1:ToValue></ns1:ToValue>
								<ns1:DataType>10</ns1:DataType>
								<ns1:DataLength>25</ns1:DataLength>
								<ns1:DataCase>2</ns1:DataCase>
								<ns1:IsParameter>true</ns1:IsParameter>
								<ns1:IsVisible>true</ns1:IsVisible>
								<ns1:IsPrompt>true</ns1:IsPrompt>
								<ns1:IsMandatory>false</ns1:IsMandatory>
								<ns1:CanBeOverridden>true</ns1:CanBeOverridden>
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
							<ns1:SearchCriteriaProperties>
								<ns1:ColumnName>order_id</ns1:ColumnName>
								<ns1:Description>Ordrenr</ns1:Description>
								<ns1:RestrictionType>&lt;&gt;</ns1:RestrictionType>
								<ns1:FromValue>{$order_id}</ns1:FromValue>
								<ns1:ToValue>{$order_id}</ns1:ToValue>
								<ns1:DataType>21</ns1:DataType>
								<ns1:DataLength>18</ns1:DataLength>
								<ns1:DataCase>0</ns1:DataCase>
								<ns1:IsParameter>true</ns1:IsParameter>
								<ns1:IsVisible>true</ns1:IsVisible>
								<ns1:IsPrompt>true</ns1:IsPrompt>
								<ns1:IsMandatory>false</ns1:IsMandatory>
								<ns1:CanBeOverridden>true</ns1:CanBeOverridden>
								<ns1:RelDateCrit></ns1:RelDateCrit>
							</ns1:SearchCriteriaProperties>
							<ns1:SearchCriteriaProperties>
								<ns1:ColumnName>responsible2</ns1:ColumnName>
								<ns1:Description>Rekvirent/Brukerid</ns1:Description>
								<ns1:RestrictionType>=</ns1:RestrictionType>
								<ns1:FromValue></ns1:FromValue>
								<ns1:ToValue></ns1:ToValue>
								<ns1:DataType>10</ns1:DataType>
								<ns1:DataLength>25</ns1:DataLength>
								<ns1:DataCase>2</ns1:DataCase>
								<ns1:IsParameter>true</ns1:IsParameter>
								<ns1:IsVisible>true</ns1:IsVisible>
								<ns1:IsPrompt>true</ns1:IsPrompt>
								<ns1:IsMandatory>false</ns1:IsMandatory>
								<ns1:CanBeOverridden>true</ns1:CanBeOverridden>
								<ns1:RelDateCrit></ns1:RelDateCrit>
							</ns1:SearchCriteriaProperties>
							<ns1:SearchCriteriaProperties>
								<ns1:ColumnName>period</ns1:ColumnName>
								<ns1:Description>Periode</ns1:Description>
								<ns1:RestrictionType>&lt;&gt;</ns1:RestrictionType>
								<ns1:FromValue>201701</ns1:FromValue>
								<ns1:ToValue>209912</ns1:ToValue>
								<ns1:DataType>3</ns1:DataType>
								<ns1:DataLength>8</ns1:DataLength>
								<ns1:DataCase>2</ns1:DataCase>
								<ns1:IsParameter>true</ns1:IsParameter>
								<ns1:IsVisible>true</ns1:IsVisible>
								<ns1:IsPrompt>true</ns1:IsPrompt>
								<ns1:IsMandatory>false</ns1:IsMandatory>
								<ns1:CanBeOverridden>true</ns1:CanBeOverridden>
								<ns1:RelDateCrit></ns1:RelDateCrit>
							</ns1:SearchCriteriaProperties>
							<ns1:SearchCriteriaProperties>
								<ns1:ColumnName>responsible</ns1:ColumnName>
								<ns1:Description>Att.ansvarlig</ns1:Description>
								<ns1:RestrictionType>=</ns1:RestrictionType>
								<ns1:FromValue></ns1:FromValue>
								<ns1:ToValue></ns1:ToValue>
								<ns1:DataType>10</ns1:DataType>
								<ns1:DataLength>25</ns1:DataLength>
								<ns1:DataCase>2</ns1:DataCase>
								<ns1:IsParameter>true</ns1:IsParameter>
								<ns1:IsVisible>false</ns1:IsVisible>
								<ns1:IsPrompt>true</ns1:IsPrompt>
								<ns1:IsMandatory>false</ns1:IsMandatory>
								<ns1:CanBeOverridden>true</ns1:CanBeOverridden>
								<ns1:RelDateCrit></ns1:RelDateCrit>
							</ns1:SearchCriteriaProperties>
							<ns1:SearchCriteriaProperties>
								<ns1:ColumnName>status</ns1:ColumnName>
								<ns1:Description>Status</ns1:Description>
								<ns1:RestrictionType>=</ns1:RestrictionType>
								<ns1:FromValue></ns1:FromValue>
								<ns1:ToValue></ns1:ToValue>
								<ns1:DataType>10</ns1:DataType>
								<ns1:DataLength>1</ns1:DataLength>
								<ns1:DataCase>2</ns1:DataCase>
								<ns1:IsParameter>true</ns1:IsParameter>
								<ns1:IsVisible>true</ns1:IsVisible>
								<ns1:IsPrompt>true</ns1:IsPrompt>
								<ns1:IsMandatory>false</ns1:IsMandatory>
								<ns1:CanBeOverridden>true</ns1:CanBeOverridden>
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
			"Content-length: ".strlen($soap_request)
			);

//			$soapUrl = "http://10.19.14.242/agresso-webservices/service.svc?QueryEngineService/QueryEngineV201101"; // asmx URL of WSDL
			$soapUrl = "http://agrpweb.adm.bgo/UBW-webservices/service.svc?QueryEngineService/QueryEngineV201101";

			$ch = curl_init($soapUrl);

			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $soap_request);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT,10);

			$response = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			curl_close($ch);

			// converting
			$response1 = str_replace(array("<soap:Body>", "</soap:Body>"),"",$response);
			// convertingc to XML

			$xmlparse = CreateObject('property.XmlToArray');
			$xmlparse->setEncoding('utf-8');
			$xmlparse->setDecodesUTF8Automaticly(false);
			$var_result = $xmlparse->parse($response1);

			if(!empty($var_result['s:Body'][0]['GetTemplateResultAsDataSetResponse']['0']['GetTemplateResultAsDataSetResult'][0]['TemplateResult'][0]['diffgr:diffgram'][0]['Agresso'][0]['AgressoQE']))
			{
				if($this->debug)
				{
					_debug_array("Ordre {$order_id} ER registrert" . PHP_EOL);
				}
				$ret = $var_result['s:Body'][0]['GetTemplateResultAsDataSetResponse']['0']['GetTemplateResultAsDataSetResult'][0]['TemplateResult'][0]['diffgr:diffgram'][0]['Agresso'][0]['AgressoQE'];
			}
			else
			{
				if($this->debug)
				{
					_debug_array("Ordre {$order_id} er IKKE registrert" . PHP_EOL);
				}
				$ret = array();
			}

			return $ret;
		}
	}