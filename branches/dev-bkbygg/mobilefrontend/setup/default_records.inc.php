<?php
	/**
	* phpGroupWare
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2013 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package mobilefrontend
	* @subpackage setup
 	* @version $Id$
	*/


	/**
	 * Description
	 * @package mobilefrontend
	 */


	// Sane defaults for the API
	$values = array
	(
		'usecookies'			=> 'True'
	);

	foreach ( $values as $name => $val )
	{
		$sql = "INSERT INTO phpgw_config VALUES('mobilefrontend', '{$name}', '{$val}')";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
	}
