<?php
	/**
	 * phpGroupWare
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2020 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/
	 * @package phpgroupware
	 * @subpackage communication
	 * @category core
	 */
	/*
	  This program is free software: you can redistribute it and/or modify
	  it under the terms of the GNU General Public License as published by
	  the Free Software Foundation, either version 2 of the License, or
	  (at your option) any later version.

	  This program is distributed in the hope that it will be useful,
	  but WITHOUT ANY WARRANTY; without even the implied warranty of
	  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	  GNU General Public License for more details.

	  You should have received a copy of the GNU General Public License
	  along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	require_once PHPGW_API_INC . '/soap_client/gi_arkiv/autoload.php';
	phpgw::import_class('phpgwapi.datetime');

	class booking_gi_arkiv
	{

		private $debug,
			$webservicehost, $username, $password, $proxy,
			$archive_user_id, $OppdateringService, $kontekst,
			$journalenhet, $arkivnoekkel, $arkivnoekkel_text,
			$fagsystem, $arkivdel, $sakspart_rolle,
			$klientnavn, $klientversjon, $referanseoppsett;

		public function __construct()
		{
			$location_id		 = $GLOBALS['phpgw']->locations->get_id('booking', 'run');
			$custom_config		 = CreateObject('admin.soconfig', $location_id);
			$custom_config_data	 = $custom_config->config_data['gi_arkiv'];
			$config				 = CreateObject('phpgwapi.config', 'booking')->read();

			if (!empty($custom_config_data['debug']))
			{
				$this->debug = true;
			}

			$this->webservicehost	 = !empty($custom_config_data['webservicehost']) ? $custom_config_data['webservicehost'] : '';
			$this->username			 = !empty($custom_config_data['username']) ? $custom_config_data['username'] : '';
			$this->password			 = !empty($custom_config_data['password']) ? $custom_config_data['password'] : '';
			$this->webservicehost	 = "https://svc-geointaktivkommune-test.baerum.kommune.no/N5WS/ArkivOppdateringservice.svc/ArkivOppdateringService";
			$this->proxy			 = !empty($config['proxy']) ? $config['proxy'] : '';
			$this->archive_user_id	 = $GLOBALS['phpgw_info']['user']['preferences']['common']['archive_user_id'];

			$this->journalenhet		 = !empty($custom_config_data['journalenhet']) ? $custom_config_data['journalenhet'] : 'DOKS';
			$this->arkivnoekkel		 = !empty($custom_config_data['arkivnoekkel']) ? $custom_config_data['arkivnoekkel'] : 'N';
			$this->arkivnoekkel_text = !empty($custom_config_data['arkivnoekkel_text']) ? $custom_config_data['arkivnoekkel_text'] : 'N – 068.1 Leieavtaler – kontrakter';
			$this->fagsystem		 = !empty($custom_config_data['fagsystem']) ? $custom_config_data['fagsystem'] : 'Aktiv kommune';
			$this->arkivdel			 = !empty($custom_config_data['arkivdel']) ? $custom_config_data['arkivdel'] : 'AKTIV';
			$this->sakspart_rolle	 = !empty($custom_config_data['sakspart_rolle']) ? $custom_config_data['sakspart_rolle'] : 'sakspart';
			$this->klientnavn		 = !empty($custom_config_data['klientnavn']) ? $custom_config_data['klientnavn'] : 'Portico';
			$this->klientversjon	 = !empty($custom_config_data['klientversjon']) ? $custom_config_data['klientversjon'] : '2';
			$this->referanseoppsett	 = !empty($custom_config_data['referanseoppsett']) ? $custom_config_data['referanseoppsett'] : 'Portico referanseoppsett';

			$wsdl = 'http://rep.geointegrasjon.no/Arkiv/Oppdatering/xml.wsdl/2012.01.31/giArkivOppdatering20120131.wsdl';
			$options = array(
				'login'			 => $this->username,
				'password'		 => $this->password,
				'soap_version'	 => SOAP_1_1,
				'location'		 => $this->webservicehost,//the URL of the SOAP server to send the request to
				'trace'			 => true,
				'proxy_host'	 => 'proxy.bergen.kommune.no',
				'proxy_port'	 => 8080,
				'encoding'		 => 'UTF-8',
			);

			$this->OppdateringService	 = new OppdateringService($options, $wsdl);
			$header = $this->soapClientWSSecurityHeader($this->username, $this->password);
			$this->OppdateringService->__setSoapHeaders( $header );

			$kontekst = new ArkivKontekst();
			$kontekst->setKlientnavn($this->klientnavn);
			$kontekst->setKlientversjon($this->klientversjon);
			$kontekst->setReferanseoppsett($this->referanseoppsett);

			$this->kontekst = $kontekst;
		}

		/**
		 * This function implements a WS-Security digest authentification for PHP.
		 * Adapted from https://stackoverflow.com/questions/953639/connecting-to-ws-security-protected-web-service-with-php
		 * @access private
		 * @param string $user
		 * @param string $password
		 * @return SoapHeader
		 */
		function soapClientWSSecurityHeader( $user, $password, $encode = false )
		{
			// Creating date using yyyy-mm-ddThh:mm:ssZ format
			$tm_created	 = gmdate('Y-m-d\TH:i:s\Z');
			$tm_expires	 = gmdate('Y-m-d\TH:i:s\Z', gmdate('U') + 180); //only necessary if using the timestamp element
			// Generating and encoding a random number
			$simple_nonce	 = mt_rand();
			$encoded_nonce	 = base64_encode($simple_nonce);

			// Initializing namespaces
			$ns_wsse		 = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
			$ns_wsu			 = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd';

			if($encode)
			{
				$encoding_type	 = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary';
				$password_type	 = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest';
				// Compiling WSS string
				$passdigest = base64_encode(sha1($simple_nonce . $tm_created . $password, true));
			}
			else
			{
				$password_type	 = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText';
				$passdigest = $password;
			}

			// Creating WSS identification header using SimpleXML
			$root = new SimpleXMLElement('<root/>');

			$security = $root->addChild('wsse:Security', null, $ns_wsse);
			$security->addAttribute('mustUnderstand', 1);

			//the timestamp element is not required by all servers
			$timestamp = $security->addChild('wsu:Timestamp', null, $ns_wsu);
			$timestamp->addAttribute('wsu:Id', 'Timestamp-28');
			$timestamp->addChild('wsu:Created', $tm_created, $ns_wsu);
			$timestamp->addChild('wsu:Expires', $tm_expires, $ns_wsu);

			$usernameToken = $security->addChild('wsse:UsernameToken', null, $ns_wsse);
			$usernameToken->addChild('wsse:Username', $user, $ns_wsse);
			$usernameToken->addChild('wsse:Password', $passdigest, $ns_wsse)->addAttribute('Type', $password_type);
			if($encode)
			{
				$usernameToken->addChild('wsse:Nonce', $encoded_nonce, $ns_wsse)->addAttribute('EncodingType', $encoding_type);
			}
			$usernameToken->addChild('wsu:Created', $tm_created, $ns_wsu);

			// Recovering XML value from that object
			$root->registerXPathNamespace('wsse', $ns_wsse);
			$full	 = $root->xpath('/root/wsse:Security');
			$auth	 = $full[0]->asXML();

			$header_content = new SoapVar($auth, XSD_ANYXML);
			$header = new SoapHeader($ns_wsse, 'Security', $header_content, true);
			return $header;
		}

		public function export_data( $_title, $application, $files )
		{
			if (!$this->archive_user_id)
			{
				phpgwapi_cache::message_set('Ansvarlig arkiv-bruker er ikke angitt under innstillinger', 'error');
				return;
			}

			$title = str_replace(array('(', ')'), array('[', ']'), $_title);

			$case_result = $this->create_case($title, $application);

			$journal_result = new stdClass();

			if ($case_result->getSaksnr())
			{
				$journal_result = $this->create_journal($case_result, $title, $files, $application);
			}

			return array(
				'external_archive_key'	 => $case_result->getSaksnr(),
				'case_result'			 => $case_result,
				'document_result'		 => $journal_result
			);
		}

		private function get_person( $ssn )
		{
			phpgw::import_class('bookingfrontend.bouser');

			$data = array(
				'ssn' => $ssn
			);

			$configfrontend			 = CreateObject('phpgwapi.config', 'bookingfrontend')->read();
			$get_name_from_external	 = isset($configfrontend['get_name_from_external']) && $configfrontend['get_name_from_external'] ? $configfrontend['get_name_from_external'] : '';

			$file = PHPGW_SERVER_ROOT . "/bookingfrontend/inc/custom/default/{$get_name_from_external}";

			if (is_file($file))
			{
				require_once $file;
				$external_user = new bookingfrontend_external_user_name();
				try
				{
					$external_user->get_name_from_external_service($data);
				}
				catch (Exception $exc)
				{

				}
			}
			return $data;
		}

		private function create_case( $title, $application )
		{
			$Saksmappe = new Saksmappe($title);
			$Saksmappe->setMappetype(new Mappetype('GS'));	//GS : Generell sak

			/*
			 * A : Saken avsluttet
			 * B : Under behandling
			 * R : Reservert sak
			 * U : Saken utgår
			 * X : Saken er ikke gjenstand for oppfølging
			 */
			$Saksmappe->setSaksstatus(new Saksstatus('B'));//B : Under behandling

			/**
			 * Note: konfigurable...
			 */
			$Saksmappe->setJournalenhet(new Journalenhet($this->journalenhet));//DOKS : Felles journalenhet i Bærum kommune

			$Klasser = array();
			$KlasseListe = new KlasseListe();

			$Klassifikasjonssystem = new Klassifikasjonssystem('FNR');//FNR : Fødselsnummer
			$Klasse = new Klasse($Klassifikasjonssystem, $application['customer_ssn']);
			$Klasse->setRekkefoelge(1);
			$Klasse->setSkjermetKlasse(false);

			$Klasser[] = $Klasse;

			//konfigurerbar
			$Klasse2 = new Klasse(new Klassifikasjonssystem($this->arkivnoekkel), $this->arkivnoekkel_text);//N : Arkivnøkkel
			$Klasse2->setRekkefoelge(2);
			$Klasse2->setSkjermetKlasse(false);

			$Klasser[] = $Klasse2;

			$KlasseListe->setListe($Klasser);
			$Saksmappe->setKlasse($KlasseListe);


			//konfigurerbar
			$EksternNoekkel = new EksternNoekkel($this->fagsystem);
			$EksternNoekkel->setNoekkel($application['id']);

			$Saksmappe->setReferanseEksternNoekkel($EksternNoekkel);
			$Saksmappe->setSaksansvarlig($this->archive_user_id);

			$Saksparter = array();
			if ($application['customer_ssn'])
			{
				$Kontakt		 = $this->get_contakt_person($application);
				$Sakspart		 = new Sakspart($Kontakt);
				$Sakspart->setSakspartRolle(new SakspartRolle($this->sakspart_rolle));
				$Saksparter[]	 = $Sakspart;
			}

			if ($application['customer_organization_number'])
			{
				$Kontakt2		 = $this->get_contakt_organization($application);
				$Sakspart2		 = new Sakspart($Kontakt2);
				$Sakspart2->setSakspartRolle(new SakspartRolle($this->sakspart_rolle));
				$Saksparter[]	 = $Sakspart2;
			}

			$SakspartListe = new SakspartListe();
			$SakspartListe->setListe($Saksparter);
			$Saksmappe->setSakspart($SakspartListe);

//	_debug_array($Saksmappe);
//			die();
			try
			{
				$ret = $this->OppdateringService->NySaksmappe(new NySaksmappe($Saksmappe, $this->kontekst));

				if ($this->debug)
				{
					echo "SOAP HEADERS:\n" . $this->OppdateringService->__getLastRequestHeaders() . PHP_EOL;
					echo "SOAP REQUEST:\n" . $this->OppdateringService->__getLastRequest() . PHP_EOL;
				}
			}
			catch (SoapFault $fault)
			{
				echo "SOAP HEADERS:\n" .  $this->OppdateringService->__getLastRequestHeaders() . PHP_EOL;
				echo "SOAP REQUEST:\n" . $this->OppdateringService->__getLastRequest() . PHP_EOL;
				echo '<pre>';
				print_r($fault);
				echo '</pre>';
				$msg = "SOAP Fault:\n faultcode: {$fault->faultcode},\n faultstring: {$fault->faultstring}";
				echo $msg . PHP_EOL;
				trigger_error(nl2br($msg), E_USER_ERROR);
			}

//			$this->oppdater_sakspart($ret->getReturn(), $SakspartListe);
//			$this->oppdater_sakspart(array(), $SakspartListe);
			return $ret->getReturn();
		}


		/**
		 * Test oppdatering av saksparter for å inkludere Personidentifikator
		 * @param type $case_data
		 * @param type $SakspartListe
		 */
		private function oppdater_sakspart($case_data, $SakspartListe )
		{
			$Saksnoekkel = new SakSystemId(new SystemID($case_data->getSystemID()));
//			$Saksnoekkel = new SakSystemId(new SystemID('tt'));
			try
			{
				$ret = $this->OppdateringService->NySakspart( new NySakspart($SakspartListe, $Saksnoekkel, $this->kontekst));

				if ($this->debug)
				{
					echo "SOAP HEADERS:\n" . $this->OppdateringService->__getLastRequestHeaders() . PHP_EOL;
					echo "SOAP REQUEST:\n" . $this->OppdateringService->__getLastRequest() . PHP_EOL;
				}
			}
			catch (SoapFault $fault)
			{
				echo "SOAP HEADERS:\n" .  $this->OppdateringService->__getLastRequestHeaders() . PHP_EOL;
				echo "SOAP REQUEST:\n" . $this->OppdateringService->__getLastRequest() . PHP_EOL;
				echo '<pre>';
				print_r($fault);
				echo '</pre>';
				$msg = "SOAP Fault:\n faultcode: {$fault->faultcode},\n faultstring: {$fault->faultstring}";
				echo $msg . PHP_EOL;
				trigger_error(nl2br($msg), E_USER_ERROR);
			}

		}

		private function get_contakt_person( $application )
		{
			$person_data = $this->get_person($application['customer_ssn']);

			$name_array	 = explode(' ', trim(str_replace('  ', ' ', $application['contact_name'])));
			$last_name	 = end($name_array);
			array_pop($name_array);
			$first_name	 = implode(' ', $name_array);

			$FirstName	 = !empty($person_data['first_name']) ? $person_data['first_name'] : $first_name;
			$MiddleName	 = !empty($person_data['Middle_name']) ? " {$person_data['Middle_name']}" : ' ';
			$LastName	 = !empty($person_data['last_name']) ? $person_data['last_name'] : $last_name;

			$EnkelAdresseListe = new EnkelAdresseListe();

			$EnkelAdressetype			 = new EnkelAdressetype('Postadresse');
			$EnkelAdresse				 = new EnkelAdresse($EnkelAdressetype);
			$EnkelAdresse->setAdresselinje1($person_data['street']);
			$PostadministrativeOmraader	 = new PostadministrativeOmraader();
			$PostadministrativeOmraader->setPostnummer($person_data['zip_code']);
			$PostadministrativeOmraader->setPoststed($person_data['city']);
			$EnkelAdresse->setPostadresse($PostadministrativeOmraader);
			$EnkelAdresse->setLandkode('NOR');

			$EnkelAdresseListe->setListe(array($EnkelAdresse));

			$Kontakt = new Person("{$FirstName}{$MiddleName}{$LastName}");
			$Kontakt->setAdresser($EnkelAdresseListe);

			$ElektroniskAdresseListe = new ElektroniskAdresseListe();
			$ElektroniskAdresseListe->setListe(array(new ElektroniskAdresse()));
			$Kontakt->setElektroniskeAdresser($ElektroniskAdresseListe);

			$Personidentifikator = new Personidentifikator();
			$Personidentifikator->setPersonidentifikatorNr($application['customer_ssn']);
//			$Personidentifikator->setpersonidentifikatorType(new PersonidentifikatorType('F'));
			$PersonidentifikatorType = new PersonidentifikatorType('F');
			$PersonidentifikatorType->setErGyldig(true);
			$PersonidentifikatorType->setKodebeskrivelse('Fødselsnummer');
			$Personidentifikator->setPersonidentifikatorType($PersonidentifikatorType);

			$Kontakt->setPersonid($Personidentifikator);
			$Kontakt->setEtternavn($LastName);
			$Kontakt->setFornavn(trim("{$FirstName}{$MiddleName}"));

			return $Kontakt;
		}

		private function get_contakt_organization( $application )
		{
			$organization = $this->get_organization($application['customer_organization_number']);

			$Kontakt = new Organisasjon($organization['navn']);
			$Kontakt->setOrganisasjonsnummer($application['customer_organization_number']);

			$EnkelAdresseListe			 = new EnkelAdresseListe();
			$EnkelAdresse				 = new EnkelAdresse();
			$EnkelAdresse->setAdresselinje1(implode(', ', $organization['postadresse']['adresse']));
			$PostadministrativeOmraader	 = new PostadministrativeOmraader();
			$PostadministrativeOmraader->setPostnummer($organization['postadresse']['postnummer']);
			$PostadministrativeOmraader->setPoststed($organization['postadresse']['poststed']);
			$EnkelAdresse->setPostadresse($PostadministrativeOmraader);
			$EnkelAdresse->setLandkode('NOR');

			$EnkelAdresseListe->setListe(array($EnkelAdresse));

			$Kontakt->setAdresser($EnkelAdresseListe);
			return $Kontakt;
		}

		private function create_journal( $case_data, $title, $files, $application )
		{

			$journalpost = new Journalpost($title);
			/**
				I : Inngående brev
				SK : Sakskart
				N : Internt notat
				MP : Møteprotokoll
				S : Saksframlegg/innstilling
				U : Utgående brev
				X : Internt notat uten oppfølging
				MB : Møtebok
				L : Ikke arkivverdig dokument
				R : Registrert dokument
			 */
			$journalpost->setJournalposttype(new Journalposttype('U'));

			/**
				A : Registrering avsluttet, arkiveksemplar tilgjengelig.
				E : Ekspedert av saksbehandler, leder eller annen instans.
				F : Ferdig fra saksbehandler/leder og klargjort for ekspedering.
				J : Journalført og/eller kontrollert av arkivet.
				M : Midlertidig journalført av arkivet
				R : Reservert av/for saksbehandler.
				S : Registrert i første hånd eller ajourført av saksbehandler/leder.
				U : Dokumentet utgår eller er flyttet til en annen sak.
				D : Skjemaer fra ACOS Mottak
			 */
			$journalpost->setJournalstatus(new Journalstatus('J'));
			$journalpost->setSkjermetTittel(false);

			$timezone = !empty($GLOBALS['phpgw_info']['user']['preferences']['common']['timezone']) ? $GLOBALS['phpgw_info']['user']['preferences']['common']['timezone'] : 'UTC';

			try
			{
				$DateTimeZone = new DateTimeZone($timezone);
			}
			catch (Exception $ex)
			{
				$DateTimeZone = null;
			}

			$journalpost->setDokumentetsDato(new DateTime('now', $DateTimeZone));

			$journalpost->setSaksnr($case_data->getSaksnr());

			if (false)
			{
				$skjerming			 = new Skjerming();
				// konfigurerbar..
				$tilgangsrestriksjon = new Tilgangsrestriksjon('U');
				$tilgangsrestriksjon->setKodebeskrivelse('Unntatt offentlighet');
				$skjerming->setTilgangsrestriksjon($tilgangsrestriksjon);
				$skjerming->setSkjermingshjemmel('offl. § 13 Taushetsplikt, jf fvl. §13');
				$journalpost->setSkjerming($skjerming);
			}

			//konfigurerbar
			$journalpost->setReferanseArkivdel(new Arkivdel($this->arkivdel));
			$journalpost->setOffentligTittel($title);
			$KorrespondansepartListe = new KorrespondansepartListe();

			$korrespondanseparter = array();


			if ($application['customer_ssn'])
			{
				$kontakt_1				 = $this->get_contakt_person($application);
				$korrespondansepart_1	 = new Korrespondansepart(new Korrespondanseparttype('Mottaker'), $kontakt_1);
				$korrespondansepart_1->setSkjermetKorrespondansepart(false);
				$korrespondanseparter[]	 = $korrespondansepart_1;
			}

			if ($application['customer_organization_number'])
			{
				$kontakt_2				 = $this->get_contakt_organization($application);
				$korrespondansepart_2	 = new Korrespondansepart(new Korrespondanseparttype('Mottaker'), $kontakt_2);
				$korrespondansepart_2->setSkjermetKorrespondansepart(false);
				$korrespondanseparter[]	 = $korrespondansepart_2;
			}

			$kontakt_3				 = new Kontakt('');
			$korrespondansepart_3	 = new Korrespondansepart(new Korrespondanseparttype(''), $kontakt_3);
			$korrespondansepart_3->setBehandlingsansvarlig(1);
			$korrespondansepart_3->setSkjermetKorrespondansepart(false);
			$korrespondansepart_3->setSaksbehandlerInit($this->archive_user_id);
			$korrespondanseparter[]	 = $korrespondansepart_3;

			$KorrespondansepartListe->setListe($korrespondanseparter);

			$journalpost->setKorrespondansepart($KorrespondansepartListe);

			$ret = $this->OppdateringService->NyJournalpost(new NyJournalpost($journalpost, $this->kontekst));

			$mime_magic = createObject('phpgwapi.mime_magic');

			foreach ($files as $file)
			{
				$dokument = new Dokument();
				$dokument->setTittel($file['file_name']);

				$dokument->setVariantformat(new Variantformat('p'));
				$dokument->setTilknyttetRegistreringSom(new TilknyttetRegistreringSom('H'));

				/*
				 * B : Under behandling
				 * F : Ferdig
				 */
				$dokument->setDokumentstatus(new Dokumentstatus('B'));

				$mimeType = $mime_magic->filename2mime($file['file_name']);

				$dokument->setReferanseJournalpostSystemID($ret->getReturn()->getSystemID());
				$fil = new Filinnhold(
						$file['file_name'],
						$mimeType,
						base64_encode($file['file_data']
					)
				);

				$dokument->setFil($fil);

				$this->OppdateringService->NyDokument(new NyDokument($dokument, false, $this->kontekst));
			}

			return $ret->getReturn();
		}

		/**
		 *
		 * @param string $organization_number
		 * @return array
		 */
		private function get_organization( $organization_number )
		{
			$url = "https://data.brreg.no/enhetsregisteret/api/enheter/{$organization_number}";

			$ch = curl_init();
			if ($this->proxy)
			{
				curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
			}
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'accept: application/json',
				'Content-Type: application/json',
				'Content-Length: ' . strlen($data_json)
			));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$result = curl_exec($ch);

			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$ret = json_decode($result, true);

			return $ret;
		}
	}