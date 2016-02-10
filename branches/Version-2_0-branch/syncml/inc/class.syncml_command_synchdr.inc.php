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

	require_once 'inc/class.syncml_command.inc.php';

	require_once 'inc/class.syncml_auth_basic.inc.php';
	require_once 'inc/class.syncml_auth_md5.inc.php';

	require_once 'inc/class.sosession.inc.php';

	/**
	 * Handle the SYNCHDR.
	 */
	class syncml_command_synchdr extends syncml_command
	{
		function syncml_command_synchdr($xml_array = array())
		{
			if(is_array($xml_array))
			{
				$this->parse_xml_array($xml_array);
			}
		}

		function parse_xml_array($xml_array)
		{
			$this->verdtd =
				isset($xml_array['VERDTD'][0][SYNCML_XML_DATA]) ?
				$xml_array['VERDTD'][0][SYNCML_XML_DATA] : '';

			$this->verproto =
				isset($xml_array['VERPROTO'][0][SYNCML_XML_DATA]) ?
				$xml_array['VERPROTO'][0][SYNCML_XML_DATA] : '';

			$this->sessionid =
				isset($xml_array['SESSIONID'][0][SYNCML_XML_DATA]) ?
				$xml_array['SESSIONID'][0][SYNCML_XML_DATA] : '';

			$this->verproto =
				isset($xml_array['VERPROTO'][0][SYNCML_XML_DATA]) ?
				$xml_array['VERPROTO'][0][SYNCML_XML_DATA] : '';

			$this->msgid = isset($xml_array['MSGID'][0][SYNCML_XML_DATA]) ?
				$xml_array['MSGID'][0][SYNCML_XML_DATA] : '';

			if(isset($xml_array['RESPURI'][0][SYNCML_XML_DATA]))
			{
				$this->respuri = $xml_array['RESPURI'][0][SYNCML_XML_DATA];
			}

			parent::parse_xml_array($xml_array);

			if(!isset($this->source['locuri']))
			{
				$this->source['locuri'] = '';
			}

			if(!isset($this->target['locuri']))
			{
				$this->target['locuri'] = '';
			}
		}

		function execute(&$response, &$session)
		{
			$response->set_max_size(isset($this->meta['maxmsgsize']) ?
				min($this->meta['maxmsgsize'], SYNCML_MAXMSGSIZE) :
				SYNCML_MAXMSGSIZE);

			$response->set_header(
				$this->verdtd, $this->verproto,
				$this->sessionid, $this->msgid,
				isset($this->respuri) ?
					$this->respuri : $this->source['locuri'], '',
				$this->target['locuri'], ''
			);

			switch($this->verproto)
			{
				case 'SyncML/1.0':
				case 'SyncML/1.1':
					break;
				default:
					syncml_logger::get_instance()->log(
						"bad verproto: " . $this->verproto);
					$this->handle_failure(
						SYNCML_STATUS_PROTOCOLVERSIONNOTSUPPORTED,
						$response, $session);
					return;
			}

			switch($this->verdtd)
			{
				case '1.0':
				case '1.1':
					$response->set_syncml_namespace_version($this->verdtd);
					break;
				default:
					syncml_logger::get_instance()->log(
						"bad verdtd: " . $this->verdtd);
					$this->handle_failure(
						SYNCML_STATUS_DTDCOLVERSIONNOTSUPPORTED,
						$response, $session);
					return;
			}

			$sosession = new syncml_sosession();

			$id = array
			(
				$this->target['locuri'],
				$this->source['locuri'],
				$this->sessionid
			);

			list($phpgw_session_id, $next_nonce) =
				$sosession->get_session_mapping($id);

			$session->next_nonce = $next_nonce;
			$session->id = $id;

			if($GLOBALS['phpgw']->session->verify($phpgw_session_id))
			{
				$this->handle_success($response, $session);
			}
			else
			{
				syncml_logger::get_instance()->log("failed to verify session");

				$tmp = $this->process_cred($session);

				// tmp is session string on success and
				// int error code or failure.

				if(is_string($tmp))
				{
					syncml_logger::get_instance()->log(
						"credentials was OK");
					$sosession->set_session_mapping($id, $tmp);
					$this->handle_success($response, $session);
				}
				else
				{
					syncml_logger::get_instance()->log(
						"bad credentials");
					$this->handle_failure($tmp, $response, $session);
				}
			}

			$session->msgid = $this->msgid;
			$session->set_var('device_uri', $this->source['locuri']);
		}

		function handle_success(&$response, &$session)
		{
			$session->account_id = $GLOBALS['phpgw']->session->account_id;

			$session->session_data = $GLOBALS['phpgw']->session->appsession(
				'session_data', 'syncml');

			syncml_logger::get_instance()->log_data(
				"loaded session data", $session->session_data);

			$this->add_authentication_status(
				SYNCML_STATUS_AUTHENTICATIONACCEPTED, $response, $session);
		}

		/**
		 * Handle a authentication failure.
		 *
		 * @param $code     Status code to include in the command.
		 * @param $response Response object to write the status command to.
		 * @param $session  Session object.
		 */
		function handle_failure($code, &$response, &$session)
		{
			switch($code)
			{
				case SYNCML_STATUS_PROTOCOLVERSIONNOTSUPPORTED:
				case SYNCML_STATUS_DTDVERSIONNOTSUPPORTED:
					break;
				case SYNCML_STATUS_MISSINGCREDENTIALS:
				case SYNCML_UNSUPPORTEDAUTHTYPE:
					$code = SYNCML_STATUS_MISSINGCREDENTIALS;
					break;
				case FALSE:
				default:
					$code = SYNCML_STATUS_INVALIDCREDENTIALS;
					break;
			}

			$response->set_global_status_code($code);

			$this->add_authentication_status($code, $response, $session);
		}

		/**
		 *
		 */
		function add_authentication_status($code, &$response, &$session)
		{
			$auth_type = (isset($this->cred) &&
				isset($this->cred['meta']['type'])) ?
				$this->cred['meta']['type'] : '';

			$nonce = NULL;

			switch($auth_type)
			{
				case 'syncml:auth-md5':
					$nonce = md5(uniqid(time()));
					$session->next_nonce = $nonce;
				case 'syncml:auth-basic':
					break;
				default:
					$auth_type = 'syncml:auth-basic';
			}

			$response->add_status_with_chal(
				0, $this->msgid, 'SyncHdr',
				isset($this->target['locuri']) ?
					$this->target['locuri'] : NULL,
				isset($this->source['locuri']) ?
					$this->source['locuri'] : NULL,
				$code, $auth_type, $nonce
			);
		}

		/**
		 * Process the CRED node in SYNCHDR.
		 *
		 * @param $session  Session object.
		 * @return mixed    On failure, return error code. On success return
		 *                  new session ID.
		 */
		function process_cred(&$session)
		{
			// check if client sent credentials at all.
			if(!isset($this->cred) || !isset($this->cred['meta']['type']) ||
				!isset($this->cred['data']))
			{
				return SYNCML_STATUS_MISSINGCREDENTIALS;
			}

			// create auth object.
			switch($this->cred['meta']['type'])
			{
				case "syncml:auth-basic":
					$auth = new syncml_auth_basic();
					break;
				case "syncml:auth-md5":
					$auth = new syncml_auth_md5(
						$session,
						isset($this->source) &&
							isset($this->source['locname']) ?
						$this->source['locname'] : NULL);
					break;
				default:
					// unsupported auth type
					return SYNCML_UNSUPPORTEDAUTHTYPE;
			}

			// this returns either false, int or a session_id string.
			// if auth data is b64 encoded, decode it.
			return $auth->authenticate(
				(isset($this->cred['meta']['format']) &&
					$this->cred['meta']['format'] = 'b64') ?
				base64_decode($this->cred['data']) : $this->cred['data']);
		}
	}
?>
