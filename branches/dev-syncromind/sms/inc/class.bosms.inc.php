<?php
	/**
	* phpGroupWare - SMS: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2011 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package sms
	* @subpackage place
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package sms
	 */

	class sms_bosms
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $acl_location;

		var $public_functions = array
		(
			'read'			=> true,
			'read_single'	=> true,
			'save'			=> true,
			'delete'		=> true,
			'check_perms'	=> true
		);

		function __construct($session=false)
		{
			$this->sms 		= CreateObject('sms.sms');
			$this->so 		= CreateObject('sms.sosms');

			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

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
			$allrows= phpgw::get_var('allrows', 'bool');

			$this->start = $start ? $start : 0;

			if(array_key_exists('query',$_POST) || array_key_exists('query',$_GET))
			{
				$this->query = $query;
			}
			if(array_key_exists('filter',$_POST) || array_key_exists('filter',$_GET))
			{
				$this->filter = $filter;
			}
			if(array_key_exists('sort',$_POST) || array_key_exists('sort',$_GET))
			{
				$this->sort = $sort;
			}
			if(array_key_exists('order',$_POST) || array_key_exists('order',$_GET))
			{
				$this->order = $order;
			}
			if(array_key_exists('cat_id',$_POST) || array_key_exists('cat_id',$_GET))
			{
				$this->cat_id = $cat_id;
			}
			if ($allrows)
			{
				$this->allrows = $allrows;
			}
		}


		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','hr_place',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','hr_place');

			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->sort		= $data['sort'];
			$this->order	= $data['order'];
			$this->cat_id	= $data['cat_id'];
		}

		function read_inbox()
		{
			$inbox = $this->so->read_inbox(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'allrows'=>$this->allrows,'acl_location' =>$this->acl_location));
			$this->total_records = $this->so->total_records;

			foreach($inbox as $dummy => &$msg)
			{
				$msg['entry_time'] = $GLOBALS['phpgw']->common->show_date(strtotime($msg['entry_time']));
			}

			return $inbox;
		}

		function read_outbox()
		{
			$outbox = $this->so->read_outbox(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'allrows'=>$this->allrows,'acl_location' =>$this->acl_location));

			foreach($outbox as $dummy => &$msg)
			{
				$msg['entry_time'] = $GLOBALS['phpgw']->common->show_date(strtotime($msg['entry_time']));
			}

			$this->total_records = $this->so->total_records;
			return $outbox;
		}

		function read_single($id)
		{
			$values =$this->so->read_single($id);
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			if($values['entry_date'])
			{
				$values['entry_date']	= $GLOBALS['phpgw']->common->show_date($values['entry_date'],$dateformat);
			}

			return $values;
		}


		function send_sms($values)
		{
			$username = $GLOBALS['phpgw']->accounts->id2name($this->account);

			$p_num = $values['p_num'];
			if (!$p_num[0])
			{
	    		$p_num = $values['p_num_text'];
			}
			$sms_to			= $p_num;
			$msg_flash		= $values['msg_flash'];
			$msg_unicode	= $values['msg_unicode'];
			$message		= $values['message'];
			if (($p_num || $sms_to) && $message)
			{
				$sms_type = "text";
				if ($msg_flash == "on")
				{
					$sms_type = "flash";
			    }
			    $unicode = "0";
			    if ($msg_unicode == "on")
	    		{
					$unicode = "1";
	    		}

				list($ok,$to) = $this->sms->websend2pv($username,$sms_to,$message,$sms_type,$unicode);
				for ($i=0;$i<count($ok);$i++)
			    {
					if ($ok[$i])
					{
				    		$receipt['message'][]=array('msg'=>lang('Your SMS for %1 has been delivered to queue',$to[$i] ));
				    		$error_string .= "Your SMS for `".$to[$i]."` has been delivered to queue<br>";
					}
					else
					{
				    		$receipt['message'][]=array('msg'=>lang('Fail to sent SMS to %1',$to[$i] ));
			        }
			    }
			}
			return $receipt;
		}

		function delete_out($id)
		{
			$this->so->delete_out($id);
		}

		function delete_in($id)
		{
			$this->so->delete_in($id);
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

			$categories= $this->so->select_category_list();

			while (is_array($categories) && list(,$category) = each($categories))
			{
				$sel_category = '';
				if ($category['id']==$selected)
				{
					$sel_category = 'selected';
				}

				$category_list[] = array
				(
					'cat_id'	=> $category['id'],
					'name'		=> $category['name'],
					'selected'	=> $sel_category
				);
			}

			for ($i=0;$i<count($category_list);$i++)
			{
				if ($category_list[$i]['selected'] != 'selected')
				{
					unset($category_list[$i]['selected']);
				}
			}

			return $category_list;
		}

	}
