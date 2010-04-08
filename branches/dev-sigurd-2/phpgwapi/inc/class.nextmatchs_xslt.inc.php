<?php
	/**
	* Handles xslt nm widgets
	* @author Bettina Gille <ceb@phpgroupware.org>
	* @copyright Copyright (C) 2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id$
	*/

	/**
	* XSLT nextmatch
	*
	* @package phpgwapi
	* @subpackage gui
	*/
	class phpgwapi_nextmatchs_xslt
	{
		var $maxmatches;
		var $action;
		var $template;

		function nextmatchs_xslt()
		{

		}

		function xslt_filter($data=0)
		{
			$GLOBALS['phpgw']->xslttpl->add_file('filter_select', PHPGW_TEMPLATE_DIR);

			if(is_array($data))
			{
				$filter		= (isset($data['filter'])?$data['filter']:'');
				$format		= (isset($data['format'])?$data['format']:'all');
				$link_data	= (isset($data['link_data'])?$data['link_data']:'');
			}
			else
			{
				//$filter = phpgw::get_var('filter');
				//$filter = $data;
				//$format	= 'all';
				return False;
			}

			switch($format)
			{
				case 'yours':
					$filter_obj = array
					(
						array('key' => 'none','lang' => lang('show all')),
						array('key' => 'yours','lang' => lang('only yours'))
					);
					break;
				case 'private':
					$filter_obj = array
					(
						array('key' => 'none','lang' => lang('show all')),
						array('key' => 'private','lang' => lang('only private'))
					);
					break;
				default:
					$filter_obj = array
					(
						array('key' => 'none','lang' => lang('show all')),
						array('key' => 'yours','lang' => lang('only yours')),
						array('key' => 'private','lang' => lang('only private'))
					);
			}

			for($i=0;$i<count($filter_obj);$i++)
			{
				if($filter_obj[$i]['key'] == $filter)
				{
					$filter_obj[$i]['selected'] = 'yes';
				}
			}

			$filter_data = array
			(
				'filter_list'				=> $filter_obj,
				'lang_filter_statustext'	=> lang('Select the filter. To show all entries select SHOW ALL'),
				'lang_submit'				=> lang('submit'),
				'select_url'				=> $GLOBALS['phpgw']->link('/index.php',$link_data)
			);
			return $filter_data;
		}

		function xslt_search($values=0)
		{
			$GLOBALS['phpgw']->xslttpl->add_file('search_field', PHPGW_TEMPLATE_DIR);

			$search_data = array
			(
				'lang_searchfield_statustext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'	=> lang('Submit the search string'),
				'query'							=> $values['query'],
				'lang_search'					=> lang('search'),
				'select_url'					=> $GLOBALS['phpgw']->link('/index.php',$values['link_data'])
			);
			return $search_data;
		}

		function xslt_nm($values = 0)
		{
			$GLOBALS['phpgw']->xslttpl->add_file('nextmatchs', PHPGW_TEMPLATE_DIR);

			$start = isset($values['start']) && $values['start'] ? (int) $values['start'] : 0;

			$nm_data = array
			(
				'img_width'			=> $GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'] == 'funkwerk' ? '' : '12',
				'img_height'		=> $GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'] == 'funkwerk' ? '' : '12',
				'allow_all_rows'	=> isset($values['allow_all_rows']) ? true : false,
				'allrows'			=> isset($values['allrows']) && $values['allrows'] ? true : false,
				'start_record'		=> $start,
				'record_limit'		=> $this->maxmatches,
				'num_records'		=> (int) $values['num_records'],
				'all_records'		=> (int) $values['all_records'],
				'nextmatchs_url'	=> $GLOBALS['phpgw']->link('/index.php',$values['link_data']),
				'first_img'			=> $GLOBALS['phpgw']->common->image('phpgwapi','first'),
				'first_grey_img'	=> $GLOBALS['phpgw']->common->image('phpgwapi','first-grey'),
				'left_img'			=> $GLOBALS['phpgw']->common->image('phpgwapi','left'),
				'left_grey_img'		=> $GLOBALS['phpgw']->common->image('phpgwapi','left-grey'),
				'right_img'			=> $GLOBALS['phpgw']->common->image('phpgwapi','right'),
				'right_grey_img'	=> $GLOBALS['phpgw']->common->image('phpgwapi','right-grey'),
				'last_img'			=> $GLOBALS['phpgw']->common->image('phpgwapi','last'),
				'last_grey_img'		=> $GLOBALS['phpgw']->common->image('phpgwapi','last-grey'),
				'all_img'			=> $GLOBALS['phpgw']->common->image('phpgwapi','down_nm'),
				'title_first'		=> lang('first page'),
				'title_previous'	=> lang('previous page'),
				'title_next'		=> lang('next page'),
				'title_last'		=> lang('last page'),
				'title_all'			=> lang('show all'),
				'lang_showing'		=> $this->show_hits((int)$values['all_records'],$start,(int)$values['num_records'])
			);
			return $nm_data;
		}
	}
?>
