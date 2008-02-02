<?php
  /**************************************************************************\
  * phpGroupWare - MediaDB Functions                                         *
  * http://www.phpgroupware.org                                              *
  * This file written by Sam Wynn <neotexan@wynnsite.com>                    *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */
require("inc/decoder.inc.php");

function media_rating($rating)
{
   global $phpgw_info;

   while ($rating > 10)
   {
       $rating -= 10.0;
   }
        
   if ($rating > 5)
   {
       $rating = ($rating / 2.0) * 10.0;
   }
   else
   {
       $rating *= 10;
   }

   $usehalf = $rating % 10;
   
   for ($loop = 0; $loop < 5; $loop++)
   {
       if ($loop < (int)($rating / 10))
       {
           echo '<img src="'.$GLOBALS['phpgw']->common->image('mediadb','redstar').'">';
       }
       else
       {
           if ($usehalf != 0)
           {
					echo '<img src="'.$GLOBALS['phpgw']->common->image('mediadb','halfstar').'">';
               $usehalf = 0;
           }
           else
           {
					echo '<img src="'.$GLOBALS['phpgw']->common->image('mediadb','blackstar').'">';
               $usehalf = 0;
           }
       }
   }
}

function page_color($field)
{
   global $phpgw_info, $cat;

   if ($cat == "")
   {
      $cat = "home";
   }

   if ($field == $cat)
   {
      $color = $phpgw_info["theme"]["bg01"];
   }
   else
   {
      $color = $phpgw_info["theme"]["navbar_bg"];
   }
   return $color;
}

function action_color($field, $field2)
{
   global $phpgw_info, $act, $list;

   if (($field == $act) && ($field2 == $list))
   {
         $color = $phpgw_info["theme"]["bg01"];
   }
   else
   {
      $color = $phpgw_info["theme"]["navbar_bg"];
   }
   return $color;
}

function cat_table()
{
   $GLOBALS['phpgw']->db->query('select cat_name from phpgw_mediadb_cat where cat_enabled=1',__LINE__,__FILE__);

   // open the table
   printf("<table border=\"0\" cellpadding=\"1\"" 
          . " cellspacing=\"1\" width=\"95%%\" align=\"center\">\n");
   printf("  <tr bgcolor=\"%s\" align=\"center\">\n",
          $GLOBALS['phpgw_info']['theme']['navbar_bg']);

   // the home column page link
   printf("    <td bgcolor=\"%s\">\n", page_color("home"));
   printf("      <a href=" . $GLOBALS['phpgw']->link('/mediadb/index.php','cat=home')
          . lang('Home') . "</a>\n");
   printf("    </td>\n");

   // create the media type column page links
   while ($GLOBALS['phpgw']->db->next_record()) 
   {
      printf("    <td bgcolor=\"%s\">\n", page_color($GLOBALS['phpgw']->db->f(0)));
      printf("      <a href=" 
             . $GLOBALS['phpgw']->link('/mediadb/index.php','cat='.$GLOBALS['phpgw']->db->f(0)
             . '&act=list&list=new') . $GLOBALS['phpgw']->db->f(0) . '</a>'."\n");
      printf("    </td>\n");
   }

   // close the table
   printf("  </tr>\n");
   printf("</table>\n");
}

function act_table()
{
   global $cat;

   $list_type = Array(
   	'new',
   	'all',
   	'borrowed',
   	'loaned',
   	'requested'
   );

   // the start of the table
   printf("<table border=\"0\" cellpadding=\"1\"" 
          . " cellspacing=\"1\" width=\"95%%\" align=\"center\">\n");

   // begin row
   printf("  <tr bgcolor=\"%s\" align=\"center\">\n",
          $GLOBALS['phpgw_info']['theme']['navbar_bg']);

   // list columns
   $indexlimit = count($list_type);
   for($index=0; $index < $indexlimit; $index++)
   {  
      printf("    <td bgcolor=\"%s\">\n", 
             action_color("list", $list_type[$index]));
      printf("      <a href=\"%s\">%s %s</a>\n", 
             $GLOBALS['phpgw']->link('/mediadb/index.php', 
                          'cat='.$cat.'&act=list&list='.$list_type[$index]),
             lang('list'),
             lang($list_type[$index]));
      printf("    </td>\n");
   }

   // other columns
   printf("    <td bgcolor=\"%s\">\n", 
          action_color("stats", ""));
   printf("      <a href=\"%s\">%s</a>\n", 
          $GLOBALS['phpgw']->link('/mediadb/index.php', 
                       'cat='.$cat.'&act=stats'),
          lang('stats'));
   printf("    </td>\n");


   // close the table and give separation
   printf("  </tr>\n");
   printf("</table>\n");
}

