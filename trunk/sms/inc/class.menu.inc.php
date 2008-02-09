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
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package sms
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
		}

		/**
		 * Get the menus for the sms
		 *
		 * @return array available menus for the current user
		 */
		public function get_menu()
		{
			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'sms';

			$acl = CreateObject('phpgwapi.acl');
			$menus = array();

			$start_page = 'sms';
			if ( isset($GLOBALS['phpgw_info']['user']['preferences']['sms']['default_start_page'])
					&& $GLOBALS['phpgw_info']['user']['preferences']['sms']['default_start_page'] )
			{
					$start_page = $GLOBALS['phpgw_info']['user']['preferences']['sms']['default_start_page'];
			}

			$menus['navbar'] = array
			(
				'sms' => array
				(
					'text'	=> lang('sms'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "sms.ui{$start_page}.index") ),
					'image'	=> array('sms', 'navbar'),
					'order'	=> 35,
					'group'	=> 'facilities management'
				),
			);

			$menus['toolbar'] = array();
			if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
			{

				$menus['admin'] = array
				(
					'config'	=> array
					(
						'text'	=> lang('config'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'sms.uiconfig.index'))
					),
					'refresh'	=> array
					(
						'text'	=> lang('Daemon manual refresh'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'sms.uiconfig.daemon_manual'))
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
						'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname' => 'sms', 'type'=> 'user') )
					),
					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Grant Access', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'preferences.uiadmin_acl.aclprefs', 'acl_app'=> 'sms') )
					)
				);

				$menus['toolbar'][] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname'	=> 'sms')),
					'image'	=> array('sms', 'preferences')
				);
			}

			$command_children = array
			(
				'commands'	=> array
				(
					'text'	=> lang('commands'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'sms.uicommand.index'))
				),
				'log'	=> array
				(
					'text'	=> lang('log'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'sms.uicommand.log'))
				)
			);

			$menus['navigation'] = array
			(
				'inbox'	=> array
				(
					'text'	=> lang('Inbox'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'sms.uisms.index'))
				),
				'outbox'	=> array
				(
					'text'	=> lang('Outbox'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'sms.uisms.outbox'))
				),
				'autoreply'	=> array
				(
					'text'	=> lang('Autoreply'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'sms.uiautoreply.index'))
				),
				'board'	=> array
				(
					'text'	=> lang('Boards'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'sms.uiboard.index'))
				),
				'command'	=> array
				(
					'text'	=> lang('Command'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'sms.uicommand.index')),
					'children'	=> $command_children
				),
				'custom'	=> array
				(
					'text'	=> lang('Custom'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'sms.uicustom.index'))
				),
				'poll'	=> array
				(
					'text'	=> lang('Polls'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'sms.uipoll.index'))
				)
			);
			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;
			return $menus;
		}


		function links($page='',$page_2='')
		{
			$currentapp='sms';
			$sub = $this->sub;

			$menu = $GLOBALS['phpgw']->session->appsession('menu',substr(md5($currentapp.$sub . '_' . $page . '_' . $page_2),-20));

			if(!isset($menu) || !$menu)
			{
				$menu = array(); 

				$i=0;
				if($sub=='.inbox')
				{
					$menu['module'][$i]['this']=True;
				}
				$menu['module'][$i]['url'] 		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uisms.index'));
				$menu['module'][$i]['text'] 		= lang('Inbox');
				$menu['module'][$i]['statustext'] 	= lang('Inbox');
				$i++;

				if($sub=='.outbox')
				{
					$menu['module'][$i]['this']=True;
				}
				$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uisms.outbox'));
				$menu['module'][$i]['text']			=	lang('outbox');
				$menu['module'][$i]['statustext']	=	lang('outbox');
				$i++;

				if($sub=='.autoreply')
				{
					$menu['module'][$i]['this']=True;
				}
				$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiautoreply.index'));
				$menu['module'][$i]['text']			=	lang('autoreply');
				$menu['module'][$i]['statustext']		=	lang('autoreply');
				$i++;

				if($sub=='.board')
				{
					$menu['module'][$i]['this']=True;
				}
				$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiboard.index'));
				$menu['module'][$i]['text']			=	lang('boards');
				$menu['module'][$i]['statustext']		=	lang('boards');
				$i++;

				if($sub=='.command')
				{
					$menu['module'][$i]['this']=True;
				}
				$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uicommand.index'));
				$menu['module'][$i]['text']			=	lang('commands');
				$menu['module'][$i]['statustext']		=	lang('commands');
				$i++;

				if($sub=='.custom')
				{
					$menu['module'][$i]['this']=True;
				}
				$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uicustom.index'));
				$menu['module'][$i]['text']			=	lang('customs');
				$menu['module'][$i]['statustext']		=	lang('customs');
				$i++;

				if($sub=='.poll')
				{
					$menu['module'][$i]['this']=True;
				}
				$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uipoll.index'));
				$menu['module'][$i]['text']			=	lang('polls');
				$menu['module'][$i]['statustext']		=	lang('polls');
				$i++;

				if($sub=='.config')
				{
					$menu['module'][$i]['this']=True;
				}
				$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiconfig.index'));
				$menu['module'][$i]['text']			=	lang('config');
				$menu['module'][$i]['statustext']		=	lang('config');
				$i++;

				$j=0;
				if ($sub == '.config')
				{
					if($page=='.config.type')
					{
						$menu['sub_menu'][$j]['this']=True;
					}
					$menu['sub_menu'][$j]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiconfig.index'));
					$menu['sub_menu'][$j]['text']			=	lang('config');
					$menu['sub_menu'][$j]['statustext']		=	lang('config');
					$j++;

					if($page=='.config.daemon_manual')
					{
						$menu['sub_menu'][$j]['this']=True;
					}
					$menu['sub_menu'][$j]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiconfig.daemon_manual'));
					$menu['sub_menu'][$j]['text']			=	lang('Daemon manual refresh');
					$menu['sub_menu'][$j]['statustext']		=	lang('Daemon manual refresh');
					$j++;
				}

				if ($sub == '.command')
				{
					if($page=='.command.list')
					{
						$menu['sub_menu'][$j]['this']=True;
					}
					$menu['sub_menu'][$j]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uicommand.index'));
					$menu['sub_menu'][$j]['text']			=	lang('commands');
					$menu['sub_menu'][$j]['statustext']		=	lang('commands');
					$j++;

					if($page=='.command.log')
					{
						$menu['sub_menu'][$j]['this']=True;
					}
					$menu['sub_menu'][$j]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uicommand.log'));
					$menu['sub_menu'][$j]['text']			=	lang('log');
					$menu['sub_menu'][$j]['statustext']		=	lang('log');
					$j++;
				}

				$GLOBALS['phpgw']->session->appsession('menu',substr(md5($currentapp.$sub . '_' . $page . '_' . $page_2),-20),$menu);
			}

			$GLOBALS['phpgw']->session->appsession('menu_sms','sidebox',$menu);
			return $menu;
		}
	}
