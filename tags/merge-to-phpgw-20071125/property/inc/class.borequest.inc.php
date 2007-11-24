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
	* @subpackage project
 	* @version $Id: class.borequest.inc.php,v 1.15 2007/01/26 14:53:46 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_borequest
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;

		var $public_functions = array
		(
			'read'				=> True,
			'read_single'		=> True,
			'save'				=> True,
			'delete'			=> True,
			'check_perms'		=> True
		);

		function property_borequest($session=False)
		{
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so 			= CreateObject('property.sorequest');
			$this->bocommon 	= CreateObject('property.bocommon');
			$this->solocation 	= CreateObject('property.solocation');
			$this->historylog	= CreateObject('property.historylog','request');
			$this->vfs 			= CreateObject('phpgwapi.vfs');
			$this->rootdir 		= $this->vfs->basedir;
			$this->fakebase 	= $this->vfs->fakebase;

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = True;
			}

			$start	= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query	= phpgw::get_var('query');
			$sort	= phpgw::get_var('sort');
			$order	= phpgw::get_var('order');
			$filter	= phpgw::get_var('filter', 'int');
			$cat_id	= phpgw::get_var('cat_id', 'int');
			$status_id	= phpgw::get_var('status_id');
			$allrows	= phpgw::get_var('allrows', 'bool');

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
			if(isset($allrows))
			{
				$this->allrows = $allrows;
			}
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','request',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','request');

			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->sort		= $data['sort'];
			$this->order	= $data['order'];
			$this->cat_id	= $data['cat_id'];
			$this->status_id	= $data['status_id'];
			$this->allrows		= $data['allrows'];
		}


		function create_home_dir($receipt='')
		{
			if(!$this->vfs->file_exists(array(
					'string' => $this->fakebase. SEP . 'request',
					'relatives' => Array(RELATIVE_NONE)
				)))
			{
				$this->vfs->override_acl = 1;

				if(!$this->vfs->mkdir (array(
				     'string' => $this->fakebase. SEP . 'request',
				     'relatives' => array(
				          RELATIVE_NONE
				     )
				)))
				{
					$receipt['error'][]=array('msg'=>lang('failed to create directory') . ' :'. $this->fakebase. SEP . 'request');
				}
				else
				{
					$receipt['message'][]=array('msg'=>lang('directory created') . ' :'. $this->fakebase. SEP . 'request');
				}
				$this->vfs->override_acl = 0;
			}

			return $receipt;
		}

		function create_document_dir($location_code='',$id='')
		{
			if(!$this->vfs->file_exists(array(
					'string' => $this->fakebase. SEP . 'request' .  SEP . $location_code,
					'relatives' => Array(RELATIVE_NONE)
				)))
			{
				$this->vfs->override_acl = 1;
				if(!$this->vfs->mkdir (array(
				     'string' => $this->fakebase. SEP . 'request' .  SEP . $location_code,
				     'relatives' => array(
				          RELATIVE_NONE
				     )
				)))
				{
					$receipt['error'][]=array('msg'=>lang('failed to create directory') . ' :'. $this->fakebase. SEP . 'request' .  SEP . $location_code);
				}
				else
				{
					$receipt['message'][]=array('msg'=>lang('directory created') . ' :'. $this->fakebase. SEP . 'request' .  SEP . $location_code);
				}
				$this->vfs->override_acl = 0;
			}


			if(!$this->vfs->file_exists(array(
					'string' => $this->fakebase. SEP . 'request' .  SEP . $location_code .  SEP . $id,
					'relatives' => Array(RELATIVE_NONE)
				)))
			{
				$this->vfs->override_acl = 1;
				if(!$this->vfs->mkdir (array(
				     'string' => $this->fakebase. SEP . 'request' .  SEP . $location_code .  SEP . $id,
				     'relatives' => array(
				          RELATIVE_NONE
				     )
				)))
				{
					$receipt['error'][]=array('msg'=>lang('failed to create directory') . ' :'. $this->fakebase. SEP  . 'request'  .  SEP . $location_code .  SEP . $id);
				}
				else
				{
					$receipt['message'][]=array('msg'=>lang('directory created') . ' :'. $this->fakebase. SEP . 'request' .  SEP . $location_code .  SEP . $id);
				}
				$this->vfs->override_acl = 0;
			}

//_debug_array($receipt);
			return $receipt;
		}


		function select_degree_list($degree_value='',$degreedefault_type='')
		{
			if ($degree_value)
			{
				$selected=$degree_value;
			}
			else
			{
				$selected=$GLOBALS['phpgw_info']['user']['preferences'][$this->currentapp][$degreedefault_type];
			}

			$degree_comment[0]=' - '.lang('None');
			$degree_comment[1]=' - '.lang('Minor');
			$degree_comment[2]=' - '.lang('Medium');
			$degree_comment[3]=' - '.lang('Serious');
			for ($i=0; $i<=3; $i++)
			{
				$degree_list[$i][id] = $i;
				$degree_list[$i]['name'] = $i . $degree_comment[$i];
				if ($i==$selected)
				{
					$degree_list[$i]['selected']= 'selected';
				}
			}

			return $degree_list;
		}

		function select_probability_list($probability_value='')
		{
			$selected=$probability_value;

			$probability_comment[1]=' - '.lang('Small');
			$probability_comment[2]=' - '.lang('Medium');
			$probability_comment[3]=' - '.lang('Large');
			for ($i=1; $i<=3; $i++)
			{
				$probability_list[$i][id] = $i;
				$probability_list[$i]['name'] = $i . $probability_comment[$i];
				if ($i==$selected)
				{
					$probability_list[$i]['selected']= 'selected';
				}
			}

			return $probability_list;
		}

		function select_conditions($request_id='')
		{
			$condition_type_list = $this->so->select_condition_type_list();

			if($request_id)
			{
				$conditions = $this->so->select_conditions($request_id,$condition_type_list);
			}

			for ($i=0;$i<count($condition_type_list);$i++)
			{
				$conditions[$i]['degree'] 		= $this->select_degree_list($conditions[$i]['degree']);
				$conditions[$i]['probability'] 		= $this->select_probability_list($conditions[$i]['probability']);
				$conditions[$i]['consequence'] 		= $this->select_consequence_list($conditions[$i]['consequence']);
				$conditions[$i]['condition_type']	= $condition_type_list[$i]['id'];
				$conditions[$i]['condition_type_name']	= $condition_type_list[$i]['name'];
			}

			return $conditions;
		}

		function select_consequence_list($consequence_value='',$consequencedefault_type='')
		{
			if ($consequence_value)
			{
				$selected=$consequence_value;
			}
			else
			{
				$selected=$GLOBALS['phpgw_info']['user']['preferences'][$this->currentapp][$consequencedefault_type];
			}

			$consequence_comment[0]=' - '.lang('None Consequences');
			$consequence_comment[1]=' - '.lang('Minor Consequences');
			$consequence_comment[2]=' - '.lang('Medium Consequences');
			$consequence_comment[3]=' - '.lang('Serious Consequences');
			for ($i=0; $i<=3; $i++)
			{
				$consequence_list[$i][id] = $i;
				$consequence_list[$i]['name'] = $i . $consequence_comment[$i];
				if ($i==$selected)
				{
					$consequence_list[$i]['selected']= 'selected';
				}
			}

			return $consequence_list;
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


		function read_priority_key()
		{
			return	$this->so->read_priority_key();
		}

		function update_priority_key($values)
		{
			return	$this->so->update_priority_key($values);
		}

		function read($data)
		{
			$request = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'filter' => $this->filter,'cat_id' => $this->cat_id,'status_id' => $this->status_id,
											'project_id' => $data['project_id'],'allrows'=>$data['allrows'],'list_descr' => $data['list_descr']));
			$this->total_records = $this->so->total_records;

			$this->uicols	= $this->so->uicols;
			$cols_extra		= $this->so->cols_extra;

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			for ($i=0; $i<count($request); $i++)
			{
				$request[$i]['coordinator'] = $GLOBALS['phpgw']->accounts->id2name($request[$i]['coordinator']);
				$request[$i]['entry_date'] = $GLOBALS['phpgw']->common->show_date($request[$i]['entry_date'],$dateformat);
				$location_data=$this->solocation->read_single($request[$i]['location_code']);

				for ($j=0;$j<count($cols_extra);$j++)
				{
					$request[$i][$cols_extra[$j]] = $location_data[$cols_extra[$j]];
				}
			}

			return $request;
		}

		function read_single($request_id)
		{
			$request						= $this->so->read_single($request_id);
			$dateformat						= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$request['start_date']			= $GLOBALS['phpgw']->common->show_date($request['start_date'],$dateformat);
			$request['end_date']			= $GLOBALS['phpgw']->common->show_date($request['end_date'],$dateformat);

			if($request['location_code'])
			{
				$request['location_data'] =$this->solocation->read_single($request['location_code']);
			}

			if($request['tenant_id']>0)
			{
				$tenant_data=$this->bocommon->read_single_tenant($request['tenant_id']);
				$request['location_data']['tenant_id']= $request['tenant_id'];
				$request['location_data']['contact_phone']= $tenant_data['contact_phone'];
				$request['location_data']['last_name']	= $tenant_data['last_name'];
				$request['location_data']['first_name']	= $tenant_data['first_name'];
			}
			else
			{
				unset($request['location_data']['tenant_id']);
				unset($request['location_data']['contact_phone']);
				unset($request['location_data']['last_name']);
				unset($request['location_data']['first_name']);
			}

			if($request['p_num'])
			{
				$soadmin_entity	= CreateObject('property.soadmin_entity');
				$category = $soadmin_entity->read_single_category($request['p_entity_id'],$request['p_cat_id']);

				$request['p'][$request['p_entity_id']]['p_num']=$request['p_num'];
				$request['p'][$request['p_entity_id']]['p_entity_id']=$request['p_entity_id'];
				$request['p'][$request['p_entity_id']]['p_cat_id']=$request['p_cat_id'];
				$request['p'][$request['p_entity_id']]['p_cat_name'] = $category['name'];
			}

			$this->vfs->override_acl = 1;

			$request['files'] = $this->vfs->ls (array(
			     'string' => $this->fakebase. '/' . 'request' . '/' . $request['location_code'] .  '/' . $request_id,
			     'relatives' => array(RELATIVE_NONE)));

			$this->vfs->override_acl = 0;

			if(!$request['files'][0]['file_id'])
			{
				unset($request['files']);
			}

			return $request;
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
					$record_history[$i]['value_new_value']	= $this->so->read_single_request_category($value['new_value']);
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


		function next_id()
		{
			return $this->so->next_id();
		}

		function save($request,$action='')
		{
			while (is_array($request['location']) && list(,$value) = each($request['location']))
			{
				if($value)
				{
					$location[] = $value;
				}
			}

			$request['location_code']=implode("-", $location);
			$request['start_date']	= $this->bocommon->date_to_timestamp($request['start_date']);
			$request['end_date']	= $this->bocommon->date_to_timestamp($request['end_date']);

			if ($action=='edit')
			{
				$receipt = $this->so->edit($request);

				if($request['delete_file'])
				{
					for ($i=0;$i<count($request['delete_file']);$i++)
					{
						$file = $this->fakebase. SEP . 'request' . SEP . $request['location_code'] . SEP . $request['request_id'] . SEP . $request['delete_file'][$i];

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
								$receipt['error'][]=array('msg'=>lang('failed to delete file') . ' :'. $this->fakebase. SEP . 'request' . SEP . $request['location_code']. SEP . $request['request_id'] . SEP .$request['delete_file'][$i]);
							}
							else
							{
								$receipt['message'][]=array('msg'=>lang('file deleted') . ' :'. $this->fakebase. SEP . 'request' . SEP . $request['location_code']. SEP . $request['request_id'] . SEP . $request['delete_file'][$i]);
							}
							$this->vfs->override_acl = 0;
						}
					}
				}

			}
			else
			{
				$receipt = $this->so->add($request);
			}
			return $receipt;
		}

		function delete($request_id)
		{
			$this->so->delete($request_id);
		}
	}
?>