function list_default($cat1, $list)
{
   global $cat, $sort;

   $GLOBALS['phpgw']->db->query("select * from phpgw_mediadb_cat where cat_name='" . $cat1 . "'");
   if ($GLOBALS['phpgw']->db->next_record())
   {
       $column = explode(',', $GLOBALS['phpgw']->db->f('cat_fname'));
   }
   else
   {
       $GLOBALS['phpgw']->db->query("select * from phpgw_mediadb_cat where cat_id='1'");
       $GLOBALS['phpgw']->db->next_record();
       $column = array(
       	'title',
       	'artist',
       	'format',
       	'year',
       	'date',
       	'genre',
       	'rating',
       	'score',
       	'owner',
       	'comments',
       	'imdb',
       	'edit',
       	'avail'
       );
       $sortfields = explode(',', $GLOBALS['phpgw']->db->f('cat_fname'));
   }
   $enable = explode(',', $GLOBALS['phpgw']->db->f('cat_fenabled'));
   $width  = explode(',', $GLOBALS['phpgw']->db->f('cat_fwidth'));
   $sortby = explode(',', $GLOBALS['phpgw']->db->f('cat_fsort'));

   if ($GLOBALS['phpgw']->db->f('cat_id') != '1')
   {
       $GLOBALS['phpgw']->db->query("select * from phpgw_mediadb_cat where cat_id='1'");
       $GLOBALS['phpgw']->db->next_record();
       $sortfields = explode(',', $GLOBALS['phpgw']->db->f('cat_fname'));
   }

   $checksize = 3;

   // the start of the table
   printf("<table border=\"0\" cellpadding=\"1\"" 
          . " cellspacing=\"1\" width=\"95%%\" align=\"center\">\n");

   if (($cat == "") || ($cat == "home"))
   {  
      printf("  <tr bgcolor=\"%s\" align=\"center\">\n",
             $GLOBALS['phpgw_info']['theme']['table_bg']);
      printf("    <td width=\"%d%%\" align=\"left\" colspan=2>%s %s</td>\n",
             $checksize,
             lang("$list"),
             lang("$cat1"));
      printf("  </tr>\n");
   }

   // header row
   printf("  <tr bgcolor=\"%s\" align=\"center\">\n",
          $GLOBALS['phpgw_info']['theme']['th_bg']);

   // the check column
   printf("    <td width=\"3%%\" align=\"center\">\n");
   printf("      &nbsp;\n");
   printf("    </td>\n");

   // linked columns
   $indexlimit = count($column) - 4;
   for($index=0; $index < $indexlimit; $index++)
   {  
      if ($enable[$index] == 1)
      {
          printf("    <td width=\"%d%%\">\n", $width[$index]);

          if ($sortby[$index] == 1)
          {
              printf("      <a href=\"%s\">%s</a>\n", 
                     $GLOBALS['phpgw']->link('/mediadb/index.php', 
                                  'cat='.$cat.'&act=list&list='.$list.'&sort='.$sortfields[$index]),
                     lang($column[$index]));
          }
          else
          {
              printf("%s\n", lang($column[$index]));
          }
          
          printf("    </td>\n");
      }
   }

   // fixed columns
   for($index=$indexlimit; $index < ($indexlimit + 4); $index++)
   {  if ($enable[$index] == 1)
      {
         printf("    <td width=\"%d%%\">%s</td>\n",
                $width[$index],
                lang($column[$index]));
      }
   }
   printf("  </tr>\n");

   // the rest of the table goes here
   include(PHPGW_APP_INC . "/temp/$cat1.inc.php");

   // batch bar goes here (delete...etc)

   // close the table
   printf("</table>\n");
}

