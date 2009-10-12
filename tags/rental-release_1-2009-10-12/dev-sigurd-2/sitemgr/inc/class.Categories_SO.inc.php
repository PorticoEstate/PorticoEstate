<?php
	class Categories_SO
	{
		var $cats;
		var $db;
		var $site_id;
		
		function Categories_SO()
		{
			$this->cats = CreateObject('phpgwapi.categories',-1,'sitemgr');
			$this->db = $GLOBALS['phpgw']->db;			 
		}

		function isactive($cat_id,$states=false)
		{
			if (!$states)
			{
				$states = $GLOBALS['Common_BO']->visiblestates;
			}
			$this->db->query('SELECT cat_id from phpgw_sitemgr_categories_state '
				. ' WHERE cat_id = ' . intval($cat_id) 
				. ' AND state IN ( ' . implode(',',$states) . ')', __LINE__, __FILE__);

			return $this->db->next_record();
		}

		function getChildrenIDList($parent)
		{
			$cats = $this->cats->return_array('all','',False,'','','cat_data',False,$parent,-1,'id');
			$result = array();

			if(is_array($cats))
			{
				foreach($cats as $ign => $subs)
				{
					$result[] = $subs['id'];
				}
			}
			return $result;
		}

		function addCategory($name, $description, $parent = False)
		{
			$data = array
			(
				'name'		=> $name,
				'descr'		=> $description,
				'access'	=> 'public',
				'parent'	=> $parent,
				'old_parent' => $parent
			);
			$cat_id =  $this->cats->add($data);
			$this->db->query('INSERT INTO phpgw_sitemgr_categories_state (cat_id)'
					. ' VALUES (' . intval($cat_id) . ')', __LINE__, __FILE__);
			return $cat_id;
		}

		function removeCategory($cat_id)
		{
			$cat_id = intval($cat_id);
			$this->cats->delete($cat_id,False,True);
			$this->db->query('DELETE FROM phpgw_sitemgr_categories_lang'
					. " WHERE cat_id = $cat_id", __LINE__, __FILE__);
			
			$this->db->query('DELETE FROM phpgw_sitemgr_categories_state'
					. " WHERE cat_id = $cat_id", __LINE__, __FILE__);
			return True;
		}

		function saveCategory($cat_info)
		{
			$data = array
			(
				'name'		=> $cat_info->name,
				'descr'		=> $cat_info->description,
				'data'		=> intval($cat_info->sort_order),
				'access'	=> 'public',
				'id'		=> $cat_info->id,
				'parent'	=> $cat_info->parent,
				'old_parent'	=> $cat_info->old_parent
			);
			$this->cats->edit($data);
			$this->db->query('UPDATE phpgw_sitemgr_categories_state'
					. ' SET state = ' . intval($cat_info->state) 
					. ' WHERE cat_id = ' . intval($cat_info->id), __LINE__,__FILE__);
		}

		function saveCategoryLang($cat_id, $cat_name, $cat_description, $lang)
		{
			$this->db->query('SELECT * FROM phpgw_sitemgr_categories_lang '
					. 'WHERE cat_id=' . intval($cat_id) 
					." AND lang='" . $this->db->db_addslashes($lang) . "'", __LINE__,__FILE__);
			if ($this->db->next_record())
			{
				$this->db->query('UPDATE phpgw_sitemgr_categories_lang ' 
						. "SET name='" . $this->db->db_addslashes($cat_name) . "', "
						. " description='" . $this->db->db_addslashes($cat_description) . "' "
						. 'WHERE cat_id=' . intval($cat_id) 
						. " AND lang='" . $this->db->db_addslashes($lang) . "'", __LINE__,__FILE__);
			}
			else
			{
				$this->db->query('INSERT INTO phpgw_sitemgr_categories_lang (cat_id,lang,name,description) '
						. 'VALUES (' . intval($cat_id) . ','
						. "'" . $this->db->db_addslashes($lang) . "',"
						. "'" . $this->db->db_addslashes($cat_name) . "',"
						. "'" . $this->db->db_addslashes($cat_description) 
						. "')", __LINE__,__FILE__);
			}
		}

		function getlangarrayforcategory($cat_id)
		{
			$retval = array();
			$this->db->query('SELECT lang FROM phpgw_sitemgr_categories_lang'
					. ' WHERE cat_id=' . intval($cat_id), __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$retval[] = $this->db->f('lang');
			}
			return $retval;
		}

		function getCategory($cat_id,$lang=False)
		{
			$cat = $this->cats->return_single($cat_id);

			if (is_array($cat))
			{
				$cat_info		= CreateObject('sitemgr.Category_SO', True);
				$cat_info->id		= $cat[0]['id'];
				//$cat_info->name	= stripslashes($cat[0]['name']);
				$cat_info->sort_order	= $cat[0]['data'];
				//$cat_info->description= stripslashes($cat[0]['description']);
				$cat_info->parent	= $cat[0]['parent'];
				$cat_info->depth	= $cat[0]['level'];
				$cat_info->root		= $cat[0]['main'];

				$this->db->query('SELECT state FROM phpgw_sitemgr_categories_state'
						. ' WHERE cat_id=' . intval($cat_id), __LINE__, __FILE__);
				$cat_info->state = $this->db->next_record() ? $this->db->f('state') : 0;

				if ($lang)
				{
					$this->db->query('SELECT * FROM phpgw_sitemgr_categories_lang'
							. ' WHERE cat_id=' . intval($cat_id) 
							. " AND lang='" . $this->db->db_addslashes($lang) 
							. "'", __LINE__, __FILE__);

					if ($this->db->next_record())
					{
						$cat_info->name = $this->db->f('name');
						$cat_info->description = $this->db->f('description');
					}
// 					else
// 					{
// 						//return False;
// 						$cat_info->name	= lang("not yet translated");
// 					}
				}

				//if there is no lang argument we return the content in whatever languages turns up first
				else
				{
					$this->db->query('SELECT * FROM phpgw_sitemgr_categories_lang'
							. ' WHERE cat_id=' . intval($cat_id), __LINE__, __FILE__);
					if ($this->db->next_record())
					{
						$cat_info->name	= $this->db->f('name');
						$cat_info->description = $this->db->f('description');
						$cat_info->lang = $this->db->f('lang');
					}
					else
					{
						$cat_info->name = "This category has no data in any langugage: this should not happen";
					}
				}
				
				return $cat_info;
			}
			else
			{
				return false;
			}
		}

		function removealllang($lang)
		{
			$this->db->query('DELETE FROM phpgw_sitemgr_categories_lang '
					. "WHERE lang='" . $this->db->db_addslashes($lang), __LINE__, __FILE__);
		}

		function migratealllang($oldlang,$newlang)
		{
			$this->db->query('UPDATE phpgw_sitemgr_categories_lang'
					. " SET lang='" . $this->db->db_addslashes($newlang) . "'"
					. " WHERE lang='" . $this->db->db_addslashes($oldlang) . "'", __LINE__,__FILE__);
		}

		function commit($cat_id)
		{
			$this->db->query('UPDATE phpgw_sitemgr_categories_state'
					. ' SET state = ' . SITEMGR_STATE_PUBLISH 
					. ' WHERE state = ' . SITEMGR_STATE_PREPUBLISH 
					. ' AND cat_id = ' . intval($cat_id), __LINE__, __FILE__);
			$this->db->query('UPDATE phpgw_sitemgr_categories_state '
					. ' SET state = ' . SITEMGR_STATE_ARCHIVE 
					. ' WHERE state = ' . SITEMGR_STATE_PREUNPUBLISH 
					. ' AND cat_id = ' . intval($cat_id), __LINE__, __FILE__);
		}

		function reactivate($cat_id)
		{
			$this->db->query('UPDATE phpgw_sitemgr_categories_state'
					. ' SET state = ' . SITEMGR_STATE_DRAFT 
					. ' WHERE state = ' . SITEMGR_STATE_ARCHIVE 
					. ' AND cat_id = ' . intval($cat_id), __LINE__, __FILE__);
		}
	}
?>
