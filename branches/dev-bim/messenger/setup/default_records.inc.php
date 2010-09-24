<?php
	/**
	* messenger
	* @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package frontend
	* @subpackage setup
	* @version $Id$
	*/

	$GLOBALS['phpgw']->locations->add('.', 'top', 'messenger', false);
	$GLOBALS['phpgw']->locations->add('.compose', 'compose messages to users', 'messenger', false);
	$GLOBALS['phpgw']->locations->add('.compose_groups', 'compose messages to groups', 'messenger', false);
	$GLOBALS['phpgw']->locations->add('.compose_global', 'compose global message', 'messenger', false);

