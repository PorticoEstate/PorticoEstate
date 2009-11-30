<?php
    /**************************************************************************\
    * phpGroupWare - Weather Request Preferences                               *
    * http://www.phpgroupware.org                                              *
    * This file written by Sam Wynn <neotexan@wynnsite.com>                    *
    * --------------------------------------------                             *
    *  This program is free software; you can redistribute it and/or modify it *
    *  under the terms of the GNU General Public License as published by the   *
    *  Free Software Foundation; either version 2 of the License, or (at your  *
    *  option) any later version.                                              *
    \**************************************************************************/

    /* $Id$ */
{
    $GLOBALS['phpgw_info']['flags']['currentapp'] = 'comic';

    include('../header.inc.php');

    if(!$GLOBALS['phpgw_info']['user']['apps']['preferences'])
    {
    	Header('Location: ' . $GLOBALS['phpgw']->link('/home.php') );
    }

    $title             = lang("Daily Comic Preferences");
    $layout_label      = lang("Display Layout");
    $template_label    = lang("Template");
    $option_label      = lang("Display Options");
    $perpage_label     = lang("Comics Per Page");
    $scale_label       = lang("Comics Scaled");
    $frontpage_label   = lang("Front Page Comic");
    $fpscale_label     = lang("Front Page Comic Scaled");
    $censor_label      = lang("Censorship Level");
    $comic_label       = lang("Comics");
    $action_label      = lang("Submit");
    $reset_label       = lang("Reset");
    $done_label        = lang("Done");
    $comic_size        = 8;
    
    $actionurl         = $GLOBALS['phpgw']->link('/comic/preferences.php');
    $doneurl           = $GLOBALS['phpgw']->link('/preferences/index.php') . '#comic';
    $message           = "";
    
    $scale_enabled	= intval($_POST['scale_enabled']);
    $perpage		= intval($_POST['perpage']);
    $frontpage		= intval($_POST['frontpage']);
    $fpscale_enabled	= intval($_POST['fpscale_enabled']);
    $censor_level	= intval($_POST['censor_level']);
    $comic_template	= intval($_POST['comic_template']);
    $comic_id		= intval($_POST['comic_id']);
    
    if( isset($_POST['data_ids']) && count($_POST['data_ids']) )
    {
    		foreach($_POST['data_ids'] as $id)
		{
			$data_ids[] = intval($id);
		}
    }


    
    if ($_POST['submit'])
    {
	$message = lang("Comic Preferences Updated");

        if ($data_ids)
        {
            $data_ids = implode($data_ids,":");
        }
        else
        {
            $data_ids = "";
        }
        
        $sql = 'UPDATE phpgw_comic SET'
		. " comic_list='" . $data_ids . "',"
		. ' comic_scale=' . $scale_enabled . ','
		. ' comic_perpage=' . $perpage . ','
		. ' comic_frontpage=' . $frontpage . ','
		. ' comic_fpscale=' . $fpscale_enabled . ','
		. ' comic_censorlvl=' . $censor_level . ','
		. ' comic_template=' . $comic_template
		. ' WHERE comic_id=' . $comic_id;
	
	$GLOBALS['phpgw']->db->lock('phpgw_comic');
	$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);
        $GLOBALS['phpgw']->db->unlock();
    }

    $GLOBALS['phpgw']->db->query("select * from phpgw_comic "
                      ."WHERE comic_owner='"
                      .$GLOBALS['phpgw_info']["user"]["account_id"]."'");

    if ($GLOBALS['phpgw']->db->num_rows() == 0)
    {
        $GLOBALS['phpgw']->db->query("insert into phpgw_comic (comic_owner) values ".
                          "('".$GLOBALS['phpgw_info']["user"]["account_id"]."')");
        $GLOBALS['phpgw']->db->query("select * from phpgw_comic "
                          ."WHERE comic_owner='"
                          .$GLOBALS['phpgw_info']["user"]["account_id"]."'");
    }

    $GLOBALS['phpgw']->db->next_record();

    $comic_id        = $GLOBALS['phpgw']->db->f("comic_id");
    $data_ids        = explode(":", $GLOBALS['phpgw']->db->f("comic_list"));
    $scale_enabled   = $GLOBALS['phpgw']->db->f("comic_scale");
    $perpage         = $GLOBALS['phpgw']->db->f("comic_perpage");
    $frontpage       = $GLOBALS['phpgw']->db->f("comic_frontpage");
    $fpscale_enabled = $GLOBALS['phpgw']->db->f("comic_fpscale");
    $censor_level    = $GLOBALS['phpgw']->db->f("comic_censorlvl");
    $comic_template  = $GLOBALS['phpgw']->db->f("comic_template");
    
    $indexlimit = count($data_ids);

    if ($scale_enabled == 1)
    {
        $scale_checked = "checked";
    }

    if ($fpscale_enabled == 1)
    {
        $fpscale_checked = "checked";
    }

    template_options($comic_template, $t_options_c, $t_images_c);
    
    $prefs_tpl = $GLOBALS['phpgw']->template;
    $prefs_tpl->set_unknowns("remove");
    $prefs_tpl->set_file(
        array(message   => "message.common.tpl",
              prefs     => "prefs.body.tpl",
              perpage   => "option.common.tpl",
              frontpage => "option.common.tpl",
              comic     => "option.common.tpl",
              censor    => "option.common.tpl"));

    for ($loop = 1; $loop <= 15; $loop++)
    {
        $selected = "";
        if ($loop == $perpage)
        {
            $selected = "selected";
        }
        
        $prefs_tpl->set_var(array(OPTION_SELECTED => $selected,
                                  OPTION_VALUE    => $loop,
                                  OPTION_NAME     => $loop));
        $prefs_tpl->parse(option_list, "perpage", TRUE);
    }
    $perpage_c = $prefs_tpl->get("option_list");

    for ($loop = 0; $loop < count($g_censor_level); $loop++)
    {
        $selected = "";
        if ($censor_level == $loop)
        {
            $selected = "selected";
        }
        
        $prefs_tpl->set_var(array(OPTION_SELECTED => $selected,
                                  OPTION_VALUE    => $loop,
                                  OPTION_NAME     => $g_censor_level[$loop]));
        $prefs_tpl->parse(censor_list, "censor", TRUE);
    }
    $censor_c = $prefs_tpl->get("censor_list");
    
    for ($loop = -1; $loop < 1; $loop++)
    {
        $selected = "";
        if ($loop == $frontpage)
        {
            $selected = "selected";
        }

        switch ($loop)
        {
          case -1:
            $name = "None";
            break;
          case 0:
            $name = "Random";
            break;
        }
        
        $prefs_tpl->set_var(array(OPTION_SELECTED => $selected,
                                  OPTION_VALUE    => $loop,
                                  OPTION_NAME     => lang($name)));
        $prefs_tpl->parse(fpage_list, "frontpage", TRUE);
    }

    $GLOBALS['phpgw']->db->query("select * from phpgw_comic_data "
                      ."where data_enabled='T' order by data_name");

    $index = 0;
    
    asort($data_ids);
    
    while ($GLOBALS['phpgw']->db->next_record())
    {
        $selected = "";
        if ($GLOBALS['phpgw']->db->f("data_id") == $frontpage)
        {
            $selected = "selected";
        }

        $prefs_tpl->set_var
            (array(OPTION_SELECTED => $selected,
                   OPTION_VALUE    => $GLOBALS['phpgw']->db->f("data_id"),
                   OPTION_NAME     => $GLOBALS['phpgw']->db->f("data_title")));
        $prefs_tpl->parse(fpage_list, "frontpage", TRUE);


        $selected = "";
        if ($GLOBALS['phpgw']->db->f("data_id") == $data_ids[$index])
        {
            $index++;
            
            $selected = "selected";
        }

        $name = sprintf("%s - %s",
                        $GLOBALS['phpgw']->db->f("data_resolve"),
                        $GLOBALS['phpgw']->db->f("data_title"));
        
        $prefs_tpl->set_var
            (array(OPTION_SELECTED => $selected,
                   OPTION_VALUE    => $GLOBALS['phpgw']->db->f("data_id"),
                   OPTION_NAME     => $name));
        $prefs_tpl->parse(comic_list, "comic", TRUE);
    }
    
    $frontpage_c = $prefs_tpl->get("fpage_list");
    $comic_c     = $prefs_tpl->get("comic_list");
        
    $prefs_tpl->
        set_var(array
                (messagename      => $message,
                 title            => $title,
                 action_url       => $actionurl,
		 action_label     => $action_label,
                 done_url         => $doneurl,
		 done_label       => $done_label,
		 reset_label      => $reset_label,
                 layout_label     => $layout_label,
                 template_label   => $template_label,
                 option_label     => $option_label,
                 perpage_label    => $perpage_label,
                 scale_label      => $scale_label,
                 scale_checked    => $scale_checked,
                 frontpage_label  => $frontpage_label,
                 fpscale_label    => $fpscale_label,
                 fpscale_checked  => $fpscale_checked,
                 comic_label      => $comic_label,
                 comic_size       => $comic_size,
                 template_options => $t_options_c,
                 template_images  => $t_images_c,
                 perpage_options  => $perpage_c,
                 frontpage_options=> $frontpage_c,
                 censor_label     => $censor_label,
                 censor_options   => $censor_c,
                 comic_options    => $comic_c,
                 comic_id         => $comic_id,
                 th_bg            => $GLOBALS['phpgw_info']["theme"]["th_bg"],
                 th_text          => $GLOBALS['phpgw_info']["theme"]["th_text"]));

    $prefs_tpl->parse(message_part, "message");
    $message_c = $prefs_tpl->get("message_part");

    $prefs_tpl->parse(body_part, "prefs");
    $body_c = $prefs_tpl->get("body_part");
    
    /**************************************************************************
     * pull it all together
     *************************************************************************/
    $body_tpl = $GLOBALS['phpgw']->template;
    $body_tpl->set_unknowns("remove");
    $body_tpl->set_file(body, "prefs.common.tpl");
    $body_tpl->set_var(array(preferences_message => $message_c,
                             preferences_body    => $body_c));
    $body_tpl->parse(BODY, "body");
    $body_tpl->p("BODY");

    $GLOBALS['phpgw']->common->phpgw_footer();
}

?>
