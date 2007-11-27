<?php
	/**
	* Query statements for "comm_descr" table
	* @author Edgar Antonio Luna <eald@co.com.mx>
	* @copyright Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage contacts
	* @version $Id: class.contact_comm_descr.inc.php 15562 2004-12-30 06:47:35Z skwashd $
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
	* Query statements for "comm_descr" table
	*
	* @package phpgwapi
	* @subpackage contacts
	*/
	class contact_comm_descr extends sql_entity
	{
		var $map = array('comm_descr_id'	=> array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> '',
								 'type'		=> 'integer'),
				 'comm_type'		=> array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> 'comm_type_id',
								 'type'		=> 'integer'),
				 'comm_description'	=> array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> 'descr',
								 'type'		=> 'string'),
				 'comm_find_descr'	=> array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> 'descr'));

		function contact_comm_descr ($ali = '', $field = '', $criteria = 	'')
		{
			$this->_constructor('phpgw_contact_comm_descr', 'contact_comm_descr');
			if($field)
			{
				$this->add_select($field);
			}
			if($criteria)
			{
				$this->add_criteria($criteria);
			}
			$this->set_ilinks('comm_descr_id', 'phpgwapi.contact_comm','comm_descr');
			$this->set_elinks('comm_type', 'phpgwapi.contact_comm_type','comm_type_id');
		}

		function criteria_comm_descr_id($element)
		{
			$this->_add_criteria($this->index_criteria($element));
		}

		function criteria_comm_find_descr($element)
		{
			$field = $this->put_alias($element['real_field']);
			
			foreach($element['value'] as $value)
			{
				$data[] = sql_criteria::equal($field, sql::string($value));
			}

			$criteria = sql_criteria::append_or($data);
			$this->_add_criteria($criteria);
		}
	}
?>