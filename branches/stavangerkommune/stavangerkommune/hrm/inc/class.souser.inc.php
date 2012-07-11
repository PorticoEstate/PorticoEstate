<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
	* @subpackage user
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package hrm
	 */

	class hrm_souser
	{
		var $grants;
		private $db;
		var $account;

		public function __construct()
		{
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->db 			= & $GLOBALS['phpgw']->db;
			$this->like 		= & $this->db->like;
			$this->join 		= & $this->db->join;
			$this->left_join	= & $this->db->left_join;
			$GLOBALS['phpgw']->acl->set_account_id($this->account);
			$this->grants		= $GLOBALS['phpgw']->acl->get_grants('hrm','.user');
		}

		function read($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$query		= isset($data['query']) ? $data['query'] : '';
				$sort		= isset($data['sort']) && $data['sort'] == 'ASC' ? $data['sort']:'DESC';
				$order		= isset($data['order']) ? $data['order'] : '';
				$allrows	= isset($data['allrows']) && $data['allrows'] ? $data['allrows'] : false;
			}

			$accounts =& $GLOBALS['phpgw']->accounts;

			$list = $accounts->get_list('accounts', $start, $sort, $order, $query);
			$this->total_records = $accounts->total;

			$account_info = array();
			foreach ( $list as $entry )
			{
				$account_info[] = array
				(
					'grants'			=> isset($this->grants[$entry->id]) ? $this->grants[$entry->id] : 0,
					'account_firstname'	=> $entry->firstname,
					'account_lastname'	=> $entry->lastname,
					'account_id'		=> $entry->id
				);
			}
			return $account_info;
		}


		function read_single_training($id)
		{

			$sql = 'SELECT * FROM phpgw_hrm_training where id=' . (int) $id;

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$values = array
				(
					'id'			=> $id,
					'descr'			=> $this->db->f('descr', true),
					'user_id'		=> $this->db->f('user_id'),
					'cat_id'		=> $this->db->f('category'),
					'title'			=> $this->db->f('title', true),
					'start_date'	=> $this->db->f('start_date'),
					'end_date'		=> $this->db->f('end_date'),
					'reference'		=> $this->db->f('reference', true),
					'skill'			=> $this->db->f('skill'),
					'place_id'		=> $this->db->f('place_id'),
					'credits'		=> (int)$this->db->f('credits'),
					'entry_date'	=> $this->db->f('entry_date'),
					'owner'			=> $this->db->f('owner')
				);
			}
			return $values;
		}

		function read_training($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$query		= isset($data['query']) ? $data['query'] : '';
				$sort		= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'ASC';
				$order		= isset($data['order']) && $data['order'] ? $data['order'] : 'category DESC, start_date';
				$allrows	= isset($data['allrows']) ? $data['allrows'] : '';
			}

			$user_id = $data['user_id'];

			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";

			}
			else
			{
				$ordermethod = ' ORDER BY start_date asc';
			}

			$sql = "SELECT phpgw_hrm_training.id as training_id,phpgw_hrm_training.title as title, phpgw_hrm_training.start_date,"
				. " phpgw_hrm_training.end_date,phpgw_hrm_training_place.name as place, phpgw_hrm_training_category.descr as category, credits"
				. " FROM phpgw_hrm_training $this->left_join phpgw_hrm_training_place on phpgw_hrm_training.place_id=phpgw_hrm_training_place.id"
				. " $this->join phpgw_hrm_training_category ON phpgw_hrm_training.category = phpgw_hrm_training_category.id"
				. " WHERE phpgw_hrm_training.user_id=" . intval($user_id);
		//		. " order BY category";

			$this->db->query($sql . $ordermethod ,__LINE__,__FILE__);

			$training = array();
			while ($this->db->next_record())
			{
				$training[] = array
				(
					'training_id'	=> $this->db->f('training_id'),
					'start_date'	=> $this->db->f('start_date'),
					'end_date'		=> $this->db->f('end_date'),
					'title'			=> $this->db->f('title', true),
					'place'			=> $this->db->f('place', true),
					'credits'		=> (int)$this->db->f('credits'),
					'category'		=> $this->db->f('category', true)
				);

			}
			return $training;
		}

		function add_place($values)
		{
			$values['new_place_name'] = $this->db->db_addslashes($values['new_place_name']);
			$values['new_place_address'] = $this->db->db_addslashes($values['new_place_address']);
			$values['new_place_town'] = $this->db->db_addslashes($values['new_place_town']);
			$values['new_place_descr'] = $this->db->db_addslashes($values['new_place_descr']);
			$values['place_id'] = $this->db->next_id('phpgw_hrm_training_place');

			$insert_values=array(
				$values['place_id'],
				$values['new_place_name'],
				$values['new_place_address'],
				$values['new_place_zip'],
				$values['new_place_town'],
				$values['new_place_remark'],
				);

			$insert_values	= $this->db->validate_insert($insert_values);
			$this->db->query("INSERT INTO phpgw_hrm_training_place (id,name,address,zip,town, remark) "
				. "VALUES ($insert_values)",__LINE__,__FILE__);

			return $values['place_id'];
		}

		function add_training($values)
		{
			$values['descr'] = $this->db->db_addslashes($values['descr']);
			$values['title'] = $this->db->db_addslashes($values['title']);

			$this->db->transaction_begin();

			if($values['new_place_name'] && $values['place_id']=='new_place')
			{
				$values['place_id'] = $this->add_place($values);
			}

			$training_id = $this->db->next_id('phpgw_hrm_training');

			$insert_values=array(
				$training_id,
				$values['user_id'],
				$values['cat_id'],
				$values['title'],
				$values['start_date'],
				$values['end_date'],
				$values['reference'],
				$values['skill'],
				$values['place_id'],
				(int)$values['credits'],
				$values['descr'],
				time(),
				$this->account
				);

			$insert_values	= $this->db->validate_insert($insert_values);

			$this->db->query("INSERT INTO phpgw_hrm_training (id,user_id,category,title,start_date,end_date,reference,skill,place_id,credits,descr,entry_date,owner) "
				. "VALUES ($insert_values)",__LINE__,__FILE__);

			$receipt['message'][]=array('msg'=>lang('training item has been saved'));

			$receipt['training_id']= $training_id;

			$this->db->transaction_commit();
			return $receipt;
		}

		function edit_training($values)
		{
			$this->db->transaction_begin();

			if($values['new_place_name'] && $values['place_id']=='new_place')
			{
				$values['place_id'] = $this->add_place($values);
			}

			$value_set['descr']			= $this->db->db_addslashes($values['descr']);
			$value_set['category']		= $values['cat_id'];
			$value_set['title']			= $this->db->db_addslashes($values['title']);
			$value_set['start_date']	= $values['start_date'];
			$value_set['end_date']		= $values['end_date'];
			$value_set['reference']		= $this->db->db_addslashes($values['reference']);
			$value_set['skill']			= $values['skill'];
			$value_set['place_id']		= $values['place_id'];
			$value_set['credits']		= (int)$values['credits'];

			$value_set	= $this->db->validate_update($value_set);

			$table='phpgw_hrm_training';

			$this->db->query("UPDATE $table set $value_set WHERE id=" . $values['training_id'],__LINE__,__FILE__);

			$this->db->transaction_commit();

			$receipt['message'][]=array('msg'=>lang('Training item has been edited'));

			$receipt['training_id']= $values['training_id'];
			return $receipt;
		}

		function delete_training($user_id,$id)
		{
			$user_id = (int) $user_id;
			$id = (int) $id;

			$this->db->query("DELETE FROM phpgw_hrm_training WHERE id={$id} AND user_id={$user_id}", __LINE__, __FILE__);
		}

		function select_category_list()
		{
			$this->db->query("SELECT id, descr FROM phpgw_hrm_training_category  ORDER BY descr ");

			$i = 0;
			while ($this->db->next_record())
			{
				$categories[$i]['id']				= $this->db->f('id');
				$categories[$i]['name']				= stripslashes($this->db->f('descr'));
				$i++;
			}
			return $categories;
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
