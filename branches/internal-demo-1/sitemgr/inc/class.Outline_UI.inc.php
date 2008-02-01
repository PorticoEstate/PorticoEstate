<?php
	/*************************************************************************\
	* http://www.phpgroupware.org                                             *
	* -------------------------------------------------                       *
	* This program is free software; you can redistribute it and/or modify it *
	* under the terms of the GNU General Public License as published by the   *
	* Free Software Foundation; either version 2 of the License, or (at your  *
	* option) any later version.                                              *
	\*************************************************************************/
	/* $Id: class.Outline_UI.inc.php 17906 2007-01-24 16:25:33Z Caeies $ */
	
	class Outline_UI
	{
		var $common_ui;
		var $t;
		var $acl;
		var $pagebo;
		var $cat_bo;
		var $isadmin;
		
		
		var $public_functions=array
		(
			'manage' => True,
		);
		
		function Outline_UI()			
		{
			$this->common_ui = CreateObject('sitemgr.Common_UI',True);
			$this->t = $GLOBALS['phpgw']->template;
			$this->pagebo = &$GLOBALS['Common_BO']->pages;
			$this->cat_bo = &$GLOBALS['Common_BO']->cats;
			$this->acl = $GLOBALS['Common_BO']->acl;
			$this->isadmin = $this->acl->is_admin();
		}

		function manage()
		{
			$this->common_ui->DisplayHeader();

			$this->t->set_file('ManageOutline','manage_outline.tpl');
			$this->t->set_block('ManageOutline', 'PageBlock', 'PBlock');
			$this->t->set_block('ManageOutline', 'CategoryBlock', 'CBlock');
			$this->t->set_var('outline_manager', lang('Manage categories and pages'));

			if ($this->isadmin)
			{
				$this->t->set_var('addcategory','<a target="editwindow" href="' . 
					$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sitemgr.Categories_UI.edit')).
					'">' . lang('Add a category') .'</a>'
				);
			}

			$cat_list = $this->cat_bo->getpermittedcatsWrite();

			if (!$cat_list)
			{
				 echo lang("You do not have write permissions for any site categories.") . '<br><br>';
			}

			while (list(,$cat_id) = @each($cat_list))
			{
				$category = $this->cat_bo->getCategory($cat_id);
				$this->t->set_var('PBlock', '');
				$page_list = $this->pagebo->getPageIDList($cat_id);

				while (list(,$page_id) = @each($page_list))
				{
					$page = $this->pagebo->getPage($page_id,$this->sitelanguages[0]);
					$page_description = sprintf(
						'<b>%s</b>: %s &nbsp;&nbsp;<b>ID</b>: %s<br><b>%s</b>: %s',
						lang('Name'),
						$page->name,
						$page_id,
						lang('Title'),
						$page->title
					);
					$this->t->set_var('page', $page_description);
					$link_data['page_id'] = $page_id;
					$link_data['menuaction'] = "sitemgr.Pages_UI.edit";
					$this->t->set_var('editpage','<a target="editwindow" href="' . $GLOBALS['phpgw']->link('/index.php',$link_data) .
						'">' . lang('Edit page') . '</a>'
					);
					$link_data['menuaction'] = "sitemgr.Content_UI.manage";
					$this->t->set_var('pagecontent','<a href="' . $GLOBALS['phpgw']->link('/index.php',$link_data) .
						'">' . lang('Manage page specific content') . '</a>'
					);
					$link_data['menuaction'] = "sitemgr.Pages_UI.delete";
					$this->t->set_var('deletepage','<a href="' . $GLOBALS['phpgw']->link('/index.php',$link_data) .
						'">' . lang('Delete page') . '</a>'
					);
					$this->t->parse('PBlock', 'PageBlock', true);
				}
				$this->t->set_var(array(
					'indent' => $category->depth * 5,
					'category' => $category->name
				));
				$link_data['page_id'] = 0;
				$link_data['cat_id'] = $cat_id;
				if ($this->isadmin)
				{
					$link_data['menuaction'] = "sitemgr.Categories_UI.edit";
					$this->t->set_var('editcat','<a target="editwindow" href="' . $GLOBALS['phpgw']->link('/index.php',$link_data) .
						'">' . lang('Edit category') . '</a>');
					$link_data['menuaction'] = "sitemgr.Categories_UI.delete";
					$this->t->set_var('deletecat','<a href="' . $GLOBALS['phpgw']->link('/index.php',$link_data) .
						'">'  . lang('Delete category') . '</a>');
					$link_data['menuaction'] = "sitemgr.Modules_UI.manage";
					$this->t->set_var('moduleconfig','<a href="' . $GLOBALS['phpgw']->link('/index.php',$link_data) .
						'">'  . lang('Manage category wide module properties') . '</a>');
				}

				$link_data['menuaction'] = "sitemgr.Pages_UI.edit";
				$this->t->set_var('addpage','<a target="editwindow" href="' . $GLOBALS['phpgw']->link('/index.php',$link_data) .
					'">' . lang('Add page to category') . '</a>');
				$link_data['menuaction'] = "sitemgr.Content_UI.manage";
				$this->t->set_var('catcontent','<a href="' . $GLOBALS['phpgw']->link('/index.php',$link_data) .
					'">' . lang('Manage category wide content') . '</a>');
				$this->t->parse('CBlock', 'CategoryBlock', true); 
			}
			$this->t->pfp('out','ManageOutline');
			$this->common_ui->DisplayFooter();
		}
	}
