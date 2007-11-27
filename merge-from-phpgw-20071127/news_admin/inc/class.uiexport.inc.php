<?php
	/**************************************************************************\
	* phpGroupWare - News                                                      *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	* --------------------------------------------                             *
	\**************************************************************************/

	/* $Id: class.uiexport.inc.php 16990 2006-08-23 13:32:03Z skwashd $ */

	class uiexport
	{
		var $start = 0;
		var $query = '';
		var $sort  = '';
		var $order = '';
		var $bo;
		var $nextmatchs = '';
		var $public_functions = array(
			'exportlist' 	=> True,
			);
		var $exporttypes;

		function uiexport()
		{
			$this->bo = createobject('news_admin.boexport',True);
			$this->nextmatchs = createobject('phpgwapi.nextmatchs');
			$this->start = $this->bo->start;
			$this->query = $this->bo->query;
			$this->order = $this->bo->order;
			$this->sort = $this->bo->sort;
			$this->exporttypes = array(
				0 => lang('No RSS export'),
				1 => 'RSS 0.91',
				2 => 'RSS 1.0',
				3 => 'RSS 2.0'
			);
			$this->itemsyntaxtypes = array(
				0 => '?item=n',
				1 => '&item=n',
				2 => '?news%5Bitem%5D=n',
				3 => '&news%5Bitem%5D=n'
			);
		}
		
		function exportlist()
		{
			if (!$GLOBALS['phpgw']->acl->check('run',1,'admin'))
			{
				$this->deny();
			}

			if ($_POST['btnDone'])
			{
				$GLOBALS['phpgw']->redirect_link('/admin/index.php');
			}

			$GLOBALS['phpgw']->common->phpgw_header(true);

			if ($_POST['btnSave'])
			{
				foreach($_POST['catids'] as $cat_id)
				{
					$this->bo->saveconfig($cat_id,$_POST['inputconfig'][$cat_id]);
				}
			}

			$GLOBALS['phpgw']->template->set_file('export', 'export.tpl');
			$GLOBALS['phpgw']->template->set_block('export','cat_list','Cblock');
			$GLOBALS['phpgw']->template->set_block('cat_list','config','confblock');
			$GLOBALS['phpgw']->template->set_var(array(
				'title' => $GLOBALS['phpgw_info']['apps']['news_admin']['title'] . ' - ' . lang('Configure RSS exports'),
				'lang_search' => lang('Search'),
				'lang_save' => lang('Save'),
				'lang_done' => lang('Done'),
				'lang_search' => lang('Search'),
				'lang_configuration' => lang('Configuration'),
			));

			$left  = $this->nextmatchs->left('/index.php',$this->start,$this->bo->catbo->total_records,'menuaction=news_admin.uiexport.exportlist');
			$right = $this->nextmatchs->right('/index.php',$this->start,$this->bo->catbo->total_records,'menuaction=news_admin.uiexport.exportlist');

			
			$GLOBALS['phpgw']->template->set_var(array(
				'left' => $left,
				'right' => $right,
				'lang_showing' => $this->nextmatchs->show_hits($this->bo->catbo->total_records,$this->start),
				'query' => $this->query,
			));

			foreach ( $this->bo->cats as $cat )
			{
				$config = $this->bo->readconfig($cat['id']);
				$GLOBALS['phpgw']->template->set_var(array
				(
					'catname' => $cat['name'],
					'catid' => $cat['id'],
					'lang_type' => lang('Format of export'),
					'typeselectlist' => $this->selectlist($this->exporttypes,$config['type']),
					'lang_item' => lang('Format for links to items'),
				));
				$GLOBALS['phpgw']->template->set_var('confblock','');
				foreach (array(
					'title'        => lang('Title'),
					'link'         => lang('Link'),
					'description'  => lang('description'),
					'img_title'    => lang('Image Title'),
					'img_url'      => lang('Image URL'),
					'img_link'     => lang('Image Link')) as $setting => $label)
				{
					$GLOBALS['phpgw']->template->set_var(array
					(
						'config_value'	=> $config[$setting],
						'label'			=> $label,
						'setting'		=> $setting
					));
					$GLOBALS['phpgw']->template->parse('confblock','config',True);
				}
				$GLOBALS['phpgw']->template->parse('Cblock','cat_list',True);
			}
			$GLOBALS['phpgw']->template->pfp('out','export');
		}

		function selectlist($values,$default)
		{
			while (list($value,$type) = each($values))
			{
				$selectlist .= '<option value="' . $value . '"';
				if ($value == $default)
				{
					$selectlist .= ' selected="selected"';
				}
				$selectlist .= '>' . $type  . '</option>' . "\n";
			}
			return $selectlist;
		}

		function deny()
		{
			echo '<p><center><b>'.lang('Access not permitted').'</b></center>';
			$GLOBALS['phpgw']->common->phpgw_exit(True);
		}
	}
?>
