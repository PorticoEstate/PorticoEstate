<?php
 /**************************************************************************\
 * phpGroupWare - News                                                      *
 * http://www.phpgroupware.org                                              *
 * --------------------------------------------                             *
 *  This program is free software; you can redistribute it and/or modify it *
 *  under the terms of the GNU General Public License as published by the   *
 *  Free Software Foundation; either version 2 of the License, or (at your  *
 *  option) any later version.                                              *
 \**************************************************************************/

/* $Id: class.uinews.inc.php 17992 2007-02-24 20:42:12Z sigurdne $ */

class uinews
{
	var $start = 0;
	var $query = '';
	var $sort  = '';
	var $order = ''; 
	var $cat_id;
	var $template;
	var $bo;
	var $news_data;
	var $news_id;
	var $sbox;
	var $public_functions = array
		(	
		 'add'       	=> True,
		 'edit'      	=> True,
		 'delete'    	=> True,
		 'delete_item'	=> True,
		 'newsletter'	=> True,
		 'read_news'      => True,
		 'show_news_home'=> True,
		 'write_news' 	=> True
		);

	/**
	 * @constructor
	 */
	function uinews()
	{
		$this->nextmatchs = createobject('phpgwapi.nextmatchs');
		$this->template = $GLOBALS['phpgw']->template;
		$this->bo   = CreateObject('news_admin.bonews',True);
		$this->sbox = createObject('phpgwapi.sbox');
		$this->start = $this->bo->start;
		$this->query = $this->bo->query;
		$this->order = $this->bo->order;
		$this->sort = $this->bo->sort;
		$this->cat_id = $this->bo->cat_id;
	}

