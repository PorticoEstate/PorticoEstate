<?php
/**
 * eGroupWare API: generates html with methods representing html-tags or higher widgets
 *
 * @link http://www.egroupware.org
 * @author Ralf Becker <RalfBecker-AT-outdoor-training.de> complete rewrite in 6/2006 and earlier modifications
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
 * @author RalfBecker-AT-outdoor-training.de
 * @copyright 2001-2008 by RalfBecker@outdoor-training.de
 * @package api
 * @subpackage html
 * @version $Id$
 */

/**
 * Generates html with methods representing html-tags or higher widgets
 *
 * The class has only static methods now, so there's no need to instanciate the object anymore!
 *
 */
class html
{
	/**
	 * user-agent: 'mozilla','msie','konqueror', 'safari', 'opera'
	 * @var string
	 */
	static $user_agent;
	/**
	 * version of user-agent as specified by browser
	 * @var string
	 */
	static $ua_version;
	/**
	 * what attribute to use for the title of an image: 'title' for everything but netscape4='alt'
	 * @var string
	 */
	static $netscape4;
	static private $prefered_img_title;
	/**
	 * charset used by the page, as returned by $GLOBALS['phpgw']->translation->charset()
	 * @var string
	 */
	static $charset;
	/**
	 * URL (NOT path) of the js directory in the api
	 * @var string
	 */
	static $api_js_url;
	/**
	 * do we need to set the wz_tooltip class, to be included at the end of the page
	 * @var boolean
	 */
	static private $wz_tooltip_included = False;

	/**
	 * initialise our static vars
	 */
	static function _init_static()
	{
		// should be Ok for all HTML 4 compatible browsers
		if (!preg_match('/(Safari)\/([0-9.]+)/i',$_SERVER['HTTP_USER_AGENT'],$parts) &&
			!preg_match('/compatible; ([a-z_]+)[\/ ]+([0-9.]+)/i',$_SERVER['HTTP_USER_AGENT'],$parts))
		{
			preg_match('/^([a-z_]+)\/([0-9.]+)/i',$_SERVER['HTTP_USER_AGENT'],$parts);
		}
		list(,self::$user_agent,self::$ua_version) = $parts;
		self::$user_agent = strtolower(self::$user_agent);

		self::$netscape4 = self::$user_agent == 'mozilla' && self::$ua_version < 5;
		self::$prefered_img_title = self::$netscape4 ? 'alt' : 'title';
		//echo "<p>HTTP_USER_AGENT='$_SERVER[HTTP_USER_AGENT]', UserAgent: 'self::$user_agent', Version: 'self::$ua_version', img_title: 'self::$prefered_img_title'</p>\n";

		self::$charset = 'utf-8';
		self::$api_js_url = $GLOBALS['phpgw_info']['server']['webserver_url'].'/felamimail/js';
	}

	/**
	* Created an input-field with an attached color-picker
	*
	* Please note: it need to be called before the call to phpgw_header() !!!
	*
	* @param string $name the name of the input-field
	* @param string $value the actual value for the input-field, default ''
	* @param string $title tooltip/title for the picker-activation-icon
	* @return string the html
	*/
	static function inputColor($name,$value='',$title='')
	{
		$id = str_replace(array('[',']'),array('_',''),$name).'_colorpicker';
		$onclick = "javascript:window.open('".self::$api_js_url.'/colorpicker/select_color.html?id='.urlencode($id)."&color='+document.getElementById('$id').value,'colorPicker','width=240,height=187,scrollbars=no,resizable=no,toolbar=no');";
		return '<input type="text" name="'.$name.'" id="'.$id.'" value="'.self::htmlspecialchars($value).'" /> '.
			'<a href="#" onclick="'.$onclick.'">'.
			'<img src="'.self::$api_js_url.'/colorpicker/ed_color_bg.gif'.'"'.($title ? ' title="'.self::htmlspecialchars($title).'"' : '')." /></a>";
	}

	/**
	* Handles tooltips via the wz_tooltip class from Walter Zorn
	*
	* Note: The wz_tooltip.js file gets automaticaly loaded at the end of the page
	*
	* @param string/boolean $text text or html for the tooltip, all chars allowed, they will be quoted approperiate
	*	Or if False the content (innerHTML) of the element itself is used.
	* @param boolean $do_lang (default False) should the text be run though lang()
	* @param array $options param/value pairs, eg. 'TITLE' => 'I am the title'. Some common parameters:
	*  title (string) gives extra title-row, width (int,'auto') , padding (int), above (bool), bgcolor (color), bgimg (URL)
	*  For a complete list and description see http://www.walterzorn.com/tooltip/tooltip_e.htm
	* @return string to be included in any tag, like '<p'.html::tooltip('Hello <b>Ralf</b>').'>Text with tooltip</p>'
	*/
	static function tooltip($text,$do_lang=False,$options=False)
	{
		if (!self::$wz_tooltip_included)
		{
			if (strpos($GLOBALS['phpgw_info']['flags']['need_footer'],'wz_tooltip')===false)
			{
				$GLOBALS['phpgw_info']['flags']['need_footer'] .= '<script language="JavaScript" type="text/javascript" src="'.self::$api_js_url.'/wz_tooltip/wz_tooltip.js"></script>'."\n";
			}
			self::$wz_tooltip_included = True;
		}
		if ($do_lang) $text = lang($text);

		$opt_out = 'this.T_WIDTH = 200;';
		if (is_array($options))
		{
			foreach($options as $option => $value)
			{
				$opt_out .= 'this.T_'.strtoupper($option).'='.(is_numeric($value)?$value:"'".str_replace(array("'",'"'),array("\\'",'&quot;'),$value)."'").'; ';

			}
		}
		if ($text === False) return ' onmouseover="'.$opt_out.'return escape(this.innerHTML);"';

		return ' onmouseover="'.$opt_out.'return escape(\''.str_replace(array("\n","\r","'",'"'),array('','',"\\'",'&quot;'),$text).'\')"';
	}

