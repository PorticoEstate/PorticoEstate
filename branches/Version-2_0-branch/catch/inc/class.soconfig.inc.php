<?php
	/**
	* phpGroupWare - CATCH: An application for importing data from handhelds into property.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2009 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package catch
	* @subpackage config
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package catch
	 */

	class catch_soconfig
	{
		var $grants;
		var $db;
		var $db2;
		var $account;
		var $config_data;

		public function __construct()
		{
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->db 			= & $GLOBALS['phpgw']->db;
			$this->join			= & $this->db->join;
			$this->left_join	= & $this->db->left_join;
			$this->like			= & $this->db->like;
		}


		function read_repository()
		{
			$sql = "SELECT fm_catch_config_type.name as type, value as config_value, fm_catch_config_attrib.name as config_name"
				. " FROM fm_catch_config_attrib $this->join fm_catch_config_type ON fm_catch_config_attrib.type_id = fm_catch_config_type.id";

			$this->db->query($sql,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$test = @unserialize($this->db->f('config_value'));
				if($test)
				{
					$this->config_data[$this->db->f('type')][$this->db->f('config_name')] = $test;
				}
				else
				{
					$this->config_data[$this->db->f('type')][$this->db->f('config_name')] = $this->db->f('config_value');
				}
			}
		}


		function read_type($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$query		= isset($data['query']) ? $data['query'] : '';
				$sort		= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
				$order		= isset($data['order']) ? $data['order'] : '';
				$allrows	= isset($data['allrows']) ? $data['allrows'] : '';
			}

			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";

			}
			else
			{
				$ordermethod = ' ORDER BY name ASC';
			}

			$table = 'fm_catch_config_type';

			if($query)
			{
				$query = $this->db->db_addslashes($query);

				$querymethod = " WHERE name $this->like '%$query%'";
			}

			$sql = "SELECT * FROM $table $querymethod";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$config_info = array();
			while ($this->db->next_record())
			{
				$config_info[] = array
				(
					'id'	=> $this->db->f('id'),
					'name'	=> $this->db->f('name', true),
					'descr'	=> $this->db->f('descr', true),
					'schema'	=> $this->db->f('schema_')
				);
			}

			return $config_info;
		}


		function read_single_type($id)
		{
			$sql = 'SELECT * FROM fm_catch_config_type WHERE id=' . intval($id);

			$this->db->query($sql,__LINE__,__FILE__);

			$values = array();
			if ($this->db->next_record())
			{
				$values = array
				(
					'id'		=> $id,
					'name'		=> $this->db->f('name', true),
					'descr'		=> $this->db->f('descr', true),
					'schema'	=> $this->db->f('schema_')
				);
			}
			return $values;
		}


		function add_type($values)
		{
			$receipt = array();
	
			$files_dir = isset($GLOBALS['phpgw_info']['server']['files_dir']) && $GLOBALS['phpgw_info']['server']['files_dir'] ? $GLOBALS['phpgw_info']['server']['files_dir'] : '';
			$createcatalog = !!$files_dir;
			$schema_dir = '';
			if($files_dir)
			{
				$schema_dir = "{$files_dir}/catch/pickup/{$values['schema']}";
				if( !is_dir($schema_dir))
				{
					$createcatalog = @mkdir("{$schema_dir}/imported", 0770, true);
				}
			}

			if(!$createcatalog)
			{
				$receipt['error'][]=array('msg'=>lang('unable to create pickup catalog'));
			}
			

			$this->db->transaction_begin();

			$values['name'] = $this->db->db_addslashes($values['name']);
			$values['descr'] = $this->db->db_addslashes($values['descr']);
			$values['type_id'] = $this->db->next_id('fm_catch_config_type');

			$insert_values=array(
				$values['type_id'],
				$values['name'],
				$values['descr'],
				$values['schema']
				);

			$insert_values	= $this->db->validate_insert($insert_values);
			$this->db->query("INSERT INTO fm_catch_config_type (id,name,descr,schema_) "
				. "VALUES ($insert_values)",__LINE__,__FILE__);

			$receipt['message'][]=array('msg'=>lang('config type has been saved'));
			$receipt['type_id']= $values['type_id'];

			$attrib_id = $this->db->next_id('fm_catch_config_attrib',array('type_id'=>$values['type_id']));
			$insert_values = array();
			$attrib_values[] = array
			(
				$values['type_id'],
				$attrib_id,
				'text',
				'pickup_path',
				lang('where to drop the files from the external system'),
				$schema_dir
			);
			$attrib_id ++;

			$attrib_values[] = array
			(
				$values['type_id'],
				$attrib_id,
				'text',
				'target',
				lang('where to import the data'),
				$values['schema']
			);

			foreach ($attrib_values as $insert_values)
			{
				$insert_values	= $this->db->validate_insert($insert_values);
				$this->db->query("INSERT INTO fm_catch_config_attrib (type_id,id,input_type,name,descr,value) "
					. "VALUES ($insert_values)",__LINE__,__FILE__);			
			}

			$this->db->transaction_commit();

			return $receipt;
		}

		function edit_type($values)
		{
			$receipt = array();
			$this->db->transaction_begin();

			$value_set['name']		= $this->db->db_addslashes($values['name']);
			$value_set['descr']		= $this->db->db_addslashes($values['descr']);

			$value_set	= $this->db->validate_update($value_set);

			$this->db->query("UPDATE fm_catch_config_type set $value_set WHERE id=" . (int) $values['type_id'],__LINE__,__FILE__);

			$this->db->transaction_commit();

			$receipt['message'][]=array('msg'=>lang('config type has been edited'));

			$receipt['type_id']= $values['type_id'];
			return $receipt;
		}

		function delete_type($id)
		{
			$id = (int)$id;
			$this->db->transaction_begin();
			$this->db->query("DELETE FROM fm_catch_config_choice WHERE type_id = {$id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_catch_config_attrib WHERE type_id = {$id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_catch_config_type WHERE id= {$id}",__LINE__,__FILE__);
			$this->db->transaction_commit();
		}

		function read_attrib($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$query		= isset($data['query']) ? $data['query'] : '';
				$sort		= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
				$order		= isset($data['order']) ? $data['order'] : '';
				$allrows	= isset($data['allrows']) ? $data['allrows'] : '';
				$type_id	= isset($data['type_id']) && $data['type_id'] ? (int)$data['type_id'] : 0;
			}

			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";

			}
			else
			{
				$ordermethod = ' ORDER BY name ASC';
			}

			if($query)
			{
				$query = $this->db->db_addslashes($query);

				$querymethod = " AND name $this->like '%$query%'";
			}

			$sql = "SELECT *  FROM fm_catch_config_attrib WHERE type_id = {$type_id} {$querymethod}";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$config_info = array();
			while ($this->db->next_record())
			{
				$config_info[] = array
				(
					'id'		=> $this->db->f('id'),
					'type_id'	=> $this->db->f('type_id'),
					'name'		=> $this->db->f('name', true),
					'value'		=> $this->db->f('value', true)
				);
			}

			return $config_info;
		}


		function read_single_attrib($type_id,$id)
		{
			$sql = 'SELECT * FROM fm_catch_config_attrib WHERE type_id =' . intval($type_id) . ' AND id=' . intval($id);

			$this->db->query($sql,__LINE__,__FILE__);

			$values =array();
			if ($this->db->next_record())
			{
				$values['id']			= $id;
				$values['input_type']	= $this->db->f('input_type');
				$values['name']			= $this->db->f('name', true);
				$values['descr']		= $this->db->f('descr', true);
				$values['value']		= $this->db->f('value', true);
				if($this->db->f('input_type')=='listbox')
				{
					$values['choice'] = $this->read_attrib_choice($type_id,$id);
				}
			}

			return $values;
		}


		function read_attrib_choice($type_id,$attrib_id)
		{
			$choice_table = 'fm_catch_config_choice';
			$sql = "SELECT * FROM $choice_table WHERE type_id=$type_id AND attrib_id=$attrib_id ";
			$this->db->query($sql,__LINE__,__FILE__);

			$choice = array();
			while ($this->db->next_record())
			{
				$choice[] = array
				(
					'id'	=> $this->db->f('id'),
					'value'	=> $this->db->f('value')
				);
			}
			return $choice;
		}


		function add_attrib($values)
		{
			$receipt = array();
			$this->db->transaction_begin();

			$values['name'] = $this->db->db_addslashes($values['name']);
			$values['descr'] = $this->db->db_addslashes($values['descr']);
			$values['attrib_id'] = $this->db->next_id('fm_catch_config_attrib',array('type_id'=>$values['type_id']));

			$insert_values=array(
				$values['type_id'],
				$values['attrib_id'],
				$values['input_type'],
				$values['name'],
				$values['descr'],
				);

			$insert_values	= $this->db->validate_insert($insert_values);
			$this->db->query("INSERT INTO fm_catch_config_attrib (type_id,id,input_type,name,descr) "
				. "VALUES ($insert_values)",__LINE__,__FILE__);

			$receipt['message'][]=array('msg'=>lang('config attrib has been saved'));
			$receipt['attrib_id']= $values['attrib_id'];

			$this->db->transaction_commit();

			return $receipt;
		}

		function edit_attrib($values)
		{
			$receipt = array();
			$this->db->transaction_begin();

			$value_set['name']	= $this->db->db_addslashes($values['name']);
			$value_set['descr']	= $this->db->db_addslashes($values['descr']);
			$value_set['input_type']	= $values['input_type'];

			$value_set	= $this->db->validate_update($value_set);

			$this->db->query("UPDATE fm_catch_config_attrib set $value_set WHERE type_id =" . $values['type_id'] . " AND id=" . $values['attrib_id'],__LINE__,__FILE__);


			if($values['new_choice'])
			{
				$choice_id = $this->db->next_id('fm_catch_config_choice' ,array('type_id'=>$values['type_id'],'attrib_id'=>$values['attrib_id']));

				$values_insert= array(
					$values['type_id'],
					$values['attrib_id'],
					$choice_id,
					$values['new_choice']
					);

				$values_insert	= $this->db->validate_insert($values_insert);

				$this->db->query("INSERT INTO fm_catch_config_choice (type_id,attrib_id,id,value) "
				. "VALUES ($values_insert)",__LINE__,__FILE__);
			}

			if($values['delete_choice'])
			{
				for ($i=0;$i<count($values['delete_choice']);$i++)
				{
					$this->db->query("DELETE FROM fm_catch_config_choice WHERE type_id=" . $values['type_id']. " AND attrib_id=" . $values['attrib_id']  ." AND id=" . $values['delete_choice'][$i],__LINE__,__FILE__);
				}
			}

			$this->db->transaction_commit();

			$receipt['message'][]=array('msg'=>lang('config attrib has been edited'));

			$receipt['attrib_id']= $values['attrib_id'];
			return $receipt;
		}

		function delete_attrib($type_id,$id)
		{
			$this->db->transaction_begin();
			$this->db->query('DELETE FROM fm_catch_config_choice WHERE type_id =' . intval($type_id) . ' AND attrib_id=' . intval($id),__LINE__,__FILE__);
			$this->db->query('DELETE FROM fm_catch_config_attrib WHERE type_id =' . intval($type_id) . ' AND id=' . intval($id),__LINE__,__FILE__);
			$this->db->transaction_commit();
		}

		function read_value($data)
		{
			if(is_array($data))
			{

				$start		= isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$query		= isset($data['query']) ? $data['query'] : '';
				$sort		= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
				$order		= isset($data['order']) ? $data['order'] : '';
				$allrows	= isset($data['allrows']) ? $data['allrows'] : '';
				$type_id	= isset($data['type_id']) && $data['type_id'] ? (int)$data['type_id'] : 0;
				$attrib_id	= isset($data['attrib_id']) && $data['attrib_id'] ? (int)$data['attrib_id'] : 0;
			}

			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";

			}
			else
			{
				$ordermethod = ' ORDER BY value ASC';
			}

			$table = 'fm_catch_config_attrib';

			if($query)
			{
				$query = $this->db->db_addslashes($query);

				$querymethod = " AND name $this->like '%$query%'";
			}

			$sql = "SELECT * FROM {$table} WHERE type_id = {$type_id} AND attrib_id = {$attrib_id} {$querymethod}";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$config_info = array();
			while ($this->db->next_record())
			{
				$config_info[] = array
				(
					'type_id'	=> $type_id,
					'attrib_id'	=> $attrib_id,
					'value'		=> $this->db->f('value', true)
				);
			}

			return $config_info;
		}


		function read_single_value($type_id,$attrib_id)
		{
			$sql = 'SELECT * FROM fm_catch_config_attrib WHERE type_id =' . (int)$type_id . ' AND id=' . (int)$attrib_id;

			$this->db->query($sql,__LINE__,__FILE__);

			$values = array();
			if ($this->db->next_record())
			{
				$values = array
				(
					'type_id'	=> $type_id,
					'attrib_id'	=> $attrib_id,					
					'value'		=> $this->db->f('value', true)
				);
			}

			return $values;
		}

		function add_value($values)
		{
			return $this->edit_value($values);
		}

		function edit_value($values)
		{
			$receipt = array();
			if(!isset($values['value']) || !$values['value'])
			{
				$this->delete_value($values['type_id'],$values['attrib_id']);
			}
			else
			{
				$this->db->transaction_begin();
				$value_set['value']	= $this->db->db_addslashes($values['value']);
				$value_set	= $this->db->validate_update($value_set);
				$this->db->query("UPDATE fm_catch_config_attrib SET $value_set WHERE type_id =" . (int)$values['type_id'] . " AND id=" . (int)$values['attrib_id'],__LINE__,__FILE__);
				$this->db->transaction_commit();
			}

			$receipt['message'][]=array('msg'=>lang('config attrib has been edited'));

			$receipt['attrib_id']= $values['attrib_id'];
			return $receipt;
		}

		function delete_value($type_id,$attrib_id)
		{
			$this->db->transaction_begin();
			$this->db->query('UPDATE fm_catch_config_attrib SET value = NULL WHERE type_id =' . (int)$type_id . ' AND id=' . (int)$attrib_id, __LINE__,__FILE__);
			$this->db->transaction_commit();
		}

		function select_choice_list($type_id,$attrib_id)
		{
			$this->db->query('SELECT * FROM fm_catch_config_choice WHERE type_id =' . intval($type_id) . ' AND attrib_id=' . intval($attrib_id) . ' ORDER BY value');

			$choice = array();
			while ($this->db->next_record())
			{
				$choice[] = array
				(
					'id'	=> $this->db->f('value', true),
					'name'	=> $this->db->f('value', true)
				);
			}
			return $choice;
		}

		function select_conf_list()
		{
			$this->db->query("SELECT * FROM fm_catch_config_type  ORDER BY name");

			$type = array();
			while ($this->db->next_record())
			{
				$type[] = array
				(
					'id'		=> $this->db->f('id'),
					'name'		=> $this->db->f('name', true)
				);
			}
			return $type;
		}
	}
