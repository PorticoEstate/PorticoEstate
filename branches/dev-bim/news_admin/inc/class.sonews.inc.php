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

	/* $Id$ */

	class sonews
	{
		/**
		* @var $db reference to the global databse object
		*/
		var $db;

		/**
		* @constructor
		*/
		function sonews()
		{
			$this->db =& $GLOBALS['phpgw']->db;
		}

		/**
		* Add a news story
		*
		* @param array $news the news item to add
		* @return int the ID of the new story (0 == failure)
		*/
		function add($news)
		{
			$sql  = 'INSERT INTO phpgw_news '
				. ' (news_date, news_submittedby, news_content, news_subject, news_begin,'
				. ' news_end,news_teaser,news_cat,is_html, lastmod) '
				. 'VALUES (' . intval($news['date'])  . ','
				. $GLOBALS['phpgw_info']['user']['account_id'] . ','
				. "'" . $this->db->db_addslashes($news['content']) ."',"
				. "'" . $this->db->db_addslashes($news['subject']) ."',"
				. intval($news['begin']) . ',' 
				. intval($news['end']) . ','
				. "'" . $this->db->db_addslashes($news['teaser']) . "'," 
				. intval($news['category']) . ',1,' . time() .')';
				
			$this->db->query($sql, __LINE__, __FILE__);

			return $this->db->get_last_insert_id('phpgw_news', 'news_id');
		}

		/**
		* Delete a news item
		*
		* @param int $news_id the story to be deleted
		*/
		function delete($news_id)
		{
			$this->db->query('DELETE FROM phpgw_news WHERE news_id=' . intval($news_id) ,__LINE__,__FILE__);
		}
		
		/**
		* Edit a news item
		*
		* @param array $news the editted version of the news item
		* @return bool was the story updated?
		*/
		function edit($news)
		{
			//echo '<br />SO-save:<pre>'; print_r($news); echo '</pre>';
			$this->db->query("UPDATE phpgw_news SET "
				. "news_content='" . $this->db->db_addslashes($news['content']) . "',"
				. "news_subject='" . $this->db->db_addslashes($news['subject']) . "', "
				. "news_teaser='" . $this->db->db_addslashes($news['teaser']) . "', "
				. 'news_begin=' . intval($news['begin']) . ', '
				. 'news_end=' . intval($news['end']) . ', '
				. 'news_cat=' . intval($news['category']) . ', '
				. 'is_html=1,  '
				. 'lastmod=' . time() . ' '
				. 'WHERE news_id=' . intval($news['id']),__LINE__,__FILE__);
			return !!$this->db->affected_rows();
		}

		/**
		* Get a single news item
		*
		* @param int $news_id the news item sought
		* @return array the news itme (empty array on failure)
		*/
		function get_news($news_id)
		{
			$this->db->query('SELECT * FROM phpgw_news WHERE news_id=' . intval($news_id),__LINE__,__FILE__);
			$this->db->next_record();

			$item = array(
				'id'		=> $this->db->f('news_id'),
				'date'		=> $this->db->f('news_date'),
				'subject'	=> $this->db->f('news_subject', True),
				'submittedby'	=> $this->db->f('news_submittedby'),
				'teaser'	=> $this->db->f('news_teaser', True),
				'content'	=> $this->db->f('news_content', True),
				'begin'		=> $this->db->f('news_begin'),
				'end' 		=> $this->db->f('news_end'),
				'category'	=> $this->db->f('news_cat'),
				'is_html'	=> ($this->db->f('is_html') ? True : False),
				'lastmod'	=> $this->db->f('lastmod')
			);
			//echo '<pre>'; print_r($item); echo '</pre>';
			return $item;
		}

		/**
		* Get a list of news stories
		*
		* @param int $cat the category to take the news items from (0 == all available to the user)
		* @start int $start the story in the sequence to start at
		* @param string $order the db field to sort on
		* @param string $sort "ASC"ending|"DESC"ending
		* @param int $limit the maximum number of stories to return (0 == no limit)
		* @param bool $activeonly only return stories which are "active"
		* @return array the news items (empty array on failure)
		*/
		function get_newslist($cat_id, $start, $order,$sort,$limit=0,$activeonly,&$total)
		{
			if ($order)
			{
				$ordermethod = ' ORDER BY ' . $this->db->db_addslashes($order) . ' ' . $this->db->db_addslashes($sort);
			}
			else
			{
				$ordermethod = ' ORDER BY news_date DESC';
			}

			if (is_array($cat_id))
			{
				$filter = "IN (" . implode(',',$cat_id) . ')';
			}
			else
			{
				$filter = "=" . intval($cat_id);
			}

			$sql = 'SELECT * FROM phpgw_news WHERE news_cat ' . $filter;
			if ($activeonly)
			{
				$now = time();
				$sql .= " AND news_begin<=$now AND news_end>=$now";
			}
			$sql .= $ordermethod;

			$this->db->query($sql,__LINE__,__FILE__);
			$total = $this->db->num_rows();
			$this->db->limit_query($sql,$start,__LINE__,__FILE__,$limit);

			$news = array();

			while ($this->db->next_record())
			{
				$news[$this->db->f('news_id')] = array
				(
					'subject'	=> $this->db->f('news_subject', True),
					'submittedby'	=> $this->db->f('news_submittedby'),
					'date'		=> $this->db->f('news_date'),
					'id'		=> $this->db->f('news_id'),
					'begin'		=> $this->db->f('news_begin'),
					'end'		=> $this->db->f('news_end'),
					'category'	=> $this->db->f('news_cat'),
					'teaser'	=> $this->db->f('news_teaser', True),
					'content'	=> $this->db->f('news_content',True),
					'is_html'	=> ($this->db->f('is_html') ? True : False),
					'lastmod'	=> $this->db->f('lastmod')
				);
			}
			return $news;
		}
	}
?>
