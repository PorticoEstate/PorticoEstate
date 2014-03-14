<?php

	/**
	 * phpGroupWare - property: a part of a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
	 * This file is part of phpGroupWare.
	 *
	 * phpGroupWare is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * phpGroupWare is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with phpGroupWare; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	 *
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/
	 * @package property
	 * @subpackage logistic
	 * @version $Id: class.uidocuments.inc.php 11515 2013-12-08 21:25:19Z sigurdne $
	 */
	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('phpgwapi.jquery');

	class manual_uidocuments extends phpgwapi_uicommon
	{

		private $bo;
		private $receipt = array();
		public $public_functions = array
		(
			'query'						=> true,
			'index'						=> true,
			'view'						=> true,
			'add'						=> true,
			'edit'						=> true,
			'save'						=> true,
			'get_files'					=> true,
			'view_file'					=> true,
		);

		public function __construct()
		{
			parent::__construct();

			$this->bo 					= CreateObject('property.bocondition_survey');
			$this->bocommon				= & $this->bo->bocommon;
			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= '.documents';
			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'manual');
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'manual');
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'manual');
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'manual');
			$this->acl_manage 			= $this->acl->check($this->acl_location, 16, 'manual');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "manual";
		}


		public function download()
		{
			if(!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}

			$values = $this->query();

			$descr = array();
			$columns = array();
			$columns[]	= 'id';
			$columns[]	= 'title';
			$columns[]	= 'descr';
			$columns[]	= 'address';
			$columns[]	= 'cnt';

			foreach($columns as $_column)
			{
				$descr[] = lang(str_replace('_', ' ', $_column));
			}

			$this->bocommon->download($values,$columns,$descr);

		}


		public function index()
		{
			if(!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			self::add_javascript('phpgwapi', 'yahoo', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');

			$categories = $this->_get_categories();


			$data = array(
				'datatable_name'	=> lang('condition survey'),
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'filter',
								'name' => 'cat_id',
								'text' => lang('category') . ':',
								'list' => $categories,
							),
							array('type' => 'text',
								'text' => lang('search'),
								'name' => 'query'
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search')
							),
							array(
								'type' => 'link',
								'value' => lang('new'),
								'href' => self::link(array('menuaction' => 'property.uicondition_survey.add')),
								'class' => 'new_item'
							),
							array(
								'type' => 'link',
								'value' => lang('download'),
								'href' => 'javascript:window.open("'. self::link(array('menuaction' => 'property.uicondition_survey.download', 'export' => true, 'allrows' => true)) . '","window")',
								'class' => 'new_item'
							),
							array(
								'type' => 'link',
								'value' => $_SESSION['allrows'] ? lang('Show only active') : lang('Show all'),
								'href' => self::link(array('menuaction' => 'property.uicondition_survey.index', 'allrows' => true))
							),

						),
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'property.uicondition_survey.index', 'phpgw_return_as' => 'json')),
					'editor_action' => 'property.uicondition_survey.edit_survey_title',
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'sortable' => true,
							'formatter' => 'YAHOO.portico.formatLink'
						),
		/*				array(
							'key' => 'title',
							'label' => lang('title'),
							'sortable' => true,
							'editor' => 'new YAHOO.widget.TextboxCellEditor({disableBtns:false})'
						),
						array(
							'key' => 'descr',
							'label' => lang('description'),
							'sortable' => false,
						),*/
						array(
							'key' => 'address',
							'label' => lang('buildingname'),
							'sortable' => true
						),
						array(
							'key' => 'vendor',
							'label' => lang('vendor'),
							'sortable' => true
						),
						array(
							'key' => 'year',
							'label' => lang('year'),
							'sortable' => true,
						),
						array(
							'key' => 'multiplier',
							'label' => lang('multiplier'),
							'sortable' => false,
						),
						array(
							'key' => 'cnt',
							'label' => lang('count'),
							'sortable' => false,
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				),
			);

			$parameters = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'id',
							'source'	=> 'id'
						),
					)
				);

			$data['datatable']['actions'][] = array
					(
						'my_name'		=> 'view_survey',
						'text' 			=> lang('view'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uicondition_survey.view'
						)),
						'parameters'	=> json_encode($parameters)
					);

			$data['datatable']['actions'][] = array
					(
						'my_name'		=> 'edit_survey',
						'text' 			=> lang('edit'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uicondition_survey.edit'
						)),
						'parameters'	=> json_encode($parameters)
					);

			$data['datatable']['actions'][] = array
					(
						'my_name'		=> 'import_survey',
						'text' 			=> lang('import'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uicondition_survey.import'
						)),
						'parameters'	=> json_encode($parameters)
					);


			if($GLOBALS['phpgw']->acl->check('.admin', PHPGW_ACL_DELETE, 'property'))
			{
				$data['datatable']['actions'][] = array
					(
						'my_name'		=> 'delete_imported_records',
						'text' 			=> lang('delete imported records'),
						'confirm_msg'	=> lang('do you really want to delete this entry') . '?',
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uicondition_survey.delete_imported_records'
						)),
						'parameters'	=> json_encode($parameters)
					);
			}

			if($GLOBALS['phpgw']->acl->check('.admin', PHPGW_ACL_DELETE, 'property'))
			{
				$data['datatable']['actions'][] = array
					(
						'my_name'		=> 'delete_survey',
						'text' 			=> lang('delete'),
						'confirm_msg'	=> lang('do you really want to delete this entry') . '?',
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uicondition_survey.delete'
						)),
						'parameters'	=> json_encode($parameters)
					);
			}

			self::render_template_xsl('datatable_common', $data);
		}


		public function query()
		{
			$params = array(
				'start' => phpgw::get_var('startIndex', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('results', 'int', 'REQUEST', 0),
				'query' => phpgw::get_var('query'),
				'sort' => phpgw::get_var('sort'),
				'dir' => phpgw::get_var('dir'),
				'cat_id' => phpgw::get_var('cat_id', 'int', 'REQUEST', 0),
				'allrows' => phpgw::get_var('allrows', 'bool')
			);

			$result_objects = array();
			$result_count = 0;

			$values = $this->bo->read($params);
			if ( phpgw::get_var('export', 'bool'))
			{
				return $values;
			}

			$result_data = array('results' => $values);

			$result_data['total_records'] = $this->bo->total_records;
			$result_data['start'] = $params['start'];
			$result_data['sort'] = $params['sort'];
			$result_data['dir'] = $params['dir'];

			array_walk(	$result_data['results'], array($this, '_add_links'), "property.uicondition_survey.view" );

			return $this->yui_results($result_data);
		}


		public function view()
		{
			if(!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}
			$this->edit(null, $mode = 'view');
		}

		public function add()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "manual::add";
			$this->edit();
		}

		/**
		* Prepare data for view and edit - depending on mode
		*
		* @param int    $cat_id  type of documents
		* @param string $mode    edit or view
		*
		* @return void
		*/

		public function edit($cat_id = 0, $mode = 'edit')
		{
			if(!$cat_id)
			{
				$cat_id = phpgw::get_var('cat_id', 'int');
			}

			if(!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'manual.uidocuments.view', 'cat_id'=> $cat_id));
			}

			if($mode == 'view')
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] = "manual::view";
				if( !$this->acl_read)
				{
					$this->bocommon->no_access();
					return;
				}
			}
			else
			{
				if(!$this->acl_add && !$this->acl_edit)
				{
					$this->bocommon->no_access();
					return;
				}
			}

			$categories = $this->_get_categories($cat_id);


			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$file_def = array
			(
				array('key' => 'file_name','label'=>lang('Filename'),'sortable'=>false,'resizeable'=>true),
			);
			if($mode == 'edit')
			{
				$file_def[1]= array('key' => 'delete_file','label'=>lang('Delete file'),'sortable'=>false,'resizeable'=>true);
			}

			$datatable_def = array();
			$datatable_def[] = array
			(
				'container'		=> 'datatable-container_0',
				'requestUrl'	=> json_encode(self::link(array('menuaction' => 'manual.uidocuments.get_files', 'cat_id' => $cat_id,'phpgw_return_as'=>'json'))),
				'ColumnDefs'	=> $file_def,

			);

			$data = array
			(
				'datatable_def'					=> $datatable_def,
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'categories'					=> array('options' => $categories),
				'editable' 						=> $mode == 'edit',
				'multiple_uploader'				=> $mode == 'edit' ? true : '',
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('manual') . '::' . lang('documents');

			if($mode == 'edit')
			{
				phpgwapi_jquery::load_widget('core');
				self::add_javascript('phpgwapi', 'yui3', 'yui/yui-min.js');
				self::add_javascript('phpgwapi', 'yui3', 'gallery-formvalidator/gallery-formvalidator-min.js');
				$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yui3/gallery-formvalidator/validatorCss.css');
				self::add_javascript('phpgwapi', 'tinybox2', 'packed.js');	
				$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/tinybox2/style.css');
				self::add_javascript('manual', 'portico', 'documents.add.js');
			}

			self::add_javascript('manual', 'portico', 'documents.view.js');

//			$GLOBALS['phpgw_info']['server']['no_jscombine'] = true;

			self::render_template_xsl(array('documents_add'), $data);
		}


		/**
		* Saves an entry to the database for new/edit - redirects to view
		*
		* @param int  $id  entity id - no id means 'new'
		*
		* @return void
		*/

		public function save()
		{
			$cat_id = phpgw::get_var('cat_id', 'int');

			if( !$cat_id )
			{
				$this->edit();
			}
			else
			{
				try
				{
					$this->_handle_files($cat_id);
				}

				catch(Exception $e)
				{
					if ( $e )
					{
						phpgwapi_cache::message_set($e->getMessage(), 'error'); 
						$this->edit( $values );
						return;
					}
				}

				phpgwapi_cache::message_set('ok!', 'message'); 
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'manual.uidocuments.edit', 'cat_id' => $cat_id));
			}
		}

		/**
		* Fetch a list of files to be displayed in view/edit
		*
		* @param int  $id  entity id
		*
		* @return array $ResultSet json resultset
		*/

		public function get_files()
		{
			$cat_id 	= phpgw::get_var('cat_id', 'int', 'REQUEST');

			if( !$this->acl_read)
			{
				return;
			}

			$cat_filter = array();
			if($cat_id)
			{
				$cats	= CreateObject('phpgwapi.categories', -1, 'manual', $this->acl_location);
				$cats->supress_info	= true;
				$cat_list_files	= $cats->return_sorted_array(0, false, '', '', '', false, $cat_id, false);
				$cat_filter[] = $cat_id;
				foreach ($cat_list_files as $_category)
				{
					$cat_filter[] = $_category['id'];
				}
			}

			$link_file_data = array
			(
				'menuaction'	=> 'manual.uidocuments.view_file'
			);

			$files = array();

			$link_view_file = self::link($link_file_data);

			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			foreach ($cat_filter as $_cat_id)
			{
				$_files = $vfs->ls(array(
					'string' => "/manual/{$_cat_id}",
					'relatives' => array(RELATIVE_NONE)));
					
				foreach ($_files as &$_file)
				{
					$_file['path'] = "{$_cat_id}/{$_file['name']}";
				}
					
				$files = array_merge($files,$_files);
			}

			$vfs->override_acl = 0;


//------ Start pagination

			$start = phpgw::get_var('startIndex', 'int', 'REQUEST', 0);
			$total_records = count($files);

			$num_rows = isset($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] ? (int) $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] : 15;

			if($allrows)
			{
				$out = $files;
			}
			else
			{
			//	$page = ceil( ( $start / $total_records ) * ($total_records/ $num_rows) );
				$page = ceil( ( $start / $num_rows) );
				$files_part = array_chunk($files, $num_rows);
				$out = $files_part[$page];
			}

