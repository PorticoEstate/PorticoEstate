<?php
	/**
	* Rich Text Editor Handler class
	*
	* Allows different rich text wysiwyg html editors to be used within phpgw
	*
	* @author Dave Hall skwashd at phpgroupware.org
	* @copyright Copyright (C) 2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id: class.richtext.inc.php 17066 2006-09-03 11:14:42Z skwashd $
	*/

	/**
	* Rich Text Editor Handler class
	*
	* @package phpgwapi
	* @subpackage gui
	*/
	class richtext
	{
		/**
		 * @var the base URL for all links, defaults to phpgw url
		 */
		 var $base_url;
		 
		/**
		* @var string $rte which rich text editor will be used
		*/
		var $rte;

		/**
		* @var array $targets the targets which will be replaced by the rich text editor
		*/
		var $targets = array();
		
		/**
		* Constructor
		*/
		function richtext()
		{
			$this->base_url = $GLOBALS['phpgw_info']['server']['webserver_url'];
			$this->rte = '';
			if(!is_object($GLOBALS['phpgw']->js))
			{
				$GLOBALS['phpgw']->js = createObject('phpgwapi.javascript');
			}
			$this->_init_head();
		}

		/**
		* Generate the dynamic script content for the header
		*/
		function generate_script()
		{
			if ( count($this->targets) )
			{
				switch($this->rte)
				{
					case 'fckeditor':
						$this->_generate_script_fckeditor();
						break;
					
					case 'tinymce':
						$this->_generate_script_tinymce();
						break;
					default:
						//do nothing
				}
			}
		}

		/**
		* Replaces an element with a rich text editor instance
		*/
		function replace_element($html_id)
		{
			$this->targets[$html_id] = true; //stops duplicates :)
		}

		/**
		 * Set the base URL for all links
		 */
		 function set_base_url($url)
		 {
			$this->base_url = $url;
		 }

		/**
		 * Generate the js needed for FCKeditor to function properly 
		 */
		 function _generate_script_fckeditor()
		 {
		 	$str = '';
			
		 	foreach ( $this->targets as $target => $crap )
		 	{
		 		$str .=  "var oFCKeditor_{$target} = new FCKeditor( '{$target}' ) ;\n" .
		 				"\toFCKeditor_{$target}.AutoDetectLanguage = false;\n" .
		 				"\toFCKeditor_{$target}.BaseHref = '{$this->base_url}';\n" .
						"\toFCKeditor_{$target}.BasePath = '{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/js/fckeditor/';\n" .
		 				"\toFCKeditor_{$target}.DefaultLanguage = '{$GLOBALS['phpgw_info']['user']['preferences']['common']['lang']}';\n" .
		 				"\toFCKeditor_{$target}.GeckoUseSPAN = false;\n" .
		 				"\toFCKeditor_{$target}.SpellChecker = '" . (extension_loaded('pspell') ? 'SpellerPages' : 'ieSpell') . "';\n" .
		 				"\toFCKeditor_{$target}.ReplaceTextarea();\n\n";
		 	}
		 	$GLOBALS['phpgw']->js->add_event('load', $str);
		 }

		/**
		 * Generate the js needed for tiny to function properly 
		 */
		function _generate_script_tinymce()
		{
			$GLOBALS['phpgw_info']['flags']['java_script'] .= 
			"<script type=\"text/javascript\">\n//<![CDATA[\n" .
				//"tinyMCE.baseURL = '{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/js/tinymce/';\n" .
				"tinyMCE.init({\n" .
					"\t\tdocument_base_url : '{$this->base_url}',\n" .
					"\t\tinline_styles : false,\n" .
					"\t\tlanguage : '{$GLOBALS['phpgw_info']['user']['preferences']['common']['lang']}',\n" .
					"\t\tmode : 'none',\n" .
					"\t\ttheme : 'advanced',\n" .
					"\t\ttheme_advanced_toolbar_align : 'left',\n" .
					"\t\ttheme_advanced_toolbar_location : 'top',\n" .
					//FIXME make this more configrable (using array? and unset?)
					"\t\tvalid_elements : ''" . '
+"a[accesskey|charset|class|coords|dir<ltr?rtl|href|hreflang|id|lang|name|rel|rev|shape<circle?default?poly?rect|tabindex|title|target|type],"
+"abbr[class|dir<ltr?rtl|id|lang|title],"
+"acronym[class|dir<ltr?rtl|id|id|lang|title],"
+"address[class|align|dir<ltr?rtl|id|lang|title],"
+"area[accesskey|alt|class|coords|dir<ltr?rtl|href|id|lang|nohref<nohref|shape<circle?default?poly?rect|tabindex|title|target],"
+"bdo[class|dir<ltr?rtl|id|lang|title],"
+"big[class|dir<ltr?rtl|id|lang|title],"
+"blockquote[dir|cite|class|dir<ltr?rtl|id|lang|title],"
+"body[alink|background|bgcolor|class|dir<ltr?rtl|id|lang|link|title|text|vlink],"
+"br[class|clear<all?left?none?right|id|title],"
+"button[accesskey|class|dir<ltr?rtl|disabled<disabled|id|lang|name|tabindex|title|type|value],"
+"caption[align<bottom?left?right?top|class|dir<ltr?rtl|id|lang|title],"
+"cite[class|dir<ltr?rtl|id|lang|title],"
+"code[class|dir<ltr?rtl|id|lang|title],"
+"col[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl|id|lang|span|title|valign<baseline?bottom?middle?top|width],"
+"colgroup[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl|id|lang|span|title|valign<baseline?bottom?middle?top|width],"
+"dd[class|dir<ltr?rtl|id|lang|title],"
+"del[cite|class|datetime|dir<ltr?rtl|id|lang|title],"
+"dfn[class|dir<ltr?rtl|id|lang|title],"
+"dir[class|compact<compact|dir<ltr?rtl|id|lang|title],"
+"div[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|title],"
+"dl[class|compact<compact|dir<ltr?rtl|id|lang|title],"
+"dt[class|dir<ltr?rtl|id|lang|title],"
+"em/i[class|dir<ltr?rtl|id|lang|title],"
+"fieldset[class|dir<ltr?rtl|id|lang|title],"
+"font[class|color|dir<ltr?rtl|face|id|lang|size|title],"
+"form[accept|accept-charset|action|class|dir<ltr?rtl|enctype|id|lang|method<get?post|name|title|target],"
+"h1[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|title],"
+"h2[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|title],"
+"h3[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|title],"
+"h4[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|title],"
+"h5[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|title],"
+"h6[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|title],"
+"hr[align<center?left?right|class|dir<ltr?rtl|id|lang|noshade<noshade|size|title|width],"
+"img[align<bottom?left?middle?right?top|alt|border|class|dir<ltr?rtl|height|hspace|id|ismap<ismap|lang|longdesc|name|src|title|usemap|vspace|width],"
+"input[accept|accesskey|align<bottom?left?middle?right?top|alt|checked<checked|class|dir<ltr?rtl|disabled<disabled|id|ismap<ismap|lang|maxlength|name|readonly<readonly|size|src|tabindex|title|type<button?checkbox?file?hidden?image?password?radio?reset?submit?text|usemap|value],"
+"ins[cite|class|datetime|dir<ltr?rtl|id|lang|title],"
+"isindex[class|dir<ltr?rtl|id|lang|prompt|title],"
+"kbd[class|dir<ltr?rtl|id|lang|title],"
+"label[accesskey|class|dir<ltr?rtl|for|id|lang|title],"
+"legend[align<bottom?left?right?top|accesskey|class|dir<ltr?rtl|id|lang|title],"
+"li[class|dir<ltr?rtl|id|lang|title|type|value],"
+"link[charset|class|dir<ltr?rtl|href|hreflang|id|lang|media|rel|rev|title|target|type],"
+"map[class|dir<ltr?rtl|id|lang|name|title],"
+"menu[class|compact<compact|dir<ltr?rtl|id|lang|title],"
+"object[align<bottom?left?middle?right?top|archive|border|class|classid|codebase|codetype|data|declare|dir<ltr?rtl|height|hspace|id|lang|name|standby|tabindex|title|type|usemap|vspace|width],"
+"ol[class|compact<compact|dir<ltr?rtl|id|lang|start|title|type],"
+"optgroup[class|dir<ltr?rtl|disabled<disabled|id|label|lang|title],"
+"option[class|dir<ltr?rtl|disabled<disabled|id|label|lang|selected<selected|title|value],"
+"p[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|title],"
+"param[id|name|type|value|valuetype<DATA?OBJECT?REF],"
+"pre/listing/plaintext/xmp[align|class|dir<ltr?rtl|id|lang|title|width],"
+"q[cite|class|dir<ltr?rtl|id|lang|title],"
+"s[class|dir<ltr?rtl|id|lang|title],"
+"samp[class|dir<ltr?rtl|id|lang|title],"
+"select[class|dir<ltr?rtl|disabled<disabled|id|lang|multiple<multiple|name|size|tabindex|title],"
+"small[class|dir<ltr?rtl|id|lang|title],"
+"span[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|title],"
+"strike[class|class|dir<ltr?rtl|id|lang|title],"
+"strong/b[class|dir<ltr?rtl|id|lang|title],"
+"style[dir<ltr?rtl|lang|media|title|type],"
+"sub[class|dir<ltr?rtl|id|lang|title],"
+"sup[class|dir<ltr?rtl|id|lang|title],"
+"table[align<center?left?right|bgcolor|border|cellpadding|cellspacing|class|dir<ltr?rtl|frame|height|id|lang|rules|summary|title|width],"
+"tbody[align<center?char?justify?left?right|char|class|charoff|dir<ltr?rtl|id|lang|title|valign<baseline?bottom?middle?top],"
+"td[abbr|align<center?char?justify?left?right|axis|bgcolor|char|charoff|class|colspan|dir<ltr?rtl|headers|height|id|lang|nowrap<nowrap|rowspan|scope<col?colgroup?row?rowgroup|title|valign<baseline?bottom?middle?top|width],"
+"textarea[accesskey|class|cols|dir<ltr?rtl|disabled<disabled|id|lang|name|readonly<readonly|rows|tabindex|title],"
+"tfoot[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl|id|lang|title|valign<baseline?bottom?middle?top],"
+"th[abbr|align<center?char?justify?left?right|axis|bgcolor|char|charoff|class|colspan|dir<ltr?rtl|headers|height|id|lang|nowrap<nowrap|rowspan|scope<col?colgroup?row?rowgroup|title|valign<baseline?bottom?middle?top|width],"
+"thead[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl|id|lang|title|valign<baseline?bottom?middle?top],"
+"title[dir<ltr?rtl|lang],"
+"tr[abbr|align<center?char?justify?left?right|bgcolor|char|charoff|class|rowspan|dir<ltr?rtl|id|lang|title|valign<baseline?bottom?middle?top],"
+"tt[class|dir<ltr?rtl|id|lang|title],"
+"u[class|dir<ltr?rtl|id|lang|title],"
+"ul[class|compact<compact|dir<ltr?rtl|id|lang|title|type],"
+"var[class|dir<ltr?rtl|id|lang|title]"'
					. "});\n"
					.  "\n//]]>\n</script>\n\n";
			
			foreach ( $this->targets as $target => $crap )
		 	{
				$GLOBALS['phpgw']->js->add_event('load', "tinyMCE.execCommand('mceAddControl', true, '{$target}');");
			}
		}

		/**
		* Add the appropriate <script>s to the <head> section
		*
		* @access private
		*/
		function _init_head()
		{
			if ( !isset($GLOBALS['phpgw_info']['user']['preferences']['common']['rteditor'])
				|| $GLOBALS['phpgw_info']['user']['preferences']['common']['rteditor'] == 'none' )
			{
				//nothing to do here as the user doesn't want to use a rte
				return true;
			}
			$rte_name = $GLOBALS['phpgw_info']['user']['preferences']['common']['rteditor'];
			
			if ( isset($GLOBALS['phpgw_info']['server']['enable_' . $rte_name]) )
			{
				//now that we know that the user is allowed to use it we init it
				switch($rte_name)
				{
					case 'fckeditor':
						$this->_init_fckeditor();
						break;
					case 'tinymce':
						$this->_init_tinymce();
						break;
					default:
						//do nothing
				}
			}
		}

		/**
		* Prepare FCKeditor to be used in a page
		*/
		function _init_fckeditor()
		{
			$GLOBALS['phpgw']->js->validate_file('fckeditor', 'fckeditor');
			$this->rte = 'fckeditor';
		}

		/**
		* Prepare tinymce to be used in a page
		*/
		function _init_tinymce()
		{
			$GLOBALS['phpgw']->js->validate_file('tinymce', 'tiny_mce');
			$this->rte = 'tinymce';
		}
	}
?>
