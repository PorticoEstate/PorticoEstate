<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage admin
 	* @version $Id: hook_admin.inc.php,v 1.28 2007/01/26 14:53:47 sigurdne Exp $
	*/

		{
			$file = array
			(
				'Configuration'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'property') ),
				'Street'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'street') ),
				'District'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'district') ),
				'Part of town'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uip_of_town.index') ),
				'Admin entity'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin_entity.index') ),
				'Admin Location'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin_location.index') ),
				'Update the not active category for locations'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uilocation.update_cat') ),
				'Request Categories'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'request') ),
				'Workorder Categories'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'wo') ),
				'Workorder Detail Categories'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'wo_hours') ),
				'Ticket Categories'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'ticket') ),
				'Tenant Claim Categories'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'tenant_claim') ),
				'Tenant Categories'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'tenant') ),
				'Tenant Global Categories'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'fm_tenant', 'global_cats' => 'True') ),
				'Tenant Attributes'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiactor.list_attribute', 'role' => 'tenant') ),
				'Tenant'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiactor.index', 'role' => 'tenant') ),
				'Owner'						=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiactor.index', 'role' => 'owner') ),
				'Owner Categories'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'owner') ),
				'Owner Attributes'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiactor.list_attribute', 'role' => 'owner') ),
				'Vendor'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiactor.index', 'role' => 'vendor') ),
				'Vendor Categories'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'vendor') ),
				'Vendor Global Categories'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'fm_vendor', 'global_cats' => 'True') ),
				'Vendor Attributes'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiactor.list_attribute', 'role' => 'vendor') ),
				'Document Categories'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'document') ),
				'Building Part'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uistandard_2.index', 'type' => 'building_part') ),
				'Tender chapter'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'tender_chapter') ),
				'ID Control'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin.edit_id') ),
				'Permissions'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin.list_acl') ),
				'User contact info'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin.contact_info') ),
				'Request status'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uistandard_2.index', 'type' => 'request_status') ),
				'Request condition_type'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'r_condition_type') ),
				'Workorders status'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uistandard_2.index', 'type' => 'workorder_status') ),
				'Agreement status'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uistandard_2.index', 'type' => 'agreement_status') ),
				'Agreement Attributes'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute', 'appname' => $appname, 'location' =>'.agreement')),
				'service agreement categories'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 's_agreement') ),
		//		'service agreement Attributes'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uis_agreement.list_attribute') ),
				'service agreement Attributes'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute', 'appname' => $appname, 'location' =>'.s_agreement')),
		//		'service agreement item Attributes'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uis_agreement.list_attribute', 'role' => 'detail') ),
				'service agreement item Attributes'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute', 'appname' => $appname, 'location' =>'.s_agreement.detail')),
				'rental agreement categories'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'r_agreement') ),
				'rental agreement Attributes'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uir_agreement.list_attribute') ),
				'rental agreement item Attributes'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uir_agreement.list_attribute', 'role' => 'detail') ),
				'Document Status'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uistandard_2.index', 'type' => 'document_status') ),
				'Unit'						=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uistandard_2.index', 'type' => 'unit') ),
				'Key location'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uistandard_3.index', 'type' => 'key_location') ),
				'Branch'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uistandard_3.index', 'type' => 'branch') ),
				'Accounting'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uib_account.index') ),
				'Accounting Categories'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'b_account') ),
				'Accounting dim b'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'dim_b') ),
				'Accounting dim d'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'dim_d') ),
				'Accounting tax'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'tax') ),
				'Accounting voucher category'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'voucher_cat') ),
				'Accounting voucher type'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'voucher_type') ),

				'Import'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiXport.import') ),
				'Export'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiXport.export') ),
				'Admin Async servises'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uialarm.index') ),
				'Async servises'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiasync.index') ),
				'Admin custom functions'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin_custom.index') ),
	);
	$GLOBALS['phpgw']->common->display_mainscreen($appname,$file);

		}
?>
