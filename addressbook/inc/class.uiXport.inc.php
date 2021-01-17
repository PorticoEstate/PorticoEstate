<?php
  /**************************************************************************\
  * phpGroupWare - addressbook                                               *
  * http://www.phpgroupware.org                                              *
  * Written by Joseph Engo <jengo@phpgroupware.org>                          *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('phpgwapi.datetime');

	class uiXport extends phpgwapi_uicommon
	{
		var $template;
		var $public_functions = array(
			'import' => True,
			'export' => True
		);
		var $bo;
		var $cat;

		function __construct()
		{
			parent::__construct();
			
			//$this->currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];
			
			//$this->template = $GLOBALS['phpgw']->template;
			$this->cat      = CreateObject('phpgwapi.categories');
			$this->bo       = CreateObject('addressbook.boXport',True);
			$this->browser  = CreateObject('phpgwapi.browser');
		}

		function import()
		{
			$convert = phpgw::get_var('convert');
			$fcat_id = phpgw::get_var('fcat_id');
			$private = phpgw::get_var('private');
			$conv_type = phpgw::get_var('conv_type');
			self::set_active_menu("{$this->currentapp}::xport_import");
				
			if ($convert)
			{
				if (empty($_FILES['tsvfile']['tmp_name']))
				{
					phpgwapi_cache::message_set(lang('Please choose file to import'), 'message');
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'addressbook.uiXport.import'));
				}
				
				if (empty($conv_type) || $conv_type == 'none')
				{
					phpgwapi_cache::message_set(lang('Please choose a conversion type from the list'), 'message');
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'addressbook.uiXport.import'));
				}
				
				$buffer = $this->bo->import($_FILES['tsvfile']['tmp_name'],$conv_type,$private,$fcat_id);
				phpgwapi_cache::message_set($buffer, 'message');
			}
			
			$conv = $this->get_convert_type_import();
			$all_cats = $this->get_categories();
						
			$tabs = array();
			$tabs['import'] = array('label' => lang('Import'), 'link' => '#import');

			$data = array(
				'form_action' => self::link(array('menuaction' => "{$this->currentapp}.uiXport.import")),
				'cancel_url' => self::link(array('menuaction' => "{$this->currentapp}.uiaddressbook_persons.index",)),
				'categories' => array('options' => $all_cats),
				'conv' => array('options' => $conv),
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, 0),
				'value_active_tab' => 0
			);
				
			self::render_template_xsl(array('xport'), array('import' => $data));
		}
		
		function get_convert_type_import()
		{
			$dir_handle = opendir(PHPGW_APP_INC . '/import');
			$i=0; 
			$myfilearray = array();
			while ($file = readdir($dir_handle))
			{
				if ((substr($file, 0, 1) != '.') && is_file(PHPGW_APP_INC . "/import/{$file}") )
				{
					$myfilearray[$i] = $file;
					$i++;
				}
			}
			closedir($dir_handle);
			sort($myfilearray);
			$conv = array();
			$conv[] = array('id' => 'none', 'name' => lang('none'));

			for ($i=0;$i<count($myfilearray);$i++)
			{
				$fname = preg_replace('/_/',' ',$myfilearray[$i]);
				$conv[] = array('id'=> $myfilearray[$i], 'name' => $fname);
			}
				
			return $conv;
		}

		function get_convert_type_export()
		{
			$dir_handle = opendir(PHPGW_APP_INC. '/export');
			$i=0;
			$myfilearray = array();
			while ($file = readdir($dir_handle))
			{
				if ((substr($file, 0, 1) != '.') && is_file(PHPGW_APP_INC . "/export/{$file}") )
				{
					$myfilearray[$i] = $file;
					$i++;
				}
			}
			
			closedir($dir_handle);
			sort($myfilearray);
			$conv = array();
			$conv[] = array('id' => 'none', 'name' => lang('none'));
			
			for ($i=0;$i<count($myfilearray);$i++)
			{
				$fname = preg_replace('/_/',' ',$myfilearray[$i]);
				$conv[] = array('id'=> $myfilearray[$i], 'name' => $fname);
			}
				
			return $conv;
		}
		
		function export()
		{
			$convert = phpgw::get_var('convert');
			$tsvfilename = phpgw::get_var('tsvfilename');
			$fcat_id = phpgw::get_var('fcat_id');
			$download = phpgw::get_var('download');
			$conv_type = phpgw::get_var('conv_type');
			$both_types = phpgw::get_var('both_types');
			$sub_cats = phpgw::get_var('sub_cats');
			self::set_active_menu("{$this->currentapp}::xport_export");
			
			if ($convert)
			{
				if (empty($conv_type) || $conv_type == 'none')
				{
					phpgwapi_cache::message_set(lang('Please choose a conversion type from the list'), 'message');
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'addressbook.uiXport.export'));
				}

				$buffer = $this->bo->export($conv_type,$fcat_id,$both_types,$sub_cats);
				
				// Note our use of ===.  Simply == would not work as expected
				if(!(strpos($conv_type, 'OpenOffice') === false))
				{
					$this->browser->content_header(basename($buffer),'');//echo $buffer;
					readfile($buffer);
					
				}
				elseif(($download == 'on') || ($conv_type == 'Palm_PDB') )
				{
					// filename, default application/octet-stream, length of file, default nocache True
					$this->browser->content_header($tsvfilename,'application/x-octet-stream',strlen($buffer));
					echo $buffer;
				}
				else
				{
					$GLOBALS['phpgw']->common->phpgw_header();
					echo parse_navbar();
					echo "<pre>\n";
					echo $buffer;
					echo "\n</pre>\n";
					echo '<a href="'.$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'addressbook.uiXport.export')) . '">' . lang('OK') . '</a>';
					$GLOBALS['phpgw']->common->phpgw_footer();
				}
			}
						
			$conv = $this->get_convert_type_export();
			$all_cats = $this->get_categories();
						
			$tabs = array();
			$tabs['export'] = array('label' => lang('Export'), 'link' => '#export');

			$data = array(
				'form_action' => self::link(array('menuaction' => "{$this->currentapp}.uiXport.export")),
				'cancel_url' => self::link(array('menuaction' => "{$this->currentapp}.uiaddressbook_persons.index",)),
				'categories' => array('options' => $all_cats),
				'conv' => array('options' => $conv),
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, 0),
				'value_active_tab' => 0
			);
				
			self::render_template_xsl(array('xport'), array('export' => $data));			
		}
		
		function get_categories()
		{
			$cats = $this->cat->return_array('all', 0, false, '', '', '', true);
			$all_cats = array();
			
			$all_cats[] = array('id' => '', 'name' => lang('All'));
			foreach ($cats as $data)
			{
				$all_cats[] = array('id'=> $data['id'], 'name' => $data['name']);
			}

			return $all_cats;
		}
	}
