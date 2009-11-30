<?php
	/**
	* EMail - SpellChecking Header Include file
	*
	* Structures shared between Spell Checking and HTML widgets
	* A simple C-Style Include .h file, holds public data structure classes for 
	* class email spell.
	* Class Email Spell can be used with other classess such as the html widget class,
	* however the html widget class, in this example, must be made aware of any data 
	* structures that the spell class may pass to it. Use this file like an include
	* file for such purposes. I suggest require_once.
	* @author Angelo (Angles) Puglisi <angles@aminvestments.com>
	* @copyright Copyright (C) 2002 Angelo Tony Puglisi (Angles)
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package email
	* @version $Id$
	* @internal Based on AngleMail http://www.anglemail.org/
	*/

	
	/**
	 * Unknown what spell checker wants
	 */
	define('SP_FEED_UNKNOWN',1);
	/**
	 * Spell checker want single words
	 */
	define('SP_FEED_WORDS',2);
	/**
	 * Spell checker want lines of text
	 */
	define('SP_FEED_LINES',4);
	

	/**
	* Coherently combine spelling suggextions with the original text
	*
	* Holds information about a misspelled word including where 
	* it appears in the original text and up to MAX_SUGGEST suggestions.
	* There should be different ways to spell check depending on what your system 
	* has installed. The php builtin pspell extension appears to take one word 
	* at a time, the command line version of aspell takes a string, a line of text, 
	* at one time. class.spell constructor should determine this and fill $this->sp_feed_type.
	* @package email
	*/	
	class correction_info
	{
		/**
		 * @var string $orig_word
		 */
		var $orig_word;
		var $orig_word_clean;
		/**
		 * @var integer $line_num
		 */
		var $line_num;
		/**
		 * @var integer $word_num
		 */
		var $word_num;
		/**
		 * @var array $suggestions
		 */
		var $suggestions;
		
		function correction_info()
		{
			$this->orig_word='';
			$this->orig_word_clean = '';
			$this->line_num=0;
			$this->word_num=0;
			$this->suggestions=array();
		}
	}
?>
