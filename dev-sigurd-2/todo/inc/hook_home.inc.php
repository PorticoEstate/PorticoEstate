<?php
	/**
	* Todo - admin hook
	*
	* @copyright Copyright (C) 2002,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package todo
	* @subpackage hooks
	* @version $Id$
	*/

	if ( isset($GLOBALS['phpgw_info']['user']['preferences']['todo']['mainscreen_showevents'])
		&& $GLOBALS['phpgw_info']['user']['preferences']['todo']['mainscreen_showevents'] == True)
	{
		$todo = CreateObject('todo.ui');
		$todo->bo->start = 0;
		$todo->bo->limit = 5;
		$todo->start = 0;
		$todo->limit = 5;
		$extra_data = '<td>'."\n".$todo->show_list_body(False).'</td>'."\n";

		$app_id = $GLOBALS['phpgw']->applications->name2id('todo');
		$GLOBALS['portal_order'][] = $app_id;

		$GLOBALS['phpgw']->portalbox->set_params(array('app_id'	=> $app_id,
														'title'	=> lang('todo')));
		$GLOBALS['phpgw']->portalbox->draw($extra_data);
	}
?>
