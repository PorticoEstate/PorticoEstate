<?php
	/**************************************************************************\
	* phpGroupWare Application - phonelog                                      *
	* http://www.phpgroupware.org                                              *
	* Written by Mathieu van Loon <mathieu@playcollective.com>                 *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: index.php 10108 2002-04-30 01:09:13Z skeeter $ */

	$GLOBALS['phpgw_info']["flags"]["currentapp"] = "phonelog";
	include("../header.inc.php");
?>

<?php
// CHECK & INIT STUFF
	$filter_callfor = get_var('filter_callfor',Array('DEFAULT','POST'),$GLOBALS['phpgw']->accounts->name2id($GLOBALS['phpgw_info']['user']['userid']));
	$filter_callstatus = intval(get_var('filter_callstatus',Array('DEFAULT','POST'),1));
	$orderby = get_var('orderby',Array('DEFAULT','POST'),'callstatus');
	$orderway = get_var('orderway',Array('POST'));

	if($orderway=='DESC')
	{
		$orderway='ASC';
	}
	else
	{
		$orderway='DESC';
	}

	// DEBUG
	$GLOBALS['phpgw']->db->Debug = 0;

	// Build the filters to be added to the WHERE clause of the SELECT query.
	$query_filters = '';
	if($filter_callfor!='-')
	{
		$query_filters .= ' AND account_id='.$filter_callfor;
	}
	if(intval($filter_callstatus)>0)
	{
		$query_filters .= ' AND pl_status='.$filter_callstatus;
	}

	// Build the ORDER BY clause for the SELECT query;
	$query_order = '';
	switch($orderby)
	{
		case 'callfor' :
			$query_order = 'ORDER BY account_lid '.$orderway;
			break;
		case 'callfrom' :
			$query_order = 'ORDER BY pl_callfrom_id '.$orderway;
			break;
		case 'callstatus' :
			$query_order = 'ORDER BY pl_status '.$orderway;
			break;
		case '' :
			break;
	}

	// Execute SELECT query on phonelog_entry table
	$db1 = $GLOBALS['phpgw']->db;
	$db2 = $GLOBALS['phpgw']->db;
	$db1->query("SELECT pl_id, pl_callfrom_id, pl_callfrom_txt, CONCAT(account_firstname, ' ', account_lastname), pl_calldate, pl_status, pl_desc_short FROM phonelog_entry, phpgw_accounts where pl_callfor=account_id ".$query_filters.' '.$query_order,__LINE__,__FILE__);

	$i=0;
	while($db1->next_record())
	{
		list($calls[$i]['callid'], $tmp_callfrom_id, $tmp_callfrom_txt, $calls[$i]['callfor'], $tmp_calldate, $calls[$i]['callstatus'],$calls[$i]['callsubject'] ) = $db1->Record;
		if($tmp_callfrom_id)
		{
			$db2->query('SELECT fn FROM phpgw_addressbook WHERE id='.$tmp_callfrom_id,__LINE__,__FILE__);
			if($db2->next_record())
			{
				list($calls[$i]['callfrom']) = $db2->Record;
			}
			else
			{
				list($calls[$i]['callfrom']) = 'N/A';
			}
			$db2->free();
		}
		else
		{
			$calls[$i]['callfrom'] = $tmp_callfrom_txt;
		}
 
//		$calls[$i]['calldate'] = $GLOBALS['phpgw']->common->show_date($tmp_calldate);
		$calls[$i]['calldate'] = $tmp_calldate;

		$i++;
	}
	$db1->free();

	// PREPARE SELECT LISTS
	$filter_callfor_list[] = array('-', lang('All'));
	$users = $GLOBALS['phpgw']->accounts->get_list('accounts');
	for ($i=0;$i<sizeof($users);$i++)
	{
		if(empty($users[$i]['account_id']))
		{
			$GLOBALS['phpgw']->db->query('SELECT account_id FROM phpgw_accounts WHERE account_lid='.$users[$i]['account_lid'],__LINE__,__FILE__);
			$GLOBALS['phpgw']->db->next_record();
			list($users[$i]['account_id']) = $GLOBALS['phpgw']->db->Record;
		}
		$filter_callfor_list[] = array($users[$i]['account_id'],$users[$i]['account_lid']);
	}

	for($i=0;$i<sizeof($phonelog['entry_status']);$i++)
	{
		$filter_callstatus_list[] = array($i, $phonelog['entry_status'][$i]);
	}
