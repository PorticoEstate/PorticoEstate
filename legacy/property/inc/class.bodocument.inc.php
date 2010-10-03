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

	class property_bodocument
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $entity_id;
		var $status_id;

		var $public_functions = array
		(
			'read'			=> true,
			'read_single'		=> true,
			'save'			=> true,
			'delete'		=> true,
			'check_perms'		=> true
		);

		function property_bodocument($session=false)
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so 			= CreateObject('property.sodocument');
			$this->bocommon 	= CreateObject('property.bocommon');
			$this->solocation 	= CreateObject('property.solocation');
			$this->historylog	= CreateObject('property.historylog','document');
			$this->contacts		= CreateObject('property.soactor');
			$this->contacts->role='vendor';

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start	= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query	= phpgw::get_var('query');
			$sort	= phpgw::get_var('sort');
			$order	= phpgw::get_var('order');
			$filter	= phpgw::get_var('filter', 'int');
			$cat_id	= phpgw::get_var('cat_id', 'int');
			$status_id	= phpgw::get_var('status_id');
			$entity_id	= phpgw::get_var('entity_id', 'int');
			$doc_type	= phpgw::get_var('doc_type');
			$query_location	= phpgw::get_var('query_location');


			if ($start)
			{
				$this->start=$start;
			}
			else
			{
				$this->start=0;
			}

			if(isset($query))
			{
				$this->query = $query;
			}
			if(isset($filter))
			{
				$this->filter = $filter;
			}
			if(isset($sort))
			{
				$this->sort = $sort;
			}
			if(isset($order))
			{
				$this->order = $order;
			}
			if(isset($cat_id))
			{
				$this->cat_id = $cat_id;
			}
			if(isset($status_id))
			{
				$this->status_id = $status_id;
			}
			if($entity_id)
			{
				$this->entity_id = $entity_id;
			}
			if(isset($doc_type))
			{
				$this->doc_type = $doc_type;
			}
			if(isset($query_location))
			{
				$this->query_location = $query_location;
			}

		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','document',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','document');

			//_debug_array($data);

			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->sort		= $data['sort'];
			$this->order	= $data['order'];
			$this->cat_id	= $data['cat_id'];
		//	$this->entity_id	= $data['entity_id'];
			$this->doc_type	= $data['doc_type'];
			$this->query_location	= $data['query_location'];
		}

		function select_status_list($format='',$selected='')
		{
			switch($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('status_select'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('status_filter'));
					break;
			}

			$status_entries= $this->so->select_status_list();
			return $this->bocommon->select_list($selected,$status_entries);
		}

		function select_branch_list($selected='')
		{
			$branch_entries= $this->so->select_branch_list();
			return $this->bocommon->select_list($selected,$branch_entries);
		}

		function read()
		{
			$document = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'filter' => $this->filter,'cat_id' => $this->cat_id,'entity_id' => $this->entity_id,'doc_type'=>$this->doc_type));
			$this->total_records = $this->so->total_records;

			$this->uicols	= $this->so->uicols;
			$cols_extra		= $this->so->cols_extra;

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			for ($i=0; $i<count($document); $i++)
			{
				$location_data=$this->solocation->read_single($document[$i]['location_code']);

				for ($j=0;$j<count($cols_extra);$j++)
				{
					$document[$i][$cols_extra[$j]] = $location_data[$cols_extra[$j]];
				}
			}

			return $document;
		}

		function read_at_location($location_code='')
		{
			$document = $this->so->read_at_location(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'filter' => $this->filter,'cat_id' => $this->cat_id,'entity_id' => $this->entity_id,
											'location_code' => $location_code,'doc_type'=>$this->doc_type));
			$this->total_records = $this->so->total_records;

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			for ($i=0; $i<count($document); $i++)
			{
				$document[$i]['user'] = $GLOBALS['phpgw']->accounts->id2name($document[$i]['user_id']);
				$document[$i]['document_date'] = $GLOBALS['phpgw']->common->show_date($document[$i]['start_date'],$dateformat);
				$document[$i]['entry_date'] = $GLOBALS['phpgw']->common->show_date($document[$i]['entry_date'],$dateformat);
			}

			return $document;
		}

		function read_single($document_id)
		{
			$document						= $this->so->read_single($document_id);
			$dateformat						= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$document['document_date']		= $GLOBALS['phpgw']->common->show_date($document['document_date'],$dateformat);

			if(isset($document['vendor_id']) && $document['vendor_id'])
			{
				$custom 				= createObject('property.custom_fields');
				$vendor['attributes']	= $custom->find('property','.vendor', 0, '', 'ASC', 'attrib_sort', true, true);
				$vendor					= $this->contacts->read_single($document['vendor_id'],$vendor);
				foreach($vendor['attributes'] as $attribute)
				{
					if($attribute['name']=='org_name')
					{
						$document['vendor_name']=$attribute['value'];
						break;
					}
				}
			}

			if($document['location_code'])
			{
				$document['location_data'] =$this->solocation->read_single($document['location_code']);
			}

			if($document['p_num'])
			{
				$soadmin_entity	= CreateObject('property.soadmin_entity');
				$category = $soadmin_entity->read_single_category($document['p_entity_id'],$document['p_cat_id']);

				$document['p'][$document['p_entity_id']]['p_num']=$document['p_num'];
				$document['p'][$document['p_entity_id']]['p_entity_id']=$document['p_entity_id'];
				$document['p'][$document['p_entity_id']]['p_cat_id']=$document['p_cat_id'];
				$document['p'][$document['p_entity_id']]['p_cat_name'] = $category['name'];
			}

			return $document;
		}

		function read_location_data($location_code)
		{
			$location_data= 	$this->solocation->read_single($location_code);

			return $location_data;
		}

		function select_category_list($format='',$selected='')
		{
			$soadmin_entity 	= CreateObject('property.soadmin_entity');
			$categories		= $soadmin_entity->read_category(array('allrows'=>true,'entity_id'=>$this->entity_id));

			$category_list	= $this->bocommon->select_list($selected,$categories);

			return $category_list;
		}



		function read_record_history($id)
		{
			$history_array = $this->historylog->return_array(array('O'),array(),'','',$id);
			$i=0;
			while (is_array($history_array) && list(,$value) = each($history_array))
			{

				$record_history[$i]['value_date']	= $GLOBALS['phpgw']->common->show_date($value['datetime']);
				$record_history[$i]['value_user']	= $value['owner'];

				switch ($value['status'])
				{
					case 'R': $type = lang('Re-opened'); break;
					case 'X': $type = lang('Closed');    break;
					case 'O': $type = lang('Opened');    break;
					case 'A': $type = lang('Re-assigned'); break;
					case 'P': $type = lang('Priority changed'); break;
					case 'CO': $type = lang('Initial Coordinator'); break;
					case 'C': $type = lang('Coordinator changed'); break;
					case 'TO': $type = lang('Initial Category'); break;
					case 'T': $type = lang('Category changed'); break;
					case 'SO': $type = lang('Initial Status'); break;
					case 'S': $type = lang('Status changed'); break;
					case 'FO': $type = lang('Initial File'); break;
					case 'F': $type = lang('File changed'); break;
					case 'LO': $type = lang('Initial Link'); break;
					case 'L': $type = lang('Link changed'); break;
					default: break;
				}

				if($value['new_value']=='O'){$value['new_value']=lang('Opened');}
				if($value['new_value']=='X'){$value['new_value']=lang('Closed');}


				$record_history[$i]['value_action']	= $type?$type:'';
				unset($type);

				if ($value['status'] == 'A')
				{
					if (! $value['new_value'])
					{
						$record_history[$i]['value_new_value']	= lang('None');
					}
					else
					{
						$record_history[$i]['value_new_value']	= $GLOBALS['phpgw']->accounts->id2name($value['new_value']);
					}
				}
				else if ($value['status'] == 'C' || $value['status'] == 'CO')
				{
					$record_history[$i]['value_new_value']	= $GLOBALS['phpgw']->accounts->id2name($value['new_value']);
				}
				else if ($value['status'] == 'T' || $value['status'] == 'TO')
				{
					$record_history[$i]['value_new_value']	= $this->so->read_single_category($value['new_value']);
				}
				else if ($value['status'] != 'O' && $value['new_value'])
				{
					$record_history[$i]['value_new_value']	= $value['new_value'];
				}
				else
				{
					$record_history[$i]['value_new_value']	= '';
				}

				$i++;
			}

			return $record_history;
		}

		function save($values)
		{

			$document_date	= $this->bocommon->date_array($values['document_date']);
			$values['document_date']	= mktime (2,0,0,$document_date['month'],$document_date['day'],$document_date['year']);

			while (is_array($values['location']) && list(,$value) = each($values['location']))
			{
				if($value)
				{
					$location[] = $value;
				}
			}

			$values['location_code']=implode("-", $location);

//_debug_array($values);
			if ($values['document_id'])
			{
				if ($values['document_id'] != 0)
				{
					$receipt = $this->so->edit($values);
				}
			}
			else
			{
				$receipt = $this->so->add($values);
			}
			return $receipt;
		}

		function delete($document_id)
		{
			$this->so->delete($document_id);
		}
	}

