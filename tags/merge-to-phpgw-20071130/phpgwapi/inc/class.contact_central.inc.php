<?php
	/**
	* Query statements for "central" table
	* @author Edgar Antonio Luna <eald@co.com.mx>
	* @copyright Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage contacts
	* @version $Id: class.contact_central.inc.php 17062 2006-09-03 06:15:27Z skwashd $
	*/

	/**
	* Use SQL criteria
	*/
	include_once(PHPGW_API_INC . '/class.sql_criteria.inc.php');
	/**
	* Use SQL entity
	*/
	include_once(PHPGW_API_INC . '/class.sql_entity.inc.php');

	/**
	* Query statements for "central" table
	*
	* @package phpgwapi
	* @subpackage contacts
	*/
	class contact_central extends sql_entity
	{
		var $map = array('contact_id'	=> array('select'	=> '',
							 'criteria' 	=> '',
							 'insert'   	=> '',
							 'update'	=> '',
							 'delete'	=> '',
							 'sort'		=> '',
							 'field'	=> '',
							 'type'		=> 'integer'),
				 'owner'	=> array('select'   	=> '',
							 'criteria' 	=> '',
							 'insert'   	=> '',
							 'update'	=> '',
							 'delete'	=> '',
							 'sort'     	=> '',
							 'field'	=> '',
							 'type'		=> 'integer'),
				 'access'	=> array('select'   	=> '',
							 'criteria' 	=> '',
							 'insert'   	=> '',
							 'update'	=> '',
							 'delete'	=> '',
							 'sort'     	=> '',
							 'field'	=> '',
				 			 'type'		=> 'string'),
				 'cat_id'	=> array('select'   	=> '',
							 'criteria' 	=> '',
							 'insert'   	=> '',
							 'update'	=> '',
							 'delete'	=> '',
							 'sort'     	=> '',
							 'field'	=> '',
				 			 'type'		=> 'string'),
				 'contact_type'	=> array('select'   	=> '',
							 'criteria' 	=> '',
							 'insert'   	=> '',
							 'update'	=> '',
							 'delete'	=> '',
							 'sort'     	=> '',
							 'field'	=> 'contact_type_id',
				 			 'type'		=> 'integer'),
				 'sel_cat_id'	=> array('select'   	=> '',
							 'criteria' 	=> '',
							 'insert'   	=> '',
							 'update'	=> '',
							 'delete'	=> '',
							 'sort'     	=> '',
							 'field'	=> 'cat_id'),
				'count_contacts'=> array('select'   	=> '',
							 'criteria' 	=> '',
							 'insert'   	=> '',
							 'update'	=> '',
							 'delete'	=> '',
							 'sort'     	=> '',
							 'field'	=> ''),
				 'max_contacts' => array('select'   	=> '',
							 'criteria' 	=> '',
							 'insert'   	=> '',
							 'update'	=> '',
							 'delete'	=> '',
							 'sort'     	=> '',
							 'field'	=> ''),
				'people_contact'=> array('select'	=> 'unlink_org'),
			'organizations_contact'	=> array('select'	=> 'unlink_org'));
		


		function contact_central ($ali = '', $field = '', $criteria = 	'')
		{
			$this->_constructor('phpgw_contact', 'contact_central');

			if($field)
			{
				$this->add_select($field);
			}
			if($criteria)
			{
				$this->add_criteria($criteria);
			}
			$this->set_elinks('contact_id', 'phpgwapi.contact_person', 'person_id');
			$this->set_elinks('contact_id', 'phpgwapi.contact_org', 'org_id');
			$this->set_elinks('contact_id', 'phpgwapi.contact_note', 'note_contact_id');
			$this->set_elinks('contact_id', 'phpgwapi.contact_addr', 'addr_contact_id');
			$this->set_elinks('contact_id', 'phpgwapi.contact_comm', 'comm_contact_id');
			$this->set_elinks('contact_id', 'phpgwapi.contact_others', 'other_contact_id');

			$this->set_ilinks('contact_type', 'phpgwapi.contact_types','contact_type_id');
			// $this->set_ilinks('owner', 'phpgwapi.contact_accounts','account_id');
			//$this->set_ilinks('cat_id', 'contact_categories','key_cat_id');
		}

		function unlink_org()
		{
			unset($this->elink['phpgwapi.contact_org']);
		}

		function unlink_person()
		{
			unset($this->elink['phpgwapi.contact_person']);
		}

		function criteria_contact_id($element)
		{
 			$this->_add_criteria($this->index_criteria($element));			
		}

		function criteria_access($element)
		{
			$this->_add_criteria($this->index_criteria($element)); 
		}

		function criteria_contact_type($element)
		{
			$this->_add_criteria($this->index_criteria($element)); 
		}

		function criteria_owner($element)
		{
			$this->_add_criteria($this->index_criteria($element));
		}

		function select_count_contacts()
		{
			$this->add_field('count_contacts', 'count(contact_id)');
		}

		function select_max_contacts()
		{
			$this->add_field('max_contacts', 'max(contact_id)');
		}

		function criteria_sel_cat_id($element)
		{
			$field = $this->put_alias($element['real_field']);

			if(is_array($element['value']))
			{
				foreach($element['value'] as $value)
				{
					$data[] = sql_criteria::or_(sql_criteria::equal($field, sql::string($value)),
									sql_criteria::has($field, ',' . $value . ','));
				}
				
				$criteria = sql_criteria::append_or($data);
				$this->_add_criteria($criteria);
			}
			else
			{
				$this->_add_criteria(sql_criteria::equal($field, sql::string($element['value'])));
			}
		}
	}
?>