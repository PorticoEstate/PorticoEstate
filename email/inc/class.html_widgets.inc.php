<?php
	/**
	* EMail - HTML Widgets and Utility Functions
	*
	* @author Angelo (Angles) Puglisi <angles@aminvestments.com>
	* @copyright Copyright (C) 2002 Angelo Tony Puglisi (Angles)
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package email
	* @version $Id$
	* @internal Based on AngleMail http://www.anglemail.org/
	*/

	
	/**
	* Data structure used with class html_widgets
	*
	* Used in making select text, value items, such as in the combobox widget.
	* @package email
	* @access private
	*/	
	class html_option
	{
		// @param $value (class var) (string) this options value that will be submitted if selected
		var $value;
		// @param $text (class var) (string) the text the user sees, a description of this option
		var $text;
		// @param $selected  (class var) (boolean)  whether this option should be pre-selected in a combobox, default False
		var $selected;
		
		function __construct()
		{
			$this->value = '';
			$this->text = '';
			$this->selected = False;
		}
	}
	

	/**
	* Data structure used with hidden data in forms
	*
	* @package email
	* @access private
	*/	
	class hiddenvar_option
	{
		var $name;
		var $value;
		
		function __construct()
		{
			$this->name = '';
			$this->value = '';
		}
	}
	

	/**
	* Generate HTML widgets for use in web pages.
	*
	* Producing common widgets in html pages, such as a combobox. 
	* Designed to hiding the details of the html tags and by treating these 
	* things as widgets which is what they are, more than just simple html markup.
	* @package email
	*/	
	class html_widgets
	{
		/**************************************************************************\
		*	VARS
		\**************************************************************************/		
		var $debug = 0;
		var $debug_init = 0;
		
		// if calling from home page it is optional to force currentapp as a constructor param
		var $my_currentapp='';
		// bootstraper for the msg class
		var $msg_bootstrap;
		// private template to var names do not collide
		var $tpl;
		
		// A HREF OOP properties
		var $href_link='';
		var $href_target='';
		var $href_clickme='not_provided';
		var $href='';
		
		// combo box OOP properties
		var $cbox_name='not_provided';
		var $cbox_onChange='';
		var $cbox_items=array();
		var $combobox='';
		
		// form OOP properties
		var $form_name='';
		var $form_action='';
		var $form_method='';
		var $form_hiddenvars=array();
		var $form='';
		
		// TOOLBAR
		var $toolbar_msg='';
		var $toolbar_row_one='';
		var $toolbar_row_two='';
		var $toolbar='';
		
		// ALL FOLDERS ALL ACCOUNTS MEGA LISTBOX
		var $F_megalist_form_reference='';
		var $F_megalist_widget_name='';
		var $F_megalist_preselected_fldball='';
		var $F_megalist_skip_fldball='';
		var $F_megalist_first_item_text = '';
		
		// RELOAD WIDGET
		var $refresh_js='';
		
		// GENERIC ERROR REPORT
		var $F_mindless_default_txt = 'error text not provided';
		var $F_error_report_text='';
		var $F_go_somewhere_link='';
		var $F_go_home_link='';
		
		
		/**************************************************************************\
		*	CONSTRUCTOR
		\**************************************************************************/
		function __construct()
		{
			if ($this->debug_init > 0) { echo 'ENTER: email.html_widgets.CONSTRUCTOR'.'<br />'."\r\n"; }
			/*!
			@class requires including spell_struct header file
			@discussion  class html_widgets needs the special C-Style Include .h like file, 
			class.spell_struct which holds data structure class correction_info used here for integration 
			with the mail.spell  spellchecking class.
			*/
			$required_class = 'spell_struct';
			
			// if calling this class from the home page, then the currentapp will be 
			//set to "home" instead of "email", which messes up the include statement below, 
			// so set a local var to "email" to force considering the currentapp to be "email".
			$this->my_currentapp = 'email';
			//require_once(PHPGW_INCLUDE_ROOT.'/'.$GLOBALS['phpgw_info']['flags']['currentapp'].'/inc/class.'.$required_class.'.inc.php');
			require_once(PHPGW_INCLUDE_ROOT.'/'.$this->my_currentapp.'/inc/class.'.$required_class.'.inc.php');
			
			if ($this->debug_init > 0) { echo 'EXIT: email.html_widgets.CONSTRUCTOR'.'<br />'."\r\n"; }
			return;
		}
		
		
		/**************************************************************************\
		*	HREF LINK WIDGET
		\**************************************************************************/	
		
		/*!
		@capability HREF LINK WIDGET
		@discussion generate an a href item, includes link, target, text. CLEARS ITSELF after each "get_href"
		@author Angles
		@example $this->widgets->set_href_link('index.php');
		$this->widgets->set_href_target('top');
		$this->widgets->get_href_clickme(lang('click here for more info in a new window'));
		$my_href_tag = $this->widgets->get_href();
		*/
		/*!
		@function clear_href_vars
		@abstract after every call to "->get_href" all internal vars are cleared automatically with this function.
		@author Angles
		@discussion href class vars for link, target, text. CLEARS ITSELF after each "get_href". This utilty 
		function does that. It is RARE that you would ever need to call this directly.
		@access private
		*/
		function clear_href_vars()
		{
			$this->set_href_link('');
			$this->set_href_target('');
			$this->set_href_clickme('');
		}
		
		/*!
		@function set_href_link
		@abstract set the URL link of this this HREF widget
		@param $href_link (string) 
		@author Angles
		@access public
		*/
		function set_href_link($href_link='')
		{
			$this->href_link = $href_link;
		}
		/*!
		@function get_href_link
		@abstract used check or verify the value if the "href_link" property
		@author Angles
		@access public
		*/
		function get_href_link()
		{
			return $this->href_link;
		}
		
		/*!
		@function set_href_target
		@abstract OPTIONAL set the "target", i.e. what browser window this HREF should apply to, 
		default is not to specify any target in the HREF tag. 
		@param $href_target (string) 
		@author Angles
		@access public
		*/
		function set_href_target($href_target='')
		{
			$this->href_target = $href_target;
		}
		/*!
		@function get_href_target
		@abstract used check or verify the value if the "href_target" property
		@author Angles
		@access public
		*/
		function get_href_target()
		{
			return $this->href_target;
		}
		
		/*!
		@function set_href_clickme
		@abstract what the user needs to click on to activate the HREF link, can be text or a COMPLETE img tag.
		@param $href_clickme (string)  text or a COMPLETE img tag
		@author Angles
		@access public
		*/
		function set_href_clickme($href_clickme='')
		{
			$this->href_clickme = $href_clickme;
		}
		/*!
		@function get_href_clickme
		@abstract used check or verify the value if the "href_clickme" property
		@author Angles
		@access public
		*/
		function get_href_clickme()
		{
			return $this->href_clickme;
		}
		
		/*!
		@function get_href
		@abstract generate and return an HREF tag using information you set in the OOP "set_href_" functions
		@author Angles
		@result (string) a complete HREF tag generated from data you set in the "set_href_" functions,
		@discussion After you set "href_target", "href_clickme" and other optional properties ("href_target") this 
		function generates an HREF tag from that data and returns it. NOTE after generating the HREF tag, and 
		before returning it, this function CLEARS ALL PROPERTIES that it used to make that href tag, so this 
		"widget" automatically is ready to new usage without having to explicitly call any "new" or "clear" function.
		@access public
		*/
		function get_href()
		{
			$target = '';
			if ($this->href_target != '')
			{
				$target = ' target="'.$this->href_target.'"';
			}
			// put the href return value in storage so we can clear all href internal vars before we exit this function with a return.
			$href = '<a href="' .$this->href_link .'"'.$target.'>' .$this->href_clickme .'</a>' ."\r\n";
			// this widget clears itself automatically after every call to this function.
			$this->clear_href_vars();
			return $href;
		}
		
		/**************************************************************************\
		*	QUICK HREF TAG - depreciated
		\**************************************************************************/	
		/*!
		@function href_maketag (not related to the HREF widget)
		@abstract QUICK way to generate a typical A HREF html item in a single function call. NOT PART OF 
		THE HREF WIDGET, not OOP style, just a quick utilty function. ALL params must be passed in this function call.
		@param $href_link (string) URL for this HREF tag
		@param $href_text (string) what the user clicks on to activate this HREF tag, can be text OR a COMPLETE IMG tag.
		@param $target (string) OPTIONAL target for the link, such as when using frames or opening a new browser window.
		@author Angles
		@discussion not really a widget, does not use OOP style, but it gets the job done quickly. Somewhat Depreciated, 
		use the OOP style href widget instead.
		@example this->href_maketag("index.jsp", "click here for home page", "new");
		@access public
		*/
		function href_maketag($href_link='',$href_text='default text',$target='')
		{
			if ($target != '')
			{
				$target = ' target="'.$target.'"';
			}
			return '<a href="' .$href_link .'"'.$target.'>' .$href_text .'</a>' ."\r\n";
		}
		
		/**************************************************************************\
		*	QUICK IMG TAG
		\**************************************************************************/	
		/*!
		@function img_maketag
		@abstract quick and dirty to make an IMG html tag in one function call. 
		@author Angles 
		@param $location (string int) URL to the image, cal be releative or fully qualified 
		@param $alt (string) text displayed (a) in place of the image if not displayed, and (b) as a image tooltip on some browsers 
		@param $height (string int) int passed as a string, OPTIONAL, not included in img tag if not provided 
		@param $width (string int)  int passed as a string OPTIONAL, not included in img tag if not provided 
		@param $border (string int) int passed as string OPTIONAL , not included in img tag if not provided , 
		the size of the border around the image, often set to "0"
		@discussion not really a widget but it gets the job done. QUICK way to generate a typical A IMG html item 
		in a single function call. Not OOP style, just a quick utilty function. ALL params must be passed in this function call.
		@example $my_img = widgets->img_maketag("poweredby.png", "powered by RedHat",  "", "", "0");
		@access public
		*/
		function img_maketag($location='',$alt='',$height='',$width='',$border='')
		{
			$alt_default_txt = 'image';
			$alt_unknown_txt = 'unknown';
			if ($location == '')
			{
				return '<img src="" alt="['.$alt_unknown_txt.']">';
			}
			if ($alt != '')
			{
				$alt_tag = ' alt="['.$alt.']"';
				$title_tag = ' title="'.$alt.'"';
			}
			else
			{
				$alt_tag = ' alt="['.$alt_default_txt.']"';
				$title_tag = '';
			}
			if ($height != '')
			{
				$height_tag = ' height="' .$height .'"';
			}
			else
			{
				$height_tag = '';
			}
			if ($width != '')
			{
				$width_tag = ' width="' .$width .'"';
			}
			else
			{
				$width_tag = '';
			}
			if ($border != '')
			{
				$border_tag = ' border="' .$border .'"';
			}
			else
			{
				$border_tag = '';
			}
			$image_html = '<img src="'.$location.'"' .$height_tag .$width_tag .$border_tag .$title_tag .$alt_tag .'>';
			return $image_html;
		}
		
		/**************************************************************************\
		*	COMBOBOX WIDGET
		\**************************************************************************/	
		
		/*!
		@capability COMBOBOX WIDGET
		@discussion generate a combo box html widget.
		@author Angles
		@example 
		$this->widgets->new_combobox();
		$this->widgets->set_cbox_name("user_choice");
		$this->widgets->set_cbox_item("yes"', "Customer is Right");
		$this->widgets->set_cbox_item("no"', "Customer is Wrong");
		$my_combobox_wirget = $this->widgets->get_combobox();
		*/
		// combo box OOP access functions
		/*!
		@function set_cbox_name
		@abstract ? 
		*/
		function set_cbox_name($str='')
		{
			if ($str == '')
			{
				$this->cbox_name = 'not_provided';
			}
			else
			{
				$this->cbox_name = $str;
			}
		}
		/*!
		@function get_cbox_name
		@abstract ? 
		*/
		function get_cbox_name()
		{
			return $this->cbox_name;
		}
		
		/*!
		@function set_cbox_onChange
		@abstract ? 
		*/
		function set_cbox_onChange($str='')
		{
			if ($str != '')
			{
				$this->cbox_onChange = $str;
			}
		}
		/*!
		@function get_cbox_onChange
		@abstract ? 
		*/
		function get_cbox_onChange()
		{
			return $this->cbox_onChange;
		}
		
		/*!
		@function set_cbox_item
		@abstract ? 
		*/
		function set_cbox_item($value='',$text='',$selected=False)
		{
			// make sure $selected is boolean
			if (!is_bool($selected))
			{
				// replace param with a default boolean value of False
				$selected = False;
			}
			// I've actually seen bomboboxes with an item (usually the first) with no name
			if (($value != '') || ($text != ''))
			{
				$item_idx = count($this->cbox_items);
				$this->cbox_items[$item_idx] = new html_option;
				$this->cbox_items[$item_idx]->value = $value;
				$this->cbox_items[$item_idx]->text = $text;
				$this->cbox_items[$item_idx]->selected = $selected;
			}
		}
		/*!
		@function set_cbox_item_spellcheck
		@abstract makes a special kind of combobox select item for use with spellcheck
		@param $this_bad_word_element (class "correction_info" structued data from file class.spell_struct) 
		@param $suggestion_num (int OR empty string) see discussion below.  
		@author Angles
		@discussion  This function makes use of structure "correction_info" which we expose 
		by including file class.spell_struct in the constructor for this class. The combobox select item this 
		function makes embeds array data in the items "value" by setting it to a string that 
		resembles a URL get request, which we then can recover this array by applying php function 
		parse_str to the value when the user submits the form. The idea is to provide enough data 
		in the value that the spellcheck replacement code can accurately find and replace the desired 
		word, or not change the word at all if special suggestion string "K_E_E_P" is present.
		The first suggestion should be en empty string with special value "K_E_E_P" 
		which means no change to the misspelled word, this is indicated by passing an empty string 
		for param $suggestion_num. 
		The value for the suggestion item is a URL type string that contains as much informaion 
		as we can preserve from the $this_bad_word_element object param, but specifying the individual 
		suggestion provieded by the $suggestion_num arg which is the index to this particular 
		suggestion in the $this_bad_word_element->suggestions[] numbered array of suggestions. 
		Upon submit, we can apply php function parse_str() to this uri to recover the desired array structure. 
		Parse_str will even urldecode the items for us.
		*/
		function set_cbox_item_spellcheck($this_bad_word_element='', $suggestion_num='')
		{
			// we included the spell_struct file above, so this object knows what 
			// correction_info struct is
			if (is_object($this_bad_word_element))
			{
				if ((string)$suggestion_num != '')
				{
					$suggestion_txt = $this_bad_word_element->suggestions[(int)$suggestion_num];
					$suggestion_value = $this_bad_word_element->suggestions[(int)$suggestion_num];
				}
				else
				{
					// the first suggestion should be en empty string with special value "K_E_E_P"
					// which means no change to the misspelled word
					// this is indicated by passing an empty string for $suggestion_num
					$suggestion_txt = '';
					$suggestion_value = 'K_E_E_P';
				}
				// the value for the suggestion item is a URL type string that contains as much informaion
				// as we can preserve from the $this_bad_word_element object, but specifying the individual 
				// suggection provieded by the $suggestion_num arg which is the index to this particular 
				// suggestion in the $this_bad_word_element->suggestions[] numbered array of suggestions
				// upon submit, we can apply php function parse_str() to this uri to recover the desired array structure.
				// parse_str will even urldecode the items for us.
				/*
				$uri_value = 
					  'cbox[orig_word]='.urlencode($this_bad_word_element->orig_word)
					.'&cbox[orig_word_clean]='.urlencode($this_bad_word_element->orig_word_clean)
					.'&cbox[line_num]='.urlencode($this_bad_word_element->line_num)
					.'&cbox[word_num]='.urlencode($this_bad_word_element->word_num)
					.'&cbox[suggestion_value]='.urlencode($suggestion_value);
				*/
				$uri_value = 
					  'orig_word='.urlencode($this_bad_word_element->orig_word)
					.'&orig_word_clean='.urlencode($this_bad_word_element->orig_word_clean)
					.'&line_num='.urlencode($this_bad_word_element->line_num)
					.'&word_num='.urlencode($this_bad_word_element->word_num)
					.'&suggestion_value='.urlencode($suggestion_value);
				
				$item_idx = count($this->cbox_items);
				$this->cbox_items[$item_idx] = new html_option;
				$this->cbox_items[$item_idx]->value = $uri_value;
				$this->cbox_items[$item_idx]->text = $suggestion_txt;
			}
		}
		/*!
		@function get_cbox_item
		@abstract ? 
		*/
		function get_cbox_item($idx='')
		{
			if ((string)$idx == '')
			{
				return $this->cbox_items;
			}
			else
			{
				return $this->cbox_items[$idx];
			}
		}
		
		/*!
		@function new_combobox
		@abstract ALWAYS start a new combobox widget by calling this function first. Clears all combobox 
		properties. 
		*/
		function new_combobox()
		{
			$this->cbox_name='not_provided';
			$this->cbox_onChange='';
			$this->cbox_items=array();
			$this->combobox='';
		}
		
		/*!
		@function get_combobox
		@abstract generate and return a HTML select (combobo) widget using the values you set in 
		the "set_cbox_" functions.
		@author Angles
		@discussion this function does not clear its properties, so if for some reason you want THE SAME 
		comboboxes more then one time, calling "get_combobox" will return the same combobox until you 
		clear it by calling "new_combobox", which you should ALWAYS do when starting a new combobox 
		widget. VALUES ARE NOT URLENCODED, except that the special spellcheck item stuff 
		does it, but before this function, and this does not happen for the normal set cbox item. It IS 
		html specialchars encoded here.
		@access public
		*/
		function get_combobox()
		{
			if ($this->cbox_onChange != '')
			{
				$onChange_tag = ' onChange="'.$this->cbox_onChange.'"';
			}
			else
			{
				$onChange_tag = '';
			}
			$this->combobox = '<select name="'.$this->cbox_name.'"'.$onChange_tag.'>';
			$loops = count($this->cbox_items);
			if ($loops > 0)
			{
				for ($i=0; $i < $loops; $i++)
				{
					if ($this->cbox_items[$i]->selected == True)
					{
						$selected_tag = ' selected';
					}
					else
					{
						$selected_tag = '';
					}
					$this->combobox .= 
						'<option value="'.$this->cbox_items[$i]->value.'"'.$selected_tag.'>'
						.htmlspecialchars($this->cbox_items[$i]->text)
						.'</option>';
					$this->combobox .= "\r\n";
				}
			}
			$this->combobox .= '</select>';
			return $this->combobox;
		}
		// <select name="filter" onChange="this.form.submit();"><option value="all">show all</option>
		// <option value="public">only yours</option>
		// <option value="private">Private</option>
		// </select>
		
		/**************************************************************************\
		*	FORM WIDGET
		\**************************************************************************/	
		
		/*!
		@capability FORM WIDGET
		@discussion generate a opening tag of a form including name, action, method, and hiddenvars
		@author Angles
		@example 
		$this->widgets->new_form();
		$this->widgets->set_form_name('spell_review');
		$this->widgets->set_form_method('POST');
		$this->widgets->set_form_action('index.php?email.targets.move');
		$this->widgets->set_form_hiddenvar('subject', 'stock');
		$this->widgets->set_form_hiddenvar('symbol', 'GM');
		// OPTIONAL if you have set "preserve_vars" you can include them as hidden vars with this command
		$this->commit_preserve_vars_to_form();
		$my_form_tag = $this->widgets->get_form();
		*/
		function new_form()
		{
			$this->form_name='not_provided';
			$this->form_action='';
			$this->form_method='POST';
			$this->form_hiddenvars=array();
			$this->form = '';
		}
		
		/*!
		@function set_form_name
		@abstract ? 
		*/
		function set_form_name($str='')
		{
			if ($str != '')
			{
				$this->form_name = $str;
			}
		}
		/*!
		@function get_form_name
		@abstract ? 
		*/
		function get_form_name()
		{
			return $this->form_name;
		}
		
		/*!
		@function set_form_action
		@abstract ? 
		*/
		function set_form_action($str='')
		{
			if ($str != '')
			{
				$this->form_action = $str;
			}
		}
		/*!
		@function 
		@abstract ? get_form_action
		*/
		function get_form_action()
		{
			return $this->form_action;
		}
		
		/*!
		@function set_form_method
		@abstract ? 
		*/
		function set_form_method($str='')
		{
			if ($str != '')
			{
				$this->form_method = $str;
			}
		}
		/*!
		@function get_form_method
		@abstract ? 
		*/
		function get_form_method()
		{
			return $this->form_method;
		}
		
		/*!
		@function set_form_hiddenvar
		@abstract put hidden  vars in a form tag.
		@param $name the "key" in the GPC key - value pair
		@param $value  the "value" in the GPC key - value pair
		@param $do_urlencode (boolean) OPTIONAL default is True, whether or not to urlencode the name and value params.
		@author Angles
		@access public
		*/
		function set_form_hiddenvar($name='',$value='', $do_urlencode=True)
		{
			// I've actually seen bomboboxes with an item (usually the first) with no name
			if (($name != '') || ($value != ''))
			{
				if ($do_urlencode == True)
				{
					$name = urlencode($name);
					$value = urlencode($value);
				}
				$item_idx = count($this->form_hiddenvars);
				$this->form_hiddenvars[$item_idx] = new hiddenvar_option;
				$this->form_hiddenvars[$item_idx]->name = $name;
				$this->form_hiddenvars[$item_idx]->value = $value;
			}
		}
		/*!
		@function get_form_hiddenvar
		@abstract ? 
		*/
		function get_form_hiddenvar($idx='')
		{
			if ((string)$idx == '')
			{
				return $this->form_hiddenvars;
			}
			else
			{
				return $this->form_hiddenvars[$idx];
			}
		}
		
		// <form enctype="multipart/form-data" name="spell_review" action="/mail/index.php?menuaction=email.bosend.sendorspell&fldball[folder]=INBOX&fldball[acctnum]=3&sort=1&order=1&start=0" method="POST">
		//<input type="hidden" name="sort" value="1">
		//<input type="hidden" name="order" value="1">
		//<input type="hidden" name="start" value="0">
		//<input type="hidden" name="force_showsize" value="1">
		/*!
		@function get_form
		@abstract generate and return an opening FORM tag using the data you set in the "set_form_" functions. 
		@author Angles
		@discussion Any hiddenvars will be urlencoded. NOTE YOU MUST PUT THE CLOSING &lt;&#047;form&gt; somewhere 
		after this form tag. This functions generates the opening tag of the form, which is where all the complicated stuff 
		is. The ending tag is a normal html markup closing tag you must supply. NOTE on form enctype, HTML 401 says 
		"multipart/form-data" is very strict to MIME rfc2045-49 and will add hard wrap CRLF to each line of text 
		in any control. For example, a TEXTAREA input control will hard wrap at whatever its column length is, 
		because it represents the end of a line of text, eventhough the user did not explicitly press return, and therefor 
		it should be a soft return, not a CRLF. On the other hand, "application/x-www-form-urlencoded" is the default 
		value and is assumed if not provided, and is the kind of submit we are more used to. NOTE "multipart/form-data" 
		is also designed to handle large amounts of text or binary data, so "multipart/form-data" is used with file upload 
		forms, which means "multipart/form-data" WILL NOT WORK if the php.ini file does not allow "FILE UPLOADS". 
		This can be a confusing issue because we are not submitting a file upload, just some text, but the 
		"multipart/form-data"  is used for file upload forms, hence the association.  UPDATE testing shows this 
		may not be true eventhough HTML v401 says so. In a TEXTAREA control, using wrap="hard" yields the 
		CRLF hard wrap regardless of the enctype, and without the wrap="hard" there is no CRLF line ends to soft 
		wraps, eventhough there should be with "multipart/form-data" forms.
		@access prublic
		*/
		function get_form()
		{
			$this->form = 
				// '<form enctype="multipart/form-data" '
				 '<form enctype="application/x-www-form-urlencoded" '
				.'name="'.$this->form_name.'" '
				.'action="'.$this->form_action.'" '
				.'method="'.$this->form_method.'">'
				."\r\n";
			
			$loops = count($this->form_hiddenvars);
			if ($loops > 0)
			{
				for ($i=0; $i < $loops; $i++)
				{
					$this->form .= 
						 '<input type="hidden" '
						.'name="'.$this->form_hiddenvars[$i]->name.'" '
						.'value="'.$this->form_hiddenvars[$i]->value.'">';
					// just to be safe, send a line break after every one of these
					$this->form .= "\r\n";
				}
			}
			return $this->form;
		}
		
		/*!
		@function form_closetag
		@abstract SIMPLE - returns the closing tag for a form &lt;&#047;form&gt; 
		@author Angles
		@discussion This seems dumb at first, but take the folders combobox as an example, 
		when the user selects a folder from the combbox the OnChange submits the form associated 
		with that combobox, i.e. the form OnChange and the form name should be the same name and 
		the form age should surround the combobox, I think, Anyway, IMAP servers have folders but 
		POP servers do now, so we will not show said folder combobox if viewing a POP3 mail server. 
		Therefor, in our template we put a non-breaking-space in the place of the combobox html. 
		BUT what about that those form tage that surround this combobox, we should leave them out 
		too. In typical templaying this is ease to replace the form opening tag with a non-breaking-space. 
		BUT the closing tag is such a simple thing we often hard code it into the template, not a template 
		var. SO we can not leave out the form open tag but have the form close tag still be in the template. 
		This no-brainer function just makes it easy to remember to leave out or include, as the case may 
		be, the form closeing tag. 
		@access public
		*/
		function form_closetag()
		{
			return '</form>';
		}
		
		/**************************************************************************\
		*	BUTTON WIDGET
		\**************************************************************************/	
		
		/*!
		@function make_button
		@abstract generate a button in a SINGLE FUNCTION CALL, all params must be passed.
		@author Angles
		@param $type (string) usually "SUBMIT"
		@param $name (string) they GPC "key" in the "key - value" pair
		@param $value (string) they GPC "value" in the "key - value" pair AND THE TEXT on the button
		@param $onClick (string) OPTIONAL usually some javascript for the onclick event of the button. Default is none
		@discussion Since a button is generally considered a "widget", I will call this a widget although it does 
		not use "set_" property functions the other widgets do. All params must be passed in the function call.
		@access public
		@example $my_button = $this->widgets->make_button('submit', 'btn_send', lang('Send'));
		*/
		//<input type="submit" name="btn_send" value="Send">
		function make_button($type='', $name='',$value='', $onClick='')
		{
			if ($type == '')
			{
				$type='submit';
			}
			if ($name == '')
			{
				$name=$type.'_button';
			}
			if ($value == '')
			{
				$value=lang('submit');
			}
			if ($onClick == '')
			{
				$onClick_tag = '';
			}
			else
			{
				$onClick_tag = ' onClick="'.$onClick.'"';;
			}
			
			$button = '<input type="'.$type.'" name="'.$name.'" value="'.$value.'"'.$onClick_tag.'>';
			return $button;
		}
		
		/**************************************************************************\
		*	TOOLBAR COMPOUND WIDGET
		\**************************************************************************/	
		
		/*!
		@capability TOOLBAR COMPOUND WIDGET
		@discussion generate a toolbar for use in email pages  NOTE: usually only one toolbar made per page view 
		so there is no "new_toolbar" function because we should not need it. set_toolbar_msg is the only value 
		that you might want to check if you make more then one toolbar.
		@author Angles
		*/
		
		/*!
		@function set_toolbar_msg
		@abstract the toolbar has a 3rd row which can display text to the user. This function sets that.
		@param $msg (string) 
		@author Angles
		@discussion Usually after deleting or moving messages or creating, renaming, or deleting folders, 
		the mail_msg generates some kind of message to report to the user what actions were just taken. 
		Note a blank string will clear a msg if needed. Currently only the uiindex page and the uifolder page 
		might provide a msg.
		@access public
		*/
		function set_toolbar_msg($msg='')
		{
			$this->toolbar_msg = $msg;
		}
		/*!
		@function get_toolbar_msg
		@abstract get whatever value the toolbar_msg property has.
		@author Angles
		@access public
		*/
		function get_toolbar_msg()
		{
			return $this->toolbar_msg;
		}
		
		/*!
		@function get_toolbar
		@abstract this function returns a complete html toolbar widget that is on top of many email pages.
		@author Angles
		@discussion generate a toolbar for use in email pages  NOTE: 
		WE NEED globals[phpge]->msg OBJECT and A LOGIN in order to make this toolbar, 
		THEREFOR do not put this toolbar on a page where login may not be available.Currently, 
		Preferencs pages DO NOT LOGIN, because if you are setting preferences you do not need 
		a mailserver stream AND you may not even have enough data set to get a mailserver stream. 
		Note: uses a private template object to assemple the toolbar, calling function is responsible for 
		putting the result in the global template.
		@access public
		*/
		function get_toolbar()
		{
			// WE NEED ->msg OBJECT and A LOGIN in order to make this toolbar
			$this->msg_bootstrap = CreateObject("email.msg_bootstrap");
			$this->msg_bootstrap->ensure_mail_msg_exists('emai.html_widgets.get_toolbar', 0);
			
			// we use a PRIVATE template object to produce this toolbar
			$this->tpl = CreateObject('phpgwapi.template',PHPGW_APP_TPL);
			
			// if we already made this toolbar, retuen it from L1 cache ????
			//$my_acctnum = $GLOBALS['phpgw']->msg->get_acctnum();
			$this->tpl->set_file(array('T_widget_toolbar' => 'widget_toolbar.tpl'));
			$this->tpl->set_block('T_widget_toolbar','B_toolbar_row_one','V_toolbar_row_one');
			$this->tpl->set_block('T_widget_toolbar','B_toolbar_row_two','V_toolbar_row_two');
			
			// We use these over and over, so figure them out now
			// some fonts and font sizes
			$row1_rowcolor_key = 'row_off';
			$row2_rowcolor_key = 'row_on';
			$this->tpl->set_var('row1_rowcolor_key',$row1_rowcolor_key);
			$this->tpl->set_var('row2_rowcolor_key',$row2_rowcolor_key);
			$this->tpl->set_var('toolbar_row1_bgcolor','');
			$this->tpl->set_var('toolbar_row2_bgcolor','');
			$this->tpl->set_var('toolbar_font','');
			$this->tpl->set_var('toolbar_font_size','2');
			$this->tpl->set_var('report_this_font_size','1');
			
			$this->tpl->set_var('report_this', $GLOBALS['phpgw']->msg->report_moved_or_deleted());
			
			$icon_theme = $GLOBALS['phpgw']->msg->get_pref_value('icon_theme',$GLOBALS['phpgw']->msg->get_acctnum());
			$icon_size = $GLOBALS['phpgw']->msg->get_pref_value('icon_size',$GLOBALS['phpgw']->msg->get_acctnum());
			$svr_image_dir = PHPGW_IMAGES_DIR;
			$image_dir = PHPGW_IMAGES;
			
			// this is optional
			$this->clear_href_vars();
			// Create Links for all the buttons
			$folders_link = $GLOBALS['phpgw']->link('/index.php',array(
									'menuaction' => 'email.uifolder.folder',
									// going to the folder list page, we only need log into the INBOX folder
									'fldball[folder]' => 'INBOX',
									'fldball[acctnum]' => $GLOBALS['phpgw']->msg->get_acctnum()));
			$compose_link = $GLOBALS['phpgw']->link('/index.php',array(
									'menuaction' => 'email.uicompose.compose',
									// this data tells us where to return to after sending a message
									'fldball[folder]' => $GLOBALS['phpgw']->msg->prep_folder_out(),
									'fldball[acctnum]' => $GLOBALS['phpgw']->msg->get_acctnum(),
									'sort' => $GLOBALS['phpgw']->msg->get_arg_value('sort'),
									'order' => $GLOBALS['phpgw']->msg->get_arg_value('order'),
									'start' => $GLOBALS['phpgw']->msg->get_arg_value('start')));
			$search_link = $GLOBALS['phpgw']->link('/index.php', array(
									'menuaction' => 'email.uisearch.form',
									// this data tells us what account we are operating in
									'fldball[folder]' => $GLOBALS['phpgw']->msg->prep_folder_out(),
									'fldball[acctnum]' => $GLOBALS['phpgw']->msg->get_acctnum()
));
	
			$filters_link = $GLOBALS['phpgw']->link('/index.php',array(
								'menuaction' => 'email.uifilters.filters_list',
								// this data tells us what folder and account was last active
								'fldball[folder]' => $GLOBALS['phpgw']->msg->prep_folder_out(),
								'fldball[acctnum]' => $GLOBALS['phpgw']->msg->get_acctnum()));
			$accounts_link = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.uipreferences.ex_accounts_list'));
			$email_prefs_link = $GLOBALS['phpgw']->link('/index.php',array(
								'menuaction' => 'email.uipreferences.preferences',
								'ex_acctnum' => $GLOBALS['phpgw']->msg->get_acctnum()));					
			// Check to see if mailserver supports folders.
			$has_folders = $GLOBALS['phpgw']->msg->get_mailsvr_supports_folders();
			// Create Buttons
			switch ($GLOBALS['phpgw']->msg->get_pref_value('button_type'))
			{
				case 'text':
					//Create Compose Button
					$this->set_href_link($compose_link);
					$this->set_href_clickme(lang('Compose'));
					$this->tpl->set_var('compose_txt_link', $this->get_href());			
					$this->tpl->set_var('compose_img_link', '&nbsp;');
					//Create Search Button
					$this->set_href_link($search_link);
					$this->set_href_clickme(lang('Search'));
					$this->tpl->set_var('search_txt_link', $this->get_href());			
					$this->tpl->set_var('search_img_link', '&nbsp;');
					//Create Filter Button
					$this->set_href_link($filters_link);
					$this->set_href_clickme(lang('Filters'));
					$this->tpl->set_var('filters_txt_link', $this->get_href());
					$this->tpl->set_var('filters_img_link', '&nbsp;');
					//Create Accounts Button
					$this->set_href_link($accounts_link);
					$this->set_href_clickme(lang('Accounts'));
					$this->tpl->set_var('accounts_txt_link', $this->get_href());
					$this->tpl->set_var('accounts_img_link', '&nbsp;');
					//Create Settings Button
					$this->set_href_link($email_prefs_link);
					$this->set_href_clickme(lang('Settings'));
					$this->tpl->set_var('settings_txt_link', $this->get_href());
					$this->tpl->set_var('settings_img_link', '&nbsp;');
					//Check for folder support and create Folder Button
					if ($has_folders == True)
					{
						//$this->set_href_clickme($this->img_maketag($image_dir.'/'.$icon_theme.'-folder-'.$icon_size.'.png',lang('Folders'),'','','0'));
						//$this->tpl->set_var('folders_img_link', $this->get_href());
						//Create Folder Text Link
						$this->set_href_link($folders_link);
						$this->set_href_clickme(lang('Folders'));
						$this->tpl->set_var('folders_txt_link', $this->get_href());
						$this->tpl->set_var('folders_img_link', '&nbsp;');
					}
					else
					{
						$this->tpl->set_var('folders_img_link', '&nbsp;');
						$this->tpl->set_var('folders_txt_link', '&nbsp;');
					}
					break;
				case 'image':
					//Create Compose Button
					$this->set_href_link($compose_link);
					$this->set_href_clickme($this->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$icon_theme.'/compose-message-'.$icon_size,'_on'),lang('Compose'),'','','0'));
					$this->tpl->set_var('compose_img_link', $this->get_href());
					$this->tpl->set_var('compose_txt_link', '&nbsp;');			
					//Create Search Button
					$this->set_href_link($search_link);
					//$this->set_href_clickme($this->img_maketag($image_dir.'/'.$icon_theme.'-search-16.png',lang('Search'),'','','0'));
					// will fix this later when new images are made
					$this->set_href_clickme($this->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$icon_theme.'/search-'.$icon_size,'_on'),lang('Search'),'','','0'));
					$this->tpl->set_var('search_img_link', $this->get_href());
					$this->tpl->set_var('search_txt_link', '&nbsp;');			
					//Create Filter Button
					$this->set_href_link($filters_link);
					$this->set_href_clickme($this->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$icon_theme.'/filters-'.$icon_size,'_on'),lang('Filters'),'','','0'));
					$this->tpl->set_var('filters_img_link', $this->get_href());
					$this->tpl->set_var('filters_txt_link', '&nbsp;');
					//Create Accounts Button
					$this->set_href_link($accounts_link);
					$this->set_href_clickme($this->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$icon_theme.'/accounts-'.$icon_size,'_on'),lang('Accounts'),'','','0'));
					$this->tpl->set_var('accounts_img_link', $this->get_href());
					$this->tpl->set_var('accounts_txt_link', '&nbsp;');
					//Create Settings Button
					$this->set_href_link($email_prefs_link);
					$this->set_href_clickme($this->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$icon_theme.'/customize-'.$icon_size,'_on'),lang('Settings'),'','','0'));
					$this->tpl->set_var('settings_img_link', $this->get_href());
					$this->tpl->set_var('settings_txt_link', '&nbsp;');
					//Check for folder support and create Folder Button
					if ($has_folders == True)
					{
						//Create Folder Image Link
						$this->set_href_link($folders_link);
						$this->set_href_clickme($this->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$icon_theme.'/folder-'.$icon_size,'_on'),lang('Folders'),'','','0'));
						$this->tpl->set_var('folders_img_link', $this->get_href());
						$this->tpl->set_var('folders_txt_link', '&nbsp;');
						
					}
					else
					{
						$this->tpl->set_var('folders_txt_link', '&nbsp;');
						$this->tpl->set_var('folders_txt_link', '&nbsp;');
					}
					break;
				case 'both':
					//Create Compose Button
					$this->set_href_link($compose_link);
					$this->set_href_clickme($this->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$icon_theme.'/compose-message-'.$icon_size,'_on'),lang('Compose'),'','','0'));
					$this->tpl->set_var('compose_img_link', $this->get_href());
					$this->set_href_link($compose_link);
					$this->set_href_clickme(lang('Compose'));
					$this->tpl->set_var('compose_txt_link', $this->get_href());			
					//Create Search Button
					$this->set_href_link($search_link);
					//$this->set_href_clickme($this->img_maketag($image_dir.'/'.$icon_theme.'-search-16.png',lang('Search'),'','','0'));
					// will fix this later when new images are made
					$this->set_href_clickme($this->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$icon_theme.'/search-'.$icon_size,'_on'),lang('Search'),'','','0'));
					$this->tpl->set_var('search_img_link', $this->get_href());
					$this->set_href_link($search_link);
					$this->set_href_clickme(lang('Search'));
					$this->tpl->set_var('search_txt_link', $this->get_href());			
					//Create Filter Button
					$this->set_href_link($filters_link);
					$this->set_href_clickme($this->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$icon_theme.'/filters-'.$icon_size,'_on'),lang('Filters'),'','','0'));
					$this->tpl->set_var('filters_img_link', $this->get_href());
					$this->set_href_link($filters_link);
					$this->set_href_clickme(lang('Filters'));
					$this->tpl->set_var('filters_txt_link', $this->get_href());
					//Create Accounts Button
					$this->set_href_link($accounts_link);
					$this->set_href_clickme($this->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$icon_theme.'/accounts-'.$icon_size,'_on'),lang('Accounts'),'','','0'));
					$this->tpl->set_var('accounts_img_link', $this->get_href());
					$this->set_href_link($accounts_link);
					$this->set_href_clickme(lang('Accounts'));
					$this->tpl->set_var('accounts_txt_link', $this->get_href());
					//Create Settings Button
					$this->set_href_link($email_prefs_link);
					$this->set_href_clickme($this->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$icon_theme.'/customize-'.$icon_size,'_on'),lang('Settings'),'','','0'));
					$this->tpl->set_var('settings_img_link', $this->get_href());
					$this->set_href_link($email_prefs_link);
					$this->set_href_clickme(lang('Settings'));
					$this->tpl->set_var('settings_txt_link', $this->get_href());
					//Check for folder support and create Folder Button
					if ($has_folders == True)
					{
						//Create Folder Image Link
						$this->set_href_link($folders_link);
						$this->set_href_clickme($this->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$icon_theme.'/folder-'.$icon_size,'_on'),lang('Folders'),'','','0'));
						$this->tpl->set_var('folders_img_link', $this->get_href());
						//Create Folder Text Link
						$this->set_href_link($folders_link);
						$this->set_href_clickme(lang('Folders'));
						$this->tpl->set_var('folders_txt_link', $this->get_href());
					}
					else
					{
						$this->tpl->set_var('folders_img_link', '&nbsp;');
						$this->tpl->set_var('folders_txt_link', '&nbsp;');
					}
					break;
				}
			// WAIT if this is NOT IMAP then we can NOT search
			// use the has_folders var from above, it should be a good enough indicator
			if ($has_folders == False)
			{
					$this->tpl->set_var('search_img_link', '&nbsp;');
					$this->tpl->set_var('search_txt_link', '&nbsp;');
			}
			// make the 1st row
			$this->toolbar_row_one = $this->tpl->parse('V_toolbar_row_one','B_toolbar_row_one');
			// END TOOL BAR ROW 1
			
			// BEGIN TOOL BAR ROW2 
			// ---- folders switchbox  ----
			//<form name="folders_cbox" action="/mail/index.php?menuaction=email.uiindex.index" method="post">
			if ($has_folders == True)
			{
				$this->new_form();
				$this->set_form_name('folders_cbox');
				$this->set_form_action($GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.uiindex.index')));
				$this->set_form_method('post');
				$this->tpl->set_var('form_folders_cbox_opentag', $this->get_form());
				$this->tpl->set_var('folders_combobox', $this->all_folders_combobox());
				$this->tpl->set_var('form_folders_cbox_closetag', $this->form_closetag());
			}
			else
			{
				$this->tpl->set_var('form_folders_cbox_opentag', '');
				$this->tpl->set_var('folders_combobox', '&nbsp;');
				$this->tpl->set_var('form_folders_cbox_closetag', '');
			}
			// associated image is still filled from row one
			
			
			// ---- account switchbox  ----
			// <form name="acctbox" action="/mail/index.php?menuaction=email.uiindex.index" method="post">
			$this->new_form();
			$this->set_form_name('accounts_cbox');
			$this->set_form_action($GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.uiindex.index')));
			$this->set_form_method('post');
			$this->tpl->set_var('form_acctbox_opentag', $this->get_form());
			$this->tpl->set_var('acctbox_combobox', $this->all_accounts_combobox());
			// associated image is still filled from row one
			
			// show he user a message if this property is filled, if empty then output a nbsp for html sanity
			if (trim($this->get_toolbar_msg()) != '')
			{
				$toolbar_report_msg = $this->get_toolbar_msg();
			}
			else
			{
				$toolbar_report_msg = '&nbsp;';
			}
			$this->tpl->set_var('toolbar_report_msg', $toolbar_report_msg);
			
			
			// make the 2nd row AND the 3rd row
			$this->toolbar_row_two = $this->tpl->parse('V_toolbar_row_two','B_toolbar_row_two');
			
			return $this->toolbar_row_one . $this->toolbar_row_two;
			
		}
		
		
		/*!
		@function all_folders_combobox
		@abstract high level function, uses functions in mail_msg and this class html_widgets to make an acct switchbox 
		UNDER DEVELOPMENT.
		@param $form_reference (string) this bombobox sets an "onChange" event, which will submit the form you put here. 
		Default value is "document.folders_cbox.submit()" where "folders_cbox" is the default value 
		for the $form_reference param. 
		@param $is_move_box (boolean) OPTIONAL default is False, use is making a Move Messages To combo box, 
		which requires a different cbox name and different first line text.
		@result string representing an HTML listbox widget 
		@author Angles
		@discussion The first item in this folder combo box tells the user to "pick a folder to change to", and has 
		no "value", the value is an empty string, this is more like a label than a combobox item. 
		@access private, maybe made public
		*/
		function all_folders_combobox($form_reference='',$is_move_box=False,$skip_fldball='',$first_line_txt='')
		{
			if ($form_reference == '')
			{
				$form_reference = 'folders_cbox';
			}
			$acctnum = $GLOBALS['phpgw']->msg->get_acctnum();
			
			$this->new_combobox();
			if ($is_move_box)
			{
				// right now ONLY the "Move Message To" combo box needs to use this
				$this->set_cbox_name('to_fldball_fake_uri');
				$this->set_cbox_onChange('do_action(\'move\')');
				if ($first_line_txt)
				{
					// right now ONLY the Message View page "Move This Message To" combo box uses this
					$this->set_cbox_item('', $first_line_txt);
				}
				else
				{
					$this->set_cbox_item('', lang('move selected messages into'));
				}
			}
			else
			{
				$this->set_cbox_name('fldball_fake_uri');
				// default is "document.folders_cbox.submit()"
				$this->set_cbox_onChange('document.'.$form_reference.'.submit()');
				// set_cbox_item(value, text, selected(optional, boolean, default false)
				$this->set_cbox_item('', lang('switch current folder to'));
			}
			
			// get the actual list of folders we are going to put into the combobox
			//$folder_list = $GLOBALS['phpgw']->msg->get_folder_list();
			$folder_list = $GLOBALS['phpgw']->msg->get_arg_value('folder_list', $acctnum);
			//$folder_list =& $GLOBALS['phpgw']->msg->get_arg_value_ref('folder_list');
			
			$listbox_show_unseen = $GLOBALS['phpgw']->msg->get_isset_pref('newmsg_combobox', $acctnum);
			
			for ($i=0; $i<count($folder_list);$i++)
			{
				// folder long needs urlencoding ONCE, string can NOT be plain and can NOT be urlencoded more once.
				//$folder_long = $GLOBALS['phpgw']->msg->ensure_one_urlencoding($folder_list[$i]['folder_long']);
				$folder_long = $GLOBALS['phpgw']->msg->prep_folder_out($folder_list[$i]['folder_long']);
				// for display to the user, if this is the INBOX, then translate that using lang INBOX
				if ($folder_list[$i]['folder_short'] == 'INBOX')
				{
				    //$folder_short = lang('INBOX');
					// try this for common folder related lang strings
					$folder_short = $GLOBALS['phpgw']->msg->get_common_langs('lang_inbox');
				}
				else
				{
					// not inINBOX, so use actual folder name, no translation for the user is done
					$folder_short = $folder_list[$i]['folder_short'];
				}
				$folder_acctnum = $folder_list[$i]['acctnum'];
				$skip_me = False;
				if ($skip_fldball)
				{
					// move folder lists usually skip the current folder because you can not move to current folder
					if (($skip_fldball['folder'] == $folder_long)
					&& ($skip_fldball['acctnum'] == $acctnum))
					{
						$skip_me = True;
					}
				}
				if ($skip_me)
				{
					continue;
				}
				
				if ($listbox_show_unseen == True)
				{
					$tmp_fldball = array();
					$tmp_fldball['folder'] = $folder_long;
					$tmp_fldball['acctnum'] = $folder_acctnum;
					$folder_status = $GLOBALS['phpgw']->msg->get_folder_status_info($tmp_fldball);
					$folder_unseen = number_format($folder_status['number_new']);
					$tmp_fldball = array();
				}
				
				// set_cbox_item(value, text, selected(optional, boolean, default false)
				if ($listbox_show_unseen == True)
				{
					$this->set_cbox_item('&folder='.$folder_long.'&acctnum='.$folder_acctnum, $folder_short . ' (' . $folder_unseen . ')');
				}
				else
				{
					$this->set_cbox_item('&folder='.$folder_long.'&acctnum='.$folder_acctnum, $folder_short);
				}
			}
			return $this->get_combobox();
			
		}
		
		
		/*!
		@function all_folders_mega_combobox
		@abstract high level function, uses functions in mail_msg and this class html_widgets to make a listbox for 
		all folders in all accounts.  DEPRECIATED. 
		@param $form_reference (string) this combobox sets an "onChange" event, which will submit the form you put here. 
		Default value is "document.folders_cbox.submit()" where "" is the default value for the $form_reference param. 
		DEPRECIATED in favor of all_folders_megalist.
		@result string representing an HTML listbox widget 
		@author Angles
		@discussion ?
		@access private, maybe made public
		*/
		function all_folders_mega_combobox_OLD($form_reference='')
		{
				$feed_args = Array(
					'mailsvr_stream'	=> '',
					'pre_select_folder'	=> $pre_select_folder,
					'pre_select_folder_acctnum' => $pre_select_folder_acctnum,
					'skip_folder'		=> '',
					'show_num_new'		=> $listbox_show_unseen,
					'widget_name'		=> $folder_listbox_name,
					'folder_key_name'	=> 'folder',
					'acctnum_key_name'	=> 'acctnum',
					'on_change'			=> '',
					'first_line_txt'	=> lang('if fileto then select destination folder')
				);
				$folder_listbox = $GLOBALS['phpgw']->msg->folders_mega_listbox($feed_args);
		}
		
		/*!
		@function new_all_folders_megalist
		@abstract Resets all Properties all_folders_megalist
		@discussion Delphi style OOP property GetSet functions are used, this resets them all.
		@author Angles
		*/
		function new_all_folders_megalist()
		{
				$this->F_megalist_form_reference = '';
				// this is the only think that actually needs a value
				$this->F_megalist_widget_name = 'not_provided';
				$this->F_megalist_preselected_fldball = '';
				$this->F_megalist_skip_fldball = '';
				// the first item can be used to display instructional text to the user
				$this->F_megalist_first_item_text = '';
		}
		
		/*!
		@function prop_megalist_form_reference
		@abstract Property function form_reference for folders_mega_listbox, form_reference is used in onChange JS.
		@discussion Delphi style OOP property GetSet function. 
		@author Angles
		*/
		function prop_megalist_form_reference($form_reference='')
		{
			if ($form_reference)
			{
				$this->F_megalist_form_reference = $form_reference;
			}
			return $this->F_megalist_form_reference;
		}
		
		/*!
		@function prop__megalist_widget_name
		@abstract Property function widget name for folders_mega_listbox
		@discussion Delphi style OOP property GetSet function. 
		@author Angles
		*/
		function prop_megalist_widget_name($widget_name='')
		{
			if ($widget_name)
			{
				$this->F_megalist_widget_name = $widget_name;
			}
			return $this->F_megalist_widget_name;
		}
		
		/*!
		@function prop_megalist_preselected_fldball
		@abstract Property function preselected folder (in fldball form) for folders_mega_listbox
		@discussion Delphi style OOP property GetSet function. 
		@author Angles
		*/
		function prop_megalist_preselected_fldball($fldball='')
		{
			if ((isset($fldball))
			&& ($fldball['folder'] != '')
			&& ((string)$fldball['acctnum'] != ''))
			{
				$this->F_megalist_preselected_fldball = $fldball;
			}
			return $this->F_megalist_preselected_fldball;
		}
		
		/*!
		@function prop_megalist_skip_fldball
		@abstract Property function folder (in fldball form) to NOT show in the folders_mega_listbox
		@discussion Delphi style OOP property GetSet function. 
		@author Angles
		*/
		function prop_megalist_skip_fldball($fldball='')
		{
			if ((isset($fldball))
			&& ($fldball['folder'] != '')
			&& ((string)$fldball['acctnum'] != ''))
			{
				$this->F_megalist_skip_fldball = $fldball;
			}
			return $this->F_megalist_skip_fldball;
		}
		
		/*!
		@function prop__megalist_widget_name
		@abstract Property function for folders_mega_listbox, the first item can be used to display instructional text to the user
		@discussion Delphi style OOP property GetSet function. 
		@author Angles
		*/
		function prop_megalist_first_item_text($first_item_text='')
		{
			if ($first_item_text)
			{
				$this->F_megalist_first_item_text = $first_item_text;
			}
			return $this->F_megalist_first_item_text;
		}
		
		/*!
		@function all_folders_megalist
		@abstract All accounts All Folders in a html listbox
		@discussion UNDER DEVELOPMENT, right now the leading candidate to be THE folder list 
		function, but now sure yet. 
		@author Angles
		*/
		function all_folders_megalist()
		{
			$debug_mega_listbox = 0;
			//$debug_mega_listbox = 3;
			
			if ($debug_mega_listbox > 0) { echo 'folders_mega_listbox('.__LINE__.'): ENTERING<br />'; }
			
			$this->new_combobox();
			$this->set_cbox_name($this->F_megalist_widget_name);
			
			// there is NO ON change right now, this is currently used on the filters page, we do not need action onChange there
			// default is "document.mega_folders_cbox.submit()"
			//$this->set_cbox_onChange('document.'.$form_reference.'.submit()');
			
			// set_cbox_item(value, text, selected(optional, boolean, default false)
			if ($this->F_megalist_first_item_text)
			{
				$this->set_cbox_item('', $this->F_megalist_first_item_text);
			}
			
			// we need the loop to include the default account AS WELL AS the extra accounts
			for ($x=0; $x < count($GLOBALS['phpgw']->msg->extra_and_default_acounts); $x++)
			{
				$this_acctnum = $GLOBALS['phpgw']->msg->extra_and_default_acounts[$x]['acctnum'];
				$this_status = $GLOBALS['phpgw']->msg->extra_and_default_acounts[$x]['status'];
				// do not enable this yet, maybe later
				//$listbox_show_unseen = $GLOBALS['phpgw']->msg->get_isset_pref('newmsg_combobox', $acctnum);
				$listbox_show_unseen = False;
				if ($this_status != 'enabled')
				{
					// Do Nothing, This account is not in use
					if ($debug_mega_listbox > 1) { echo 'folders_mega_listbox('.__LINE__.'): $this_acctnum ['.$this_acctnum.'] is not in use, so skip folderlist<br />'; }
				}
				else
				{
					$folder_list = $GLOBALS['phpgw']->msg->get_arg_value('folder_list', $this_acctnum);
					if ($debug_mega_listbox > 1) { echo 'folders_mega_listbox('.__LINE__.'): $this_acctnum ['.$this_acctnum.'] IS enabled, got folder list<br />'; }
					if ($debug_mega_listbox > 2) { echo 'folders_mega_listbox('.__LINE__.'): $folder_list for $this_acctnum ['.$this_acctnum.'] DUMP<pre>'; print_r($folder_list); echo '</pre>'; }
					
					// iterate thru the folder list for this acctnum
					for ($i=0; $i<count($folder_list);$i++)
					{
						$folder_long = $folder_list[$i]['folder_long'];
						$folder_long_preped_out = $GLOBALS['phpgw']->msg->prep_folder_out($folder_long);
						$folder_short = $folder_list[$i]['folder_short'];
						// yes we need $folder_acctnum to help make the "folder ball", yes I know it *should* be the same as $this_acctnum
						$folder_acctnum = $folder_list[$i]['acctnum'];
						
						// this logic determines we should not include a certain folder in the combobox list
						if (($this->F_megalist_skip_fldball)
						&& ($folder_long_preped_out == $this->F_megalist_skip_fldball['folder'])
						&& ($folder_acctnum == $this->F_megalist_skip_fldball['acctnum']))
						{
							// Do Nothing, this folder should not be included
							if ($debug_mega_listbox > 1) { echo 'folders_mega_listbox('.__LINE__.'): skipping $this->F_megalist_skip_fldball ['.htmlspecialchars(serialize($this->F_megalist_skip_fldball)).'] has been matched<br />'; } 
						}
						else
						{
							// this logic determines if the combobox should be initialized with certain folder already selected
							// we use "folder short" as the comparator because that way at least we know we are comparing syntatic-ally similar items
							if (($this->F_megalist_preselected_fldball)
							&& ($folder_long_preped_out == $this->F_megalist_preselected_fldball['folder'])
							&& ($folder_acctnum == $this->F_megalist_preselected_fldball['acctnum']))
							{
								$preselected = True;
							}
							else
							{
								$preselected = False;
							}
							
							if ($listbox_show_unseen == True)
							{
								$tmp_fldball = array();
								$tmp_fldball['folder'] = $folder_long;
								$tmp_fldball['acctnum'] = $folder_acctnum;
								$folder_status = $GLOBALS['phpgw']->msg->get_folder_status_info($tmp_fldball);
								$folder_unseen = number_format($folder_status['number_new']);
								// complete the text here so we do not need another if ... then below
								$folder_unseen = ' ('. $folder_unseen.')';
								$tmp_fldball = array();
							}
							else
							{
								$folder_unseen = '';
							}
							
							$option_value = '&folder='.$folder_long_preped_out.'&acctnum='.$folder_acctnum;
							//$option_value =	'&folder='.$folder_long.'&acctnum='.$folder_acctnum;
							// if $folder_unseen has anything it gets added to the string here
							$text_blurb = '['.$folder_acctnum.'] '.$folder_short.$folder_unseen;
							
							// set_cbox_item(value, text, selected(optional, boolean, default false)
							$this->set_cbox_item($option_value, $text_blurb, $preselected);
						}
					}
				}
			}
			if ($debug_mega_listbox > 0) { echo 'folders_mega_listbox('.__LINE__.'): LEAVING<br />'; }
			return $this->get_combobox();
		}
		

		
		/*!
		@function all_accounts_combobox
		@abstract UNDER DEVELOPMENT
		@author Angles
		@discussion the "values" are in the form of a URI request string, since a combobox can 
		only submit a single string as its value. This way we put alot of information if the form 
		of the URI request and use php function "parse_str" to "recover" all this data on the submit. 
		Accounts have a "status" associated with them, can be "enabled", "disabled", or "empty". 
		In this combobox we show "enabled" and "disabled" accounts, not "empty" accounts. 
		NOTE "disabled" really has no use, and "empty" I am not sure if that is ever used anywhere at all. 
		Also note that a "disabled" account should never be "pre-selected" in this combobox, which seems logical.
		Almost always there is an email account that can be considered "active" because the user is viewing its 
		data (folders, messages, its preferences) or if the user is composing a message then the last "active" 
		account is considered the account which this mail will be "from". Therefor, generally all mail activity 
		have an account that it applies to. So when this combobox comes across the account that is currently 
		"active", that account will be "pre-selected" in the combobox. This serves two purposes, ONE, the user 
		can not swicth to an account that is currently the "active" account, the user can only switch do 
		a different account, and TWO, this gives the user visual feedback about which account is currently 
		"active", on some pages, such as the compose page, this remines the user who the mail will be "from", 
		i.e. which account sent the mail. In making this thing we iterate thru the "extra_and_default_acounts list", 
		which is an numbered array whose members are structured array data describing the account. 
		*/
		function all_accounts_combobox()
		{
			// $GLOBALS['phpgw']->msg->ex_accounts_count
			// $GLOBALS['phpgw']->msg->extra_accounts
			
			//$debug_widget = True;
			$debug_widget = False;
			$acctnum = $GLOBALS['phpgw']->msg->get_acctnum();
			
			$this->new_combobox();
			$this->set_cbox_name('fldball_fake_uri');
			$this->set_cbox_onChange('document.accounts_cbox.submit()');
			
			for ($i=0; $i < count($GLOBALS['phpgw']->msg->extra_and_default_acounts); $i++)
			{
				$this_acctnum = $GLOBALS['phpgw']->msg->extra_and_default_acounts[$i]['acctnum'];
				$this_acct_status = $GLOBALS['phpgw']->msg->extra_and_default_acounts[$i]['status'];
				$this_acct_fullname = $GLOBALS['phpgw']->msg->get_pref_value('fullname', $this_acctnum);
				
				if ($this_acct_status == 'disabled')
				{
					// set_cbox_item(value, text, selected(optional, boolean, default false)
					$this->set_cbox_item('&folder=INBOX&acctnum=0', 
						lang('account').' ['.$this_acctnum.'] '.lang('disabled'));
				}
				elseif ($this_acct_status == 'enabled')
				{
					// set_cbox_item(value, text, selected(optional, boolean, default false)
					if ($GLOBALS['phpgw']->msg->get_pref_value('account_name', $this_acctnum))
					{
						$this->set_cbox_item('&folder=INBOX&acctnum='.$this_acctnum, 
						$GLOBALS['phpgw']->msg->get_pref_value('account_name', $this_acctnum),
						((string)$acctnum == (string)$this_acctnum));
							
					} else {
						$this->set_cbox_item('&folder=INBOX&acctnum='.$this_acctnum,
							lang('account').' '.$this_acctnum.':  '.$this_acct_fullname, 
							((string)$acctnum == (string)$this_acctnum));
					}
				}
			}
			return $this->get_combobox();
		}

		/*!
		@function auto_refresh
		@example I know of 3 ways to get a page to reload, 2 of those ways are pretty much the same
		1. the http header 
			Refresh: 5;
		2. the META http-equiv 
			&lt;META HTTP-EQUIV="Refresh" CONTENT="60"&gt>
		both 1 and 2 have the same effect as hitting the "reload" button, which in *many* browsers will
		force a re-download of all the images on the page, i.e. the browser will NOT use the cached images
		3. java script combo of "window.setTimeout" with "window.location"
			window.setTimeout('window.location="http://example.com/phpgw/email/index.php"; ',1800000);
		method 3 is the only one I know of that will use the images from the cache.
		also, 3 takes a reload value in miliseconds, so a value of 180000 is really 3 minutes
		ALSO, use if..then code to only auto-refresh certain pages, such as email/index.php
		@author Angles
		*/
		function auto_refresh($reload_me='', $feed_refresh_ms='')
		{
			if ($GLOBALS['phpgw']->msg->get_isset_pref('refresh_ms'))
			{
				$pref_refresh_ms = $GLOBALS['phpgw']->msg->get_pref_value('refresh_ms');
			}
			else
			{
				$pref_refresh_ms = '';
			}
			// which do we use 
			$refresh_ms = '';
			if ($feed_refresh_ms)
			{
				$refresh_ms = $feed_refresh_ms;
			}
			elseif ($pref_refresh_ms)
			{
				$refresh_ms = $pref_refresh_ms;
			}
			else
			{
				// user pref is NOT to refresh AND we were not given another value to use
				// LEAVING
				return '';
			}
			
			/*
			// if NOT supplied a "reload_me" URI then we must figure one out
			if ($reload_me == '')
			{
				if ((stristr($GLOBALS['PHP_SELF'], '/email/index.php'))
				||  (	((isset($GLOBALS['phpgw']->msg->ref_GET['menuaction']))
					&& (stristr($GLOBALS['phpgw']->msg->ref_GET['menuaction'], 'email.uiindex.index')))
					)
				)
				{
					if ((isset($GLOBALS['phpgw_info']['flags']['email_refresh_uri']))
					&& ($GLOBALS['phpgw_info']['flags']['email_refresh_uri'] != ''))
					{
						$reload_me = $GLOBALS['phpgw']->link('/index.php',$GLOBALS['phpgw_info']['flags']['email_refresh_uri']);
					}
					else
					{
						$reload_me = $GLOBALS['phpgw']->link('/email/index.php');
					}
				}
				elseif (eregi("^.*\/home\.php.*$",$GLOBALS['PHP_SELF']))
				{
					$reload_me = $GLOBALS['phpgw']->link('/home.php');			
				}
			}
			*/
			
			// reality check
			$int_refresh_ms = (int)$refresh_ms;
			if ($int_refresh_ms < 60000)
			{
				// less than 1 minute us BS, use a fallback value of 4 minutes
				$refresh_ms = 240000;
			}
			
			// make the $refresh_ms into a string
			$refresh_ms = (string)$refresh_ms;
			// now if we have a reload_me URI, then
			// make the JS command string if necessary
			if (($reload_me != '')
			&& ($refresh_ms != ''))
			{
					
				//$reload_me_full = $GLOBALS['phpgw']->link('/index.php',$reload_me);
				// set refresh time in miliseconds  (1000 = 1 sec)  (180000 = 180 sec = 3 minutes)
				//  ( 240000 = 240 sec = 4 min)   (300000 = 5 min)   (600000 = 10 min)
				//$refresh_ms = '240000';

				$oArgs = '{';
				foreach($reload_me as $key => $value)
				{
					$oArgs .= str_replace(array('fldball[',']'),'',$key) . ":'" . $value . "',\r\n";
				}
				$oArgs .= '};';

	/*			$reload_js = 
					 '<script language="javascript">'."\r\n"
					.'window.setTimeout('."'".'window.location="'
					.$reload_me_full.'"; '."'".','.$refresh_ms.');'."\r\n"
					.'</script>'."\r\n";
	*/	
				$reload_js = 
					 '<script language="javascript">'."\r\n"
					.' var oArgs = ' . $oArgs . "\r\n"
					." var strURL = phpGWLink('/index.php', oArgs); \r\n"
					.'window.setTimeout('."'".'window.location=strURL;'."'".','.$refresh_ms.');'."\r\n"
					.'</script>'."\r\n";

			}
			else
			{
				// we have no URI to reload
				$reload_js = '';
			}
			// returning  $reload_js which may be '' if we did not have enough info
			return $reload_js;
		}
		
		/**************************************************************************\
		*	GENERIC ERROR REPORT
		\**************************************************************************/	
		
		/*!
		@capability GENERIC ERROR REPORT
		@discussion This is not really a widget, but this is a good place to put this. In cases such 
		as a login error, it is not user friendly to output a text only page with an echod out error. 
		At the very least we should output the template as usual, and insert the error text where 
		the page content would go. This way the user has all the links and buttons to click on 
		to get out of the error page.
		@author Angles
		*/
		
		/*!
		@function init_error_report_values
		@abstract Initialize error report with default text, please call this first.
		@discussion Simple function to initialize the error report with some default text. 
		I can not imagine how you could use this twice since the actual error report is a full 
		template page output, BUT still this is how the initial default values are filled. Please 
		call this first.
		@author Angles
		*/
		function init_error_report_values()
		{
			$this->F_error_report_text = lang('error text not provided');
			$this->F_go_somewhere_link = '';
			$go_home_url = $GLOBALS['phpgw']->link('/home.php');
			$go_home_text = lang('click here to return to your home page.');
			$this->F_go_home_link = '<a href="'.$go_home_url.'">'.$go_home_text.'</a>';
		}
		
		/*!
		@function prop_error_report_text
		@abstract Set or Get the error report text.
		@param $error_report_text (string) the error to show the user.
		@param $append (boolean) if true, then add to the error text. If false, replace error text.
		@discussion It initialized with a generic "not provided" string, 
		which you change to the real error report text with this function. 
		It checks for the default generic text, and always replaces it even if append it true. 
		So this way you can specify append but still never accidently keep the mindless 
		default error text. 
		@author Angles
		*/
		function prop_error_report_text($error_report_text='', $append=False)
		{
			if ($error_report_text)
			{
				// ALWAYS make sure to clear the mindless default text before you append
				if (($this->F_error_report_text == lang($this->F_mindless_default_txt))
				|| ($append == False))
				{
					$this->F_error_report_text = $error_report_text;
				}
				else
				{
					$this->F_error_report_text .= $error_report_text;
				}
			}
			return $this->F_error_report_text;
		}
		
		/*!
		@function prop_go_somewhere_link
		@abstract Set or Get the go somewhere link.
		@param $go_somewhere_url (string in url form) a helpful link to show the user. 
		@param $go_somewhere_text (string in url form) the text for the HREF for this helpful link to show the user. 
		@discussion This is optional, in any case the "go home link" will be displayed, 
		But with this function you can additionally show a link to something useful given 
		the error the user just encountered, perhaps to the preferences page, for example. 
		The two params are required for this function to make the HREF.
		@author Angles
		*/
		function prop_go_somewhere_link($go_somewhere_url='', $go_somewhere_text='')
		{
			if (($go_somewhere_url) 
			&& ($go_somewhere_text))
			{
				$this->set_href_link($go_somewhere_url);
				$this->set_href_clickme($go_somewhere_text);
				$this->F_go_somewhere_link = $this->get_href();
			}
			return $this->F_go_somewhere_link;
		}
		
		/*!
		@function display_error_report_page
		@abstract A complete output of the template with your error report in the content section. 
		@param $do_exit (boolean) if empty or false, then this function will NOT call common exit, 
		if filled or True, it will call msg end_request and common EXIT.
		@discussion Handles all necessary template parsing, you should set the error text and helpful 
		href, call this function. Param $do_exit is useful if you are calling this report from within 
		the msg object itself, then this function will call msg end_request and then the common phpgw exit.
		@author Angles
		*/
		function display_error_report_page($do_exit='')
		{
			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			$GLOBALS['phpgw_info']['flags']['noappheader'] = True;
			$GLOBALS['phpgw_info']['flags']['noappfooter'] = True;
			$GLOBALS['phpgw']->common->phpgw_header(True);
			$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);

			$GLOBALS['phpgw']->template->set_file(array
			(
				'T_error_report' => 'error_report.tpl'
			));
			$GLOBALS['phpgw']->template->set_var('error_report_text', $this->prop_error_report_text());
			$GLOBALS['phpgw']->template->set_var('go_somewhere_link', $this->prop_go_somewhere_link());
			$GLOBALS['phpgw']->template->set_var('go_home_link', $this->F_go_home_link);
			$GLOBALS['phpgw']->template->pfp('out','T_error_report');
			// do we exit the script here?
			if ($do_exit)
			{
				if (is_object($GLOBALS['phpgw']->msg))
				{
					$GLOBALS['phpgw']->msg->end_request();
					unset($GLOBALS['phpgw']->msg);
				}
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
		}
		
		/*!
		@function get_geek_bar
		@abstract TESTING goes on bottom of index page
		@author Angles
		*/
		function get_geek_bar()
		{
			//disabling "geekbar"
			return '';

			$row_on = $GLOBALS['phpgw_info']['theme']['row_on'];
			$this_server_type = $GLOBALS['phpgw']->msg->get_pref_value('mail_server_type');
			if (extension_loaded('imap') && function_exists('imap_open'))
			{
				$library_usage = 'builtin';
			}
			else
			{
				$library_usage = 'AM sockets';
			}
			$anglemail_table_exists = 'installed';
			if ($GLOBALS['phpgw']->msg->so->so_am_table_exists() == False)
			{
				$anglemail_table_exists = 'NOT '.$anglemail_table_exists;
			}
			$compression = 'NOT available';
			//if (function_exists('bzcompress'))
			//{
			//	$compression = 'bz2';
			//}
			//else
			if (function_exists('gzcompress'))
			{
				$compression = 'gzip';
			}
			$spell_available = 'available';
			if (function_exists('pspell_check') == False)
			{
				$spell_available = 'NOT '.$spell_available;
			}
			if ($GLOBALS['phpgw']->msg->phpgw_before_xslt == True)
			{
				$using_xslt = 'no';
			}
			else
			{
				$using_xslt = 'yes';
			}
			
			// did we connect
			$accts_connected = '';
			// put together a list of all enabled accounts so we will check them for an open stream
			for ($i=0; $i < count($GLOBALS['phpgw']->msg->extra_and_default_acounts); $i++)
			{
				if ($GLOBALS['phpgw']->msg->extra_and_default_acounts[$i]['status'] == 'enabled')
				{
					$this_acctnum = (int)$GLOBALS['phpgw']->msg->extra_and_default_acounts[$i]['acctnum'];
					if (($GLOBALS['phpgw']->msg->get_isset_arg('mailsvr_stream', $this_acctnum) == True)
					&& ((string)$GLOBALS['phpgw']->msg->get_arg_value('mailsvr_stream', $this_acctnum) != ''))
					{
						$accts_connected .= (string)$this_acctnum.',';
					}
				}
			}
			// get rid of trailing , if it exists
			if (stristr($accts_connected, ','))
			{
				$accts_connected = substr($accts_connected,0,-1);
				$did_connect = 'yes ('.$accts_connected.')';
				
			}
			else
			{
				$did_connect = 'no';
			}
			
			$geek_bar = 
			'<br />
			<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
			<tr bgcolor="'.$row_on.'" class="row_on">
				<td width="100%" align="left">'."\r\n"
					//.'<small style="font-size: 10pt;">'
					.'<small style="font-size: xx-small;">'
					.'<font color="brown">GeekBar:</font> '
					.'Server Type: ['.$this_server_type.'] -- '
					.'IMAP library: ['.$library_usage.'] -- '
					.'AngleMail Table: ['.$anglemail_table_exists.'] -- '
					.'compression: ['.$compression.'] -- '
					.'spelling: ['.$spell_available.'] -- '
					.'using XSLT: ['.$using_xslt.'] -- '
					.'did connect: ['.$did_connect.'] '
					.'</small>'
				."\r\n"
			.'	</td>
			</table>';
			return $geek_bar;
		}
	}

