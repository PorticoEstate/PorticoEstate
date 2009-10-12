<?php
	/**
	 * felamimail - Menus
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @copyright Copyright (C) 2007 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package felamimail 
	 * @version $Id$
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */


	/**
	 * Menus
	 *
	 * @package felamimail
	 */	
	class felamimail_menu
	{
		/**
		 * Get the menus for the felamimail
		 *
		 * @return array available menus for the current user
		 */
		function get_menu()
		{
			$preferences = ExecMethod('felamimail.bopreferences.getPreferences');
			$linkData = array
			(
				'menuaction'    => 'felamimail.uicompose.compose'
			);

			$menus = array();

			$menus['navbar'] = array
			(
				'felamimail'	=> array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Felamimail', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/felamimail/index.php'),
					'image'	=> array('felamimail', 'navbar'),
					'order'	=> 6,
					'group'	=> 'office'
				)
			);

			$menus['toolbar'] = array
			(
				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('New', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'felamimail.uicompose.compose'))
				),
			);


/*
	$file = array(
		array(
			'text' => '<a class="textSidebox" href="'. htmlspecialchars($GLOBALS['phpgw']->link('/index.php', $linkData)).'" target="_blank" onclick="egw_openWindowCentered(\''.$GLOBALS['phpgw']->link('/index.php', $linkData).'\',\''.lang('compose').'\',700,750); return false;">'.lang('compose'),
                        'no_lang' => true,
                    ),

	);
*/

			if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
			{
				$menus['admin'] = array
				(
					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Site Configuration', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'emailadmin.emailadmin_ui.listProfiles'))
					)
				);
			}

			if ( isset($GLOBALS['phpgw_info']['user']['apps']['preferences']) )
			{
				$menus['preferences'] = array
				(
					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname' => 'felamimail')),
					),
					array
					(
						'text'	=>'Manage Folders',
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'felamimail.uipreferences.listFolder') )
					)
				);

				
				if($preferences && ($preferences->userDefinedAccounts || $preferences->userDefinedIdentities))
				{
					$menus['preferences'][] = 	array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Manage eMail: Accounts / Identities', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'felamimail.uipreferences.listAccountData')),
					);
				}

				if($preferences && $preferences->ea_user_defined_signatures)
				{
					$menus['preferences'][] = 	array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Manage Signatures', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'felamimail.uipreferences.listSignatures')),
					);

				}

				$menus['toolbar'][] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname'	=> 'felamimail')),
					'image'	=> array('felamimail', 'preferences')
				);
				$menus['toolbar'][] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Manage Folders', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'felamimail.uipreferences.listFolder') )
				);
			}

			$menus['navigation'] = array
			(
				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('New', array(), true),
					'url'	=> "javascript:openwindow('"
					 			. $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'felamimail.uicompose.compose')) . "','700','600')"
				)
			);

			if($preferences)
			{
				$icServer = $preferences->getIncomingServer(0);
				if(is_a($icServer, 'defaultimap'))
				{
					if($icServer->enableSieve) 
					{
						$menus['navigation'][] = array
						(
							array
							(
								'text'	=> $GLOBALS['phpgw']->translation->translate('filter rules', array(), true),
								'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'felamimail.uisieve.listRules'))
							)
						);
						$menus['navigation'][] = array
						(
							array
							(
								'text'	=> $GLOBALS['phpgw']->translation->translate('vacation notice', array(), true),
								'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'felamimail.uisieve.editVacation'))
							)
						);
						$menus['navigation'][] = array
						(
							array
							(
								'text'	=> $GLOBALS['phpgw']->translation->translate('email notification', array(), true),
								'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'felamimail.uisieve.editEmailNotification'))
							)
						);
					}
				}

				$ogServer = $preferences->getOutgoingServer(0);
				if(is_a($ogServer, 'defaultsmtp'))
				{
					if($ogServer->editForwardingAddress)
					{
						$menus['navigation'][] = array
						(
							array
							(
								'text'	=> $GLOBALS['phpgw']->translation->translate('Forwarding', array(), true),
								'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'felamimail.uipreferences.editForwardingAddress'))
							)
						);
					}
				}
			}
		//$menus['folders'] = phpgwapi_menu::get_categories('felamimail');
			return $menus;
		}
	}
