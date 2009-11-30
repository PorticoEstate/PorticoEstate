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

  $GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
  $GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";

  include("../header.inc.php");
	if (! $cid)
	{
     Header("Location: " . $GLOBALS['phpgw']->link("/timetrack/customers.php"));
	}

	inc_cal();
	if ($error)
	{
		echo "<center>" . lang("Error") . ":$error</center>";
	}
	
  $GLOBALS['phpgw']->db->query("select * from phpgw_ttrack_customers where company_id='$cid'");
  $GLOBALS['phpgw']->db->next_record();
  $company_id = $GLOBALS['phpgw']->db->f("company_id");
  $company_name = $GLOBALS['phpgw']->db->f("company_name");
  $website = $GLOBALS['phpgw']->db->f("website");
  $ftpsite = $GLOBALS['phpgw']->db->f("ftpsite");
  $industry_type = $GLOBALS['phpgw']->db->f("industry_type");
  $status = $GLOBALS['phpgw']->db->f("status");
  $software = $GLOBALS['phpgw']->db->f("software");
  $lastjobnum = $GLOBALS['phpgw']->db->f("lastjobnum");
  $lastjobfinished = $GLOBALS['phpgw']->db->f("lastjobfinished");
  $busrelationship = $GLOBALS['phpgw']->db->f("busrelationship");
  $notes = $GLOBALS['phpgw']->db->f("notes");
  $active = $GLOBALS['phpgw']->db->f("active");
  //$ = $GLOBALS['phpgw']->db->f("");
  if ($website == "") $website = "&nbsp;";
  if ($ftpsite == "") $ftpsite = "&nbsp;";
  if ($industry_type == "") $industry_type = "&nbsp;";
  if ($status == "") $status = "&nbsp;";
  if ($software == "") $software = "&nbsp;";
  if ($lastjobnum == "") $lastjobnum = "&nbsp;";
  if ($lastjobfinished == "") $lastjobfinished = "&nbsp;";
  if ($busrelationship == "") $busrelationship = "&nbsp;";
  if ($notes == "") $notes = "&nbsp;";
  //if ($ == "") $ = "&nbsp;";

  ?>
   <center>
   <p><table border=0 width=50%>

    <tr bgcolor="<?php echo $GLOBALS['phpgw_info']["theme"]["th_bg"]; ?>">
     <th colspan="2"align="center"><?php echo $company_name; ?></th>
    </tr>

    <tr bgcolor="<?php echo $GLOBALS['phpgw_info']["theme"]["row_off"]; ?>">
     <td width="40%"><?php echo lang("Web Site"); ?></td>
     <td width="60%"><?php echo '<a href="http://' . $website . '" TARGET="Remote Site">' . $website 
		. '</a>'; ?></td>
    </tr>

    <tr bgcolor="<?php echo $GLOBALS['phpgw_info']["theme"]["row_on"]; ?>">
     <td width="40%"><?php echo lang("FTP Site"); ?></td>
     <td width="60%"><?php echo '<a href="ftp://' . $ftpsite . '" TARGET="Remote Site">' . $ftpsite 
		. '</a>'; ?></td>
    </tr>

    <tr bgcolor="<?php echo $GLOBALS['phpgw_info']["theme"]["row_off"]; ?>">
     <td width="40%"><?php echo lang("Industry Type"); ?></td>
     <td width="60%"><?php echo $industry_type; ?></td>
    </tr>

    <tr bgcolor="<?php echo $GLOBALS['phpgw_info']["theme"]["row_on"]; ?>">
     <td width="40%"><?php echo lang("Status"); ?></td>
     <td width="60%"><?php echo $status; ?></td>
    </tr>

    <tr bgcolor="<?php echo $GLOBALS['phpgw_info']["theme"]["row_off"]; ?>">
     <td width="40%"><?php echo lang("Software"); ?></td>
     <td width="60%"><?php echo $software; ?></td>
    </tr>

    <tr bgcolor="<?php echo $GLOBALS['phpgw_info']["theme"]["row_on"]; ?>">
     <td width="40%"><?php echo lang("Last Job"); ?></td>
     <td width="60%"><?php echo $lastjobnum; ?></td>
    </tr>

    <tr bgcolor="<?php echo $GLOBALS['phpgw_info']["theme"]["row_off"]; ?>">
     <td width="40%"><?php echo lang("Date Finished"); ?></td>
     <td width="60%"><?php echo $lastjobfinished; ?></td>
    </tr>

    <tr bgcolor="<?php echo $GLOBALS['phpgw_info']["theme"]["row_on"]; ?>">
     <td width="40%"><?php echo lang("Relationship"); ?></td>
     <td width="60%"><?php echo $busrelationship; ?></td>
    </tr>

    <tr bgcolor="<?php echo $GLOBALS['phpgw_info']["theme"]["row_off"]; ?>">
     <td width="40%"><?php echo lang("Active Jobs"); ?></td>
     <td width="60%"><?php echo $active; ?></td>
    </tr>

    <tr bgcolor="<?php echo $GLOBALS['phpgw_info']["theme"]["row_on"]; ?>">
     <td width="40%"><?php echo lang("Notes"); ?></td>
     <td width="60%"><?php echo $notes; ?></td>
    </tr>

    <tr bgcolor="<?php echo $GLOBALS['phpgw_info']["theme"]["th_bg"]; ?>">
     <td colspan="2">&nbsp;</td>
    </tr>

    </table>
   </center>

<?php
  // add form button for generating detail reports
  $thismonth = date("n") - 1;
  $thisyear = date("Y");
  echo '<form name="Report" method=POST action="' . $GLOBALS['phpgw']->link("/timetrack/detail_report_bycust.php")
        . '">';
  echo '<input type="hidden" name="company_id" value="' . $company_id . '">';
  echo '<center><table width="65%" border="0">'
        . '<th colspan="4" bgcolor="' . $GLOBALS['phpgw_info']["theme"]["th_bg"] . '">'
        . lang("Activity Report") . '</th>'
        . '<tr>'; //<td width="20%"><input type="submit" value="Generate"</td>';

  echo '<td width="40%">&nbsp;' . lang("Start Date") . ':';
  // Set the beginning date to automatically be the same as the quote date here.
  $yr=strval(substr($n_quote_date,0,4));
  $mo=strval(substr($n_quote_date,5,2));
  $da=strval(substr($n_quote_date,8,2));
  CalDateSelector("Report","startdate",0,"",$mo,$da,$yr);
  echo '</td>';

  echo '<td width="40%">&nbsp;' . lang("End Date") . ':'; 
  CalDateSelector("Report","enddate",0,"");
  echo '</td>';

  echo '<td>';
  //cal_layer();
  echo '</td>';

  echo '<td align="center"><input type="submit" value="'
        . lang("Generate") . '"</td></tr>';

  echo '</table></form></center>';

  $GLOBALS['phpgw']->common->phpgw_footer();
?>
