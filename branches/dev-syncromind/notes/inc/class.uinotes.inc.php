<?php
	/**
	* Notes
	* @author Andy Holman
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2003,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package notes
	* @version $Id$
	*/

	/*
		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation; either version 3 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program.  If not, see <http://www.gnu.org/licenses/>.
	*/

	/**
	* Notes GUI class
	*
	* @package notes
	*/
	class notes_uinotes
	{
		protected $grants;
		protected $cat_id;
		protected $start;
		protected $query;
		protected $sort;
		protected $order;
		protected $filter;

		public $public_functions = array
		(
			'index'  => true,
			'view'   => true,
			'edit'   => true,
			'delete' => true
		);

		/**
		 * Constructor
		 *
		 * @return void
		 */
		public function __construct()
		{
			$GLOBALS['phpgw_info']['flags'] = array
			(
				'xslt_app'	=> true,
				'noheader'	=> true,
				'nonavbar'	=> true,
				'currentapp'	=> 'notes'
			);

			$this->cats			= CreateObject('phpgwapi.categories');
			$this->nextmatchs	= CreateObject('phpgwapi.nextmatchs');
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

			$GLOBALS['phpgw']->acl->set_account_id($this->account);
			$this->grants		= $GLOBALS['phpgw']->acl->get_grants('notes');
			$this->grants[$this->account] = PHPGW_ACL_READ + PHPGW_ACL_ADD + PHPGW_ACL_EDIT + PHPGW_ACL_DELETE;
			$this->bonotes		= CreateObject('notes.bonotes',true);

			$this->start		= $this->bonotes->start;
			$this->query		= $this->bonotes->query;
			$this->sort			= $this->bonotes->sort;
			$this->order		= $this->bonotes->order;
			$this->filter		= $this->bonotes->filter;
			$this->cat_id		= $this->bonotes->cat_id;
		}

		protected function save_sessiondata()
		{
			$data = array
			(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'filter'	=> $this->filter,
				'cat_id'	=> $this->cat_id
			);
			$this->bonotes->save_sessiondata($data);
		}

		public function index()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('notes') . ': ' . lang('list notes');

			$notes_list = $this->bonotes->read();

			$link_data = array
			(
				'menuaction' => 'notes.uinotes.index'
			);
			$content = array();
			foreach ( $notes_list as $note )
			{
				$words = explode(' ', phpgw::strip_html($note['content']), 10);
				$first = implode(' ', $words);
				if ( count($words) > 10 )
				{
					$first .= ' ...';
				}
				unset($words);

				$content[] = array
				(
					'note_id'					=> $note['note_id'],
					'first'						=> $first,
					'date'						=> $note['date'],
					'owner'						=> $note['owner'],
					'link_view'					=> $GLOBALS['phpgw']->link(
						'/index.php', array(
							'menuaction' => 'notes.uinotes.view',
							'note_id'    => $note['note_id'])),
					'link_edit'					=> $GLOBALS['phpgw']->link(
						'/index.php', array(
							'menuaction' => 'notes.uinotes.edit',
							'note_id'    => $note['note_id'])),
					'link_delete'				=> $GLOBALS['phpgw']->link(
						'/index.php', array(
							'menuaction' => 'notes.uinotes.delete',
							'note_id'    => $note['note_id'])),
					'lang_view_statustext'		=> lang('view the note'),
					'lang_edit_statustext'		=> lang('edit the note'),
					'lang_delete_statustext'	=> lang('delete the note'),
					'text_view'					=> lang('view'),
					'text_edit'					=> lang('edit'),
					'text_delete'				=> lang('delete')
				);
			}

			$table_header = array
			(
				'sort_time_created'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'note_date',
											'order'	=> $this->order,
											'extra'	=> $link_data
										)),
				'sort_note_id'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'note_id',
											'order'	=> $this->order,
											'extra'	=> $link_data
										))
			);

			$table_add = array
			(
				'add_action'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'notes.uinotes.edit'))
			);

			$nm = array
			(
				'start'			=> $this->start,
				'start_record'	=> $this->start,
 				'num_records'	=> count($notes_list),
 				'all_records'	=> $this->bonotes->total_records,
				'link_data'		=> $link_data
			);

			$data = array
			(
				'nm_data'				=> $this->nextmatchs->xslt_nm($nm),
				'cat_filter'			=> $this->cats->formatted_xslt_list(array('select_name' => 'cat_id','selected' => $this->cat_id,'globals' => true,'link_data' => $link_data)),
				'filter_data'			=> $this->nextmatchs->xslt_filter(array('filter' => $this->filter,'link_data' => $link_data)),
				'search_data'			=> $this->nextmatchs->xslt_search(array('query' => $this->query,'link_data' => $link_data)),
				'table_header'			=> $table_header,
				'values'				=> $content,
				'table_add'				=> $table_add 
			);

			$this->save_sessiondata();
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
		}

		public function edit()
		{
			$note_id	= phpgw::get_var('note_id', 'int');
			$values		= phpgw::get_var('values', 'string', 'POST');
			$save		= phpgw::get_var('save', 'bool', 'POST');
			$cancel		= phpgw::get_var('cancel', 'bool', 'POST');
			$apply		= phpgw::get_var('apply', 'bool', 'POST');
			$action		= '';

			if ( $save || $cancel || $apply )
			{
				if ( $cancel )
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',
							array('menuaction' => 'notes.uinotes.index'));
				}

				$values['note_id'] = $note_id;
				$values['content'] = phpgw::get_var('note_content', 'html', 'POST');
				if ( $values['cat_id'] )
				{
					$this->cat_id = (int) $values['cat_id'];
				}
				$note_id = $this->bonotes->save($values);

				if ( $save )
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',
							array('menuaction' => 'notes.uinotes.index'));
				}

				// Must be a reply request
				$action	= 'apply';
				$GLOBALS['phpgw']->redirect_link('/index.php', 
							array('menuaction' => 'notes.uinotes.edit', 'note_id' => $note_id));
			}

			if ( $note_id )
			{
				$lang_title = lang('edit note');
				$note = $this->bonotes->read_single($note_id);
				$this->cat_id = ($note['cat_id']?$note['cat_id']:$this->cat_id);
			}
			else
			{
				$lang_title = lang('add note');
				$note = array
				(
					'content'	=> '',
					'access'	=> '',
					'cat_id'	=> $this->cat_id
				);
			}

			$note['content'] = phpgw::clean_html($note['content']);

			$edit_url = $GLOBALS['phpgw']->link('/index.php', array
			(
				'menuaction'	=> 'notes.uinotes.edit',
				'note_id'		=> $note_id
			));

			$msg_box = '';
			if ( $action == 'apply' )
			{
				$msg_box = $GLOBALS['phpgw']->common->msgbox('note has been saved', true);
			}

			$cats = $this->cats->formatted_xslt_list(array
					(
						'select_name'	=> 'values[cat_id]',
						'selected'		=> $note['cat_id']
					));
 
			$data = array
			(
				'msgbox_data'					=> $msg_box,
				'edit_url'						=> $edit_url,
				'lang_content'					=> lang('content'),
				'lang_category'					=> lang('category'),
				'lang_access'					=> lang('private'),
				'lang_save'						=> lang('save'),
				'lang_cancel'					=> lang('cancel'),
				'lang_apply'					=> lang('apply'),
				'value_content'					=> $note['content'],
				'value_access'					=> $note['access'],
				'lang_no_cat'					=> lang('no category'),
				'cat_select'					=> $cats
			);

			$GLOBALS['phpgw']->richtext->replace_element('note_content');
			$GLOBALS['phpgw']->richtext->generate_script();

			$GLOBALS['phpgw']->xslttpl->add_file('msgbox');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('notes') . ': ' . $lang_title;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		}

		public function delete()
		{
			$note_id	= phpgw::get_var('note_id', 'int');
			$delete		= phpgw::get_var('delete', 'bool', 'POST');
			$cancel		= phpgw::get_var('cancel', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => 'notes.uinotes.index'
			);

			if ($delete)
			{
				$this->bonotes->delete($note_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if ($cancel)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file('delete');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('notes') . ': ' . lang('delete note');

			$data = array
			(
				'delete_url'				=> $GLOBALS['phpgw']->link(
					'/index.php', array(
						'menuaction' => 'notes.uinotes.delete',
						'note_id'    => $note_id)),
				'lang_confirm_msg'			=> lang('do you really want to delete this note ?'),
				'lang_delete'				=> lang('delete'),
				'lang_delete_statustext'	=> lang('Delete the note'),
				'lang_cancel_statustext'	=> lang('Leave the note untouched and return back to the list'),
				'lang_cancel'				=> lang('cancel')
			);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}

		public function view()
		{
			$note_id	= phpgw::get_var('note_id', 'int', 'GET');
			$action		= phpgw::get_var('action', 'string', 'GET');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('notes') . ': ' . lang('view note');

			$note = $this->bonotes->read_single($note_id);
			$note['content'] = phpgw::clean_html($note['content']);

			$cat = lang('unfiled');
			if ( $note['cat_id'] )
			{
				$cat = lang('filed under: %1', $this->cats->id2name($note['cat_id']));
			}

			$date = $GLOBALS['phpgw']->common->show_date($note['date']);
			$done = $GLOBALS['phpgw']->link('/index.php', 
						array('menuaction' => 'notes.uinotes.index'));

			$data = array
			(
				'done_action'		=> $done,
				'lang_category'		=> $cat,
				'lang_created'		=> lang('created at %1', $date),
				'lang_done'			=> lang('done'),
				'value_content'		=> $note['content']
			);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		}
	}
