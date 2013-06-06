<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007,2008,2009 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @subpackage admin
 	* @version $Id$
	*/
	phpgw::import_class('phpgwapi.yui');

	/**
	 * Description
	 * @package property
	 */

	class property_uigallery
	{
		var $grants;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $sub;
		var $currentapp;
		var $location_info;

		var $public_functions = array
			(
				'index'		=> true,
				'view_file'	=> true
			);

		function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.bogallery',true);
			$this->bocommon				= CreateObject('property.bocommon');

			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location 		= '.document';
			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage 			= $this->acl->check($this->acl_location, 16, 'property');

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->allrows				= $this->bo->allrows;
			$this->cat_id				= $this->bo->cat_id;
			$this->user_id				= $this->bo->user_id;
			$this->mime_type			= $this->bo->mime_type;
		}

		function save_sessiondata()
		{
			$data = array
				(
					'start'			=> $this->start,
					'query'			=> $this->query,
					'sort'			=> $this->sort,
					'order'			=> $this->order,
					'allrows'		=> $this->allrows,
					'cat_id'		=> $this->cat_id,
					'user_id'		=> $this->user_id,
					'mime_type'		=> $this->mime_type
				);
			$this->bo->save_sessiondata($data);
		}

		private function get_external_source($file, $thumb)
		{
			$file = ltrim($file,'external_source/');
			
			$url = "bkbilde.bergen.kommune.no/fotoweb/cmdrequest/rest/PreviewAgent.fwx?ar=5008&rs=0&pg=0&username=FDV&password=FDV123&sr={$file}*";
			
			if($thumb)
			{
				$url .= '&sz=50';
			}
			else
			{
				header('Content-Type: image/jpeg');			
			}

/*
			$file = 'http://somehosted.com/file.pdf'; // URL to the file

			$contents = file_get_contents($file); // read the remote file
*/

			$ch = curl_init ($url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
			$raw=curl_exec($ch);
			curl_close ($ch);
			echo $raw;

		}
		
		function view_file()
		{
			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$file	= urldecode(phpgw::get_var('file'));
			$thumb	= phpgw::get_var('thumb', 'bool');


			$directory = explode('/', $file);
			
			if($directory[0] == 'external_source')
			{
				return $this->get_external_source($file, $thumb);
			}

			$location_info = $this->bo->get_location($directory);

			if(!$this->acl->check($location_info['location'], PHPGW_ACL_READ, 'property'))
			{
				echo 'sorry - no access';
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$bofiles	= CreateObject('property.bofiles');
			$source = "{$bofiles->rootdir}{$file}";
			$thumbfile = "$source.thumb";

			// prevent path traversal
			if ( preg_match('/\.\./', $source) )
			{
				return false;
			}

			$re_create= false;
			if($this->is_image($source) && $thumb && $re_create)
			{
				$this->create_thumb($source,$thumbfile,$thumb_size = 100);
				readfile($thumbfile);
			}
			else if($thumb && is_file($thumbfile))
			{
				readfile($thumbfile);
			}
			else if($this->is_image($source) && $thumb)
			{
				$this->create_thumb($source,$thumbfile,$thumb_size = 100);
				readfile($thumbfile);
			}
			else
			{
				$bofiles->view_file('', $file);
			}
		}

		function create_thumb($source,$dest,$target_height = 100)
		{
			$size = getimagesize($source);
			$width = $size[0];
			$height = $size[1];

			$target_width = round($width*($target_height/$height));

			if ($width > $height)
			{
				$x = ceil(($width - $height) / 2 );
				$width = $height;
			}
			else if($height > $width)
			{
				$y = ceil(($height - $width) / 2);
				$height = $width;
			}

			$new_im = ImageCreatetruecolor($target_width,$target_height);

			@$imgInfo = getimagesize($source);

			if ($imgInfo[2] == IMAGETYPE_JPEG)
			{
				$im = imagecreatefromjpeg($source);
				imagecopyresampled($new_im,$im,0,0,$x,$y,$target_width,$target_height,$width,$height);
				imagejpeg($new_im,$dest,75); // Thumbnail quality (Value from 1 to 100)
			}
			else if ($imgInfo[2] == IMAGETYPE_GIF)
			{
				$im = imagecreatefromgif($source);
				imagecopyresampled($new_im,$im,0,0,$x,$y,$target_width,$target_height,$width,$height);
				imagegif($new_im,$dest);
			}
			else if ($imgInfo[2] == IMAGETYPE_PNG)
			{
				$im = imagecreatefrompng($source);
				imagecopyresampled($new_im,$im,0,0,$x,$y,$target_width,$target_height,$width,$height);
				imagepng($new_im,$dest);
			}
		}

		function is_image($fileName)
		{
			// Verifies that a file is an image
			if ($fileName !== '.' && $fileName !== '..')
			{
				@$imgInfo = getimagesize($fileName);

				$imgType = array
					(
						IMAGETYPE_JPEG,
						IMAGETYPE_GIF,
						IMAGETYPE_PNG,
					);

				if (in_array($imgInfo[2],$imgType))
				{
					return true;
				}
				return false;
			}
		}

		function index()
		{
			//_debug_array($_REQUEST);
			$this->acl_location = '.document';
			if (!$this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property') )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage 			= $this->acl->check($this->acl_location, 16, 'property');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "property::documentation::gallery";

			$this->save_sessiondata();

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'	=> 'property.uigallery.index',
						'mime_type'		=> $this->mime_type,
						'cat_id'		=> $this->cat_id,
						'query'			=> $this->query,
						'allrows'		=> $this->allrows,
						'user_id'		=> $this->user_id
					));

				$datatable['config']['base_java_url'] = "menuaction:'property.uigallery.index',"
					."mime_type:'{$this->mime_type}',"
					."cat_id:'{$this->cat_id}',"
					."query:'{$this->query}',"
					."allrows:'{$this->allrows}',"
					."user_id:'{$this->user_id}'";

				$values_combo_box = array();

				$values_combo_box[0]  = $this->bo->get_filetypes();
				$default_value = array ('id'=> '', 'name'=>lang('no filetype'));
				array_unshift ($values_combo_box[0],$default_value);

				$values_combo_box[1]  = $this->bo->get_gallery_location();
				$default_value = array ('id'=> '', 'name'=>lang('no category'));
				array_unshift ($values_combo_box[1],$default_value);

				$values_combo_box[2]  = $this->bocommon->get_user_list_right2('filter',2,$this->user_id,$this->acl_location);
				array_unshift ($values_combo_box[2],array('id'=>$GLOBALS['phpgw_info']['user']['account_id'],'name'=>lang('mine documents')));
				$default_value = array('id'=>'','name'=>lang('no user'));
				array_unshift ($values_combo_box[2],$default_value);

				$datatable['config']['allow_allrows'] = true;

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction'	=> 'property.uigallery.index',
								'type'			=> $type,
								'type_id'		=> $type_id
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( //boton 	filetype
									'id' => 'btn_mime_type',
									'name' => 'mime_type',
									'value'	=> lang('filetype'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 1
								),
								array
								( //boton 	CATEGORY
									'id' => 'btn_cat_id',
									'name' => 'cat_id',
									'value'	=> lang('Category'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 2
								),
								array
								( //boton 	USER
									'id' => 'btn_user_id',
									'name' => 'user_id',
									'value'	=> lang('User'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 3
								),
/*
								array
								( // boton SAVE
									'id'	=> 'btn_save',
									//'name' => 'save',
									'value'	=> lang('save'),
									'tab_index' => 7,
									'type'	=> 'button'
									),
 */
								array
								( //hidden start_date
									'type' => 'hidden',
									'id' => 'start_date',
									'value' => $start_date
								),
								array
								( //hidden end_date
									'type' => 'hidden',
									'id' => 'end_date',
									'value' => $end_date
								),
								array
								(//for link "None",
									'type'=> 'label_date'
								),
								array
								(//for link "Date search",
									'type'=> 'link',
									'id'  => 'btn_data_search',
									'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
									array(
										'menuaction' => 'property.uiproject.date_search'))."','','left=50,top=100,width=350,height=250')",
										'value' => lang('Date search'),
										'tab_index' => 6
									),
									array
									( //button     SEARCH
										'id' => 'btn_search',
										'name' => 'search',
										'value'    => lang('search'),
										'type' => 'button',
										'tab_index' => 5
									),
									array
									( // TEXT INPUT
										'name'     => 'query',
										'id'     => 'txt_query',
										'value'    => $this->query,
										'type' => 'text',
										'onkeypress' => 'return pulsar(event)',
										'size'    => 28,
										'tab_index' => 4
									),
									array
									( //place holder for selected events
										'type'	=> 'hidden',
										'id'	=> 'event',
										'value'	=> ''
									)
								),
								'hidden_value' => array
								(
									array
									( //div values  combo_box_0
										'id' => 'values_combo_box_0',
										'value'	=> $this->bocommon->select2String($values_combo_box[0])
									),
									array
									( //div values  combo_box_1
										'id' => 'values_combo_box_1',
										'value'	=> $this->bocommon->select2String($values_combo_box[1])
									),
									array
									( //div values  combo_box_2
										'id' => 'values_combo_box_2',
										'value'	=> $this->bocommon->select2String($values_combo_box[2])
									)
								)
							)
						)
					);				
				$dry_run = true;
			}

			$values = $this->bo->read($dry_run);
			$uicols = array();$this->bo->uicols;

			$uicols['name'][]		= 'img_id';
			$uicols['descr'][]		= 'dummy';
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= 'hidden';

			$uicols['name'][]		= 'directory';
			$uicols['descr'][]		= 'directory';
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= '';//'hidden';

			$uicols['name'][]		= 'id';
			$uicols['descr'][]		= lang('id');
			$uicols['sortable'][]	= true;
			$uicols['sort_field'][]	= 'id';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= '';

			$uicols['name'][]		= 'date';
			$uicols['descr'][]		= lang('date');
			$uicols['sortable'][]	= true;
			$uicols['sort_field'][]	= 'date';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= '';

			$uicols['name'][]		= 'name';
			$uicols['descr'][]		= lang('name');
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= '';

			$uicols['name'][]		= 'size';
			$uicols['descr'][]		= lang('size');
			$uicols['sortable'][]	= true;
			$uicols['sort_field'][]	= 'size';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= '';

			$uicols['name'][]		= 'location_name';
			$uicols['descr'][]		= lang('location name');
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= '';

			$uicols['name'][]		= 'url';
			$uicols['descr'][]		= lang('url');
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= 'link';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= '';

			$uicols['name'][]		= 'document_url';
			$uicols['descr'][]		= lang('document');
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= 'link';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= '';

			$uicols['name'][]		= 'user';
			$uicols['descr'][]		= lang('user');
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= '';

			$uicols['name'][]		= 'picture';
			$uicols['descr'][]		= lang('picture');
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= 'show_picture';
			$uicols['input_type'][]	= '';

