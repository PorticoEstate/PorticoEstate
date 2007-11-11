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
	* @subpackage entity
 	* @version $Id: class.boentity.inc.php,v 1.30 2007/09/21 19:36:38 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_boentity
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $allrows;
		var $part_of_town_id;

		var $public_functions = array
		(
			'read'			=> True,
			'read_single'		=> True,
			'save'			=> True,
			'delete'		=> True,
			'check_perms'		=> True
		);

		var $soap_functions = array(
			'list' => array(
				'in'  => array('int','int','struct','string','int'),
				'out' => array('array')
			),
			'read' => array(
				'in'  => array('int','struct'),
				'out' => array('array')
			),
			'save' => array(
				'in'  => array('int','struct'),
				'out' => array()
			),
			'delete' => array(
				'in'  => array('int','struct'),
				'out' => array()
			)
		);

		function property_boentity($session=False)
		{
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->solocation 	= CreateObject('property.solocation');
			$this->bocommon 	= CreateObject('property.bocommon');
			$this->vfs 			= CreateObject('phpgwapi.vfs');
			$this->rootdir 		= $this->vfs->basedir;
			$this->fakebase 	= $this->vfs->fakebase;

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = True;
			}

			$start		= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query		= phpgw::get_var('query');
			$sort		= phpgw::get_var('sort');
			$order		= phpgw::get_var('order');
			$filter		= phpgw::get_var('filter', 'int');
			$cat_id		= phpgw::get_var('cat_id', 'int');
			$district_id	= phpgw::get_var('district_id', 'int');
			$entity_id	= phpgw::get_var('entity_id', 'int');
			$status		= phpgw::get_var('status');
			$start_date	= phpgw::get_var('start_date');
			$end_date	= phpgw::get_var('end_date');
			$allrows	= phpgw::get_var('allrows', 'bool');
		

			$this->soadmin_entity 	= CreateObject('property.soadmin_entity',$entity_id,$cat_id);
			$this->so 		= CreateObject('property.soentity',$entity_id,$cat_id);
			$this->category_dir = 'entity_' . $entity_id . '_' . $cat_id;

			if ($start)
			{
				$this->start=$start;
			}
			else
			{
				$this->start=0;
			}
			if(isset($_POST['query']) || isset($_GET['query']))
			{
				$this->query = $query;
			}
			if(isset($_POST['filter']) || isset($_GET['filter']))
			{
				$this->filter = $filter;
			}
			if(isset($_POST['sort']) || isset($_GET['sort']))
			{
				$this->sort = $sort;
			}
			if(isset($_POST['order']) || isset($_GET['order']))
			{
				$this->order = $order;
			}
			if(isset($_POST['cat_id']) || isset($_GET['cat_id']))
			{
				$this->cat_id = $cat_id;
			}
			if(isset($_POST['district_id']) || isset($_GET['district_id']))
			{
				$this->district_id = $district_id;
			}
			if($entity_id)
			{
				$this->entity_id = $entity_id;
			}
			if(isset($_POST['status']) || isset($_GET['status']))
			{
				$this->status = $status;
			}
			if(isset($_POST['start_date']) || isset($_GET['start_date']))
			{
				$this->start_date = $start_date;
			}
			if(isset($_POST['end_date']) || isset($_GET['end_date']))
			{
				$this->end_date = $end_date;
			}
			if($allrows)
			{
				$this->allrows = $allrows;
			}
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','entity',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','entity');
			//_debug_array($data);
			$this->start		= isset($data['start'])?$data['start']:'';
			$this->query		= isset($data['query'])?$data['query']:'';
			$this->filter		= isset($data['filter'])?$data['filter']:'';
			$this->sort			= isset($data['sort'])?$data['sort']:'';
			$this->order		= isset($data['order'])?$data['order']:'';
			$this->district_id	= isset($data['district_id'])?$data['district_id']:'';
			$this->status		= isset($data['status'])?$data['status']:'';
			$this->start_date	= isset($data['start_date'])?$data['start_date']:'';
			$this->end_date		= isset($data['end_date'])?$data['end_date']:'';
			//$this->allrows		= $data['allrows'];
		}

		function column_list($selected='',$entity_id='',$cat_id,$allrows='')
		{
			$soadmin_entity	= CreateObject('property.soadmin_entity');

			if(!$selected)
			{
				$selected=$GLOBALS['phpgw_info']['user']['preferences'][$this->currentapp]["entity_columns_" . $this->entity_id . '_' . $this->cat_id];
			}

			$columns = $soadmin_entity->read_attrib(array('entity_id'=>$entity_id,'cat_id'=>$cat_id,'allrows'=>$allrows,'filter_list' =>true));
			$column_list=$this->bocommon->select_multi_list($selected,$columns);
			return $column_list;
		}

		function select_category_list($format='',$selected='')
		{
			switch($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('cat_select'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('cat_filter'));
					break;
			}

			$categories= $this->soadmin_entity->read_category(array('allrows'=>True,'entity_id'=>$this->entity_id));

			return $this->bocommon->select_list($selected,$categories);
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

			$status_entries= $this->so->select_status_list($this->entity_id,$this->cat_id);

			return $this->bocommon->select_list($selected,$status_entries);
		}

		function read($data='')
		{
			if(isset($this->allrows))
			{
				$data['allrows'] = true;
			}

			$entity = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'filter' => $this->filter,'cat_id' => $this->cat_id,'district_id' => $this->district_id,
											'lookup'=>isset($data['lookup'])?$data['lookup']:'','allrows'=>isset($data['allrows'])?$data['allrows']:'','entity_id'=>$this->entity_id,'cat_id'=>$this->cat_id,'status'=>$this->status,
											'start_date'=>$this->bocommon->date_to_timestamp($data['start_date']),'end_date'=>$this->bocommon->date_to_timestamp($data['end_date'])));

			$this->total_records = $this->so->total_records;
			$this->uicols	= $this->so->uicols;
			$cols_extra		= $this->so->cols_extra;
			$cols_return_lookup		= $this->so->cols_return_lookup;
