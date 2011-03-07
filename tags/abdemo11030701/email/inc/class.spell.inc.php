<?php
	/**
	* EMail - SpellChecking Functions
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
	* Handles spell checking for email messages. Give it all necessary data and it 
	* handles the rest. All class params are private unless an OOP style access 
	* function is provided.
	*
	* Provide this class with any of the above vars that you have. Subject and 
	* Body will be spellchecked, but the other data is needed because this class 
	* handles the rest of the logic from spell checking, to user review of 
	* spellcheck, to applying the users choices to the body, only then is the 
	* message handed off to another object, either class send for sending, or 
	* class compose if the user wants to edit the text some more. Calling process 
	* MUST set any vars it wants preserved through the spellcheck process, they 
	* will be put in the spellcheck page form.
	* @package email
	*/	
	class spell
	{	
		var $public_functions = array(
			'basic_spcheck'	=> True,
			'spell_review'	=> True,
			'spell_finished' => True
		);
		
		/**
		 * @var integer $max_suggest Maximum number is suggestions to show in a combobox
		 * Max suggest is zero based, to 4 suggestions = max_suggest of 3
		 */
		var $max_suggest=5;
		/**
		 * @var object $spell_svc The spell service access class, a dummy module if pspell is not compiled in
		 */
		var $spell_svc;
		/**
		 * @var boolean $can_spell This is picked up from the spell service module, a dummy module will report False. Read Only, Access with ->get_can_spell()
		 */
		var $can_spell = False;
		/**
		 * @var integer $sp_feed_type SP_FEED_UNKNOWN or SP_FEED_WORDS or SP_FEED_LINES, defined in file class.spell_struct
		 */
		var $sp_feed_type=1;
		/**
		 * @var integer $pspell_link A pointer kind of thing you get when you create a new pspell dictionary link.
		 */
		var $pspell_link;
		/**
		 * @var object $my_validator Used to help decide what words to not bother spellchecking, like URLs.
		 */
		var $my_validator;
		/**
		 * @var object $widgets
		 */
		var $widgets;
		var $msg_bootstrap;
		/**
		 * @var string $user_lang Obtained from "phpgw_info", used when creating the pspell disctionary link. Read Only, Access with ->get_user_lang()
		 */
		var $user_lang='';
		/**
		 * @var string $reject_reason When this class decides a word is not worth checking, it fills this with the reason, ex. "do not check URLs"
		 */
		var $reject_reason='';
		
		// the form action URI will carry these vars for us:
		// sort, order, start, fldball / msgball, fwd_proc
		
		// some of the following we must preserve in hidden var
		/**
		 * @var array $preserve_tokens 
		 * A list of GPC vars we typically want to preserve thru the spellchecking 
		 * process. However, it is too limiting to had a hard-coded list, need to 
		 * allow more flexibility.
		 * @internal Scheduled For Depreciation, but still used. 
		 */
		var $preserve_tokens=array();
		/**
		 * @var array $preserve_vars 
		 * Use OOP access functions ->set_preserve_var and ->get_preserve_vars. 
		 * Currently, you can set any vars you want, but only those in the 
		 * $preserve_tokens will be preserved as hiddenvars. 
		 * @todo NEEDS FIXING. 
		 */
		var $preserve_vars=array();
		//var $action='';
		//var $from='';
		//var $sender='';
		//var $to='';
		//var $cc='';
		//var $bcc='';
		//var $msgtype='';
		/**
		 * @var string $subject Use OOP access functions ->set_subject and ->get_subject.
		 */
		var $subject='';
		/**
		 * @var string $body_orig
		 * Use OOP access functions ->set_body_orig and ->get_body_orig. 
		 * This is record of the original message before any spell check changes.
		 */
		var $body_orig='';
		var $body_array=array();
		var $bad_words=array();
		/**
		 * @var string $body_display 
		 * Scheduled for depreciation because the function "prep_body_display" 
		 * directly returns this data and there us no readon to keep it stored in 
		 * this param. "prep_body_display" is quite useful, do not limit it by 
		 * bounding it to this param.
		 * @internal ACCESS ONLY use OOP access functions  ->get_body_display.
		 * @access private
		 */
		var $body_display='';
		var $body_with_suggest='';
		var $form_open_tags='';
		
		
		//var $debug = 3;
		var $debug_init = 0;
		var $debug_spellcheck = 0;
		var $debug_spell_finished = 0;
		
		
		/**
		* Constructor
		*/
		function spell()
		{
			if ($this->debug_init > 0) { echo 'ENTERING: email.spell.CONSTRUCTOR'.'<br />'."\r\n"; }
			
			/*!
			@class requires including header utility file spell_struct
			@discussion  class spell needs the public shared C-Style Include .h file, which 
			holds public data structure classes for class email spell
			*/
			$required_file = 'spell_struct';
			require_once(PHPGW_INCLUDE_ROOT.'/'.$GLOBALS['phpgw_info']['flags']['currentapp'].'/inc/class.'.$required_file.'.inc.php');
			
			$this->msg_bootstrap = CreateObject("email.msg_bootstrap");
			
			// NOTE: keep an eye on whether PHP calls this constructors too many times, means code elsewhere needs fixing.
			// FIXME : what if we want to preserve other stuff ? Get rid of this array and use for each loop to iterate thru whatever the user wants to preserve
			$this->preserve_tokens = array(
				0 => 'action',
				// why is this different, "orig_action" can have the value "new" meaning new mail
				// whereas plain old "action" can not tell us of a new mail situation, not right now anyway
				// darn, I'm not sure this is needed here, is not this value kept in the URI or does that not matter here
				1 => 'orig_action',
				2 => 'from',
				3 => 'sender',
				4 => 'to',
				5 => 'cc',
				6 => 'bcc',
				7 => 'msgtype',
				8 => 'attach_sig',
				9 => 'req_notify'
			);
			$this->preserve_vars['action'] = '';
			$this->preserve_vars['orig_action'] = '';
			$this->preserve_vars['from'] = '';
			$this->preserve_vars['sender'] = '';
			$this->preserve_vars['to'] ='';
			$this->preserve_vars['cc'] = '';
			$this->preserve_vars['bcc'] = '';
			$this->preserve_vars['msgtype'] = '';
			$this->preserve_vars['attach_sig'] = '';
			$this->preserve_vars['req_notify'] = '';
			
			$this->subject = '';
			$this->body_orig = '';
			$this->body_array = array();
			$this->body_display = '';
			
			
			//$this->my_validator = CreateObject("phpgwapi.validator");
			$this->widgets = CreateObject("email.html_widgets");
			$this->user_lang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
			if ($this->user_lang == '')
			{
				$this->user_lang = 'en';
			}
			
			/* -----  is PSPELL compiled into PHP */
			//if ('one' == 'two')
			if (function_exists('pspell_new') && function_exists('pspell_check'))
			{
				if ($this->debug_init > 0) { echo 'email.spell.CONSTRUCTOR: loading real spell service "spell_svc_php"'.'<br />'."\r\n"; }
				// load the spell service class for php builtin pspell extension
				$this->spell_svc = CreateObject("email.spell_svc_php");
				// open connection to dictionary backend 
				$this->pspell_link = $this->spell_svc->pgw_pspell_new ("$this->user_lang", "", "", "", (PSPELL_FAST|PSPELL_RUN_TOGETHER));
			}
			else
			{
				if ($this->debug_init > 0) { echo 'email.spell.CONSTRUCTOR: loading DUMMY spell service'.'<br />'."\r\n"; }
				// load the DUMMY spell service class so php does not complain about undefined functions.
				$this->spell_svc = CreateObject("email.spell_svc_none");
				
			}
			
			// ask this service if it can reallyso spell checking, or is it a dummy class. 
			// This spell class is required to discover that and make it public
			//$svc_can_spell = $this->spell_svc->get_can_spell();
			$this->set_can_spell($this->spell_svc->get_can_spell());
			// ask this service how it wants its input, single words or lines of text
			// This spell class is required to discover that and make it public
			$this->set_sp_feed_type($this->spell_svc->get_sp_feed_type());
			if ($this->debug_init > 0) { echo 'email.spell.CONSTRUCTOR: $this->get_can_spell() returns: ['.serialize($this->get_can_spell()).']<br />'."\r\n"; }
			
			if ($this->debug_init > 0) { echo 'EXIT: email.spell.CONSTRUCTOR'.'<br />'."\r\n"; }
		}
		
		/**************************************************************************\
		*	OO ACCESS METHODS
		\**************************************************************************/
		/*!
		@function set_can_spell (PRIVATE)
		@abstract This class must discover if its spell service module is real or dummy, uses this to set that access to a working spell service backend.
		@author Angles
		@discussion This class must discover if its spell service module is real or dummy, and use this function to set that 
		information and make it available publicly via "get_can_spell". Should be discovered and set in the constructor.
		@access private
		*/
		function set_can_spell($can_spell)
		{
			if (is_bool($can_spell))
			{
				$this->can_spell = $can_spell;
			}
		}
		/*!
		@function get_can_spell
		@abstract check is this spelling class has access to a working spell service backend.
		@result Boolean
		@discussion If a real, working spell service is abailable, such as pspell support compiled into php, 
		this will return True, if a dummy placeholder service is loaded, such as when the pspell extension is NOT 
		compiled into php, this returns False. This class picks up this value in its constructor, when it loads a spell 
		service backend, that the service object will report if it can do anything or not. Dummy placeholder class is 
		loaded only so certain function names exists, so php will not complain anout undefined functions.
		@access public
		*/
		function get_can_spell()
		{
			return $this->can_spell;
		}
		
		/*!
		@function set_sp_feed_type (PRIVATE)
		@abstract This class must discover if its spell service module takes single words or lines (strings) of text.
		@author Angles
		@discussion This class must discover if its spell service module takes single words or lines (strings) of text, 
		and use this function to set that information and make it available publicly via "get_sp_feed_type".  
		Should be discovered and set in the constructor. Can be one of SP_FEED_UNKNOWN, 
		SP_FEED_WORDS, or SP_FEED_LINES, as defined in file class.spell_struct
		@access private
		*/
		function set_sp_feed_type($feed_type)
		{
			if (($feed_type == SP_FEED_UNKNOWN)
			|| ($feed_type == SP_FEED_WORDS)
			|| ($feed_type == SP_FEED_LINES))
			{
				$this->sp_feed_type = $feed_type;
			}
		}
		/*!
		@function get_sp_feed_type
		@abstract public access to how the spell service backend wants its input, as single words or lines of text.
		@result Boolean
		@discussion Supporting more than one backend service means this class must report how the backend 
		wants the input, single words or lines of text.  Can be one of SP_FEED_UNKNOWN, 
		SP_FEED_WORDS, or SP_FEED_LINES, as defined in file class.spell_struct.
		@access public
		*/
		function get_sp_feed_type()
		{
			return $this->sp_feed_type;
		}
		
		/*!
		@function set_preserve_var
		@abstract spell form needs to preserve, or carry forward, any send related vars that 
		were sent to class.bosend.
		@param $name (string) 
		@param $value (string) 
		@discussion Spell check is called from class.bosend, where the compose form gets submitted. 
		If the spellcheck button was pressed, then class.bosend creates an object of this class to handle the 
		spell checking. When the user submits the spellcheck form, the desired misspelled words will 
		be replaced and then this class invokes class.bocompose to take the user back to the compose form 
		with the corrected body text. From there the user may click send which submits the form to 
		class.bosend. Therefor any send related GPC vars that the compose form had when it was submitted 
		for spellchecking must be put back in the compose form after spellchecking so that class.bosend 
		gets the information it needs, the same information it would have gotten had a spell check not 
		occurred. Certain vars will be passed in the URL, these are sort, order, start, the msgball or 
		fldball, and fwd_proc are all passed in the URL that is the form "action". Other vars have their 
		own special requirements, these are body and subject vars, because they will be spellchecked. 
		All other vars that need to be preserved use this function to set their data. These var names 
		are enumerated in $this->preserve_tokens[] array, so that one code loop thru these tokens can 
		handle all the necessary vars to be preserved, such as in function "commit_preserve_vars_to_form". 
		Note that when class.bosend gets the submitted compose form and determines it needs to be directed 
		to this class for spellchecking, class.bosend MUST USE THE SET_* CODE in this class to set 
		any vars that class.bosend thinks should be saved. Class spell does not grab these vars, the calling 
		process must use the OOP style access methods to set them. Only then will this class be able to preserve 
		them, this class can only preserve what it is told to preserve.
		*/
		function set_preserve_var($name='',$value='')
		{
			if ((isset($name)) && ($name != '')
			&& (isset($value)) && ($value != ''))
			{
				$this->preserve_vars[$name] = $value;
			}
		}
		/*!
		@function commit_preserve_vars_to_form
		@abstract Any valid items in $this->preserve_vars will be turned into widget form hiddenvars for preservation.
		@discussion Uses a loop of vars we need to preserve (that are not preserved elsewhere) that tells the 
		widget class to make them hiddenvars in the form we will use in the spell check page.
		*/
		function commit_preserve_vars_to_form()
		{
			$loops = count($this->preserve_tokens);
			for ($i=0; $i < $loops; $i++)
			{
				$name = $this->preserve_tokens[$i];
				$value = 	$this->preserve_vars[$name];
				if (trim($value) != '')
				{
					$this->widgets->set_form_hiddenvar($name, $value);
				}
			}
		}
		function get_preserve_vars()
		{
			return $this->preserve_vars;
		}
		
		/*!
		@function set_subject
		@abstract Set this to the subject of the message to be spell checked.
		@param $str (string) 
		@author Angles
		@discussion This class needs all the information that is available about a message, which it will preserve 
		and pass back to the compose (or send) code when the spell check is done. This is for the subject of the message 
		to be spell checked. NOTE: in the future the subject *should* be spell checked too, currently the subject is *NOT* spellchecked,
		@access Public
		*/
		function set_subject($str='')
		{
			$this->subject = $str;
		}
		/*!
		@function get_subject
		@abstract Returns the value set with "set_subject"
		@author Angles
		@discussion ?
		@access Public
		*/
		function get_subject()
		{
			return $this->subject;
		}
		
		/*!
		@function set_body_orig
		@abstract Set this to the body of the message to be spell checked.
		@param $str (string) 
		@author Angles
		@discussion An this original, unmodified copy of the body is stored and available after the spell check if 
		you wish to compare to the spell fixed text. This explains why this is "body_orig" instead of just "body".  
		NOTE this is the text that gets spell checked, so it is REQUIRED to set this or else there is nothing 
		to spellcheck. NOTE: if it is necessary, STRIP SLASHES BEFORE SETTING THIS. This class has no way 
		of knowing if the body has the "magic" GPC slashes or not. It is recommended to pass the text thru function 
		"stripslashes_gpc" (in the mail_msg class) before setting this value.
		@access Public
		*/
		function set_body_orig($str='')
		{
			// STRIP SLASHES BEFORE YOU GIVE ME THE BODY
			$this->body_orig = $str;
			$this->prep_body_in();
		}
		/*!
		@function get_body_orig
		@abstract Returns the original, unmodified message text which was spell checked, without any corrections.
		@param $str (string) 
		@author Angles
		@discussion An this original, unmodified copy of the body is stored and available after the spell check if 
		you wish to compare to the spell fixed text, or for any reason, if you want it use this function.
		@access Public
		*/
		function get_body_orig()
		{
			return $this->body_orig;
		}
		
		/**************************************************************************\
		*	UTILITY CODE
		\**************************************************************************/
		/*!
		@function prep_body_in
		@abstract prepare bodt yexy for the spellchecker by normalizing CRLFs and decoding html specialchars. 
		@param none, this is an OOP class function, operates directly on $this->body_orig.
		@result none, this is an object call, works directly on the objects vars.
		@discussion YOU MUST STRIPSLASHES BEFORE calling ->set_body_orig(), which in turn calls this function, 
		because this function has no way of knowing if it should stripslashes on submitted data or not, but YOU do because 
		you are writting the code that uses this object, so YOU must strip slashes (if necessary) before calling ->set_body_orig(), 
		which in turn calls this function.
		@access Private
		*/
		function prep_body_in()
		{
			$tmp_text = $this->body_orig;
			// make all stray CR and LF  into CRLF combos which is standard text structure in the email world
			$tmp_text = $GLOBALS['phpgw']->msg->normalize_crlf($tmp_text);
			// we do not want to spell check html specialchars (&gt, $lt), so DECODE them now
			$tmp_text = $GLOBALS['phpgw']->msg->htmlspecialchars_decode($tmp_text);
			// now body is tripped and normalized as much as is possible before we do esoteric stuff in the spellcheck function
			$this->body_orig = $tmp_text;
			// note - although we do not want to spell check html specialchars, we still need to REENCODE them for display to the user
			// see prep_body_display()
		}
		
		/*!
		@function prep_body_display (somewhat DEPRECIATED)
		@abstract prepare text into something we can display in an HTML page. This particular function is supposed 
		to be private but is may be useful for other things too.
		@param $feed_text (string) this is the text that you want prepared for showing in an HTML page.
		@result string
		@author Angles
		@discussion In order to show text in an html browser this functions does a few things to it, such as convery CRLF chars into BR 
		tags, and to encode HTML offensive chars into their htlp specialchar equivalents. Returns the prepared string and 
		also directly fills class var $this->body_display with this prepared text, but that is a private class param.
		@access Private
		*/
		function prep_body_display($feed_text)
		{
			$body_display = $feed_text;
			// to make the body DISPLAYABLE to the user, do 2 things
			// 1) enc ode any offensive ASCII (&, ", ', <, >) into their html specialchars equivalents
			$body_display = $GLOBALS['phpgw']->msg->htmlspecialchars_encode($body_display);
			// 2) convert linebreaks to <br /> tags
			//$body_display = ereg_replace("\r\n","_CRLF_",$body_display);
			$body_display = ereg_replace("\r\n","<br />",$body_display);
			return $body_display;
		}
		
		/*!
		@function care_about_word
		@abstract spell checker is stupid, so we only give it text we believe to be words.
		@param $str (string) a string to test
		@result boolean
		@author Angles
		@discussion returns True if we should spell check the text, False if we should ignore this text. 
		Uses a variety of tests to determine what is probably not a "real word" so these words can be skipped 
		during the spell checking. This is because the spell checker will often report these type of words to be 
		misspelled, mostly because of strange punctuation of ASCII "art". Here is a list of the kind of strings 
		what will cause a return of False :
		empty strings, CR or LF chars, single letter words, numbers, email addresses, URLs, internet 
		host names,  anything surrounded by typical markup brackets &lt; &gt;, words with a dot in the 
		middle of it.
		Not every check is perfect, but this function will return it best assumption about the given string, 
		whether it should be considered a "real word" or not.
		@access Private
		*/
		function care_about_word($str='')
		{
			if (trim($str) == '')
			{
				$this->reject_reason = "don't spellcheck an empty string";
				return False;
			}
			
			if ((stristr($str, "\r"))
			|| (stristr($str, "\n")))
			{
				$this->reject_reason = "don't spellcheck CRLF chars";
				return False;
			}
			elseif (
				(stristr($str, "--"))
			|| (stristr($str, "_"))
			|| (stristr($str, "="))
			|| (stristr($str, ">"))
			|| (stristr($str, "<"))
			)
			{
				$this->reject_reason = "don't spellcheck something that's obviously not a conventional word";
				return False;
			}
			elseif (strlen($str) < 2)
			{
				$this->reject_reason = "don't spellcheck single letters or blank strings";
				return False;
			}
			elseif (preg_match('/['.chr(48).'-'.chr(57).']/', $str))
			{
				$this->reject_reason = "don't spellcheck something with numeric chars  [0 - 9]";
				return False;
			}
			//elseif ($this->my_validator->is_email($str) == True)
			elseif (preg_match('/.*@.*\..*/', $str))
			{
				$this->reject_reason = "don't spellcheck email addresses";
				return False;
			}
			//elseif ($this->my_validator->is_url($str) == True)
			elseif (preg_match('/[a-zA-Z0-9 \-\.]+\.([a-zA-Z]{2,4})/', $str))
			{
				$this->reject_reason = "don't spellcheck a URL or hostname";
				return False;
			}
			/*
			elseif ($this->my_validator->is_hostname($str) == True)
			{
				$this->reject_reason = "don't spellcheck internet hostnames";
				return False;
			}
			*/
			elseif (preg_match('/^<.*>$/', $str))
			{
				$this->reject_reason = "don't spellcheck bracked markup tags";
				return False;
			}
			//elseif (preg_match('/^.*\..*$/', $str))
			//elseif (preg_match('/\S*\.\S{2,3}$/i', $str))
			//elseif (preg_match('/.*\w\.\w{2,3}$/i', $str))
			//{
				// OOPS - not working correctly
				// \S	any character that is not a whitespace character
				// trying to match [ALPHA].[ALPHA][ALPHA][ALPHA]  because we do not like dots in words 
				// BUT a sentence where the user forgets the space after the period and before the next word, is an actual user error
				// so we are trying to match a interner hostname type dot, trying to be somewhat picky about the dot.
				
				//$this->reject_reason = "don't spellcheck words with a dot in the middle of it";
				//return False;
			//}
			else
			{
				$this->reject_reason = '"care_about_word" cleared word for spellcheck, word passed test, no rejecion';
				return True;
			}
		}
		
		
		/*!
		@function strip_punctuation
		@abstract this utility function will strip punctuation from text
		@param $str (string) ?
		@result string
		@discussion spell checker is stupid, this utility function will strip punctuation from text so that 
		the spell checker can look at the word itself, and not be confused by any punctuation.
		@access Private
		*/
		function strip_punctuation($str='')
		{
			$no_punct = $str;
			$no_punct = str_replace('.','',$no_punct);
			$no_punct = str_replace(',','',$no_punct);
			$no_punct = str_replace('?','',$no_punct);
			$no_punct = str_replace(':','',$no_punct);
			$no_punct = str_replace(';','',$no_punct);
			$no_punct = str_replace('!','',$no_punct);
			$no_punct = str_replace('"','',$no_punct);
			$no_punct = str_replace('(','',$no_punct);
			$no_punct = str_replace(')','',$no_punct);
			$no_punct = str_replace('*','',$no_punct);
			$no_punct = str_replace('[','',$no_punct);
			$no_punct = str_replace(']','',$no_punct);
			return $no_punct;
		}
		
		/*!
		@function html_mark_bad_word
		@abstract DEPRECIATED
		*/
		function html_mark_bad_word($line_of_text, $bad_word_data)
		{
			return '';
		}
		
		/*!
		@function html_mark_entire_line
		@abstract DEPRECIATED
		@discussion This Depreciated function was used during developement of this class, before comboboxes were 
		used, it uses HTML tags to make the misspelled word bold and red, then puts up to $this->max_suggest 
		suggestions after the word in brackets, with each suggestion seperated by the pipe character. Although this 
		is no loner used at this moment, it may be of some use in the future. In theory, the user would get back the 
		text as described, then have to hand edit the corrections. Not very user friendly so it is DEPRECIATED.
		@example
		## User gets spell corrections like this:
		Could you <b><font color="red">pleeze</font></b> <i>[ please | sneeze | freeze ]</i> send it to me.
		## note: this example is fictional, not actual spell suggestions.
		@access Private
		*/
		function html_mark_entire_line($line_of_text, $line_num)
		{
			$line_array = explode(' ',$line_of_text);
			
			$bad_word_loops = count($this->bad_words);
			for ($x=0; $x < $bad_word_loops; $x++)
			{
				$this_bad_word_element = $this->bad_words[$x];
				if ($this_bad_word_element->line_num == $line_num)
				{
					if ($this->debug_spellcheck > 0) { echo 'bad word ['.$this_bad_word_element->orig_word.'] exists on this line num ['.$this_bad_word_element->line_num.'], it is word num ['.$this_bad_word_element->word_num.']<br />'; }
					// make this particular bad word bolded
					$word_in_question = $line_array[$this_bad_word_element->word_num];
					// is there any punctuation we need to consider seperate from this word?
					$last_char_idx = strlen($word_in_question) - 1;
					$last_char = $word_in_question[$last_char_idx];
					if (
						($last_char == '.')
					||	($last_char == ',')
					||	($last_char == '?')
					||	($last_char == ':')
					||	($last_char == ';')
					||	($last_char == '!')
					||	($last_char == '"')
					||	($last_char == '(')
					||	($last_char == ')')
					||	($last_char == '*')
					||	($last_char == '[')
					||	($last_char == ']')
					)
					{
						// remove last char from word_in_question and store it in $punctuation
						$punctuation = $last_char;
						$word_in_question = substr($word_in_question, 0, -1);
					}
					else
					{
						$punctuation = '';
					}
					
					// add the correction suggestions with html visual markup, add back punctuation if any
					$word_in_question = '<strong><font color="red">'.$word_in_question.'</font></strong>'.$punctuation;
					$suggestion_string = '';
					$loops = count($this_bad_word_element->suggestions);
					if ($loops > 0)
					{
						for ($i=0; $i < $loops; $i++)
						{
							if ($i > 0)
							{
								$pipe_sep = '&#124;';
							}
							else
							{
								$pipe_sep = '';
							}
							$suggestion_string .= $pipe_sep.$this_bad_word_element->suggestions[$i];
						}
						// surround them with html niceties
						$bad_word_with_suggestions =	
							$word_in_question.' <font color="green"><em>&#091;'.$suggestion_string.'&#093;</em></font>';
					}
					else
					{
						// no suggestions, all we can do is flag the word but offer no alternatives
						$bad_word_with_suggestions = $word_in_question;
					}
					// replace original word with this marked up text
					$line_array[$this_bad_word_element->word_num] = $bad_word_with_suggestions;
				}
			}
			$remade_line = implode(' ', $line_array);
			return $remade_line;
		}
		
		
		/*!
		@function add_option_boxes
		@abstract takes one line of text and inserts suggestion comboboxes after each misspelled word.
		@param $line_of_text (string) a single line of text from the body of the message, whicj message is exploded by linebreaks.
		@param $line_num (int) the line number in relation to the entire body of text when exploded by linebreaks.
		@discussion Uses class.html_widgets to simplify the HTML of the select boxes. Must operate 
		on an entire line of text because the suggesion data targets the mispelled word by storing its 
		line number and word number within that line, where the line is exploded by space. Thus, 
		in adding the comboboxes there will be additional spaces and words added to the line, but this 
		is not a problem is an entire line of text is processed in one pass. The function simply stores an 
		unmodified version of the line and uses that to target words in any location in the line of text. 
		Additonally, this function directly access the class var $this->bad_words which contains an 
		object of type "correction_info" (as defined in file class.spell_struct) to obtain the suggestion data.
		@access Private
		*/
		function add_option_boxes($line_of_text, $line_num)
		{
			$line_array = explode(' ',$line_of_text);
			
			$bad_word_loops = count($this->bad_words);
			for ($x=0; $x < $bad_word_loops; $x++)
			{
				$this_bad_word_element = $this->bad_words[$x];
				if ($this_bad_word_element->line_num == $line_num)
				{
					if ($this->debug_spellcheck > 0) { echo 'bad word ['.$this_bad_word_element->orig_word.'] exists on this line num ['.$this_bad_word_element->line_num.'], it is word num ['.$this_bad_word_element->word_num.']<br />'; }
					// make this particular bad word bolded
					// update: we don;t care about this unclean version, we want the word that the spellchecker got, stripped of punctuation
					// however the punctuation around it must be preserved for the email message
					$word_in_question_unclean = $line_array[$this_bad_word_element->word_num];
					$word_in_question_clean = $this_bad_word_element->orig_word_clean;
					// clean vs. unclean: is the cleaned word different, do we need to get rid of 
					// any punctuation we need to consider seperate from this word?
					
					// add the correction suggestions with html visual markup, add back punctuation if any
					$word_in_question_clean = '<strong><font color="red">'.$word_in_question_clean.'</font></strong>';
					$suggestion_string = '';
					$loops = count($this_bad_word_element->suggestions);
					if ($loops == 0)
					{
						// no suggestions, all we can do is flag the word but offer no alternatives
						$bad_word_with_suggestions = $word_in_question_clean;
					}
					else
					{
						// make option combobox widget
						$this->widgets->new_combobox();
						$this->widgets->set_cbox_name('line'.$line_num.'_word'.$this_bad_word_element->word_num.'_'.$this_bad_word_element->orig_word_clean);
						//$this->widgets->set_cbox_item('K_E_E_P', lang('no change'));
						//$this->widgets->set_cbox_item('K_E_E_P', '');
						$this->widgets->set_cbox_item_spellcheck($this_bad_word_element, '');
						for ($i=0; $i < $loops; $i++)
						{
							//$this->widgets->set_cbox_item($this_bad_word_element->suggestions[$i], $this_bad_word_element->suggestions[$i]);
							$this->widgets->set_cbox_item_spellcheck($this_bad_word_element, $i);
						}
						// add cobmobox of suggestions right next the misspelled word
						$correction_box = $this->widgets->get_combobox();
						//echo '<pre>'.htmlspecialchars($correction_box).'<.pre>';
						$bad_word_with_suggestions = $word_in_question_clean.' '.$correction_box;
					}
					
					// replace original word with this marked up text
					$processed_word = str_replace($this_bad_word_element->orig_word_clean, $bad_word_with_suggestions, $word_in_question_unclean);
					$line_array[$this_bad_word_element->word_num] = $processed_word;
				}
			}
			$remade_line = implode(' ', $line_array);
			return $remade_line;
		}
		
		/*!
		@function get_form_action_url
		@abstract generate the URL which is the target, or action, of the spell review form
		@result string
		@discussion This function uses the code in class.bocompose to generate the return value. 
		It does this by creating a class.bocompose object and calling its function get_compose_form_action_url().
		The only difference is we pass a $menuaction_target var unique to this forms needs, 
		whereas the compose form uses a different $menuaction_target specific to the compose form. 
		*/
		function get_form_action_url()
		{
			$bocompose_obj = CreateObject("email.bocompose");
			//$menuaction_target = 'email.bocompose.spell_finished';
			$menuaction_target = 'email.spell.spell_finished';
			$my_form_action_url = $bocompose_obj->get_compose_form_action_url($menuaction_target);
			return $my_form_action_url;
		}
		
		/*!
		@function make_correction_struct_from_array
		@abstract recover badwords "correction_info" structure data from the GPC vars of the spell_review form.
		@param $submitted_correction_data (array) numbered array where each element is an associative array of badwords data.
		@author Angles
		@discussion the spell review form submits correction data in the form of URI string which 
		is made into an array with parse_str. This data originated as "correction_info" structure 
		data generated during spell_review, designed to be very similar to the "correction_info" structure, 
		so it is possible to match the submitted badword data array to the "correction_info" structure it was 
		modeled after.
		*/
		function make_correction_struct_from_array($submitted_correction_data='')
		{
			$this->bad_words = array();
			if ((!is_array($submitted_correction_data))
			|| (count($submitted_correction_data) == 0))
			{
				// invalid data OR no correction data was submitted
				// no harm in returning the empty array
				return $this->bad_words;
			}
			
			// ... continue ...
			$loops = count($submitted_correction_data);
			for ($i=0; $i < $loops; $i++)
			{
				$this_correction = $submitted_correction_data[$i];
				$idx = count($this->bad_words);
				$this->bad_words[$idx] = new correction_info;
				$this->bad_words[$idx]->orig_word = $this_correction['orig_word'];
				$this->bad_words[$idx]->orig_word_clean = $this_correction['orig_word_clean'];
				$this->bad_words[$idx]->line_num = $this_correction['line_num'];
				$this->bad_words[$idx]->word_num = $this_correction['word_num'];
				$this->bad_words[$idx]->suggestions = array();
				$this->bad_words[$idx]->suggestions[0] = $this_correction['suggestion_value'];
			}
			return $this->bad_words;
		}
		
		/*!
		@function replace_badwords_this_line
		@abstract takes one line of text and replace each misspelled word the user has specified a replacement for
		@param $line_of_text (string) a single line of text from the body of the message, whicj message is exploded by linebreaks.
		@param $line_num (int) the line number in relation to the entire body of text when exploded by linebreaks.
		@discussion Must operate on an entire line of text because the suggesion data targets the mispelled word 
		by line number and word number within that line, where the line is exploded by space. Thus, 
		if a corrected word has a space there will be additional spaces and words added to the line, but this 
		is not a problem is an entire line of text is processed in one pass. The function simply stores an 
		unmodified version of the line and uses that to target words in any location in the line of text. 
		Additonally, this function directly access the class var $this->bad_words which contains an 
		object of type "correction_info" (as defined in file class.spell_struct) to obtain the suggestion data. 
		That data was recovered from the user submitted form, made into an array, then made into the 
		"correction_info" data. 
		IMPORTANT: $this->bad_words->suggestions[0] IS THE USERS CHOICE of replacement word. 
		During the initial spell checking it holds the suggestions from the spell checker backend, BUT after user 
		submits the form with corrections, ->suggestions[0] is the bombobox value which was the users choice 
		of a replacement word for that particular misspelled word, unless ->suggestions[0] is the special 
		token "K_E_E_P" in which case the user desires no change for this word.
		@access Private
		*/
		function replace_badwords_this_line($line_of_text, $line_num)
		{
			$line_array = explode(' ',$line_of_text);
			
			$bad_word_loops = count($this->bad_words);
			for ($x=0; $x < $bad_word_loops; $x++)
			{
				$this_bad_word_element = $this->bad_words[$x];
				if ($this_bad_word_element->line_num == $line_num)
				{
					//if ($this->debug_spell_finished > 1) { echo 'email.spell.replace_badwords_this_line: bad word ['.$this_bad_word_element->orig_word_clean.'] exists on this line num ['.$this_bad_word_element->line_num.'], it is word num ['.$this_bad_word_element->word_num.']<br />'; }
					//if ($this->debug_spell_finished > 2) { echo 'email.spell.replace_badwords_this_line: bad word: ['.$this_bad_word_element->orig_word_clean.'] :: user wants this replacement: ['.$this_bad_word_element->suggestions[0].'], if it is "K_E_E_P", do not replace<br />'; }
					if ($this_bad_word_element->suggestions[0] == "K_E_E_P")
					{
						if ($this->debug_spell_finished > 1) { echo 'email.spell.replace_badwords_this_line: bad word: ['.$this_bad_word_element->orig_word_clean.'] :: user wants NO CHANGE, found "K_E_E_P" as suggextion[0], skipping to next bad_word loop via "continue"'; }
						continue;
					}
					
					// ... replace the word ...
					if ($this->debug_spell_finished > 1) { echo 'email.spell.replace_badwords_this_line: bad word: ['.$this_bad_word_element->orig_word_clean.'] :: user wants it replaced with ['.$this_bad_word_element->suggestions[0].']<br />'; }
					$word_in_quesion_raw = $line_array[$this_bad_word_element->word_num];
					$processed_word = str_replace($this_bad_word_element->orig_word_clean, $this_bad_word_element->suggestions[0], $word_in_quesion_raw);
					$line_array[$this_bad_word_element->word_num] = $processed_word;
				}
			}
			$remade_line = implode(' ', $line_array);
			return $remade_line;
		}
		/**************************************************************************\
		*	CODE
		\**************************************************************************/
		// DEPRECIATED
		function basic_spcheck()
		{
			if ($this->debug_spellcheck > 0) { echo 'ENTERING: email.spell.basic_spcheck'.'<br />'; }
			
			// DEPRECIATED
			
			if ($this->debug_spellcheck > 0) { echo 'LEAVING: email.spell.basic_spcheck'.'<br />'; }
		}
		
		/*!
		@function spell_review
		@abstract The guts of the spellcheck logic is here, where the body is examined and corrections suggested.
		@author Angles
		@discussion ?
		*/
		function spell_review()
		{
			
			if ($this->debug_spellcheck > 0) { echo 'ENTERING: email.spell.spell_review'.'<br />'; }
			if ($this->debug_spellcheck > 0) { echo 'spell_review lang(charset): ['.lang('charset').']<br />'; }
			if ($this->debug_spellcheck > 0) { echo 'spell_review $this->user_lang: ['.$this->user_lang.']<br />'; }
			
			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			$GLOBALS['phpgw_info']['flags']['noappheader'] = True;
			$GLOBALS['phpgw_info']['flags']['noappfooter'] = True;
			$GLOBALS['phpgw']->common->phpgw_header(true);
			
			$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);
			$GLOBALS['phpgw']->template->set_file(array(
				'T_spell_main' => 'spell_review.tpl'
			));
			$GLOBALS['phpgw']->template->set_var('page_desc', 'Email Spell Check Review Page');
			
			// setup the form
			$this->widgets->new_form();
			$this->widgets->set_form_name('spell_review');
			$this->widgets->set_form_method('POST');
			$form_action = $this->get_form_action_url();
			$this->widgets->set_form_action($form_action);
			// include copy of the original body as a hiddenvar, 
			// prep it with (a) base64 encoded and (b) urlencoded, so no html offensive chars are present
			$body_orig_b64_urlencode = base64_encode($this->body_orig);
			// do NOT urlencode here  - WIDGET URLENCODES THIS FOR US! 
			$this->widgets->set_form_hiddenvar('body_orig_b64_urlencode', $body_orig_b64_urlencode);
			// preserve the subject (FIXME: we need to spell check the subject too)
			$this->widgets->set_form_hiddenvar('subject', $this->subject);
			// and any vars we are supposed to preserve as hidden vars
			$this->commit_preserve_vars_to_form();
			$this->form_open_tags = $this->widgets->get_form();
			$GLOBALS['phpgw']->template->set_var('form_open_tags', $this->form_open_tags);
			
			// now prepare to check words.
			$this->body_array = array();
			$this->body_array = $GLOBALS['phpgw']->msg->explode_linebreaks($this->body_orig);
			
			if ($this->debug_spellcheck > 0) { echo 'spell_review: $this->body_array DUMP <pre>'; print_r($this->body_array); echo '</pre>'; }
			
			
			$loops = count($this->body_array);
			for ($i=0; $i < $loops; $i++)
			{
				$this_line_str = $this->body_array[$i];
				if ($this->debug_spellcheck > 0) { echo '<br />spell_review: this_line: ['.$this_line_str.']<br />'; }
				
				if (strlen($this_line_str) == 0)
				{
					// is this a blank line
					if ($this->debug_spellcheck > 0) { echo 'spell_review: skipping this line, it\'s blank'.'<br />'; }
				}
				elseif ((stristr($this_line_str, ">"))
				&& ($this_line_str[0] == '>'))
				{
					// does the line begin with a repoly quoting char
					if ($this->debug_spellcheck > 0) { echo 'spell_review: skipping this line, it starts with a reply quote &gt; char'.'<br />'; }
				}
				else
				{
					// Line OK to check
					if ($this->debug_spellcheck > 0) { echo 'spell_review: we will examine this line... '.'<br />'; }
					$word_for_word = array();
					$word_for_word = explode(' ', $this_line_str);
					//echo 'spell_review: $word_for_word DUMP <pre>'; print_r($word_for_word); echo '</pre>';
					$words = count($word_for_word);
					for ($x=0; $x < $words; $x++)
					{
						$this_word = $word_for_word[$x];
						if ($this->debug_spellcheck > 0) { echo '&nbsp; spell_review: $this_word: ['.$this_word.'] ; htmlspecialchars($this_word) ['.htmlspecialchars($this_word).']<br />'; }
						if ($this->care_about_word($this_word) == False)
						{
							if ($this->debug_spellcheck > 0) { echo '&nbsp; spell_review: 1st test $this->care_about_word advises to IGNORE this word, skipping to next word via "continue"'.'<br /> ... reject_reason: '.$this->reject_reason.'<br /> . . . <br />'; }
							continue;
						}
						//strip punctuation
						$this_word_clean = $this->strip_punctuation($this_word);
						
						if ($this->debug_spellcheck > 0) { echo '&nbsp; spell_review: $this->strip_punctuation('.$this_word.') returns ['.$this_word_clean.']<br />'; }
						if ($this->care_about_word($this_word_clean) == False)
						{
							if ($this->debug_spellcheck > 0) { echo '&nbsp; spell_review: 2nd test $this->care_about_word advises to IGNORE this word, skipping to next word via "continue"'.'<br /> ... reject_reason: '.$this->reject_reason.'<br /> . . . <br />'; }
							continue;
						}
						
						if ($this->debug_spellcheck > 0) { echo '&nbsp; spell_review: Spell Check OK to proceed on word: ['.$this_word_clean.']<br />'; }
						// DO THE SPELL CHECK
						
						if ($this->spell_svc->pgw_pspell_check ($this->pspell_link, $this_word_clean))
						{
							if ($this->debug_spellcheck > 0) { echo '&nbsp; &nbsp; spell_review: spelling OK for ['.$this_word_clean.']<br />'; }
						}
						else
						{
							$idx = count($this->bad_words);
							$this->bad_words[$idx] = new correction_info;
							$this->bad_words[$idx]->orig_word = $this_word;
							$this->bad_words[$idx]->orig_word_clean = $this_word_clean;
							$this->bad_words[$idx]->line_num = $i;
							$this->bad_words[$idx]->word_num = $x;
							$this->bad_words[$idx]->suggestions = array();
							
							
							$cur_suggest=0;
							$suggestions = $this->spell_svc->pgw_pspell_suggest ($this->pspell_link, $this_word_clean);
							if ( count($suggestions) == 0)
							{
								if ($this->debug_spellcheck > 0) { echo '&nbsp; &nbsp; spell_review: ['.$this_word_clean.'] has spelling problems, got '.count($suggestions).' suggestions, sorry'.'<br />'; }
							}
							else
							{
								if ($this->debug_spellcheck > 0) { echo '&nbsp; &nbsp; spell_review: ['.$this_word_clean.'] has spelling problems, got '.count($suggestions).' suggestions, first '.$this->max_suggest.' are:<pre>'; }
								foreach ($suggestions as $suggestion)
								{
									if ($cur_suggest > $this->max_suggest)
									{
										break;
									}
									//echo ' * for ['.$this_word.'] Possible spelling: ['.$suggestion.']'.'<br />'."\r\n";
									if ($this->debug_spellcheck > 0) { echo ' * '.$suggestion.'<br />'."\r\n"; }
									$this->bad_words[$idx]->suggestions[$cur_suggest] = $suggestion;
									$cur_suggest++;
								}
								//echo 'done suggesting'.'<br /><br />'."\r\n";
								if ($this->debug_spellcheck > 0) { echo '</pre>'."\r\n"; }
							}
						}
						// debug out a spacer before we go to the next word
						if ($this->debug_spellcheck > 0) { echo ' . . . <br />'."\r\n"; }
					}
				}
				
			}
			if ($this->debug_spellcheck > 0) { echo 'spell_review: done with body array spell check loop'.'<br />'; }
			if ($this->debug_spellcheck > 0) { echo 'spell_review: $this->bad_words DUMP<pre>'; print_r($this->bad_words); echo '</pre>'; }
			
			
			// put body_array back together inserting bad_words data
			$loops = count($this->body_array);
			for ($i=0; $i < $loops; $i++)
			{
				$this_line_str = $this->body_array[$i];
				// first, we can encode the html specialchars that exist in the orig line
				// this *should* not add any spaces, so word number data should still be valid
				$this_line_str = $GLOBALS['phpgw']->msg->htmlspecialchars_encode($this_line_str);
				if ($this->debug_spellcheck > 0) { echo 'spell_review: RECONSTRUCT BODY this_line: ['.$this_line_str.']<br />'; }
				
				// any and all bad words in this line of text are flagged now
				//$this_line_str = $this->html_mark_entire_line($this_line_str, $i);
				// add an optionbox of suggestions for any and all words on this line that are misspelled and have suggestions
				$this_line_str = $this->add_option_boxes($this_line_str, $i);
				$this->body_with_suggest .= $this_line_str."\r\n";
			}
			
			$this->body_with_suggest = ereg_replace("\r\n","<br />",$this->body_with_suggest);
			$GLOBALS['phpgw']->template->set_var('body_with_suggestions', $this->body_with_suggest);
			
			// BUTTONS
			// make_button ( $type , $name , $value )
			$btn_apply = $this->widgets->make_button('submit','btn_apply', lang('apply'));
			$btn_cancel = $this->widgets->make_button('submit','btn_cancel', lang('cancel'));
			$GLOBALS['phpgw']->template->set_var('btn_apply', $btn_apply);
			$GLOBALS['phpgw']->template->set_var('btn_cancel', $btn_cancel);
			
			$GLOBALS['phpgw']->template->pfp('out','T_spell_main');
			if ($this->debug_spellcheck > 0) { echo 'EXITT: email.spell.spell_review'.'<br />'; }
		
		}


		/*!
		@function spell_finished
		@abstract User reviewed spell check data is submitted to this function, which replaces words and calls compose.
		@author Angles
		@discussion Spell check form is submitted to this funcion, which replaces any misspelled words the user wants 
		replaced, then invokes class.bocompose and takes the user back to the compose page with the corrected 
		message text. Note this function must pass any GPC vars preserved when the compose form eas submitted 
		for spellcheck, and put those bars back in the compose form where they are needed for when the compose 
		form is submitted to class.bosend.
		*/
		function spell_finished()
		{
			if ($this->debug_spell_finished > 0) { echo 'ENTERING: email.spell.spell_finished'.'<br />'; }
			// this function is in class.msgl_bootstrap.inc.php, we created in the constructor for this class.
			$this->msg_bootstrap->ensure_mail_msg_exists('email.spell.spell_finished', $this->debug_spell_finished);
			
			if ($this->debug_spell_finished > 2) { 	echo 'email.spell.spell_finished: data dump: $GLOBALS[HTTP_POST_VARS]<pre>'; print_r($GLOBALS['phpgw']->msg->ref_POST); echo '</pre>'."\r\n";
									echo 'email.spell.spell_finished: data dump: $GLOBALS[HTTP_GET_VARS]<pre>'; print_r($GLOBALS['phpgw']->msg->ref_GET); echo '</pre>'."\r\n"; }
			
			// recover the body_orig
			$this->body_orig = urldecode($GLOBALS['phpgw']->msg->ref_POST['body_orig_b64_urlencode']);
			$this->body_orig = base64_decode($this->body_orig);
			if ($this->debug_spell_finished > 1) { echo 'email.spell.spell_finished: recovered $this->body_orig DUMP:<pre>'; print_r($this->body_orig); echo '</pre>'; }
			
			// CANCEL OR APPLY - which button was pressed
			if ((isset($GLOBALS['phpgw']->msg->ref_POST['btn_apply']))
			&& ($GLOBALS['phpgw']->msg->ref_POST['btn_apply'] != ''))
			{
				if ($this->debug_spell_finished > 1) { echo 'email.spell.spell_finished: btn_apply was pressed<br />'; }
				// recover the spellcheck data which was encooded as a URI get string in the spelling comboboxes
				$all_badwords = array();
				// ----  extract any "fake_uri" embedded data from HTTP_POST_VARS  ----
				// note: this happens automatically for HTTP_GET_VARS 
				if (is_array($GLOBALS['phpgw']->msg->ref_POST))
				{
					if ($this->debug_spell_finished > 1) { echo '$GLOBALS[HTTP_POST_VARS] is array<br />'; }
					@reset($GLOBALS['phpgw']->msg->ref_POST);
					while(list($key,$value) = each($GLOBALS['phpgw']->msg->ref_POST))
					{
						// looking for any key like this: "lineX_wordX_alphachars"
						if (stristr($key, '_word'))
						{
							$uri_type_string = $GLOBALS['phpgw']->msg->ref_POST[$key];
							$embeded_data = array();
							// parse_str also urldecodes the items automatically.
							parse_str($uri_type_string, $embeded_data);
							// stripslashes_gpc if needed
							$embeded_data['orig_word'] = $GLOBALS['phpgw']->msg->stripslashes_gpc($embeded_data['orig_word']);
							$embeded_data['orig_word_clean'] = $GLOBALS['phpgw']->msg->stripslashes_gpc($embeded_data['orig_word_clean']);
							$embeded_data['suggestion_value'] = $GLOBALS['phpgw']->msg->stripslashes_gpc($embeded_data['suggestion_value']);
							// add the key just for the record, we may not need it 
							//it needs urldecoded since it did not pass thru parse_str
							$embeded_data['post_key'] =  $GLOBALS['phpgw']->msg->stripslashes_gpc(urldecode($key));
							$idx = count($all_badwords);
							$all_badwords[$idx] = $embeded_data;
						}
					}
				}
				if ($this->debug_spell_finished > 1) { echo 'email.spell.spell_finished: recovered $all_badwords DUMP:<pre>'; print_r($all_badwords); echo '</pre>'; }
				$this->make_correction_struct_from_array($all_badwords);
				if ($this->debug_spell_finished > 1) { echo 'email.spell.spell_finished: $this->make_correction_struct_from_array($all_badwords) Fills $this->bad_words[] DUMP:<pre>'; print_r($this->bad_words); echo '</pre>'; }
			
				// REPLACE WORDS
				if ($this->debug_spell_finished > 1) { echo 'email.spell.spell_finished: btn_apply was pressed, fix misspelled words if necessary <br />'; }
				
				$this->body_array = array();
				$this->body_array = $GLOBALS['phpgw']->msg->explode_linebreaks($this->body_orig);
				
				if ($this->debug_spell_finished > 2) { echo 'email.spell.spell_finished: $this->body_array DUMP <pre>'; print_r($this->body_array); echo '</pre>'; }
				
				// put the corrected line in $this->body_with_suggest 
				$this->body_with_suggest = '';
				$loops = count($this->body_array);
				for ($i=0; $i < $loops; $i++)
				{
					$this_line_str = $this->body_array[$i];
					if ($this->debug_spell_finished > 0) { echo '<br />email.spell.spell_finished: this_line: ['.$this_line_str.']<br />'; }
					// any and all replacable bad words in this line of text are replaced now, the entire line os processed at the same time
					$this_line_str = $this->replace_badwords_this_line($this_line_str, $i);
					$this->body_with_suggest .= $this_line_str."\r\n";
				}
				
			}
			else
			{
				if ($this->debug_spell_finished > 1) { echo 'email.spell.spell_finished: btn_cancel was pressed (or at lease, btn_apply was not pressed ...)<br />'; }
				// CANCEL was pressed, user wants no word substitutions
				// simply fill $this->body_with_suggest with $this->body_orig, then the rest of the code is the same
				$this->body_with_suggest = $this->body_orig;
			}
			
			//$this->body_with_suggest = ereg_replace("\r\n","<br />",$this->body_with_suggest);
			if ($this->debug_spell_finished > 1) { echo 'email.spell.spell_finished: FINAL PROCESSED BODY: $this->body_with_suggest:<pre>'.$this->body_with_suggest.'</pre>'; }
			
			// TELL BOCOMPOSE TO DO ITS THING WITH THIS SPELL FIXED BODY
			$GLOBALS['phpgw']->msg->set_arg_value('body', $this->body_with_suggest);
			$GLOBALS['phpgw']->uicompose = CreateObject("email.uicompose");
			$GLOBALS['phpgw']->uicompose->compose('mail_spell_special_handling');
			
			
			
			if ($this->debug_spell_finished > 0) { echo 'EXIT: email.spell.spell_finished'.'<br />'; }
		}


	}
?>
