<?php
	/******************************************************\
	* These functions are my attempt to improve on the     *
	* butt ugly phpNuke themes implementation.  Yes, I     *
	* know I'd be better off starting from scratch.        *
	\******************************************************/

	/******************************************************\
	* So here's the deal.  A theme file can have one of a  *
	* few different variables.  It can have a plain $var   *
	* variable in it that gets eval'ed to whatever.  There *
	* can be a phplib style {var}.  Then there can be link *
	* vars which look like this: {?sitemgr:page_id=1} or       *
	* {?phpgw:/sitemgr/index.php,cat_id=3}.  These resolve to  *
	* the appropriate link(file,extravars) function calls. *
	\******************************************************/
	function add_theme_var($var, $value)
	{
		switch($var)
		{
			case 'xheader':
			case 'xuser':
			case 'xsite_name':
			case 'sitename':
				globalize_var($var, $value);
				break;
			case 'footer':
				globalize_var('foot1',$value);
				break;
			default:
				$GLOBALS['theme_vars'][$var] = $value;
				break;
		}
	}

	function parse_theme_vars($html_file)
	{
		return preg_replace_callback("/\{([^{ ]+)\}/",'replace_var',$html_file);
	}

	function replace_var($vars)
	{
		$var = $vars[1];
		if (substr($var,0,9)=='?sitemgr:')
		{
			$params=explode(',',substr($var,9));
			switch(count($params))
			{
				case 0:
					$val = '';
					break;
				case 1:
					$val = sitemgr_link2('',$params[0]);
					break;
				case 2:
					$val = sitemgr_link2($params[0],$params[1]);
					break;
				default:
					$val = $var;
			}
		}
		elseif (substr($var,0,7)=='?phpgw:')
		{
			$params=explode(',',substr($var,7));
			switch(count($params))
			{
				case 0:
					$val = '';
					break;
				case 1:
					$val = phpgw_link('',$params[0]);
					break;
				case 2:
					$val = phpgw_link($params[0],$params[1]);
					break;
				default:
					$val = $var;
			}
		}
		elseif (substr($var,0,1)=='?')
		{
			$val = sitemgr_link2('/index.php',substr($var,1));
		}
		elseif ($var == 'news')
		{
			$ui = new ui;
			$val = $ui->get_news();
			unset($ui);
		}
		elseif (substr($var,0,6) == 'block-')
		{
			if (file_exists('blocks/'.$var.'.php'))
			{
				$title=ereg_replace('_',' ',substr($var,6));
				include 'blocks/'.$var.'.php';
			}
			else
			{
				$title = lang('Block not found.');
				$content = lang('Contact the administrator.');
			}

			add_theme_var('block_title',$title);
			add_theme_var('block_content',$content);

			if(function_exists('themecenterbox'))
			{
				$val = themecenterbox($title, $content);
			}
			else
			{
				$val = parse_theme_vars(implode("",file('templates/'.$GLOBALS['sitemgr_info']['themesel'].'/centerblock.tpl')));
			}
		}
		else
		{
			/* Check for reserved vars first, otherwise
			   get from the global theme_vars
			*/
			switch (strtolower($var))
			{
				case 'opentable':
					$val = OpenTable();
					break;
				case 'opentable2':
					$val = OpenTable2();
					break;
				case 'closetable':
					$val = CloseTable();
					break;
				case 'closetable2':
					$val = CloseTable2();
					break;
				default:
					$val = $GLOBALS['theme_vars'][$var];
			}
		}
		return $val;
	}

	function globalize_var($var, $value)
	{
		$GLOBALS[$var] = $value;
	}

	/******************************************************\
	* These functions are callbacks that get or put text   *
	* for the themes.  They're ascinine and used only      *
	* sporadically.
	\******************************************************/
	function title($text) 
	{
		OpenTable();
		echo '<center><font class="title"><b>'.$text.'</b></font></center>';
		CloseTable();
		echo '<br>';
	}
	function footmsg()
	{
		$objbo = new bo;
		echo $objbo->get_footer();
	}

	/******************************************************\
	* These functions mostly just return dummy values to   *
	* keep the themes from complaining.                    *
	\******************************************************/
	function is_admin()
	{
		$acl = CreateObject('sitemgr.ACL_BO');
		$retval = $acl->is_admin();
		unset($acl);
		return $retval;
	}
	function is_user()
	{
		global $sitemgr_info;
		if ($GLOBALS['phpgw_info']['user']['account_lid'] != $sitemgr_info['login'])
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function get_lang()
	{
		return '';
	}
	function translate()
	{
		return '';
	}
	function sql_num_rows()
	{
		return '';
	}
	function cookiedecode($user)
	{
		return '';
	}
	function sql_query()
	{
		return '';
	}
	function sql_fetch_row()
	{
		return '';
	}
	function is_active($module) 
	{
		return false;
    }
	function message_box() 
	{
		// For displaying news
		return '';
	}
	function online() 
	{
		//used to setup session stuff
		return '';
	}
	function selectlanguage() 
	{
		return '';
	}
	function delQuotes($string)
	{
		return $string;
	}
	function searchblock() 
	{
		return '';
	}
	function FixQuotes ($what = "") 
	{
		$what = ereg_replace("'","''",$what);
		while (eregi("\\\\'", $what)) 
		{
			$what = ereg_replace("\\\\'","'",$what);
		}
		return $what;
	}
	function check_words($Message) 
	{
    	return ($Message);
	}
	function getusrinfo($user)
	{
		$userinfo = array(
			'uid' => $GLOBALS['phpgw_info']['user']['account_id'],
			'name' => $GLOBALS['phpgw_info']['user']['account_lid'],
			'uname' => '',
			'email' => '',
			'femail' => '',
			'url' => '',
			'user_avatar' => '',
			'user_icq' => '',
			'user_occ' => '',
			'user_from' => '',
			'user_interest' => '',
			'user_sig' => '',
			'user_viewemail' => '',
			'user_theme' => '',
			'user_aim' => '',
			'user_yim' => '',
			'user_msnm' => '',
			'pass' => '',
			'storynum' => '',
			'umode' => '',
			'uorder' => '',
			'thold' => '',
			'noscore' => '',
			'bio' => '',
			'ublockon' => '',
			'ublock' => '',
			'theme' => '',
			'commentmax' => '',
			'newsletter' => 0
		);

    	return $userinfo;
	}
	
	/******************************************************\
	* These functions are used for displaying blocks       *
	\******************************************************/
	function render_blocks($side, $blockfile, $title, $content, $bid, $url) 
	{
    	if ($url == '') 
		{
			if ($blockfile == "") 
			{
	    		if ($side == "c") 
				{
					themecenterbox($title, $content);
	    		} 
				else 
				{
					themesidebox($title, $content);
	    		}
			} 
			else 
			{
	    		if ($side == "c") 
				{
					blockfileinc($title, $blockfile, 1);
	    		} 
				else 
				{
					blockfileinc($title, $blockfile);
	    		}
			}
    	} 
		else 
		{
			if ($side == "c") 
			{
				headlines($bid,1);
			} 
			else 
			{
				headlines($bid);
			}
    	}
	}

	function blocks($side) 
	{
    	global $blocks;

		//switch(strtolower(substr($side,0,1)))
		switch(strtolower(substr($side,0,1)))
		{
			case 'l':
			case 'r':
			case 'c':
				$side = strtolower(substr($side,0,1));
				break;
			default:
				echo "<h1>something wierd</h1>";
		}
		for ($i=0; $i<count($blocks);$i++)
		{
			if ($side == $blocks[$i]['position'])
			{
				$bid=$i;
				$bkey=$blocks[$i]['bkey'];
				$title=$blocks[$i]['title'];
				$content=$blocks[$i]['content'];
				$url=$blocks[$i]['url'];
				$blockfile=$blocks[$i]['blockfile'];
				$view=$blocks[$i]['view'];

				if ($bkey == 'admin') 
				{
	    			adminblock();
				} 
				elseif ($bkey == 'userbox') 
				{
	    			userblock();
				} 
				elseif ($bkey == '') 
				{
	    			if ($view==0) 
					{
						render_blocks($side, $blockfile, $title, $content, $bid, $url);
	    			} 
					elseif ($view==1 && is_user()) 
					{
						render_blocks($side, $blockfile, $title, $content, $bid, $url);
	    			} 
					elseif ($view==2 && is_admin()) 
					{
						render_blocks($side, $blockfile, $title, $content, $bid, $url);
	    			} 
					elseif (($view==3) && (!is_user()))
					{
						render_blocks($side, $blockfile, $title, $content, $bid, $url);
	    			}
				}
			}
    	}
	}


	function blockfileinc($title, $blockfile, $side=0) 
	{
    	$blockfiletitle = $title;
    	$file = @file("blocks/$blockfile");
    	if (!$file) 
		{
			$content = lang('Block not found.');
    	} 
		else 
		{
			include("blocks/$blockfile");
    	}
    	if ($content == '') 
		{
			$content = lang('Block returned no content.');
    	}
    	if ($side == 1) 
		{
			themecenterbox($blockfiletitle, $content);
    	} 
		else 
		{
			themesidebox($blockfiletitle, $content);
    	}
	}

	function adminblock() 
	{
    	if (is_admin()) 
		{
			global $blocks;

			foreach($blocks as $block)
			{
				if ($block['bkey']=='admin')
				{
	    			$content = '<font class="content">'.$block['content'].'</font>';
					$title = $block['title'];
	    			themesidebox($title, $content);
				}
			}
    	}
	}

	function loginbox() {
    	if (!is_user()) 
		{
			$title = 'Login';
			$boxstuff = '<form name="login" action="'.$GLOBALS['phpgw_info']['server']['webserver_url'].'/login.php" method="post">';
			$boxstuff .= '<input type="hidden" name="passwd_type" value="text">';
			$boxstuff .= '<center><font class="content">Login Name<br>';
			$boxstuff .= '<input type="text" name="login" size="8" value=""><br>';
			$boxstuff .= 'Password<br>';
			$boxstuff .= '<input name="passwd" size="8" type="password"><br>';
			$boxstuff .= '<input type="submit" value="Login" name="submitit">';
			$boxstuff .= '</font></center></form>';
			$boxstuff .= "<center><font class=\"content\">Don't have an account?  Maybe in a future version you can create one. :-)</font></center>";
			themesidebox($title, $boxstuff);
    	}
	}

	function userblock() 
	{
    	if(is_user())
		{
			//this is a handy function to allow the user to 
			//have their own custom block.  too bad we don't
			//have that feature (yet).
		}
    }


	/*
	function themecenterbox($title, $content) 
	{
    	$contents = OpenTable();
    	$contents.='<center><font class="option"><b>'.
			$title.'</b></font></center><br>'."$content";
    	$contents.=CloseTable();
    	$contents.= "<br>";
		echo $contents;
	}
	*/

?>
