<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
	* @subpackage admin
 	* @version $Id: class.socategory.inc.php,v 1.9 2006/12/27 10:38:35 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package hrm
	 */

	class hrm_socategory
	{

		function hrm_socategory()
		{
			$this->currentapp	= 'hrm';
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon		= CreateObject('hrm.bocommon');
			$this->db			= $this->bocommon->new_db();

			$this->join			=& $this->bocommon->join;
			$this->like			=& $this->bocommon->like;

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
				$type		= (isset($data['type'])?$data['type']:'');
				$type_id		= (isset($data['type_id'])?$data['type_id']:'');
				$allrows	= (isset($data['allrows'])?$data['allrows']:'');
			}

			if(!$type)
			{
				return;
			}
			if ($order)
			{
				$ordermethod = " order by $order $sort";

			}
			else
			{
				$ordermethod = ' order by id asc';
			}

			$table = $this->select_table($type,$type_id);

			if($query)
			{
				$query = ereg_replace("'",'',$query);
				$query = ereg_replace('"','',$query);

				$querymethod = " where id $this->like '%$query%' or descr $this->like '%$query%'";
			}

			$sql = "SELECT * FROM $table $querymethod";

			$this->db->query("SELECT COUNT(id) AS cnt FROM $table $querymethod", __LINE__, __FILE__);
			$this->total_records = 0;
			if ( $this->db->next_record() )
			{
				$this->total_records = $this->db->f('cnt');
			}

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
				$category[] = array
				(
					'id'	=> $this->db->f('id'),
					'descr'	=> stripslashes($this->db->f('descr'))
				);
			}
			return $category;
		}


		function select_table($type)
		{
			switch($type)
			{
				case 'training':
					$table='phpgw_hrm_training_category';
					break;
				case 'experience':
					$table='phpgw_hrm_experience_category';
					break;
				case 'skill_level':
					$table='phpgw_hrm_skill_level';
					break;
				case 'qualification':
					$table='phpgw_hrm_quali_category';
					break;					
			}

			return $table;
		}


		function read_single($id,$type,$type_id)
		{

			$table = $this->select_table($type,$type_id);

			$sql = "SELECT * FROM $table  where id='$id'";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$category['id']		= $this->db->f('id');
				$category['descr']	= stripslashes($this->db->f('descr'));

				return $category;
			}
		}

		function add($category,$type,$type_id)
		{
			$table = $this->select_table($type,$type_id);

			$category['descr'] = $this->db->db_addslashes($category['descr']);

			$this->db->query("INSERT INTO $table (id, descr) "
				. "VALUES ('" . $category['id'] . "','" . $category['descr']. "')",__LINE__,__FILE__);

			$receipt['message'][]=array('msg'=>lang('category has been saved'));
			return $receipt;
		}

		function edit($category,$type,$type_id)
		{

			$table = $this->select_table($type,$type_id);

			$category['descr'] = $this->db->db_addslashes($category['descr']);

			$this->db->query("UPDATE $table set descr='" . $category['descr']
							. "' WHERE id='" . $category['id']. "'",__LINE__,__FILE__);


			$receipt['message'][]=array('msg'=>lang('category has been edited'));
			return $receipt;
		}

		function delete($id,$type,$type_id)
		{
			$table = $this->select_table($type, $type_id);

			$this->db->query("DELETE FROM $table WHERE id='" . $id . "'",__LINE__,__FILE__);
		}

		function select_category_list($type)
		{
			$table = $this->select_table($type);

			$this->db->query("SELECT id, descr FROM $table ORDER BY id ");

			while ( $this->db->next_record() )
			{
				$categories = array
				(
					'id'	=> $this->db->f('id'),
					'name'	=> $this->db->f('descr', true)
				);
			}
			return $categories;
		}		
	}
