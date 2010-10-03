<?php
  /**************************************************************************\
  * phpGroupWare - addressbook                                               *
  * http://www.phpgroupware.org                                              *
  * Written by Miles Lott <milosch@phpgroupware.org>                         *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

	$conf['paths']['co']          = '/usr/bin/co';
	$conf['paths']['rcs']         = '/usr/bin/rcs';
	$conf['paths']['rcsdiff']     = '/usr/bin/rcsdiff';
	$conf['paths']['rlog']        = '/usr/bin/rlog';
	$conf['paths']['cvs']         = '/usr/bin/cvs';
	$conf['options']['adminName'] = 'Site Admin';
	$conf['options']['adminEmail'] = 'admin@localhost';
	$conf['options']['shortLogLength'] = '75';

	$GLOBALS['phpgw']->config->read_repository();
	$current_config = $GLOBALS['phpgw']->config->config_data;

	if($current_config)
	{
		/* Location of RCS binaries you must have installed as
		 * part of CVS */
		$conf['paths']['co']          = $current_config['co'];
		$conf['paths']['rcs']         = $current_config['rcs'];
		$conf['paths']['rcsdiff']     = $current_config['rcsdiff'];
		$conf['paths']['rlog']        = $current_config['rlog'];
		$conf['paths']['cvs']         = $current_config['cvs'];
		$conf['options']['adminName'] = $current_config['adminname'];
		$conf['options']['adminEmail'] = $current_config['adminemail'];
		$conf['options']['shortLogLength'] = $current_config['shortloglength'];
	}

	/* Location of CVS HTML templates directory */
	$conf['paths']['templates'] = PHPGW_APP_ROOT . '/templates/default';

	/* To cache stylesheets in the browser to improve
	 * browsing speed, set this to true */
	$conf['css']['cached'] = false;

	$cvsroots = array();
	$GLOBALS['phpgw']->db->query("SELECT * FROM phpgw_chora_sites");
	while ($GLOBALS['phpgw']->db->next_record())
	{
		if ($GLOBALS['phpgw']->db->f('name'))
		{
			$cvsroots[$GLOBALS['phpgw']->db->f('name')] = array(
				'name'     => $GLOBALS['phpgw']->db->f('name'),
				'location' => $GLOBALS['phpgw']->db->f('location'),
				'title'    => $GLOBALS['phpgw']->db->f('title')
			);
			if ($GLOBALS['phpgw']->db->f('intro'))
			{
				$cvsroots[$GLOBALS['phpgw']->db->f('name')]['intro'] = $GLOBALS['phpgw']->db->f('intro');
			}
			if ($GLOBALS['phpgw']->db->f('is_default'))
			{
				$cvsroots[$GLOBALS['phpgw']->db->f('name')]['default'] = True;
			}
		}
	}

	$mime = array();
	/* Set the default MIME value if it cannot otherwise
	 * be determined */
	$mime['default'] = 'text/plain';

	/* Set the location of the Apache mime.types database
	 * with the suffix information in it */
	$mime['mimeTypes'] = '/usr/local/apache/conf/mime.types';

	/* This array is a cache of common extensions which are
	 * directly looked up to avoid the overhead of reading
	 * the mime.types database.  Add any commonly used MIME
	 * types as your site needs */

	$mime['types'] = array(
		'html'  => 'text/html',
		'shtml' => 'text/html',
		'gif'   => 'image/gif',
		'jpeg'  => 'image/jpeg',
		'jpg'   => 'image/jpep',
		'htm'   => 'text/html'
	);

	define('CHORA_VERSION', '0.4.2-cvs');

	/* Variable we wish to propagate across web pages
	 *  sbt = Sort By Type (name, age, author, etc)
	 *  ha  = Hide Attic Files
	 *  ord = Sort order
	 *
	 * Obviously, defaults go into $defaultActs :)
	 * TODO: defaults of 1 will not get propagated correctly - avsm
	 */
	$defaultActs = array(
		'sbt' => CVSLIB_SORT_NONE,
		'sa'  => 0, 
		'ord' => CVSLIB_SORT_ASCENDING
	);

	//for ($cvsroots as $key => $val)
	while (list($key,$val) = each($cvsroots))
	{
		if (isset($val['default']) || !isset($defaultActs['rt']))
		{
			$defaultActs['rt'] = $key;
		}
	}

	$acts = $defaultActs;

	/* See if any have been passed as GET variables, and if
	 * so, assign them into the acts array */
	while(list($key,)=each($acts))
	{
		if(!empty($$key))
		{
			$acts[$key]=$$key;
		}
	}
