<?php
	/**
	* Trouble Ticket System
	*
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package tts
	* @subpackage hooks
	* @version $Id: hook_preferences.inc.php 17581 2006-11-24 19:50:50Z sigurdne $
	*/

	$values = array(
		'Preferences'		=> $GLOBALS['phpgw']->link('/preferences/preferences.php',array('appname'=>'tts')),
		'Edit Categories'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'preferences.uicategories.index','cats_app'=>$appname,'cats_level'=>'True','global_cats'=>'True'))
	);
	display_section('tts','Trouble Ticket System',$values);
?>
