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
	 * @version $Id: class.uigeneric_document.inc.php 14913 2016-04-11 12:27:37Z sigurdne $
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('phpgwapi.jquery');

	//include_class('property', 'import_component_files', 'inc/import/');

	class property_uiimport_documents extends phpgwapi_uicommon_jquery
	{

		private $receipt		 = array();
		protected $path_upload_dir;
		public $public_functions = array(
			'query'							 => true,
			'index'							 => true,
			'get_locations_for_type'		 => true,
			'import_component_files'		 => true,
			'handle_import_files'			 => true,
			'import_components'				 => true,
			'get_attributes_from_template'	 => true,
			'get_profile'					 => true,
			'download'						 => true,
			'get_files'						 => true,
			'handle_import_files'			 => true
		);

		public function __construct()
		{
			parent::__construct();

			$this->bocommon			 = CreateObject('property.bocommon');
			$this->bo				 = CreateObject('property.boadmin_entity', true);
			$this->acl				 = & $GLOBALS['phpgw']->acl;
			$this->db				 = & $GLOBALS['phpgw']->db;

			$GLOBALS['phpgw_info']['flags']['menu_selection']	 = 'admin::property::import_documents';
			$config = CreateObject('phpgwapi.config', 'property')->read();

			if (!empty($config['temp_files_components']))
			{
				$temp_files_components	 = trim($config['temp_files_components'], '/');
				$this->path_upload_dir	 = "/$temp_files_components";
			}
			else
			{
				$fakebase			 = '/temp_files_components';
				$this->path_upload_dir	 = $GLOBALS['phpgw_info']['server']['files_dir'] . $fakebase;
			}

		}

		public function download()
		{
		}

		private function _msg_data( $receipt )
		{
			if (isset($receipt['error']) && is_array($receipt['error']))
			{
				foreach ($receipt['error'] as $dummy => $error)
				{
					$this->receipt['error'][] = $error;
				}
			}

			if (isset($receipt['message']) && is_array($receipt['message']))
			{
				foreach ($receipt['message'] as $dummy => $message)
				{
					$this->receipt['message'][] = $message;
				}
			}

			return $this->receipt;
		}

		private function _getexcelcolumnname( $index )
		{
		}

		public function import_component_files()
		{

		}

		private function _getArrayItem( $id, $name, $selected, $options = array(), $no_lang = false, $attribs = '' )
		{
		}


		private function _get_components_cached_file()
		{
			$cached_file = phpgwapi_cache::session_get('property', 'components_import_file');

			if ($_FILES['file']['tmp_name'])
			{
				if ($cached_file)
				{
					phpgwapi_cache::session_clear('property', 'components_import_file');
					unlink($cached_file);
					unset($cached_file);
				}

				$file		 = $_FILES['file']['tmp_name'];
				$cached_file = "{$file}_temporary_import_file";

				file_put_contents($cached_file, file_get_contents($file));
				phpgwapi_cache::session_set('property', 'components_import_file', $cached_file);
			}

			return $cached_file;
		}

		private function _build_sheets()
		{
		}

		private function _build_start_line()
		{
		}

		private function _get_default_options()
		{
		}

		private function _build_columns()
		{

		}

		private function _prepare_profile()
		{

		}

		private function _prepare_values_to_preview()
		{
		
		}

		private function _save_values_import()
		{
			return $this->receipt;
		}

		public function import_components()
		{

			return $result;
		}

		/**
		 * Prepare UI
		 * @return void
		 */
		public function index()
		{
			$tabs				 = array();
			$tabs['locations']	 = array('label' => lang('Locations'), 'link' => '#locations');
			$tabs['components']	 = array('label' => lang('Components'), 'link'		 => '#components',
				'disable'	 => 1);
			$tabs['files']		 = array('label' => lang('Files'), 'link' => '#files', 'disable' => 0);
			$tabs['relations']	 = array('label' => lang('Relations'), 'link'		 => '#relations',
				'disable'	 => 1);

			$active_tab = 'locations';


			$files_def = array
				(
				array('key'	 => 'file',
					'label'	 => lang('file'),
					'sortable'	 => true,
					'resizeable' => true
					),
				array('key' => 'doument_type',
					'label' => lang('doument type'),
					'sortable' => true,
					'resizeable' => true
					),
				array('key' => 'branch',
					'label' => lang('branch'),
					'sortable' => true,
					'resizeable' => true
					),
				array('key' => 'building_part',
					'label' => lang('building part'),
					'sortable' => true,
					'resizeable' => true
					),
			);


			$datatable_def = array();
			$requestUrl	 = json_encode(self::link(array(
				'menuaction' => 'property.uitts.update_file_data',
				'location_id' => $GLOBALS['phpgw']->locations->get_id('property', '.ticket'),
				'location_item_id' => $id,
				'phpgw_return_as'	 => 'json')
				));
			$requestUrl = str_replace('&amp;', '&', $requestUrl);

			$buttons = array
			(
				array(
					'action' => 'set_tag',
					'type'	 => 'buttons',
					'name'	 => 'set_tag',
					'label'	 => lang('set tag'),
					'funct'	 => 'onActionsClick_files',
					'classname'	=> '',
					'value_hidden'	 => ""
					),
				array(
					'action' => 'remove_tag',
					'type'	 => 'buttons',
					'name'	 => 'remove_tag',
					'label'	 => lang('remove tag'),
					'funct'	 => 'onActionsClick_files',
					'classname'	=> '',
					'value_hidden'	 => ""
					),
				array(
					'action' => 'delete_file',
					'type'	 => 'buttons',
					'name'	 => 'delete',
					'label'	 => lang('Delete file'),
					'funct'	 => 'onActionsClick_files',
					'classname'	 => '',
					'value_hidden'	 => "",
					'confirm_msg'		=> "Vil du slette fil(er)"
					),
			);

			$tabletools = array
			(
				array('my_name' => 'select_all'),
				array('my_name' => 'select_none')
			);

			foreach ($buttons as $entry)
			{
				$tabletools[] = array
				(
					'my_name'		 => $entry['name'],
					'text'			 => $entry['label'],
					'className'		 =>	$entry['classname'],
					'confirm_msg'	=>	$entry['confirm_msg'],
					'type'			 => 'custom',
					'custom_code'	 => "
						var api = oTable2.api();
						var selected = api.rows( { selected: true } ).data();
						var ids = [];
						for ( var n = 0; n < selected.length; ++n )
						{
							var aData = selected[n];
							ids.push(aData['file_id']);
						}
						{$entry['funct']}('{$entry['action']}', ids);
						"
				);
			}

			$code		 = <<<JS

	this.onActionsClick_filter_files=function(action, ids)
	{
		var tags = $('select#tags').val();
		var oArgs = {menuaction: 'property.uitts.update_data',action:'get_files', id: {$id}};
		var requestUrl = phpGWLink('index.php', oArgs, true);
		$.each(tags, function (k, v)
		{
			requestUrl += '&tags[]=' + v;
		});
		JqueryPortico.updateinlineTableHelper('datatable-container_2', requestUrl);
	}

	this.onActionsClick_files=function(action, ids)
	{
		var numSelected = 	ids.length;

		if (numSelected ==0)
		{
			alert('None selected');
			return false;
		}
		var tags = $('select#tags').val();

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: {$requestUrl},
			data:{ids:ids, tags:tags, action:action},
			success: function(data) {
				if( data != null)
				{

				}
				var oArgs = {menuaction: 'property.uitts.update_data',action:'get_files', id: {$id}};
				var strURL = phpGWLink('index.php', oArgs, true);

				JqueryPortico.updateinlineTableHelper('datatable-container_2',strURL);

				if(action=='delete_file')
				{
					refresh_glider(strURL);
				}
			},
			error: function(data) {
				alert('feil');
			}
		});
	}