/*
	if (!isset($cvsroots[$acts['rt']]))
	{
		fatal(404,'Malformed URL');
	}
*/
	$cvsrootopts = $cvsroots[$acts['rt']];

	$cvsroot = $cvsrootopts['location'];

	$conf['paths']['cvsRoot'] = $cvsrootopts['location'];
	$conf['paths']['cvsusers'] = "$cvsroot/".@$cvsrootopts['cvsusers'];
	$conf['paths']['introText'] = ''; //@$cvsrootopts['intro'];
	$conf['options']['introTitle'] = @$cvsrootopts['title'];
	$conf['options']['cvsRootName'] = $cvsrootopts['name'];

	$CVS = CreateObject('chora.cvslib',$conf,$mime);

	/*
	 * Return an array with the names of any of the variables we
	 * need to keep, that are different from the defaults
	 *
	 * @ret Array containing names/vals of differing variables
	 */
	function differingVars()
	{
		@reset($GLOBALS['acts']);
		$ret = array();
		while(list($key,$val) = @each($GLOBALS['acts']))
		{
			if($val != $GLOBALS['defaultActs'][$key])
			{
				$ret[$key]=$val;
			}
		}
		return $ret;
	}

	/*
	* Generate a series of HIDDEN input forms based on the 
	* GET parameters which are different from the defaults
	*
	* @param except Array of exceptions to never output
	* @return A set of INPUT tags with the different variables
	*/
	function generateHiddens($except='')
	{
		if(!is_array($except))
		{
			$except = array();
		}
		$toOut = differingVars();
		$ret = "";
		while (list($key,$val) = each($toOut))
		{
			if (is_array($except) && !in_array($key, $except))
			{
				$ret .= "<input type=\"hidden\" name=\"$key\" value=\"$val\" />\n";
			}
		}
		return $ret;
	}

	/**
	* Generate A HREF tags for urls found within an input string
	* Only http and ftp URIs are scanned for.
	*
	* @param text The input text to be scanned for URLs
	* @return The processed text with <A HREF> tags
	*/
	function htmlify($text)
	{
		/* TODO: cleanup all this - avsm */
		return preg_replace('|<br />(\W*<br />)+|','<br />',preg_replace('|<br>|','<br />',nl2br(trim(preg_replace('%(http|ftp)(://\S+)%', '<a href="\1\2">\1\2</a>', htmlspecialchars($text))))));
	}

	/**
	* Convert a commit-name into whatever the user wants
	* @param commit name
	* @return transformed name 
	*/
	function showAuthorName($name, $fullname=false)
	{
		if(!isset($GLOBALS['cvsusers']))
		{
			$cvsusers = $GLOBALS['CVS']->parseCVSUsers();
		}
		else
		{
			$cvsusers = $GLOBALS['cvsusers'];
		}

		if(is_array($cvsusers) && isset($cvsusers[$name]))
		{
			return '<a href="mailto:'.$cvsusers[$name]['mail'].'">'.($fullname?$cvsusers[$name]['name']:$name).'</a>'.($fullname?" <i>($name)</i>":'');
		}
		else
		{
			return $name;
		}
	}

	/**
	* Output an error page with relevant HTTP error headers
	*
	* @param errcode The HTTP error number and text
	* @param errmsg The verbose error message to be displayed
	*/
	function fatal($errcode, $errmsg)
	{
		//header("Status: $errcode");
		include($GLOBALS['conf']['paths']['templates'].'/error_page.tpl');
		$GLOBALS['phpgw']->common->phpgw_footer();
		$GLOBALS['phpgw']->common->phpgw_exit();
	}

	/**
	* Given a return object from a CVSLib call, make sure
	* that it's not a CVSLib_Error object.
	* @param e Return object from a CVSLib call
	*/
	function checkError($e)
	{
		if(is_object($e) && $e->id()==CVSLIB_ERROR)
		{
			fatal($e->error_header(), $e->error_body());
		}
	}

	function repositories()
	{
		$arr = array();
		@reset($GLOBALS['cvsroots']);
		while(list($key,$val) = @each($GLOBALS['cvsroots']))
		{
			if($cvsroot != $val['location'])
			{
				$arg = (($GLOBALS['defaultActs']['rt'] == $key)?'':$key);
				$arr[] = '<b><a href="' . $GLOBALS['phpgw']->link('/chora/cvs.php','rt=' . $arg).'">'.$val['name'].'</a></b>';
			}
		}

		if(sizeof($arr))
		{
			return 'Other Repositories: '.implode(' , ', $arr);
		}
		else
		{
			return '';
		}
	}

	function url($script, $uri='', $args='', $anchor='')
	{
		$scriptPath = ereg_replace('phpgroupware','',$GLOBALS['scriptPath']);
		$url = $scriptPath . '/' . $script . '.php' . '/' . $uri;
		$arglist = array_merge(differingVars(), $args);
		$argarr = array();

		while(list($key, $val)=each($arglist))
		{
			if($val)
			{
				$argarr[] = $key . '=' . $val;
			}
		}
		if(sizeof($argarr)>0)
		{
			$url = $url . '?' . implode('&',$argarr);
			$glue = '&';
		}
		else
		{
			$glue = '?';
		}

		if(!empty($implodedQuery))
		{
			$url = $url . $glue . $implodedQuery;
		}

		if(!empty($anchor))
		{
			$url .= '#' . $anchor;
		}

		$url = preg_replace('|/\?|','?',$url);
		$url = htmlspecialchars(preg_replace('|/+|','/',$url));
		$url = $GLOBALS['phpgw']->link($url);
		$url = ereg_replace("\?sessionid","&sessionid",$url);
		return $url; 
	}
 
	function graphic($name)
	{
		//global $scriptPath;
		$imgpath = $GLOBALS['phpgw']->common->get_image_path('chora');
		return $imgpath . '/' . $name; 
	}
?>
