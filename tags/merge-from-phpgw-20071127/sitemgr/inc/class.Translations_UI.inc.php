<?php
	/***************************************************************************\
	* phpGroupWare - Web Content Manager                                        *
	* http://www.phpgroupware.org                                               *
	* -------------------------------------------------                         *
	* This program is free software; you can redistribute it and/or modify it   *
	* under the terms of the GNU General Public License as published by the     *
	* Free Software Foundation; either version 2 of the License, or (at your    *
	* option) any later version.                                                *
	\***************************************************************************/

	class Translations_UI
	{
		var $t;
		var $cat_bo;
		var $acl;
		var $sitelanguages;
		var $common_ui;
		var $pagebo;
		var $contentbo;
		var $modulebo;

		var $public_functions = array
		(
			'manage' => True,
			'translateCategory' => True,
			'translatePage' => True,
			'translateSitecontent' => True,
		);

		function Translations_UI()
		{
			$this->common_ui = CreateObject('sitemgr.Common_UI',True);
			$this->t = $GLOBALS['phpgw']->template;
			$this->cat_bo = &$GLOBALS['Common_BO']->cats;
			$this->acl = &$GLOBALS['Common_BO']->acl;
			$this->sitelanguages = $GLOBALS['Common_BO']->sites->current_site['sitelanguages'];
			$this->pagebo = &$GLOBALS['Common_BO']->pages;
			$this->contentbo = &$GLOBALS['Common_BO']->content;
			$this->modulebo = &$GLOBALS['Common_BO']->modules;
		}

		function manage()
		{
			$this->common_ui->DisplayHeader();

			$this->t->set_file('ManageTranslations', 'manage_translations.tpl');
			$this->t->set_block('ManageTranslations', 'PageBlock', 'PBlock');
			$this->t->set_block('PageBlock', 'langexistpage', 'langpageBlock');
			$this->t->set_block('ManageTranslations', 'CategoryBlock', 'CBlock');
			$this->t->set_block('CategoryBlock', 'langexistcat', 'langcatBlock');
			$this->t->set_block('ManageTranslations', 'sitelanguages', 'slBlock');

			foreach ($this->sitelanguages as $lang)
			{
				$this->t->set_var('sitelanguage',$lang);
				$this->t->parse('slBlock', 'sitelanguages', true);
			}
			$link_data['menuaction'] = "sitemgr.Translations_UI.translateSitecontent";
			$this->t->set_var(Array(
				'translation_manager' => lang('Translation Manager'),
				'lang_catname' => lang('Category Name'),
				'translate_site_content' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_site_content' => lang('Translate site-wide content blocks'),
				'colspan' => (count($this->sitelanguages) + 2)
			));
			$cat_list = $this->cat_bo->getpermittedcatsWrite();
			if($cat_list)
			{
				for($i = 0; $i < sizeof($cat_list); $i++)
				{			
					//setup entry in categorblock for translations of categories
					$cat = $this->cat_bo->getCategory($cat_list[$i]);
					if ($cat->depth)
					{
						$buffer = '-';
					}
					else
					{
						$buffer = '';
					}
					$buffer = str_pad('',$cat->depth*18,
						'&nbsp;',STR_PAD_LEFT).$buffer;
					$this->t->set_var('buffer', $buffer);
					$this->t->set_var('category', $cat->name);
					$category_id = $cat_list[$i];

					$availablelangsforcat = $this->cat_bo->getlangarrayforcategory($cat_list[$i]);
					$this->t->set_var('langcatBlock','');
					foreach ($this->sitelanguages as $lang)
					{
						$this->t->set_var('catexistsinlang', in_array($lang,$availablelangsforcat) ? 'ø' : '&nbsp;');
						$this->t->parse('langcatBlock', 'langexistcat', true);
					}

					$link_data['menuaction'] = 'sitemgr.Translations_UI.translateCategory';
					$link_data['category_id'] = $cat_list[$i];
					$this->t->set_var('translatecat', 
						'<form action="' . $GLOBALS['phpgw']->link('/index.php',$link_data) .
						'" method="POST"><input type="submit" name="btnTranslateCategory" value="' . lang('Translate') .'"></form>');

					//setup page list
					$this->t->set_var('PBlock', '');
					$page_list = $this->pagebo->getPageIDList($cat_list[$i]);
					if($page_list && sizeof($page_list)>0)
					{
						for($j = 0; $j < sizeof($page_list); $j++)
						{
							$page = $this->pagebo->getPage($page_list[$j],$this->sitelanguages[0]);
							$page_description = '<i>' . lang('Page') . ': </i>'.$page->name.'<br><i>' . lang('Title') . ': </i>'.$page->title;
							$this->t->set_var('page', $page_description);

							$availablelangsforpage = $this->pagebo->getlangarrayforpage($page_list[$j]);
							$this->t->set_var('langpageBlock','');
							foreach ($this->sitelanguages as $lang)
							{
								$this->t->set_var('pageexistsinlang', in_array($lang,$availablelangsforpage) ? 'ø' : '&nbsp;');
								$this->t->parse('langpageBlock', 'langexistpage', true);
							}

							$link_data['page_id'] = $page_list[$j];
							$link_data['menuaction'] = 'sitemgr.Translations_UI.translatePage';
							$this->t->set_var('translatepage', 
								'<form action="' . $GLOBALS['phpgw']->link('/index.php',$link_data) .
								'" method="POST"><input type="submit" name="btnTranslatePage" value="' . lang('Translate') .'"></form>');
							$this->t->parse('PBlock', 'PageBlock', true);
						}
					}

					$this->t->parse('CBlock', 'CategoryBlock', true); 
				}
			}
			else
			{
				$this->t->set_var('category','No category is available');
			}
			$this->t->pfp('out', 'ManageTranslations');

			$this->common_ui->DisplayFooter();
		}

		function translateCategory()
		{
			$GLOBALS['Common_BO']->globalize(array('changelanguage','showlanguage','savelanguage','btnSaveCategory','savecatname','savecatdesc','btnSaveBlock','element','blockid','blocktitle'));
			global $changelanguage, $showlanguage, $savelanguage, $btnSaveCategory, $savecatname, $savecatdesc,$btnSaveBlock;
			$category_id = $_GET['category_id'];

			if ($btnSaveCategory)
			{
				$this->cat_bo->saveCategoryLang($category_id, $savecatname, $savecatdesc, $savelanguage);
			}
			elseif ($btnSaveBlock)
			{
				$this->save_block();
			}

			$this->common_ui->DisplayHeader();
			$this->t->set_file('TranslateCategory', 'translate_category.tpl');
			$this->t->set_file('Blocks','translate_block.tpl');
			$this->t->set_block('Blocks','Blocktranslator');
			$this->t->set_block('Blocktranslator','Version','Vblock');
			$this->t->set_block('Blocks','EditorElement','Eblock');
			
			if($error)
			{
				$this->t->set_var('error_msg',lang('You failed to fill in one or more required fields.'));
				$cat->name = $savecatname;
				$cat->description = $savecatdesc;
			}
			else
			{
				$cat = $this->cat_bo->getCategory($category_id);
				$showlanguage = $showlanguage ? $showlanguage : $this->sitelanguages[0];
				$showlangdata = $this->cat_bo->getCategory($category_id,$showlanguage);
				$savelanguage = $savelanguage ? $savelanguage : $this->sitelanguages[1]; 
				$savelangdata = $this->cat_bo->getCategory($category_id,$savelanguage);

				$this->templatehelper();
				$this->t->set_var(Array(
					'translate' => lang('Translate Category'),
					'catid' => $category_id,
					'lang_catname' => lang('Category Name'),
					'showcatname' => $showlangdata->name,
					'savecatname' => $savelangdata->name,
					'lang_catdesc' => lang('Category Description'),
					'showcatdesc' => $showlangdata->description,
					'savecatdesc' => $savelangdata->description,
				));

				//Content blocks
				$this->process_blocks($this->contentbo->getblocksforscope($category_id,0));
				$this->t->pfp('out','TranslateCategory');
			}
			$this->common_ui->DisplayFooter();
		}

		function translatePage()
		{
			$GLOBALS['Common_BO']->globalize(array('changelanguage','showlanguage','savelanguage','btnSavePage','savepagetitle','savepagesubtitle','btnSaveBlock','element','blockid','blocktitle'));
			global $changelanguage, $showlanguage, $savelanguage, $btnSavePage, $savepagetitle, $savepagesubtitle,$btnSaveBlock;
			$page_id = $_GET['page_id'];
			
			if ($btnSavePage)
			{
				$page->id = $page_id;
				$page->title = $savepagetitle;
				$page->subtitle = $savepagesubtitle;
				$this->pagebo->savePageLang($page, $savelanguage);
			}
			elseif ($btnSaveBlock)
			{
				$this->save_block();
			}
			$this->common_ui->DisplayHeader();

			$this->t->set_file('TranslatePage', 'translate_page.tpl');
			$this->t->set_file('Blocks','translate_block.tpl');
			$this->t->set_block('Blocks','Blocktranslator');
			$this->t->set_block('Blocktranslator','Version','Vblock');
			$this->t->set_block('Blocks','EditorElement','Eblock');

			//TODO: error handling seems not correct
			if($error)
			{
				$this->t->set_var('error_msg',lang('You failed to fill in one or more required fields.'));
				$page->title = $savepagetitle;
				$page->subtitle = $savepagesubtitle;
			}
			else
			{
				$page = $this->pagebo->getPage($page_id);
				$showlanguage = $showlanguage ? $showlanguage : $this->sitelanguages[0];
				$showlangdata = $this->pagebo->getPage($page_id,$showlanguage);
				$savelanguage = $savelanguage ? $savelanguage : $this->sitelanguages[1]; 
				$savelangdata = $this->pagebo->getPage($page_id,$savelanguage);

				$this->templatehelper();
				$this->t->set_var(Array(
					'translate' => lang('Translate Page'),
					'pageid' => $page_id,
					'lang_pagename' => lang('Page Name'),
					'pagename' => $page->name,
					'lang_pagetitle' => lang('Page Title'),
					'showpagetitle' => $showlangdata->title,
					'savepagetitle' => $savelangdata->title,
					'lang_pagesubtitle' => lang('Page Subtitle'),
					'showpagesubtitle' => $showlangdata->subtitle,
					'savepagesubtitle' => $savelangdata->subtitle,
				));

				//Content blocks
				$this->process_blocks($this->contentbo->getblocksforscope($page->cat_id,$page_id));
				$this->t->pfp('out','TranslatePage');
			}
			$this->common_ui->DisplayFooter();
		}

		function translateSitecontent()
		{
			$GLOBALS['Common_BO']->globalize(array('changelanguage','showlanguage','savelanguage','btnSaveBlock','element','blockid','blocktitle'));
			global $changelanguage, $showlanguage, $savelanguage, $btnSaveBlock;

			if ($btnSaveBlock)
			{
				$this->save_block();
			}

			$this->common_ui->DisplayHeader();
			$this->t->set_file('TranslateSitecontent', 'translate_sitecontent.tpl');
			$this->t->set_file('Blocks','translate_block.tpl');
			$this->t->set_block('Blocks','Blocktranslator');
			$this->t->set_block('Blocktranslator','Version','Vblock');
			$this->t->set_block('Blocks','EditorElement','Eblock');

			$showlanguage = $showlanguage ? $showlanguage : $this->sitelanguages[0];
			$savelanguage = $savelanguage ? $savelanguage : $this->sitelanguages[1]; 

			$this->templatehelper();

			$this->process_blocks($this->contentbo->getblocksforscope(CURRENT_SITE_ID,0));
			$this->t->pfp('out','TranslateSitecontent');
		}

		function process_blocks($blocks)
		{
			global $showlanguage,$savelanguage;

			while (list($id,$block) = @each($blocks))
			{
				$moduleobject = $this->modulebo->createmodule($block->module_name);
				$this->t->set_var('moduleinfo',($block->module_name));

				$savelangtitle = $this->contentbo->getlangblocktitle($id,$savelanguage);
				$showlangtitle = $this->contentbo->getlangblocktitle($id,$showlanguage);
				$savelangversions = $this->contentbo->getallversionsforblock($id,$savelanguage);
				$showlangversions = $this->contentbo->getallversionsforblock($id,$showlanguage);
				$translatorstandardelements = array(
					array('label' => lang('Title'),
						  'value' => ($showlangtitle ? $showlangtitle : $moduleobject->title),
						  'form' => ('<input type="text" name="blocktitle" value="' . 
							($savelangtitle ? $savelangtitle : $moduleobject->title) . '" />')
					)
				);
				$moduleobject->set_block($block);
				$saveblock = $block;
//				$translatormoduleelements = $moduleobject->get_translation_interface($block,$saveblock);

//				$interface = array_merge($translatorstandardelements,$translatormoduleelements);

				$this->t->set_var('standardelements','');
				while (list(,$element) = each($translatorstandardelements))
				{
					$this->t->set_var(Array(
						'label' => $element['label'],
						'value' => $element['value'],
						'form' => $element['form']
					));
					$this->t->parse('standardelements','EditorElement', true);
				}
				$this->t->set_var('Vblock','');
				while (list($version_id,$version) = each($showlangversions))
				{
					//set the version of the block which is referenced by the moduleobject, 
					//so that we retrieve a interface with the current version's arguments 
					$block->set_version($version);
					$saveblock->set_version($savelangversions[$version_id]);
					$translatormoduleelements = $moduleobject->get_translation_interface($block,$saveblock);
						$this->t->set_var(array(
						'version_id' => $version_id,
						'version_state' => $GLOBALS['Common_BO']->state[$version['state']],
						'versionelements' => ''
					));
					while (list(,$element) = each($translatormoduleelements))
					{
						$this->t->set_var(Array(
							'label' => $element['label'],
							'value' => $element['value'],
							'form' => $element['form']
						));
						$this->t->parse('versionelements','EditorElement', true);
					}
					$this->t->parse('Vblock','Version', true);
				}
				$this->t->set_var(Array(
					'blockid' => $id,
				));
				$this->t->parse('blocks','Blocktranslator', true);
			}
		}

		function save_block()
		{
			global $blockid, $element,$blocktitle,$savelanguage;

			$block = CreateObject('sitemgr.Block_SO',True);
			$block->id = $blockid;
			$block->title = $blocktitle;
			$result = $this->contentbo->saveblockdatalang($block,$element,$savelanguage);
			if ($result !== True)
			{
				$this->t->set_var('validationerror', $result);
			}
		}

		function templatehelper()
		{
			global $showlanguage,$savelanguage;
			
			$this->t->set_var(Array(
				'lang_refresh' => '<input type="submit" value="' . lang('Refresh') .'" name="changelanguage">',
				'savebutton' => '<input type="submit" value="Save" name="btnSaveBlock" />',
				'lang_reset' => lang('Reset'),
				'lang_save' => lang('Save')
			));
			$select = '<select name="showlanguage">';
			foreach ($this->sitelanguages as $lang)
			{
				$selected= '';
				if ($lang == $showlanguage)
				{
					$selected = 'selected="selected" ';
				}
				$select .= '<option ' . $selected .'value="' . $lang . '">'. $GLOBALS['Common_BO']->getlangname($lang) . '</option>';
			}
			$select .= '</select> ';
			$this->t->set_var('showlang', $select);

			$select = '<select name="savelanguage">';
			foreach ($this->sitelanguages as $lang)
			{
				$selected= '';
				if ($lang == $savelanguage)
				{
					$selected = 'selected="selected" ';
				}
				$select .= '<option ' . $selected .'value="' . $lang . '">'. $GLOBALS['Common_BO']->getlangname($lang) . '</option>';
			}
			$select .= '</select>';
			$this->t->set_var('savelang', $select);
		}
	}