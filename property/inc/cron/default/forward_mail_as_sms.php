<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage custom
 	* @version $Id$
	*/

	/**
	 * Description
	 * usage:
	 * @package property
	 */

	include_class('property', 'cron_parent', 'inc/cron/');

	class forward_mail_as_sms extends property_cron_parent
	{
		function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location = lang('Async service');
			$this->function_msg	= 'Forward email as SMS';

			$this->bocommon		= CreateObject('property.bocommon');
		}

		function execute($data = array())
		{
			$data['account_id'] = $GLOBALS['phpgw']->accounts->name2id($data['user']);
			$this->check_for_new_mail($data);
		}

		function check_for_new_mail($data)
		{
			$GLOBALS['phpgw_info']['user']['account_id'] = $data['account_id'];
			$GLOBALS['phpgw']->preferences->set_account_id($data['data_id'], true);

			$GLOBALS['phpgw_info']['user']['preferences']= $GLOBALS['phpgw']->preferences->read();

			$boPreferences  = CreateObject('felamimail.bopreferences');

			$bofelamimail	= CreateObject('felamimail.bofelamimail');

//			$bofelamimail->closeConnection();
//			$boPreferences->setProfileActive(false);
//			$boPreferences->setProfileActive(true,2); //2 for selected user

			$connectionStatus = $bofelamimail->openConnection();
			$headers = $bofelamimail->getHeaders('INBOX', 1, $maxMessages = 15, $sort = 0, $_reverse = 1, $_filter = array('string' => '', 'type' => 'quick', 'status' => 'unseen'));

//_debug_array($headers);
//die();

			$sms = array();
			$j = 0;
			if (isset($headers['header']) && is_array($headers['header']))
			{
				foreach ($headers['header'] as $header)
				{
		//			if(!$header['seen'])
					{
						$sms[$j]['message'] = utf8_encode($header['subject']);
						$bodyParts = $bofelamimail->getMessageBody($header['uid']);
						$sms[$j]['message'] .= "\n";
						for($i=0; $i<count($bodyParts); $i++ )
						{
							$sms[$j]['message'] .= utf8_encode($bodyParts[$i]['body']) . "\n";
						}

						$sms[$j]['message'] = substr($sms[$j]['message'],0,160);
						$j++;
					}
					$bofelamimail->flagMessages('read', $header['uid']);
				}
			}

			if($connectionStatus)
			{
				$bofelamimail->closeConnection();
			}

			$bosms	= CreateObject('sms.bosms',false);
			foreach ($sms as $entry)
			{
				$bosms->send_sms(array('p_num_text'=>$data['cellphone'], 'message' =>$entry['message']));
			}

			if($j)
			{
				$msg = $j . ' meldinger er sendt';
				$this->receipt['message'][]=array('msg'=> $msg);
			}
		}
	}
