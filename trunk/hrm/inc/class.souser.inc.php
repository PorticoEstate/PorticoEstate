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
 	* @version $Id: class.souser.inc.php,v 1.18 2007/01/02 09:35:14 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package hrm
	 */

	class hrm_souser
	{
		var $grants;
		var $db;
		var $db2;
		var $account;

		function hrm_souser()
		{
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon	= CreateObject('hrm.bocommon');
			$this->db           	= $this->bocommon->new_db();
			$this->db2           	= $this->bocommon->new_db();

			$this->acl		= CreateObject('phpgwapi.acl');
			$this->grants		= $this->acl->get_grants('hrm','.user');
			$this->left_join	= $this->bocommon->left_join;
			$this->join		= $this->bocommon->join;
			$this->like		= $this->bocommon->like;
		}

		function read($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start']) && $data['start'] ? $data['start']:'0';
				$query		= isset($data['query']) ? $data['query']:'';
				$sort		= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
				$order		= isset($data['order']) ? $data['order']:'';
				$allrows	= isset($data['allrows']) ? $data['allrows']:'';
			}
			$filtermethod = '';
			$querymethod = '';
/*			$filtermethod = ' AND ( account_id=' . $this->account;
			if (is_array($this->grants))
			{
				$grants = $this->grants;
				while (list($user) = each($grants))				{
					$public_user_list[] = $user;				}
				reset($public_user_list);
				$filtermethod .= " OR ( account_id IN(" . implode(',',$public_user_list) . ")))";
			}
			else
			{
				$filtermethod .= ' )';
			}
			
*/
			if ($order)
			{
				$ordermethod = " order by $order $sort";

			}
			else
			{
				$ordermethod = ' order by account_lastname asc';
			}

			$table = 'phpgw_accounts';

			if($query)
			{
				$query = ereg_replace("'",'',$query);
				$query = ereg_replace('"','',$query);

				$querymethod = " AND account_firstname $this->like '%$query%' or account_lastname $this->like '%$query%'";
			}

			$sql = "SELECT * FROM $table WHERE account_type = 'u' AND account_status = 'A' $filtermethod $querymethod";

			$this->db2->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db2->num_rows();

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
				$account_info[] = array
				(
					'account_id'		=> $this->db->f('account_id'),
					'account_firstname'	=> stripslashes($this->db->f('account_firstname')),
					'account_lastname'	=> stripslashes($this->db->f('account_lastname')),
					'grants'		=> isset($this->grants[$this->db->f('account_id')])?$this->grants[$this->db->f('account_id')]:''
				);
			}

			return $account_info;
		}


		function read_single_training($id)
		{

			$sql = 'SELECT * FROM phpgw_hrm_training where id=' . intval($id);

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$values['id']		= $id;
				$values['descr']	= stripslashes($this->db->f('descr'));
				$values['user_id']	= $this->db->f('user_id');
				$values['cat_id']	= $this->db->f('category');
				$values['title']	= stripslashes($this->db->f('title'));
				$values['start_date']	= $this->db->f('start_date');
				$values['end_date']	= $this->db->f('end_date');
				$values['reference']	= stripslashes($this->db->f('reference'));
				$values['skill']	= $this->db->f('skill');
				$values['place_id']	= $this->db->f('place_id');
				$values['entry_date']	= $this->db->f('entry_date');
				$values['owner']	= $this->db->f('owner');
			}
			return $values;
		}

		function read_training($data)
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

			$user_id = $data['user_id'];

			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";

			}
			else
			{
				$ordermethod = 'ORDER BY start_date asc';
			}

			$sql = "SELECT phpgw_hrm_training.id as training_id,phpgw_hrm_training.title as title, phpgw_hrm_training.start_date,"
				. " phpgw_hrm_training.end_date,phpgw_hrm_training_place.name as place, phpgw_hrm_training_category.descr as category"
				. " FROM phpgw_hrm_training $this->left_join phpgw_hrm_training_place on phpgw_hrm_training.place_id=phpgw_hrm_training_place.id"
				. " $this->join phpgw_hrm_training_category ON phpgw_hrm_training.category = phpgw_hrm_training_category.id"
				. " WHERE phpgw_hrm_training.user_id=" . intval($user_id);
		//		. " order BY category";

			$this->db->query($sql . $ordermethod ,__LINE__,__FILE__);

			while ($this->db->next_record())
			{				$training[] = array
				(
					'training_id'	=> $this->db->f('training_id'),
					'start_date'	=> $this->db->f('start_date'),
					'end_date'	=> $this->db->f('end_date'),
					'title'	=> stripslashes($this->db->f('title')),
					'place'	=> stripslashes($this->db->f('place')),
					'category'=> stripslashes($this->db->f('category'))
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
			$values['place_id'] = $this->bocommon->next_id('phpgw_hrm_training_place');

			$insert_values=array(
				$values['place_id'],
				$values['new_place_name'],
				$values['new_place_address'],
				$values['new_place_zip'],
				$values['new_place_town'],
				$values['new_place_remark'],
				);

			$insert_values	= $this->bocommon->validate_db_insert($insert_values);
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

			$training_id = $this->bocommon->next_id('phpgw_hrm_training');

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
				$values['descr'],
				time(),
				$this->account
				);

			$insert_values	= $this->bocommon->validate_db_insert($insert_values);

			$this->db->query("INSERT INTO phpgw_hrm_training (id,user_id,category,title,start_date,end_date,reference,skill,place_id,descr,entry_date,owner) "
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

			$value_set	= $this->bocommon->validate_db_update($value_set);

			$table='phpgw_hrm_training';

			$this->db->query("UPDATE $table set $value_set WHERE id=" . $values['training_id'],__LINE__,__FILE__);

			$this->db->transaction_commit();

			$receipt['message'][]=array('msg'=>lang('Training item has been edited'));

			$receipt['training_id']= $values['training_id'];
			return $receipt;
		}

		function delete_training($user_id,$id)
		{

			$this->db->query('DELETE FROM phpgw_hrm_training WHERE id='  . intval($id) . ' AND user_id='  . intval($user_id),__LINE__,__FILE__);
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
