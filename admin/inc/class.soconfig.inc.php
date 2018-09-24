<?php
	/**
	* phpGroupWare - SMS: A SMS Gateway.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package admin
	* @subpackage config
 	* @version $Id: class.soconfig.inc.php 3613 2009-09-18 16:19:49Z sigurd $
	*/

	phpgw::import_class('phpgwapi.datetime');
	/**
	 * Description
	 * @package admin
	 */

	class admin_soconfig
	{
		public $config_data = array();
		protected $db;
		protected $location_id = 0;
		protected $global_lock = false;

		public function __construct($location_id = 0)
		{
			$this->db 			= &$GLOBALS['phpgw']->db;
			$this->join			= $this->db->join;
			$this->left_join	= $this->db->left_join;
			$this->like			= $this->db->like;

			if($location_id)
			{
				$this->set_location($location_id);
				$this->read_repository();
			}
		}

		public function set_location(int $location_id)
		{
			$this->location_id = (int)$location_id;
		}

		public function read()
		{
			if(!$this->location_id)
			{
				throw new Exception("location_id is not set");
			}
			
			if(!$this->config_data)
			{
				$this->read_repository();
			}
			return $this->config_data;
		}

		public function read_repository()
		{
			$sql = "SELECT phpgw_config2_section.name as section, value as config_value, phpgw_config2_attrib.name as config_name "
				. " FROM phpgw_config2_value $this->join phpgw_config2_attrib ON "
				. " phpgw_config2_value.attrib_id = phpgw_config2_attrib.id AND "
				. " phpgw_config2_value.section_id = phpgw_config2_attrib.section_id"
				. " $this->join phpgw_config2_section ON  phpgw_config2_value.section_id = phpgw_config2_section.id"
				. " WHERE location_id = {$this->location_id}";

			$this->db->query($sql,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$test = @unserialize($this->db->f('config_value',true));
				if($test)
				{
					$this->config_data[$this->db->f('section')][$this->db->f('config_name')] = $test;
				}
				else
				{
					$this->config_data[$this->db->f('section')][$this->db->f('config_name')] = $this->db->f('config_value',true);
				}
			}
		}


		function read_section(array $data)
		{
			$start		= isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$query		= isset($data['query']) ? $data['query'] : '';
			$sort		= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
			$order		= isset($data['order']) ? $data['order'] : '';
			$allrows	= isset($data['allrows']) ? $data['allrows'] : '';

			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";

			}
			else
			{
				$ordermethod = ' ORDER BY name ASC';
			}

			$table = 'phpgw_config2_section';

			$querymethod = '';
			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$querymethod = "AND name $this->like '%$query%'";
			}

			$sql = "SELECT * FROM $table WHERE location_id = {$this->location_id} {$querymethod}";

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
					'name'	=> stripslashes($this->db->f('name')),
					'descr'	=> stripslashes($this->db->f('descr'))
				);
			}

			return $config_info;
		}


		function read_single_section(int $id)
		{
			$id = (int)$id;
			$sql = "SELECT * FROM phpgw_config2_section WHERE location_id = {$this->location_id} AND id={$id}";

			$this->db->query($sql,__LINE__,__FILE__);

			$values = array();
			if ($this->db->next_record())
			{
				$values['id']		= $id;
				$values['name']		= $this->db->f('name', true);
				$values['descr']	= $this->db->f('descr', true);
			}
			return $values;
		}


		function add_section(array $values)
		{
			if ( $this->db->get_transaction() )
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}


			$values['name'] = $this->db->db_addslashes($values['name']);
			$values['descr'] = $this->db->db_addslashes($values['descr']);

			$sql = "SELECT id FROM phpgw_config2_section WHERE location_id = {$this->location_id} AND descr = '{$values['descr']}'";

			$this->db->query($sql,__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				$receipt['section_id']=  $this->db->f('id');
				$receipt['message'][]=array('msg'=>lang('config section has not been saved'));
				return $receipt;
			}

			$values['section_id'] = $this->db->next_id('phpgw_config2_section');

			$insert_values = array
			(
				$values['section_id'],
				$this->location_id,
				$values['name'],
				$values['descr'],
			);

			$insert_values	= $this->db->validate_insert($insert_values);
			$this->db->query("INSERT INTO phpgw_config2_section (id,location_id,name,descr) "
				. "VALUES ($insert_values)",__LINE__,__FILE__);

			$receipt['message'][]=array('msg'=>lang('config section has been saved'));
			$receipt['section_id']= $values['section_id'];

			if ( !$this->global_lock )
			{
				$this->db->transaction_commit();
			}

			return $receipt;
		}

		function edit_section(array $values)
		{
			if ( $this->db->get_transaction() )
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}


			$value_set['name']		= $this->db->db_addslashes($values['name']);
			$value_set['descr']		= $this->db->db_addslashes($values['descr']);

			$value_set	= $this->db->validate_update($value_set);

			$this->db->query("UPDATE phpgw_config2_section set $value_set WHERE id=" . $values['section_id'],__LINE__,__FILE__);

			if ( !$this->global_lock )
			{
				$this->db->transaction_commit();
			}

			$receipt['message'][]=array('msg'=>lang('config section has been edited'));

			$receipt['section_id']= $values['section_id'];
			return $receipt;
		}

		function delete_section(int $id)
		{
			$id = (int)$id;

			if ( $this->db->get_transaction() )
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}

			$this->db->query("DELETE FROM phpgw_config2_value WHERE section_id = {$id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM phpgw_config2_choice WHERE section_id = {$id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM phpgw_config2_attrib WHERE section_id = {$id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM phpgw_config2_section WHERE id = {$id}",__LINE__,__FILE__);

			if ( !$this->global_lock )
			{
				$this->db->transaction_commit();
			}
		}

		function read_attrib(array $data)
		{
			$start		= isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$query		= isset($data['query'])?$data['query']:'';
			$sort		= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
			$order		= isset($data['order']) ? $data['order'] : '';
			$allrows	= isset($data['allrows'])?$data['allrows']:'';
			$section_id	= isset($data['section_id']) && $data['section_id'] ? (int)$data['section_id'] : 0;

			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";
			}
			else
			{
				$ordermethod = ' ORDER BY name asc';
			}

			$section_table = 'phpgw_config2_section';
			$attrib_table = 'phpgw_config2_attrib';
			$value_table = 'phpgw_config2_value';

			if($query)
			{
				$query = $this->db->db_addslashes($query);

				$querymethod = " AND name $this->like '%$query%'";
			}

			$sql = "SELECT $attrib_table.id, $attrib_table.section_id, $value_table.id as value_id, $attrib_table.name, $attrib_table.descr, $attrib_table.input_type, $value_table.value"
			. " FROM ($section_table {$this->join} $attrib_table ON  ($section_table.id = $attrib_table.section_id))"
			. " {$this->left_join} $value_table ON ($attrib_table.section_id = $value_table.section_id AND $attrib_table.id = $value_table.attrib_id)"
			. " WHERE location_id = {$this->location_id} AND $attrib_table.section_id = '$section_id' $querymethod";

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
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$config_info = array();
			while ($this->db->next_record())
			{
				$input_type	= $this->db->f('input_type');
				switch($input_type)
				{
					case 'password':
						$value = '****';
						break;
					case 'date':
						$value = $GLOBALS['phpgw']->common->show_date($this->db->f('value'),$dateformat);
						break;
					default:
						$value = $this->db->f('value', true);
				}
				$config_info[] = array
				(
					'id'			=> $this->db->f('id'),
					'section_id'	=> $this->db->f('section_id'),
					'value_id'		=> $this->db->f('value_id'),
					'name'			=> $this->db->f('name', true),
					'value'			=> $value,
					'descr'			=> $this->db->f('descr', true),
				);
			}
			return $config_info;
		}


		function read_single_attrib(int $section_id, int $id)
		{
			$section_id	= (int) $section_id;
			$id			= (int) $id;
			$sql = "SELECT * FROM phpgw_config2_attrib WHERE section_id = {$section_id} AND id = {$id}";

			$this->db->query($sql,__LINE__,__FILE__);

			$values = array();
			if ($this->db->next_record())
			{
				$values['id']			= $id;
				$values['input_type']	= $this->db->f('input_type');
				$values['name']			= $this->db->f('name', true);
				$values['descr']		= $this->db->f('descr', true);
				if($this->db->f('input_type')=='listbox')
				{
					$values['choice'] = $this->read_attrib_choice($section_id,$id);
				}
			}

			return $values;
		}


		function read_attrib_choice(int $section_id, int $attrib_id)
		{
			$section_id	= (int) $section_id;
			$attrib_id	= (int) $attrib_id;

			$choice_table = 'phpgw_config2_choice';
			$sql = "SELECT * FROM {$choice_table} WHERE section_id={$section_id} AND attrib_id={$attrib_id}";
			$this->db->query($sql,__LINE__,__FILE__);

			$choice = array();
			while ($this->db->next_record())
			{
				$choice[] = array
				(
					'id'	=> $this->db->f('id'),
					'value'	=> $this->db->f('value',true)
				);
			}
			return $choice;
		}


		function add_attrib(array $values)
		{
			if ( $this->db->get_transaction() )
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}

			$values['name'] = $this->db->db_addslashes($values['name']);
			$values['descr'] = $this->db->db_addslashes($values['descr']);

			$sql = "SELECT id FROM phpgw_config2_attrib WHERE section_id = '{$values['section_id']}' AND name = '{$values['name']}'";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$receipt['attrib_id']	= $this->db->f('id');
				$receipt['error'][]=array('msg'=>lang('config attrib has been saved'));
				return $receipt;
			}

			$values['attrib_id'] = $this->db->next_id('phpgw_config2_attrib',array('section_id'=>$values['section_id']));

			$insert_values = array
			(
				$values['section_id'],
				$values['attrib_id'],
				$values['input_type'],
				$values['name'],
				$values['descr'],
			);

			$insert_values	= $this->db->validate_insert($insert_values);
			$this->db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name,descr) "
				. "VALUES ($insert_values)",__LINE__,__FILE__);


			$choice_map = array();
			if(isset($values['choice']) && $values['choice'])
			{
				foreach ($values['choice'] as $choice)
				{
					$values['new_choice'] = $choice;
					$this->edit_attrib($values);				
				}
			}

			if(isset($values['value']) && $values['value'])
			{
				$this->add_value($values);
			}

			$receipt['message'][]=array('msg'=>lang('config attrib has been saved'));
			$receipt['attrib_id']= $values['attrib_id'];

			if ( !$this->global_lock )
			{
				$this->db->transaction_commit();
			}

			return $receipt;
		}

		function edit_attrib(array $values)
		{
			if ( $this->db->get_transaction() )
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}


			$value_set['name']	= $this->db->db_addslashes($values['name']);
			$value_set['descr']	= $this->db->db_addslashes($values['descr']);
			$value_set['input_type']	= $values['input_type'];

			$value_set	= $this->db->validate_update($value_set);

			$this->db->query("UPDATE phpgw_config2_attrib SET $value_set WHERE section_id =" . (int)$values['section_id'] . " AND id=" . (int)$values['attrib_id'],__LINE__,__FILE__);


			if($values['new_choice'])
			{
				$choice_id = $this->db->next_id('phpgw_config2_choice' ,array('section_id'=>$values['section_id'],'attrib_id'=>$values['attrib_id']));

				$values_insert= array
				(
					$values['section_id'],
					$values['attrib_id'],
					$choice_id,
					$values['new_choice']
				);

				$values_insert	= $this->db->validate_insert($values_insert);

				$this->db->query("INSERT INTO phpgw_config2_choice (section_id,attrib_id,id,value) "
				. "VALUES ($values_insert)",__LINE__,__FILE__);
			}

			if($values['delete_choice'] && is_array($values['delete_choice']))
			{
				foreach ($values['delete_choice'] as $choice_id)
				{
					$this->db->query("DELETE FROM phpgw_config2_choice WHERE section_id=" . (int)$values['section_id']. " AND attrib_id=" . (int)$values['attrib_id']  ." AND id=" . (int)$choice_id,__LINE__,__FILE__);
				}
			}

			if ( !$this->global_lock )
			{
				$this->db->transaction_commit();
			}

			$receipt['message'][]=array('msg'=>lang('config attrib has been edited'));

			$receipt['attrib_id']= $values['attrib_id'];
			$receipt['choice_id'] = $choice_id;
			return $receipt;
		}

		function delete_attrib(int $section_id, int $id)
		{
			$section_id	= (int) $section_id;
			$id			= (int) $id;

			if ( $this->db->get_transaction() )
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}

			$this->db->query("DELETE FROM phpgw_config2_value WHERE section_id ={$section_id} AND attrib_id={$id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM phpgw_config2_choice WHERE section_id ={$section_id} AND attrib_id={$id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM phpgw_config2_attrib WHERE section_id ={$section_id} AND id={$id}",__LINE__,__FILE__);
			if ( !$this->global_lock )
			{
				$this->db->transaction_commit();
			}
		}

		function read_value(array $data)
		{
			$start		= isset($data['start']) && (int)$data['start'] ? $data['start'] : 0;
			$query		= isset($data['query']) ? $data['query'] : '';
			$sort		= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
			$order		= isset($data['order']) ? $data['order'] : '';
			$allrows	= isset($data['allrows']) ? $data['allrows'] : '';
			$section_id	= isset($data['section_id']) && $data['section_id'] ? (int)$data['section_id'] : 0;
			$attrib_id	= isset($data['attrib_id']) && $data['attrib_id'] ? (int)$data['attrib_id'] : 0;

			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";
			}
			else
			{
				$ordermethod = ' ORDER BY value ASC';
			}

			$table = 'phpgw_config2_value';

			if($query)
			{
				$query = $this->db->db_addslashes($query);

				$querymethod = " AND name $this->like '%$query%'";
			}

			$sql = "SELECT * FROM $table WHERE section_id = '$section_id' AND attrib_id = '$attrib_id' $querymethod";

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
					'section_id'	=> $section_id,
					'attrib_id'	=> $attrib_id,
					'value'		=> $this->db->f('value', true),
				);
			}

			return $config_info;
		}

		function read_single_value(int $section_id, int $attrib_id, int $id)
		{
			$section_id	= (int) $section_id;
			$attrib_id	= (int) $attrib_id;
			$id			= (int) $id;

			$sql = "SELECT * FROM phpgw_config2_value WHERE section_id ={$section_id} AND attrib_id={$attrib_id} AND id={$id}";
			$this->db->query($sql,__LINE__,__FILE__);

			$values = array();
			if ($this->db->next_record())
			{
				$values['id']		= $id;
				$values['value']	= stripslashes($this->db->f('value'));
			}

			return $values;
		}

		function add_value($values)
		{
			if(isset($values['input_type']) && $values['input_type'] == 'date')
			{
				$values['value'] = phpgwapi_datetime::date_to_timestamp($values['value']);
			}

			if ( $this->db->get_transaction() )
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}


			$values['value'] = $this->db->db_addslashes($values['value']);
			$id = $this->db->next_id('phpgw_config2_value',array('section_id'=>$values['section_id'],'attrib_id'=>$values['attrib_id']));

			$insert_values = array
			(
				$values['section_id'],
				$values['attrib_id'],
				$id,
				$values['value']
			);

			$insert_values	= $this->db->validate_insert($insert_values);
			$this->db->query("INSERT INTO phpgw_config2_value (section_id,attrib_id,id,value) "
				. "VALUES ($insert_values)",__LINE__,__FILE__);

			$receipt['message'][]=array('msg'=>lang('config value has been saved'));
			$receipt['id']= $id;

			if ( !$this->global_lock )
			{
				$this->db->transaction_commit();
			}

			return $receipt;
		}

		function edit_value($values)
		{
			if(isset($values['input_type']) && $values['input_type'] == 'date')
			{
				$values['value'] = phpgwapi_datetime::date_to_timestamp($values['value']);
			}

			if(!$values['value'])
			{
				$this->delete_value($values['section_id'],$values['attrib_id'],$values['id']);
			}
			else
			{
				if ( $this->db->get_transaction() )
				{
					$this->global_lock = true;
				}
				else
				{
					$this->db->transaction_begin();
				}

				$value_set['value']	= $this->db->db_addslashes($values['value']);
				$value_set	= $this->db->validate_update($value_set);
				$this->db->query("UPDATE phpgw_config2_value SET {$value_set} WHERE section_id =" . (int)$values['section_id'] . ' AND attrib_id=' . (int)$values['attrib_id'] . ' AND id=' . (int)$values['id'],__LINE__,__FILE__);

				if ( !$this->global_lock )
				{
					$this->db->transaction_commit();
				}
			}

			$receipt['message'][]=array('msg'=>lang('config value has been edited'));

			$receipt['id']= $values['id'];
			return $receipt;
		}

		function delete_value($section_id,$attrib_id,$id)
		{
			$section_id	= (int) $section_id;
			$attrib_id	= (int) $attrib_id;
			$id			= (int) $id;

			if ( $this->db->get_transaction() )
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}

			$this->db->query("DELETE FROM phpgw_config2_value WHERE section_id ={$section_id} AND attrib_id={$attrib_id} AND id={$id}",__LINE__,__FILE__);
			if ( !$this->global_lock )
			{
				$this->db->transaction_commit();
			}
		}

		function select_choice_list($section_id,$attrib_id)
		{
			$section_id	= (int) $section_id;
			$attrib_id	= (int) $attrib_id;

			$this->db->query("SELECT * FROM phpgw_config2_choice WHERE section_id ={$section_id} AND attrib_id={$attrib_id} ORDER BY value");

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
			$this->db->query("SELECT * FROM phpgw_config2_section WHERE location_id = {$this->location_id} ORDER BY name ");
			$section = array();
			while ($this->db->next_record())
			{
				$section[] = array
				(
					'id'	=> $this->db->f('id'),
					'name'	=> $this->db->f('name', true)
				);
			}
			return $section;
		}
	}
