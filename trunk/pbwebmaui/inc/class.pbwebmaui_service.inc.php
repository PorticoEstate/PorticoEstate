<?php
/**
 * pbwebmaui
 *
 * @author Philipp Kamps <pkamps@probusiness.de>
 * @author Andreas Schiller <aschiller@probusiness.de>
 * @copyright Copyright (C) 2003-2005 Free Software Foundation http://www.fsf.org/
 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
 * @package pbwebmaui
 * @subpackage service
 * @version $Id$
 */

/**
* service class
*
* @package pbwebmaui
* @subpackage service
*/
class pbwebmaui_service
{
	var $bomailfilter;

	// constructor
	function pbwebmaui_service()
	{
//		$this->bopreferences = CreateObject('email.bopreferences');
		$this->bomailfilter = CreateObject('pbwebmaui.bomailfilter');
	}

	function calendar_event($args)
	{
		/** /
		echo '<pre>';
		print_r (array(
			'_calendar_event_edit args'=>$args,
			'filterid'=>$filterid,
			'filter'=>$filter
			));
		echo '</pre>';
		/**/

		switch ($args['function']) {
			case 'view':
				return $this->_calendar_event_view($args);
			case 'edit':
				return $this->_calendar_event_edit($args);
			case 'update':
				return $this->_calendar_event_update($args);
			case 'delete':
				return $this->_calendar_event_delete($args);
		}
	}

	function _oooid($event_id)
	{
		return '.cal_'.$event_id;
	}

	function _calendar_event_view($args)
	{
		$filterid = $this->bomailfilter->find_name($this->_oooid($args['event_id']), 3);
		$filter = $this->bomailfilter->read_entry($filterid);

		$l_withooo = (is_array($filter) && $filter['active']);
		$l_ooomessage = $filter['autoreply'];
		$l_oooforwardto = $filter['forwardto'];

		$vars['pbwebmaui_withooo'] = array(
			'field'	=> lang('Generate Out of Office Message'),
			'data'	=> htmlentities($l_withooo? lang('yes') : lang('no'))
			);

		$vars['pbwebmaui_ooomessage'] = array(
			'field'	=> lang('Message'),
			'data'	=> nl2br(htmlentities($l_ooomessage)) . '&nbsp;'
			);

		$vars['pbwebmaui_oooforwardto'] = array(
			'field'	=> lang('forward to'),
			'data'	=> htmlentities($l_oooforwardto) . '&nbsp;'
			);

		return (array("vars" => $vars));
	}

	function _calendar_event_edit($args)
	{
		$filterid = $this->bomailfilter->find_name($this->_oooid($args['event_id']), 3);
		$filter = $this->bomailfilter->read_entry($filterid);

		$l_withooo = (is_array($filter) && $filter['active']);
		$l_ooomessage = $filter['autoreply'];
		$l_oooforwardto = $filter['forwardto'];

		$fields['pbwebmaui_withooo'] = array(
			'name'=>'pbwebmaui_withooo',
			'disabled'=>''
			);

		$fields['pbwebmaui_ooomessage'] = array(
			'name'=>'pbwebmaui_ooomessage',
			'disabled'=>''
			);

		/* $_FILES (in bomailfilter::update) does not work??? * /
		$fields['pbwebmaui_ooomessage_file'] = array(
			'name'=>'pbwebmaui_ooomessage_file',
			'disabled'=>''
			);
		/**/

		$fields['pbwebmaui_oooforwardto'] = array(
			'name'=>'pbwebmaui_oooforwardto',
			'disabled'=>''
			);

		$vars['pbwebmaui_withooo'] = array(
			'field'	=> lang('Generate Out of Office Message'),
			'data'	=> '<input type="checkbox" name="cal[pbwebmaui_withooo]" value="'.($l_withooo?'true':'false').'" '.($l_withooo?'"checked"':'').'>'
			);

		$vars['pbwebmaui_ooomessage'] = array(
			'field'	=> lang('Message'),
			'data'	=> '<textarea name="cal[pbwebmaui_ooomessage]" rows="5" cols="70" wrap="virtual" maxlength="2048">'.$l_ooomessage.'</textarea>'
			);

		$vars['pbwebmaui_ooomessage_file'] = array(
			'field'	=> lang('upload text file'),
			'data'	=> '<input type="hidden" name="MAX_FILE_SIZE" value="0" /><input name="cal[pbwebmaui_ooomessage_file]" type="file" size="60" />'
			);

		$js_addylink = $GLOBALS['phpgw']->link(
			"/index.php",
			array(
				"menuaction"=>"phpgwapi.uijsaddressbook.show",
				"viewmore" => "1",
				"cat_id" => "-1",
				"update_opener" => "1"
				)
			);

		$js_addybook  = '<script type="text/javascript">'."\n";
		$js_addybook .= '<!--'."\n";
		$js_addybook .= '	function addybook(extraparm)'."\n";
		$js_addybook .= '	{'."\n";
		$js_addybook .= '		Window1 = window.open ("'.$js_addylink.'"+extraparm, "Search", "width=800, height=600, toolbar=no, scrollbars=yes, resizable=yes");'."\n";
		$js_addybook .= '	}'."\n";
		$js_addybook .= '//-->'."\n";
		$js_addybook .= '</script>'."\n";

		$vars['pbwebmaui_oooforwardto'] = array(
			'field'	=> lang('forward to'),
			'data'	=> $js_addybook .
				'<input name="cal[pbwebmaui_oooforwardto]" type="text" size="60" value="'.$l_oooforwardto.'" /><a href="javascript:addybook(\'&hidecc=1&hidebcc=1&formname=app_form&fn_to=cal[pbwebmaui_oooforwardto]\')">'.lang('select address').'</a>'
			);

		return (array("fields" => $fields, "vars" => $vars));
	}

	function _calendar_event_update($args)
	{
		$name = $this->_oooid($args['event_id']);
		$this->bomailfilter->update(array('name'=>$name, 'data'=>$args));
	}

	function _calendar_event_delete($args)
	{
		$name = $this->_oooid($args['event_id']);
		$this->bomailfilter->delete(array('name'=>$name));
	}
}
?>