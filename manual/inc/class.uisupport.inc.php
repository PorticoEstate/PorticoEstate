<?php
	/**
	 * Support - ask for support
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package Frontend
	 * @version $Id$
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

	/**
	 * Manual
	 *
	 * @package Manual
	 */
	class manual_uisupport
	{

		public $public_functions = array
			(
			'send' => true,
		);

		public function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
		}

		public function send()
		{
			$values = phpgw::get_var('values');

			$receipt = array();
			if (isset($values['save']))
			{
				if ($GLOBALS['phpgw']->session->is_repost())
				{
					$receipt['error'][] = array('msg' => lang('repost'));
				}

				if (!isset($values['address']) || !$values['address'])
				{
					$receipt['error'][] = array('msg' => lang('Missing address'));
				}

				if (!isset($values['details']) || !$values['details'])
				{
					$receipt['error'][] = array('msg' => lang('Please give som details'));
				}

				$attachments = array();

				if (isset($_FILES['file']['name']) && $_FILES['file']['name'])
				{
					$file_name = str_replace(' ', '_', $_FILES['file']['name']);
					$mime_magic = createObject('phpgwapi.mime_magic');
					$mime = $mime_magic->filename2mime($file_name);

					$attachments[] = array
						(
						'file' => $_FILES['file']['tmp_name'],
						'name' => $file_name,
						'type' => $mime
					);
				}

				if (!$receipt['error'])
				{
					if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
					{
						if (!is_object($GLOBALS['phpgw']->send))
						{
							$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
						}

						$from = "{$GLOBALS['phpgw_info']['user']['fullname']}<{$values['from_address']}>";

						$receive_notification = true;
						$rcpt = $GLOBALS['phpgw']->send->msg('email', $values['address'], 'Support', stripslashes(nl2br($values['details'])), '', '', '', $from, $GLOBALS['phpgw_info']['user']['fullname'], 'html', '', $attachments, $receive_notification);

						if ($rcpt)
						{
							$receipt['message'][] = array('msg' => lang('message sent'));
						}
					}
					else
					{
						$receipt['error'][] = array('msg' => lang('SMTP server is not set! (admin section)'));
					}
				}
			}

			//optional support address per app
			$app = phpgw::get_var('app');
			$config = CreateObject('phpgwapi.config', $app);
			$config->read();
			$support_address = isset($config->config_data['support_address']) && $config->config_data['support_address'] ? $config->config_data['support_address'] : $GLOBALS['phpgw_info']['server']['support_address'];

			$data = array
				(
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($receipt)),
				'from_name' => $GLOBALS['phpgw_info']['user']['fullname'],
				'from_address' => $GLOBALS['phpgw_info']['user']['preferences']['common']['email'],
				'form_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'manual.uisupport.send')),
				'support_address' => $support_address,
				'form_type'		=> phpgw::get_var('form_type', 'string', 'GET', 'aligned')
			);

			$GLOBALS['phpgw']->xslttpl->add_file('support');
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('send' => $data));
		}
	}