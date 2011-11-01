<?php
  /**************************************************************************\
  * phpGroupWare - soaddressbook                                             *
  * http://www.phpgroupware.org                                              *
  * This program is part of the GNU project, see http://www.gnu.org/         *
  *                                                                          *
  * Copyright 2003 Free Software Foundation, Inc.                            *
  *                                                                          *
  * Originally Written by Jonathan Alberto Rivera Gomez - jarg at co.com.mx  *
  * Current Maintained by Jonathan Alberto Rivera Gomez - jarg at co.com.mx  *
  * --------------------------------------------                             *
  * Development of this application was funded by http://www.sogrp.com       *
  * --------------------------------------------                             *
  *  This program is Free Software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

/* $Id$ */

	class soaddressbook
	{
		
		/**
		* @var array $addr_type the address types available
		*/ 
		var $addr_type;

		/**
		* @var array $comm_descr the high level communication types available
		*/
		var $comm_descr;
		
		/**
		* @var array $comm_type the low level communication types available
		*/
		var $comm_type;

		/**
		* @var array $contact_type the types of contacts currently available
		*/
		var $contact_type;

		/**
		* @var array $note_type the available note types
		*/
		var $note_type;

		/**
		* @var ??? $tab_main_persons someone document me please but this one is used for organizations - think
		*/
		var $tab_main_organizations;

		/**
		* @var ??? $tab_main_persons someone document me please
		*/
		var $tab_main_persons;

		/**
		* @constructor
		*
		* @param bool $useacl respect the access controls - should be true unless you know what you are doing
		*/
		function soaddressbook($useacl = true)
		{
			$this->contacts = CreateObject('phpgwapi.contacts');

			if($useacl)
			{
				$GLOBALS['phpgw']->acl->set_account_id($GLOBALS['phpgw_info']['user']['account_id']);
				$this->grants = $GLOBALS['phpgw']->acl->get_grants('addressbook','.');				
			}

			if(!isset($GLOBALS['owner']))
			{
				$GLOBALS['owner'] = 0;
			}

			$this->owner = $GLOBALS['owner'];

			if(!isset($this->owner) || !$this->owner)
			{
				$this->owner = $GLOBALS['phpgw_info']['user']['account_id'];
				/* echo $this->owner; */
				$this->rights = PHPGW_ACL_READ + PHPGW_ACL_ADD + PHPGW_ACL_EDIT + PHPGW_ACL_DELETE + 16;
				/* echo $rights; */
			}
			else
			{
				if($this->grants[$this->owner])
				{
					$this->rights = $this->grants[$this->owner];
					if (!($this->rights & PHPGW_ACL_READ))
					{
						$this->owner = $GLOBALS['phpgw_info']['user']['account_id'];
						$this->rights = PHPGW_ACL_READ + PHPGW_ACL_ADD + PHPGW_ACL_EDIT + PHPGW_ACL_DELETE + 16;
					}
				}
			}

			$this->contact_type = $this->contacts->contact_type;
			$this->comm_descr = $this->contacts->comm_descr;
			$this->comm_type = $this->contacts->comm_type;
			$this->note_type = $this->contacts->note_type;
			$this->addr_type = $this->contacts->addr_type;
			$this->tab_main_persons = $this->contacts->get_person_name();
			$this->tab_main_organizations = $this->contacts->get_org_name();
		}

		/*************************************************************\
		* Retrieve functions section                                  *
		\*************************************************************/

		/**
		* This function call to get_persons from contact object
		* See the documentation in contact objet
		*
		*/
		function get_persons($fields, $start='', $limit='', $orderby='', $sort='', $criteria='', $token_criteria='')
		{
			return $this->contacts->get_persons($fields, $start, $limit, $orderby, $sort, $criteria, $token_criteria);
		}

		/**
		* This function call to get_principal_persons_data from contact object
		* See the documentation in contact objet
		*
		*/
		function get_principal_persons_data($id, $get_org=True)
		{
			return $this->contacts->get_principal_persons_data($id, PHPGW_SQL_RUN_SQL, $get_org);
		}

		/**
		* This function call to get_organizations_by_person from contact object
		* See the documentation in contact objet
		*
		*/
		function get_organizations_by_person($person_id, $criteria='')
		{
			return $this->contacts->get_organizations_by_person($person_id, $criteria);
		}

		/**
		* This function call to get_orgs from contact object
		* See the documentation in contact objet
		*
		*/
		function get_orgs($fields, $start='', $limit='', $orderby='', $sort='', $criteria='', $token_criteria='')
		{
			return $this->contacts->get_orgs($fields, $start, $limit, $orderby, $sort, $criteria, $token_criteria);
		}

		/**
		* This function call to get_principal_organizations_data from contact object
		* See the documentation in contact objet
		*
		*/
		function get_principal_organizations_data($id)
		{
			return $this->contacts->get_principal_organizations_data($id);
		}

		/**
		* This function call to get_people_by_organizations from contact object
		* See the documentation in contact objet
		*
		*/
		function get_people_by_organizations($id, $criteria='')
		{
			return $this->contacts->get_people_by_organizations($id, $criteria);
		}

		/**
		* This function call to get_comm_contact_data from contact object
		* See the documentation in contact objet
		*
		*/
		function get_comm_contact_data($contacts, $fields_comms='')
		{
			return $this->contacts->get_comm_contact_data($contacts, $fields_comms);
		}

		/**
		* This function call to get_addr_contact_data from contact object
		* See the documentation in contact objet
		*
		*/
		function get_addr_contact_data($contact_id, $criteria='')
		{
			return $this->contacts->get_addr_contact_data($contact_id, $criteria);
		}

		/**
		* This function call to get_others_contact_data from contact object
		* See the documentation in contact objet
		*
		*/
		function get_others_contact_data($id, $criteria='')
		{
			return $this->contacts->get_others_contact_data($id, $criteria);
		}

		/**
		* This function call to get_contact_addr_type from contact object
		* See the documentation in contact objet
		*
		*/
		function get_addr_type()
		{
			return $this->contacts->get_contact_addr_type();
		}

		/**
		* This function call to get_contact_comm_descr from contact object
		* See the documentation in contact objet
		*
		*/
		function get_comm_descr()
		{
			return $this->contacts->get_contact_comm_descr();
		}

		/**
		* This function call to get_contact_comm_type from contact object
		* See the documentation in contact objet
		*
		*/
		function get_comm_type()
		{
			return $this->contacts->get_contact_comm_type();
		}

		/**
		* This function call to get_count_persons from contact object
		* See the documentation in contact objet
		*
		*/
		function get_count_persons($criteria='')
		{
			return $this->contacts->get_count_persons($criteria);
		}

		/**
		* This function call to get_count_orgs from contact object
		* See the documentation in contact objet
		*
		*/
		function get_count_orgs($criteria='')
		{
			return $this->contacts->get_count_orgs($criteria);
		}

		/**
		* This function call to get_persons_by_cat from contact object
		* See the documentation in contact objet
		*
		*/
		function get_persons_by_cat($cats)
		{
			return $this->contacts->get_persons_by_cat($cats);
		}

		/**
		* This function call to get_email from contact object
		* See the documentation in contact objet
		*
		*/
		function get_email($id)
		{
			return $this->contacts->get_email($id);
		}

		/**
		* This function call to get_phone from contact object
		* See the documentation in contact objet
		*
		*/
		function get_phone($id)
		{
			return $this->contacts->get_phone($id);
		}

		/**
		* This function call to get_sub_cats from contact object
		* See the documentation in contact objet
		*
		*/
		function get_sub_cats($cat_to_find)
		{
			return $this->contacts->get_sub_cats($cat_to_find);
		}

		/*************************************************************\
		* Search in catalogs functions section                        *
		\*************************************************************/

		/**
		* This function call to search_location_type_id from contact object
		* See the documentation in contact objet
		*
		*/
		function search_location_type_id($id)
		{
			return $this->contacts->search_location_type_id($id);
		}
		
		/**
		* This function call to search_location_type from contact object
		* See the documentation in contact objet
		*
		*/
		function search_location_type($description)
		{
			return $this->contacts->search_location_type($description);
		}

		/**
		* This function call to search_note_type_id from contact object
		* See the documentation in contact objet
		*
		*/
		function search_note_type_id($id)
		{
			return  $this->contacts->search_note_type_id($id);
		}
		
		/**
		* This function call to search_note_type from contact object
		* See the documentation in contact objet
		*
		*/
		function search_note_type($description)
		{
			return $this->contacts->search_note_type($description);
		}
		
		/**
		* This function call to search_comm_type_id from contact object
		* See the documentation in contact objet
		*
		*/
		function search_comm_type_id($id)
		{
			return $this->contacts->search_comm_type_id($id);
		}
		
		/**
		* This function call to search_comm_type from contact object
		* See the documentation in contact objet
		*
		*/
		function search_comm_type($description)
		{
			return $this->contacts->search_comm_type($description);
		}

		/**
		* This function call to search_comm_descr_id from contact object
		* See the documentation in contact objet
		*
		*/
		function search_comm_descr_id($id)
		{
			return $this->contacts->search_comm_descr_id($id);
		}
		
		/**
		* This function call to search_comm_descr from contact object
		* See the documentation in contact objet
		*
		*/
		function search_comm_descr($description)
		{
			return $this->contacts->search_comm_descr($description);
		}

		/**
		* This function call to search_contact_type_id from contact object
		* See the documentation in contact objet
		*
		*/
		function search_contact_type_id($id)
		{
			return $this->contacts->search_contact_type_id($id);
		}
		
		/**
		* This function call to search_contact_type from contact object
		* See the documentation in contact objet
		*
		*/
		function search_contact_type($description)
		{
			return $this->contacts->search_contact_type($description);
		}

		/*************************************************************\
		* Edit contact section                                        *
		\*************************************************************/

		function edit_person($person_id, $fields)
		{
			$principal['owner'] = $fields['owner'];
			$principal['access'] = $fields['tab_person_data']['ispublic'];
			$preferred_force_addr = isset($fields['tab_address']['addr_preferred']) ? $fields['tab_address']['addr_preferred'] : 0;

			$cats = array();
			if(is_array($fields['tab_cats']['my_cats']))
			{
				foreach($fields['tab_cats']['my_cats'] as $cat)
				{
					if($cat)
					{
						$cats[] = $cat;
					}
				}
			}

			$principal['cat_id'] = $cats;
			$person = array_merge($fields['tab_person_data'], $fields['tab_extra']);
			$orgs = $fields['edit_orgs'];
			$orgs['preferred_org'] = isset($fields['tab_orgs']['preferred_org']) ? $fields['tab_orgs']['preferred_org'] : 0;
			$queries = $fields['transactions'];

			//unset($fields['tab_person_data']);
			unset($fields['tab_extra']);
			unset($fields['tab_orgs']);
			unset($fields['edit_orgs']);
			unset($fields['tab_cats']);
			unset($fields['old_my_orgs']);
			unset($fields['tab_address']);
			unset($fields['addr_data']);
			unset($fields['transactions']);
			//unset($fields['tab_comms']);
			unset($fields['old_comm']);

			$this->contacts->edit_contact($person_id, $principal, PHPGW_SQL_RUN_SQL);
			$this->contacts->edit_person($person_id, $person, PHPGW_SQL_RUN_SQL);
			foreach($orgs['delete'] as $org_id)
			{
				$this->contacts->delete_org_person_relation($org_id, $person_id, PHPGW_SQL_RUN_SQL);
			}

			if($orgs['preferred_org'])
			{
				$fields['preferred_address'] = $this->contacts->get_location_pref_org($orgs['preferred_org']);
			}

			if(count($orgs['insert'])>0)
			{
				$this->contacts->add_orgs_for_person($orgs['insert'], 
								     $orgs['preferred_org'], 
								     $fields['preferred_address'], 
								     $person_id, PHPGW_SQL_RUN_SQL);
			}
			else
			{
				if ( !isset($fields['preferred_address']) )
				{
					$fields['preferred_address'] = 0;
				}

				$data =  array('my_preferred' => 'N');
				$this->contacts->edit_org_person_relation('', $person_id, $data, PHPGW_SQL_RUN_SQL);
				$data = array('my_preferred' => 'Y',
					      'my_addr_id' => $fields['preferred_address']);
				$this->contacts->edit_org_person_relation($orgs['preferred_org'], $person_id, $data, PHPGW_SQL_RUN_SQL);
			}
			
			$comm_preferred = $fields['tab_comms']['preferred'];

			//FIXME this is a hack cos i am sick of fixing broken written by lazy developers! skwashd 20060908
			@$this->upgrade_comms($fields['edit_comms']['insert'], 
					     $fields['edit_comms']['delete'], 
					     $fields['edit_comms']['edit'],
			    	     $fields['comm_data'], $comm_preferred, $person_id);

			//FIXME this is a hack cos i am sick of fixing broken written by lazy developers! skwashd 20060908
			@$this->upgrade_others($fields['edit_others']['insert'],
					      $fields['edit_others']['delete'],
					      $fields['edit_others']['edit'],
					      $fields['tab_others']['other_value'], $person_id);
			
			$this->execute_queries($queries);
			
			if($preferred_force_addr && $preferred_force_addr!='')
                        {
                                $preferred_force_addr = $this->get_preferred_location($person_id, $preferred_force_addr);

                                $this->contacts->edit_location_by_contact(
					$person_id,
					array('addr_preferred' => 'N'),
					PHPGW_SQL_RUN_SQL);

                                $this->contacts->edit_location(
					$preferred_force_addr,
					array('addr_preferred' => 'Y'),
					PHPGW_SQL_RUN_SQL);
                        }

			/* Update the first and last name in accounts */
			if($fields['tab_person_data'])
			{
				$account_id = $this->contacts->get_account_id($person_id);
				if($account_id)
				{
					$account = CreateObject('phpgwapi.accounts',$account_id,'u');
					$account_data = $account->read();
					$account_data->firstname = $fields['tab_person_data']['per_first_name'];
					$account_data->lastname = $fields['tab_person_data']['per_last_name'];
					$account->update_data($account_data);
					$account->save_repository();
				}
			}
			
			$this->contacts->finalize_edit($person_id);
		}
		
		function edit_org($org_id, $fields)
		{
			$principal['owner'] = $fields['owner'];
			$principal['access'] = $fields['tab_org_data']['ispublic'];
			$preferred_force_addr = $fields['tab_address']['addr_preferred'];

			if(is_array($fields['tab_cats']['my_cats']))
			{
				foreach($fields['tab_cats']['my_cats'] as $cat)
				{
					if($cat)
					{
						$cats[] = $cat;
					}
				}
			}
			else
			{
				$cats = '';
			}
			
			$principal['cat_id'] = $cats;
			$org = $fields['tab_org_data'];
			$persons = $fields['edit_persons'];
			$queries = $fields['transactions'];

			$this->contacts->edit_contact($org_id, $principal, PHPGW_SQL_RUN_SQL);
			$this->contacts->edit_org($org_id, $org, PHPGW_SQL_RUN_SQL);
			foreach($persons['delete'] as $person_id)
			{
				$this->contacts->delete_org_person_relation($org_id, $person_id, PHPGW_SQL_RUN_SQL);
			}
			
			$this->contacts->add_people_for_organzation($persons['insert'], $org_id, PHPGW_SQL_RUN_SQL);

			$this->upgrade_comms($fields['edit_comms']['insert'], 
					     $fields['edit_comms']['delete'], 
					     $fields['edit_comms']['edit'],
					     $fields['comm_data'], '', $org_id);
			
			$this->upgrade_others($fields['edit_others']['insert'],
					      $fields['edit_others']['delete'],
					      $fields['edit_others']['edit'],
					      $fields['tab_others']['other_value'], $org_id);
			
 			$this->execute_queries($queries);

			if($preferred_force_addr && $preferred_force_addr!='')
                        {
                                $preferred_force_addr = $this->get_preferred_location($org_id, $preferred_force_addr);

                                $this->contacts->edit_location_by_contact(
					$org_id,
					array('addr_preferred' => 'N'),
					PHPGW_SQL_RUN_SQL);

                                $this->contacts->edit_location(
					$preferred_force_addr,
					array('addr_preferred' => 'Y'),
					PHPGW_SQL_RUN_SQL);
                        }
		}

		function get_preferred_location($contact_id, $preferred_forced)
                {
                        $addr_tmp = $this->contacts->get_addr_contact_data($contact_id);
                        if(is_array($addr_tmp))
                        {
                                foreach($addr_tmp as $data)
				{
					if($preferred_forced==$data['key_addr_id'])
					{
						return $preferred_forced;
					}
					else
					{
						$locations[$data['key_addr_id']] = $data;
					}
				}
                        }
                        ksort($locations);
                        end($locations);
                        return key($locations);
                }

		/**
		* This function call to edit_location from contact object
		* See the documentation in contact objet
		*
		*/
		function edit_location($contact_id, $fields)
		{
			return $this->contacts->edit_location($contact_id, $fields);
		}

		/**
		* This function call to edit_comms from contact object
		* See the documentation in contact objet
		*
		*/
		function edit_comms($comm_id, $fields, $action=PHPGW_SQL_RETURN_SQL)
		{
			return $this->contacts->edit_comms($comm_id, $fields, $action);
		}

		/**
		* This function call to edit_other from contact object
		* See the documentation in contact objet
		*
		*/
		function edit_other($contact_id, $fields, $action=PHPGW_SQL_RETURN_SQL)
		{
			return $this->contacts->edit_other($contact_id, $fields, $action);
		}

		/**
		* This function call to edit_comms_by_contact from contact object
		* See the documentation in contact objet
		*
		*/
		function edit_comms_by_contact($id, $data, $action=PHPGW_SQL_RETURN_SQL)
		{
			return $this->contacts->edit_comms_by_contact($id, $data, $action);
		}

		/*************************************************************\
		* Add contact section                                        *
		\*************************************************************/

		//used
		function add_person($fields)
		{			
			$principal = array_merge($fields['tab_person_data'], $fields['tab_extra']);
			
			if(!is_array($fields['tab_comms']['comm_data']))
			{
				$fields['tab_comms']['comm_data']=array();
			}
			
			$comms = array();
			foreach($fields['tab_comms']['comm_data'] as $type_descr => $data)
			{
				if($data)
				{
					$comms[] = array('comm_descr' 		=> $this->contacts->search_comm_descr($type_descr),
							 'comm_data' 		=> $data,
							 'comm_preferred' 	=> ($type_descr==$fields['tab_comms']['preferred']?'Y':'N'));
				}
			}
			
			$addr = array();
			if( isset($fields['addr_data']) && isset($fields['tab_address']['addr_preferred'])
				&& is_array($fields['addr_data']))
			{
				$fields['addr_data'][$fields['tab_address']['addr_preferred']]['addr_preferred']='Y';
				$addr = $fields['addr_data'];
			}

			if( isset($fields['others_data'])
				&& is_array($fields['others_data']) )
			{
				foreach($fields['others_data'] as $key => $data)
				{
					$fields['others_data'][$key]['other_value'] = $fields['tab_others']['other_value'][$key];
				}
			}
			$others = $fields['others_data'];
			
			$orgs = isset($fields['tab_orgs']['my_orgs']) ? $fields['tab_orgs']['my_orgs'] : array();
			$principal['preferred_org'] = isset($fields['tab_orgs']['preferred_org']) ? $fields['tab_orgs']['preferred_org'] : 0;

			if( isset($fields['tab_orgs']['preferred_org'])
				&& $fields['tab_orgs']['preferred_org'] )
			{
				$principal['preferred_address'] = $this->contacts->get_location_pref_org($principal['preferred_org']);
			}
			else
			{
				$principal['preferred_address'] = 0;
			}

			$cats = isset($fields['tab_cats']['my_cats']) ? $fields['tab_cats']['my_cats'] : array();

			$type = $this->contacts->search_contact_type($this->contacts->get_person_name());
			$c_id = $this->contacts->add_contact($type, $principal, $comms, $addr, $cats, $others, $orgs);

			return $c_id;
		}

		//used
		function add_org($fields)
		{
			$principal = $fields['tab_org_data'];

			if(!is_array($fields['tab_comms']['comm_data']))
			{
				$fields['tab_comms']['comm_data']=array();
			}

			foreach($fields['tab_comms']['comm_data'] as $type_descr => $data)
			{
				if($data)
				{
					$comms[] = array('comm_descr' 		=> $this->contacts->search_comm_descr($type_descr),
							 'comm_data' 		=> $data,
							 'comm_preferred' 	=> ($type_descr==$fields['tab_comms']['preferred']?'Y':'N'));
				}
			}
			
			if(is_array($fields['addr_data']))
			{
				$fields['addr_data'][$fields['tab_address']['addr_preferred']]['addr_preferred']='Y';
				$addr = $fields['addr_data'];
			}
			
			if(is_array($fields['others_data']))
			{
				foreach($fields['others_data'] as $key => $data)
				{
					$fields['others_data'][$key]['other_value'] = $fields['tab_others']['other_value'][$key];
				}
			}
			$others = $fields['others_data'];
			
			$persons = $fields['tab_persons']['my_person'];
			
			$cats = $fields['tab_cats']['my_cats'];

			$type = $this->contacts->search_contact_type($this->contacts->get_org_name());
			$c_id = $this->contacts->add_contact($type, $principal, $comms, $addr, $cats, $others, $persons);
			return $c_id;
		}

		/**
		* This function call to add_others from contact object
		* See the documentation in contact objet
		*
		*/
		function add_others($fields, $contact_id, $action=PHPGW_SQL_RETURN_SQL)
		{
			return $this->contacts->add_others($fields, $contact_id, $action);
		}
		
		/**
		* This function call to add_communication_media from contact object
		* See the documentation in contact objet
		*
		*/
		function add_communication_media($fields, $contact_id, $action=PHPGW_SQL_RETURN_SQL)
		{
			return $this->contacts->add_communication_media($fields, $contact_id, $action);
		}

		/**
		* This function call to add_location from contact object
		* See the documentation in contact objet
		*
		*/
		function add_location($fields, $contact_id)
		{
			return $this->contacts->add_location($fields, $contact_id);
		}

		function add_contact_with_email($name, $email)
		{
                        $named = explode(' ', $name);
			for ($i=count($named);$i>=0;$i--)
                        {
				$names[$i] = $named[$i];
			}
			if ($names[2])
                        {
				$principal['per_first_name']  = $names[0];
                                $principal['per_middle_name'] = $names[1];
                                $principal['per_last_name'] = $names[2];
                        }
                        else
                        {
                                $principal['per_first_name']  = $names[0];
                                $principal['per_last_name'] = $names[1];
                        }

			$principal['access'] = 'private';
			$principal['owner'] = $GLOBALS['phpgw_info']['user']['account_id'];
			
			$comms[] = array('comm_descr' 		=> $this->search_comm_descr('work email'),
					 'comm_data' 		=> $email,
					 'comm_preferred' 	=> 'Y');

			$type = $this->contacts->search_contact_type($this->contacts->get_person_name());
			$c_id = $this->contacts->add_contact($type, $principal, $comms);

			return $c_id;
		}
		

		/*************************************************************\
		* Delete contact section                                    *
		\*************************************************************/

		/**
		* This function call to delete from contact object
		* See the documentation in contact objet
		*
		*/
		function delete($contact_id, $contact_type)
		{
			return $this->contacts->delete($contact_id, $contact_type);
		}

		/**
		* Delete the specified communication media.
		* 
		* @param integer|array $id Key of the comm media what you want
		*/
		function delete_specified_comm($id, $action=PHPGW_SQL_RETURN_SQL)
		{
			return $this->contacts->delete_specified_comm($id, $action);
		}

		/**
		* Delete the specified address.
		* 
		* @param integer|array $id Key of the address what you want
		*/
		function delete_specified_location($id, $action=PHPGW_SQL_RETURN_SQL)
		{
			return $this->contacts->delete_specified_location($id, $action);
		}
		/**
		* Delete the specified others field.
		* 
		* @param integer|array $id Key of the other field what you want
		*/
		function delete_specified_other($id, $action=PHPGW_SQL_RETURN_SQL)
		{
			return $this->contacts->delete_specified_other($id, $action);
		}

		/**
		* Delete the specified note.
		* 
		* @param integer|array $id Key of the note what you want
		*/
		function delete_specified_note($id, $action=PHPGW_SQL_RETURN_SQL)
		{
			return $this->contacts->delete_specified_note($id, $action);
		}

		/*************************************************************\
		* Misc functions section                                     *
		\*************************************************************/

		function upgrade_comms($add_comms=array(), $del_comms=array(), $edit_comms=array(), $data=array(), $comm_preferred='', $contact_id='')
		{
			if(!is_array($add_comms))
			{
				$add_comms = array();
			}
			if(!is_array($del_comms))
			{
				$del_comms = array();
			}
			if(!is_array($edit_comms))
			{
				$edit_comms = array();
			}

			if(!is_array($data))
			{
				$data = array();
			}
			$this->edit_comms_by_contact($contact_id, array('comm_preferred'=>'N'), PHPGW_SQL_RUN_SQL);

			foreach($data as $key => $value)
			{
				if(array_key_exists($value['comm_description'], $del_comms))
				{
					$this->delete_specified_comm($value['key_comm_id'], PHPGW_SQL_RUN_SQL);
				}
				
				if(array_key_exists($value['comm_description'], $edit_comms))
				{
					if($comm_preferred == $value['comm_description'])
					{
						$preferred = 'Y';
					}
					else
					{
						$preferred = 'N';
					}
					
					$this->edit_comms($value['key_comm_id'],
							  array('comm_data' => $edit_comms[$value['comm_description']],
								'comm_preferred' => $preferred), 
							  PHPGW_SQL_RUN_SQL);
				}
			}

			foreach($add_comms as $key => $value)
			{
				if($comm_preferred == $key)
				{
					$pref = 'Y';
				}
				else
				{
					$pref = 'N';
				}
				
				$fields = array('comm_descr' => $this->search_comm_descr($key),
						'comm_data' => $value,
						'comm_preferred' => $pref);
				
				$this->add_communication_media($fields, $contact_id, PHPGW_SQL_RUN_SQL);
			}
			$this->unlock();
		}

		function upgrade_others($add_others=array(), $del_others=array(), $edit_others=array(), $data=array(), $contact_id)
		{
			if(!is_array($data))
			{
				$data = array();
			}
			if(!is_array($edit_others))
			{
				$edit_others = array();
			}
			if(!is_array($add_others))
			{
				$add_others = array();
			}
			foreach($data as $key => $value)
			{
				if(array_key_exists($key, $edit_others))
				{
					$this->edit_other($key, 
							  array('other_value' => $value), 
							  PHPGW_SQL_RUN_SQL);
				}
			}

			foreach($add_others as $key => $fields)
			{
				$fields['other_value'] = $data[$key];
				$this->add_others($fields, $contact_id, PHPGW_SQL_RUN_SQL);
			}
			
			$this->unlock();
		}

		/**
		* Criteria for index primordially
		*
		* return string criteria for search.
		*/
		function criteria_contacts($user, $access, $category, $fields, $pattern, $show_fields)
		{
			return $this->contacts->criteria_for_index($user, $access, $category, $fields, $pattern, $show_fields);
		}

		/**
		* This function call to get_type_contact from contact object
		* See the documentation in contact objet
		*
		*/
		function get_type_contact($contact_id)
		{
			return $this->contacts->get_type_contact($contact_id);
		}

		/**
		* This function call to copy_contact from contact object
		* See the documentation in contact objet
		*
		*/
		function copy_contact($contact_id)
		{
			return $this->contacts->copy_contact($contact_id);
		}

		/**
		* This function call to contact_import from contact object
		* See the documentation in contact objet
		*
		*/
		function contact_import($entry)
		{
			return $this->contacts->contact_import($entry);
		}

		/**
		* This function call to display_name from contact object
		* See the documentation in contact objet
		*
		*/
		function display_name($column)
		{
			return $this->contacts->display_name($column);
		}

		/**
		* This function call to execute_queries from contact object
		* See the documentation in contact objet
		*
		*/
		function execute_queries($queries)
		{
			return $this->contacts->execute_queries($queries);
		}

		/**
		* This function call to unlock_table from contact object
		* See the documentation in contact objet
		*
		*/
		function unlock()
		{
			$this->contacts->unlock_table();
		}

		/*************************************************************\
		* Check ACL contact section                                   *
		\*************************************************************/

		/**
		* Check if the contact has add permissions.
		* 
		* @param integer $contact_id The contact_id which you want to check
		* @param integer $owner_id The owner_id of the contact which you want to check
		*/
		function check_add($contact_id, $owner_id='')
		{
			return $this->contacts->check_add($contact_id, $owner_id);
		}
		
		/**
		* Check if the contact has edit permissions.
		* 
		* @param integer $contact_id The contact_id which you want to check
		* @param integer $owner_id The owner_id of the contact which you want to check
		*/
		function check_edit($contact_id, $owner_id='')
		{
			return $this->contacts->check_edit($contact_id, $owner_id);
		}
		
		/**
		* Check if the contact has read permissions.
		* 
		* @param integer $contact_id The contact_id which you want to check
		* @param integer $owner_id The owner_id of the contact which you want to check
		*/
		function check_read($contact_id, $owner_id='')
		{
			return $this->contacts->check_read($contact_id, $owner_id);
		}
		
		/**
		* Check if the contact has delete permissions.
		* 
		* @param integer $contact_id The contact_id which you want to check
		* @param integer $owner_id The owner_id of the contact which you want to check
		*/
		function check_delete($contact_id, $owner_id='')
		{
			return $this->contacts->check_delete($contact_id, $owner_id);
		}

		/**
		* Load a user's prefernces
		*
		* @param string $contact_type the current type of contacts being used [organization|persons]
		* @return array list of fields the user wants displayed in summary view - empty array for not set
		*/
		function read_preferences($contact_type)
		{
			$prefs = $GLOBALS['phpgw']->preferences->read();
			$prefs = isset($prefs['addressbook']) ? $prefs['addressbook'] : array();
			if(isset($prefs['person_columns']) && $contact_type==$this->tab_main_persons)
			{
				
				return $prefs['person_columns'];
			}
			elseif(isset($prefs['org_columns']) && $contact_type==$this->tab_main_organizations)
			{
				return $prefs['org_columns'];
			}	
		}
	}
