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
	 * @subpackage location
	 * @version $Id$
	 */

	/**
	 * Description
	 * @package property
	 */

	require_once PHPGW_SERVER_ROOT . "/phpgwapi/js/jquery/file-upload/server/php/UploadHandler.php";

	class property_multiuploader extends UploadHandler
	{
		
		function __construct($options = null, $initialize = true, $error_messages = null)
		{
			$fakebase = !empty($options['fakebase']) ? $options['fakebase'] : '/property';
			
			$this->bofiles = CreateObject('property.bofiles', $fakebase);
			
			parent::__construct($options, $initialize, $error_messages);
		}
		
		public function add_file($print_response = true) 
		{
			if ($this->get_query_param('_method') === 'DELETE') {
				return $this->delete_file($print_response);
			}
			$upload = $this->get_upload_data($this->options['param_name']);

			// Parse the Content-Disposition header, if available:
			$content_disposition_header = $this->get_server_var('HTTP_CONTENT_DISPOSITION');
			$file_name = $content_disposition_header ?
				rawurldecode(preg_replace(
					'/(^[^"]+")|("$)/',
					'',
					$content_disposition_header
				)) : null;
			// Parse the Content-Range header, which has the following form:
			// Content-Range: bytes 0-524287/2000000
			$content_range_header = $this->get_server_var('HTTP_CONTENT_RANGE');
			$content_range = $content_range_header ?
				preg_split('/[^0-9]+/', $content_range_header) : null;
			$size =  $content_range ? $content_range[3] : null;
			$files = array();
			if ($upload) {
				if (is_array($upload['tmp_name'])) {
					// param_name is an array identifier like "files[]",
					// $upload is a multi-dimensional array:
					foreach ($upload['tmp_name'] as $index => $value) {
						$files[] = $this->handle_file_upload_custom(
							$upload['tmp_name'][$index],
							$file_name ? $file_name : $upload['name'][$index],
							$size ? $size : $upload['size'][$index],
							$upload['type'][$index],
							$upload['error'][$index],
							$index,
							$content_range
						);
					}
				} else {
					// param_name is a single object identifier like "file",
					// $upload is a one-dimensional array:
					$files[] = $this->handle_file_upload_custom(
						isset($upload['tmp_name']) ? $upload['tmp_name'] : null,
						$file_name ? $file_name : (isset($upload['name']) ?
								$upload['name'] : null),
						$size ? $size : (isset($upload['size']) ?
								$upload['size'] : $this->get_server_var('CONTENT_LENGTH')),
						isset($upload['type']) ?
								$upload['type'] : $this->get_server_var('CONTENT_TYPE'),
						isset($upload['error']) ? $upload['error'] : null,
						null,
						$content_range
					);
				}
			}
			$response = array($this->options['param_name'] => $files);
			return $this->generate_response($response, $print_response);
		}
	
		protected function handle_file_upload_custom($uploaded_file, $name, $size, $type, $error,
				$index = null, $content_range = null) 
		{
			$file = new \stdClass();
			$file->name = $this->get_file_name($uploaded_file, $name, $size, $type, $error,
				$index, $content_range);
			$file->size = $this->fix_integer_overflow((int)$size);
			$file->type = $type;

			if ($this->custom_validate($uploaded_file, $file, $error, $index)) 
			{
				$this->handle_form_data($file, $index);
				//$upload_dir = $this->get_upload_path();

				$file_path = $this->get_upload_path($file->name);
				$append_file = $content_range && is_file($file_path) &&
					$file->size > $this->get_file_size($file_path);
				
				$this->upload_file($this->options['base_dir'], $uploaded_file, $file);
				if ($file->error) {
					return $file;
				}
			
				$file_size = $this->get_file_size($file_path, $append_file);
				if ($file_size === $file->size) {
					$file->url = $this->get_download_url($file->name);
					if ($this->is_valid_image_file($file_path)) {
						$this->handle_image_file($file_path, $file);
					}
				} else {
					$file->size = $file_size;
					if (!$content_range && $this->options['discard_aborted_uploads']) {
						unlink($file_path);
						$file->error = $this->get_error_message('abort');
					}
				}
				$this->set_additional_file_properties($file);
			}

			return $file;
		}
		
		
		public function custom_validate( $uploaded_file, $file, $error, $index)
		{
			// Check post_max_size (http://us3.php.net/manual/en/features.file-upload.php#73762)
			$POST_MAX_SIZE = ini_get('post_max_size');
			$unit = strtoupper(substr($POST_MAX_SIZE, -1));
			$multiplier = ($unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1)));

			if ((int)$_SERVER['CONTENT_LENGTH'] > $multiplier * (int)$POST_MAX_SIZE && $POST_MAX_SIZE)
			{
				$file->error = lang('POST exceeded maximum allowed size.');
				return false;				
			}

			$max_file_size_in_bytes = 2147483647; // 2GB in bytes

			$currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];

			$config = CreateObject('phpgwapi.config', $currentapp)->read();
			if(empty($config['uploader_filetypes']))
			{
				$config = CreateObject('phpgwapi.config', 'property')->read();
			}

			$uploader_filetypes = isset($config['uploader_filetypes']) ? $config['uploader_filetypes'] : 'jpg,gif,png';

			//$extension_whitelist = array("jpg", "gif", "png");	// Allowed file extensions
			$extension_whitelist = explode(',', $uploader_filetypes);

			$valid_chars_regex = '.A-Z0-9_ !@#$%^&()+={}\[\]\',~`-'; // Characters allowed in the file name (in a Regular Expression format)
			// Other variables	
			$MAX_FILENAME_LENGTH = 260;
			$uploadErrors = array
				(
				0 => lang("There is no error, the file uploaded successfully"),
				1 => lang("The uploaded file exceeds the upload_max_filesize directive in php.ini"),
				2 => lang("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form"),
				3 => lang("The uploaded file was only partially uploaded"),
				4 => lang("No file was uploaded"),
				6 => lang("Missing a temporary folder")
			);

			// Validate the upload
			if (!isset($uploaded_file))
			{
				$file->error = lang("No upload found");
				return false;	
			}
			else if (isset($error) && $error != 0)
			{
				$file->error = $uploadErrors[$error];
				return false;
			}
			else if (!isset($uploaded_file) || !@is_uploaded_file($uploaded_file))
			{
				$file->error = lang("Upload failed is_uploaded_file test.");
				return false;
			}
			else if (!isset($file->name))
			{
				$file->error = lang("File has no name.");
				return false;
			}

			// Validate file extension
			$path_info = pathinfo($file->name);
			$file_extension = $path_info["extension"];
			$is_valid_extension = false;
			foreach ($extension_whitelist as $extension)
			{
				if (strcasecmp($file_extension, $extension) == 0)
				{
					$is_valid_extension = true;
					break;
				}
			}
			
			if (!$is_valid_extension)
			{
				$file->error = lang("Invalid file extension");
				return false;
			}
			
			// Validate the file size (Warning: the largest files supported by this code is 2GB)
			$file_size = $file->size;
			if (!$file_size || $file_size > $max_file_size_in_bytes)
			{
				$file->error = lang("File exceeds the maximum allowed size");
				return false;
			}

			if ($file_size <= 0)
			{
				$file->error = lang("File size outside allowed lower bound");
				return false;
			}

			// Validate file name (for our purposes we'll just remove invalid characters)
			$file_name = preg_replace('/[^' . $valid_chars_regex . ']|\.+$/i', "", basename($file->name));
			if (strlen($file_name) == 0 || strlen($file_name) > $MAX_FILENAME_LENGTH)
			{
				$file->error = lang("Invalid file name");
				return false;
			}
			
			return true;
		}
		

		private function upload_file( $save_path, $uploaded_file, $file)
		{			
			$to_file = "{$this->bofiles->fakebase}/{$save_path}/{$file->name}";

			// Validate that we won't over-write an existing file
			if ($this->bofiles->vfs->file_exists(array(
					'string' => $to_file,
					'relatives' => Array(RELATIVE_NONE)
				)))
			{
				$file->error = lang('This file already exists !');
				return false;
			}

			$receipt = $this->bofiles->create_document_dir($save_path);
			if ($receipt['error'])
			{
				$file->error = $receipt['error'][0]['msg'];
				return false;
			}

			$this->bofiles->vfs->override_acl = 1;
			if ($this->bofiles->vfs->cp(array(
					'from' => $uploaded_file,
					'to' => "{$this->bofiles->fakebase}/{$save_path}/{$file->name}",
					'relatives' => array(RELATIVE_NONE | VFS_REAL, RELATIVE_ALL))))
			{
				return true;
			}
			else
			{
				$file->error = lang('Failed to upload file !');
				return false;
			}

			$this->bofiles->vfs->override_acl = 0;
		}
		
		public function delete_file($print_response = true) 
		{
			$file_names = $this->get_file_names_params();
			if (empty($file_names)) {
				$file_names = array($this->get_file_name_param());
			}
			$response = array();
			foreach ($file_names as $file_name) 
			{
				$file_path = $this->get_upload_path($file_name);
				
				//$check_path = trim("{$this->fakebase}{$path}", "/");

				$file = "{$this->bofiles->fakebase}/{$this->options['base_dir']}/{$file_name}";

				/*if($check_path != trim($file_info['directory'], "/"))
				{
					phpgwapi_cache::message_set( "deleting file from wrong location", 'error');
					return false;
				}*/

				if ($this->bofiles->vfs->file_exists(array(
						'string' => $file,
						'relatives' => array(RELATIVE_NONE)
					)))
				{
					$this->bofiles->vfs->override_acl = 1;

					if (!$this->bofiles->vfs->rm(array(
							'string' => $file,
							'relatives' => array(
								RELATIVE_NONE
							)
						)))
					{
						//phpgwapi_cache::message_set(lang('failed to delete file') . ' :' .$file, 'error');
						$success = false;
					}
					else
					{
						$thumbfile = "{$this->bofiles->rootdir}/{$this->bofiles->fakebase}/{$this->options['base_dir']}/{$file_name}.thumb";

						if(is_file($thumbfile))
						{
							unlink($thumbfile);
						}
						//phpgwapi_cache::message_set(lang('file deleted') . ' :' . $file, 'message');
						$success = true;
					}
					$this->bofiles->vfs->override_acl = 0;
				}
				
				//echo $file_path; die;
				//$success = is_file($file_path) && $file_name[0] !== '.' && unlink($file_path);
				if ($success) {
					foreach ($this->options['image_versions'] as $version => $options) {
						if (!empty($version)) {
							$file = $this->get_upload_path($file_name, $version);
							if (is_file($file)) {
								unlink($file);
							}
						}
					}
				}
				$response[$file_name] = $success;
			}
			return $this->generate_response($response, $print_response);
		}
		
		public function generate_response($_content, $print_response = true) {

			$content = array();
			/**
			 * Filter out thumbs as individual entries
			 */

			if(!empty($_content['files']))
			{
				$content['files'] = array();
				foreach ($_content['files'] as $file)
				{
					if(substr($file->name, -5) == 'thumb')
					{
						continue;
					}
					$content['files'][] = $file;
				}
			}

			$this->response = $content;
			
			if ($print_response) 
			{
				$content_files = scandir($this->options['upload_dir']);
				$count_files = 0;
				foreach($content_files as $key => $value)
				{
					$path = realpath($this->options['upload_dir'].'/'.$value);
					if(is_file($path)) 
					{				
						$count_files ++;
					} 
				}
				$content['num_files'] = $count_files;
			
				$json = json_encode($content);
				$redirect = stripslashes($this->get_post_param('redirect'));
				if ($redirect && preg_match($this->options['redirect_allow_target'], $redirect)) {
					$this->header('Location: '.sprintf($redirect, rawurlencode($json)));
					return;
				}
				$this->head();
				if ($this->get_server_var('HTTP_CONTENT_RANGE')) {
					$files = isset($content[$this->options['param_name']]) ?
						$content[$this->options['param_name']] : null;
					if ($files && is_array($files) && is_object($files[0]) && $files[0]->size) {
						$this->header('Range: 0-'.(
							$this->fix_integer_overflow((int)$files[0]->size) - 1
						));
					}
				}
				$this->body($json);
			}
			return $content;
		}
		
	}