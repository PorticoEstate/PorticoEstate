<?php
	/**
	* phpGroupWare - DEMO: A demo application.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package demo
	* @subpackage setup
 	* @version $Id$
	*/


	/**
	 * Description
	 * @package demo
	 */

	$GLOBALS['phpgw']->locations->add('.demo_location', 'Demo location', 'demo', $allow_grant = true, $custom_tbl = 'phpgw_demo_table', $c_function = true);
