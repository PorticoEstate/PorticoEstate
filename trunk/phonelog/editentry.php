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

	/* $Id: editentry.php 8257 2001-11-13 03:53:16Z milosch $ */

	if(empty($querytype) || !$querytype)
	{
		$querytype = "NEW";
	}
	if(($querytype=="SELECT" || $querytype=="UPDATE") && !$callid)
	{
		die("No callid supplied while require for this type of query : $querytype. Session halted.");
	}
	if ($querytype=="INSERT" || $querytype=="UPDATE")
	{
		$GLOBALS['phpgw_info']["flags"] = array("noheader" => True, "nonavbar" => True);
	}

	$GLOBALS['phpgw_info']["flags"]["currentapp"] = "phonelog";
	include("../header.inc.php");

	// PREPARE LISTS
	if(is_array($phonelog["entry_status"]))
	{
		for($i=1;$i<=max(array_keys($phonelog["entry_status"]));$i++)
		{
			$callstatus_list[] = array($i, $phonelog["entry_status"][$i]);
		}
	}

	$calldate_month_list[] = array(1, lang("january"));
	$calldate_month_list[] = array(2, lang("february"));
	$calldate_month_list[] = array(3, lang("march"));
	$calldate_month_list[] = array(4, lang("may"));
	$calldate_month_list[] = array(5, lang("april"));
	$calldate_month_list[] = array(6, lang("june"));
	$calldate_month_list[] = array(7, lang("july"));
	$calldate_month_list[] = array(8, lang("august"));
	$calldate_month_list[] = array(9, lang("september"));
	$calldate_month_list[] = array(10, lang("october"));
	$calldate_month_list[] = array(11, lang("november"));
	$calldate_month_list[] = array(12, lang("december"));

	$users = $GLOBALS['phpgw']->accounts->get_list("accounts");
	for($i=0;$i<sizeof($users);$i++)
	{
		$callfor_list[$i] = array($users[$i]["account_id"], $users[$i]["account_firstname"] . " " . $users[$i]["account_lastname"]);
	}

	$callfrom_company_all_list[] = array(0 , "------------------");
	$GLOBALS['phpgw']->db->query("SELECT DISTINCT org_name FROM phpgw_addressbook ORDER BY org_name",__LINE__,__FILE__);
	while($GLOBALS['phpgw']->db->next_record())
	{
		list($company_name) = $GLOBALS['phpgw']->db->Record;
		$callfrom_company_all_list[] = array($company_name, $company_name);
	}
	$GLOBALS['phpgw']->db->free();
	$callfrom_person_one_list[] = array(0, "---------------------");

	$callfrom_person_all_list[] = array(0, "---------------------");
	$GLOBALS['phpgw']->db->query("SELECT id, n_given, n_middle, n_family FROM phpgw_addressbook ORDER BY n_family",__LINE__,__FILE__);
	while($GLOBALS['phpgw']->db->next_record())
	{
		list($ab_id, $ab_firstname, $ab_middle, $ab_lastname) = $GLOBALS['phpgw']->db->Record;
		$callfrom_person_all_list[] = array($ab_id, $ab_firstname . " " . $ab_middle ." ".$ab_lastname);
	}
	$GLOBALS['phpgw']->db->free();

	// END PREPARE LISTS

	function prepForDb()
	{
		global $callfrom_type, $callfrom_person_all, $callfrom_person_one;
		global $calldate_year, $calldate_month, $calldate_day, $calldate_hour, $calldate_minute;
		global $callfrom_txt, $calldesc_short, $calldesc_long;
		global $tmp_callfrom_id, $tmp_calldate;
		// Handle caller id
		if($callfrom_type==0)
		$tmp_callfrom_id = $callfrom_person_all;
		elseif($callfrom_type==1)
		$tmp_callfrom_id = $callfrom_person_one;
		else
		$tmp_callfrom_id = 0;

		// Format time
		$tmp_calldate = mktime($calldate_hour,$calldate_minute,$calldate_second,$calldate_month,$calldate_day,$calldate_year);
	}

	if ($querytype == "INSERT")
	{
		prepForDb();
		$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_phonelog_entry (pl_callfrom_id, pl_callfrom_txt, pl_callfor, "
		. "pl_calldate, pl_status, pl_desc_short, pl_desc_long) VALUES ($tmp_callfrom_id,"
		. "'$callfrom_txt', '$callfor', '$tmp_calldate', $callstatus, '$calldesc_short',"
		. "'$calldesc_long')",__LINE__,__FILE__);
		Header("Location: " . $GLOBALS['phpgw']->link("/phonelog/index.php"));
	}
	if ($querytype == "UPDATE")
	{
		prepForDb();
		$GLOBALS['phpgw']->db->query("UPDATE phpgw_phonelog_entry SET pl_callfrom_id=$tmp_callfrom_id, pl_callfrom_txt='$callfrom_txt', pl_callfor='$callfor', pl_calldate='$tmp_calldate', pl_status=$callstatus, pl_desc_short='$calldesc_short', pl_desc_long='$calldesc_long' WHERE pl_id=$callid",__LINE__,__FILE__);
		Header("Location: " . $GLOBALS['phpgw']->link("/phonelog/"));
	}

	if ($querytype == "NEW")
	{
		$calldate_month = date("n");
		$calldate_day = date("j");
		$calldate_year = date("Y");
		$calldate_hour = date("G");
		$calldate_minute = date("i");
		$callstatus = 3; // Default to status 3 ('call back') at this moment
		$callfor = 0;
		$callfrom_type = 0;
		$callfrom_txt = "";
		$callfrom_person_all = 0;
		$callfrom_company_one = "";
		$callfrom_company_all = 0;
		$callfrom_person_one = 0;
		$calldesc_short =  "";
		$calldesc_long = "";

		$querytype_new = "INSERT";
	}
	if ($querytype == "CALLFROMSELECTED")
	{
		if($callfrom_type == 0)
		{
			$callfrom_company_all = 0;
			$callfrom_person_one = 0;
			$GLOBALS['phpgw']->db->query("SELECT org_name FROM phpgw_addressbook WHERE id=".$callfrom_person_all,__LINE__,__FILE__);
			$GLOBALS['phpgw']->db->next_record();
			list($callfrom_company_one) = $GLOBALS['phpgw']->db->Record;
			$GLOBALS['phpgw']->db->free();
		}
		elseif($callfrom_type == 1)
		{
			$callfrom_person_all = 0;
			$callfrom_company_one = "";
			$GLOBALS['phpgw']->db->query("SELECT id, fn FROM phpgw_addressbook WHERE "
			. "org_name='".$callfrom_company_all."' ORDER BY n_family",__LINE__,__FILE__);
			while($GLOBALS['phpgw']->db->next_record())
			{
				list($ab_id, $ab_fullname) = $GLOBALS['phpgw']->db->Record;
				$callfrom_person_one_list[] = array($ab_id, $ab_fullname);
			}
		}
		else
		{
			$callfrom_person_all = 0;
			$callfrom_company_one = "";
			$callfrom_company_all = 0;
			$callfrom_person_one = 0;
		}

		if($callid)
		{
			$querytype_new = "UPDATE";
		}
		else
		{
			$querytype_new = "INSERT";
		}
	}
	if($querytype=="SELECT")
	{
		$GLOBALS['phpgw']->db->query("SELECT pl_callfrom_id, pl_callfrom_txt, pl_callfor, YEAR(pl_calldate), "
		. "MONTH(pl_calldate), DAYOFMONTH(pl_calldate), HOUR(pl_calldate), "
		. "MINUTE(pl_calldate), pl_status, pl_desc_short, pl_desc_long FROM "
		. "phpgw_phonelog_entry WHERE pl_id=$callid",__LINE__,__FILE__);
		$GLOBALS['phpgw']->db->next_record();
		list($callfrom_person_all, $callfrom_txt, $callfor, $calldate_year, $calldate_month, $calldate_day, $calldate_hour, $calldate_minute, $callstatus, $calldesc_short, $calldesc_long) = $GLOBALS['phpgw']->db->Record;
		$GLOBALS['phpgw']->db->free();
		// Set callfrom
		$callfrom_person_one = 1;
		$callfrom_company_all = 0;
		if($callfrom_person_all)
		{
			$callfrom_type = 0;
			if($GLOBALS['phpgw_info']["apps"]["timetrack"]["enabled"])
			{
				$GLOBALS['phpgw']->db->query("SELECT company_name FROM addressbook, customers WHERE company_id=ab_company_id AND ab_id=".$callfrom_person_all,__LINE__,__FILE__);
			}
			else
			{
				$GLOBALS['phpgw']->db->query("SELECT ab_company FROM addressbook WHERE ab_id=".$callfrom_person_all,__LINE__,__FILE__);
			}
			$GLOBALS['phpgw']->db->next_record();
			list($callfrom_company_one) = $GLOBALS['phpgw']->db->Record;
			$GLOBALS['phpgw']->db->free();
		}
		else
		{
			$callfrom_type = 2;
			$callfrom_company_one = "";
			$callfrom_person_all = 0;
		}
		$querytype_new="UPDATE";
	}

	$lang_phonelog_action = lang("phone log - add");
	$lang_callstatus = lang("status");
	$lang_calldate = lang("call came in at");
	$lang_callfrom = lang("call from");
	$lang_by_person = lang("by person");
	$lang_by_company = lang("by company");
	$lang_by_txt = lang("as free text");
	$lang_callfor = lang("call for");
	$lang_desclong = lang("description");
	$lang_descshort = lang("summary");
	$lang_addsubmitb = lang("Add");
	$lang_addresetb = lang("Clear Form");
