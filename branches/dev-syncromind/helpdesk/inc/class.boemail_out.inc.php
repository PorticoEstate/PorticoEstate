<?php
	/**
	 * phpGroupWare
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License v2 or later
	 * @internal
	 * @package helpdesk
	 * @subpackage email_out
	 * @version $Id:$
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU Lesser General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	phpgw::import_class('phpgwapi.bocommon');
	phpgw::import_class('helpdesk.soemail_out');

	include_class('helpdesk', 'email_out', 'inc/model/');

	class helpdesk_boemail_out extends phpgwapi_bocommon
	{
		protected static
			$bo,
			$fields,
			$acl_location;

		public function __construct()
		{
			$this->fields = helpdesk_email_out::get_fields();
			$this->acl_location = helpdesk_email_out::acl_location;
		}

		/**
		 * Implementing classes must return an instance of itself.
		 *
		 * @return the class instance.
		 */
		public static function get_instance()
		{
			if (self::$bo == null)
			{
				self::$bo = new helpdesk_boemail_out();
			}
			return self::$bo;
		}

		public function store($object)
		{
			$this->store_pre_commit($object);
			$ret = helpdesk_soemail_out::get_instance()->store($object);
			$this->store_post_commit($object);
			return $ret;
		}

		public function read($params)
		{
			if(empty($params['filters']['active']))
			{
				$params['filters']['active'] = 1;
			}
			else
			{
				unset($params['filters']['active']);
			}
			$values =  helpdesk_soemail_out::get_instance()->read($params);
	//		$status_text = helpdesk_email_out::get_status_list();
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			foreach ($values['results'] as &$entry)
			{
	//				$entry['status'] = $status_text[$entry['status']];
					$entry['created'] = $GLOBALS['phpgw']->common->show_date($entry['created']);
					$entry['modified'] = $GLOBALS['phpgw']->common->show_date($entry['modified']);
			}
			return $values;
		}

		public function read_single($id, $return_object = true)
		{
			if ($id)
			{
				$values = helpdesk_soemail_out::get_instance()->read_single($id, $return_object);
			}
			else
			{
				$values = new helpdesk_email_out();
			}

			return $values;
		}

		public function get_recipient_candidates( $recipient_set_id, $email_out_id)
		{
			return helpdesk_soemail_out::get_instance()->get_recipient_candidates($recipient_set_id, $email_out_id);
		}

		function set_candidates($id, $ids)
		{
			return helpdesk_soemail_out::get_instance()->set_candidates($id, $ids);
		}

		function delete_recipients($id, $ids)
		{
			return helpdesk_soemail_out::get_instance()->delete_recipients($id, $ids);
		}

		public function get_recipients( $email_out_id )
		{
			return helpdesk_soemail_out::get_instance()->get_recipients($email_out_id);
		}

		public function send_email( $id, $ids = array() )
		{
			$email_out = $this->read_single($id);
			$subject = $email_out->subject;
			$content = nl2br($email_out->content);
			$sogeneric = CreateObject('helpdesk.sogeneric','email_recipient_list');
			$email_validator = CreateObject('phpgwapi.EmailAddressValidator');
			if (!is_object($GLOBALS['phpgw']->send))
			{
				$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
			}
			$config	= CreateObject('phpgwapi.config','helpdesk')->read();;

			$cc ='';
			$bcc = '';
			$from_email = $config['from_email'];
			$from_name = $config['from_email'];
			foreach ($ids as $recipient_id)
			{
				$recipient = $sogeneric->read_single(array('id' => $recipient_id));

				$to_email = $recipient['email'];
				if (!$email_validator->check_email_address($to_email) )
				{
					helpdesk_soemail_out::get_instance()->set_status($id, $recipient_id, helpdesk_email_out::STATUS_ERROR);
					continue;
				}

				try
				{
					$rcpt = $GLOBALS['phpgw']->send->msg('email', $to_email, $subject, stripslashes($content), '', $cc, $bcc, $from_email, $from_name, 'html');
					helpdesk_soemail_out::get_instance()->set_status($id, $recipient_id, helpdesk_email_out::STATUS_SENT);
				}
				catch (Exception $exc)
				{
					helpdesk_soemail_out::get_instance()->set_status($id, $recipient_id, helpdesk_email_out::STATUS_ERROR);
				}
			}
		}
	}