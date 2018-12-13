<?php
	/**
	 * Query statements for "categories" table
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
	 * Query statements for "categories" table
	 *
	 * @package phpgwapi
	 * @subpackage contacts
	 */
	class contact_categories extends phpgwapi_sql_entity
	{

		var $map = array('key_cat_id' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'cat_id',
				'type' => 'integer'),
			'parent_id' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'cat_id',
				'type' => 'integer'));

		function __construct( $ali = '', $field = '', $criteria = '' )
		{
			parent::__construct('phpgw_categories', 'contact_categories');
			if ($field)
			{
				$this->add_select($field);
			}
			if ($criteria)
			{
				$this->add_criteria($criteria);
			}
			//$this->set_elinks('key_cat_id', 'contact_central','cat_id');
		}

		function select_parent_id( $element )
		{
			$this->set_alias('categorie_parent');
			$this->_add_field(array('field' => 'parent_id', 'real_field' => 'cat_id'));
			$this->set_elinks('key_cat_id', 'phpgwapi.contact_categories', 'parent_id');
		}
	}