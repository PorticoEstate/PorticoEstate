<?php
	/**
	 * Query statements for "org" table
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
	 * Query statements for "org" table
	 *
	 * @package phpgwapi
	 * @subpackage contacts
	 */
	class contact_org extends phpgwapi_sql_entity
	{

		var $map = array('org_id' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'org_id',
				'type' => 'integer'),
			'org_name' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'name',
				'type' => 'string'),
			'org_active' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'active',
				'type' => 'string'),
			'org_parent' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'parent',
				'type' => 'integer'),
			'org_creaton' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'created_on',
				'type' => 'integer'),
			'org_creatby' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'created_by',
				'type' => 'integer'),
			'org_modon' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'modified_on',
				'type' => 'integer'),
			'org_modby' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'modified_by',
				'type' => 'integer'),
			'name' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => ''),
			'count_orgs' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => ''),
			'people_org' => array('select' => 'unlink_org'),
			'organizations' => array('select' => 'relink_org_person'),
			'orgs_local' => array('select' => 'relink_org_person'));

		function __construct( $ali = '', $field = '', $criteria = '' )
		{
			parent::__construct('phpgw_contact_org', 'contact_org');
			if ($field)
			{
				$this->add_select($field);
			}
			if ($criteria)
			{
				$this->add_criteria($criteria);
			}
			$this->set_ilinks('org_id', 'phpgwapi.contact_central', 'contact_id', PHPGW_SQL_REQUIRED_KEY);
			$this->set_ilinks('org_id', 'phpgwapi.contact_org_person', 'my_org_id', PHPGW_SQL_LAZY_KEY);
		}

		function unlink_org()
		{
			unset($this->ilink['phpgwapi.contact_central']);
		}

		function relink_org_person()
		{
			unset($this->ilink['phpgwapi.contact_org_person']);
			$this->set_elinks('org_id', 'phpgwapi.contact_org_person', 'my_org_id');
		}

		function criteria_org_id( $element )
		{
			$field = $this->put_alias($element['real_field']);
			if (is_array($element['value']))
			{
				$this->_add_criteria(phpgwapi_sql_criteria::in($field, $element['value']));
			}
			else
			{
				$this->_add_criteria(phpgwapi_sql_criteria::equal($field, $element['value']));
			}
		}

		function select_count_orgs()
		{
			$this->add_field('count_orgs', 'count(org_id)');
		}
	}