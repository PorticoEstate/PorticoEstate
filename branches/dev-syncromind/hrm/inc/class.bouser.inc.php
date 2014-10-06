<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
	* @subpackage user
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package hrm
	 */

	class hrm_bouser
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $allrows;
		var $use_session;

		var $public_functions = array
		(
			'read'			=> true,
			'read_single'		=> true,
			'save'			=> true,
			'delete'		=> true,
			'check_perms'		=> true
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

		function hrm_bouser($session=false)
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so 		= CreateObject('hrm.souser');
			$this->bocommon 	= CreateObject('hrm.bocommon');
			$this->grants = $this->so->grants;

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$this->start	= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$this->query	= phpgw::get_var('query');
			$this->sort		= phpgw::get_var('sort');
			$this->order	= phpgw::get_var('order');
			$this->filter	= phpgw::get_var('filter', 'int');
			$this->cat_id	= phpgw::get_var('cat_id', 'int');
			$this->allrows	= phpgw::get_var('allrows', 'bool');
		}


		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','hr_user',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','hr_user');

			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->sort	= $data['sort'];
			$this->order	= $data['order'];
			$this->cat_id	= $data['cat_id'];
		}


		function read()
		{
			$params = array
			(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'allrows'	=>$this->allrows
			);

			$account_info = $this->so->read($params);
			$this->total_records = $this->so->total_records;
			return $account_info;
		}

		function read_single_training($id)
		{
			$values =$this->so->read_single_training($id);
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			if($values['start_date'])
			{
				$values['start_date']	= $GLOBALS['phpgw']->common->show_date($values['start_date'],$dateformat);
			}
			if($values['end_date'])
			{
				$values['end_date']	= $GLOBALS['phpgw']->common->show_date($values['end_date'],$dateformat);
			}
			if($values['entry_date'])
			{
				$values['entry_date']	= $GLOBALS['phpgw']->common->show_date($values['entry_date'],$dateformat);
			}

			return $values;
		}

		function read_training($user_id)
		{
			$values = $this->so->read_training(array('user_id'=>$user_id, 'start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'allrows'=>$this->allrows));
			return $values;
		}


		function save($values,$action='')
		{
			$values['start_date']	= $this->bocommon->date_to_timestamp($values['start_date']);
			$values['end_date']	= $this->bocommon->date_to_timestamp($values['end_date']);

			if ($action=='edit')
			{
				if ($values['training_id'] != '')
				{

					$receipt = $this->so->edit_training($values);
				}
				else
				{
					$receipt['error'][]=array('msg'=>lang('Error'));
				}
			}
			else
			{
				$receipt = $this->so->add_training($values);
			}

			return $receipt;
		}

		function delete_training($user_id,$id)
		{
			$this->so->delete_training($user_id,$id);
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


		function select_place_list($selected='')
		{
			$places= $this->so->select_place_list();
			$place_list = $this->bocommon->select_list($selected,$places);
			return $place_list;
		}

		function get_user_data($user_id)
		{
			$account =& $GLOBALS['phpgw']->accounts;
			$account->set_account($user_id, 'u');
			$account_info = $account->read();
			$membership = $account->membership($user_id);
			$contacts = CreateObject('phpgwapi.contacts');

			$qcols = array
			(
				'n_given'    => 'n_given',
				'n_family'   => 'n_family',
				'tel_work'   => 'tel_work',
				'tel_home'   => 'tel_home',
				'tel_cell'   => 'tel_cell',
				'title'      => 'title',
				'email'      => 'email',
				'email_home' => 'email_home',
			);

			$fields = $contacts->are_users($account_info->person_id, $qcols);

			$this->boaddressbook  = CreateObject('addressbook.boaddressbook');
			$comms = $this->boaddressbook->get_comm_contact_data($fields[0]['contact_id']);

			if(is_array($comms) && isset($comms[$fields[0]['contact_id']]) )
			{
				$fields[0]['tel_work'] = $comms[$fields[0]['contact_id']]['work phone'];
				$fields[0]['tel_home'] = $comms[$fields[0]['contact_id']]['home phone'];
				$fields[0]['tel_cell'] = $comms[$fields[0]['contact_id']]['mobile (cell) phone'];
				$fields[0]['email_home'] = $comms[$fields[0]['contact_id']]['home email'];
			}

			if(!$account_info->person_id)
			{
				$sfields = rawurlencode(serialize($fields[0]));
				$contact_link   = $GLOBALS['phpgw']->link('/index.php',
					array
					(
						'menuaction'	=> 'addressbook.uiaddressbook.add_person',
						'entry'			=> $sfields,
					)
				);
			}
			else
			{
				$contact_link   = $GLOBALS['phpgw']->link('/index.php',
					array
					(
						'menuaction'	=> 'addressbook.uiaddressbook.view_person',
						'ab_id'		=> $fields[0]['contact_id']
					)
				);
			}

			$prefs_user = $this->bocommon->create_preferences('email',$user_id);

			$email_work = '';
			if($fields[0]['email'] || $prefs_user['address'])
			{
				if(isset($prefs_user['address']) && $prefs_user['address'])
				{
					$email_work = $prefs_user['address'];
				}
				else
				{
					$email_work = $fields[0]['email'];
				}
			}

			$email_home = '';
			if($fields[0]['email_home'])
			{
				$email_home = $fields[0]['email_home'];
			}

			$qcols_extra = array
			(
				array('name' =>lang('first name'), 'type' => 'link', 'link_value' =>$contact_link),
				array('name' =>lang('last name'), 'type' => 'text'),
				array('name' =>lang('work phone'), 'type' => 'text'),
				array('name' =>lang('home phone'), 'type' => 'text'),
				array('name' =>lang('cellular phone'), 'type' => 'text'),
				array('name' =>lang('title'), 'type' => 'text'),
				array('name' =>lang('work email'), 'type' => 'mail', 'link_value' => $email_work),
				array('name' =>lang('home email'), 'type' => 'mail', 'link_value' => $email_home),
			);

			if(is_array($fields))
			{
				$qcols = array_keys($qcols);
				$j=0;
				for ($i=0;$i<count($qcols);$i++)
				{
					$user_values[$j]['value'] = $fields[0][$qcols[$i]];
					$user_values[$j]['name'] = $qcols_extra[$i]['name'];
					$user_values[$j]['type'] = $qcols_extra[$i]['type'];
					$user_values[$j]['link_value'] = isset($qcols_extra[$i]['link_value']) ? $qcols_extra[$i]['link_value'] : '';
					$j++;
				}
			}

			return $user_values;
		}
	}
