<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage manual
 	* @version $Id$
	*/

	/**
	 * This is the manual entry for the overview
	 */

	$GLOBALS['phpgw']->help->set_params(array('app_name'	=> 'property',
												'title'		=> lang('property') . ' - ' . lang('overview'),
												'controls'	=> array('down' => 'property.php'
												)));
	$values	= array
	(
		'title'		=> lang('property') . ' - ' . lang('overview'),
		'controls'	=> array('down' => 'property.php'),
		'app_name'	=> 'property',
		'intro'				=> 'A web-based Facilities Management application. Integration with other applications in the phpGroupWare suite.',
		'text'	=> 'Preferences settings:<br>When you enter the property the first time it shows on the top the message *Please set your preferences for this application!*. This means you still have to adapt the application for your special needs. Each applications preferences section can be found within the preferences application. For further informations please see the section preferences.'
	);

	$GLOBALS['phpgw']->help->xdraw($values);
?>