?>
<br>
<br>
<script>
function doAdd() {
  window.location="<?php echo $GLOBALS['phpgw']->link('/phonelog/editentry.php') ?>";
}
</script>
<form name="selectform" method="post" action="<?php echo $GLOBALS['phpgw']->link('/phonelog/index.php'); ?>">
<input type="hidden" name="orderby" value="">
<center>
<table border="0" align="center" bgcolor="486591" width="75%" cellpadding="0" cellspacing="0">
  <tr bgcolor="e6e6e6">
    <td>
      <select name="filter_callfor">
        <?php echo printSelectList($filter_callfor, $filter_callfor_list) ?>
      </select>
    </td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>
      <select name="filter_callstatus">
        <?php echo printSelectList($filter_callstatus, $filter_callstatus_list) ?>
      </select>
    </td>
    <td>&nbsp;</td>
    <td><input type="submit" value="<?php echo lang('filter'); ?>"></td>
  </tr>
  <tr bgcolor="ffffff">
    <td colspan="6">&nbsp;</td>
  </tr>
  <tr bgcolor="486591">
    <td align="left">&nbsp;<a href="<?php echo $GLOBALS['phpgw']->link('/phonelog/index.php',"filter_callfor=$filter_callfor&filter_callstatus=$filter_callstatus&orderway=$orderway&orderby=callfrom") ?>"><font color="fefefe"><?php echo lang('call for') ?></font></a></td>
    <td align="left"><a href="<?php echo $GLOBALS['phpgw']->link('/phonelog/index.php',"filter_callfor=$filter_callfor&filter_callstatus=$filter_callstatus&orderway=$orderway&orderby=callfrom") ?>"><font color="fefefe"><?php echo lang('call from') ?></font></a></td>
    <td align="left"><a href="<?php echo $GLOBALS['phpgw']->link('/phonelog/index.php',"filter_callfor=$filter_callfor&filter_callstatus=$filter_callstatus&orderway=$orderway&orderby=callsubject") ?>"><font color="fefefe"><?php echo lang('subject') ?></font></a></td>
    <td align="left"><a href="<?php echo $GLOBALS['phpgw']->link('/phonelog/index.php',"filter_callfor=$filter_callfor&filter_callstatus=$filter_callstatus&orderway=$orderway&orderby=callstatus") ?>"><font color="fefefe"><?php echo lang('status') ?></font></a></td>
    <td align="left"><a href="<?php echo $GLOBALS['phpgw']->link('/phonelog/index.php',"filter_callfor=$filter_callfor&filter_callstatus=$filter_callstatus&orderway=$orderway&orderby=calldate") ?>"><font color="fefefe"><?php echo lang('date called') ?></font></a></td>
    <td>&nbsp;</td>
  </tr>

    <?php for($i=0;$i<sizeof($calls);$i++) { ?>
      <tr bgcolor="e6e6e6">
        <td align="left"><font color="000000">&nbsp;<?php echo $calls[$i]['callfor'] ?></font></td>
        <td align="left"><font color="000000"><?php echo $calls[$i]['callfrom'] ?></font></td>
        <td align="left"><font color="000000"><?php echo $calls[$i]['callsubject'] ?></font></td>
        <td align="left"><font color="000000"><?php echo $phonelog['entry_status'][$calls[$i]['callstatus']] ?></font></td>
        <td align="left"><font color="000000"><?php echo $calls[$i]['calldate'] ?></font></td>
        <td align="left"><font color="000000"><a href="<?php echo $GLOBALS['phpgw']->link('/phonelog/editentry.php','querytype=SELECT&callid='.$calls[$i]['callid']) ?>"><?php echo lang('edit') ?></a></font></td>
      </tr>
    <?php } ?>
</table>
<button type="button" onClick="doAdd();"><a href="<?php echo $GLOBALS['phpgw']->link('/phonelog/editentry.php'); ?>"><?php echo lang('add'); ?></a></button>
</center>
</form>
<br>
<br>
<br>
<?php $GLOBALS['phpgw']->common->phpgw_footer(); ?>
