<?php

	class Content_SO
	{
		var $db;

		function Content_SO()
		{
			$this->db = $GLOBALS['phpgw']->db;
		}

		function addblock($block)
		{
			if (!$block->cat_id)
			{
				$block->cat_id = 0;
			}
			if (!$block->page_id)
			{
				$block->page__id = 0;
			}
			$this->db->query('INSERT INTO phpgw_sitemgr_blocks (area,module_id,page_id,'
						. 'cat_id,sort_order,viewable) '
					. "VALUES ('" .	$this->db->db_addslashes($block->area) . "'," 
						. intval($block->module_id) . "," 
						. intval($block->page_id) . "," 
						. intval($block->cat_id) . ',0,0)', __LINE__, __FILE__);
			return $this->db->get_last_insert_id('phpgw_sitemgr_blocks','block_id');
		}

		function createversion($blockid)
		{
			return $this->db->query('INSERT INTO phpgw_sitemgr_content (block_id,state)'
						. ' VALUES (' . intval($blockid) . ',' . SITEMGR_STATE_DRAFT  
						. ')', __LINE__, __FILE__);
		}

		function deleteversion($id)
		{
			$sql = 'DELETE FROM phpgw_sitemgr_content WHERE version_id = ' . intval($id);
			if ($this->db->query($sql,__LINE__,__FILE__))
 			{
				return $this->db->query('DELETE FROM phpgw_sitemgr_content_lang'
					. ' WHERE version_id = ' . intval($id), __LINE__,__FILE__);
 			}
			else
			{
				return false;
			}
		}

		function getblockidforversion($versionid)
		{
			$this->db->query('SELECT block_id FROM phpgw_sitemgr_content'
					. ' WHERE version_id = ' . intval($versionid), __LINE__, __FILE__);
			return $this->db->next_record() ? $this->db->f('block_id') : false;
		}

		function removeblock($id)
		{
			$sql = 'DELETE FROM phpgw_sitemgr_blocks WHERE block_id = '.  intval($id);
 			if ($this->db->query($sql,__LINE__,__FILE__))
 			{
				return $this->db->query('DELETE FROM phpgw_sitemgr_blocks_lang'
							. ' WHERE block_id = ' . intval($id), __LINE__, __FILE__);
 			}
			else
			{
				return false;
			}
		}

		function getblocksforscope($cat_id,$page_id)
		{
			$block = CreateObject('sitemgr.Block_SO',True);

			$this->db->query('SELECT t1.block_id,t1.module_id,module_name,area'
					. ' FROM phpgw_sitemgr_blocks AS t1,phpgw_sitemgr_modules AS t2'
					. ' WHERE t1.module_id = t2.module_id'
					. ' AND cat_id = ' . intval($cat_id)
					. ' AND page_id = ' . intval($page_id)
					. ' ORDER by sort_order', __LINE__, __FILE__);

			$result = array();

			while ($this->db->next_record())
			{
				$id = $this->db->f('block_id');
				$block->id = $id;
				$block->module_id = $this->db->f('module_id');
				$block->module_name = $this->db->f('module_name');
				$block->area = $this->db->f('area');
				$result[$id] = $block;
			}
			return $result;
		}

		function getallblocksforarea($area,$cat_list,$page_id,$lang)
		{
			$sql = 'SELECT t1.block_id, area, cat_id, page_id, t1.module_id, '
					. 'module_name, sort_order, title, viewable'
				. ' FROM phpgw_sitemgr_blocks AS t1 '
				. ' LEFT JOIN phpgw_sitemgr_modules AS t2 ON t1.module_id=t2.module_id'
				. ' LEFT JOIN phpgw_sitemgr_blocks_lang AS t3 ON (t1.block_id=t3.block_id'
					. " AND lang='" . $this->db->db_addslashes($lang) . "') "
				. " WHERE area = '" . $this->db->db_addslashes($area) . "'"
				. ' AND ((page_id = 0 and cat_id = '. CURRENT_SITE_ID  . ')';

			if ($cat_list)
			{
				$sql .= " OR (page_id = 0 AND cat_id IN (" . implode(',',$cat_list) . "))";
			}
			if ($page_id)
			{
				$sql .= " OR (page_id = $page_id) ";
			}
			$sql .= ") ORDER by sort_order";

			$block = CreateObject('sitemgr.Block_SO',True);
			$result = array();
			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$id = $this->db->f('block_id');
				$block->id = $id;
				$block->area = $this->db->f('area');
				$block->cat_id = $this->db->f('cat_id');
				$block->page_id = $this->db->f('page_id');
				$block->module_id = $this->db->f('module_id');
				$block->module_name = $this->db->f('module_name');
				$block->sort_order = $this->db->f('sort_order');
				$block->title = stripslashes($this->db->f('title'));
				$block->view = $this->db->f('viewable');
				$result[$id] = $block;
			}
			return $result;
		}

		function getversionidsforblock($blockid)
		{
			$this->db->query('SELECT version_id FROM phpgw_sitemgr_content'
					. ' WHERE block_id = ' . intval($blockid), __LINE__, __FILE__);
			$result = array();

			while ($this->db->next_record())
			{
				$result[] = $this->db->f('version_id');
			}
			return $result;
		}


		function getallversionsforblock($blockid,$lang)
		{
			$this->db->query('SELECT t1.version_id, arguments,arguments_lang,state'
					. ' FROM phpgw_sitemgr_content AS t1'
					. ' LEFT JOIN phpgw_sitemgr_content_lang AS t2 ON (t1.version_id=t2.version_id'
						. " AND lang = '" . $this->db->db_addslashes($lang) . "')"
					. ' WHERE block_id =' . intval($blockid), __LINE__, __FILE__);

			$result = array();

			while ($this->db->next_record())
			{
				$id = $this->db->f('version_id');
 				$version['arguments'] = array_merge(
 					unserialize(stripslashes($this->db->f('arguments'))),
 					unserialize(stripslashes($this->db->f('arguments_lang')))
				);
				$version['state'] = $this->db->f('state');
				$version['id'] = $id;
				$result[$id] = $version;
			}
			return $result;
		}

		//selects all blocks from a given cat_list + site-wide blocks that are in given states
		function getallblocks($cat_list,$states)
		{
			$cat_list[] = CURRENT_SITE_ID;
			$block = CreateObject('sitemgr.Block_SO',True);

			$this->db->query('SELECT COUNT(state) AS cnt,t1.block_id,area,cat_id,page_id,viewable,state '
					. ' FROM phpgw_sitemgr_blocks AS t1,phpgw_sitemgr_content AS t2 '
					. ' WHERE t1.block_id=t2.block_id '
					. ' AND cat_id IN (' . implode(',',$cat_list) . ')'
					. ' AND state IN (' . implode(',',$states) .')'
					. ' GROUP BY t1.block_id,area,cat_id,page_id,viewable', __LINE__, __FILE__);

			$result = array();

			while ($this->db->next_record())
			{
				$id = $this->db->f('block_id');
				$block->id = $id;
				$block->area = $this->db->f('area');
				$block->cat_id = $this->db->f('cat_id');
				$block->page_id = $this->db->f('page_id');
//				$block->module_id = $this->db->f('module_id');
//				$block->module_name = $this->db->f('module_name');
				$block->view = $this->db->f('viewable');
				$block->state = $this->db->f('state');
				//in cnt we retrieve the numbers of versions that are commitable for a block,
				//i.e. if there are more than one, it should normally be a prepublished version 
				//that will replace a preunpublished version
				$block->cnt =  $this->db->f('cnt');
				$result[$id] = $block;
			}
			return $result;
		}

		function getvisibleblockdefsforarea($area,$cat_list,$page_id,$isadmin,$isuser)
		{
			$viewable = SITEMGR_VIEWABLE_EVERBODY  . ',';
			$viewable .= $isuser ? SITEMGR_VIEWABLE_USER : SITEMGR_VIEWABLE_ANONYMOUS;
			$viewable .= $isadmin ? (',' . SITEMGR_VIEWABLE_ADMIN) : '';

			$sql = 'SELECT t1.block_id,area,cat_id,page_id,t1.module_id,module_name,state,version_id'
				. ' FROM phpgw_sitemgr_blocks AS t1, phpgw_sitemgr_modules AS t2,' 
					. ' phpgw_sitemgr_content AS t3'
				. ' WHERE t1.module_id = t2.module_id'
				. " AND t1.block_id=t3.block_id AND area = '" . $this->db->db_addslashes($area) . "'" 
				. ' AND  ((page_id = 0 and cat_id = '. CURRENT_SITE_ID  . ')';
			if ($cat_list)
			{
				$sql .= ' OR (page_id = 0 AND cat_id IN (' . implode(',',$cat_list) . '))';
			}
			if ($page_id)
			{
				$sql .= ' OR (page_id = ' . intval($page_id) . ')';
			}
			$sql .= ') AND viewable IN (' . $viewable . ') '
				. ' AND state IN (' . implode(',',$GLOBALS['Common_BO']->visiblestates) . ')'
				. ' ORDER by sort_order';

			$block = CreateObject('sitemgr.Block_SO',True);
			$result = array();

			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$id = $this->db->f('block_id');
				$block->id = $id;
				$block->area = $this->db->f('area');
				$block->cat_id = $this->db->f('cat_id');
				$block->page_id = $this->db->f('page_id');
				$block->module_id = $this->db->f('module_id');
				$block->module_name = $this->db->f('module_name');
				$block->view = $this->db->f('viewable');
				$block->state = $this->db->f('state');
				$block->version = $this->db->f('version_id');
				$result[$id] = $block;
			}
			return $result;
		}

		function getlangarrayforblocktitle($block_id)
		{
			$retval = array();
			$this->db->query('SELECT lang FROM phpgw_sitemgr_blocks_lang'
					. ' WHERE block_id = ' . intval($block_id), __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$retval[] = $this->db->f('lang');
			}
			return $retval;
		}

		//find out in what languages this block has data and return 
		function getlangarrayforversion($version_id)
		{
			$retval = array();
			$this->db->query('SELECT lang FROM phpgw_sitemgr_content_lang'
					. ' WHERE version_id = ' . intval($version_id), __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$retval[] = $this->db->f('lang');
			}
			return $retval;
		}

		//returns the versions arguments array
		function getversion($version_id,$lang=false)
		{
			$fields = "arguments" . ($lang ? ', arguments_lang' : '');
			$lang_join = $lang ? 'LEFT JOIN phpgw_sitemgr_content_lang AS t2'
							. ' ON (t1.version_id = t2.version_id'
							. " AND lang='" . $this->db->db_addslashes($lang) . "')" 
						: '';
			$this->db->query("SELECT $fields FROM phpgw_sitemgr_content AS t1 $lang_join "
					. ' WHERE t1.version_id = ' . intval($version_id), __LINE__, __FILE__);
			if ($this->db->next_record())
			{
				 return $lang ? 
					array_merge(
						unserialize($this->db->f('arguments', True)),
						unserialize($this->db->f('arguments_lang', True)) 
					) : 
					unserialize($this->db->f('arguments', True));
			}
			else
			{
				return false;
			}
		}

		function getblock($block_id,$lang)
		{
			$this->db->query('SELECT area,cat_id,page_id,area,t1.module_id,module_name,sort_order,'
						. ' title,viewable'
				. ' FROM phpgw_sitemgr_blocks AS t1 '
				. ' LEFT JOIN phpgw_sitemgr_modules as t2 ON t1.module_id=t2.module_id '
				. ' LEFT JOIN phpgw_sitemgr_blocks_lang AS t3 '
					. " ON (t1.block_id=t3.block_id AND lang='" . $this->db->db_addslashes($lang) .  "')"
				. ' WHERE t1.block_id = ' . intval($block_id), __LINE__, __FILE__);
			if ($this->db->next_record())
			{
				$block = CreateObject('sitemgr.Block_SO',True);
				$block->id = $block_id;
				$block->cat_id = $this->db->f('cat_id');
				$block->page_id = $this->db->f('page_id');
				$block->area = $this->db->f('area');
				$block->module_id = $this->db->f('module_id');
 				$block->module_name = $this->db->f('module_name');
 				$block->sort_order = $this->db->f('sort_order');
 				$block->title = stripslashes($this->db->f('title'));
 				$block->view = $this->db->f('viewable');
				return $block;
			}
			else
			{
				return false;
			}
		}

		//this function only retrieves basic info for the block
		function getblockdef($block_id)
		{
			$this->db->query('SELECT cat_id,page_id,area,t1.module_id,module_name '
					. ' FROM phpgw_sitemgr_blocks AS t1,phpgw_sitemgr_modules AS t2'
					. ' WHERE t1.module_id = t2.module_id '
					. ' AND t1.block_id = ' . intval($block_id), __LINE__, __FILE__);

			if ($this->db->next_record())
			{
				$block = CreateObject('sitemgr.Block_SO',True);
				$block->id = $block_id;
				$block->cat_id = $this->db->f('cat_id');
				$block->page_id = $this->db->f('page_id');
				$block->area = $this->db->f('area');
				$block->module_id = $this->db->f('module_id');
 				$block->module_name = $this->db->f('module_name');
				return $block;
			}
			else
			{
				return false;
			}
		}

		function getlangblocktitle($id,$lang)
		{
			if ($lang)
			{
				$this->db->query('SELECT title FROM phpgw_sitemgr_blocks_lang'
						. ' WHERE block_id =' . intval($id)
						. " AND lang = '" . $this->db->db_addslashes($lang) . "'", __LINE__, __FILE__);
				return $this->db->next_record() ? $this->db->f('title') : false;
			}
			else
			{
				$this->db->query('SELECT title FROM phpgw_sitemgr_blocks_lang'
						. ' WHERE block_id =' . intval($id), __LINE__, __FILE__);
				return $this->db->next_record() ? $this->db->f('title') : false;
			}
		}

		function saveblockdata($block)
		{
			return $this->db->query('UPDATE phpgw_sitemgr_blocks '
				. ' SET sort_order = ' . intval($block->sort_order) . ', '
					. ' viewable = ' . intval($block->view) 
					. ' WHERE block_id = ' . intval($block->id), __LINE__, __FILE__);
		}

		function saveblockdatalang($id,$title,$lang)
		{
			$this->db->query('DELETE FROM phpgw_sitemgr_blocks_lang '
					. ' WHERE block_id = ' . intval($id) 
					. " AND lang = '" . $this->db->db_addslashes($lang) . "'", __LINE__, __FILE__);

			return $this->db->query('INSERT INTO phpgw_sitemgr_blocks_lang (block_id,title,lang)'
						. ' VALUES (' . intval($id) . ','
						. "'" . $this->db->db_addslashes($title) . "',"
						. "'" . $this->db->db_addslashes($lang) . "')", __LINE__, __FILE__);
	}

		function saveversiondata($block_id,$version_id,$data)
		{
			//this is necessary because double slashed data breaks while serialized
			if (isset($data))
			{
				$this->remove_magic_quotes($data);
			}
			$s = $this->db->db_addslashes(serialize($data));
			//by requiring block_id, we make sur that we only touch versions that really belong to the block
			return $this->db->query('UPDATE phpgw_sitemgr_content'
						. " SET arguments = '$s' "
						. ' WHERE version_id = ' . intval($version_id)
						. ' AND block_id = ' . intval($block_id), __LINE__, __FILE__);
		}

		function saveversionstate($block_id,$version_id,$state)
		{
			return $this->db->query('UPDATE phpgw_sitemgr_content '
						. ' SET state = ' . intval($state)
						. ' WHERE version_id = ' . intval($version_id)
						. ' AND block_id = ' . intval($block_id), __LINE__, __FILE__);
		}

		function saveversiondatalang($id,$data,$lang)
		{
			//this is necessary because double slashed data breaks while serialized
			if (isset($data))
			{
				$this->remove_magic_quotes($data);
			}
			$s = $this->db->db_addslashes(serialize($data));
			$blockid = $block->id;
			$this->db->query('DELETE FROM phpgw_sitemgr_content_lang '
					. ' WHERE version_id = ' . intval($id) 
					. " AND lang = '" . $this->db->db_addslashes($lang). "'", __LINE__, __FILE__);
			return $this->db->query('INSERT INTO phpgw_sitemgr_content_lang (version_id,lang,arguments_lang) '
						. ' VALUES (' . intval($id) . ','
						. "'" . $this->db->db_addslashes($lang) . "',"
						. "'$s')", __LINE__, __FILE__);
		}

		function remove_magic_quotes(&$data)
		{
			if (is_array($data))
			{
				reset($data);
				foreach($data as $key => $val)
				{
					$this->remove_magic_quotes($data[$key]);
				}
			}
			elseif (get_magic_quotes_gpc()) 
			{
				$data = stripslashes($data);
			}
		}

		function commit($block_id)
		{
			$this->db->query('UPDATE phpgw_sitemgr_content '
					. ' SET state = ' . SITEMGR_STATE_PUBLISH
					. ' WHERE state = ' . SITEMGR_STATE_PREPUBLISH
					. ' AND block_id = ' . intval($block_id), __LINE__, __FILE__);

			$this->db->query('UPDATE phpgw_sitemgr_content '
					. ' SET state = ' . SITEMGR_STATE_ARCHIVE
					. ' WHERE state = ' . SITEMGR_STATE_PREUNPUBLISH 
					. ' AND block_id = ' . intval($block_id), __LINE__, __FILE__);
		}

		function reactivate($block_id)
		{
			$this->db->query('UPDATE phpgw_sitemgr_content '
					. ' SET state = ' . SITEMGR_STATE_DRAFT
					. ' WHERE state = ' . SITEMGR_STATE_ARCHIVE
					. ' AND block_id = ' . intval($block_id), __LINE__, __FILE__);
		}
	}
?>
