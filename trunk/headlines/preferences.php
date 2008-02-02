<?php
	/**************************************************************************\
	* phpGroupWare - headlines                                                 *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	$phpgw_info = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp'              => 'headlines',
		'noheader'                => True,
		'nonavbar'                => True,
		'enable_nextmatchs_class' => True
	);
	include('../header.inc.php');

	$submit = get_var('submit',array('POST'));
	if(!$submit)
	{
		unset($GLOBALS['phpgw_info']['flags']['noheader']);
		unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
		$GLOBALS['phpgw']->common->phpgw_header();

		$GLOBALS['phpgw']->template->set_file(array('form' => 'preferences.tpl'));

		$GLOBALS['phpgw']->template->set_var('form_action',$GLOBALS['phpgw']->link('/headlines/preferences.php'));
		$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
		$GLOBALS['phpgw']->template->set_var('lang_header',lang('select headline news sites'));
		$GLOBALS['phpgw']->template->set_var('lang_headlines',lang('Headline preferences'));

		$GLOBALS['phpgw']->db->query('SELECT con,display FROM phpgw_headlines_sites ORDER BY display asc',__LINE__,__FILE__);
		while($GLOBALS['phpgw']->db->next_record())
		{
			$html_select .= '<option value=\'' . $GLOBALS['phpgw']->db->f('con') . '\'';
			//. $users_headlines[$GLOBALS['phpgw']->db->f('con')];

			if($GLOBALS['phpgw_info']['user']['preferences']['headlines'][$GLOBALS['phpgw']->db->f('con')])
			{
				$html_select .= ' selected';
			}
			$html_select .= '>' . $GLOBALS['phpgw']->db->f('display') . '</option>'."\n";
		}
		$GLOBALS['phpgw']->template->set_var('select_options',$html_select);

		$GLOBALS['phpgw']->template->set_var('tr_color_1',$GLOBALS['phpgw']->nextmatchs->alternate_row_color());
		$GLOBALS['phpgw']->template->set_var('tr_color_2',$GLOBALS['phpgw']->nextmatchs->alternate_row_color());

		$GLOBALS['phpgw']->template->set_var('lang_submit',lang('submit'));

		$GLOBALS['phpgw']->template->parse('phpgw_body','form');
	}
	else
	{
		$i = 0;
		while(is_array($GLOBALS['phpgw_info']['user']['preferences']['headlines']) &&
			$preference = each($GLOBALS['phpgw_info']['user']['preferences']['headlines']))
		{
			$GLOBALS['phpgw']->preferences->delete('headlines',$preference[0]);
		}

		$user_selected = get_var('headlines',Array('POST'));
		if(count($user_selected))
		{
			while($value = each($user_selected))
			{
				$GLOBALS['phpgw']->preferences->add('headlines',$value[1],'True');
			}
		}

		$GLOBALS['phpgw']->preferences->save_repository(True);

		Header('Location: ' . $GLOBALS['phpgw']->link('/preferences/index.php'));
	}
//	$GLOBALS['phpgw']->common->phpgw_footer();
?>
