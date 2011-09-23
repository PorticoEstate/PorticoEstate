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
	phpgw::import_class('phpgwapi.datetime');

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
		var $allrows;

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
			$this->so 				= CreateObject('property.sodocument');
			$this->bocommon 		= CreateObject('property.bocommon');
			$this->solocation 		= CreateObject('property.solocation');
			$this->historylog		= CreateObject('property.historylog','document');
			$this->contacts			= CreateObject('property.sogeneric');
			$this->contacts->get_location_info('vendor',false);
			$this->cats				= & $this->so->cats;
			$this->bofiles			= CreateObject('property.bofiles');

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start					= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query					= phpgw::get_var('query');
			$sort					= phpgw::get_var('sort');
			$order					= phpgw::get_var('order');
			$filter					= phpgw::get_var('filter', 'int');
			$cat_id					= phpgw::get_var('cat_id', 'int');
			$status_id				= phpgw::get_var('status_id');
			$entity_id				= phpgw::get_var('entity_id', 'int');
			$doc_type				= phpgw::get_var('doc_type');
			$query_location			= phpgw::get_var('query_location');
			$allrows				= phpgw::get_var('allrows', 'bool');

			$this->start			= $start ? $start : 0;
			$this->query			= isset($query) ? $query : '';
			$this->sort				= isset($sort) && $sort ? $sort : '';
			$this->order			= isset($order) && $order ? $order : '';
			$this->filter			= isset($filter) && $filter ? $filter : '';
			$this->cat_id			= isset($cat_id) && $cat_id ? $cat_id : '';
			$this->status_id		= isset($status_id) && $status_id ? $status_id : '';
			$this->entity_id		= isset($entity_id) && $entity_id ? $entity_id : '';
			$this->doc_type			= isset($doc_type) && $doc_type ? $doc_type : '';
			$this->query_location	= isset($query_location) && $query_location ? $query_location : '';
			$this->allrows			= isset($allrows) && $allrows ? $allrows : '';
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
//			$this->entity_id	= $data['entity_id'];
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
			$documents = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'filter' => $this->filter,'cat_id' => $this->cat_id,'entity_id' => $this->entity_id,'doc_type'=>$this->doc_type));
			$this->total_records = $this->so->total_records;

			$this->uicols	= $this->so->uicols;
			$cols_extra		= $this->so->cols_extra;

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			foreach ($documents as &$document)
			{
				$location_data	= $this->solocation->read_single($document['location_code']);

				if(isset($location_data['street_name']) && $location_data['street_name'])
				{
					$document['address'] = "{$location_data['street_name']} {$location_data['street_number']}";
				}
				elseif($location_data['loc2_name'])
				{
					$document['address'] = $location_data['loc2_name'];
				}
				elseif($location_data['loc1_name'])
				{
					$document['address'] = $location_data['loc1_name'];
				}

				for ($j=0;$j<count($cols_extra);$j++)
				{
					$document[$cols_extra[$j]] = $location_data[$cols_extra[$j]];
				}
			}

			return $documents;
		}

		function get_files_at_location($data)
		{
			return $this->so->get_files_at_location($data);
		}


		function read_at_location($location_code='')
		{
			$use_svn = false;
			if(ereg('svn[s:][:/]/', $GLOBALS['phpgw_info']['server']['files_dir']))
			{
				//		$use_svn = true;
			}


			$document = $this->so->read_at_location(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'filter' => $this->filter,'cat_id' => $this->cat_id,'entity_id' => $this->entity_id,
				'location_code' => $location_code,'doc_type'=>$this->doc_type, 'allrows' => $this->allrows));
			$this->total_records = $this->so->total_records;

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			foreach ($document as & $entry)
			{
				$entry['user'] = $GLOBALS['phpgw']->accounts->id2name($entry['user_id']);
				$entry['document_date'] = $GLOBALS['phpgw']->common->show_date($entry['document_date'],$dateformat);
				$entry['entry_date'] 	= $GLOBALS['phpgw']->common->show_date($entry['entry_date'],$dateformat);
				if($use_svn)
				{
					$entry['journal'] 		= $this->get_file($entry['document_id'], true);
				}
			}

			return $document;
		}

		function read_single($document_id)
		{
			$document						= $this->so->read_single($document_id);
			$dateformat						= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$document['document_date']		= $GLOBALS['phpgw']->common->show_date($document['document_date'],$dateformat);

			if(ereg('svn[s:][:/]/', $GLOBALS['phpgw_info']['server']['files_dir']))
			{
				$document['journal']			= $this->get_file($document_id, true, $document);
			}

			if(isset($document['vendor_id']) && $document['vendor_id'])
			{
				$custom 				= createObject('property.custom_fields');
				$vendor['attributes']	= $custom->find('property','.vendor', 0, '', 'ASC', 'attrib_sort', true, true);
				$vendor					= $this->contacts->read_single(array('id' => $document['vendor_id']),$vendor);
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
					$category 								= $this->cats->return_single($value['new_value']);
					$record_history[$i]['value_new_value']	= $category[0]['name'];
					if($value['old_value'])
					{
						$category 								= $this->cats->return_single($value['old_value']);
						$record_history[$i]['value_old_value']	= $category[0]['name'];
					}
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

		function get_file($document_id, $get_journal = false, $values = array())
		{
			if(!$values)
			{
				$values = $this->read_single($document_id);
			}

			if($values['p_num'])
			{
				$file	= "{$this->bofiles->fakebase}/document/entity_{$values['p_entity_id']}_{$values['p_cat_id']}/{$values['p_num']}/{$values['doc_type']}/{$values['document_name']}";
			}
			else
			{
				$file	= "{$this->bofiles->fakebase}/document/{$values['location_code']}/{$values['doc_type']}/{$values['document_name']}";
			}

			if($this->bofiles->vfs->file_exists(array(
				'string' => $file,
				'relatives' => Array(RELATIVE_NONE)
			)))
			{

				if($get_journal)
				{
					return $this->bofiles->vfs->get_journal(array(
						'string' => $file,
						'relatives' => Array(RELATIVE_NONE)
					));
				}
				else
				{
					return $file;
				}
			}
			return false;
		}

		function save($values)
		{

			$document_date	= phpgwapi_datetime::date_array($values['document_date']);
			$values['document_date']	= mktime (2,0,0,$document_date['month'],$document_date['day'],$document_date['year']);

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
