<?php
	/**
	* EMail
	*
	* @author Mark Cushman <mark@cushman.net>
	* @copyright Copyright (C) xxxx Mark Cushman
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @version $Id$
	* @internal Based on Aeromail http://the.cushman.net/
	*/
	
	/*
		magic_quotes_runtime essentially handles slashes when communicating with databases.
		PHP MANUAL says:
			If magic_quotes_runtime is enabled, most functions that return data from any sort of 
			external source including databases and text files will have quotes escaped with a backslash.
			
			this is undesirable so we turn it off
	*/
	@set_magic_quotes_runtime(0);
?>
