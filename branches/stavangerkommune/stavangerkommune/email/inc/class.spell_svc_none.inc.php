<?php
	/**
	* EMail - SpellChecking Backend Service Class - Dummy Class
	*
	* If PHP psspell support is not compiled in,  this  dummy module 
	* spell_svc_none is loaded so there are no errors related to undefined 
	* pspell functions.
	* @author Angelo (Angles) Puglisi <angles@aminvestments.com>
	* @copyright Copyright (C) 2002 Angelo Tony Puglisi (Angles)
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package email
	* @version $Id$
	* @internal Based on AngleMail http://www.anglemail.org/
	*/

	
	/**
	* Dummy placeholder for spell-less installations
	*
	* @package email
	*/	
	class spell_svc_none
	{
		/**
		 * Flag if this is a working module or a dummy one.
		 * 
		 * @var boolean $can_spell F
		 * @access private
		 */
		var $can_spell = False;
		/**
		 * If tis services takes single words or strings, values are defined 
		 * in spell class, which gets the value from here and makes it public.
		 * @var integer $sp_feed_type
		 * @access private
		 */
		var $sp_feed_type;
		
		/**
		* Constructor
		*/
		function spell_svc_none()
		{
			// this is a dummy module for installations with no spell capability
			$this->can_spell = False;
			// SP_FEED_UNKNOWN is defined in the spell class.
			$this->sp_feed_type = SP_FEED_UNKNOWN;
			return;
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
		maked that information public. This is a dummy placeholder module so it returns False.
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
		@abstract  dummy placeholder for spell-less installations
		@param string language, string [spelling], string [jargon], string [encoding], int [mode]
		@discussion returns False so ignorant calling code will sense something is wrong with 
		spelling code.
		@access public
		*/
		function pgw_pspell_new($language, $spelling, $jargon, $encoding, $mode)
		{
			return False;
		}
		
		/*!
		@function pgw_pspell_check
		@abstract  dummy placeholder for spell-less installations
		@param int dictionary_link, string word
		@discussion Returns True to imitate a word is spelled correctly, then ignorant 
		calling code will not ask for suggestions, hopefully.
		@access public
		*/
		function pgw_pspell_check($dictionary_link, $word)
		{
			return True;
		}
		
		/*!
		@function pgw_pspell_suggest
		@abstract  dummy placeholder for spell-less installations
		@param int dictionary_link, string word
		@discussion Returns empty array to imitate pspell hafving no suggestions, 
		since this is a dummy module there are indeed no suggestions, and ignorant calling 
		code will not act on any suggestions if it gets an empty array back.
		@access public
		*/
		function pgw_pspell_suggest($dictionary_link, $word)
		{
			return array();
		}
	}
?>
