<?php
	/**
	* phpGroupWare - SMS:
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package sms
	* @subpackage core
 	* @version $Id: class.menu.inc.php 17785 2006-12-27 10:39:15Z sigurdne $
	*/

	/**
	 * Description
	 * @package hrm
	 */

	class sms_menu
	{
		var $sub;

		var $public_functions = array
		(
			'links'	=> True,
		);

		function sms_menu($sub='')
		{
			if(!$sub)
			{
				$this->sub		= $sub;
			}
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
		}

		function links($page='',$page_2='')
		{
			$currentapp=$this->currentapp;
			$sub = $this->sub;

			$i=0;
			if($sub=='.inbox')
			{
				$menu['module'][$i]['this']=True;
			}
			$menu['module'][$i]['link'] 		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uisms.index'));
			$menu['module'][$i]['name'] 		= lang('Inbox');
			$menu['module'][$i]['statustext'] 	= lang('Inbox');
			$i++;

			if($sub=='.outbox')
			{
				$menu['module'][$i]['this']=True;
			}
			$menu['module'][$i]['link']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uisms.outbox'));
			$menu['module'][$i]['name']			=	lang('outbox');
			$menu['module'][$i]['statustext']	=	lang('outbox');
			$i++;


			if($sub=='.autoreply')
			{
				$menu['module'][$i]['this']=True;
			}
			$menu['module'][$i]['link']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiautoreply.index'));
			$menu['module'][$i]['name']			=	lang('autoreply');
			$menu['module'][$i]['statustext']		=	lang('autoreply');
			$i++;
			if($sub=='.board')
			{
				$menu['module'][$i]['this']=True;
			}
			$menu['module'][$i]['link']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiboard.index'));
			$menu['module'][$i]['name']			=	lang('boards');
			$menu['module'][$i]['statustext']		=	lang('boards');
			$i++;
			if($sub=='.command')
			{
				$menu['module'][$i]['this']=True;
			}
			$menu['module'][$i]['link']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uicommand.index'));
			$menu['module'][$i]['name']			=	lang('commands');
			$menu['module'][$i]['statustext']		=	lang('commands');
			$i++;
			if($sub=='.custom')
			{
				$menu['module'][$i]['this']=True;
			}
			$menu['module'][$i]['link']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uicustom.index'));
			$menu['module'][$i]['name']			=	lang('customs');
			$menu['module'][$i]['statustext']		=	lang('customs');
			$i++;
			if($sub=='.poll')
			{
				$menu['module'][$i]['this']=True;
			}
			$menu['module'][$i]['link']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uipoll.index'));
			$menu['module'][$i]['name']			=	lang('polls');
			$menu['module'][$i]['statustext']		=	lang('polls');
			$i++;


			if($sub=='.config')
			{
				$menu['module'][$i]['this']=True;
			}
			$menu['module'][$i]['link']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiconfig.index'));
			$menu['module'][$i]['name']			=	lang('config');
			$menu['module'][$i]['statustext']		=	lang('config');
			$i++;

			$j=0;
			if ($sub == '.config')
			{
				if($page=='.config.type')
				{
					$menu['sub_menu'][$j]['this']=True;
				}
				$menu['sub_menu'][$j]['link']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiconfig.index'));
				$menu['sub_menu'][$j]['name']			=	lang('config');
				$menu['sub_menu'][$j]['statustext']		=	lang('config');
				$j++;

				if($page=='.config.daemon_manual')
				{
					$menu['sub_menu'][$j]['this']=True;
				}
				$menu['sub_menu'][$j]['link']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiconfig.daemon_manual'));
				$menu['sub_menu'][$j]['name']			=	lang('Daemon manual refresh');
				$menu['sub_menu'][$j]['statustext']		=	lang('Daemon manual refresh');
				$j++;
			}


			if ($sub == '.command')
			{
				if($page=='.command.list')
				{
					$menu['sub_menu'][$j]['this']=True;
				}
				$menu['sub_menu'][$j]['link']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uicommand.index'));
				$menu['sub_menu'][$j]['name']			=	lang('commands');
				$menu['sub_menu'][$j]['statustext']		=	lang('commands');
				$j++;

				if($page=='.command.log')
				{
					$menu['sub_menu'][$j]['this']=True;
				}
				$menu['sub_menu'][$j]['link']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uicommand.log'));
				$menu['sub_menu'][$j]['name']			=	lang('log');
				$menu['sub_menu'][$j]['statustext']		=	lang('log');
				$j++;
			}


			return $menu;
		}
	}
