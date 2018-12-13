<?php
	/**
	 * Query statements for "comm" table
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
	 * Query statements for "comm" table
	 *
	 * @package phpgwapi
	 * @subpackage contacts
	 */
	class contact_comm extends phpgwapi_sql_entity
	{

		var $map = array('key_comm_id' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'type' => 'integer',
				'field' => 'comm_id',
				'type' => 'integer'),
			'comm_contact_id' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'type' => 'integer',
				'field' => 'contact_id',
				'type' => 'integer'),
			'comm_descr' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'comm_descr_id',
				'type' => 'integer'),
			'comm_preferred' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'preferred',
				'type' => 'string'),
			'comm_data' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => '',
				'type' => 'string'),
			'comm_creaton' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'created_on',
				'type' => 'integer'),
			'comm_creatby' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'created_by',
				'type' => 'integer'),
			'comm_modon' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'modified_on',
				'type' => 'integer'),
			'comm_modby' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'modified_by',
				'type' => 'integer'),
			'phone' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'comm_data'),
			'phone_home' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'comm_data'),
			'phone_prefer' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'comm_data'),
			'email' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'comm_data'),
			'email_home' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'comm_data'),
			'tel_work' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'comm_data'),
			'tel_home' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'comm_data'),
			'tel_voice' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'comm_data'),
			'tel_fax' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'comm_data'),
			'tel_msg' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'comm_data'),
			'tel_cell' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'comm_data'),
			'tel_pager' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'comm_data'),
			'tel_bbs' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'comm_data'),
			'tel_modem' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'comm_data'),
			'tel_car' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'comm_data'),
			'tel_isdn' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'comm_data'),
			'tel_video' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'comm_data'),
			'tel_prefer' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'comm_data'),
			'url' => array('select' => '',
				'criteria' => '',
				'insert' => '',
				'update' => '',
				'delete' => '',
				'sort' => '',
				'field' => 'comm_data'));

		function __construct( $ali = '', $field = '', $criteria = '' )
		{
			parent::__construct('phpgw_contact_comm', 'contact_comm');
			if ($field)
			{
				$this->add_select($field);
			}
			if ($criteria)
			{
				$this->add_criteria($criteria);
			}
			$this->set_ilinks('comm_contact_id', 'phpgwapi.contact_central', 'contact_id');
			$this->set_elinks('comm_descr', 'phpgwapi.contact_comm_descr', 'comm_descr_id');
		}

		function criteria_key_comm_id( $element )
		{
			$this->_add_criteria($this->index_criteria($element));
		}

		function criteria_comm_contact_id( $element )
		{
			$this->_add_criteria($this->index_criteria($element));
		}

		function criteria_comm_descr( $element )
		{
			$this->_add_criteria($this->index_criteria($element));
		}
	}