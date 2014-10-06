<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage catch
 	* @version $Id$
	*/

	/**
	 * Description
	 * example cron : /usr/local/bin/php -q /var/www/html/phpgroupware/property/inc/cron/cron.php default catch_ppc
	 * @package property
	 */

	include_class('property', 'cron_parent', 'inc/cron/');

	class catch_ppc extends property_cron_parent
	{
		public function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location = lang('catch');
			$this->function_msg	= 'Import info from files';

			set_time_limit(1000);
		}


		function execute()
		{
			try
			{
				$this->import_ppc();
			}
			catch(Exception $e)
			{
				$this->receipt['error'][]=array('msg'=>$e->getMessage());
			}
		}

		function import_ppc()
		{

			//do the actual import
 			$config = CreateObject('catch.soconfig');
 			$config->read_repository();
			$entity	= CreateObject('property.soentity');
			$entity->type = 'catch';
			$admin_entity = CreateObject('property.soadmin_entity');
			$admin_entity->type = 'catch';

			$bofiles	= CreateObject('property.bofiles');

			foreach($config->config_data as $config_data)
 			{
 				$this->pickup_path = $config_data['pickup_path'];
 				$target = $config_data['target'];
 				$target_table = "fm_catch_{$target}";
				list($entity_id, $cat_id) = split('[_]', $target);
				$this->category_dir = "catch_{$entity_id}_{$cat_id}";
				$category			= $admin_entity->read_single_category($entity_id, $cat_id);
				$schema_text		= "{$target} {$category['name']}";

				$metadata = $this->db->metadata($target_table);
				if(!$metadata)
				{
					throw new Exception(lang('no valid target'));
				}
			
//				$xmlparse = CreateObject('property.XmlToArray');
//				$xmlparse->setEncoding('UTF-8');

				$file_list = $this->get_files();

 				$i = 0;
				foreach ($file_list as $file)
				{
					$xml = new DOMDocument('1.0', 'utf-8');
					$xml->load($file);

					$var_result = array();
					
					//_debug_array($xml->getElementsByTagName('PPCC')->item(0)->getattribute('UUID'));die();
					
					foreach($metadata as $field => $field_info)
					{
						$var_result[$field] = $xml->getElementsByTagName($field)->item(0)->nodeValue;
					}
					$var_result['unitid'] = $xml->getElementsByTagName('UnitID')->item(0)->nodeValue;
//					_debug_array($var_result);die();

//					$var_result = $xmlparse->parseFile($file);
//					$var_result = array_change_key_case($var_result, CASE_LOWER);

					//data
					$insert_values	= array();
					$cols		= array();
					$val_errors	= array();

					foreach($metadata as $field => $field_info)
					{
						// If field is missing from file jump to next
						if(!isset($var_result[$field]))
						{
							continue;
						}

						$insert_value = trim($var_result[$field]);
						switch ( $field_info->type )
						{
							case 'string':
							case 'varchar':
								$max_length = intval($field_info->max_length);
								$input_length = strlen( $insert_value );

								if( $input_length > $max_length ) {
									$val_errors[] = lang('Input for field "%1" is %2 characters, max for field is %3 (%4)', 
										$field_info->name, $input_length, $max_length, $file);
								}
								break;
							case 'int2':
							case 'int4':
								// Check if input starts with - (optional) and then only
								// contains numbers
								/*
								if( preg_match('@^[-]?[0-9]+$@', $insert_value) !== 1 )
								{
									$val_errors[] = lang('Input for field "%1" is "%2", but should be int (%3)',
										$field_info->name, $insert_value, $file);
								}
								*/
								$insert_value = $insert_value ? (int) $insert_value : '';
								break;
							case 'numeric':
								$insert_value = str_replace( ',', '.', $insert_value);
								$insert_value = floatval($insert_value);
								break;
							case 'timestamp':
								$insert_value = date( $this->db->date_format(), strtotime( $insert_value ) );
								break;
						}
						$insert_values[] = $insert_value;
						$cols[]	= $field;
					}

					// Raise exception if we have validation errors
					if( count( $val_errors ) > 0 )
					{
						throw new Exception( implode("<br>", $val_errors) );						
					}

					if($cols) // something to import
					{
						$movefiles = array();

						$this->db->transaction_begin();

						$cols[]	= 'entry_date';
						$insert_values[] = time();
						$id = $entity->generate_id(array('entity_id'=>$entity_id,'cat_id'=>$cat_id));
						$num = $entity->generate_num($entity_id, $cat_id, $id);
						$this->db->query("SELECT * FROM fm_catch_1_1 WHERE unitid ='{$var_result['unitid']}'",__LINE__,__FILE__);
						$this->db->next_record();
						$user_id = $this->db->f('user_');
						if(!$user_id)
						{
							throw new Exception(lang('no valid user for this UnitID: %1', $var_result['unitid']));
						}

						$bofiles->set_account_id($user_id);
						$GLOBALS['phpgw_info']['user']['account_id'] = $user_id; // needed for the vfs::mkdir()
						$GLOBALS['phpgw_info']['flags']['currentapp'] = 'property';

						$insert_values	= $this->db->validate_insert($insert_values);
						$this->db->query("INSERT INTO $target_table (id, num, user_id, " . implode(',', $cols) . ')'
						. "VALUES ($id, '$num', $user_id, $insert_values)",__LINE__,__FILE__);

						//attachment
						foreach($var_result as $field => $data)
						{
							if(is_file("{$this->pickup_path}/{$data}"))
							{
								$to_file = "{$bofiles->fakebase}/{$this->category_dir}/dummy/{$id}/{$field}_{$data}"; // the dummy is for being consistant with the entity-code that relies on loc1
								$bofiles->create_document_dir("{$this->category_dir}/dummy/{$id}");

								$bofiles->vfs->override_acl = 1;

								if(!$bofiles->vfs->cp (array (
									'from'	=> "{$this->pickup_path}/{$data}",
									'to'	=> $to_file,
									'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))))
								{
									$this->receipt['error'][]=array('msg'=>lang('Failed to upload file %1 on id %2', $data, $num));
								}
								$bofiles->vfs->override_acl = 0;
								// move attachment
								$movefiles["{$this->pickup_path}/{$data}"] = "{$this->pickup_path}/imported/{$data}";
							}
						}
						// move file
						$_file = basename($file);
						$movefiles["{$this->pickup_path}/{$_file}"] = "{$this->pickup_path}/imported/{$_file}";

						$i++;


						$ok = false;
						if($this->db->transaction_commit())
						{
							foreach ($movefiles as $movefrom => $moveto)
							{
								$ok = @rename($movefrom, $moveto);
							}
						}

						if(!$ok)
						{
							$this->db->query("DELETE FROM $target_table WHERE id =" . (int)$id,__LINE__,__FILE__);
							$i--;
							$this->receipt['error'][]=array('msg'=>lang('There was a problem moving the file(s), imported records are reverted'));
						}
						else
						{

							// finishing
							$criteria = array
							(
								'appname'	=> 'catch',
								'location'	=> '.catch.' . str_replace('_','.',$target),
								'allrows'	=> true
							);

							$custom_functions = $GLOBALS['phpgw']->custom_functions->find($criteria);

							foreach ( $custom_functions as $entry )
							{
								// prevent path traversal
								if ( preg_match('/\.\./', $entry['file_name']) )
								{
									continue;
								}

								$file = PHPGW_SERVER_ROOT . "/catch/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";
								if ( $entry['active'] && is_file($file) )
								{
									require $file;
								}
							}
						}
					}
				}
				$this->receipt['message'][]=array('msg'=>lang('%1 records imported to %2', $i, $schema_text));
			}
		}

		public function get_files()
		{
			$dirname = $this->pickup_path;
			// prevent path traversal
			if ( preg_match('/\./', $dirname) 
			 || !is_dir($dirname) )
			{
				return array();
			}

			$file_list = array();
			$dir = new DirectoryIterator($dirname); 
			if ( is_object($dir) )
			{
				foreach ( $dir as $file )
				{
					if ( $file->isDot()
						|| !$file->isFile()
						|| !$file->isReadable()
						//|| mime_content_type($file->getPathname()) != 'text/xml')
						//|| finfo_file( finfo_open(FILEINFO_MIME, '/usr/share/file/magic'), $file->getPathname() ) != 'text/xml')
						|| strcasecmp( end( explode( ".", $file->getPathname() ) ), 'xml' ) != 0 )
 					{
						continue;
					}

					$file_list[] = (string) "{$dirname}/{$file}";
				}
			}

			return $file_list;
		}
	}
