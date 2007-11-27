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

	/* $Id: class.bokb.inc.php 16713 2006-05-09 05:14:07Z skwashd $ */

	/**
	* Business logic layer of the Knowledge Base
	* 
	* Last Editor:	$Author: skwashd $
	* @author		Dave Hall & Alejandro Pedraza
	* @package		phpbrain
	* @version		$Revision: 16713 $
	* @license		GPL
	**/
	class bokb
	{
		/**
		* Data manipulation object
		*
		* @access	private
		* @var		object so
		*/
		var $so;

		/**
		* Categories object
		*
		* @access	public
		* @var		object categories
		*/
		var $categories_obj;

		/**
		* Array of all categories accesible by the current user
		*
		* @access	public
		* @var		array
		*/
		var $all_categories;

		/**
		* Variable holding categories to show
		*
		* @access	public
		* @var		array
		*/
		var $categories;

		/**
		* Array of current user's grants on other users or groups
		*
		* @access	public
		* @var		array
		*/
		var $grants;

		/**
		* Preferences for this application
		*
		* @access	public
		* @var		array
		*/
		var $preferences;

		/**
		* For pagination
		*
		* @access	public
		* @var		int
		*/
		var $start;

		/**
		* Sorting order
		*
		* @access	public
		* @var		string	ASC | DESC
		*/
		var $sort;

		/**
		* Sorting field
		*
		* @access	public
		* @var		string
		*/
		var $order;

		/**
		* Administration options for this app
		*
		* @access	public
		* @var		array
		*/
		var $admin_config;

		/**
		* Number of entries returned by a query
		*
		* @access	public
		* @var		int
		*/
		var $num_rows;

		/**
		* Number of questions returned by a query
		*
		* @access	public
		* @var		int
		*/
		var $num_questions;

		/**
		* Number of comments returned by a query
		*
		* @access	public
		* @var		int
		*/
		var $num_comments;

		/**
		* Error messages produced by methods
		*
		* @access	public
		* @var		string
		*/
		var $error_msg;

		/**
		* Filter by publication status
		*
		* @access	public
		* @var		string
		*/
		var $publish_filter;

		/**
		* Search string
		*
		* @access	public
		* @var		string
		*/
		var $query;

		/**
		* Current article owner's id
		*
		* @access	public
		* @var		int
		*/
		var $article_owner;

		/**
		* Current article id
		*
		* @access	public
		* @var		int
		*/
		var $article_id;

		/**
		* Success or error messages returned by methods
		*
		* @access	public
		* @var		array
		*/
		var $messages_array = array(
			'no_perm'				=> 'You have not the proper permissions to do that',
			'add_ok_cont'			=> 'Article added to database, you can now attach files or links, or relate to other articles',
			'comm_submited'			=> 'Comment has been submited for revision',
			'comm_ok'				=> 'Comment has been published',
			'rate_ok'				=> 'Rating has been submited',
			'comm_rate_ok'			=> 'Comment and rating have been published',
			'comm_rate_submited'	=> 'Comment has been submited for revision and rating will be published',
			'no_basedir'			=> 'Base directory does not exist, please ask adminstrator to check the global configuration',
			'no_kbdir'				=> '/kb directory does not exist and could not be created, please ask adminstrator to check the global configuration',
			'overwrite'				=> 'That file already exists',
			'no_file_serv'			=> 'The file was already missing in the server',
			'failure_delete'		=> 'Failure trying to delete the file',
			'file_del_ok'			=> 'File was deleted successfully',
			'file_db_del_err'		=> 'File could be deleted from server but not from database',
			'file_noserv_db_ok'		=> "File was already missing from server, and was deleted from the database",
			'file_noserv_db_err'	=> "File wasn't in server and it couldn't be deleted from the database",
			'del_rel_ok'			=> 'Relation with article was removed successfully',
			'link_del_err'			=> 'Error deleting link',
			'link_del_ok'			=> 'Link deleted successfully',
			'error_cd'				=> 'Error locating files directory',
			'nothing_uploaded'		=> 'Nothing was uploaded!',
			'error_cp'				=> 'Error moving file to directory',
			'upload_ok'				=> 'File has been successfully uploaded',
			'articles_added'		=> 'Articles added',
			'articles_not_added'	=> 'Problem relating articles',
			'link_ok'				=> 'Link has been added',
			'link_prob'				=> 'Link could not be added',
			'err_del_art'			=> 'Error deleting article from database',
			'err_del_q'				=> 'Error trying to delete question',
			'del_art_ok'			=> 'Article deleted successfully',
			'del_arts_ok'			=> 'Articles deleted successfully',
			'del_q_ok'				=> 'Question deleted successfully',
			'del_qs_ok'				=> 'Questions deleted successfully',
			'del_comm_err'			=> 'Error trying to delete comment',
			'del_comm_ok'			=> 'Comment has been deleted',
			'edit_err'				=> 'Error trying to edit article',
			'publish_err'			=> 'Error trying to publish article',
			'publish_comm_err'		=> 'Error publishing comment',
			'publish_ok'			=> 'Article has been published',
			'publish_comm_ok'		=> 'Comment has been pusblished',
			'publishs_ok'			=> 'Articles have been published',
			'mail_ok'				=> 'e-mail has been sent',
			'mail_err'				=> 'Error in e-mail address'
		);


		/**
		* Class constructor
		*
		* @author	Alejandro Pedraza
		* @access	public
		**/
		function bokb()
		{
			// version check
			if ($GLOBALS['phpgw_info']['apps']['phpbrain']['version'] != '0.9.17.505')
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();
				die("Please upgrade this application to be able to use it");
			}

			$this->so						= CreateObject('phpbrain.sokb');
			$this->categories_obj			= CreateObject('phpgwapi.categories', '', 'phpbrain');	// force phpbrain cause it might be running from sitemgr...
			$GLOBALS['phpgw']->config		= CreateObject('phpgwapi.config');
			$GLOBALS['phpgw']->vfs			= CreateObject('phpgwapi.vfs');
			$GLOBALS['phpgw']->historylog	= CreateObject('phpgwapi.historylog','phpbrain');

			$this->grants				= $GLOBALS['phpgw']->acl->get_grants('phpbrain');
			//echo "grants: <pre>";print_r($this->grants);echo "</pre>";
			// full grants for admin on user 0 (anonymous questions on previous phpbrain version)
			if ($GLOBALS['phpgw']->acl->check('run',1,'admin')) $this->grants[0] = -1;
			$this->preferences			= $GLOBALS['phpgw']->preferences->data['phpbrain'];
			
			$this->read_right			= PHPGW_ACL_READ;
			$this->edit_right			= PHPGW_ACL_EDIT;
			$this->publish_right		= PHPGW_ACL_CUSTOM_1;

			// acl grants puts all rights (-1) on current the user itself. That has to be modified here since the user doesn't have necessarily publish rights
			// Here I have to accumulate the rights the user has on every group it belongs to
			$grants_user = $this->read_right | $this->edit_right;	// The user can always read and edit his own articles
			$user_groups = $GLOBALS['phpgw']->accounts->membership($GLOBALS['phpgw_info']['user']['account_id']);
			foreach ($user_groups as $group)
			{
				$grants_user |= $this->grants[$group['account_id']];
				//echo "for the group: ";echo $group['account_id'];echo " the right: ";echo $this->grants[$group['account_id']];echo "<br>";
			}
			//echo "grants_user: $grants_user<br>";
			$this->grants[$GLOBALS['phpgw_info']['user']['account_id']] = $grants_user;

			$this->admin_config		= $GLOBALS['phpgw']->config->read_repository();

			if (!$this->all_categories = $this->categories_obj->return_sorted_array('', False, '', '', '', True, 0)) $this->all_categories = array();

			// default preferences and admin config
			if (!$this->preferences['num_lines']) $this->preferences['num_lines'] = 3;
			if (!$this->preferences['show_tree']) $this->preferences['show_tree'] = 'all';
			if (!$this->preferences['num_comments']) $this->preferences['num_comments'] = '5';
			if (!$this->admin_config['publish_comments']) $this->admin_config['publish_comments'] = 'True';
			if (!$this->admin_config['publish_articles']) $this->admin_config['publish_articles'] = 'True';
			if (!$this->admin_config['publish_questions']) $this->admin_config['publish_questions'] = 'True';
			
			$this->start			= get_var('start', 'any', 0);
			$this->query			= urldecode(get_var('query', 'any', ''));
			$this->sort				= get_var('sort', 'any', '');
			$this->order			= get_var('order', 'any', '');
			$this->publish_filter	= get_var('publish_filter', 'any', 'all');

			// advanced search parameters
			$this->all_words	= get_var('all_words', 'any', '');
			$this->phrase		= get_var('phrase', 'any', '');
			$this->one_word		= get_var('one_word', 'any', '');
			$this->without_words= get_var('without_words', 'any', '');
			$this->cat			= get_var('cat', 'any', 0);
			$this->include_subs	= get_var('include_subs', 'any', '');
			$this->pub_date		= get_var('pub_date', 'any', '');
			$this->ocurrences	= get_var('ocurrences', 'any', 0);
			$this->num_res		= get_var('num_res', 'any', '');
		}

		/**
		* Returns a single category
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$cat_id	Category id
		* @return	array			Category infoA
		*/
		function return_single_category($cat_id)
		{
			return $this->categories_obj->return_single($cat_id);
		}

		/**
		* Loads in object $this->categories, an array of the descendant categories of $parent_cat_id
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int	$parent_cat_id	id of the parent category
		* @return	void
		*/
		function load_categories($parent_cat_id)
		{
			if (!$this->categories = $this->categories_obj->return_sorted_array('', False, '', '', '', True, $parent_cat_id)) $this->categories = array();
		}

		/**
		* Return html code for drop-down select box with categories accessible by user
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$category_selected	id of category to show selected
		* @return	string						Html code
		*/
		function select_category($category_selected = '')
		{
			return $this->categories_obj->formated_list('select', 'all', $category_selected , True);
		}

		/**
		* Returns list of user ids to which the current user has permissions
		*
		* @author	Alejandro Pedraza
		* @access	private
		* @param	int		$permissions	Permissions bitmask. If not given, uses $this->read_right
		* @return	array					User ids
		*/
		function accessible_owners($permissions = 0)
		{
			$owners = array($GLOBALS['phpgw_info']['user']['account_id']);
			if (!$permissions) $permissions = $this->read_right;
			foreach ($this->grants as $user=>$right)
			{
				if ($right & $permissions)
				{
					$owners[] = $user;
				}
			}

			return $owners;
		}

		/**
		* Checks for rights on article
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$check_rights	bitmask ACL right (use $this->read_right or $this->edit_right)
		* @param	int		$article_owner	if not set, checks rights against current article
		* @return	bool					True if has rights, False if not
		*/
		function check_permission($check_rights, $article_owner = 0)
		{
			if (!$article_owner) $article_owner = $this->article_owner;
			if ($this->grants[$article_owner])
			{
				$rights_on_owner = $this->grants[$article_owner];
			}
			else
			{
				return False;
			}

			return ($rights_on_owner & $check_rights);
		}

		/**
		* Returns array of articles
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$category_id	Category under which articles are to be retrieved
		* @param	mixed	$publish_filter	To filter pusblished or unpublished entries
		* @param	int		$permissions	Specific permissions on article owners
		* @param	bool	$questions		Whether looking for questions or articles
		* @return	array	Articles
		**/
		function search_articles($category_id, $publish_filter = False, $permissions=0, $questions=False)
		{
			$search = $questions? 'unanswered_questions' : 'search_articles';
			if (!$permissions) $permissions = $this->read_right;

			$owners = $this->accessible_owners($permissions);
			// admins can also see questions asked by user_id=0 (questions that were passed from previous phpbrain version were the user_id wasn't recorded)
			if ($questions && $GLOBALS['phpgw']->acl->check('run',1,'admin')) $owners[0] = 0;
			
			if ($this->preferences['show_tree'] == 'all')
			{
				// show all articles under present category and descendant categories
				$cats_ids = array();
				foreach ($this->categories as $cat)
				{
					$cats_ids[] = $cat['id'];
				}
				$cats_ids[] = $category_id;

				$articles = $this->so->$search($owners, $cats_ids, $this->start, '', $this->sort, $this->order, $publish_filter, $this->query);
			}
			elseif ($category_id == 0)
			{
				// show only articles that are not categorized
				$articles = $this->so->$search($owners, 0, $this->start, '', $this->sort, $this->order, $publish_filter, $this->query);
			}
			else
			{
				// show only articles in present category
				$articles = $this->so->$search($owners, array($category_id), $this->start, '', $this->sort, $this->order, $publish_filter, $this->query);
			}

			$this->num_rows = $this->so->num_rows;
			return $articles;
		}

		/**
		* Returns results of advanced search
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @return	array	Articles
		*/
		function adv_search_articles()
		{
			$owners = $this->accessible_owners();

			$cats_ids = array();
			if ($this->cat && !$this->include_subs)
			{
				// only search in one category
				$cats_ids[] = $this->cat;
			}
			elseif ($this->cat)
			{
				// search in category passed and all its descendency
				foreach ($this->categories as $cat)
				{
					$cats_ids[] = $cat['id'];
				}
				$cats_ids[] = $this->cat;
			}

			$articles = $this->so->adv_search_articles($owners, $cats_ids, $this->ocurrences, $this->pub_date, $this->start, $this->num_res, $this->all_words, $this->phrase, $this->one_word, $this->without_words, $this->cat, $this->include_subs);
			$this->num_rows = $this->so->num_rows;
			return $articles;
		}

		/**
		* Returns unanswered questions
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$category_id	Category in which to look for
		* @return	array					Questions
		*/
		function unanswered_questions($category_id)
		{
			$owners = $this->accessible_owners();

			// admins can also see questions asked by user_id=0 (questions that were passed from previous phpbrain version were the user_id wasn't recorded)
			if ($GLOBALS['phpgw']->acl->check('run',1,'admin')) $owners[0] = 0;
			
			$cats_ids = array();
			foreach ($this->categories as $cat)
			{
				$cats_ids[] = $cat['id'];
			}
			$cats_ids[] = $category_id;

			$questions = $this->so->unanswered_questions($owners, $cats_ids, 0, $this->preferences['num_lines'], 'DESC', 'creation', 'published', '');
			$this->num_questions = $this->so->num_questions;
			return $questions;
		}

		/**
		* Returns article's history
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @return	array					Articles's history
		*/
		function return_history()
		{
			$history = $GLOBALS['phpgw']->historylog->return_array('', '', 'history_timestamp', 'DESC', $this->article_id);
			// echo "history: <pre>";print_r($history);echo "</pre>";
			for ($i = 0; $i<sizeof($history); $i++)
			{
				$history[$i]['datetime'] = $GLOBALS['phpgw']->common->show_date($history[$i]['datetime'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				$GLOBALS['phpgw']->accounts->get_account_name($history[$i]['owner'], $lid, $fname, $lname);
				$history[$i]['owner'] = $fname . ' ' . $lname;

				switch ($history[$i]['status'])
				{
					case 'AF':
						$history[$i]['action'] = lang ('Added file %1', $history[$i]['new_value']);
						break;
					case 'RF':
						$history[$i]['action'] = lang ('Removed file %1', $history[$i]['new_value']);
						break;
					case 'AL':
						$history[$i]['action'] = lang ('Added link %1', $history[$i]['new_value']);
						break;
					case 'RL':
						$history[$i]['action'] = lang ('Removed link %1', $history[$i]['new_value']);
						break;
					case 'AR':
						$history[$i]['action'] = lang ('Added related articles %1', $history[$i]['new_value']);
						break;
					case 'DR':
						$history[$i]['action'] = lang ('Deleted relation to  article %1', $history[$i]['new_value']);
						break;
					case 'EA':
						$history[$i]['action'] = lang ('Article edited');
						break;
					case 'NA':
						$history[$i]['action'] = lang ('Article created');
						break;
					case 'AD':
						$history[$i]['action'] = lang ('Article deleted');
						break;
				}
			}
			return $history;
		}

		/**
		* Returns latest or most viewed articles
		*
		* @author	Alejandro Pedraza
		* @param	int		$category_id	articles must belong to the descendancy of this category
		* @param	string	$order			Field by which the query is ordered, determines whether latest or most viewed articles are returned
		* @return	array					array of articles
		*/
		function return_latest_mostviewed($category_id = 0, $order = '')
		{
			$owners = $this->accessible_owners();

			$cats_ids = array($category_id);
			foreach ($this->categories as $cat)
			{
				$cats_ids[] = $cat['id'];
			}

			return $this->so->search_articles($owners, $cats_ids, 0, $this->preferences['num_lines'], 'DESC', $order, 'published', '');
		}

		/**
		* Saves a new or edited article
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	array	$content	Article contents. Extracted from $_POST if empty
		* @return	bool				True on success, False on failure
		**/
		function save_article($content = '')
		{
			if (!$content) {
				$content = array();
				$content['exec'] = get_var('exec', 'POST');
				$content['editing_article_id'] = (int)get_var('editing_article_id', 'POST', 0);
				$content['articleID'] = get_var('articleID', 'POST');
				$content['answering_question'] = (int)get_var('answering_question', 'POST', 0);
				$content['title'] = get_var('title', 'POST');
				$content['topic'] = get_var('topic', 'POST');
				$content['cat_id'] = (int)get_var('cat_id', 'POST', 0);
				$content['keywords'] = get_var('keywords', 'POST');
			}

			$content['text'] = $content['exec']['text'];

			// if editing an article, check it has the right to do so
			if ($content['editing_article_id'] && !($this->check_permission($this->edit_right)))
			{
				$this->error_msg = lang('You have not the proper permissions to do that');
				return False;
			}
			elseif ($content['editing_article_id'])
			{
				if(!$art_id = $this->so->save_article($content, False))
				{
					$this->error_msg = 'edit_err';
					return False;
				}
				$GLOBALS['phpgw']->historylog->add('EA', $this->article_id, 'article edited', '');
				return $art_id;
			}

			// if given, articleID must be a number
			if ($content['articleID']!='' && !is_numeric($content['articleID']))
			{
				$this->error_msg = lang('The article ID must be a number');
				return False;
			}

			// if adding a new article, check that the  articleID doesn't already exist if it was given
			if ($content['articleID'] && $this->so->exist_articleID($content['articleID']))
			{
				$this->error_msg = "Article ID already exists";
				return False;
			}

			$publish = False;
			if ($this->admin_config['publish_articles'] == 'True') $publish = True;

			$art_id = $this->so->save_article($content, True, $publish);
			if ($art_id) $GLOBALS['phpgw']->historylog->add('NA', $art_id, 'article created', '');
			return $art_id;
		}

		/**
		* Deletes article
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	array	Article's files
		* @param	int		$art_id		Article id. If not given, $this->article_id is used
		* @param	int		$owner		Article's owner id. -1 to avoid checking permissions (when admin is deleting all user's articles)
		* @return	string				Success or failure message
		*/
		function delete_article($files, $art_id = 0, $owner = 0)
		{
			if (!$art_id) $art_id = $this->article_id;
			// check user has edit rights
			if ($owner != -1 && !$this->check_permission($this->edit_right, $owner)) return 'no_perm';
			// delete files
			if ($files)
			{
				foreach ($files as $file)
				{
					// verify the file exists in the server
					$test = $GLOBALS['phpgw']->vfs->ls(array(
						'string'		=> '/kb/' . $file['file'],
						'relatives'		=> array(RELATIVE_NONE),
						'checksubdirs'	=> False,
						'nofiles'		=> False
					));
					if ($test[0]['name'])
					{
						// the file is in the server, proceed to rm it
						$remove = $GLOBALS['phpgw']->vfs->rm(array(
							'string'	=> '/kb/' . $file['file'],
							'relatives'	=> array(RELATIVE_NONE)
						));
					}
				}
			}
			// delete comments
			$this->so->delete_comments($art_id);
			// delete ratings
			$this->so->delete_ratings($art_id);
			// delete related articles
			$this->so->delete_related($art_id, $art_id, True);
			// delete search index
			$this->so->delete_search($art_id);
			// delete files entries in phpgw_kb_files
			$this->so->delete_files($art_id);
			// delete urls
			$this->so->delete_urls($art_id);
			// delete article
			if (!$this->so->delete_article($art_id)) return 'err_del_art';
			if ($art_id) $GLOBALS['phpgw']->historylog->add('AD', $art_id, 'article deleted', '');
			return 'del_art_ok';
		}

		/**
		* Deletes owners articles (called by hook_deleteaccount)
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$owner		Article's owner id
		* @return	void
		*/
		function delete_owner_articles($owner)
		{
			// check if user calling deletion is an admin with user deletion privileges
			if ($GLOBALS['phpgw']->acl->check('account_access',32,'admin'))
			{
				$this->list_users();
				die('invalid rights');
			}

			// fetch articles from user
			$GLOBALS['phpgw']->vfs->override_acl = 1;
			$owner = (int)$owner;
			$articles_ids = $this->so->get_articles_ids($owner);
			foreach ($articles_ids as $article_id)
			{
				$article = $this->so->get_article($article_id);
				$this->delete_article($article['files'], $article_id, -1);
			}
		}

		/**
		* changes articles owner (called by hook_deleteaccount)
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$owner		Article's owner id
		* @param	int		$owner		Article's new owner id
		* @return	void
		*/
		function change_articles_owner($owner, $new_owner)
		{
			// check if user calling deletion is an admin with user deletion privileges
			if ($GLOBALS['phpgw']->acl->check('account_access',32,'admin'))
			{
				$this->list_users();
				die('invalid rights');
			}

			$owner = (int)$owner;
			$new_owner = (int)$new_owner;

			// first change file owners in vfs
			$articles_ids = $this->so->get_articles_ids($owner);
			foreach ($articles_ids as $article_id)
			{
				$article = $this->so->get_article($article_id);
				$GLOBALS['phpgw']->vfs->override_acl = 1;
				if (is_array($article['files']))
				{
					foreach ($article['files'] as $file)
					{
						$GLOBALS['phpgw']->vfs->set_attributes(array(
							'string'		=> '/kb/' . $file['file'],
							'relatives'		=> array(RELATIVE_NONE),
							'attributes'	=> array('owner_id' => $new_owner),
						));
					}
				}
			}

			// now change articles owners
			$this->so->change_articles_owner($owner, $new_owner);
		}

		/**
		* Deletes question
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$q_id		Question id
		* @param	int		$owner		Article's owner id
		* @return	string				Success or failure message
		**/
		function delete_question($q_id, $owner)
		{
			// check user has edit rights on owner
			$this->article_owner = $owner;
			if (!$this->check_permission($this->edit_right, $owner)) return 'no_perm';

			if (!$this->so->delete_question($q_id)) return 'err_del_q';
			return 'del_q_ok';
		}

		/**
		* Returns article
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$art_id		article id
		* @return	array	Article
		**/
		function get_article($art_id)
		{
			if (!$article = $this->so->get_article($art_id)) return False;
			$this->article_id = $article['art_id'];

			// check permissions
			$this->article_owner = $article['user_id'];
			if (!$this->check_permission($this->read_right | $this->publish_right)) $this->die_peacefully('You have not the proper permissions to do that');

			$GLOBALS['phpgw']->accounts->get_account_name($article['user_id'], $lid, $fname, $lname);
			$article['username'] = $fname . ' ' . $lname;
			$fname = ''; $lname = '';
			$GLOBALS['phpgw']->accounts->get_account_name($article['modified_user_id'], $lid, $fname, $lname);
			$article['modified_username'] = $fname . ' ' .$lname;

			// register article view if it has been published (one hit per session)
			if (!$data = $GLOBALS['phpgw']->session->appsession('views', 'phpbrain')) $data = array();
			if ($article['published'] && !in_array($this->article_id, $data))
			{
				$data[] = $this->article_id;
				$GLOBALS['phpgw']->session->appsession('views', 'phpbrain', $data);
				$this->so->register_view($this->article_id, $article['views']);
			}

			// process search_feedback (can do this only once per session per article)
			if (!$data = $GLOBALS['phpgw']->session->appsession('feedback', 'phpbrain')) $data = array();
			if ($_POST['feedback_query'] && !in_array($this->article_id, $data))
			{
				$data[] = $this->article_id;
				$GLOBALS['phpgw']->session->appsession('feedback', 'phpbrain', $data);
				$upgrade_key = $_POST['yes_easy']? True : False;
				$words = explode(' ', $_POST['feedback_query']);
				foreach ($words as $word)
				{
					$this->so->update_keywords($this->article_id, $word, $upgrade_key);
				}
			}

			return $article;
		}

		/**
		* Previous checks before downloading a file
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$art_id		Article id
		* @param	string	$filename	filename
		* @return	void
		*/
		function download_file_checks($art_id, $filename)
		{
			if (!$article = $this->get_article($art_id)) $this->die_peacefully('Error downloading file');
			if (!$this->check_permission($this->read_right)) $this->die_peacefully('You have not the proper permissions to do that');
			$found_file = False;
			foreach ($article['files'] as $article_file)
			{
				if ($article_file['file'] == $filename) $found_file = True;
			}
			if (!$found_file) $this->die_peacefully("Error: file doesn't exist in the database");
		}

		/**
		* Returns article's comments
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$art_id		article id
		* @param	int		$limit		Number of comments to return
		* @return	array				Comments
		*/
		function get_comments($art_id, $limit = False)
		{
			if ($limit) $limit = $this->preferences['num_comments'];
			$comments = $this->so->get_comments($art_id, $limit);
			$this->num_comments = $this->so->num_comments;
			return $comments;
		}

		/**
		* Returns an article related comments
		*
		* @author	Alejandro Pedraza
		* @acces	public
		* @param	int		$art_id	Article id
		* @return	array			IDs and titles of articles
		*/
		function get_related_articles($art_id)
		{
			$owners = $this->accessible_owners();
			return $this->so->get_related_articles($art_id, $owners);
		}

		/**
		* Tells if the current user has already rated the article
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @returns	bool				1 if he has, 0 if not
		**/
		function user_has_voted()
		{
			return $this->so->user_has_voted($this->article_id);
		}

		/**
		* Registers user's vote. When accessing through egroupware users can only vote once. When accessign through sitemgr, they can vote as many times they wish, but only once per session on an individual article
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$current_rating	Current number of votes in the level $rating (this saves me a trip to the db)
		* @param	bool	$sitemgr		Whether user is accessing through sitemgr
		* @return	bool					1 on success, 0 on failure
		*/
		function add_rating($current_rating, $sitemgr=False)
		{
			if(!$this->so->add_vote($this->article_id, $_POST['Rate'], $current_rating)) return 0;
			if (!$sitemgr)
			{
				if (!$this->so->add_rating_user($this->article_id)) return 0;
			}
			// register vote in session
			if (!$data = $GLOBALS['phpgw']->session->appsession('ratings', 'phpbrain')) $data = array();
			$data[] = $this->article_id;
			$GLOBALS['phpgw']->session->appsession('ratings', 'phpbrain', $data);

			return 1;
		}

		/**
		* Stores new comment
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @return	mixed	Success message or 0 if failure
		*/
		function add_comment()
		{
			$comment = get_var('comment_box', 'POST');
			if ($this->admin_config['publish_comments'] == 'True')
			{
				$publish = True;
				$message = 'comm_ok';
			}
			else
			{
				$publish = False;
				$message = 'comm_submited';
			}
			if (!$this->so->add_comment($comment, $this->article_id, $publish)) return 0;
			return $message;
		}

		/**
		* Adds link to article
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @return	string	Success or failure message
		*/
		function add_link()
		{
			// first check permission
			if (!$this->check_permission($this->edit_right)) return 'no_perm';

			if(!$this->so->add_link(get_var('url', 'POST'), get_var('url_title', 'POST'), $this->article_id)) return 'link_prob';

			$GLOBALS['phpgw']->historylog->add('AL', $this->article_id, get_var('url', 'POST'), '');
			return 'link_ok';
		}

		/**
		* Publishes article
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @params	int		$art_id	Article ID. If not given uses current article
		* @params	int		$owner	Article's owner ID. If not given uses owner of current article
		* @return	string			Success or error message
		**/
		function publish_article($art_id=0, $owner=0)
		{
			if (!$art_id) $art_id = $this->article_id;

			// first check permission
			if (!$this->check_permission($this->publish_right, $owner)) return 'no_perm';

			if (!$this->so->publish_article($art_id)) return 'publish_err';
			return 'publish_ok';
		}

		/**
		* Publishes question
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @params	int		$q_id	Question ID.
		* @params	int		$owner	Article's owner ID
		* @return	string			Success or error message
		**/
		function publish_question($q_id, $owner)
		{
			// first check permission
			if (!$this->check_permission($this->publish_right, $owner)) return 'no_perm';

			if (!$this->so->publish_question($q_id)) return 'publish_err';
			return 'publish_ok';
		}

		/**
		* Publishes article comment
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @return	string	Success or error message
		*/
		function publish_comment()
		{
			$comment_id = (int)$_GET['pub_com'];
			// first check permission
			if (!$this->check_permission($this->edit_right)) return 'no_perm';

			if (!$this->so->publish_comment($this->article_id, $comment_id)) return 'publish_comm_err';
			return 'publish_comm_ok';
		}

		/**
		* Deletes article comment
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @return	string	Success or error message
		*/
		function delete_comment()
		{
			$comment_id = (int)$_GET['del_comm'];

			// check permission
			if (!$this->check_permission($this->edit_right)) return 'no_perm';

			if (!$this->so->delete_comment($this->article_id, $comment_id)) return 'del_comm_err';
			return 'del_comm_ok';
		}

		/**
		* Deletes article comment
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @return	string	Success or error message
		*/
		function delete_link()
		{
			$link = urldecode($_GET['delete_link']);
			// first check permission
			if (!$this->check_permission($this->edit_right)) return 'no_perm';

			if (!$this->so->delete_link($this->article_id, $link)) return 'link_del_err';

			$GLOBALS['phpgw']->historylog->add('RL', $this->article_id, $delete_link, '');
			return 'link_del_ok';
		}

		/**
		* @function process_upload 
		*
		* @abstract	Uploads file to system
		* @author	Alejandro Pedraza
		* @return	string: error or confirmation message
		*/
		function process_upload()
		{
			// check permissions
			if (!$this->check_permission($this->edit_right)) return 'no_perm';
			// check something was indeed uploaded
			if ($_FILES['new_file']['error'] == 4) return 'nothing_uploaded';

			// TODO: check filename for invalid characters
		
			// check if basedir exists
			$test=$GLOBALS['phpgw']->vfs->get_real_info(array('string' => '/', 'relatives' => array(RELATIVE_NONE), 'relative' => False));
			if($test[mime_type]!='Directory')
			{
				return 'no_basedir';
			}

			// check if /kb  exists
			$test = @$GLOBALS['phpgw']->vfs->get_real_info(array('string' => '/kb', 'relatives' => array(RELATIVE_NONE), 'relative' => False));
			if($test[mime_type]!='Directory')
			{
				// if not, create it
				$GLOBALS['phpgw']->vfs->override_acl = 1;
				$GLOBALS['phpgw']->vfs->mkdir(array(
					'string' => '/kb',
					'relatives' => array(RELATIVE_NONE)
				));
				$GLOBALS['phpgw']->vfs->override_acl = 0;

				// test one more time
				$test = $GLOBALS['phpgw']->vfs->get_real_info(array('string' => '/kb', 'relatives' => array(RELATIVE_NONE), 'relative' => False));
				if($test[mime_type]!='Directory')
				{
					return 'no_kbdir';
				}
			}
			// prefix with article number
			$filename = stripslashes('kb' . $this->article_id . '-' . $_FILES['new_file']['name']);	// strip slashes eventually generated if magic_quotes_gpc is set on
			
			// check the file doesn't already exist (happens when double POSTing)
			$test = $GLOBALS['phpgw']->vfs->ls(array(
				'string'		=> '/kb/' . $filename,
				'relatives'		=> array(RELATIVE_NONE),
				'checksubdirs'	=> False,
				'nofiles'		=> False
			));
			if ($test[0]['name']) return 'overwrite';

			// at last, copy the file from /tmp to /kb
			$cd_args = array('string'	=> '/kb', 'relative' => False, 'relatives' => RELATIVE_NONE);
			if (!$GLOBALS['phpgw']->vfs->cd($cd_args)) return 'error_cd';

			$cp_args = array(
						'from'		=> $_FILES['new_file']['tmp_name'],
						'to'		=> $filename,
						'relatives'	=> array(RELATIVE_NONE|VFS_REAL, RELATIVE_ALL)
					);
			$GLOBALS['phpgw']->vfs->override_acl = 1; // should I implement ACL on this folder? Don't think so :>
			if (!$GLOBALS['phpgw']->vfs->cp($cp_args)) return 'error_cp';
			$GLOBALS['phpgw']->vfs->override_acl = 0;

			$this->so->add_file($this->article_id, $filename);

			$GLOBALS['phpgw']->historylog->add('AF', $this->article_id, $_FILES['new_file']['name'], '');
			return 'upload_ok';
		}

		/**
		* Deletes file
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	string	$current_files	Forgot why this
		* @param	string	$file			File name
		* @return	string					Success or error message
		* @todo		see what's the deal with $current_files
		*/
		function delete_file($current_files, $file = '')
		{
			if (!$file) $file = urldecode($_GET['delete_file']);

			// check permissions
			if (!$this->check_permission($this->edit_right)) return 'no_perm';

			// verify the file exists in the server
			$test = $GLOBALS['phpgw']->vfs->ls(array(
				'string'		=> '/kb/' . $file,
				'relatives'		=> array(RELATIVE_NONE),
				'checksubdirs'	=> False,
				'nofiles'		=> False
			));
			if ($test[0]['name'])
			{
				// the file is in the server, proceed to rm it
				$remove = $GLOBALS['phpgw']->vfs->rm(array(
					'string'	=> '/kb/' . $file,
					'relatives'	=> array(RELATIVE_NONE)
				));
				if (!$remove) return 'failure_delete';
				$in_server = True;

			}
			else
			{
				// the file was already missing
				$in_server = False;
			}

			// now delete it from the database
			ereg('^kb[0-9]*-(.*)', $file, $new_filename);
			if ($success = $this->so->delete_files($this->article_id, $file))
				$GLOBALS['phpgw']->historylog->add('RF', $this->article_id, $new_filename[1], '');
			if ($in_server && $success) return 'file_del_ok';
			if ($in_server && !$success) return 'file_db_del_err';
			if (!$in_server && $success) return 'file_noserv_db_ok';
			return 'file_noserv_db_err';
		}

		/**
		* Adds related article to article
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @return	string	Success or error message
		*/
		function add_related()
		{
			$parsed_list = array();
			$final_list = array();

			// validate list
			$list = explode(', ', $_POST['related_articles']);
			for ($i=0; $i<sizeof($list); $i++)
			{
				if ((int)$list[$i])
				{
					$parsed_list[] = (int)$list[$i];
				}
			}

			// check permissions on those articles
			$owners_list = $this->so->owners_list($parsed_list);
			foreach ($owners_list as $owner)
			{
				if ($this->check_permission($this->edit_right, $owner['user_id'])) $final_list[] = $owner['art_id'];
			}

			// update database
			if (!$this->so->add_related($this->article_id, $final_list)) return 'articles_not_added';

			$final_list = implode(', ', $final_list);
			$GLOBALS['phpgw']->historylog->add('AR', $this->article_id, $final_list, '');
			return 'articles_added';
		}

		/**
		* Deletes related article to article
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @return	void
		*/
		function delete_related()
		{
			$related_article = urldecode($_GET['delete_related']);
			$this->so->delete_related($this->article_id, $related_article);
			$GLOBALS['phpgw']->historylog->add('DR', $this->article_id, $related_article, '');
		}

		/**
		* Adds question to database
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @return	int				Numbers of lines affected (should be 1, if not there's an error)
		*/
		function add_question()
		{
			$data = array();
			$data['summary'] = get_var('summary', 'POST');
			$data['details'] = get_var('details', 'POST');
			$data['cat_id'] = (int)get_var('cat_id', 'POST', 0);
			$publish = ($this->admin_config['publish_questions'] == 'True')? True : False;
			return $this->so->add_question($data, $publish);
		}

		/**
		* Returns question
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$q_id	Question id
		* @return	array			Question
		*/
		function get_question($q_id)
		{
			$question = $this->so->get_question($q_id);
			$username = $GLOBALS['phpgw']->accounts->get_account_name($question['user_id'], $lid, $fname, $lname);
			$question['username'] = $fname . ' ' . $lname;
			$question['creation'] = $GLOBALS['phpgw']->common->show_date($question['creation'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			return $question;
		}

		/**
		* Mails article
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	array	$article_contents
		* @return	string	Success or error message
		*/
		function mail_article($article_contents)
		{
			// check address syntaxis
			$recipient = get_var('recipient', 'POST');
			$reply_to = get_var('reply', 'POST', 0);
			$theresults = ereg("^[^@ ]+@[^@ ]+\.[^@ \.]+$", $recipient, $trashed);
			if (!$theresults) return 'mail_err';

			$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
			$GLOBALS['phpgw']->send->From = $GLOBALS['phpgw_info']['user']['email'];
			$GLOBALS['phpgw']->send->FromName = $GLOBALS['phpgw_info']['user']['fullname'];
			if ($reply_to) $GLOBALS['phpgw']->send->AddReplyTo($reply_to);
			$GLOBALS['phpgw']->send->AddAddress($recipient);
			$GLOBALS['phpgw']->send->Subject = get_var('subject', 'POST');
			$GLOBALS['phpgw']->send->Body = get_var('txt_message', 'POST', lang('E-GroupWare Knowledge Base article attached'));
			$GLOBALS['phpgw']->send->AddStringAttachment($article_contents, lang('article').'.html', 'base64', 'text/html');
			$message = '';
			if (!$GLOBALS['phpgw']->send->Send())
			{
				 $message = lang('Your message could not be sent!') . '<br>' . lang('The mail server returned:') . htmlspecialchars($GLOBALS['phpgw']->send->ErrorInfo);
			}
			if ($message) return $message;
			return 'mail_ok';
		}

		/**
		* Stop execution and show error message
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	string	$error_msg	Error message to translate and show
		* @return	void
		*/
		function die_peacefully($error_msg)
		{
			if (!$this->navbar_shown)
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
