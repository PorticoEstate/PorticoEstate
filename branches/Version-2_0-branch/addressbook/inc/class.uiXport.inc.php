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

	class uiXport
	{
		var $template;
		var $public_functions = array(
			'import' => True,
			'export' => True
		);
		var $bo;
		var $cat;

		var $start;
		var $limit;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $cat_id;

		function __construct()
		{
			$this->template = $GLOBALS['phpgw']->template;
			$this->cat      = CreateObject('phpgwapi.categories');
			$this->bo       = CreateObject('addressbook.boXport',True);
			$this->browser  = CreateObject('phpgwapi.browser');

			$this->start    = $this->bo->start;
			$this->limit    = $this->bo->limit;
			$this->query    = $this->bo->query;
			$this->sort     = $this->bo->sort;
			$this->order    = $this->bo->order;
			$this->filter   = $this->bo->filter;
			$this->cat_id   = $this->bo->cat_id;
		}

		/* Return a select form element with the categories option dialog in it */
		function cat_option($cat_id='',$notall=False,$java=True,$multiple=False)
		{
			if ($java)
			{
				$jselect = ' onChange="this.form.submit();"';
			}
			/* Setup all and none first */
			$cats_link  = "\n" .'<select name="fcat_id'.($multiple?'[]':'').'"' .$jselect . ($multiple ? 'multiple size="3"' : '') . ">\n";
			if (!$notall)
			{
				$cats_link .= '<option value=""';
				if ($cat_id=='all')
				{
					$cats_link .= ' selected';
				}
				$cats_link .= '>'.lang('all').'</option>'."\n";
			}

			/* Get global and app-specific category listings */
			$cats_link .= $this->cat->formated_list('select','all',$cat_id,True);
			$cats_link .= '</select>'."\n";
			return $cats_link;
		}

		function import()
		{
			$conv_type_a = $GLOBALS['phpgw']->session->appsession('conv_type_values_i', 'addressbook');
			if ($_REQUEST['convert'] && is_array($conv_type_a) && in_array($_REQUEST['conv_type'], $conv_type_a)) //&& ($_FILES['tsvfile']['error'] == UPLOAD_ERR_OK))
			{
				$buffer = $this->bo->import($_FILES['tsvfile']['tmp_name'],$_REQUEST['conv_type'],$_REQUEST['private'],$_REQUEST['fcat_id']);

				if ($_REQUEST['download'] == '')
				{
					if($_REQUEST['conv_type'] == 'Debug LDAP' || $_REQUEST['conv_type'] == 'Debug SQL' )
					{
						// filename, default application/octet-stream, length of file, default nocache True
						$GLOBALS['phpgw']->browser->content_header($tsvfilename,'',strlen($buffer));
						echo $buffer;
					}
					else
					{
						$GLOBALS['phpgw']->common->phpgw_header();
						echo parse_navbar();
						$this->template->set_root(PHPGW_APP_TPL);
						echo "<pre>$buffer</pre>";
						echo '<a href="'.$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'addressbook.uiaddressbook.index')) . '">'.lang('OK').'</a>';
						$GLOBALS['phpgw']->common->phpgw_footer();
					}
				}
				else
				{
					$GLOBALS['phpgw']->common->phpgw_header();
					echo parse_navbar();
					$this->template->set_root(PHPGW_APP_TPL);
					echo "<pre>$buffer</pre>";
					echo '<a href="'.$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'addressbook.uiaddressbook.index')). '">'.lang('OK').'</a>';
					$GLOBALS['phpgw']->common->phpgw_footer();
				}

			}
			else
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();
				$this->template->set_root(PHPGW_APP_TPL);
				set_time_limit(0);

				$this->template->set_file(array('import' => 'import.tpl'));

				$dir_handle = opendir(PHPGW_APP_INC . '/import');
				$i=0; $myfilearray = '';
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
				for ($i=0;$i<count($myfilearray);$i++)
				{
					$fname = preg_replace('/_/',' ',$myfilearray[$i]);
					$conv .= '<OPTION VALUE="' . $myfilearray[$i].'">' . $fname . '</OPTION>';
				}

				$GLOBALS['phpgw']->session->appsession('conv_type_values_i', 'addressbook', $myfilearray);
				$this->template->set_var('lang_cancel',lang('Cancel'));
				$this->template->set_var('lang_cat',lang('Select Category'));
				$this->template->set_var('cancel_url',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'addressbook.uiaddressbook.index')));
				$this->template->set_var('navbar_bg',$GLOBALS['phpgw_info']['theme']['navbar_bg']);
				$this->template->set_var('navbar_text',$GLOBALS['phpgw_info']['theme']['navbar_text']);
				$this->template->set_var('import_text',lang('Import from LDIF, CSV, or VCard'));
				$this->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'addressbook.uiXport.import')));
				$this->template->set_var('cat_link',$this->cat_option($this->cat_id,False,False));
				//$this->template->set_var('cat_link',$this->cat_option($this->cat_id,True,False));
				$this->template->set_var('tsvfilename','');
				$this->template->set_var('conv',$conv);
				$this->template->set_var('debug',lang('Debug output in browser'));
				$this->template->set_var('filetype',lang('LDIF'));
				$this->template->set_var('download',lang('Submit'));
				$this->template->set_var('start',$this->start);
				$this->template->set_var('sort',$this->sort);
				$this->template->set_var('order',$this->order);
				$this->template->set_var('filter',$this->filter);
				$this->template->set_var('query',$this->query);
				$this->template->set_var('cat_id',$this->cat_id);
				$this->template->pparse('out','import');
			}
