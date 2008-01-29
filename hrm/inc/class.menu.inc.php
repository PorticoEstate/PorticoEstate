<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
	* @subpackage core
 	* @version $Id: class.menu.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package hrm
	 */

	class hrm_menu
	{
		var $sub;

		var $public_functions = array
		(
			'links'	=> True,
		);

		function hrm_menu($sub='')
		{
			$this->sub		= $sub;
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
		}

		function links($page='',$page_2='')
		{
			$currentapp=$this->currentapp;
			$sub = $this->sub;

			$i=0;
			if($sub=='user')
			{
				$menu['module'][$i]['this']=True;
			}
			$menu['module'][$i]['link'] 		= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $currentapp.'.uiuser.index'));
			$menu['module'][$i]['name'] 		= lang('User');
			$menu['module'][$i]['statustext'] 	= lang('User');
			$i++;

			if($sub=='job')
			{
				$menu['module'][$i]['this']=True;
			}
			$menu['module'][$i]['link']			=	$GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $currentapp.'.uijob.index'));
			$menu['module'][$i]['name']			=	lang('Job');
			$menu['module'][$i]['statustext']		=	lang('Job');
			$i++;

			if($sub=='place')
			{
				$menu['module'][$i]['this']=True;
			}
			$menu['module'][$i]['link']			=	$GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $currentapp.'.uiplace.index'));
			$menu['module'][$i]['name']			=	lang('PLace');
			$menu['module'][$i]['statustext']		=	lang('Place');
			$i++;

			$j=0;
			if ($sub == 'job')
			{
				if($page=='job_type')
				{
					$menu['sub_menu'][$j]['this']=True;
				}
				$menu['sub_menu'][$j]['link']			=	$GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $currentapp.'.uijob.index'));
				$menu['sub_menu'][$j]['name']			=	lang('Job type');
				$menu['sub_menu'][$j]['statustext']		=	lang('Job type');
				$j++;

				if($page=='hierarchy')
				{
					$menu['sub_menu'][$j]['this']=True;
				}
				$menu['sub_menu'][$j]['link']			=	$GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $currentapp.'.uijob.hierarchy'));
				$menu['sub_menu'][$j]['name']			=	lang('Organisation');
				$menu['sub_menu'][$j]['statustext']		=	lang('Organisation');
				$j++;
			}

			return $menu;
		}
	}
