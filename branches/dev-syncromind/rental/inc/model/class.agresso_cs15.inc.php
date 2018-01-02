<?php
	include_class('rental', 'exportable', 'inc/model/');

	class rental_agresso_cs15 implements rental_exportable
	{

		protected $parties;
		protected $lines;

		public function __construct( &$parties )
		{
			$this->parties = $parties;
			$this->lines = null;
		}

		/**
		 * @see rental_exportable
		 */
		public function get_id()
		{
			return 'Agresso CS15';
		}

		/**
		 * Returns the file contents as a string.
		 * 
		 * @see rental_exportable
		 */
		public function get_contents()
		{
			$contents = '';
			if ($this->lines == null) // Data hasn't been created yet
			{
				$this->run();
			}
			foreach ($this->lines as $line)
			{
				$contents .= "{$line}\n";
			}
			return $contents;
		}

		public function get_missing_billing_info( $contract )
		{
			$missing_billing_info = array();
			return $missing_billing_info;
		}

		protected function run()
		{
			$this->lines = array();
			$counter = 1; // set to 1 initially to satisfy agresso requirements
			foreach ($this->parties as $party) // Runs through all parties
			{
				$country_code = strtoupper($party->get_postal_country_code());
				$place = '';
				// TODO: Which standard for the country codes does Agresso follow?
				if ($country_code != 'NO' && $country_code != 'SV' && $country_code != 'IS') // Shouldn't get postal place for Norway, Sweden and Iceland
				{
					$party->get_postal_place();
				}
				$phone = $party->get_phone();
				if ($phone == null || $phone == '') // Phone not set..
				{
					$phone = $party->get_mobile_phone(); // ..so we try mobile phone
				}
				$this->lines[] = $this->get_line($party->get_name(), $party->get_identifier(), $party->get_address_1(), $party->get_address_2(), $country_code, $place, $phone, $party->get_postal_code(), $counter);
				$counter++;
			}
		}

		/**
		 * Builds one single line of the Agresso file.

		 * @return string
		 */
		protected function get_line( $name, $identifier, $address1, $address2, $country_code, $postal_place, $phone, $postal_code, $counter )
		{
			// XXX: Which charsets do Agresso accept/expect? Do we need to something regarding padding and UTF-8?
			$line = '1'  //  1	full_record
				. 'I'  //  2	change_status
				. '10'  //  3	apar_gr_id
				. sprintf("%9s", $counter)   //  4	apar_id, sequence number, right justified
				. sprintf("%9s", '')  //  5	apar_id_ref
				. sprintf("%-50.50s", iconv("UTF-8", "ISO-8859-1", $name)) //  6	apar_name
				. 'R'  //  7	apar_type
				. sprintf("%-35s", '') //  8	bank_account
				. sprintf("%-4s", '') //  9	bonus_gr
				. sprintf("%3s", '')  // 10	cash_delay
				. sprintf("%-13s", '') // 11	clearing_code
				. 'BY'  // 12	client
				. sprintf("%1s", '')  // 13	collect_flag
				. sprintf("%-25.25s", $identifier) // 14	comp_reg_no
				. 'P'  // 15	control
				. sprintf("%20s", '') // 16	credit_limit
				. 'NOK'  // 17	NOK
				. sprintf("%1s", '')  // 18	currency_set
				. sprintf("%-4s", '') // 19	disc_code
				. sprintf("%-15s", '') // 20	ext_apar_ref
				. sprintf("%-8s", '') // 21	factor_short
				. sprintf("%-35s", '') // 22	foreign_acc
				. sprintf("%-6s", '') // 23	int_rule_id
				. sprintf("%-12s", '') // 24	invoice_code
				. 'NO'  // 25	language
				. sprintf("%9s", '')  // 26	main_apar_id
				. sprintf("%-80s", '') // 27	message_text
				. sprintf("%3s", '')  // 28	pay_delay
				. 'IP'  // 29	pay_method
				. sprintf("%-13s", '') // 30	postal_acc
				. sprintf("%-1s", '') // 31	priority_no
				. sprintf("%-10s", '.') // 32	short_name
				. 'N'  // 33	status
				. sprintf("%-11s", '') // 34	swift
				. sprintf("%-1s", '') // 35	tax_set
				. sprintf("%-2s", '') // 36	tax_system
				. sprintf("%-2s", '') // 37	terms_id
				. sprintf("%-1s", '') // 38	terms_set
				. sprintf("%-25s", '') // 39	vat_reg_no
				. sprintf("%-40.40s", iconv("UTF-8", "ISO-8859-1", $address1)) // 40	address1
				. sprintf("%-40.40s", iconv("UTF-8", "ISO-8859-1", $address2)) // 40	address2
				. sprintf("%-40.40s", '')   // 40	address3
				. sprintf("%-40.40s", '')   // 40	address4
				. '1'  // 41	address_type
				. sprintf("%-6s", '') // 42	agr_user_id
				. sprintf("%-255s", '') // 43	cc_name
				. sprintf("%-3.3s", $country_code) // 44	country_code
				. sprintf("%-50s", '') // 45	description
				. sprintf("%-40.40s", iconv("UTF-8", "ISO-8859-1", $postal_place)) // 46	place
				. sprintf("%-40s", '') // 47	province
				. sprintf("%-35.35s", $phone)  // 48	telephone_1
				. sprintf("%-35s", '') // 49	telephone_2
				. sprintf("%-35s", '') // 50	telephone_3
				. sprintf("%-35s", '') // 51	telephone_4
				. sprintf("%-35s", '') // 52	telephone_5
				. sprintf("%-35s", '') // 53	telephone_6
				. sprintf("%-35s", '') // 54	telephone_7
				. sprintf("%-255s", '') // 55	to_name
				. sprintf("%-15.15s", $postal_code) // 56	zip_code
				. sprintf("%-50s", '') // 57	e_mail
				. sprintf("%-35s", '') // 58	pos_title
				. sprintf("%-4s", '') // 59	pay_temp_id
				. sprintf("%-25s", '') // 60	reference_1
			;

			return str_replace(array("\n", "\r"), '', $line);
		}
	}