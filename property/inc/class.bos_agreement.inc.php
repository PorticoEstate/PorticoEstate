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
	* @subpackage agreement
 	* @version $Id: class.bos_agreement.inc.php,v 1.16 2007/08/12 21:25:10 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_bos_agreement
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $role;
		var $member_id;

		var $public_functions = array
		(
			'read'				=> True,
			'read_single'		=> True,
			'save'				=> True,
			'delete'			=> True,
			'check_perms'		=> True
		);

		function property_bos_agreement($session=False)
		{
			$this->so = CreateObject('property.sos_agreement');
			$this->bocommon = CreateObject('property.bocommon');
			$this->custom 		= createObject('phpgwapi.custom_fields');
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
			$vendor_id	= phpgw::get_var('vendor_id', 'int');
			$allrows	= phpgw::get_var('allrows', 'bool');
			$role	= phpgw::get_var('role');
			$member_id	= phpgw::get_var('member_id', 'int');


			$this->role	= $role;
			$this->so->role	= $role;

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
			if(!empty($filter))
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
			if(isset($cat_id) && !empty($cat_id))
			{
				$this->cat_id = $cat_id;
			}
			else
			{
				unset($this->cat_id);
			}
			if(isset($allrows))
			{
				$this->allrows = $allrows;
			}
			if(isset($member_id))
			{
				$this->member_id = $member_id;
			}
			if(isset($vendor_id))
			{
				$this->vendor_id = $vendor_id;
			}
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','s_agreement',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','s_agreement');

			//_debug_array($data);

			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->sort		= $data['sort'];
			$this->order	= $data['order'];
			$this->cat_id	= $data['cat_id'];
			$this->vendor_id= $data['vendor_id'];
			$this->member_id= $data['member_id'];
			$this->allrows	= $data['allrows'];
		}

		function check_perms($has, $needed)
		{
			return (!!($has & $needed) == True);
		}

		function select_vendor_list($format='',$selected='')
		{
			switch($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('select_vendor'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('filter_vendor'));
					break;
			}

			$input_list= $this->so->select_vendor_list();
			$vendor_list= $this->bocommon->select_list($selected,$input_list);

			return $vendor_list;
		}

		function read()
		{
			$s_agreement = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'filter' => $this->filter,'cat_id' => $this->cat_id,'allrows'=>$this->allrows,'member_id'=>$this->member_id,
											'vendor_id'=>$this->vendor_id));
			$this->total_records = $this->so->total_records;

			$this->uicols	= $this->so->uicols;

			for ($i=0; $i<count($s_agreement); $i++)
			{
				if($s_agreement[$i]['start_date'])
				{
					$s_agreement[$i]['start_date']  = $GLOBALS['phpgw']->common->show_date($s_agreement[$i]['start_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				}
				if($s_agreement[$i]['end_date'])
				{
					$s_agreement[$i]['end_date']  = $GLOBALS['phpgw']->common->show_date($s_agreement[$i]['end_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				}
			}
			return $s_agreement;
		}

		function read_details($id)
		{
			$list = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'filter' => $this->filter,'cat_id' => $this->cat_id,'allrows'=>$this->allrows,'member_id'=>$this->member_id,
											's_agreement_id'=>$id,'detail'=>True));
			$this->total_records = $this->so->total_records;

			$this->uicols	= $this->so->uicols;

			for ($i=0; $i<count($list); $i++)
			{
				$list[$i]['index_date']  = $GLOBALS['phpgw']->common->show_date($list[$i]['index_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			}

			return $list;
		}

		function read_prizing($data)
		{
			$list = $this->so->read_prizing($data);
			$this->total_records = $this->so->total_records;

			$this->uicols	= $this->so->uicols;

			for ($i=0; $i<count($list); $i++)
			{
				$list[$i]['index_date']  = $GLOBALS['phpgw']->common->show_date($list[$i]['index_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			}

			return $list;
		}

		function read_event($data)
		{
			$boalarm		= CreateObject('property.boalarm');
			$event	= $this->so->read_single($data);
			$event['alarm_date']=$event['termination_date'];
			$event['alarm']	= $boalarm->read_alarms($type='s_agreement',$data['s_agreement_id']);
			return $event;
		}

		function read_single($data)
		{
			$values['attributes'] = $this->custom->get_attribs('property', '.s_agreement', 0, '', 'ASC', 'attrib_sort', true, true);
			
			if(isset($data['s_agreement_id']) && $data['s_agreement_id'])
			{
				$values = $this->so->read_single($data['s_agreement_id'], $values);
			}

			$values = $this->custom->prepare_attributes($values, 'property', '.s_agreement');

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$values['start_date']		= $GLOBALS['phpgw']->common->show_date($values['start_date'],$dateformat);
			$values['end_date']		= $GLOBALS['phpgw']->common->show_date($values['end_date'],$dateformat);
			if($values['termination_date'])
			{
				$values['termination_date']= $GLOBALS['phpgw']->common->show_date($values['termination_date'],$dateformat);
			}

			$this->vfs->override_acl = 1;

			$values['files'] = $this->vfs->ls (array(
			     'string' => $this->fakebase. '/' . 'service_agreement' .  '/' . $data['s_agreement_id'],
			     'relatives' => array(RELATIVE_NONE)));

			$this->vfs->override_acl = 0;

			if(!$values['files'][0]['file_id'])
			{
				unset($values['files']);
			}

			return $values;

		}

		function read_single_item($data)
		{
			$item	= $this->so->read_single_item($data);
//_debug_array($item);
			$item	= $this->convert_attribute($item,True);

			if($item['location_code'])
			{
				$solocation	= CreateObject('property.solocation');
				$item['location_data'] =$solocation->read_single($item['location_code']);
			}

			if($item['p_num'])
			{
				$soadmin_entity	= CreateObject('property.soadmin_entity');
				$category = $soadmin_entity->read_single_category($item['p_entity_id'],$item['p_cat_id']);

				$item['p'][$item['p_entity_id']]['p_num']=$item['p_num'];
				$item['p'][$item['p_entity_id']]['p_entity_id']=$item['p_entity_id'];
				$item['p'][$item['p_entity_id']]['p_cat_id']=$item['p_cat_id'];
				$item['p'][$item['p_entity_id']]['p_cat_name'] = $category['name'];
			}

			return $item;
		}

		function convert_attribute($list,$detail='')
		{
			if($detail)
			{
				$this->so->role	= 'detail';
			}
			$contacts			= CreateObject('phpgwapi.contacts');

			$vendor = CreateObject('property.soactor');
			$vendor->role = 'vendor';

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$input_type_array = array(
				'R' => 'radio',
				'CH' => 'checkbox',
				'LB' => 'listbox'
			);

			$sep = '/';
			$dlarr[strpos($dateformat,'Y')] = 'Y';
			$dlarr[strpos($dateformat,'m')] = 'm';
			$dlarr[strpos($dateformat,'d')] = 'd';
			ksort($dlarr);

			$dateformat= (implode($sep,$dlarr));

//html_print_r($list);
			$m=0;
			for ($i=0;$i<count($list['attributes']);$i++)
			{
				if($list['attributes'][$i]['datatype']=='D' && $list['attributes'][$i]['value'])
				{
					$timestamp_date= mktime(0,0,0,date(m,strtotime($list['attributes'][$i]['value'])),date(d,strtotime($list['attributes'][$i]['value'])),date(y,strtotime($list['attributes'][$i]['value'])));
					$list['attributes'][$i]['value']	= $GLOBALS['phpgw']->common->show_date($timestamp_date,$dateformat);
				}
				if($list['attributes'][$i]['datatype']=='AB')
				{
					if($list['attributes'][$i]['value'])
					{
						$contact_data	= $contacts->read_single_entry($list['attributes'][$i]['value'],array('n_given'=>'n_given','n_family'=>'n_family','email'=>'email'));
						$list['attributes'][$i]['contact_name']	= $contact_data[0]['n_family'] . ', ' . $contact_data[0]['n_given'];
					}

					$insert_record_list[]	= $list['attributes'][$i]['name'];
					$lookup_link		= $GLOBALS['phpgw']->link('/index.php','menuaction='.'property.uilookup.addressbook&column=' . $list['attributes'][$i]['name']);

					$lookup_functions[$m]['name'] = 'lookup_'. $list['attributes'][$i]['name'] .'()';
					$lookup_functions[$m]['action'] = 'Window1=window.open('."'" . $lookup_link ."'" .',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';
					$m++;
				}
				if($list['attributes'][$i]['datatype']=='VENDOR')
				{
					if($list['attributes'][$i]['value'])
					{
						$vendor_data	= $vendor->read_single(array('actor_id'=>$list['attributes'][$i]['value']));

						for ($n=0;$n<count($vendor_data['attributes']);$n++)
						{
							if($vendor_data['attributes'][$n]['name'] == 'org_name')
							{
								$list['attributes'][$i]['vendor_name']= $vendor_data['attributes'][$n]['value'];
								$n =count($vendor_data['attributes']);
							}
						}
					}

					$insert_record_list[]	= $list['attributes'][$i]['name'];
					$lookup_link		= $GLOBALS['phpgw']->link('/index.php','menuaction='.'property.uilookup.vendor&column=' . $list['attributes'][$i]['name']);

					$lookup_functions[$m]['name'] = 'lookup_'. $list['attributes'][$i]['name'] .'()';
					$lookup_functions[$m]['action'] = 'Window1=window.open('."'" . $lookup_link ."'" .',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';
					$m++;
				}
				if($list['attributes'][$i]['datatype']=='R' || $list['attributes'][$i]['datatype']=='CH' || $list['attributes'][$i]['datatype']=='LB')
				{
					$list['attributes'][$i]['choice']	= $this->so->read_attrib_choice($list['attributes'][$i]['attrib_id']);
					$input_type=$input_type_array[$list['attributes'][$i]['datatype']];

					if($list['attributes'][$i]['datatype']=='CH')
					{
						$list['attributes'][$i]['value']=unserialize($list['attributes'][$i]['value']);
						$list['attributes'][$i]['choice'] = $this->bocommon->select_multi_list_2($list['attributes'][$i]['value'],$list['attributes'][$i]['choice'],$input_type);

					}
					else
					{
						for ($j=0;$j<count($list['attributes'][$i]['choice']);$j++)
						{
							$list['attributes'][$i]['choice'][$j]['input_type']=$input_type;
							if($list['attributes'][$i]['choice'][$j]['id']==$list['attributes'][$i]['value'])
							{
								$list['attributes'][$i]['choice'][$j]['checked']='checked';
							}
						}
					}
				}

				$list['attributes'][$i]['datatype_text'] = $this->bocommon->translate_datatype($list['attributes'][$i]['datatype']);
				$list['attributes'][$i]['counter']	= $i;
				$list['attributes'][$i]['type_id']	= $data['type_id'];
			}

			for ($j=0;$j<count($lookup_functions);$j++)
			{
				$list['lookup_functions'] .= 'function ' . $lookup_functions[$j]['name'] ."\r\n";
				$list['lookup_functions'] .= '{'."\r\n";
				$list['lookup_functions'] .= $lookup_functions[$j]['action'] ."\r\n";
				$list['lookup_functions'] .= '}'."\r\n";
			}

			$GLOBALS['phpgw']->session->appsession('insert_record_s_agreement' . !!$detail,'property',$insert_record_list);

//html_print_r($list);
			return $list;

		}

		function convert_attribute_save($values_attribute='')
		{

			for ($i=0;$i<count($values_attribute);$i++)
			{
				if($values_attribute[$i]['datatype']=='CH' && $values_attribute[$i]['value'])
				{
					$values_attribute[$i]['value'] = serialize($values_attribute[$i]['value']);
				}
				if($values_attribute[$i]['datatype']=='R' && $values_attribute[$i]['value'])
				{
					$values_attribute[$i]['value'] = $values_attribute[$i]['value'][0];
				}

				if($values_attribute[$i]['datatype']=='N' && $values_attribute[$i]['value'])
				{
					$values_attribute[$i]['value'] = str_replace(",",".",$values_attribute[$i]['value']);
				}

				if($values_attribute[$i]['datatype']=='D' && $values_attribute[$i]['value'])
				{
					$values_attribute[$i]['value'] = date($this->bocommon->dateformat,$this->bocommon->date_to_timestamp($values_attribute[$i]['value']));
				}
			}

			return $values_attribute;
		}

		function save($values,$values_attribute='',$action='')
		{

			$values['start_date']	= $this->bocommon->date_to_timestamp($values['start_date']);
			$values['end_date']	= $this->bocommon->date_to_timestamp($values['end_date']);
			$values['termination_date']	= $this->bocommon->date_to_timestamp($values['termination_date']);

			if (is_array($values_attribute))
			{
				$values_attribute = $this->convert_attribute_save($values_attribute);
			}

			if ($action=='edit')
//			if ($values['s_agreement_id'])
			{
				if ($values['s_agreement_id'] != 0)
				{
					$receipt=$this->so->edit($values,$values_attribute);

					if($values['delete_file'])
					{
						for ($i=0;$i<count($values['delete_file']);$i++)
						{
							$file = $this->fakebase. SEP . 'service_agreement' . SEP . $values['s_agreement_id'] . SEP . $values['delete_file'][$i];

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
									$receipt['error'][]=array('msg'=>lang('failed to delete file') . ' :'. $this->fakebase. SEP . 'service_agreement'. SEP . $values['s_agreement_id'] . SEP .$values['delete_file'][$i]);
								}
								else
								{
									$receipt['message'][]=array('msg'=>lang('file deleted') . ' :'. $this->fakebase. SEP . 'service_agreement'. SEP . $values['id'] . SEP . $values['delete_file'][$i]);
								}
								$this->vfs->override_acl = 0;
							}
						}
					}
				}
			}
			else
			{
				$receipt = $this->so->add($values,$values_attribute);
			}
			return $receipt;
		}

		function save_item($values,$values_attribute='')
		{

			while (is_array($values['location']) && list(,$value) = each($values['location']))
			{
				if($value)
				{
					$location[] = $value;
				}
			}

			$values['location_code']=@implode("-", $location);

			if (is_array($values_attribute))
			{
				$values_attribute = $this->convert_attribute_save($values_attribute);
			}

			if ($values['id'])
			{
				if ($values['id'] != 0)
				{
					$receipt=$this->so->edit_item($values,$values_attribute);
				}
			}
			else
			{
				$receipt = $this->so->add_item($values,$values_attribute);
			}
			return $receipt;
		}


		function import($import_data,$id)
		{
			$this->so->role = 'detail';
			$custom_attributes = $this->so->read_attrib(array('allrows'=>true));

			foreach($custom_attributes as $attrib)
			{
				if(array_key_exists($attrib['column_name'],$import_data)
					&& ($attrib['datatype'] == 'LB' || $attrib['datatype'] == 'R' || $attrib['datatype'] == 'CH')
				)
				{
					$import_data[$attrib['column_name']] = $this->so->attrib_choise2id($attrib['id'],$import_data[$attrib['column_name']]);
				}
			}

			$values = array(
			'extra' 			=> $import_data,
			's_agreement_id' 	=> $id,
			'location_code'		=> $import_data['location_code'],
			'location_name'		=> $import_data['address'],
			'cost'				=> $import_data['cost']			
			);
			unset($values['extra']['location_code']);
			unset($values['extra']['address']);
			unset($values['extra']['cost']);
			return $this->so->add_item($values);
		}

		function update($values)
		{
			$values['date']	= $this->bocommon->date_to_timestamp($values['date']);

			return $this->so->update($values);
		}

		function delete_last_index($s_agreement_id,$id)
		{
			$this->so->delete_last_index($s_agreement_id,$id);
		}


		function delete_item($s_agreement_id,$item_id)
		{
			$this->so->delete_item($s_agreement_id,$item_id);
		}

		function delete($s_agreement_id)
		{
			$this->so->delete($s_agreement_id);
		}


		function column_list($selected='',$allrows='')
		{
			if(!$selected)
			{
				$selected=$GLOBALS['phpgw_info']['user']['preferences']['property']["s_agreement_columns"];
			}

			$columns = $this->so->read_attrib(array('allrows'=>$allrows,'column_list'=>True));

			$column_list=$this->bocommon->select_multi_list($selected,$columns);

			return $column_list;
		}

		function request_next_id()
		{
				return $this->so->request_next_id();
		}

		function create_home_dir($receipt='')
		{
			if(!$this->vfs->file_exists(array(
					'string' => $this->fakebase. SEP . 'service_agreement',
					'relatives' => Array(RELATIVE_NONE)
				)))
			{
				$this->vfs->override_acl = 1;

				if(!$this->vfs->mkdir (array(
				     'string' => $this->fakebase. SEP . 'service_agreement',
				     'relatives' => array(
				          RELATIVE_NONE
				     )
				)))
				{
					$receipt['error'][]=array('msg'=>lang('failed to create directory') . ' :'. $this->fakebase. SEP . 'service_agreement');
				}
				else
				{
					$receipt['message'][]=array('msg'=>lang('directory created') . ' :'. $this->fakebase. SEP . 'service_agreement');
				}
				$this->vfs->override_acl = 0;
			}

			return $receipt;
		}

		function create_document_dir($id='')
		{

			if(!$this->vfs->file_exists(array(
					'string' => $this->fakebase. SEP . 'service_agreement' .  SEP . $id,
					'relatives' => Array(RELATIVE_NONE)
				)))
			{
				$this->vfs->override_acl = 1;
				if(!$this->vfs->mkdir (array(
				     'string' => $this->fakebase. SEP . 'service_agreement' .  SEP . $id,
				     'relatives' => array(
				          RELATIVE_NONE
				     )
				)))
				{
					$receipt['error'][]=array('msg'=>lang('failed to create directory') . ' :'. $this->fakebase. SEP  . 'service_agreement' .  SEP . $id);
				}
				else
				{
					$receipt['message'][]=array('msg'=>lang('directory created') . ' :'. $this->fakebase. SEP . 'service_agreement' .  SEP . $id);
				}
				$this->vfs->override_acl = 0;
			}

//_debug_array($receipt);
			return $receipt;
		}

		function read_attrib_history($data)
		{
		//	_debug_array($data);
			$historylog = CreateObject('property.historylog','s_agreement');
			$history_values = $historylog->return_array(array(),array('SO'),'history_timestamp','ASC',$data['id'],$data['attrib_id'],$data['item_id']);
			$this->total_records = count($history_values);
		//	_debug_array($history_values);
			return $history_values;
		}

		function delete_history_item($data)
		{
			$historylog = CreateObject('property.historylog','s_agreement');
			$historylog->delete_single_record($data['history_id']);
		}


	}
?>
