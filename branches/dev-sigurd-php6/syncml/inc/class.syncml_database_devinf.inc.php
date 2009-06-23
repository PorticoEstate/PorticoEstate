<?php
	/**
	 * phpGroupWare (http://phpgroupware.org/)
	 * SyncML interface
	 *
	 * @author    Johan Gunnarsson <johang@phpgroupware.org>
	 * @copyright Copyright (c) 2007 Free Software Foundation, Inc.
	 * @license   GNU General Public License 3 or later
	 * @package   syncml
	 * @version   $Id$
	 */

	require_once 'inc/functions.inc.php';

	/**
	 * A device information database.
	 */
	class syncml_database_devinf
	{
		var $session;

		function syncml_database_devinf(&$session)
		{
			$this->session = &$session;
		}

		/**
		 * Get DEVINF from this database.
		 *
		 * @param $uri   URI of item to get.
		 * @param $type  Type of input data.
		 * @return mixed Returns NULL if item was not found.
		 */
		function get_item($uri, $type)
		{
			switch($uri)
			{
				case './devinf10':
					return $this->_encode(
						$this->_get_devinf_10(), $type, '1.0');
				case './devinf11':
					return $this->_encode(
						$this->_get_devinf_11(), $type, '1.1');
				default:
					return NULL;
			}
		}

		/**
		 * Put DEVINF.
		 *
		 * @param $uri  URI of DEVINF to put.
		 * @param $data DEVINF data.
		 * @param $type Type of input data.
		 * @return bool True on success, false on failure.
		 */
		function put_item($uri, $data, $type)
		{
			switch($uri)
			{
				case './devinf10':
				case './devinf11':
					return $this->_put_devinf($this->_decode($data, $type));
				default:
					return FALSE;
			}
		}

		/**
		 * Encode to type from XML string.
		 *
		 * @param $data   Data to decode.
		 * @param $type   Type of input data.
		 * @return string Decoded data in XML format.
		 */
		function _encode($data, $type, $dtd_version)
		{
			switch($type)
			{
				case 'application/vnd.syncml-devinf+wbxml':
				case 'application/vnd.syncml-devinf+xml':
					return $data;
				default:
					return NULL;
			}
		}

		/**
		 * Decode from type to XML array.
		 *
		 * @param $data        Data to decode.
		 * @param $type        Type of input data.
		 * @param $dtd_version 
		 * @return string Decoded data in XML array format.
		 */
		function _decode($data, $type)
		{
			switch($type)
			{
				case 'application/vnd.syncml-devinf+wbxml':
					$parser = new wbxml_parser();
					return $parser->parse(
						wbxml_decode($data), new xml_mapper());
				case 'application/vnd.syncml-devinf+xml':
					return $data;
				default:
					return NULL;
			}
		}

		function _get_devinf_10()
		{
			return
				'<DevInf xmlns="syncml:devinf">' .
					'<VerDTD>1.0</VerDTD>' .
					'<DevID>485749KR</DevID>' .
					'<DevTyp>server</DevTyp>' .
				'</DevInf>';
		}

		function _get_devinf_11()
		{
			return
				'<DevInf xmlns="syncml:devinf">' .
					'<VerDTD>1.1</VerDTD>' .
					'<DevID>485749KR</DevID>' .
					'<DevTyp>server</DevTyp>' .
					'<SupportLargeObjs/>' .
				'</DevInf>';
		}

		/**
		 * Process DEVINF data.
		 *
		 * @param $data    Devinf data as XML array.
		 */
		function _put_devinf($data)
		{
			$this->session->set_var(
				SYNCML_SUPPORTNUMBEROFCHANGES,
				isset($data['DEVINF'][0]['SUPPORTNUMBEROFCHANGES'])
			);

			$this->session->set_var(
				SYNCML_SUPPORTLARGEOBJS,
				isset($data['DEVINF'][0]['SUPPORTLARGEOBJS'])
			);

			return TRUE;
		}
	}
?>
