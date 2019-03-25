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
	 * example cron : /usr/local/bin/php -q /var/www/html/phpgroupware/property/inc/cron/cron.php default hent_leverandorer_fra_UBW_BK
	 * @package property
	 */
	include_class('property', 'cron_parent', 'inc/cron/');

	class hent_leverandorer_fra_UBW_BK extends property_cron_parent
	{

		var $b_accounts = array();

		public function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location	 = lang('property');
			$this->function_msg	 = 'Sjekk manglende ordreregistering i Agresso fra Portico';
			$this->db			 = & $GLOBALS['phpgw']->db;
			$this->join			 = & $this->db->join;
		}

		function execute()
		{
			$start = time();

			set_time_limit(120);

			require_once PHPGW_SERVER_ROOT . '/property/inc/soap_client/agresso/autoload.php';

			$this->update_vendor();


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

		function update_vendor()
		{
			$metadata = $GLOBALS['phpgw']->db->metadata('fm_vendor_temp');
			if (!$metadata)
			{
				$sql_table = <<<SQL
				CREATE TABLE fm_vendor_temp
				(
				  id integer NOT NULL,
				  status character varying(1),
				  navn character varying(255),
				  adresse character varying(255),
				  postnummer character varying(50),
				  sted character varying(50),
				  organisasjonsnr character varying(50),
				  bankkontonr character varying(50),
				  aktiv integer,
				  email character varying(64),
				  CONSTRAINT fm_vendor_temp_pkey PRIMARY KEY (id)
				);
SQL;
				$GLOBALS['phpgw']->db->query($sql_table, __LINE__, __FILE__);
			}
			else if (empty($metadata['email']))
			{
				$GLOBALS['phpgw']->db->query('ALTER TABLE public.fm_vendor_temp ADD COLUMN email character varying(64)', __LINE__, __FILE__);
			}

			$GLOBALS['phpgw']->db->query('DELETE FROM fm_vendor_temp', __LINE__, __FILE__);

			$error = false;

			$values = array();
			try
			{
				$values = $this->get_vendors();
			}
			catch (Exception $exc)
			{
				$error = true;
				echo $exc->getTraceAsString();
			}

			$GLOBALS['phpgw']->db->transaction_begin();

			$sql = 'INSERT INTO fm_vendor_temp (id, status, navn, adresse, postnummer, sted, organisasjonsnr, bankkontonr, aktiv, email)'
				. ' VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

			/**
			 * remove duplicates
			 */
			if (empty($values[0]['apar_id']))
			{
				_debug_array($values);
				$error = true;
			}
			$vendors = array();
			foreach ($values as $entry)
			{
				$vendors[$entry['apar_id']] = $entry;
			}

			unset($entry);
			/*
			  [_recno] => 0
			  [_section] => D
			  [tab] => A
			  [address] => Postboks 6227
			  [apar_id] => 9901
			  [apar_name] => Bykassen (BY)
			  [comp_reg_no] => 964338531
			  [bank_account] => 52100539187
			  [zip_code] => 5893
			  [place] => BERGEN
			  [e_mail] => agresso.kunde@bergen.kommune.no
			  [status] => N
			 */

			$valueset = array();

			foreach ($vendors as $key => $entry)
			{
				$email_arr	 = explode(';', $entry['e_mail']);
				$valueset[]	 = array
					(
					1	 => array
						(
						'value'	 => (int)$entry['apar_id'],
						'type'	 => PDO::PARAM_INT
					),
					2	 => array
						(
						'value'	 => $entry['status'],
						'type'	 => PDO::PARAM_STR
					),
					3	 => array
						(
						'value'	 => $entry['apar_name'],
						'type'	 => PDO::PARAM_STR
					),
					4	 => array
						(
						'value'	 => $entry['address'],
						'type'	 => PDO::PARAM_STR
					),
					5	 => array
						(
						'value'	 => $entry['zip_code'],
						'type'	 => PDO::PARAM_STR
					),
					6	 => array
						(
						'value'	 => $entry['place'],
						'type'	 => PDO::PARAM_STR
					),
					7	 => array
						(
						'value'	 => $entry['comp_reg_no'],
						'type'	 => PDO::PARAM_STR
					),
					8	 => array
						(
						'value'	 => $entry['bank_account'],
						'type'	 => PDO::PARAM_STR
					),
					9	 => array
						(
						'value'	 => $entry['status'] == 'N' ? 1 : 0,
						'type'	 => PDO::PARAM_INT
					),
					10	 => array
						(
						'value'	 => $email_arr[0],
						'type'	 => PDO::PARAM_STR
					),
				);
			}

			if ($valueset && !$error)
			{
				$GLOBALS['phpgw']->db->insert($sql, $valueset, __LINE__, __FILE__);
			}

			/*
			  [leverandornummer] => 9906
			  [status] => N
			  [navn] => Bergen Vann KF (BV)
			  [adresse] => Postboks 7700
			  [postnummer] => 5020
			  [sted] => BERGEN
			  [organisasjonsNr] => 987328096
			  [bankkontoNr] => 52020801786
			  [aktiv] => 1
			 */
//			_debug_array($valueset);die();


			$sql = "SELECT fm_vendor_temp.*"
				. " FROM fm_vendor RIGHT OUTER JOIN fm_vendor_temp ON (fm_vendor.id = fm_vendor_temp.id)"
				. " WHERE fm_vendor.id IS NULL";

			$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);
			$vendors = array();
			while ($GLOBALS['phpgw']->db->next_record())
			{
				$vendors[] = array(
					1	 => array(
						'value'	 => (int)$GLOBALS['phpgw']->db->f('id'),
						'type'	 => PDO::PARAM_INT
					),
					2	 => array(
						'value'	 => $GLOBALS['phpgw']->db->f('navn'),
						'type'	 => PDO::PARAM_STR
					),
					3	 => array(
						'value'	 => 1,
						'type'	 => PDO::PARAM_INT
					),
					4	 => array(
						'value'	 => 6,
						'type'	 => PDO::PARAM_INT
					),
					5	 => array(
						'value'	 => (int)$GLOBALS['phpgw']->db->f('aktiv'),
						'type'	 => PDO::PARAM_INT
					),
					6	 => array(
						'value'	 => $GLOBALS['phpgw']->db->f('adresse'),
						'type'	 => PDO::PARAM_STR
					),
					7	 => array(
						'value'	 => $GLOBALS['phpgw']->db->f('postnummer'),
						'type'	 => PDO::PARAM_STR
					),
					8	 => array(
						'value'	 => $GLOBALS['phpgw']->db->f('sted'),
						'type'	 => PDO::PARAM_STR
					),
					9	 => array(
						'value'	 => $GLOBALS['phpgw']->db->f('organisasjonsnr'),
						'type'	 => PDO::PARAM_STR
					),
					10	 => array(
						'value'	 => $GLOBALS['phpgw']->db->f('bankkontonr'),
						'type'	 => PDO::PARAM_STR
					),
					11	 => array(
						'value'	 => $GLOBALS['phpgw']->db->f('email'),
						'type'	 => PDO::PARAM_STR
					)
				);
			}
			$sql = 'INSERT INTO fm_vendor (id, org_name,category, owner_id, active, adresse, postnr, poststed, org_nr, konto_nr, email)'
				. ' VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

			if ($valueset && !$error)
			{
				$GLOBALS['phpgw']->db->insert($sql, $vendors, __LINE__, __FILE__);

				$GLOBALS['phpgw']->db->query("UPDATE fm_vendor SET active = 0", __LINE__, __FILE__);

				$GLOBALS['phpgw']->db->query("UPDATE fm_vendor SET"
					. " active = 1,"
					. " org_name = fm_vendor_temp.navn,"
					. " adresse = fm_vendor_temp.adresse,"
					. " postnr = fm_vendor_temp.postnummer,"
					. " poststed = fm_vendor_temp.sted,"
					. " org_nr = fm_vendor_temp.organisasjonsnr"
					. " FROM fm_vendor_temp WHERE fm_vendor.id = fm_vendor_temp.id", __LINE__, __FILE__);

				$GLOBALS['phpgw']->db->query("UPDATE fm_vendor SET"
					. " email = fm_vendor_temp.email"
					. " FROM fm_vendor_temp"
					. " WHERE fm_vendor.id = fm_vendor_temp.id"
					. " AND length(fm_vendor_temp.email) !=0  AND (fm_vendor.email IS NULL OR length(fm_vendor.email) = 0)", __LINE__, __FILE__);
			}

			$GLOBALS['phpgw']->db->transaction_commit();
		}

		function get_vendors_old()
		{
			$this->debug = true;

			static $first_connect	 = false;
			$username				 = 'WEBSER';
			$password				 = 'wser10';
			$client					 = 'BY';
			$TemplateId				 = '6039'; //Spørring på leverandører

			$service	 = new \QueryEngineV201101(array('trace' => 1));
			$Credentials = new \WSCredentials();
			$Credentials->setUsername($username);
			$Credentials->setPassword($password);
			$Credentials->setClient($client);

			// Get the default settings for a template (templateId)
			try
			{
				$searchProp = $service->GetSearchCriteria(new \GetSearchCriteria($TemplateId, true, $Credentials));
				if (!$first_connect)
				{
//					echo "SOAP HEADERS:\n" . $service->__getLastRequestHeaders() . PHP_EOL;
//					echo "SOAP REQUEST:\n" . $service->__getLastRequest() . PHP_EOL;
				}
				$first_connect = true;
			}
			catch (SoapFault $fault)
			{
				$msg = "SOAP Fault:\n faultcode: {$fault->faultcode},\n faultstring: {$fault->faultstring}";
				echo $msg . PHP_EOL;
				trigger_error(nl2br($msg), E_USER_ERROR);
			}

			//Kriterier
			//		_debug_array($searchProp->getGetSearchCriteriaResult()->getSearchCriteriaPropertiesList()->getSearchCriteriaProperties());

			/**
			 * Funkar inte
			 */
			//$searchProp->getGetSearchCriteriaResult()->getSearchCriteriaPropertiesList()->getSearchCriteriaProperties()[1]->setFromValue($vendor_id)->setToValue($vendor_id);
			//$searchProp->getGetSearchCriteriaResult()->getSearchCriteriaPropertiesList()->getSearchCriteriaProperties()[2]->setFromValue($vendor_id)->setToValue($vendor_id);
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
			echo "SOAP HEADERS:\n" . $service->__getLastRequestHeaders() . PHP_EOL;
			echo "SOAP REQUEST:\n" . $service->__getLastRequest() . PHP_EOL;

			$xmlparse	 = CreateObject('property.XmlToArray');
			$xmlparse->setEncoding('utf-8');
			$xmlparse->setDecodesUTF8Automaticly(false);
			$var_result	 = $xmlparse->parse($data);

			if ($var_result)
			{
				$count = count($var_result['Agresso'][0]['AgressoQE']);
				//		if($this->debug)
				{
					_debug_array("{$count} leverandører funnet" . PHP_EOL);
				}
				$ret = $var_result['Agresso'][0]['AgressoQE'];
			}
			else
			{
				//		if($this->debug)
				{
					_debug_array("Leverandører IKKE funnet" . PHP_EOL);
				}
				$ret = array();
			}

			return $ret;
		}

		function get_vendors()
		{
			//Data, connection, auth
			$soapUser		 = "WEBSER";  //  username
			$soapPassword	 = "wser10"; // password
			$CLIENT			 = 'BY';
			$TemplateId		 = '6039'; //Spørring bilag_Portico ordrer
			// xml post structure

			$soap_request = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://services.agresso.com/QueryEngineService/QueryEngineV201101">
	<SOAP-ENV:Body>
		<ns1:GetTemplateResultAsDataSet>
			<ns1:input>
				<ns1:TemplateId>6039</ns1:TemplateId>
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
						<ns1:ColumnName>address_type</ns1:ColumnName>
						<ns1:Description>Adressetype</ns1:Description>
						<ns1:RestrictionType>=</ns1:RestrictionType>
						<ns1:FromValue>1</ns1:FromValue>
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
						<ns1:ColumnName>apar_id</ns1:ColumnName>
						<ns1:Description>Lev.nr</ns1:Description>
						<ns1:RestrictionType>!()</ns1:RestrictionType>
						<ns1:FromValue>´9999´,´99999´,´9999999´,´99999999´</ns1:FromValue>
						<ns1:ToValue></ns1:ToValue>
						<ns1:DataType>10</ns1:DataType>
						<ns1:DataLength>25</ns1:DataLength>
						<ns1:DataCase>2</ns1:DataCase>
						<ns1:IsParameter>true</ns1:IsParameter>
						<ns1:IsVisible>true</ns1:IsVisible>
						<ns1:IsPrompt>false</ns1:IsPrompt>
						<ns1:IsMandatory>false</ns1:IsMandatory>
						<ns1:CanBeOverridden>false</ns1:CanBeOverridden>
						<ns1:RelDateCrit></ns1:RelDateCrit>
					</ns1:SearchCriteriaProperties>
					<ns1:SearchCriteriaProperties>
						<ns1:ColumnName>apar_id</ns1:ColumnName>
						<ns1:Description>Lev.nr</ns1:Description>
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
						<ns1:ColumnName>apar_name</ns1:ColumnName>
						<ns1:Description>Navn</ns1:Description>
						<ns1:RestrictionType>=</ns1:RestrictionType>
						<ns1:FromValue></ns1:FromValue>
						<ns1:ToValue></ns1:ToValue>
						<ns1:DataType>10</ns1:DataType>
						<ns1:DataLength>255</ns1:DataLength>
						<ns1:DataCase>0</ns1:DataCase>
						<ns1:IsParameter>true</ns1:IsParameter>
						<ns1:IsVisible>true</ns1:IsVisible>
						<ns1:IsPrompt>true</ns1:IsPrompt>
						<ns1:IsMandatory>false</ns1:IsMandatory>
						<ns1:CanBeOverridden>true</ns1:CanBeOverridden>
						<ns1:RelDateCrit></ns1:RelDateCrit>
					</ns1:SearchCriteriaProperties>
					<ns1:SearchCriteriaProperties>
						<ns1:ColumnName>comp_reg_no</ns1:ColumnName>
						<ns1:Description>Organisasjonsnr</ns1:Description>
						<ns1:RestrictionType>![]</ns1:RestrictionType>
						<ns1:FromValue></ns1:FromValue>
						<ns1:ToValue></ns1:ToValue>
						<ns1:DataType>10</ns1:DataType>
						<ns1:DataLength>25</ns1:DataLength>
						<ns1:DataCase>2</ns1:DataCase>
						<ns1:IsParameter>true</ns1:IsParameter>
						<ns1:IsVisible>true</ns1:IsVisible>
						<ns1:IsPrompt>false</ns1:IsPrompt>
						<ns1:IsMandatory>false</ns1:IsMandatory>
						<ns1:CanBeOverridden>false</ns1:CanBeOverridden>
						<ns1:RelDateCrit></ns1:RelDateCrit>
					</ns1:SearchCriteriaProperties>
					<ns1:SearchCriteriaProperties>
						<ns1:ColumnName>comp_reg_no</ns1:ColumnName>
						<ns1:Description>Organisasjonsnr</ns1:Description>
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
						<ns1:ColumnName>bank_account</ns1:ColumnName>
						<ns1:Description>Giro</ns1:Description>
						<ns1:RestrictionType>=</ns1:RestrictionType>
						<ns1:FromValue></ns1:FromValue>
						<ns1:ToValue></ns1:ToValue>
						<ns1:DataType>10</ns1:DataType>
						<ns1:DataLength>35</ns1:DataLength>
						<ns1:DataCase>2</ns1:DataCase>
						<ns1:IsParameter>true</ns1:IsParameter>
						<ns1:IsVisible>true</ns1:IsVisible>
						<ns1:IsPrompt>true</ns1:IsPrompt>
						<ns1:IsMandatory>false</ns1:IsMandatory>
						<ns1:CanBeOverridden>true</ns1:CanBeOverridden>
						<ns1:RelDateCrit></ns1:RelDateCrit>
					</ns1:SearchCriteriaProperties>
					<ns1:SearchCriteriaProperties>
						<ns1:ColumnName>status</ns1:ColumnName>
						<ns1:Description>Status</ns1:Description>
						<ns1:RestrictionType>=</ns1:RestrictionType>
						<ns1:FromValue>N</ns1:FromValue>
						<ns1:ToValue></ns1:ToValue>
						<ns1:DataType>10</ns1:DataType>
						<ns1:DataLength>1</ns1:DataLength>
						<ns1:DataCase>2</ns1:DataCase>
						<ns1:IsParameter>true</ns1:IsParameter>
						<ns1:IsVisible>true</ns1:IsVisible>
						<ns1:IsPrompt>false</ns1:IsPrompt>
						<ns1:IsMandatory>false</ns1:IsMandatory>
						<ns1:CanBeOverridden>false</ns1:CanBeOverridden>
						<ns1:RelDateCrit></ns1:RelDateCrit>
					</ns1:SearchCriteriaProperties>
				</ns1:SearchCriteriaPropertiesList>
			</ns1:input>
			<ns1:credentials>
				<ns1:Username>WEBSER</ns1:Username>
				<ns1:Client>BY</ns1:Client>
				<ns1:Password>wser10</ns1:Password>
			</ns1:credentials>
		</ns1:GetTemplateResultAsDataSet>
	</SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML;

			$headers = array(
				"POST /UBW-webservices/service.svc HTTP/1.1",
				"Host: agrweb04a.adm.bgo",
				"Accept: text/xml",
				"Cache-Control: no-cache",
				"User-Agent: PHP-SOAP/7.1.15-1+ubuntu16.04.1+deb.sury.org+2",
				"Content-Type: text/xml; charset=utf-8",
				"SOAPAction: http://services.agresso.com/QueryEngineService/QueryEngineV201101/GetTemplateResultAsDataSet",
				"Content-length: " . strlen($soap_request)
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
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);

			$response	 = curl_exec($ch);
			$httpCode	 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
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
				$ret	 = $var_result['s:Body'][0]['GetTemplateResultAsDataSetResponse']['0']['GetTemplateResultAsDataSetResult'][0]['TemplateResult'][0]['diffgr:diffgram'][0]['Agresso'][0]['AgressoQE'];
				$count	 = count($ret);
				//	if($this->debug)
				{
					_debug_array("{$count} leverandører funnet" . PHP_EOL);
				}
			}
			else
			{
				//	if($this->debug)
				{
					_debug_array("Leverandører IKKE funnet" . PHP_EOL);
				}
				$ret = array();
			}

			return $ret;
		}
	}