function search_table()
{
   global $phpgw, $phpgw_info, $cat;
   global $order, $filter, $act, $list, $sort, $query, $qfield, $start;

   
   $phpgw->db->query("select * from phpgw_mediadb_cat where cat_name='" . $cat . "'");
   if ($phpgw->db->next_record())
   {
       $column = explode(",", $phpgw->db->f("cat_fname"));
   }
   else
   {
       $phpgw->db->query("select * from phpgw_mediadb_cat where cat_id='1'");
       $phpgw->db->next_record();
       $column = array("title", "artist", "format", "year", "date", "genre", 
                       "rating", "score", "owner", "comments", "imdb", "edit", 
                       "avail");
       $sortfields = explode(",", $phpgw->db->f("cat_fname"));
   }
   $enable = explode(",", $phpgw->db->f("cat_fenabled"));

   if ($phpgw->db->f("cat_id") != "1")
   {
       $phpgw->db->query("select * from phpgw_mediadb_cat where cat_id='1'");
       $phpgw->db->next_record();
       $sortfields = explode(",", $phpgw->db->f("cat_fname"));
   }

   $indexcount = count($column) - 4;
   $searchindex = 0;
   
   for ($index = 0; $index < $indexcount; $index++)
   {
       if ($enable[$index] == 1)
       {
           $searchobj[$searchindex][0] = $sortfields[$index];
           $searchobj[$searchindex][1] = $column[$index];
           $searchindex++;
       }
   }
   
   if ($order)
   {
      $ordermethod = "order by $order $sort";
   }
   else
   {
      $ordermethod = "order by cat_id asc";
   }

   if (! $sort)
   {
      $sort = "desc";
   }

   if (! $start)
   {
       $start = 0;
   }

   if (! $filter)
   {
       $filter = "none";
   }

   $limit =$phpgw->db->limit($start);

   if (! $qfield)
   {
       $qfield = "cat_name";
   }

   if (! $query)
   {
       $phpgw->db->query("select count(*) from phpgw_mediadb_cat "
                         .$ordermethod);
   }
   else
   {
       $phpgw->db->query("select count(*) from phpgw_mediadb_cat "
                         ."WHERE $qfield like '%$query%' "
                         .$ordermethod);
   }

   $phpgw->db->next_record();
   if ($phpgw->db->f(0) > 
       $phpgw_info["user"]["preferences"]["common"]["maxmatchs"])
   {
       $retstr = lang("showing %1 - %2 of %3",($start + 1),
                      ($start +
                       $phpgw_info["user"]["preferences"]["common"]["maxmatchs"]),
                      $phpgw->db->f(0));
   }
   else
   {
       $retstr = lang("showing %1",$phpgw->db->f(0));
   }

   printf("<center>\n");

   $phpgw->nextmatchs->show_tpl("index.php",$start,$phpgw->db->f(0), "",
                            "95%", $phpgw_info["theme"]["th_bg"],$searchobj);
   printf("</center>\n");

   return $retstr;
}

function list_loaner($cat1,$list)
{
   global $phpgw, $phpgw_info, $cat;

   if ($list == "borrowed")
   {
       $heading = array("$cat1", "title", "media", "due date", "owner");
   }
   else
   {
       $heading = array("$cat1", "title", "media", "due date", "borrower");
   }
   
   // the start of the table
   printf("<table border=\"0\" cellpadding=\"1\"" 
          . " cellspacing=\"1\" width=\"95%%\" align=\"center\">\n");

   if (($cat == "") || ($cat == "home"))
   {  
      printf("  <tr bgcolor=\"%s\" align=\"center\">\n",
             $phpgw_info["theme"]["table_bg"]);
      printf("    <td width=\"10%%\" align=\"left\">%s %s</td>\n",
             lang("$list"),
             lang("$heading[0]"));
      printf("  </tr>\n");
   }

   // header row
   printf("  <tr bgcolor=\"%s\" align=\"center\">\n",
          $phpgw_info["theme"]["th_bg"]);

   // list columns
   $indexlimit = count($heading);
   for($index=1; $index < $indexlimit; $index++)
   {  
      printf("    <td>\n");
      printf("      <a href=\"%s\">%s</a>\n", 
             $phpgw->link("/mediadb/index.php", 
                          "cat=$cat&act=list&list=$list"),
             lang("$heading[$index]"));
      printf("    </td>\n");
   }
   
   // close the table
   printf("  </tr>\n");
   printf("</table>\n");
}

