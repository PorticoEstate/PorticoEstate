<?php
	/**
	 * Query statements for "org_person" table
	 * @author Edgar Antonio Luna <eald@co.com.mx>
	 * @copyright Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	 * @package phpgwapi
	 * @subpackage contacts
	 * @version $Id$
	 */
	/**
	 * Use SQL criteria
	 */
	phpgw::import_class('phpgwapi.sql_criteria');

	/**
	 * Use SQL entity
	 */
	phpgw::import_class('phpgwapi.sql_entity');

	/**
	 * Query statements for "org_person" table
	 *
	 * @package phpgwapi
	 * @subpackage contacts
	 */
	class contact_org_person extends phpgwapi_sql_entity
	{

		var $map = array('my_org_id' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'org_id',
				'type' => 'integer'),
			'my_person_id' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'person_id',
				'type' => 'integer'),
			'my_addr_id' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'addr_id',
				'type' => 'integer'),
			'my_preferred' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'preferred',
				'type' => 'string'),
			'my_creaton' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'created_on',
				'type' => 'integer'),
			'my_creatby' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'created_by',
				'type' => 'integer'),
			'people_org_person' => array('select' => 'relink_person'),
			'organizations_org_person' => array('select' => 'relink_org'));

		function __construct( $ali = '', $field = '', $criteria = '' )
		{
			parent::__construct('phpgw_contact_org_person', 'contact_org_person');
			if ($field)
			{
				$this->add_select($field);
			}
			if ($criteria)
			{
				$this->add_criteria($criteria);
			}
			$this->set_elinks('my_org_id', 'phpgwapi.contact_org', 'org_id');
			$this->set_elinks('my_person_id', 'phpgwapi.contact_person', 'person_id');
		}

		function relink_person()
		{
			unset($this->elink['phpgwapi.contact_person']);
			$this->set_ilinks('my_person_id', 'phpgwapi.contact_person', 'person_id', PHPGW_SQL_LAZY_KEY);
		}

		function relink_org()
		{
			unset($this->elink['phpgwapi.contact_org']);
			$this->set_ilinks('my_org_id', 'phpgwapi.contact_org', 'org_id', PHPGW_SQL_LAZY_KEY);
		}

		function criteria_my_org_id( $element )
		{
			$field = $this->put_alias($element['real_field']);
			$new_criteria = phpgwapi_sql_criteria::equal($field, $element['value']);
			$this->_add_criteria($new_criteria);
			$this->relink_person();
		}

		function criteria_my_person_id( $element )
		{
			$field = $this->put_alias($element['real_field']);
			$new_criteria = phpgwapi_sql_criteria::equal($field, $element['value']);
			$this->_add_criteria($new_criteria);
			$this->relink_org();
		}

		function select_my_org_id( $element )
		{
			$this->_add_field(array('field' => 'my_org_id', 'real_field' => 'org_id'));
		}

		function select_my_person_id( $element )
		{
			$this->_add_field(array('field' => 'my_person_id', 'real_field' => 'person_id'));
		}

		function criteria_my_preferred( $element )
		{
			$field = $this->put_alias($element['real_field']);
			$criteria = phpgwapi_sql_criteria::or_(phpgwapi_sql_criteria::equal($field, phpgwapi_sql::string($element['value'])), phpgwapi_sql_criteria::is_null($field));
			$this->_add_criteria($criteria);
			return $criteria;
		}
	}