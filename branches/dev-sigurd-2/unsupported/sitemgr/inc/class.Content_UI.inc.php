<?php

	class Content_UI
	{
		var $common_ui;
		var $t;
		var $bo;
		var $modulebo;
		var $acl;
		var $viewable;
		var $sitelanguages;
		var $worklanguage;
		var $errormsg;
		var $langselect;

		var $public_functions = array
		(
			'manage' => True,
			'commit' => True,
			'archive' => True
		);

		function Content_UI()
		{
			$this->common_ui = CreateObject('sitemgr.Common_UI',True);
			$this->t = $GLOBALS["phpgw"]->template;
			$this->bo = &$GLOBALS['Common_BO']->content;
			$this->acl = &$GLOBALS['Common_BO']->acl;
			$this->modulebo = &$GLOBALS['Common_BO']->modules;
			$this->viewable = array(
				SITEMGR_VIEWABLE_EVERBODY => lang('everybody'),
				SITEMGR_VIEWABLE_USER => lang('phpgw users'),
				SITEMGR_VIEWABLE_ADMIN => lang('administrators'),
				SITEMGR_VIEWABLE_ANONYMOUS => lang('anonymous')
			);

			$this->sitelanguages = $GLOBALS['Common_BO']->sites->current_site['sitelanguages'];
			$savelanguage = $_POST['savelanguage'];
			if ($savelanguage)
			{
				//we save the language the user chooses as session variable
				$this->worklanguage = $savelanguage;
				$GLOBALS['phpgw']->session->appsession('worklanguage','sitemgr',$savelanguage);
			}
			else
			{
				$sessionlang = $GLOBALS['phpgw']->session->appsession('worklanguage','sitemgr');
				$this->worklanguage = $sessionlang ? $sessionlang : $this->sitelanguages[0];
			}
			$this->errormsg = array();

			if (count($this->sitelanguages) > 1)
			{
				$this->langselect = lang('as') . ' <select name="savelanguage">';
				foreach ($this->sitelanguages as $lang)
				{
					$selected= '';
                                        if ($lang == $this->worklanguage)
                                        {
                                                $selected = 'selected="selected" ';
                                        }
                                        $this->langselect .= '<option ' . $selected .'value="' . $lang . '">'. $GLOBALS['Common_BO']->getlangname($lang) . '</option>';
                                }
                                $this->langselect .= '</select> ';
                        }
		}

		function manage()
		{
			$GLOBALS['Common_BO']->globalize(array(
				'inputblockid','inputblocktitle','inputblocksort','inputblockview',
				'inputstate','btnSaveBlock','btnDeleteBlock','btnCreateVersion',
				'btnDeleteVersion','inputmoduleid','inputarea','btnAddBlock','element'
			));
			global $inputblockid, $inputblocktitle, $inputblocksort,$inputblockview;
			global $inputstate,$btnSaveBlock,$btnDeleteBlock,$btnCreateVersion;
			global $inputmoduleid, $inputarea, $btnAddBlock, $btnDeleteVersion, $element;

			global $page_id,$cat_id;
			$page_id = $_GET['page_id'];
			$cat_id = $_GET['cat_id'];
			$block_id = $_GET['block_id'];

			if ($block_id)
			{}
			elseif ($page_id)
			{
				$page = $GLOBALS['Common_BO']->pages->getPage($page_id);
				if (!$GLOBALS['Common_BO']->acl->can_write_category($page->cat_id))
				{
					$GLOBALS['phpgw']->redirect($GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sitemgr.Outline_UI.manage')));
					return;
				}
				$page_or_cat_name = $page->name;
				$cat_id = $page->cat_id;
				$goto = lang('Page manager');
				$scopename = lang('Page');
			}
			elseif ($cat_id != CURRENT_SITE_ID)
			{
				$cat = $GLOBALS['Common_BO']->cats->getCategory($cat_id);
				if (!$GLOBALS['Common_BO']->acl->can_write_category($cat_id))
				{
					$GLOBALS['phpgw']->redirect($GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sitemgr.Outline_UI.manage')));
					return;
				}
				$page_or_cat_name = $cat->name;
				$page_id = 0;
				$goto = lang('Category manager');
				$scopename = lang('Category');
			}
			else
			{
				$page_id = 0;
				$scopename = lang('Site');
			}

			if ($btnAddBlock)
			{
				if ($inputmoduleid)
				{
					$block = CreateObject('sitemgr.Block_SO',True);
					$block->module_id = $inputmoduleid;
					$block->area = $inputarea;
					$block->page_id = $page_id;
					$block->cat_id = $cat_id;

					$newblock = $this->bo->addblock($block);
					if ($newblock)
					{
						$this->bo->createversion($newblock);
					}
					else
					{
						$this->errormsg[] = lang("You are not entitled to create module %1 on this scope",$inputmoduleid);
					}
				}
				else
				{
					$this->errormsg[] = lang("You did not choose a module.");
				}
			}
			elseif ($btnSaveBlock)
			{
				$block = CreateObject('sitemgr.Block_SO',True);
				$block->id = $inputblockid;
				$block->title = $inputblocktitle;
				$block->sort_order = $inputblocksort;
				$block->view = $inputblockview;
				$result = $this->bo->saveblockdata($block,$element,$inputstate,$this->worklanguage);
				if ($result !== True)
				{
					//result should be an array of validationerrors
					$this->errormsg = $result;
				}
			}
			elseif ($btnDeleteBlock)
			{
				if (!$this->bo->removeblock($inputblockid))
				{
					$this->errormsg[] =  lang("You are not entitled to edit block %1",$inputblockid);
				}
				//if we delete a block we were editing, there is nothing left to do
				if ($block_id)
				{
					echo '<html><head></head><body onload="opener.location.reload();self.close()"></body></html>';
				}
			}
			elseif ($btnCreateVersion)
			{
				$this->bo->createversion($inputblockid);
			}
			elseif ($btnDeleteVersion)
			{
				$version_id = array_keys($btnDeleteVersion);
				$this->bo->deleteversion($version_id[0]);
			}

			//if we are called with a block_id GET parameter, it is from sitemgr-site edit mode or from archiv/commit
			//we are shown in a separate edit window, without navbar. 
			if ($block_id)
			{
				$block = $this->bo->getblock($block_id,$this->worklanguage);
				if (!($block && $GLOBALS['Common_BO']->acl->can_write_category($block->cat_id)))
				{
					echo '<p><center><b>'.lang('Attempt to edit non-editable block').'</b></center>';
					$GLOBALS['phpgw']->common->phpgw_exit(True);
				}
				$this->t->set_file('Blocks', 'edit_block.tpl');
				$this->t->set_block('Blocks','Block');
				$this->t->set_block('Block','Moduleeditor','MeBlock');
				$this->t->set_block('Block','Moduleview','MvBlock');
				$this->t->set_block('Moduleeditor','Version','EvBlock');
				$this->t->set_block('Blocks','EditorElement','EeBlock');
				$this->t->set_block('Moduleview','ViewElement','VeBlock');

				$this->t->set_var(array(
					'savebutton' => lang('Save block'),
					'deletebutton' => lang('Delete block'),
					'contentarea' => lang('Contentarea'),
					'createbutton' => lang('Create new version'),
					'standalone' => "<html><head></head><body>",
					'donebutton' => '<input type="reset" onclick="opener.location.reload();self.close()" value="' . lang('Done') . '"  />'
				));
				$this->showblock($block,True,True);
				$this->t->pfp('out','Block');
				return;
			}

			$this->common_ui->DisplayHeader();

			$this->t->set_file('Managecontent', 'manage_content.tpl');
			$this->t->set_file('Blocks','edit_block.tpl');
			$this->t->set_block('Managecontent','Contentarea','CBlock');
			$this->t->set_block('Blocks','Block');
			$this->t->set_block('Block','Moduleeditor','MeBlock');
			$this->t->set_block('Block','Moduleview','MvBlock');
			$this->t->set_block('Moduleeditor','Version','EvBlock');
			$this->t->set_block('Blocks','EditorElement','EeBlock');
			$this->t->set_block('Moduleview','ViewElement','VeBlock');
			$this->t->set_var(Array(
				'content_manager' => lang('%1 content manager', $scopename),
				'page_or_cat_name' => ($page_or_cat_name ? (' - ' . $page_or_cat_name) : '')
				));


			$contentareas = $this->bo->getContentAreas();
			if (is_array($contentareas))
			{
				$this->t->set_var(array(
					'help' => lang('You can override each content blocks default title. Be aware that not in all content areas the block title will be visible.'),
					'savebutton' => lang('Save block'),
					'deletebutton' => lang('Delete block'),
					'contentarea' => lang('Contentarea'),
					'createbutton' => lang('Create new version'),
				));

				foreach ($contentareas as $contentarea)
				{
					$permittedmodules = $this->modulebo->getcascadingmodulepermissions($contentarea,$cat_id);

					$this->t->set_var(Array(
						'area' => $contentarea,
						'addblockform' => 
							($permittedmodules ?
								('<form method="POST"><input type="hidden" value="' . $contentarea . '" name="inputarea" />' .
									'<select style="vertical-align:middle" size="10" name="inputmoduleid">' .
									$this->inputmoduleselect($permittedmodules) .
									'</select><input type="submit" name="btnAddBlock" value="' .
									lang('Add block to content area %1',$contentarea) .
									'" /></form>') :
								lang('No modules permitted for this content area/category')
							),
						'error' => (($contentarea == $inputarea) && $this->errormsg) ? join('<br>',$this->errormsg) : '',
					));

					//we get all blocks for the page and its category, and site wide,
					//but only the current scope is editable
					//if we have just edited a block in a certain language, we request all blocks in this language
					$blocks = $this->bo->getallblocksforarea($contentarea,$cat_id,$page_id,$this->worklanguage);

					$this->t->set_var('blocks','');

					if ($blocks)
					{
						while (list(,$block) = each($blocks))
						{
							//if the block is in our scope and we are entitled we edit it
							$editable = ($block->page_id == $page_id && $block->cat_id == $cat_id);
							$this->showblock($block,$editable);
							$this->t->parse('blocks','Block', true);
						}
					}
					$this->t->parse('CBlock','Contentarea', true);
				}
			}
			else
			{
				$this->t->set_var('CBlock',$contentareas);
			}
			$this->t->pfp('out', 'Managecontent');
			$this->common_ui->DisplayFooter();
		}

		function commit()
		{
			if ($_POST['btnCommit'])
			{
				while(list($cat_id,) = @each($_POST['cat']))
				{
					$GLOBALS['Common_BO']->cats->commit($cat_id);
				}
				while(list($page_id,) = @each($_POST['page']))
				{
					$GLOBALS['Common_BO']->pages->commit($page_id);
				}
				while(list($block_id,) = @each($_POST['block']))
				{
					$this->bo->commit($block_id);
				}
			}
			$this->common_ui->DisplayHeader();

			$this->t->set_file('Commit','commit.tpl');
			$this->t->set_block('Commit','Category','Cblock');
			$this->t->set_block('Commit','Page','Pblock');
			$this->t->set_block('Commit','Block','Bblock');
			$this->t->set_var(array(
				'commit_manager'	=> lang('Commit changes'),
				'lang_categories'	=> lang('Categories'),
				'lang_pages'		=> lang('Pages'),
				'lang_blocks'		=> lang('Content blocks'),
				'lang_commit'		=> lang('Commit changes'),
				'lang_select_all'	=> lang('select all')
			));

			//Categories
			$cats = $GLOBALS['Common_BO']->cats->getpermittedcatsCommitable();
			while (list(,$cat_id) = @each($cats))
			{
				$cat = $GLOBALS['Common_BO']->cats->getCategory($cat_id,$this->sitelanguages[0]);
				$this->t->set_var(array(
					'category' => $cat->name,
					'catid' => $cat_id,
					'addedorremoved' => ($cat->state == SITEMGR_STATE_PREPUBLISH) ? 'added' : 'removed',
					'edit' => $GLOBALS['phpgw']->link('/index.php',array(
						'cat_id' => $cat_id,
						'menuaction' => 'sitemgr.Categories_UI.edit'
					))
				));
				$this->t->parse('Cblock','Category',True);
			}

			//Pages
			$pages = $GLOBALS['Common_BO']->pages->getpageIDListCommitable();

			while (list(,$page_id) = @each($pages))
			{
				$page = $GLOBALS['Common_BO']->pages->getPage($page_id);
				$this->t->set_var(array(
					'page' => $page->name,
					'pageid' => $page_id,
					'addedorremoved' => ($page->state == SITEMGR_STATE_PREPUBLISH) ? 'added' : 'removed',
					'edit' => $GLOBALS['phpgw']->link('/index.php',array(
						'page_id' => $page_id,
						'menuaction' => 'sitemgr.Pages_UI.edit'
					))
				));
				$this->t->parse('Pblock','Page',True);
			}

			//Content Blocks
			$blocks = $this->bo->getcommitableblocks();
			while (list($block_id,$block) = @each($blocks))
			{
				$this->t->set_var(array(
					'block' => $this->bo->getlangblocktitle($block_id,$this->sitelanguages[0]),
					'blockid' => $block_id,
					'scope' => $this->blockscope($block->cat_id,$block->page_id),
					'addedorremovedorreplaced' => ($block->cnt == 2) ? 'replaced' : 
						(($block->state == SITEMGR_STATE_PREPUBLISH) ? 'added' : 'removed'),
					'edit' =>  $GLOBALS['phpgw']->link('/index.php',array(
						'block_id' => $block_id,
						'menuaction' => 'sitemgr.Content_UI.manage'
					))
				));
				$this->t->parse('Bblock','Block',True);
			}

			$this->t->pfp('out', 'Commit');
			$this->common_ui->DisplayFooter();
		}

		function archive()
		{
			if ($_POST['btnReactivate'])
			{
				while(list($cat_id,) = @each($_POST['cat']))
				{
					$GLOBALS['Common_BO']->cats->reactivate($cat_id);
				}
				while(list($page_id,) = @each($_POST['page']))
				{
					$GLOBALS['Common_BO']->pages->reactivate($page_id);
				}
				while(list($block_id,) = @each($_POST['block']))
				{
					$this->bo->reactivate($block_id);
				}
			}

			$this->common_ui->DisplayHeader();

			$this->t->set_file('Commit','archive.tpl');
			$this->t->set_block('Commit','Category','Cblock');
			$this->t->set_block('Commit','Page','Pblock');
			$this->t->set_block('Commit','Block','Bblock');
			$this->t->set_var(array(
				'commit_manager' => lang('Archived content'),
				'lang_categories' => lang('Categories'),
				'lang_pages' => lang('Pages'),
				'lang_blocks' => lang('Content blocks'),
				'lang_reactivate' => lang('Reactivate content')
			));

			//Categories
			$cats = $GLOBALS['Common_BO']->cats->getpermittedcatsArchived();
			//we have to append the archived cats to the currentcats, in order to be able to access them later
			$GLOBALS['Common_BO']->cats->currentcats = array_merge($GLOBALS['Common_BO']->cats->currentcats,$cats);
			while (list(,$cat_id) = @each($cats))
			{
				$cat = $GLOBALS['Common_BO']->cats->getCategory($cat_id,$this->sitelanguages[0],True);
				$this->t->set_var(array(
					'category' => $cat->name,
					'catid' => $cat_id,
					'edit' => $GLOBALS['phpgw']->link('/index.php',array(
						'cat_id' => $cat_id,
						'menuaction' => 'sitemgr.Categories_UI.edit'
					))
				));
				$this->t->parse('Cblock','Category',True);
			}

			//Pages
			$pages = $GLOBALS['Common_BO']->pages->getpageIDListArchived();

			while (list(,$page_id) = @each($pages))
			{
				$page = $GLOBALS['Common_BO']->pages->getPage($page_id);
				$this->t->set_var(array(
					'page' => $page->name,
					'pageid' => $page_id,
					'edit' => $GLOBALS['phpgw']->link('/index.php',array(
						'page_id' => $page_id,
						'menuaction' => 'sitemgr.Pages_UI.edit'
					))
				));
				$this->t->parse('Pblock','Page',True);
			}

			//Content Blocks
			$blocks = $this->bo->getarchivedblocks();
			while (list($block_id,$block) = @each($blocks))
			{
				$this->t->set_var(array(
					'block' => $this->bo->getlangblocktitle($block_id,$this->sitelanguages[0]),
					'blockid' => $block_id,
					'scope' => $this->blockscope($block->cat_id,$block->page_id),
					'edit' =>  $GLOBALS['phpgw']->link('/index.php',array(
						'block_id' => $block_id,
						'menuaction' => 'sitemgr.Content_UI.manage'
					))
				));
				$this->t->parse('Bblock','Block',True);
			}

			$this->t->pfp('out', 'Commit');
			$this->common_ui->DisplayFooter();
		}

		function inputmoduleselect($modules)
		{
			$returnValue = '';
			while (list($id,$module) = each($modules))
			{ 
				$returnValue.='<option title="' . $module['description'] . '" value="'.$id.'">'.
					$module['module_name'].'</option>'."\n";
			}
			return $returnValue;
		}

		function inputviewselect($default)
		{
			$returnValue = '';
			foreach($this->viewable as $value => $display)
			{
				$selected = ($default == $value) ? $selected = 'selected="selected" ' : '';
				$returnValue.='<option '.$selected.'value="'.$value.'">'.
					$display.'</option>'."\n";
			}
			return $returnValue;
		}

		function blockscope($cat_id,$page_id)
		{
			if ($cat_id == CURRENT_SITE_ID)
			{
				$scope = lang('Site wide');
			}
			else
			{
				$cat = $GLOBALS['Common_BO']->cats->getCategory($cat_id);
				$scope = lang('Category') . ' ' . $cat->name;
				if ($page_id)
				{
					$page = $GLOBALS['Common_BO']->pages->getPage($page_id);
					$scope .= ' - ' . lang('Page') . ' ' . $page->name;
				}
			}
			return $scope;
		}

		//if the block is shown on its own ($standalone), we add information about its,scope 
		function showblock($block,$editable,$standalone=False)
		{
			global $page_id,$cat_id, $inputblockid;
			//TODO: wrap a module storage around createmodule as in template3, 
			//TODO: so that we do not create the same module object twice
			$moduleobject = $this->modulebo->createmodule($block->module_name);

			$this->t->set_var(array(
				'moduleinfo' => ($block->module_name),
				'description' => $moduleobject->description,
				'savelang' => $this->langselect
			));

			//if the block is in our scope and we are entitled we edit it
			if ($editable)
			{
				$editorstandardelements = array(
					array('label' => lang('Title'),
						  'form' => ('<input type="text" name="inputblocktitle" value="' . 
							($block->title ? $block->title : $moduleobject->title) . '" />')
					),
					array('label' => lang('Seen by'),
						  'form' => ('<select name="inputblockview">' .
							$this->inputviewselect((int)$block->view) . '</select>')
					),
					array('label' => lang('Sort order'),
						  'form' => ('<input type="text" name="inputblocksort" size="2" value="' .
							(int)$block->sort_order . '">')
					)
				);
				if ($standalone)
				{
					$editorstandardelements[] = array(
						'label' => lang('Scope'),
						'form' => $this->blockscope($block->cat_id,$block->page_id)
					);
				}

				$moduleobject->set_block($block);

				$this->t->set_var(Array(
					'blockid' => $block->id,
					'validationerror' => (($block->id == $inputblockid) && $this->errormsg) ? join('<br>',$this->errormsg) : '',
				));
				$this->t->set_var('standardelements','');
				while (list(,$element) = each($editorstandardelements))
				{
					$this->t->set_var(Array(
						'label' => $element['label'],
						'form' => $element['form']
					));
					$this->t->parse('standardelements','EditorElement', true);
				}

				$versions = $this->bo->getallversionsforblock($block->id,$this->worklanguage);
				$this->t->set_var('EvBlock','');
				while (list($version_id,$version) = each($versions))
				{
					//set the version of the block which is referenced by the moduleobject, 
					//so that we retrieve a interface with the current version's arguments 
					$block->set_version($version);
					$editormoduleelements = $moduleobject->get_user_interface();
					$this->t->set_var(array(
						'version_id' => $version_id,
						'state' => $GLOBALS['Common_BO']->inputstateselect($version['state']),
						'deleteversion' => lang('Delete Version'),
						'versionelements' => ''
					));
					while (list(,$element) = each($editormoduleelements))
					{
						$this->t->set_var(Array(
							'label' => $element['label'],
							'form' => $element['form']
						));
						$this->t->parse('versionelements','EditorElement', true);
					}
					$this->t->parse('EvBlock','Version', true);
				}

				$this->t->parse('MeBlock','Moduleeditor');
				$this->t->set_var('MvBlock','');
			}
			//otherwise we only show it
			else
			{
				if ($block->page_id)
				{
					$blockscope = lang('Page');
				}
				elseif ($block->cat_id != CURRENT_SITE_ID)
				{
					$cat = $GLOBALS['Common_BO']->cats->getCategory($block->cat_id);
					$blockscope =  lang('Category') . ' - ' . $cat->name;
				}
				else
				{
					$blockscope =  lang('Site');
				}

				$viewstandardelements = array(
					array('label' => lang('Scope'),
						  'value' => $blockscope
					),
					array('label' => lang('Title'),
						  'value' => ($block->title ? $block->title : $moduleobject->title)
					),
					array('label' => lang('Seen by'),
						  'value' => $this->viewable[(int)$block->view]
					),
					array('label' => lang('Sort order'),
						  'value' => (int)$block->sort_order
					)
				);
// 								$viewmoduleelements = array();
// 								while (list($argument,$argdef) = @each($moduleobject->arguments))
// 								{
// 									$value = $block->arguments[$argument];
// 									$viewmoduleelements[] = array(
// 										'label' => $argdef['label'],
// 										'value' => $GLOBALS['phpgw']->strip_html($value)
// 									);
// 								}
//								$interface = array_merge($viewstandardelements,$viewmoduleelements);
$interface = $viewstandardelements;
				$this->t->set_var('VeBlock','');
				while (list(,$element) = each($interface))
				{
					$this->t->set_var(Array(
						'label' => $element['label'],
						'value' => $element['value'])
					);
					$this->t->parse('VeBlock','ViewElement', true);
				}
				$this->t->parse('MvBlock','Moduleview');
				$this->t->set_var('MeBlock','');
			}
		}
	}

