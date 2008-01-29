<?php
  /**************************************************************************\
  * phpGroupWare - MediaDB                                                   *
  * http://www.phpgroupware.org                                              *
  * This file written by Sam Wynn <neotexan@wynnsite.com>                    *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: index.php 9331 2002-01-27 13:53:15Z skeeter $ */
  
  $phpgw_info['flags'] = array('currentapp' => 'mediadb',
                               'enable_nextmatchs_class' => True);

  include('../header.inc.php');
?>

  <script>
    hrefloc=this.location;

    self.name="wizard_window";
    function launch_wizard(link, title)
    {
        WizWindow=window.open(link, title, "width=500,height=300,toolbar=no,scrollbars=yes,resizable=yes");
    }
  </script>


<center><h2><?php echo lang('Media Database');?></h2></center>
<p>
<?php

   // page navigation table
   cat_table();

   // functional operations only
   if (($cat != '') && ($cat != 'home'))
   {
       // action table
       act_table();

       // filter and search navigation table
       $str = search_table();

       // body
       switch ($act)
       {
         // various forms of listing the data
         case 'list':
           list_body($str);
           break;
         // addition
         case 'add':
           break;
         // modify
         case 'edit':
           break;
         // deletion
         case 'delete':
           break;
         // varied statistics
         case 'stats':
           break;
         // potential borrower requests loan
         case 'requests':
           break;
         // owner checks in from borrower
         case 'checkin':
           break;
         // owner checks out to borrower
         case 'checkout':
           break;
         // borrower wants longer
         case 'longer':
           break;
       }
   }
   else
   {
       home_body();
   }

    $GLOBALS['phpgw']->common->phpgw_footer();
?>
