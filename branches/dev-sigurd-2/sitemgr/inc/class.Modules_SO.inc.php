<?php

	class Modules_SO
	{
		var $db;

		function Modules_SO()
		{
			$this->db = $GLOBALS['phpgw']->db;
		}

		function savemoduleproperties($module_id,$data,$contentarea,$cat_id)
		{
			$this->deletemoduleproperties($module_id,$contentarea,$cat_id);
			$s = $this->db->db_addslashes(serialize($data));
			$this->db->query('INSERT INTO phpgw_sitemgr_properties (area,cat_id,module_id,properties)'
					. " VALUES ('" . $this->db->db_addslashes($contentarea) . "',"
					. intval($cat_id) . ','
					. intval($module_id) . ','
					. "'$s')", __LINE__, __FILE__);
		}

		function deletemoduleproperties($module_id,$contentarea,$cat_id)
		{
			$this->db->query('DELETE FROM phpgw_sitemgr_properties'
					. " WHERE area='" . $this->db->db_addslashes($contentarea) . "'"
					. ' AND cat_id = ' . intval($cat_id)
					. ' AND module_id = ' . intval($module_id), __LINE__, __FILE__);
		}

		function getmoduleproperties($module_id,$contentarea,$cat_id,$modulename)
		{
			if ($module_id)
			{
				$sql = 'SELECT properties FROM phpgw_sitemgr_properties'
					. " WHERE area='" . $this->db->db_addslashes($contentarea) . "'"
					. ' AND cat_id = ' . intval($cat_id)
					. ' AND module_id = ' . intval($module_id);
			}
			else
			{
				$sql = 'SELECT properties FROM phpgw_sitemgr_properties AS t1'
					. ' LEFT JOIN phpgw_sitemgr_modules AS t2 ON t1.module_id=t2.module_id'
					. " WHERE area='" . $this->db->db_addslashes($contentarea) . "'"
					. ' AND cat_id = ' . intval($cat_id)
					. " AND module_name = '" . $this->db->db_addslashes($modulename) . "'";
			}
			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				return unserialize($this->db->f('properties', True));
			}
			else
			{
				return false;
			}
		}

		function registermodule($modulename,$description)
		{
			$description = $this->db->db_addslashes($description);
			$this->db->query('SELECT COUNT(*) FROM phpgw_sitemgr_modules'
					. " WHERE module_name='" . $this->db->db_addslashes($modulename) . "'"
				, __LINE__, __FILE__);
			$this->db->next_record();
			if ($this->db->f(0) == 0)
			{
				$this->db->query('INSERT INTO phpgw_sitemgr_modules (module_name,description)'
						. " VALUES ('" . $this->db->db_addslashes($modulename) ."',"
						. "'" . $this->db->db_addslashes($description) ."')"
					, __LINE__, __FILE__);
			}
			else
			{
				$this->db->query('UPDATE phpgw_sitemgr_modules'
						. " SET description = '" . $this->db->db_addslashes($description) . "'"
						. " WHERE module_name='" . $this->db->db_addslashes($modulename) . "'"
					, __LINE__, __FILE__);
			}
		}

		function getallmodules()
		{
			$sql = "SELECT * FROM phpgw_sitemgr_modules";
			return $this->constructmodulearray($sql);
		}

		function getmoduleid($modulename)
		{
			$this->db->query('SELECT module_id FROM phpgw_sitemgr_modules'
					. " WHERE module_name = '" . $this->db->db_addslashes($modulename) . "'"
				,__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				return $this->db->f('module_id');
			}
		}

		function getmodule($module_id)
		{
			$this->db->query('SELECT * FROM phpgw_sitemgr_modules'
					. ' WHERE module_id = ' . intval($module_id), __LINE__, __FILE__);
			if ($this->db->next_record())
			{
				$result['id'] = $this->db->f('module_id');
				$result['module_name'] = $this->db->f('module_name');
				$result['description'] = stripslashes($this->db->f('description'));
			}
			return $result;
		}

		function constructmodulearray($sql)
		{
			$result = array();
			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$id = $this->db->f('module_id');
				$result[$id]['module_name'] = $this->db->f('module_name');
				$result[$id]['description'] = stripslashes($this->db->f('description'));
			}
			return $result;
		}

		function savemodulepermissions($contentarea,$cat_id,$modules)
		{
			$cat_id = ($cat_id ? intval($cat_id) : 0);

			$this->db->query('DELETE FROM phpgw_sitemgr_active_modules'
					. " WHERE area='" . $this->db->db_addslashes($contentarea) . "'"
					. " AND cat_id = $cat_id", __LINE__, __FILE__);
			foreach($modules as $module)
			{
				$this->db->query('INSERT INTO phpgw_sitemgr_active_modules (area,cat_id,module_id)'
						. " VALUES ('" . $this->db->db_addslashes($contentarea) . "',"
						. $cat_id . ',' . intval($module) . ')', __LINE__, __FILE__);
			}
		}


		function getpermittedmodules($contentarea,$cat_id)
		{
			if (!$cat_id)
			{
				$cat_id = 0;
			}
			$sql = 'SELECT * from phpgw_sitemgr_modules AS t1'
				. ' LEFT JOIN phpgw_sitemgr_active_modules AS t2 ON t1.module_id=t2.module_id '
				. " WHERE area='" . $this->db->db_addslashes($contentarea) . "'"
				. ' AND cat_id = ' . intval($cat_id);
			return $this->constructmodulearray($sql);
		}
	}
?>
