<?php
  /**************************************************************************\
  * phpGroupWare application (NNTP)                                          *
  * http://www.phpgroupware.org                                              *
  * Written by Mark Peters <mpeters@satx.rr.com>                             *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: read_article.php 8699 2001-12-21 03:59:13Z milosch $ */

	$phpgw_flags = Array(
		'currentapp'       => 'nntp',
		'enable_vfs_class' => True
	);

	$GLOBALS['folder'] = (isset($GLOBALS['HTTP_GET_VARS']['folder'])?$GLOBALS['HTTP_GET_VARS']['folder']:'');
	$GLOBALS['msgnum'] = (isset($GLOBALS['HTTP_GET_VARS']['msgnum'])?$GLOBALS['HTTP_GET_VARS']['msgnum']:'');

	$GLOBALS['phpgw_info']['flags'] = $phpgw_flags;
	include('../header.inc.php');

//	include(PHPGW_APP_INC.'/message.inc.php');

	function close_routine()
	{
		$GLOBALS['nntp']->close_port();
	}

	@set_time_limit(0);

	$GLOBALS['phpgw']->translation->add_app('email');

	function compose($action,$icon)
	{
		$str = '';
/*		$str = '<a href="'.$GLOBALS['phpgw']->link('/nntp/compose.php','action='.$action.'&folder='.$GLOBALS['nntp']->con.'&msgnum='.$GLOBALS['nntp']->msgnum).'">';	*/
		$str .= '<img src="'.$GLOBALS['phpgw']->common->image('email',$icon).'" height="19" width="26" alt="'.lang($action).'">';
/*		$str .= '</a>';	*/
		return $str;
	}

	if (!$GLOBALS['phpgw_info']['user']['preferences']['nntp'][$folder])
	{
		echo 'You do not have access to this newsgroup!<br>'."\n";
		$GLOBALS['phpgw']->common->phpgw_footer();
		$GLOBALS['phpgw']->common->phpgw_exit();
	}

	if(!$GLOBALS['HTTP_GET_VARS']['folder'] && !$GLOBALS['HTTP_GET_VARS']['msgnum'])
	{
		echo lang('Cannot display the requested article from this newsgroup').'<br>'."\n";
		$GLOBALS['phpgw']->common->phpgw_footer();
		$GLOBALS['phpgw']->common->phpgw_exit();
	}

	if ($GLOBALS['nntp']->errorset)
	{
		echo $GLOBALS['nntp']->error['msg'].':'.$GLOBALS['nntp']->error['desc']."<br>\n";
		$GLOBALS['phpgw']->common->phpgw_footer();
		$GLOBALS['phpgw']->common->phpgw_exit();
	}

	register_shutdown_function('close_routine');

	$p = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('nntp'));
	$templates = Array(
		'message' => 'msg.tpl'
	);

	$p->set_file($templates);
	$p->set_block('message','msg','msg');
	$p->set_block('message','action','action');
	$p->set_block('message','next_prev','next_prev');
	$p->set_block('message','header','header');
	$p->set_block('message','header_data','header_data');

	$var = Array(
		'th_em_folder' => $GLOBALS['phpgw_info']['theme']['em_folder'],
		'th_font'      => $GLOBALS['phpgw_info']['theme']['font'],
		'th_em_text'   => $GLOBALS['phpgw_info']['theme']['em_folder_text'],
		'th_bg'        => $GLOBALS['phpgw_info']['theme']['th_bg'],
		'th_row_on'    => $GLOBALS['phpgw_info']['theme']['row_on'],
		'folder_url'   => $GLOBALS['phpgw']->link('/nntp/viewgroup.php','folder='.$GLOBALS['nntp']->con),
		'folder'       => $GLOBALS['nntp']->folder
	);

	$p->set_var($var);

	$p->parse('out','header');

	$p->set_var('url',compose('reply','sm_reply.gif'));
	$p->parse('rows','action',True);

	$p->set_var('url',compose('reply all','sm_reply_all.gif'));
	$p->parse('rows','action',True);

	$p->set_var('url',compose('forward','sm_forward.gif'));
	$p->parse('rows','action',True);

	$p->set_var('url','<img src="'.$GLOBALS['phpgw']->common->image('email','sm_delete.gif').'" height="19" width="26" alt="'.lang('delete').'">');
	$p->parse('rows','action',True);

	$np_msg = '<a href="'.$GLOBALS['phpgw']->link('/nntp/read_article.php','folder='.$GLOBALS['nntp']->con);

	$pm = $GLOBALS['nntp']->get_prev_article_number($GLOBALS['nntp']->msgnum);
	if ((int)$pm <> 0)
	{
		$prev_msg = $np_msg.'&msgnum='.(int)$pm.'"><img border="0" src="'.$GLOBALS['phpgw']->common->image('phpgwapi','left.gif').'" alt="'.lang('Previous').'"></a>';
	}
	else
	{
		$prev_msg = '<img border="0" src="'.$GLOBALS['phpgw']->common->image('phpgwapi','left-grey.gif').'" alt="'.lang('Previous').'">';
	}

	$nm = $GLOBALS['nntp']->get_next_article_number($GLOBALS['nntp']->msgnum);
	if ((int)$nm <> 0)
	{
		$next_msg = $np_msg.'&msgnum='.(int)$nm.'"><img border="0" src="'.$GLOBALS['phpgw']->common->image('phpgwapi','right.gif').'" alt="'.lang('Next').'"></a>';
	}
	else
	{
		$next_msg = '<img border="0" src="'.$GLOBALS['phpgw']->common->image('phpgwapi','right-grey.gif').'" alt="'.lang('Next').'">';
	}

	$var = Array(
		'pm' => $prev_msg,
		'nm' => $next_msg
	);
	$p->set_var($var);

	$p->parse('rows','next_prev',True);

	$from = $GLOBALS['nntp']->msg->from[0];
	$var = Array(
		'label'        => lang('from'),
		'header_title' => send_to($from,$GLOBALS['nntp']->con),
		'header_icon'  => add_to_addressbook($from)
	);
	$p->set_var($var);
	$p->parse('data','header_data');
	$p->parse('rows','header',True);

	$var = Array(
		'data'  => '',
		'label' => lang('to')
	);
	$p->set_var($var);
	$to = new address;
	for($i=0;$i<count($GLOBALS['nntp']->msg->to);$i++)
	{
		if (!$GLOBALS['nntp']->msg->to[$i]) { break; }
		$to = $GLOBALS['nntp']->msg->to[$i];
		$topersonal = $to->personal;
		$GLOBALS['nntp']->db->query("SELECT con FROM newsgroups WHERE name='".$topersonal."' and active='Y'");

		if ($GLOBALS['nntp']->db->num_rows() > 0)
		{
			$GLOBALS['nntp']->db->next_record();
			$con = $GLOBALS['nntp']->db->f('con');
			$header_title = send_to($to,$GLOBALS['nntp']->con);
			if (!$GLOBALS['phpgw_info']['user']['preferences']['nntp'][$con])
			{
				$monitor = monitor(1,$con);
			}
			else
			{
				$monitor = monitor(0,0);
			}
		}
		else
		{
			$toadl = $to->adl;
			$header_title = $toadl;
			$monitor = monitor(0,0);
		}
		if($i<count($GLOBALS['nntp']->msg->to)) { $monitor .= '<br>'; }
		$var = Array(
			'header_title' => $header_title,
			'header_icon'  => $monitor
		);
		$p->set_var($var);
		$p->parse('data','header_data',True);
	}
	$p->parse('rows','header',True);

	if (isset($GLOBALS['nntp']->header['Cc']) && $GLOBALS['nntp']->header['Cc'])
	{
		$var = Array(
			'data'  => '',
			'label' => lang('cc')
		);
		$p->set_var($var);
		for($I=0;$i<count($GLOBALS['nntp']->msg->cc);$i++)
		{
			$var = Array(
				'header_title' => send_to($GLOBALS['nntp']->msg->cc[$i],$GLOBALS['nntp']->con),
				'header_icon'  => add_to_addressbook($GLOBALS['nntp']->msg->cc[$i])
			);
			
			$p->set_var($var);
			$p->parse('data','header_data',True);
		}
		$p->parse('rows','header',True);
	}

	$var = Array(
		'label'        => lang('date'),
		'header_title' => $GLOBALS['phpgw']->common->show_date($GLOBALS['nntp']->msg->udate),
		'header_icon'  => '',
		'data'         => ''
	);
	$p->set_var($var);

	$p->parse('data','header_data',True);
	$p->parse('rows','header',True);

// Still doing nothing with attachments

	$var = Array(
		'label'        => lang('subject'),
		'header_title' => $GLOBALS['nntp']->msg->subject,
		'header_icon'  => '',
		'data'         => ''
	);
	$p->set_var($var);
	
	$p->parse('data','header_data',True);
	$p->parse('rows','header',True);

	$p->set_var('textbody',$GLOBALS['nntp']->build_body_to_print());
	$p->pparse('out','msg');

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
