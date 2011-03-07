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

	class forward_mail_as_sms
	{
		var	$function_name = 'forward_mail_as_sms';

		function forward_mail_as_sms()
		{
			$this->bocommon		= CreateObject('property.bocommon');
			$this->db     			= & $GLOBALS['phpgw']->db;
		}

		function pre_run($data = array())
		{
			if(isset($data['enabled']) && $data['enabled']==1)
			{
				$confirm	= true;
				$cron		= true;
				$data['account_id'] = $GLOBALS['phpgw']->accounts->name2id($data['user']);
				$GLOBALS['phpgw_info']['user']['account_id'] = $data['account_id'];
				$GLOBALS['phpgw']->session->account_id = $data['account_id'];
				$GLOBALS['phpgw']->session->appsession('session_data','mail2sms',$data);
			}
			else
			{
				$confirm	= phpgw::get_var('confirm', 'bool', 'POST');
				$execute	= phpgw::get_var('execute', 'bool', 'GET');
				$cron = false;
			}

			if (isset($confirm) && $confirm)
			{
				$this->execute($cron);
			}
			else
			{
				$this->confirm($execute=false);
				$data['account_id'] = $GLOBALS['phpgw']->accounts->name2id($data['user']);
				$GLOBALS['phpgw']->session->appsession('session_data','mail2sms',$data);
			}
		}

		function confirm($execute='')
		{
			$link_data = array
			(
				'menuaction' => 'property.custom_functions.index',
				'function'	=> $this->function_name,
				'execute'	=> $execute,
			);

			if(!$execute)
			{
				$lang_confirm_msg 	= lang('Do you want to execute this action?');
			}

			$lang_yes			= lang('yes');

			$GLOBALS['phpgw']->xslttpl->add_file(array('confirm_custom'));

			$msgbox_data = isset($this->receipt)?$this->bocommon->msgbox_data($this->receipt):'';

			$data = array
			(
				'msgbox_data'			=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'done_action'			=> $GLOBALS['phpgw']->link('/admin/index.php'),
				'run_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'message'				=> $this->receipt['message'],
				'lang_confirm_msg'		=> $lang_confirm_msg,
				'lang_yes'				=> $lang_yes,
				'lang_yes_statustext'	=> lang('Check for new mail - and forward as sms'),
				'lang_no_statustext'	=> 'tilbake',
				'lang_no'				=> lang('no'),
				'lang_done'				=> lang('cancel'),
				'lang_done_statustext'	=> 'tilbake'
			);

			$appname		= lang('Async service');
			$function_msg	= 'Forward email as SMS';
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('confirm' => $data));
			$GLOBALS['phpgw']->xslttpl->pp();
		}

		function execute($cron='')
		{
			$receipt = $this->check_for_new_mail();
			if($receipt)
			{
				$this->cron_log($receipt,$cron);
			}

			if(!$cron)
			{
				$this->confirm($execute=false);
			}
		}

		function cron_log($receipt='',$cron='')
		{
			$insert_values= array(
				$cron,
				date($this->bocommon->datetimeformat),
				$this->function_name,
				$receipt
				);

			$insert_values	= $this->bocommon->validate_db_insert($insert_values);

			$sql = "INSERT INTO fm_cron_log (cron,cron_date,process,message) "
					. "VALUES ($insert_values)";
			$this->db->query($sql,__LINE__,__FILE__);
		}

		function check_for_new_mail()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','mail2sms');

			$GLOBALS['phpgw_info']['user']['account_id'] = $data['account_id'];
			$GLOBALS['phpgw']->preferences->account_id = $data['account_id'];
			$pref = $GLOBALS['phpgw']->preferences->read();
			$GLOBALS['phpgw_info']['user']['preferences']['felamimail'] = isset($pref['felamimail']) ? $pref['felamimail'] : '';

			$boPreferences  = CreateObject('felamimail.bopreferences');
			$boPreferences->setProfileActive(false);
			$boPreferences->setProfileActive(true,2); //2 for selected user
			$bofelamimail	= CreateObject('felamimail.bofelamimail');

			$connectionStatus = $bofelamimail->openConnection();
			$headers = $bofelamimail->getHeaders('INBOX', 1, $maxMessages = 15, $sort = 0, $_reverse = 1, $_filter = array('string' => '', 'type' => 'quick', 'status' => 'unseen'));

			$j = 0;
			if (isset($headers['header']) && is_array($headers['header']))
			{
				foreach ($headers['header'] as $header)
				{
					if($header['seen'] == 0)
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
				}
			}
			if($connectionStatus == 'true')
			{
				$bofelamimail->closeConnection();
			}

			$bosms	= CreateObject('sms.bosms',false);
			if(isset($sms) && is_array($sms))
			{
				foreach ($sms as $entry)
				{
					$bosms->send_sms(array('p_num_text'=>$data['cellphone'], 'message' =>$entry['message']));
				}
			}

			$msg = $j . ' messages er sendt';
			$this->receipt['message'][]=array('msg'=> $msg);

			if($j>0)
			{
				return $msg;
			}
			else
			{
				return false;
			}
		}
	}
