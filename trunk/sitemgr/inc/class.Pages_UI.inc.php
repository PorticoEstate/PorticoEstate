<?php
	/*************************************************************************\
	* http://www.phpgroupware.org                                             *
	* -------------------------------------------------                       *
	* This program is free software; you can redistribute it and/or modify it *
	* under the terms of the GNU General Public License as published by the   *
	* Free Software Foundation; either version 2 of the License, or (at your  *
	* option) any later version.                                              *
	\*************************************************************************/
	/* $Id$ */
	
	class Pages_UI
	{
		var $common_ui;
		var $t;
		var $pagebo;
		var $categorybo;
		var $pageso; // page class
		var $sitelanguages;
		
		var $public_functions=array
		(
			'edit' => True,
			'delete' => True
		);
		
		function Pages_UI()			
		{
			$this->common_ui = CreateObject('sitemgr.Common_UI',True);
			$this->t = $GLOBALS['phpgw']->template;
			$this->pagebo = &$GLOBALS['Common_BO']->pages;
			$this->categorybo = &$GLOBALS['Common_BO']->cats;
			$this->sitelanguages = $GLOBALS['Common_BO']->sites->current_site['sitelanguages'];
		}
	
		function delete()
		{
			$page_id = $_GET['page_id'];
			$this->pagebo->removePage($page_id);
			$GLOBALS['phpgw']->redirect($GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Outline_UI.manage'));
			return;
		}

		function edit()
		{
			$GLOBALS['Common_BO']->globalize(array(
				'inputhidden','btnAddPage','btnDelete','btnEditPage','btnSave','inputsort','inputstate',
				'inputtitle','inputname','inputsubtitle','savelanguage','inputpageid','inputcategoryid'));

			global $inputpageid,$inputcategoryid, $inputhidden, $inputstate;
			global $btnAddPage, $btnDelete, $btnEditPage, $btnSave;
			global $inputsort,$inputtitle, $inputname, $inputsubtitle;
			global $savelanguage;
			$page_id = $inputpageid ? $inputpageid : $_GET['page_id'];
			$category_id = $inputcategoryid ? $inputcategoryid : $_GET['cat_id'];

			$this->t->set_file('EditPage', 'edit_page.tpl');

			if($btnSave)
			{
				if ($inputname == '' || $inputtitle == '')
				{
					$error = lang('You failed to fill in one or more required fields.');
					$this->t->set_var('message',$error);
				}
				else
				{
					if(!$page_id)
					{		
						$page_id = $this->pagebo->addPage($inputcategoryid);
						if(!$page_id)
						{
	//						echo lang("You don't have permission to write in the category");
							$GLOBALS['phpgw']->redirect($GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sitemgr.Outline_UI.manage')));
							return;
						}
					}
					$page->id = $page_id;
					$page->title = $inputtitle;
					$page->name = $inputname;
					$page->subtitle = $inputsubtitle;
					$page->sort_order = $inputsort;
					$page->cat_id = $category_id;
					$page->hidden = $inputhidden ? 1: 0;
					$page->state = $inputstate;
					$savelanguage = $savelanguage ? $savelanguage : $this->sitelanguages[0];
					$save_msg = $this->pagebo->savePageInfo($page,$savelanguage);
					if (!is_string($save_msg))
					{
						$this->t->set_var('message',lang('Page saved'));
					}
					else
					{
						$this->t->set_var('message',$save_msg);
					}
				}
			}

			if($page_id)
			{
				$page = $this->pagebo->getPage($page_id,$this->sitelanguages[0]);
				if (!$GLOBALS['Common_BO']->acl->can_write_category($page->cat_id))
				{
					$GLOBALS['phpgw']->redirect($GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sitemgr.Outline_UI.manage')));
					return;
				}
				$this->t->set_var(array(
					'add_edit' => lang('Edit Page'),
					'catselect' => $this->getParentOptions($page->cat_id)
			));
			}
			else
			{
				$this->t->set_var(array(
					'add_edit' => lang('Add Page'),
					'catselect' => $this->getParentOptions($category_id)
				));
			}

			if (count($this->sitelanguages) > 1)
			{
				$select = lang('as') . ' <select name="savelanguage">';
			
				foreach ($this->sitelanguages as $lang)
				{
					$selected= '';
					if ($lang == $page->lang)
					{
						$selected = 'selected="selected" ';
					}
					$select .= '<option ' . $selected .'value="' . $lang . '">'. $GLOBALS['Common_BO']->getlangname($lang) . '</option>';
				}
				$select .= '</select> ';
				$this->t->set_var('savelang',$select);
			}

			$link_data['page_id'] = $page_id;
			$link_data['category_id'] = $inputcategoryid;
			$this->t->set_var(array(
				'title' =>$page->title,
				'subtitle' => $page->subtitle,
				'name'=>$page->name,
				'sort_order'=>$page->sort_order,
				'page_id'=>$page_id,
				'hidden' => $page->hidden ? 'CHECKED' : '',
				'stateselect' => $GLOBALS['Common_BO']->inputstateselect($page->state),
				'lang_name' => lang('Name'),
				'lang_title' => lang('Title'),
				'lang_subtitle' => lang('Subtitle'),
				'lang_sort' => lang('Sort order'),
				'lang_category' => lang('Category'),
				'lang_hide' => lang('Check to hide from condensed site index.'),
				'lang_required' => lang('Required Fields'),
				'lang_done' => lang('Done'),
				'lang_reset' => lang('Reset'),
				'lang_save' => lang('Save'),
				'lang_state' => lang('State'),
			));
			
			$this->t->pfp('out','EditPage');
		}

		function getParentOptions($selected_id=0)
		{
			$option_list=$this->categorybo->getCategoryOptionList();
			if (!$selected_id)
			{
				$selected=' SELECTED'; 
			}
			$retval="\n".'<SELECT NAME="inputcategoryid">'."\n";
			foreach($option_list as $option)
			{
				if ((int) $option['value']!=0)
				{
					$selected='';
					if ($option['value']==$selected_id)
					{
						$selected=' SELECTED';
					}
					$retval.='<OPTION VALUE="'.$option['value'].'"'.$selected.'>'.
					$option['display'].'</OPTION>'."\n";
				}
			}
			$retval.='</SELECT>';
			return $retval;
		}
	}	
?>
