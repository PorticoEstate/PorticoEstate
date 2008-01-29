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

	/* $Id: class.bonews.inc.php 17992 2007-02-24 20:42:12Z sigurdne $ */

	/**
	* phpGroupWare News Management Logic layer
	*
	* @package news_admin
	*/
	class bonews
	{
		/**
		* @var object $acl access control object
		*/
		var $acl;

		/**
		* @var int $cat_id the category to limit selection to
		*/
		var $cat_id;
		
		/**
		* @var bool $debug enable debugging
		*/
		var $debug = false;

		/**
		* @param string $sort the sort order
		*/
		var $order = '';

		/**
		* @var object $sonews storage layer
		*/
		var $sonews;

		/**
		* @param string $sort the field to sort on
		*/
		var $sort  = '';
		
		/**
		* @var int $start listing
		*/
		var $start = 0;

		/**
		* @var int $total number of stories found
		*/
		var $total = 0;

		/**
		* @var bool $use_session is the session cache being used to store data?
		*/
		var $use_session = false;

		/**
		* @var int maximum unix timestamp
		* @todo change this around 19 Jan 2038 03:14:07 GMT
		*/
		var $unixtimestampmax = 2147483647;
		var $dateformat;

		/**
		* @constructor
		*/
		function bonews($session = false)
		{
			$this->acl = CreateObject('news_admin.boacl');
			$this->sonews = CreateObject('news_admin.sonews');
			if($session)
			{
				$this->_read_sessiondata();
				$this->use_session = True;
				foreach(array('start','query','sort','order','cat_id') as $var)
				{
					$this->$var = isset($_REQUEST[$var]) ? $_REQUEST[$var] : '';
				}

				$this->cat_id = $this->cat_id ? $this->cat_id : 'all';
				$this->_save_sessiondata();
			}
			$this->catbo = createobject('phpgwapi.categories','','news_admin');
			$this->cats = $this->catbo->return_array('all',0,False,'','','cat_name',True);
			$this->dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
		}

		/**
		* Add a news story
		*
		* @param array $news the news item to add
		* @return int the ID of the new story (0 == failure)
		*/
		function add($news)
		{
			return $this->acl->is_writeable($news['category']) ? $this->sonews->add($news) : 0;
		}

		/**
		* Delete a news item
		*
		* @param int $news_id the story to be deleted
		*/
		function delete($news_id)
		{
			$this->sonews->delete($news_id);
		}
		
		/**
		* Edit a news item
		*
		* @param array $news the editted version of the news item
		* @return bool was the story updated?
		*/
		function edit($news)
		{
			$oldnews = $this->sonews->get_news($news['id']);
			return ($this->acl->is_writeable($oldnews['category']) && 
					$this->acl->is_writeable($news['category'])) ?
				$this->sonews->edit($news) :
				false;
		}

		/**
		* Get a single news item
		*
		* @param int $news_id the news item sought
		* @return array the news itme (empty array on failure)
		*/
		function get_news($news_id)
		{
			$news = $this->sonews->get_news($news_id);
			//echo '<br />BO:<br />'; print_r($news); echo '</pre>';
			if ($this->acl->is_readable($news['category']))
			{
				$this->total = 1;
				$news['content'] = $news['content'];
				//echo '<br />BO2:<br />'; print_r($news); echo '</pre>';
				return $news;
			}
			return false;
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
		function get_newslist($cat_id, $start=0, $order='',$sort='',$limit=0,$activeonly=False)
		{
			$cats = array();
			if ($cat_id == 0)
			{
				if(! ($this->cats && is_array($this->cats)) )
				{
					$this->cats = array();
				}
				foreach($this->cats as $cat)
				{
					if ($this->acl->is_readable($cat['id']))
					{
						$cats[] = $cat['id'];
					}
				}
			}
			elseif($this->acl->is_readable($cat_id))
			{
				$cats[] = $cat_id;
			}
			
			if( count($cats) )
			{
				$news = $this->sonews->get_newslist($cats, $start,$order,$sort,$limit,$activeonly,$this->total);
				return $news;
			}
			return array();
		}

		/**
		* Sets the visibility status of a story
		*
		* @param array $news a reference to the news story so the date can be set correctly
		*/
		function get_visibility(&$news)
		{
			$now = time();

			if ($news['end'] < $now)
			{
				return lang('Never');
			}
			else
			{
				if ($news['begin'] < $now)
				{
					if ($news['end'] == $this->unixtimestampmax)
					{
						return lang('Always');
					}
					else
					{
						return lang('until') . date($this->dateformat,$news['end']);
					}
				}
				else
				{
					if ($news['end'] == $this->unixtimestampmax)
					{
						return lang('from') . date($this->dateformat,$news['begin']);

					}
					else
					{
						return lang('from') . ' ' . date($this->dateformat,$news['begin']) . ' ' . 
							lang('until') . ' ' . date($this->dateformat,$news['end']);
					}
				}
			}
		}

		/**
		* set the begin and end dates 
		*
		* @param int $from the unix timestamp the story should be shown from
		* @param int $until the unix timestamp the story should be shown until
		* @param array the news story
		*/
		function set_dates($from, $until, &$news)
		{
			switch($from)
			{
				//always
				case 1:
					$news['begin'] = $news['date'];
					$news['end'] = $this->unixtimestampmax;
					break;
				//never
				case 0:
					$news['begin'] = 0;
					$news['end'] = 0;
					break;
				default:
					if ( $until )
					{
						$news['end'] = $this->unixtimestampmax;
					}
			}
		}

		/**
		* Read the values of some basic variable from the session cache
		*
		* @access private
		*/
		function _read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','news_admin');
			if($this->debug)
			{
				echo '<br>Read:'; _debug_array($data);
			}

			$this->start  = $data['start'];
			$this->query  = $data['query'];
			$this->sort   = $data['sort'];
			$this->order  = $data['order'];
			$this->cat_id = $data['cat_id'];
		}
		
		/**
		* Save basic variable values to the session cache
		*
		* @access private
		*/
		function _save_sessiondata()
		{
			$data = array(
				'start' => $this->start,
				'query' => $this->query,
				'sort'  => $this->sort,
				'order' => $this->order,
				'cat_id' => $this->cat_id,
			);
			
			if($this->debug)
			{
				echo '<br>Save:'; _debug_array($data);
			}
			
			$GLOBALS['phpgw']->session->appsession('session_data','news_admin',$data);
		}
	}
?>