	/**
	 * Display a list of news items for the user to read
	 */
	function read_news()
	{
		$limit = isset($GLOBALS['phpgw_info']['common']['maxmatchs'])
				? $GLOBALS['phpgw_info']['common']['maxmatchs'] : 10;

		$news_id = isset($_REQUEST['news_id']) ? $_REQUEST['news_id'] : 0;

		$news = $news_id ? array($news_id => $this->bo->get_news($news_id)) :  
			$this->bo->get_newslist($this->cat_id,$this->start,'','',$limit,True);

		$this->template->set_file('news', 'read.tpl');
		$this->template->set_block('news', 'cat_option', 'cat_options');
		$this->template->set_block('news', 'cat_form', 'cat_frm');
		$this->template->set_block('news', 'less_li', 'less');
		$this->template->set_block('news', 'maintain_li', 'maintain');
		$this->template->set_block('news', 'newsletter_li', 'newsletter');
		$this->template->set_block('news', 'more_li', 'more');
		$this->template->set_block('news', 'news_item', 'news_items');
		$this->template->set_block('news', 'summary_item', 'summary_items');
		$this->template->set_block('news','news_summary', 'news_sum');

		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar();

		$cats = ( is_array($this->bo->cats) && count($this->bo->cats) ) ? $this->bo->cats : array();

		$this->template->set_var(
				array
				(
				 'cat_id'	=> 0,
				 'cat_name'	=> lang('all news')
				)
				);
		$this->template->parse('cat_options', 'cat_option', true);
		foreach($cats as $cat)
		{
			if($this->bo->acl->is_permitted($cat['id'], PHPGW_ACL_READ))
			{
				$this->template->set_var(
						array
						(
						 'cat_id'	=> (int) $cat['id'],
						 'cat_name'	=> htmlspecialchars($cat['name']),
						 'selected'	=> ($this->bo->cat_id == $cat['id']
							 ? 'selected="selected"'
							 : '')
						)
						);
				$this->template->parse('cat_options', 'cat_option', True);
			}
		}
		$this->template->set_var('lang_go', lang('go') );
		$this->template->parse('cat_frm', 'cat_form');

		$var['lang_read'] = lang('read');
		$var['lang_write'] = lang('write');
		$var['cat_name'] = ($this->cat_id != 'all') ? $this->bo->catbo->id2name($this->cat_id) : lang('all news');
		$this->template->set_var($var);

		if ( ($this->cat_id != 'all') && $this->bo->acl->is_permitted($this->cat_id, PHPGW_ACL_ADD) 
				|| isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
		{
			$this->template->set_var(
					array
					(
					 'href_maintain'	=> $GLOBALS['phpgw']->link('/index.php',
						 array(
							 'menuaction'	=> 'news_admin.uinews.write_news',
							 'start'		=> 0,
							 'cat_id'	=> $this->cat_id
							 )
						 ),
					 'lang_maintain'	=> lang('maintain')
					)
					);

			$this->template->parse('maintain', 'maintain_li');
		}
		else
		{
			$this->template->set_var('maintain', '&nbsp;');
		}

		/*
		if ( count( $this->bo->acl->get_permissions(true) ) > 1 )
		{
			$this->template->set_var(
					array
					(
					 'href_newsletter'	=> $GLOBALS['phpgw']->link('/index.php',
						 array('menuaction' => 'news_admin.uinews.newsletter')
						 ),
					 'lang_newsletter'	=> lang('create newsletter')
					)
					);

			$this->template->parse('newsletter', 'newsletter_li');
		}
		else
		*/
		if (true)
		{
			$this->template->set_var('newsletter', '&nbsp;');

		}

		if ( !$this->bo->total )
		{
			$this->template->set_var('content', lang('No entries found') );
			$this->template->parse('news_itm', 'news_item', True);
			$this->template->pfp('out', 'news');
			exit;
		}
		elseif ( $news_id )
		{
			$newsitem = &$news[$news_id];
			//echo '<pre>' . print_r($newsitem, True) . '</pre>';
			$var = array(
					'subject'	=> htmlspecialchars($newsitem['subject']),
					'submission'	=> lang('submitted by %1 on %2', 
						$GLOBALS['phpgw']->accounts->id2name($newsitem['submittedby']), 
						$GLOBALS['phpgw']->common->show_date($newsitem['date']) ),
					'content'	=> $newsitem['is_html'] ? $newsitem['content'] : nl2br(htmlspecialchars($newsitem['content']) ),
					);

			$this->template->set_var($var);
			$this->template->parse('news_items', 'news_item', True);
		}
		else
		{
			foreach($news as $newsitem)
			{
				$var = array(
						'subject'	=> htmlspecialchars($newsitem['subject']),
						'submission'	=> lang('submitted by %1 on %2', 
							$GLOBALS['phpgw']->accounts->id2name($newsitem['submittedby']), 
							$GLOBALS['phpgw']->common->show_date($newsitem['date']) ),
						'summary'	=> htmlspecialchars($newsitem['teaser']),
						'lang_read'	=> lang('read full story'),
						'href_read'	=> $GLOBALS['phpgw']->link('/index.php', 
							array
							(
							 'menuaction'	=> 'news_admin.uinews.read_news',
							 'news_id'	=> $newsitem['id']
							)
							)
						);

				$this->template->set_var($var);
				$this->template->parse('summary_items', 'summary_item', True);
			}
			$this->template->parse('news_sum', 'news_summary', True);
		}

		if ( !$news_id && $this->start )
		{
			$link_data['menuaction'] = 'news_admin.uinews.read_news';
			$link_data['start'] = $this->start - $limit;
			$this->template->set_var('lesslink', $GLOBALS['phpgw']->link('/index.php', $link_data) );
			$this->template->parse('less', 'less_li');
		}
		else
		{
			$this->template->set_var('less', '&nbsp;');
		}

		if ( !$news_id && ($this->bo->total > $this->start + $limit) )
		{
			$link_data['menuaction'] = 'news_admin.uinews.read_news';
			$link_data['start'] = $this->start + $limit;
			$this->template->set_var('morelink', $GLOBALS['phpgw']->link('/index.php',$link_data) );
			$this->template->parse('more', 'less_li');
		}
		else
		{
			$this->template->set_var('more', '&nbsp;');
		}
		$this->template->pfp('out','news');
	}

	/**
	 * Display news on the "home screen" called by hook_home
	 * @internal this is currently broken
	 */
	function show_news_home()
	{
		$title = '<font color="#FFFFFF">'.lang('News Admin').'</font>';
		$portalbox = CreateObject('phpgwapi.listbox',array(
					'title'     => $title,
					'primary'   => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'secondary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'tertiary'  => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'width'     => '100%',
					'outerborderwidth' => '0',
					'header_background_image' => $GLOBALS['phpgw']->common->image('phpgwapi','bg_filler', 'png', False)
					));

		$app_id = $GLOBALS['phpgw']->applications->name2id('news_admin');
		$GLOBALS['portal_order'][] = $app_id;

		$btns = Array(
				'up'       => Array('url' => '/set_box.php', 'app' => $app_id),
				'down'     => Array('url' => '/set_box.php', 'app' => $app_id),
				'close'    => Array('url' => '/set_box.php', 'app' => $app_id),
				'question' => Array('url' => '/set_box.php', 'app' => $app_id),
				'edit'     => Array('url' => '/set_box.php', 'app' => $app_id)
				);

		foreach($btns as $key => $value)
		{
			$portalbox->set_controls($key,$value);
		}
		unset($btns);

		$newslist = $this->bo->get_newslist($this->cat_id, 0, '', '', $GLOBALS['phpgw_info']['common']['maxmatchs'], True);

		$image_path = $GLOBALS['phpgw']->common->get_image_path('news_admin');

		if(is_array($newslist))
		{
			foreach($newslist as $newsitem)
			{
				$portalbox->data[] = array(
						'text' => htmlspecialchars($newsitem['subject']) . ' - ' . lang('Submitted by') . ' ' . $GLOBALS['phpgw']->accounts->id2name($newsitem['submittedby']) . ' ' . lang('on') . ' ' . $GLOBALS['phpgw']->common->show_date($newsitem['date']),
						'link' => $GLOBALS['phpgw']->link('/index.php',
							array('menuaction'	=> 'news_admin.uinews.show_news',
								'news_id' 	=>  $newsitem['id'],
								)
							)
						);
			}
		}
		else
		{
			$portalbox->data[] = array('text' => lang('no news'));
		}

		$tmp = "\r\n"
			. '<!-- start News Admin -->' . "\r\n"
			. $portalbox->draw()
			. '<!-- end News Admin -->'. "\r\n";
		$this->template->set_var('phpgw_body',$tmp,True);
		$this->template->pfp('out', 'phpgw_body');
	}

	/**
	 * Show news on the "website", replaced by sitemgr news module/s
	 * @internal the following function is unmaintained
	 *
	 * @param string $section the "section" of the page where news will be displayed
	 * @returns string Error message
	 */
	function show_news_website($section='mid')
	{
		return "uinews::show_news_website({$section}) has been removed, please use sitemgr module";
	}

	/**
	 * Add a new news item
	 */
	function add()
	{
		if ( isset($_POST['cancel']) )
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'news_admin.uinews.write_news') );
			exit;
		}
		if ( isset($_POST['submitit']) )
		{
			$this->news_data = $_POST['news'];
			if (! $this->news_data['subject'])
			{
				$errors[] = lang('The subject is missing');
			}

			if (! $this->news_data['content'])
			{
				$errors[] = lang('The news content is missing');
			}

			if ( !isset($GLOBALS['data_cleaner']) || !is_object($GLOBALS['data_cleaner']) )
			{
				$GLOBALS['data_cleaner'] = createObject('phpgwapi.data_cleaner', '');
			}
			$this->news_data['content'] = $GLOBALS['data_cleaner']->clean($GLOBALS['RAW_REQUEST']['news']['content'], false);

			if (!is_array($errors))
			{
				$this->news_data['date'] = time();
				$this->bo->set_dates($_POST['from'], $_POST['until'], $this->news_data);					
				$this->news_id = $this->bo->add($this->news_data);
				$this->message = lang('Message has been added');
				//after having added, we must switch to edit mode instead of stay in add
				$this->news_data = $this->bo->get_news($this->news_id, True);
				$this->_modify('edit');
				return;
			}
			else
			{
				$this->message = $errors;
			}
		}
		else
		{
			$this->news_data['category'] = $this->cat_id;
		}
		$this->_modify('add');
	}

	/**
	 * Confirm deletition of a news item
	 */
	function delete()
	{
		$news_id = $_POST['news_id'] ? $_POST['news_id'] : $_GET['news_id'];

		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar();
		$this->template->set_file(array(
					'form' => 'admin_delete.tpl'
					));
		$this->template->set_var('lang_message',lang('Are you sure you want to delete this entry ?'));
		$this->template->set_var('lang_yes',lang('Yes'));
		$this->template->set_var('lang_no',lang('No'));

		$this->template->set_var('link_yes',$GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uinews.delete_item&news_id=' . $news_id));
		$this->template->set_var('link_no',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'news_admin.uinews.write_news')));

		$this->template->pfp('_out','form');
	}

	/**
	 * Delete a news item
	 */
	function delete_item()
	{
		$item = intval(get_var('news_id'));
		if($item)
		{
			$this->bo->delete($item);
			$msg = lang('Item has been deleted');
		}
		else
		{
			$msg = lang('Item not found');
		}
		$this->write_news($msg);
	}

	/**
	 * Edit an existing news item
	 */
	function edit()
	{
		$jscal = createObject('phpgwapi.jscalendar', False);
		$this->news_data	= $_POST['news'];
		$this->news_id		= (isset($_GET['news_id']) ? $_GET['news_id'] 
				: $_POST['news']['id']);

		if ( isset($_POST['cancel']) && $_POST['cancel'] )
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'news_admin.uinews.write_news') );
			exit;
		}
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
		{
			if(! $this->news_data['subject'])
			{
				$errors[] = lang('The subject is missing');
			}
			if(! $this->news_data['content'])
			{
				$errors[] = lang('The news content is missing');
			}

			$begin = $jscal->input2date($this->news_data['begin']);
			$this->news_data['begin'] = $begin['raw'];
			unset($begin);

			$end = $jscal->input2date($this->news_data['end']);
			$this->news_data['end']   = $end['raw'];
			unset($end);

			if ( !isset($GLOBALS['data_cleaner']) || !is_object($GLOBALS['data_cleaner']) )
			{
				$GLOBALS['data_cleaner'] = createObject('phpgwapi.data_cleaner', '');
			}
			$this->news_data['content'] = $GLOBALS['data_cleaner']->clean($GLOBALS['RAW_REQUEST']['news']['content'], false);

			if(!is_array($errors))
			{
				$this->bo->set_dates($_POST['from'], $_POST['until'], $this->news_data);
				$this->bo->edit($this->news_data);
				$this->message = lang('News item has been updated');
				$this->news_data = $this->bo->get_news($this->news_id, True);
			}
			else
			{
				$this->message = $errors;
			}
		}
		else
		{
			$this->news_data = $this->bo->get_news($this->news_id, True);
		}

		$this->_modify();
	}

	/**
	 * Create an email newsletter from news items
	 */
	function newsletter()
	{
		$msg = '';
		$content = '';
		$config = createObject('phpgwapi.config');
		$config->read_repository();
		//echo '<pre>' . print_r($config->config_data, true) . '</pre>';
		if ( strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' )
		{
			if ( isset($_POST['send']) && $_POST['send'] )
			{
				if ( !isset($GLOBALS['data_cleaner']) || !is_object($GLOBALS['data_cleaner']) )
				{
					$GLOBALS['data_cleaner'] = createObject('phpgwapi.data_cleaner');
				}
				$content = stripslashes($GLOBALS['data_cleaner']->clean($GLOBALS['RAW_REQUEST']['nl_content'], false));
				$html2text = createObject('phpgwapi.html2text', $content);
				$content_txt = $html2text->get_text();
				unset($html2text);

				$smtp = createObject('phpgwapi.mailer_smtp');
				$smpt->CharSet = lang('charset');
				$smtp->From = $smtp->Sender = $config->config_data['newsletter_from_email'];
				$smtp->FromName = $config->config_data['newsletter_from_name'];
				$smtp->AddAddress($config->config_data['newsletter_to']);
				$smtp->Subject = get_var('subject', array('POST'));

				if ( isset($_POST['cc_recipients']) && is_array($_POST['cc_recipients']) )
				{
					foreach ( $_POST['cc_recipients'] as $cc )
					{
						$smtp->AddCC($cc);
					}
				}
				else if ( isset($_POST['cc_recipients']) && $_POST['cc_recipients'])
				{

					$smtp->AddCC($_POST['cc_recipients']);
				}

				if ( $config->config_data['force_plain'] )
				{
					unset($content);
					$smtp->IsHTML(false);
					$smtp->Body = $content_txt;
				}
				else
				{
					$smtp->IsHTML(true);
					$smtp->Body = $content;
					$smtp->AltBody = $content_txt;
				}
				unset($content_txt);

				//FIXME: Handle this better
				if ( $smtp->Send() )
				{
					$msg = lang('newsletter sent');
					$content = '';
				}
				else
				{
					$msg = lang('sending newsletter failed: %1', $smtp->ErrorInfo);
				}
			}
		}

		if ( !$content )
		{
			$content = $config->config_data['newsletter_header_html'] 
				. "<div id=\"news\"></div>\n" 
				. $config->config_data['newsletter_footer_html'];
		}

		if ( !isset($GLOBALS['phpgw']->richtext) || !is_object($GLOBALS['phpgw']->richtext) )
		{
			$GLOBALS['phpgw']->richtext =& createObject('phpgwapi.richtext');
		}
		$GLOBALS['phpgw']->richtext->replace_element('nl_content');
		$GLOBALS['phpgw']->richtext->generate_script();

		$GLOBALS['phpgw']->js->validate_file('base', 'tabs', 'news_admin');
		$GLOBALS['phpgw']->js->validate_file('base', 'news_builder', 'news_admin');

		$GLOBALS['phpgw_info']['flags']['java_script'] = '
			<script type="text/javascript">
			<!--
			//Global scope
			var oTabs;
		var strLinkURL = \'' . $config->config_data['more_link_url'] . '\';
		oLang = {
read_more: "' . lang('read more') . ' >>"
		}
		//-->
		</script>
			<noscript>
			<p>' . lang('newsletter builder requires javascript to be enabled') . '!</p>
			</noscript>
			';
		$GLOBALS['phpgw_info']['flags']['css'] .= '</style><link rel="StyleSheet" href="news_admin/css/base.css"><style type="text/css">';

		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar();

		$this->template->set_file(array('newsletter' => 'newsletter_creator.tpl'));
		$this->template->set_block('newsletter', 'article', 'articles');
		$this->template->set_var(
				array
				(
				 'form_action'		=> $GLOBALS['phpgw']->link('/index.php', 
					 array
					 (
					  'menuaction'	=> 'news_admin.uinews.newsletter'
					 )),
				 'href_cancel'		=> $GLOBALS['phpgw']->link('/news_admin/index.php'),
				 'href_help'		=> $GLOBALS['phpgw']->link('/index.php', 
					 array
					 (
					  'menuaction'	=> 'news_admin.uinews.help'
					 )),
				 'img_cancel'		=> $GLOBALS['phpgw']->common->find_image('news_admin', 'cancel'),
				 'img_help'		=> $GLOBALS['phpgw']->common->find_image('news_admin', 'help'),
				 'img_send'		=> $GLOBALS['phpgw']->common->find_image('news_admin', 'send'),
				 'lang_add'		=> lang('add'),
				 'lang_all_changes_will_be_lost'	=> lang('all changes will be lost'),
				 'lang_cc_recipients'	=> lang('cc recipients'),
				 'lang_articles' 	=> lang('articles'),
				 'lang_author'		=> lang('author'),
				 'lang_cancel'		=> lang('cancel'),
				 'lang_help'		=> lang('help'),
				 'lang_message'		=> lang('message'),
				 'lang_preview'		=> lang('preview'),
				 'lang_recipients'	=> lang('recipients'),
				 'lang_remove'		=> lang('remove'),
				 'lang_send'		=> lang('send'),
				 'lang_subject'		=> lang('subject'),
				 'lang_title'		=> lang('title'),
				 'messages'		=> $msgs,
				 'nl_content'		=> htmlspecialchars($content),
				 )
					 );

		$news = $this->bo->get_newslist(0, 0, '', '', -1);

		$i = 0;
		foreach($news as $item)
		{
			//echo '<pre>' . print_r($item, True) . '</pre>';
			$item['css_row'] = 'row_' . ( $i%2 ? 'on' : 'off');
			$item['author'] = $GLOBALS['phpgw']->accounts->id2name($item['submittedby']);
			$this->template->set_var($item);
			$this->template->parse('articles', 'article', True);
			++$i;
		}
		$this->template->pfp('out', 'newsletter');
	}

	/**
	 * Write a news item
	 *
	 * @param string $message action feedback message to user
	 */
	function write_news($message = '')
	{		
		$this->template->set_file(array('main' => 'write.tpl'));
		$this->template->set_block('main','list');
		$this->template->set_block('main','row');
		$this->template->set_block('main','row_empty');

		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar();
		$this->template->set_block('main','category');
		$var['lang_read'] = lang('Read');
		$var['lang_write'] = lang('Write');
		$var['readable'] = $this->_selectlist('read');
		$var['cat_name'] = $this->cat_id ? $this->bo->catbo->id2name($this->cat_id) : lang('Global news');
		$var['cat_url'] = $GLOBALS['phpgw']->link('/index.php', 
				array
				(
				 'menuaction'	=> 'news_admin.uinews.write_news',
				 'start'		=> 0
				) );

		$this->template->set_var($var);
		$this->template->parse('_category','category');

		if ($message)
		{
			$this->template->set_var('message',$message);
		}

		$this->template->set_var('header_date',$this->nextmatchs->show_sort_order($this->sort,'news_date',$this->order,'/index.php',lang('Date'),'&menuaction=news_admin.uinews.write_news'));
		$this->template->set_var('header_subject',$this->nextmatchs->show_sort_order($this->sort,'news_subject',$this->order,'/index.php',lang('Subject'),'&menuaction=news_admin.uinews.write_news'));
		$this->template->set_var('header_status',lang('Visible'));
		$this->template->set_var('header_edit','edit');
		$this->template->set_var('header_delete','delete');
		$this->template->set_var('header_view','view');

		$items      = $this->bo->get_newslist($this->cat_id,$this->start,$this->order,$this->sort);

		$left  = $this->nextmatchs->left('/index.php',$this->start,$this->bo->total,'menuaction=news_admin.uinews.write_news');
		$right = $this->nextmatchs->right('/index.php',$this->start,$this->bo->total,'menuaction=news_admin.uinews.write_news');

		$this->template->set_var(array(
					'left' => $left,
					'right' => $right,
					'lang_showing' => $this->nextmatchs->show_hits($this->bo->total,$this->start),
					));

		foreach($items as $item)
		{
			$this->nextmatchs->template_alternate_row_color(&$this->template);
			$this->template->set_var('row_date',$GLOBALS['phpgw']->common->show_date($item['date']));
			if (strlen($item['news_subject']) > 40)
			{
				$subject = substr($item['subject'],40,strlen($item['subject'])) . ' ...';
			}
			else
			{
				$subject = $item['subject'];
			}
			$this->template->set_var('row_subject', htmlspecialchars($subject) );
			$this->template->set_var('row_status', $this->bo->get_visibility($item));

			$this->template->set_var('row_view','<a href="' . $GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uinews.read_news&news_id=' . $item['id']) . '">' . lang('view') . '</a>');
			$this->template->set_var('row_edit','<a href="' . $GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uinews.edit&news_id=' . $item['id']) . '">' . lang('edit') . '</a>');
			$this->template->set_var('row_delete','<a href="' . $GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uinews.delete&news_id=' . $item['id']) . '">' . lang('Delete') . '</a>');

			$this->template->parse('rows','row',True);
		}

		if (! $this->bo->total)
		{
			$this->nextmatchs->template_alternate_row_color(&$this->template);
			$this->template->set_var('row_message',lang('No entries found'));
			$this->template->parse('rows','row_empty',True);
		}

		$this->template->set_var('link_add',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'news_admin.uinews.add')));
		$this->template->set_var('lang_add',lang('Add new news'));

		$this->template->pfp('out','list');
	}

	/**
	 * Get the values needed for build the date range fields in edit mode
	 *
	 * @param array $news news item record
	 * @returns array values as jscal friendly array
	 */
	function _get_option_vals(&$news)
	{
		$now = time();
		if (!isset($news['begin']) || $news['begin'] == 0 ) //always is default
		{
			//these are only displayed values not necessarily the ones that will get stored
			$news['begin'] = 0;
			$news['end'] = $this->bo->unixtimestampmax;
			$from = 1;
			$until = 1;
		}
		elseif ($news['end'] < $now) //if enddate is in the past set option to never
		{
			$from = 0;
			$until = 1;
		}
		else
		{
			if ($news['begin'] < $now)
			{
				$news['begin'] = $now;
				if ($news['end'] == $this->bo->unixtimestampmax)
				{
					$from = 1;
					$until = 1;
				}
				else
				{
					$from = 0.5;
					$until = 0.5;
				}
			}
			else
			{
				if ($news['end'] == $this->unixtimestampmax)
				{
					$from = 0.5;
					$until = 1;
				}
				else
				{
					$from = 0.5;
					$until = 0.5;
				}
			}
		}
		$options['lang_always']	= lang('always');
		$options['lang_from']	= lang('from');
		$options['lang_never']	= lang('never');
		$options['lang_until']	= lang('until');

		$options['from_always_selected']= '';
		$options['from_never_selected']	= '';
		$options['from_from_selected']	= '';

		$options['to_until_selected'] = '';
		$options['to_always_selected'] = '';

		switch($from * 10)
		{
			case 0:
				$options['from_never_selected'] = 'selected="selected" ';
				break;
			case 5:
				$options['from_from_selected']	= 'selected="selected" ';
				break;
			default:
				$options['from_always_selected']= 'selected="selected" ';

		}

		switch($until * 10)
		{
			case 5:
				$options['to_until_selected'] = 'selected="selected" ';
				break;
			default:
				$options['to_always_selected'] = 'selected="selected" ';

		}
		return $options;
	}

	/**
	 * Render story editor
	 *
	 * @access private
	 * @param string $type edit or create
	 */
	function _modify($type = 'edit')
	{
		if ( !is_object($GLOBALS['phpgw']->js) )
		{
			$GLOBALS['phpgw']->js = CreateObject('phpgwapi.javascript');
		}
		$GLOBALS['phpgw']->js->validate_file('base', 'toggle', 'news_admin');
		$GLOBALS['phpgw']->js->add_event('load', 'toggle();');

		if ( !isset($GLOBALS['phpgw']->richtext) || !is_object($GLOBALS['phpgw']->richtext) )
		{
			$GLOBALS['phpgw']->richtext =& createObject('phpgwapi.richtext');
		}
		$GLOBALS['phpgw']->richtext->replace_element('news_content');
		$GLOBALS['phpgw']->richtext->generate_script();

		$jscal = createObject('phpgwapi.jscalendar');

		//$GLOBALS['phpgw_info']['flags']['css'] .= '</style><link rel="StyleSheet" href="news_admin/css/base.css"/><style type="text/css">';

		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar();

		$this->template->set_file( array('form'	=> 'admin_form.tpl') );

		if ( isset($this->message) && is_array($this->message) )
		{
			$this->template->set_var('errors',$GLOBALS['phpgw']->common->error_list($this->message));
		}
		elseif ( isset($this->message) && $this->message )
		{
			$this->template->set_var('errors',$this->message);
		}

		$this->template->set_var('lang_header',lang($type . ' news item'));
		$this->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php',
					array
					(
					 'menuaction'	=> 'news_admin.uinews.'.$type,
					 'news_id'	=> $this->news_id
					)
					)
				);

		$this->template->set_var(array
		(
			 'img_cancel'	=> $GLOBALS['phpgw']->common->find_image('news_admin', 'cancel'),
			 'img_save'		=> $GLOBALS['phpgw']->common->find_image('news_admin', 'save'),
			 'lang_save'	=> lang('save'),
			 'value_id'		=> $this->news_id,
			 'lang_cancel'	=> lang('cancel'),
			 'label_subject'=> lang('subject'),
			 'value_subject'=> isset($this->news_data['subject']) ? htmlspecialchars($this->news_data['subject']) : '',
			 'label_teaser'	=> lang('summary'),
			 'value_teaser'	=> isset($this->news_data['teaser']) ? htmlspecialchars($this->news_data['teaser']) : '',
			 'value_content'=> isset($this->news_data['content']) && $this->news_data['content'] ? htmlspecialchars($this->news_data['content']) : '&nbsp;',
			 'label_category'=> lang('category'),
			 'value_category'=> $this->_selectlist('write', intval($this->news_data['category'])),
			 'label_visible'=> lang('visible'),
			 'value_begin'	=> $jscal->input('news[begin]', isset($this->news_data['begin'])?$this->news_data['begin']:''),
			 'select_from'	=> isset($options['from'])?$options['from']:'',
			 'select_until'	=> isset($options['until'])?$options['until']:'',
			 'value_end'	=> $jscal->input('news[end]', isset($this->news_data['end'])?$this->news_data['end']:''),
		));
		//echo '<pre>' . print_r($this->news_data, true) . '</pre>';
		//echo '<pre>' . print_r($this->_get_option_vals($this->news_data), true) . '</pre>';

		$this->template->set_var( $this->_get_option_vals($this->news_data) );
		$this->template->pfp('out','form');
	}

	//with $default, we are called from the news form
	function _selectlist($type, $default = 0)
	{
		$right = ($type == 'read') ? PHPGW_ACL_READ : PHPGW_ACL_ADD;
		$selectlist = ( !$default && $type == 'read' ? ('<option>' . lang($type . ' news') . '</option>') : '');
		$cats = is_array($this->bo->cats) ? $this->bo->cats : array();
		foreach($cats as $cat)
		{
			if($this->bo->acl->is_permitted($cat['id'],$right))
			{
				$selectlist .= "<option value=\"{$cat['id']}\"";
				$selectlist .= ($default == $cat['id']) ? ' selected="selected"' : ''; 
				$selectlist .= '>' . $cat['name'] . '</option>' . "\n";
			}
		}
		if (!$default && $type == 'read' )
		{
			$selectlist .= '<option style="font-weight:bold" value="all">' . lang('All news') . '</option>'  . "\n";
		}
		return $selectlist;
	}
}
?>
