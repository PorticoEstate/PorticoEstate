<?php
/**************************************************************************\
* phpGroupWare - KnowledgeBase                                             *
* http://www.phpgroupware.org                                              *
*                                                                          *
* Copyright (c) 2003-2006 Free Sofware Foundation Inc                      *
* Written by Dave Hall skwashd at phpgropware.org                          *
* Written by Alejandro Pedraza <alpeb@users.sourceforge.net>               *
* Headers unlawfuly removed by Alejandro Pedraza                           *
* ------------------------------------------------------------------------ *
*  Started off as a port of phpBrain - http://vrotvrot.com/phpBrain/	   *
*  but quickly became a full rewrite					                   *
* ------------------------------------------------------------------------ *
*  This program is free software; you can redistribute it and/or modify it *
*  under the terms of the GNU General Public License as published by the   *
*  Free Software Foundation; either version 2 of the License, or (at your  *
*  option) any later version.                                              *
\**************************************************************************/

	/* $Id$ */

	/**
	* User Interface layer of the Knowledge Base
	* 
	* Last Editor:	$Author: skwashd $
	* @author		Dave Hall & Alejandro Pedraza
	* @package		phpbrain
	* @version		$Revision: 16713 $
	* @license		GPL
	**/
	class uikb
	{
		/**
		* Array of public functions in this class
		*
		* @access	public
		* @var		array
		*/
		var $public_functions = array(	'index'					=> True,
										'advsearch'				=> True,
										'edit_article'			=> True,
										'view_article'			=> True,
										'mail_article'			=> True,
										'pop_search'			=> True,
										'download_file'			=> True,
										'add_question'			=> True,
										'maintain_articles'		=> True,
										'maintain_questions'	=> True,
						);

		/**
		* Success or error messages
		*
		* @access	private
		* @var		string
		*/
		var $message;

		/**
		* To keep track if the nav bar has already been shown
		*
		* @access	private
		* @var		bool
		*/
		var $navbar_shown = False;

		/**
		* Business Object
		*
		* @access	private
		* @var		object	bo
		*/
		var $bo;

		/**
		* Template Object
		*
		* @access	private
		* @var		object	template
		*/
		var $t;

		/**
		* Categories to show
		*
		* @access	private
		* @var		array
		*/
		var $categories;

		/**
		* All categories accessible by user
		*
		* @access	private
		* @var		array
		*/
		var $all_categories;

		/**
		* Categories path
		*
		* @access	private
		* @var		string
		*/
		var $path = '';

		/**
		* Whether using sitemgr or not
		*
		* @access	private
		* @var		bool
		*/
		var $sitemgr;

		/**
		* Link string
		*
		* @access	private
		* @var		string
		*/
		var $link;

		/**
		* If using sitemgr, whether to allow question posting or not
		*
		* @access	private
		* @var		bool
		*/
		var $allow_questions = False;

		/**
		* Class constructor, instanciates bo class and auxiliary API classes, and reads confirmation messages
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	bool	$sitemgr	True if accessed through sitemgr
		* @param	string	$link		link prefix to use if accessed through sitemgr
		* @param	array	$arguments	Arguments passed by sitemgr
		*/
		function uikb($sitemgr=False, $link=False, $arguments=False)
		{
			$this->sitemgr					= $sitemgr;
			if ($link)
			{
				$this->link					= $link;
				$GLOBALS['phpgw']->translation->add_app('phpbrain');
			}
			else
			{
				$this->link					= '/index.php';
			}
			$this->bo						= CreateObject('phpbrain.bokb');
			if ($sitemgr)
			{
				$this->t					= CreateObject('phpgwapi.Template', $this->sitemgr);
				$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs', True);
				$this->nextmatchs->template = createobject('phpgwapi.Template', $this->sitemgr);
				$this->nextmatchs->template->set_file(array(
					'_nextmatchs' => 'nextmatchs.tpl'
				));
				$this->nextmatchs->template->set_block('_nextmatchs','nextmatchs');
				$this->nextmatchs->template->set_block('_nextmatchs','filter');
				$this->nextmatchs->template->set_block('_nextmatchs','form');
				$this->nextmatchs->template->set_block('_nextmatchs','icon');
				$this->nextmatchs->template->set_block('_nextmatchs','link');
				$this->nextmatchs->template->set_block('_nextmatchs','search');
				$this->nextmatchs->template->set_block('_nextmatchs','cats');
				$this->nextmatchs->template->set_block('_nextmatchs','search_filter');
				$this->nextmatchs->template->set_block('_nextmatchs','cats_search_filter');
			}
			else
			{
				$this->t					= $GLOBALS['phpgw']->template;
				$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			}
			if (isset($arguments['post_questions']) && $arguments['post_questions'] == 'on')
			{
				$this->allow_questions = True;
			}

			$this->message					= get_var('message', 'any', '');
			if ($this->bo->messages_array[$this->message])
			{
				$this->message = lang($this->bo->messages_array[$this->message]);
			}
		}
	
		/**
		* Shows main screen
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @return	mixed	Returns output string if accessed through sitemgr
		*/
		function index()
		{
			$category_passed	= (int) $_REQUEST['cat'];

			$this->t->set_file('main', 'main.tpl');
			$this->t->set_block('main', 'articles_block', 'articles');
			$this->t->set_block('main', 'articles_navigation_block', 'articles_navigation');
			$this->t->set_block('main', 'articles_latest_block', 'articles_latest');
			$this->t->set_block('main', 'articles_mostviewed_block', 'articles_mostviewed');
			$this->t->set_block('main', 'unanswered_questions_block', 'unanswered_questions');
			$this->t->set_var(array(
				'lang_last_modified'			=> lang('Last Modified'),
				'message'				=> $this->message,
				'search_tpl'			=> $this->show_basic_search(),
			));

			if(!$this->sitemgr)
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();
				$this->navbar_shown = True;

				$this->t->set_var('links_nav' ,'');
			}
			elseif($this->allow_questions)
			{
				$this->t->set_var('links_nav', "<a href='". $this->link('menuaction=phpbrain.uikb.index') ."'>". lang('Main View', 'phpbrain') ."</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href='". $this->link('menuaction=phpbrain.uikb.add_question') ."'>". lang('Add Question') ."</a>&nbsp;&nbsp;|<br>");
			}
			else
			{
				$this->t->set_var('link_add_question', "<a href='". $this->link('menuaction=phpbrain.uikb.index') ."'>". lang('Main View', 'phpbrain') ."</a>&nbsp;&nbsp;|<br><br>");
			}
			
			// *** SHOW CATEGORIES (not if searching) *** 
			if (!$this->bo->query && !$_POST['adv_search'])
			{
				$parent_id = 0;
				$show_path = '';
				if ($category_passed)
				{
					$parent_cat = $this->bo->return_single_category($category_passed);
					list(,$parent_cat) = each($parent_cat);
					$parent_id = $parent_cat['id'];
					$this->path= '';
					$show_path = lang ('You are in %1', $this->category_path($category_passed, True));
				}

				$this->bo->load_categories($category_passed);
				$num_main_categories = 0;
				foreach ($this->bo->categories as $cat)
				{
					if ($cat['parent'] == $parent_id) $num_main_categories ++;
				}
				$show_categories = $this->build_categories($parent_id, $num_main_categories);
				$tr_class = $this->sitemgr? 'divSideboxHeader' : 'th';
				if (!$show_categories)
				{
					if ($category_passed)
					{
						$browse_cats = '';
						$show_categories = '';
					}
					elseif (!$this->sitemgr)
					{
						$browse_cats = "<tr class='$tr_class'><td align=left><b>" . lang('Or browse the categories') . "</b></td></tr>";
						$show_categories = "<span style='text-align:center'>" . lang("To create categories, press 'Edit Categories' in the preferences menu")  . "</span>";
					}
					else
					{
						$browse_cats = '';
					}
				}

				if (!$category_passed && ($this->bo->preferences['show_tree'] == 'only_cat'))
				{
					$lang_articles = lang('Articles not classified under any category');
				}
				elseif (!$category_passed && ($this->bo->preferences['show_tree'] == 'all'))
				{
					$lang_articles = lang('All articles');
				}
				elseif (($category_passed && ($this->bo->preferences['show_tree'] == 'only_cat')) || !$this->bo->categories)
				{
					$lang_articles = lang('Articles in %1', $parent_cat['name']);
				}
				else
				{
					$lang_articles = lang('Articles in %1 and all its subcategories', $parent_cat['name']);
				}
			}
			else
			{
				$browse_cats = '';
				$show_categories = '';
				$show_path = '';
				$lang_articles = lang('Search results');
				$this->bo->load_categories($this->bo->cat);
			}
	
			$this->t->set_var(array(
				'browse_cats'	=> $browse_cats,
				'categories'	=> $show_categories,
				'path'			=> $show_path
			));

			// *** SHOW ARTICLES LIST ***
			// results from advanced search
			if ($_POST['adv_search'])
			{
				$articles_list = $this->bo->adv_search_articles();
			}
			// normal browsing or basic search
			else
			{
				$articles_list = $this->bo->search_articles($category_passed, 'published');
			}
			// echo "articles list: <pre>";print_r($articles_list);echo "</pre>";
			if (!$articles_list)
			{
				$this->t->set_var(array(
					'articles_navigation'	=> "<br>----- " . lang('There are no articles') . "-----",
					'articles'				=> ''
				));
			}
			else
			{
				if ($this->sitemgr) $this->nextmatchs->template->set_var('action_sitemgr', $this->link('menuaction=phpbrain.uikb.index'));
				$this->t->set_var(array(
					'left'		=> $this->nextmatchs->left($this->link, $this->bo->start, $this->bo->num_rows, 'menuaction=phpbrain.uikb.index&cat='.$category_passed),
					'right'		=> $this->nextmatchs->right($this->link, $this->bo->start, $this->bo->num_rows, 'menuaction=phpbrain.uikb.index&cat='.$category_passed),
					'num_regs'	=> $this->nextmatchs->show_hits($this->bo->num_rows, $this->bo->start)
				));
				$this->t->parse('articles_navigation', 'articles_navigation_block');

				foreach ($articles_list as $article_preview)
				{
					if ($article_preview['total_votes'])	// only show stars if article has been rated
					{
						$img_stars = "<img src='" . $GLOBALS['phpgw']->common->image('phpbrain', round($article_preview['average_votes']) . 'stars') . "' width=50 height=10>";
					}
					else
					{
						$img_stars = '';
					}
					if ($article_preview['files'])
					{
						$attachment = "<img src='" . $GLOBALS['phpgw']->common->image('phpbrain', 'attach') . "'>";
					}
					else
					{
						$attachment = '';
					}
	
					$query = $this->bo->query? '&query=' . urlencode($this->bo->query) : '';
					$this->path = ''; // have always to reset this before calling category_path()
					$category_path = $this->category_path($article_preview['cat_id']);
					$this->t->set_var(array(
						'art_num'		=> $article_preview['art_id'],
						'art_href'		=> $this->link('menuaction=phpbrain.uikb.view_article&art_id=' . $article_preview['art_id'] . $query),
						'art_title'		=> $article_preview['title'],
						'art_date'		=> $GLOBALS['egw']->common->show_date($article_preview['modified'], $GLOBALS['egw_info']['user']['preferences']['common']['dateformat'] . ' H:i'),
						'img_stars'		=> $img_stars,
						'attachment'	=> $attachment,
						'art_category'	=> $category_path? lang('in %1', $category_path) : '',
						'art_topic'		=> $article_preview['topic']
					));
					$this->t->parse('articles', 'articles_block', True);
				}
			}
			$this->t->set_var('lang_articles', $lang_articles);

			// *** SHOW LATEST ARTICLES LIST *** 
			if (!$articles_latest = $this->bo->return_latest_mostviewed($category_passed, 'created'))
			{
				$this->t->set_var('articles_latest', "<tr><td colspan=2 align=center><br>----- " . lang('None') . " -----</td></tr>");
			}

			for ($i=0; $i<sizeof($articles_latest); $i++)
			{
				$unpublished = $articles_latest[$i]['published']? '' : '(' . lang('unpublished') . ')';
				$this->path = '';
				$category_path = $this->category_path($articles_latest[$i]['cat_id']);
				$this->t->set_var(array(
					'line_num'		=> $i+1,
					'art_href'		=> $this->link('menuaction=phpbrain.uikb.view_article&art_id=' . $articles_latest[$i]['art_id']),
					'art_title'		=> $articles_latest[$i]['title'],
					'unpublished'	=> $unpublished,
					'art_date'		=> $GLOBALS['phpgw']->common->show_date($articles_latest[$i]['created'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
					'art_category'	=> $category_path? lang('in %1', $category_path) : ''
				));
				$this->t->parse('articles_latest', 'articles_latest_block', True);
			}
			$this->t->set_var('lang_latest', lang('Latest'));

			// *** SHOW MOST POPULAR ARTICLES LIST *** 
			if (!$most_viewed= $this->bo->return_latest_mostviewed($category_passed, 'views'))
			{
				$this->t->set_var('articles_mostviewed', "<tr><td colspan=2 align=center><br>----- " . lang('None') . " -----</td></tr>");
			}

			for ($i=0; $i<sizeof($most_viewed); $i++)
			{
				$unpublished = $most_viewed[$i]['published']? '' : '(' . lang('unpublished') . ')';
				$this->path = '';
				$this->t->set_var(array(
					'line_num'		=> $i+1,
					'art_href'		=> $this->link('menuaction=phpbrain.uikb.view_article&art_id=' . $most_viewed[$i]['art_id']),
					'art_title'		=> $most_viewed[$i]['title'],
					'unpublished'	=> $unpublished,
					'art_category'	=> $this->category_path($most_viewed[$i]['cat_id']),
					'art_views'		=> $most_viewed[$i]['views']
				));
				$this->t->parse('articles_mostviewed', 'articles_mostviewed_block', True);
			}
			$this->t->set_var(array(
				'lang_most_viewed'	=> lang('Most viewed'),
				'lang_views'		=> lang('views')
			));

			// *** SHOW UNANSWERED QUESTIONS *** 
			if (!$unanswered_questions = $this->bo->unanswered_questions($category_passed))
			{
				$this->t->set_var('unanswered_questions', "<tr><td colspan=2 align=center><br>----- " . lang('None') . " -----</td></tr>");
			}

			foreach ($unanswered_questions as $unanswered)
			{
				$this->path = '';
				$category_path = $this->category_path($unanswered['cat_id']);
				$this->t->set_var(array(
					'art_href'				=> $this->link(array('menuaction' => 'phpbrain.uikb.edit_article', 'q_id' => $unanswered['question_id'])),
					'art_title'				=> $unanswered['summary'],
					'who'					=> $unanswered['username'],
					'unanswered_category'	=> $category_path? lang('in %1', $category_path) : ''
				));
				$this->t->parse('unanswered_questions', 'unanswered_questions_block', True);
			}

			$more_questions = '';
			if ($this->bo->num_questions > $this->bo->preferences['num_lines'])
			{
				$more_questions = "<div style='text-align:right; padding-top:10px'><a href='" . $this->link('menuaction=phpbrain.uikb.maintain_questions') . "'>" . lang('See more questions...') . "</a></div>";
			}
			$this->t->set_var(array(
				'lang_unanswered'	=> lang('Unanswered questions'),
				'more_questions'	=> $more_questions
			));
			
			if ($this->sitemgr)
			{
				return $this->t->parse('out', 'main');
			}
			else
			{
				$this->t->pparse('output', 'main');
			}
		}

		/**
		* Shows advanced search form, that is posted to function index to handle the search
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @return	mixed	Returns output string if accessed through sitemgr
		*/
		function advsearch()
		{
			if (!$this->sitemgr)
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();
				$this->navbar_shown = True;
			}
			$this->t->set_file('search_form', 'adv_search.tpl');
			
			$this->t->set_var(array(
				'row_on'			=> $GLOBALS['phpgw_info']['theme']['row_on'],
				'row_off'			=> $GLOBALS['phpgw_info']['theme']['row_off'],
				'lang_advanced_search' => lang('Advanced Search'),
				'lang_find'			=> lang('Find results'),
				'lang_all_words'	=> lang('With all the words'),
				'lang_phrase'		=> lang('With the exact phrase'),
				'lang_one_word'		=> lang('With at least one of the words'),
				'lang_without_word'	=> lang('Without the words'),
				'lang_show_cats'	=> lang('Show messages in category'),
				'lang_all'			=> lang('all'),
				'lang_include_subs'	=> lang('Include subcategories'),
				'lang_pub_date'		=> lang('Publication date'),
				'lang_anytime'		=> lang('anytime'),
				'lang_3_months'		=> lang('past %1 months', 3),
				'lang_6_months'		=> lang('past %1 months', 6),
				'lang_past_year'	=> lang('past year'),
				'lang_ocurrences'	=> lang('Ocurrences'),
				'lang_anywhere'		=> lang('Anywhere in the article'),
				'lang_in_title'		=> lang('in the title'),
				'lang_in_topic'		=> lang('in the topic'),
				'lang_in_text'		=> lang('in the text'),
				'lang_num_res'		=> lang('Number of results per page'),
				'lang_user_prefs'	=> lang('User preferences'),
				'lang_order'		=> lang('Order results by'),
				'lang_created'		=> lang('Creation date'),
				'lang_artid'		=> lang('Article ID'),
				'lang_title'		=> lang('title'),
				'lang_user'			=> lang('user'),
				'lang_modified'		=> lang('Modification date'),
				'lang_desc'			=> lang('Descendent'),
				'lang_asc'			=> lang('Ascendent'),
				'lang_search'		=> lang('search'),
				'form_action'		=> $this->link('menuaction=phpbrain.uikb.index'),
				'select_categories'	=> $this->bo->categories_obj->formated_list('select', 'all', '', True)
			));
			if ($this->sitemgr)
			{
				return $this->t->parse('out', 'search_form');
			}
			else
			{
				$this->t->pparse('output', 'search_form');
			}
		}

		/**
		* Shows article details
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @return	mixed	Returns output string if accessed through sitemgr
		*/
		function view_article()
		{
			$article_id		= (int)get_var('art_id', 'GET', 0);
			$more_comments	= (int)get_var('more_comments', 'GET', 0);
			if ($_GET['printer'] || $_GET['mail'])
			{
				$print_view = True;
			}
			else
			{
				$print_view = False;
			}
			$article		= $this->bo->get_article($article_id);
			//echo "article: <pre>";print_r($article);echo  "</pre>";

			if (!$article_id || !$article) $this->die_peacefully("Error retrieving article");
			$can_edit = $this->bo->check_permission($this->bo->edit_right)? True : False;

			// Process article deletion
			if ($_POST['delete_article'])
			{
				$message = $this->bo->delete_article($article['files']);
				$GLOBALS['phpgw']->redirect_link($this->link, 'menuaction=phpbrain.uikb.index&message=' . $message);
				die();
			}

			// Process article publication
			if ($_POST['publish_article'])
			{
				$message = $this->bo->publish_article();
				$this->reload_page($article_id, $message);
				die();
			}

			// Process comment publication
			if ($_GET['pub_com'])
			{
				$message = $this->bo->publish_comment();
				$this->reload_page($article_id, $message);
				die();
			}

			// Process comment deletion
			if ($_GET['del_comm'])
			{
				$message = $this->bo->delete_comment();
				$this->reload_page($article_id, $message);
				die();
			}

			// Process comment and rating
			if ($_POST['comment_box'] || $_POST['Rate'])
			{
				$message = '';
				if ($_POST['comment_box'])
				{
					if (!$message = $this->bo->add_comment()) $this->die_peacefully('Comment could not be inserted in the database');
				}
				if ($_POST['Rate'])
				{
					$valid_rates = array(1,2,3,4,5);
					if (!$data = $GLOBALS['phpgw']->session->appsession('ratings', 'phpbrain')) $data = array();
					if (($this->bo->user_has_voted() && !$this->sitemgr) || in_array($article['art_id'], $data) || !in_array($_POST['Rate'], $valid_rates))
						$this->die_peacefully('Rating invalid');
					if (!$this->bo->add_rating($article['votes_' . $_POST['Rate']], $this->sitemgr)) $this->die_peacefully('Unable to add rating to database');
					switch($message)
					{
						case 'comm_ok':
							$message = 'comm_rate_ok';
							break;
						case 'comm_submited':
							$message = 'comm_rate_submited';
							break;
						default:
							$message = 'rate_ok';
							break;
					}
				}
				$this->reload_page($article_id, $message);
				die();
			}

			// Process file upload
			if ($_FILES)
			{
				$message = $this->bo->process_upload();
				$this->reload_page($article_id, $message);
				die();
			}
			// Process file deletion
			if ($_GET['delete_file'])
			{
				$message = $this->bo->delete_file($article['files']);
				$this->reload_page($article_id, $message);
				die();
			}

			// Process related articles added
			if ($_POST['update_related'] && !empty($_POST['related_articles']))
			{
				$message = $this->bo->add_related();
				$this->reload_page($article_id, $message);
				die();
			}
			// Process related articles deletion
			if ($_GET['delete_related'])
			{
				$this->bo->delete_related();
				$this->reload_page($article_id, 'del_rel_ok');	// I think there's no way of telling a deletion went wrong... (affected rows=0 always)
				die();
			}

			// Process links added
			if ($_POST['submit_link'])
			{
				$message = $this->bo->add_link();
				$this->reload_page($article_id, $message);
				die();
			}
			// Process links deletion
			if ($_GET['delete_link'])
			{
				$message = $this->bo->delete_link();
				$this->reload_page($article_id, $message);
				die();
			}

			// *** SHOW ARTICLE ***
			if ($print_view)
			{
				// add a content-type header to overwrite an existing default charset in apache (AddDefaultCharset directiv)
				header('Content-type: text/html; charset='.$GLOBALS['egw']->translation->charset());
				ob_end_flush();

				$this->t->set_file('view_article', 'print_article.tpl');
				$this->t->set_block('view_article', 'file_item_block', 'file_item');
				$this->t->set_block('view_article', 'file_block', 'file');
				$this->t->set_block('view_article', 'related_article_block', 'related_article');
				$this->t->set_block('view_article', 'related_block', 'related');
				$this->t->set_block('view_article', 'links_block', 'links');
				$this->t->set_block('view_article', 'show_links_block', 'show_links');
			}
			else
			{
				$this->t->set_file('view_article', 'view_article.tpl');
				$this->t->set_block('view_article', 'easy_question_block', 'easy_question');
				$this->t->set_block('view_article', 'comment_block', 'comment');
				$this->t->set_block('view_article', 'comment_form_block', 'comment_form');
				$this->t->set_block('view_article', 'rating_graph_block', 'rating_graph');
				$this->t->set_block('view_article', 'rating_form_block', 'rating_form');
				$this->t->set_block('view_article', 'file_item_block', 'file_item');
				$this->t->set_block('view_article', 'file_upload_block', 'file_upload');
				$this->t->set_block('view_article', 'related_article_block', 'related_article');
				$this->t->set_block('view_article', 'related_article_add_block', 'related_article_add');
				$this->t->set_block('view_article', 'links_block', 'links');
				$this->t->set_block('view_article', 'links_add_block', 'links_add');
				$this->t->set_block('view_article', 'img_delete_block', 'img_delete');
				$this->t->set_block('view_article', 'edit_del_block', 'edit_del');
				$this->t->set_block('view_article', 'publish_btn_block', 'publish_btn');
				$this->t->set_block('view_article', 'history_line_block', 'history_line');

				if (!$this->sitemgr)
				{
					// $GLOBALS['phpgw_info']['flags']['css'] = $this->tabs_css();	Don't use this 'cause incompatible with sitemgr
					$GLOBALS['phpgw']->js->validate_file('tabs','tabs');
					$GLOBALS['phpgw']->js->set_onload('tab.init();');
					$GLOBALS['phpgw_info']['flags']['java_script_thirst'] = "<script>function openpopup() {window1=window.open('" . $this->link('menuaction=phpbrain.uikb.pop_search') . "', 'Search', 'width=800,height=600,toolbar=no,scrollbars=yes,resizable=yes');}</script>";

					$this->t->set_var('link_main_view', '');

					$GLOBALS['phpgw']->common->phpgw_header();
					echo parse_navbar();
					$this->navbar_shown = True;
				}
				else
				{
					// Since cannot use js object with sitemanager, I have to manually insert the javascript include:
					$this->t->set_var('tabs_script', $GLOBALS['phpgw']->link('/phpgwapi/js/tabs/tabs.js'));

					$this->t->set_var('link_main_view', "<a href='". $this->link('menuaction=phpbrain.uikb.index') ."'>". lang('Main View', 'phpbrain') ."</a>&nbsp;&nbsp;|<br>");
				}
			}

			$this->t->set_var(array(
				'message'				=> "<div style='text-align:center; color:red'>".$this->message."</div>",
				'mail_message'			=> '',
				'search_tpl'			=> $this->show_basic_search(),
				'lang_article'			=> lang('Article'),
				'lang_linksfiles'		=> lang('Links & Files'),
				'lang_history'			=> lang('History'),
				'lang_category'			=> lang('Category'),
				'lang_title'			=> lang('Title'),
				'lang_topic'			=> lang('Topic'),
				'lang_keywords'			=> lang('Keywords'),
				'lang_add_comments'		=> lang('If you wish, you can comment this article here'),
				'lang_please_rate'		=> lang('Please rate the pertinence and quality of this article'),
				'lang_poor'				=> lang('Poor'),
				'lang_excellent'		=> lang('Excellent'),
				'lang_attached_files'	=> lang('Attached Files'),
				'lang_related_articles'	=> lang('Related Articles in the Knowledge Base'),
				'lang_links'			=> lang('Links'),
				'lang_date'				=> lang('Date'),
				'lang_user'				=> lang('User'),
				'lang_action'			=> lang('Action'),
				'lang_upload'			=> lang('upload'),
				'lang_attach_file'		=> lang('Attach file'),
				'lang_delete'			=> lang('delete'),
				'img_printer'			=> $GLOBALS['phpgw']->common->image('phpbrain', 'articleprint'),
				'href_printer'			=> $this->link('menuaction=phpbrain.uikb.view_article&art_id='. $article_id .'&printer=1'),
				'img_mail'				=> $GLOBALS['phpgw']->common->image('phpbrain', 'mail'),
				'img_src_del'			=> $GLOBALS['phpgw']->common->image('phpbrain', 'delete'),
				'alt_printer'			=> lang('Printer view'),
				'alt_mail'				=> lang('Mail article'),
				'href_mail'				=> $this->link('menuaction=phpbrain.uikb.mail_article&art_id='. $article_id),
				'form_article_action'	=> $this->link('menuaction=phpbrain.uikb.view_article&art_id=' . $article_id),
				'form_del_action'		=> $this->link('menuaction=phpbrain.uikb.view_article&art_id=' . $article_id),
				'url_bluedot'			=> $GLOBALS['phpgw']->common->image('phpbrain', 'bluedot')
			));

			$published = $article['published']? '' : lang("This article hasn't yet been published in the Knowledge Base");
			$lastmodif = '';
			$img_stars = '';
			if ($article['modified_username'])
			{
				$lastmodif = lang('Last modification by %1 on %2', $article['modified_username'], $GLOBALS['phpgw']->common->show_date($article['modified'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']));
			}
			// only show stars if article has been rated
			if ($article['total_votes'])
			{
				$img_stars = "<img src='" . $GLOBALS['phpgw']->common->image('phpbrain', round($article['average_votes']) . 'stars') . "' width=50 height=10>";
			}

			// show edit and delete button if user has edit rights and he's not using sitemgr
			if (!$print_view && $can_edit && !$this->sitemgr)
			{
				$this->t->set_var(array(
					'form_edit_art'		=> $this->link('menuaction=phpbrain.uikb.edit_article&art_id=' . $article_id),
					'form_del_art'		=> $this->link('menuaction=phpbrain.uikb.view_article&art_id=' . $article_id),
					'lang_edit_art'			=> lang('Edit article'),
					'lang_delete_article'	=> lang('Delete article')
				));
				$this->t->parse('edit_del', 'edit_del_block');
			}
			else
			{
				$this->t->set_var('edit_del', '');
			}

			// show publish button if article is unpublish and user has publish rights on owner
			if (!$print_view && !$article['published'] && ($this->bo->grants[$article['user_id']] & $this->bo->publish_right))
			{
				$this->t->set_var(array(
					'form_publish_art'		=> $this->link('menuaction=phpbrain.uikb.view_article&art_id='. $article_id),
					'lang_publish_article'	=> lang('publish')
				));
				$this->t->parse('publish_btn', 'publish_btn_block');
			}
			else
			{
				$this->t->set_var('publish_btn', '');
			}

			$this->path = '';
			$this->t->set_var(array(
				'art_id'			=> $article['art_id'],
				'lang_unpublished'	=> $published,
				'img_stars'			=> $img_stars,
				'links_cats'		=> $this->category_path($article['cat_id'], !$print_view),
				'title'				=> $article['title'],
				'topic'				=> $article['topic'],
				'keywords'			=> $article['keywords'],
				'createdby'			=> lang('Created by %1 on %2', $article['username'], $GLOBALS['phpgw']->common->show_date($article['created'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'])),
				'last_modif'		=> $lastmodif,
				'content'			=> $article['text']
			));
			
			$this->t->set_var(array(
				'easy_question'			=> '',
				'lang_comments'			=> '',
				'link_more_comments'	=> '',
				'comment'				=> '',
				'comment_form'			=> '',
				'rating_form'			=> '',
				'rating_graph'			=> '',
				'submit_comment'		=> '',
				'form_article_action'	=> ''
			));

			if (!$print_view && $article['published'])
			{
				// show feedback question if article has been published, a basic search was done and this article hasn't been given any feedback on this session
				if (!$data = $GLOBALS['phpgw']->session->appsession('feedback', 'phpbrain')) $data = array();
				if ($this->bo->query && !in_array($article['art_id'], $data))
				{
					$this->t->set_var(array(
						'tr_bgcolor'			=> $GLOBALS['phpgw_info']['theme']['row_off'],
						'query'					=> $this->bo->query,
						'form_easy_q_action'	=> $this->link('menuaction=phpbrain.uikb.view_article&art_id=' . $article_id),
						'lang_question_easy'	=> lang('Was it easy to find this article using the above search string?'),
						'lang_yes'				=> lang('yes'),
						'lang_no'				=> lang('no'),
						'lang_please'			=> lang('By answering this question you will help to get the answer quicker the next time')
					));
					$this->t->parse('easy_question', 'easy_question_block');
				}

				// show comments if article has been published
				$comments = $this->bo->get_comments($article_id, !$more_comments);
				foreach ($comments as $comment)
				{
					// only show unpublished comments is user has edition rights on article owner
					if (!$comment['published'] && !($this->bo->grants[$article['user_id']] & $this->bo->edit_right)) continue;
					if ($comment['published'])
					{
						$link_publish = '';
					}
					else
					{
						$link_publish = "<a href='". $this->link('menuaction=phpbrain.uikb.view_article&art_id='. $article_id .'&pub_com='. $comment['comment_id']) ."'>" . lang('publish') . "</a>";
					}

					// user can delete comment if he has edition rights and didn't enter through sitemgr
					if (!$this->sitemgr && ($this->bo->grants[$article['user_id']] & $this->bo->edit_right))
					{
						$link_delete = "<a href='". $this->link('menuaction=phpbrain.uikb.view_article&art_id='. $article_id . '&del_comm='. $comment['comment_id']) ."'>". lang('delete') ."</a>";
					}
					else
					{
						$Link_delete = '';
					}

					$this->t->set_var(array(
						'comment_date'		=> $GLOBALS['phpgw']->common->show_date($comment['entered'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
						'comment_user'		=> $comment['username'],
						'link_publish'		=> $link_publish,
						'link_delete'		=> $link_delete,
						'comment_content'	=> $comment['comment']
					));
					$this->t->parse('comment', 'comment_block', True);
				}
				$lang_comments = lang('Comments');
				if (!$more_comments && $this->preferences['num_comments'] != 'All' && $this->bo->num_comments > $this->bo->preferences['num_comments'])
				{
					$link_more_comments = "<a href='" . $this->link('menuaction=phpbrain.uikb.view_article&art_id=' . $article_id . '&more_comments=1') . "'>" . lang('Show all comments') . "</a>";
					$lang_comments = lang('Latest comments');
				}
				$this->t->parse('comment_form', 'comment_form_block');
				$this->t->set_var(array(
					'link_more_comments'	=> $link_more_comments,
					'lang_comments'			=> $lang_comments
				));

				// setup voting form if article has been published and (user has not voted already or accessed through sitemgr) and has not voted this article in this session
				if (!$data = $GLOBALS['phpgw']->session->appsession('ratings', 'phpbrain')) $data = array();
				if (($this->bo->user_has_voted($article_id) && !$this->sitemgr) || in_array($article['art_id'], $data))
				{
					$this->t->set_var(array(
						'rating_form'			=> lang('You have already qualified this article'),
						'submit_comment'		=> "<tr><td colspan=7 align=left><br><input type=submit name='comment' value='". lang('Submit comment') . "'></td></tr>",
					));
				}
				else
				{
					$this->t->set_var('submit_comment', "<tr><td colspan=7 align=left><br><input type=submit name='comment' value='". lang('Submit comment and rating') . "'></td></tr>");
					$this->t->parse('rating_form', 'rating_form_block');
				}

				// setup voting graph if article has been published
				if ($article['votes_1'] != 0 || $article['votes_2'] != 0 || $article['votes_3'] != 0 || $article['votes_4'] != 0 || $article['votes_5'] != 0)
				{
					// normalize vote frequency to range 0 - 40
					$max_vote = max($article['votes_1'], $article['votes_2'], $article['votes_3'], $article['votes_4'], $article['votes_5']);
					for ($i=1; $i<=5; $i++)
					{
						$this->t->set_var('bar_' . $i, $article['votes_' . $i] / $max_vote *40);
					}
					$this->t->set_var(array(
						'lang_average'	=> lang('Average rating'),
						'average_rating'=> sprintf("%01.1f", $article['average_votes']),
						'numpeople'		=> $article['total_votes'],
						'lang_people'	=> lang('people have rated this article')
					));
					$this->t->parse('rating_graph', 'rating_graph_block', True);
				}
				else
				{
					$this->t->set_var('rating', lang('Nobody has rated this article so far'));
				}
			}

			// show file list
			if (!$article['files'])
			{
				$this->t->set_var($print_view? 'file' : 'file_item', '');
			}
			else
			{	
				foreach ($article['files'] as $file)
				{
					ereg('^kb[0-9]*-(.*)', $file['file'], $new_filename);
					if (!$this->sitemgr && !$print_view && $can_edit)
					{
						$this->t->set_var(array(
							'href_del'	=> $this->link('menuaction=phpbrain.uikb.view_article&art_id=' . $article_id . '&delete_file=' . urlencode($file['file'])),
						));
						$this->t->parse('img_delete', 'img_delete_block');
					}
					else
					{
						$this->t->set_var('img_delete', '');
					}
					$this->t->set_var(array(
						'file_name'		=> $new_filename[1],
						'file_comment'	=> $file['comment'],
						'href_file'		=>	$this->link('menuaction=phpbrain.uikb.download_file&art_id=' . $article_id . '&file=' . urlencode($file['file']))
					));
					$this->t->parse('file_item', 'file_item_block', True);
				}
				if ($print_view) $this->t->parse('file', 'file_block');
			}
			// show upload form if user has edition rights and is not in sitemgr
			if (!$this->sitemgr && !$print_view && $can_edit)
			{
				$this->t->set_var(array(
					'form_file_action'		=> $this->link('menuaction=phpbrain.uikb.view_article&art_id=' . $article_id),
					'lang_attach_file'		=> lang('Attach File'),
					'lang_comment'			=> lang('comment'),
					'lang_upload'			=> lang('Upload')
				));
				$this->t->parse('file_upload', 'file_upload_block');
			}
			else
			{
				$this->t->set_var('file_upload', '');
			}

			// show related articles list
			if (!$related_articles = $this->bo->get_related_articles($article_id))
			{
				$this->t->set_var($print_view? 'related' : 'related_article', '');
			}
			else
			{
				foreach ($related_articles as $related)
				{
					if (!$this->sitemgr && !$print_view && $can_edit)
					{
						$this->t->set_var(array(
							'href_del'	=> $this->link('menuaction=phpbrain.uikb.view_article&art_id=' . $article_id . '&delete_related=' . urlencode($related['art_id'])),
						));
						$this->t->parse('img_delete', 'img_delete_block');
					}
					else
					{
						$this->t->set_var('img_delete', '');
					}
					$this->t->set_var(array(
						'related_id'		=> $related['art_id'],
						'href_related'		=> $this->link('menuaction=phpbrain.uikb.view_article&art_id=' . $related['art_id']),
						'title_related'		=> $related['title']
					));
					$this->t->parse('related_article', 'related_article_block', True);
				}
				if ($print_view) $this->t->parse('related', 'related_block');
			}
			// show add new article if user has edition rights and is not in sitemgr
			if (!$this->sitemgr && !$print_view && $can_edit)
			{
				$this->t->set_var(array(
					'lang_add_related'		=> lang('Add articles'),
					'lang_select_articles'	=> lang('Select articles'),
					'lang_clear'			=> lang('clear'),
					'lang_update'			=> lang('update'),
					'form_add_article_action' => $this->link('menuaction=phpbrain.uikb.view_article&art_id=' . $article_id)
				));
				$this->t->parse('related_article_add', 'related_article_add_block');
			}
			else
			{
				$this->t->set_var('related_article_add', '');
			}

			// show links
			if (!$links = $article['urls'])
			{
				$this->t->set_var('links', '');
				$this->t->set_var('show_links', '');
			}
			else
			{
				foreach ($article['urls'] as $link)
				{
					if (!$this->sitemgr && !$print_view && $can_edit)
					{
						$this->t->set_var(array(
							'href_del'	=> $this->link('menuaction=phpbrain.uikb.view_article&art_id=' . $article_id . '&delete_link=' . urlencode($link['link'])),
						));
						$this->t->parse('img_delete', 'img_delete_block');
					}
					else
					{
						$this->t->set_var('img_delete', '');
					}
					// if protocol not set, add it
					if (!ereg('://', $link['link'])) $link['link'] = 'http://' . $link['link'];

					if (!$link['title']) $link['title'] = $link['link'];
					$this->t->set_var(array(
						'href_link'		=> $link['link'],
						'title_link'	=> $link['title']
					));
					$this->t->parse('links', 'links_block', True);
				}
				if ($print_view) $this->t->parse('show_links', 'show_links_block');
			}
			// show add new link if user has edition rights and is not in sitemgr
			if (!$this->sitemgr && !$print_view && $this->bo->check_permission($can_edit))
			{
				$this->t->set_var(array(
					'lang_add_link'		=> lang('Add link'),
					'lang_title'		=> lang('title'),
					'lang_update'		=> lang('Update'),
					'form_add_link_action' => $this->link('menuaction=phpbrain.uikb.view_article&art_id=' . $article_id)
				));
				$this->t->parse('links_add', 'links_add_block');
			}
			else
			{
				$this->t->set_var('links_add', '');
			}

			// Show history
			if ($print_view || !$history = $this->bo->return_history())
			{
				$this->t->set_var('history_line', '');
			}
			else
			{
				foreach ($history as $event)
				{
					$this->t->set_var(array(
						'tr_color'			=> $this->nextmatchs->alternate_row_color($tr_color),
						'history_date'		=> $event['datetime'],
						'history_user'		=> $event['owner'],
						'history_action'	=> $event['action']
					));
					$this->t->parse('history_line', 'history_line_block', True);
				}
			}

			$this->t->set_var('img_delete', '');
			if ($_GET['mail'])
			{
				$this->t->set_var('mail_message', $_POST['val_message']);
				$this->t->parse('plain_html', 'view_article');
				$message = $this->bo->mail_article($this->t->get_var('plain_html'));
				$this->reload_page($article_id, $message);
				die();
			}
			elseif ($this->sitemgr && !$print_view)
			{
				return $this->t->parse('out', 'view_article');
			}
			else
			{
				$this->t->pparse('output', 'view_article');
			}
		}

		/**
		* Mails article
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @return	mixed	When showing form, returns string output if acccess through sitemgr
		*/
		function mail_article()
		{
			$article_id = (int)get_var('art_id', 'GET', 0);

			$recipient = '';
			$subject = lang('Knowledge Base article #%1', $article_id);
			$reply = '';
			$message = '';

			$this->t->set_file('mail_form', 'mail_article.tpl');
			$this->t->set_var(array(
				'form_action'		=> $this->link('menuaction=phpbrain.uikb.view_article&mail=1&art_id='. $article_id),
				'row_on'			=> $GLOBALS['phpgw_info']['theme']['row_on'],
				'row_off'			=> $GLOBALS['phpgw_info']['theme']['row_off'],
				'lang_recipient'	=> lang('Recipient'),
				'val_recipient'		=> $recipient,
				'lang_subject'		=> lang('Subject'),
				'val_subject'		=> $subject,
				'lang_reply'		=> lang('Reply-to'),
				'val_reply'			=> $reply,
				'lang_message'		=> lang('Message'),
				'val_message'		=> $message,
				'lang_send'			=> lang('send')
			));

			if (!$this->sitemgr)
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();
				$this->navbar_shown = True;
			}

			if ($this->sitemgr)
			{
				return $this->t->parse('out', 'mail_form');
			}
			else
			{
				$this->t->pparse('out', 'mail_form');
			}
		}

		/**
		* Shows popup windows with articles table
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @return	void
		*/
		function pop_search()
		{
			$actual_category			= (int)get_var('cat', 'GET', 0);
			$this->bo->sort				= get_var('sort', 'any', 'ASC');
			$this->bo->order			= get_var('order', 'any', 'title');
			$this->bo->query			= get_var('query', 'any', '');
			$this->bo->load_categories(0);
			$articles_list = $this->bo->search_articles($actual_category);
			$this->t->set_file('popup', 'popup_search.tpl');
			$this->t->set_block('popup', 'table_row_block', 'table_row');
			$this->t->set_var(array(
				'lang_category'		=> lang('Category'),
				'lang_all'			=> lang('All'),
				'lang_search'		=> lang('Search'),
				'lang_select'		=> lang('Select'),
				'th_color'			=> $GLOBALS['phpgw_info']['theme']['th_bg'],
				'value_query'		=> $this->bo->query,
				'form_select_articles_action' => $this->link('menuaction=phpbrain.uikb.pop_search'),
				'form_filters_action' => $this->link('menuaction=phpbrain.uikb.pop_search&start=' . $this->bo->start . '&sort=' . $this->bo->sort),
				'head_number'		=> $this->nextmatchs->show_sort_order($this->bo->sort, 'art_id', $this->bo->order, '', lang('Article ID')),
				'head_title'		=> $this->nextmatchs->show_sort_order($this->bo->sort, 'title', $this->bo->order, '', lang('Title')),
				'left'				=> $this->nextmatchs->left($this->link, $this->bo->start, $this->bo->num_rows, 'menuaction.phpbrain.uikb.pop_search&query=' . $this->bo->query),
				'right'				=> $this->nextmatchs->right($this->link, $this->bo->start, $this->bo->num_rows, 'menuaction.phpbrain.uikb.pop_search&query=' . $this->bo->query),
				'num_regs'			=> $this->nextmatchs->show_hits($this->bo->num_rows, $this->bo->start),
				'select_categories'	=> $this->bo->categories_obj->formated_list('select', 'all', '', True)
			));

			foreach ($articles_list as $article)
			{
				$tr_color = $this->nextmatchs->alternate_row_color($tr_color);
				$this->t->set_var(array(
					'tr_color'		=> $tr_color,
					'number'		=> $article['art_id'],
					'title'			=> $article['title'],

				));
				$this->t->parse('table_row', 'table_row_block', True);
			}

			$this->t->pparse('output', 'popup');
		}

		/**
		* New articles (answering questions or just new) and edit existing articles
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @return	void
		*/
		function edit_article()
		{
			$this->t->set_file('edit_article', 'edit_article.tpl');
			$this->t->set_block('edit_article', 'answer_question_block', 'answer_question');
			$this->t->set_block('edit_article', 'article_id_block', 'article_id');
			
			$this->t->set_var(array(
				'lang_articleID'		=> lang('Article ID'),
				'lang_category'			=> lang('Category'),
				'lang_none'				=> lang('None'),
				'lang_title'			=> lang('Title'),
				'lang_topic'			=> lang('Topic'),
				'lang_keywords'			=> lang('Keywords'),
			));

			// These are the default values, that apply for entering a new article
			$article_id			= (int)get_var('art_id', 'any', 0);
			$title				= '';
			$topic				= '';
			$keywords			= '';
			$content			= '';
			$category_selected	= '';
			$hidden_fields		= '';
			$btn_save			= "<input type='submit' value='". lang('Save') . "' name='save'>&nbsp;";
			$btn_cancel			= "<input type='submit' value='". lang('Cancel') . "' name='cancel'>";
			$extra				= '';
			$this->t->set_var(array(
				'answer_question'	=> '',
				'article_id'		=> '',
				));

			// saving either an edited or a new article (answering a question or just a new article)
			if ($_POST['save'])
			{
				$article_id = (int)get_var('editing_article_id', 'POST', 0);
				$article	= ($article_id)? $this->bo->get_article($article_id) : false;

				//data validation
				if (!$_POST['title'])
				{
					$this->message .= lang('You must enter a title') . '<br>';
				}
				if (!$_POST['topic'])
				{
					$this->message .= lang('You must enter a topic') . '<br>';
				}
				if (!$_POST['exec']['text'])
				{
					$this->message .= lang('The article is empty') . '<br>';
				}
				
				if ($this->message)
				{
					$this->message .= '<br>' . lang('Please try again');
				}
				elseif ($edited_art = $this->bo->save_article())
				{
					// if article is new tell to insert files and stuff
					$message = '';
					if (!$article) $message = '&message=add_ok_cont&tabpage=2';
					$GLOBALS['phpgw']->redirect_link($this->link, 'menuaction=phpbrain.uikb.view_article&art_id=' .  $edited_art . $message);
					die();
				}
				else
				{
					$this->message = $this->bo->error_msg;
				}
			}

			// if an error ocurred fill fields with values
			if ($this->message)
			{
				$category_selected	= (int)get_var('cat_id', 'POST', 0);
				$title				= get_var('title', 'POST', '');
				$topic				= get_var('topic', 'POST', '');
				$keywords			= get_var('keywords', 'POST', '');
				$temp = get_var('exec', 'POST', '');
				$content = $temp['text'];
			}

			// Edit existant article
			if ((int)get_var('art_id', 'GET', 0))
			{
				// Process cancel button
				if ($_POST['cancel'])
				{
					$GLOBALS['phpgw']->redirect_link($this->link, 'menuaction=phpbrain.uikb.view_article&art_id=' .  $_GET['art_id']);
					die();
				}

				if (!$this->message)
				{
					$article	= $this->bo->get_article($article_id);

					// Check edit rights
					if (!$this->bo->check_permission($this->bo->edit_right)) $this->die_peacefully('You have not the proper permissions to do that');

					$title		= $article['title'];
					$topic		= $article['topic'];
					$keywords	= $article['keywords'];
					$content	= $article['text'];
					$category_selected = $article['cat_id'];
				}

				$this->t->set_var(array(
					'show_articleID'	=> $article_id . "<input type=hidden name='editing_article_id' value=" . $article_id . ">",
				));
				$this->t->parse('article_id', 'article_id_block');
			}

			// answering a question
			if ((int)get_var('q_id', 'GET', 0))
			{
				// Process cancel button
				if ($_POST['cancel'])
				{
					$GLOBALS['phpgw']->redirect_link($this->link, 'menuaction=phpbrain.uikb.index');
					die();
				}
				$q_id = (int)get_var('q_id', 'GET', 0);
				$question = $this->bo->get_question($q_id);
				$hidden_fields .= "<input type=hidden name='answering_question' value='" . $q_id . "'>";
				$this->t->set_var(array(
					'lang_summary'			=> lang('Summary'),
					'lang_details'			=> lang('Details'),
					'lang_category'			=> lang('Suggested category'),
					'lang_head_question'	=> lang('Create a new article to answer the question asked by %1 in %2', $question['username'], $question['creation']),
					'question_summary'		=> $question['summary'],
					'question_details'		=> $question['details']
				));
				$this->t->parse('answer_question', 'answer_question_block');

				$title = $question['summary'];
				$category_selected = $question['cat_id'];
			}
		
			if ( !isset($GLOBALS['phpgw']->richtext) || !is_object($GLOBALS['phpgw']->richtext) )
			{
				$GLOBALS['phpgw']->richtext =& createObject('phpgwapi.richtext');
			}
			$GLOBALS['phpgw']->richtext->replace_element('exec_text');
			$GLOBALS['phpgw']->richtext->generate_script();

			// Finally, fill the input fields
			if (!$this->sitemgr)
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();
				$this->navbar_shown = true;
			}	

			$select_category = $this->bo->select_category($category_selected);
			$this->t->set_var('select_category', $select_category);

			$link_params = array('menuaction' => 'phpbrain.uikb.edit_article');
			if ( isset($_GET['art_id']) && (int)$_GET['art_id'])
			{
				$link_params['art_id'] = $_GET['art_id'];
			}
			if ( isset($_GET['q_id']) && (int)$_GET['q_id'])
			{
				$link_params['q_id'] = $_GET['q_id'];
			}

			$this->t->set_var(array(
				'message'			=> "<tr><td colspan=2 align=center style='color:red'>" . $this->message . "</td></tr>",
				'hidden_fields'		=> $hidden_fields,
				'form_action'		=> $this->link($link_params),
				'value_title'		=> $title,
				'value_topic'		=> $topic,
				'value_keywords'	=> $keywords,
				'value_text'		=> $content,
				'btn_save'			=> $btn_save,
				'btn_cancel'		=> $btn_cancel
			));

			$this->t->pparse('output', 'edit_article');
		}

		/**
		* Adds question to knowledge base.
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @return	mixed	When showing form, returns string output if acccess through sitemgr
		**/
		function add_question()
		{
			// if in sitemgr, check that questions can be added
			if ($this->sitemgr && !$this->allow_questions) $this->die_peacefully('You have not the proper permissions to do that');
			if ($_POST['submit'])
			{
				$this->bo->add_question();
				$GLOBALS['phpgw']->redirect($this->link('menuaction=phpbrain.uikb.index'));	// don't use redirect_link cause it ain't work in sitemgr
				end;
			}

			$this->t->set_file('question_form', 'question.tpl');
			$message = '';

			if ($this->bo->admin_config['publish_questions'] == 'True')
			{
				$lang_posting_process = 'Your question will be published immediately';
			}
			else
			{
				$lang_posting_process = 'Your question will be posted, but will only be published after approval by a user with publishing rights';
			}

			$this->t->set_var(array(
				'null'					=> '',
				'message'				=> $message,
				'lang_search_kb'		=> lang('Before submiting a question, please search in the knowledge base first'),
				'lang_enter_words'		=> lang('Enter one or two words describing the issue, or type the article number if you know it'),
				'lang_search'			=> lang('Search'),
				'lang_advanced_search'	=> lang('Advanced Search'),
				'lang_post_question'	=> lang("If you can't find answers to your problem in the knowledge base, describe it below"),
				'lang_summary'			=> lang('Summary'),
				'lang_details'			=> lang('Details'),
				'lang_select_cat'		=> lang('category'),
				'lang_submit'			=> lang('Submit'),
				'lang_cancel'			=> lang('Cancel'),
				'lang_none'				=> lang('none'),
				'posting_process'		=> lang($lang_posting_process),
				'form_search_action'	=> $this->link('menuaction=phpbrain.uikb.index'),
				'form_question_action'	=> $this->link('menuaction=phpbrain.uikb.add_question'),
				'link_adv_search'		=> $this->link('menuaction=phpbrain.uikb.advsearch')
				));

			if (!$this->sitemgr)
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();
				$this->navbar_shown = True;
			}

			$select_category = $this->bo->select_category();
			$this->t->set_var('select_category', $select_category);

			if ($this->sitemgr)
			{
				return $this->t->parse('out', 'question_form');
			}
			else
			{
				$this->t->pparse('output', 'question_form');
			}
		}

		/**
		* Article maintenance view
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @return	void
		*/
		function maintain_articles()
		{
			$actual_category = (int)get_var('cat', 'any', 0);

			if (!$this->bo->order) $this->bo->order = 'created';
			if (!$this->bo->sort) $this->bo->sort = 'DESC';
			
			$this->bo->load_categories($actual_category);

			// obtain articles to which one has any kind of permission
			$articles_list = $this->bo->search_articles($actual_category, $this->bo->publish_filter, $this->bo->read_right | $this->bo->edit_right | $this->bo->publish_right);
			//echo "articles_list: <pre>";print_r($articles_list);echo "</pre>";

			// Process article deletion
			if ($_GET['delete'] || $_POST['delete_selected'])
			{
				if ($_GET['delete'])
				{
					$selected = array($_GET['delete'] => '');
				}
				else
				{
					$selected = $_POST['select'];
				}
				$errors = False;
				foreach ($selected as $art_id => $trash)
				{
					$target_art = array();
					foreach($articles_list as $article)
					{
						if ($article['art_id'] == $art_id)
						{
							$target_art = $article;
							break;
						}
					}
					$message = $this->bo->delete_article($target_art['files'], $target_art['art_id'], $target_art['user_id']);
					if ($message != 'del_art_ok') $errors = $message;
				}
				if (!$errors)
				{
					$message = $_GET['delete']? 'del_art_ok' : 'del_arts_ok';
				}
				$GLOBALS['phpgw']->redirect_link($this->link, 'menuaction=phpbrain.uikb.maintain_articles&message=' . $message);
				die();
			}

			// Process article publication
			if ($_GET['publish'] || $_POST['publish_selected'])
			{
				if ($_GET['publish'])
				{
					$selected = array($_GET['publish'] => '');
				}
				else
				{
					$selected = $_POST['select'];
				}
				$errors = False;
				foreach ($selected as $art_id => $trash)
				{
					$target_art = array();
					foreach ($articles_list as $article)
					{
						if ($article['art_id'] == $art_id)
						{
							$target_art = $article;
							break;
						}
					}
					$message = $this->bo->publish_article($target_art['art_id'], $target_art['user_id']);
					if ($message != 'publish_ok') $errors = $message;
				}
				if (!$errors)
				{
					$message = $_GET['publish']? 'publish_ok' : 'publishs_ok';
				}
				$GLOBALS['phpgw']->redirect_link($this->link, 'menuaction=phpbrain.uikb.maintain_articles&message=' . $message);
				die();
			}

			// Show table
			$this->t->set_file('maintain_articles', 'maintain_articles.tpl');
			$this->t->set_block('maintain_articles', 'table_row_block', 'table_row');
			$this->t->set_var('table_row', '');

			if ($articles_list)
			{
				foreach ($articles_list as $article)
				{
					$actions = '';

					// skip if article unpublished, user has no publish right on owner and user!=owner
					if (!$article['published'] && !($this->bo->grants[$article['user_id']] & $this->bo->publish_right) && $article['user_id']!=$GLOBALS['phpgw_info']['user']['account_id']) continue;

					$actions = "<a href='". $this->link('menuaction=phpbrain.uikb.view_article&art_id='. $article['art_id']) ."'>
								<img src='" . $GLOBALS['phpgw']->common->image('phpbrain', 'view') . "' title='". lang('view')  ."'>
								</a>";
					if (!$article['published'] && ($this->bo->grants[$article['user_id']] & $this->bo->publish_right))
					{
						$actions .= "<a href='". $this->link('menuaction=phpbrain.uikb.maintain_articles&publish='. $article['art_id']  .'&order='. $this->bo->order .'&sort='. $this->bo->sort .'&query='. $this->bo->query) ."'>
											<img src='" . $GLOBALS['phpgw']->common->image('phpbrain', 'new') . "' title='". lang('publish')  ."'>
											</a>";
					}
					if ($this->bo->grants[$article['user_id']] & $this->bo->edit_right)
					{
						$actions .= "<a href='". $this->link('menuaction=phpbrain.uikb.maintain_articles&delete='. $article['art_id']  .'&order='. $this->bo->order .'&sort='. $this->bo->sort .'&query='. $this->bo->query). "'>
											<img src='" . $GLOBALS['phpgw']->common->image('phpbrain', 'delete') . "' title='" . lang('delete') . "'>
											</a>";
					}
					$this->t->set_var(array(
						'tr_color'			=> $this->nextmatchs->alternate_row_color($tr_color),
						'title'				=> $article['title'],
						'topic'				=> $article['topic'],
						'author'			=> $article['username'],
						'date'				=> $GLOBALS['phpgw']->common->show_date($article['modified'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
						'actions'			=> $actions,
						'name_checkbox'		=> 'select[' . $article['art_id']  . ']'
					));
					$this->t->parse('table_row', 'table_row_block', True);
				}
			}
			else
			{
				$this->t->set_var('table_row', '<tr bgcolor="'. $this->nextmatchs->alternate_row_color($tr_color) .'"><td colspan="5" align="center">'. lang('There are no articles available') .'</td></tr>');
			}

			$select_publish = "<option value='all'";
			if ($this->bo->publish_filter == 'all') $select_publish .= ' selected';
			$select_publish .= ">" . lang('All') . "</option><option value='unpublished'";
			if ($this->bo->publish_filter == 'unpublished') $select_publish .= ' selected';
			$select_publish .= ">" . lang('unpublished') . "</option><option value='published'";
			if ($this->bo->publish_filter == 'published') $select_publish .= ' selected';
			$select_publish .= '>' . lang('Published') . '</option>';

			$GLOBALS['phpgw_info']['flags']['java_script_thirst'] = $this->javascript_check_all();

			$this->t->set_var(array(
				'message'				=> $this->message,
				'lang_actions'			=> lang('Actions'),
				'lang_search'			=> lang('Search'),
				'value_query'			=> $this->bo->query,
				'form_maintain_articles_action'=> $this->link('menuaction=phpbrain.uikb.maintain_articles'),
				'form_filters_action'	=> $this->link(array('menuaction' => 'phpbrain.uikb.maintain_articles', 'start' => $this->bo->start, 'sort' => $this->bo->sort)),
				'img_src_checkall'		=> $GLOBALS['phpgw']->common->image('phpbrain', 'check'),
				'order'					=> $this->bo->order, 
				'publish_filter'		=> $this->bo->publish_filter,
				'head_title'			=> $this->nextmatchs->show_sort_order($this->bo->sort, 'title', $this->bo->order, '', lang('Title')),
				'head_topic'			=> $this->nextmatchs->show_sort_order($this->bo->sort, 'topic', $this->bo->order, '', lang('Topic')),
				'head_author'			=> $this->nextmatchs->show_sort_order($this->bo->sort, 'user_id', $this->bo->order, '', lang('Author')),
				'head_date'				=> $this->nextmatchs->show_sort_order($this->bo->sort, 'created', $this->bo->order, '', lang('Date')),
				'left'					=> $this->nextmatchs->left($this->link, $this->bo->start, $this->bo->num_rows, 'menuaction.phpbrain.uikb.maintain_articles&cat='. $actual_category . '&publish_filter=' . $this->bo->publish_filter . '&query=' . $this->bo->query),
				'right'					=> $this->nextmatchs->right($this->link, $this->bo->start, $this->bo->num_rows, 'menuaction.phpbrain.uikb.maintain_articles&cat='. $actual_category .'&publish_filter=' . $this->bo->publish_filter . '&query=' . $this->bo->query),
				'num_regs'				=> $this->nextmatchs->show_hits($this->bo->num_rows, $this->bo->start),
				'select_categories'		=> $this->bo->categories_obj->formated_list('select', 'all', $actual_category, True),
				'select_publish'		=> $select_publish,
				'lang_publish_selected'	=> lang('Publish selected'),
				'lang_delete_selected'	=> lang('Delete selected')
			));

			if (!$this->sitemgr)
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();
				$this->navbar_shown = True;
			}

			$this->t->pparse('output', 'maintain_articles');
		}

		/**
		* Question maintenance view
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @return	mixed	When showing form, returns string output if acccess through sitemgr
		*/
		function maintain_questions()
		{
			$actual_category = (int)get_var('cat', 'any', 0);

			if (!$this->bo->order) $this->bo->order = 'creation';
			if (!$this->bo->sort) $this->bo->sort = 'DESC';

			$this->bo->load_categories($actual_category);

			// obtain articles to which one has any kind of permission
			if ($this->sitemgr) $this->bo->publish_filter = 'published';
			$questions_list = $this->bo->search_articles($actual_category, $this->bo->publish_filter, $this->bo->read_right | $this->bo->edit_right | $this->bo->publish_right, True);
			//echo "questions_list: <pre>";print_r($questions_list);echo "</pre>";

			// Process question deletion
			if ($_GET['delete'] || $_POST['delete_selected'])
			{
				if ($_GET['delete'])
				{
					$selected = array($_GET['delete'] => '');
				}
				else
				{
					$selected = $_POST['select'];
				}
				$errors = False;
				foreach ($selected as $q_id => $trash)
				{
					$target_q = array();
					foreach($questions_list as $question)
					{
						if ($question['question_id'] == $q_id)
						{
							$target_q = $question;
							break;
						}
					}
					$message = $this->bo->delete_question($target_q['question_id'], $target_q['user_id']);
					if ($message != 'del_q_ok') $errors = $message;
				}
				if (!$errors)
				{
					$message = $_GET['delete']? 'del_q_ok' : 'del_qs_ok';
				}
				$GLOBALS['phpgw']->redirect_link($this->link, 'menuaction=phpbrain.uikb.maintain_questions&message=' . $message);
				die();
			}

			// Process question publication
			if ($_GET['publish'] || $_POST['publish_selected'])
			{
				if ($_GET['publish'])
				{
					$selected = array($_GET['publish'] => '');
				}
				else
				{
					$selected = $_POST['select'];
				}
				$errors = False;
				foreach ($selected as $question_id => $trash)
				{
					$target_question = array();
					foreach ($questions_list as $question)
					{
						if ($question['question_id'] == $question_id)
						{
							$target_question = $question;
							break;
						}
					}
					$message = $this->bo->publish_question($target_question['question_id'], $target_question['user_id']);
					if ($message != 'publish_ok') $errors = $message;
				}
				if (!$errors)
				{
					$message = $_GET['publish']? 'publish_ok' : 'publishs_ok';
				}
				$GLOBALS['phpgw']->redirect_link($this->link, 'menuaction=phpbrain.uikb.maintain_questions&message=' . $message);
				die();
			}

			// Show table
			$this->t->set_file('maintain_questions', 'maintain_questions.tpl');
			$this->t->set_block('maintain_questions', 'table_row_block', 'table_row');
			$this->t->set_var('table_row', '');

			foreach ($questions_list as $question)
			{
				$actions = '';
				// skip if question unpublished and user has no publish right on owner
				if (!$question['published'] && !($this->bo->grants[$question['user_id']] & $this->bo->publish_right)) continue;

				// can only attempt to answer a question if it has been published
				if ($question['published'])
				{
					$actions = "<a href='". $this->link('menuaction=phpbrain.uikb.edit_article&q_id='. $question['question_id']) ."'>
							<img src='" . $GLOBALS['phpgw']->common->image('phpbrain', 'answer') . "' title='". lang('answer')  ."'>
							</a>";
				}

				// can only attempt to publish a question if it is unpblished and user has publish rights on owner
				if (!$question['published'] && ($this->bo->grants[$question['user_id']] & $this->bo->publish_right))
				{
					$actions .= "<a href='". $this->link('menuaction=phpbrain.uikb.maintain_questions&publish='. $question['question_id']  .'&order='. $this->bo->order .'&sort='. $this->bo->sort .'&query='. $this->bo->query) ."'>
										<img src='" . $GLOBALS['phpgw']->common->image('phpbrain', 'new') . "' title='". lang('publish')  ."'>
										</a>";
				}

				// can only delete question if user has edit rights on owner
				if ($this->bo->grants[$question['user_id']] & $this->bo->edit_right)
				{
					$actions .= "<a href='". $this->link('menuaction=phpbrain.uikb.maintain_questions&delete='. $question['question_id']  .'&order='. $this->bo->order .'&sort='. $this->bo->sort .'&query='. $this->bo->query). "'>
										<img src='" . $GLOBALS['phpgw']->common->image('phpbrain', 'delete') . "' title='" . lang('delete') . "'>
										</a>";
				}
				$this->t->set_var(array(
					'tr_color'			=> $this->nextmatchs->alternate_row_color($tr_color),
					'summary'			=> $question['summary'],
					'details'			=> $question['details'],
					'date'				=> $GLOBALS['phpgw']->common->show_date($question['creation'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
					'author'			=> $question['username'],
					'actions'			=> $actions,
					'name_checkbox'		=> 'select[' . $question['question_id']  . ']'
				));
				$this->t->parse('table_row', 'table_row_block', True);
			}

			if ($this->sitemgr)
			{
				$this->nextmatchs->template->set_var('action_sitemgr', $this->link('menuaction=phpbrain.uikb.maintain_questions'));
				$this->t->set_var(array(
					'lang_outstanding_q'	=> lang('Outstanding published questions'),
					'head_summary'			=> lang('Summary'),
					'head_details'			=> lang('Details'),
					'head_date'				=> lang('creation'),
					'head_author'			=> lang('Author')
				));
				if ($this->allow_questions)
				{
					$this->t->set_var('links_nav', "<a href='". $this->link('menuaction=phpbrain.uikb.index') ."'>". lang('Main View', 'phpbrain') ."</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href='". $this->link('menuaction=phpbrain.uikb.add_question') ."'>". lang('Add Question') ."</a>&nbsp;&nbsp;|<br>");
				}
				else
				{
				$this->t->set_var('links_nav', "<a href='". $this->link('menuaction=phpbrain.uikb.index') ."'>". lang('Main View', 'phpbrain') ."</a>&nbsp;&nbsp;|<br>");
				}
			}
			else
			{
				$select_publish = "<option value='all'";
				if ($this->bo->publish_filter == 'all') $select_publish .= ' selected';
				$select_publish .= ">" . lang('All') . "</option><option value='unpublished'";
				if ($this->bo->publish_filter == 'unpublished') $select_publish .= ' selected';
				$select_publish .= ">" . lang('unpublished') . "</option><option value='published'";
				if ($this->bo->publish_filter == 'published') $select_publish .= ' selected';
				$select_publish .= '>' . lang('Published') . '</option>';
				$GLOBALS['phpgw_info']['flags']['java_script_thirst'] = $this->javascript_check_all();

				$this->t->set_var(array(
					'head_summary'			=> $this->nextmatchs->show_sort_order($this->bo->sort, 'summary', $this->bo->order, '', lang('Summary')),
					'head_details'			=> $this->nextmatchs->show_sort_order($this->bo->sort, 'details', $this->bo->order, '', lang('Details')),
					'head_date'				=> $this->nextmatchs->show_sort_order($this->bo->sort, 'creation', $this->bo->order, '', lang('creation')),
					'head_author'			=> $this->nextmatchs->show_sort_order($this->bo->sort, 'user_id', $this->bo->order, '', lang('Author')),
					'select_publish'		=> $select_publish
				));
			}
			$this->t->set_var(array(
				'message'				=> $this->message,
				'lang_actions'			=> lang('Actions'),
				'lang_search'			=> lang('Search'),
				'value_query'			=> $this->bo->query,
				'form_maintain_questions_action'=> $this->link('menuaction=phpbrain.uikb.maintain_questions'),
				'form_filters_action'	=> $this->link('menuaction=phpbrain.uikb.maintain_questions&start='. $this->bo->start .'&sort='. $this->bo->sort),
				'img_src_checkall'		=> $GLOBALS['phpgw']->common->image('phpbrain', 'check'),
				'order'					=> $this->bo->order, 
				'publish_filter'		=> $this->bo->publish_filter,
				'left'					=> $this->nextmatchs->left($this->link, $this->bo->start, $this->bo->num_rows, 'menuaction.phpbrain.uikb.maintain_questions&cat='. $actual_category . '&publish_filter=' . $this->bo->publish_filter . '&query=' . $this->bo->query),
				'right'					=> $this->nextmatchs->right($this->link, $this->bo->start, $this->bo->num_rows, 'menuaction.phpbrain.uikb.maintain_questions&cat='. $actual_category .'&publish_filter=' . $this->bo->publish_filter . '&query=' . $this->bo->query),
				'num_regs'				=> $this->nextmatchs->show_hits($this->bo->num_rows, $this->bo->start),
				'select_categories'		=> $this->bo->categories_obj->formated_list('select', 'all', $actual_category, True),
				'lang_publish_selected'	=> lang('Publish selected'),
				'lang_delete_selected'	=> lang('Delete selected')
			));

			if (!$this->sitemgr)
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();
				$this->navbar_shown = True;
			}

			if ($this->sitemgr)
			{
				return $this->t->parse('out', 'maintain_questions');
			}
			else
			{
				$this->t->pparse('output', 'maintain_questions');
			}
		}

		/**
		* Auxiliary function that reloads the article view showing a confirmation message on top
		*
		* @author	Alejandro Pedraza
		* @access	private
		* @param	int		$article_id	Id of article to show
		* @param	string	$message	Message to show
		* @return	void
		*/
		function reload_page($article_id, $message)
		{
			$GLOBALS['phpgw']->redirect($this->link("menuaction=phpbrain.uikb.view_article&art_id=$article_id&message=$message"));
		}

		/**
		* Downloads file
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @return	void
		*/
		function download_file()
		{
			$article_id		= (int)get_var('art_id', 'GET');
			$filename		= urldecode(get_var('file', 'GET'));
			
			$this->bo->download_file_checks($article_id, $filename);

			// remove kb-# prefix
			ereg('^kb[0-9]*-(.*)', $filename, $new_filename);

			$download_browser = CreateObject('phpgwapi.browser');
			$download_browser->content_header($new_filename[1]);
			$cd_args = array('string'	=> '/kb', 'relative' => False, 'relatives' => RELATIVE_NONE);
			if (!$GLOBALS['phpgw']->vfs->cd($cd_args)) die('could not cd');
			echo $GLOBALS['phpgw']->vfs->read(array('string' => $filename));
			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		/**
		* Returns HTML string of categories menu
		*
		* @author	Alejandro Pedraza
		* @access	private
		* @param	int		$parent_id				id of parent category
		* @param	int		$num_main_categories	Number of main categories
		* @return	string							HTML string of categories menu
		*/
		function build_categories($parent_id, $num_main_categories)
		{
			$categories_str = '';
			$num_main_cat = 0;
			foreach ($this->bo->categories as $cat)
			{
				$data = unserialize($cat['data']);
				if ($data) $cat['icon'] = $data['icon'];
				if ($cat['parent'] != $parent_id) continue;
				$num_main_cat ++;
				$categories_str .= "<tr><td valign=top>";
				if ($cat['icon'])
						$categories_str .= "<img src=\"{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/images/cats/{$cat['icon']}\">";
				$categories_str .= "</td><td><a href='".$this->link('menuaction=phpbrain.uikb.index&cat='.$cat['id'])
										."'><b>".$cat['name']."</b></a><br><div style='padding-left:10px'>";
				$has_subcats = False;
				foreach ($this->bo->categories as $subcat)
				{
					if ($subcat['parent'] != $cat['id']) continue;
					$has_subcats = True;
					$categories_str .= "<a href='".$this->link('menuaction=phpbrain.uikb.index&cat='.$subcat['id'])
										."'>".$subcat['name']."</a>, ";
				}
				if ($has_subcats)
				{
					$categories_str = substr($categories_str, 0, strlen($categories_str)-2); // remove the last comma
				}
				$categories_str .= "</div></td></tr>\n";
				if ($num_main_cat == ceil($num_main_categories/2)) $categories_str .= "</table></td>\n<td width=50% valign=top style='padding:10px 5px 10px 10px'><table>";
			}
			if ($categories_str) $categories_str = "<tr><td width=50% valign=top style='padding:10px 5px 10px 10px'><table>" . $categories_str . "</table></td></tr>";
			return $categories_str;
		}

		/**
		* Returns HTML string of categories from the topmost to the actual one
		*
		* @author	Alejandro Pedraza
		* @access	private
		* @param	int		$category_id	Id of current category
		* @param	bool	$links			Whether to make categories clickable or not
		* @return	string					HTML string
		*/
		function category_path($category_id, $links = False)
		{
			$cat_data = $this->cat_data($category_id);
			if ($cat_data)
			{
				if (!$this->path)
				{
					if ($links)
					{
						$this->path = "<a href='" . $this->link('menuaction=phpbrain.uikb.index&cat=' . $category_id) . "'>"
										. $cat_data['name'] . "</a>";
					}
					else
					{
						$this->path = $cat_data['name'];
					}
				}
				else
				{
					if ($links)
					{
						$this->path = "<a href='" . $this->link('menuaction=phpbrain.uikb.index&cat=' . $category_id) . "'>"
										. $cat_data['name'] . " >> " . $this->path;
					}
					else
					{
						$this->path = $cat_data['name'] . ' >> ' . $this->path;
					}
				}
				return $this->category_path($cat_data['parent_id'], $links);
			}
			return $this->path;
		}

		/**
		* Auxiliary function to category_path function
		*
		* @author	Alejandro Pedraza
		* @access	private
		* @param	int		$category_id
		* @return	mixed	Array or 0
		*/
		function cat_data($category_id)
		{
			$cat_data = array();
			foreach ($this->bo->all_categories as $cat)
			{
				if ($cat['id'] == $category_id)
				{
					$cat_data['name'] 		= $cat['name'];
					$cat_data['parent_id']	= $cat['parent'];
					return $cat_data;
				}
			}
			return 0;
		}

		/**
		* Shows basic search form
		*
		* @author	Alejandro Pedraza
		* @access	private
		* @return	string	Form to place inside other templatesA
		*/
		function show_basic_search()
		{
			$this->t->set_file('basic_search', 'basic_search.tpl');
			$this->t->set_var(array(
				'lang_search_kb'		=> lang('Search in all the Knowledge Base'),
				'lang_enter_words'		=> lang('Enter one or two words describing the issue, or type the article number if you know it'),
				'lang_search'			=> lang('Search'),
				'lang_advanced_search'	=> lang('Advanced Search'),
				'class_tr'				=> 'th',
				'query_value'			=> $this->bo->query? $this->bo->query : '',
				'link_adv_search'		=> $this->link('menuaction=phpbrain.uikb.advsearch'),
				'form_search_action'	=> $this->link('menuaction=phpbrain.uikb.index')
			));
			return $this->t->parse('output', 'basic_search');

		}

		/**
		* Shows link string. Necessary because might be different depending if entered through sitemgr or not
		*
		* @author	Alejandro Pedraza
		* @access	private
		* @param	string	$args	GET arguments to be appended to link
		* @return	string			Link string
		*/
		function link($args)
		{
			if ($this->sitemgr)
			{
				return $this->link . '&' . $args;
			}
			else
			{
				return $GLOBALS['phpgw']->link($this->link, $args);
			}
		}

		/**
		* Javascript code to check all check boxes in a table
		*
		* @author	Alejandro Pedraza
		* @access	private
		* @return	string	Code
		*/
		function javascript_check_all()
		{
			$javascript = "<script>
			function check_all(which)
			{
			  for (i=0; i<document.admin_articles.elements.length; i++)
			  {
			    if (document.admin_articles.elements[i].type == 'checkbox' && document.admin_articles.elements[i].name.substring(0,which.length) == which)
			    {
			      if (document.admin_articles.elements[i].checked)
			      {
			        document.admin_articles.elements[i].checked = false;
			      }
			      else
			      {
			        document.admin_articles.elements[i].checked = true;
			      }
			    } 
			  }
			}</script>";
			return $javascript;
		}

		/**
		* To stop execution showing error message
		*
		* @author	Alejandro Pedraza
		* @access	private
		* @param	string	$error_msg	Error message to be translated and shown
		* @return	void
		*/
		function die_peacefully($error_msg)
		{
			if (!$this->navbar_shown && !$this->sitemgr)
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();
			}
			echo "<div style='text-align:center; font-weight:bold'>" . lang($error_msg) . "</div>";
			$GLOBALS['phpgw']->common->phpgw_footer();
			die();
		}
	}	
?>
