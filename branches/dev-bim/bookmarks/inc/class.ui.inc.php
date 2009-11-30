<?php
	/**
	* phpGroupWare - Bookmarks
	* http://www.phpgroupware.org
	* @author Joseph Engo
	* @author Michael Totschnig
	* @copyright Copyright (C) 2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package bookmarks
	* @version $Id$
	* @internal Based on Bookmarker, Copyright (C) 1998 Padraic Renaghan, http://www.renaghan.com/bookmarker
	* @internal Ported to phpGroupWare by Joseph Engo
	* @internal Ported to three-layered design by Michael Totschnig
	*/

/**
 * Define TREE
 */
define('TREE',1);

/**
 * Define _LIST
 */
define('_LIST',2);

/**
 * Define CREATE
 */
define('CREATE',3);

/**
 * Define SEARCH
 */
define('SEARCH',4);

	/**
	 * Bookmarks GUI class
	 * 
	 * @package bookmarks
	 */
	class bookmarks_ui
	{
		var $t;
		var $bo;
		var $img;
		var $expandedcats;
		var $nextmatchs;
		var $page_header_shown = false;
		var $massupdate_shown = false;

		var $public_functions = array
		(
			'edit' => True,
			'create' => True,
			'_list' => True,
			'search' => True,
			'tree' => True,
			'view' => True,
			'mail' => True,
			'mass' => True,
			'redirect' => True,
			'export' => True,
			'import' => True
		);

		function bookmarks_ui()
		{
			$this->t =& $GLOBALS['phpgw']->template;
			$this->bo = createobject('bookmarks.bo');
			$this->img = array(
				'collapse' => $GLOBALS['phpgw']->common->image('bookmarks','tree_collapse'),
				'expand' => $GLOBALS['phpgw']->common->image('bookmarks','tree_expand'),
				'edit' => $GLOBALS['phpgw']->common->image('bookmarks','edit'),
				'view' => $GLOBALS['phpgw']->common->image('bookmarks','document'),
				'mail' => $GLOBALS['phpgw']->common->image('bookmarks','mail'),
				'delete' => $GLOBALS['phpgw']->common->image('bookmarks','delete')
			);
			$this->expandedcats = array();
			$this->location_info = $this->bo->read_session_data();
			$this->nextmatchs = createobject('phpgwapi.nextmatchs');

		}

		function init()
		{
			// we maintain two levels of state:
			// returnto the main interface (tree, list, or search)
			// returnto2 temporaray interface (create, edit, view, mail)
			if ( isset($this->location_info['returnto2']) )
			{
				$returnto2 = $this->location_info['returnto2'];
				$this->$returnto2();
			}
			else if ( isset($this->location_info['returnto']) )
			{
				$returnto = $this->location_info['returnto'];
				$this->$returnto();
			}
			elseif ($GLOBALS['phpgw_info']['user']['preferences']['bookmarks']['defaultview'] == 'tree')
			{
				$this->tree();
			}
			else
			{
				$this->_list();
			}
		}

		function app_header($where=0)
		{ 
			$tabs[1]['label'] = lang('Tree view');
			$tabs[1]['link']  = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookmarks.ui.tree') );

			$tabs[2]['label'] = lang('List');
			$tabs[2]['link']  = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookmarks.ui._list') );

			if (! $GLOBALS['phpgw']->acl->check('anonymous',1,'bookmarks'))
			{
				$tabs[3]['label'] = lang('New');
				$tabs[3]['link']  = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookmarks.ui.create') );
			}

			$tabs[4]['label'] = lang('Search');
			$tabs[4]['link']  = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookmarks.ui.search') );

			$this->t->set_var('app_navbar',$GLOBALS['phpgw']->common->create_tabs($tabs,$where));
		}

		function app_messages()
		{
			$bk_output_html = '';
			if ($this->bo->error_msg)
			{
				$bk_output_html = '<center style="color:red">' . lang('Error') . ': ' . $this->bo->error_msg . '</center>';
			}
			if ($this->bo->msg)
			{
				$bk_output_html .= '<center>' . $this->bo->msg . '</center>';
			}

			if ($bk_output_html)
			{
				$this->t->set_var('messages',$bk_output_html);
			}
		}

		function app_template()
		{
			$this->t->set_var(array(
				'lang_url' => lang('URL'),
				'lang_name' => lang('Name'),
				'lang_desc' => lang('Description'),
				'lang_keywords' => lang('Keywords'),
				'lang_access' => lang('Private'),
				'lang_category' => lang('Category'),
				'lang_rating' => lang('Rating'),
				'lang_owner' => lang('Created by'),
				'lang_added' => lang('Date added'),
				'lang_updated' => lang('Date last updated'),
				'lang_visited' => lang('Date last visited'),
				'lang_visits' => lang('Total visits'),
				'cancel_button' => ('<input type="image" name="cancel" title="' . lang('Done') . '" src="'
					. $GLOBALS['phpgw']->common->image('bookmarks','cancel') . '" border="0">'
				),
				'save_button' => ('<input type="image" name="save" title="' . lang('Save') . '" src="'
					. $GLOBALS['phpgw']->common->image('bookmarks','save') . '" border="0">'
				),
				'category_image' => ('<input type="image" name="edit_category" title="' . lang('Edit category') . '" src="'
					. $GLOBALS['phpgw']->common->image('bookmarks','edit') . '" border="0">'
				),
			));
		}

		function create()
		{
			//if we redirect to edit categories, we remember form values and try to come back to create
			if ( (isset($_POST['edit_category_x']) && $_POST['edit_category_x'])
				|| ( isset($_POST['edit_category_y']) && $_POST['edit_category_y']) )
			{
				$this->bo->grab_form_values($this->location_info['returnto'],'create',$_POST['bookmark']);
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'preferences.uicategories.index', 'cats_app' => 'bookmarks', 'global_cats' => True) );
				exit;
			}
			//save bookmark
			if ( (isset($_POST['save_x']) && $_POST['save_x'])
				|| (isset($_POST['save_y']) && $_POST['save_y']) )
			{
				$bookmark = $_POST['bookmark'];
				$bm_id = $this->bo->add($bookmark);
				if ($bm_id)
				{
					$this->location_info['bm_id'] = $bm_id;
					$this->view();
					return;
				}
			}
			//if we come back from editing categories we restore form values
			else if ( isset($this->location_info['returnto2']) 
				&& $this->location_info['returnto2'] == 'create')
			{
				$bookmark['name']        = $this->location_info['bookmark']['name'];
				$bookmark['url']         = $this->location_info['bookmark']['url'];
				$bookmark['desc']        = $this->location_info['bookmark']['desc'];
				$bookmark['keywords']    = $this->location_info['bookmark']['keywords'];
				$bookmark['category']    = $this->location_info['bookmark']['category'];
				$bookmark['rating']      = $this->location_info['bookmark']['rating'];
				$bookmark['access']      = $this->location_info['bookmark']['access'];
			}
			else
			{
				$bookmark = array
				(
					'name'		=> '',
					'url'		=> 'http://',
					'desc'		=> '',
					'keywords'	=> '',
					'category'	=> 0,
					'rating'	=> 0,
					'access'	=> ''
				);
			}
			//if the user cancelled we go back to the view we came from
			if ( (isset($_POST['cancel_x']) && $_POST['cancel_x'])
				|| ( isset($_POST['cancel_y']) && $_POST['cancel_y']) )
			{
				unset($this->location_info['returnto2']);
				$this->init();
				return;
			}
			//store the view, we came from originally(list,tree,search), and the view we are in
			$this->location_info['bookmark'] = False;
			$this->location_info['returnto2'] = 'create';
			$this->bo->save_session_data($this->location_info);
			
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$this->app_header(CREATE);

			$this->t->set_file(array(
				'common_'            => 'common.tpl',
				'form'               => 'form.tpl'
			));
			$this->t->set_block('form','body');
			$this->t->set_block('form','form_info');

			$selected = array('', '', '', '', '', '', '', '', '', '', '');
			$selected[$bookmark['rating']] = ' selected';
			$this->app_template();
			$this->t->set_var(array(
				'form_info' => '',
				'lang_header' => lang('Create new bookmark'),
				'input_category' => $this->bo->categories_list($bookmark['category']),
				'input_rating' => ('<select name="bookmark[rating]">'
					. ' <option value="0"' . $selected[0] . '>--</option>'
					. ' <option value="1"' . $selected[1] . '>1 - ' . lang('Lowest') . '</option>'
					. ' <option value="2"' . $selected[2] . '>2</option>'
					. ' <option value="3"' . $selected[3] . '>3</option>'
					. ' <option value="4"' . $selected[4] . '>4</option>'
					. ' <option value="5"' . $selected[5] . '>5</option>'
					. ' <option value="6"' . $selected[6] . '>6</option>'
					. ' <option value="7"' . $selected[7] . '>7</option>'
					. ' <option value="8"' . $selected[8] . '>8</option>'
					. ' <option value="9"' . $selected[9] . '>9</option>'
					. ' <option value="10"' . $selected[10] . '>10 - ' . lang('Highest') . '</option>'
					. '</select>'
				),
				'input_url' => ('<input name="bookmark[url]" size="60" maxlength="255" value="' . 
					($bookmark['url']?$bookmark['url']:'http://') . '">'
				),
				'input_name' => ('<input name="bookmark[name]" size="60" maxlength="255" value="' . 
					$bookmark['name'] . '">'
				),
				'input_desc' => ('<textarea name="bookmark[desc]" rows="3" cols="60" wrap="virtual">' . 
					$bookmark['desc'] . '</textarea>'
				),
				'input_keywords' => ('<input type="text" name="bookmark[keywords]" size="60" maxlength="255" value="' . 
					$bookmark['keywords'] . '">'
				),
				'input_access' => ('<input type="checkbox" name="bookmark[access]" value="private"' . 
					(isset($bookmark['access']) &&  $bookmark['access'] ?' checked' : '') . '>'
				),
			));

			$this->t->fp('body','form');
			$this->app_messages();
			$this->t->pfp('out','common_');
		}

		function edit()
		{
			if (isset($_GET['bm_id']))
			{
				$bm_id = $_GET['bm_id'];
			}
			elseif (is_array($this->location_info))
			{
				$bm_id = $this->location_info['bm_id'];
			}
			//if the user cancelled we go back to the view we came from
			if ($_POST['cancel_x'] || $_POST['cancel_y'])
			{
				unset($this->location_info['returnto2']);
				$this->init();
				return;
			}
			//delete bookmark and go back to view we came from
			if ($_POST['delete_x'] || $_POST['delete_y'])
			{
				$this->bo->delete($bm_id);
				unset($this->location_info['returnto2']);
				$this->init();
				return;
			}
			//if we redirect to edit categories, we remember form values and try to come back to edit
			if ($_POST['edit_category_x'] || $_POST['edit_category_y'])
			{
				$this->bo->grab_form_values($this->location_info['returnto'],'edit',$_POST['bookmark']);
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'preferences.uicategories.index', 'cats_app' => 'bookmarks', 'global_cats' => 'True'));
				exit;
			}
			//save bookmark and go to view interface
			if ($_POST['save_x'] || $_POST['save_y'])
			{
				$bookmark = $_POST['bookmark'];
				if ($this->bo->update($bm_id,$bookmark))
				{
					$this->location_info['bm_id'] = $bm_id;
					$this->view();
					return;
				}
			}
			$bookmark = $this->bo->read($bm_id);

			if (!$bookmark[PHPGW_ACL_EDIT])
			{
				$this->bo->error_msg = lang('Bookmark not editable');
				unset($this->location_info['returnto2']);
				$this->init();
				return;
			}

			//if we come back from editing categories we restore form values
			if ($this->location_info['bookmark'])
			{
				$bookmark['name']     = $location_info['bookmark_name'];
				$bookmark['url']      = $location_info['bookmark_url'];
				$bookmark['desc']     = $location_info['bookmark_desc'];
				$bookmark['keywords'] = $location_info['bookmark_keywords'];
				$bookmark['category'] = $location_info['bookmark_category'];
				$bookmark['rating']   = $location_info['bookmark_rating'];
			}

			//store the view we are in
			$this->location_info['bookmark'] = False;
			$this->location_info['returnto2'] = 'edit';
			$this->location_info['bm_id'] = $bm_id;
			$this->bo->save_session_data($this->location_info);

			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$this->app_header();

			$this->t->set_file(array(
				'common_'            => 'common.tpl',
				'form'               => 'form.tpl'
			));
			$this->t->set_block('form','body');
			$this->t->set_block('form','form_info');

			$this->bo->date_information($this->t,$bookmark['info']);

			$rs[$bookmark['rating']] = ' selected';
			$rating_select = '<select name="bookmark[rating]">'
				. ' <option value="0">--</option>'
				. ' <option value="1"' . $rs[1] . '>1 - ' . lang('Lowest') . '</option>'
				. ' <option value="2"' . $rs[2] . '>2</option>'
				. ' <option value="3"' . $rs[3] . '>3</option>'
				. ' <option value="4"' . $rs[4] . '>4</option>'
				. ' <option value="5"' . $rs[5] . '>5</option>'
				. ' <option value="6"' . $rs[6] . '>6</option>'
				. ' <option value="7"' . $rs[7] . '>7</option>'
				. ' <option value="8"' . $rs[8] . '>8</option>'
				. ' <option value="9"' . $rs[9] . '>9</option>'
				. ' <option value="10"' . $rs[10] . '>10 - ' . lang('Highest') . '</option>'
				. '</select>';

			$account = createobject('phpgwapi.accounts',$bookmark['owner']);
			$ad      = $account->read_repository();

			$this->app_template();
			$this->t->set_var(array(
				'lang_header' => lang('Edit bookmark'),
				'total_visits' => $bookmark['visits'],
				'owner_value' => $GLOBALS['phpgw']->common->display_fullname($ad['account_lid'],$ad['firstname'],$ad['lastname'])
			));
			$this->t->parse('info','form_info');
			$this->t->set_var(array(
				'form_info' => '',
				'form_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookmarks.ui.edit', 'bm_id' => (int)$bm_id) ),
				'lang_access' => lang('Private'),
				'input_access' => ('<input type="checkbox" name="bookmark[access]" value="private"' . 
					($bookmark['access']=='private'?' checked':'') . '>'
				),
				'input_rating' => $rating_select,
				'input_category' => $this->bo->categories_list($bookmark['category']),
				'input_url' => ('<input name="bookmark[url]" size="60" maxlength="255" value="' . 
					$bookmark['url'] . '">'
				),
				'input_name' => ('<input name="bookmark[name]" size="60" maxlength="255" value="' . 
					$bookmark['name'] . '">'
				),
				'input_desc' => ('<textarea name="bookmark[desc]" rows="3" cols="60" wrap="virtual">' . 
					$bookmark['desc'] . '</textarea>'
				),
				'input_keywords' => ('<input type="text" name="bookmark[keywords]" size="60" maxlength="255" value="' . 
					$bookmark['keywords'] . '">'
				),
				'delete_button' => ($this->bo->check_perms($bm_id,PHPGW_ACL_DELETE,$bookmark['owner']) ?
					('<input type="image" name="delete" title="' . lang('Delete') . '" src="'
						. $GLOBALS['phpgw']->common->image('bookmarks','delete') . '" border="0">'
					) :
					''
				),
			));

			$this->t->fp('body','form');
			$this->app_messages();
			$this->t->pfp('out','common_');
		}

		function _list()
		{
			if (is_array($this->location_info))
			{
				$start = isset($this->location_info['start']) ? $this->location_info['start'] : 0;
				$bm_cat = isset($this->location_info['bm_cat']) ? $this->location_info['bm_cat'] : 0;
			}
			if (isset($_GET['bm_cat']))
			{
				$bm_cat = $_GET['bm_cat'];
			}
			if (isset($_GET['start']))
			{
				$start = $_GET['start'];
			}
			if (isset($_POST['start']))
			{
				$start = $_POST['start'];
			}
			$this->location_info['start'] = $start;
			$this->location_info['bm_cat'] = $bm_cat;
			$this->location_info['returnto'] = '_list';
			unset($this->location_info['returnto2']);
			$this->bo->save_session_data($this->location_info);

			$GLOBALS['phpgw']->common->phpgw_header(true);
			$this->app_header(_LIST);

			$this->t->set_file(array(
				'common_' => 'common.tpl',
				'listbody'    => 'list.body.tpl'
			));

			$this->t->set_var(array(
				'lang_url' => lang('URL'),
				'lang_name' => lang('Name')
			));

			// We need to send the $start var instead of the page number
			// Use appsession() to remeber the return page,instead of always passing it 
			$where_clause = '';
			$this->print_list($where_clause,$start,$bm_cat,$bookmark_list);

			$this->t->set_var('BOOKMARK_LIST', $bookmark_list);

			$total_bookmarks = $this->bo->so->total_records;
			if ($total_bookmarks > $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'])
			{
				$next = $start + $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
				$total_matchs = lang('showing %1 - %2 of %3',($start + 1),
					($next <= $total_bookmarks) ? $next : $total_bookmarks,$total_bookmarks);
			}
			else
			{
				$total_matchs = lang('showing %1',$total_bookmarks);
			}
			if ($bm_cat)
			{
				$total_matchs .= ' ' . 
					lang('from category %1',$GLOBALS['phpgw']->strip_html($this->bo->categories->id2name($bm_cat))) .
					' - <a href="' . 
					$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookmarks.ui._list', 'bm_cat' => 0, 'start' => 0) ) .
					'">' .
					lang('All bookmarks') .
					'</a>';
			}
			$link_data = array
			(
				'menuaction' => 'bookmarks.ui._list',
				'bm_cat' => $bm_cat
			);

			$this->t->set_var(array(
				'next_matchs_left' =>  $this->nextmatchs->left('/index.php',$start,$total_bookmarks,$link_data),
				'next_matchs_right' => $this->nextmatchs->right('/index.php',$start,$total_bookmarks,$link_data),
				'showing' => $total_matchs
			));

			$this->t->fp('body','listbody');
			$this->app_messages();
			$this->t->pfp('out','common_');
		}

		function search()
		{
			global $y, $x;
			if (is_array($this->location_info))
			{
				$start = $this->location_info['searchstart'];
				$x = $this->location_info['x'];
			}
			if (isset($_POST['x']))
			{
				$x = $_POST['x'];
				$this->location_info['x'] = $x;
			}
			if (isset($_POST['start']))
			{
				$start = $_POST['start'];
				$this->location_info['searchstart'] = $start;
			}
			$this->location_info['returnto'] = 'search';
			
			$this->bo->save_session_data($this->location_info);

			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$this->app_header(SEARCH);
			
			$q = createobject('bookmarks.sqlquery');

			$this->t->set_file(array(
				'common_'  => 'common.tpl',
				'searchbody'    => 'search.body.tpl',
				'results' => 'search.results.tpl'
			));

			// the following fields are selectable
			$field = array(
				'phpgw_bookmarks.bm_name'        => lang('Name'),
				'phpgw_bookmarks.bm_keywords'    => lang('Keywords'),
				'phpgw_bookmarks.bm_url'         => lang('URL'),
				'phpgw_bookmarks.bm_desc'        => lang('Description')
			//		'phpgw_bookmarks.bm_category'    => 'Category',
			//		'phpgw_bookmarks.bm_subcategory' => 'Sub Category',
			);

			// PHPLIB's sqlquery class loads this string when
			// no query has been specified.
			$noquery = "1=0";

			# build the where clause based on user entered fields
			if (isset($x))
			{
				#
				# we need to pre-process the input fields so we can
				# handle quotes properly. we can't put an addslashes
				# on the resulting sql because the sql_query object
				# doesn't do the quotes correctly
				reset($x);
				while (list($key, $value) = each ($x))
				{
					$y[$key] = addslashes($value);
				}
				$q->query = $q->where("y", 1);
			}

			$this->t->set_var(array(
				'SEARCH_SELECT' => $search_select,
				'FORM_ACTION'   => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookmarks.ui.search') )
			));

			# build the search form
			$this->t->set_var(QUERY_FORM, $q->form("x", $field, "qry", $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookmarks.ui.search') ) ) );

			if ($q->query == $noquery)
			{
			}
			else
			{
				$this->print_list($q->query, $start,0,$bookmark_list);

			$total_bookmarks = $this->bo->so->total_records;
			if ($total_bookmarks > $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'])
			{
				$next = $start + $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
				$total_matchs = lang('showing %1 - %2 of %3',($start + 1),
					($next <= $total_bookmarks) ? $next : $total_bookmarks,$total_bookmarks);
			}
			else
			{
				$total_matchs = lang('showing %1',$total_bookmarks);
			}
			$link_data = array
			(
				'menuaction' => 'bookmarks.ui.search',
			);

			$this->t->set_var(array(
				'next_matchs_left' =>  $this->nextmatchs->left('/index.php',$start,$total_bookmarks,$link_data),
				'next_matchs_right' => $this->nextmatchs->right('/index.php',$start,$total_bookmarks,$link_data),
				'showing' => $total_matchs
			));

				$this->t->set_var(array(
					'QUERY_CONDITION' => $GLOBALS['phpgw']->strip_html($q->query),
					'LANG_QUERY_CONDITION' => lang('Query Condition'),
					'BOOKMARK_LIST'   => $bookmark_list,
				));
				$this->t->parse('QUERY_RESULTS', "results");
			}

			$this->t->fp('body','searchbody');
			$this->app_messages();
			$this->t->pfp('out','common_');
		}

		function print_list_break ($category_id)
		{
			$category = $GLOBALS['phpgw']->strip_html($this->bo->categories->id2name($category_id));

			// We only want to display the massupdate section once
			if (! $this->massupdate_shown)
			{
				$this->t->set_var(array(
					'lang_massupdate' => lang('Mass update:'),
					'massupdate_delete_icon' => sprintf('<input type="image" name="delete" border="0" src="%s">',$this->img['delete']),
					'massupdate_mail_icon' => sprintf('<input type="image" name="mail" border="0" src="%s">',$this->img['mail'])
				));
				$this->massupdate_shown = true;
			}
			else
			{
				$this->t->set_var(array(
					'lang_massupdate' => '',
					'massupdate_delete_icon' => '',
					'massupdate_mail_icon' =>''
				));
			}

			$this->t->set_var('CATEGORY',$GLOBALS['phpgw']->strip_html($category));

			$this->t->fp('LIST_HDR','list_header');
			$this->t->fp('LIST_FTR','list_footer');        
			$this->t->fp('CONTENT','list_section',TRUE);
			$this->t->set_var('LIST_ITEMS','');
		}

	function print_list($where_clause, $start, $bm_cat, &$content)
	{
		$this->t->set_file(array(
			'list' => 'list.tpl'
		));
		$this->t->set_block('list','list_section');
		$this->t->set_block('list','list_header');
		$this->t->set_block('list','list_footer');
		$this->t->set_block('list','list_item');
		$this->t->set_block('list','list_keyw');
		$this->t->set_block('list','page_header');
		$this->t->set_block('list','page_footer');

		$this->t->set_var('list_mass_select_form',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookmarks.ui.mass') ) );

		if (! $this->page_header_shown)
		{
			$this->t->fp('header','page_header');
			$this->page_header_shown = True;
		}
		else
		{
			$this->t->set_var('header','');
		}

		$bm_list = $this->bo->_list($bm_cat,$start,$where_clause);

		$prev_category_id = -1;
		$rows_printed = 0;

		while (list($bm_id,$bookmark) = @each($bm_list))
		{
			$rows_printed++;

			if ($bookmark['category'] != $prev_category_id)
			{
				if ($rows_printed > 1)
				{
					$this->print_list_break($prev_category_id);
				}
				$prev_category_id       = $bookmark['category'];
			}

			if ($bookmark['keywords'])
			{
				$this->t->set_var('BOOKMARK_KEYW', $bookmark['keywords']);
				$this->t->parse('bookmark_keywords','list_keyw');
			}
			else
			{
				$this->t->set_var('bookmark_keywords','');
			}

			// Check owner
			if ($this->bo->check_perms2($bookmark['owner'],$bookmark['access'],PHPGW_ACL_EDIT))
			{
				$maintain_url  = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookmarks.ui.edit', 'bm_id' => $bm_id) );
				$maintain_link = sprintf(
					'<a href="%s"><img src="%s" align="top" border="0" alt="%s"></a>', 
					$maintain_url,
					$this->img['edit'],
					lang('Edit this bookmark')
				);
			}
			else
			{
				$maintain_link = '';
			}

			$view_url      = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookmarks.ui.view', 'bm_id' => (int)$bm_id) );
			$view_link     = sprintf(
				'<a href="%s"><img src="%s" align="top" border="0" alt="%s"></a>', 
				$view_url,
				$this->img['view'],
				lang('View this bookmark')
			);

			$mail_link = sprintf(
				'<a href="%s"><img align="top" border="0" src="%s" alt="%s"></a>',
				$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookmarks.ui.mail', 'bm_id' => (int)$bm_id) ),
				$this->img['mail'],
				lang('Mail this bookmark')
				);

			$this->t->set_var(array(
				'maintain_link' => $maintain_link,
				'bookmark_url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookmarks.ui.redirect', 'bm_id' => (int)$bm_id) ),
				'view_link' => $view_link,
				'mail_link' => $mail_link,
				'checkbox' => '<input type="checkbox" name="item_cb[]" value="' . $bm_id . '">',
				'bookmark_name' => $bookmark['name'],
				'bookmark_desc' => nl2br($bookmark['desc']),
				'bookmark_rating' => sprintf('<img src="%s/bar-%s.jpg">',PHPGW_IMAGES,$bookmark['rating'])
			));
			$this->t->parse('LIST_ITEMS', 'list_item', true);
		}

		if ($rows_printed > 0)
		{
			$this->print_list_break($prev_category_id);
			$content = $this->t->get('CONTENT');
			$this->t->fp('footer','page_footer');
		}
	}

		function tree()
		{
			$this->location_info['returnto'] = 'tree';
			unset($this->location_info['returnto2']);
			$this->bo->save_session_data($this->location_info);

			if ( isset($_COOKIE['menutree']) && $_COOKIE['menutree'] )
			{
				$this->expandedcats = array_keys($_COOKIE['menutree']);
			}
			else
			{
				$this->expandedcats = Array();
			}

			$GLOBALS['phpgw']->common->phpgw_header(true);
			$this->app_header(TREE);

			$this->t->set_file(array(
				'common_' => 'common.tpl',
			));

			$categories = $this->bo->categories->return_array('mains',0,False,'','cat_name','',True);

			$tree = "<script type='text/javascript'>
// the whole thing only works in a DOM capable browser or IE 4*/

function add(catid)
{
	document.cookie = 'menutree[' + catid + ']=';
}

function remove(catid)
{
	var now = new Date();
	document.cookie = 'menutree[' + catid + ']=; expires=' + now.toGMTString();
}

function toggle(image, catid)
{
	if (document.getElementById)
	{ //DOM capable
		styleObj = document.getElementById(catid);
	}
	else //we're helpless
	{
	return 
	}

	if (styleObj.style.display == 'none')
	{
		add(catid);
		image.src = '" . $this->img['collapse'] . "';
		styleObj.style.display = 'block';
	}
	else
	{
		remove(catid);
		image.src = '" . $this->img['expand'] . "';
		styleObj.style.display = 'none';
	}
}
</script>" . 
				'<table border="0" cellspacing="0" cellpadding="0" width="100%">' .
				$this->showcat($categories) .
				'</table>' .
				"\n";

			$this->t->set_var('body',$tree);
			$this->app_messages($this->t);
			$this->t->pfp('out','common_');
		}

		function showcat($cats)
		{
			$tree = '';
			while(list(,$cat) = @each($cats))
			{
				$cat_id = $cat['id'];
				$status = in_array($cat_id,$this->expandedcats);
				$tree .= "\n" . 
					'<tr><td width="10%">' . 
					'<img src="' .
					$this->img[$status ? "collapse" : "expand"] .
					'" onclick="toggle(this, \'' . 
					$cat_id . 
					'\')"></td><td><a style="font-weight:bold" title="' .
					$GLOBALS['phpgw']->strip_html($cat['description']) .
					'" href="' .
					$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookmarks.ui._list', 'start' => 0, 'bm_cat' => (int)$cat_id) ) .
					'">' .
					$GLOBALS['phpgw']->strip_html($cat['name']) . 
					'</a></td></tr>' . 
					"\n";
				$subcats = $this->bo->categories->return_array('subs',0,False,'','','',True,$cat_id);
		 		$bookmarks = $this->bo->_list($cat_id,False,False,False);
				if ($subcats || $bookmarks)
				{
					$tree .= '<tr><td></td><td><table style="display:' .
						($status ? "block" : "none") .
						'" border="0" cellspacing="0" cellpadding="0" width="100%" id="'.
						$cat_id .
						'">';

					while(list($bm_id,$bookmark) = @each($bookmarks))
					{
						$tree .= '<tr><td colspan="2">';
						if ($this->bo->check_perms2($bookmark['owner'],$bookmark['access'],PHPGW_ACL_EDIT))
						{
							$maintain_url  = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookmarks.ui.edit', 'bm_id' => (int)$bm_id) );
							$maintain_link = sprintf(
								'<a href="%s"><img src="%s" align="top" border="0" alt="%s"></a>', 
								$maintain_url,
								$this->img['edit'],
								lang('Edit this bookmark')
							);
						}
						else
						{
							$maintain_link = '';
						}

						$view_url      = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookmarks.ui.view', 'bm_id' => (int)$bm_id) );
						$view_link     = sprintf(
							'<a href="%s"><img src="%s" align="top" border="0" alt="%s"></a>', 
							$view_url,
							$this->img['view'],
							lang('View this bookmark')
						);

						$redirect_link = '<a href="' . 
							$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookmarks.ui.redirect', 'bm_id' => (int)$bm_id) ) .
							'" target="_new">' . $bookmark['name'] . '</a>';

						$tree .= $maintain_link . $view_link . $redirect_link . 
							'</td></tr>';
					}

					if ($subcats)
					{
						$tree .= $this->showcat($subcats);
					}

					$tree .= '</table></td></tr>';
				}
			}
			return $tree;
		}

		function view()
		{
			if (isset($_GET['bm_id']))
			{
				$bm_id = $_GET['bm_id'];
			}
			elseif (is_array($this->location_info))
			{
				$bm_id = $this->location_info['bm_id'];
			}
			//if the user cancelled we go back to the view we came from
			if ( (isset($_POST['cancel_x']) && $_POST['cancel_x'])
				|| ( isset($_POST['cancel_y']) && $_POST['cancel_y']) )
			{
				unset($this->location_info['returnto2']);
				$this->init();
				return;
			}
			//delete bookmark and go back to view we came from
			if ( (isset($_POST['delete_x']) && $_POST['delete_x'])
				|| (isset($_POST['delete_y']) && $_POST['delete_y']) )
			{
				$this->bo->delete($bm_id);
				unset($this->location_info['returnto2']);
				$this->init();
				return;
			}
			if ( (isset($_POST['edit_x']) && $_POST['edit_x'])
				|| ( isset($_POST['edit_y']) && $_POST['edit_y']) )
			{
				$this->edit();
				return;
			}

			$bookmark = $this->bo->read($bm_id);

			if (!$bookmark[PHPGW_ACL_READ])
			{
				$this->bo->error_msg = lang('Bookmark not readable');
				unset($this->location_info['returnto2']);
				$this->init();
				return;
			}

			//store the view we are in
			$this->location_info['returnto2'] = 'view';
			$this->location_info['bm_id'] = $bm_id;
			$this->bo->save_session_data($this->location_info);

			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$this->app_header();

			$this->t->set_file(array(
				'common_' => 'common.tpl',
				'form'     => 'form.tpl',
			));

			$this->t->set_block('form','body');
			$this->t->set_block('form','form_info');

			$this->bo->date_information($this->t,$bookmark['info']);
			$this->app_template();

			$account = createobject('phpgwapi.accounts',$bookmark['owner']);
			$ad      = $account->read_repository();
			$category  = $GLOBALS['phpgw']->strip_html($this->bo->categories->id2name($bookmark['category']));

			$this->t->set_var(array(
				'total_visits' => $bookmark['visits'],
				'owner_value' => $GLOBALS['phpgw']->common->display_fullname($ad['account_lid'],$ad['firstname'],$ad['lastname'])
			));
			$this->t->parse('info','form_info');
			$this->t->set_var(array(
				'form_info' => '',
				'form_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookmarks.ui.view', 'bm_id' => $bm_id) ),
				'lang_access' => lang('Access'),
				'input_access' => lang($bookmark['access']),
				'lang_header' => lang('View bookmark'),
				'input_url' => ('<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookmarks.ui.redirect', 'bm_id' => $bm_id) ) .
					'" target="_new">' . $bookmark['url'] . '</a>'
				),
				'input_name' => $bookmark['name'],
				'input_desc' => $bookmark['desc'],
				'input_keywords' => $bookmark['keywords'],
				'input_rating' => ('<img src="' . $GLOBALS['phpgw']->common->get_image_path('bookmarks') . 
					'/bar-' . $bookmark['rating'] . '.jpg">'
				),
				'input_category' => $category,
				'edit_button' => ($this->bo->check_perms($bm_id,PHPGW_ACL_EDIT) ?
					('<input type="image" name="edit" title="' . lang('Edit') . '" src="'
						. $GLOBALS['phpgw']->common->image('bookmarks','edit') . '" border="0">'
					) :
				''
				),
				'delete_button' => ($this->bo->check_perms($bm_id,PHPGW_ACL_DELETE) ?
					('<input type="image" name="delete" title="' . lang('Delete') . '" src="'
						. $GLOBALS['phpgw']->common->image('bookmarks','delete') . '" border="0">'
					) :
					''
				)
			));
			$this->t->fp('body','form');
			$this->app_messages($this->t);
			$this->t->pfp('out','common_');
		}

		function mail()
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$this->app_header();

			$this->t->set_file(array(
				'common_' => 'common.tpl',
				'mail'    => 'maillink.body.tpl'
			));

			if ($_POST['send'])	// Send button clicked
			{
				$validate = createobject('phpgwapi.validator');
				// Strip space and tab from anywhere in the To field
				$to = $validate->strip_space($_POST['to']);

				// Trim the subject
				$subject = $GLOBALS['phpgw']->strip_html(trim($_POST['subject']));

				$message = $GLOBALS['phpgw']->strip_html($_POST['message']);

				// Do we have all necessary data?
				if (empty($to) || empty($subject) || empty($message))
				{
					$this->bo->error_msg .= '<br>'.lang('Please fill out <B>To E-Mail Address</B>, <B>Subject</B>, and <B>Message</B>!');
				}
				else
				{
					// the To field may contain one or more email addresses
					// separated by commas. Check each one for proper format.
					$to_array = explode(",", $to);

					while (list($key, $val) = each($to_array))
					{
						// Is email address in the proper format?
						if (!$validate->is_email($val))
						{
							$this->bo->error_msg .= '<br>' .
								lang('To address %1 invalid. Format must be <strong>user@domain</strong> and domain must exist!',$val).
								'<br><small>'.$validate->ERROR.'</small>';
							break;
						}
					}
				}
				if (!isset ($this->bo->error_msg))
				{
					$send     = createobject('phpgwapi.send');
					// add additional headers to our email
					$addl_headers = sprintf("%s: %s <%s>",lang('From'),stripslashes($from_name), $from);

					//$addl_headers = sprintf('%s\n%s',$addl_headers,$GLOBALS['phpgw']->template->parse('_footer','footer'));
					$reply_to = $GLOBALS['phpgw_info']['user']['fullname'] .
						' <'.$GLOBALS['phpgw_info']['user']['preferences']['email']['address'].'>';

					if (empty($replay_to))
					{
						$reply_to = 'No reply <noreply@' . $GLOBALS['phpgw_info']['server']['mail_suffix'].'>';
					}
					// send the message
					$send->msg('email',$to,$subject,$message ."\n". $this->bo->config['mail_footer'],'','','',$reply_to);
					$this->bo->msg .= '<br>'.lang('mail-this-link message sent to %1.',$to);
				}
			}

			if (empty($subject))
			{
				$subject = lang('Found a link you might like');
			}

			if (empty($message))
			{
				if (is_array($_POST['item_cb']))
				{
					while (list(,$id) = each($_POST['item_cb']))
					{
						$bookmark = $this->bo->read($id);
						$links[] = array(
							'name' => $bookmark['name'],
							'url'  => $bookmark['url']
					);
					}
				}
				else
				{
					$bookmark = $this->bo->read($_GET['bm_id']);
					$links[] = array(
						'name' => $bookmark['name'],
						'url'  => $bookmark['url']
					);
				}
				$message = lang('I thought you would be interested in the following link(s):')."\n";
				while (list(,$link) = @each($links))
				{
					$message .= sprintf("%s - %s\n",$link['name'],$link['url']);
				}
			}

			$this->t->set_var(array(
				'th_bg' => $GLOBALS['phpgw_info']['theme']['th_bg'],
				'header_message' => lang('Send bookmark'),
				'lang_from' => lang('Message from'),
				'lang_to' => lang('To E-Mail Addresses'),
				'lang_multiple_addr' => lang('(comma separate multiple addresses)'),
				'lang_subject' => lang('Subject'),
				'lang_message' => lang('Message'),
				'lang_send' => lang('Send'),
				'from_name' => $GLOBALS['phpgw']->common->display_fullname(),
				'form_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookmarks.ui.mail')),
				'to' => $to,
				'subject' => $subject,
				'message' => $message
			));
			$this->t->fp('body','mail');
			$this->app_messages();
			$this->t->pfp('out','common_');
		}

		function mass()
		{
			$item_cb = $_POST['item_cb'];
			if ($_POST['delete_x'] || $_POST['delete_y'])
			{
				if (is_array($item_cb))
				{
					$i = 0;
					while (list(,$id) = each($item_cb))
					{
						if ($this->bo->delete($id))
						{
							$i++;
						}
					}
					$this->bo->msg = lang('%1 bookmarks have been deleted',$i);
				}

				$this->_list();
			}
			elseif ($_POST['mail_x'] || $_POST['mail_y'])
			{
				$this->mail();
			}
		}

		function redirect()
		{
			$bm_id = $_GET['bm_id'];
			$bookmark = $this->bo->read($bm_id);
			$ts = explode(",",$bookmark['info']);
			$newtimestamp = sprintf("%s,%s,%s",$ts[0],time(),$ts[2]);
			$this->bo->updatetimestamp($bm_id,$newtimestamp);
			$GLOBALS['phpgw']->redirect($bookmark['url']);
		}

		function export()
		{
			if ($_POST['export'])
			{
				#  header("Content-type: text/plain");
				header("Content-type: application/octet-stream");

				if ($_POST['exporttype'] == 'Netscape/Mozilla')
				{
					header("Content-Disposition: attachment; filename=bookmarks.html");
					echo $this->bo->export($_POST['bookmark']['category'],'ns');
				}
				else
				{
					header("Content-Disposition: attachment; filename=bookmarks.xbel");
					echo $this->bo->export($_POST['bookmark']['category'],'xbel');
				}
			}
			else
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();
				$this->t->set_file('body','export.body.tpl');
				$this->t->set_var(Array(
					'FORM_ACTION' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookmarks.ui.export') ),
					'input_categories' => $this->bo->categories_list(0,True)
				));
				$this->t->pfp('out','body');
				$GLOBALS['phpgw']->common->phpgw_footer();
			}
		}

		function import()
		{
			if ($_POST['import'])
			{
				$this->bo->import($_FILES['bkfile'],$_POST['bookmark']['category']);
			}

			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$this->t->set_file('body','import.body.tpl');
			$this->t->set_var(Array(
				'FORM_ACTION' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookmarks.ui.import') ),
				'lang_name' => lang('Enter the name of the Netscape bookmark file<br>that you want imported into bookmarker below.'),
				'lang_file' => lang('Netscape Bookmark File'),
				'lang_import_button' => lang('Import Bookmarks'),
				'lang_note' => lang('<b>Note:</b> This currently works with netscape bookmarks only'),
				'lang_catchoose' => lang('To which category should the imported folder hierarchy be attached'),
				'input_categories' => $this->bo->categories_list(0),
			));
			$this->app_messages();
			$this->t->pfp('out','body');
			$GLOBALS['phpgw']->common->phpgw_footer();
		}
	}