?>

<p>&nbsp; <?php echo $lang_phonelog_action ?><br>
	<hr noshade width="98%" align="center" size="1">

	<script>
	function doShowCustomerDetails(inForm) {
		inForm.querytype.value = "CALLFROMSELECTED";
		inForm.submit();
	}
	</script>
	<center>
<form name="editform" action="<?php echo $GLOBALS['phpgw']->link("/phonelog/editentry.php"); ?>" method="POST">
<input type="hidden" name="callid" value="<?php echo $callid ?>">
<input type="hidden" name="querytype" value="<?php echo $querytype_new ?>">
	<table width="75%" border="0" cellspacing="0" cellpadding="0">
	<tr>
<td rowspan="3" valign=top><?php echo $lang_callfrom ?>:</td>
	<td>
<?php echo $lang_by_person ?> :
	</td>
	<td>
<input type=radio name="callfrom_type" value="0" <?php if(!$callfrom_type) echo "CHECKED" ?>>
	<select name="callfrom_person_all" size="1" onFocus="this.form.callfrom_type[0].checked = true;" onChange="doShowCustomerDetails(this.form);">
<?php echo printSelectList($callfrom_person_all, $callfrom_person_all_list) ?>
	</select>
	</td>
	<td>
<input type=text name="callfrom_company_one" READONLY value="<?php echo $callfrom_company_one ?>">
	</td>
	</tr>
	<tr>
	<td>
