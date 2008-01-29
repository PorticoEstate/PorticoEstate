<?php
	/*****************************************************************************\
	* phpGroupWare - uiForums                                                     *
	* http://www.phpgroupware.org                                                 *
	* Written by Mark A Peters <skeeter@phpgroupware.org>                         *
	* Based off of Jani Hirvinen <jpkh@shadownet.com>                             *
	* -------------------------------------------                                 *
	*  This program is free software; you can redistribute it and/or modify it    *
	*  under the terms of the GNU General Public License as published by the      *
	*  Free Software Foundation; either version 2 of the License, or (at your     *
	*  option) any later version.                                                 *
	\*****************************************************************************/

	/* $Id: class.uiforum.inc.php 18290 2007-09-27 12:07:49Z skwashd $ */

	class uiforum
	{
		var $public_functions = array(
			'index' => True,
			'forum' => True,
			'threads' => True,
			'read'  => True,
			'post'  => True
		);

		var $debug;
		var $bo;
		var $template_dir;
		var $template;

		function uiforum($session=0)
		{
			$GLOBALS['phpgw']->nextmatchs = CreateObject('phpgwapi.nextmatchs');
			$this->bo = CreateObject('forum.boforum',1);
			$this->debug = $this->bo->debug;
			if($this->bo->use_session)
			{
				$this->save_sessiondata();
			}

			if($this->debug)
			{
				$this->_debug_sqsof();
			}
			$this->template_dir = $GLOBALS['phpgw']->common->get_tpl_dir('forum');
			$this->template = CreateObject('phpgwapi.Template',$this->template_dir);
		}

		/* Private functions */
		function _debug_sqsof()
		{
			$data = array(
				'view'     => $this->bo->view,
				'location' => $this->bo->location,
				'cat_id'   => $this->bo->cat_id,
				'forum_id' => $this->bo->forum_id
			);
			echo '<br>UI:';
			_debug_array($data);
		}

		/* Called only by get_list(), just prior to page footer. */
		function save_sessiondata()
		{
			$data = array(
				'view'     => $this->bo->view,
				'location' => $this->bo->location,
				'cat_id'   => $this->bo->cat_id,
				'forum_id' => $this->bo->forum_id
			);
			$this->bo->save_sessiondata($data);
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
					'INDEX' => 'index.body.tpl'
				)
			);
			$this->template->set_block('INDEX','CategoryForum','CatF');

			$var = Array(
				'CAT_IMG' => $GLOBALS['phpgw']->common->image('forum','category'),
				'BGROUND' => $GLOBALS['phpgw_info']['theme']['th_bg'],
				'FORUM'   => lang('Forum')
			);
			$this->template->set_var($var);

			$cats = $this->bo->get_all_cat_info();

			while(list($key,$cat) = each($cats))
			{
				$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
				$var = Array(
					'COLOR' => $tr_color,
					'CAT'   => $cat['name'],
					'DESC'  => $cat['descr'],
					'CAT_LINK' => $GLOBALS['phpgw']->link('/index.php',
							Array(
								'menuaction' => 'forum.uiforum.forum',
								'cat_id'     => $cat['id']
							)
						),
					'value_last_post' => $cat['last_post'],
					'value_total'     => $cat['total']
				);
				$this->template->set_var($var);
				$this->template->parse('CatF','CategoryForum',true);
			}
			$this->template->parse('Out','INDEX');
			$this->template->p('Out');
		}

		function forum()
		{
			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			unset($GLOBALS['phpgw_info']['flags']['noappheader']);
			unset($GLOBALS['phpgw_info']['flags']['noappfooter']);
			$GLOBALS['phpgw']->common->phpgw_header();

			$this->template->set_file(
				Array(
					'_list' => 'forums.body.tpl'
				)
			);
			$this->template->set_block('_list','row_empty');
			$this->template->set_block('_list','list');
			$this->template->set_block('_list','row');

			$cat = $this->bo->get_cat_info($this->bo->cat_id);

			$var = Array(
				'BGROUND'   => $GLOBALS['phpgw_info']['theme']['th_bg'],
				'FORUM_IMG' => $GLOBALS['phpgw']->common->image('forum','forum'),
				'CATEGORY'  => $cat['name'],
				'LANG_MAIN' => lang('Forum'),
				'MAIN_LINK' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiforum.index'))
			);
			$this->template->set_var($var);

			$forum_info = $this->bo->get_forums_for_cat($this->bo->cat_id);

			if(!$forum_info)
			{
				$GLOBALS['phpgw']->nextmatchs->template_alternate_row_color($this->template);
				$this->template->set_var('lang_no_forums',lang('There are no forums in this category'));
				$this->template->fp('rows','row_empty');
			}
			else
			{
				while (list($key,$forum) = each($forum_info))
				{
					$GLOBALS['phpgw']->nextmatchs->template_alternate_row_color($this->template);
					$this->template->set_var(
						Array(
							'NAME'         => $forum['name'],
							'DESC'         => $forum['descr'],
							'THREADS_LINK' => $GLOBALS['phpgw']->link('/index.php',
								Array(
									'menuaction'	=> 'forum.uiforum.threads',
									'cat_id'		=> (int) $this->bo->cat_id,
									'forum_id'		=> $forum['id']
								)
							),
							'value_last_post' => $forum['last_post'],
							'value_total'     => $forum['total']
						)
					);
					$this->template->fp('rows','row',True);
				}
			}
			$this->template->pfp('out','list');
		}

		function threads()
		{
			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			unset($GLOBALS['phpgw_info']['flags']['noappheader']);
			unset($GLOBALS['phpgw_info']['flags']['noappfooter']);
			$GLOBALS['phpgw']->common->phpgw_header();

			$category = $this->bo->get_cat_info($this->bo->cat_id);
			$forum = $this->bo->get_forum_info($this->bo->cat_id,$this->bo->forum_id);

			$pre_var = array(
				'BGROUND'        => $GLOBALS['phpgw_info']['theme']['th_bg'],
				'LANG_TOPIC'     => lang('Topic'),
				'LANG_AUTHOR'    => lang('Author'),
				'LANG_REPLIES'   => lang('Replies'),
				'LANG_LATREP'    => lang('Latest Reply'),
				'LANG_MAIN'      => lang('Forums'),
				'LANG_NEWTOPIC'  => lang('New Topic'),
				'LANG_CATEGORY'  => $category['name'],
				'LANG_FORUM'     => $forum['name'],
				'FORUM_LINK'     => $GLOBALS['phpgw']->link('/index.php',
					Array(
						'menuaction' => 'forum.uiforum.forum',
						'cat_id'     => $this->bo->cat_id
					)
				),
				'MAIN_LINK'      => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiforum.index')),
				'POST_LINK'      => $GLOBALS['phpgw']->link('/index.php',
					Array(
						'menuaction' => 'forum.uiforum.post',
						'type'       => 'new'
					)
				)
			);

			$is_collapsed = (strcmp($this->bo->view,'collapsed') == 0);
			$thread_listing = $this->bo->get_thread($this->bo->cat_id,$this->bo->forum_id,$is_collapsed);
			if($is_collapsed)
			{
				$this->template->set_file(
					Array(
						'COLLAPSE' => 'collapse.threads.tpl'
					)
				);
				$this->template->set_block('COLLAPSE','CollapseThreads','CollapseT');

				$this->template->set_var($pre_var);
				$this->template->set_var('THREAD_IMG',$GLOBALS['phpgw']->common->image('forum','thread'));

				while($thread_listing && list($key,$thread) = each($thread_listing))
				{
					$GLOBALS['tr_color'] = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($GLOBALS['tr_color']);
					
					$var = Array(
						'COLOR'     => $GLOBALS['tr_color'],
						'TOPIC'     => ($thread['subject']?$thread['subject']:'[No subject]'),
						'AUTHOR'    => ($thread['author']?$GLOBALS['phpgw']->common->grab_owner_name($thread['author']):lang('Unknown')),
						'REPLIES'   => $thread['replies'],
						'READ_LINK' => $GLOBALS['phpgw']->link('/index.php',
							Array(
								'menuaction'	=> 'forum.uiforum.read',
								'cat_id'		=> $this->bo->cat_id,
								'forum_id'		=> $this->bo->forum_id,
								'msg'			=> $thread['id']
							)
						),
						'LATESTREPLY' => $thread['last_reply']
					);
					$this->template->set_var($var);
					$this->template->parse('CollapseT','CollapseThreads',true);
				}
				$var = Array(
					'THREADS_LINK' => $GLOBALS['phpgw']->link('/index.php',
						Array(
							'menuaction' => 'forum.uiforum.threads',
							'view'       => 'threads',
							'cat_id'	=> $this->bo->cat_id,
							'forum_id'	=> $this->bo->forum_id
						)
					),
					'LANG_THREADS' => lang('View Threads')
				);
				$this->template->set_var($var);
				$this->template->parse('Out','COLLAPSE');
			} //end if
			//For viewing the normal view
			else
			{
				$this->template->set_file(
					Array(
						'NORMAL' => 'normal.threads.tpl'
					)
				);
				$this->template->set_block('NORMAL','NormalThreads','NormalT');
				$this->template->set_var($pre_var);

				while($thread_listing && list($key,$thread) = each($thread_listing))
				{
					$GLOBALS['tr_color'] = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($GLOBALS['tr_color']);

					$move = '';
					for($tmp = 1;$tmp <= $thread['depth']; $tmp++)
					{
						$move .= '&nbsp;&nbsp;';
					}
					$move .= '<img src="'.$GLOBALS['phpgw']->common->image('forum','n').'">';
					$move .= '&nbsp;&nbsp;';

					$var = Array(
						'COLOR'     => $GLOBALS['tr_color'],
						'TOPIC'     => ($thread['subject']?$thread['subject']:'[No subject]'),
						'AUTHOR'    => ($thread['author']?$GLOBALS['phpgw']->common->grab_owner_name($thread['author']):lang('Unknown')),
						'REPLIES'   => $thread['replies'],
						'READ_LINK' => $GLOBALS['phpgw']->link('/index.php',
							Array(
								'menuaction' => 'forum.uiforum.read',
								'msg'        => $thread['id'],
								'pos'        => $thread['pos']
							)
						),
						'LATESTREPLY' => $thread['last_reply'],
						'DEPTH' => $move
					);
					$this->template->set_var($var);
					$this->template->parse('NormalT','NormalThreads',true);
				} //end while

				$var = Array(
					'THREADS_LINK' => $GLOBALS['phpgw']->link('/index.php',
						Array(
							'menuaction' => 'forum.uiforum.threads',
							'view'       => 'collapsed'
						)
					),
					'LANG_THREADS' => lang('Collapse Threads')
				);
				$this->template->set_var($var);
				$this->template->parse('Out','NORMAL');
			}
			$this->template->p('Out');
		}

		function read()
		{
			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			unset($GLOBALS['phpgw_info']['flags']['noappheader']);
			unset($GLOBALS['phpgw_info']['flags']['noappfooter']);
			$GLOBALS['phpgw']->common->phpgw_header();

			$this->template->set_file(
				Array(
					'READ' => 'read.body.tpl'
				)
			);

			$this->template->set_block('READ','read_body','read_body');
			$this->template->set_block('READ','msg_template','msg_template');

			$msg = get_var('msg',Array('GET'));
			$pos = get_var('pos',Array('GET'));

			$this->template->set_var('row_off',$GLOBALS['phpgw_info']['theme']['row_off']);
			$this->template->set_var('row_on',$GLOBALS['phpgw_info']['theme']['row_on']);

			$category = $this->bo->get_cat_info($this->bo->cat_id);
			$forum = $this->bo->get_forum_info($this->bo->cat_id,$this->bo->forum_id);

			$var = array(
				'BGROUND'       => $GLOBALS['phpgw_info']['theme']['th_bg'],
				'LANG_TOPIC'    => lang('Topic'),
				'LANG_AUTHOR'   => lang('Author'),
				'LANG_REPLIES'  => lang('Replies'),
				'LANG_LATREP'   => lang('Latest Reply'),
				'LANG_MAIN'     => lang('Forum'),
				'LANG_NEWTOPIC' => lang('New Topic'),
				'LANG_REPLYTOPIC' => lang('Post A Message To This Thread').':',
				'LANG_CATEGORY' => $category['name'],
				'LANG_FORUM'    => $forum['name'],
				'LANG_SEARCH'   => lang('Search'),
				'LANG_POST'     => lang('Post'),
				'FORUM_LINK'    => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiforum.forum')),
				'MAIN_LINK'     => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiforum.index')),
				'POST_LINK'     => $GLOBALS['phpgw']->link('/index.php',
					Array(
						'menuaction' => 'forum.uiforum.post',
						'type'       => 'new'
					)
				),
				'THREADS_LINK' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiforum.threads')),
				'SEARCH_LINK'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiforum.search')),
				'READ_ACTION'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.boforum.reply')),
				'MSG'          => $msg,
				'POST'         => $pos,
				'CAT_ID'       => $this->bo->cat_id,
				'FORUM_ID'     => $this->bo->forum_id,
				'ACTION'       => 'reply',
				'LANG_AUTHOR'  => lang('Author'),
				'LANG_DATE'    => lang('Date'),
				'LANG_SUBJECT' => lang('Subject'),
				'LANG_REPLY'   => lang('Email replies to this thread, to the address above'),
				'LANG_SUBMIT'  => lang('Submit'),
				'LANG_MESSAGE' => lang('Message'),
				'LANG_THREADS' => lang('Return to forums')
/*
				'THREADS_LINK' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiforum.threads')),
*/
			);

			$this->template->set_var($var);
			$post_ul = '';
			$pre_ul = '';
			$messages = $this->bo->read_msg($this->bo->cat_id,$this->bo->forum_id,$msg);
			while($messages && list($key,$message) = each($messages))
			{
				if($message['id'] == $msg)
				{
					$var = Array(
						'THREAD'     => $message['thread'],
						'DEPTH'      => $message['depth'],
						'RE_SUBJECT' => (!strpos(' '.strtoupper($message['subject']),'RE: ')?'RE: ':'').$message['subject']
					);
					$this->template->set_var($var);
				}

				$var = Array(
					'AUTHOR'       => ($message['thread_owner']?$GLOBALS['phpgw']->common->grab_owner_name($message['thread_owner']):lang('Unknown')),
					'POSTDATE'     => $GLOBALS['phpgw']->common->show_date($GLOBALS['phpgw']->db->from_timestamp($message['postdate'])),
					'SUBJECT_LINK' => $GLOBALS['phpgw']->link('/index.php',
						Array(
							'menuaction' => 'forum.uiforum.read',
							'msg'        => $message['id'],
							'pos'        => $message['pos']
						)
					),
					'SUBJECT' => $message['subject'],
					'MESSAGE' => nl2br($message['message']),
					'NAME'    => $message['name'],
					'EMAIL'   => $message['email']
				);

				if($key > 0)
				{
					for($i=$depth,$pre_ul='',$post_ul='';$i<($message['depth'] - 1);$i++,$pre_ul.='<ul>',$post_ul.='</ul>')
					{
					}
					$this->template->set_var('UL_PRE',$pre_ul);
				}
				else
				{
					$depth = $message['depth'] - 1;
				}

				$this->template->set_var($var);

				$this->template->parse('MESSAGE_TEMPLATE','msg_template',True);
				$this->template->set_var('UL_PRE','');
			}
			if($post_ul)
			{
				$this->template->set_var('UL_POST',$post_ul);
			}
			
			$this->template->pfp('Out','read_body');
		}

		function post()
		{
			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			unset($GLOBALS['phpgw_info']['flags']['noappheader']);
			unset($GLOBALS['phpgw_info']['flags']['noappfooter']);
			$GLOBALS['phpgw']->common->phpgw_header();

			$this->template->set_file(
				Array(
					'POST' => 'post.body.tpl'
				)
			);

			$this->template->set_var('row_off',$GLOBALS['phpgw_info']['theme']['row_off']);
			$this->template->set_var('row_on',$GLOBALS['phpgw_info']['theme']['row_on']);

			$category = $this->bo->get_cat_info($this->bo->cat_id);
			$forum = $this->bo->get_forum_info($this->bo->cat_id,$this->bo->forum_id);

			$var = array(
				'BGROUND'       => $GLOBALS['phpgw_info']['theme']['th_bg'],
				'LANG_TOPIC'    => lang('Topic'),
				'LANG_AUTHOR'   => lang('Author'),
				'LANG_REPLIES'  => lang('Replies'),
				'LANG_LATREP'   => lang('Latest Reply'),
				'LANG_MAIN'     => lang('Forum'),
				'LANG_NEWTOPIC' => lang('New Topic'),
				'LANG_REPLYTOPIC' => lang('Post A Message To This Thread').':',
				'LANG_CATEGORY' => $category['name'],
				'LANG_FORUM'    => $forum['name'],
				'LANG_SEARCH'   => lang('Search'),
				'LANG_POST'     => lang('Post'),
				'FORUM_LINK'    => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiforum.forum')),
				'MAIN_LINK'     => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiforum.index')),
				'POST_LINK'     => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiforum.post')),
				'THREADS_LINK'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiforum.threads', 'cat_id' => $this->bo->cat_id, 'forum_id' => $this->bo->forum_id)),
				'SEARCH_LINK'   => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiforum.search')),
				'POST_ACTION'   => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.boforum.post')),
				'CAT_ID'        => $this->bo->cat_id,
				'FORUM_ID'      => $this->bo->forum_id,
				'TYPE'          => get_var('type',Array('GET')),
				'ACTION'        => 'post',
				'LANG_DATE'     => lang('Date'),
				'LANG_SUBJECT'  => lang('Subject'),
				'LANG_REPLY'    => lang('Email replies to this thread, to the address above'),
				'LANG_SUBMIT'   => lang('Submit'),
				'LANG_MESSAGE'  => lang('Message'),
				'LANG_THREADS'  => lang('Return to forums')
/*
				'THREADS_LINK' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'forum.uiforum.threads')),
*/
			);

			$this->template->set_var($var);
			$this->template->pfp('Out','POST');
		}
	}
?>
