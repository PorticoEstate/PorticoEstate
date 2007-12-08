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
	* @subpackage admin
 	* @version $Id: class.boactor.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_boactor
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
			'read'			=> True,
			'read_single'	=> True,
			'save'			=> True,
			'delete'		=> True,
			'check_perms'	=> True
		);

		function property_boactor($session=False)
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so 		= CreateObject('property.soactor');
			$this->bocommon 	= CreateObject('property.bocommon');

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
			$allrows	= phpgw::get_var('allrows', 'bool');
			$role		= phpgw::get_var('role');
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
				$this->cat_id = '';
			}
			if(isset($allrows))
			{
				$this->allrows = $allrows;
			}
			if(isset($member_id))
			{
				$this->member_id = $member_id;
			}
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','actor_' . $this->role,$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','actor_' . $this->role);

			//_debug_array($data);

			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->sort		= $data['sort'];
			$this->order	= $data['order'];
			$this->cat_id	= $data['cat_id'];
			$this->member_id= $data['member_id'];
			$this->allrows	= $data['allrows'];
		}

		function check_perms($has, $needed)
		{
			return (!!($has & $needed) == True);
		}

		function read()
		{
			$actor = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'filter' => $this->filter,'cat_id' => $this->cat_id,'allrows'=>$this->allrows,'member_id'=>$this->member_id));
			$this->total_records = $this->so->total_records;

			$this->uicols	= $this->so->uicols;

			for ($i=0; $i<count($actor); $i++)
			{
				if(isset($actor[$i]['entry_date']) && $actor[$i]['entry_date'])
				{
					$actor[$i]['entry_date']  = $GLOBALS['phpgw']->common->show_date($actor[$i]['entry_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				}
			}
			return $actor;
		}

		function read_single($data)
		{
			$contacts			= CreateObject('phpgwapi.contacts');

			$vendor = CreateObject('property.soactor');
			$vendor->role = 'vendor';


			$actor	= $this->so->read_single($data);
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

			$m=0;
			for ($i=0;$i<count($actor['attributes']);$i++)
			{
				if($actor['attributes'][$i]['datatype']=='D' && $actor['attributes'][$i]['value'])
				{
					$timestamp_date= mktime(0,0,0,date(m,strtotime($actor['attributes'][$i]['value'])),date(d,strtotime($actor['attributes'][$i]['value'])),date(y,strtotime($actor['attributes'][$i]['value'])));
					$actor['attributes'][$i]['value']	= $GLOBALS['phpgw']->common->show_date($timestamp_date,$dateformat);
				}
				if($actor['attributes'][$i]['datatype']=='AB')
				{
					if($actor['attributes'][$i]['value'])
					{
						$contact_data	= $contacts->read_single_entry($actor['attributes'][$i]['value'],array('n_given'=>'n_given','n_family'=>'n_family','email'=>'email'));
						$actor['attributes'][$i]['contact_name']	= $contact_data[0]['n_family'] . ', ' . $contact_data[0]['n_given'];
					}

					$insert_record_actor[]	= $actor['attributes'][$i]['name'];
					$lookup_link		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.addressbook', 'column'=> $actor['attributes'][$i]['name']));

					$lookup_functions[$m]['name'] = 'lookup_'. $actor['attributes'][$i]['name'] .'()';
					$lookup_functions[$m]['action'] = 'Window1=window.open('."'" . $lookup_link ."'" .',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';
					$m++;
				}
				if($actor['attributes'][$i]['datatype']=='VENDOR')
				{
					if($actor['attributes'][$i]['value'])
					{
						$vendor_data	= $vendor->read_single(array('actor_id'=>$actor['attributes'][$i]['value']));

						for ($n=0;$n<count($vendor_data['attributes']);$n++)
						{
							if($vendor_data['attributes'][$n]['name'] == 'org_name')
							{
								$actor['attributes'][$i]['vendor_name']= $vendor_data['attributes'][$n]['value'];
								$n =count($vendor_data['attributes']);
							}
						}
					}

					$insert_record_actor[]	= $actor['attributes'][$i]['name'];
					$lookup_link		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.vendor', 'column'=> $actor['attributes'][$i]['name']));

					$lookup_functions[$m]['name'] = 'lookup_'. $actor['attributes'][$i]['name'] .'()';
					$lookup_functions[$m]['action'] = 'Window1=window.open('."'" . $lookup_link ."'" .',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';
					$m++;
				}

				if($actor['attributes'][$i]['datatype']=='user')
				{
					if($actor['attributes'][$i]['value'])
					{
						$actor['attributes'][$i]['user_name']= $GLOBALS['phpgw']->accounts->id2name($actor['attributes'][$i]['value']);
					}

					$insert_record_actor[]	= $actor['attributes'][$i]['name'];
					$lookup_link		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.phpgw_user', 'column'=> $actor['attributes'][$i]['name']));

					$lookup_functions[$m]['name'] = 'lookup_'. $actor['attributes'][$i]['name'] .'()';
					$lookup_functions[$m]['action'] = 'Window1=window.open('."'" . $lookup_link ."'" .',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';
					$m++;
				}

				if($actor['attributes'][$i]['datatype']=='R' || $actor['attributes'][$i]['datatype']=='CH' || $actor['attributes'][$i]['datatype']=='LB')
				{
					$actor['attributes'][$i]['choice']	= $this->so->read_attrib_choice($actor['attributes'][$i]['attrib_id']);
					$input_type=$input_type_array[$actor['attributes'][$i]['datatype']];

					if($actor['attributes'][$i]['datatype']=='CH')
					{
						$actor['attributes'][$i]['value']=unserialize($actor['attributes'][$i]['value']);
						$actor['attributes'][$i]['choice'] = $this->bocommon->select_multi_list_2($actor['attributes'][$i]['value'],$actor['attributes'][$i]['choice'],$input_type);

					}
					else
					{
						for ($j=0;$j<count($actor['attributes'][$i]['choice']);$j++)
						{
							$actor['attributes'][$i]['choice'][$j]['input_type']=$input_type;
							if($actor['attributes'][$i]['choice'][$j]['id']==$actor['attributes'][$i]['value'])
							{
								$actor['attributes'][$i]['choice'][$j]['checked']='checked';
							}
						}
					}
				}

				$actor['attributes'][$i]['datatype_text'] = $this->bocommon->translate_datatype($actor['attributes'][$i]['datatype']);
				$actor['attributes'][$i]['counter']	= $i;
//				$actor['attributes'][$i]['type_id']	= $data['type_id'];
			}

			if(isset($lookup_functions) && is_array($lookup_functions))
			{
				$actor['lookup_functions'] = '';
				for ($j=0;$j<count($lookup_functions);$j++)
				{
					$actor['lookup_functions'] .= 'function ' . $lookup_functions[$j]['name'] ."\r\n";
					$actor['lookup_functions'] .= '{'."\r\n";
					$actor['lookup_functions'] .= $lookup_functions[$j]['action'] ."\r\n";
					$actor['lookup_functions'] .= '}'."\r\n";
				}
			}

			$GLOBALS['phpgw']->session->appsession('insert_record_actor','property',isset($insert_record_actor)?$insert_record_actor:'');

//_debug_array($actor);
			return $actor;
		}

		function save($actor,$values_attribute='')
		{
			if(is_array($values_attribute))
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
			}

			if ($actor['actor_id'])
			{
				if ($actor['actor_id'] != 0)
				{
					$actor_id = $actor['actor_id'];
					$receipt=$this->so->edit($actor,$values_attribute);
				}
			}
			else
			{
				$receipt = $this->so->add($actor,$values_attribute);
			}
			return $receipt;
		}

		function delete($actor_id='',$id='',$attrib='')
		{
			if ($attrib)
			{
				$this->so->delete_attrib($id);
			}
			else
			{
				$this->so->delete($actor_id);
			}
		}

		function read_attrib($type_id='')
		{
			$attrib = $this->so->read_attrib(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'allrows'=>$this->allrows));

			for ($i=0; $i<count($attrib); $i++)
			{
				$attrib[$i]['datatype'] = $this->bocommon->translate_datatype($attrib[$i]['datatype']);
			}

			$this->total_records = $this->so->total_records;

			return $attrib;
		}

		function read_single_attrib($id)
		{
			return $this->so->read_single_attrib($id);
		}

		function resort_attrib($data)
		{
			$this->so->resort_attrib($data);
		}

		function save_attrib($attrib)
		{
			if ($attrib['id'] != '')
			{
				$receipt = $this->so->edit_attrib($attrib);
			}
			else
			{
				$receipt = $this->so->add_attrib($attrib);
			}

			return $receipt;
		}

		function column_list($selected='',$allrows='')
		{
			if(!$selected)
			{
				$selected=$GLOBALS['phpgw_info']['user']['preferences']['property']["actor_columns_" . $this->role];
			}

			$columns = $this->so->read_attrib(array('allrows'=>$allrows,'column_list'=>True));

			$column_list=$this->bocommon->select_multi_list($selected,$columns);

			return $column_list;
		}
	}
?>
