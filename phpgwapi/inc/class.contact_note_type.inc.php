<?php
	/**
	* Query statements for "note_type" table
	* @author Edgar Antonio Luna <eald@co.com.mx>
	* @copyright Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage contacts
	* @version $Id: class.contact_note_type.inc.php 15562 2004-12-30 06:47:35Z skwashd $
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
	* Query statements for "note_type" table
	*
	* @package phpgwapi
	* @subpackage contacts
	*/
	class contact_note_type extends sql_entity
	{
		var $map = array('note_type_id'		=> array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> '',
								 'type'		=> 'integer'),
				 'note_description'	=> array('select'	=> '',
								 'criteria' 	=> '',
								 'insert'   	=> '',
								 'update'	=> '',
								 'delete'	=> '',
								 'sort'		=> '',
								 'field'	=> 'description',
								 'type'		=> 'string'));
		
		function contact_note_type ($ali = '', $field = '', $criteria = 	'')
		{
			$this->_constructor('phpgw_contact_note_type', 'contact_note_type');
			if($field)
			{
				$this->add_select($field);
			}
			if($criteria)
			{
				$this->add_criteria($criteria);
			}
			$this->set_ilinks('note_type_id', 'phpgwapi.contact_note','note_type');
		}		

	}
?>