	/**
	 * activates URLs in a text, URLs get replaced by html-links
	 *
	 * @param string $content text containing URLs
	 * @return string html with activated links
	 */
	static function activate_links($content)
	{
		if (!$content || strlen($content) < 20) return $content;	// performance

		// Exclude everything which is already a link
		$NotAnchor = '(?<!"|href=|href\s=\s|href=\s|href\s=)';

		// spamsaver emailaddress
		$result = preg_replace('/'.$NotAnchor.'mailto:([a-z0-9._-]+)@([a-z0-9_-]+)\.([a-z0-9._-]+)/i',
			'<a href="#" onclick="document.location=\'mai\'+\'lto:\\1\'+unescape(\'%40\')+\'\\2.\\3\'; return false;">\\1 AT \\2 DOT \\3</a>',
			$content);

		//  First match things beginning with http:// (or other protocols)
		$Protocol = '(http:\/\/|(ftp:\/\/|https:\/\/))';	// only http:// gets removed, other protocolls are shown
		$Domain = '([\w-]+\.[\w-.]+)';
		$Subdir = '([\w\-\.,@?^=%&;:\/~\+#]*[\w\-\@?^=%&\/~\+#])?';
		$Expr = '/' . $NotAnchor . $Protocol . $Domain . $Subdir . '/i';

		$result = preg_replace( $Expr, "<a href=\"$0\" target=\"_blank\">$2$3$4</a>", $result );

		//  Now match things beginning with www.
		$NotHTTP = '(?<!:\/\/|" target=\"_blank\">)';	//	avoid running again on http://www links already handled above
		$Domain = 'www(\.[\w-.]+)';
		$Subdir = '([\w\-\.,@?^=%&:\/~\+#]*[\w\-\@?^=%&\/~\+#])?';
		$Expr = '/' . $NotAnchor . $NotHTTP . $Domain . $Subdir . '/i';

		return preg_replace( $Expr, "<a href=\"http://$0\" target=\"_blank\">$0</a>", $result );
	}

	/**
	 * escapes chars with special meaning in html as entities
	 *
	 * Allows to use and char in the html-output and prefents XSS attacks.
	 * Some entities are allowed and get NOT escaped:
	 * - &# some translations (AFAIK the arabic ones) need this
	 * - &nbsp; &lt; &gt; for convinience
	 *
	 * @param string $str string to escape
	 * @return string
	 */
	static function htmlspecialchars($str)
	{
		// add @ by lkneschke to supress warning about unknown charset
		$str = @htmlspecialchars($str,ENT_COMPAT,self::$charset);

		// we need '&#' unchanged, so we translate it back
		$str = str_replace(array('&amp;#','&amp;nbsp;','&amp;lt;','&amp;gt;'),array('&#','&nbsp;','&lt;','&gt;'),$str);

		return $str;
	}

	/**
	 * allows to show and select one item from an array
	 *
	 * @param string $name	string with name of the submitted var which holds the key of the selected item form array
	 * @param string/array $key key(s) of already selected item(s) from $arr, eg. '1' or '1,2' or array with keys
	 * @param array $arr array with items to select, eg. $arr = array ( 'y' => 'yes','n' => 'no','m' => 'maybe');
	 * @param boolean $no_lang NOT run the labels of the options through lang(), default false=use lang()
	 * @param string $options additional options (e.g. 'width')
	 * @param int $multiple number of lines for a multiselect, default 0 = no multiselect, < 0 sets size without multiple
	 * @return string to set for a template or to echo into html page
	 */
	static function select($name, $key, $arr=0,$no_lang=false,$options='',$multiple=0)
	{
		if (!is_array($arr))
		{
			$arr = array('no','yes');
		}
		if ((int)$multiple > 0)
		{
			$options .= ' multiple="1" size="'.(int)$multiple.'"';
			if (substr($name,-2) != '[]')
			{
				$name .= '[]';
			}
		}
		elseif($multiple < 0)
		{
			$options .= ' size="'.abs($multiple).'"';
		}
		$out = "<select name=\"$name\" $options>\n";

		if (!is_array($key))
		{
			// explode on ',' only if multiple values expected and the key contains just numbers and commas
			$key = $multiple > 0 && preg_match('/^[,0-9]+$/',$key) ? explode(',',$key) : array($key);
		}
		foreach($arr as $k => $data)
		{
			if (!is_array($data) || count($data) == 2 && isset($data['label']) && isset($data['title']))
			{
				$out .= self::select_option($k,is_array($data)?$data['label']:$data,$key,$no_lang,
					is_array($data)?$data['title']:'');
			}
			else
			{
				if (isset($data['lable']))
				{
					$k = $data['lable'];
					unset($data['lable']);
				}
				$out .= '<optgroup label="'.self::htmlspecialchars($no_lang || $k == '' ? $k : lang($k))."\">\n";

				foreach($data as $k => $label)
				{
					$out .= self::select_option($k,is_array($label)?$label['label']:$label,$key,$no_lang,
						is_array($label)?$lable['title']:'');
				}
				$out .= "</optgroup>\n";
			}
		}
		$out .= "</select>\n";

		return $out;
	}