function list_requests($cat1,$list,$user)
{
   global $phpgw, $phpgw_info, $cat;

   $heading = array("id", "$user", "title", "media");

   switch ($user)
   {
      case "owner":
        $action = array("status", "withdraw");
        break;
      case "borrower":
        $action = array("pend", "honor", "deny");
        break;
   }

   // the start of the table
   printf("<table border=\"0\" cellpadding=\"1\"" 
          . " cellspacing=\"1\" width=\"95%%\" align=\"center\">\n");

   printf("  <tr bgcolor=\"%s\" align=\"center\"><font color=\"%s\">\n",
          $phpgw_info["theme"]["table_bg"],
          $phpgw_info["theme"]["table_text"]);
   printf("    <td width=\"10%%\" align=\"left\" colspan=2>%s %s %s</td>\n",
          lang("$user"),
          lang("$list"),
          lang("$cat1"));
   printf("  </font></tr>\n");

   // header row
   printf("  <tr bgcolor=\"%s\" align=\"center\">\n",
          $phpgw_info["theme"]["th_bg"]);

   // list columns
   $indexlimit = count($heading);
   for($index=0; $index < $indexlimit; $index++)
   {  
      printf("    <td>\n");
      printf("      <a href=\"%s\">%s</a>\n", 
             $phpgw->link("/mediadb/index.php", 
                          "cat=$cat&act=list&list=$list"),
             lang("$heading[$index]"));
      printf("    </td>\n");
   }

   // action columns
   $indexlimit = count($action);
   for ($index=0; $index < $indexlimit; $index++)
   {
      printf("    <td>%s</td>\n",lang($action[$index]));
   }
   
   // close the table
   printf("  </tr>\n");
   printf("</table>\n");
}

function home_body()
{
   $section = Array(
   	'borrowed',
   	'loaned'
   );

   $GLOBALS['phpgw']->db->query("select cat_name from phpgw_mediadb_cat where cat_enabled=1");

   $index = 0;
   while ($GLOBALS['phpgw']->db->next_record()) 
   {
      $category[$index] = $GLOBALS['phpgw']->db->f(0);
      $index++;
   }
   $indexlimit = $index;

   printf("<p>\n");

   for($index=0; $index < $indexlimit; $index++)
   {
      list_requests($category[$index], 'requested', 'borrower');
   }

   $loanerlimit = count($section);
   for($loaner=0; $loaner < $loanerlimit; $loaner++)
   {
      // do a column for each category
      for($index=0; $index < $indexlimit; $index++)
      {
         list_loaner($category[$index], "$section[$loaner]");
      }
   }

   for($index=0; $index < $indexlimit; $index++)
   {
      list_default($category[$index], "new");
   }
}

function list_body($str)
{
   global $cat, $list;
   
   // section and search count
   printf("<center><strong>%s %s</strong> (%s)</center>\n",
          lang($list), lang($cat), $str);
   
   switch($list)
   {
     case 'loaned':
     case 'borrowed':
       list_loaner($cat, $list);
       break;
     case 'requested':
       list_requests($cat, $list, 'borrower');
       list_requests($cat, $list, 'owner');
       break;
     case 'new':
     case 'own':
     case 'others':
     default:
       list_default($cat, $list);
       break;
   }
   
   $link =  $GLOBALS['phpgw']->link('/mediadb/add.php','phase=1&cat='.$cat);
   $title = lang('add').'_'.lang($cat).'_'."Wizard";

   // start the form
   printf("<form method=POST>\n");
   
   // the start of the table
   printf("  <table border=\"0\" cellpadding=\"1\"" 
          . " cellspacing=\"0\" width=\"95%%\" align=\"center\">\n");

   // begin row
   printf("    <tr bgcolor=\"%s\" align=\"center\" valign=\"center\">\n",
          $GLOBALS['phpgw_info']['theme']['navbar_bg']);

   printf("      <td width=\"10%%\" height=\"15\" bgcolor=\"%s\" valign=\"center\">\n", 
          action_color('add', ""));
   printf("        <input type=\"button\" value=\""
          .lang('add')
          ."\" onclick=\"launch_wizard('$link', '$title');\">");
   printf("      </td>\n");
   printf("      <td width=\"90%%\">&nbsp;</td>\n");
   printf("    </tr>\n");
   printf("  </table>\n");

   // end the form
   printf("</form>\n");
   
/*   
   printf("      <a href=\""
          ." launch_wizard('$link','$title');\"\n"
          ."onClick=\"launch_wizard('$link','$title');\">"
          .lang("add")
          ."</a>\n");
*/
}

