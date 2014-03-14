<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage document
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_sodocument
	{

		function __construct()
		{
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon				= CreateObject('property.bocommon');
			$this->historylog			= CreateObject('property.historylog','document');
			$this->vfs 					= CreateObject('phpgwapi.vfs');
			$this->vfs->fakebase 		= '/property';
			$this->fakebase 			= $this->vfs->fakebase;
			$this->cats					= CreateObject('phpgwapi.categories', -1, 'property', '.document');
			$this->cats->supress_info	= true;

			$this->db           		= & $GLOBALS['phpgw']->db;
			$this->join					= & $this->db->join;
			$this->like					= & $this->db->like;
		}


		function select_status_list()
		{
			$status = array();
			$this->db->query("SELECT id, descr FROM fm_document_status ORDER BY id ");

			while ($this->db->next_record())
			{
				$status[] = array
					(
						'id'	=> $this->db->f('id'),
						'name'	=> $this->db->f('descr',true)
					);
			}
			return $status;
		}

		function select_branch_list()
		{
			$branch = array();
			$this->db->query("SELECT id, descr FROM fm_branch ORDER BY id ");

			while ($this->db->next_record())
			{
				$branch[] = array
					(
						'id'	=> $this->db->f('id'),
						'name'	=> $this->db->f('descr',true)
					);
			}
			return $branch;
		}

		function read($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$filter		= isset($data['filter'])?$data['filter']:'';
				$query		= isset($data['query'])?$data['query']:'';
				$sort		= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
				$order		= isset($data['order'])?$data['order']:'';
				$cat_id		= isset($data['cat_id']) && $data['cat_id'] ? $data['cat_id']:0;
				$entity_id	= isset($data['entity_id'])?$data['entity_id']:'';
				$doc_type	= isset($data['doc_type']) && $data['doc_type'] ? $data['doc_type']: 0;
			}

			$doc_types = $this->get_sub_doc_types($doc_type);

			$sql = $this->bocommon->fm_cache('sql_document_' . $entity_id);

			if(!$sql)
			{

				$document_table = 'fm_document';

				$cols = $document_table . '.location_code';
				$cols_return[] = 'location_code';

				$uicols = array();
				$joinmethod = '';
				$paranthesis = '';

				if ($entity_id)
				{
					$cols .= ",$document_table.p_num as p_num";
					$cols_return[] 				= 'p_num';
					$uicols['input_type'][]		= 'text';
					$uicols['name'][]			= 'p_num';
					$uicols['descr'][]			= lang('ID');
					$uicols['statustext'][]		= lang('ID');

					$cols .= ',fm_entity_category.name as category';
					$cols_return[] 				= 'category';
					$uicols['input_type'][]		= 'text';
					$uicols['name'][]			= 'category';
					$uicols['descr'][]			= lang('Type');
					$uicols['statustext'][]		= lang('Type');

					$cols .= ",$document_table.p_entity_id";
					$cols_return[] 				= 'p_entity_id';
					$cols .= ",$document_table.p_cat_id";
					$cols_return[] 				= 'p_cat_id';

					$joinmethod .= " $this->join  fm_entity_category ON (fm_entity_category.entity_id =$document_table.p_entity_id AND fm_entity_category.id = $document_table.p_cat_id))";
					$paranthesis .='(';

				}


				$sql	= $this->bocommon->generate_sql(array('entity_table'=> $document_table,
					'cols'			=> $cols,
					'cols_return'	=> $cols_return,
					'uicols'		=> $uicols,
					'joinmethod'	=> $joinmethod,
					'paranthesis'	=> $paranthesis,
					'query'			=> $query,
					'force_location'=> true,
					'no_address'	=> false,
				));


				$this->bocommon->fm_cache('sql_document_' . $entity_id,$sql);

				$this->uicols		= $this->bocommon->uicols;
				$cols_return		= $this->bocommon->cols_return;
				$type_id			= $this->bocommon->type_id;
				$this->cols_extra	= $this->bocommon->cols_extra;

				$this->bocommon->fm_cache('uicols_document_' . $entity_id,$this->uicols);
				$this->bocommon->fm_cache('cols_return_document_' . $entity_id,$cols_return);
				$this->bocommon->fm_cache('type_id_document_' . $entity_id,$type_id);
				$this->bocommon->fm_cache('cols_extra_document_' . $entity_id,$this->cols_extra);

			}
			else
			{
				$this->uicols		= $this->bocommon->fm_cache('uicols_document_' . $entity_id);
				$cols_return		= $this->bocommon->fm_cache('cols_return_document_' . $entity_id);
				$type_id			= $this->bocommon->fm_cache('type_id_document_' . $entity_id);
				$this->cols_extra	= $this->bocommon->fm_cache('cols_extra_document_' . $entity_id);
			}

			$groupmethod= " GROUP BY fm_document.location_code, fm_location1.loc1_name";

			if ($entity_id)
			{

				$groupmethod.= " ,fm_document.p_entity_id,fm_entity_category.name,fm_document.p_num,fm_document.p_cat_id";
			}

			//FIXME
			$groupmethod = '';

			if ($order)
			{
				$ordermethod = " order by fm_document.$order $sort";
			}
			else
			{
				$ordermethod = ' order by fm_document.location_code ASC';
			}

			$where= 'WHERE';

			$filtermethod = '';

			$GLOBALS['phpgw']->config->read();
			if(isset($GLOBALS['phpgw']->config->config_data['acl_at_location']) && $GLOBALS['phpgw']->config->config_data['acl_at_location'])
			{
				$access_location = $this->bocommon->get_location_list(PHPGW_ACL_READ);
				$filtermethod = " WHERE fm_document.loc1 in ('" . implode("','", $access_location) . "')";
				$where= 'AND';
			}

			if(!$entity_id)
			{
				$filtermethod .= " $where ( fm_document.p_num is NULL OR fm_document.p_num='') ";
				$where= 'AND';
			}

			if ($cat_id)
			{
				$filtermethod .= " $where fm_document.p_cat_id=$cat_id ";
				$where= 'AND';
			}

			if ($doc_types && is_array($doc_types))
			{
				$filtermethod .= " $where fm_document.category IN (". implode(',', $doc_types) . ')';
				$where = 'AND';
			}

			if ($filter!='all' && $filter)
			{
				$filtermethod .= " $where fm_document.user_id='$filter' ";
			}

			$querymethod = '';
			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$querymethod = " $where (fm_document.address $this->like '%$query%' or fm_document.location_code $this->like '$query%')";
			}

			$sql .= " $filtermethod $querymethod $groupmethod";

			//echo $sql;

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();
			$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);

			$document_list = array();
			$j=0;
			while ($this->db->next_record())
			{
				for ($i=0;$i<count($cols_return);$i++)
				{
					$document_list[$j][$cols_return[$i]] = stripslashes($this->db->f($cols_return[$i]));
				}

				$location_code=	$this->db->f('location_code');
				$location = explode('-',$location_code);
				for ($m=0;$m<count($location);$m++)
				{
					$document_list[$j]['loc' . ($m+1)] = $location[$m];
					$document_list[$j]['query_location']['loc' . ($m+1)]=implode("-", array_slice($location, 0, ($m+1)));
				}

				$j++;
			}


			//_debug_array($document_list);
			return $document_list;
		}

		function read_at_location($data)
		{
			if(is_array($data))
			{
				$start			= isset($data['start']) && $data['start'] ? $data['start']:0;
				$query			= isset($data['query'])?$data['query']:'';
				$sort			= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
				$order			= isset($data['order'])?$data['order']:'';
				$filter			= isset($data['filter']) && $data['filter'] ? (int) $data['filter']: 0;
				$entity_id		= isset($data['entity_id']) && $data['entity_id'] ? (int)$data['entity_id']:0;
				$cat_id			= isset($data['cat_id']) && $data['cat_id'] ? (int)$data['cat_id']: 0;
				$doc_type		= isset($data['doc_type']) && $data['doc_type'] ? $data['doc_type']: 0;
				$allrows		= isset($data['allrows'])?$data['allrows']:'';
				$location_code	= isset($data['location_code'])?$data['location_code']:'';
			}

			if( !$location_code )
			{
				return array();
			}

			$doc_types = $this->get_sub_doc_types($doc_type);

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by location_code ASC';
			}

			$where = 'WHERE';
			$filtermethod = '';
			if($location_code)
			{
				$filtermethod = " $where fm_document.location_code $this->like '$location_code%'";
				$where = 'AND';
			}

			if ($doc_types && is_array($doc_types))
			{
				$filtermethod .= " $where fm_document.category IN (". implode(',', $doc_types) . ')';
				$where = 'AND';
			}
			if ($cat_id > 0)
			{
				$filtermethod .= " $where fm_document.p_cat_id={$cat_id} AND fm_document.p_entity_id={$entity_id} ";
				$where = 'AND';
			}

			if ($filter > 0)
			{
				$filtermethod .= "  $where fm_document.user_id='$filter' ";
				$where = 'AND';
			}

			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$querymethod = " $where (fm_document.title $this->like '%$query%' OR fm_document.document_name"
					. " $this->like '%$query%')";
			}

			$sql = "SELECT fm_document.*, phpgw_categories.cat_name as category FROM fm_document"
				. " $this->join phpgw_categories on fm_document.category = phpgw_categories.cat_id"
				. " $filtermethod $querymethod";

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

			$document_list = array();
			while ($this->db->next_record())
			{
				$document_list[] = array
					(
						'document_id'		=> $this->db->f('id'),
						'document_name'		=> $this->db->f('document_name', true),
						'link'				=> $this->db->f('link', true),
						'title'				=> $this->db->f('title', true),
						'doc_type'			=> $this->db->f('category'),
						'user_id'			=> $this->db->f('coordinator'),
						'document_date'		=> $this->db->f('document_date')
					);
			}

			return $document_list;
		}

		function read_single($document_id)
		{
			$sql = "SELECT * from fm_document where id='$document_id'";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$document['document_id']		= $this->db->f('id');
				$document['title']				= $this->db->f('title', true);
				$document['document_name']		= $this->db->f('document_name', true);
				$document['link']				= $this->db->f('link', true);
				$document['location_code']		= $this->db->f('location_code');
				$document['branch_id']			= $this->db->f('branch_id');
				$document['version']			= $this->db->f('version', true);
				$document['vendor_id']			= $this->db->f('vendor_id');
				$document['floor_id']			= $this->db->f('floor_id');
				$document['descr']				= $this->db->f('descr', true);
				$document['status']				= $this->db->f('status');
				$document['user_id']			= $this->db->f('user_id');
				$document['coordinator']		= $this->db->f('coordinator');
				$document['access']				= $this->db->f('access');
				$document['document_date']		= $this->db->f('document_date');
				$document['doc_type']			= $this->db->f('category');
				$document['p_num']				= $this->db->f('p_num');
				$document['p_entity_id']		= $this->db->f('p_entity_id');
				$document['p_cat_id']			= $this->db->f('p_cat_id');
			}

			//_debug_array($document);
			return $document;
		}

		function add($document)
		{
			if(isset($document['link']) && $document['link'])
			{
				$document['link'] = str_replace('\\' , '/', $document['link']);	
			}

			while (is_array($document['location']) && list($input_name,$value) = each($document['location']))
			{
				if($value)
				{
					$cols[] = $input_name;
					$vals[] = $value;
				}
			}

			while (is_array($document['extra']) && list($input_name,$value) = each($document['extra']))
			{
				if($value)
				{
					$cols[] = $input_name;
					$vals[] = $value;
				}
			}

			if($cols)
			{
				$cols	= "," . implode(",", $cols);
				$vals	= ",'" . implode("','", $vals) . "'";
			}

			if($document['street_name'])
			{
				$address[]= $document['street_name'];
				$address[]= $document['street_number'];
				$address	= $this->db->db_addslashes(implode(" ", $address));
			}

			if(!$address)
			{
				$address = $this->db->db_addslashes($document['location_name']);
			}

			$document['descr'] = $this->db->db_addslashes($document['descr']);
			$document['title'] = $this->db->db_addslashes($document['title']);
			//_debug_array($document);

			$values= array(
				$this->db->db_addslashes($document['document_name']),
				$this->db->db_addslashes($document['link']),
				$document['title'],
				'public',
				$document['doc_type'],
				time(),
				$document['document_date'],
				$document['version'],
				$document['coordinator'],
				$document['status'],
				$document['descr'],
				$document['location_code'],
				$address,
				$document['branch_id'],
				$document['vendor_id'],
				$this->account);

			$values	= $this->db->validate_insert($values);

			$this->db->query("INSERT INTO fm_document (document_name,link,title,access,category,entry_date,document_date,version,coordinator,status,"
				. "descr,location_code,address,branch_id,vendor_id,user_id $cols) "
				. "VALUES ($values $vals )",__LINE__,__FILE__);

			$receipt['document_id'] = $this->db->get_last_insert_id('fm_document','id');

			$this->historylog->add('SO',$receipt['document_id'],$document['status']);
			$this->historylog->add('TO',$receipt['document_id'],$document['doc_type']);
			if($document['coordinator'])
			{
				$this->historylog->add('CO',$receipt['document_id'],$document['coordinator']);
			}
			if($document['document_name'])
			{
				$this->historylog->add('FO',$receipt['document_id'],$document['coodocument_name']);
			}
			if($document['link'])
			{
				$this->historylog->add('LO',$receipt['document_id'],$document['link']);
			}

			$receipt['message'][] = array('msg'=>lang('document %1 has been saved',"'".$document['document_name']."'"));
			return $receipt;
		}

		function edit($document)
		{

			if(isset($document['link']) && $document['link'])
			{
				$document['link'] = str_replace('\\' , '/', $document['link']);	
			}

			$receipt = array();
			$value_set=array();
			if (isset($document['location']) && is_array($document['location']))
			{
				foreach($document['location'] as $input_name => $value)
				{
					$value_set[$input_name]	= $value;
				}
			}

			if (isset($document['extra']) && is_array($document['extra']))
			{
				foreach($document['extra'] as $input_name => $value)
				{
					$value_set[$input_name]	= $value;
				}
			}

			if($document['street_name'])
			{
				$address[]= $document['street_name'];
				$address[]= $document['street_number'];
				$address	= $this->db->db_addslashes(implode(" ", $address));
			}

			if(!$address)
			{
				$address = $this->db->db_addslashes($document['location_name']);
			}

			$this->db->query("SELECT status,category,coordinator,document_name,location_code,link,p_entity_id,p_cat_id,p_num FROM fm_document where id='" .$document['document_id']."'",__LINE__,__FILE__);
			$this->db->next_record();

			$old_status			= $this->db->f('status');
			$old_doc_type		= $this->db->f('category');
			$old_coordinator	= $this->db->f('coordinator');
			$old_document_name	= $this->db->f('document_name');
			$old_link			= $this->db->f('link',true);
			$old_location_code	= $this->db->f('location_code');
			$old_p_entity_id	= $this->db->f('p_entity_id');
			$old_p_cat_id		= $this->db->f('p_cat_id');
			$old_p_num			= $this->db->f('p_num');

			$move_file = false;

			if($old_location_code != $document['location_code'])
			{
				$move_file = true;
			}

			if("{$old_p_entity_id}_{$old_p_cat_id}" != "{$document['extra']['p_entity_id']}_{$document['extra']['p_cat_id']}")
			{
				$move_file = true;
			}

			if ($old_status != $document['status'])
			{
				$this->historylog->add('S',$document['document_id'],$document['status'],$old_status);
			}
			if ($old_doc_type != $document['doc_type'])
			{
				$this->historylog->add('T',$document['document_id'],$document['doc_type'],$old_doc_type);
				$move_file = true;
			}
			if ((int)$old_coordinator != (int)$document['coordinator'])
			{
				$this->historylog->add('C',$document['document_id'],$document['coordinator'],$old_coordinator);
			}

			if($document['document_name_orig'] && !$document['document_name'] )
			{
				$document['document_name'] = $document['document_name_orig'];
			}

			if($old_link != $document['link'] )
			{
				$this->historylog->add('L',$document['document_id'],$this->db->db_addslashes($document['link']),$old_link);
				$alter_link=true;
			}


			if ($old_document_name && ($old_document_name != $document['document_name'] || $move_file = true))
			{
				if($document['link'] && !$alter_link)
				{
					$this->historylog->add('L',$document['document_id'],$document['link'],$old_document_name);
				}
				else
				{
					$this->historylog->add('F',$document['document_id'],$document['document_name'],$old_document_name);
				}

				// file is already moved
			/*	if($old_p_entity_id)
				{
					$file = "{$this->fakebase}/document/entity_{$old_p_entity_id}_{$old_p_cat_id}/{$old_p_num}/{$old_doc_type}/$old_document_name";
				}
				else
				{
					$file = "{$this->fakebase}/document/{$old_location_code}/{$old_doc_type}/{$old_document_name}";
				}

				$receipt= $this->delete_file($file);
			 */
			}

			if($document['link'])
			{
				$document['document_name'] = '';
			}

			$value_set['document_name']	= $this->db->db_addslashes($document['document_name']);
			$value_set['link']			= $this->db->db_addslashes($document['link']);
			$value_set['title']			= $this->db->db_addslashes($document['title']);
			$value_set['branch_id']		= $document['branch_id'];
			$value_set['status']		= $document['status'];
			$value_set['category']		= $document['doc_type'];
			$value_set['document_date']	= $document['document_date'];
			$value_set['coordinator']	= $document['coordinator'];
			$value_set['descr']			= $this->db->db_addslashes($document['descr']);
			$value_set['version']		= $document['version'];
			$value_set['location_code']	= $document['location_code'];
			$value_set['vendor_id']		= $document['vendor_id'];
			$value_set['address']		= $address;

			$value_set	= $this->db->validate_update($value_set);

			$this->db->query("UPDATE fm_document set $value_set $vals WHERE id= '" . $document['document_id'] ."'",__LINE__,__FILE__);

			$receipt['document_id'] = $document['document_id'];
			$receipt['message'][] = array('msg'=>lang('document %1 has been edited',"'".$document['title']."'"));
			return $receipt;
		}

		/**
		 * Get a list of doc-types , included subtypes
		 *
		 * @param int $doc_type the parent doc-type
		 * @return array parent and children
		 */

		function get_sub_doc_types($doc_type = 0)
		{
			$doc_types = array();
			if($doc_type)
			{
				$doc_types[] = $doc_type;
				$cat_sub = $this->cats->return_sorted_array($start = 0,$limit = false,$query = '',$sort = '',$order = '',$globals = False, $parent_id = $doc_type);
				foreach ($cat_sub as $doc_type)
				{
					$doc_types[] = $doc_type['id'];
				}
			}
			return $doc_types;
		}


		/**
		 * Get a hierarchical array of doc-types with number of hits for each level at locatin and link to list.
		 * Basis for the folder-menu
		 *
		 * @param string $location_code location_code in the (physical) location-hierarchy
		 * @return array parent and children
		 */

		function get_files_at_location($data)
		{
			$location_code = isset($data['location_code']) ? $data['location_code'] : '';
			$entity_id = (int)$data['entity_id'];
			$cat_id = (int)$data['cat_id'];
			$num = $data['num'];

			if( !$location_code )
			{
				if( !$entity_id  || !$cat_id || !$num)
				{
					throw new Exception("property_soentity::read_entity_to_link - Missing entity information info in input");
				}
			}
			else if( !$entity_id  || !$cat_id || !$num)
			{
				if( !$location_code)
				{
					throw new Exception("property_soentity::read_entity_to_link - Missing entity information info in input");
				}
			}

			$acl_add = $GLOBALS['phpgw']->acl->check('.document', PHPGW_ACL_ADD, 'property');
			$documents = array();
			if($location_code)
			{
				$sql = "SELECT count(*) as hits FROM fm_document WHERE location_code {$this->like} '$location_code%' AND p_num IS NULL";
			}
			else
			{
				$sql = "SELECT count(*) as hits FROM fm_document WHERE p_entity_id = {$entity_id} AND p_cat_id = {$cat_id} AND p_num = '{$num}'";
			}
			$this->db->query($sql,__LINE__,__FILE__);
			if($this->db->next_record())
			{
				$hits = (int) $this->db->f('hits');

				$x = 0; // within level
				$y = 0; //level
				$cache_x_at_y[$y] = $x;
				$documents[$x] = array
					(
						'link'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'property.uidocument.list_doc','location_code'=> $location_code,
															'entity_id' => $entity_id,
															'cat_id'	=> $cat_id,
															'p_num'		=> $num)),
						'text'		=> lang('documents') . ' [' . $hits . ']:',
						'descr'		=> lang('Documentation'),
						'level'		=> 0
					);
			}
			else
			{
//				return $documents;
			}

			$categories = $this->cats->return_sorted_array(0, false);

			$location_filter = 'WHERE 1=1';

			if($location_code)
			{
				$location_filter = "WHERE location_code {$this->like} '{$location_code}%' AND p_num IS NULL";
			}
			else
			{
				$location_filter = "WHERE p_entity_id = {$entity_id} AND p_cat_id = {$cat_id} AND p_num = '{$num}'";
			}

			foreach ($categories as $category)
			{
				$doc_types = $this->get_sub_doc_types($category['id']);

				$sql = "SELECT count(*) as hits FROM fm_document {$location_filter} AND category IN (". implode(',', $doc_types) . ')';
				$this->db->query($sql,__LINE__,__FILE__);
				$this->db->next_record();
				$hits = (int) $this->db->f('hits');

				$level = $category['level']+1;
				if($level == $y)
				{
					$x++;
				}
				else if($level < $y )
				{
					$x = $cache_x_at_y[$level]+1;
				}
				else if($level > $y )
				{
					$x = 0;
				}
				$y = $level;

				$map = '$documents'; 
				for ($i = 0; $i < $level ; $i++)
				{

					$map .= '[' . $cache_x_at_y[$i] ."]['children']"; 
				}

				$map .= '[]'; 

				eval($map . ' =array('
					.	"'link'	=> '" . $GLOBALS['phpgw']->link('/index.php',array('menuaction'	=> 'property.uidocument.list_doc',
																				'location_code'	=> $location_code,
																				'doc_type'		=> $category['id'],
																				'entity_id' 	=> $entity_id,
																				'cat_id'		=> $cat_id,
																				'p_num'			=> $num)) . "',\n"
					.	"'text'			=> '" . $category['name'] . ' [' . $hits . ']' . "',\n"
					.	"'descr'		=> '" . lang('Documentation') . "',\n"
					.	"'level'		=> "  . ($category['level']+1) . "\n"
					. ');');

				$cache_x_at_y[$y] = $x;

//--add node

				if($acl_add && count($doc_types) == 1) // node
				{
					$level = $level+1;
					if($level == $y)
					{
						$x++;
					}
					else if($level < $y )
					{
						$x = $cache_x_at_y[$level]+1;
					}
					else if($level > $y )
					{
						$x = 0;
					}
					$y = $level;
	
					$map = '$documents'; 
					for ($i = 0; $i < $level ; $i++)
					{
						$map .= '[' . $cache_x_at_y[$i] ."]['children']"; 
					}

					$map .= '[]'; 

					eval($map . ' =array('
						.	"'link'	=> '" . $GLOBALS['phpgw']->link('/index.php',array('menuaction'	=> 'property.uidocument.edit',
																				'location_code'	=> $location_code,
																				'doc_type'		=> $category['id'],
																				'p_entity_id' 	=> $entity_id,
																				'p_cat_id'		=> $cat_id,
																				'p_num'			=> $num)) . "',\n"
						.	"'text'			=> '" . lang('add') . "',\n"
						.	"'descr'		=> '" . lang('Add Document') . "',\n"
						.	"'level'		=> "  . $y . "\n"
						. ');');

					$cache_x_at_y[$y] = $x;
				}
			}
			return $documents;
		}

		function delete_file($file)
		{
			$receipt = array();
			if($this->vfs->file_exists(array(
				'string' => $file,
				'relatives' => Array(RELATIVE_NONE)
			)))
			{
				$this->vfs->override_acl = 1;

				if(!$this->vfs->rm (array(
					'string' => $file,
					'relatives' => array(
						RELATIVE_NONE
					)
				)))
				{
					$receipt['error'][]=array('msg'=>lang('failed to delete file') . ' :'. $file);
				}
				else
				{
					$receipt['message'][]=array('msg'=>lang('file deleted') . ' :'. $file);
				}
				$this->vfs->override_acl = 0;
			}
			return $receipt;
		}

		function delete($document_id)
		{
			$receipt = array();
			$document_id = (int) $document_id;
			$this->db->query("SELECT document_name,location_code,p_num,p_entity_id,p_cat_id,category FROM fm_document where id='$document_id'",__LINE__,__FILE__);
			$this->db->next_record();
			$document_name	= $this->db->f('document_name');
			$location_code	= $this->db->f('location_code');
			$p_entity_id	= $this->db->f('p_entity_id');
			$p_cat_id		= $this->db->f('p_cat_id');
			$p_num		= $this->db->f('p_num');
			$category	= $this->db->f('category');

			if($document_name)
			{
				if($p_cat_id > 0)
				{
					$file = "{$this->fakebase}/document/entity_{$p_entity_id}_{$p_cat_id}/{$p_num}/{$category}/$document_name";
				}
				else
				{
					$file = "{$this->fakebase}/document/{$location_code}/{$category}/{$document_name}";
				}

				$receipt= $this->delete_file($file);
			}
			if(!isset($receipt['error']))
			{
				$this->db->transaction_begin();
				$this->db->query("DELETE FROM fm_document WHERE id={$document_id}",__LINE__,__FILE__);
				$this->db->query("DELETE FROM fm_document_history  WHERE  history_record_id={$document_id}",__LINE__,__FILE__);
				$this->db->transaction_commit();
			}
			return $receipt;
		}


		/**
		 * used for retrive a child-node from a hierarchy
		 *
		 * @param string $dirname current path
		 * @param integer $level is increased when we go deeper into the tree,
		 * @param integer $maks_level is how deep we want to go
		 * @param integer $filter_level search for filter at a predefined level
		 * @param string $filter
		 * @param string $menuaction is used to make an url to the item
		 * @return array $child Children
		 */

		protected function get_children($dirname, $level, $maks_level = 0, $filter_level=1, $filter = 'hei', $menuaction)
		{
			// prevent path traversal
			if ( preg_match('/\./', $dirname) 
				|| !is_dir($dirname) )
			{
				return array();
			}
			$children = array();

			$dir = new DirectoryIterator($dirname); 
			if ( is_object($dir) )
			{
				foreach ( $dir as $file )
				{
					if ( ($file->isDot() || !$file->isReadable())
						|| ($level == $filter_level && !preg_match("/{$filter}/i", $file->getFilename()))
					)
					{
						continue;
					}
					$children[] =array
						(
							'link'			=> $GLOBALS['phpgw']->link('/home.php'),
							'text'			=> $file->getFilename(),
							'is_dir'		=> $file->isDir(),
							'path'			=> $file->getPathname(),
							'level'			=> $level,
						);
				}
			}

			foreach($children as &$child)
			{
				if($child['is_dir'] && $child['level'] < ($maks_level))
				{
					if($_children = $this->get_children($child['path'], ($child['level']+1), $maks_level, $menuaction))
					{
						$child['children'] = $_children;
					}
				}
			}
			return $children;
		}

		/**
		 * used for retrive a filetree from a given start point
		 *
		 * @param string $dirname Start point
		 * @param integer $maks_level is how deep we want to go
		 * @param integer $filter_level search for filter at a predefined level
		 * @param string $filter
		 * @param string $menuaction is used to make an url to the item
		 * @return array $child Children
		 */

		public function read_file_tree($dirname = '', $maks_level = 2, $filter_level, $filter, $menuaction = '')
		{
			$dirname = $dirname ? $dirname : $GLOBALS['phpgw_info']['server']['temp_dir'];
			// prevent path traversal
			if ( preg_match('/\./', $dirname) 
				|| !is_dir($dirname) )
			{
				return array();
			}

			$file_list = array();
			$dir = new DirectoryIterator($dirname); 
			if ( is_object($dir) )
			{
				foreach ( $dir as $file )
				{
					if ( $file->isDot()
						|| !$file->isReadable()
					)
					{
						continue;
					}
					$file_list[] =array
						(
							'link'			=> $GLOBALS['phpgw']->link('/home.php'),
							'text'			=> $file->getFilename(),
							'is_dir'		=> $file->isDir(),
							'path'			=> $file->getPathname(),
							'level'			=> 0,
						);
				}
			}

			foreach($file_list as &$file)
			{
				if($file['is_dir'])
				{
					if ($children = $this->get_children($file['path'], 1, $maks_level, $filter_level, $filter, $menuaction))
					{
						$file['children'] = $children;
					}
				}
			}
			return $file_list;
		}
	}
