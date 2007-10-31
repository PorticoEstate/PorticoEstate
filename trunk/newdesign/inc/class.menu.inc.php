<?php
	/**
	* phpGroupWare - DEMO: a demo aplication.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package demo
	* @subpackage core
 	* @version $Id: class.menu.inc.php,v 1.3 2006/12/27 11:04:41 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package demo
	 */
	class newdesign_menu
	{
		var $sub;

		var $public_functions = array
		(
			'links'	=> True,
		);

		function newdesign_menu($sub='')
		{
			$this->sub		= $sub;
			$this->currentapp	= 'newdesign';
		}

		function links($page='',$page_2='')
		{
			$currentapp=$this->currentapp;
			$sub = $this->sub;

			$i=0;
			if($sub=='html')
			{
				$menu['module'][$i]['this']=True;
			}
			$menu['module'][$i]['url'] 		= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $currentapp.'.uinewdesign.index','output'=>'html'));
			$menu['module'][$i]['text'] 		= 'Form';
			$i++;

			if($sub=='wml')
			{
				$menu['module'][$i]['this']=True;
			}
			$menu['module'][$i]['url'] 		= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $currentapp.'.uinewdesign.grid','output'=>'html'));
			$menu['module'][$i]['text']			= 'Grid';
			$i++;

			if($sub=='alternative')
			{
				$menu['module'][$i]['this']=True;
			}
			$menu['module'][$i]['url'] 		= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $currentapp.'.uidemo.index2','output'=>'html'));
			$menu['module'][$i]['text']			= 'Alternative';
			$menu['module'][$i]['statustext']	= 'Alternative list';
			$i++;

			$GLOBALS['phpgw']->session->appsession('menu_demo','sidebox',$menu);
			return $menu;
		}
	}
