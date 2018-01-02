<?php
	/**
	* Preferences - business object categories
	*
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package preferences
	* @version $Id$
	*/

	/**
	 * Business object categories
	 *
	 * @package preferences
	 */
	class bocategories
	{
		/**
		 * Categories
		 * @var unknown
		 */
		var $cats;

		/**
		 * 
		 * @var string
		 */
		var $start;
		/**
		 * 
		 * @var string
		 */
		var $query;
		/**
		 * 
		 * @var string
		 */
		var $sort;
		/**
		 * 
		 * @var string
		 */
		var $order;

		/**
		 * 
		 * 
		 * @param $cats_app
		 */
		function bocategories($cats_app='')
		{
			$this->cats           = CreateObject('phpgwapi.categories');
			$this->cats->app_name = $cats_app;

			$this->read_sessiondata($cats_app);

			$start  = isset($_REQUEST['start'])	? $_REQUEST['start']	: 0;
			$query  = isset($_REQUEST['query'])	? $_REQUEST['query']	: '';
			$sort   = isset($_REQUEST['sort'])	? $_REQUEST['sort']		: '';
			$order  = isset($_REQUEST['order'])	? $_REQUEST['order']	: '';

			if(!empty($start) || $start == '0' || $start == 0)
			{
				$this->start = $start;
			}
			if((empty($query) && !empty($this->query)) || !empty($query))
			{
				$this->query = $query;
			}

			if(isset($sort) && !empty($sort))
			{
				$this->sort = $sort;
			}
			if(isset($order) && !empty($order))
			{
				$this->order = $order;
			}
		}

		/**
		 * 
		 * 
		 * @param $data
		 * @param $cats_app
		 */
		function save_sessiondata($data, $cats_app)
		{
			$column = $cats_app . '_cats';
			$GLOBALS['phpgw']->session->appsession('session_data',$column,$data);
		}

		/**
		 * 
		 * 
		 * @param $cats_app
		 */
		function read_sessiondata($cats_app)
		{
			$column = $cats_app . '_cats';
			$data = $GLOBALS['phpgw']->session->appsession('session_data',$column);

			$this->start  = $data['start'];
			$this->query  = $data['query'];
			$this->sort   = $data['sort'];
			$this->order  = $data['order'];
		}

		/**
		 * 
		 * 
		 * @param $global_cats
		 * @return array
		 */
		function get_list($global_cats)
		{
			return $this->cats->return_sorted_array($this->start,True,$this->query,$this->sort,$this->order,$global_cats);
		}

		/**
		 * 
		 * 
		 * @param $values
		 * @return unknown
		 */
		function save_cat($values)
		{
			if ($values['access'])
			{
				$values['access'] = 'private';
			}
			else
			{
				$values['access'] = 'public';
			}

			if ( isset($values['id']) && $values['id'] != 0)
			{
				return $this->cats->edit($values);
			}
			else
			{
				return $this->cats->add($values);
			}
		}

		/**
		 * 
		 * 
		 * @param $data
		 * @return unknown
		 */
		function exists($data)
		{
			$data['type']   = $data['type'] ? $data['type'] : '';
			$data['cat_id'] = $data['cat_id'] ? $data['cat_id'] : '';
			return $this->cats->exists($data['type'],$data['cat_name'],$data['cat_id']);
		}

		/**
		 * 
		 * 
		 * @param $format
		 * @param $type
		 * @param $cat_parent
		 * @param $global_cats
		 * @return unknown
		 */
		function formatted_list($format,$type,$cat_parent,$global_cats)
		{
			return $this->cats->formated_list($format,$type,$cat_parent,$global_cats);
		}

		/**
		 * 
		 * 
		 * @param $cat_id
		 * @param $subs
		 * @return unknown
		 */
		function delete($cat_id,$subs)
		{
			return $this->cats->delete($cat_id,$subs);
		}

		/**
		 * 
		 * 
		 * @param $values
		 * @return string Error message
		 */
		function check_values($values)
		{
			if (strlen($values['descr']) >= 255)
			{
				$error[] = lang('Description can not exceed 255 characters in length !');
			}

			if (!$values['name'])
			{
				$error[] = lang('Please enter a name');
			}
			else
			{
				if (!$values['parent'])
				{
					$exists = $this->exists(array
					(
						'type'     => 'appandmains',
						'cat_name' => $values['name'],
						'cat_id'   => isset($values['id']) ? $values['id'] : 0
					));
				}
				else
				{
					$exists = $this->exists(array
					(
						'type'     => 'appandsubs',
						'cat_name' => $values['name'],
						'cat_id'   => isset($values['id']) ? $values['id'] : 0
					));
				}

				if ($exists == True)
				{
					$error[] = lang('This name has been used already');
				}
			}

			if (is_array($error))
			{
				return $error;
			}
		}
	}
