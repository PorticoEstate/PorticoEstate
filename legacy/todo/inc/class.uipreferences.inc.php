<?php
	/**
	* Todo preferences
	*
	* @author Craig Knudsen <cknudsen@radix.net>
	* @author Mark Peters <skeeter@phpgroupware.org>
	* @copyright Copyright (C) Craig Knudsen <cknudsen@radix.net>
	* @copyright Copyright (C) 2002,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package todo
	* @version $Id$
	* @internal Based on Webcalendar by Craig Knudsen http://www.radix.net/~cknudsen
	*/


	/**
	* Todo preferences
	*  
	* @package todo
	*/
	class uipreferences
	{
//		var $template_dir;
		var $template;

		var $bo;
		
		var $debug = False;
//		var $debug = True;

		var $theme;

		var $public_functions = array(
			'preferences' => True
		);

		function uipreferences()
		{
			$GLOBALS['phpgw']->nextmatchs = CreateObject('phpgwapi.nextmatchs');
			$this->template = $GLOBALS['phpgw']->template;
			$this->theme = $GLOBALS['phpgw_info']['theme'];
			$this->bo = CreateObject('todo.bopreferences');
		}

		function preferences()
		{
			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			$GLOBALS['phpgw_info']['flags']['noappheader'] = True;
			$GLOBALS['phpgw_info']['flags']['noappfooter'] = True;
			$GLOBALS['phpgw']->common->phpgw_header();

			$this->template->set_file(
			   Array(
   			   'pref_temp'      =>'pref.tpl'
			   )
			);

			$this->template->set_block('pref_temp','pref','pref');
			$this->template->set_block('pref_temp','pref_colspan','pref_colspan');
			$this->template->set_block('pref_temp','pref_list','pref_list');

			$var = Array(
				'title'	   	=>	lang('ToDo Preferences'),
				'action_url'	=>	$GLOBALS['phpgw']->link('/index.php',Array('menuaction'=>'todo.bopreferences.preferences')),
				'bg_color   '	=>	$this->theme['th_bg'],
				'submit_lang'	=>	lang('submit'),
				'text'   		=> '&nbsp;'
			);
	
			$this->output_template_array('row','pref_colspan',$var);

			$this->display_item(lang('Show ToDo items on main screen'),'<input type="checkbox" name="prefs[mainscreen_showevents]" value="True"'.(@$this->bo->prefs['todo']['mainscreen_showevents']?' checked':'').'>');

			$this->template->pparse('out','pref');
		}

		function output_template_array($row,$list,$var)
		{
			$this->template->set_var($var);
			$this->template->parse($row,$list,True);
		}

		function display_item($field,$data)
		{
			static $tr_color;
			$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
			$var = Array(
				'bg_color'	=>	$tr_color,
				'field'		=>	$field,
				'data'		=>	$data
			);
			$this->output_template_array('row','pref_list',$var);
		}
	}
