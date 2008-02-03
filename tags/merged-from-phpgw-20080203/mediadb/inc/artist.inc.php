<?php
  /**************************************************************************\
  * phpGroupWare - MediaDB Artist Functions                                  *
  * http://www.phpgroupware.org                                              *
  * This file written by Sam Wynn <neotexan@wynnsite.com>                    *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

	function list_artist($order, $sort, $filter, $start, $query, $qfield)
	{
		$search_obj = Array(
			Array(
				'artist_fname',
				'first name'
			),
			Array(
				'artist_lname',
				'last name'
			)
		);
   
		if($order)
		{
			$ordermethod = 'order by '.$order.' '.$sort;
		}
		else
		{
			$ordermethod = 'order by artist_lname asc';
		}

		if(!$sort)
		{
			$sort = 'desc';
		}

		if(!$start)
		{
			$start = 0;
		}

		if(!$qfield)
		{
			$qfield = 'artist_fname';
		}
   
		if(!$filter)
		{
			$filter = 'none';
		}

		if(!$query)
		{
			$GLOBALS['phpgw']->db->query('select count(*) from phpgw_mediadb_artist '.$ordermethod);
		}
		else
		{
			$GLOBALS['phpgw']->db->query('select count(*) from phpgw_mediadb_artist WHERE '.$qfield." like '%".$query."%'".$ordermethod);
		}

		$GLOBALS['phpgw']->db->next_record(); 
		if($GLOBALS['phpgw']->db->f(0) > $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'])
		{
			echo '<center>'.lang('showing %1 - %2 of %3',($start + 1),
                   ($start + $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']),
                   $GLOBALS['phpgw']->db->f(0)).'</center>'."\n";
		}
		else
		{
			echo '<center>'.lang('showing %1',$GLOBALS['phpgw']->db->f(0)).'</center>'."\n";
		}

		echo '<center>'."\n";
   
		$GLOBALS['phpgw']->nextmatchs->show_tpl('/mediadb/artist.php',$start,$GLOBALS['phpgw']->db->f(0),'','75%', $GLOBALS['phpgw_info']['theme']['th_bg'],$search_obj,0);
		echo '</center>'."\n";

		echo '<table border="0" width="75%" align="center">'."\n"
   		.'  <tr>'."\n"
	   	.'    <td colspan="4">&nbsp;</td>'."\n"
   		.'  </tr>'."\n"
   		.'  <tr bgcolor="'.$GLOBALS['phpgw_info']['theme']['th_bg'].'">'."\n"
			.'    <td>'.$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'artist_lname',$order,'artist.php',lang('Last Name')).'</td>'."\n"
   		.'    <td>'.$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'artist_fname',$order,'artist.php',lang('First Name')).'</td>'."\n"
			.'    <td>'.lang('Edit').'</td>'."\n"
			.'    <td>'.lang('Delete').'</td>'."\n"
			.'  </tr>'."\n";

   
		if(!$query)
		{
			$GLOBALS['phpgw']->db->limit_query('select * from phpgw_mediadb_artist '.$ordermethod,$start);
		}
		else
		{
			$GLOBALS['phpgw']->db->limit_query('select * from phpgw_mediadb_artist WHERE '.$qfield." like '%".$query."%' ".$ordermethod,$start);
		}

		while($GLOBALS['phpgw']->db->next_record()) 
		{
			$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);

			$fname = $GLOBALS['phpgw']->db->f('artist_fname');
			if(!$fname)
			{
				$fname = '&nbsp;';
			}

			$lname = $GLOBALS['phpgw']->db->f('artist_lname');
			if (! $lname)
			{
				$lname = '&nbsp;';
			}

			echo '  <tr bgcolor="'.$tr_color.'">'."\n"
				.'    <td>'.$lname.'</td>'."\n"
				.'    <td>'.$fname.'</td>'."\n"
				.'    <td width="5%"><a href="'.$GLOBALS['phpgw']->link('/mediadb/artist.php',
					'con='.urlencode($GLOBALS['phpgw']->db->f('artist_id'))
					.'&act=edit&start='.$start.'&order='.$order.'&filter='.$filter.'&qfield='.$qfield.'&sort='.$sort
					.'&query='.urlencode($query)
				).'">'.lang('Edit').'</a></td>'."\n"
				.'    <td width="5%"><a href="'.$GLOBALS['phpgw']->link('/mediadb/artist.php',
					'con='.urlencode($GLOBALS['phpgw']->db->f('artist_id'))
					.'&act=delete&start='.$start.'&order='.$order.'&filter='.$filter.'&qfield='.$qfield.'&sort='.$sort
					.'&query='.urlencode($query)
				).'">'.lang('Delete').'</a></td>'."\n"
				.'  </tr>'."\n";
	   }
   	echo '</table>'."\n";
	}

	function add_artist_entry($order, $sort, $filter, $start, $query, $qfield)
	{
		$color = $GLOBALS['phpgw_info']['theme']['th_bg'];
    
		echo '<form method="POST" action="'.$GLOBALS['phpgw']->link('/mediadb/artist.php',
				'act=add&start='.$start.'&order='.$order.'&filter='.$filter.'&qfield='.$qfield.'&sort='.$sort
				.'&query='.urlencode($query)
			).'">'."\n"
			.'  <table border="0" cellpadding="0" cellspacing="0" width="75%" align="center">'."\n"
			.'    <tr bgcolor="'.$color.'">'."\n"
			.'      <td align="left" width="15%">'."\n"
			.'        <input type="submit" name="submit" value="'.lang('Add').'">'."\n"
			.'      </td>'."\n"
			.'      <td width="10%">'.lang('First').':</td>'."\n"
			.'      <td>'."\n"
			.'        <input type="text" name="artist_fname" value="'.$afname.'" maxlength="30">'."\n"
			.'      </td>'."\n"
			.'      <td width="10%">'.lang('Last').':</td>'."\n"
			.'      <td>'."\n"
			.'        <input type="text" name="artist_lname" value="'.$alname.'" maxlength="50">'."\n"
			.'      </td>'."\n"
			.'    </tr>'."\n"
			.'  </table>'."\n"
			.'</form>'."\n";
	}

	function modify_artist_entry($con, $act, $order, $sort, $filter, $start, $query, $qfield)
	{
		$GLOBALS['phpgw']->db->query('select * from phpgw_mediadb_artist where artist_id='.$con,__LINE__,__FILE__);
		$GLOBALS['phpgw']->db->next_record();
		$afname = $GLOBALS['phpgw']->db->f('artist_fname');
		$alname = $GLOBALS['phpgw']->db->f('artist_lname');

		switch($act)
		{
			case 'delete':
				$color = $GLOBALS['phpgw_info']['theme']['bg07'];
				break;
			default:
				$color = $GLOBALS['phpgw_info']['theme']['table_bg'];
				break;
		}
    
		echo '<form method="POST" action="'.$GLOBALS['phpgw']->link('/mediadb/artist.php',
				'act='.$act.'&start='.$start.'&order='.$order.'&filter='.$filter.'&qfield='.$qfield.'&sort='.$sort
				.'&query='.urlencode($query)
			).'">'."\n"
			.'<input type="hidden" name="artist_id" value="'.$con.'">'."\n"
			.'  <table border="0" cellpadding="0" cellspacing="0" width="75" align="center">'."\n"
			.'    <tr bgcolor="'.$color.'">'."\n"
			.'      <td align="left" width="15%">'."\n"
			.'        <input type="submit" name="submit" value="'.lang($act).'">'."\n"
			.'      </td>'."\n"
			.'      <td width="10%">'.lang('First').':</td>'."\n"
			.'      <td>'."\n"
			.'        <input type="text" name="artist_fname" value="'.$afname.'" maxlength="30">'."\n"
			.'      </td>'."\n"
			.'      <td width="10%">'.lang('Last').':</td>'."\n"
			.'      <td>'."\n"
			.'        <input type="text" name="artist_lname" value="'.$alname.'" maxlength="50">'."\n"
			.'      </td>'."\n"
			.'    </tr>'."\n"
			.'  </table>'."\n"
			.'</form>'."\n";
	}
?>
