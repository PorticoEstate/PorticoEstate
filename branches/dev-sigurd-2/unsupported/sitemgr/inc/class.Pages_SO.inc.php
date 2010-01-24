<?php
	class Pages_SO
	{
		var $db;

		function Pages_SO()
		{
			$this->db = $GLOBALS['phpgw']->db;
		}

		
		//if $cats is an array, pages from this list are retrieved,
		//is $cats is an int, pages from this cat are retrieved,
		//if $cats is 0 or false, pages from currentcats are retrieved
		function getPageIDList($cats=False,$states=false)
		{
			if (!$states)
			{
				$states = $GLOBALS['Common_BO']->visiblestates;
			}

			$page_id_list = array();
			$cat_list = (is_array($cats) 
					? implode(',',$cats) 
					: ($cats 
						? $cats 
						: ($GLOBALS['Common_BO']->cats->currentcats 
							? implode(',',$GLOBALS['Common_BO']->cats->currentcats) 
							: false
						)
					)
				);
			if ($cat_list)
			{
				$sql = "SELECT page_id FROM phpgw_sitemgr_pages WHERE cat_id IN ($cat_list) ";
				if ($states)
				{
					$sql .= 'AND state in ('. implode(',',$states)  . ')';
				}
				$sql .=' ORDER BY cat_id, sort_order ASC'; 
				$this->db->query($sql,__LINE__,__FILE__);
				while ($this->db->next_record())
				{
					$page_id_list[] = $this->db->f('page_id');
				}
			}
			return $page_id_list;
		}

		function addPage($cat_id)
		{
			$this->db->query('INSERT INTO phpgw_sitemgr_pages (cat_id)'
					. ' VALUES (' . intval($cat_id) . ')',  __LINE__, __FILE__);
			return $this->db->get_last_insert_id('phpgw_sitemgr_pages','page_id');
		}

		function removePage($page_id)
		{
			$this->db->query('DELETE FROM phpgw_sitemgr_pages'
					. ' WHERE page_id=' . intval($page_id), __LINE__, __FILE__);

			$this->db->query('DELETE FROM phpgw_sitemgr_pages_lang'
					. ' WHERE page_id=' . intval($page_id),  __LINE__, __FILE__);
		}

		//this function should be a deprecated function - IMHO - skwashd
		function pageExists($page_name, $exclude_page_id='')
		{
			$page_id = $this->PagetoID($page_name);
			if($page_id)
			{
				return ($page_id != $exclude_page_id ? $page_id : False);
			}
			else
			{
				return False;
			}
		}


		function getlangarrayforpage($page_id)
		{
			$retval = array();
			$this->db->query('SELECT lang FROM phpgw_sitemgr_pages_lang'
					. ' WHERE page_id=' . intval($page_id), __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$retval[] = $this->db->f('lang');
			}
			return $retval;
		}

		function PagetoID($page_name)
		{
			$cats = CreateObject('phpgwapi.categories', -1, 'sitemgr');
			$cat_list = $cats->return_sorted_array(0, False, '', '', '', False, CURRENT_SITE_ID);
			
			if($cat_list)
			{
				foreach($cat_list as $null => $val)
				{
					$site_cats[] = $val['id'];
				}
			}
			
			$sql  = 'SELECT page_id FROM phpgw_sitemgr_pages '
				. " WHERE name='" . $this->db->db_addslashes($page_name) . "' ";
			if($site_cats)
			{
				$sql .= 'AND cat_id IN(' . implode(',', $site_cats) . ')';
			}

			$this->db->query($sql,__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				return $this->db->f('page_id');
			}
			else
			{
				return false;
			}
		}

		function getcatidforpage($page_id)
		{
			$this->db->query('SELECT cat_id FROM phpgw_sitemgr_pages'
				. ' WHERE page_id = ' . intval($page_id), __LINE__, __FILE__);
			if ($this->db->next_record())
 			{
				return $this->db->f('cat_id');
			}
			else
			{
				return false;
			}
		}

		function getPage($page_id,$lang=False)
		{
			$this->db->query('SELECT * FROM phpgw_sitemgr_pages'
					. ' WHERE page_id=' . intval($page_id), __LINE__, __FILE__);
			if ($this->db->next_record())
			{
				$page = CreateObject('sitemgr.Page_SO', True);
				$page->id = $page_id;
				$page->cat_id = $this->db->f('cat_id');
				$page->sort_order = (int) $this->db->f('sort_order');
				$page->name = stripslashes($this->db->f('name'));
				$page->hidden = $this->db->f('hide_page');
				$page->state = $this->db->f('state');
				
				if ($lang)
				{
					$this->db->query('SELECT * FROM phpgw_sitemgr_pages_lang'
							. ' WHERE page_id=' . intval($page_id)
							. " AND lang='" . $this->db->db_addslashes($lang) . "'"
						, __LINE__, __FILE__);
				
					if ($this->db->next_record())
					{
						$page->title= stripslashes($this->db->f('title'));
						$page->subtitle = stripslashes($this->db->f('subtitle'));
						$page->lang = $lang;
					}
					else
					{
						$page->title = lang("not yet translated");
					}
				}
				
				//if there is no lang argument we return the content in whatever languages turns up first 
				else
				{
					$this->db->query('SELECT * FROM phpgw_sitemgr_pages_lang'
							. ' WHERE page_id=' . intval($page->id), __LINE__, __FILE__);
				
					if ($this->db->next_record())
					{
						$page->title= stripslashes($this->db->f('title'));
						$page->subtitle = stripslashes($this->db->f('subtitle'));
						$page->lang = $this->db->f('lang');
					}
					else
					{
						$page->title = "This page has no data in any langugage: this should not happen";
					}
				}

				return $page;
			}
			else
			{
				return false;
			}
		}

		function savePageInfo($pageInfo)
		{
			$this->db->query('UPDATE phpgw_sitemgr_pages SET '
					. 'cat_id=' . intval($pageInfo->cat_id) . ', '
					. 'name=\'' . $this->db->db_addslashes($pageInfo->name) . '\', '
					. 'sort_order=' . intval($pageInfo->sort_order) . ', '
					. 'hide_page=' . intval($pageInfo->hidden) . ', '
					. 'state=' . intval($pageInfo->state) . ' '
					. 'WHERE page_id=' . intval($pageInfo->id) . '', __LINE__, __FILE__);
			return true;
		}
		
		function savePageLang($pageInfo,$lang)
		{
			$page_id = $pageInfo->id;
			$this->db->query('SELECT * FROM phpgw_sitemgr_pages_lang'
					. ' WHERE page_id=' . intval($page_id) 
					. " AND lang='" . $this->db->db_addslashes($lang). "'", __LINE__,__FILE__);
			if ($this->db->next_record())
			{
				$this->db->query('UPDATE phpgw_sitemgr_pages_lang SET '
						. " title='" . $this->db->db_addslashes($pageInfo->title) . "',"
						. " subtitle='" . $this->db->db_addslashes($pageInfo->subtitle) . "'"
						. ' WHERE page_id=' . intval($page_id) 
						. " AND lang='" . $this->db->db_addslashes($lang) . "'"
					, __LINE__,__FILE__);
				return true;
			}
			else
			{
				$this->db->query('INSERT INTO phpgw_sitemgr_pages_lang (page_id,lang,title,subtitle)'
						. ' VALUES ('. intval($page_id) . ','
						. "'" . $this->db->db_addslashes($lang) . "'," 
						. "'" . $this->db->db_addslashes($pageInfo->title) . "',"
						. "'" . $this->db->db_addslashes($pageInfo->subtitle) . "')"
					, __LINE__,__FILE__);
				return true;
			}
		}

		function removealllang($lang)
		{
			$this->db->query('DELETE FROM phpgw_sitemgr_pages_lang '
					. " WHERE lang='" . $this->db->db_addslashes($lang) . "'", __LINE__, __FILE__);
		}

		function migratealllang($oldlang,$newlang)
		{
			$this->db->query('UPDATE phpgw_sitemgr_pages_lang SET'
					. " lang='" . $this->db->db_addslashes($newlang) . "'"
					. " WHERE lang='" . $this->db->db_addslashes($oldlang) . "'", __LINE__, __FILE__);
		}

		function commit($page_id)
		{
			$this->db->query('UPDATE phpgw_sitemgr_pages SET'
					. ' state = ' . SITEMGR_STATE_PUBLISH 
					. ' WHERE state = ' . SITEMGR_STATE_PREPUBLISH 
					. ' AND page_id = ' . intval($page_id), __LINE__, __FILE__);

			$this->db->query('UPDATE phpgw_sitemgr_pages SET'
					. ' state = ' . SITEMGR_STATE_ARCHIVE 
					. ' WHERE state = ' . SITEMGR_STATE_PREUNPUBLISH 
					. ' AND page_id = ' . intval($page_id), __LINE__, __FILE__);
		}

		function reactivate($page_id)
		{
			$this->db->query('UPDATE phpgw_sitemgr_pages SET'
					. ' state = ' . SITEMGR_STATE_DRAFT 
					. ' WHERE state = ' . SITEMGR_STATE_ARCHIVE 
					. ' AND page_id = ' . intval($page_id), __LINE__, __FILE__);
		}
	}
?>
