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
	phpgw::import_class('phpgwapi.uicommon_jquery');

	/**
	 * Description
	 * @package property
	 */
	class property_uigallery extends phpgwapi_uicommon_jquery
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
			'query'		 => true,
			'index'		 => true,
			'view_file'	 => true
		);

		function __construct()
		{
			parent::__construct();

			$this->account	 = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo		 = CreateObject('property.bogallery', true);
			$this->bocommon	 = CreateObject('property.bocommon');

			$this->acl			 = & $GLOBALS['phpgw']->acl;
			$this->acl_location	 = '.document';
			$this->acl_read		 = $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add		 = $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit		 = $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete	 = $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage	 = $this->acl->check($this->acl_location, 16, 'property');

			$this->start	 = $this->bo->start;
			$this->query	 = $this->bo->query;
			$this->sort		 = $this->bo->sort;
			$this->order	 = $this->bo->order;
			$this->allrows	 = $this->bo->allrows;
			$this->cat_id	 = $this->bo->cat_id;
			$this->user_id	 = $this->bo->user_id;
			$this->mime_type = $this->bo->mime_type;
		}

		function save_sessiondata()
		{
			$data = array
				(
				'start'		 => $this->start,
				'query'		 => $this->query,
				'sort'		 => $this->sort,
				'order'		 => $this->order,
				'allrows'	 => $this->allrows,
				'cat_id'	 => $this->cat_id,
				'user_id'	 => $this->user_id,
				'mime_type'	 => $this->mime_type
			);
			$this->bo->save_sessiondata($data);
		}

		private function get_external_source( $file, $thumb )
		{
			$file = ltrim($file, 'external_source/');

			$url = "bkbilde.bergen.kommune.no/fotoweb/cmdrequest/rest/PreviewAgent.fwx?ar=5008&rs=0&pg=0&username=FDV&password=FDV123&sr={$file}*";

			if ($thumb)
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

			$ch	 = curl_init($url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
			$raw = curl_exec($ch);
			curl_close($ch);
			echo $raw;
		}

		function view_file()
		{
			$GLOBALS['phpgw_info']['flags']['noheader']	 = true;
			$GLOBALS['phpgw_info']['flags']['nofooter']	 = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app']	 = false;

			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 1, 'acl_location'	 => $this->acl_location));
			}

			$file	 = urldecode(phpgw::get_var('file'));
			$thumb	 = phpgw::get_var('thumb', 'bool');


			$directory = explode('/', $file);

			if ($directory[0] == 'external_source')
			{
				return $this->get_external_source($file, $thumb);
			}

			$location_info = $this->bo->get_location($directory);
			if (!$this->acl->check($location_info['location'], PHPGW_ACL_READ, 'property'))
			{
				echo 'sorry - no access';
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$img_id = phpgw::get_var('img_id', 'int');

			$bofiles = CreateObject('property.bofiles');

			if ($img_id)
			{
				$file_info	 = $bofiles->vfs->get_info($img_id);
				$file		 = "{$file_info['directory']}/{$file_info['name']}";
			}



			$source		 = "{$bofiles->rootdir}{$file}";
			$thumbfile	 = "$source.thumb";

			// prevent path traversal
			if (preg_match('/\.\./', $source))
			{
				return false;
			}

			$re_create = false;
			if ($this->is_image($source) && $thumb && $re_create)
			{
				$this->create_thumb($source, $thumbfile, $thumb_size = 100);
				readfile($thumbfile);
			}
			else if ($thumb && is_file($thumbfile))
			{
				readfile($thumbfile);
			}
			else if ($this->is_image($source) && $thumb)
			{
				$this->create_thumb($source, $thumbfile, $thumb_size = 100);
				readfile($thumbfile);
			}
			else if ($img_id)
			{
				$bofiles->get_file($img_id);
			}
			else
			{
				$bofiles->view_file('', $file);
			}
		}

		function create_thumb( $source, $dest, $target_height = 100 )
		{
			$size	 = getimagesize($source);
			$width	 = $size[0];
			$height	 = $size[1];

			$target_width = round($width * ($target_height / $height));

			if ($width > $height)
			{
				$x		 = ceil(($width - $height) / 2);
				$width	 = $height;
			}
			else if ($height > $width)
			{
				$y		 = ceil(($height - $width) / 2);
				$height	 = $width;
			}

			$new_im = ImageCreatetruecolor($target_width, $target_height);

			@$imgInfo = getimagesize($source);

			if ($imgInfo[2] == IMAGETYPE_JPEG)
			{
				$im = imagecreatefromjpeg($source);
				imagecopyresampled($new_im, $im, 0, 0, $x, $y, $target_width, $target_height, $width, $height);
				imagejpeg($new_im, $dest, 75); // Thumbnail quality (Value from 1 to 100)
			}
			else if ($imgInfo[2] == IMAGETYPE_GIF)
			{
				$im = imagecreatefromgif($source);
				imagecopyresampled($new_im, $im, 0, 0, $x, $y, $target_width, $target_height, $width, $height);
				imagegif($new_im, $dest);
			}
			else if ($imgInfo[2] == IMAGETYPE_PNG)
			{
				$im = imagecreatefrompng($source);
				imagecopyresampled($new_im, $im, 0, 0, $x, $y, $target_width, $target_height, $width, $height);
				imagepng($new_im, $dest);
			}
		}

		function is_image( $fileName )
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

				if (in_array($imgInfo[2], $imgType))
				{
					return true;
				}
				return false;
			}
		}

		private function _get_Filters()
		{
			$values_combo_box	 = array();
			$combos				 = array();

			$values_combo_box[0] = $this->bo->get_filetypes();
			$default_value		 = array('id' => '', 'name' => lang('no filetype'));
			array_unshift($values_combo_box[0], $default_value);
			$combos[]			 = array
				(
				'type'	 => 'filter',
				'name'	 => 'mime_type',
				'text'	 => lang('Filetype'),
				'list'	 => $values_combo_box[0]
			);

			$values_combo_box[1] = $this->bo->get_gallery_location();
			$default_value		 = array('id' => '', 'name' => lang('no category'));
			array_unshift($values_combo_box[1], $default_value);
			$combos[]			 = array
				(
				'type'	 => 'filter',
				'name'	 => 'cat_id',
				'text'	 => lang('Category'),
				'list'	 => $values_combo_box[1]
			);

			$values_combo_box[2] = $this->bocommon->get_user_list_right2('filter', 2, $this->user_id, $this->acl_location);
			array_unshift($values_combo_box[2], array('id'	 => $GLOBALS['phpgw_info']['user']['account_id'],
				'name'	 => lang('mine documents')));
			$default_value		 = array('id' => '', 'name' => lang('no user'));
			array_unshift($values_combo_box[2], $default_value);
			$combos[]			 = array
				(
				'type'	 => 'filter',
				'name'	 => 'user_id',
				'text'	 => lang('User'),
				'list'	 => $values_combo_box[2]
			);

			return $combos;
		}

		function index()
		{
			$this->acl_location = '.document';
			if (!$this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 1, 'acl_location'	 => $this->acl_location));
			}

			$this->acl_read		 = $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add		 = $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit		 = $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete	 = $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage	 = $this->acl->check($this->acl_location, 16, 'property');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "property::documentation::gallery";

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$GLOBALS['phpgw']->jqcal->add_listener('filter_start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('filter_end_date');
			phpgwapi_jquery::load_widget('datepicker');

			$appname		 = lang('gallery');
			$function_msg	 = lang('list pictures');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . "::{$appname}::{$function_msg}";

			$data	 = array(
				'datatable_name' => $appname,
				'form'			 => array(
					'toolbar' => array(
						'item' => array(
							array
								(
								'type'	 => 'date-picker',
								'id'	 => 'start_date',
								'name'	 => 'start_date',
								'value'	 => '',
								'text'	 => lang('from')
							),
							array
								(
								'type'	 => 'date-picker',
								'id'	 => 'end_date',
								'name'	 => 'end_date',
								'value'	 => '',
								'text'	 => lang('to')
							)
						)
					)
				),
				'datatable'		 => array(
					'source'		 => self::link(array(
						'menuaction'		 => 'property.uigallery.index',
						'mime_type'			 => $this->mime_type,
						'cat_id'			 => $this->cat_id,
						'user_id'			 => $this->user_id,
						'phpgw_return_as'	 => 'json'
					)),
					'allrows'		 => true,
					'editor_action'	 => '',
					'field'			 => array(
						array(
							'key'		 => 'img_id',
							'label'		 => lang('dummy'),
							'sortable'	 => true,
							'hidden'	 => true
						),
						array(
							'key'		 => 'directory',
							'label'		 => lang('directory'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'id',
							'label'		 => lang('id'),
							'sortable'	 => true
						),
						array(
							'key'		 => 'date',
							'label'		 => lang('date'),
							'sortable'	 => true
						),
						array(
							'key'		 => 'name',
							'label'		 => lang('name'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'size',
							'label'		 => lang('size'),
							'sortable'	 => true
						),
						array(
							'key'		 => 'location_name',
							'label'		 => lang('location name'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'url',
							'label'		 => lang('url'),
							'sortable'	 => false,
							'formatter'	 => 'JqueryPortico.formatLinkGallery'
						),
						array(
							'key'		 => 'document_url',
							'label'		 => lang('document'),
							'sortable'	 => false,
							'formatter'	 => 'JqueryPortico.formatLinkGallery'
						),
						array(
							'key'		 => 'user',
							'label'		 => lang('user'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'picture',
							'label'		 => lang('picture'),
							'sortable'	 => false,
							'formatter'	 => 'JqueryPortico.showPicture'
						)
					)
				)
			);
			$filters = $this->_get_Filters();
			foreach ($filters as $filter)
			{
				array_unshift($data['form']['toolbar']['item'], $filter);
			}

			$values = $this->bo->read(array('dry_run' => true));

			unset($parameters);
			$data['datatable']['actions'][] = array();

			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query()
		{
			$start_date	 = urldecode(phpgw::get_var('start_date'));
			$end_date	 = urldecode(phpgw::get_var('end_date'));

			$search	 = phpgw::get_var('search');
			$order	 = phpgw::get_var('order');
			$draw	 = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');
			$export	 = phpgw::get_var('export', 'bool');

			if ($start_date && empty($end_date))
			{
				$dateformat	 = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
				$end_date	 = $GLOBALS['phpgw']->common->show_date(mktime(0, 0, 0, date("m"), date("d"), date("Y")), $dateformat);
			}

			$params = array(
				'start'			 => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results'		 => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query'			 => $search['value'],
				'order'			 => $columns[$order[0]['column']]['data'],
				'sort'			 => $order[0]['dir'],
				'allrows'		 => phpgw::get_var('length', 'int') == -1 || $export,
				'location_id'	 => $this->location_id,
				'user_id'		 => $this->user_id,
				'mime_type'		 => $this->mime_type,
				'cat_id'		 => $this->cat_id,
				'start_date'	 => $start_date,
				'end_date'		 => $end_date
			);

			$result_objects	 = array();
			$result_count	 = 0;

			$values = $this->bo->read($params);

			if ($export)
			{
				return $values;
			}

			$result_data					 = array('results' => $values);
			$result_data['total_records']	 = $this->bo->total_records;
			$result_data['draw']			 = $draw;

			return $this->jquery_results($result_data);
		}
	}