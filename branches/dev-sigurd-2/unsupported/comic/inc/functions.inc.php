<?php
  /**************************************************************************\
  * phpGroupWare - Comic Functions                                           *
  * http://www.phpgroupware.org                                              *
  * This file written by Sam Wynn <neotexan@wynnsite.com>                    *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

define('COMIC_STATIC',  0);
define('COMIC_SNARFED', 1);

define('STD_SUCCESS',   0);
define('STD_ERROR',     1);
define('STD_WARNING',   2);
define('STD_FORBIDDEN', 3);
define('STD_CENSOR',    4);
define('STD_NOREMOTE',  5);
define('STD_CURRENT',   6);
define('STD_TEMPLATE',  7);

define('COMIC_LEFT',    1);
define('COMIC_RIGHT',   0);

$g_censor_level = array
(
	0 => 'G',
	1 => 'PG',
	2 => 'R'
);

$g_image_source = array
(
	0 => 'Remote',
	1 => 'Local'
);
			
$g_dayofweek = array
(
	'Mon' => 1,
	'Tue' => 2,
	'Wed' => 3,
	'Thu' => 4,
	'Fri' => 5,
	'Sat' => 6,
	'Sun' => 7
);

function comic_admin_data(&$image, &$censor, &$override, &$remote, &$filesize)
{
	$GLOBALS['phpgw']->db->query('SELECT * FROM phpgw_comic_admin', __LINE__, __FILE__);

	if (!$GLOBALS['phpgw']->db->num_rows())
	{
		$GLOBALS['phpgw']->db->query('INSERT INTO phpgw_comic_admin'
			. ' VALUES(0,0,0,0,120000)', __LINE__, __FILE__);
		
		$GLOBALS['phpgw']->db->query('SELECT * FROM phpgw_comic_admin', __LINE__, __FILE__);
	}

	$GLOBALS['phpgw']->db->next_record();

	$image    = $GLOBALS['phpgw']->db->f('admin_imgsrc');
	$censor   = $GLOBALS['phpgw']->db->f('admin_censorlvl');
	$override = $GLOBALS['phpgw']->db->f('admin_coverride');
	$remote   = $GLOBALS['phpgw']->db->f('admin_rmtenabled');
	$filesize = $GLOBALS['phpgw']->db->f('admin_filesize');
}

function comic_flag_success($comic_url, $data_id)
{
	$GLOBALS['phpgw']->db->lock('phpgw_comic_data');
	
	$GLOBALS['phpgw']->db->query('UPDATE phpgw_comic_data SET'
		      .' data_date=' . date('Ymd') . ','
		      ." data_imageurl='" .$GLOBALS['phpgw']->db->db_addslashes($comic_url).'"'
		      .' WHERE data_id='.intval($data_id), __LINE__, __FILE__);
	
	$GLOBALS['phpgw']->db->unlock();
}

function comic_resolve_url($remote_enabled, &$fetch_url, &$comic_url, &$comic_day)
{
    
    $status = STD_SUCCESS;

    /**************************************************************************
     * check to see if already 'snarfed' today
     *************************************************************************/
    if ($GLOBALS['phpgw']->db->f('data_date') == (int)date('Ymd'))
    {
        $status = STD_CURRENT;
        $comic_url = $GLOBALS['phpgw']->db->f('data_imageurl');

        /**********************************************************************
         * need to generate resolve type links without going to the web
         * and not putting them in the database
         *********************************************************************/
        if ($GLOBALS['phpgw']->db->f('data_linkurl') == '')
        {
            $fetch_url = $GLOBALS['phpgw']->db->f('data_baseurl')
                .$GLOBALS['phpgw']->db->f('data_parseurl');
            $comic_time   = time() - ($GLOBALS['phpgw']->db->f('data_daysold')*3600*24);
            comic_resolver($fetch_url, $comic_time);
            
        }
        
    }
    else
    {
        /**********************************************************************
         * resolve our comic url, link url and comic day
         *********************************************************************/
        switch ($GLOBALS['phpgw']->db->f('data_resolve'))
        {	 
          case 'Static':
            $status = comic_resolve_static($comic_url, $comic_day);
            break;
          case 'Remote':
            $status = comic_resolve_remote($remote_enabled, $fetch_url,
                                           $comic_url, $comic_day);
            break;
        }
    }
    
    return $status;
}