//_debug_array($entity);
//_debug_array($cols_extra);
//_debug_array($cols_return_lookup);

			if(isset($data['lookup']) && $data['lookup'])
			{
				for ($i=0;$i<count($entity);$i++)
				{
					$location_data=$this->solocation->read_single($entity[$i]['location_code']);
					for ($j=0;$j<count($cols_extra);$j++)
					{
						$entity[$i][$cols_extra[$j]] = $location_data[$cols_extra[$j]];
					}

					if($cols_return_lookup)
					{
						for ($k=0;$k<count($cols_return_lookup);$k++)
						{
							$entity[$i][$cols_return_lookup[$k]] = $location_data[$cols_return_lookup[$k]];
						}
					}
				}
			}

			return $entity;
		}

		function read_single($data)
		{
			$soadmin_entity	= CreateObject('property.soadmin_entity');
			$contacts		= CreateObject('phpgwapi.contacts');
			$vendor 		= CreateObject('property.soactor');
			$vendor->role	= 'vendor';

			$entity	= $this->so->read_single($data);

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
		//	$entity['date']  = $GLOBALS['phpgw']->common->show_date($entity['date'],$dateformat);

			if($entity['location_code'])
			{
				$entity['location_data']=$this->solocation->read_single($entity['location_code']);
				if($entity['tenant_id'])
				{
					$tenant_data=$this->bocommon->read_single_tenant($entity['tenant_id']);
					$entity['location_data']['tenant_id']	= $entity['tenant_id'];
					$entity['location_data']['contact_phone']= $entity['contact_phone'];
					$entity['location_data']['last_name']	= $tenant_data['last_name'];
					$entity['location_data']['first_name']	= $tenant_data['first_name'];
				}
			}

			if($entity['p_num'])
			{
				$category = $soadmin_entity->read_single_category($entity['p_entity_id'],$entity['p_cat_id']);
				$entity['p'][$entity['p_entity_id']]['p_num']=$entity['p_num'];
				$entity['p'][$entity['p_entity_id']]['p_entity_id']=$entity['p_entity_id'];
				$entity['p'][$entity['p_entity_id']]['p_cat_id']=$entity['p_cat_id'];
				$entity['p'][$entity['p_entity_id']]['p_cat_name'] = $category['name'];
			}

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
			$m=0;

			for ($i=0;$i<count($entity['attributes']);$i++)
			{
				if($entity['attributes'][$i]['datatype']=='D' && $entity['attributes'][$i]['value'])
				{
					$timestamp_date= mktime(0,0,0,date(m,strtotime($entity['attributes'][$i]['value'])),date(d,strtotime($entity['attributes'][$i]['value'])),date(y,strtotime($entity['attributes'][$i]['value'])));
					$entity['attributes'][$i]['value']	= $GLOBALS['phpgw']->common->show_date($timestamp_date,$dateformat);
				}

				if($entity['attributes'][$i]['datatype']=='AB')
				{
					if($entity['attributes'][$i]['value'])
					{
						$contact_data				= $contacts->read_single_entry($entity['attributes'][$i]['value'],array('n_given'=>'n_given','n_family'=>'n_family','email'=>'email'));
						$entity['attributes'][$i]['contact_name']	= $contact_data[0]['n_family'] . ', ' . $contact_data[0]['n_given'];
					}

					$insert_record_entity[]	= $entity['attributes'][$i]['name'];
					$lookup_link		= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uilookup.addressbook', 'column'=> $entity['attributes'][$i]['name']));
					$lookup_functions[$m]['name'] = 'lookup_'. $entity['attributes'][$i]['name'] .'()';
					$lookup_functions[$m]['action'] = 'Window1=window.open('."'" . $lookup_link ."'" .',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';
					$m++;
				}

				if($entity['attributes'][$i]['datatype']=='VENDOR')
				{
					if($entity['attributes'][$i]['value'])
					{
						$vendor_data	= $vendor->read_single(array('actor_id'=>$entity['attributes'][$i]['value']));
						for ($n=0;$n<count($vendor_data['attributes']);$n++)
						{
							if($vendor_data['attributes'][$n]['name'] == 'org_name')
							{
								$entity['attributes'][$i]['vendor_name']= $vendor_data['attributes'][$n]['value'];
								$n =count($vendor_data['attributes']);
							}
						}
					}

					$insert_record_entity[]	= $entity['attributes'][$i]['name'];
					$lookup_link		= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uilookup.vendor', 'column'=> $entity['attributes'][$i]['name']));
					$lookup_functions[$m]['name'] = 'lookup_'. $entity['attributes'][$i]['name'] .'()';
					$lookup_functions[$m]['action'] = 'Window1=window.open('."'" . $lookup_link ."'" .',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';
					$m++;
				}

				if($entity['attributes'][$i]['datatype']=='R' || $entity['attributes'][$i]['datatype']=='CH' || $entity['attributes'][$i]['datatype']=='LB')
				{
					$entity['attributes'][$i]['choice']	= $soadmin_entity->read_attrib_choice($data['entity_id'],$data['cat_id'],$entity['attributes'][$i]['attrib_id']);
					$input_type=$input_type_array[$entity['attributes'][$i]['datatype']];

					if($entity['attributes'][$i]['datatype']=='CH')
					{
						$entity['attributes'][$i]['value']=unserialize($entity['attributes'][$i]['value']);
						$entity['attributes'][$i]['choice'] = $this->bocommon->select_multi_list_2($entity['attributes'][$i]['value'],$entity['attributes'][$i]['choice'],$input_type);
					}
					else
					{
						for ($j=0;$j<count($entity['attributes'][$i]['choice']);$j++)
						{
							$entity['attributes'][$i]['choice'][$j]['input_type']=$input_type;
							if($entity['attributes'][$i]['choice'][$j]['id']==$entity['attributes'][$i]['value'])
							{
								$entity['attributes'][$i]['choice'][$j]['checked']='checked';
							}
						}
					}
				}

				$entity['attributes'][$i]['datatype_text'] = $this->bocommon->translate_datatype($entity['attributes'][$i]['datatype']);
				$entity['attributes'][$i]['counter']	= $i;
//				$entity['attributes'][$i]['type_id']	= $data['type_id'];
			}

			if(isset($lookup_functions) && is_array($lookup_functions))
			{
				for ($j=0;$j<count($lookup_functions);$j++)
				{
					$entity['lookup_functions'] .= 'function ' . $lookup_functions[$j]['name'] ."\r\n";
					$entity['lookup_functions'] .= '{'."\r\n";
					$entity['lookup_functions'] .= $lookup_functions[$j]['action'] ."\r\n";
					$entity['lookup_functions'] .= '}'."\r\n";
				}
			}

			$this->vfs->override_acl = 1;
			$entity['files'] = $this->vfs->ls (array(
			     'string' => $this->fakebase. '/' . $this->category_dir . '/' . $entity['location_data']['loc1'] .  '/' . $data['id'],
			     'relatives' => array(RELATIVE_NONE)));

			$this->vfs->override_acl = 0;

			if(!isset($entity['files'][0]['file_id']) || !$entity['files'][0]['file_id'])
			{
				unset($entity['files']);
			}


			$GLOBALS['phpgw']->session->appsession('insert_record_entity',$this->currentapp,isset($insert_record_entity)?$insert_record_entity:'');

//_debug_array($insert_record_entity);
			return $entity;
		}


		function create_home_dir($receipt='')
		{
			if(!$this->vfs->file_exists(array(
					'string' => $this->fakebase. SEP . $this->category_dir,
					'relatives' => Array(RELATIVE_NONE)
				)))
			{
				$this->vfs->override_acl = 1;

				if(!$this->vfs->mkdir (array(
				     'string' => $this->fakebase. SEP . $this->category_dir,
				     'relatives' => array(
				          RELATIVE_NONE
				     )
				)))
				{
					$receipt['error'][]=array('msg'=>lang('failed to create directory') . ' :'. $this->fakebase. SEP . $this->category_dir);
				}
				else
				{
					$receipt['message'][]=array('msg'=>lang('directory created') . ' :'. $this->fakebase. SEP . $this->category_dir);
				}
				$this->vfs->override_acl = 0;
			}

			return $receipt;
		}

		function create_document_dir($loc1='',$id='')
		{
			if(!$this->vfs->file_exists(array(
					'string' => $this->fakebase. SEP . $this->category_dir .  SEP . $loc1,
					'relatives' => Array(RELATIVE_NONE)
				)))
			{
				$this->vfs->override_acl = 1;
				if(!$this->vfs->mkdir (array(
				     'string' => $this->fakebase. SEP . $this->category_dir .  SEP . $loc1,
				     'relatives' => array(
				          RELATIVE_NONE
				     )
				)))
				{
					$receipt['error'][]=array('msg'=>lang('failed to create directory') . ' :'. $this->fakebase. SEP . $this->category_dir .  SEP . $loc1);
				}
				else
				{
					$receipt['message'][]=array('msg'=>lang('directory created') . ' :'. $this->fakebase. SEP . $this->category_dir .  SEP . $loc1);
				}
				$this->vfs->override_acl = 0;
			}


			if(!$this->vfs->file_exists(array(
					'string' => $this->fakebase. SEP . $this->category_dir .  SEP . $loc1 .  SEP . $id,
					'relatives' => Array(RELATIVE_NONE)
				)))
			{
				$this->vfs->override_acl = 1;
				if(!$this->vfs->mkdir (array(
				     'string' => $this->fakebase. SEP . $this->category_dir .  SEP . $loc1 .  SEP . $id,
				     'relatives' => array(
				          RELATIVE_NONE
				     )
				)))
				{
					$receipt['error'][]=array('msg'=>lang('failed to create directory') . ' :'. $this->fakebase. SEP  . $this->category_dir  .  SEP . $loc1 .  SEP . $id);
				}
				else
				{
					$receipt['message'][]=array('msg'=>lang('directory created') . ' :'. $this->fakebase. SEP . $this->category_dir .  SEP . $loc1 .  SEP . $id);
				}
				$this->vfs->override_acl = 0;
			}

//_debug_array($receipt);
			return $receipt;
		}

		function save($values,$values_attribute,$action='',$entity_id,$cat_id)
		{
			while (is_array($values['location']) && list(,$value) = each($values['location']))
			{
				if($value)
				{
					$location[] = $value;
				}
			}

			$values['location_code']=(isset($location)?implode("-", $location):'');

			$values['date']	= $this->bocommon->date_to_timestamp($values['date']);

			if (is_array($values_attribute))
			{
				for ($i=0;$i<count($values_attribute);$i++)
				{
					if(isset($values_attribute[$i]['value']) && $values_attribute[$i]['value'])
					{
						switch($values_attribute[$i]['datatype'])
						{
							case 'CH':
								$values_attribute[$i]['value'] = serialize($values_attribute[$i]['value']);
								break;
							case 'R':
								$values_attribute[$i]['value'] = $values_attribute[$i]['value'][0];
								break;
							case 'N':
								$values_attribute[$i]['value'] = str_replace(",",".",$values_attribute[$i]['value']);
								break;
							case 'D':
								$values_attribute[$i]['value'] = date($this->bocommon->dateformat,$this->bocommon->date_to_timestamp($values_attribute[$i]['value']));
								break;
							case 'T':
								$values_attribute[$i]['value'] = $GLOBALS['phpgw']->db->db_addslashes($values_attribute[$i]['value']);
								break;
						}
					}
				}
			}

			if ($action=='edit')
			{
				$receipt = $this->so->edit($values,$values_attribute,$entity_id,$cat_id);

				if(isset($values['delete_file']) && is_array($values['delete_file']))
				{
					for ($i=0;$i<count($values['delete_file']);$i++)
					{
						$file = $this->fakebase. SEP . $this->category_dir . SEP . $location[0] . SEP . $values['id'] . SEP . $values['delete_file'][$i];

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
								$receipt['error'][]=array('msg'=>lang('failed to delete file') . ' :'. $this->fakebase. SEP . $this->category_dir . SEP . $location[0]. SEP . $values['id'] . SEP .$values['delete_file'][$i]);
							}
							else
							{
								$receipt['message'][]=array('msg'=>lang('file deleted') . ' :'. $this->fakebase. SEP . $this->category_dir . SEP . $location[0]. SEP . $values['id'] . SEP . $values['delete_file'][$i]);
							}
							$this->vfs->override_acl = 0;
						}
					}
				}
			}
			else
			{
				$receipt = $this->so->add($values,$values_attribute,$entity_id,$cat_id);
			}

			$acl_location = '.entity.' . $entity_id . '.' . $cat_id;
			$custom_functions = $this->soadmin_entity->read_custom_function(array('acl_location' => $acl_location,'allrows'=>True));

			if (isSet($custom_functions) AND is_array($custom_functions))
			{
				foreach($custom_functions as $entry)
				{
					if (is_file(PHPGW_APP_INC . SEP . 'custom' . SEP . $entry['file_name']) && $entry['active'])
					include (PHPGW_APP_INC . SEP . 'custom' . SEP . $entry['file_name']);
				}
			}
			return $receipt;
		}


		function delete($id )
		{
			$this->so->delete($this->entity_id,$this->cat_id,$id);
		}

		function generate_id($data )
		{
			if($data['cat_id'])
			{
				return $this->so->generate_id($data);
			}
		}

		function read_attrib_history($data)
		{
		//	_debug_array($data);
			$historylog = CreateObject('property.historylog','entity_' . $data['entity_id'] .'_' . $data['cat_id']);
			$history_values = $historylog->return_array(array(),array('SO'),'history_timestamp','ASC',$data['id'],$data['attrib_id']);
			$this->total_records = count($history_values);
		//	_debug_array($history_values);
			return $history_values;
		}

		function delete_history_item($data)
		{
			$historylog = CreateObject('property.historylog','entity_' . $data['entity_id'] .'_' . $data['cat_id']);
			$historylog->delete_single_record($data['history_id']);
		}

		function read_attrib_help($data)
		{
			return $this->so->read_attrib_help($data);
		}
	}
?>
