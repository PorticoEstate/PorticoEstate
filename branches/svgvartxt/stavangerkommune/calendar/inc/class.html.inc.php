<?php
	/**************************************************************************\
	* phpGroupWare - HTML creation class                                       *
	* http://www.phpgroupware.org                                              *
	* Written by Ralf Becker <RalfBecker@outdoor-training.de>                  *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

class calendar_html
{
	var $user_agent;
	var $ua_version;

	/**
	* @constructor
	*/
	function __construct()
	{
		//This should really be handled in API browser class - not the html class
		//Sigurd: Does not look like this is used
/*		if ( preg_match('/compatible; ([a-z_]+)[/ ]+([0-9.]+)/i', phpgw::get_var('HTTP_USER_AGENT', 'string', 'SERVER'), $parts))
		{
			preg_match('/^([a-z_]+)/([0-9.]+)/i', phpgw::get_var('HTTP_USER_AGENT', 'string', 'SERVER'), $parts);
		}
		list(,$this->user_agent,$this->ua_version) = $parts;
		$this->user_agent = strtolower($this->user_agent);
*/		
		//echo "<p>HTTP_USER_AGENT='{$_SERVER['HTTP_USER_AGENT']}', UserAgent: '{$this->user_agent}', Version: '{$this->ua_version}'</p>\n";
	}

	/**
	* Function:		Allows to show and select one item from an array
	*	Parameters:		$name		string with name of the submitted var which holds the key of the selected item form array
	*						$key		key(s) of already selected item(s) from $arr, eg. '1' or '1,2' or array with keys
	*						$arr		array with items to select, eg. $arr = array ( 'y' => 'yes','n' => 'no','m' => 'maybe');
	*						$no_lang	if !$no_lang send items through lang()
	*						$options	additional options (e.g. 'multiple')
	* On submit		$XXX		is the key of the selected item (XXX is the content of $name)
	* Returns:			string to set for a template or to echo into html page
	*/
	function select($name, $key, $arr=0,$no_lang=0,$options='',$multiple=0)
	{
		// should be in class common.sbox
		if (!is_array($arr))
		{
			$arr = array('no','yes');
		}
		if (intval($multiple) > 0)
		{
			$options .= ' multiple="multiple" size="'.intval($multiple).'"';
			if (substr($name,-2) != '[]')
			{
				$name .= '[]';
			}
		}
		$out = "<select name=\"$name\" $options>\n";

		if (is_array($key))
		{
			$key = implode(',',$key);
		}
		foreach($arr as $k => $text)
		{
			$out .= '<option value="'.htmlspecialchars($k).'"';

			if("$k" == "$key" || strstr(",$key,",",$k,"))
			{
				$out .= ' selected="selected"';
			}
			$out .= ">" . ($no_lang || $text == '' ? $text : lang($text)) . "</option>\n";
		}
		$out .= "</select>\n";

		return $out;
	}

	/**
	* Create a xhtml DIV
	*
	* @param string $content the content for the DIV
	* @paeam string $options the html options of the div
	* @returns string DIV element
	*/
	function div($content,$options='')
	{
		return "<div $options>\n$content</div>\n";
	}

	/**
	* Create a input type="hidden" xhtml form element/s
	*
	* @param array $vars the variable names for the form element/ss
	* @param mixed $value the value for the form element/s
	* @param bool $ignore_empty should empty values be ignored?
	* @return string hidden form element
	*/
	function input_hidden($vars,$value='',$ignore_empty=True)
	{
		$html = '';
		if (!is_array($vars))
		{
			$vars = array( $vars => $value );
		}
		foreach($vars as $name => $value)
		{
			if (is_array($value))
			{
				$value = serialize($value);
			}
			if (!$ignore_empty || $value && !($name == 'filter' && $value == 'none'))	// dont need to send all the empty vars
			{
				$html .= "<input type=\"hidden\" name=\"$name\" value=\"".htmlspecialchars($value)."\" />\n";
			}
		}
		return $html;
	}

	/**
	* Create a textarea html form element
	*
	* @param string $name the name of the textarea
	* @param string $value the value for the textarea
	* @param string optios the html options of the textarea
	* @returns string textarea element
	*/
	function textarea($name,$value='',$options='' )
	{
		return "<textarea name=\"$name\" $options>".htmlspecialchars($value)."</textarea>\n";
	}

	/**
	* Create a generic html form input element
	*
	* @param string $name the name of the input element
	* @param string $value the value for the form element
	* @param string $type the type of input element to generate
	* @param string options the html options of the input element
	* @returns string form input element
	*/
	function input($name, $value = '', $type = 'text', $options = '')
	{
		if ( strlen($type) )
		{
			$type = 'type="'.$type.'"';
		}
		else
		{
			$type = 'type="text"';
		}
		return "<input $type name=\"$name\" value=\"".htmlspecialchars($value)."\" $options>\n";
	}

	function submit_button($name, $lang, $onClick='', $no_lang=0, $options='', $image='', $app='')
	{
		if ($image != '')
		{
			if (strpos($image,'.')) 
			{
				$image = substr($image,0,strpos($image,'.'));
			}
			if (!($path = $GLOBALS['phpgw']->common->image($app,$image)) &&
				!($path = $GLOBALS['phpgw']->common->image('phpgwapi',$image)))
			{
				$path = $image;		// name may already contain absolut path 
			}
			$image = ' SRC="'.$path.'"';
		}
		if (!$no_lang)
		{
			$lang = lang($lang);
		}
		if (($accesskey = strstr($lang,'&')) && $accesskey[1] != ' ' &&
			(($pos = strpos($accesskey,';')) === False || $pos > 5))
		{
			$lang_u = str_replace('&'.$accesskey[1],'<u>'.$accesskey[1].'</u>',$lang);
			$lang = str_replace('&','',$lang);
			$options = 'ACCESSKEY="'.$accesskey[1].'" '.$options;
		}
		else
		{
			$accesskey = '';
			$lang_u = $lang;
		}
		if ($onClick) $options .= " onClick=\"$onClick\"";

		// <button> is not working in all cases if ($this->user_agent == 'mozilla' && $this->ua_version < 5 || $image)
		{
			return $this->input($name,$lang,$image != '' ? 'image' : 'submit',$options.$image);
		}
		return '<button type="submit" name="'.$name.'" value="'.$lang.'" '.$options.'>'.
			($image != '' ? "<img$image alt=\"$lang\"> " : '').
			($image == '' || $accesskey ? $lang_u : '').'</button>';
	}

	/**
	 * Creates an absolute URI with optional query string (GET variables)
	 *
	 * @param string $uri phpgw-relative URI, may include query / get-vars
	 * $vars array|string $vars query or array ('name' => 'value', ...) with query
	 * link('/index.php?menuaction=infolog.uiinfolog.get_list',array('info_id' => 123))
	 *  = 'http://domain/phpgw-path/index.php?menuaction=infolog.uiinfolog.get_list&info_id=123'
	 * @return string absolute URI ( parsed by $GLOBALS['phpgw']->link )
	 */
	function link($uri, $vars='')
	{
		if (!is_array($vars))
		{
			$vars = explode('&',$vars);
		}
		return $GLOBALS['phpgw']->link($uri, $vars);
	}

	function checkbox($name,$value='')
	{
		return "<input type=\"checkbox\" name=\"$name\" value=\"True\"" .($value ? ' checked="checked"' : '') . ">\n";
	}

	function form($content,$hidden_vars,$url,$url_vars='',$name='',$options='',$method='POST')
	{
		$html = "<form method=\"$method\" ".($name != '' ? "name=\"$name\" " : '')."action=\"".$this->link($url,$url_vars)."\" $options>\n";
		$html .= $this->input_hidden($hidden_vars);

		if ($content) 
		{
			$html .= $content;
			$html .= "</form>\n";
		}
		return $html;
	}

	function form_1button($name,$lang,$hidden_vars,$url,$url_vars='',$form_name='',$method='POST')
	{
		return $this->form($this->submit_button($name,$lang),
			$hidden_vars,$url,$url_vars,$form_name,'',$method);
	}

	/**
	 * creates table from array with rows
	 * abstract the html stuff
	 * @param $rows array with rows, each row is an array of the cols
	 * @param $options options for the table-tag
	 * $rows = array ( '1'  => array( 1 => 'cell1', '.1' => 'colspan=3',
	 *                                2 => 'cell2', 3 => 'cell3', '.3' => 'width="10%"' ),
	 *                 '.1' => 'BGCOLOR="#0000FF"' );
	 * table($rows,'WIDTH="100%"') = '<table WIDTH="100%"><tr><td colspan=3>cell1</td><td>cell2</td><td width="10%">cell3</td></tr></table>'
	 * @return string with html-code of the table
	 */
	function table($rows,$options = '',$no_table_tr=False)
	{
		$html = $no_table_tr ? '' : "<table $options>\n";

		foreach($rows as $key => $row)
		{
			if (!is_array($row))
			{
				continue;					// parameter
			}
			$html .= $no_table_tr && $key == 1 ? '' : "\t<tr ".$rows['.'.$key].">\n";

			foreach($row as $key => $cell)
			{
				if ($key[0] == '.')
				{
					continue;				// parameter
				}
				$table_pos = strpos($cell,'<table');
				$td_pos = strpos($cell,'<td');
				if ($td_pos !== False && ($table_pos === False || $td_pos < $table_pos))
				{
					$html .= $cell;
				}
				else
				{
					$html .= "\t\t<TD ".$row['.'.$key].">$cell</TD>\n";
				}
			}
			$html .= "\t</tr>\n";
		}
		$html .= "</table>\n";

		if ($no_table_tr)
		{
			$html = substr($html,0,-16);
		}
		return $html;
	}
	
	function sbox_submit( $sbox,$no_script=0 )
	{
		$html = str_replace('<select','<select onChange="this.form.submit()" ',
								  $sbox);
		if ($no_script)
		{
			$html .= '<noscript>'.$this->submit_button('send','>').'</noscript>';
		}
		return $html;
	}

	function image( $app,$name,$title='',$options='' )
	{
		if (strstr($name,'.') === False)
		{
			$name .= '.png';
		}
		if (!($path = $GLOBALS['phpgw']->common->image($app,$name)))
		{
			$path = $name;		// name may already contain absolut path
		}
		if (!@is_readable($_SERVER['DOCUMENT_ROOT'] . $path))
		{
			return $title;
		}
		if ($title)
		{
			$options .= " alt=\"".htmlspecialchars($title).'"';
		}
		return "<img src=\"$path\" $options />";
	}

	function a_href( $content,$url,$vars='',$options='')
	{
		if (!strstr($url,'/') && count(explode('.',$url)) == 3)
		{
			$url = "/index.php?menuaction=$url";
		}
		if (is_array($url))
		{
			$vars = $url;
			$url = '/index.php';
		}
		return '<a href="'.$this->link($url,$vars).'" '.$options.'>'.$content.'</a>';
	}

	function bold($content)
	{
		return "<strong>{$content}</strong>";
	}

	function italic($content)
	{
		return "<em>{$content}</em>";
	}

	//FIXME Deprecated Tag! use div with hr class? &nbsp; 1px high and $width wide? - skwashd nov2005
	function hr($width,$options='')
	{
		if ($width)
		{
			$options .= " width=\"$width\"";
		}
		return "<hr $options>\n";
	}

	/**
	 * formats option-string for most of the above functions
	 *
	 * @internal TODO look at if we still need this - most of this should be handled by CSS - skwashd nov2005
	 * @param $options String (or Array) with option-values eg. '100%,,1'
	 * @param $names String (or Array) with the option-names eg. 'WIDTH,HEIGHT,BORDER'
	 * formatOptions('100%,,1','WIDTH,HEIGHT,BORDER') = ' WIDTH="100%" BORDER="1"'
	 * @return option string
	 */
	function formatOptions($options, $names)
	{
		if (!is_array($options))
		{
			$options = explode(',',$options);
		}
		
		if (!is_array($names))
		{
			$names   = explode(',',$names);
		}

		while (list($n,$val) = each($options))
			if ($val != '' && $names[$n] != '')
				$html .= ' '.$names[$n].'="'.$val.'"';

		return $html;
	}

	/**
	 * Create the required CSS style definitiokn in a style tag beeded for nextmatch row-colors
	 *
	 * @return the classes 'th' = nextmatch header, 'row_on'+'row_off' = alternating rows
	 */
	function themeStyles()
	{
		return $this->style($this->theme2css());
	}

	/**
	 * returns simple stylesheet for nextmatch row-colors
	 *
	 * @deprecated
	 * @internal FIXME As we are moving to full CSS this is redundant and should be dropped! - skwashd nov2005
	 *
	 * @return the classes 'th' = nextmatch header, 'row_on'+'row_off' = alternating rows
	 */
	function theme2css()
	{
		return 
			".th { background-color: {$GLOBALS['phpgw_info']['theme']['th_bg']}; font-weight: bold; }\n".
			".row_on,.th_bright { background-color: {$GLOBALS['phpgw_info']['theme']['row_on']}; }\n".
			".row_off { background-color: {$GLOBALS['phpgw_info']['theme']['row_off']}; }\n";
	}

	/**
	* Create and populate a html style tag
	*
	* @param string $styles the styles to be contained within the style tag
	* @returns a html style tag
	*/
	function style($styles)
	{
		return $styles ? "<style type=\"text/css\">\n<!--\n$styles\n-->\n</style>" : '';
	}

	/**
	* Create a label for a html form input element
	*
	* @internal TODO add the ability to highlight the shortcut key - skwashd nov2005
	* @param string $content the contents of the label
	* @param string $id the unique ID for the label
	* @param string $accesskey the keyboard shortcut for the label
	* @param string $options the html options for the label
	* @return string the label as html
	*/
	function label($content, $id, $accesskey='', $options='')
	{
		$id = " for=\"$id\"";
		if ($accesskey != '')
		{
			$accesskey = " accesskey=\"$accesskey\"";
		}
		return "<label$id$accesskey $options>$content</label>";
	}
}