function comic_resolve_linkurl($status, $fetch_url, &$link_url, &$comment)
{
    $link_tgt = '_blank';
    
    if ($status != STD_CENSOR)
    {
        /**********************************************************************
         * ultimately want to go where DB says
         *********************************************************************/
        $link_url = $GLOBALS['phpgw']->db->f('data_linkurl');

        /**********************************************************************
         * if can't then see if we built a url where we got the image from
         *********************************************************************/
        if ($link_url == '')
        {
            $link_url = $fetch_url;
        
            /******************************************************************
             * still no good, try the base url
             *****************************************************************/
            if ($link_url == '')
            {
                $link_url = $GLOBALS['phpgw']->db->f('data_baseurl');

                /**************************************************************
                 * everything else is failing so let's link self
                 *************************************************************/
                if ($link_url == '')
                {
                    $link_url = $GLOBALS['phpgw']->link('/comic/index.php');
                    $link_tgt = '_self';
                }
                
            }
        }
    }
    else
    {
        /**********************************************************************
         * need to break the link url
         *********************************************************************/
        $link_url = $GLOBALS['phpgw']->link('/comic/index.php');
        $link_tgt = '_self';
    }

    $comment =  lang('Visit %1', $link_url);
    return $link_tgt;
}
                    
function comic_resolve_static(&$comic_url, &$comic_day)
{
    $status       = STD_SUCCESS;

    $comic_url    = $GLOBALS['phpgw']->db->f('data_baseurl');
    $comic_time   = time() - ($GLOBALS['phpgw']->db->f('data_daysold')*3600*24);
    $comic_day    = substr(date('D', $comic_time),0,2);

    /**************************************************************************
     * rather straight forward resolve of the url
     *************************************************************************/
    $status       = comic_resolver($comic_url, $comic_time);


    return $status;
}

