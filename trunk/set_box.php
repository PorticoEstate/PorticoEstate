<?php
	/**
	* phpGroupWare
	*
	* phpgroupware base
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @version $Id$
	*/

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'noheader'	=> True,
		'nofooter'	=> True,
		'currentapp'	=> 'home'
	);
	
	/**
	* Include phpgroupware header
	*/
	include_once('header.inc.php');

	/**
	* Move content "boxes" on the home.php "screen"
	*
	* @param integer $curr_position
	* @param integer $new_order
	* @param integer $offset
	* @param integer $value_to_check
	* @param integer $max_num
	*/
	function move_boxes($curr_position,$new_order,$offset,$value_to_check,$max_num)
	{
		if(isset($GLOBALS['phpgw_info']['user']['preferences']['portal_order'][$new_order]))
		{
			if($new_order == $max_num)
			{
				if($offset < 0)
				{
					@ksort($GLOBALS['phpgw_info']['user']['preferences']['portal_order']);
				}
				else
				{
					@krsort($GLOBALS['phpgw_info']['user']['preferences']['portal_order']);
				}
				while(list($seq_order,$appid) = each($GLOBALS['phpgw_info']['user']['preferences']['portal_order']))
				{
					if($seq_order != $value_to_check)
					{
						$prev_seq = $seq_order + $offset;
						$GLOBALS['phpgw']->preferences->delete('portal_order',$prev_seq);
						$GLOBALS['phpgw']->preferences->add('portal_order',$prev_seq,$appid);
					}
				}
			}
			else
			{
				$GLOBALS['phpgw']->preferences->delete('portal_order',$curr_position);
				$GLOBALS['phpgw']->preferences->add('portal_order',$curr_position,intval($GLOBALS['phpgw_info']['user']['preferences']['portal_order'][$new_order]));
			}
		}
		$GLOBALS['phpgw']->preferences->delete('portal_order',$new_order);
		$GLOBALS['phpgw']->preferences->add('portal_order',$new_order,intval($_GET['app']));
			
		$GLOBALS['phpgw']->preferences->save_repository();
	}

	switch($_GET['control'])
	{
		case 'up':
			$curr_position = $GLOBALS['phpgw']->common->find_portal_order((int) $_GET['app']);
			$max_count = count($GLOBALS['phpgw_info']['user']['preferences']['portal_order']) - 1;
			$offset = -1;
			if($curr_position == 0)
			{
				$new_order = $max_count;
			}
			else
			{
				$new_order = $curr_position + $offset;
			}
			move_boxes($curr_position,$new_order,$offset,0,$max_count);
			break;
		case 'down':
			$curr_position = $GLOBALS['phpgw']->common->find_portal_order((int) $_GET['app']);
			$max_count = count($GLOBALS['phpgw_info']['user']['preferences']['portal_order']) - 1;
			$offset = 1;
			if($curr_position == $max_count)
			{
				$new_order = 0;
			}
			else
			{
				$new_order = $curr_position + $offset;
			}
			move_boxes($curr_position,$new_order,$offset,$max_count,0);
			break;
		case 'edit':
		case 'question':
		case 'close':
		default:
	}

	Header('Location: '.$GLOBALS['phpgw']->link('/home.php'));
	$GLOBALS['phpgw']->common->phpgw_exit();