/*
			$uicols['name'][]		= 'select';
			$uicols['descr'][]		= lang('select');
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= 'myFormatterCheck';
			$uicols['input_type'][]	= '';
 */
			$j = 0;
			$count_uicols_name = count($uicols['name']);

			foreach($values as $entry)
			{
				for ($k=0;$k<$count_uicols_name;$k++)
				{
					$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols['name'][$k];
					$datatable['rows']['row'][$j]['column'][$k]['value']			= $entry[$uicols['name'][$k]];
					if($uicols['format'][$k]=='link' &&  $entry[$uicols['name'][$k]])
					{
						$datatable['rows']['row'][$j]['column'][$k]['format'] 		= 'link';
						$datatable['rows']['row'][$j]['column'][$k]['value']		= lang('link');
						$datatable['rows']['row'][$j]['column'][$k]['link']			= $entry[$uicols['name'][$k]];
						$datatable['rows']['row'][$j]['column'][$k]['target']	   = '_blank';
					}
				}
				$j++;
			}

			$datatable['rowactions']['action'] = array();
/*
			$parameters = array
			(
				'parameter' => array
				(
					array
					(
						'name'		=> 'location',
						'source'	=> 'location'
					),
					array
					(
						'name'		=> 'attrib_id',
						'source'	=> 'attrib_id'
					),
					array
					(
						'name'		=> 'item_id',
						'source'	=> 'location_item_id'
					),
					array
					(
						'name'		=> 'id',
						'source'	=> 'id'
					)
				)
			);

			if($this->acl_edit)
			{
				$datatable['rowactions']['action'][] = array
				(
					'my_name'		=> 'edit',
					'text' 			=> lang('edit serie'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'		=> 'property.uigallery.edit',
											'type'				=> $type,
											'type_id'			=> $type_id,
											'target'			=> '_blank'
										)),
					'parameters'	=> $parameters
				);
			}


			if($this->acl_delete)
			{
				$datatable['rowactions']['action'][] = array
				(
					'my_name' 		=> 'delete',
					'statustext' 	=> lang('delete the actor'),
					'text'			=> lang('delete'),
					'confirm_msg'	=> lang('do you really want to delete this entry'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uigallery.delete',
											'type'			=> $type,
											'type_id'		=> $type_id
										)),
					'parameters'	=> $parameters
				);
			}
 */
			unset($parameters);

			if($this->acl_add)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name' 			=> 'add',
						'statustext' 	=> lang('add'),
						'text'			=> lang('add'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uigallery.edit',
							'type'			=> $type,
							'type_id'		=> $type_id
						))
					);
			}

			for ($i=0;$i<$count_uicols_name;$i++)
			{
				$datatable['headers']['header'][$i]['formatter'] 		= $uicols['formatter'][$i] ? $uicols['formatter'][$i] : '""';
				$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
				$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
				$datatable['headers']['header'][$i]['visible'] 			= $uicols['input_type'][$i]!='hidden';
				$datatable['headers']['header'][$i]['sortable']			= $uicols['sortable'][$i];
				$datatable['headers']['header'][$i]['sort_field']   	= $uicols['sort_field'][$i];
				$datatable['headers']['header'][$i]['format'] 			= $uicols['format'][$i];
			}

			//path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];

			if($dry_run)
			{
				$datatable['pagination']['records_returned'] = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];			
			}
			else
			{
				$datatable['pagination']['records_returned']= count($values);
			}

			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$appname			= lang('gallery');
			$function_msg		= lang('list pictures');

			if ( ($this->start == 0) && (!$this->order))
			{
				$datatable['sorting']['order'] 			= 'date'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'asc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= $this->order; // name of column of Database
				$datatable['sorting']['sort'] 			= $this->sort; // ASC / DESC
			}

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			//			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');

			//-- BEGIN----------------------------- JSON CODE ------------------------------
			//values for Pagination
			$json = array
				(
					'recordsReturned' 	=> $datatable['pagination']['records_returned'],
					'totalRecords' 		=> (int)$datatable['pagination']['records_total'],
					'startIndex' 		=> $datatable['pagination']['records_start'],
					'sort'				=> $datatable['sorting']['order'],
					'dir'				=> $datatable['sorting']['sort'],
					'records'			=> array()
				);

			// values for datatable
			if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row']))
			{
				foreach( $datatable['rows']['row'] as $row )
				{
					$json_row = array();
					foreach( $row['column'] as $column)
					{
						if(isset($column['format']) && $column['format']== "link" && $column['java_link']==true)
						{
							$json_row[$column['name']] = "<a href='#' id='".$column['link']."' onclick='javascript:filter_data(this.id);'>" .$column['value']."</a>";
						}
						else if(isset($column['format']) && $column['format']== "link")
						{
							$json_row[$column['name']] = "<a href='".$column['link']."' target='_blank'>" .$column['value']."</a>";
							//			$json_row[$column['name']] = "<a href='{$column['value']}' title='test' id='img-0' rel='colorbox' target='_blank'><img src='{$column['value']}&thumb=1'  alt='name' /></a>";
						}
						else
						{
							$json_row[$column['name']] = $column['value'];
						}
					}
					$json['records'][] = $json_row;
				}
			}

			// right in datatable
			if(isset($datatable['rowactions']['action']) && is_array($datatable['rowactions']['action']))
			{
				$json ['rights'] = $datatable['rowactions']['action'];
			}

			if(isset($receipt) && is_array($receipt) && count($receipt))
			{
				$json['message'][] = $receipt;
			}

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				return $json;
			}

			$datatable['json_data'] = json_encode($json);
			//-------------------- JSON CODE ----------------------

			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

			if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}

			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . "::{$appname}::{$function_msg}";

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'gallery.index', 'property' );

			//FIXME: have a look at this one: http://thecodecentral.com/2008/01/01/yui-based-lightbox-final
			//			$GLOBALS['phpgw']->js->validate_file( 'jquery', 'jquery.min', 'property' );
			//			$GLOBALS['phpgw']->js->validate_file( 'jquery', 'jquery.colorbox', 'property' );
			//			$GLOBALS['phpgw']->js->validate_file( 'jquery', 'gallery.index', 'property' );
		}
	}
