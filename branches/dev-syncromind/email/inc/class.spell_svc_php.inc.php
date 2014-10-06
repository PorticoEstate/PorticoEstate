<?php
	/**
	* EMail - SpellChecking Backend Service Class - for PHP pspell Extension
	*
	* This is loaded if PHP has psspell support compiled in. If it is not compiled 
	* in, a dummy module spell_ svc_none is loaded so there are no errors related 
	* to undefined pspell functions.
	* @author Angelo (Angles) Puglisi <angles@aminvestments.com>
	* @copyright Copyright (C) 2002 Angelo Tony Puglisi (Angles)
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package email
	* @version $Id$
	* @internal Based on AngleMail http://www.anglemail.org/
	*/

	
	/**
	* Wraps calls to the spell checking backend psspell buildin tp PHP
	*
	* @package email
	*/	
	class spell_svc_php
	{
		/**
		 * Flag if this is a working module or a dummy one
		 * @var boolean
		 * @access private
		 */
		var $can_spell = True;
		/**
		 * If tis services takes single words or strings, values are defined 
		 * in spell class, which gets the value from here and makes it public.
		 * @var integer
		 * @access private
		 */
		var $sp_feed_type;
		
		/**
		* Constructor
		*/
		function spell_svc_php()
		{
			$this->can_spell = True;
			// SP_FEED_WORDS is defined in the spell class.
			$this->sp_feed_type = SP_FEED_WORDS;
		}
		
		/**************************************************************************\
		*	OO ACCESS METHODS
		\**************************************************************************/
		/*!
		@function get_can_spell
		@abstract Read Only, report if this spell service is capable of spell check or not. 
		@author Angles
		@discussion The calling spell class will ask if this spell service is capable of spell check or not. 
		This function is exposed to the calling spell class for this purpose. The calling spell class then 
		maked that information public.
		@access private
		*/
		function get_can_spell()
		{
			return $this->can_spell;
		}
		
		/*!
		@function get_sp_feed_type
		@abstract Read Only, report if this spell service takes single words or strings. 
		@author Angles
		@discussion The calling spell class will ask if this spell service takes single words or strings. 
		This function is exposed to the calling spell class for this purpose. The calling spell class then 
		maked that information public.
		@access private
		*/
		function get_sp_feed_type()
		{
			return $this->sp_feed_type;
		}
		
		/**************************************************************************\
		*	CODE
		\**************************************************************************/
		/*!
		@function pgw_pspell_new
		@abstract wraps calls to "pspell_new"
		@param string language, string [spelling], string [jargon], string [encoding], int [mode]
		@discussion Php manual shows params to be: 
		pspell_new  (string language, string [spelling], string [jargon], string [encoding], int [mode])
		@access public
		*/
		function pgw_pspell_new($language, $spelling, $jargon, $encoding, $mode)
		{
			// open connection to dictionary backend
			// see: http://rock.earthlink.net/manual/mod/mod_php4/function.pspell-new.html
			return pspell_new($language, $spelling, $jargon, $encoding, $mode);
		}
		
		
		/*!
		@function pgw_pspell_check
		@abstract wraps calls to "pspell_check"
		@param int dictionary_link, string word
		@discussion Php manual shows params to be: 
		pspell_check  (int dictionary_link, string word)
		@access public
		*/
		function pgw_pspell_check($dictionary_link, $word)
		{
			return pspell_check($dictionary_link, $word);
		}

		/*!
		@function pgw_pspell_suggest
		@abstract wraps calls to "pspell_suggest"
		@param int dictionary_link, string word
		@discussion Php manual shows params to be:  
		pspell_suggest (int dictionary_link, string word)
		@access public
		*/
		function pgw_pspell_suggest($dictionary_link, $word)
		{
			// http://rock.earthlink.net/manual/mod/mod_php4/function.pspell-suggest.html
			return pspell_suggest($dictionary_link, $word);
		}
	}
?>
