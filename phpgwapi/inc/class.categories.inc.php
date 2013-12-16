<?php
	/**
	* Category manager
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Bettina Gille <ceb@phpgroupware.org>
	* @copyright Copyright (C) 2000-2012 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage utilities
	* @version $Id$
	*/

	/**
	* Add ability for applications to make use of categories
	*
	* @package phpgwapi
	* @subpackage utilities
	* @internal Examples can be found in notes app
	*/
	class phpgwapi_categories
	{
		var $account_id;
		var $app_name;
		var $cats;
		var $db;
		var $total_records;
		var $grants;
		protected $location;
		protected $location_id;
		/**
		* @var bool $supress_info supress the [' . lang('Global') . '&nbsp;' . lang($this->app_name) . ']'
		*/
		var $supress_info = false;

		/**
		* Constructor
		*
		* @param integer $accountid Account id
		* @param string $app_name Application name defaults to current application
		*/
		function __construct($accountid = '', $app_name = '', $location = '')
		{
			$account_id = (int)get_account_id($accountid);

			if (! $app_name)
			{
				$app_name = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}

			$this->account_id	= (int) $account_id;
			$this->db			=& $GLOBALS['phpgw']->db;
			$this->set_appname($app_name, $location);
		}

		/**
		* Define filter type
		*
		* @param string $type Can be 'subs', 'mains', 'appandmains', 'appandsubs', 'noglobal' or 'noglobalapp'
		* @return string|boolean Part of SQL where clause or false
		*/
		function filter($type)
		{
			
			$filter_location = '';
			if($this->location_id)
			{
				$filter_location = "AND location_id = {$this->location_id}";
			}
			$s = '';
			switch ($type)
			{
				case 'subs':
					$s = ' AND cat_parent != 0';
					break;
				case 'mains':
					$s = ' AND cat_parent = 0';
					break;
				case 'appandmains':
					$s = " AND cat_appname='{$this->app_name}' {$filter_location} AND cat_parent = 0";
					 break;
				case 'appandsubs':
					$s = " AND cat_appname='{$this->app_name}' {$filter_location} AND cat_parent <> 0";
					break;
				case 'noglobal':
					$s = " AND cat_appname != '{$this->app_name}' {$filter_location}";
					break;
				case 'noglobalapp':
					$s = " AND cat_appname = '{$this->app_name}' {$filter_location} AND cat_owner <> {$this->account_id}";
					break;
				default:
					return '';
			}
			return $s;
		}

		/**
		* Get the total number of categories
		*
		* @param string $for Can be 'app', 'appandmains', 'appandsubs', 'subs' or 'mains'
		* @return integer|boolean Number of categories or false
		*/
		function total($for = 'app')
		{
			$filter_location = '';
			if($this->location_id)
			{
				$filter_location = "AND location_id = {$this->location_id}";
			}
			switch($for)
			{
				case 'app':			$w = " WHERE cat_appname='{$this->app_name}' {$filter_location}"; break;
				case 'appandmains':	$w = " WHERE cat_appname='{$this->app_name}' {$filter_location} AND cat_parent =0"; break;
				case 'appandsubs':	$w = " WHERE cat_appname='{$this->app_name}' {$filter_location} AND cat_parent !=0"; break;
				case 'subs':		$w = ' WHERE cat_parent != 0'; break;
				case 'mains':		$w = ' WHERE cat_parent = 0'; break;
				default:			return 0;
			}

			$this->db->query("SELECT COUNT(cat_id) as cnt_cats FROM phpgw_categories $w",__LINE__,__FILE__);
			$this->db->next_record();

			return $this->db->f('cnt_cats');
		}

		/**
		* Get categories
		*
		* @param string $type Can be 'subs', 'mains', 'appandmains', 'appandsubs', 'noglobal' or 'noglobalapp'
		* @param integer $start Start position
		* @param integer $limit Limit for number of categories
		* @param string $query Query to search for in cat_name and cat_description
		* @param string $sort Sort order 'ASC'ending or 'DESC'ending, defaults to ascending
		* @param string $order Fieldname(s) on which the result should be ordered
		* @param boolean $globals True or False, includes the global phpgroupware categories or not
		* @return array $cats Categories
		*/
		function return_array($type,$start,$limit = True,$query = '',$sort = '',$order = '',$globals = False, $parent_id = '', $lastmod = -1, $column = '', $use_acl = false)
		{
			//casting and addslashes for security
			$start		= intval($start);
			$parent_id	= intval($parent_id);
			$query		= $this->db->db_addslashes($query);
			$sort		= $this->db->db_addslashes($sort);
			$order		= $this->db->db_addslashes($order);

			$global_cats = '';
			if ($globals)
			{
				$global_cats = " OR cat_appname='phpgw'";
			}

			$filter = $this->filter($type);

			if (!$sort)
			{
				$sort = 'ASC';
			}

			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";
			}
			else
			{
				$ordermethod = ' ORDER BY cat_main, cat_level, cat_name ASC';
			}

			if ($this->account_id == '-1')
			{
				$grant_cats = ' cat_owner=-1 ';
			}
			else
			{
				if (is_array($this->grants))
				{
					$grants = $this->grants;
					while(list($user) = each($grants))
					{
						$public_user_list[] = $user;
					}
					reset($public_user_list);
					$grant_cats = ' (cat_owner=' . $this->account_id . " OR cat_owner=-1 OR cat_access='public' AND cat_owner in(" . implode(',',$public_user_list) . ')) ';
				}
				else
				{
					$grant_cats = ' cat_owner=' . $this->account_id . ' OR cat_owner=-1 ';
				}
			}

			$parent_filter = '';
			if ($parent_id > 0)
			{
				$parent_filter = ' AND cat_parent=' . $parent_id;
			}

			$querymethod = '';
			if ($query)
			{
				$querymethod = " AND (cat_name LIKE '%$query%' OR cat_description LIKE '%$query%') ";
			}

			if($lastmod && $lastmod >= 0)
			{
				$querymethod .= ' AND last_mod > ' . $lastmod;
			}

			if ($column)
			{
				switch($column)
				{	
					case 'id': 			$table_column = ' cat_id '; break;
					case 'owner': 		$table_column = ' cat_owner '; break;
					case 'access': 		$table_column = ' cat_access '; break;
					case 'app_name': 	$table_column = ' cat_appname '; break;
					case 'main': 		$table_column = ' cat_main '; break;
					case 'parent': 		$table_column = ' cat_parent '; break;
					case 'name': 		$table_column = ' cat_name '; break;
					case 'description': $table_column = ' cat_description '; break;
					case 'data': 		$table_column = ' cat_data '; break;
					case 'last_mod':	$table_column = ' last_mod '; break;
					default:			$table_column = ' cat_id '; break;
				}
			}
			else
			{
				$table_column = ' * ';
			}

			$filter_location = '';
			if($this->location_id)
			{
				$filter_location = "AND location_id = {$this->location_id}";
			}

			$sql = "SELECT $table_column from phpgw_categories WHERE (cat_appname='{$this->app_name}' {$filter_location} AND" . $grant_cats . $global_cats . ')'
				. $parent_filter . $querymethod . $filter;

			$this->db->query($sql, __LINE__, __FILE__);
			$this->total_records = $this->db->num_rows();

			if ($limit)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$_cats = array();
			$cats = array();
			while ($this->db->next_record())
			{
				if ($column)
				{
					$cats[] = array
					(
						"$column" => $this->db->f($column)
					);
				}
				else
				{
					$_cats[] = array
					(
						'id'			=> $this->db->f('cat_id'),
						'owner'			=> $this->db->f('cat_owner'),
						'access'		=> $this->db->f('cat_access'),
						'app_name'		=> $this->db->f('cat_appname'),
						'main'			=> $this->db->f('cat_main'),
						'level'			=> $this->db->f('cat_level'),
						'parent'		=> $this->db->f('cat_parent'),
						'name'			=> $this->db->f('cat_name'),
						'description'	=> $this->db->f('cat_description'),
						'data'			=> $this->db->f('cat_data'),
						'last_mod'		=> $this->db->f('last_mod'),
						'active'		=> (int)$this->db->f('active')
					);
				}
			}
			if($use_acl)
			{
				foreach( $_cats as $cat)
				{
					$location = "{$this->location}.category.{$cat['id']}";
					if ($GLOBALS['phpgw']->acl->check($location, PHPGW_ACL_READ, $this->app_name))
					{
						$cats[] = $cat;
					}
				}
			}
			else
			{
				$cats = $_cats;
			}

			return $cats;
		}

		function return_sorted_array($start,$limit = True,$query = '',$sort = '',$order = '',$globals = False, $parent_id = '', $use_acl = false)
		{
			//casting and slashes for security
			$start		= intval($start);
			$query		= $this->db->db_addslashes($query);
			$sort		= $sort?$this->db->db_addslashes($sort):'ASC';
			$order		= $order?$this->db->db_addslashes($order):'cat_name';
			$parent_id	= intval($parent_id);

			$global_cats = '';
			if ($globals)
			{
				$global_cats = " OR cat_appname='phpgw'";
			}

			$ordermethod = " ORDER BY $order $sort";

			if ($this->account_id == '-1')
			{
				$grant_cats = " cat_owner='-1' ";
			}
			else
			{
				if (is_array($this->grants))
				{
					$grants = $this->grants;
					while(list($user) = each($grants))
					{
						$public_user_list[] = $user;
					}
					reset($public_user_list);
					$grant_cats = " (cat_owner='" . $this->account_id . "' OR cat_owner='-1' OR cat_access='public' AND cat_owner in(" . implode(',',$public_user_list) . ")) ";
				}
				else
				{
					$grant_cats = " cat_owner='" . $this->account_id . "' or cat_owner='-1' ";
				}
			}

			$parent_select = ' AND cat_parent=' . $parent_id;

			$querymethod = '';
			if ($query)
			{
				$querymethod = " AND (cat_name LIKE '%$query%' OR cat_description LIKE '%$query%') ";
			}

			$filter_location = '';
			if($this->location_id)
			{
				$filter_location = "AND location_id = {$this->location_id}";
			}

			$sql = "SELECT * FROM phpgw_categories WHERE (cat_appname='{$this->app_name}' {$filter_location} AND $grant_cats $global_cats) $querymethod";

			$this->db->query($sql . $parent_select . $ordermethod,__LINE__,__FILE__);
			$total = $this->db->num_rows();

			$_cats = array();
			$cats = array();
			while ($this->db->next_record())
			{
				$_cats[] = array
				(
					'id'			=> (int)$this->db->f('cat_id'),
					'owner'			=> (int)$this->db->f('cat_owner'),
					'access'		=> $this->db->f('cat_access'),
					'app_name'		=> $this->db->f('cat_appname'),
					'main'			=> (int)$this->db->f('cat_main'),
					'level'			=> (int)$this->db->f('cat_level'),
					'parent'		=> (int)$this->db->f('cat_parent'),
					'name'			=> $this->db->f('cat_name'),
					'description'	=> $this->db->f('cat_description'),
					'data'			=> $this->db->f('cat_data'),
					'active'		=> (int)$this->db->f('active')
				);
			}

			if($use_acl)
			{
				foreach( $_cats as $cat)
				{
					$location = "{$this->location}.category.{$cat['id']}";
					if ($GLOBALS['phpgw']->acl->check($location, PHPGW_ACL_READ, $this->app_name))
					{
						$cats[] = $cat;
					}
				}
			}
			else
			{
				$cats = $_cats;
			}
			unset($_cats);

			$num_cats = count($cats);
			for ($i=0;$i < $num_cats;$i++)
			{
				$sub_select = ' AND cat_parent=' . $cats[$i]['id'] . ' AND cat_level=' . ($cats[$i]['level']+1);

				$this->db->query($sql . $sub_select . $ordermethod,__LINE__,__FILE__);
				$total += $this->db->num_rows();

				$_subcats = array();
				$subcats = array();

				while ($this->db->next_record())
				{
					$_subcats[] = array
					(
						'id'			=> (int)$this->db->f('cat_id'),
						'owner'			=> (int)$this->db->f('cat_owner'),
						'access'		=> $this->db->f('cat_access'),
						'app_name'		=> $this->db->f('cat_appname'),
						'main'			=> (int)$this->db->f('cat_main'),
						'level'			=> (int)$this->db->f('cat_level'),
						'parent'		=> (int)$this->db->f('cat_parent'),
						'name'			=> $this->db->f('cat_name'),
						'description'	=> $this->db->f('cat_description'),
						'data'			=> $this->db->f('cat_data'),
						'active'		=> (int)$this->db->f('active')
					);
				}

				if($use_acl)
				{
					foreach( $_subcats as $cat)
					{
						$location = "{$this->location}.category.{$cat['id']}";
						if ($GLOBALS['phpgw']->acl->check($location, PHPGW_ACL_READ, $this->app_name))
						{
							$subcats[] = $cat;
						}
					}
				}
				else
				{
					$subcats = $_subcats;
				}
				unset($_subcats);

				$num_subcats = count($subcats);
				if ($num_subcats != 0)
				{
					$newcats = array();
					for ($k = 0; $k <= $i; $k++)
					{
						$newcats[$k] = $cats[$k];
					}
					for ($k = 0; $k < $num_subcats; $k++)
					{
						$newcats[$k+$i+1] = $subcats[$k];
					}
					for ($k = $i+1; $k < $num_cats; $k++)
					{
						$newcats[$k+$num_subcats] = $cats[$k];
					}
					$cats = $newcats;
					$num_cats = count($cats);
				}
			}

			$this->total_records = $total;
			if ($limit)
			{
				$scats = array();
				$max = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
				$max = $max + $start;

				$k=0;
				for($i=$start;$i<$max;++$i)
				{
					if ( isset($cats[$i]) && 
						is_array($cats[$i]) )
					{
						$scats[$k] = $cats[$i];
						++$k;
					}
				}
				if(is_array($scats))
				{
					$cats = $scats;
				}
			}
			return $cats;
		}

		/**
		* Get single category
		*
		* @param integer $id Category id
		* @return array $cats Information for one category in index 0. Includes the fields:
		* 'id', 'owner', 'access', 'app_name', 'main', 'level', 'parent', 'name', 'description', 'data'
		*/
		function return_single($id = '')
		{
			$id = (int) $id;
			$this->db->query("SELECT * FROM phpgw_categories WHERE cat_id = {$id}",__LINE__,__FILE__);

			$cats = array();
			if ($this->db->next_record())
			{
				$cats[0] = array
				(
					'id'			=> $this->db->f('cat_id'),
					'owner'			=> $this->db->f('cat_owner'),
					'access'		=> $this->db->f('cat_access'),
					'app_name'		=> $this->db->f('cat_appname', true),
					'main'			=> $this->db->f('cat_main'),
					'level'			=> $this->db->f('cat_level'),
					'parent'		=> $this->db->f('cat_parent'),
					'name'			=> $this->db->f('cat_name', true),
					'description'	=> $this->db->f('cat_description', true),
					'data'			=> $this->db->f('cat_data'),
					'active'		=> (int)$this->db->f('active')
				);
			}

			return $cats;
		}

		/**
		* Return into a select box, list or other formats
		*
		* @param string|array $format Currently supports 'select' (select box) or 'list'
		* @param string $type Can be 'all' or something different
		* @param array|string $selected Cat_id (list) or array with cat_id values
		* @param boolean $globals True or False, includes the global phpgroupware categories or not
		* @param string $site_link URL
		* @return array Categories
		*/
		function formatted_list($format,$type='',$selected = '',$globals = False,$site_link = 'site', $use_acl = false)
		{
			return $this->formated_list($format,$type,$selected,$globals,$site_link,$use_acl);
		}

		function formated_list($format, $type='', $selected = '', $globals = False, $site_link = 'site', $use_acl = false)
		{
			$self = '';
			if(is_array($format))
			{
				$temp_format = $format['format'];
				$type = isset($format['type']) ? $format['type'] : 'all';
				$selected = isset($format['selected']) ? $format['selected'] : '';
				$self = isset($format['self']) ? $format['self'] : '';
				$globals = isset($format['globals']) ? $format['globals'] : true;
				$site_link = isset($format['site_link']) ? $format['site_link'] : 'site';
				$use_acl = isset($format['use_acl']) ? $format['use_acl'] : '';
				settype($format,'string');
				$format = $temp_format ? $temp_format : 'select';
				unset($temp_format);
			}

			if (!is_array($selected))
			{
				$selected = explode(',',$selected);
			}

			if ($type != 'all')
			{
				$cats = $this->return_array($type, 0, False, '', '', '',$globals, '', '', '', $use_acl);
			}
			else
			{
				$cats = $this->return_sorted_array(0, False, '', '', '',$globals, '', $use_acl);
			}

			$s = '';
			if ($format == 'select')
			{
				foreach ( $cats as $cat )
				{
					if ( $cat['id'] == $self )
					{
						continue;
					}

					$s .= '<option value="' . $cat['id'] . '"';
					if (in_array($cat['id'],$selected))
					{
						$s .= ' selected="selected"';
					}

/*
					$s .= '>';
					for ($j=0;$j<$cat['level'];++$j)
					{
						$s .= '&nbsp;';
					}
*/

					$s .= '>' . str_repeat('&nbsp;' , (int)$cat['level'] ) . $GLOBALS['phpgw']->strip_html($cat['name']);

					$s .= $GLOBALS['phpgw']->strip_html($cat['name']);
					if ($cat['app_name'] == 'phpgw')
					{
						$s .= '&nbsp;[' . lang('Global') . ']';
					}
					if ($cat['owner'] == '-1' && !$this->supress_info)
					{
						$s .= '&nbsp;[' . lang('Global') . '&nbsp;' . lang($this->app_name) . ($this->location?"::{$this->location}":'') . ']';
					}
					$s .= '</option>' . "\n";
				}
				return $s;
			}

			if ($format == 'list')
			{
				$space = '&nbsp;&nbsp;';

				$s  = '<table border="0" cellpadding="2" cellspacing="2">' . "\n";

				if ($this->total_records > 0)
				{
					for ($i=0;$i<count($cats);++$i)
					{
						$image_set = '&nbsp;';

						if (in_array($cats[$i]['id'],$selected))
						{
							$image_set = '<img src="' . PHPGW_IMAGES_DIR . '/roter_pfeil.gif" />';
						}

						if (($cats[$i]['level'] == 0) && !in_array($cats[$i]['id'],$selected))
						{
							$image_set = '<img src="' . PHPGW_IMAGES_DIR . '/grauer_pfeil.gif" />';
						}

						$space_set = str_repeat($space,$cats[$i]['level']);

						$s .= '<tr>' . "\n";
						$s .= '<td width="8">' . $image_set . '</td>' . "\n";
						$s .= '<td>' . $space_set . '<a href="' . $GLOBALS['phpgw']->link($site_link,'cat_id=' . $cats[$i]['id']) . '">'
							. $GLOBALS['phpgw']->strip_html($cats[$i]['name'])
							. '</a></td>' . "\n"
							. '</tr>' . "\n";
					}
				}
				$s .= '</table>' . "\n";
				return $s;
			}
		}


		function formatted_xslt_list($data=0)
		{
			if(is_array($data))
			{
				$format			= isset($data['format']) ? $data['format'] : 'filter';
				$type			= isset($data['type']) ? $data['type'] : 'all';
				$selected		= isset($data['selected']) ? $data['selected'] : '';
				$self			= isset($data['self']) ? $data['self'] : '';
				$globals		= isset($data['globals']) ? $data['globals'] : true;
				$link_data		= isset($data['link_data']) ? $data['link_data'] : array();
				$select_name	= isset($data['select_name'])?$data['select_name'] : 'cat_id';
				$use_acl		= isset($data['use_acl']) ? $data['use_acl'] : '';
			}
			else
			{
				return array();
			}

			if (!is_array($selected))
			{
				$selected = explode(',',$selected);
			}

			if ($type != 'all')
			{
				$cats = $this->return_array($type, 0, false, '', '', '', $globals, '', '', '', $use_acl);
			}
			else
			{
				$cats = $this->return_sorted_array(0, false, '', '', '', $globals, '', $use_acl);
			}

			$GLOBALS['phpgw']->xslttpl->add_file($GLOBALS['phpgw']->common->get_tpl_dir('phpgwapi','base') . '/categories');

			if($self)
			{
				for ($i=0;$i<count($cats);$i++)
				{
					if ($cats[$i]['id'] == $self)
					{
						unset($cats[$i]);
					}
				}
				reset($cats);
			}

			$cat_list = array();
			while (is_array($cats) && list(,$cat) = each($cats))
			{
				$sel_cat = '';
				if (in_array($cat['id'],$selected))
				{
					$sel_cat = 'selected';
				}

				$name = str_repeat(' . ' , (int)$cat['level'] ) . $GLOBALS['phpgw']->strip_html($cat['name']);

				if ($cat['app_name'] == 'phpgw')
				{
					$name .= ' [' . lang('Global') . ']';
				}
				if ($cat['owner'] == '-1' && !$this->supress_info)
				{
					$name .= ' [' . lang('Global') . ' ' . lang($this->app_name) . ($this->location?"::{$this->location}":'') . ']';
				}

				$cat_list[] = array
				(
					'cat_id'		=> $cat['id'],
					'name'			=> $name,
					'description'	=> $cat['description'],
					'selected'		=> $sel_cat
				);
			}

			$cat_data = array
			(
				'cat_list'				=> $cat_list,
				'lang_no_cat'			=> lang('no category'),
				'lang_cat_statustext'	=> lang('Select the category the data belong to. To do not use a category select NO CATEGORY'),
				'select_url'			=> $GLOBALS['phpgw']->link('/index.php', $link_data),
				'select_name'			=> $select_name,
				'lang_submit'			=> lang('submit')
			);
			return $cat_data;
		}


		/**
		* Add category
		*
		* @param array $values Array with the following fields: 'id', 'parent', 'level', 'main', 'descr', 'name', 'data', 'access'
		* @return integer Id for the new category
		*/
		function add($values)
		{
			$values['id']		= isset($values['id']) ? (int) $values['id'] : 0;
			$values['parent']	= isset($values['parent']) ? (int) $values['parent'] : 0;

			$values['level'] = 0;
			$values['main'] = 0;
			$values['active'] = (int) $values['active'];
			if ($values['parent'] > 0)
			{
				$values['level']	= (int) $this->id2name($values['parent'],'level')+1;
				$values['main']		= (int) $this->id2name($values['parent'],'main');
			}

			$values['descr'] = $this->db->db_addslashes($values['descr']);
			$values['name'] = $this->db->db_addslashes($values['name']);
			$values['data'] = isset($values['data']) ? $this->db->db_addslashes($values['data']) : '';
			
			$id_col = '';
			$id_val = '';
			if ($values['id'] > 0)
			{
				$id_col = 'cat_id,';
				$id_val = $values['id'] . ',';
			}

			$this->db->query("INSERT INTO phpgw_categories ($id_col cat_parent, cat_owner, cat_access, cat_appname, location_id, cat_name, cat_description, cat_data, cat_main ,cat_level, active, last_mod)"
				. " VALUES ($id_val {$values['parent']}, {$this->account_id}, '{$values['access']}', '{$this->app_name}',{$this->location_id},"
					."'{$values['name']}', '{$values['descr']}', '{$values['data']}', {$values['main']}, {$values['level']}, {$values['active']}," . time() . ')',__LINE__,__FILE__);

			if ($values['id'] > 0)
			{
				$max = $values['id'];
			}
			else
			{
				$max = $this->db->get_last_insert_id('phpgw_categories','cat_id');
			}

			$max = intval($max);
			if ($values['parent'] == 0)
			{
				$this->db->query('UPDATE phpgw_categories SET cat_main=' . $max . ' WHERE cat_id=' . $max,__LINE__,__FILE__);
			}
			
			$args = array
			(
				'cat_id'	=> $max,
				'cat_name'	=> $values['name'],
				'cat_owner'	=> $this->account_id,
				'location'	=> 'cat_add',
				'location_id' => $this->location_id
			);
			$GLOBALS['phpgw']->hooks->single($args, $this->app_name);

			return $max;
		}

		/**
		* Delete category
		*
		* @param integer $cat_id Category id
		* @param boolean $drop_subs Delete subcategroies
		* @param boolean $modify_subs Modify subcategories
		*/
		function delete($cat_id, $drop_subs = False, $modify_subs = False)
		{
			$cat_id = (int)$cat_id;

			$subdelete = '';
			if ($drop_subs)
			{
				$subdelete = ' OR cat_parent=' . $cat_id . ' OR cat_main=' . $cat_id; 
			}

			if ($modify_subs)
			{
				$cats = $this->return_sorted_array('',False,'','','',False, $cat_id);

				$new_parent = $this->id2name($cat_id,'parent');

				for ($i=0;$i<count($cats);$i++)
				{
					if ($cats[$i]['level'] == 1)
					{
						$this->db->query('UPDATE phpgw_categories set cat_level=0, cat_parent=0, cat_main=' . (int)$cats[$i]['id']
										. ' WHERE cat_id=' . (int)$cats[$i]['id'] . " AND cat_appname='" . $this->app_name . "'",__LINE__,__FILE__);//FIXME: should not be necesarry with appname
						$new_main = $cats[$i]['id'];
					}
					else
					{
						if ($new_main)
						{
							$update_main = ',cat_main=' . $new_main;
						}

						if ($cats[$i]['parent'] == $cat_id)
						{
							$update_parent = ',cat_parent=' . $new_parent;
						}

						$this->db->query('UPDATE phpgw_categories set cat_level=' . ($cats[$i]['level']-1) . $update_main . $update_parent 
										. ' WHERE cat_id=' . (int)$cats[$i]['id'] . " AND cat_appname='" . $this->app_name . "'",__LINE__,__FILE__);
					}
				}
			}

			$this->db->query('SELECT cat_id FROM phpgw_categories WHERE cat_id=' . $cat_id . $subdelete . " AND cat_appname='"
							. $this->app_name . "'",__LINE__,__FILE__);
			$_cats = array();
			while ($this->db->next_record())
			{
				$_cats[] = $this->db->f('cat_id');			
			}

			$this->db->query('DELETE FROM phpgw_categories WHERE cat_id=' . $cat_id . $subdelete . " AND cat_appname='"
							. $this->app_name . "'",__LINE__,__FILE__);
			
			foreach($_cats as $_cat_id)
			{
				$args = array
				(
					'cat_id'	=> $_cat_id,
					'cat_owner'	=> $this->account_id,
					'location'	=> 'cat_delete',
					'location_id' => $this->location_id
				);

				$GLOBALS['phpgw']->hooks->single($args, $this->app_name);
			}
		}

		/**
		* Edit a category
		*
		* @param array $values Array with the following fields: 'id', 'parent', 'old_parent', 'level', 'main', 'descr', 'name', 'data', 'access'
		* @return integer Category id
		*/
		function edit($values)
		{
			$values['id']		= intval($values['cat_id']);
			$values['parent']	= intval($values['parent']);

			//Sigurd feb 09: Seems like an error to me - will break relations to existing records
/*			if (isset($values['old_parent']) && intval($values['old_parent']) != $values['parent'])
			{
				$this->delete($values['id'],False,True);
				return $this->add($values);
			}
			else

*/
			{
				if ($values['parent'] > 0)
				{
					$values['main']  = intval($this->id2name($values['parent'],'main'));
					$values['level'] = intval($this->id2name($values['parent'],'level')+1);
				}
				else
				{
					$values['main']  = $values['id'];
					$values['level'] = 0;
				}
			}

			$values['descr'] = $this->db->db_addslashes($values['descr']);
			$values['name'] = $this->db->db_addslashes($values['name']);
			$values['data'] = isset($values['data']) ? $this->db->db_addslashes($values['data']) : '';

			$sql = "UPDATE phpgw_categories SET cat_name='" . $values['name'] . "', cat_description='" . $values['descr']
					. "', cat_data='" . $values['data'] . "', cat_parent=" . $values['parent'] . ", cat_access='"
					. $values['access'] . "', cat_main=" . $values['main'] . ', cat_level=' . $values['level'] . ',last_mod=' . time()
					. ', active = ' . (int) $values['active']
					. " WHERE cat_appname='" . $this->app_name . "' AND cat_id=" . $values['id'];

			$this->db->query($sql,__LINE__,__FILE__);
			
			$args = array
			(
				'cat_id'	=> $values['id'],
				'cat_name'	=> $values['name'],
				'cat_owner'	=> $this->account_id,
				'active'	=> (int) $values['active'],
				'location'	=> 'cat_edit',
				'location_id' => $this->location_id
			);

			$GLOBALS['phpgw']->hooks->single($args, $this->app_name);

			return $values['id'];
		}

		function name2id($cat_name)
		{
			$filter_location = '';
			if($this->location_id)
			{
				$filter_location = "AND location_id = {$this->location_id}";
			}

			$this->db->query("SELECT cat_id FROM phpgw_categories WHERE cat_name='" . $this->db->db_addslashes($cat_name) . "' "
							."AND cat_appname='{$this->app_name}' {$filter_location} AND (cat_owner=" . $this->account_id . ' OR cat_owner=-1)',__LINE__,__FILE__);

			if(!$this->db->num_rows())
			{
				return 0;
			}

			$this->db->next_record();

			return $this->db->f('cat_id');
		}

		function id2name($cat_id = '', $item = 'name')
		{
			$cat_id = intval($cat_id);
			if ($cat_id == 0)
			{
				return '--';
			}
			switch($item)
			{
				case 'name':	$value = 'cat_name'; break;
				case 'owner':	$value = 'cat_owner'; break;
				case 'main':	$value = 'cat_main'; break;
				case 'level':	$value = 'cat_level'; break;
				case 'parent':	$value = 'cat_parent'; break;
			}

			$this->db->query("SELECT $value FROM phpgw_categories WHERE cat_id=" . $cat_id,__LINE__,__FILE__);
			$this->db->next_record();

			if ($this->db->f($value))
			{
				return $this->db->f($value);
			}
			else
			{
				if ($item == 'name')
				{
					return '--';
				}
			}
		}

		/**
		* Return category name given $cat_id
		*
		* @param $cat_id Category id
		* @return string Category name
		* @deprecated This is only a temp wrapper, use id2name() to keep things matching across the board.
		*/
		function return_name($cat_id)
		{
			return $this->id2name($cat_id);
		}

		/**
		* Test if a category name exists
		*
		* @param $type Can be 'subs', 'mains', 'appandmains', 'appandsubs', 'noglobal' or 'noglobalapp'
		* @param $cat_name Category name
		* @param $cat_id Category ID
		* @return boolean True when the category exists otherwise false
		*/
		function exists($type, $cat_name = '', $cat_id = 0 )
		{
			if ( is_array($type) )
			{
				$real_type = $type['type'];
				$cat_id = $type['cat_id'];
				$cat_name = $type['cat_name'];
				$type = $real_type;
				unset($real_type);
			}
			
			$cat_id = (int) $cat_id;
			$filter = $this->filter($type);

			if ($cat_name)
			{
				$cat_exists = " cat_name='" . $this->db->db_addslashes($cat_name) . "' "; 
			}

			if ($cat_id)
			{
				$cat_exists = ' cat_parent=' . $cat_id;
			}

			if ($cat_name && $cat_id)
			{
				$cat_exists = " cat_name='" . $this->db->db_addslashes($cat_name) . "' AND cat_id != $cat_id ";
			}

			$this->db->query("SELECT COUNT(cat_id) as cnt FROM phpgw_categories WHERE $cat_exists $filter",__LINE__,__FILE__);

			$this->db->next_record();

			if ($this->db->f('cnt'))
			{
				return True;
			}
			else
			{
				return False;
			}
		}
		
		/**
		 * Sets the app to use for category look ups, handy for getting around recycling the class
		 * 
		 * @param string $appname the new app name
		 */
		function set_appname($appname, $location = '')
		{
			$this->app_name		= $GLOBALS['phpgw']->db->db_addslashes($appname);
			$this->location		= $GLOBALS['phpgw']->db->db_addslashes($location);
			$this->location_id	= $GLOBALS['phpgw']->locations->get_id($appname, $location);
			$this->grants		= $GLOBALS['phpgw']->acl->get_grants($appname, $location);
		}

		/**
		 * used for retrive the path for a particular node from a hierarchy
		 *
		 * @param integer $cat_id is the id of the node we want the path of
		 * @return array $path Path
		 */

		public function get_path($cat_id)
		{
			$cat_id = (int) $cat_id;
			$sql = "SELECT cat_parent, cat_name FROM phpgw_categories WHERE cat_id = {$cat_id}";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();

			$parent_id = $this->db->f('cat_parent');
			$name = $this->db->f('cat_name', true);
			$path = array
			(
				array
				(
					'id' => $cat_id,
					'name' => $name
				)
			);

			if ($parent_id)
			{
				$path = array_merge($this->get_path($parent_id), $path);
			}

			return $path;
		}
	}
