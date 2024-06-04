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
	 * @version $Id$
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('phpgwapi.jquery');
	phpgw::import_class('phpgwapi.phpqrcode');

	class property_uiqr_generator extends phpgwapi_uicommon_jquery
	{

		var $acl_location, $acl_read;
		public $public_functions = array
		(
			'index'					 => true,
		);

		public function __construct()
		{
			parent::__construct();

			$this->acl			 = & $GLOBALS['phpgw']->acl;
			$this->acl_location	 = '.project';
			$this->acl_read		 = $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');

			$GLOBALS['phpgw_info']['flags']['menu_selection']	 = 'admin::property::qr_generator';

		}


		/**
		 * Fetch data from $this->bo based on parametres
		 * @return array
		 */
		public function query()
		{
		}


		public function index()
		{
			$this->_handle_import();
		}

		/**
		 * Import deviations found in the survey to the database from a spreadsheet
		 *
		 * @param int  $id  entity id
		 *
		 * @return void
		 */
		private function _handle_import(  )
		{
			phpgwapi_jquery::formvalidator_generate(array('file'));

			$step		 = phpgw::get_var('step', 'int', 'REQUEST');
			$sheet_id	 = phpgw::get_var('sheet_id', 'int', 'REQUEST');

			$sheet_id = $sheet_id ? $sheet_id : phpgw::get_var('selected_sheet_id', 'int', 'REQUEST');

			if (!$step)
			{
				if ($cached_file = phpgwapi_cache::session_get('property', 'qr_generator_import_file'))
				{
					phpgwapi_cache::session_clear('property', 'qr_generator_import_file');
					unlink($cached_file);
					unset($cached_file);
				}
			}

			if ($start_line = phpgw::get_var('start_line', 'int', 'REQUEST'))
			{
				phpgwapi_cache::system_set('property', 'import_sheet_start_line', $start_line);
			}
			else
			{
				$start_line	 = phpgwapi_cache::system_get('property', 'import_sheet_start_line');
				$start_line	 = $start_line ? $start_line : 1;
			}


			if ($columns = phpgw::get_var('columns'))
			{
				phpgwapi_cache::system_set('property', 'import_sheet_columns', $columns);
			}
			else
			{
				$columns = phpgwapi_cache::system_get('property', 'import_sheet_columns');
				$columns = $columns && is_array($columns) ? $columns : array();
			}


			if ($step > 1)
			{
				$cached_file = phpgwapi_cache::session_get('property', 'qr_generator_import_file');
			}

			if ($step == 1 || isset($_FILES['import_file']['tmp_name']))
			{
				$file		 = $_FILES['import_file']['tmp_name'];
				$cached_file = "{$file}_temporary_import_file";
				// save a copy to survive multiple steps
				file_put_contents($cached_file, file_get_contents($file));
				phpgwapi_cache::session_set('property', 'qr_generator_import_file', $cached_file);
				$step		 = 1;

			}

			$tabs = array();

			switch ($step)
			{
				case 0:
					$active_tab		 = 'step_1';
					$lang_submit	 = lang('continue');
					$tabs['step_1']	 = array('label' => lang('choose file'), 'link' => '#step_1');
					$tabs['step_2']	 = array('label' => lang('choose sheet'), 'link' => null, 'disable' => true);
					$tabs['step_3']	 = array('label'		 => lang('choose start line'), 'link'		 => null,
						'disable'	 => true);
					$tabs['step_4']	 = array('label'		 => lang('choose columns'), 'link'		 => null,
						'disable'	 => true);
					$tabs['step_5']	 = array('label'		 => lang('completed'), 'link'		 => null,
						'disable'	 => true);
					break;
				case 1:
					$active_tab		 = 'step_2';
					$lang_submit	 = lang('continue');
					$tabs['step_1']	 = array('label'	 => lang('choose file'), 'link'	 => self::link(array(
							'menuaction' => 'property.uiqr_generator.index', 'step'		 => 0,
							'sheet_id'	 => $sheet_id, 'start_line' => $start_line)));
					$tabs['step_2']	 = array('label' => lang('choose sheet'), 'link' => '#step_2');
					$tabs['step_3']	 = array('label'		 => lang('choose start line'), 'link'		 => null,
						'disable'	 => true);
					$tabs['step_4']	 = array('label'		 => lang('choose columns'), 'link'		 => null,
						'disable'	 => true);
					$tabs['step_5']	 = array('label'		 => lang('completed'), 'link'		 => null,
						'disable'	 => true);
					break;
				case 2:
					$active_tab		 = 'step_3';
					$lang_submit	 = lang('continue');
					$tabs['step_1']	 = array('label'	 => lang('choose file'), 'link'	 => self::link(array(
							'menuaction' => 'property.uiqr_generator.index', 'step'		 => 0,
							'sheet_id'	 => $sheet_id, 'start_line' => $start_line)));
					$tabs['step_2']	 = array('label'	 => lang('choose sheet'), 'link'	 => self::link(array(
							'menuaction' => 'property.uiqr_generator.index', 'step'		 => 1,
							'sheet_id'	 => $sheet_id, 'start_line' => $start_line)));
					$tabs['step_3']	 = array('label' => lang('choose start line'), 'link' => '#step_3');
					$tabs['step_4']	 = array('label'		 => lang('choose columns'), 'link'		 => null,
						'disable'	 => true);
					$tabs['step_5']	 = array('label'		 => lang('completed'), 'link'		 => null,
						'disable'	 => true);
					break;
				case 3:
					$active_tab		 = 'step_4';
					$lang_submit	 = lang('import');
					$tabs['step_1']	 = array('label'	 => lang('choose file'), 'link'	 => self::link(array(
							'menuaction' => 'property.uiqr_generator.index', 'step'		 => 0,
							'sheet_id'	 => $sheet_id, 'start_line' => $start_line)));
					$tabs['step_2']	 = array('label'	 => lang('choose sheet'), 'link'	 => self::link(array(
							'menuaction' => 'property.uiqr_generator.index', 'step'		 => 1,
							'sheet_id'	 => $sheet_id, 'start_line' => $start_line)));
					$tabs['step_3']	 = array('label'	 => lang('choose start line'), 'link'	 => self::link(array(
							'menuaction' => 'property.uiqr_generator.index', 'step'		 => 2,
							'sheet_id'	 => $sheet_id, 'start_line' => $start_line)));
					$tabs['step_4']	 = array('label' => lang('choose columns'), 'link' => '#step_4');
					$tabs['step_5']	 = array('label'		 => lang('completed'), 'link'		 => null,
						'disable'	 => true);
					break;
				case 4:
					$active_tab		 = 'step_5';
					$lang_submit	 = '';
					$tabs['step_1']	 = array('label' => lang('choose file'), 'link' => null, 'disable'	 => true);
					$tabs['step_2']	 = array('label'	 => lang('choose sheet'), 'link' => null, 'disable'	 => true);
					$tabs['step_3']	 = array('label'	 => lang('choose start line'), 'link' => null, 'disable'	 => true);
					$tabs['step_4']	 = array('label' => lang('choose columns'), 'link' => null, 'disable'	 => true);
					$tabs['step_5']	 = array('label' => lang('completed'), 'link' => '#step_5');
					break;
			}

//-----------
			$convert_data = array();

			if (!$step)
			{
				phpgwapi_cache::session_clear('property', 'qr_generator_import_file');
				if($cached_file)
				{
					unlink($cached_file);
				}
			}
			else if ($cached_file)
			{
				phpgw::import_class('phpgwapi.phpspreadsheet');

				try
				{

					$inputFileType	 = \PhpOffice\PhpSpreadsheet\IOFactory::identify($cached_file);
					$reader			 = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
					$reader->setReadDataOnly(true);
					$spreadsheet	 = $reader->load($cached_file);
					$AllSheets		 = $spreadsheet->getSheetNames();

					$sheets = array();
					if ($AllSheets)
					{
						foreach ($AllSheets as $key => $sheet)
							$sheets[] = array(
								'id'		 => $key,
								'name'		 => $sheet,
								'selected'	 => $sheet_id == $key
							);
					}

					$spreadsheet->setActiveSheetIndex((int)$sheet_id);
					$rows				 = $spreadsheet->getActiveSheet()->getHighestRow();
					$highestColumn		 = $spreadsheet->getActiveSheet()->getHighestColumn($start_line);
					$highestColumnIndex	 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
				}
				catch (Exception $e)
				{
					if ($e)
					{
						phpgwapi_cache::message_set($e->getMessage(), 'error');
						phpgwapi_cache::session_clear('property', 'qr_generator_import_file');
						unlink($cached_file);
					}
				}
			}

			$i			 = 0;
			$html_table	 = '<table class="pure-table pure-table-bordered">';
			if ($rows > 1 && $step == 2)
			{

				$cols = array();
				for ($j = 1; $j <= $highestColumnIndex; $j++)
				{
					$cols[] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($j);
				}

				$html_table .= "<thead><tr><th align = 'center'>" . lang('select') . "</th><th align = 'center'>" . lang('row') . "</th><th align='center'>" . implode("</th><th align='center'>", $cols) . '</th></tr></thead>';
				foreach ($spreadsheet->getActiveSheet()->getRowIterator() as $row)
				{
					if ($i > 20)
					{
						break;
					}
					$i++;

					$row_key	 = $i;
					$_checked	 = '';
					if ($start_line == $row_key)
					{
						$_checked = 'checked="checked"';
					}

					$_radio = "<input id=\"start_line\" type =\"radio\" {$_checked} name=\"start_line\" value=\"{$row_key}\">";

					$cellIterator = $row->getCellIterator();
					$cellIterator->setIterateOnlyExistingCells(false);

					$row_values = array();
					foreach ($cellIterator as $cell)
					{
						if (!is_null($cell))
						{
							$row_values[] = nl2br($cell->getCalculatedValue());
						}
					}
					$html_table .= "<tr><td>{$_radio}</td><td>{$row_key}</td><td>" . implode('</td><td>', $row_values) . '</td></tr>';
				}
				echo '</table>';
			}
			else if ($rows > 1 && $step == 3)
			{
				$_options = array(
					'_skip_import_'		 => 'Utelates fra import',
					'qr_input'			 => 'QR-input',
				);

				phpgw::import_class('phpgwapi.sbox');

				for ($j = 1; $j <= $highestColumnIndex; $j++)
				{
					$_column	 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($j);
					$_value		 = nl2br($spreadsheet->getActiveSheet()->getCellByColumnAndRow($j, $start_line)->getCalculatedValue());
					$selected	 = isset($columns[$_column]) && $columns[$_column] ? $columns[$_column] : '';

					$_listbox	 = phpgwapi_sbox::getArrayItem("columns[{$_column}]", $selected, $_options, true);
					$html_table	 .= "<tr><td>[{$_column}] {$_value}</td><td>{$_listbox}</td><tr>";
				}
			}
			else if ($rows > 1 && $step == 4)
			{

				$rows	 = $spreadsheet->getActiveSheet()->getHighestDataRow();
				$rows	 = $rows ? $rows : 1;


				for ($i = $start_line +1; $i <= $rows; $i++)
				{
					$_result = array();

					foreach ($columns as $_row_key => $_value_key)
					{
						if ($_value_key != '_skip_import_')
						{
							$_result[$_value_key] = $spreadsheet->getActiveSheet()->getCell("{$_row_key}{$i}")->getCalculatedValue();
						}
					}
					$convert_data[] = $_result;
				}
				if ($convert_data)
				{
					try
					{
						$this->_get_qr($convert_data);
					}
					catch (Exception $e)
					{
						if ($e)
						{
							phpgwapi_cache::message_set($e->getMessage(), 'error');
						}
					}
				}

			}

			$html_table .= '</table>';


			$data = array(
				'lang_submit'	 => $lang_submit,
				'step'			 => $step + 1,
				'sheet_id'		 => $sheet_id,
				'start_line'	 => $start_line,
				'html_table'	 => $html_table,
				'sheets'		 => array('options' => $sheets),
				'tabs'			 => $GLOBALS['phpgw']->common->create_tabs($tabs, $active_tab),
				'convert_data'	 => $convert_data
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . '::' . lang('qr-generator');

			self::render_template_xsl(array('qr_generator'), $data);
		}

		private function _get_qr( & $convert_data )
		{
			foreach ($convert_data as & $entry)
			{
				$entry['qr_input']		 = nl2br($entry['qr_input']);
				$code_text				 = $entry['qr_input'];
				$filename				 = $GLOBALS['phpgw_info']['server']['temp_dir'] . '/' . md5($code_text) . '.png';
				QRcode::png($code_text, $filename);
				$entry['encoded_text']	 = 'data:image/png;base64,' . base64_encode(file_get_contents($filename));
				unlink($filename);
			}
		}
	}