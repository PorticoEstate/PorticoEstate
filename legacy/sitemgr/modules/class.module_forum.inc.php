<?php

class module_forum extends Module 
{
	var $template;
	var $startlevel;

	function module_forum()
	{
		$this->arguments = array(
			'startlevel' => array(
				'type' => 'select',
				'label' => lang('With which view should the module be displayed in the beginning?'),
				'options' => array(
					1 => lang('Overview of all available categories'),
					2 => lang('Overview of one specific category'),
					3 => lang('Summary of one forum'),
				),
			),
			'startcat_id' => array(
				'type' => 'select',
				'label' => lang('Select a category'),
				'options' => Array(),
			),
			'startforum_id' => array(
				'type' => 'select',
				'label' => lang('Select a forum'),
				'options' => Array(),
			),
		);
		$this->get = array('level','cat_id','forum_id','msg','pos','view');
		$this->session = array('level','cat_id','forum_id','view');
		$this->title = lang('Discussions');
		$this->description = lang('This module displays the phpgw forums on the web site');
		$this->bo = CreateObject('forum.boforum',1);
	}

	function get_user_interface()
	{
		$defaults = $this->block->arguments;
		if ($defaults['startlevel'] == 2 || $defaults['startlevel'] == 3)
		{
			$options=array();
			$cats = $this->bo->get_all_cat_info();
			while(list($key,$cat) = @each($cats))
			{
				$options[$cat['id']] = $cat['name'];
			}
			$this->arguments['startcat_id']['options'] = $options;
		}
		else
		{
			unset($this->arguments['startcat_id']);
		}
		if (($defaults['startlevel'] == 3) && $defaults['startcat_id'])
		{
			$options=array();
			$forums = $this->bo->get_forums_for_cat($defaults['startcat_id']);
			while (list($key,$forum) = @each($forums))
			{
				$options[$forum['id']] = $forum['name'];
			}
			$this->arguments['startforum_id']['options'] = $options;
		}
		else
		{
			unset($this->arguments['startforum_id']);
		}
		return parent::get_user_interface();
	}

	function get_content(&$arguments,$properties)
	{
		$this->startlevel = $arguments['startlevel'];
		if (!$arguments['level'] || $arguments['level'] < $this->startlevel)
		{
			$arguments['level'] = $this->startlevel;
		}
		$this->template = CreateObject('phpgwapi.Template');
		$this->template->set_root($this->find_template_dir());

		if ($arguments['level'] == 1)
		{
			return $this->index();
		}

		//$arguments['level'] > 1
		$cat_id = ($this->startlevel == 1) ? $arguments['cat_id'] : $arguments['startcat_id'];
		$cat = $cat_id ? $this->bo->get_cat_info($cat_id) : False;
		if (!$cat)
		{
			$cats = array_values($this->bo->get_all_cat_info());
			if (!$cats)
			{
				return lang('There are no categories');
			}
			$cat = $cats[0];
		}
		if ($arguments['level'] == 2)
		{
			return $this->forums($cat);
		}

		//$arguments['level'] > 2
		$forum_id = ($this->startlevel > 3) ? $arguments['forum_id'] : $arguments['startforum_id'];
		$forum = $forum_id ? $this->bo->get_forum_info($cat['id'],$forum_id) : False;
		if (!$forum)
		{
			$forums = array_values($this->bo->get_forums_for_cat($cat['id']));
			if (!$forums)
			{
				return lang('There are no forums in this category');
			}
			$forum = $forums[0];
		}
		if ($arguments['level'] == 3)
		{
			return $this->threads($cat,$forum,(strcmp($arguments['view'],'collapsed') == 0));
		}

		//$arguments['level'] == 4, if msg is not defined we fall back to level 3
		return $arguments['msg'] ? 
			$this->read($cat,$forum,$arguments['msg']) : 
			$this->threads($cat,$forum,(strcmp($arguments['view'],'collapsed') == 0));
	}

	function index()
	{
		$this->template->set_file(
			Array(
				'INDEX'	=> 'index.body.tpl'
			)
		);
		$this->template->set_block('INDEX','CategoryForum','CatF');

		$var = Array(
			'CAT_IMG'	=> $GLOBALS['phpgw']->common->image('forum','category'),
		);
		$this->template->set_var($var);

		$cats = $this->bo->get_all_cat_info();

		$rowon = true;
		while(list($key,$cat) = @each($cats))
		{
			$rowon = !$rowon;

			$var = Array(
				'ROWONOFF'	=> $rowon ? 'rowon' : 'rowoff',
				'CAT'	=> $cat['name'],
				'DESC'	=> $cat['descr'],
				'CAT_LINK'	=> $this->link(Array(
					'level'	=> 2,
					'cat_id'	=> $cat['id']
				)),
				'value_last_post' => $cat['last_post'],
				'value_total'=> $cat['total']
			);
			$this->template->set_var($var);
			$this->template->parse('CatF','CategoryForum',true);
		}
		$this->template->parse('Out','INDEX');
		return $this->template->get('Out');
	}