function comic_resolve_remote($remote_enabled, &$fetch_url, &$comic_url, &$comic_day)
{
    $status       = STD_SUCCESS;
    $unresolved   = True;
    
    $comic_time   = time() - ($GLOBALS['phpgw']->db->f('data_daysold')*3600*24);

    /**************************************************************************
     * generate the resolver components
     *************************************************************************/
    switch ($GLOBALS['phpgw']->db->f('data_parser'))
    {
      case 'Comics':
      case 'Comiczone7':
      case 'Comiczone10':
      case 'United':
        $baseurl    = 'http://www.comics.com';
        $parseurl   = '/comics/'.$GLOBALS['phpgw']->db->f('data_name').'/index.html';
        $parse_expr = '/comics/'
            .$GLOBALS['phpgw']->db->f('data_name')
            .'/archive/images/'
            .$GLOBALS['phpgw']->db->f('data_name')
            .'[0-9]*.(gif|jpg)';
        break;
      case 'King':
/*
        if ($GLOBALS['phpgw']->db->f('data_daysold') == 0)
        {
            $comic_time = time() - (14*3600*24);
        }
        $baseurl  = 'http://est.rbma.com/content/';
        $parseurl = $GLOBALS['phpgw']->db->f('data_prefix').'?date={Ymd}';

        comic_resolver(&$parseurl, $comic_time);

        $fetch_url = 'http://www.kingfeatures.com/comics/'.
            $GLOBALS['phpgw']->db->f('data_name');

        $comic_url = $baseurl.$parseurl;
        
        $unresolved = False;
*/
        break;
        
      case 'Toonville':
        $baseurl    = 'http://aolsvc.toonville.aol.com';
        $parseurl   = '/main.asp?fnum='.$GLOBALS['phpgw']->db->f('data_comicid');
        $parse_expr = '/[cC]*ontent1/'
            .$GLOBALS['phpgw']->db->f('data_name')
            .'/'
            .$GLOBALS['phpgw']->db->f('data_prefix')
            .'[0-9]*'
            .'[g]*.(jpg|gif)';

        comic_resolver($parse_expr, $comic_time);
        break;
      case 'Creators':
        $baseurl    = 'http://www.comics.com';
        $parseurl   = '/creators/'.$GLOBALS['phpgw']->db->f('data_name').'/index.html';
        $parse_expr = '/creators/'
            .$GLOBALS['phpgw']->db->f('data_name')
            .'/archive/images/'
            .$GLOBALS['phpgw']->db->f('data_name')
            .'[0-9]*.(gif|jpg)';
        break;
      case 'ComicsPage':
        if ($GLOBALS['phpgw']->db->f('data_daysold') == 0)
        {
            $comic_time = time() - (7*3600*24);
        }
        $baseurl    = 'http://www.comicspage.com';
        $parseurl   = '/'.$GLOBALS['phpgw']->db->f('data_name').'/index.html';
        $parse_expr = '/daily/'
            .$GLOBALS['phpgw']->db->f('data_prefix')
            .'/{Ymd}'
            .$GLOBALS['phpgw']->db->f('data_prefix')
            .'-a.gif';
        comic_resolver($parse_expr, $comic_time);

        $fetch_url  = $baseurl.$parseurl;

        $comic_url  = $baseurl.$parse_expr;

        $unresolved = False;
        break;
      case 'Ucomics':
        $baseurl    = 'http://images.ucomics.com';
        $parseurl   = '/comics/'
            .$GLOBALS['phpgw']->db->f('data_prefix')
            .'/{Y}/'
            .$GLOBALS['phpgw']->db->f('data_prefix')
            .'{ymd}.gif';
        comic_resolver($parseurl, $comic_time);

        $fetch_url  = $GLOBALS['phpgw']->db->f('data_linkurl');

        $comic_url  = $baseurl.$parseurl;

        $unresolved = False;
        break;
      default:
        $baseurl      = $GLOBALS['phpgw']->db->f('data_baseurl');
        $parseurl     = $GLOBALS['phpgw']->db->f('data_parseurl');
        $parse_expr   = $GLOBALS['phpgw']->db->f('data_parsexpr');
        
        $status = comic_resolver($parseurl, $comic_time);

        if ($status == STD_SUCCESS)
        {
            $status = comic_resolver($parse_expr, $comic_time);
        }
        break;
    }
    
    /**************************************************************************
     * try to call the parser on the html we resolved to get our url
     *************************************************************************/
    if (($status == STD_SUCCESS) && ($unresolved == True))
    {
        $fetch_url = $baseurl . $parseurl;
        
        if ($remote_enabled)
        {
            $status = comic_parser($baseurl, $fetch_url, $parse_expr,
                                   $comic_url);
        }
        else
        {
            $status = STD_NOREMOTE;
        }
    }
    
    $comic_day    = substr(date('D', $comic_time),0,2);
    
    return $status;
}
    
function comic_resolver(&$myurl, $comic_time)
{
    global $g_dayofweek;
    
    $status = STD_SUCCESS;
    
    /**************************************************************************
     * get all of our resolver fields
     *************************************************************************/
    if (preg_match_all('/{[A-Za-z0-9\-]*}/', $myurl, $strings))
    {
        /**********************************************************************
         * replace matches
         *********************************************************************/
        for ($loop = 0; $loop < sizeof($strings[0]); $loop++)
        {
            $repl_str = '';
            
            switch($strings[0][$loop])
            {
              /****************************************************************
               * date components of the url
               ***************************************************************/
              case '{y}':
                $repl_str  = date('y', $comic_time);
                break;
              case '{m}':
                $repl_str  = date('m', $comic_time);
                break;
              case '{d}':
                $repl_str  = date('d', $comic_time);
                break;
              case '{Ml}':
                $repl_str = date('M', $comic_time);
                $repl_str = strtolower($repl_str);
                break;
              case '{Y}':
                $repl_str = date('Y', $comic_time);
                break;
              case '{ym}':
                $repl_str = date('ym', $comic_time);
                break;
              case '{ymd}':
                $repl_str = date('ymd', $comic_time);
                break;
              case '{Ymd}':
                $repl_str = date('Ymd', $comic_time);
                break;
              case '{day}':
                $day = date('D', $comic_time);
                $repl_str = $g_dayofweek[$day];
                break;
                
              default:
                $status = STD_ERROR;
                break;
            }
            if ($status != STD_ERROR)
            {
                $myurl =
                    str_replace($strings[0][$loop], $repl_str, $myurl);
            }
            else
            {
                break;
            }
        }
    }
    return $status;
}

