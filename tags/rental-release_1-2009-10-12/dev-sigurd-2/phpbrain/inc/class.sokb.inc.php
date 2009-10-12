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
	* Storage logic layer of the Knowledge Base
	* 
	* Last Editor:	$Author: skwashd $
	* @author		Dave Hall & Alejandro Pedraza
	* @package		phpbrain
	* @version		$Revision: 16713 $
	* @license		GPL
	**/
	class sokb
	{
		/**
		* Database object
		*
		* @access	private
		* @var		object db
		*/
		var $db;

		/**
		* Number of rows in result set
		*
		* @access	public
		* @var		int
		*/
		var $num_rows;

		/**
		* Number of unanswered questions in result set
		*
		* @access	public
		* @var		int
		*/
		var $num_questions;

		/**
		* Number of comments in result set
		*
		* @access	public
		* @var		int
		*/
		var $num_comments;

		/**
		* Type of LIKE SQL operator to use
		*
		* @access	private
		* @var		string
		*/
		var $like;

		/**
		* Class constructor
		*
		* @author	Alejandro Pedraza
		* @access	public
		**/
		function sokb()
		{
			$this->db	= clone($GLOBALS['phpgw']->db);

            // postgresql is case sensite by default, so make it case insensitive
            if ($this->db->Type == 'pgsql')
            {
                $this->like = 'ILIKE';
            }
            else
            {
                $this->like = 'LIKE';
            }
		}

		/**
		* Returns array of articles
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	array	$owners			Users ids accessible by current user
		* @param	array	$categories		Categories ids
		* @param	int		$start			For pagination
		* @param	int		$upper_limit	For pagination
		* @param	srting	$sort			Sorting direction: ASC | DESC
		* @param	string	$order			Sorting field name
		* @param	mixed	$publish_filter	To filter pusblished or unpublished entries
		* @param	string	$query			Search string
		* @return	array					Articles
		*/
		function search_articles($owners, $categories, $start, $upper_limit = '', $sort, $order, $publish_filter = False, $query)
		{
			$order = $this->db->db_addslashes($order);
			if ($sort != 'DESC') $sort = 'ASC';

			// We use COALESCE (VALUE in case of maxdb) to turn NULLs into zeros, to avoid some databases (postgres and maxdb, don't know about mssql)
			// to sort records with score NULL before records with a score > 0.
			$score = (($this->db->Type == 'maxdb')? 'VALUE(SUM(phpgw_kb_search.score), 0)' : 'SUM(COALESCE(phpgw_kb_search.score))') . ' AS pertinence';
            // have to figure out later if maxdb is broken here...
			$files_field = (($this->db->Type == 'maxdb')? 'VALUE(art_file)' : 'COUNT(COALESCE(art_file))') . ' AS files';

			$fields = array('phpgw_kb_articles.art_id', 'title', 'topic', 'views', 'cat_id', 'published', 'user_id', 'created', 'modified', 'votes_1', 'votes_2', 'votes_3', 'votes_4', 'votes_5', $score, $files_field);
			$fields_str = implode(', ', $fields);
			$owners = implode(', ', $owners);

			$sql = "SELECT $fields_str FROM phpgw_kb_articles LEFT JOIN phpgw_kb_search ON phpgw_kb_articles.art_id=phpgw_kb_search.art_id ";
            $sql .= "LEFT JOIN phpgw_kb_files ON phpgw_kb_articles.art_id=phpgw_kb_files.art_id ";
			$sql .= "WHERE user_id IN ($owners)";
			if ($publish_filter && $publish_filter!='all') 
			{
				($publish_filter == 'published')? $publish_filter = 1 : $publish_filter = 0;
				$sql .= " AND published=$publish_filter";
			}
			if (!$categories)
			{
				$sql .= " AND cat_id = 0";
			}
			else
			{
				$categories = implode(",", $categories);
				$sql .= " AND cat_id IN(" . $categories . ")";
			}

			if ($query)
			{
				$words_init = explode(' ', $query);
				$words = array();
				foreach ($words_init as $word_init)
				{
					$words[] = $this->db->db_addslashes($word_init);
				}
				$likes = array();
				foreach ($words as $word)
				{
					if ((int)$word) 
					{
						$likes[] = "phpgw_kb_articles.art_id='$word'";
						break;
					}
					$likes[] = "title {$this->like} '%$word%' OR topic {$this->like} '%$word%' OR text {$this->like} '%$word%'";
				}
				$likes = implode(' OR ', $likes);
			
				if ($likes)
				{
					// build query for results matching keywords (these are the most relevant results, and so appear first)
					$sql_keywords = $sql . " AND (keyword='" . implode("' OR keyword='", $words) . "')";

					// build query for the rest of results (looking in title, topic and text only). These appear after the previous ones are shown.
					// I must use the negation of the previous conditions to avoid shown repeated records
					$sql_rest = $sql . " AND (keyword!='" . implode("' AND keyword!='", $words) . "' AND $likes)";
				}
			}

			// Group by on all fields to return unique records and calculate pertinence scores
			$groupby = " GROUP BY phpgw_kb_articles.art_id, title, topic, views, cat_id, published, user_id, created, modified, votes_1, votes_2, votes_3, votes_4, votes_5";
			$order_sql = array();
			if ($order)
			{
				$order_sql[] = "$order $sort";
			}
			if ($query)
			{
				$order_sql[] = "pertinence DESC";
			}
			if (!$order && !$query)
			{
				$order_sql[] = "modified DESC";
			}
			$order_sql = ' ORDER BY ' . implode(',', $order_sql);	// only PHP lets me write crap like this

			if ($query)
			{
				$sqls[0] = $sql_keywords.$groupby.$order_sql;
				$sqls[1] = $sql_rest.$groupby.$order_sql;
			}
			else
			{
				$sqls[0] = $sql.$groupby.$order_sql;
			}

			//echo "sqls: "._debug_array($sqls);
			$articles = array();
			$this->num_rows = 0;
			foreach ($sqls as $sql)
			{
				$this->db->query($sql, __LINE__, __FILE__);
				$this->num_rows += $this->db->num_rows();
				$this->db->limit_query($sql, $start, __LINE__, __FILE__, $upper_limit);
				$start = $this->db->num_rows();
				$articles = array_merge($articles, $this->results_to_array($fields));
			}

			return $articles;
		}

		/**
		* Returns results of advanced search
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	array	$owners			User ids accessible by current user
		* @param	array	$cats_ids		Categories filtering
		* @param	string	$ocurrences		Field name where to search
		* @param	string	$pub_date		Articles from last 3 or 6 months, or last year
		* @param	int		$start			For pagination
		* @param	int		$num_res		For pagination
		* @param	string	$all_words		'with all the words' filtering
		* @param	string	$phrase			'exact phrase' filtering
		* @param	string	$one_word		'with at least one of the words' filtering
		* @param	string	$without_words	'without the words' filtering
		* @param	int		$cat			Don't know
		* @param	bool	$include_subs	Include subcategories when filtering by categories. Seems to not being working
		* @return	array					Articles
		* @todo		use params $cat and $include_subs
		*/
		function adv_search_articles($owners, $cats_ids, $ocurrences, $pub_date, $start, $num_res, $all_words, $phrase, $one_word, $without_words, $cat, $include_subs)
		{
			$fields= array('phpgw_kb_articles.art_id', 'title', 'topic', 'views', 'cat_id', 'published', 'user_id', 'created', 'modified', 'votes_1', 'votes_2',  'votes_3', 'votes_4', 'votes_5');
			$fields_str	= implode(' , ', $fields);

			// permissions filtering
			$owners	= implode(', ', $owners);
			$sql = "SELECT DISTINCT $fields_str FROM phpgw_kb_articles LEFT JOIN phpgw_kb_search ON phpgw_kb_articles.art_id=phpgw_kb_search.art_id WHERE user_id IN ($owners)";

			// categories filtering
			$cats_ids	= implode (',', $cats_ids);
			if ($cats_ids) $sql .= " AND cat_id IN ($cats_ids)";

			// date filtering
			switch ($pub_date)
			{
				case '3':
					$sql .= " AND created>" . mktime(0, 0, 0, date('n')-3);
					break;
				case '6':
					$sql .= " AND created>" . mktime(0, 0, 0, date('n')-6);
					break;
				case 'year':
					$sql .= " AND created>" . mktime(0, 0, 0, date('n')-12);
					break;
			}

			// ocurrences filtering
			switch ($ocurrences)
			{
				case 'title':
					$target_fields = array('title');
					break;
				case 'topic':
					$target_fields = array('topic');
					break;
				case 'text':
					$target_fields = array('text');
					break;
				default:
					$target_fields = array('title', 'topic', 'keyword', 'text');
					break;
			}

			// "with all the words" filtering
			$all_words = $this->db->db_addslashes($all_words);
			$all_words = strlen($all_words)? explode(' ', $all_words) : False;
			$each_field = array();
			if ($all_words)
			{
				foreach ($all_words as $word)
				{
					$each_field[] = "(" . implode(" {$this->like} '%$word%' OR ", $target_fields) . " {$this->like} '%$word%')";
				}
				if ($each_field)
				{
					$sql .= " AND " . implode(" AND ", $each_field);
				}
			}

			// "with the exact phrase" filtering
			$phrase = $this->db->db_addslashes($phrase);
			if ($phrase)
			{
				$sql .= " AND (" . implode (" {$this->like} '%$phrase%' OR ", $target_fields) . " {$this->like} '%$phrase%')";
			}

			// "With at least one of the words" filtering
			$one_word = $this->db->db_addslashes($one_word);
			$one_word = strlen($one_word)? explode(' ', $one_word) : False;
			if ($one_word)
			{
				$each_field = array();
				foreach ($one_word as $word)
				{
					$each_field[] = "(" . implode(" {$this->like} '%$word' OR ", $target_fields) . " {$this->like} '%$word%')";
				}
				$sql .= " AND (". implode (" OR ", $each_field) . ")";
			}

			// "Without the words" filtering
			$without_words = $this->db->db_addslashes($without_words);
			$without_words = strlen($without_words)? explode(' ', $without_words) : False;
			$each_field = array();
			if ($without_words)
			{
				foreach ($without_words as $word)
				{
					$each_field[] = "(" . implode(" NOT {$this->like} '%word' AND ", $target_fields) . " NOT {$this->like} '%$word%')";
				}
				$sql .= " AND " . implode(" AND ", $each_field);
			}

			// do the query
			//echo "query: $sql <br>";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->num_rows = $this->db->num_rows();
			$this->db->limit_query($sql, $start, __LINE__, __FILE__, $num_res);
			return $this->results_to_array($fields);
		}

		/**
		* Fetches results from database and returns array of articles
		*
		* @author	Alejandro Pedraza
		* @access 	private
		* @param	array	$fields	Which fields to fetch
		* @return	array	Articles
		*/
		function results_to_array($fields)
		{
			$articles = array();
			for ($i=0; $this->db->next_record(); $i++)
			{
				foreach ($fields as $field)
				{
                    if (preg_match('/.* AS (.*)/', $field, $matches))
					{
                        $modified_field = $matches[1];
                    }
					else
					{
                        $modified_field = $field;
                    }
					$articles[$i][$modified_field] = $this->db->f($modified_field);
				}
				$articles[$i]['art_id'] = $this->db->f('art_id');
				$articles[$i]['total_votes'] = $articles[$i]['votes_1'] + $articles[$i]['votes_2'] + $articles[$i]['votes_3'] + $articles[$i]['votes_4'] + $articles[$i]['votes_5'];
				if ($articles[$i]['total_votes'])
				{
					$articles[$i]['average_votes'] = (1*$articles[$i]['votes_1'] + 2*$articles[$i]['votes_2'] + 3*$articles[$i]['votes_3'] + 4*$articles[$i]['votes_4'] + 5*$articles[$i]['votes_5']) / ($articles[$i]['total_votes']);
				}
				else
				{
					$articles[$i]['average_votes'] = 0;	// avoid division by zero
				}
			}

			foreach ( $articles as &$a )
			{
				$a['username'] = (string) $GLOBALS['phpgw']->accounts->get($a['user_id']);

			}
			return $articles;
		}

		/**
		* Upgrades phpgw_kb_search table given user input
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$art_id	Article ID
		* @param	string	$word			Keyword
		* @param	mixed	$upgrade_key	Whether to give more or less score to $word
		* @return	void
		*/
		function update_keywords($art_id, $word, $upgrade_key)
		{
			$word = $this->db->db_addslashes(substr($word, 0, 30));

			// retrieve current score
			$sql = "SELECT score FROM phpgw_kb_search WHERE keyword='$word' AND art_id=$art_id";
			$this->db->query($sql, __LINE__, __FILE__);
			$keyword_exists = $this->db->next_record();
			if ($keyword_exists && upgrade_key != 'same')
			{
				// upgrade score
				$old_score = $this->db->f('score');
				$new_score = $upgrade_key? $old_score + 1 : $old_score - 1;
				$sql = "UPDATE phpgw_kb_search SET score=$new_score WHERE keyword='$word' AND art_id=$art_id";
				$this->db->query($sql, __LINE__, __FILE__);
			}
			elseif (!$keyword_exists || $upgrade_key != 'same')
			{
				// create new entry for word
				$sql = "INSERT INTO phpgw_kb_search (keyword, art_id, score) VALUES('$word', $art_id, 1)";
				$this->db->query($sql, __LINE__, __FILE__);
			}
		}

		/**
		* Returns unanswered questions
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	array	$owners			User ids accessible by current user
		* @param	array	$categories		Categories ids
		* @param	int		$start			For pagination
		* @param	int		$upper_limit	For pagination
		* @param	srting	$sort			Sorting direction: ASC | DESC
		* @param	string	$order			Sorting field name
		* @param	mixed	$publish_filter	To filter pusblished or unpublished entries
		* @param	string	$query			Search string
		* @return	array					Questions
		*/
		function unanswered_questions($owners, $categories, $start, $upper_limit='', $sort, $order, $publish_filter=False, $query)
		{
			$fields = array('question_id', 'user_id', 'summary', 'details', 'cat_id', 'creation', 'published');
			$fields_str = implode(', ', $fields);
			$owners = implode(', ', $owners);
			$sql = "SELECT $fields_str FROM phpgw_kb_questions WHERE user_id IN ($owners)";
			if ($publish_filter && $publish_filter!='all') 
			{
				($publish_filter == 'published')? $publish_filter = 1 : $publish_filter = 0;
				$sql .= " AND published=$publish_filter";
			}
			if (!$categories)
			{
				$sql .= " AND cat_id = 0";
			}
			else
			{
				$categories = implode(",", $categories);
				$sql .= " AND cat_id IN(" . $categories . ")";
			}
			if ($query)
			{
				$query = $this->db->db_addslashes($query);
				$words = explode(' ', $query);
				$sql .= " AND (summary {$this->like} '%" . implode("%' OR summary {$this->like} '%", $words) . "%' OR details {$this->like} '%" . implode("%' OR details {$this->like} '%", $words) . "%')";
			}
			if ($order)
			{
				$sql .= " ORDER BY $order $sort";
			}
			//echo "sql: $sql <br><br>";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->num_rows = $this->db->num_rows();
			$this->num_questions = $this->num_rows;
			$this->db->limit_query($sql, $start, __LINE__, __FILE__, $upper_limit);
			$questions = array();
			for ($i=0; $this->db->next_record(); $i++)
			{
				foreach ($fields as $field)
				{
					$questions[$i][$field] = $this->db->f($field);
				}
			}
			foreach ( $questions as &$q )
			{
				$q['username'] = (string) $GLOBALS['phpgw']->accounts->get($q['user_id']);
			}

			return $questions;
		}

		/**
		* Saves a new or edited article
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	array	$contents	article contents
		* @param	bool	$is_new		True if it's a new article, False if its an edition
		* @param	bool	$publish	True if the article is to be published without revision
		* @return	mixed				article id or False if failure
		**/
		function save_article($contents, $is_new, $publish = False)
		{
			$current_time = time();
			if ($is_new)
			{
				($publish)? $publish = 1 : $publish = 0;
				$q_id = $contents['answering_question']? $contents['answering_question'] : 0;
				$sql = "INSERT INTO phpgw_kb_articles (q_id, title, topic, text, cat_id, published, user_id, created, modified, modified_user_id, votes_1, votes_2, votes_3, votes_4, votes_5) VALUES ("
						. "$q_id, '"
						. $this->db->db_addslashes($contents['title']) . "', '"
						. $this->db->db_addslashes($contents['topic']) . "', '"
						. $this->db->db_addslashes($contents['text']) . "', "
						. (int) $contents['cat_id'] . ", "
						. $publish . ", "
						. $GLOBALS['phpgw_info']['user']['account_id'] . ", "
						. $current_time . ", " . $current_time . ", "
						. $GLOBALS['phpgw_info']['user']['account_id'] . ", "
						. " 0, 0, 0, 0, 0)";
				$this->db->query($sql, __LINE__, __FILE__);
				$article_id = $this->db->get_last_insert_id('phpgw_kb_articles', 'art_id');

				// update table phpgw_kb_search with keywords. Even if no keywords were introduced, generate an entry
				$keywords = explode (' ', $contents['keywords']);
				foreach ($keywords as $keyword)
				{
					$this->update_keywords($article_id, $keyword, 'same');
				}

				// if publication is automatic and the article answers a question, delete the question
				if ($publish && $contents['answering_question'])
				{
					$sql = "DELETE FROM phpgw_kb_questions WHERE question_id=$q_id";
					$this->db->query($sql, __LINE__, __FILE__);
				}

				return $article_id;
			}
			else
			{
				$sql = "UPDATE phpgw_kb_articles SET "
						." title='" . $this->db->db_addslashes($contents['title'])
						."', topic='" . $this->db->db_addslashes($contents['topic'])
						."', text='" . $this->db->db_addslashes($contents['text'])
						."', cat_id='" . (int)($contents['cat_id'])
						."', modified=" . $current_time
						.", modified_user_id=" . $GLOBALS['phpgw_info']['user']['account_id']
						." WHERE art_id=" . $contents['editing_article_id'];
				$this->db->query($sql, __LINE__, __FILE__);
				$queries_ok = false;
				if ($this->db->affected_rows()) $queries_ok = true;

				// update keywords
				$keywords = explode (' ', $contents['keywords']);
				foreach ($keywords as $keyword)
				{
					$this->update_keywords($contents['editing_article_id'], $keyword, True, False);
				}

				if ($queries_ok)
				{
					return $contents['editing_article_id'];
				}
				else
				{
					return False;
				}
			}
		}

		/**
		* Changes article owner when user is deleted
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int	$owner		actual owner
		* @param	int	$new_owner	new owner
		* @return	void
		**/
		function change_articles_owner($owner, $new_owner)
		{
			$sql = "UPDATE phpgw_kb_articles SET user_id='$new_owner' WHERE user_id='$owner'";
			$this->db->query($sql, __LINE__, __FILE__);
		}

		/**
		* Deletes article
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$art_id		article id
		* @return	bool	1 on success, 0 on failure
		**/
		function delete_article($art_id)
		{
			$sql = "DELETE FROM phpgw_kb_articles WHERE art_id=$art_id";
			if (!$this->db->query($sql, __LINE__, __FILE__)) return 0;
			return 1;
		}

		/**
		* Deletes question
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$q_id		Question id
		* @return	bool	1 on success, 0 on failure
		**/
		function delete_question($q_id)
		{
			$sql = "DELETE FROM phpgw_kb_questions WHERE question_id=$q_id";
			if (!$this->db->query($sql, __LINE__, __FILE__)) return 0;
			return 1;
		}

		/**
		* Returns latest articles entered
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int	$parent_cat	Category id
		* @return	array	Articles
		*/
		function get_latest_articles($parent_cat)
		{
			$sql = "SELECT art_id, title, topic, text, modified, votes_1, votes_2, votes_3, votes_4, votes_5 FROM phpgw_kb_articles";
			$this->db->query($sql, __LINE__, __FILE__);

			$articles = array();
			while ($this->db->next_record())
			{
				$rating = 1*$this->db->f('votes_1') + 2*$this->db->f('votes_2') + 3*$this->db->f('votes_3') + 4*$this->db->f('votes_4') + 5*$this->db->f('votes_5');
				$articles[$this->db->f('art_id')] = array(
					'title'		=> $this->db->f('title'),
					'topic'		=> $this->db->f('topic'),
					'text'		=> $this->db->f('text'),
					'modified'	=> $this->db->f('modified'),
					'rating'	=> $rating
				);
			}

			return $articles;
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
			$fields = array('art_id', 'title', 'topic', 'text', 'views', 'cat_id', 'published', 'user_id', 'created', 'modified', 'modified_user_id', 'votes_1', 'votes_2', 'votes_3', 'votes_4', 'votes_5');
			$fields_str = implode(", ", $fields);

			$sql =	"SELECT $fields_str FROM phpgw_kb_articles WHERE art_id=$art_id";
			//echo "sql: $sql <br>";
			$this->db->query($sql, __LINE__, __FILE__);
			$article = array();
			if (!$this->db->next_record()) return 0;
			foreach ($fields as $field)
			{
				$article[$field] = $this->db->f($field);
			}

			// get article's attached files names
			$this->db->query("SELECT art_file, art_file_comments FROM phpgw_kb_files WHERE art_id=$art_id", __LINE__, __FILE__);
			$article['files'] = array();
			$i = 0;
			while ($this->db->next_record())
			{
				$article['files'][$i]['file'] = $this->db->f('art_file');
				$article['files'][$i]['comment'] = $this->db->f('art_file_comments');
				$i++;
			}

			// get article's attached urls
			$this->db->query("SELECT art_url, art_url_title FROM phpgw_kb_urls WHERE art_id=$art_id", __LINE__, __FILE__);
			$article['urls'] = array();
			$i = 0;
			while ($this->db->next_record())
			{
				$article['urls'][$i]['link'] = $this->db->f('art_url');
				$article['urls'][$i]['title'] = $this->db->f('art_url_title');
				$i++;
			}

			// get article's keywords
			$this->db->query("SELECT keyword FROM phpgw_kb_search WHERE art_id=$art_id", __LINE__, __FILE__);
			$article['keywords'] = array();
			while ($this->db->next_record())
			{
				$article['keywords'][] = $this->db->f('keyword');
			}
			$article['keywords'] = implode(' ', $article['keywords']);

			// normalize vote frequence to the range 0 - 40
			$votes = array();
			$article['total_votes'] = $article['votes_1'] + $article['votes_2'] + $article['votes_3'] + $article['votes_4'] + $article['votes_5'];
			if ($article['total_votes'])
			{
				$article['average_votes'] = ($article['votes_1'] + 2*$article['votes_2'] + 3*$article['votes_3'] + 4*$article['votes_4'] + 5*$article['votes_5']) / $article['total_votes'];
			}
			else
			{
				$article['average_votes'] = 0;
			}

			return $article;
		}

		/**
		* Returns all articles ids from a given owner
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$owner		owner id
		* @return	array	Articles ids
		**/
		function get_articles_ids($owner)
		{
			$sql = "SELECT art_id FROM phpgw_kb_articles WHERE user_id=$owner";
			$this->db->query($sql, __LINE__, __FILE__);
			$articles_ids = array();
			while ($this->db->next_record())
			{
				$articles_ids[] = $this->db->f('art_id');
			}
			return $articles_ids;
		}

		/**
		* Increments the view count of a published article
		*
		* @author	Alejandro Pedraza
		* @param	int	$art_id			article id
		* @param	int	$current_count	current view count
		* @return	void
		**/
		function register_view($art_id, $current_count)
		{
			$current_count ++;
			$sql = "UPDATE phpgw_kb_articles SET views=$current_count WHERE art_id=$art_id";
			$this->db->query($sql, __LINE__, __FILE__);
		}

		/**
		* Returns article's comments
		*
		* @author	Alejandro Pedraza
		* @param	integer	$id			article id
		* @param	integer	$limit		Number of comments to return
		* @return	array				Comments
		*/
		function get_comments($id, $limit)
		{
			$id = (int) $id;
			$fields = array('comment_id', 'user_id', 'comment', 'entered', 'art_id', 'published');
			$fields_str = implode(", ", $fields);
			$sql = "SELECT " . $fields_str . " FROM phpgw_kb_comment WHERE art_id={$id} ORDER BY entered DESC";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->num_comments = $this->db->num_rows();
			if ($limit)
			{
				$this->db->limit_query($sql, 0, __LINE__, __FILE__, $limit);
			}
			$comments = array();
			for ($i=0; $this->db->next_record(); $i++)
			{
				foreach ($fields as $field)
				{
					$comments[$i][$field] = $this->db->f($field);
				}
			}
			foreach ( $comments as &$c )
			{
				$c['username'] = (string) $GLOBALS['phpgw']->accounts->get($c['user_id']); 
			}
			return $comments;
		}

		/**
		* Delete article's comments
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	integer	$id		article id
		* @return	void
		*/
		function delete_comments($id)
		{
			$id = (int) $id;
			$sql = "DELETE FROM phpgw_kb_comment WHERE art_id = {$id}";
			$this->db->query($sql, __LINE__, __FILE__);
		}

		/**
		* Delete article's ratings
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$art_id		article id
		* @return	void
		*/
		function delete_ratings($art_id)
		{
			$sql = "DELETE FROM phpgw_kb_ratings WHERE art_id=$art_id";
			$this->db->query($sql, __LINE__, __FILE__);
		}

		/**
		* Delete article's file entries in phpgw_kb_files
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$art_id		article id
		* @param	string	$file_to_erase	File name
		* @return	bool					1 on success, 0 on failure
		*/
		function delete_files($art_id, $file_to_erase = false)
		{
			$files = '';
			if ($file_to_erase)
			{
				$file_to_erase = $this->db->db_addslashes($file_to_erase);
				$files = " AND art_file='$file_to_erase'";
			}
			$sql = "DELETE FROM phpgw_kb_files WHERE art_id=$art_id$files";
			$this->db->query($sql, __LINE__, __FILE__);
			if ($this->db->affected_rows()) return True;
			return False;
		}

		/**
		* Delete article's urls
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$art_id		article id
		* @return	void
		*/
		function delete_urls($art_id)
		{
			$sql = "DELETE FROM phpgw_kb_urls WHERE art_id=$art_id";
			$this->db->query($sql, __LINE__, __FILE__);
		}

		/**
		* Returns an article related comments
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$art_id	Article id
		* @param	array	$owners	Accessible owners to current user
		* @return	array	IDs and titles of articles
		*/
		function get_related_articles($art_id, $owners)
		{
			$owners = implode(', ', $owners);
			$sql = "SELECT phpgw_kb_articles.art_id, phpgw_kb_articles.title FROM phpgw_kb_related_art, phpgw_kb_articles WHERE phpgw_kb_related_art.related_art_id=phpgw_kb_articles.art_id AND phpgw_kb_related_art.art_id=$art_id AND phpgw_kb_articles.user_id IN ($owners)";
			$this->db->query($sql, __LINE__, __FILE__);
			$related = array();
			while ($this->db->next_record())
			{
				$related[] = array('art_id' => $this->db->f('art_id'), 'title' => $this->db->f('title'));
			}
			return $related;
		}

		/**
		* Tells if the current user has already rated the article
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$art_id		article id
		* @return	bool				1 if he has, 0 if not
		**/
		function user_has_voted($art_id)
		{
			$sql = "SELECT * FROM phpgw_kb_ratings WHERE user_id=" . $GLOBALS['phpgw_info']['user']['account_id'] . " AND art_id=$art_id";
			$this->db->query($sql, __LINE__, __FILE__);
			if ($this->db->next_record()) return 1;
			return 0;
		}

		/**
		* Stores new comment
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	string	$comment	comment text
		* @param	int		$art_id		article id
		* @param	bool	$publish	True if comment is to be published, False if not
		* @return	bool				1 on success, 0 on failure
		**/
		function add_comment($comment, $art_id, $publish)
		{
			$comment = $this->db->db_addslashes($comment);
			($publish)? $publish = 1 : $publish = 0;
			$sql = "INSERT INTO phpgw_kb_comment (user_id, comment, entered, art_id, published) VALUES("
					. $GLOBALS['phpgw_info']['user']['account_id'] . ", '$comment', " . time() . ", $art_id, $publish)";
			$this->db->query($sql, __LINE__, __FILE__);
			if (!$this->db->affected_rows()) return 0;
			return 1;
		}

		/**
		* Adds link to article
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	string	$url		Url
		* @param	string	$title		Url title
		* @param	int		$art_id		article id
		* @return	bool				1 on success, 0 on failure
		*/
		function add_link($url, $title, $art_id)
		{
			$sql = "INSERT INTO phpgw_kb_urls (art_id, art_url, art_url_title) VALUES ($art_id, '$url', '$title')";
			$this->db->query($sql, __LINE__, __FILE__);
			if (!$this->db->affected_rows()) return 0;
			return 1;
		}

		/**
		* Publishes article, and resets creation and modification date
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$art_id		article id
		* @return	int					Numbers of lines affected (should be 1, if not there's an error)
		**/
		function publish_article($art_id)
		{
			$sql = "UPDATE phpgw_kb_articles SET published=1, created=". time() . ", modified=" . time() . " WHERE art_id=$art_id";
			$this->db->query($sql, __LINE__, __FILE__);

			// check if the article answers a question, and if so, delete it
			$sql = "SELECT q_id FROM phpgw_kb_articles WHERE art_id=$art_id";
			$this->db->query($sql, __LINE__, __FILE__);
			if ($this->db->next_record())
			{
				$sql = "DELETE FROM phpgw_kb_questions WHERE question_id=".$this->db->f('q_id');
				$this->db->query($sql, __LINE__, __FILE__);
			}

			return True;
		}

		/**
		* Publishes question
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$q_id		Question id
		* @return	int					Numbers of lines affected (should be 1, if not there's an error)
		**/
		function publish_question($q_id)
		{
			$sql = "UPDATE phpgw_kb_questions SET published=1 WHERE question_id=$q_id";
			$this->db->query($sql, __LINE__, __FILE__);
			return ($this->db->affected_rows());
		}

		/**
		* Publishes article comment
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int	$art_id		Article ID
		* @param	int $comment_id	Comment ID
		* @return	int				Numbers of lines affected (should be 1, if not there's an error)
		*/
		function publish_comment($art_id, $comment_id)
		{
			$sql = "UPDATE phpgw_kb_comment SET published=1 WHERE art_id=$art_id AND comment_id=$comment_id";
			$this->db->query($sql, __LINE__, __FILE__);
			return ($this->db->affected_rows());
		}

		/**
		* Deletes article comment
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int	$art_id		Article ID
		* @param	int $comment_id	Comment ID
		* @return	int				Numbers of lines affected (should be 1, if not there's an error)
		*/
		function delete_comment($art_id, $comment_id)
		{
			$sql = "DELETE FROM phpgw_kb_comment WHERE art_id=$art_id AND comment_id=$comment_id";
			$this->db->query($sql, __LINE__, __FILE__);
			return ($this->db->affected_rows());
		}

		/**
		* Deletes article comment
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int	$art_id			Article ID
		* @param	int $delete_link	Link ID
		* @return	bool				1 on success, 0 on failure
		*/
		function delete_link($art_id, $delete_link)
		{
			$delete_link = $this->db->db_addslashes($delete_link);
			$sql = "DELETE FROM phpgw_kb_urls WHERE art_id=$art_id AND art_url='$delete_link'";
			$this->db->query($sql, __LINE__, __FILE__);
			if (!$this->db->affected_rows()) return 0;
			return 1;
		}

		/**
		* Increments vote_x in table
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int	$art_id			Article id
		* @param 	int	$rating			Rating between 1 and 5
		* @param	int	$current_rating	Number of current votes in that rating
		* @return	bool				1 on success, 0 on failure
		**/
		function add_vote($art_id, $rating, $current_rating)
		{
			$new_rating = $current_rating + 1;
			$sql = "UPDATE phpgw_kb_articles SET votes_" . $rating . "=$new_rating WHERE art_id=$art_id";
			$this->db->query($sql, __LINE__, __FILE__);
			if (!$this->db->affected_rows()) return 0;
			return 1;
		}

		/**
		* Registers that actual user has voted this article
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int	$art_id		article id
		* @return	bool			1 on success, 0 on failure
		**/
		function add_rating_user($art_id)
		{
			$sql = "INSERT INTO phpgw_kb_ratings (user_id, art_id) VALUES(" . $GLOBALS['phpgw_info']['user']['account_id'] . ", $art_id)";
			$this->db->query($sql, __LINE__, __FILE__);
			if (!$this->db->affected_rows()) return 0;
			return 1;
		}

		/**
		* Register file upload in the article's database record
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$article_id	Article id
		* @param	string	$file_name	File name
		* @return	bool			1 on success, 0 on failure
		*/
		function add_file($article_id, $file_name)
		{
			$file_name = $this->db->db_addslashes($file_name);
			$comment = $_POST['file_comment']? $_POST['file_comment'] : '';
			$comment = $this->db->db_addslashes($comment);

			$sql = "INSERT INTO phpgw_kb_files (art_id, art_file, art_file_comments) VALUES($article_id, '$file_name', '$comment')";
			$this->db->query($sql, __LINE__, __FILE__);
			if (!$this->db->next_record()) return 0;
			return 1;
		}

		/**
		* Checks if there is already an article in the db with the given ID
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$art_id		article id
		* @return	bool				1 if there is one, 0 if not
		**/
		function exist_articleID($article_id)
		{
			$sql = "SELECT art_id FROM phpgw_kb_articles WHERE art_id=" . $article_id;
			$this->db->query($sql, __LINE__, __FILE__);
			return $this->db->next_record();
		}

		/**
		* Returns ids of owners of articles
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	array	$articles	Ids of articles
		* @return	array				Article ids and owners ids
		*/
		function owners_list($articles)
		{
			$articles = implode(', ', $articles);
			$sql = "SELECT art_id, user_id FROM phpgw_kb_articles WHERE art_id IN($articles)";
			$this->db->query($sql, __LINE__, __FILE__);
			$owners = array();
			while ($this->db->next_record())
			{
				$owners[] = array('art_id' => $this->db->f('art_id'), 'user_id' => $this->db->f('user_id'));
			}
			return $owners;
		}

		/**
		* Adds related article to article
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$art_id		Article id
		* @param	array	$articles	Articles id to relate to $art_id
		* @return	bool				1 on success, 0 on failure
		*/
		function add_related($art_id, $articles)
		{
			$added = False;
			foreach ($articles as $article)
			{
				$sql = "INSERT INTO phpgw_kb_related_art (art_id, related_art_id) VALUES($art_id, $article)";
				$this->db->query($sql, __LINE__, __FILE__);
				if ($this->db->affected_rows()) $added = True;
			}
			return $added;
		}

		/**
		* Deletes related article to article
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$art_id		Article id
		* @param	int		$related_id	Article id to delete
		* @return	void
		*/
		function delete_related($art_id, $related_id, $all = False)
		{
			$sql_operator = $all? 'OR' : 'AND';
			$sql = "DELETE FROM phpgw_kb_related_art WHERE art_id=$art_id $sql_operator related_art_id=$related_id";
			$this->db->query($sql, __LINE__, __FILE__);
		}

		/**
		* Deletes entry in keywords table
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	int		$art_id		Article id
		* @return	void
		*/
		function delete_search($art_id)
		{
			$sql = "DELETE FROM phpgw_kb_search WHERE art_id=$art_id";
			$this->db->query($sql, __LINE__, __FILE__);
		}

		/**
		* Adds question to database
		*
		* @author	Alejandro Pedraza
		* @access	public
		* @param	array	$data		Question data
		* @param	bool	$publish	Whether to publish the question or not
		* @return	int				Numbers of lines affected (should be 1, if not there's an error)
		*/
		function add_question($data, $publish)
		{
			($publish)? $publish = 1 : $publish = 0;
			$sql = "INSERT INTO phpgw_kb_questions (user_id, summary, details, cat_id, creation, published) VALUES ("
					. $GLOBALS['phpgw_info']['user']['account_id'] . ", '"
					. $this->db->db_addslashes($data['summary']) . "', '"
					. $this->db->db_addslashes($data['details']) . "', "
					. (int)$data['cat_id'] . ", "
					. time() . ", "
					. $publish . ")";
			$this->db->query($sql, __LINE__, __FILE__);
			return $this->db->affected_rows();
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
			$fields = array('user_id', 'summary', 'details', 'cat_id', 'creation');
			$fields_str = implode(", ", $fields);

			$sql = "SELECT $fields_str FROM phpgw_kb_questions WHERE question_id=$q_id AND published=1";
			$this->db->query($sql, __LINE__, __FILE__);
			$question = array();
			while ($this->db->next_record())
			{
				foreach ($fields as $field)
				{
					$question[$field] = $this->db->f($field);
				}
			}
			return $question;
		}
	}
?>
