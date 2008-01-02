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

  /* $Id: admin_deletequestion.php 17907 2007-01-24 16:51:08Z Caeies $ */

	$GLOBALS['phpgw_info'] = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp'   => 'polls',
		'enable_nextmatchs_class' => True,
		'admin_header' => True
	);
	if($HTTP_GET_VARS['confirm'])
	{
		$GLOBALS['phpgw_info']['flags']['noheader'] = True;
		$GLOBALS['phpgw_info']['flags']['nonavbar'] = True;
		$GLOBALS['phpgw_info']['flags']['admin_header'] = False;
	}
	include('../header.inc.php');

	$poll_id = intval(get_var('poll_id',Array('GET')));
	if(get_var('confirm',Array('GET')))
	{
		$GLOBALS['phpgw']->db->query("delete from phpgw_polls_desc where poll_id='".$poll_id."'",__LINE__,__FILE__);
		$GLOBALS['phpgw']->db->query("delete from phpgw_polls_data where poll_id='".$poll_id."'",__LINE__,__FILE__);
		$GLOBALS['phpgw']->db->query("delete from phpgw_polls_user where poll_id='".$poll_id."'",__LINE__,__FILE__);
		$GLOBALS['phpgw']->db->query("select MAX(poll_id) from phpgw_polls_desc",__LINE__,__FILE__);
		$max = $GLOBALS['phpgw']->db->f('1');
		$GLOBALS['phpgw']->db->query("update phpgw_polls_settings set setting_value='$max' where setting_name='currentpoll'");
		Header('Location: ' . $GLOBALS['phpgw']->link('/polls/admin.php', array('show' => 'questions')));
	}
	else
	{
		echo '<p><br><table border="0" width="40%" align="center"><tr><td align="center" colspan="center">';
		echo lang('Are you sure want to delete this question ?') . '</td></tr>';
		echo '<tr><td align="left"><a href="' . $GLOBALS['phpgw']->link('/polls/admin.php', array('show' => 'questions')) . '">' . lang('No') . '</td>';
		echo '    <td align="right"><a href="' . $GLOBALS['phpgw']->link('/polls/admin_deletequestion.php','poll_id='
			. $poll_id .'&confirm=True') . '">' . lang('Yes') . '</td></tr>';
		echo '</table>';
	}
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