function comic_parser($baseurl, $fetch_url, $parse_expr, &$comic_url)
{
    $status = STD_SUCCESS;
    
    /**************************************************************************
     * get the file to parse
     *************************************************************************/
    if ($file  = $GLOBALS['phpgw']->network->gethttpsocketfile($fetch_url))
    {
        $lines = count($file);

        /**********************************************************************
         * if succeed grok to find file or error
         *********************************************************************/
        for($index=0;($index < $lines && (!$status));$index++)
        {
            if (eregi('forbidden', $file[$index]))
            {
                $status = STD_FORBIDDEN;
                break;
            }

            if (ereg($parse_expr, $file[$index], $elements))
            {
                $comic_url = $baseurl . $elements[0];
                break;
            }
        }
    }
    else
    {
        $status = STD_ERROR;
    }
    
    return $status;
}

function comic_snarf(&$comic_url, $filesize)
{
    $status = STD_SUCCESS;
    $filename   = 'images/' . $GLOBALS['phpgw']->db->f('data_name') . substr($comic_url,-4);
    $filename_w = PHPGW_SERVER_ROOT.'/comic/'.$filename;
    
    /**************************************************************************
     * get our image or fail
     *************************************************************************/
    // if($file  = $GLOBALS['phpgw']->network->gethttpsocketfile($comic_url))

    if($fpread = @fopen($comic_url, 'r'))
    {
        // $lines = count($file);
        
        /**********************************************************************
         * if succeed grok it for errors
         *********************************************************************/
        // for($index=0;($index < 10 && (!$status));$index++)
        // {
            // if (eregi('forbidden', $file[$index]))
            // {
                // $status = STD_FORBIDDEN;
            // }
        // }
        
        // if (!$status)
        {
            $file = fread($fpread, $filesize);
            
            /******************************************************************
             * if succeed, put it in our local file
             *****************************************************************/
            if ($fp = fopen($filename_w,'w'))
            {
                fwrite($fp, $file);
                
                fclose($fp);
            
                $comic_url = $GLOBALS['phpgw_info']['server']['webserver_url']
                    .'/comic/'
                    .$filename;
            }
            else
            {
                $status = STD_ERROR;
            }
            fclose($fpread);
        }
    }
    else
    {
        $status == STD_ERROR;
    }
    
    return $status;
}

function comic_error($image_location, $status, &$comic_url)
{
    /**************************************************************************
     * the image should either be a side or center
     *************************************************************************/
    switch ($image_location)
    {
      case 'S':
        $image_size = '_sm';
        break;
      default:
        $image_size = '';
        break;
    }

    /**************************************************************************
     * our image will be dressed with some pertinent error message
     *************************************************************************/
    switch ($status)
    {
      case STD_CENSOR:
        $label = '_censor';
        break;
      case STD_FORBIDDEN:
        $label = '_forbid';
        break;
      default:
        $label = '';
        break;
    }
    
    /**************************************************************************
     * compose the error comic url
     *************************************************************************/
    $comic_url = $GLOBALS['phpgw_info']['server']['webserver_url']
        .'/'.$GLOBALS['phpgw_info']['flags']['currentapp']
        .'/images/template'
        .$label
        .$image_size
        .'.png';
}

function comic_match_bar($start, $end, $indexlimit,
                         $comics_displayed, &$matchs_c)
{
    switch ($indexlimit)
    {
      case 0:
      case 1:
        {
            $showstring =
                lang('showing %1', $comics_displayed);
        }
        break;

      default:
        {
            $showstring =
                lang('showing %1 (%2 - %3 of %4)',
                     $comics_displayed,
                     ($start + 1), $end, $indexlimit);
        }
        break;
    }

    $matchs_tpl = $GLOBALS['phpgw']->template;
    $matchs_tpl->set_unknowns('remove');
    $matchs_tpl->set_file(matchs, 'matchs.comic.tpl');
    $matchs_tpl->
        set_var
        (array(next_matchs_left  =>
               $GLOBALS['phpgw']->nextmatchs->left('/comic/index.php',$start,$indexlimit,''),
               next_matchs_label => $showstring,
               next_matchs_right =>
               $GLOBALS['phpgw']->nextmatchs->right('/comic/index.php',$start,$indexlimit,''),
               navbar_bg         => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
               navbar_text       => $GLOBALS['phpgw_info']['theme']['navbar_text']));
    $matchs_tpl->parse(MATCHS, 'matchs');
    $matchs_c = $matchs_tpl->get('MATCHS');
}