//			$GLOBALS['phpgw']->common->phpgw_footer();
		}

		function export()
		{
			//global $tsvfilename,$both_types,$sub_cats;
			$convert = phpgw::get_var('convert');
			$tsvfilename = phpgw::get_var('tsvfilename');
			$fcat_id = phpgw::get_var('fcat_id');
			$download = phpgw::get_var('download');
			$conv_type = phpgw::get_var('conv_type');
			$both_types = phpgw::get_var('both_types');
			$sub_cats = phpgw::get_var('sub_cats');
			
			// get the data to create the sql query used by the addressbook display 
			$export_vars = $GLOBALS['phpgw']->session->appsession('export_vars','addressbook');
			//echo "<pre>Export_vars: "; print_r($export_vars); echo "</pre>\n";
			
			//$entries = $this->bo->$get_data_function($fields, $this->limit, $this->start, $this->order, $this->sort, '', $criteria);

			$conv_type_a = $GLOBALS['phpgw']->session->appsession('conv_type_values_e', 'addressbook');
			if ($_REQUEST['convert'] && is_array($conv_type_a) && in_array($_REQUEST['conv_type'], $conv_type_a))
			{
				if ($_REQUEST['conv_type'] == 'none')
				{
					$GLOBALS['phpgw_info']['flags']['noheader'] = False;
					$GLOBALS['phpgw_info']['flags']['noheader'] = True;
					$GLOBALS['phpgw']->common->phpgw_header();
					echo parse_navbar();
					echo lang('<b>No conversion type &lt;none&gt; could be located.</b>  Please choose a conversion type from the list');
					echo '&nbsp<a href="'.$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'addressbook.uiXport.export')) . '">' . lang('OK') . '</a>';
					$GLOBALS['phpgw']->common->phpgw_footer();
					$GLOBALS['phpgw']->common->phpgw_exit();
				}

				$buffer = $this->bo->export($_REQUEST['conv_type'],$_REQUEST['fcat_id'],$both_types,$sub_cats);
				
				// Note our use of ===.  Simply == would not work as expected
				if(!(strpos($_REQUEST['conv_type'], 'OpenOffice') === false))
				{
					// filename, default application/octet-stream, length of file, default nocache True
					//$this->browser->content_header($tsvfilename,'application/x-octet-stream',strlen($buffer));
					//$this->browser->content_header($tsvfilename,'application/vnd.sun.xml.writer');
					//echo $tsvfilename;
					//echo $buffer;
					//echo basename($buffer);
					//$this->browser->content_header($tsvfilename,'');
					$this->browser->content_header(basename($buffer),'');//echo $buffer;
					readfile($buffer);
					//echo $tsvfilename;
					
				}
				elseif(($_REQUEST['download'] == 'on') || ($_REQUEST['conv_type'] == 'Palm_PDB') )
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
			else
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();
				$this->template->set_root(PHPGW_APP_TPL);
				set_time_limit(0);
				
				$this->template->set_file(array('export' => 'export.tpl'));

				$dir_handle = opendir(PHPGW_APP_INC. '/export');
				$i=0; $myfilearray = '';
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
				for ($i=0;$i<count($myfilearray);$i++)
				{
					$fname = preg_replace('/_/',' ',$myfilearray[$i]);
					$conv .= '        <option value="'.$myfilearray[$i].'">'.$fname.'</option>'."\n";
				}

				$GLOBALS['phpgw']->session->appsession('conv_type_values_e', 'addressbook', $myfilearray);
				$this->template->set_var('lang_cancel',lang('Cancel'));
				$this->template->set_var('lang_cat',lang('Select Category'));
				$this->template->set_var('cat_link',$this->cat_option($this->cat_id,False,False));
				$this->template->set_var('cancel_url',$GLOBALS['phpgw']->link('/addressbook/index.php'));
				$this->template->set_var('navbar_bg',$GLOBALS['phpgw_info']['theme']['navbar_bg']);
				$this->template->set_var('navbar_text',$GLOBALS['phpgw_info']['theme']['navbar_text']);
				$this->template->set_var('export_text',lang('Export from Addressbook'));
				$this->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'addressbook.uiXport.export')));
				$this->template->set_var('filename',lang('Export file name'));
				$this->template->set_var('conv',$conv);
				$this->template->set_var('debug',lang(''));
				$this->template->set_var('download',lang('Submit'));
				$this->template->set_var('start',$this->start);
				$this->template->set_var('sort',$this->sort);
				$this->template->set_var('order',$this->order);
				$this->template->set_var('filter',$this->filter);
				$this->template->set_var('query',$this->query);
				$this->template->set_var('cat_id',$this->cat_id);
				$this->template->pparse('out','export');

				$GLOBALS['phpgw']->common->phpgw_footer();
			}
		}
	}
