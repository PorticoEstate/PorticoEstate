<?php

	class property_uiimport
	{
		var $public_functions = array
		(
			'index'		=> true
		);

		const DELIMITER = ";";
		const ENCLOSING = "'";

		// List of messages, warnings and errors to be displayed to the user after the import
		protected $messages = array();
		protected $warnings = array();
		protected $errors = array();

		// File system path to import folder on server
		protected $file;
		protected $district;
		protected $csvdata;
		protected $account;
		protected $conv_type;
		protected $import_conversion;
		protected $steps = 0;
		protected $fields = array();
		protected $table;
		protected $debug;

		// Label on the import button. Changes as we step through the import process.
		protected $import_button_label;
		protected $download_template_button_label;

		protected $defalt_values;


		private $valid_tables = array();

		public function __construct()
		{
			if ( !$GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin')
				&& !$GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'property'))
			{
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				execMethod('property.bocommon.no_access');
			}

			set_time_limit(10000);
//			$GLOBALS['phpgw']->common->phpgw_header(true);
			$this->account		= (int)$GLOBALS['phpgw_info']['user']['account_id'];
			$this->db           = & $GLOBALS['phpgw']->db;
			$this->table 		= phpgw::get_var('table');
			
			$this->valid_tables = array
			(
				'fm_vendor'						=> array('name' => 'fm_vendor (' . lang('vendor') . ')', 'permission' => PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT),
				'fm_condition_survey'			=> array('name' => 'fm_condition_survey (' . lang('condition survey') . ')', 'permission' => PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT),
				'fm_ecodimb'					=> array('name' => 'fm_ecodimb (' . lang('dimb') . ')', 'permission' => PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT),
				'fm_budget'						=> array('name' => 'fm_budget (' . lang('budget') . ')', 'permission' => PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT),
				'fm_department'					=> array('name' => 'fm_department (' . lang('department') . ')', 'permission' => PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT),
				'fm_eco_periodization_outline'	=> array('name' => 'fm_eco_periodization_outline (' . lang('periodization outline') . ')', 'permission' => PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT),
				'fm_eco_periodization'			=> array('name' => 'fm_eco_periodization (' . lang('periodization') . ')', 'permission' => PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT),
				'fm_ecodimd'					=> array('name' => 'fm_ecodimd', 'permission' => PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT),
				'phpgw_categories'				=> array('name' => 'phpgw_categories (' . lang('categories') . ')', 'permission' => PHPGW_ACL_READ),
			);

			$location_types = execMethod('property.soadmin_location.select_location_type');
			
			foreach ($location_types as $location_type)
			{
				$this->valid_tables["fm_location{$location_type['id']}"] = array('name' => "fm_location{$location_type['id']} ({$location_type['name']})", 'permission' => PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT);
			}

			if($this->table && !in_array($this->table, array_keys($this->valid_tables)))
			{
				throw new Exception("Not a valid table: {$this->table}");			
			}
		}


		/**
		 * Public method. 
		 * 
		 * @return unknown_type
		 */
		public function index()
		{
			// Set the submit button label to its initial state
			$this->import_button_label = "Start import";
			$this->download_template_button_label = 'Download template';

			$check_method = 0;
			if($this->conv_type = phpgw::get_var('conv_type'))
			{
				$check_method ++;
			}
			if ($location_id = phpgw::get_var('location_id', 'int'))
			{
				$check_method ++;
			}
				
			if($table = phpgw::get_var('table'))
			{
				$check_method ++;
			}

			if($check_method > 1)
			{
				phpgwapi_cache::session_set('property', 'import_message', 'choose only one target!');
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=>'property.uiimport.index'));
			}


			phpgwapi_cache::session_set('property', 'import_settings', $_POST);			

			$download_template = phpgw::get_var('download_template');
			
			if($download_template)
			{
				$this->get_template($location_id);
			}
			
			// If the parameter 'importsubmit' exist (submit button in import form), set path
			if (phpgw::get_var("importsubmit")) 
			{
				if($GLOBALS['phpgw']->session->is_repost() && !phpgw::get_var('debug', 'bool'))
				{
					phpgwapi_cache::session_set('property', 'import_message', 'Hmm... looks like a repost!');
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=>'property.uiimport.index'));
				}


				$start_time = time(); // Start time of import
				$start = date("G:i:s",$start_time);
				echo "<h3>Import started at: {$start}</h3>";
				echo "<ul>";

				if($this->conv_type)
				{
					if ( preg_match('/\.\./', $this->conv_type) )
					{
							throw new Exception("Not a valid file: {$this->conv_type}");
					}

					$file = PHPGW_SERVER_ROOT . "/property/inc/import/{$GLOBALS['phpgw_info']['user']['domain']}/{$this->conv_type}";

					if ( is_file($file) )
					{
						require_once $file;
					}
				}
				else
				{
					require_once PHPGW_SERVER_ROOT . "/property/inc/import/import_update_generic.php";
				}


				$this->debug = phpgw::get_var('debug', 'bool');
				$this->import_conversion = new import_conversion($location_id,$this->debug);

				// Get the path for user input or use a default path

				$files = array();
				if(isset($_FILES['file']['tmp_name']) && $_FILES['file']['tmp_name'])
				{
					$files[] = array
					(
						'name'	=> $_FILES['file']['tmp_name'],
						'type'	=> $_FILES['file']['type']
					);

				}
				else
				{
					$path = phpgw::get_var('path', 'string');
					$files = $this->get_files($path);
				}

				if(!$files)
				{
					phpgwapi_cache::session_set('property', 'import_message', 'Ingen filer er valgt');
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=>'property.uiimport.index'));
				}

				foreach ($files as $file)
				{
					$valid_type = false;
					switch ($file['type'])
					{
						case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
						case 'application/vnd.oasis.opendocument.spreadsheet':
						case 'application/vnd.ms-excel':
							$this->csvdata = $this->getexceldata($file['name']);
							$valid_type = true;
							break;
						case 'text/csv':
						case 'text/comma-separated-values':
							$this->csvdata = $this->getcsvdata($file['name']);
							$valid_type = true;
							break;
						default:
							throw new Exception("Not a valid filetype: {$file['type']}");
					}

					if($valid_type)
					{
						$result = $this->import();
						$this->messages = array_merge($this->messages,$this->import_conversion->messages);
						$this->warnings = array_merge($this->warnings,$this->import_conversion->warnings);
						$this->errors = array_merge($this->errors,$this->import_conversion->errors);
						$this->csvdata = array();
						echo '<li class="info">Import: finished step ' .$result. '</li>';
					}
				}


				echo "</ul>";
				$end_time = time();
				$difference = ($end_time - $start_time) / 60;
				$end = date("G:i:s",$end_time);
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
				echo '<a href="'. $GLOBALS['phpgw']->link('/home.php') . '">Home</a>';
				echo '</br><a href="'. $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'property.uiimport.index')) . '">Import</a>';

			}
			else
			{
				$import_settings	= phpgwapi_cache::session_get('property', 'import_settings');
				$import_message		= phpgwapi_cache::session_get('property', 'import_message');

				phpgwapi_cache::session_clear('property', 'import_message');
				$conv_list			= $this->get_import_conv($import_settings['conv_type']);

				$conv_option = '<option value="">' . lang('none selected') . '</option>' . "\n";
				foreach ( $conv_list as $conv)
				{
					$selected = '';
					if ( $conv['selected'])
					{
						$selected = 'selected =  "selected"';
					}

					$conv_option .=  <<<HTML
					<option value='{$conv['id']}'{$selected}>{$conv['name']}</option>
HTML;
				}

				$tables = $this->valid_tables;

				ksort($tables);

				$table_option = '<option value="">' . lang('none selected') . '</option>' . "\n";
				foreach ( $tables as $table => $table_info)
				{
					$selected = $import_settings['table'] == $table ? 'selected =  "selected"' : '';
					$table_option .=  <<<HTML
					<option value='{$table}'{$selected}>{$table_info['name']}::{$table_info['permission']}</option>
HTML;
				}

				$entity			= CreateObject('property.soadmin_entity');
				$entity_list 	= $entity->read(array('allrows' => true));
				$category_option = '<option value="">' . lang('none selected') . '</option>' . "\n";
				foreach($entity_list as $entry)
				{
					$category_list = $entity->read_category_tree2($entry['id']);

					foreach($category_list as $category)
					{
						$selected = $import_settings['location_id'] == $category['location_id'] ? 'selected =  "selected"' : '';
						$category_option .=  <<<HTML
						<option value="{$category['location_id']}"{$selected}>{$category['name']}</option>
HTML;
					}
				}

				$home =  $GLOBALS['phpgw']->link('/home.php');
				$action =  $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'property.uiimport.index'));

				$debug_checked = isset($import_settings['debug']) && $import_settings['debug'] ? 'checked =  "checked"' : '';
				$html = <<<HTML
				<h1><img src="rental/templates/base/images/32x32/actions/document-save.png" /> Importer ( MsExcel / CSV )</h1>
				<div id="messageHolder">{$import_message}</div>
				<form action="{$action}" method="post" enctype="multipart/form-data">
					<fieldset>
						<p>
							<label for="file">Choose file:</label>
							<input type="file" name="file" id="file" title = 'Single file'/>
						</p>
						<p>
							<label for="path">Local path:</label>
							<input type="text" name="path" id="path" value = '{$import_settings['path']}' title = 'Alle filer i katalogen'/>
						</p>
						<p>
							<label for="conv_type">Choose conversion:</label>
							<select name="conv_type" id="conv_type">
							{$conv_option}
						</select>
						</p>
						<p>
							<label for="table">Choose Table:</label>
							<select name="table" id="table">
							{$table_option}
						</select>
						</p>
						<p>
							<label for="category">Choose category:</label>
							<select name="location_id" id="category">
							{$category_option}
						</select>
						</p>

						<p>
							<label for="debug">Debug:</label>
							<input type="checkbox" name="debug" id="debug" {$debug_checked} value ='1' />
						</p>
						<p>
							<input type="submit" name="download_template" value="{$this->download_template_button_label}"  />
						</p>
						<p>
							<input type="submit" name="importsubmit" value="{$this->import_button_label}"  />
						</p>
		 			</fieldset>
				</form>
				<br><a href='$home'>Home</a>
