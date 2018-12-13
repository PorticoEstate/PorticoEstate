<?php
	/**
	 * Query statements for "addr" table
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
	 * Query statements for "addr" table
	 *
	 * @package phpgwapi
	 * @subpackage contacts
	 */
	class contact_addr extends phpgwapi_sql_entity
	{

		var $map = array('key_addr_id' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'contact_addr_id',
				'type' => 'integer'),
			'addr_contact_id' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'contact_id',
				'type' => 'integer'),
			'addr_type' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'addr_type_id',
				'type' => 'integer'),
			'addr_add1' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'add1',
				'type' => 'string'),
			'addr_add2' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'add2',
				'type' => 'string'),
			'addr_add3' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'add3',
				'type' => 'string'),
			'addr_city' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'city',
				'type' => 'string'),
			'addr_state' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'state',
				'type' => 'string'),
			'addr_postal_code' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'postal_code',
				'type' => 'string'),
			'addr_country' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'country',
				'type' => 'string'),
			'addr_preferred' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'preferred',
				'type' => 'string'),
			'addr_creaton' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'created_on',
				'type' => 'integer'),
			'addr_creatby' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'created_by',
				'type' => 'integer'),
			'addr_modon' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'modified_on',
				'type' => 'integer'),
			'addr_modby' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'modified_by',
				'type' => 'integer'),
			'addr_precedence' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'precedence'),
			'addr_address' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => ''),
			'adr_one_street' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'add1'),
			'adr_one_locality' => array('select' => 'select_primary',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'city'),
			'adr_one_region' => array('select' => 'select_primary',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'state'),
			'adr_one_postalcode' => array('select' => 'select_primary',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'postal_code'),
			'adr_one_countryname' => array('select' => 'select_primary',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'country'),
			'adr_two_street' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => ''),
			'adr_two_locality' => array('select' => 'select_secondary',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'city'),
			'adr_two_region' => array('select' => 'select_secondary',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'state'),
			'adr_two_postalcode' => array('select' => 'select_secondary',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'postal_code'),
			'adr_two_countryname' => array('select' => 'select_secondary',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'country'),
			'addr_pref_val' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'preferred'));
		var $primary_set;
		var $secondary_set;

		function __construct( $ali = '', $field = '', $criteria = '' )
		{
			parent::__construct('phpgw_contact_addr', 'contact_addr');
			if ($field)
			{
				$this->add_select($field);
			}
			if ($criteria)
			{
				$this->add_criteria($criteria);
			}
			$this->set_ilinks('addr_contact_id', 'phpgwapi.contact_central', 'contact_id');
			$this->set_elinks('addr_type', 'phpgwapi.contact_addr_type', 'addr_type_id');
		}

		/**
		 * Get address fields 1-3 from database
		 *
		 * @param mixed $element Unused
		 * @internal addr_address false field is a concatenation of add1 and add2 therefore, we use the || standard concatenation operator for select
		 */
		function select_addr_address( $element )
		{
			$this->field_list['addr_address'] = phpgwapi_sql::concat_null(array($this->put_alias('add1'),
					$this->put_alias('add2'), $this->put_alias('add3')));
		}

		/**
		 * Get address fields 1-3 for primary address from database
		 *
		 * @param mixed $element
		 * @internal addr_address false field is a concatenation of add1 and add2 therefore, we use the || standard concatenation operator for select
		 */
		function select_adr_one_street( $element )
		{
			$this->set_primary_address($element);
			$this->field_list['adr_one_street'] = phpgwapi_sql::concat_null(array($this->put_alias('add1'),
					$this->put_alias('add2'), $this->put_alias('add3')));
		}

		/**
		 * Write primary address to database
		 *
		 * @param mixed $element Unused
		 * @internal Generic select builder for adr_one fields that need to be limited to criteria precedence=1 but map 1 to 1 to real fields (city,country,postalcode)
		 */
		function select_primary( $element )
		{
			$this->set_primary_address($element);
			$this->field_list[$element['field']] = $this->put_alias($element['real_field']);
		}

		/**
		 * Get address fields 1-3 for secondary address from database
		 *
		 * @param mixed $element
		 * @internal Street address as asked for by the adr_*_street false fields are actually concatenations of add1 and add2, therefore that needs its own handler
		 */
		function select_adr_two_street( $element )
		{
			$this->set_secondary_address($element);
			$this->field_list['adr_one_street'] = phpgwapi_sql_criteria::concat_null(array(
					$this->put_alias('add1'), $this->put_alias('add2'), $this->put_alias('add3')));
		}

		/**
		 * Write secondary address to database
		 *
		 * @param mixed $element
		 * @internal Generic select builder for adr_two fields that need to be limited to criteria precedence=2 but map 1 to 1 to real fields (city,country,postalcode)
		 */
		function select_secondary( $element )
		{
			$this->set_secondary_address($element);
			$this->field_list[$element['field']] = $this->put_alias(phpgwapi_sql::string($element['real_field']));
		}

		/**
		 * Get address fields 1-2 for primary address from database
		 *
		 * @param mixed $element
		 * @internal Backwards compatibility for adr_one adr_two when asked to SEARCH CRITERIA (where clause)
		 * @internal In this case, searching in the adr_one_street false field should look for the value in add1 and add2 since adr_one_street is defined to be the concatenation of this two fields
		 */
		function criteria_adr_one_street( $element )
		{
			$this->set_primary_address($element);
			$criteria = phpgwapi_sql_criteria::or_(phpgwapi_sql_criteria::has($this->put_alias('add1'), $element['value']), phpgwapi_sql_criteria::has($this->put_alias('add2'), $element['value']));
			$this->_add_criteria($criteria);
		}

		/**
		 * Get primary address from database
		 *
		 * @param mixed $element
		 * @internal Generic criteria builder for adr_one type fields. They need an extra precedence and then are a LIKE clause which is given by phpgwapi_sql_criteria::has(). So, if the user asks for adr_one_country, the resulting query will be tablename.country = %%value%%
		 */
		function criteria_primary( $element )
		{
			$this->set_primary_address($element);
			$criteria = phpgwapi_sql_criteria::has($this->put_alias($element['real_field']), phpgwapi_sql::string($element['value']));
			$this->add_criteria($criteria);
		}

		/**
		 * Get address fields 1-2 for secondary address from database
		 *
		 * @param mixed $element
		 * @internal Same as criteria_adr_one_street but with precedence 2
		 */
		function criteria_adr_two_street( $element )
		{
			$this->set_secondary_address($element);
			$criteria = phpgwapi_sql_criteria::or_(phpgwapi_sql_criteria::has($this->put_alias('add1'), phpgwapi_sql::string($element['value'])), phpgwapi_sql_criteria::has($this->put_alias('add2'), phpgwapi_sql::string($element['value'])));
			$this->_add_criteria($criteria);
		}

		function criteria_secondary( $element )
		{
			$this->set_secondary_address($element);
			$criteria = phpgwapi_sql_criteria::has($this->put_alias($element['real_field']), phpgwapi_sql::string($element['value']));
			$this->_add_criteria($criteria);
		}

		function set_primary_address( $element )
		{
			if ($this->primary_set != 'primary')
			{
				$criteria = phpgwapi_sql_criteria::equal($this->put_alias('precedence'), '1');
				$this->_add_criteria($criteria);
				$this->primary_set = 'primary';
			}
		}

		function set_secondary_address( $element )
		{
			if ($this->secondary_set != 'secondary')
			{
				$criteria = phpgwapi_sql_criteria::equal($this->put_alias('precedence'), '2');
				$this->_add_criteria($criteria);
				$this->secondary_set = 'secondary';
			}
		}

		function criteria_addr_pref_val( $element )
		{
			$field = $this->put_alias($element['real_field']);
			$criteria = phpgwapi_sql_criteria::or_(phpgwapi_sql_criteria::equal($field, phpgwapi_sql::string($element['value'])), phpgwapi_sql_criteria::is_null($field));
			$this->_add_criteria($criteria);
			return $criteria;
		}

		function criteria_addr_contact_id( $element )
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
	}