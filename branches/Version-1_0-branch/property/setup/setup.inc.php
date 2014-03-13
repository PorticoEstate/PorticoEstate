<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2012 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage setup
 	* @version $Id$
	*/

	$setup_info['property']['name']			= 'property';
	$setup_info['property']['version']		= '0.9.17.678';
	$setup_info['property']['app_order']	= 8;
	$setup_info['property']['enable']		= 1;
	$setup_info['property']['app_group']	= 'office';

	$setup_info['property']['author'] = array
	(
		'name'	=> 'Sigurd Nes',
		'email'	=> 'sigurdne@online.no'
	);

	$setup_info['property']['maintainer'] = array
	(
		'name'	=> 'Sigurd Nes',
		'email'	=> 'sigurdne@online.no'
	);

	$setup_info['property']['license']  = 'GPL';
	$setup_info['property']['description'] =
	'<div align="left">

	<b>FM</b> (Facilities-management) providing:
	<ol>
		<li>Helpdesk</li>
		<li>Property register - with drawing and document-archive(vfs)</li>
		<li>Equipment register</li>
		<li>Vendor register (using Addressbook)</li>
		<li>Tenant register</li>
		<li>Project/workorder by email/pdf with ability to save workorders as templates for later use</li>
			<ol>
			<li>From vendors prizebook - or</li>
			<li>from templates - or</li>
			<li> by custom definition as (optional) tender for bidding based on (for the moment) norwegian building standards.</li>
			</ol>
		<li>Service agreements with prizebook with historical prizing per vendor</li>
		<li>Invoice handling</li>
			<ol>
			<li>Import from file (currently Three different formats)</li>
            <li>Approval per Role</li>
				<ol>
				<li>Janitor</li>
            	<li>Supervisor</li>
            	<li>Budget responsible</li>
				</ol>
            <li>Export to payment system / accounting system</li>
			</ol>
	</ol>


	<b>Property</b> is organized as a set of submodules - each with theis own set of user permission-settings.
	<br>
	<b>Workorder</b> can be used as a general tool for producing tender for bidding - the document is presented as a pdf-document.
	</div>';

	$setup_info['property']['note'] =
		'I am also planning to add maintenance planning as events in the calendar app.';


	$setup_info['property']['tables'] = array(
		'fm_part_of_town',
		'fm_gab_location',
		'fm_streetaddress',
		'fm_tenant',
		'fm_tenant_category',
		'fm_vendor',
		'fm_vendor_category',
		'fm_district',
		'fm_locations',
		'fm_location1_category',
		'fm_location1',
		'fm_location1_history',
		'fm_location2_category',
		'fm_location2',
		'fm_location2_history',
		'fm_location3_category',
		'fm_location3',
		'fm_location3_history',
		'fm_location4_category',
		'fm_location4',
		'fm_location4_history',
		'fm_location_type',
		'fm_location_config',
		'fm_location_contact',
		'fm_building_part',
		'fm_b_account',
		'fm_b_account_category',
		'fm_workorder',
		'fm_workorder_budget',
		'fm_workorder_history',
		'fm_workorder_status',
		'fm_activities',
		'fm_agreement_group',
		'fm_agreement',
		'fm_agreement_status',
		'fm_activity_price_index',
		'fm_branch',
		'fm_wo_hours',
		'fm_wo_hours_category',
		'fm_wo_h_deviation',
		'fm_key_loc',
		'fm_authorities_demands',
		'fm_condition_survey_status',
		'fm_condition_survey_history',
		'fm_condition_survey',
		'fm_request',
		'fm_request_responsible_unit',
		'fm_request_condition_type',
		'fm_request_condition',
		'fm_request_status',
		'fm_request_history',
		'fm_request_consume',
		'fm_request_planning',
		'fm_template',
		'fm_template_hours',
		'fm_chapter',
		'fm_ns3420',
		'fm_project_status',
		'fm_project',
		'fm_project_buffer_budget',
		'fm_projectbranch',
		'fm_project_group',
		'fm_project_history',
		'fm_project_budget',
		'fm_tts_status',
		'fm_tts_priority',
		'fm_tts_tickets',
		'fm_tts_history',
		'fm_tts_views',
		'fm_department',
		'fm_ecoart',
		'fm_ecoavvik',
		'fm_ecobilag_process_code',
		'fm_ecobilag_process_log',
		'fm_ecobilag',
		'fm_ecobilagkilde',
		'fm_ecobilagoverf',
		'fm_ecobilag_category',
		'fm_ecodimb',
		'fm_ecodimb_role',
		'fm_ecodimb_role_user',
		'fm_ecodimd',
		'fm_ecologg',
		'fm_ecomva',
		'fm_ecouser',
		'fm_eco_periodization',
		'fm_eco_periodization_outline',
		'fm_eco_period_transition',
		'fm_event',
		'fm_event_action',
		'fm_event_exception',
		'fm_event_schedule',
		'fm_investment',
		'fm_investment_value',
		'fm_event_receipt',
		'fm_idgenerator',
		'fm_document',
		'fm_document_history',
		'fm_document_status',
		'fm_standard_unit',
		'fm_owner',
		'fm_owner_category',
		'fm_cache',
		'fm_entity',
		'fm_entity_category',
		'fm_entity_lookup',
		'fm_entity_history',
		'fm_custom',
		'fm_custom_cols',
		'fm_orders',
		'fm_order_dim1',
		'fm_order_template',
		'fm_response_template',
		'fm_s_agreement',
		'fm_s_agreement_budget',
		'fm_s_agreement_category',
		'fm_s_agreement_detail',
		'fm_s_agreement_pricing',
		'fm_s_agreement_history',
		'fm_async_method',
		'fm_cron_log',
		'fm_tenant_claim',
		'fm_tenant_claim_category',
		'fm_tenant_claim_history',
		'fm_budget_basis',
		'fm_budget',
		'fm_budget_period',
		'fm_budget_cost',
		'fm_responsibility',
		'fm_responsibility_role',
		'fm_responsibility_contact',
		'fm_responsibility_module',
		'fm_action_pending',
		'fm_action_pending_category',
		'fm_jasper',
		'fm_jasper_input_type',
		'fm_jasper_format_type',
		'fm_jasper_input',
		'fm_custom_menu_items',
		'fm_regulations'
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['property']['hooks'] = array
	(
		'manual',
		'settings',
		'help',
		'config',
		'menu'					=> 'property.menu.get_menu',
		'cat_add'				=> 'property.cat_hooks.cat_add',
		'cat_delete'			=> 'property.cat_hooks.cat_delete',
		'cat_edit'				=> 'property.cat_hooks.cat_edit',
		'home'					=> 'property.hook_helper.home_backend',
		'home_mobilefrontend'	=> 'property.hook_helper.home_mobilefrontend',
		'addaccount'			=> 'property.hook_helper.clear_userlist',
		'editaccount'			=> 'property.hook_helper.clear_userlist',
		'deleteaccount'			=> 'property.hook_helper.clear_userlist',
		'addgroup'				=> 'property.hook_helper.clear_userlist',
		'deletegroup'			=> 'property.hook_helper.clear_userlist',
		'editgroup'				=> 'property.hook_helper.clear_userlist',
		'registration'			=> 'property.hook_helper.add_location_contact'
	);

	/* Dependencies for this app to work */
	$setup_info['property']['depends'][] = array
	(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.17', '0.9.18')
	);

	$setup_info['property']['depends'][] = array
	(
		'appname'  => 'admin',
		'versions' => Array('0.9.17', '0.9.18')
	);

	$setup_info['property']['depends'][] = array
	(
		'appname'  => 'preferences',
		'versions' => Array('0.9.17', '0.9.18')
	);
