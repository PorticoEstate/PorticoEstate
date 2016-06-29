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

	class property_uiimport_components extends phpgwapi_uicommon_jquery
	{
		public $public_functions = array(
			'query' => true,
			'index' => true,
			'get_locations_for_type' => true
		);

		public function __construct()
		{
			parent::__construct();

			$this->bocommon = CreateObject('property.bocommon');
			$this->acl = & $GLOBALS['phpgw']->acl;


			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "property::documentation::generic";
		}

		public function download()
		{
			return;
		}

		/**
		 * Prepare UI
		 * @return void
		 */
		public function index()
		{
			// Set the submit button label to its initial state
			$this->import_button_label = "Start import";

			$check_method = 0;
			$get_identificator = false;

			if ($check_method > 1)
			{
				phpgwapi_cache::session_set('property', 'import_message', 'choose only one target!');
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiimport.components'));
			}


			phpgwapi_cache::session_set('property', 'import_settings', $_POST);


			// If the parameter 'importsubmit' exist (submit button in import form), set path
			if (phpgw::get_var("importsubmit"))
			{
				if ($GLOBALS['phpgw']->session->is_repost() && !phpgw::get_var('debug', 'bool'))
				{
					phpgwapi_cache::session_set('property', 'import_message', 'Hmm... looks like a repost!');
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiimport.components'));
				}


				$start_time = time(); // Start time of import
				$start = date("G:i:s", $start_time);
				echo "<h3>Import started at: {$start}</h3>";
				echo "<ul>";

				$this->debug = phpgw::get_var('debug', 'bool');
				//$this->import_conversion = new import_conversion($this->location_id, $this->debug);

				// Get the path for user input or use a default path

				$files = array();
				if (isset($_FILES['file']['tmp_name']) && $_FILES['file']['tmp_name'])
				{
					$files[] = array
						(
						'name' => $_FILES['file']['tmp_name'],
						'type' => $_FILES['file']['type']
					);
				}
				else
				{
					$path = phpgw::get_var('path', 'string');
					$files = $this->get_files($path);
				}

				if (!$files)
				{
					phpgwapi_cache::session_set('property', 'import_message', 'Ingen filer er valgt');
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiimport.components'));
				}

				$entity_categories_in_xml = array();
				foreach ($files as $file)
				{
					$valid_type = true;

					$result = $this->getxmldata($file['name'], $get_identificator);
					
					$postnrdelkode = $result['Prosjekter']['ProsjektNS']['Postnrplan']['PostnrdelKoder']['PostnrdelKode'];
					$entities_name = array();
					foreach ($postnrdelkode as $items) 
					{
						if ($items['PostnrdelKoder']['PostnrdelKode']['Kode'])
						{
								$entities_name[$items['PostnrdelKoder']['PostnrdelKode']['Kode']] = array(
									'name' => $items['PostnrdelKoder']['PostnrdelKode']['Kode'].' - '.$items['PostnrdelKoder']['PostnrdelKode']['Navn']
								);							
						}
						else {
							foreach ($items['PostnrdelKoder']['PostnrdelKode'] as $item) 
							{
								$entities_name[$item['Kode']] = array('name' => $item['Kode'].' - '.$item['Navn']);
							}
						}
					}
					
					$posts = $result['Prosjekter']['ProsjektNS']['Prosjektdata']['Post'];
					foreach ($posts as $post) 
					{
						$buildingpart = $post['Postnrdeler']['Postnrdel'][1]['Kode'];
						$entity_categories_in_xml[$buildingpart]['name'] = $entities_name[$buildingpart]['name'];
						$entity_categories_in_xml[$buildingpart]['components'][] = array(
							array('name' => 'benevnelse', 'value' => trim($post['Egenskaper']['Egenskap']['Verdi'])),
							array('name' => 'beskrivelse', 'value' => trim($post['Tekst']['Uformatert']))
						);
						
						//$buildingpart_in_xml[$post['Postnrdeler']['Postnrdel'][1]['Kode']] = $post['Postnrdeler']['Postnrdel'][1]['Kode'];
					}
		
					//echo '<li class="info">Import: finished step ' . print_r($buildingpart) . '</li>';
				}
				
				require_once PHPGW_SERVER_ROOT . "/property/inc/import/import_update_components.php";

				$import_components = new import_components();
				$entity_categories  = $import_components->get_entity_categories();

				$buildingpart_out_table = array();
				foreach ($entity_categories_in_xml as $k => $v) 
				{
					if (!array_key_exists((string)$k, $entity_categories))
					{
						$buildingpart_parent = substr($k, 0, strlen($k) -1);
						if (empty($entity_categories[$buildingpart_parent]))
						{
							
						}
						$buildingpart_out_table[$k] = array('parent' => $entity_categories[$buildingpart_parent], 'name' => $v['name']);
					} else {
						$entity_categories_in_xml[$k]['cat_id'] = $entity_categories[$k]['id'];
						$entity_categories_in_xml[$k]['entity_id'] = $entity_categories[$k]['entity_id'];
					}
				}
				
				if (count($buildingpart_out_table))
				{
					$buildingpart_processed = $import_components->add_entity_categories($buildingpart_out_table);
					
					if (count($buildingpart_processed['added']))
					{
						echo 'Entities added: <br>';
						foreach($buildingpart_processed['added'] as $k => $v)
						{
							$entity_categories_in_xml[$k]['cat_id'] = $v['id'];
							$entity_categories_in_xml[$k]['entity_id'] = $v['entity_id'];			
							echo $v['name'].'<br>';
						}
					} 
					
					if (count($buildingpart_processed['not_added']))
					{
						echo '<br>Entities not added: <br>';
						foreach($buildingpart_processed['not_added'] as $k => $v)
						{
							unset($entity_categories_in_xml[$k]);	
							echo $v['name'].'<br>';
						}						
					}
				}
				
				$components_not_added = $import_components->add_bim_item($entity_categories_in_xml);
				if (count($components_not_added))
				{
					echo '<br>Components not added: <br>';
					foreach ($components_not_added as $k => $v)
					{
						echo $k.' => not added: '.$v.'<br>';
					}
				}
				
				//print_r($entity_categories_in_xml);
				
				echo "</ul>";
				$end_time = time();
				$difference = ($end_time - $start_time) / 60;
				$end = date("G:i:s", $end_time);
				echo "<h3>Import ended at: {$end}. Import lasted {$difference} minutes.";

				if ($this->errors)
				{
					echo "<ul>";
					foreach ($this->errors as $error)
					{
						echo '<li class="error">Error: ' . $error . '</li>';
					}

					echo "</ul>";
				}

				if ($this->warnings)
				{
					echo "<ul>";
					foreach ($this->warnings as $warning)
					{
						echo '<li class="warning">Warning: ' . $warning . '</li>';
					}
					echo "</ul>";
				}

				if ($this->messages)
				{
					echo "<ul>";

					foreach ($this->messages as $message)
					{
						echo '<li class="info">Message: ' . $message . '</li>';
					}
					echo "</ul>";
				}
				echo '<a href="' . $GLOBALS['phpgw']->link('/home.php') . '">Home</a>';
				echo '</br><a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiimport.components')) . '">Import</a>';
			}
			
			$tabs = array();
			$tabs['locations'] = array('label' => lang('Locations'), 'link' => '#locations');
			$tabs['upload'] = array('label' => lang('Upload'), 'link' => '#upload', 'disable' => 1);
			
			$active_tab = 'locations';

			$type_filter = 	execMethod('property.soadmin_location.read', array());			
			$category_filter = $this->get_categories_for_type();

			$district_filter = $this->bocommon->select_district_list('filter');
			array_unshift($district_filter, array('id' => '', 'name' => lang('no district')));

			$part_of_town_filter = $this->get_part_of_town();

			$related_def = array
				(
				array('key' => 'location_code', 'label' => lang('location'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'loc1_name', 'label' => lang('name'), 'sortable' => false, 'resizeable' => true)
			);


			$datatable_def[] = array
			(
				'container' => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uiimport_components.get_locations_for_type', 'phpgw_return_as' => 'json'))),
				'ColumnDefs' => $related_def,
				'tabletools' => array(),
				'config' => array(
					array('singleSelect' => true)
				)				
			);	
				
			$data = array
			(
				'datatable_def' => $datatable_def,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				
				'type_filter' => array('options' => $type_filter),
				'category_filter' => array('options' => $category_filter),
				'district_filter' => array('options' => $district_filter),
				'part_of_town_filter' => array('options' => $part_of_town_filter),
				
				'link_controller_example' => self::link(array('menuaction' => 'controller.uicomponent.index'))
			);
			
			self::add_javascript('property', 'portico', 'import_components.js');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . '::' . lang('Importer ');

			self::render_template_xsl(array('import_components', 'datatable_inline'), $data);
		}

		public function get_locations_for_type()
		{
			$type_id = phpgw::get_var('type_id', 'int');

			if (!$type_id)
			{
				$type_id = 1;
			}
			
			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'cat_id' => phpgw::get_var('cat_id', 'int', 'REQUEST', 0),
				'type_id' => $type_id,
				'district_id' => phpgw::get_var('district_id', 'int', 'REQUEST', 0),
				'part_of_town_id' => phpgw::get_var('part_of_town_id', 'int', 'REQUEST', 0),
				'allrows' => phpgw::get_var('length', 'int') == -1
			);
			
            $solocation = CreateObject('property.solocation');
            $locations = $solocation->read($params);
			
			$values = array();
			foreach($locations as $item)
			{
				$values[] = array(
					'location_code' => $item['location_code'],
					'loc1_name' => $item['loc1_name']
				);				
			}

			$result_data = array('results' => $values);

			$result_data['total_records'] = $solocation->total_records;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
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
			$type_id = phpgw::get_var('type_id', 'int');

			if (!$type_id)
			{
				$type_id = 1;
			}
			
			$categories = $this->bocommon->select_category_list(array
				('format' => 'filter',
				'selected' => '',
				'type' => 'location',
				'type_id' => $type_id,
				'order' => 'descr')
			);
			array_unshift($categories, array('id' => '', 'name' => lang('no category')));

			return $categories;
		}
		
		public function get_part_of_town()
		{
			$district_id = phpgw::get_var('district_id', 'int');
			$values = $this->bocommon->select_part_of_town('filter', '', $district_id);
			array_unshift($values, array('id' => '', 'name' => lang('no part of town')));

			return $values;
		}
	}