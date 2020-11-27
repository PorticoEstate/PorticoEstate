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

	class opne_bestillinger_BK_EBF extends property_cron_parent
	{

		var $b_accounts = array();

		public function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location	 = lang('property');
			$this->function_msg	 = 'Hent opne bestillinger som er løpende, og skal kunne motta flere fakturaer';
			$this->db			 = & $GLOBALS['phpgw']->db;
			$this->join			 = & $this->db->join;
		}

		function execute()
		{
			$start = time();

			$orders = $this->get_orders();

			$this->get_agresso_status($orders);

			$cols	 = array('order_id', 'status', 'agresso_status', 'dato', 'brukernavn', 'bestiller');
			if (!$fp		 = fopen('php://temp ', 'w'))
			{
				die('Unable to write to "php://output" - pleace notify the Administrator');
			}

			$date = date('Ymd');

			$BOM = "\xEF\xBB\xBF"; // UTF-8 BOM
			fwrite($fp, $BOM); // NEW LINE
			fputcsv($fp, $cols, ';');

			$html = <<<HTML
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
					 <caption>Portico ordrer som må stå åpne pr {$date}</caption>
					<tr>
						<th>Ordre_id</th>
						<th>Portico status</th>
						<th>Agresso status</th>
						<th>dato</th>
						<th>Brukernavn</th>
						<th>Bestiller</th>
					</tr>
HTML;

			$update_orders = array();
			foreach ($orders as $entry)
			{
				if (empty($entry['agresso_status']) || $entry['agresso_status'] == "O")
				{
					continue;
				}

				$update_orders[] = $entry['order_id'];

				$entry['order_link'] = '<a href ="' . $GLOBALS['phpgw']->link('/index.php', array(
						'menuaction' => 'property.uiworkorder.edit',
						'id'		 => $entry['order_id']), false, true) . "\">{$entry['order_id']}</a>";

				$row = array();

				$html .= <<<HTML
					<tr>
HTML;

				foreach ($cols as $key)
				{
					$row[] = $entry[$key];

					if ($key == 'order_id')
					{
						$_key = 'order_link';
					}
					else
					{
						$_key = $key;
					}

					$html .= <<<HTML
						<td>{$entry[$_key]}</td>
HTML;
				}

				fputcsv($fp, $row, ';');

				$html .= <<<HTML
			</tr>
HTML;
			}

			// Place stream pointer at beginning
			rewind($fp);

			// Return the data
			$content = stream_get_contents($fp);
			fclose($fp);
			$html	 .= <<<HTML
					</table>
				</body>
			</html>
HTML;

			$attachments = array();

			if ($content)
			{
				$dir = "{$GLOBALS['phpgw_info']['server']['temp_dir']}/csv_files";

				//save the file
				if (!file_exists($dir))
				{
					mkdir($dir, 0777);
				}
				$fname	 = tempnam($dir . '/', 'CSV_') . '.csv';
				$fp		 = fopen($fname, 'w');
				fwrite($fp, $content);
				fclose($fp);

				$attachments[] = array
					(
					'file'	 => $fname,
					'name'	 => "Portico_open_order_{$date}.csv",
					'type'	 => 'text/csv'
				);
			}

			$subject = 'Åpne Portico bestillinger som er løpende, og skal kunne motta flere fakturaer';

			$toarray = array(
				'hc483@bergen.kommune.no'
			);
			$to		 = implode(';', $toarray);

			if (false)
			{
				try
				{
					$rc	 = CreateObject('phpgwapi.send')->msg('email', $to, $subject, $html, '', $cc	 = '', $bcc = '', 'hc483@bergen.kommune.no', 'Ikke svar', 'html', '', $attachments);
				}
				catch (Exception $e)
				{
					$this->receipt['error'][] = array('msg' => $e->getMessage());
				}
			}

			unlink($fname);

			if ($update_orders)
			{
				$toarray = array(
//					'hc483@bergen.kommune.no',
					'Postmottak LRS System <LRS.System@bergen.kommune.no>'
				);
				$to		 = implode(';', $toarray);

				$update_orders_condition = implode(',', $update_orders);

				$message = "Oppdateringsskript for å åpne Portico-ordrer for del-faktura:\n\n";
				$message .= "UPDATE apoheader SET status='O' WHERE client='BY' AND order_id IN ({$update_orders_condition}) AND status='F';\n";
				$message .= "UPDATE apodetail SET status='O' WHERE client='BY' AND order_id IN ({$update_orders_condition}) AND status='F';";


				try
				{
					$rc	 = CreateObject('phpgwapi.send')->msg('email', $to, $subject, nl2br($message), '', $cc	 = '', $bcc = 'hc483@bergen.kommune.no', 'hc483@bergen.kommune.no', 'Ikke svar', 'html');
				}
				catch (Exception $e)
				{
					$this->receipt['error'][] = array('msg' => $e->getMessage());
				}
			}

			$msg						 = 'Tidsbruk: ' . (time() - $start) . ' sekunder';
			$this->cron_log($msg, $cron);
			echo "$msg\n";
			$this->receipt['message'][]	 = array('msg' => $msg);
		}

		function get_orders()
		{
			$sql = "SELECT fm_workorder.id as order_id, status, to_char(to_timestamp(entry_date),'YYYY.MM.DD') as dato,"
				. " account_lid as brukernavn, concat(account_firstname || ' ' || account_lastname) as bestiller"
				. " FROM fm_workorder"
				. " JOIN fm_workorder_status ON fm_workorder.status = fm_workorder_status.id"
				. " JOIN phpgw_accounts ON fm_workorder.user_id = phpgw_accounts.account_id"
				. " WHERE continuous = 1 AND closed IS NULL"
				. " ORDER BY fm_workorder.id";

			$this->db->query($sql, __LINE__, __FILE__);
			$orders = array();
			while ($this->db->next_record())
			{
				$orders[] = array(
					'order_id'	 => $this->db->f('order_id'),
					'status'	 => $this->db->f('status'),
					'dato'		 => $this->db->f('dato'),
					'brukernavn' => $this->db->f('brukernavn'),
					'bestiller'	 => $this->db->f('bestiller'),
				);
			}
			return $orders;
		}

		function get_agresso_status( & $orders )
		{
			foreach ($orders as &$order)
			{
				$this->check_order($order);
			}
		}

		function check_order( & $order )
		{
			$order_id		 = $order['order_id'];
			//Data, connection, auth
			$soapUser		 = "WEBSER";  //  username
			$soapPassword	 = "wser10"; // password
			$CLIENT			 = 'BY';
			$TemplateId		 = '10771'; //Spørring bilag_Portico ordrer
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
						<ns1:ColumnName>order_id</ns1:ColumnName>
						<ns1:Description>Ordrenr</ns1:Description>
						<ns1:RestrictionType>=</ns1:RestrictionType>
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
				</ns1:SearchCriteriaPropertiesList>
			</ns1:input>
			<ns1:credentials>
				<ns1:Username>{$soapUser}</ns1:Username>
				<ns1:Client>{$CLIENT}</ns1:Client>
				<ns1:Password>{$soapPassword}</ns1:Password>
			</ns1:credentials>
		</ns1:GetTemplateResultAsDataSet>
	</SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML;

			$headers = array(
				"POST /UBW-webservices/service.svc HTTP/1.1",
				"Host: agrpweb.adm.bgo",
				"Accept: text/xml",
				"Cache-Control: no-cache",
				"User-Agent: PHP-SOAP/7.1.15-1+ubuntu16.04.1+deb.sury.org+2",
				"Content-Type: text/xml; charset=utf-8",
				"SOAPAction: http://services.agresso.com/QueryEngineService/QueryEngineV201101/GetTemplateResultAsDataSet",
				"Content-length: " . strlen($soap_request)
			);

			$soapUrl = "http://agrpweb.adm.bgo/UBW-webservices/service.svc?QueryEngineService/QueryEngineV201101";

			$ch = curl_init($soapUrl);

			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $soap_request);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 2);

			$response	 = curl_exec($ch);
			$httpCode	 = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			curl_close($ch);

			$result = array();
			try
			{
				$sxe = new SimpleXMLElement($response);

				$sxe->registerXPathNamespace('diffgr', 'urn:schemas-microsoft-com:xml-diffgram-v1');
				$result = $sxe->xpath('//diffgr:diffgram/Agresso/AgressoQE');
			}
			catch (Exception $ex)
			{
				$order['agresso_status'] = 'Uten resultat';
				return;
			}

			$order['agresso_status'] = (string)$result[0]->status;

			return $result;
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
	}