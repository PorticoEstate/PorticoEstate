<?php
	/**
	* phpGroupWare - logistic: a part of a Facilities Management System.
	*
	* @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
	* @copyright Copyright (C) 2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/
	* @package property
	* @subpackage logistic
 	* @version $Id$
	*/

	class logistic_menu
	{
		function get_menu()
		{
			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'logistic';
			$menus = array();

			$menus['navbar'] = array
			(
				'logistic' => array
				(
					'text'	=> lang('logistic'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'logistic.uiproject.index') ),
          'image'	=> array('property', 'location'),
					'order'	=> 10,
					'group'	=> 'office'
				)
			);

			$menus['navigation'] =  array
			(
				'project' => array
				(
					'text'	=> lang('project'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'logistic.uiproject.index') ),
          'image'	=> array('property', 'location_tenant'),
					'children'	=> array(
							'activity' => array
								(
										'text'	=> lang('activity'),
										'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'logistic.uiactivity.index') ),
										'image'	=> array('property', 'location_tenant')
								),
								'requirement' => array
								(
										'text'	=> lang('requirement'),
										'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'logistic.uirequirement.index') ),
										'image'	=> array('property', 'location_tenant'),
								),
								'booking' => array
								(
										'text'	=> lang('booking'),
										'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'logistic.uibooking.index') ),
										'image'	=> array('property', 'location_tenant'),
								),
						)
				)     
			);
			
			$menus['folders'] = phpgwapi_menu::get_categories('bergen');

			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;

			return $menus;
		}
	}