//------ End pagination


			$lang_view = lang('click to view file');
			$lang_delete = lang('click to delete file');

			$values = array();
			foreach($out as $_entry )
			{
				$values[] = array
				(
					'file_name' => "<a href='{$link_view_file}&amp;file_name={$_entry['path']}' target='_blank' title='{$lang_view}'>{$_entry['name']}</a>",
					'delete_file' => "<input type='checkbox' name='file_action[]' value='{$_entry['name']}' title='$lang_delete'>",
				);
			}

			$data = array(
				 'ResultSet' => array(
					'totalResultsAvailable' => $total_records,
					'startIndex' => $start,
					'sortKey' => 'file_name', 
					'sortDir' => "ASC", 
					'Result' => $values,
					'pageSize' => $num_rows,
					'activePage' => floor($start / $num_rows) + 1
				)
			);
			return $data;

		}



		/**
		* Dowloads a single file to the browser
		*
		* @param int  $id  entity id
		*
		* @return file
		*/

		function view_file()
		{
			if(!$this->acl_read)
			{
				return lang('no access');
			}

			$bofiles	= CreateObject('property.bofiles', '/manual');

			$file_name	= html_entity_decode(urldecode(phpgw::get_var('file_name')));
			$file		= "{$bofiles->fakebase}/{$file_name}";
			$bofiles->view_file('',$file);
		}


		/**
		* Store and / or delete files related to an entity
		*
		* @param int  $cat_id  entity id
		*
		* @return void
		*/
		private function _handle_files($cat_id)
		{
			$cat_id = (int)$cat_id;
			if(!$cat_id)
			{
				throw new Exception('uidocuments::_handle_files() - missing cat_id');
			}
			$bofiles	= CreateObject('property.bofiles', '/manual');

			if(isset($_POST['file_action']) && is_array($_POST['file_action']))
			{
				$bofiles->delete_file("/{$cat_id}/", array('file_action' => $_POST['file_action']));
			}
			$file_name=str_replace(' ','_',$_FILES['file']['name']);

			if($file_name)
			{
				if(!is_file($_FILES['file']['tmp_name']))
				{
					phpgwapi_cache::message_set(lang('Failed to upload file !'), 'error');
					return;
				}

				$to_file = "{$bofiles->fakebase}/{$cat_id}/{$file_name}";
				if($bofiles->vfs->file_exists(array(
					'string' => $to_file,
					'relatives' => Array(RELATIVE_NONE)
				)))
				{
					phpgwapi_cache::message_set(lang('This file already exists !'), 'error'); 
				}
				else
				{
		//			$bofiles->create_document_dir($cat_id);
					$bofiles->vfs->override_acl = 1;

					if(!$bofiles->vfs->cp (array (
						'from'	=> $_FILES['file']['tmp_name'],
						'to'	=> $to_file,
						'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))))
					{
						phpgwapi_cache::message_set(lang('Failed to upload file !'), 'error'); 
					}
					$bofiles->vfs->override_acl = 0;
				}
			}
		}


		private function _get_categories($selected = 0)
		{
			$cats	= CreateObject('phpgwapi.categories', -1, 'manual', $this->acl_location);
			$cats->supress_info	= true;
			$categories = $cats->formatted_xslt_list(array('format'=>'filter','selected' => $selected,'globals' => true,'use_acl' => $this->_category_acl));
			$default_value = array ('cat_id'=>'','name'=> lang('no category'));
			array_unshift ($categories['cat_list'],$default_value);

			foreach ($categories['cat_list'] as & $_category)
			{
				$_category['id'] = $_category['cat_id'];
			}

			return $categories['cat_list'];
		}
	}
