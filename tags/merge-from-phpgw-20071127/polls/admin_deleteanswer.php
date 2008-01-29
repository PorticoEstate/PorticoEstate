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

  /* $Id: admin_deleteanswer.php 17907 2007-01-24 16:51:08Z Caeies $ */

	$phpgw_info = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'admin_only'              => True,
		'currentapp'              => 'polls',
		'enable_nextmatchs_class' => True,
		'admin_header'            => True
	);
	if($HTTP_GET_VARS['confirm'])
	{
		$GLOBALS['phpgw_info']['flags']['noheader'] = True;
		$GLOBALS['phpgw_info']['flags']['nonavbar'] = True;
		$GLOBALS['phpgw_info']['flags']['admin_header'] = False;
	}
	include('../header.inc.php');

	$vote_id = get_vars('vots_id',Array('GET'));
	if(get_var('confirm',Array('GET')))
	{
		$GLOBALS['phpgw']->db->query("delete from phpgw_polls_data where vote_id='".$vote_id."'",__LINE__,__FILE__);
		Header('Location: ' . $GLOBALS['phpgw']->link('/polls/admin.php', array('show' => 'answers')));
	}
	else
	{
		echo '<p><br><table border="0" width="40%" align="center"><tr><td align="center" colspan="center">';
		echo lang('Are you sure want to delete this answer ?') . '</td></tr>';
		echo '<tr><td align="left"><a href="' . $GLOBALS['phpgw']->link('/polls/admin.php', array('show' => 'answers')) . '">' . lang('No') . '</td>';
		echo '    <td align="right"><a href="' . $GLOBALS['phpgw']->link('/polls/admin_deleteanswer.php','vote_id='
			.$vote_id.'&confirm=True') . '">' . lang('Yes') . '</td></tr>';
		echo '</table>';
	}
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
