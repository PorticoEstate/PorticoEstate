<?php
	/*****************************************************************************\
	* phpGroupWare - boForums                                                     *
	* http://www.phpgroupware.org                                                 *
	* Written by Mark A Peters <skeeter@phpgroupware.org>                         *
	* Based off of Jani Hirvinen <jpkh@shadownet.com>                             *
	* -------------------------------------------                                 *
	*  This program is free software; you can redistribute it and/or modify it    *
	*  under the terms of the GNU	General Public License as published by the      *
	*  Free Software Foundation; either version 2 of the License, or (at your     *
	*  option) any later version.                                                 *
	\*****************************************************************************/

	/* $Id$ */

	class uiadmin
	{
		var $public_functions = Array(
			'header'          => True,
			'index'           => True,
			'edit_category'   => True,
			'edit_forum'      => True,
			'add_category'    => True,
			'add_forum'       => True,
			'delete_category' => True,
			'delete_forum'    => True
		);

		var $debug;
		var $bo;
		var $template_dir;
		var $template;
		var $current_page;

		function uiadmin()
		{
			if(!$GLOBALS['phpgw_info']['user']['apps']['admin'])
			{
				Header('Location: '.$GLOBALS['phpgw']->link('/index.php', array ('menuaction' => 'forum.uiforum.index')));
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$GLOBALS['phpgw']->nextmatchs = CreateObject('phpgwapi.nextmatchs');

			$this->bo = CreateObject('forum.boforum');
			$this->debug = $this->bo->debug;

			$this->template_dir = $GLOBALS['phpgw']->common->get_tpl_dir('forum');
			$this->template = CreateObject('phpgwapi.Template',$this->template_dir);
			$info = explode('.',MENUACTION);
			$this->current_page = $info[2];
		}

		function header()
		{
			echo '<table border="0" width="100%" align="center" cellspacing="1" cellpadding="0">'."\n"
				. ' <tr>'."\n"
				. '  <td bgcolor="'.$GLOBALS['phpgw_info']['theme']['bg03'].'" align="left">'.lang('Forums').' '.lang('Admin').'</td>'."\n"
				. ' </tr>'."\n"
				. ' <tr>'."\n"
				. '  <td bgcolor="'.$GLOBALS['phpgw_info']['theme']['row_off'].'" align="left">'
				. '[<font size="-1">'."\n"
				. '<a href="'.$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiadmin.add_category')).'">'.lang('New Category').'</a> '."\n"
				. '| <a href="'.$GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'forum.uiadmin.add_forum')).'">'.lang('New Forum').'</a> '."\n"
				. ($this->current_page!='index'?'| <a href="'.$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiadmin.index')).'">'.lang('Return to Admin').'</a> ':' ')."\n"
				. '| <a href="'.$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiforum.index')).'">'.lang('Return to Forums').'</a>'."\n"
				. '</font>]'
				. '  </td>'."\n".' </tr>'."\n".'</table>'."\n";
		}

		function index()
		{
			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			unset($GLOBALS['phpgw_info']['flags']['noappheader']);
			unset($GLOBALS['phpgw_info']['flags']['noappfooter']);
			$GLOBALS['phpgw']->common->phpgw_header();

			$this->template->set_file(
				Array(
					'INDEX'	=> 'admin.index.tpl'
				)
			);

			$this->template->set_block('INDEX','ForumBlock','ForumB');
			$this->template->set_block('INDEX','CatBlock','CatB');

			$var = Array(
				'FORUM_ADMIN'	=> lang('Forums') . ' '	. lang('Admin'),
				'TB_BG'	=> $GLOBALS['phpgw_info']['theme']['table_bg'],
				//TRY TO FIND A	PERFECT	CHOICE
				// $GLOBALS['phpgw_info']['theme']['bg_color']
	
				'TR_BG'		=> $GLOBALS['phpgw_info']['theme']['bg_color'],
				'CAT_IMG'	=> $GLOBALS['phpgw']->common->image('forum','category'),
				'FORUM_IMG'	=> $GLOBALS['phpgw']->common->image('forum','forum'),
				'CAT_LINK'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiadmin.add_category')),
				'FOR_LINK'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiadmin.add_forum')),
				'MAIN_LINK'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiforum.index')),
				'LANG_CAT'	=> lang('New Category'),
				'LANG_FOR'	=> lang('New Forum'),
				'LANG_MAIN'	=> lang('Return to Forums'),
				'LANG_CURRENT_SUBFORUM'	=> lang('Current Categories and Sub Forums'),
				'LANG_CATEGORY'	=> lang('Category'),
				'LANG_SUBCAT'	=> lang('Sub Category'),
				'LANG_ACTION'	=> lang('Action')
			);
			$this->template->set_var($var);

			$cats = $this->bo->get_all_cat_info();

			$var = Array(
				'BG6'       => $GLOBALS['phpgw_info']['theme']['bg03'],
				'LANG_EDIT'	=> lang('Edit'),
				'LANG_DEL'  => lang('Delete'),
				'LANG_FORUM'   => lang('Forum'),
				'TD_BG'        => 'ffffff'
			);
			$this->template->set_var($var);

			while(list($key,$cat) = each($cats))
			{
				$var = Array(
					'CAT_NAME'  => $cat['name'],
					'CAT_DESC'  => ($cat['descr']?$cat['descr']:'&nbsp;'),
					'EDIT_LINK'	=> $GLOBALS['phpgw']->link('/index.php',
							Array(
								'menuaction'	=> 'forum.uiadmin.edit_category',
								'cat_id'	=> $cat['id']
							)
						),
					'DEL_LINK'  => $GLOBALS['phpgw']->link('/index.php',
							Array(
								'menuaction'	=> 'forum.uiadmin.delete_category',
								'cat_id'	=> $cat['id']
							)
						)
				);
				$this->template->set_var($var);

				$GLOBALS['tr_color'] = $GLOBALS['phpgw_info']['theme']['row_off'];
				//Cleaning the ForumB variable because the blocks use more than	once
				$this->template->set_var('ForumB','');

				$forums = $this->bo->get_forums_for_cat($cat['id']);
				if(sizeof($forums))
				{
					while(list($key,$forum) = each($forums))
					{
						$GLOBALS['tr_color'] = $GLOBALS['phpgw']->nextmatchs->alternate_row_color();
						$var = Array(
							'TR_BG'        => $GLOBALS['tr_color'],
							'SUBCAT_NAME'  => $forum['name'],
							'SUBCAT_DESC'  => ($forum['descr']?$forum['descr']:'&nbsp;'),
							'SUBEDIT_LINK' => $GLOBALS['phpgw']->link('/index.php',
									Array(
										'menuaction'	=> 'forum.uiadmin.edit_forum',
										'cat_id'	=> $cat['id'],
										'forum_id'	=> $forum['id']
									)
								),
							'SUBDEL_LINK'  => $GLOBALS['phpgw']->link('/index.php',
									Array(
										'menuaction'	=> 'forum.uiadmin.delete_forum',
										'cat_id'	=> $cat['id'],
										'forum_id'	=> $forum['id']
									)
								)
						);
						$this->template->set_var($var);
						//Parsing the inner block
						$this->template->fp('ForumB','ForumBlock',true);
					}
				}
				// Parsing the outer block
				$var = Array(
					'TD_BG'		=> 'ffffff',
					'TR_BG'		=> $GLOBALS['tr_color']
				);
				$this->template->set_var($var);
				$this->template->fp('CatB','CatBlock',true);
			}
			$this->template->pfp('Out','INDEX');
		}

		function edit_category()
		{
			$this->category_screen(get_var('cat_id',Array('GET')));
		}

		function edit_forum()
		{
			$this->forum_screen(get_var('forum_id',Array('GET')));
		}

		function add_category()
		{
			$this->category_screen(0);			
		}

		function add_forum()
		{
			$this->forum_screen(0);			
		}

		function delete_category()
		{
			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			$GLOBALS['phpgw_info']['flags']['noappheader'] = True;
			$GLOBALS['phpgw_info']['flags']['noappfooter'] = True;
			$GLOBALS['phpgw']->common->phpgw_header();

			$this->template->set_file(
				Array(
					'DELETE'	=> 'admin.delete.tpl',
					'form_button'	=> 'form_button_script.tpl'
				)
			);

			$cat = $this->bo->get_cat_info($this->bo->cat_id);

			$var = Array(
				'ARE_U_SURE'	=> lang('Are you sure you want to delete this category?'),
				'CAT_NAME'	=> $cat['name']
			);

			$this->template->set_var($var);

			$var = Array(
				'action_url_button'	=> $GLOBALS['phpgw']->link('/index.php',
						Array(
							'menuaction'	=> 'forum.boforum.delete_category',
							'cat_id'	=> get_var('cat_id',Array('GET'))
						)
					),
				'action_text_button'	=> lang('Delete'),
				'action_confirm_button'	=> "onClick=\"return confirm('".lang('All forums, user posts, and topics in this category will be lost!')."')\"",
				'action_extra_field'	=> ''
			);
			$this->template->set_var($var);
			$this->template->parse('YES','form_button');

			$var = Array(
				'action_url_button'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiadmin.index')),
				'action_text_button'	=> lang('Cancel'),
				'action_confirm_button'	=> '',
				'action_extra_field'	=> ''
			);
			$this->template->set_var($var);
			$this->template->parse('NO','form_button');
			$this->template->pfp('Out','DELETE');
		}

		function delete_forum()
		{
			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			$GLOBALS['phpgw_info']['flags']['noappheader'] = True;
			$GLOBALS['phpgw_info']['flags']['noappfooter'] = True;
			$GLOBALS['phpgw']->common->phpgw_header();

			$this->template->set_file(
				Array(
					'DELETE'	=> 'admin.delete.tpl',
					'form_button'	=> 'form_button_script.tpl'
				)
			);

			$cat = $this->bo->get_cat_info($this->bo->cat_id);
			$forum = $this->bo->get_forum_info($this->bo->cat_id,$this->bo->forum_id);

			$var = Array(
				'ARE_U_SURE'	=> lang('Are you sure you want to delete this forum?'),
				'CAT_NAME'	=> $cat['name'].' : '.$forum['name']
			);


			$this->template->set_var($var);

			$var = Array(
				'action_url_button'	=> $GLOBALS['phpgw']->link('/index.php',
						Array(
							'menuaction'	=> 'forum.boforum.delete_forum',
							'cat_id'	=> get_var('cat_id',Array('GET')),
							'forum_id'	=> get_var('forum_id',Array('GET'))
						)
					),
				'action_text_button'	=> lang('Delete'),
				'action_confirm_button'	=> "onClick=\"return confirm('".lang('All user posts, and topics in this forum will be lost!')."')\"",
				'action_extra_field'	=> ''
			);
			$this->template->set_var($var);
			$this->template->parse('YES','form_button');

			$var = Array(
				'action_url_button'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'forum.uiadmin.index')),
				'action_text_button'	=> lang('Cancel'),
				'action_confirm_button'	=> '',
				'action_extra_field'	=> ''
			);
			$this->template->set_var($var);
			$this->template->parse('NO','form_button');
			$this->template->pfp('Out','DELETE');
		}

		function category_screen($cat_id)
		{
			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			unset($GLOBALS['phpgw_info']['flags']['noappheader']);
			unset($GLOBALS['phpgw_info']['flags']['noappfooter']);
			$GLOBALS['phpgw']->common->phpgw_header();

			$this->template->set_file(
				Array(
					'CATEGORY'	=> 'admin.category.tpl'
				)
			);

			$var = Array(
				'FORUM_ADMIN' 	=> lang('Forums') . ' ' . lang('Admin'),
				'TABLEBG'	=> $GLOBALS['phpgw_info']['theme']['th_bg'],
				//TRY TO FIND A PERFECT CHOICE
				'THBG'		=>  $GLOBALS['phpgw_info']['theme']['bg09'],
				//'TRBG'		=> $GLOBALS['phpgw_info']['theme']['row_off'],

				'CAT_LINK'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiadmin.category')),
				'FOR_LINK'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiadmin.forum')),
				'MAIN_LINK'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiforum.index')),
				'ADM_LINK'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiadmin.index')),
				'LANG_ADM_MAIN'	=> lang('Return to Admin'),
				'LANG_CAT'	=> lang('New Category'),
				'LANG_FOR'	=> lang('New Forum'),
				'LANG_MAIN'       => lang('Return to Forums'),
				'LANG_FORUM'	=> lang('Forum Name'),
				'LANG_FORUM_DESC'	=> lang('Forum Description'),
				'LANG_CAT_NAME'	=> lang('Category Name'),
				'LANG_CAT_DESC' => lang('Category Description'),
				'BELONG_TO'	=> lang('Belongs to Category'),
				'ACTION'	=> 'addforum',
				'ACTION_LINK'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.boforum.category')),
				'LANG_ADD_CAT' 	=> lang('Edit Category'),
				'CAT_ID'	=> $cat_id
			);
			$this->template->set_var($var);

			if($cat_id)
			{
				$cat = $this->bo->get_cat_info($cat_id);
				
				$var = Array(
					'BUTTONLANG'	=> lang('Update Category'),
					'CAT_NAME'	=> $cat['name'],
					'CAT_DESC'	=> $cat['descr'],
					'ACTIONTYPE'	=> 'updcat'
				);
				$this->template->set_var($var);
			}
			//Need to set up some var that different for the edit act and add act
			else
			{
				$var = Array(
					'BUTTONLANG' 	=> lang('Add Category'),
					'ACTIONTYPE' 	=> 'addcat'
				);
				$this->template->set_var($var);
			}
			$this->template->pfp('Out','CATEGORY');
		}

		function forum_screen($forum_id)
		{
			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			unset($GLOBALS['phpgw_info']['flags']['noappheader']);
			unset($GLOBALS['phpgw_info']['flags']['noappfooter']);
			$GLOBALS['phpgw']->common->phpgw_header();

			$this->template->set_file(
				Array(
					'FORUM'	=> 'admin.forum.tpl'
				)
			);

			$var = Array(
				'FORUM_ADMIN' 	=> lang('Forums') . ' ' . lang('Admin'),
				'TABLEBG'	=> $GLOBALS['phpgw_info']['theme']['th_bg'],
				//TRY TO FIND A PERFECT CHOICE
				'THBG'		=>  $GLOBALS['phpgw_info']['theme']['bg09'],
				//'TRBG'		=> $GLOBALS['phpgw_info']['theme']['row_off'],

				'CAT_LINK'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiadmin.category')),
				'FOR_LINK'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiadmin.forum')),
				'MAIN_LINK'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiforum.index')),
				'ADM_LINK'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiadmin.index')),
				'LANG_ADM_MAIN'	=> lang('Return to Admin'),
				'LANG_CAT'	=> lang('New Category'),
				'LANG_FOR'	=> lang('New Forum'),
				'LANG_MAIN'       => lang('Return to Forums'),
				'LANG_FORUM'	=> lang('Forum Name'),
				'LANG_FORUM_DESC'	=> lang('Forum Description'),
				'LANG_CAT_NAME'	=> lang('Category Name'),
				'LANG_CAT_DESC' => lang('Category Description'),
				'BELONG_TO'	=> lang('Belongs to Category'),
				'ACTION'	=> 'addforum',
				'ACTION_LINK'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.boforum.forum')),
				'FORID'	=> $forum_id,
				'CATID'	=> $this->bo->cat_id
			);
			$this->template->set_var($var);

			// for the drop down category
			$cats = $this->bo->get_all_cat_info();
			while(list($key,$cat) = each($cats))
			{
				$this->template->set_var('SELECTED','<option'.($this->bo->cat_id == $cat['id']?' selected':'').' value="'.$cat['id'].'">'.$cat['name'].'</option>');
				$this->template->parse('DROP_DOWN','SELECTED',true);
			}

			if($forum_id)
			{
				$forum = $this->bo->get_forum_info($this->bo->cat_id,$forum_id);
				
				$var = Array(
					'BUTTONLANG'	=> lang('Update Forum'),
					'LANG_ADD_FORUM' 	=> lang('Edit Forum'),
					'FORUM_NAME'	=> $forum['name'],
					'FOR_DESC'	=> $forum['descr'],
					'ACTIONTYPE'	=> 'updfor'
				);
				$this->template->set_var($var);
			}
			//Need to set up some var that different for the edit act and add act
			else
			{
				$var = Array(
					'BUTTONLANG' 	=> lang('Add Forum'),
					'LANG_ADD_FORUM' 	=> lang('Add Forum'),
					'ACTIONTYPE' 	=> 'addfor'
				);
				$this->template->set_var($var);
			}
			$this->template->pfp('Out','FORUM');
		}

	}
?>
