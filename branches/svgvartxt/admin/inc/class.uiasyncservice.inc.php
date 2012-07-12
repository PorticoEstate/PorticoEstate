<?php
	/**************************************************************************\
	* phpGroupWare Admin - Timed Asynchron Services for phpGroupWare           *
	* Written by Ralf Becker <RalfBecker@outdoor-training.de>                  *
	* Class to admin cron-job like timed calls of phpGroupWare methods         *
	* -------------------------------------------------------------------------*
	* This library is part of the phpGroupWare API                             *
	* http://www.phpgroupware.org/                                             *
	* ------------------------------------------------------------------------ *
	* This library is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU Lesser General Public License as published by *
	* the Free Software Foundation; either version 2.1 of the License,         *
	* or any later version.                                                    *
	* This library is distributed in the hope that it will be useful, but      *
	* WITHOUT ANY WARRANTY; without even the implied warranty of               *
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                     *
	* See the GNU Lesser General Public License for more details.              *
	* You should have received a copy of the GNU Lesser General Public License *
	* along with this library; if not, write to the Free Software Foundation,  *
	* Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA            *
	\**************************************************************************/

	/* $Id$ */

	class admin_uiasyncservice
	{
		public $public_functions = array('index' => True);

		public function __construct()
		{
			if ( !isset($GLOBALS['phpgw']->asyncservice)
				|| !is_object($GLOBALS['phpgw']->asyncservice) )
			{
				$GLOBALS['phpgw']->asyncservice = CreateObject('phpgwapi.asyncservice');
			}
		}

		public function index()
		{
			if ($GLOBALS['phpgw']->acl->check('asyncservice_access',1,'admin'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php');
			}

			$GLOBALS['phpgw_info']['flags']['current_selection'] = 'admin::admin::async';

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Admin').' - '.lang('Asynchronous timed services');
			$GLOBALS['phpgw']->common->phpgw_header(true);

			$manual_run = phpgw::get_var('manual_run', 'bool', 'POST');

			$async = clone($GLOBALS['phpgw']->asyncservice);	// use an own instance, as we might set debug=True

			$async->debug = phpgw::get_var('debug', 'bool', 'POST');

			$units = array
			(
				'year'  => lang('Year'),
				'month' => lang('Month'),
				'day'   => lang('Day'),
				'dow'   => lang('Day of week<br>(0-6, 0=Sun)'),
				'hour'  => lang('Hour<br>(0-23)'),
				'min'   => lang('Minute')
			);

			$debug = phpgw::get_var('debug', 'bool', 'POST');

			$send		= phpgw::get_var('send', 'bool', 'POST');
			$test		= phpgw::get_var('test', 'bool', 'POST');
			$cancel		= phpgw::get_var('cancel', 'bool', 'POST');
			$install	= phpgw::get_var('install', 'bool', 'POST');
			$update		= phpgw::get_var('update', 'bool', 'POST');
			$asyncservice = phpgw::get_var('asyncservice', 'string', 'POST');

			if ( $send || $test || $cancel || $install || $update || $asyncservice || $manual_run)
			{

				if($manual_run)
				{
					$account_id = $GLOBALS['phpgw_info']['user']['account_id'];
					$num = $async->check_run('crontab');
					echo "<p><b>{$num} jobs found</b></p>\n";
					//reset your environment
					$GLOBALS['phpgw']->session->set_account_id($account_id);
					$GLOBALS['phpgw']->session->read_repositories(False,False);
					$GLOBALS['phpgw_info']['user']  = $GLOBALS['phpgw']->session->get_user();
				}

				$times = array();
				foreach ( array_keys($units) as $u )
				{
					$times[$u] = phpgw::get_var($u, 'string', 'POST');
					if ( $times[$u] === '' )
					{
						unset($times[$u]);
					}
				}

				if ( $test )
				{
					$email = phpgw::get_var('email', 'string', 'POST');
					if(!$email)
					{
						$prefs = $GLOBALS['phpgw']->preferences->create_email_preferences();
						$email = $prefs['email']['address'];
					}
					$validator = CreateObject('phpgwapi.EmailAddressValidator');
					if(!$validator->check_email_address($email))
					{
						echo '<p><b>'.lang("Not a not valid email address")."</b></p>\n";	
					}
					else if (!$async->set_timer($times,'test','admin.uiasyncservice.test',array('to' => $email)))
					{
						echo '<p><b>'.lang("Error setting timer, wrong syntax or maybe there's one already running !!!")."</b></p>\n";
					}
					unset($prefs);
				}
				if ( $cancel )
				{
					if (!$async->cancel_timer('test'))
					{
						echo '<p><b>'.lang("Error canceling timer, maybe there's none set !!!")."</b></p>\n";
					}
				}
				if ( $install )
				{
					if (!($install = $async->install($times)))
					{
						echo '<p><b>'.lang('Error: %1 not found or other error !!!',$async->crontab)."</b></p>\n";
					}
					else
					{
						$asyncservice = 'cron';
					}
				}

				if ( $asyncservice )
				{
					if (!isset($GLOBALS['phpgw_info']['server']['asyncservice'])
						|| $asyncservice != $GLOBALS['phpgw_info']['server']['asyncservice'] )
					{
						$config = CreateObject('phpgwapi.config','phpgwapi');
						$config->read();
						$config->value('asyncservice', $asyncservice);
						$config->save_repository();
						unset($config);
						$GLOBALS['phpgw_info']['server']['asyncservice'] = $asyncservice;
					}

					if(($asyncservice == 'off' || $asyncservice == 'fallback') && !$install)
					{
						$async->uninstall();
					}
				}
			}
			else
			{
				$times = array('min' => '*/5');		// set some default
			}
			echo '<form action="'.$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'admin.uiasyncservice.index')).'" method="POST">'."\n<p>";
			echo '<div style="text-align: left; margin: 10px;">'."\n";

			$last_run = $async->last_check_run();
			$lr_date = $last_run['end'] ? $GLOBALS['phpgw']->common->show_date($last_run['end']) : lang('never');
			echo '<p><b>'.lang('Async services last executed').'</b>: '.$lr_date.' ('.$last_run['run_by'].")</p>\n<hr>\n";

			if (!$async->only_fallback)
			{
				$installed = $async->installed();
				if (is_array($installed) && isset($installed['cronline']))
				{
					$async_use['cron'] = lang('crontab only (recomended)');
				}
			}
			$async_use['fallback']    = lang('fallback (after each pageview)');
			$async_use['off'] = lang('disabled (not recomended)');
			echo '<p><b>'.lang('Run Asynchronous services').'</b>'.
				' <select name="asyncservice" onChange="this.form.submit();">';
			foreach ($async_use as $key => $label)
			{
				$selected = $key == $GLOBALS['phpgw_info']['server']['asyncservice'] ? ' selected' : ''; 
				echo "<option value=\"$key\"$selected>$label</option>\n";
			}
			echo "</select></p>\n";

			if ($async->only_fallback)
			{
				echo '<p>'.lang('Under windows you can only use the fallback mode at the moment. Fallback means the jobs get only checked after each page-view !!!')."</p>\n";
			}
			else
			{
				echo '<p>'.lang('Installed crontab').": \n";

				if (is_array($installed) && isset($installed['cronline']))
				{
					echo "$installed[cronline]</p>";
				}
				elseif ($installed === 0)
				{
					echo '<b>'.lang('%1 not found or not executable !!!',$async->crontab)."</b></p>\n";
				}
				else
				{
					echo '<b>'.lang('asyncservices not yet installed or other error (%1) !!!',$installed['error'])."</b></p>\n";
				}
				echo '<p><input type="submit" name="install" value="'.lang('Install crontab')."\">\n".
					lang("for the times below (empty values count as '*', all empty = every minute)")."</p>\n";
			}

			echo "<hr><table border=0><tr>\n";
			foreach ($units as $u => $ulabel)
			{
				echo " <td>$ulabel</td><td><input name=\"$u\" value=\"$times[$u]\" size=5> &nbsp; </td>\n";
			}
			echo "</tr><tr>\n <td colspan=4>\n";
			echo ' <input type="submit" name="send" value="'.lang('Calculate next run').'"></td>'."\n";
			echo ' <td colspan="8"><input type="checkbox" name="debug" value="1"' . ($debug ? ' checked' : '')."> \n".
				lang('Enable debug-messages')."</td>\n</tr></table>\n";

			if ( $send )
			{
				$next = $async->next_run($times,True);

				echo "<p>asyncservice::next_run(";print_r($times);echo")=".($next === False ? 'False':"'$next'=".$GLOBALS['phpgw']->common->show_date($next))."</p>\n";
			}
			echo '<hr><p><input type="submit" name="cancel" value="'.lang('Cancel TestJob!')."\"> &nbsp;\n";
			echo lang('email') . '<input type="text" name="email" value="">'."\n";
			echo '<input type="submit" name="test" value="'.lang('Start TestJob!')."\">\n";
			echo lang('for the times above')."</p>\n";
			echo '<p>'.lang('The TestJob sends you a mail everytime it is called.')."</p>\n";

			echo '<hr><p><b>'.lang('Jobs').":</b>\n";
			if ($jobs = $async->read('%'))
			{
				echo "<table border=1>\n<tr>\n<th>Id</th><th>".lang('Next run').'</th><th>'.lang('Times').'</th><th>'.lang('Method').'</th><th>'.lang('Data')."</th><th>".lang('LoginID')."</th></tr>\n";
				foreach($jobs as $job)
				{
					echo "<tr>\n<td>{$job['id']}</td><td>".$GLOBALS['phpgw']->common->show_date($job['next'])."</td><td>";
					print_r($job['times']); 
					echo "</td><td>{$job['method']}</td><td>"; 
					print_r($job['data']); 
					echo "</td><td align=\"center\">".$GLOBALS['phpgw']->accounts->id2name($job['account_id'])."</td></tr>\n"; 
				}
				echo "</table>\n";
			}
			else
			{
				echo lang('No jobs in the database !!!')."</p>\n";
			}
			echo '<p><input type="submit" name="update" value="'.lang('Update').'">'."\n";
			echo '<input type="submit" name="manual_run" value="'.lang('manual run').'"></p>'."\n";
			echo "</form>\n";

		}

		public function test($data)
		{
			$to = $data['to'];
			$from = $GLOBALS['phpgw']->preferences->values['email'];
			if (!is_object($GLOBALS['phpgw']->send))
			{
				$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
			}
			$returncode = $GLOBALS['phpgw']->send->msg('email', $to, $subject='Asynchronous timed services', 'Greatings from cron ;-)', '', '', '', $from);

			if (!$returncode)	// not nice, but better than failing silently
			{
				echo "<p>bocalendar::send_update: sending message to '$to' subject='$subject' failed !!!<br>\n"; 
				echo $GLOBALS['phpgw']->send->err['desc']."</p>\n";
			}
			//print_r($GLOBALS['phpgw_info']['user']);
		}
	}
