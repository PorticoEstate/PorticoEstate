<?php
	/**
	* Trouble Ticket System
	*
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package tts
	* @subpackage hooks
	* @version $Id: hook_home.inc.php 17583 2006-11-25 08:03:13Z sigurdne $
	*/

	$d1 = (isset($GLOBALS['phpgw_info']['server']['app_inc'])?strtolower(substr($GLOBALS['phpgw_info']['server']['app_inc'],0,3)):'');
	if($d1 == 'htt' || $d1 == 'ftp' )
	{
		echo 'Failed attempt to break in via an old Security Hole!<br>'."\n";
		$GLOBALS['phpgw']->common->phpgw_exit();
	}
	unset($d1);

	if ($GLOBALS['phpgw_info']['user']['apps']['tts']
		&& isset($GLOBALS['phpgw_info']['user']['preferences']['tts']['mainscreen_show_new_updated'])
		&& $GLOBALS['phpgw_info']['user']['preferences']['tts']['mainscreen_show_new_updated'])
	{
		$GLOBALS['phpgw']->translation->add_app('tts');

		$db2 = $GLOBALS['phpgw']->db;
		$GLOBALS['phpgw']->historylog = createobject('phpgwapi.historylog','tts');

		// this will be an user option
		$filtermethod="where ticket_status='O' and ticket_assignedto='".$GLOBALS['phpgw_info']['user']['account_id']."'";
		$sortmethod='order by ticket_priority desc';

		$GLOBALS['phpgw']->db->query('select ticket_id,ticket_category,ticket_priority,ticket_assignedto,ticket_owner,ticket_subject '
			. 'from phpgw_tts_tickets ' . $filtermethod . ' ' . $sortmethod,__LINE__,__FILE__);

		$tmp_app_tpl = $GLOBALS['phpgw']->common->get_tpl_dir('tts');
		$p = CreateObject('phpgwapi.Template',$tmp_app_tpl);
		$p->set_file('index','hook_home.tpl');

		$p->set_block('index', 'tts_list', 'tts_list');
		$p->set_block('index', 'tts_row', 'tts_row');
		$p->set_block('index', 'tts_ticket_id_read', 'tts_ticket_id_read');
		$p->set_block('index', 'tts_ticket_id_unread', 'tts_ticket_id_unread');
		$p->set_var(
			Array(
				'tts_head_id'			=> lang('Ticket').' #',
				'tts_head_openedby'		=> lang('Opened by'),
				'tts_head_dateopened'	=> lang('Date opened'),
				'tts_head_subject'		=> lang('Subject')
			)
		);
		while ($GLOBALS['phpgw']->db->next_record())
		{

			$p->set_var('tts_col_status','');
			$priority=$GLOBALS['phpgw']->db->f('ticket_priority');
			if($priority >= 1 && $priority <= 9)
			{
				$tr_color = $GLOBALS['phpgw_info']['theme']['bg0'.$priority];
			}
			elseif($priority==10)
			{
				$tr_color = $GLOBALS['phpgw_info']['theme']['bg10'];
			}
			else
			{
				$tr_color = $GLOBALS['phpgw_info']['theme']['bg_color'];
			}

			$db2->query("select count(*) from phpgw_tts_views where view_id='" . $GLOBALS['phpgw']->db->f('ticket_id')
				. "' and view_account_id='" . $GLOBALS['phpgw_info']['user']['account_id'] . "'",__LINE__,__FILE__);
			$db2->next_record();
			if($db2->f(0))
			{
				$t_read=1;
			}
			else
			{
				$t_read=0;
			}
			$p->set_var('tts_row_color', $tr_color );
			$p->set_var('tts_ticketdetails_link', $GLOBALS['phpgw']->link('/tts/viewticket_details.php','ticket_id=' . $GLOBALS['phpgw']->db->f('ticket_id')));

			$p->set_var('tts_t_id',$GLOBALS['phpgw']->db->f('ticket_id') );

			if (!$t_read==1)
			{
				$p->fp('tts_ticket_id', 'tts_ticket_id_unread', false );
			}
			else
			{
				$p->fp('tts_ticket_id', 'tts_ticket_id_read', false );
			}

			$p->set_var('tts_t_user', $GLOBALS['phpgw']->accounts->id2name($GLOBALS['phpgw']->db->f('ticket_owner')));
			$history_values = $GLOBALS['phpgw']->historylog->return_array(array(),array('O'),'history_timestamp','ASC',$GLOBALS['phpgw']->db->f('ticket_id'));
			$p->set_var('tts_t_timestampopened',$GLOBALS['phpgw']->common->show_date($history_values[0]['datetime'] - ((60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'])));
			$p->set_var('tts_t_subject', $GLOBALS['phpgw']->db->f('ticket_subject'));

			$p->fp('rows','tts_row',true);
		}
		
		$extra_data = '<td>'."\n".$p->fp('out','tts_list').'</td>'."\n";
		
		$portalbox = CreateObject('phpgwapi.listbox',
			array(
				'title'     => '<font color="#FFFFFF">' . lang('Trouble Ticket System') . '</font>',
				'primary'   => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
				'secondary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
				'tertiary'  => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
				'width'     => '100%',
				'outerborderwidth' => '0',
				'header_background_image' => $GLOBALS['phpgw']->common->image('phpgwapi', 'bg_filler', '.png', 'bg_filler', False)
			)
		);

		$app_id = $GLOBALS['phpgw']->applications->name2id('tts');
		$GLOBALS['portal_order'][] = $app_id;
		$var = array(
			'up'       => array('url' => '/set_box.php', 'app' => $app_id),
			'down'     => array('url' => '/set_box.php', 'app' => $app_id),
			'close'    => array('url' => '/set_box.php', 'app' => $app_id),
			'question' => array('url' => '/set_box.php', 'app' => $app_id),
			'edit'     => array('url' => '/set_box.php', 'app' => $app_id)
		);

		while(list($key,$value) = each($var))
		{
			$portalbox->set_controls($key,$value);
		}
		$portalbox->data = array();
		echo "\n".'<!-- Begin TTS New/Updated -->'."\n".$portalbox->draw($extra_data)."\n".'<!-- End TTS New/Updated -->'."\n";
	}
?>