function comic_display($comic_list, $comic_scale, $comic_perpage,
                       $user_censorlvl, $start, &$comic_left_c,
                       &$comic_right_c, &$comic_center_c, &$matchs_c)
{
    $sideno = COMIC_LEFT;
    
    /**************************************************************************
     * how many potential comics
     *************************************************************************/
    $indexlimit = count($comic_list);
    
    /**************************************************************************
     * number of comics displayed
     *************************************************************************/
    $comics_displayed = 0;
    
    /**************************************************************************
     * no reason to generate data if don't have any comics
     *************************************************************************/
    if ($indexlimit > 0)
    {
        /**********************************************************************
         * get the admin settings
         *********************************************************************/
        comic_admin_data($image_src,
                         $admin_censorlvl,
                         $censor_override,
                         $remote_enabled,
                         $filesize);
        
        /**********************************************************************
         * start our template
         *********************************************************************/
        $comic_tpl = $GLOBALS['phpgw']->template;
        $comic_tpl->set_unknowns('remove');
        $comic_tpl->set_file(
            array(tableleft   => 'table.left.tpl',
                  tableright  => 'table.right.tpl',
                  tablecenter => 'table.center.tpl',
                  row         => 'row.common.tpl'));
        
        /**********************************************************************
         * where to start and end
         *********************************************************************/
        if (!$start)
        {
            $start = 0;
        }

        $end = $start + $comic_perpage;
        
        if ($end > $indexlimit)
        {
            $end = $indexlimit;
        }
        
        /**********************************************************************
         * step through the comics
         *********************************************************************/
        for ($index=$start; (($index < $end) && ($index < $indexlimit));
             $index++)
        {
            /******************************************************************
             * get the comic data
             *****************************************************************/
            $GLOBALS['phpgw']->db->query('SELECT * FROM phpgw_comic_data'
                              .' WHERE data_id='.intval($comic_list[$index])
			      ." AND data_enabled='T'", __LINE__, __FILE__);

            if ($GLOBALS['phpgw']->db->next_record())
            {
                $comic_censorlvl = $GLOBALS['phpgw']->db->f('data_censorlvl');
                $comic_day       = substr(date('D', time()),0,2);   /* today */
                $comic_url       = '';
                $fetch_url       = '';

                /**************************************************************
                 * if user meets censorship criteria
                 *************************************************************/
                if (($comic_censorlvl <= $user_censorlvl) &&
                    (($comic_censorlvl <= $admin_censorlvl) ||
                     ($censor_override == 1)))
                {
                    /**********************************************************
                     * resolve the url
                     *********************************************************/
                    $status = comic_resolve_url($remote_enabled, $fetch_url,
                                                $comic_url, $comic_day);
                }
                else
                {
                    /**********************************************************
                     * otherwise have been censored
                     *********************************************************/
                    $status = STD_CENSOR;
                }

                /**************************************************************
                 * set the link data
                 *************************************************************/
                $link_tgt =
                    comic_resolve_linkurl($status, $fetch_url,
                                          $link_url, $comment);
                
                /**************************************************************
                 * if comic_day is not in days allowed flag error
                 *************************************************************/
                if (!strstr($GLOBALS['phpgw']->db->f('data_pubdays'), $comic_day))
                {
                    $status = STD_WARNING;
                    $end++;
                }
                
                /**************************************************************
                 * snarf the image
                 *************************************************************/
                if (($image_src == COMIC_SNARFED) && ($status == STD_SUCCESS))
                {
                    $status = comic_snarf($comic_url, $filesize);
                }
                
                /**************************************************************
                 * need to determine image size & location
                 *************************************************************/
                if ($comic_day == 'Su')
                {
                    $image_width = $GLOBALS['phpgw']->db->f('data_swidth');
                }
                else
                {
                    $image_width = $GLOBALS['phpgw']->db->f('data_width');
                }
                if ( $image_width <= 300)
                {
                    $image_location = 'S';
                }
                else
                {
                    $image_location = 'C';
                }
                
                /**************************************************************
                 * if no image available, then give error image
                 *************************************************************/
                if (($status != STD_SUCCESS) &&
                    ($status != STD_WARNING) &&
                    ($status != STD_CURRENT))
                {
                    comic_error($image_location, $status, $comic_url);
                    $status = STD_TEMPLATE;
                }

                /**************************************************************
                 * effectively have something to display
                 *************************************************************/
                if (($status == STD_SUCCESS) ||
                    ($status == STD_CURRENT) ||
                    ($status == STD_TEMPLATE))
                {
                    /**********************************************************
                     * image scaling
                     *********************************************************/
                    switch ($comic_scale)
                    {
                      case 1:
                        switch ($image_location)
                        {
                          case 'S':
                            $image_width   = '280';
                            break;
                          default:
                            $image_width   = '580';
                            break;
                        }
                        break;
                      default:
                        if ($image_width <= 0)
                        {
                            $image_width   = '100%';
                        }
                        break;
                    }

                    /**********************************************************
                     * find which template set (left/center/right) gets comic
                     *********************************************************/
                    switch($image_location)
                    {
                      case 'S':
                        switch ($sideno)
                        {
                          case COMIC_RIGHT:
                            $sideno = COMIC_LEFT;
                            $side = 'right';
                            break;
                          case COMIC_LEFT:
                            $sideno = COMIC_RIGHT;
                            $side = 'left';
                            break;
                        }
                        break;
                      default:
                        $side = 'center';
                        break;
                    }
                    
                    $name = lang('%1 by %2 (%3)',
                                 $GLOBALS['phpgw']->db->f('data_title'),
                                 $GLOBALS['phpgw']->db->f('data_author'),
                                 $index+1);
                    
                    $comic_tpl->
                        set_var
                        (array
                         (image_url   => $comic_url,
                          image_width => $image_width,
                          link_url    => $link_url,
                          link_tgt    => $link_tgt,
                          comment     => $comment,
                          name        => $name,
                          th_bg       => $GLOBALS['phpgw_info']['theme']['th_bg'],
                          th_text     => $GLOBALS['phpgw_info']['theme']['th_text']));
                    $comic_tpl->parse($side.'_part', 'row', TRUE);

                    $comics_displayed++;

                    /**********************************************************
                     * put the url and date in the database
                     *********************************************************/
                    if ($status == STD_SUCCESS)
                    {
                        comic_flag_success($comic_url, $comic_list[$index]);
                    }
                }
            }
            else
            {
                /**************************************************************
                 * was unable to fetch a comic
                 *************************************************************/
                $end++;
            }
        }
        
        /**********************************************************************
         * get the template body
         *********************************************************************/
        $comic_tpl->parse(TABLELEFT,   'tableleft');
        $comic_tpl->parse(TABLERIGHT,  'tableright');
        $comic_tpl->parse(TABLECENTER, 'tablecenter');
        $comic_left_c   = $comic_tpl->get('TABLELEFT');
        $comic_right_c  = $comic_tpl->get('TABLERIGHT');
        $comic_center_c = $comic_tpl->get('TABLECENTER');
        
        if ($end > $indexlimit)
        {
            $end = $indexlimit;
        }
    
        /**********************************************************************
         * finish out the template with the next matchs bar
         *********************************************************************/
        $temp = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
        $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']
            = $comic_perpage;
        comic_match_bar($start, $end, $indexlimit,
                        $comics_displayed,
                        $matchs_c);
        $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']
            = $temp;
    }
}

