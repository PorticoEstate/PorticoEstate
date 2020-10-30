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

		private $debug, $webservicehost, $authkey, $proxy, $archive_user_id, $OppdateringService, $kontekst;

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
			$this->authkey			 = !empty($custom_config_data['authkey']) ? $custom_config_data['authkey'] : '';
			$this->proxy			 = !empty($config['proxy']) ? $config['proxy'] : '';
			$this->archive_user_id	 = $GLOBALS['phpgw_info']['user']['preferences']['common']['archive_user_id'];

			$wsdl						 = "{$this->webservicehost}&wsdl";
			$this->OppdateringService	 = new OppdateringService($options, $wsdl);

			$kontekst = new ArkivKontekst();
			$kontekst->setKlientnavn("Portico");
			$kontekst->setKlientversjon("2");
			$kontekst->setReferanseoppsett("Portico referanseoppsett");

			$this->kontekst = $kontekst;
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

			$EksternNoekkel = new EksternNoekkel('Portico');
			$EksternNoekkel->setNoekkel($application['id']);

			$Saksmappe->setReferanseEksternNoekkel($EksternNoekkel);
			$Saksmappe->setSaksansvarlig($this->archive_user_id);


			$Saksparter = array();
			if ($application['customer_ssn'])
			{
				$Kontakt		 = $this->get_contakt_person($application);
				$Sakspart		 = new Sakspart($Kontakt);
				$Sakspart->setSakspartRolle(new SakspartRolle('sakspart'));
				$Saksparter[]	 = $Sakspart;
			}

			if ($application['customer_organization_number'])
			{
				$Kontakt2		 = $this->get_contakt_organization($application);
				$Sakspart2		 = new Sakspart($Kontakt2);
				$Sakspart2->setSakspartRolle(new SakspartRolle('sakspart'));
				$Saksparter[]	 = $Sakspart2;
			}

			$SakspartListe = new SakspartListe();
			$SakspartListe->setListe($Saksparter);
			$Saksmappe->setSakspart($SakspartListe);

			$ret = $this->OppdateringService->NySaksmappe(new NySaksmappe($Saksmappe, $this->kontekst));
			return $ret->getReturn();
		}

		private function get_contakt_person( $application )
		{
			$person_data = $this->get_person($application['customer_ssn']);

			$name_array	 = explode(' ', trim(str_replace('  ', ' ', $application['contact_name'])));
			$last_name	 = end($name_array);
			array_pop($name_array);
			$first_name	 = implode(' ', $name_array);

			$FirstName	 = $person_data['FirstName'] ? $person_data['FirstName'] : $data['first_name'];
			$MiddleName	 = $person_data['MiddleName'] ? " {$person_data['MiddleName']}" : ' ';
			$LastName	 = $person_data['LastName'] ? $person_data['LastName'] : $data['last_name'];

			$EnkelAdresseListe = new EnkelAdresseListe();

			$EnkelAdresse				 = new EnkelAdresse();
			$EnkelAdresse->setAdresselinje1($data['street']);
			$PostadministrativeOmraader	 = new PostadministrativeOmraader();
			$PostadministrativeOmraader->setPostnummer($data['zip_code']);
			$PostadministrativeOmraader->setPoststed($data['city']);
			$EnkelAdresse->setPostadresse($PostadministrativeOmraader);
			$EnkelAdresse->setLandkode('NOR');

			$EnkelAdresseListe->setListe(array($EnkelAdresse));

			$Kontakt = new Person("{$FirstName}{$MiddleName}{$LastName}");
			$Kontakt->setAdresser($EnkelAdresseListe);

			$ElektroniskAdresseListe = new ElektroniskAdresseListe();
			$ElektroniskAdresseListe->setListe(array(new ElektroniskAdresse()));
			$Kontakt->setElektroniskeAdresser($ElektroniskAdresseListe);

			$Kontakt->setPersonid(new Personidentifikator());
			$Kontakt->personid->setPersonidentifikatorNr($application['customer_ssn']);
			$Kontakt->personid->setpersonidentifikatorType(new PersonidentifikatorType('F'));
			$Kontakt->setEtternavn($LastName);
			$Kontakt->setFornavn("{$FirstName}{$MiddleName}");

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
			$journalpost->setJournalposttype(new Journalposttype('U'));
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
				$tilgangsrestriksjon = new Tilgangsrestriksjon('UO');
				$tilgangsrestriksjon->setKodebeskrivelse('Unntatt offentlighet');
				$skjerming->setTilgangsrestriksjon($tilgangsrestriksjon);
				$skjerming->setSkjermingshjemmel('Offl §13, Fvl § 13.1');
				$journalpost->setSkjerming($skjerming);
			}

			$journalpost->setReferanseArkivdel(new Arkivdel(''));
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
				$dokument->setDokumentstatus(new Dokumentstatus('F'));

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