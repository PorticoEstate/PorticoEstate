<?php
	$test = new test_soap_ubw();
	$test->GetSearchCriteria();
	$test->GetTemplateResultOptions();
	$test->GetTemplateResultAsDataSet();

	class test_soap_ubw
	{

		public function __construct()
		{
			
		}

		function GetSearchCriteria()
		{

			$soap_request = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://services.agresso.com/QueryEngineService/QueryEngineV201101">
	<SOAP-ENV:Body>
		<ns1:GetSearchCriteria>
			<ns1:templateId>10771</ns1:templateId>
			<ns1:hideUnused>true</ns1:hideUnused>
			<ns1:credentials>
				<ns1:Username>WEBSER</ns1:Username>
				<ns1:Client>BY</ns1:Client>
				<ns1:Password>wser10</ns1:Password>
			</ns1:credentials>
		</ns1:GetSearchCriteria>
	</SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML;

			$headers = array(
				"POST /UBW-webservices/service.svc HTTP/1.1",
				"Host: agrweb05a.adm.bgo",
				"Connection: Keep-Alive",
				"User-Agent: PHP-SOAP/7.3.11-1+ubuntu18.04.1+deb.sury.org+1",
				"Content-Type: text/xml; charset=utf-8",
				"SOAPAction: \"http://services.agresso.com/QueryEngineService/QueryEngineV201101/GetSearchCriteria\"",
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
			echo "Test: GetSearchCriteria<br/>";
			echo "http kode: {$httpCode}<br/>";
			echo "response:<br/>";
			echo '<pre>', htmlentities($response), '</pre>';
		}

		function GetTemplateResultOptions()
		{

			$soap_request = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://services.agresso.com/QueryEngineService/QueryEngineV201101">
	<SOAP-ENV:Body>
		<ns1:GetTemplateResultOptions>
			<ns1:credentials>
				<ns1:Username>WEBSER</ns1:Username>
				<ns1:Client>BY</ns1:Client>
				<ns1:Password>wser10</ns1:Password>
			</ns1:credentials>
		</ns1:GetTemplateResultOptions>
	</SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML;

			$headers = array(
				"POST /UBW-webservices/service.svc HTTP/1.1",
				"Host: agrweb05a.adm.bgo",
				"Connection: Keep-Alive",
				"User-Agent: PHP-SOAP/7.3.11-1+ubuntu18.04.1+deb.sury.org+1",
				"Content-Type: text/xml; charset=utf-8",
				"SOAPAction: \"http://services.agresso.com/QueryEngineService/QueryEngineV201101/GetTemplateResultOptions\"",
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
			echo "Test: GetTemplateResultOptions<br/>";
			echo "http kode: {$httpCode}<br/>";
			echo "response:<br/>";
			echo '<pre>', htmlentities($response), '</pre>';
		}
		/*
		 * Test for ordre 45031871
		 */

		function GetTemplateResultAsDataSet()
		{

			$soap_request = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://services.agresso.com/QueryEngineService/QueryEngineV201101">
	<SOAP-ENV:Body>
		<ns1:GetTemplateResultAsDataSet>
			<ns1:input>
				<ns1:TemplateId>10771</ns1:TemplateId>
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
						<ns1:FromValue>45032874</ns1:FromValue>
						<ns1:ToValue>45032874</ns1:ToValue>
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
				"Host: agrweb05a.adm.bgo",
				"Connection: Keep-Alive",
				"User-Agent: PHP-SOAP/7.3.11-1+ubuntu18.04.1+deb.sury.org+1",
				"Content-Type: text/xml; charset=utf-8",
				"SOAPAction: \"http://services.agresso.com/QueryEngineService/QueryEngineV201101/GetTemplateResultAsDataSet\"",
				"Content-length: " . strlen($soap_request)
			);

			$soapUrl = "http://agrpweb.adm.bgo/UBW-webservices/service.svc?QueryEngineService/QueryEngineV201101"; // asmx URL of WSDL

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

			echo "Test: GetTemplateResultAsDataSet<br/>";
			echo "http kode: {$httpCode}<br/>";
			echo "response:<br/>";
			echo '<pre>', htmlentities($response), '</pre>';

			$result = array();
			try
			{
				$sxe = new SimpleXMLElement($response);

				$sxe->registerXPathNamespace('diffgr', 'urn:schemas-microsoft-com:xml-diffgram-v1');
				$result = $sxe->xpath('//diffgr:diffgram/Agresso/AgressoQE');

			}
			catch (Exception $ex)
			{
				throw $ex;
			}

			echo '<pre>', print_r($result), '</pre>';

		}
	}