function comic_display_frontpage($data_id, $scale, $censor_level)
{
    /**************************************************************************
     * get the admin settings
     *************************************************************************/
    comic_admin_data($image_src,
                     $admin_censorlvl,
                     $censor_override,
                     $remote_enabled,
                     $filesize);

    /**************************************************************************
     * determine what comic to get
     *************************************************************************/
    $comic_day = substr(date('D', time()),0,2);   /* today */

    /**************************************************************************
     * try to successfully get a random comic match...
     * this would be better with a validated comic_day since some
     * comics have to look back
     *************************************************************************/
    if ($data_id == 0)
    {
        $match_str =
            'WHERE data_censorlvl <= '.intval($censor_level)
	    . " AND data_pubdays LIKE '%".$GLOBALS['phpgw']->db->db_addslashes($comic_day)."%'"
	    . " AND data_enabled='T'";
        
    }
    /**************************************************************************
     * they specifically want a comic
     *************************************************************************/
    else
    {
        $match_str =
            'WHERE data_id='.intval($data_id)
	    . " AND data_enabled='T'";
    }

    /**************************************************************************
     * get the comic data
     *************************************************************************/
    $GLOBALS['phpgw']->db->query('SELECT * FROM phpgw_comic_data '
                      .$match_str, __LINE__, __FILE__);

    /**************************************************************************
     * of the potentially valid returns...lets try to shake it up a bit and
     * pick only one
     *************************************************************************/
    if ($data_id == 0)
    {
        srand((double)microtime()*1000000);
        $randval = rand(1,$GLOBALS['phpgw']->db->num_rows());

        for ($index = 1; $index < $randval; $index++)
        {
            $GLOBALS['phpgw']->db->next_record();
            
        }
    }
    
    /**************************************************************************
     * process valid comic data
     *************************************************************************/
	$end = 0;
    if ($GLOBALS['phpgw']->db->next_record())
    {
        $comic_censorlvl = $GLOBALS['phpgw']->db->f('data_censorlvl');
        $comic_url       = '';
        $fetch_url       = '';
        
        /**********************************************************************
         * if user meets censorship criteria
         *********************************************************************/
        if (($comic_censorlvl <= $censor_level) &&
            (($comic_censorlvl <= $admin_censorlvl) ||
             ($censor_override == 1)))
        {
            /******************************************************************
             * resolve the url
             *****************************************************************/
            $status = comic_resolve_url($remote_enabled, $fetch_url,
                                        $comic_url, $comic_day);
        }
        else
        {
            /******************************************************************
             * otherwise have been censored
             *****************************************************************/
            $status = STD_CENSOR;
        }

        /**********************************************************************
         * set the link data
         *********************************************************************/
        $link_tgt =
            comic_resolve_linkurl($status, $fetch_url,
                                  $link_url, $comment);
        
        /**********************************************************************
         * if comic_day is not in days allowed flag error
         *********************************************************************/
        if (!strstr($GLOBALS['phpgw']->db->f('data_pubdays'), $comic_day))
        {
            $status = STD_WARNING;
            ++$end;
        }
                
        /**********************************************************************
         * snarf the image
         *********************************************************************/
        if (($image_src == COMIC_SNARFED) && ($status == STD_SUCCESS))
        {
            $status = comic_snarf($comic_url, $filesize);
        }
        
        /**********************************************************************
         * need to determine image size & location
         *********************************************************************/
        if ($comic_day == 'Su')
        {
            $image_width = $GLOBALS['phpgw']->db->f('data_swidth');
        }
        else
        {
            $image_width = $GLOBALS['phpgw']->db->f('data_width');
        }
        if ( $image_width <= 300)
        {
            $image_location = 'S';
        }
        else
        {
            $image_location = 'C';
        }

        /**********************************************************************
         * if no image available, then give error image
         *********************************************************************/
        if (($status != STD_SUCCESS) &&
            ($status != STD_WARNING) &&
            ($status != STD_CURRENT))
        {
            comic_error($image_location, $status, $comic_url);
            $status = STD_TEMPLATE;
        }

        /**********************************************************************
         * effectively have something to display
         *********************************************************************/
        if (($status == STD_SUCCESS) ||
            ($status == STD_CURRENT) ||
            ($status == STD_TEMPLATE))
        {
            /******************************************************************
             * image scaling
             *****************************************************************/
            switch ($scale)
            {
              case 1:
                switch ($image_location)
                {
                  case 'S':
                    $image_width   = '350';
                    break;
                  default:
                    $image_width   = '700';
                    break;
                }
                break;
              default:
                if ($image_width <= 0)
                {
                    $image_width   = '100%';
                }
                break;
            }

            $name = lang('%1 by %2',
                         $GLOBALS['phpgw']->db->f('data_title'),
                         $GLOBALS['phpgw']->db->f('data_author'));

            /******************************************************************
             * our template
             *****************************************************************/
            $comic_tpl = CreateObject('phpgwapi.Template',
                                      $GLOBALS['phpgw']->common->get_tpl_dir('comic'));
            $comic_tpl->set_unknowns('remove');
            $comic_tpl->set_file(
                array(table       => 'table.front.tpl',
                      row         => 'row.common.tpl'));
            $comic_tpl->
                set_var
                (array
                 (image_url   => $comic_url,
                  image_width => $image_width,
                  link_url    => $link_url,
                  link_tgt    => $link_tgt,
                  comment     => $comment,
                  name        => $name,
                  th_bg       => $GLOBALS['phpgw_info']['theme']['th_bg'],
                  th_text     => $GLOBALS['phpgw_info']['theme']['th_text']));
            $comic_tpl->parse('center_part', 'row');
            $comic_tpl->parse(TABLE, 'table');
            
            /******************************************************************
             * put the url and date in the database
             *****************************************************************/
            if ($status == STD_SUCCESS)
            {
                comic_flag_success($comic_url, $data_id);
            }
            return $comic_tpl->fp('out','TABLE');
        }
    }
}