function list_feature($order, $sort, $filter, $start, $query)
{
   if ($order)
   {
      $ordermethod = "order by $order $sort";
   }
   else
   {
      $ordermethod = "order by feature_id asc";
   }

   if (! $sort)
   {
      $sort = "desc";
   }

   printf("<p>\n");
   printf("<table border=\"0\" width=\"65%%\" align=\"center\">\n");
   printf("  <tr bgcolor=\"%s\">\n", $GLOBALS['phpgw_info']['theme']['bg_color']);
   printf("    <td align=\"center\" colspan=5><b>%s</b></td>\n", lang('Features'));
   printf("  </tr>\n");
   printf("  <tr>\n");
   printf("    <td colspan=5>&nbsp;</td>\n");
   printf("  </tr>\n");
   printf("  <tr bgcolor=\"%s\">\n", $phpgw_info['theme']['th_bg']);
   printf("    <td>%s</td>\n",
          $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,"feature_id",$order,"feature.php",
                                              lang('ID')));
   printf("    <td>%s</td>\n",
          $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,"feature_type",$order,"feature.php",
                                              lang('Type')));
   printf("    <td>%s</td>\n",
          $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,"feature_desc",$order,"feature.php",
                                              lang('Description')));
   printf("    <td>%s</td>\n", lang('Edit'));
   printf("    <td>%s</td>\n", lang('Delete'));
   printf("  </tr>\n");

   $GLOBALS['phpgw']->db->query("select * from phpgw_mediadb_feature $ordermethod");

   while ($GLOBALS['phpgw']->db->next_record()) 
   {
      $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);

      $name = $GLOBALS['phpgw']->db->f('feature_type');
      if (! $name)
      {
         $name = '&nbsp;';
      }

      $desc = $GLOBALS['phpgw']->db->f('feature_desc');
      if (! $desc)
      {
         $desc = '&nbsp;';
      }

      printf("  <tr bgcolor=$tr_color>\n");
      printf("    <td>%s</td>\n", lang($name));
      printf("    <td>%s</td>\n", lang($desc));
      printf("    <td width=5%%><a href=\"%s\">%s</a></td>\n",
             $GLOBALS['phpgw']->link('/mediadb/feature.php',
                          'con=' . urlencode($GLOBALS['phpgw']->db->f('feature_id')) . '&act=edit'),
             lang('Edit'));
      if ($GLOBALS['phpgw']->db->f('cat_id') != '1')
      {
         printf("    <td width=5%%><a href=\"%s\">%s</a></td>\n",
                $GLOBALS['phpgw']->link('/mediadb/feature.php',
                             'con=' . urlencode($GLOBALS['phpgw']->db->f('feature_id')) . '&act=delete'),
                lang('Delete'));
      }
      else
      {
         printf("    <td width=5%%>&nbsp;</td>\n");
      }

      printf("  </tr>\n");
   }
   printf("</table>\n");
   
   printf("<form method=POST action=\"%s\">\n",$GLOBALS['phpgw']->link('/mediadb/feature.php','act=add'));
   printf("  <table border=0 width=65%% align=center>\n");
   printf("    <tr><td align=left><input type=\"submit\" value=\"%s\"></td></tr>\n",lang('Add'));
   printf("  </table>\n");
   printf("</form>\n");
}

