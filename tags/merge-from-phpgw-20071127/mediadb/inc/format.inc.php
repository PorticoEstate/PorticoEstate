<?php
  /**************************************************************************\
  * phpGroupWare - MediaDB Format Functions                                  *
  * http://www.phpgroupware.org                                              *
  * This file written by Sam Wynn <neotexan@wynnsite.com>                    *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: format.inc.php 15848 2005-04-18 08:38:02Z powerstat $ */

function add_format_entry($order, $sort, $filter, $start, $query, $qfield)
{
    $color = $GLOBALS['phpgw_info']['theme']['th_bg'];
    
    printf("<form method=POST action=\"%s\">\n",
           $GLOBALS['phpgw']->link('/mediadb/format.php',
                        'act=add'
                        .'&start='.$start.'&order='.$order.'&filter='.$filter
                        .'&sort='.$sort
                        .'&query='.urlencode($query)
                        .'&qfield='.$qfield)
           );
    printf("  <table border=\"0\" cellpadding=\"0\""
           . " cellspacing=\"0\" width=90%% align=center>\n");
    printf("    <tr bgcolor=%s>\n", $color);
    printf("      <td align=left width=15%%>\n");
    printf("        <input type=\"submit\" name=\"submit\" value=\"%s\">\n",lang('Add'));
    printf("      </td>\n");
    printf("      <td align=right width=10%%>%s:</td>\n",lang('Desc'));
    printf("      <td align=left>\n");
    printf("        <input type=\"text\" name=\"format_desc\""
           ." value=\"%s\" maxlength=30>\n", $fdesc);
    printf("      </td>\n");
    printf("      <td align=center width=10%%>%s<br>\n",lang('Files'));
    printf("        <input type=\"checkbox\" name=\"format_efiles\" value=\"1\">\n");
    printf("      </td>\n");
    printf("      <td align=center width=10%%>%s<br>\n",lang('Pages'));
    printf("        <input type=\"checkbox\" name=\"format_pages\" value=\"1\">\n");
    printf("      </td>\n");
    printf("      <td align=center width=10%%>%s<br>\n",lang('Regions'));
    printf("        <input type=\"checkbox\" name=\"format_regions\" value=\"1\">\n");
    printf("      </td>\n");
    printf("      <td align=center width=10%%>%s<br>\n",lang('Scores'));
    printf("        <input type=\"checkbox\" name=\"format_hscores\" value=\"1\">\n");
    printf("      </td>\n");

    printf("      <td align=center>\n");
    printf("        <select name=\"cat_id\" size=1>\n");
    
    $GLOBALS['phpgw']->db->query('select * from phpgw_mediadb_cat where cat_id > 1');
    while ($GLOBALS['phpgw']->db->next_record())
    {
        printf("          <option value=\"%s\"", $GLOBALS['phpgw']->db->f('cat_id'));
        
        printf(">%s</option>\n", lang($GLOBALS['phpgw']->db->f('cat_name')));
    }
    
    printf("        </select>\n");
    printf("      </td>\n");

    printf("    </tr>\n");
    printf("  </table>\n");
    printf("</form>\n");
}

function modify_format_entry($con, $act, $order, $sort, $filter, $start, $query, $qfield)
{
    $GLOBALS['phpgw']->db->query('select * from phpgw_mediadb_format where format_id='.$con);
    $GLOBALS['phpgw']->db->next_record();

    $fdesc  = $GLOBALS['phpgw']->db->f('format_desc');
    $cat    = $GLOBALS['phpgw']->db->f('cat_id');
    
    if ($GLOBALS['phpgw']->db->f('format_efiles'))
    {
        $fchecked = 'checked';
    }
    if ($GLOBALS['phpgw']->db->f('format_pages'))
    {
        $pchecked = 'checked';
    }
    if ($GLOBALS['phpgw']->db->f('format_regions'))
    {
        $rchecked = 'checked';
    }
    if ($GLOBALS['phpgw']->db->f('format_hscores'))
    {
        $schecked = 'checked';
    }
    
    switch($act)
    {
      case 'delete':
        $color = $GLOBALS['phpgw_info']['theme']['bg07'];
        break;
      default:
        $color = $GLOBALS['phpgw_info']['theme']['table_bg'];
        break;
    }
    
    printf("<form method=POST action=\"%s\">\n",
           $GLOBALS['phpgw']->link('/mediadb/format.php',
                        'act='.$act
                        .'&start='.$start.'&order='.$order.'&filter='.$filter
                        .'&sort='.$sort
                        .'&query='.urlencode($query)
                        .'&qfield='.$qfield)
           );
    printf("<input type=\"hidden\" name=\"format_id\" value=\"%s\">\n",$con);
    printf("  <table border=\"0\" cellpadding=\"0\""
           . " cellspacing=\"0\" width=90%% align=center>\n");
    printf("    <tr bgcolor=%s>\n",$color);
    printf("      <td align=left width=15%%>\n");
    printf("        <input type=\"submit\" name=\"submit\" value=\"%s\">\n",lang($act));
    printf("      </td>\n");
    printf("      <td align=right width=10%%>%s:</td>\n",lang('Desc'));
    printf("      <td align=left>\n");
    printf("        <input type=\"text\" name=\"format_desc\""
           ." value=\"%s\" maxlength=30>\n", $fdesc);
    printf("      </td>\n");
    printf("      <td align=center width=10%%>%s<br>\n",lang('Files'));
    printf("        <input type=\"checkbox\" %s name=\"format_efiles\" value=\"1\">\n",
           $fchecked);
    printf("      </td>\n");
    printf("      <td align=center width=10%%>%s<br>\n",lang('Pages'));
    printf("        <input type=\"checkbox\" %s name=\"format_pages\" value=\"1\">\n",
           $pchecked);
    printf("      </td>\n");
    printf("      <td align=center width=10%%>%s<br>\n",lang('Regions'));
    printf("        <input type=\"checkbox\" %s name=\"format_regions\" value=\"1\">\n",
           $rchecked);
    printf("      </td>\n");
    printf("      <td align=center width=10%%>%s<br>\n",lang('Scores'));
    printf("        <input type=\"checkbox\" %s name=\"format_hscores\" value=\"1\">\n",
           $schecked);
    printf("      </td>\n");
    printf("      <td align=center>\n");
    printf("        <select name=\"cat_id\" size=1>\n");

    $GLOBALS['phpgw']->db->query('select * from phpgw_mediadb_cat where cat_id > 1');
    while ($GLOBALS['phpgw']->db->next_record())
    {
        printf("          <option value=\"%s\"", $GLOBALS['phpgw']->db->f('cat_id'));

        if ($cat == $GLOBALS['phpgw']->db->f('cat_id'))
        {
            printf(' selected');
        }
        
        printf(">%s</option>\n", lang($GLOBALS['phpgw']->db->f('cat_name')));
    }
    
    printf("        </select>\n");
    printf("      </td>\n");
    printf("    </tr>\n");
    printf("  </table>\n");
    printf("</form>\n");
}
?>
