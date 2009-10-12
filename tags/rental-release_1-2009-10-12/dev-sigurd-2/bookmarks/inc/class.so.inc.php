<?php
	/**
	* phpGroupWare - Bookmarks
	* http://www.phpgroupware.org
	* @author Joseph Engo
	* @author Michael Totschnig
	* @copyright Copyright (C) 2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package bookmarks
	* @version $Id$
	* @internal Based on Bookmarker, Copyright (C) 1998 Padraic Renaghan, http://www.renaghan.com/bookmarker
	* @internal Ported to phpGroupWare by Joseph Engo
	* @internal Ported to three-layered design by Michael Totschnig
	*/

	/**
	 * Bookmarks storage object class
	 * 
	 * @package bookmarks
	 */
	class bookmarks_so
	{
		var $db;
		var $total_records;

		function bookmarks_so()
		{
			$this->db =& $GLOBALS['phpgw']->db;
		}

		function _list($cat_list,$public_user_list,$start,$where_clause)
		{
			$filtermethod = '( bm_owner=' . $GLOBALS['phpgw_info']['user']['account_id'];
			if ($public_user_list)
			{
				$filtermethod .= " OR (bm_access='public' AND bm_owner in(" . implode(',',$public_user_list) . ')))';
			}
			else
			{
				$filtermethod .= ' )';
			}
			$query = sprintf('SELECT * FROM phpgw_bookmarks WHERE %s',$filtermethod);
			if ($cat_list)
			{
				$where_clause .= " bm_category IN (" . implode(',',$cat_list) .") ";
			}
			if ($where_clause)
			{
				$where_clause_sql = ' AND ' . $where_clause;
			}
			else
			{
				$where_clause_sql = ' ';
			}
			$query .= $where_clause_sql . ' order by bm_category, bm_name';

			$this->db->query($query,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			if ($start !== False)
			{
				$this->db->limit_query($query,$start,__LINE__,__FILE__);
			}

			$result = array();
			while ($this->db->next_record())
			{
				$bookmark['name'] = $GLOBALS['phpgw']->strip_html($this->db->f('bm_name'));
				$bookmark['url'] = $GLOBALS['phpgw']->strip_html($this->db->f('bm_url'));
				$bookmark['desc'] = $GLOBALS['phpgw']->strip_html($this->db->f('bm_desc'));
				$bookmark['keywords'] = $GLOBALS['phpgw']->strip_html($this->db->f('bm_keywords'));
				$bookmark['owner'] = $this->db->f('bm_owner');
				$bookmark['access'] = $this->db->f('bm_access');
				$bookmark['category'] = $this->db->f('bm_category');
				$bookmark['rating'] = $this->db->f('bm_rating');
				$bookmark['visits'] = $this->db->f('bm_visits');
				$bookmark['info'] = $this->db->f('bm_info');
				$result[$this->db->f('bm_id')] = $bookmark;
			}
			return $result;
		}

		function read($id)
		{
			$query = "SELECT * FROM phpgw_bookmarks WHERE bm_id=$id";
			$this->db->query($query);
			if ($this->db->next_record())
			{
				$bookmark['name'] = $GLOBALS['phpgw']->strip_html($this->db->f('bm_name'));
				$bookmark['url'] = $GLOBALS['phpgw']->strip_html($this->db->f('bm_url'));
				$bookmark['desc'] = $GLOBALS['phpgw']->strip_html($this->db->f('bm_desc'));
				$bookmark['keywords'] = $GLOBALS['phpgw']->strip_html($this->db->f('bm_keywords'));
				$bookmark['owner'] = $this->db->f('bm_owner');
				$bookmark['access'] = $this->db->f('bm_access');
				$bookmark['category'] = $this->db->f('bm_category');
				$bookmark['rating'] = $this->db->f('bm_rating');
				$bookmark['visits'] = $this->db->f('bm_visits');
				$bookmark['info'] = $this->db->f('bm_info');
				return $bookmark;
			}
			else
			{
				return false;
			}
		}

		function exists($url)
		{
			$query = sprintf("select count(*) from phpgw_bookmarks where bm_url='%s' and bm_owner='%s'",$url, $GLOBALS['phpgw_info']['user']['account_id']);
			$this->db->query($query,__LINE__,__FILE__);
			$this->db->next_record();

			return (bool)$this->db->f(0);
		}

		function add($values)
		{
			if ( !isset($values['access']) || !$values['access'] )
			{
				$values['access'] = 'public';
			}

			if ( !isset($values['timestamps']) || !$value['timestamps'] )
			{
				$values['timestamps'] = time() . ',0,0';
			}

			$query = sprintf("insert into phpgw_bookmarks (bm_url, bm_name, bm_desc, bm_keywords, bm_category,"
				. "bm_rating, bm_owner, bm_access, bm_info, bm_visits) "
				. "values ('%s','%s','%s','%s',%s,%s,'%s','%s','%s',0)", 
				$this->db->db_addslashes($values['url']), $this->db->db_addslashes($values['name']), 
				$this->db->db_addslashes($values['desc']), $this->db->db_addslashes($values['keywords']),
				(int)$values['category'], (int)$values['rating'], (int)$GLOBALS['phpgw_info']['user']['account_id'], 
				$this->db->db_addslashes($values['access']), $values['timestamps']);

			if ($this->db->query($query,__LINE__,__FILE__))
			{
				return $this->db->get_last_insert_id('phpgw_bookmarks','bm_id');
			}
			else
			{
				return false;
			}
		}

		function update($id, $values)
		{
			if (! $values['access'])
			{
				$values['access'] = 'public';
			}

			$this->db->query("select bm_info from phpgw_bookmarks where bm_id='$id'",__LINE__,__FILE__);
			$this->db->next_record();
			$ts = explode(',',$this->db->f('bm_info'));
	
			$timestamps = sprintf('%s,%s,%s',$ts[0],$ts[1],time());

			// Update bookmark information.
			$query = sprintf("update phpgw_bookmarks set bm_url='%s', bm_name='%s', bm_desc='%s', "
//				. "bm_keywords='%s', bm_category='%s', bm_subcategory='%s', bm_rating='%s',"
				. "bm_keywords='%s', bm_category=%s, bm_rating='%s',"
				. "bm_info='%s', bm_access='%s' where bm_id='%s'", 
					$values['url'], addslashes($values['name']), addslashes($values['desc']), addslashes($values['keywords']), 
//					$category, $subcategory, $values['rating'], $timestamps, $values['access'], $id);
					$values['category'],$values['rating'], $timestamps, $values['access'], $id);

			if ($this->db->query($query,__LINE__,__FILE__))
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		function updatetimestamp($id,$timestamp)
		{
			$query = "UPDATE phpgw_bookmarks SET bm_info='$timestamp', bm_visits=bm_visits+1 WHERE bm_id=$id";
			$this->db->query($query,__LINE__,__FILE__);
		}

		function delete($id)
		{
			$query = "delete from phpgw_bookmarks where bm_id=$id";
			$this->db->query($query,__LINE__,__FILE__);
			if ($this->db->Errno != 0)
			{
				return False;
			}
			return true;
		}
	}
