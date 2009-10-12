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

	class Categories_UI
   	{
		var $common_ui;
		var $cat_bo;
		var $acl;
		var $isadmin;
		var $t;
		var $sitelanguages;
		
		var $public_functions = array
		(
			'edit' => True,
			'delete' => True
		);
			
		function Categories_UI()
		{
			$this->common_ui = CreateObject('sitemgr.Common_UI',True);
			$this->t = $GLOBALS['phpgw']->template;
			$this->cat_bo = $GLOBALS['Common_BO']->cats;
			$this->cat = CreateObject('sitemgr.Category_SO', True);
			$this->acl = $GLOBALS['Common_BO']->acl;
			$this->isadmin = $this->acl->is_admin();
			$this->sitelanguages = $GLOBALS['Common_BO']->sites->current_site['sitelanguages'];
		}

		function edit()
		{
			if (!$this->isadmin)
			{
				$GLOBALS['phpgw']->redirect($GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sitemgr.Outline_UI.manage')));
				return False;
			}

			$GLOBALS['Common_BO']->globalize(array(
				'btnSave','inputcatname','inputcatdesc','inputcatid','inputsortorder','inputparent','inputstate',
				'inputparentold','savelanguage','inputgetparentpermissions','inputapplypermissionstosubs',
				'inputgroupaccessread','inputgroupaccesswrite','inputindividualaccessread','individualaccesswrite'
			));

			global $btnSave, $inputcatid,$inputcatname,$inputcatdesc,$inputsortorder,$inputparent,$inputparentold,$inputstate;
			global $inputgroupaccessread, $inputgroupaccesswrite, $inputindividualaccessread, $inputindividualaccesswrite;
			global $savelanguage, $inputgetparentpermissions,$inputapplypermissionstosubs;
			$cat_id = $inputcatid ? $inputcatid : $_GET['cat_id'];

			if ($btnSave && $inputcatname && $inputcatdesc)
			{
				if ($inputcatname == '' || $inputcatdesc == '')
				{
					$error = lang('You failed to fill in one or more required fields.');
					$this->t->set_var('message',$error);
				}
				else
				{
					$cat_id =  $cat_id ? $cat_id : $this->cat_bo->addCategory('','');

					$groupaccess = array_merge_recursive($inputgroupaccessread, $inputgroupaccesswrite);
					$individualaccess = array_merge_recursive($inputindividualaccessread, $inputindividualaccesswrite);
					$savelanguage = $savelanguage ? $savelanguage : $this->sitelanguages[0];
					$this->cat_bo->saveCategoryInfo($cat_id, $inputcatname, $inputcatdesc, $savelanguage, $inputsortorder, $inputstate, $inputparent, $inputparentold);
					if ($inputgetparentpermissions)
					{
						$this->cat_bo->saveCategoryPermsfromparent($cat_id);
					}
					else
					{
						$this->cat_bo->saveCategoryPerms($cat_id, $groupaccess, $individualaccess);
					}
					if ($inputapplypermissionstosubs)
					{
						$this->cat_bo->applyCategoryPermstosubs($cat_id);
					}
					$this->t->set_var('message',lang('Category saved'));
				}
			}

			if ($cat_id)
			{
				//we use force here since we might edit an archive category
				$cat = $this->cat_bo->getCategory($cat_id,$this->sitelanguages[0],True);
			}

			$this->t->set_file('EditCategory', 'edit_category.tpl');
			$this->t->set_block('EditCategory','GroupBlock', 'GBlock');

			if (count($this->sitelanguages) > 1)
			{
				$select = lang('as') . ' <select name="savelanguage">';
				foreach ($this->sitelanguages as $lang)
				{
					$select .= '<option value="' . $lang . '">'. $GLOBALS['Common_BO']->getlangname($lang) . '</option>';
				}
				$select .= '</select> ';
				$this->t->set_var('savelang',$select);
			}

			$this->t->set_var(array(
				'add_edit' => ($cat_id ? lang('Edit Category') : lang('Add Category')),
				'cat_id' => $cat_id,
				'catname' => $cat->name,
				'catdesc' => $cat->description,
				'sort_order' => $cat->sort_order,
				'parent_dropdown' => $this->getParentOptions($cat->parent,$cat_id),
				'stateselect' => $GLOBALS['Common_BO']->inputstateselect($cat->state),
				'old_parent' => $cat->parent,
				'lang_basic' => lang('Basic Settings'),
				'lang_catname' => lang('Category Name'),
				'lang_catsort' => lang('Sort Order'),
				'lang_catparent' => lang('Parent'),
				'lang_catdesc' => lang('Category Description'),
				'lang_groupaccess' => lang('Group Access Permissions'),
				'lang_groupname' => lang('Group Name'),
				'lang_readperm' => lang('Read Permission'),
				'lang_writeperm' => lang('Write Permission'),
				'lang_implies' => lang('implies read permission'),
				'lang_useraccess' => lang('Individual Access Permissions'),
				'lang_username' => lang('User Name'),
				'lang_reset' => lang('Reset'),
				'lang_save' => lang('Save'),
				'lang_state' => lang('State'),
				'lang_done' => lang('Done'),
				'lang_getparentpermissions' => lang('Fill in permissions from parent category? If you check this, below values will be ignored'),
				'lang_applypermissionstosubs' => lang('Apply permissions also to subcategories?'),
			));
		
			$acct = CreateObject('phpgwapi.accounts');
			$grouplist = $this->acl->get_group_list();
			$permissionlist = ($cat_id ? $this->acl->get_group_permission_list($cat_id) : array());
			if($grouplist)
			{
				for($i = 0; $i < count($grouplist); $i++ )
				{
					//$account_name = $acct->id2name($permissionlist[$i]['account_id']);
					//$this->t->set_var('group_id',$permissionlist[$i]['account_id']);
					$account_name = $grouplist[$i]['account_lid'];
					$account_id = $grouplist[$i]['account_id'];
					$this->t->set_var('group_id',$account_id);
					if ($cat_id)
					{
						$permission_id = $permissionlist[$account_id];
					}
					else
					{
						$permission_id = 0;
					}

					$this->t->set_var('groupname', $account_name);
					if ($permission_id & PHPGW_ACL_READ)  
					{
						$this->t->set_var('checkedgroupread','CHECKED');
					}
					else
					{
						$this->t->set_var('checkedgroupread','');
					}
					if ($permission_id & PHPGW_ACL_ADD)
					{
						$this->t->set_var('checkedgroupwrite','CHECKED');
					}
					else
					{
						$this->t->set_var('checkedgroupwrite','');
					}

					$this->t->parse('GBlock', 'GroupBlock', True);
				}
			}
			else
			{
				$this->t->set_var('groupname',lang("No groups defined."));
			}

			$this->t->set_block('EditCategory','UserBlock', 'UBlock');

			$userlist = $this->acl->get_user_list();
			$userpermissionlist = $this->acl->get_user_permission_list($cat_id);
			if($userlist)
			{
				for($i = 0; $i < count($userlist); $i++ )
				{
					$user_name = $userlist[$i]['account_lid'];
					$user_id = $userlist[$i]['account_id'];
					if ($cat_id)
					{
						$user_permission_id = $userpermissionlist[$user_id];
					}
					else
					{
						$user_permission_id = 0;
					}
					$this->t->set_var('user_id', $user_id);
					
					$this->t->set_var('username', $user_name);
					if ($user_permission_id & PHPGW_ACL_READ )
					{
						$this->t->set_var('checkeduserread','CHECKED');
					}
					else
					{
						$this->t->set_var('checkeduserread','');
					}
					if ($user_permission_id & PHPGW_ACL_ADD )
					{
						$this->t->set_var('checkeduserwrite','CHECKED');
					}
					else
					{
						$this->t->set_var('checkeduserwrite','');
					}
					$this->t->parse('UBlock', 'UserBlock', True);
				}
			}
			else
			{
				$this->t->set_var('username',lang("No users defined."));
			}

			$this->t->pfp('out','EditCategory');

			$this->common_ui->DisplayFooter();
		}

		function getParentOptions($selected_id=0,$skip_id=0)
		{
			$option_list=$this->cat_bo->getCategoryOptionList();
			if (!$selected_id)
			{
				$selected=' SELECTED';
			}
			if (!$skip_id)
			{
				$skip_id = -1;
			}
			$retval="\n".'<SELECT NAME="inputparent">'."\n";
			foreach($option_list as $option)
			{
				if ($option['value']!=$skip_id)
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

		function delete()
		{
			if (!$this->isadmin)
			{
				$GLOBALS['phpgw']->redirect($GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Outline_UI.manage'));
				return;
			}

			$GLOBALS['Common_BO']->globalize(array('btnDelete','btnCancel'));
			global $btnDelete,$btnCancel;
			$cat_id = $_GET['cat_id'];

			if ($btnDelete)
			{
				$this->cat_bo->removeCategory($cat_id);
				$GLOBALS['phpgw']->redirect($GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Outline_UI.manage'));
				return;
			}
			if ($btnCancel)
			{
				$GLOBALS['phpgw']->redirect($GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Outline_UI.manage'));
				return;
			}

			$this->common_ui->DisplayHeader();

			$cat = $this->cat_bo->getCategory($cat_id,$this->sitelanguages[0]);
			$this->t->set_file('ConfirmDelete','confirmdelete.tpl');
			$this->t->set_var('deleteheader',lang('Are you sure you want to delete the category %1 and all of its associated pages?  You cannot retrieve the deleted pages if you continue.',$cat->name));
			$this->t->set_var('cat_id',$cat_id);
			$this->t->set_var('lang_yes',lang('Yes, please delete it'));
			$this->t->set_var('lang_no',lang('Cancel the delete'));
			$this->t->pfp('out','ConfirmDelete');
		}
	}
?>
