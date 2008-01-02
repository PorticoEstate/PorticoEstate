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

  /* $Id: viewgroup.php 8699 2001-12-21 03:59:13Z milosch $ */

	$phpgw_flags = Array(
		'currentapp'              => 'nntp',
		'enable_nextmatchs_class' => True
	);
	$GLOBALS['phpgw_info']['flags'] = $phpgw_flags;

	$GLOBALS['folder'] = (isset($GLOBALS['HTTP_GET_VARS']['folder'])?$GLOBALS['HTTP_GET_VARS']['folder']:'');
	$GLOBALS['folder'] = (isset($GLOBALS['HTTP_POST_VARS']['folder'])?$GLOBALS['HTTP_POST_VARS']['folder']:$GLOBALS['folder']);
	
	include('../header.inc.php');

	function close_routine()
	{
		$GLOBALS['nntp']->close_port();
	}

	@set_time_limit(0);

	$GLOBALS['phpgw']->translation->add_app('email');

	$GLOBALS['phpgw']->db->query('SELECT active FROM newsgroups WHERE con='.$GLOBALS['HTTP_GET_VARS']['folder']." AND active='Y'");
	if (($GLOBALS['phpgw']->db->num_rows() == 0) || !$GLOBALS['phpgw_info']['user']['preferences']['nntp'][$folder] )
	{
		echo 'You do not have access to this newsgroup!'."<br>\n";
		$GLOBALS['phpgw']->common->phpgw_exit();
	}

	if(!$GLOBALS['folder'])
	{
		echo lang('Cannot display the requested newsgroup')."<br>\n";
		$GLOBALS['phpgw']->common->phpgw_exit();
	}

	$GLOBALS['nntp']->display = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
	if ($GLOBALS['nntp']->errorset)
	{
		echo $GLOBALS['nntp']->error['msg'].':'.$GLOBALS['nntp']->error['desc']."<br>\n";
		$GLOBALS['phpgw']->common->phpgw_exit();
	}
 
	register_shutdown_function("close_routine");

	$p = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('nntp'));
	$templates = array(
		'view_group' => 'view_group.tpl'
	);
	$p->set_file($templates);
	$p->set_block('view_group','vg','vg');
	$p->set_block('view_group','vg_row','vg_row');

	$nntp->read_table($GLOBALS['folder']);

	$GLOBALS['start'] = (isset($GLOBALS['HTTP_GET_VARS']['start'])?$GLOBALS['HTTP_GET_VARS']['start']:'');
	$GLOBALS['start'] = (isset($GLOBALS['HTTP_POST_VARS']['start'])?$GLOBALS['HTTP_POST_VARS']['start']:$GLOBALS['start']);

	if(!$GLOBALS['start'])
	{
		$GLOBALS['start'] = 0;
	}

	if(!$tm)
	{
		$tm = ($GLOBALS['nntp']->highmsg - $GLOBALS['nntp']->lowmsg);
	}
	if(!$fm)
	{
		$fm = $GLOBALS['nntp']->lowmsg;
	}

	$var = Array(
		'th_bg'        => $GLOBALS['phpgw_info']['theme']['th_bg'],
		'th_font'      => $GLOBALS['phpgw_info']['theme']['font'],
		'th_text'      => $GLOBALS['phpgw_info']['theme']['th_text'],
		'th_em_folder' => $GLOBALS['phpgw_info']['theme']['em_folder'],
		'th_em_text'   => $GLOBALS['phpgw_info']['theme']['em_folder_text'],
		'folder'       => $GLOBALS['nntp']->folder,
		'nml'          => $GLOBALS['phpgw']->nextmatchs->left('/nntp/viewgroup.php',$start,$tm,'&folder='.$GLOBALS['folder'].'&tm='.$tm.'&fm='.$fm),
		'nmr'          => $GLOBALS['phpgw']->nextmatchs->right('/nntp/viewgroup.php',$start,$tm,'&folder='.$GLOBALS['folder'].'&tm='.$tm.'&fm='.$fm)
	);

	$p->set_var($var);

	$var = Array(
		'row_color' => $GLOBALS['phpgw_info']['theme']['th_bg'],
		'from'      => '<font size="2"><b>'.lang('from').'</b></font>',
		'subject'   => '<font size="2"><b>'.lang('subject').'</b></font>',
		'date'      => '<font size="2"><b>'.lang('date').'</b></font>'
//		'size'      => '<font size="2"><b>'.lang('size').'</b></font>'
	);

	$p->set_var($var);
	$p->parse('rows','vg_row',True);

	if ($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] <= ($tm - $start))
	{
		$totaltodisplay = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
	}
	else
	{
		$totaltodisplay = ($tm - $start) - 1;
	}

	$firstmessage = ($fm + $start);
	$list = $GLOBALS['nntp']->get_list('from',$firstmessage,$totaltodisplay);
//	$subject = $GLOBALS['nntp']->get_list('subject',$firstmessage,$firstmessage+$totaltodisplay);
//	$date = $GLOBALS['nntp']->get_list('date',$firstmessage,$firstmessage+$totaltodisplay);

	for ($i=0;$i<$totaltodisplay;$i++)
	{
		$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);

		$subject_url = $GLOBALS['phpgw']->link('/nntp/read_article.php','folder='.$GLOBALS['nntp']->con.'&msgnum='.$list[$i]['Msgnum']);

		$var = Array(
			'row_color' => $tr_color,
			'from'      => '<font size="-1">'.$list[$i]['From'].'</font>',
			'subject'   => '<font size="-1"><a href="'.$subject_url.'">'.$list[$i]['Subject'].'</a></font>',
			'date'      => '<font size="-2">'.$GLOBALS['phpgw']->common->show_date($list[$i]['Date']).'</font>'
//			'size'      => ''
		);
		$p->set_var($var);
//    if ($i < ($start+$totaltodisplay-1))
		$p->parse('rows','vg_row',True);
//      $GLOBALS['phpgw']->template->parse('output','vg_table_header','True');
	}        

	$p->pparse('out','vg');
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