<?php echo $lang_by_company ?>
	</td>
	<td>
<input type=radio name="callfrom_type" value="1" <?php if($callfrom_type==1) echo "CHECKED" ?>>
	<select name="callfrom_company_all" size="1" onFocus="this.form.callfrom_type[1].checked = true;" onChange="doShowCustomerDetails(this.form);">
<?php echo printSelectList($callfrom_company_all, $callfrom_company_all_list) ?>
	</select>
	</td>
	<td width="50%">
	<select name="callfrom_person_one">
<?php echo printSelectList($callfrom_person_one, $callfrom_person_one_list) ?>
	</select>
	</td>
	</tr>
	<tr>
	<td>
<?php echo $lang_by_txt ?>
	</td>
	<td colspan="2">
<input type=radio name="callfrom_type" value="2" <?php if($callfrom_type==2) echo "CHECKED" ?>>
<input type="text" value="<?php echo $callfrom_txt ?>" onFocus="this.form.callfrom_type[2].checked = true;" name="callfrom_txt" size="20" maxlength="255">
	</td>
	</tr>
	<tr>
<td><?php echo $lang_callstatus ?>:</td>
	<td colspan="3">
	<select name="callstatus">
<?php echo printSelectList($callstatus, $callstatus_list) ?>
	</select>
	</td>
	</tr>
	<tr>
<td><?php echo $lang_callfor ?>:</td>
	<td colspan="3">
	<select name="callfor">
<?php echo printSelectList($callfor, $callfor_list) ?>
	</select>
	</td>
	</tr>
	<tr>
<td><?php echo $lang_calldate ?>:</td>
	<td colspan="3">
	<?php
		$day   = '<input type="text" name="calldate_day" value="' . $calldate_day . '" size="2" maxlength="2">';
		$month = '<SELECT name="calldate_month" size="1">'
		.  printSelectList($calldate_month, $calldate_month_list)
		. '</SELECT>';
		$year  ='<input type="text" name="calldate_year" value="' . $calldate_year . '" size="4" maxlength="4">';

		echo $GLOBALS['phpgw']->common->dateformatorder($year,$month,$day,True);
	?>
	-
<input type="text" name="calldate_hour" value="<?php echo $calldate_hour ?>" size="2" maxlength="2">:
<input type="text" name="calldate_minute" value="<?php echo $calldate_minute ?>" size="2" maxlength="2">
	</td>
	</tr>
	<tr>
	<td colspan="4">&nbsp;</td>
	</tr>
	<tr>
<td><?php echo $lang_descshort ?>:</td>
<td colspan="3"><input type="text" name="calldesc_short" size="60" maxlength="255" value="<?php echo $calldesc_short ?>"></td>
	</tr>
	<tr>
<td><?php echo $lang_desclong ?>:</td>
<td colspan="3"><textarea name="calldesc_long" cols="50" rows="5"><?php echo $calldesc_long ?></textarea></td>
	</tr>
	</table>
<?php if($querytype_new=="INSERT") : ?>
	<input type="submit" value="Add">
<?php elseif($querytype_new=="UPDATE") : ?>
	<input type="submit" value="Update">
<?php endif ?>
	</form>
	</center>

<?php
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