function template_options($app_template, &$options_c, &$images_c)
{
    $directory = opendir(PHPGW_SERVER_ROOT . "comic/templates/base/");

    $index=0;

    while ($filename = readdir($directory))
    {
        if (eregi('format[0-9]{2}.$appname.tpl', $filename, $match))
        {
            $file_ar[$index] = $match[0];
            $index++;
        }
    }

    closedir($directory);

    for ($loop=0; $loop < $index; $loop++)
    {
        eregi('[0-9]{2}', $file_ar[$loop], $tid);
        eregi('format[0-9]{2}', $file_ar[$loop], $tname);

        $template_id = '$tid[0]';
        $template_name['$template_id'] = $tname[0];
    }

    /**************************************************************************
     * start our template
     *************************************************************************/
    $image_tpl = $GLOBALS['phpgw']->template;
    $image_tpl->set_unknowns('remove');
    $image_tpl->set_file(
        array(options => 'option.common.tpl',
              rows    => 'row.images.tpl',
              cells   => 'cell.images.tpl'));

    if(count($template_name))
    {
       asort($template_name);
         while (list($value, $name) = each($template_name))
         {
             $selected = '';
             if ((int)$value == $app_template)
             {
                 $selected = 'selected';
             }

             $image_tpl->set_var(array(OPTION_SELECTED => $selected,
                                       OPTION_VALUE    => (int)$value,
                                       OPTION_NAME     => $name));
        
             $image_tpl->parse(option_list, 'options', TRUE);
         }
    }
    $options_c = $image_tpl->get('option_list');
    
    if(count($template_name))
    {
       reset($template_name);
       $counter = 0;

       while (list($value, $name) = each($template_name))
       {
           $index--;
        
           $imgname = $name.'.gif';

           $filename_f =
               $GLOBALS['phpgw']->common->get_image_dir($appname).'/'.$imgname;
           $filename_a =
               $GLOBALS['phpgw']->common->get_image_path($appname).'/'.$imgname;

           if (file_exists($filename_f))
           {
               $counter++;

               $image_tpl->set_var(array(image_number => $name,
                                      image_url    => $filename_a));
               $image_tpl->parse(image_row, 'cells', TRUE);
           }
        
           if (($counter == 5) || ($index == 0))
           {
               $cells_c = $image_tpl->get('image_row');
            
               $image_tpl->set_var(image_cells, $cells_c);
               $image_tpl->parse(IMAGE_ROWS, rows, TRUE);
            
               $counter = 0;
           }
        }
    }
    $images_c = $image_tpl->get('IMAGE_ROWS');
}

?>
