<?php
	/**
	 * phpGroupWare Administration Misc Page Renderers
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @author coreteam <phpgroupware-developers@gnu.org>
	 * @author Various Others <unknown>
	 * @copyright Copyright (C) 2003-2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 * @category gui
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
	 *  Miscellaneous Admin Pages renderer 
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @author coreteam <phpgroupware-developers@gnu.org>
	 * @author Various Others <unknown>
	 * @copyright Copyright (C) 2003-2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 */
	class admin_uimainscreen
	{
		/**
		 * @var array $public_functions the publicly callable methods
		 */
		public $public_functions = array
		(
			'index'			=> true,
			'mainscreen'	=> true
		);

		/**
		 * Constucttor
		 */
		public function __construct()
		{
			$menuaction = phpgw::get_var('menuaction', 'location');
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
			$GLOBALS['phpgw']->nextmatchs = CreateObject('phpgwapi.nextmatchs');
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'admin';
		}

		/**
		 * Render the admin menu
		 *
		 * @return void
		 */
		function mainscreen()
		{
			$menu		= createObject('phpgwapi.menu');
			$navbar		= $menu->get('navbar');
			$navigation = $menu->get('admin');

			$treemenu = '';
			foreach ( $GLOBALS['phpgw_info']['user']['apps'] as $app => $app_info )
			{
				if(!in_array($app, array('logout', 'about', 'preferences')) && isset($navbar[$app]))
				{
					$treemenu .= $menu->render_menu($app, $navigation[$app], $navbar[$app], true);
				}
			}
			$GLOBALS['phpgw']->common->phpgw_header(true);
			echo $treemenu;
		}

		/**
		 * Render the welcome screen editor
		 *
		 * @return void
		 */
		public function index()
		{
			if ( phpgw::get_var('cancel', 'bool', 'POST') )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'admin.uimainscreen.mainscreen'));
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::admin::mainscreen';

			$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);
			$GLOBALS['phpgw']->template->set_file(array('message' => 'mainscreen_message.tpl'));
			$GLOBALS['phpgw']->template->set_block('message','form','form');
			$GLOBALS['phpgw']->template->set_block('message','row','row');
			$GLOBALS['phpgw']->template->set_block('message','row_2','row_2');

			$GLOBALS['phpgw']->common->phpgw_header(true);
			$select_lang = phpgw::get_var('select_lang', 'string', 'POST');
			$section     = phpgw::get_var('section', 'string', 'POST');

			if ( phpgw::get_var('update', 'bool', 'POST') )
			{
				$message     = phpgw::get_var('message', 'string', 'POST');

				$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_lang WHERE message_id='$section" . "_message' AND app_name='"
					. "$section' AND lang='$select_lang'",__LINE__,__FILE__);
				$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_lang VALUES ('$section" . "_message','$section','$select_lang','"
					. addslashes($message) . "')",__LINE__,__FILE__);
				$message = '<center>'.lang('message has been updated').'</center>';
			}

			$tr_class = '';
			if (empty($select_lang))
			{
				$GLOBALS['phpgw']->template->set_var('header_lang',lang('Main screen message'));
				$GLOBALS['phpgw']->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uimainscreen.index')));
				$GLOBALS['phpgw']->template->set_var('tr_class', 'th');
				$GLOBALS['phpgw']->template->set_var('value','&nbsp;');
				$GLOBALS['phpgw']->template->fp('rows','row_2',True);

				$tr_class = $GLOBALS['phpgw']->nextmatchs->alternate_row_class($tr_class);
				$GLOBALS['phpgw']->template->set_var('tr_class',$tr_class);

				$select_lang = '<select name="select_lang">';
				$GLOBALS['phpgw']->db->query("SELECT lang,phpgw_languages.lang_name,phpgw_languages.lang_id FROM phpgw_lang,phpgw_languages WHERE "
					. "phpgw_lang.lang=phpgw_languages.lang_id GROUP BY lang,phpgw_languages.lang_name,"
					. "phpgw_languages.lang_id ORDER BY lang");
				while ($GLOBALS['phpgw']->db->next_record())
				{
					$select_lang .= '<option value="' . $GLOBALS['phpgw']->db->f('lang') . '">' . $GLOBALS['phpgw']->db->f('lang_id')
						. ' - ' . $GLOBALS['phpgw']->db->f('lang_name') . '</option>';
				}
				$select_lang .= '</select>';
				$GLOBALS['phpgw']->template->set_var('label',lang('Language'));
				$GLOBALS['phpgw']->template->set_var('value',$select_lang);
				$GLOBALS['phpgw']->template->fp('rows','row',True);

				$tr_class = $GLOBALS['phpgw']->nextmatchs->alternate_row_class($tr_class);
				$GLOBALS['phpgw']->template->set_var('tr_class',$tr_class);
				$select_section = '<select name="section"><option value="mainscreen">' . lang('Main screen')
					. '</option><option value="loginscreen">' . lang("Login screen") . '</option>'
					. '</select>';
				$GLOBALS['phpgw']->template->set_var('label',lang('Section'));
				$GLOBALS['phpgw']->template->set_var('value',$select_section);
				$GLOBALS['phpgw']->template->fp('rows','row',True);

				$tr_class = $GLOBALS['phpgw']->nextmatchs->alternate_row_class($tr_class);
				$GLOBALS['phpgw']->template->set_var('tr_class', $tr_class);
				$GLOBALS['phpgw']->template->set_var('value','<input type="submit" name="submit" value="' . lang('Submit')
					. '"><input type="submit" name="cancel" value="'. lang('cancel') .'">');
				$GLOBALS['phpgw']->template->fp('rows','row_2',True);
			}
			else
			{
				$GLOBALS['phpgw']->db->query("SELECT content FROM phpgw_lang WHERE lang='$select_lang' AND message_id='$section"
				. "_message'");
				$GLOBALS['phpgw']->db->next_record();
				$current_message = $GLOBALS['phpgw']->db->f('content');

				if ($section == 'mainscreen')
				{
					$GLOBALS['phpgw']->template->set_var('header_lang',lang('Edit main screen message'));
				}
				else
				{
					$GLOBALS['phpgw']->template->set_var('header_lang',lang('Edit login screen message'));
				}

				$GLOBALS['phpgw']->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uimainscreen.index')));
				$GLOBALS['phpgw']->template->set_var('select_lang',$select_lang);
				$GLOBALS['phpgw']->template->set_var('section',$section);
				$GLOBALS['phpgw']->template->set_var('tr_class', 'th');
				$GLOBALS['phpgw']->template->set_var('value','&nbsp;');
				$GLOBALS['phpgw']->template->fp('rows','row_2',True);

				$tr_class = $GLOBALS['phpgw']->nextmatchs->alternate_row_class($tr_class);
				$GLOBALS['phpgw']->template->set_var('tr_class',$tr_class);
				$GLOBALS['phpgw']->template->set_var('value','<textarea name="message" cols="50" rows="10" wrap="virtual">' . stripslashes($current_message) . '</textarea>');
				$GLOBALS['phpgw']->template->fp('rows','row_2',True);

				$tr_class = $GLOBALS['phpgw']->nextmatchs->alternate_row_class($tr_class);
				$GLOBALS['phpgw']->template->set_var('tr_class', $tr_class);
				$GLOBALS['phpgw']->template->set_var('value','<input type="submit" name="update" value="' . lang('Update')
					. '"><input type="submit" name="cancel" value="'. lang('cancel') .'">'
				);
				$GLOBALS['phpgw']->template->fp('rows','row_2',True);
			}

			$GLOBALS['phpgw']->template->set_var('lang_cancel',lang('Cancel'));
			$GLOBALS['phpgw']->template->set_var('error_message',$message);
			$GLOBALS['phpgw']->template->pfp('out','form');
		}
	}