JS;
			$GLOBALS['phpgw']->js->add_code('', $code);


			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uiimport_documents.get_files',
						'phpgw_return_as'	 => 'json'))),
				'ColumnDefs' => $files_def,
				'tabletools' => $tabletools,
				'config'	 => array(
					array('disablePagination' => true),
					array('disableFilter' => true),
				)
			);

			$_doument_types = array(
				'Avtaler',
				'Beskrivelser',
				'Bilder',
				'Brosjyrer',
				'Bruksanvisninger',
				'Drifts- og systeminformasjon',
				'Fargekoder',
				'Garantibefaring',
				'Generell orientering',
				'Ikke aktiv',
				'Innholdsfortegnelse',
				'Kart',
				'Låseplaner',
				'Prosjekt- og entreprenørlister',
				'Rapport',
				'Skjema',
				'Tegning',
				'Tegning, fasade',
				'Tegning, plan',
				'Tegning, snitt',
				'Teknisk informasjon',
				'Tilsyn og vedlikehold'
			);


			$doument_types = array();
			foreach ($_doument_types as $doument_type)
			{
				$doument_types[] = array(
					'id' => $doument_type,
					'name' => $doument_type,
				);
			}

			$data = array
				(
				'datatable_def'				 => $datatable_def,
				'tabs'						 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'image_loader'				 => $GLOBALS['phpgw']->common->image('property', 'ajax-loader', '.gif', false),
				'multi_upload_action'		 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiimport_documents.handle_import_files','id' => $id)),
				'building_part_list'		 => array('options' => $this->bocommon->select_category_list(array(
					'type' => 'building_part',
					'order'		 => 'id',
					'id_in_name' => 'num',
					'filter'	 => array()))),
				'branch_list'				 =>array('options' => execMethod('property.boproject.select_branch_list')),
				'doument_type_list'			 =>array('options' => $doument_types),
			);

			phpgwapi_jquery::load_widget('file-upload-minimum');
			phpgwapi_jquery::load_widget('select2');
			self::add_javascript('property', 'portico', 'import_documents.js');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . '::' . lang('import documents');

			self::render_template_xsl(array('import_documents', 'multi_upload_file_inline', 'datatable_inline'), $data);
		}


		function get_files()
		{
			$path_upload_dir = $this->path_upload_dir;
			if (empty($path_upload_dir))
			{
				return false;
			}

			$order_id = phpgw::get_var('order_id', 'int');
			$order_id = 1;

			$options = array();
			$path_dir	 = rtrim($path_upload_dir, '/') . "/{$order_id}/";

			$list_files = $this->_get_files($path_dir);

			$total_records = count($list_files);

			return array
				(
				'data'				 => $list_files,
				'draw'				 => phpgw::get_var('draw', 'int'),
				'recordsTotal'		 => $total_records,
				'recordsFiltered'	 => $total_records
			);

			
		}

		private function _get_files( $dir, $results = array() )
		{
			$content = scandir($dir);

			foreach ($content as $key => $value)
			{
				$path = realpath($dir . '/' . $value);
				if (is_file($path))
				{
					$pos = strpos($value, '..');
					if (!$pos === false)
					{
						$new_path = str_replace('..', '.', $path);
						if (rename($path, $new_path))
						{
							$value	 = str_replace('..', '.', $value);
							$path	 = $new_path;
						}
					}

					$results[] = array(
						'file' => $value,
						'path_absolute'	 => $path,
						'path_relative'	 => '/');
				}
			}

			return $results;
		}

		public function handle_import_files()
		{
			$path_upload_dir = $this->path_upload_dir;
			if (empty($path_upload_dir))
			{
				return false;
			}

			phpgw::import_class('property.multiuploader');

			$order_id = phpgw::get_var('order_id', 'int', 'GET');
			$order_id = 1;

			$options = array();
			$options['upload_dir']	 = rtrim($path_upload_dir, '/') . "/{$order_id}/";
			$options['script_url']	 = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiimport_documents.handle_import_files'));

			if(!$order_id)
			{
				$upload_handler			 = new property_multiuploader($options, false);
				$response = array(files => array(array('error' => 'missing order_id in request')));
				$upload_handler->generate_response($response);
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			$receipt = $this->check_upload_dir($order_id);
			if (($receipt['error']))
			{
				$upload_handler			 = new property_multiuploader($options, false);
				$response = array(files => array(array('error' => $receipt['error'])));
				$upload_handler->generate_response($response);
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$upload_handler			 = new property_multiuploader($options, true);
		}

		public function check_upload_dir($order_id)
		{
			$rs = $this->create_document_dir($order_id);
			if (!$rs)
			{
				$receipt['error'] = lang('failed to create directory') . ': ' . "{$this->path_upload_dir}/{$order_id}";
			}

			if (!is_writable("{$this->path_upload_dir}/{$order_id}"))
			{
				$receipt['error'] = lang('Not have permission to access the directory') . ': ' . "{$this->path_upload_dir}/{$order_id}";
			}

			return $receipt;
		}

		private function create_document_dir($order_id)
		{
			if (is_dir("{$this->path_upload_dir}/{$order_id}"))
			{
				return true;
			}

			$old = umask(0);
			$rs	 = mkdir("{$this->path_upload_dir}/{$order_id}", 0755);
			umask($old);

			return $rs;
		}

		public function get_attributes_from_template()
		{
		}

		public function get_locations_for_type()
		{
		}

		/**
		 * Fetch data from $this->bo based on parametres
		 * @return array
		 */
		public function query()
		{
			return;
		}

		public function get_categories_for_type()
		{
		}

		public function get_profile()
		{
		}

		public function get_data_type()
		{
		}

		public function get_part_of_town()
		{
		}

		private function _get_document_categories( $selected = 0 )
		{
		}
	}