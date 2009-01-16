<?php
  /**************************************************************************\
  * phpGroupWare - Polls                                                     *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

	$GLOBALS['phpgw_info'] = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'admin_only'              => True,
		'currentapp'              => 'polls',
		'enable_nextmatchs_class' => True,
		'admin_header'            => True
	);
	include('../header.inc.php');

	if ($HTTP_POST_VARS['submit'])
	{
		$settings = $HTTP_POST_VARS['settings'];
		$GLOBALS['phpgw']->db->query('delete from phpgw_polls_settings',__LINE__,__FILE__);

		while(list($name,$value) = each($settings))
		{
			$GLOBALS['phpgw']->db->query("insert into phpgw_polls_settings values ('$name','$value')",__LINE__,__FILE__);
		}
		echo '<center>' . lang('Settings updated') . '</center>';
	}
	else
	{
		$GLOBALS['phpgw']->db->query('select * from phpgw_polls_settings',__LINE__,__FILE__);
		while ($GLOBALS['phpgw']->db->next_record())
		{
			$settings[$GLOBALS['phpgw']->db->f('setting_name')] = $GLOBALS['phpgw']->db->f('setting_value');
		}
	}

	echo '<form action="' . $GLOBALS['phpgw']->link('/polls/admin_settings.php') . '" method="POST">';
	echo '<table border="0">';
	echo '<tr><td>' . lang('Allow users to vote more then once') . '</td>'
		. '    <td><input type="checkbox" name="settings[allow_multiable_votes]"' . ($settings['allow_multiable_votes']?' checked':'') . ' value="True"></td></tr>';
	echo '<tr><td>' . lang('Select current poll') . '</td>'
		. '    <td><select name="settings[currentpoll]">';
	$GLOBALS['phpgw']->db->query('select * from phpgw_polls_desc order by poll_title',__LINE__,__FILE__);
	while ($GLOBALS['phpgw']->db->next_record())
	{
		echo '<option value="' . $GLOBALS['phpgw']->db->f('poll_id') . '"'
			. ($settings['currentpoll'] == $GLOBALS['phpgw']->db->f('poll_id')?' selected':'') 
			. '>' . stripslashes($GLOBALS['phpgw']->db->f('poll_title')) . '</option>';
	}
	echo '</select></td></tr>';

	echo '<tr><td colspan="2"><input type="submit" name="submit" value="' . lang('Submit') . '"></td></tr>';
	echo '</table></form>';
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
