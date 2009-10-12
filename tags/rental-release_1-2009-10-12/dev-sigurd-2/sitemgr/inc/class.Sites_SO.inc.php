<?php
	class Sites_SO
	{
		var $db;
		
		function Sites_SO()
		{
			$this->db = $GLOBALS['phpgw']->db;
		}

		function list_siteids()
		{
			$result = array();
			
			$this->db->query('SELECT site_id FROM phpgw_sitemgr_sites', __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$result[] = $this->db->f('site_id');
			}
			return $result;
		}

		function getWebsites($limit,$start,$sort,$order,$query,&$total)
		{
			if ($limit)
			{
				if (!$sort)
				{
					$sort = 'DESC';
				}
				if ($query)
				{
					$query = $this->db->db_addslashes($query);
					$whereclause = "WHERE site_name LIKE '%$query%'"
						. "OR site_url LIKE '%$query%'"
						. "OR site_dir LIKE '%$query%'";
				}
				if ($order)
				{
					$orderclause = 'ORDER BY ' . $this->db->db_addslashes($order) 
							. ' ' . $this->db->db_addslashes($sort);
				}
				else
				{
					$orderclause = 'ORDER BY site_name ASC';
				}
				$sql = "SELECT site_id,site_name,site_url from phpgw_sitemgr_sites $whereclause $orderclause";	
				$this->db->query($sql,__LINE__,__FILE__);
				$total = $this->db->num_rows();
				$this->db->limit_query($sql,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query('SELECT site_id,site_name,site_url FROM phpgw_sitemgr_sites', __LINE__, __FILE__);
			}
			while ($this->db->next_record())
			{
				foreach(array('site_id', 'site_name', 'site_url') as $col)
				{
					$site[$col] = $this->db->f($col);
				}
				$result[$site['site_id']] = $site;
			}
			return $result;
		}

		function getnumberofsites()
		{
			$this->db->query('SELECT COUNT(*) FROM phpgw_sitemgr_sites',__LINE__,__FILE__);
			$this->db->next_record();
			return $this->db->f(0);
		}

		function urltoid($url)
		{
			$sql  = 'SELECT site_id FROM phpgw_sitemgr_sites ';
			$sql .= "WHERE site_url ='" . $this->db->db_addslashes($url) . "'";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			return $this->db->f('site_id');
		}

		function read($id)
		{
			$sql =  'SELECT * FROM phpgw_sitemgr_sites ';
			$sql .= 'WHERE site_id = ' . intval($id);
			$this->db->query($sql,__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				foreach(
					array(
						'site_id', 'site_name', 'site_url', 'site_dir', 'themesel', 
						'site_languages', 'home_page_id', 'anonymous_user','anonymous_passwd'
					) as $col
				)
				{
					$site[$col] = $this->db->f($col);
				}
				return $site;
			}
			else
			{
				return false;
			}
		}

		function read2($id)
		{
			$sql  = 'SELECT site_url,site_dir FROM phpgw_sitemgr_sites ';
			$sql .= 'WHERE site_id = ' . intval($id);
			$this->db->query($sql,__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				foreach(
					array(
						'site_url', 'site_dir'
					) as $col
				)
				{
					$site[$col] = $this->db->f($col);
				}
				return $site;
			}
			else
			{
				return false;
			}
		}

		function add($site)
		{
			$cats = CreateObject('phpgwapi.categories',-1,'sitemgr');
				$data = array
			(
				'name'		=> $site['name'],
				'descr'		=> '',
				'access'	=> 'public',
				'parent'	=> 0,
				'old_parent' => 0
			);
			$site_id =  $cats->add($data);
			$sql = "INSERT INTO phpgw_sitemgr_sites (site_id,site_name,site_url,site_dir,anonymous_user,anonymous_passwd) VALUES ($site_id,'" . 
				$site['name'] . "','" . $site['url'] . "','" . $site['dir'] . "','" . $site['anonuser'] . "','" . $site['anonpasswd'] .
				"')";
			$this->db->query($sql,__LINE__,__FILE__);
			return $site_id;
		}

		function update($id,$site)
		{
			$sql = "UPDATE phpgw_sitemgr_sites SET site_name = '" . $site['name'] . "', site_url = '" . $site['url'] . "', site_dir = '" . 
				$site['dir'] . "', anonymous_user = '" . $site['anonuser'] . "', anonymous_passwd = '" . $site['anonpasswd'] . 
				"' WHERE site_id = $id";
			 $this->db->query($sql,__LINE__,__FILE__);
		}

		function delete($id)
		{
			$sql = "DELETE FROM phpgw_sitemgr_sites WHERE site_id = $id";
			$this->db->query($sql,__LINE__,__FILE__);
		}

		function saveprefs($prefs)
		{
			$sql = "UPDATE phpgw_sitemgr_sites SET themesel = '" . $prefs['themesel'] . "', site_languages = '" . $prefs['site_languages'] .
				"', home_page_id = " . $prefs['home_page_id'] . " WHERE site_id = " . CURRENT_SITE_ID;
			$this->db->query($sql,__LINE__,__FILE__);
		}
	}
