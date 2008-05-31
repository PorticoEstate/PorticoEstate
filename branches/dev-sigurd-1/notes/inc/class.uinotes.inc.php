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
	class uinotes
	{
		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;

		var $public_functions = array
		(
			'index'  => True,
			'view'   => True,
			'edit'   => True,
			'delete' => True
		);

		function uinotes()
		{
			$GLOBALS['phpgw_info']['flags'] = array
			(
				'xslt_app'	=> True,
				'noheader'	=> True,
				'nonavbar'	=> True,
				'currentapp'	=> 'notes'
			);

			$this->cats			= CreateObject('phpgwapi.categories');
			$this->nextmatchs	= CreateObject('phpgwapi.nextmatchs');
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->grants		= $GLOBALS['phpgw']->acl->get_grants('notes');
			$this->grants[$this->account] = PHPGW_ACL_READ + PHPGW_ACL_ADD + PHPGW_ACL_EDIT + PHPGW_ACL_DELETE;
			$this->bonotes		= CreateObject('notes.bonotes',True);

			$this->start		= $this->bonotes->start;
			$this->query		= $this->bonotes->query;
			$this->sort			= $this->bonotes->sort;
			$this->order		= $this->bonotes->order;
			$this->filter		= $this->bonotes->filter;
			$this->cat_id		= $this->bonotes->cat_id;
		}

		function save_sessiondata()
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

		function index()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('notes') . ': ' . lang('list notes');

			$notes_list = $this->bonotes->read();

			$link_data = array
			(
				'menuaction' => 'notes.uinotes.index'
			);
			$content = array();
			while (is_array($notes_list) && list(,$note) = each($notes_list))
			{
				$words = explode(' ', $note['content'], 4);
				$first = implode(' ', array_slice($words, 0, 3)) . ' ... ';

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
				'lang_content'		=> lang('content'),
				'lang_time_created'	=> lang('time created'),
				'lang_view'			=> lang('view'),
				'lang_edit'			=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'lang_note_id'		=> lang('note id'),
				'sort_note_id'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'note_id',
											'order'	=> $this->order,
											'extra'	=> $link_data
										)),
				'lang_owner'		=> lang('owner')
			);

			$table_add = array
			(
				'lang_add'				=> lang('add'),
				'lang_add_statustext'	=> lang('add a note'),
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
				'cat_filter'			=> $this->cats->formatted_xslt_list(array('select_name' => 'cat_id','selected' => $this->cat_id,'globals' => True,'link_data' => $link_data)),
				'filter_data'			=> $this->nextmatchs->xslt_filter(array('filter' => $this->filter,'link_data' => $link_data)),
				'search_data'			=> $this->nextmatchs->xslt_search(array('query' => $this->query,'link_data' => $link_data)),
				'table_header'			=> $table_header,
				'values'				=> $content,
				'table_add'				=> $table_add 
			);

			$this->save_sessiondata();
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
		}

		function edit()
		{
			$note_id	= phpgw::get_var('note_id', 'int');
			$values		= phpgw::get_var('values', 'string');

			if (is_array($values))
			{
				if (isset($values['save']) && $values['save'])
				{
					$values['note_id']	= $note_id;
					$note_id = $this->bonotes->save($values);
					$this->cat_id = ($values['cat_id']?$values['cat_id']:$this->cat_id);
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'notes.uinotes.index'));
				}
				elseif(isset($values['cancel']) && $values['cancel'])
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'notes.uinotes.index'));
				}
				else
				{
					$values['note_id']	= $note_id;
					$note_id			= $this->bonotes->save($values);
					$action				= 'apply';
				}
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('notes') . ': ' . ($note_id?lang('edit note'):lang('add note'));

			if ($note_id)
			{
				$note = $this->bonotes->read_single($note_id);
				$this->cat_id = ($note['cat_id']?$note['cat_id']:$this->cat_id);
			}

			$link_data = array
			(
				'menuaction'	=> 'notes.uinotes.edit',
				'note_id'		=> $note_id
			);

			$data = array
			(
				'msgbox_data'					=> ((isset($action) && $action=='apply')?$GLOBALS['phpgw']->common->msgbox('note has been saved',True):''),
				'edit_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_content'					=> lang('content'),
				'lang_category'					=> lang('category'),
				'lang_access'					=> lang('private'),
				'lang_save'						=> lang('save'),
				'lang_cancel'					=> lang('cancel'),
				'lang_apply'					=> lang('apply'),
				'value_content'					=> isset($note['content']) ? $note['content'] : '',
				'value_access'					=> isset($note['access']) ? $note['access'] : '',
				'lang_content_statustext'		=> lang('Enter the content of the note'),
				'lang_apply_statustext'			=> lang('Apply the values'),
				'lang_cancel_statustext'		=> lang('Leave the note untouched and return back to the list'),
				'lang_save_statustext'			=> lang('Save the note and return back to the list'),
				'lang_access_off_statustext'	=> lang('The note is public. If the note should be private, check this box'),
				'lang_access_on_statustext'		=> lang('The note is private. If the note should be public, uncheck this box'),
				'lang_no_cat'					=> lang('no category'),
				'cat_select'					=> $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => isset($note['cat_id']) ? $note['cat_id'] : ''))
			);
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		}

		function delete()
		{
			$note_id	= phpgw::get_var('note_id', 'int');
			$delete		= phpgw::get_var('delete', 'bool');
			$cancel		= phpgw::get_var('cancel', 'bool');

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

		function view()
		{
			$note_id	= phpgw::get_var('note_id', 'int');
			$action		= phpgw::get_var('action', 'string');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('notes') . ': ' . lang('view note');

			$note = $this->bonotes->read_single($note_id);

			$data = array
			(
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'notes.uinotes.index')),
				'lang_content'		=> lang('content'),
				'lang_category'		=> lang('category'),
				'lang_access'		=> lang('access'),
				'lang_time_created'	=> lang('time created'),
				'lang_done'			=> lang('done'),
				'value_content'		=> $note['content'],
				'value_access'		=> lang(ucfirst($note['access'])),
				'value_cat'			=> $this->cats->id2name($note['cat_id']),
				'value_date'		=> $GLOBALS['phpgw']->common->show_date($note['date'])
			);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		}
	}
