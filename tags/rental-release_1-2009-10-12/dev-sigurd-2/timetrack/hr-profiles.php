<?php
  /**************************************************************************\
  * phpgwtimetrack - phpGroupWare addon application                          *
  * http://phpgwtimetrack.sourceforge.net                                    *
  * Written by Robert Schader <bobs@product-des.com>                         *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

  // Update complete for phpgroupware 0.9.10 - 4/16/2001 (api calls for accounts and contacts)

  $GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
  $GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";

  include("../header.inc.php");?>
<p>
<table border="0" width="100%">
 <tr>
  <td align="left" width="50%" valign="top">
   <?php
    echo '<table border="0" width="80%">';
    echo '<tr><td bgcolor="' . $GLOBALS['phpgw_info']["theme"]["th_bg"] . '" align="center">' .
	lang("User Accounts") . '</td></tr>';

     // Use accounts->get_list() here
	$names = $GLOBALS['phpgw']->accounts->get_list('accounts');
	for ($i=0; $i<count($names); $i++) {
       $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
       echo '<tr><td bgcolor="' . $tr_color . '"><a href="'
	  . $GLOBALS['phpgw']->link("/timetrack/hr-profiles.php","user=" . $names[$i]['account_id']) . '">&nbsp;'
	  . get_fullname($names[$i]['account_id']) . '</a></td></tr>';
	}
     echo "</table>";
   ?>
  </td>
  <td align="right" width="50%" valign="top">
   <?php
      if ($group && ! $user) {
        // Page has been passed a group id, show member list
	  $current_groupname = $GLOBALS['phpgw']->accounts->id2name(intval($group));
        echo '<table border="0" width="80%">';
        echo '<tr><td bgcolor="' . $GLOBALS['phpgw_info']["theme"]["th_bg"] . '" align="center">'
	   . lang("Members belonging to group") . ' '
	   . $current_groupname . '</td></tr>';
	  $members = $GLOBALS['phpgw']->accounts->member($group);
	  for ($i=0; $i<count($members); $i++) 
	  {
	    $uid = $members[$i]['account_id'];
          $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
          echo '<tr><td bgcolor="' . $tr_color . '"><a href="'
	     . $GLOBALS['phpgw']->link("/timetrack/hr-profiles.php","user=" . $uid) . '">&nbsp;'
	     . get_fullname($uid) . '</a></td></tr>';
	  }
        echo "</table>";
     }

     if (! $group && $user) {
	$GLOBALS['phpgw']->db->query("select * from phpgw_ttrack_emplyprof where id='"
		       . $user . "'");
        $GLOBALS['phpgw']->db->next_record();

        $profile_comments     = htmlentities($GLOBALS['phpgw']->db->f("comments"));
        $profile_phone_number = htmlentities($GLOBALS['phpgw']->db->f("phone_number"));
        $profile_title        = htmlentities($GLOBALS['phpgw']->db->f("title"));
        $profile_mobilephn    = htmlentities($GLOBALS['phpgw']->db->f("mobilephn"));
        $profile_pager        = htmlentities($GLOBALS['phpgw']->db->f("pager"));
		$hired 	              = $GLOBALS['phpgw']->db->f("hire_date");
		$vacationtime         = $GLOBALS['phpgw']->db->f("yearly_vacation_hours");
		$vac_hours_used       = $GLOBALS['phpgw']->db->f("vacation_hours_used_todate");
		$location             = $GLOBALS['phpgw']->db->f("location_id");

		$n_lid              = $GLOBALS['phpgw']->db->f("lid");

        if (! $GLOBALS['phpgw']->db->f("comments"))
           $profile_comments = "&nbsp;";

        if (! $GLOBALS['phpgw']->db->f("phone_number"))
           $profile_phone_number = "&nbsp;";

        if (! $GLOBALS['phpgw']->db->f("title"))
	   $profile_title = "&nbsp;";

	if (! $GLOBALS['phpgw']->db->f("mobilephn"))
	   $profile_mobilephn = "&nbsp;";

	if (! $GLOBALS['phpgw']->db->f("pager"))
	   $profile_pager = "&nbsp;";

        echo '<table border="0" width="80%">';
        echo '<tr><td colspan="2" bgcolor="' . $GLOBALS['phpgw_info']["theme"]["th_bg"]
	   . '">&nbsp;</td></tr>';

	  $t1_account = CreateObject('phpgwapi.accounts',$user);
	  $t1_userData = $t1_account->read_repository();

        $firstname = $t1_userData['firstname'];
        $lastname  = $t1_userData['lastname'];


        if (! $firstname) $firstname = "&nbsp;";
        if (! $lastname)  $lastname = "&nbsp;";

        $GLOBALS['phpgw']->db->query("SELECT location_name from phpgw_ttrack_locations "
		. "where location_id='$location'");
        $GLOBALS['phpgw']->db->next_record();
        $location_str = $GLOBALS['phpgw']->db->f("location_name");
        // Should not need checking either^

        $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
        echo '<tr><td align="left" bgcolor="' . $tr_color . '" width="50%">'
	   . lang("First name") . ':</td>'
	   . '<td align="right" width="50%" bgcolor="' . $tr_color . '">'
	   . $firstname. '</td></tr>';

        $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
        echo '<tr><td align="left" bgcolor="' . $tr_color . '" width="50%">'
	   . lang("Last name") . ':</td>'
	   . '<td align="right" width="50%" bgcolor="' . $tr_color . '">'
	   . $lastname . '</td></tr>';

        $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
        echo '<tr><td align="left" bgcolor="' . $tr_color . '" width="50%">'
	   . lang("Title") . ':</td>'
	   . '<td align="right" width="50%" bgcolor="' . $tr_color . '">'
	   . $profile_title . '</td></tr>';

        $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
        echo '<tr><td align="left" bgcolor="' . $tr_color . '" width="50%">'
	   . lang("Work Phone") . ':</td>'
	   . '<td align="right" width="50%" bgcolor="' . $tr_color . '">'
	   . $profile_phone_number . '</td></tr>';

        $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
        echo '<tr><td align="left" bgcolor="' . $tr_color . '" width="50%">'
	   . lang("Mobile phone") . ':</td>'
	   . '<td align="right" width="50%" bgcolor="' . $tr_color . '">'
	   . $profile_mobilephn . '</td></tr>';

        $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
        echo '<tr><td align="left" bgcolor="' . $tr_color . '" width="50%">'
	   . lang("Pager") . ':</td>'
	   . '<td align="right" width="50%" bgcolor="' . $tr_color . '">'
	   . $profile_pager . '</td></tr>';

        $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
        echo '<tr><td align="left" bgcolor="' . $tr_color . '" width="50%">'
	   . lang("Comments") . ':</td>'
	   . '<td align="right" width="50%" bgcolor="' . $tr_color . '">'
	   . $profile_comments . '</td></tr>';

        $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
	if (file_exists(PHPGW_SERVER_ROOT . "/timetrack/images/" . $n_lid . ".gif"))
	 {
          echo '<tr><td align="left" bgcolor="' . $tr_color . '" width="50%">&nbsp;</td>'
     	   . '<td width="50%" align="right" bgcolor="' . $tr_color . '">'
     	   . '<img src="' . $GLOBALS['phpgw_info']["server"]["webserver_url"] 
	   . "/timetrack/images/" . $n_lid . ".gif"
     	   . '" width="100" height="120"></td></tr>';
	 }
	 else
	 {
	  echo '<tr><td align="left" bgcolor="' . $tr_color . '" width="50%">&nbsp;</td>'
	   . '<td width="50%" align="right" bgcolor="' . $tr_color . '">'
	   . '<img src="' . $GLOBALS['phpgw_info']["server"]["webserver_url"] 
	   . "/timetrack/images/blank_pic.jpg"
           . '" width="100" height="120"></td></tr>';
	 }
        $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
        echo '<tr><td align="left" bgcolor="' . $tr_color . '" width="50%">'
	   . lang("Date Hired") . ':</td>'
           . '<td align="right" width="50%" bgcolor="' . $tr_color . '">'
           . $hired . '</td></tr>';

        if($GLOBALS['phpgw_info']["apps"]["timetrack"]["ismanager"]) {
          $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
          echo '<tr><td align="left" bgcolor="' . $tr_color . '" width="50%">'
	   . lang("Yearly Vacation Hours") . ':</td>'
             . '<td align="right" width="50%" bgcolor="' . $tr_color . '">'
             . $vacationtime . '</td></tr>';

          $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
          echo '<tr><td align="left" bgcolor="' . $tr_color . '" width="50%">'
	   . lang("Vacation Hours Used") . ':</td>'
             . '<td align="right" width="50%" bgcolor="' . $tr_color . '">'
             . $vac_hours_used . '</td></tr>';
        }

        $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
        echo '<tr><td align="left" bgcolor="' . $tr_color . '" width="50%">'
	   . lang("Location") . ':</td>'
           . '<td align="right" width="50%" bgcolor="' . $tr_color . '">'
           . $location_str . '</td></tr>';

      echo '<tr><td colspan="2">&nbsp;</td></tr>';
      echo '<tr><td colspan="2" align="left" bgcolor="' . $GLOBALS['phpgw_info']["theme"]["th_bg"]
	   . '">' . lang("Member of the following groups") . '</td></tr>';

	$t2_userGroups = $GLOBALS['phpgw']->accounts->membership($user);
	// Iterate though the groups a user belongs to:
	for ($i=0; $i<count($t2_userGroups); $i++) 
	{
	  $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
	  echo "<tr bgcolor=\"$tr_color\"><td colspan=\"2\"><a href=\""
		. $GLOBALS['phpgw']->link("/timetrack/hr-profiles.php","group=" . $t2_userGroups[$i]['account_id']) . "\">"
		. $t2_userGroups[$i]['account_name'] . "</a></td></tr>";
	}

      echo "</table>";
     }

   ?>
  </td>
 </tr>
</table>
<?php
  
$GLOBALS['phpgw']->common->phpgw_footer();
