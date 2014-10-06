<?php
	/**
	* phpGroupWare - helpdesk
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package helpdesk
	* @subpackage setup
 	* @version $Id: tables_current.inc.php 6711 2010-12-28 15:15:42Z sigurdne $
	*/

	$phpgw_baseline = array(
		'phpgw_helpdesk_status' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'color' => array('type' => 'varchar','precision' => '10','nullable' => True),
				'closed' => array('type' => 'int','precision' => '2','nullable' => True),
				'approved' => array('type' => 'int','precision' => '2','nullable' => True),
				'in_progress' => array('type' => 'int','precision' => '2','nullable' => True),
				'delivered' => array('type' => 'int','precision' => '2','nullable' => True),
				'sorting' => array('type' => 'int','precision' => '4','nullable' => True)
			),
			'pk' => array('id'),
			'ix' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_helpdesk_tickets' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'group_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'priority' => array('type' => 'int','precision' => '2','nullable' => False),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'assignedto' => array('type' => 'int','precision' => '4','nullable' => True),
				'subject' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'cat_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'billable_hours' => array('type' => 'decimal','precision' => '8','scale' => '2','nullable' => True),
				'billable_rate' => array('type' => 'decimal','precision' => '8','scale' => '2','nullable' => True),
				'status' => array('type' => 'varchar','precision' => '2','nullable' => False),
				'details' => array('type' => 'text','nullable' => False),
				'location_code' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'p_num' => array('type' => 'varchar','precision' => '15','nullable' => True),
				'p_entity_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'p_cat_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'loc1' => array('type' => 'varchar','precision' => '6','nullable' => True),
				'loc2' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'loc3' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'loc4' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'floor' => array('type' => 'varchar','precision' => '6','nullable' => True),
				'address' => array('type' => 'varchar','precision' => '150','nullable' => True),
				'contact_phone' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'contact_email' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'tenant_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'finnish_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'finnish_date2' => array('type' => 'int','precision' => '4','nullable' => True),
				'contact_id' => array('type' => 'int','precision' => 4,'nullable' => True),
				'order_id' => array('type' => 'int','precision' => 8,'nullable' => True),
				'vendor_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'order_descr' => array('type' => 'text','nullable' => True),
				'b_account_id' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'ecodimb' => array('type' => 'int','precision' => 4,'nullable' => True),
				'budget' => array('type' => 'int','precision' => '4','nullable' => True),
				'actual_cost' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'),
				'order_cat_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'building_part'=> array('type' => 'varchar','precision' => 4,'nullable' => True),
				'order_dim1'=> array('type' => 'int','precision' => 4,'nullable' => True),
				'publish_note'=> array('type' => 'int','precision' => 2,'nullable' => True)
			),
			'pk' => array('id'),
			'ix' => array(),
			'ix' => array('location_code'),
			'uc' => array()
		),
		'phpgw_helpdesk_views' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'account_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'time' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array(),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		)
	);
