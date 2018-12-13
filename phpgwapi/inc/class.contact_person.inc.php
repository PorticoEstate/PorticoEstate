<?php
	/**
	 * Query statements for "person" table
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
	 * Query statements for "person" table
	 *
	 * @package phpgwapi
	 * @subpackage contacts
	 */
	class contact_person extends phpgwapi_sql_entity
	{

		var $map = array('person_id' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => '',
				'type' => 'integer'),
			'per_full_name' => array('select' => 'full_name',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => ''),
			'per_first_name' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'first_name',
				'type' => 'string'),
			'per_last_name' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'last_name',
				'type' => 'string'),
			'per_middle_name' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'middle_name',
				'type' => 'string'),
			'per_prefix' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'prefix',
				'type' => 'string'),
			'per_suffix' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'suffix',
				'type' => 'string'),
			'per_birthday' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'birthday',
				'type' => 'string'),
			'per_pubkey' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'pubkey',
				'type' => 'string'),
			'per_title' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'title',
				'type' => 'string'),
			'per_department' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'department',
				'type' => 'string'),
			'per_initials' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'initials',
				'type' => 'string'),
			'per_sound' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'sound',
				'type' => 'string'),
			'per_active' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'active',
				'type' => 'string'),
			'per_creaton' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'created_on',
				'type' => 'integer'),
			'per_creatby' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'created_by',
				'type' => 'integer'),
			'per_modon' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'modified_on',
				'type' => 'integer'),
			'per_modby' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'modified_by',
				'type' => 'integer'),
			'per_name' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => ''),
			'fn' => array('select' => 'full_name',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => ''),
			'n_given' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'first_name',
				'type' => 'string'),
			'n_family' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'last_name',
				'type' => 'string'),
			'n_middle' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'middle_name',
				'type' => 'string'),
			'n_prefix' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'per_prefix'),
			'n_suffix' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'per_suffix'),
			'sound' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'per_sound'),
			'bday' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'per_birthday'),
			'tz' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'per_timezone'),
			'geo' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'per_geo'),
			'pubkey' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'per_pubkey'),
			'org_unit' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'per_deparment'),
			'title' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'title'),
			'count_persons' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'person_id'),
			'organizations_person' => array('select' => 'unlink_person'),
			'people' => array('select' => 'relink_org_person'),
			'people_local' => array('select' => 'relink_org_person'));

		function __construct( $ali = '', $field = '', $criteria = '' )
		{
			parent::__construct('phpgw_contact_person', 'contact_person');
			if ($field)
			{
				$this->add_select($field);
			}
			if ($criteria)
			{
				$this->add_criteria($criteria);
			}
			$this->set_ilinks('person_id', 'phpgwapi.contact_central', 'contact_id', PHPGW_SQL_REQUIRED_KEY);
			$this->set_elinks('person_id', 'phpgwapi.contact_accounts', 'account_person_id');
			$this->set_ilinks('person_id', 'phpgwapi.contact_org_person', 'my_person_id', PHPGW_SQL_LAZY_KEY);
		}

		function unlink_person()
		{
			unset($this->ilink['phpgwapi.contact_central']);
		}

		function relink_org_person()
		{
			unset($this->ilink['phpgwapi.contact_org_person']);
			$this->set_elinks('person_id', 'phpgwapi.contact_org_person', 'my_person_id');
		}

		function criteria_person_id( $element )
		{
			$this->_add_criteria($this->index_criteria($element));
		}

		function select_count_persons()
		{
			$this->add_field('count_persons', 'count(person_id)');
		}

		function full_name()
		{
			$this->add_field('per_full_name', phpgwapi_sql::concat_null(array($this->real_field('per_prefix'),
					phpgwapi_sql::string(' '),
					$this->real_field('per_first_name'), phpgwapi_sql::string(' '),
					$this->real_field('per_middle_name'), phpgwapi_sql::string(' '),
					$this->real_field('per_last_name'), phpgwapi_sql::string(' '),
					$this->real_field('per_suffix'))));
		}
	}