	function forums($cat)
	{
		$this->template->set_file(
			Array(
			'_list'	=> 'forums.body.tpl'
			)
		);
		$this->template->set_block('_list','row_empty');
		$this->template->set_block('_list','list');
		$this->template->set_block('_list','row');

		$var = Array(
			'FORUM_IMG' => $GLOBALS['phpgw']->common->image('forum','forum'),
			'CATEGORY'=> $cat['name'],
			'LANG_CATEGORY'=> lang('Category'),
			'BACKLINK'=> (($this->startlevel == 1) ? 
				('<a href="' . $this->link(array('level' => 1)) . '">' . lang('All categories') . '</a>') : 
				''
			)
		);
		$this->template->set_var($var);

		$forum_info = $this->bo->get_forums_for_cat($cat['id']);

		if(!$forum_info)
		{
			$this->template->set_var('lang_no_forums',lang('There are no forums in this category'));
			$this->template->fp('rows','row_empty');
		}
		else
		{
			$rowon = true;
			while (list($key,$forum) = each($forum_info))
			{
				$rowon = !$rowon;
	
				$this->template->set_var(
					Array(
						'ROWONOFF'=> $rowon ? 'rowon' : 'rowoff',
						'NAME'=> $forum['name'],
						'DESC'=> $forum['descr'],
						'THREADS_LINK'=> $this->link(Array(
							'level'	=> 3,
							'forum_id'	=> $forum['id']
						)),
						'value_last_post' => $forum['last_post'],
						'value_total'=> $forum['total']
					)
				);
				$this->template->fp('rows','row',True);
			}
		}
		$this->template->parse('Out','list');
		return $this->template->get('Out');
	}