function list_catid($listof, $order, $sort, $filter, $start, $query, $qfield)
{
   global $phpgw, $phpgw_info;

   $searchobj = array(array($listof."_desc", "Media $listof"),
	              array(cat_name, "Media Category"));

   if ($order)
   {
      $ordermethod = "order by $order $sort";
   }
   else
   {
      $ordermethod = "order by phpgw_mediadb_".$listof.".cat_id asc";
   }

   if (! $sort)
   {
      $sort = "desc";
   }

   if (! $start)
   {
       $start = 0;
   }

   if (! $filter)
   {
       $filter = "none";
   }

   $limit =$phpgw->db->limit($start);
   
   if (!$qfield)
   {
       $qfield = $listof."_desc";
   }
   
   if (!$query)
   {

       $phpgw->db->query("select count(*) from phpgw_mediadb_$listof "
                         ."left join phpgw_mediadb_cat on "
                         ."phpgw_mediadb_$listof.cat_id = phpgw_mediadb_cat.cat_id "
                         .$ordermethod);
   }
   else
   {
       $phpgw->db->query("select count(*) from phpgw_mediadb_$listof "
                         ."left join phpgw_mediadb_cat on "
                         ."phpgw_mediadb_$listof.cat_id = phpgw_mediadb_cat.cat_id "
                         ."WHERE $qfield like '%$query%' "
                         .$ordermethod);
   }

   $phpgw->db->next_record(); 
   if ($phpgw->db->f(0) > $phpgw_info["user"]["preferences"]["common"]["maxmatchs"])
   {
       printf("<center>%s</center>\n",
              lang("showing %1 - %2 of %3",($start + 1),
                   ($start + $phpgw_info["user"]["preferences"]["common"]["maxmatchs"]),
                   $phpgw->db->f(0)));
   }
   else
   {
       printf("<center>%s</center>\n",
               lang("showing %1",$phpgw->db->f(0)));
   }

   printf("<center>\n");
   
   $phpgw->nextmatchs->show_tpl($listof.".php",$start,$phpgw->db->f(0), "",
                            "75%", $phpgw_info["theme"]["th_bg"],$searchobj,0);
   printf("</center>\n");

   printf("<table border=\"0\" width=\"75%%\" align=\"center\">\n");
   printf("  <tr>\n");
   printf("    <td colspan=4>&nbsp;</td>\n");
   printf("  </tr>\n");
   printf("  <tr bgcolor=\"%s\">\n", $phpgw_info["theme"]["th_bg"]);
   printf("    <td>%s</td>\n",
          $phpgw->nextmatchs->show_sort_order($sort,$listof."_desc",$order,$listof.".php",
                                              lang("media ".$listof)));
   printf("    <td>%s</td>\n",
          $phpgw->nextmatchs->show_sort_order($sort,"phpgw_mediadb_".$listof.".cat_id",$order,$listof.".php",
                                              lang("media category")));
   printf("    <td>%s</td>\n", lang("Edit"));
   printf("    <td>%s</td>\n", lang("Delete"));
   printf("  </tr>\n");

   if (!$query)
   {

       $phpgw->db->query("select *, cat_name from phpgw_mediadb_$listof "
                         ."left join phpgw_mediadb_cat on "
                         ."phpgw_mediadb_$listof.cat_id = phpgw_mediadb_cat.cat_id "
                         .$ordermethod
                         ." ".$limit);
   }
   else
   {
       $phpgw->db->query("select *, cat_name from phpgw_mediadb_$listof "
                         ."left join phpgw_mediadb_cat on "
                         ."phpgw_mediadb_$listof.cat_id = phpgw_mediadb_cat.cat_id "
                         ."WHERE $qfield like '%$query%' "
                         .$ordermethod
                         ." ".$limit);
   }

   while ($phpgw->db->next_record()) 
   {
      $tr_color = $phpgw->nextmatchs->alternate_row_color($tr_color);

      $name = $phpgw->db->f($listof."_desc");
      if (! $name)
      {
         $name = "&nbsp;";
      }
 
      $category = $phpgw->db->f("cat_name");
      if (! $category)
      {
         $category = "&nbsp;";
      }

      printf("  <tr bgcolor=$tr_color>\n");
      printf("    <td>%s</td>\n", lang($name));
      printf("    <td>%s</td>\n", lang($category));
      printf("    <td width=5%%><a href=\"%s\">%s</a></td>\n",
             $phpgw->link('/mediadb/' . $listof.".php",
                          "con=" . urlencode($phpgw->db->f($listof."_id"))
                          ."&act=edit"
                          ."&start=$start&order=$order&filter=$filter"
                          ."&sort=$sort"
                          ."&query=".urlencode($query)),
             lang("Edit"));

      printf("    <td width=5%%><a href=\"%s\">%s</a></td>\n",
             $phpgw->link('/mediadb/' . $listof.".php",
                          "con=" . urlencode($phpgw->db->f($listof."_id"))
                          ."&act=delete"
                          ."&start=$start&order=$order&filter=$filter"
                          ."&sort=$sort"
                          ."&query=".urlencode($query)),
             lang("Delete"));

      printf("  </tr>\n");
   }
   printf("</table>\n");
}

