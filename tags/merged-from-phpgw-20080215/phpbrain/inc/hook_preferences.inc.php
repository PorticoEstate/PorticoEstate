<?php
	/**
	* Trouble Ticket System
	*
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpbrain
	* @subpackage hooks
	* @version $Id$
	*/

	$values = array(
		'Preferences'		=> $GLOBALS['phpgw']->link('/preferences/preferences.php',array('appname' => 'phpbrain')),
		'Edit Categories'	=> $GLOBALS['phpgw']->link('/index.php',
								array
								(
									'menuaction'	=> 'preferences.uicategories.index',
									'cats_app'		=> $appname,
									'cats_level'	=> true,
									'global_cats'	=> true
								))
	);
	display_section('phpbrain',$values);
?>