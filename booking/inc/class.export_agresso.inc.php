<?php

	/**
	 * Hvordan overføre filer til agresseo: ekempel fra 'property'
	 * Prosedyre:	1) Lag filnavn
	 * 				2) Produser filen
	 * 				3) Lagre filen lokalt (for referanse/historikk/feilbehandling)
	 * 				4) Overfør filen til Agresso
	 * 				5) Evnt logg til databasen hvordan dette gikk
	 *
	 * Forutsetning:1) configurasjon for lokal lagring (katalog)
	 * 				2) configurasjon for pålogging til ftp-server (IP/Login/Passord/envt katalog)
	 *
	 */
	class export_agresso
	{

		function __construct()
		{
			$this->db = &$GLOBALS['phpgw']->db;
			$this->config = CreateObject('phpgwapi.config', 'booking');
			$this->config->read_repository();
		}

		public function do_your_magic( $buffer, $id )
		{
			// Viktig: må kunne rulle tilbake dersom noe feiler.
			$this->db->transaction_begin();

//			$buffer = 'test';

			$filnavn = $this->lagfilnavn();

			$file_written = false;

			$fp = fopen($filnavn, "wb");
			fwrite($fp, $buffer);

			if (fclose($fp))
			{
				$file_written = true;
			}

			if ($file_written && $this->config->config_data['invoice_export_method'] != 'ftp')
			{
				$transfer_ok = true;
			}
			else if ($file_written)
			{
				$transfer_ok = $this->transfer($filnavn);
			}

			if ($transfer_ok)
			{
				$this->db->transaction_commit();
				$this->config->config_data['invoice_last_id'] = $id;
				$this->config->save_repository();
				$message = "Godkjent: periode: {$Periode} antall bilag/underbilag overfort: {$antall} , fil: {$filnavn}";
			}
			else
			{
				$this->db->transaction_abort();
				$message = 'Noe gikk galt med overforing av godkjendte fakturaer!';
			}
			return $message;
		}

		protected function lagfilnavn()
		{
			$fil_katalog = $this->config->config_data['invoice_export_path'];
			$continue = true;
			$i = 1;
			do
			{
				$filnavn = $fil_katalog . '/AktivbyLG04_' . date("ymd") . '_' . sprintf("%02s", $i) . '.TXT';

				//Sjekk om filen eksisterer
				If (!file_exists($filnavn))
				{
					return $filnavn;
				}

				$i++;
			}
			while ($continue);

			//Ingen løpenr er ledige, gi feilmelding
			return false;
		}

		protected function transfer( $filnavn )
		{

			if ($this->config->config_data['invoice_export_method'] == 'ftp')
			{

				$transfer_ok = false;
				$ftp = $this->phpftp_connect();
				$basedir = $this->config->config_data['invoice_ftp_basedir'];


				if ($basedir)
				{
					$newfile = $basedir . '/' . basename($filnavn);
				}
				else
				{
					$newfile = basename($filnavn);
				}

				if (ftp_put($ftp, $newfile, $filnavn, FTP_BINARY))
				{
					//log_transaction_ok
					$transfer_ok = True;
				}
				else
				{
					//log_transaction_feil
					$transfer_ok = false;
					unlink($filnavn);
				}
				ftp_quit($ftp);
			}
			return $transfer_ok;
		}

		protected function phpftp_connect()
		{
			$host = $this->config->config_data['invoice_ftp_host'];
			$user = $this->config->config_data['invoice_ftp_user'];
			$pass = $this->config->config_data['invoice_ftp_password'];

			$ftp = ftp_connect($host);
			if ($ftp)
			{
				if ($lres = ftp_login($ftp, $user, $pass))
				{
					return $ftp;
				}
			}
		}
	}