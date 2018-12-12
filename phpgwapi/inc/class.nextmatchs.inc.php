<?php
	/**
	* Handles limiting number of rows displayed
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Bettina Gille <ceb@phpgroupware.org>
	* @author Dave Hall skwashd phpgroupware.org
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id$
	*/

	/**
	* Include nextmatchs XSLT
	* @see nextmatchs_xslt
	* @todo make a generic abstract base class which both classes extend, not this wacky relationship
	*/
	phpgw::import_class('phpgwapi.nextmatchs_xslt');

	/**
	* Handles limiting number of rows displayed
	*
	* @package phpgwapi
	* @subpackage gui
	*/
	class phpgwapi_nextmatchs extends phpgwapi_nextmatchs_xslt
	{
		/**
		* @var int $maxmatches the maximum number of records to return
		*/
		var $maxmatches;

		/**
		* @var ??? $action ???
		*/
		var $action;

		/**
		* @var object $template template class
		*/
		var $template;

		/**
		* @var array $extra_filters ???
		*/
		var $extra_filters = array();

		/**
		* @var the current row class
		*/
		var $row_class = '';

		/**
		* Constructor
		*
		* @param bool $website ???
		*/
		public function __construct($website = false)
		{
			if ( !$website )
			{
				if ( !isset($GLOBALS['phpgw']->template)
					|| is_object($GLOBALS['phpgw']->template) )
				{
					$GLOBALS['phpgw']->template = createObject('phpgwapi.template', PHPGW_TEMPLATE_DIR);
				}
				$this->template =& $GLOBALS['phpgw']->template;
				$this->template->set_file(array
				(
					'_nextmatchs' => 'nextmatchs.tpl'
				));
				$this->template->set_block('_nextmatchs','nextmatchs');
				$this->template->set_block('_nextmatchs','nm_filter');
				$this->template->set_block('_nextmatchs','nm_form');
				$this->template->set_block('_nextmatchs','nm_icon');
				$this->template->set_block('_nextmatchs','nm_link');
				$this->template->set_block('_nextmatchs','nm_search');
				$this->template->set_block('_nextmatchs','nm_cats');
				$this->template->set_block('_nextmatchs','nm_search_filter');
				$this->template->set_block('_nextmatchs','nm_cats_search_filter');
			}

			$this->maxmatches = 15;
			if ( isset($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']) &&
				(int) $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0 )
			{
				$this->maxmatches =& $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}

			if ( isset($GLOBALS['phpgw_info']['menuaction']) )
			{
				$this->action = $GLOBALS['phpgw_info']['menuaction'];
			}
		}

		/**
		 * Fetch commonly-used REQUEST vars
		 *
		 * This calls get_var() from functions.inc.php
		 *
		 * @return array the variables fetched (filter, qfield, start, order, sort)
		 */
		protected function _get_var()
		{
			return array
			(
				phpgw::get_var('filter'),
				phpgw::get_var('qfield'),
				phpgw::get_var('start'),
				phpgw::get_var('order'),
				phpgw::get_var('sort'),
				phpgw::get_var('query')
			);
		}

		/**
		 * Wrapper for phpgw::link() which sets the menuaction to the current menuaction
		 *
		 * @see phpgw::link
		 * @param array $extravars the GET vars to be append to the end of the url
		 * @return thre link url
		 */
		function page($extravars = array())
		{
			if ( is_string($extravars) )
			{
				$extras = explode('&', $extravars);
				foreach ( $extras as $extra )
				{
					$tmp = explode('=', $extra);
					if ( count($tmp) == 2 )
					{
						$extravars[$tmp[0]] = $tmp[1];
					}
				}
			}
			$extravars['menuaction'] = $this->action;
			return $GLOBALS['phpgw']->link('/index.php', $extravars);
		}

		/**
		 * ?
		*
		 * @param $sn ?
		 * @param $start ?
		 * @param $total ?
		 * @param $extra ?
		 * @param $twidth ?
		 * @param $bgtheme ?
		 * @param $search_obj ?
		 * @param $filter_obj ?
		 * @param $showsearch ?
		 */
		function show_tpl($sn,$localstart,$total,$extra, $twidth, $bgtheme,$search_obj=0,$filter_obj=1,$showsearch=1,$yours=0,$cat_id=0,$cat_field='fcat_id')
		{
			list($filter,$qfield,$start,$order,$sort,$query) = $this->_get_var();

			$start = $localstart;

			$cats = createObject('phpgwapi.categories');

			$extravars = Array();
			$extravars = $this->split_extras($extravars,$extra);

			$var = array
			(
				'form_action'   => $this->action ? $this->page($extra) : $GLOBALS['phpgw']->link($sn, $extra),
				'lang_category' => lang('Category'),
				'lang_all'      => lang('All'),
				'lang_select'   => lang('Select'),
				'cat_field'     => $cat_field,
				'categories'    => $cats->formated_list('select','all',$cat_id,'True'),
				'filter_value'  => $filter,
				'qfield'        => $qfield,
				'start_value'   => $start,
				'order_value'   => $order,
				'sort_value'    => $sort,
				'query_value'   => $query,
				'table_width'   => $twidth,
				'left'          => $this->left($sn,$start,$total,$extra),
				'search'        => ($showsearch?$this->search($search_obj):''),
				'filter'        => ($filter_obj?$this->filter($filter_obj,$yours):''),
				'right'         => $this->right($sn,$start,$total,$extra)
			);
			$this->template->set_var($var);
			$this->template->parse('cats', 'nm_cats');
			$this->template->parse('cats_search_filter_data', 'nm_cats_search_filter');
			return $this->template->fp('out','nextmatchs');
		}

		/**
		* Split url string values into arrays
		*
		* @param string $extravars the string to split
		* @param string $extradata another string?
		* @return an associative array of URL argument pairs
		*/
		function split_extras($extravars, $extradata = '')
		{
			if ( is_string($extravars) && strlen($extravars) )
			{
				$extras = array();
				$extravars = explode('&', $extravars);
				foreach ( $extravars as $extravar )
				{
					$tmp = explode('=', $extravar); //0 == key, 1 == val
					if ( is_array($tmp) && count($tmp) == 2 ) //2 == key/val pair
					{
						if ( $tmp[0] == 'menuaction')
						{
							$this->action = $tmp[1];
							continue;
						}
						$extras[$tmp[0]] = $tmp[1];
					}
				}
				$extravars = $extras;
				unset($extras);
			}
			else
			{
				$extravars = array();
			}

			if ( is_string($extradata) && strlen($extradata) )
			{
				if ( !is_array($extravars) )
				{
					$extravars = array();
				}

				$extraparams = explode('&', $extradata);
				foreach ( $extraparams as $extraparam )
				{
					$tmp = explode('=', $extraparam); //0 == key, 1 == val
					if ( is_array($tmp) && count($tmp) == 2 ) //2 == key/val pair
					{
						if ( $tmp[0] == 'menuaction')
						{
							$this->action = $tmp[1];
							continue;
						}
						$extravars[$tmp[0]] = $tmp[1];
					}
				}
			}

			if ( is_array($extravars) && isset($extravars['menuaction']) )
			{
				$this->action = $extravars['menuaction'];
				unset($extravars['menuaction']);
			}
			return $extravars;
		}

		function extras_to_string($extra)
		{
			if(is_array($extra))
			{
				//while(list($var,$value) = each($extra))
				foreach($extra as $var => $value)
				{
					$t_extras[] = $var . '=' . $value;
				}
				$extra_s = '&' . implode('&',$t_extras);
			}
			return $extra_s;
		}

		/**
		 * ?
		*
		 * @param $scriptname ?
		 * @param $start ?
		 * @param $total ?
		 * @param $extradate ?
		 */
		function left($scriptname,$start,$total,$extradata = '')
		{
			list($filter,$qfield,,$order,$sort,$query) = $this->_get_var();

			$extravars = array
			(
				'order'   => $order,
				'filter'  => $filter,
				'q_field' => $qfield,
				'sort'    => $sort,
				'query'   => $query
			);

			$extravars = $this->split_extras($extravars, $extradata);
			$ret_str = '';

			if (($start != 0) &&
				($start > $this->maxmatches))
			{
				$extravars['start'] = 0;
				$ret_str .= $this->_set_link('left','first',$scriptname,lang('First page'),$extravars);
			}
			else
			{
				$ret_str .= $this->_set_icon('left','first-grey',lang('First page'));
			}

			if ($start != 0)
			{
				// Changing the sorting order screaws up the starting number
				if (($start - $this->maxmatches) < 0)
				{
					$extravars['start'] = 0;
				}
				else
				{
					$extravars['start'] = ($start - $this->maxmatches);
				}
				$ret_str .= $this->_set_link('left','left',$scriptname,lang('Previous page'),$extravars);
			}
			else
			{
				$ret_str .= $this->_set_icon('left','left-grey',lang('Previous page'));
			}
			return $ret_str;
		} /* left() */

		/**
		 * ?
		*
		 * @param $scriptname ?
		 * @param $start ?
		 * @param $total ?
		 * @param $extradate ?
		 */
		function right($scriptname,$start,$total,$extradata = '')
		{
			list($filter,$qfield,,$order,$sort,$query) = $this->_get_var();

			$extravars = Array(
				'order'   => $order,
				'filter'  => $filter,
				'q_field' => $qfield,
				'sort'    => $sort,
				'query'   => $query
			);

			$extravars = $this->split_extras($extravars,$extradata);

			$ret_str = '';

			if (($total > $this->maxmatches) &&
				($total > $start + $this->maxmatches))
			{
				$extravars['start'] = ($start + $this->maxmatches);
				$ret_str .= $this->_set_link('right','right',$scriptname,lang('Next page'),$extravars);
			}
			else
			{
				$ret_str .= $this->_set_icon('right','right-grey',lang('Next page'));
			}

			if (($start != $total - $this->maxmatches) &&
				(($total - $this->maxmatches) > ($start + $this->maxmatches)))
			{
				$extravars['start'] = ($total - $this->maxmatches);
				$ret_str .= $this->_set_link('right','last',$scriptname,lang('Last page'),$extravars);
			}
			else
			{
				$ret_str .= $this->_set_icon('right','last-grey',lang('Last page'));
			}
			return $ret_str;
		} /* right() */

		/**
		 * ?
		*
		 * @param $search_obj default 0
		 */
		function search_filter($search_obj=0,$filter_obj=1,$yours=0,$link='',$extra='')
		{
			list($filter,$qfield,$start,$order,$sort,$query) = $this->_get_var();

			//$start = $localstart;
			$var = array(
				'form_action'  => ($this->action ? $this->page($extra) : $GLOBALS['phpgw']->link($sn, $extra)),
				'filter_value' => $filter,
				'qfield'       => $qfield,
				'start_value'  => $start,
				'order_value'  => $order,
				'sort_value'   => $sort,
				'query_value'  => $query,
				'th_bg'        => $GLOBALS['phpgw_info']['theme']['th_bg'],
				'search'       => $this->search($search_obj),
				'filter'       => ($filter_obj?$this->filter($filter_obj,$yours):'')
			);
			$this->template->set_var($var);
			return $this->template->fp('out','nm_search_filter');
		}

		/**
		 * ?
		*
		 * @param $search_obj default 0
		 */
		function cats_search_filter($search_obj=0,$filter_obj=1,$yours=0,$cat_id=0,$cat_field='fcat_id',$link='',$extra='')
		{
			list($filter,$qfield,$start,$order,$sort,$query) = $this->_get_var();

			//$start = $localstart;
			$cats  = createObject('phpgwapi.categories');
			$var = array(
				'form_action'   => ($this->action?$this->page($extra):$GLOBALS['phpgw']->link($sn, $extra)),
				'lang_category' => lang('Category'),
				'lang_all'      => lang('All'),
				'lang_select'   => lang('Select'),
				'cat_field'     => $cat_field,
				'categories'    => $cats->formated_list('select','all',$cat_id,'True'),
				'filter_value'  => $filter,
				'qfield'        => $qfield,
				'start_value'   => $start,
				'order_value'   => $order,
				'sort_value'    => $sort,
				'query_value'   => $query,
				'th_bg'         => $GLOBALS['phpgw_info']['theme']['th_bg'],
				'search'        => $this->search($search_obj),
				'filter'        => ($filter_obj?$this->filter($filter_obj,$yours):'')
			);
			$this->template->set_var($var);
			return $this->template->fp('out','nm_cats_search_filter');
		}

		/**
		 * ?
		*
		 * @param $search_obj default 0
		 */
		function search($search_obj = null, $_query = null)
		{
			if(is_array($search_obj))
			{
				$params		= $search_obj;
				$_query	= stripslashes($params['query']);
				$search_obj = (isset($params['search_obj'])?$params['search_obj']:'');
			}

			$var = array
			(
				'query_value' => $_query,
				'lang_search' => lang('Search')
			);

			if (is_array($search_obj))
			{
				$var['searchby'] = $this->searchby($search_obj);
			}

			$this->template->set_var($var);
			return $this->template->fp('out','nm_search');
		} /* search() */

		/**
		 * ?
		*
		 * @param $filtertable
		 * @param $indxfieldname ?
		 * @param $strfieldname ?
		 */
		public static function filterobj($filtertable, $idxfieldname, $strfieldname)
		{
			$filter_obj = array(array('none','show all'));
			$index = 0;

			$GLOBALS['phpgw']->db->query("SELECT $idxfieldname, $strfieldname FROM $filtertable",__LINE__,__FILE__);
			while($GLOBALS['phpgw']->db->next_record())
			{
				$index++;
				$filter_obj[$index][0] = $GLOBALS['phpgw']->db->f($idxfieldname);
				$filter_obj[$index][1] = $GLOBALS['phpgw']->db->f($strfieldname);
			}

			return $filter_obj;
		} /* filterobj() */

		/**
		 * ?
		*
		 * @param $search_obj ?
		 */
		function searchby($search_obj)
		{
			$qfield = phpgw::get_var('qfield');

			$str = '';
			if (is_array($search_obj))
			{
				$indexlimit = count($search_obj);
				for ($index=0; $index<$indexlimit; $index++)
				{
					if ($qfield == '')
					{
						$qfield = $search_obj[$index][0];
					}
					$str .= '<option value="' . $search_obj[$index][0] . '"' . ($qfield == $search_obj[$index][0]?' selected':'') . '>' . lang($search_obj[$index][1]) . '</option>';
				}
				$str = '<select name="qfield">' . $str . '</select>' . "\n";
			}
			return $str;
		} /* searchby() */

		/**
		 * ?
		*
		 * @param $filter_obj
		 */
		function filter($filter_obj,$yours=0)
		{
			if (is_array($yours))
			{
				$params	= $yours;
				$filter	= $params['filter'];
				$yours	= $params['yours'];
			}
			else
			{
				$filter = $_POST['filter'] ? $_POST['filter'] : $_GET['filter'];
			}

			if (is_long($filter_obj))
			{
				if ($filter_obj == 1)
				{
					//$user_groups = $GLOBALS['phpgw']->accounts->membership($GLOBALS['phpgw_info']['user']['account_id']);
					//$indexlimit = count($user_groups);
					$indexlimit = 0;

					if ($yours)
					{
						$filter_obj = array
						(
							array('none',lang('Show all')),
							array('yours',lang('Only yours')),
							array('private',lang('private'))
						);
					}
					else
					{
						$filter_obj = array
						(
							array('none',lang('Show all')),
							array('private',lang('private'))
						);
					}

					//while (is_array($this->extra_filters) && (list(,$efilter) = each($this->extra_filters)))
					foreach($this->extra_filters as $key => $efilter)
					{
						$filter_obj[] = $efilter;
					}

					for ($index=0; $index<$indexlimit; $index++)
					{
						$filter_obj[2+$index][0] = $user_groups[$index]['account_id'];
						$filter_obj[2+$index][1] = 'Group - ' . $user_groups[$index]['account_name'];
					}
				}
			}

			if (is_array($filter_obj))
			{
				$str = '';
				$indexlimit = count($filter_obj);

				for ($index=0; $index<$indexlimit; $index++)
				{
					if ($filter == '')
					{
						$filter = $filter_obj[$index][0];
					}
					$str .= '         <option value="' . $filter_obj[$index][0] . '"'.($filter == $filter_obj[$index][0]?' selected':'') . '>' . $filter_obj[$index][1] . '</option>'."\n";
				}

				$str = '        <select name="filter" onChange="this.form.submit()">'."\n" . $str . '        </select>';
				$this->template->set_var('select',$str);
				$this->template->set_var('lang_filter',lang('Filter'));
			}

			return $this->template->fp('out','nm_filter');
		} /* filter() */

		/* replacement for function filter */
		function new_filter($data=0)
		{
			if(is_array($data))
			{
				$filter	= (isset($data['filter'])?$data['filter']:'');
				$format	= (isset($data['format'])?$data['format']:'all');
			}
			else
			{
				//$filter = phpgw::get_var('filter');
				$filter = $data;
				$format	= 'all';
			}

			switch($format)
			{
				case 'yours':
					$filter_obj = array
					(
						array('none',lang('show all')),
						array('yours',lang('only yours'))
					);
					break;
				case 'private':
					$filter_obj = array
					(
						array('none',lang('show all')),
						array('private',lang('only private'))
					);
					break;
				default:
					$filter_obj = array
					(
						array('none',lang('show all')),
						array('yours',lang('only yours')),
						array('private',lang('only private'))
					);
			}

			$str = '';
			$indexlimit = count($filter_obj);

			for($index=0; $index<$indexlimit; $index++)
			{
				if($filter == '')
				{
					$filter = $filter_obj[$index][0];
				}
				$str .= '         <option value="' . $filter_obj[$index][0] . '"'.($filter == $filter_obj[$index][0]?' selected':'') . '>' . $filter_obj[$index][1] . '</option>'."\n";
			}

			$str = '        <select name="filter" onChange="this.form.submit()">'."\n" . $str . '        </select>';
			$this->template->set_var('select',$str);
			$this->template->set_var('lang_filter',lang('Filter'));

			return $this->template->fp('out','nm_filter');
		} /* filter() */

		/**
		 * CSS based alternate row colour
		 *
		 * @param string $currentcolor the current row class
		 * @return string the new row class
		 */
		public static function alternate_row_class($classname = null)
		{
			static $value;
			if ( $classname != null )
			{
				$value = $classname;
			}

			if ( $value == 'row_off' )
			{
				$value = 'row_on';
			}
			else
			{
				$value = 'row_off';
			}
			return $value;
		}

		/**
		 * alternate row colour
		 *
		 * @deprecated
		 * @param $currentcolor default ''
		 */
		public static function alternate_row_color($currentcolor = null)
		{
			trigger_error( lang('Call to deleted method nextmatchs::alternate_row_color() use nextmatchs::alternate_row_class() instead'), E_USER_WARNING);

			return self::alternate_row_class($currentcolor);
		}

		// If you are using the common bgcolor="{tr_color}"
		// This function is a little cleanier approch
		/**
		 * ?
		*
		 * @param $tpl ?
		 */
		public static function template_alternate_row_color(&$tpl)
		{
			$tpl->set_var('tr_color', self::alternate_row_color() );
		}

		/**
		*
		*
		* @param object $tpl reference to template class
		*/
		public static function template_alternate_row_class(&$tpl, $classname = null)
		{
			$tpl->set_var('tr_class', self::alternate_row_class($classname) );
		}

		/**
		 * ?
		*
		 * @param $sort ?
		 * @param $var ?
		 * @param $order ?
		 * @param $program ?
		 * @param $text ?
		 * @param $extra default ''
		 * @param $build_an_href default True
		 */
		function show_sort_order($sort, $var = '', $order = '', $program = '', $text = '', $extra='', $build_an_href = True)
		{
			if(is_array($sort))
			{
				$temp_format	= $sort['sort'];
				$var			= (isset($sort['var'])?$sort['var']:'');
				$order			= (isset($sort['order'])?$sort['order']:'');
				$program		= (isset($sort['program'])?$sort['program']:'/index.php');
				$text			= (isset($sort['text'])?$sort['text']:'xslt');
				$extra			= (isset($sort['extra'])?$sort['extra']:'');
				$build_an_href	= (isset($sort['build_an_href'])?$sort['build_an_href']:True);
				unset($sort);
				$sort			= $temp_format;
				unset($temp_format);
			}

			list($filter,$qfield,$start,$NULL1,$NULL,$query) = $this->_get_var();

			if(($order == $var) && ($sort == 'ASC'))
			{
				$sort = 'DESC';
			}
			elseif(($order == $var) && ($sort == 'DESC'))
			{
				$sort = 'ASC';
			}
			else
			{
				$sort = 'ASC';
			}

			if(!is_array($extra))
			{
				$extra = $this->split_extras($extra);
			}

			$extra['order'] = $var;
			$extra['sort'] = $sort;
			$extra['filter'] = $filter;
			$extra['qfield'] = $qfield;
			$extra['start'] = $start;
			$extra['query'] = $query;

			$link = strlen($this->action) ? $this->page($extra) : $GLOBALS['phpgw']->link($program, $extra);

			if ($text == 'xslt')
			{
				return $link;
			}
			elseif($build_an_href)
			{
				return '<a href="' . $link . '">' . $text . '</a>';
			}
			else
			{
				return $link;
			}
		}

		function show_hits($total_records = 0,$start = 0,$num_records = 0)
		{
			if ($total_records > $this->maxmatches && $total_records != $num_records)
			{
				if ($start + $this->maxmatches > $total_records)
				{
					$end = $total_records;
				}
				else
				{
					$end = $start + $this->maxmatches;
				}
				return lang('showing %1 - %2 of %3',($start + 1),$end,$total_records);
			}
			else
			{
				return lang('showing %1',$total_records);
			}
		}

		/**
		 * Create a sortable link
		 *
		 * @param int $old_sort the current sort value
		 * @param int $new_sort the sort value you want if you click on this
		 * @param int $default_order users preference for ordering list items (force this when a new [different] sorting is requested)
		 * @param string $order the current order (will be flipped if old_sort = new_sort)
		 * @param string $program the name of the script to call
		 * @param string $text the text label for the link
		 * @param array $extra: any extra values to be append to the URL get args
		 */
		function show_sort_order_imap($old_sort, $new_sort, $default_order, $order, $program, $text, $extra = array() )
		{
			if ( !is_array($extra) )
			{
				$extra = $this->split_extras($extra);
			}
			if($old_sort == $new_sort)
			{
				// alternate order, like on outkrook, click on present sorting reverses order
				if((int)$order == 1)
				{
					$our_order = 0;
				}
				elseif((int)$order == 0)
				{
					$our_order = 1;
				}
				else
				{
					// we should never get here
					$our_order = 1;
				}
			}
			else
			{
				//user has selected a new sort scheme, reset the order to users default
				$our_order = $default_order;
			}

			$extravar['order'] = $our_order;
			$extravar['sort'] = $new_sort;

			$link = strlen($this->action) ? $this->page($extravar) : $GLOBALS['phpgw']->link($program,$extravar);
			return '<a href="' .$link .'">' .$text .'</a>';
		}

		/**
		 * same code as left and right (as of Dec 07, 2001) except all combined into one function
		 *
		 * @param feed_vars : array with these elements: <br>
		 * 	start
		 * 	total
		 * 	cmd_prefix
		 * 	cmd_suffix
		 * @return array, combination of functions left and right above, with these elements:
		 * 	first_page
		 * 	prev_page
		 * 	next_page
		 * 	last_page
		 * @internal author: jengo, some changes by Angles
		 */
		function nav_left_right_imap($feed_vars)
		{
			$return_array = Array
			(
				'first_page' => '',
				'prev_page'  => '',
				'next_page'  => '',
				'last_page'  => ''
			);

			$out_vars = array();
			// things that might change
			$out_vars['start'] = $feed_vars['start'];
			// things that stay the same
			$out_vars['common_uri'] = $feed_vars['common_uri'];
			$out_vars['total'] = $feed_vars['total'];

			// first page
			if(($feed_vars['start'] != 0) &&
				($feed_vars['start'] > $this->maxmatches))
			{
				$out_vars['start'] = 0;
				$return_array['first_page'] = $this->_set_link_imap('left','first',lang('First page'),$out_vars);
			}
			else
			{
				$return_array['first_page'] = $this->_set_icon_imap('left','first-grey',lang('First page'));
			}
			// previous page
			if($feed_vars['start'] != 0)
			{
				// Changing the sorting order screaws up the starting number
				if(($feed_vars['start'] - $this->maxmatches) < 0)
				{
					$out_vars['start'] = 0;
				}
				else
				{
					$out_vars['start'] = ($feed_vars['start'] - $this->maxmatches);
				}
				$return_array['prev_page'] = $this->_set_link_imap('left','left',lang('Previous page'),$out_vars);
			}
			else
			{
				$return_array['prev_page'] = $this->_set_icon_imap('left','left-grey',lang('Previous page'));
			}

			// re-initialize the out_vars
			// things that might change
			$out_vars['start'] = $feed_vars['start'];
			// next page
			if(($feed_vars['total'] > $this->maxmatches) &&
				($feed_vars['total'] > $feed_vars['start'] + $this->maxmatches))
			{
				$out_vars['start'] = ($feed_vars['start'] + $this->maxmatches);
				$return_array['next_page'] = $this->_set_link_imap('right','right',lang('Next page'),$out_vars);
			}
			else
			{
				$return_array['next_page'] = $this->_set_icon_imap('right','right-grey',lang('Next page'));
			}
			// last page
			if(($feed_vars['start'] != $feed_vars['total'] - $this->maxmatches) &&
				(($feed_vars['total'] - $this->maxmatches) > ($feed_vars['start'] + $this->maxmatches)))
			{
				$out_vars['start'] = ($feed_vars['total'] - $this->maxmatches);
				$return_array['last_page'] = $this->_set_link_imap('right','last',lang('Last page'),$out_vars);
			}
			else
			{
				$return_array['last_page'] = $this->_set_icon_imap('right','last-grey',lang('Last page'));
			}
			return $return_array;
		}

		/**
		 * Create a button icon
		 *
		 * @param string $align the alignment of the icon
		 * @param string $img   the url to the image
		 * @param string $label the alt text for the image
		 *
		 * @return string html icon snippet
		 *
		 * @todo FIXME use CSS instead
		 */
		protected function _set_icon($align, $img, $label)
		{
			$var = array
			(
				'align'  => $align,
				'img'    => $GLOBALS['phpgw']->common->image('phpgwapi', $img),
				'label'  => lang($label),
			);

			$this->template->set_var($var);
			return $this->template->fp('out', 'nm_link');
		}

		/**
		 * I do something with imap and icons
		 *
		 * @param ??? $align    ?
		 * @param ??? $img      ?
		 * @param ??? $alt_text ?
		 *
		 * @return string html
		 */
		protected function _set_icon_imap($align, $img, $alt_text)
		{
			$img_full = $GLOBALS['phpgw']->common->image('phpgwapi', $img);

			return "<img src=\"{$img_full}\" alt=\"{$alt_text}\">\n";
		}

		/**
		 * Create a single image button form
		 *
		 * @param string $align     the alignment of the link
		 * @param string $img       the url of the image
		 * @param string $link      the url for the link
		 * @param string $alt       the alt text for the image
		 * @param array  $extravars the extra args for the url
		 *
		 * @return string html snippet for a nextmatch button "link"
		 */
		protected function _set_link($align, $img, $link, $alt, $extravars)
		{
			$hidden = '';

			$action = '';
			if ( strlen($this->action) )
			{
				$action = $this->page($extravars);
			}
			else
			{
				$action = $GLOBALS['phpgw']->link($link, $extravars);
			}

			$var = array
			(
				'align'     => $align,
				'action'    => $action,
				'form_name' => $img,
				'hidden'    => $hidden,
				'img'       => $GLOBALS['phpgw']->common->image('phpgwapi', $img),
				'label'     => $alt,
				'start'     => $extravars['start']
			);

			$this->template->set_var($var);
			return $this->template->fp('out', 'nm_form');
		}

		/**
		 * I do something with imap and links
		 *
		 * @param ??? $align    ?
		 * @param ??? $img      ?
		 * @param ??? $alt_text ?
		 * @param ??? $out_vars ?
		 *
		 * @return string html
		 */
		protected function _set_link_imap($align, $img, $alt_text, $out_vars)
		{
			$img_full = $GLOBALS['phpgw']->common->image('phpgwapi', $img);

			$image_part = '';

			return <<<HTML
				<a href="{$out_vars['common_uri']}&amp;start={$out_vars['start']}">
					<img src="{$img_full}" alt="{$alt_text}">
				</a>

HTML;
		}
	}