function populate_table()
{
   global $phpgw, $phpgw_info, $cat, $list, $sort;

   if ($sort == "")
   {
      $sort = "media_title";
   }

   $phpgw->db->query("select dvd.iDVDId, user.vchEmail, d
vd.vchDVDTitle, dvd.dtDVDDate, dvd.vchComments, dvd.irating, user.vchFirstname,
loan.iLoanId, genre.vchGenreDesc, dvd.iRegion from dvd,user left join loan on dv
d.iDVDId = loan.iDVDId left join genre on dvd.iGenreID = genre.iGenreID where dv
d.iUserId = user.iUserId order by '$sortby'");

}

function add_media_phase1($cat)
{
    global $phpgw;
    
    printf("<form method=POST action=\"%s\">\n",
           $phpgw->link("/mediadb/add.php",
                        "phase=2&cat=$cat"));

    
    printf("  <table border=\"0\" cellpadding=\"0\""
           . " cellspacing=\"0\" width=75%% align=center>\n");
    printf("    <tr>\n");
    printf("      <td align=left width=15%%>\n");
    printf("        <input type=\"submit\" name=\"submit\" value=\"%s\">\n",
           lang("Bar Code"));
    printf("      </td>\n");
    printf("      <td>\n");
    printf("        <input type=\"text\" name=\"raw\" maxlength=128>\n");
    printf("      </td>\n");
    printf("    </tr>\n");
    printf("  </table>\n");
    printf("</form>\n");
    printf("");

    // close button
    printf("<form method=POST>\n");
    printf("  <table border=\"0\" cellpadding=\"0\""
           . " cellspacing=\"0\" width=75%% align=center>\n");
    printf("    <tr>\n");
    printf("      <td align=center>\n");
    printf("        <input type=\"button\" value=\""
           .lang("Close")
           ."\" onclick=\"window.close();\">");
    printf("      </td>\n");
    printf("    </tr>\n");
    printf("  </table>\n");
    printf("</form>\n");
}

function add_media_phase2($raw)
{
    $decoded     = cue_decode($raw);
    $interpreted = cue_interpret($decoded);

    $links       = cue_links($interpreted["isbn"]);
    printf("TYPE:    ".$decoded["type"]."<br>");
    printf("SERIAL:  ".$decoded["serial"]."<br>");
    printf("BARCODE: ".$decoded["barcode"]."<br>");
    printf("ISBN:    ".$interpreted["isbn"]."<br>");

    if (isset($interpreted["link"]))
    {
        printf("<a href=".$interpreted["link"]." target=_top>UPC</a>");
    }
    else
    {
        printf("<a href=".$links["Amazon"]." target=_top>Amazon</a>");
    }
    
    // close button
    printf("<form method=POST>\n");
    printf("  <table border=\"0\" cellpadding=\"0\""
           . " cellspacing=\"0\" width=75%% align=center>\n");
    printf("    <tr>\n");
    printf("      <td align=center>\n");
    printf("        <input type=\"button\" value=\""
           .lang("Close")
           ."\" onclick=\"window.close();\">");
    printf("      </td>\n");
    printf("    </tr>\n");
    printf("  </table>\n");
    printf("</form>\n");
}


?>
