<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
	* @subpackage place
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package hrm
	 */

	class hrm_soplace
	{
		var $db;
		var $account;

		function hrm_soplace()
		{
			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->db 			= & $GLOBALS['phpgw']->db;
			$this->like 		= & $this->db->like;
			$this->join 		= & $this->db->join;
			$this->left_join	= & $this->db->left_join;
		}

		function read($data)
		{
			if(is_array($data))
			{
				if ($data['start'])
				{
					$start=$data['start'];
				}
				else
				{
					$start=0;
				}
				$query		= (isset($data['query'])?$data['query']:'');
				$sort		= (isset($data['sort'])?$data['sort']:'DESC');
				$order		= (isset($data['order'])?$data['order']:'');
				$allrows	= (isset($data['allrows'])?$data['allrows']:'');
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";

			}
			else
			{
				$ordermethod = ' order by name asc';
			}

			$table = 'phpgw_hrm_training_place';

			if($query)
			{
				$query = preg_replace("/'/",'',$query);
				$query = preg_replace('/"/','',$query);

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

			while ($this->db->next_record())
			{
				$place_info[] = array
				(
					'id'	=> $this->db->f('id'),
					'name'	=> stripslashes($this->db->f('name')),
					'descr'	=> stripslashes($this->db->f('descr'))
				);
			}

			return $place_info;
		}


		function read_single($id)
		{
			$sql = 'SELECT * FROM phpgw_hrm_training_place where id=' . intval($id);

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$values['id']		= $id;
				$values['name']	= stripslashes($this->db->f('name'));
				$values['address']	= stripslashes($this->db->f('address'));
				$values['remark']	= stripslashes($this->db->f('remark'));
				$values['town']	= stripslashes($this->db->f('town'));
				$values['zip']	= $this->db->f('zip');
				$values['entry_date']	= $this->db->f('entry_date');
				$values['owner']	= $this->db->f('owner');
			}
			return $values;
		}

		function read_training($id)
		{
			$sql = "SELECT phpgw_hrm_training.id as training_id,phpgw_hrm_training.title as title, phpgw_hrm_training.start_date,phpgw_hrm_training.end_date,phpgw_hrm_training_place.name as place FROM phpgw_hrm_training $this->left_join phpgw_hrm_training_place on phpgw_hrm_training.place_id=phpgw_hrm_training_place.id WHERE phpgw_hrm_training.user_id=" . intval($id);

			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$training[] = array
				(
					'training_id'	=> $this->db->f('training_id'),
					'start_date'	=> $this->db->f('start_date'),
					'end_date'	=> $this->db->f('end_date'),
					'title'	=> stripslashes($this->db->f('title')),
					'place'	=> stripslashes($this->db->f('place'))
				);

			}
			return $training;
		}

		function add($values)
		{
			$this->db->transaction_begin();

			$values['name'] = $this->db->db_addslashes($values['name']);
			$values['address'] = $this->db->db_addslashes($values['address']);
			$values['town'] = $this->db->db_addslashes($values['town']);
			$values['remark'] = $this->db->db_addslashes($values['remark']);
			$values['place_id'] = $this->db->next_id('phpgw_hrm_training_place');

			$insert_values=array(
				$values['place_id'],
				$values['name'],
				$values['address'],
				$values['zip'],
				$values['town'],
				$values['remark'],
				);

			$insert_values	= $this->db->validate_insert($insert_values);
			$this->db->query("INSERT INTO phpgw_hrm_training_place (id,name,address,zip,town, remark) "
				. "VALUES ($insert_values)",__LINE__,__FILE__);

			$receipt['message'][]=array('msg'=>lang('training item has been saved'));
			$receipt['place_id']= $values['place_id'];

			$this->db->transaction_commit();

			return $receipt;
		}

		function edit($values)
		{
			$this->db->transaction_begin();

			$value_set['name']			= $this->db->db_addslashes($values['name']);
			$value_set['address']			= $this->db->db_addslashes($values['address']);
			$value_set['zip']	= $values['zip'];
			$value_set['remark']		= $this->db->db_addslashes($values['remark']);
			$value_set['town']			= $this->db->db_addslashes($values['town']);

			$value_set	= $this->db->validate_update($value_set);


			$this->db->query("UPDATE phpgw_hrm_training_place set $value_set WHERE id=" . $values['place_id'],__LINE__,__FILE__);

			$this->db->transaction_commit();

			$receipt['message'][]=array('msg'=>lang('Place item has been edited'));

			$receipt['place_id']= $values['place_id'];
			return $receipt;
		}

		function delete($id)
		{

			$this->db->query('DELETE FROM phpgw_hrm_training_place WHERE id='  . intval($id),__LINE__,__FILE__);
		}

		function select_place_list()
		{
			$this->db->query("SELECT * FROM phpgw_hrm_training_place  ORDER BY name ");

			$i = 0;
			while ($this->db->next_record())
			{
				$place[$i]['id']				= $this->db->f('id');
				$place[$i]['name']				= stripslashes($this->db->f('name'));
				$i++;
			}
			return $place;
		}
	}