	/**
	 * emulating a multiselectbox using checkboxes
	 *
	 * Unfortunaly this is not in all aspects like a multi-selectbox, eg. you cant select options via javascript
	 * in the same way. Therefor I made it an extra function.
	 *
	 * @param string $name	string with name of the submitted var which holds the key of the selected item form array
	 * @param string/array $key key(s) of already selected item(s) from $arr, eg. '1' or '1,2' or array with keys
	 * @param array $arr array with items to select, eg. $arr = array ( 'y' => 'yes','n' => 'no','m' => 'maybe');
	 * @param boolean $no_lang NOT run the labels of the options through lang(), default false=use lang()
	 * @param string $options additional options (e.g. 'width')
	 * @param int $multiple number of lines for a multiselect, default 3
	 * @param boolean $selected_first show the selected items before the not selected ones, default true
	 * @param string $style='' extra style settings like "width: 100%", default '' none
	 * @return string to set for a template or to echo into html page
	 */
	static function checkbox_multiselect($name, $key, $arr=0,$no_lang=false,$options='',$multiple=3,$selected_first=true,$style='')
	{
		//echo "<p align=right>checkbox_multiselect('$name',".print_r($key,true).",".print_r($arr,true).",$no_lang,'$options',$multiple,$selected_first,'$style')</p>\n";
		if (!is_array($arr))
		{
			$arr = array('no','yes');
		}
		if ((int)$multiple <= 0) $multiple = 1;

		if (substr($name,-2) != '[]')
		{
			$name .= '[]';
		}
		$base_name = substr($name,0,-2);

		if (!is_array($key))
		{
			// explode on ',' only if multiple values expected and the key contains just numbers and commas
			$key = preg_match('/^[,0-9]+$/',$key) ? explode(',',$key) : array($key);
		}
		$html = '';
		$options_no_id = preg_replace('/id="[^"]+"/i','',$options);

		if ($selected_first)
		{
			$selected = $not_selected = array();
			foreach($arr as $val => $label)
			{
				if (in_array($val,$key,!$val))
				{
					$selected[$val] = $label;
				}
				else
				{
					$not_selected[$val] = $label;
				}
			}
			$arr = $selected + $not_selected;
		}
		foreach($arr as $val => $label)
		{
			if (is_array($label))
			{
				$title = $label['title'];
				$label = $label['label'];
			}
			else
			{
				$title = '';
			}
			if ($label && !$no_lang) $label = lang($label);
			if ($title && !$no_lang) $title = lang($title);

			if (strlen($label) > $max_len) $max_len = strlen($label);

			$html .= self::label(self::checkbox($name,in_array($val,$key),$val,$options_no_id.
				' id="'.$base_name.'['.$val.']'.'"').self::htmlspecialchars($label),
				$base_name.'['.$val.']','',($title ? 'title="'.self::htmlspecialchars($title).'" ':''))."<br />\n";
		}
		if ($style && substr($style,-1) != ';') $style .= '; ';
		if (strpos($style,'height')===false) $style .= 'height: '.(1.7*$multiple).'em; ';
		if (strpos($style,'width')===false)  $style .= 'width: '.(4+$max_len*($max_len < 15 ? 0.65 : 0.6)).'em; ';
		$style .= 'background-color: white; overflow: auto; border: lightgray 2px inset; text-align: left;';

		return self::div($html,$options,'',$style);
	}

	/**
	 * generates an option-tag for a selectbox
	 *
	 * @param string $value value
	 * @param string $label label
	 * @param mixed $selected value or array of values of options to mark as selected
	 * @param boolean $no_lang NOT running the label through lang(), default false=use lang()
	 * @return string html
	 */
	static function select_option($value,$label,$selected,$no_lang=0,$title='')
	{
		// the following compares strict as strings, to archive: '0' == 0 != ''
		// the first non-strict search via array_search, is for performance reasons, to not always search the whole array with php
		if (($found = ($key = array_search($value,$selected)) !== false) && (string) $value !== (string) $selected[$key])
		{
			$found = false;
			foreach($selected as $sel)
			{
				if ($found = (((string) $value) === ((string) $selected[$key]))) break;
			}
		}
		return '<option value="'.self::htmlspecialchars($value).'"'.($found  ? ' selected="selected"' : '') .
			($title ? ' title="'.self::htmlspecialchars($no_lang ? $title : lang($title)).'"' : '') . '>'.
			self::htmlspecialchars($no_lang || $label == '' ? $label : lang($label)) . "</option>\n";
	}

	/**
	 * generates a div-tag
	 *
	 * @param string $content of a div, or '' to generate only the opening tag
	 * @param string $options to include in the tag, default ''=none
	 * @param string $class css-class attribute, default ''=none
	 * @param string $style css-styles attribute, default ''=none
	 * @return string html
	 */
	static function div($content,$options='',$class='',$style='')
	{
		if ($class) $options .= ' class="'.$class.'"';
		if ($style) $options .= ' style="'.$style.'"';

		return "<div $options>\n".($content ? "$content</div>\n" : '');
	}

