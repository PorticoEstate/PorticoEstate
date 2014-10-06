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
	* @subpackage agreement
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_import
	{
		var $public_functions = array
			(
			);

		function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->bocommon		= CreateObject('property.bocommon');
		}

		function list_content($list,$uicols)
		{
			$j=0;

			if (isset($list) AND is_array($list))
			{
				foreach($list as $entry)
				{
					$content[$j]['id'] 			= $entry['id'];
					$content[$j]['item_id'] 	= $entry['item_id'];
					$content[$j]['index_count']	= $entry['index_count'];
					$content[$j]['cost'] 		= $entry['cost'];
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						if($uicols['input_type'][$i]!='hidden')
						{
							$content[$j]['row'][$i]['value'] 			= $entry[$uicols['name'][$i]];
							$content[$j]['row'][$i]['name'] 			= $uicols['name'][$i];
						}
					}
					$j++;
				}
			}

			for ($i=0;$i<count($uicols['descr']);$i++)
			{
				if($uicols['input_type'][$i]!='hidden')
				{
					$table_header[$i]['header'] 	= $uicols['descr'][$i];
					$table_header[$i]['width'] 		= '5%';
					$table_header[$i]['align'] 		= 'center';
				}
			}

			return array('content'=>$content,'table_header'=>$table_header);
		}

		function importfile()
		{
			$importfile = $_FILES['importfile']['tmp_name'];

			if(!$importfile)
			{
				$importfile = phpgw::get_var('importfile');
			}

			if($importfile)
			{
				$old = $importfile;
				$importfile = $GLOBALS['phpgw_info']['server']['temp_dir'].'/service_import_'.basename($importfile);
				if(is_file($old))
				{
					rename($old,$importfile);
				}

				if ( phpgw::get_var('cancel') && is_file($importfile))
				{
					unlink ($importfile);
				}
			}

			return $importfile;
		}


		function prepare_data($importfile = '', $list='',$uicols='')
		{
			$fields = array();
			for ($i=0; $i<count($uicols['input_type']); $i++ )
			{
				if($uicols['import'][$i])
				{
					$fields[] = array
						(
							'name' => $uicols['name'][$i],
							'descr' =>$uicols['descr'][$i]
						);
					$uicols2['input_type'][]	= 'text';
					$uicols2['name'][]			= $uicols['name'][$i];
					$uicols2['descr'][]			= $uicols['descr'][$i];
				}
			}

			$this->uicols2 = $uicols2;

			phpgw::import_class('phpgwapi.phpexcel');

			$objPHPExcel = PHPExcel_IOFactory::load($importfile);
			$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);

			foreach($fields as &$entry)
			{
				$entry['id'] = array_search($entry['descr'], $sheetData[1]);
			}

			$valueset = array();

			$rows = count($sheetData) +1;

			for ($i=2; $i<$rows; $i++ ) //First data entry on row 2
			{
				foreach ($fields as $entry)
				{
					$valueset[$i-2][$entry['name']] = $sheetData[$i][$entry['id']];
				}
			}
			return $valueset;
		}

		function pre_import($importfile = '', $valueset='',$import_action = '', $header_info = '')
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('import'));

			$list			= $this->list_content($valueset,$this->uicols2);
			$content		= $list['content'];
			$table_header	= $list['table_header'];

			$data = array
				(
					'importfile'					=> $importfile,
					'values'						=> $content,
					'table_header'					=> $table_header,
					'import_action'					=> $import_action,
					'lang_import_statustext'		=> lang('import to this location from spreadsheet'),
					'lang_import'					=> lang('import'),
					'lang_cancel'					=> lang('cancel')
				);

			$GLOBALS['phpgw_info']['flags']['app_header'] =  $header_info . ': ' . lang('import');
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('import' => $data));
		}
	}
