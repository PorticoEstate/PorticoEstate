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
		}

		function links($page='',$page_2='')
		{
			$currentapp='hrm';
			$sub = $this->sub;

			$menu = $GLOBALS['phpgw']->session->appsession('menu',substr(md5($currentapp.$sub . '_' . $page . '_' . $page_2),-20));

			if(!isset($menu) || !$menu)
			{
				$menu = array(); 

				$i=0;
				if($sub=='user')
				{
					$menu['module'][$i]['this']=True;
				}
				$menu['module'][$i]['url'] 		= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $currentapp.'.uiuser.index'));
				$menu['module'][$i]['text'] 		= lang('User');
				$menu['module'][$i]['statustext'] 	= lang('User');
				$i++;

				if($sub=='job')
				{
					$menu['module'][$i]['this']=True;
				}
				$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $currentapp.'.uijob.index'));
				$menu['module'][$i]['text']			=	lang('Job');
				$menu['module'][$i]['statustext']		=	lang('Job');
				$i++;

				if($sub=='place')
				{
					$menu['module'][$i]['this']=True;
				}
				$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $currentapp.'.uiplace.index'));
				$menu['module'][$i]['text']			=	lang('PLace');
				$menu['module'][$i]['statustext']		=	lang('Place');
				$i++;

				$j=0;
				if ($sub == 'job')
				{
					if($page=='job_type')
					{
						$menu['sub_menu'][$j]['this']=True;
					}
					$menu['sub_menu'][$j]['url']			=	$GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $currentapp.'.uijob.index'));
					$menu['sub_menu'][$j]['text']			=	lang('Job type');
					$menu['sub_menu'][$j]['statustext']		=	lang('Job type');
					$j++;

					if($page=='hierarchy')
					{
						$menu['sub_menu'][$j]['this']=True;
					}
					$menu['sub_menu'][$j]['url']			=	$GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $currentapp.'.uijob.hierarchy'));
					$menu['sub_menu'][$j]['text']			=	lang('Organisation');
					$menu['sub_menu'][$j]['statustext']		=	lang('Organisation');
					$j++;
				}

				$GLOBALS['phpgw']->session->appsession('menu',substr(md5($currentapp.$sub . '_' . $page . '_' . $page_2),-20),$menu);
			}
			$GLOBALS['phpgw']->session->appsession('menu_hrm','sidebox',$menu);
			return $menu;
		}
	}