	/**
	 * generate one or more hidden input tag(s)
	 *
	 * @param array/string $vars var-name or array with name / value pairs
	 * @param string $value value if $vars is no array, default ''
	 * @param boolean $ignore_empty if true all empty, zero (!) or unset values, plus filer=none
	 * @param string html
	 */
	static function input_hidden($vars,$value='',$ignore_empty=True)
	{
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
				$html .= "<input type=\"hidden\" name=\"$name\" value=\"".self::htmlspecialchars($value)."\" />\n";
			}
		}
		return $html;
	}

	/**
	 * generate a textarea tag
	 *
	 * @param string $name name attr. of the tag
	 * @param string $value default
	 * @param boolean $ignore_empty if true all empty, zero (!) or unset values, plus filer=none
	 * @param string html
	 */
	static function textarea($name,$value='',$options='' )
	{
		return "<textarea name=\"$name\" $options>".self::htmlspecialchars($value)."</textarea>\n";
	}

	/**
	 * Checks if HTMLarea (or an other richtext editor) is availible for the used browser
	 *
	 * @return boolean
	 */
	static function htmlarea_availible()
	{
		require_once(PHPGW_INCLUDE_ROOT.'/felamimail/js/fckeditor/fckeditor.php');

		// use FCKeditor's own check
		return FCKeditor_IsCompatibleBrowser();

		return false;
	}

	/**
	 * compability static function for former used htmlarea. Please use static function fckeditor now!
	 *
	 * creates a textarea inputfield for the htmlarea js-widget (returns the necessary html and js)
	 */
	static function htmlarea($name,$content='',$style='',$base_href='',$plugins='',$custom_toolbar='',$set_width_height_in_config=false)
	{
		if (!self::htmlarea_availible())
		{
			return self::textarea($name,$content,'style="'.$style.'"');
		}
		return self::fckEditor($name, $content, 'extended', array('toolbar_expanded' =>'true'), '400px', '100%', $base_href);
	}

	/**
	* this static function is a wrapper for fckEditor to create some reuseable layouts
	*
	* @param string $_name name and id of the input-field
	* @param string $_content of the tinymce (will be run through htmlspecialchars !!!), default ''
	* @param string $_mode display mode of the tinymce editor can be: simple, extended or advanced
	* @param array  $_options (toolbar_expanded true/false)
	* @param string $_height='400px'
	* @param string $_width='100%'
	* @param string $base_href='' if passed activates the browser for image at absolute path passed
	* @return string the necessary html for the textarea
	*/
	static function fckEditor($_name, $_content, $_mode, $_options=array('toolbar_expanded' =>'true'), $_height='400px', $_width='100%',$_base_href='')
	{
		if (!self::htmlarea_availible() || $_mode == 'ascii')
		{
			return self::textarea($_name,$_content,'style="width: '.$_width.'; height: '.$_height.';"');
		}
		include_once(PHPGW_INCLUDE_ROOT."/felamimail/js/fckeditor/fckeditor.php");

		$oFCKeditor = new FCKeditor($_name) ;
		$oFCKeditor->BasePath	= $GLOBALS['phpgw_info']['server']['webserver_url'].'/felamimail/js/fckeditor/' ;
		$oFCKeditor->Config['CustomConfigurationsPath'] = $oFCKeditor->BasePath . 'fckeditor.egwconfig.js' ;
		$oFCKeditor->Value	= $_content;
		$oFCKeditor->Width	= str_replace('px','',$_width);	// FCK adds px if width contains no %
		$oFCKeditor->Height	= str_replace('px','',$_height);

		// by default switch all browsers and uploads off
		$oFCKeditor->Config['LinkBrowser'] = $oFCKeditor->Config['LinkUpload'] = false;
		$oFCKeditor->Config['FlashBrowser'] = $oFCKeditor->Config['FlashUpload'] = false;
		$oFCKeditor->Config['ImageBrowser'] = $oFCKeditor->Config['ImageUpload'] = false;

		// Activate the image browser+upload, if $_base_href exists and is browsable by the webserver
		if ($_base_href && is_dir($_SERVER['DOCUMENT_ROOT'].$_base_href) && file_exists($_SERVER['DOCUMENT_ROOT'].$_base_href.'/.'))
		{
			// Only images for now
			if (substr($_base_href,-1) != '/') $_base_href .= '/' ;
			// store the path and application in the session, to make sure it can't be called with arbitrary pathes
			$GLOBALS['phpgw']->session->appsession($_base_href,'FCKeditor',$GLOBALS['phpgw_info']['flags']['currentapp']);

			$oFCKeditor->Config['ImageBrowserURL'] = $oFCKeditor->BasePath.'editor/filemanager/browser/default/browser.html?ServerPath='.$_base_href.'&Type=Image&Connector='.$oFCKeditor->BasePath.'editor/filemanager/connectors/php/connector.php';
			$oFCKeditor->Config['ImageBrowser'] = true;
			$oFCKeditor->Config['ImageUpload'] = is_writable($_SERVER['DOCUMENT_ROOT'].$_base_href);
		}
		// By default the editor start expanded
		if ($_options['toolbar_expanded'] == 'false')
		{
			$oFCKeditor->Config['ToolbarStartExpanded'] = $_options['toolbar_expanded'];
		}
		// switching the encoding as html entities off, as we correctly handle charsets and it messes up the wiki totally
		$oFCKeditor->Config['ProcessHTMLEntities'] = false;
		// Now setting the admin settings
		$spell = '';
		if (isset($GLOBALS['phpgw_info']['server']['enabled_spellcheck']))
		{
			$spell = '_spellcheck';
			$oFCKeditor->Config['SpellChecker'] = 'SpellerPages';
			$oFCKeditor->Config['SpellerPagesServerScript'] = 'server-scripts/spellchecker.php?enabled=1';
			if (isset($GLOBALS['phpgw_info']['server']['aspell_path']))
			{
				$oFCKeditor->Config['SpellerPagesServerScript'] .= '&aspell_path='.$GLOBALS['phpgw_info']['server']['aspell_path'];
			}
			if (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['spellchecker_lang']))
			{
				$oFCKeditor->Config['SpellerPagesServerScript'] .= '&spellchecker_lang='.$GLOBALS['phpgw_info']['user']['preferences']['common']['spellchecker_lang'];
			}
			else
			{
				$oFCKeditor->Config['SpellerPagesServerScript'] .= '&spellchecker_lang='.$GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
			}
			$oFCKeditor->Config['FirefoxSpellChecker'] = false;
		}
		// Now setting the user preferences
		if (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['rte_enter_mode']))
		{
			$oFCKeditor->Config['EnterMode'] = $GLOBALS['phpgw_info']['user']['preferences']['common']['rte_enter_mode'];
		}
		if (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['rte_skin']))
		{
			$oFCKeditor->Config['SkinPath'] = $oFCKeditor->BasePath.'editor/skins/'.$GLOBALS['phpgw_info']['user']['preferences']['common']['rte_skin'].'/';
		}

		switch($_mode) {
			case 'simple':
				$oFCKeditor->ToolbarSet = 'egw_simple'.$spell;
				$oFCKeditor->Config['ContextMenu'] = false;
				break;

			default:
			case 'extended':
				$oFCKeditor->ToolbarSet = 'egw_extended'.$spell;
				break;

			case 'advanced':
				$oFCKeditor->ToolbarSet = 'egw_advanced'.$spell;
				break;
		}
		return $oFCKeditor->CreateHTML();
	}

	/**
	* this static function is a wrapper for tinymce to create some reuseable layouts
	*
	* Please note: if you did not run init_tinymce already you this static function need to be called before the call to phpgw_header() !!!
	*
	* @param string $_name name and id of the input-field
	* @param string $_mode display mode of the tinymce editor can be: simple, extended or advanced
	* @param string $_content='' of the tinymce (will be run through htmlspecialchars !!!), default ''
	* @param string $style='' initial css for the style attribute
	* @param string $base_href=''
	* @return string the necessary html for the textarea
	*/
	static function fckEditorQuick($_name, $_mode, $_content='', $_height='400px', $_width='100%')
	{
		if (!self::htmlarea_availible() || $_mode == 'ascii')
		{
			return "<textarea name=\"$_name\" style=\"width:100%; height:400px; border:0px;\">$_content</textarea>";
		}
		else
		{
			return self::fckEditor($_name, $_content, $_mode, array(), $_height, $_width);
		}
	}

	/**
	 * represents html's input tag
	 *
	 * @param string $name name
	 * @param string $value default value of the field
	 * @param string $type type, default ''=not specified = text
	 * @param string $options attributes for the tag, default ''=none
	 */
	static function input($name,$value='',$type='',$options='' )
	{
		if ($type)
		{
			$type = 'type="'.$type.'"';
		}
		return "<input $type name=\"$name\" value=\"".self::htmlspecialchars($value)."\" $options />\n";
	}

	/**
	 * represents html's button (input type submit or input type button or image)
	 *
	 * @param string $name name
	 * @param string $label label of the button
	 * @param string $onClick javascript to call, when button is clicked
	 * @param boolean $no_lang NOT running the label through lang(), default false=use lang()
	 * @param string $options attributes for the tag, default ''=none
	 * @param string $image to show instead of the label, default ''=none
	 * @param string $app app to search the image in
	 * @param string $buttontype which type of html button (button|submit), default ='submit'
	 * @return string html
	 */
	static function submit_button($name,$label,$onClick='',$no_lang=false,$options='',$image='',$app='phpgwapi', $buttontype='submit')
	{
		// workaround for idots and IE button problem (wrong cursor-image)
		if (self::$user_agent == 'msie')
		{
			$options .= ' style="cursor: pointer;"';
		}
		if ($image != '')
		{
			$image = str_replace(array('.gif','.GIF','.png','.PNG'),'',$image);

			if (!($path = $GLOBALS['phpgw']->common->image($app,$image)))
			{
				$path = $image;		// name may already contain absolut path
			}
			$image = ' src="'.$path.'"';
		}
		if (!$no_lang)
		{
			$label = lang($label);
		}
		if (($accesskey = @strstr($label,'&')) && $accesskey[1] != ' ' &&
			(($pos = strpos($accesskey,';')) === false || $pos > 5))
		{
			$label_u = str_replace('&'.$accesskey[1],'<u>'.$accesskey[1].'</u>',$label);
			$label = str_replace('&','',$label);
			$options = 'accesskey="'.$accesskey[1].'" '.$options;
		}
		else
		{
			$accesskey = '';
			$label_u = $label;
		}
		if ($onClick) $options .= ' onclick="'.str_replace('"','\\"',$onClick).'"';

		// <button> is not working in all cases if (self::$user_agent == 'mozilla' && self::$ua_version < 5 || $image)
		{
			return self::input($name,$label,$image != '' ? 'image' : $buttontype,$options.$image);
		}
		return '<button type="'.$buttontype.'" name="'.$name.'" value="'.$label.'" '.$options.' />'.
			($image != '' ? /*self::image($app,$image,$label,$options)*/'<img'.$image.' '.self::$prefered_img_title.'="'.$label.'"> ' : '').
			($image == '' || $accesskey ? $label_u : '').'</button>';
	}

	/**
	 * creates an absolut link + the query / get-variables
	 *
	 * Example link('/index.php?menuaction=infolog.uiinfolog.get_list',array('info_id' => 123))
	 *	gives 'http://domain/phpgw-path/index.php?menuaction=infolog.uiinfolog.get_list&info_id=123'
	 *
	 * @param string $url phpgw-relative link, may include query / get-vars
	 * @param array/string $vars query or array ('name' => 'value', ...) with query
	 * @return string absolut link already run through $phpgw->link
	 */
	static function link($url,$vars='')
	{
		//echo "<p>html::link(url='$url',vars='"; print_r($vars); echo "')</p>\n";
		if (!is_array($vars))
		{
			parse_str($vars,$vars);
		}
		list($url,$v) = explode('?',$url);	// url may contain additional vars
		if ($v)
		{
			parse_str($v,$v);
			$vars += $v;
		}
		return $GLOBALS['phpgw']->link($url,$vars);
	}

	/**
	 * represents html checkbox
	 *
	 * @param string $name name
	 * @param boolean $checked box checked on display
	 * @param string $value value the var should be set to, default 'True'
	 * @param string $options attributes for the tag, default ''=none
	 * @return string html
	 */
	static function checkbox($name,$checked=false,$value='True',$options='')
	{
		return '<input type="checkbox" name="'.$name.'" value="'.self::htmlspecialchars($value).'"' .($checked ? ' checked="1"' : '') . "$options />\n";
	}

	/**
	 * represents a html form
	 *
	 * @param string $content of the form, if '' only the opening tag gets returned
	 * @param array $hidden_vars array with name-value pairs for hidden input fields
	 * @param string $url eGW relative URL, will be run through the link function, if empty the current url is used
	 * @param string/array $url_vars parameters for the URL, send to link static function too
	 * @param string $name name of the form, defaul ''=none
	 * @param string $options attributes for the tag, default ''=none
	 * @param string $method method of the form, default 'POST'
	 * @return string html
	 */
	static function form($content,$hidden_vars,$url,$url_vars='',$name='',$options='',$method='POST')
	{
		$url = $url ? self::link($url,$url_vars) : $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
		$html = "<form method=\"$method\" ".($name != '' ? "name=\"$name\" " : '')."action=\"$url\" $options>\n";
		$html .= self::input_hidden($hidden_vars);

		if ($content)
		{
			$html .= $content;
			$html .= "</form>\n";
		}
		return $html;
	}

	/**
	 * represents a html form with one button
	 *
	 * @param string $name name of the button
	 * @param string $label label of the button
	 * @param array $hidden_vars array with name-value pairs for hidden input fields
	 * @param string $url eGW relative URL, will be run through the link function
	 * @param string/array $url_vars parameters for the URL, send to link static function too
	 * @param string $options attributes for the tag, default ''=none
	 * @param string $form_name name of the form, defaul ''=none
	 * @param string $method method of the form, default 'POST'
	 * @return string html
	 */
	static function form_1button($name,$label,$hidden_vars,$url,$url_vars='',$form_name='',$method='POST')
	{
		return self::form(self::submit_button($name,$label),$hidden_vars,$url,$url_vars,$form_name,'',$method);
	}

	/**
	 * creates table from array of rows
	 *
	 * abstracts the html stuff for the table creation
	 * Example: $rows = array (
	 *	'1'  => array(
	 *		1 => 'cell1', '.1' => 'colspan=3',
	 *		2 => 'cell2',
	 *		3 => 'cell3', '.3' => 'width="10%"'
	 *	),'.1' => 'BGCOLOR="#0000FF"' );
	 *	table($rows,'width="100%"') = '<table width="100%"><tr><td colspan=3>cell1</td><td>cell2</td><td width="10%">cell3</td></tr></table>'
	 *
	 * @param array $rows with rows, each row is an array of the cols
	 * @param string $options options for the table-tag
	 * @param boolean $no_table_tr dont return the table- and outmost tr-tabs, default false=return table+tr
	 * @return string with html-code of the table
	 */
	static function table($rows,$options = '',$no_table_tr=False)
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
					$html .= "\t\t<td ".$row['.'.$key].">$cell</td>\n";
				}
			}
			$html .= "\t</tr>\n";
		}
		if (!is_array($rows))
		{
			echo "<p>".function_backtrace()."</p>\n";
		}
		$html .= "</table>\n";

		if ($no_table_tr)
		{
			$html = substr($html,0,-16);
		}
		return $html;
	}

	/**
	 * changes a selectbox to submit the form if it gets changed, to be used with the sbox-class
	 *
	 * @param string $sbox html with the select-box
	 * @param boolean $no_script if true generate a submit-button if javascript is off
	 * @return string html
	 */
	static function sbox_submit( $sbox,$no_script=false )
	{
		$html = str_replace('<select','<select onchange="this.form.submit()" ',$sbox);
		if ($no_script)
		{
			$html .= '<noscript>'.self::submit_button('send','>').'</noscript>';
		}
		return $html;
	}

	/**
	 * html-widget showing progessbar with a view div's (html4 only, textual percentage otherwise)
	 *
	 * @param mixed $percent percent-value, gets casted to int
	 * @param string $title title for the progressbar, default ''=the percentage itself
	 * @param string $options attributes for the outmost div (may include onclick="...")
	 * @param string $width width, default 30px
	 * @param string $color color, default '#D00000' (dark red)
	 * @param string $height height, default 5px
	 * @return string html
	 */
	static function progressbar( $percent,$title='',$options='',$width='',$color='',$height='' )
	{
		$percent = (int) $percent;
		if (!$width) $width = '30px';
		if (!$height)$height= '5px';
		if (!$color) $color = '#D00000';
		$title = $title ? self::htmlspecialchars($title) : $percent.'%';

		if (self::$netscape4)
		{
			return $title;
		}
		return '<div class="onlyPrint">'.$title.'</div><div class="noPrint" title="'.$title.'" '.$options.
			' style="height: '.$height.'; width: '.$width.'; border: 1px solid black; padding: 1px; text-align: left;'.
			(@stristr($options,'onclick="') ? ' cursor: pointer;' : '').'">'."\n\t".
			'<div style="height: '.$height.'; width: '.$percent.'%; background: '.$color.';"></div>'."\n</div>\n";
	}

	/**
	 * representates a html img tag, output a picture
	 *
	 * If the name ends with a '%' and the rest is numeric, a progressionbar is shown instead of an image.
	 * The vfs:/ pseudo protocoll allows to access images in the vfs, eg. vfs:/home/ralf/me.png
	 * Instead of a name you specify an array with get-vars, it is passed to eGW's link function.
	 * This way session-information gets passed, eg. $name=array('menuaction'=>'myapp.class.image','id'=>123).
	 *
	 * @param string $app app-name to search the image
	 * @param string/array $name image-name or URL (incl. vfs:/) or array with get-vars
	 * @param string $title tooltip, default '' = none
	 * @param string $options further options for the tag, default '' = none
	 * @return string the html
	 */
	static function image( $app,$name,$title='',$options='' )
	{
		if (substr($name,0,5) == 'vfs:/')	// vfs pseudo protocoll
		{
			$name = egw_vfs::download_url(substr($name,4));
		}
		if (is_array($name))	// menuaction and other get-vars
		{
			$name = $GLOBALS['phpgw']->link('/index.php',$name);
		}
		if ($name[0] == '/' || substr($name,0,7) == 'http://' || substr($name,0,8) == 'https://')
		{
			$url = $name;
		}
		else	// no URL, so try searching the image
		{
			$name = str_replace(array('.gif','.GIF','.png','.PNG'),'',$name);

			if (!($url = $GLOBALS['phpgw']->common->image($app,$name)))
			{
				$url = $name;		// name may already contain absolut path
			}
			if($GLOBALS['phpgw_info']['server']['webserver_url'])
			{
				list(,$path) = explode($GLOBALS['phpgw_info']['server']['webserver_url'],$url);

				if (!is_null($path)) $path = PHPGW_SERVER_ROOT.$path;
			}
			else
			{
				$path = PHPGW_SERVER_ROOT.$url;
			}

			if (is_null($path) || !@is_readable($path))
			{
				// if the image-name is a percentage, use a progressbar
				if (substr($name,-1) == '%' && is_numeric($percent = substr($name,0,-1)))
				{
					return self::progressbar($percent,$title);
				}
				return $title;
			}
		}
		if ($title)
		{
			$options .= ' '.self::$prefered_img_title.'="'.self::htmlspecialchars($title).'"';
		}

		// This block makes pngfix.js useless, adding a check on disable_pngfix to have pngfix.js do its thing
		if (self::$user_agent == 'msie' && self::$ua_version >= 5.5 && substr($url,-4) == '.png' && ($GLOBALS['phpgw_info']['user']['preferences']['common']['disable_pngfix'] || !isset($GLOBALS['phpgw_info']['user']['preferences']['common']['disable_pngfix'])))
		{
			$extra_styles = "display: inline-block; filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='$url',sizingMethod='image'); width: 1px; height: 1px;";
			if (false!==strpos($options,'style="'))
			{
				$options = str_replace('style="','style="'.$extra_styles, $options);
			}
			else
			{
				$options .= ' style="'.$extra_styles.'"';
			}
			return "<span $options></span>";
		}
		return "<img src=\"$url\" $options />";
	}

	/**
	 * representates a html link
	 *
	 * @param string $content of the link, if '' only the opening tag gets returned
	 * @param string $url eGW relative URL, will be run through the link function
	 * @param string/array $vars parameters for the URL, send to link static function too
	 * @param string $options attributes for the tag, default ''=none
	 * @return string the html
	 */
	static function a_href( $content,$url,$vars='',$options='')
	{
		if (is_array($url))
		{
			$vars = $url;
			$url = '/index.php';
		}
		elseif (strpos($url,'/')===false &&
			count(explode('.',$url)) >= 3 &&
			!(strpos($url,'mailto:')!==false ||
			strpos($url,'://')!==false ||
			strpos($url,'javascript:')!==false))
		{
			$url = "/index.php?menuaction=$url";
		}
		if ($url{0} == '/')		// link relative to eGW
		{
			$url = self::link($url,$vars);
		}
		//echo "<p>html::a_href('".htmlspecialchars($content)."','$url',".print_r($vars,True).") = ".self::link($url,$vars)."</p>";
		return '<a href="'.$url.'" '.$options.'>'.$content.'</a>';
	}

	/**
	 * representates a b tab (bold)
	 *
	 * @param string $content of the link, if '' only the opening tag gets returned
	 * @return string the html
	 */
	static function bold($content)
	{
		return '<b>'.$content.'</b>';
	}

	/**
	 * representates a i tab (bold)
	 *
	 * @param string $content of the link, if '' only the opening tag gets returned
	 * @return string the html
	 */
	static function italic($content)
	{
		return '<i>'.$content.'</i>';
	}

	/**
	 * representates a hr tag (horizontal rule)
	 *
	 * @param string $width default ''=none given
	 * @param string $options attributes for the tag, default ''=none
	 * @return string the html
	 */
	static function hr($width='',$options='')
	{
		if ($width) $options .= " width=\"$width\"";

		return "<hr $options />\n";
	}

	/**
	 * formats option-string for most of the above functions
	 *
	 * Example: formatOptions('100%,,1','width,height,border') = ' width="100%" border="1"'
	 *
	 * @param mixed $options String (or Array) with option-values eg. '100%,,1'
	 * @param mixed $names String (or Array) with the option-names eg. 'WIDTH,HEIGHT,BORDER'
	 * @return string with options/attributes
	 */
	static function formatOptions($options,$names)
	{
		if (!is_array($options)) $options = explode(',',$options);
		if (!is_array($names))   $names   = explode(',',$names);

		foreach($options as $n => $val)
		{
			if ($val != '' && $names[$n] != '')
			{
				$html .= ' '.strtolower($names[$n]).'="'.$val.'"';
			}
		}
		return $html;
	}

	/**
	 * returns simple stylesheet (incl. <STYLE> tags) for nextmatch row-colors
	 *
	 * @deprecated  included now always by the framework
	 * @return string classes 'th' = nextmatch header, 'row_on'+'row_off' = alternating rows
	 */
	static function themeStyles()
	{
		return self::style(self::theme2css());
	}

	/**
	 * returns simple stylesheet for nextmatch row-colors
	 *
	 * @deprecated included now always by the framework
	 * @return string classes 'th' = nextmatch header, 'row_on'+'row_off' = alternating rows
	 */
	static function theme2css()
	{
		return ".th { background: ".$GLOBALS['phpgw_info']['theme']['th_bg']."; }\n".
			".row_on,.th_bright { background: ".$GLOBALS['phpgw_info']['theme']['row_on']."; }\n".
			".row_off { background: ".$GLOBALS['phpgw_info']['theme']['row_off']."; }\n";
	}

	/**
	 * html style tag (incl. type)
	 *
	 * @param string $styles css-style definitions
	 * @return string html
	 */
	static function style($styles)
	{
		return $styles ? "<style type=\"text/css\">\n<!--\n$styles\n-->\n</style>" : '';
	}

	/**
	 * html label tag
	 *
	 * @param string $content the label
	 * @param string $id for the for attribute, default ''=none
	 * @param string $accesskey accesskey, default ''=none
	 * @param string $options attributes for the tag, default ''=none
	 * @return string the html
	 */
	static function label($content,$id='',$accesskey='',$options='')
	{
		if ($id != '')
		{
			$id = " for=\"$id\"";
		}
		if ($accesskey != '')
		{
			$accesskey = " accesskey=\"$accesskey\"";
		}
		return "<label$id$accesskey $options>$content</label>";
	}

	/**
	 * html fieldset, eg. groups a group of radiobuttons
	 *
	 * @param string $content the content
	 * @param string $legend legend / label of the fieldset, default ''=none
	 * @param string $options attributes for the tag, default ''=none
	 * @return string the html
	 */
	static function fieldset($content,$legend='',$options='')
	{
		$html = "<fieldset $options>".($legend ? '<legend>'.self::htmlspecialchars($legend).'</legend>' : '')."\n";

		if ($content)
		{
			$html .= $content;
			$html .= "\n</fieldset>\n";
		}
		return $html;
	}

	/**
	* tree widget using dhtmlXtree
	*
	* Code inspired by Lars's Felamimail uiwidgets::createFolderTree()
	*
	* @author Lars Kneschke <lars-AT-kneschke.de> original code in felamimail
	* @param array $_folders array of folders: pairs path => node (string label or array with keys: label, (optional) image, (optional) title, (optional) checked)
	* @param string $_selected path of selected folder
	* @param mixed $_topFolder=false node of topFolder or false for none
	* @param string $_onNodeSelect='alert' js static function to call if node gets selected
	* @param string $_tree='foldertree' id of the div and name of the variable containing the tree object
	* @param string $_divClass='' css class of the div
	* @param string $_leafImage='' default image of a leaf-node, ''=default of foldertree, set it eg. 'folderClosed.gif' to show leafs as folders
	* @param boolean/string $_onCheckHandler=false string with handler-name to display a checkbox for each folder, or false (default), 'null' switches checkboxes on without an handler!
	* @param string $delimiter='/' path-delimiter, default /
	* @param mixed $folderImageDir=null string path to the tree menu images, null uses default path
	*
	* @return string the html code, to be added into the template
	*/
	static function tree($_folders,$_selected,$_topFolder=false,$_onNodeSelect="null",$tree='foldertree',$_divClass='',$_leafImage='',$_onCheckHandler=false,$delimiter='/',$folderImageDir=null)
	{
		if(is_null($folderImageDir))
		{
			$folderImageDir = $GLOBALS['phpgw_info']['server']['webserver_url'].'/felamimail/templates/default/images/';
		}

		$html = self::div("\n",'id="'.$tree.'"',$_divClass);

		static $tree_initialised=false;
		if (!$tree_initialised)
		{
			$html .= '<link rel="STYLESHEET" type="text/css" href="'.$GLOBALS['phpgw_info']['server']['webserver_url'].'/felamimail/js/dhtmlxtree/css/dhtmlXTree.css" />'."\n";
			$html .= "<script type='text/javascript' src='{$GLOBALS['phpgw_info']['server']['webserver_url']}/felamimail/js/dhtmlxtree/js/dhtmlXCommon.js'></script>\n";
			$html .= "<script type='text/javascript' src='{$GLOBALS['phpgw_info']['server']['webserver_url']}/felamimail/js/dhtmlxtree/js/dhtmlXTree.js'></script>\n";
			$tree_initialised = true;
		}
		$html .= "<script type='text/javascript'>\n";
		$html .= "var $tree = new dhtmlXTreeObject('$tree','100%','100%',0);\n";
		$html .= "$tree.setImagePath('$folderImageDir/dhtmlxtree/');\n";

		if($_onCheckHandler)
		{
			$html .= "$tree.enableCheckBoxes(1);\n";
			$html .= "$tree.setOnCheckHandler('$_onCheckHandler');\n";
		}

		$top = 0;
		if ($_topFolder)
		{
			$top = '--topfolder--';
			$topImage = '';
			if (is_array($_topFolder))
			{
				$label = $_topFolder['label'];
				if (isset($_topFolder['image']))
				{
					$topImage = $_topFolder['image'];
				}
			}
			else
			{
				$label = $_topFolder;
			}
			$html .= "\n$tree.insertNewItem(0,'$top','".addslashes($label)."',$_onNodeSelect,'$topImage','$topImage','$topImage','CHILD,TOP');\n";

			if (is_array($_topFolder) && isset($_topFolder['title']))
			{
				$html .= "$tree.setItemText('$top','".addslashes($label)."','".addslashes($_topFolder['title'])."');\n";
			}
		}
		// evtl. remove leading delimiter
		if ($_selected{0} == $delimiter) $_selected = substr($_selected,1);
		foreach($_folders as $path => $data)
		{
			if (!is_array($data))
			{
				$data = array('label' => $data);
			}
			$image1 = $image2 = $image3 = '0';
			if (isset($data['image']))
			{
				$image1 = $image2 = $image3 = "'".$data['image']."'";
			}
			// evtl. remove leading delimiter
			if ($path{0} == $delimiter) $path = substr($path,1);
			$folderParts = explode($delimiter,$path);

			//get rightmost folderpart
			$label = array_pop($folderParts);
			if (isset($data['label'])) $label = $data['label'];

			// the rest of the array is the name of the parent
			$parentName = implode((array)$folderParts,$delimiter);
			if(empty($parentName)) $parentName = $top;

			$entryOptions = 'CHILD';
			if ($_onCheckHandler && $_selected)	// check selected items on multi selection
			{
				if (!is_array($_selected)) $_selected = explode(',',$_selected);
				if (in_array($path,$_selected,!is_numeric($path))) $entryOptions .= ',CHECKED';
				//echo "<p>path=$path, _selected=".print_r($_selected,true).": $entryOptions</p>\n";
			}
			// highlight current item
			elseif ((string)$_selected === (string)$path)
			{
				$entryOptions .= ',SELECT';
			}
			$html .= "$tree.insertNewItem('".addslashes($parentName)."','".addslashes($path)."','".addslashes($label).
				"',$_onNodeSelect,$image1,$image2,$image3,'$entryOptions');\n";
			if (isset($data['title']))
			{
				$html .= "$tree.setItemText('".addslashes($path)."','".addslashes($label)."','".addslashes($data['title'])."');\n";
			}
		}
		$html .= "$tree.closeAllItems(0);\n";
		if ($_selected)
		{
			foreach(is_array($_selected)?$_selected:array($_selected) as $path)
			{
				$html .= "$tree.openItem('".addslashes($path)."');\n";
			}
		}
		else
		{
				$html .= "$tree.openItem('$top');\n";
		}
		$html .= "</script>\n";

		return $html;
	}
}
html::_init_static();