HTML;
				echo $html;
			}
		}

		/**
		 * Import Facilit data to Portico Estate's rental module
		 * The function assumes CSV files have been uploaded to a location on the server reachable by the
		 * web server user.  The CSV files must correspond to the table names from Facilit, as exported
		 * from Access. Field should be enclosed in single quotes and separated by comma.  The CSV files
		 * must contain the column headers on the first line.
		 * 
		 * @return unknown_type
		 */
		public function import()
		{
			$this->steps++;

			/* Import logic:
			 * 
			 * 1. Do step logic if the session variable is not set
			 * 2. Set step result on session
			 * 3. Set label for import button
			 * 4. Log messages for this step
			 *  
			 */

			$this->import_data();
			$this->log_messages($this->steps);
			return $this->steps;
		}

		protected function get_template($location_id = 0)
		{
			$data = array();
			$_fields = array();
			if(!$location_id && $this->table)
			{
				$_permission = $this->valid_tables[$this->table]['permission'];

				if(! ($_permission & PHPGW_ACL_READ) )
				{
					throw new Exception("No READ-right for {$this->table}");
				}

				$metadata = $this->db->metadata($this->table);

				foreach ($metadata as $field => $info)
				{
					$_fields[$field] = true;
				}

				$sql = "SELECT * FROM {$this->table}";
				$this->db->query($sql,__LINE__,__FILE__);

				while ($this->db->next_record())
				{
					$data[] = $this->db->Record;
				}

			}
			else if($location_id && !$category = execMethod('property.soadmin_entity.get_single_category', $location_id ))
			{
				throw new Exception("Not a valid location for {$location_id}");
			}
			else if($location_id)
			{
				$entity_id = $category['entity_id'];
				$cat_id = $category['id'];

				if ($category['is_eav'])
				{
					$this->table = 'fm_bim_item';

					$metadata = $this->db->metadata($this->table);
				
					foreach ($metadata as $field  => $info)
					{
						if ($field == 'xml_representation' || $field == 'guid')
						{
							continue;
						}
						$_fields[$field] = true;
					}

					$custom 		= createObject('property.custom_fields');
					$attributes 	= $custom->find2($location_id, 0, '', 'ASC', 'attrib_sort', true, true);


					$sql = "SELECT * FROM {$this->table} WHERE location_id = $location_id ORDER BY id ASC";
					$this->db->query($sql,__LINE__,__FILE__);

					while ($this->db->next_record())
					{
						$_row_data = array();
						foreach ($_fields as $_field => $dummy)
						{
							$_row_data[$_field] = $this->db->f($_field,true);
						}

						$xmldata = $this->db->f('xml_representation',true);
						$xml = new DOMDocument('1.0', 'utf-8');
						$xml->loadXML($xmldata);

						foreach($attributes as $attribute)
						{
							$_row_data[$attribute['column_name']]	= $xml->getElementsByTagName($attribute['column_name'])->item(0)->nodeValue;
						}

						$data[] = $_row_data;
					}

					foreach($attributes as $attribute)
					{
						$_fields[$attribute['column_name']] = true;
					}
				}
				else
				{
					$this->table = "fm_entity_{$category['entity_id']}_{$category['id']}";
					$metadata = $this->db->metadata($this->table);
					foreach ($metadata as $field  => $info)
					{
						$_fields[$field] = true;
					}

					$sql = "SELECT * FROM {$this->table} ORDER BY id ASC";
					$this->db->query($sql,__LINE__,__FILE__);

					while ($this->db->next_record())
					{
						$data[] = $this->db->Record;
					}
				}
			}

			$fields = array_keys($_fields);

			if(phpgw::get_var('debug', 'bool'))
			{
				_debug_array($fields);
			}
			else
			{
				$bocommon = CreateObject('property.bocommon');
				$bocommon->download($data, $fields, $fields);
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
		}

		protected function import_data()
		{
			$metadata = array();
			if($this->table && $this->fields)
			{
				$_permission = $this->valid_tables[$this->table]['permission'];

				if(! ($_permission & PHPGW_ACL_ADD) )
				{
					throw new Exception("No ADD-right for {$this->table}");
				}

				$metadata = $this->db->metadata($this->table);

				if(phpgw::get_var('debug', 'bool'))
				{
					_debug_array($metadata);
				}

				foreach($this->fields as $field)
				{
					if($field && !isset($metadata[$field]))
					{
						$this->messages[] = "Feltet '{$field}' finnes ikke i tabellen '{$this->table}'";
					}
				}

				$this->import_conversion->set_table($this->table);
				$this->import_conversion->set_metadata($metadata);
			}

			if($this->fields)
			{
				$found_field = false;

				foreach($this->fields as $field)
				{
					if($field && !$found_field)
					{
						$found_field = true;
					}
				}

				if(!$found_field)
				{
					throw new Exception("Felter er ikke definert");
				}
				$this->import_conversion->fields = $this->fields;
			}


			$start_time = time();

			$datalines = $this->csvdata;

			$ok = true;
			$_ok = false;
			$this->db->transaction_begin();

			//Do your magic...
			foreach ($datalines as $data)
			{
				$_ok = $this->import_conversion->add($data);

				if( !$_ok )
				{
					$ok = false;
				}
			}

			if($ok)
			{
				$this->messages[] = "Imported data. (" . (time() - $start_time) . " seconds)";
				if($this->debug)
				{
					$this->db->transaction_abort();
					$this->messages[] = "Dry Run: transaction abortet";
				}
				else
				{
					$this->db->transaction_commit();
				}
				return true;
			}
			else
			{
				$this->errors[] = "Import of data failed. (" . (time() - $start_time) . " seconds)";
				$this->db->transaction_abort();
				return false;
			}
		}


		protected function getcsvdata($path, $skipfirstline = true)
		{
			// Open the csv file
			$handle = fopen($path, "r");

			if ($skipfirstline)
			{
				// Read the first line to get the headers out of the way
				$this->fields = $this->getcsv($handle);
			}

			$result = array();

			while(($data = $this->getcsv($handle)) !== false)
			{
				$result[] = $data;
			}

			fclose($handle);

			$this->messages[] = "Read '{$path}' file in " . (time() - $start_time) . " seconds";
			$this->messages[] = "'{$path}' contained " . count($result) . " lines";

			return $result;
		}


		protected function getexceldata($path, $skipfirstline = true)
		{
			phpgw::import_class('phpgwapi.phpexcel');

			$objPHPExcel = PHPExcel_IOFactory::load($path);
			$objPHPExcel->setActiveSheetIndex(0);

			$result = array();

			$highestColumm = $objPHPExcel->getActiveSheet()->getHighestDataColumn();

        	$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumm);

			$rows = $objPHPExcel->getActiveSheet()->getHighestDataRow();

			$start = $skipfirstline ? 2 : 1; // Read the first line to get the headers out of the way

			if ($skipfirstline)
			{
				for ($j=0; $j < $highestColumnIndex; $j++ )
				{
					$this->fields[] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($j,1)->getCalculatedValue();
				}
			}

			$rows = $rows ? $rows +1 : 0;
			for ($row=$start; $row < $rows; $row++ )
			{
				$_result = array();

				for ($j=0; $j < $highestColumnIndex; $j++ )
				{
					$_result[] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($j,$row)->getCalculatedValue();
				}

				$result[] = $_result;
			}

			$this->messages[] = "Read '{$path}' file in " . (time() - $start_time) . " seconds";
			$this->messages[] = "'{$path}' contained " . count($result) . " lines";

			return $result;
		}


		/**
		 * Read the next line from the given file handle and parse it to CSV according to the rules set up
		 * in the class constants DELIMITER and ENCLOSING.  Returns FALSE like getcsv on EOF.
		 * 
		 * @param file-handle $handle
		 * @return array of values from the parsed csv line
		 */
		protected function getcsv($handle)
		{
			return fgetcsv($handle, 1000, self::DELIMITER, self::ENCLOSING);
		}


		private function log_messages($step)
        {
        //	sort($this->errors);
        //	sort($this->warnings);
        //	sort($this->messages);
        
            $msgs = array_merge(
            	array('----------------Errors--------------------'),
            	$this->errors,
            	array('---------------Warnings-------------------'),
            	$this->warnings,
            	array('---------------Messages-------------------'),
            	$this->messages
            );

            $path = $GLOBALS['phpgw_info']['server']['temp_dir'];
            if(is_dir($path.'/logs') || mkdir($path.'/logs'))
            {
                file_put_contents("$path/logs/$step.log", implode(PHP_EOL, $msgs));
            }
        }

		protected function get_import_conv($selected='')
		{
			$dir_handle = @opendir(PHPGW_SERVER_ROOT . "/property/inc/import/{$GLOBALS['phpgw_info']['user']['domain']}");
			$i=0; $myfilearray = array();
			while ($file = readdir($dir_handle))
			{
				if ((substr($file, 0, 1) != '.') && is_file(PHPGW_SERVER_ROOT . "/property/inc/import/{$GLOBALS['phpgw_info']['user']['domain']}/{$file}") )
				{
					$myfilearray[$i] = $file;
					$i++;
				}
			}
			closedir($dir_handle);
			sort($myfilearray);

			for ($i=0;$i<count($myfilearray);$i++)
			{
				$fname = preg_replace('/_/',' ',$myfilearray[$i]);

				$conv_list[] = array
				(
					'id'		=> $myfilearray[$i],
					'name'		=> $fname,
					'selected'	=> $myfilearray[$i]==$selected ? 1 : 0
				);
			}

			return $conv_list;
		}

		protected function get_files($dirname)
		{
			// prevent path traversal
			if ( preg_match('/\./', $dirname) 
			 || !is_dir($dirname) )
			{
				return array();
			}

			$mime_magic = createObject('phpgwapi.mime_magic');

			$file_list = array();
			$dir = new DirectoryIterator($dirname); 
			if ( is_object($dir) )
			{
				foreach ( $dir as $file )
				{
					if ( $file->isDot()
						|| !$file->isFile()
						|| !$file->isReadable())
//						|| strcasecmp( end( explode( ".", $file->getPathname() ) ), 'xls' ) != 0 )
//						|| strcasecmp( end( explode( ".", $file->getPathname() ) ), 'csv' ) != 0 ))
 					{
						continue;
					}

					$file_name = $file->__toString();
					$file_list[] = array
					(
						'name'	=> (string) "{$dirname}/{$file_name}",
						'type'	=> $mime_magic->filename2mime($file_name)
					);
				}
			}

			return $file_list;
		}
	}
