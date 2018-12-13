<?php
	/**
	 * Query statements for "note_type" table
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
	 * Query statements for "note_type" table
	 *
	 * @package phpgwapi
	 * @subpackage contacts
	 */
	class contact_note_type extends phpgwapi_sql_entity
	{

		var $map = array('note_type_id' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => '',
				'type' => 'integer'),
			'note_description' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'description',
				'type' => 'string'));

		function __construct( $ali = '', $field = '', $criteria = '' )
		{
			parent::__construct('phpgw_contact_note_type', 'contact_note_type');
			if ($field)
			{
				$this->add_select($field);
			}
			if ($criteria)
			{
				$this->add_criteria($criteria);
			}
			$this->set_ilinks('note_type_id', 'phpgwapi.contact_note', 'note_type');
		}
	}