	function threads($cat,$forum,$is_collapsed)
	{
		$pre_var	= array(
			'LANG_TOPIC'=> lang('Topic'),
			'LANG_AUTHOR'=> lang('Author'),
			'LANG_REPLIES'=> lang('Replies'),
			'LANG_LATREP'=> lang('Latest Reply'),
			'LANG_FORUM'=> lang('Forum'),
			'FORUM'=> $forum['name'],
			'BACKLINK'=> (($this->startlevel < 3) ? 
				('<a href="' . 
					$this->link(array('level'	=> 2)) . '">' . 
					lang('All forums in category %1','<b>'.$cat['name'].'</b>') . '</a>'
				) : 
				''
			)
		);

		$thread_listing = $this->bo->get_thread($cat['id'],$forum['id'],$is_collapsed);
		if($is_collapsed)
		{
			$this->template->set_file(
				Array(
					'COLLAPSE'	=> 'collapse.threads.tpl'
				)
			);
			$this->template->set_block('COLLAPSE','CollapseThreads','CollapseT');

			$this->template->set_var($pre_var);
			$this->template->set_var('THREAD_IMG',$GLOBALS['phpgw']->common->image('forum','thread'));

			$rowon = true;
			while($thread_listing && list($key,$thread) = each($thread_listing))
			{
				$rowon = !$rowon;

				$var = Array(
					'ROWONOFF'	=> $rowon ? 'rowon' : 'rowoff',
					'TOPIC'	=> ($thread['subject']?$thread['subject']:'[No subject]'),
					'AUTHOR'	=> ($thread['author']?$GLOBALS['phpgw']->common->grab_owner_name($thread['author']):lang('Unknown')),
					'REPLIES'	=> $thread['replies'],
					'READ_LINK'	=> $this->link(Array(
						'level'	=> 4,
						'msg' => $thread['id'],
					)),
					'LATESTREPLY'	=> $thread['last_reply']
				);
				$this->template->set_var($var);
				$this->template->parse('CollapseT','CollapseThreads',true);
			}
			$var = Array(
				'THREADS_LINK'	=> $this->link(Array(
					'view' => 'threads',
				)),
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
					'NORMAL'	=> 'normal.threads.tpl'
				)
			);
			$this->template->set_block('NORMAL','NormalThreads','NormalT');
			$this->template->set_var($pre_var);

			$rowon = true;
			$tr_color = $this->row_on_color; 
			while($thread_listing && list($key,$thread) = each($thread_listing))
			{
				$rowon = !$rowon;

				$move = '';
				for($tmp = 1;$tmp <= $thread['depth']; $tmp++)
				{
					$move .= '&nbsp;&nbsp;';
				}
				$move .= '<img src="'.$GLOBALS['phpgw']->common->image('forum','n').'">';
				$move .= '&nbsp;&nbsp;';

				$var = Array(
					'ROWONOFF'	=> $rowon ? 'rowon' : 'rowoff',
					'TOPIC'	=> ($thread['subject']?$thread['subject']:'[No subject]'),
					'AUTHOR'	=> ($thread['author']?$GLOBALS['phpgw']->common->grab_owner_name($thread['author']):lang('Unknown')),
					'REPLIES'	=> $thread['replies'],
					'READ_LINK'	=> $this->link(Array(
						'level'	=> 4,
						'msg' => $thread['id'],
						'pos' => $thread['pos']
					)),
					'LATESTREPLY'	=> $thread['last_reply'],
					'DEPTH'	=> $move
				);
				$this->template->set_var($var);
				$this->template->parse('NormalT','NormalThreads',true);
			} //end while

			$var = Array(
				'THREADS_LINK'	=> $this->link(Array(
					'view' => 'collapsed'
				)),
				'LANG_THREADS' => lang('Collapse Threads')
			);
			$this->template->set_var($var);
			$this->template->parse('Out','NORMAL');
		}
		return $this->template->get('Out');
	}

	function read($cat,$forum,$msg)
	{
		$this->template->set_file(
			Array(
				'READ'	=> 'read.body.tpl'
			)
		);

		$this->template->set_block('READ','read_body','read_body');
		$this->template->set_block('READ','msg_template','msg_template');
		$this->template->set_block('READ','post_template','post_template');
		$var = array(
			'LANG_TOPIC'=> lang('Topic'),
			'LANG_AUTHOR'=> lang('Author'),
			'LANG_REPLIES'=> lang('Replies'),
			'LANG_LATREP'=> lang('Latest Reply'),
			'LANG_FORUM'=> lang('Forum'),
			'FORUM'=> $forum['name'],
			'LANG_SEARCH'=> lang('Search'),
			'BACKLINK' => ('<a href="' . $this->link(array('level' => 3)) . '">' . lang('Return to message list') . '</a>'),
			'LANG_DATE'=> lang('Date'),
			'LANG_SUBJECT' => lang('Subject'),
			'LANG_THREADS'	=> lang('Return to forums')
		);
		$this->template->set_var($var);

	
//it does not make sense to implement posting in this module, since the forum app is
//too badly designed to to this in a clean way.
// 		$var = array(
// 			'POST_ACTION' => $this->link(array('level' => 5)),
// 			'LANG_REPLYTOPIC' => lang('Post A Message To This Thread').':',
// 			'LANG_MESSAGE' => lang('Message'),
// 			'LANG_SUBMIT'=> lang('Submit')
// 		);
// 		$this->template->set_var($var);
// 		$this->template->parse('POST_TEMPLATE','post_template',True);

		$post_ul = '';
		$pre_ul = '';
		$messages = $this->bo->read_msg($cat['id'],$forum['id'],$msg);
		while($messages && list($key,$message) = each($messages))
		{
			if($message['id'] == $msg)
			{
				$var = Array(
					'THREAD'=> $message['thread'],
					'DEPTH'=> $message['depth'],
					'RE_SUBJECT'	=> (!strpos(' '.strtoupper($message['subject']),'RE: ')?'RE: ':'').$message['subject']
				);
				$this->template->set_var($var);
			}

			$var = Array(
				'AUTHOR'=> ($message['thread_owner']?$GLOBALS['phpgw']->common->grab_owner_name($message['thread_owner']):lang('Unknown')),
				'POSTDATE'=> $GLOBALS['phpgw']->common->show_date($GLOBALS['phpgw']->db->from_timestamp($message['postdate'])),
				'SUBJECT_LINK' => $this->link(Array(
					'msg' => $message['id'],
					'pos' => $message['pos']
				)),
				'SUBJECT'=> $message['subject'],
				'MESSAGE'		=> nl2br($message['message']),
				'NAME'=> $message['name'],
				'EMAIL'=> $message['email']
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
			
		$this->template->parse('Out','read_body');
		return $this->template->get('Out');
	}

}
