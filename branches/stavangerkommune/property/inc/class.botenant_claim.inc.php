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
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */



	class property_botenant_claim
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;

		function property_botenant_claim($session=false)
		{
			$this->bocommon = CreateObject('property.bocommon');
			$this->so = CreateObject('property.sotenant_claim');

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start	= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query	= phpgw::get_var('query');
			$sort	= phpgw::get_var('sort');
			$order	= phpgw::get_var('order');
			$user_id	= phpgw::get_var('user_id', 'int');
			$status	= phpgw::get_var('status');
			$cat_id	= phpgw::get_var('cat_id', 'int');
			$allrows= phpgw::get_var('allrows', 'bool');
			$project_id	= phpgw::get_var('project_id', 'int');
			$district_id= phpgw::get_var('district_id', 'int');

			$this->district_id		= isset($_REQUEST['district_id']) 	? $district_id		: $this->district_id;

			$this->project_id = $project_id;

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
			if(!empty($user_id))
			{
				$this->user_id = $user_id;
			}
			if(isset($status))
			{
				$this->status = $status;
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
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','tenant_claim',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','tenant_claim');

			$this->start		= $data['start'];
			$this->query		= $data['query'];
			$this->user_id		= isset($data['user_id'])?$data['user_id']:'';
			$this->status		= $data['status'];
			$this->sort			= $data['sort'];
			$this->order		= $data['order'];
			$this->cat_id		= $data['cat_id'];
			$this->district_id	= isset($data['district_id'])?$data['district_id']:'';
		}

		function check_perms($has, $needed)
		{
			return (!!($has & $needed) == true);
		}

		function get_status_list($data=0)
		{
			if(is_array($data))
			{
				$format = (isset($data['format'])?$data['format']:'');
				$selected = (isset($data['selected'])?$data['selected']:$data['default']);
			}
			else
			{
				return array();
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('status_' . $format));

			$status[0]['id']	='ready';
			$status[0]['name']	=lang('ready for processing claim');
			$status[1]['id']	='closed';
			$status[1]['name']	=lang('Closed');
			if($format == "filter")
			{
				$status[2]['id']	='all';
				$status[2]['name']	=lang('All');
			}
			else
			{
				$status[2]['id']	='open';
				$status[2]['name']	=lang('Open');
			}

			return $this->bocommon->select_list($selected,$status);
		}

		function read_category_name($cat_id='')
		{
			return $this->so->read_category_name($cat_id);
		}

		function read($data = array())
		{
			$project_id	= isset($data['project_id']) && $data['project_id'] ? $data['project_id'] : phpgw::get_var('project_id');

			$claims = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'user_id' => $this->user_id,'status' => $this->status,'cat_id' => $this->cat_id,
				'allrows'=>$this->allrows,'project_id' => $project_id, 'district_id' => $this->district_id,));
			$this->total_records = $this->so->total_records;

			foreach ($claims as &$entry)
			{
				$entry['entry_date']  = $GLOBALS['phpgw']->common->show_date($entry['entry_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				$entry['status'] = lang($entry['status']);
				$entry['user'] = $GLOBALS['phpgw']->accounts->get($entry['user_id'])->__toString();
				$location_info = execMethod('property.solocation.read_single',$entry['location_code']);
				$entry['loc1_name'] = $location_info['loc1_name'];
				$entry['loc_category'] = $location_info['category_name'];
			}
			return $claims;
		}

		function check_claim_project($project_id)
		{
			$claim = $this->so->check_claim_project($project_id);
			$this->total_records = $this->so->total_records;
			return $claim;
		}

		function check_claim_workorder($workorder_id)
		{
			$claim = $this->so->check_claim_workorder($workorder_id);
			$this->total_records = $this->so->total_records;
			return $claim;
		}

		function read_single($claim_id)
		{
			return $this->so->read_single($claim_id);
		}

		function save($claim)
		{

			if ($claim['claim_id'])
			{
				if ($claim['claim_id'] != 0)
				{
					$claim_id = $claim['claim_id'];
					$receipt=$this->so->edit($claim);
					$action = lang('altered');
				}
			}
			else
			{
				$receipt = $this->so->add($claim);
				$action = lang('added');
			}


			$this->config = CreateObject('phpgwapi.config','property');
			$this->config->read();
			$claim_notify_mails = $this->config->config_data['tenant_claim_notify_mails'];
			if ($claim_notify_mails)
			{
				// notify via email
				$current_user_id=$GLOBALS['phpgw_info']['user']['account_id'];
				$current_prefs_user = $this->bocommon->create_preferences('property',$current_user_id);
				$from=$current_prefs_user['email'];
				$subject = lang("Tenant claim %1",$receipt['claim_id']) .' ' . $action;
				$body    = lang('Reminder');

				if(!is_object($GLOBALS['phpgw']->send))
				{
					$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
				}
				$subject = $GLOBALS['phpgw']->send->encode_subject($subject);
				$notify_mails = explode(',',$claim_notify_mails);
				foreach($notify_mails as $to)
				{
					$GLOBALS['phpgw']->send->msg('email',$to,$subject,$body,'','','',$from,$from);
				}
			}

			return $receipt;
		}

		function delete($params)
		{
			if (is_array($params))
			{
				$this->so->delete($params[0]);
			}
			else
			{
				$this->so->delete($params);
			}
		}

		function read_record_history($id)
		{
			$historylog	= CreateObject('property.historylog','tenant_claim');
			$history_array = $historylog->return_array(array('O'),array(),'','',$id);

			$status_text = array();

			$status_text['ready']	= lang('ready for processing claim');
			$status_text['open']	= lang('open');
			$status_text['closed']	= lang('closed');

			$i=0;
			foreach ($history_array as $value) 
			{

				$record_history[$i]['value_date']	= $GLOBALS['phpgw']->common->show_date($value['datetime']);
				$record_history[$i]['value_user']	= $value['owner'];

				switch ($value['status'])
				{
					case 'S': $type = lang('Status changed'); break;
					default:
				}

				if($value['new_value']=='O'){$value['new_value']=lang('Opened');}
				if($value['new_value']=='X'){$value['new_value']=lang('Closed');}

				$record_history[$i]['value_action']	= $type ? $type:'';
				unset($type);

				if ($value['status'] == 'S')
				{
					$record_history[$i]['value_new_value']	= $status_text[$value['new_value']];
					$record_history[$i]['value_old_value']	= $status_text[$value['old_value']];
				}
				else
				{
					$record_history[$i]['value_new_value']	= '';
				}

				$i++;
			}

			return $record_history;
		}

		public function get_files($id = 0)
		{
			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			$files = $vfs->ls(array(
				'string' => "/property/tenant_claim/{$id}",
				'relatives' => array(RELATIVE_NONE)
			));

			$vfs->override_acl = 0;

			foreach($files as & $file)
			{
				$file['file_name']=urlencode($file['name']);
				
			} 

			return $files;
		}

	}
