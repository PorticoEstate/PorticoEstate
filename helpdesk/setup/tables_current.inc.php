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
				'reverse_id' => array('type' => 'int','precision' => '4','nullable' => True),
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
				'publish_note'=> array('type' => 'int','precision' => 2,'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => '8', 'nullable' => True),
				'external_ticket_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'external_origin_email' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
				'on_behalf_of_name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
			),
			'pk' => array('id'),
			'ix' => array('location_code'),
			'fk' => array(
				'phpgw_categories' => array('cat_id' => 'cat_id')
			),
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
		),
		'phpgw_helpdesk_response_template' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
				'content' => array('type' => 'text', 'nullable' => True),
				'public' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
				'modified_date' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_helpdesk_custom_menu_items' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'parent_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'text' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
				'url' => array('type' => 'text', 'nullable' => True),
				'target' => array('type' => 'varchar', 'precision' => '15', 'nullable' => True),
				'location' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
				'local_files' => array('type' => 'int', 'precision' => 2, 'nullable' => true),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
				'modified_date' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_helpdesk_email_out' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
				'remark' => array('type' => 'text', 'nullable' => True),
				'subject' => array('type' => 'text', 'nullable' => false),
				'content' => array('type' => 'text', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'created' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
				'modified' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_helpdesk_email_out_recipient_set' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
				'active' => array('type' => 'int', 'precision' => 2, 'nullable' => True, 'default' => '0'),
				'public' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'created' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
				'modified' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_helpdesk_email_out_recipient_list' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'set_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'alias' => array('type' => 'varchar', 'precision' => 25, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
				'email' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
				'office' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
				'department' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
				'alias_supervisor' => array('type' => 'varchar', 'precision' => 25, 'nullable' => True),
				'name_supervisor' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
				'email_supervisor' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
				'active' => array('type' => 'int', 'precision' => 2, 'nullable' => True, 'default' => '0'),
				'public' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'created' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
				'modified' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
			),
			'pk' => array('id'),
			'fk' => array(
				'phpgw_helpdesk_email_out_recipient_set' => array('set_id' => 'id'),
			),
			'ix' => array(),
			'uc' => array('set_id', 'email')
		),
		'phpgw_helpdesk_email_out_recipient' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'email_out_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'recipient_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'status' => array('type' => 'int', 'precision' => 2, 'nullable' => True, 'default' => '0'),
			),
			'pk' => array('id'),
			'fk' => array(
				'phpgw_helpdesk_email_out' => array('email_out_id' => 'id'),
				'phpgw_helpdesk_email_out_recipient_list' => array('recipient_id' => 'id'),
			),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_helpdesk_email_template' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
				'content' => array('type' => 'text', 'nullable' => True),
				'public' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
				'modified_date' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_helpdesk_external_communication' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'ticket_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'order_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'type_id' => array('type' => 'int', 'precision' => 2, 'nullable' => False),
				'vendor_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'subject' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
				'mail_recipients' => array('type' => 'text', 'nullable' => True),
				'file_attachments' => array('type' => 'text', 'nullable' => True),
				'deadline' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'deadline2' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'created_on' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
				'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'modified_date' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(
				'phpgw_helpdesk_tickets' => array('ticket_id' => 'id')
				),
			'uc' => array()
		),
		'phpgw_helpdesk_external_communication_type' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 100, 'nullable' => true),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_helpdesk_external_communication_msg' =>  array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'excom_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'message' => array('type' => 'text', 'nullable' => False),
				'timestamp_sent' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'mail_recipients' => array('type' => 'text', 'nullable' => True),
				'file_attachments' => array('type' => 'text', 'nullable' => True),
				'sender_email_address' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
				'created_on' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
				'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(
				'phpgw_helpdesk_external_communication' => array('excom_id' => 'id')
				),
			'uc' => array()
		),
		'phpgw_helpdesk_cat_assignment' => array(
			'fd' => array(
				'cat_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'group_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'created_on' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
				'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
			),
			'pk' => array('cat_id', 'group_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_helpdesk_cat_respond_messages' => array(
			'fd' => array(
				'cat_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'include_content' => array('type' => 'int', 'precision' => 2, 'nullable' => true),
				'new_message' => array('type' => 'text', 'nullable' => true),
				'set_user_message' => array('type' => 'text', 'nullable' => true),
				'update_message' => array('type' => 'text', 'nullable' => true),
				'close_message' => array('type' => 'text', 'nullable' => true),
				'created_on' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
				'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
			),
			'pk' => array('cat_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_helpdesk_cat_anonyminizer' => array(
			'fd' => array(
				'cat_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'active' => array('type' => 'int', 'precision' => 2, 'nullable' => true),
				'limit_days' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'created_on' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
				'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
			),
			'pk' => array('cat_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

	);
