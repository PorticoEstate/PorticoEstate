<?php

	interface rental_exportable
	{

		/**
		 * The exportable must identify itself with some string.
		 *
		 * @return string with id of job
		 */
		public function get_id();

		/**
		 * Must return the contents that the export consists of. Depending on the
		 * export this can be i.e. the contents that should be in an Agresso file.
		 * 
		 * @return string with contents
		 */
		public function get_contents();

		/**
		 * Must return what's missing on a contract before it can be exported.
		 *
		 * @return array with missing billing info
		 */
		public function get_missing_billing_info( $contract );
	}