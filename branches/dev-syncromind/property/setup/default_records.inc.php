<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage setup
 	* @version $Id$
	*/


	/**
	 * Description
	 * @package property
	 */

//$app_id = $GLOBALS['phpgw']->applications->name2id('property');

$GLOBALS['phpgw_setup']->oProc->query("SELECT app_id FROM phpgw_applications WHERE app_name = 'property'");
$GLOBALS['phpgw_setup']->oProc->next_record();
$app_id = $GLOBALS['phpgw_setup']->oProc->f('app_id');

#
#  phpgw_locations
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant) VALUES ({$app_id}, '.', 'Top', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.admin', 'Admin')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.admin.entity', 'Admin entity')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.admin.location', 'Admin location')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.location', 'Location')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.location.1', 'Property')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.location.2', 'Building')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.location.3', 'Entrance')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.location.4', 'Apartment')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.custom', 'custom queries')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant, allow_c_function, allow_c_attrib, c_attrib_table) VALUES ({$app_id}, '.project', 'Demand -> Workorder', 1, 1, 1, 'fm_project')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant, allow_c_function, allow_c_attrib, c_attrib_table) VALUES ({$app_id}, '.project.workorder', 'Workorder', 1, 1 ,1, 'fm_workorder')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant, allow_c_function, allow_c_attrib, c_attrib_table) VALUES ({$app_id}, '.project.request', 'Request', 1, 1 ,1, 'fm_request')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant, allow_c_function, allow_c_attrib, c_attrib_table) VALUES ({$app_id}, '.ticket', 'Helpdesk', 1, 1, 1, 'fm_tts_tickets')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.ticket.external', 'Helpdesk External user')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.ticket.order', 'Helpdesk ad hock order')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.invoice', 'Invoice')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.document', 'Documents')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.drawing', 'Drawing')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant) VALUES ({$app_id}, '.entity.1', 'Equipment', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant, allow_c_function, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.entity.1.1', 'Meter', 1, 1, 1, 'fm_entity_1_1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant, allow_c_function, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.entity.1.2', 'Elevator', 1, 1, 1, 'fm_entity_1_2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant, allow_c_function, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.entity.1.3', 'Fire alarm central', 1, 1, 1, 'fm_entity_1_3')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant) VALUES ({$app_id}, '.entity.2', 'Report', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant, allow_c_function, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.entity.2.1', 'Report type 1', 1, 1, 1, 'fm_entity_2_1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant, allow_c_function, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.entity.2.2', 'Report type 2', 1, 1, 1, 'fm_entity_2_2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.b_account', 'Budget account')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.tenant_claim', 'Tenant claim')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.budget', 'Budet')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.budget.obligations', 'Obligations')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.budget.basis', 'Basis for high level lazy budgeting')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.ifc', 'ifc integration')");

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.agreement', 'Agreement',1,'fm_agreement')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.s_agreement', 'Service agreement',1,'fm_s_agreement')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.s_agreement.detail', 'Service agreement detail',1,'fm_s_agreement_detail')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.r_agreement', 'Rental agreement',1,'fm_r_agreement')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.r_agreement.detail', 'Rental agreement detail',1,'fm_r_agreement_detail')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.tenant', 'Tenant',1,1,'fm_tenant')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.owner', 'Owner',1,1,'fm_owner')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.vendor', 'Vendor',1,1,'fm_vendor')");

$GLOBALS['phpgw']->locations->add('.jasper', 'JasperReport', 'property', $allow_grant = true);

$GLOBALS['phpgw']->locations->add('.invoice.dimb', 'A dimension for accounting', 'property');
$GLOBALS['phpgw']->locations->add('.scheduled_events', 'Scheduled events', 'property');
$GLOBALS['phpgw']->locations->add('.project.condition_survey', 'Condition Survey', 'property', true, 'fm_condition_survey', true);

$locations = array
(
	'property.ticket'	=> '.ticket',
	'property.project'	=> '.project',
	'property.document' => '.document',
	'fm_vendor'			=> '.vendor',
	'fm_tenant'			=> '.tenant',
	'fm_owner'			=> '.owner'
);

foreach($locations as $dummy => $location)
{
	$GLOBALS['phpgw']->locations->add("{$location}.category", 'Categories', 'property');
}


$GLOBALS['phpgw_setup']->oProc->query("DELETE from phpgw_config WHERE config_app='property'");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_config (config_app, config_name, config_value) VALUES ('property','meter_table', 'fm_entity_1_1')");

#
#fm_district
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_district (id, descr) VALUES ('1', 'District 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_district (id, descr) VALUES ('2', 'District 2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_district (id, descr) VALUES ('3', 'District 3')");

#
#fm_part_of_town
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_part_of_town (name, district_id) VALUES ('Part of town 1','1')");


#
#fm_owner_category
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_owner_category (id, descr) VALUES ('1', 'Owner category 1')");

#
#fm_owner
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_owner (id, abid, org_name, category) VALUES (1, 1, 'demo-owner 1',1)");



#
#fm_owner_attribute
#
$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.owner');

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, list, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable, search) VALUES ($location_id, 1, 1, 'abid', 'Contact', 'Contakt person', NULL, 'AB', 1, 4, NULL, NULL, 'True', NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, list, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable, search) VALUES ($location_id, 2, 1, 'org_name', 'Name', 'The name of the owner', NULL, 'V', 2, 50, NULL, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, list, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable, search) VALUES ($location_id, 3, 1, 'remark', 'remark', 'remark', NULL, 'T', 3, NULL, NULL, NULL, 'True', NULL)");

#
# Dumping data for table fm_location1_category
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location1_category (id, descr) VALUES (1, 'SOMETHING')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location1_category (id, descr) VALUES (99, 'not active')");
#
# Dumping data for table fm_location2_category
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location2_category (id, descr) VALUES (1, 'SOMETHING')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location2_category (id, descr) VALUES (99, 'not active')");
#
# Dumping data for table fm_location3_category
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location3_category (id, descr) VALUES (1, 'SOMETHING')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location3_category (id, descr) VALUES (99, 'not active')");
#
# Dumping data for table fm_location4_category
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location4_category (id, descr) VALUES (1, 'SOMETHING')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location4_category (id, descr) VALUES (99, 'not active')");


#
#fm_location1
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location1 ( location_code , loc1 , loc1_name , part_of_town_id , entry_date , category ,status, user_id , owner_id , remark )VALUES ('5000', '5000', 'Location name', '1', NULL , '1','1', '6', '1', 'remark')");

#
#fm_location2
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location2 ( location_code , loc1 , loc2 , loc2_name , entry_date , category, status, user_id , remark )VALUES ('5000-01', '5000', '01', 'Location name', NULL , '1','1', '6', 'remark')");


$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_streetaddress (id, descr) VALUES (1, 'street name 1')");

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location3 (location_code, loc1, loc2, loc3, loc3_name, entry_date, category, user_id, status, remark) VALUES ('5000-01-01', '5000', '01', '01', 'entrance name1', 1087745654, 1, 6, 1, NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location3 (location_code, loc1, loc2, loc3, loc3_name, entry_date, category, user_id, status, remark) VALUES ('5000-01-02', '5000', '01', '02', 'entrance name2', 1087745654, 1, 6, 1, NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location3 (location_code, loc1, loc2, loc3, loc3_name, entry_date, category, user_id, status, remark) VALUES ('5000-01-03', '5000', '01', '03', 'entrance name3', 1087745654, 1, 6, 1, NULL)");

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location4 (location_code, loc1, loc2, loc3, loc4, loc4_name, entry_date, category, street_id, street_number, user_id, tenant_id, status, remark) VALUES ('5000-01-01-001', '5000', '01', '01', '001', 'apartment name1', 1087745753, 1, 1, '1A', 6, 1, 1, NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location4 (location_code, loc1, loc2, loc3, loc4, loc4_name, entry_date, category, street_id, street_number, user_id, tenant_id, status, remark) VALUES ('5000-01-01-002', '5000', '01', '01', '002', 'apartment name2', 1087745753, 1, 1, '1B', 6, 2, 1, NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location4 (location_code, loc1, loc2, loc3, loc4, loc4_name, entry_date, category, street_id, street_number, user_id, tenant_id, status, remark) VALUES ('5000-01-02-001', '5000', '01', '02', '001', 'apartment name3', 1087745753, 1, 1, '2A', 6, 3, 1, NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location4 (location_code, loc1, loc2, loc3, loc4, loc4_name, entry_date, category, street_id, street_number, user_id, tenant_id, status, remark) VALUES ('5000-01-02-002', '5000', '01', '02', '002', 'apartment name4', 1087745753, 1, 1, '2B', 6, 4, 1, NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location4 (location_code, loc1, loc2, loc3, loc4, loc4_name, entry_date, category, street_id, street_number, user_id, tenant_id, status, remark) VALUES ('5000-01-03-001', '5000', '01', '03', '001', 'apartment name5', 1087745753, 1, 1, '3A', 6, 5, 1, NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location4 (location_code, loc1, loc2, loc3, loc4, loc4_name, entry_date, category, street_id, street_number, user_id, tenant_id, status, remark) VALUES ('5000-01-03-002', '5000', '01', '03', '002', 'apartment name6', 1087745753, 1, 1, '3B', 6, 6, 1, NULL)");

#
# fm_branch
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_branch (id, num, descr) VALUES (1, 'rør', 'rørlegger')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_branch (id, num, descr) VALUES (2, 'maler', 'maler')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_branch (id, num, descr) VALUES (3, 'tomrer', 'Tømrer')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_branch (id, num, descr) VALUES (4, 'renhold', 'Renhold')");

#
# fm_workorder_status
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_workorder_status (id, descr) VALUES ('active', 'Active')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_workorder_status (id, descr) VALUES ('ordered', 'Ordered')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_workorder_status (id, descr) VALUES ('request', 'Request')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_workorder_status (id, descr) VALUES ('closed', 'Closed')");

#
# fm_request_status
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_status (id, descr) VALUES ('request', 'Request')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_status (id, descr) VALUES ('canceled', 'Canceled')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_status (id, descr) VALUES ('closed', 'avsluttet')");


#
# fm_request_condition_type
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition_type (id, name, priority_key) VALUES (1, 'safety', 10)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition_type (id, name, priority_key) VALUES (2, 'aesthetics', 2)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition_type (id, name, priority_key) VALUES (3, 'indoor climate', 5)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition_type (id, name, priority_key) VALUES (4, 'consequential damage', 5)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition_type (id, name, priority_key) VALUES (5, 'user gratification', 4)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition_type (id, name, priority_key) VALUES (6, 'residential environment', 6)");


#
# fm_document_category
#

$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_categories WHERE cat_appname = 'property'");
$GLOBALS['phpgw_info']['server']['account_repository'] = isset($GLOBALS['phpgw_info']['server']['account_repository']) ? $GLOBALS['phpgw_info']['server']['account_repository'] : '';
$GLOBALS['phpgw']->accounts		= createObject('phpgwapi.accounts');
$GLOBALS['phpgw']->db = & $GLOBALS['phpgw_setup']->oProc->m_odb;
$GLOBALS['phpgw']->acl = CreateObject('phpgwapi.acl');
$GLOBALS['phpgw']->hooks = CreateObject('phpgwapi.hooks', $GLOBALS['phpgw_setup']->oProc->m_odb);
$cats = CreateObject('phpgwapi.categories', -1, 'property','.document');

$cats->add(	array
	(
		'name'	=> 'Picture',
		'descr'	=> 'Picture',
		'parent' => 'none',
		'old_parent' => 0,
		'access' => 'public'
	)
);

$cats->add(	array
	(
		'name'	=> 'Report',
		'descr'	=> 'Report',
		'parent' => 'none',
		'old_parent' => 0,
		'access' => 'public'
	)
);

$cats->add(	array
	(
		'name'	=> 'Instruction',
		'descr'	=> 'Instruction',
		'parent' => 'none',
		'old_parent' => 0,
		'access' => 'public'
	)
);


#
# fm_document_status
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_document_status (id, descr) VALUES ('draft', 'Draft')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_document_status (id, descr) VALUES ('final', 'Final')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_document_status (id, descr) VALUES ('obsolete', 'obsolete')");


#
# fm_standard_unit
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_standard_unit (id, name, descr) VALUES (1, 'mm', 'Millimeter')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_standard_unit (id, name, descr) VALUES (2, 'm', 'Meter')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_standard_unit (id, name, descr) VALUES (3, 'm2', 'Square meters')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_standard_unit (id, name, descr) VALUES (4, 'm3', 'Cubic meters')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_standard_unit (id, name, descr) VALUES (5, 'km', 'Kilometre')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_standard_unit (id, name, descr) VALUES (6, 'Stk', 'Stk')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_standard_unit (id, name, descr) VALUES (7, 'kg', 'Kilogram')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_standard_unit (id, name, descr) VALUES (8, 'tonn', 'Tonn')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_standard_unit (id, name, descr) VALUES (9, 'h', 'Hours')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_standard_unit (id, name, descr) VALUES (10, 'RS', 'Round Sum')");


#
#  fm_agreement_status
#
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_agreement_status (id, descr) VALUES ('closed', 'Closed')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_agreement_status (id, descr) VALUES ('active', 'Active agreement')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_agreement_status (id, descr) VALUES ('planning', 'Planning')");


#
#  fm_ns3420
#
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_ns3420 (id, tekst1, enhet) VALUES ('D00', 'RIGGING, KLARGJØRING', 'RS')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_ns3420 (id, tekst1, enhet,tekst2) VALUES ('D20', 'RIGGING, ANLEGGSTOMT', 'RS','TILFØRSEL- OG FORSYNINGSANLEGG')");

#
# Data-ark for tabell fm_idgenerator
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_idgenerator (name, value, descr) VALUES ('Bilagsnummer', '2003100000', 'Bilagsnummer')",__LINE__,__FILE__);
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_idgenerator (name, value, descr) VALUES ('bilagsnr_ut', 0, 'Bilagsnummer utgående')",__LINE__,__FILE__);
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_idgenerator (name, value, descr) VALUES ('Ecobatchid', '1', 'Ecobatchid')",__LINE__,__FILE__);
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_idgenerator (name, value, descr) VALUES ('project', '1000', 'project')",__LINE__,__FILE__);
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_idgenerator (name, value, descr) VALUES ('Statuslog', '1', 'Statuslog')",__LINE__,__FILE__);
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_idgenerator (name, value, descr) VALUES ('workorder', '1000', 'workorder')",__LINE__,__FILE__);
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_idgenerator (name, value, descr) VALUES ('request', '1000', 'request')",__LINE__,__FILE__);

#
# Dumping data for table fm_location_config
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location_config (location_type, column_name, input_text, lookup_form, f_key, ref_to_category, query_value, reference_table, reference_id, datatype, precision_, scale, default_value, nullable) VALUES (4, 'tenant_id', NULL, 1, 1, NULL, 0, 'fm_tenant', 'id', 'int', 4, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location_config (location_type, column_name, input_text, lookup_form, f_key, ref_to_category, query_value, reference_table, reference_id, datatype, precision_, scale, default_value, nullable) VALUES (4, 'street_id', NULL, 1, 1, NULL, 1, 'fm_streetaddress', 'id', 'int', 4, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location_config (location_type, column_name, input_text, lookup_form, f_key, ref_to_category, query_value, reference_table, reference_id, datatype, precision_, scale, default_value, nullable) VALUES (1, 'owner_id', NULL, NULL, 1, 1, NULL, 'fm_owner', 'id', 'int', 4, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location_config (location_type, column_name, input_text, lookup_form, f_key, ref_to_category, query_value, reference_table, reference_id, datatype, precision_, scale, default_value, nullable) VALUES (1, 'part_of_town_id', NULL, NULL, 1, NULL, NULL, 'fm_part_of_town', 'part_of_town_id', 'int', 4, NULL, NULL, 'True')");

#
# Dumping data for table fm_tenant_category
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tenant_category (id, descr) VALUES (1, 'male')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tenant_category (id, descr) VALUES (2, 'female')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tenant_category (id, descr) VALUES (3, 'organization')");

#
# Dumping data for table phpgw_cust_attribute
#
$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.tenant');

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, list, search, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable) VALUES ($location_id, 1, 1, 1, 'first_name', 'First name', 'First name', NULL, 'V', 1, 50, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, list, search, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable) VALUES ($location_id, 2, 1, 1, 'last_name', 'Last name', 'Last name', NULL, 'V', 2, 50, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, list, search, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable) VALUES ($location_id, 3, 1, 1, 'contact_phone', 'contact phone', 'contact phone', NULL, 'V', 3, 20, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, list, search, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable) VALUES ($location_id, 4, NULL, NULL, 'phpgw_account_id', 'Mapped User', 'Mapped User', NULL, 'user', 4, 4, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, list, search, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable) VALUES ($location_id, 5, NULL, NULL, 'account_lid', 'User Name', 'User name for login', NULL, 'V', 5, 25, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, list, search, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable) VALUES ($location_id, 6, NULL, NULL, 'account_pwd', 'Password', 'Users Password', NULL, 'pwd', 6, 32, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, list, search, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable) VALUES ($location_id, 7, NULL, NULL, 'account_status', 'account status', 'account status', NULL, 'LB', 7, NULL, NULL, NULL, 'True')");

#
# Dumping data for table fm_tenant_choice
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 7, 1, 'Active')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 7, 2, 'Banned')");

#
# Dumping data for table fm_tenant
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tenant (id, first_name, last_name, category) VALUES (1, 'First name1', 'Last name1', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tenant (id, first_name, last_name, category) VALUES (2, 'First name2', 'Last name2', 2)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tenant (id, first_name, last_name, category) VALUES (3, 'First name3', 'Last name3', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tenant (id, first_name, last_name, category) VALUES (4, 'First name4', 'Last name4', 2)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tenant (id, first_name, last_name, category) VALUES (5, 'First name5', 'Last name5', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tenant (id, first_name, last_name, category) VALUES (6, 'First name6', 'Last name6', 2)");

#
# Dumping data for table fm_ecoart
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_ecoart (id, descr) VALUES (1, 'faktura')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_ecoart (id, descr) VALUES (2, 'kreditnota')");


#
# Dumping data for table fm_ecobilag_category
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_ecobilag_category (id, descr) VALUES (1, 'Drift, vedlikehold')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_ecobilag_category (id, descr) VALUES (2, 'Prosjekt, Kontrakt')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_ecobilag_category (id, descr) VALUES (3, 'Prosjekt, Tillegg')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_ecobilag_category (id, descr) VALUES (4, 'Prosjekt, LP-stign')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_ecobilag_category (id, descr) VALUES (5, 'Administrasjon')");

#
# Dumping data for table fm_ecomva
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_ecomva (id, descr) VALUES (2, 'Mva 2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_ecomva (id, descr) VALUES (1, 'Mva 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_ecomva (id, descr) VALUES (0, 'ingen')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_ecomva (id, descr) VALUES (3, 'Mva 3')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_ecomva (id, descr) VALUES (4, 'Mva 4')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_ecomva (id, descr) VALUES (5, 'Mva 5')");

#
# Dumping data for table fm_entity
#

$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.entity.1');
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_entity (location_id, id, name, descr, location_form, documentation) VALUES ({$location_id}, 1, 'Equipment', 'equipment', 1, 1)");
//$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_entity (id, name, descr, location_form, documentation, lookup_entity) VALUES (2, 'Report', 'report', 1, NULL, 'a:1:{i:0;s:1:"1";}')");
$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.entity.2');
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_entity (location_id, id, name, descr, location_form, documentation, lookup_entity) VALUES ({$location_id}, 2, 'Report', 'report', 1, NULL, '')");

#
# Dumping data for table fm_entity_category
#







#
# Dumping data for table fm_entity_attribute
#
$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.entity.1.1');
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_entity_category (location_id, entity_id, id, name, descr, prefix, lookup_tenant, tracking, location_level) VALUES ({$location_id}, 1, 1, 'Meter', 'Meter', NULL, NULL, NULL, 3)");

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 1, 'status', 'Status', 'Status', 'LB', NULL, 1, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 2, 'category', 'Category', 'Category statustext', 'LB', NULL, 2, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 3, 'ext_system_id', 'Ext system id', 'External system id', 'V', NULL, 3, NULL, 12, NULL, NULL, 'False')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 4, 'maaler_nr', 'Ext meter id', 'External meter id', 'V', NULL, 4, NULL, 12, NULL, NULL, 'False')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 5, 'remark', 'Remark', 'Remark status text', 'T', NULL, 5, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 1, 1, 'status 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 1, 2, 'status 2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 2, 1, 'Tenant power meter')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 2, 2, 'Joint power meter')");

$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.entity.1.2');
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_entity_category (location_id, entity_id, id, name, descr, prefix, lookup_tenant, tracking, location_level) VALUES ({$location_id}, 1, 2, 'Elevator', 'Elevator', 'E', NULL, NULL, 3)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 1, 'status', 'Status', 'Status', 'LB', NULL, 1, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 2, 'attribute1', 'Attribute 1', 'Attribute 1 statustext', 'V', NULL, 2, NULL, 12, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 3, 'attribute2', 'Attribute 2', 'Attribute 2 status text', 'D', NULL, 3, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 4, 'attribute3', 'Attribute 3', 'Attribute 3 status text', 'R', NULL, 4, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 5, 'attribute4', 'Attribute 4', 'Attribute 4 statustext', 'CH', NULL, 5, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 6, 'attribute5', 'Attribute 5', 'Attribute 5 statustext', 'AB', NULL, 6, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 1, 1, 'status 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 1, 2, 'status 2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 4, 1, 'choice 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 4, 2, 'choice 2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 5, 1, 'choice 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 5, 2, 'choice 2')");

$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.entity.1.3');
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_entity_category (location_id, entity_id, id, name, descr, prefix, lookup_tenant, tracking, location_level) VALUES ({$location_id}, 1, 3, 'Fire alarm central', 'Fire alarm central', 'F', NULL, NULL, 3)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 1, 'status', 'Status', 'Status', 'LB', NULL, 1, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 2, 'attribute1', 'Attribute 1', 'Attribute 1 statustext', 'V', NULL, 2, NULL, 12, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 3, 'attribute2', 'Attribute 2', 'Attribute 2 status text', 'D', NULL, 3, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 4, 'attribute3', 'Attribute 3', 'Attribute 3 status text', 'R', NULL, 4, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 5, 'attribute4', 'Attribute 4', 'Attribute 4 statustext', 'CH', NULL, 5, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 6, 'attribute5', 'Attribute 5', 'Attribute 5 statustext', 'AB', NULL, 6, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 1, 1, 'status 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 1, 2, 'status 2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 4, 1, 'choice 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 4, 2, 'choice 2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 5, 1, 'choice 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 5, 2, 'choice 2')");

$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.entity.2.1');
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_entity_category (location_id, entity_id, id, name, descr, prefix, lookup_tenant, tracking, location_level) VALUES ({$location_id}, 2, 1, 'Report type 1', 'Report type 1', 'RA', 1, 1, 4)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 1, 'status', 'Status', 'Status', 'LB', NULL, 1, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 2, 'attribute1', 'Attribute 1', 'Attribute 1 statustext', 'V', NULL, 2, NULL, 12, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 3, 'attribute2', 'Attribute 2', 'Attribute 2 status text', 'D', NULL, 3, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 4, 'attribute3', 'Attribute 3', 'Attribute 3 status text', 'R', NULL, 4, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 5, 'attribute4', 'Attribute 4', 'Attribute 4 statustext', 'CH', NULL, 5, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 6, 'attribute5', 'Attribute 5', 'Attribute 5 statustext', 'AB', NULL, 6, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 1, 1, 'status 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 1, 2, 'status 2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 4, 1, 'choice 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 4, 2, 'choice 2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 5, 1, 'choice 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 5, 2, 'choice 2')");

$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.entity.2.2');
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_entity_category (location_id, entity_id, id, name, descr, prefix, lookup_tenant, tracking, location_level) VALUES ({$location_id}, 2, 2, 'Report type 2', 'Report type 2', 'RB', 1, 1, 4)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 1, 'status', 'Status', 'Status', 'LB', NULL, 1, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 2, 'attribute1', 'Attribute 1', 'Attribute 1 statustext', 'V', NULL, 2, NULL, 12, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 3, 'attribute2', 'Attribute 2', 'Attribute 2 status text', 'D', NULL, 3, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 4, 'attribute3', 'Attribute 3', 'Attribute 3 status text', 'R', NULL, 4, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 5, 'attribute4', 'Attribute 4', 'Attribute 4 statustext', 'CH', NULL, 5, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 6, 'attribute5', 'Attribute 5', 'Attribute 5 statustext', 'AB', NULL, 6, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 1, 1, 'status 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 1, 2, 'status 2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 4, 1, 'choice 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 4, 2, 'choice 2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 5, 1, 'choice 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 5, 2, 'choice 2')");


#
# Dumping data for table fm_entity_lookup
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_entity_lookup (entity_id, location, type) VALUES (1, 'project', 'lookup')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_entity_lookup (entity_id, location, type) VALUES (1, 'ticket', 'lookup')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_entity_lookup (entity_id, location, type) VALUES (2, 'request', 'start')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_entity_lookup (entity_id, location, type) VALUES (2, 'ticket', 'start')");


#
# Dumping data for table fm_custom
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_custom (id, name, sql_text) VALUES (1, 'test query', 'select * from phpgw_accounts')");

#
# Dumping data for table fm_custom_cols
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_custom_cols (custom_id, id, name, descr, sorting) VALUES (1, 1, 'account_id', 'ID', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_custom_cols (custom_id, id, name, descr, sorting) VALUES (1, 2, 'account_lid', 'Lid', 2)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_custom_cols (custom_id, id, name, descr, sorting) VALUES (1, 3, 'account_firstname', 'First Name', 3)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_custom_cols (custom_id, id, name, descr, sorting) VALUES (1, 4, 'account_lastname', 'Last Name', 4)");


#
# Dumping data for table fm_vendor_attribute
#
$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.vendor');
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, list, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable, search) VALUES ($location_id, 1, 1, 'org_name', 'Name', 'The Name of the vendor', NULL, 'V', 1, 50, NULL, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, list, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable, search) VALUES ($location_id, 2, 1, 'contact_phone', 'Contact phone', 'Contact phone', NULL, 'V', 2, 20, NULL, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, list, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable, search) VALUES ($location_id, 3, 1, 'email', 'email', 'email', NULL, 'email', 3, 64, NULL, NULL, 'True', 1)");


$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_vendor_category (id, descr) VALUES (1, 'kateogory 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_vendor (id, org_name, email, contact_phone, category) VALUES (1, 'Demo vendor', 'demo@vendor.org', '5555555', 1)");


#
# Data for table fm_location_type
#

$location_naming[1]['name']='property';
$location_naming[1]['descr']='Property';
$location_naming[2]['name']='building';
$location_naming[2]['descr']='Building';
$location_naming[3]['name']='entrance';
$location_naming[3]['descr']='Entrance';
$location_naming[4]['name']='Apartment';
$location_naming[4]['descr']='Apartment';

for ($location_type=1; $location_type<5; $location_type++)
{
	$default_attrib['id'][]= 1;
	$default_attrib['column_name'][]= 'location_code';
	$default_attrib['type'][]='V';
	$default_attrib['precision'][] =4*$location_type;
	$default_attrib['nullable'][] ='False';
	$default_attrib['input_text'][] ='location_code';
	$default_attrib['statustext'][] ='location_code';

	$default_attrib['id'][]= 2;
	$default_attrib['column_name'][]= 'loc' . $location_type . '_name';
	$default_attrib['type'][]='V';
	$default_attrib['precision'][] =50;
	$default_attrib['nullable'][] ='True';
	$default_attrib['input_text'][] ='loc' . $location_type . '_name';
	$default_attrib['statustext'][] ='loc' . $location_type . '_name';

	$default_attrib['id'][]= 3;
	$default_attrib['column_name'][]= 'entry_date';
	$default_attrib['type'][]='I';
	$default_attrib['precision'][] =4;
	$default_attrib['nullable'][] ='True';
	$default_attrib['input_text'][] ='entry_date';
	$default_attrib['statustext'][] ='entry_date';

	$default_attrib['id'][]= 4;
	$default_attrib['column_name'][]= 'category';
	$default_attrib['type'][]='I';
	$default_attrib['precision'][] =4;
	$default_attrib['nullable'][] ='False';
	$default_attrib['input_text'][] ='category';
	$default_attrib['statustext'][] ='category';

	$default_attrib['id'][]= 5;
	$default_attrib['column_name'][]= 'user_id';
	$default_attrib['type'][]='I';
	$default_attrib['precision'][] =4;
	$default_attrib['nullable'][] ='False';
	$default_attrib['input_text'][] ='user_id';
	$default_attrib['statustext'][] ='user_id';

	for ($i=1; $i<$location_type+1; $i++)
	{
		$pk[$i-1]= 'loc' . $i;

		$default_attrib['id'][]= $i+5;
		$default_attrib['column_name'][]= 'loc' . $i;
		$default_attrib['type'][]='V';
		$default_attrib['precision'][] =4;
		$default_attrib['nullable'][] ='False';
		$default_attrib['input_text'][] ='loc' . $i;
		$default_attrib['statustext'][] ='loc' . $i;
	}

/*
	if($location_type>1)
	{
		$fk_table='fm_location'. ($location_type-1);

		for ($i=1; $i<$standard['id']; $i++)
		{
			$fk['loc' . $i]	= $fk_table . '.loc' . $i;
		}
	}
*/
	$ix = array('location_code');

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location_type (id,name,descr,pk,ix) "
		. "VALUES ($location_type,'"
		.  $location_naming[$location_type]['name'] . "','"
		. $location_naming[$location_type]['descr'] . "','"
		. implode(',',$pk) . "','"
		. implode(',',$ix) . "')");

	$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_type set list_info = '" . 'a:1:{i:1;s:1:"1";}' ."' WHERE id = '1'");
	$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_type set list_info = '" . 'a:2:{i:1;s:1:"1";i:2;s:1:"2";}' ."' WHERE id = '2'");
	$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_type set list_info = '" . 'a:3:{i:1;s:1:"1";i:2;s:1:"2";i:3;s:1:"3";}' ."' WHERE id = '3'");
	$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_type set list_info = '" . 'a:1:{i:1;s:1:"1";}' ."' WHERE id = '4'");

	$location_id = $GLOBALS['phpgw']->locations->get_id('property', ".location.{$location_type}");

	for ($i=0;$i<count($default_attrib['id']);$i++)
	{
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id,column_name,datatype,precision_,input_text,statustext,nullable,custom)"
			. " VALUES ("
			. $location_id. ','
			. $default_attrib['id'][$i] . ",'"
			. $default_attrib['column_name'][$i] . "','"
			. $default_attrib['type'][$i] . "',"
			. $default_attrib['precision'][$i] . ",'"
			. $default_attrib['input_text'][$i] . "','"
			. $default_attrib['statustext'][$i] . "','"
			. $default_attrib['nullable'][$i] . "',NULL)");
	}

	unset($pk);
	unset($ix);
	unset($default_attrib);
}

#
# Dumping data for table fm_location_attrib
#
$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.location.1');
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 8, 'status', 'Status', 'Status', 'LB', NULL, 1, NULL, NULL, NULL, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 9, 'remark', 'Remark', 'Remark', 'T', NULL, 2, NULL, NULL, NULL, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 10, 'mva', 'mva', 'Status', 'I', NULL, 3, NULL, 4, NULL, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 11, 'kostra_id', 'kostra_id', 'kostra_id', 'I', NULL, 4, NULL, 4, NULL, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 12, 'part_of_town_id', 'part_of_town_id', 'part_of_town_id', 'I', NULL, NULL, NULL, 4, NULL, NULL, 'True', NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 13, 'owner_id', 'owner_id', 'owner_id', 'I', NULL, NULL, NULL, 4, NULL, NULL, 'True', NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 14, 'change_type', 'change_type', 'change_type', 'I', NULL, NULL, NULL, 4, NULL, NULL, 'True', NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 15, 'rental_area', 'Rental area', 'Rental area', 'N', NULL, 5, 20, 2, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 16, 'area_gross', 'Gross area', 'Sum of the areas included within the outside face of the exterior walls of a building.', 'N', NULL, 5, 20, 2, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 17, 'area_net', 'Net area', 'The wall-to-wall floor area of a room.', 'N', NULL, 5, 20, 2, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 18, 'area_usable', 'Usable area', 'generally measured from paint to paint inside the permanent walls and to the middle of partitions separating rooms', 'N', NULL, 5, 20, 2, NULL, 'True', 1)");

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 8, 1, 'OK')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 8, 2, 'Not OK')");


$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.location.2');
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 9, 'status', 'Status', 'Status', 'LB', NULL, 1, NULL, NULL, NULL, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 10, 'remark', 'Remark', 'Remark', 'T', NULL, 2, NULL, NULL, NULL, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 11, 'change_type', 'change_type', 'change_type', 'I', NULL, NULL, NULL, 4, NULL, NULL, 'True', NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 12, 'rental_area', 'Rental area', 'Rental area', 'N', NULL, 3, 20, 2, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 13, 'area_gross', 'Gross area', 'Sum of the areas included within the outside face of the exterior walls of a building.', 'N', NULL, 5, 20, 2, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 14, 'area_net', 'Net area', 'The wall-to-wall floor area of a room.', 'N', NULL, 5, 20, 2, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 15, 'area_usable', 'Usable area', 'generally measured from paint to paint inside the permanent walls and to the middle of partitions separating rooms', 'N', NULL, 5, 20, 2, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 9, 1, 'OK')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 9, 2, 'Not OK')");

$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.location.3');
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 10, 'status', 'Status', 'Status', 'LB', NULL, 1, NULL, NULL, NULL, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 11, 'remark', 'Remark', 'Remark', 'T', NULL, 2, NULL, NULL, NULL, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 12, 'change_type', 'change_type', 'change_type', 'I', NULL, NULL, NULL, 4, NULL, NULL, 'True', NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 13, 'rental_area', 'Rental area', 'Rental area', 'N', NULL, 3, 20, 2, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 14, 'area_gross', 'Gross area', 'Sum of the areas included within the outside face of the exterior walls of a building.', 'N', NULL, 5, 20, 2, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 15, 'area_net', 'Net area', 'The wall-to-wall floor area of a room.', 'N', NULL, 5, 20, 2, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 16, 'area_usable', 'Usable area', 'generally measured from paint to paint inside the permanent walls and to the middle of partitions separating rooms', 'N', NULL, 5, 20, 2, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 10, 1, 'OK')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 10, 2, 'Not OK')");


$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.location.4');
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 11, 'status', 'Status', 'Status', 'LB', NULL, 1, NULL, NULL, NULL, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 12, 'remark', 'Remark', 'Remark', 'T', NULL, 2, NULL, NULL, NULL, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 13, 'street_id', 'street_id', 'street_id', 'I', NULL, NULL, NULL, 4, NULL, NULL, 'True', NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 14, 'street_number', 'street_number', 'street_number', 'I', NULL, NULL, NULL, 4, NULL, NULL, 'True', NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 15, 'tenant_id', 'tenant_id', 'tenant_id', 'I', NULL, NULL, NULL, 4, NULL, NULL, 'True', NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 16, 'change_type', 'change_type', 'change_type', 'I', NULL, NULL, NULL, 4, NULL, NULL, 'True', NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 17, 'rental_area', 'Rental area', 'Rental area', 'N', NULL, 4, 20, 2, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 18, 'area_gross', 'Gross area', 'Sum of the areas included within the outside face of the exterior walls of a building.', 'N', NULL, 5, 20, 2, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 19, 'area_net', 'Net area', 'The wall-to-wall floor area of a room.', 'N', NULL, 5, 20, 2, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, precision_, scale, default_value, nullable,custom) VALUES ($location_id, 20, 'area_usable', 'Usable area', 'generally measured from paint to paint inside the permanent walls and to the middle of partitions separating rooms', 'N', NULL, 5, 20, 2, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 11, 1, 'OK')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (location_id, attrib_id, id, value) VALUES ($location_id, 11, 2, 'Not OK')");


$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_action_pending_category (num, name, descr) VALUES ('approval', 'Approval', 'Please approve the item requested')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_action_pending_category (num, name, descr) VALUES ('remind', 'Remind', 'This is a reminder of task assigned')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_action_pending_category (num, name, descr) VALUES ('accept_delivery', 'Accept delivery', 'Please accept delivery on this item')");

// Admin get full access
$GLOBALS['phpgw']->acl->enable_inheritance = true;
$aclobj =& $GLOBALS['phpgw']->acl;
$admin_group		= $GLOBALS['phpgw']->accounts->name2id('admin');
if($admin_group) // check if admin has been defined yet
{
	$aclobj->set_account_id($admin_group, true);
	$aclobj->add('property', '.', 31);
	$aclobj->add('property', 'run', 1);
	$aclobj->save_repository();
}

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_input_type (name, descr) VALUES ('integer', 'Integer')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_input_type (name, descr) VALUES ('float', 'Float')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_input_type (name, descr) VALUES ('text', 'Text')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_input_type (name, descr) VALUES ('date', 'Date')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_input_type (name, descr) VALUES ('timestamp', 'timestamp')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_input_type (name, descr) VALUES ('AB', 'Address book')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_input_type (name, descr) VALUES ('VENDOR', 'Vendor')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_input_type (name, descr) VALUES ('user', 'system user')");

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_format_type (id) VALUES ('PDF')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_format_type (id) VALUES ('CSV')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_format_type (id) VALUES ('XLS')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_format_type (id) VALUES ('XHTML')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_format_type (id) VALUES ('DOCX')");

$solocation = createObject('property.solocation');
$solocation->update_location();

		$custom_config	= CreateObject('admin.soconfig',$GLOBALS['phpgw']->locations->get_id('property', '.invoice'));

		// common
		$receipt_section_common = $custom_config->add_section(array
			(
				'name' => 'common',
				'descr' => 'common invoice config'
			)
		);

		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_common['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'host',
				'descr'			=> 'Host',
			)
		);
		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_common['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'user',
				'descr'			=> 'User',
			)
		);
		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_common['section_id'],
				'input_type'	=> 'password',
				'name'			=> 'password',
				'descr'			=> 'Password',
			)
		);
		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_common['section_id'],
				'input_type'	=> 'listbox',
				'name'			=> 'method',
				'descr'			=> 'Export / import method',
				'choice'		=> array('local','ftp','ssh'),
			)
		);

		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_common['section_id'],
				'attrib_id'		=> $receipt['attrib_id'],
				'input_type'	=> 'listbox',
				'name'			=> 'invoice_approval',
				'descr'			=> 'Number of persons required to approve for payment',
				'choice'		=> array(1,2),
			)
		);

		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_common['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'baseurl_invoice',
				'descr'			=> 'baseurl on remote server for image of invoice',
			)
		);

		// import:
		$receipt_section_import = $custom_config->add_section(array
			(
				'name' => 'import',
				'descr' => 'import invoice config'
			)
		);

		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_import['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'local_path',
				'descr'			=> 'path on local sever to store imported files',
			)
		);

		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_import['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'budget_responsible',
				'descr'			=> 'default initials if responsible can not be found',
			)
		);

		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_import['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'remote_basedir',
				'descr'			=> 'basedir on remote server',
			)
		);

		//export
		$receipt_section_export = $custom_config->add_section(array
			(
				'name' => 'export',
				'descr' => 'Invoice export'
			)
		);
		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_export['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'cleanup_old',
				'descr'			=> 'Overføre manuelt registrerte fakturaer rett til historikk'
			)
		);
		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_export['section_id'],
				'input_type'	=> 'date',
				'name'			=> 'dato_aarsavslutning',
				'descr'			=> "Dato for årsavslutning: overført pr. desember foregående år"
			)
		);
		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_export['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'path',
				'descr'			=> 'path on local sever to store exported files',
			)
		);

		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_export['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'pre_path',
				'descr'			=> 'path on local sever to store exported files for pre approved vouchers',
			)
		);

		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_export['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'remote_basedir',
				'descr'			=> 'basedir on remote server to receive files',
			)
		);

		$sql = 'CREATE OR REPLACE VIEW fm_open_workorder_view AS' 
			. ' SELECT fm_workorder.id, fm_workorder.project_id, fm_workorder_status.descr FROM fm_workorder'
			. ' JOIN fm_workorder_status ON fm_workorder.status = fm_workorder_status.id WHERE fm_workorder_status.delivered IS NULL AND fm_workorder_status.closed IS NULL';
		$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);


		$sql = 'CREATE OR REPLACE VIEW fm_ecobilag_sum_view AS'
			. ' SELECT DISTINCT bilagsnr, sum(godkjentbelop) AS approved_amount, sum(belop) AS amount FROM fm_ecobilag  GROUP BY bilagsnr ORDER BY bilagsnr ASC';
		$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);


		$sql = 'CREATE OR REPLACE VIEW fm_orders_pending_cost_view AS'
			. ' SELECT fm_ecobilag.pmwrkord_code AS order_id, sum(fm_ecobilag.godkjentbelop) AS pending_cost FROM fm_ecobilag GROUP BY fm_ecobilag.pmwrkord_code';

		$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);

		$sql = 'CREATE OR REPLACE VIEW fm_orders_actual_cost_view AS'
 			. ' SELECT fm_ecobilagoverf.pmwrkord_code AS order_id, sum(fm_ecobilagoverf.godkjentbelop) AS actual_cost FROM fm_ecobilagoverf  GROUP BY fm_ecobilagoverf.pmwrkord_code';

		$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);

		switch ( $GLOBALS['phpgw_info']['server']['db_type'] )
		{
			case 'postgres':
				$sql = 'CREATE OR REPLACE VIEW fm_orders_paid_or_pending_view AS 
				 SELECT orders_paid_or_pending.order_id, orders_paid_or_pending.periode,orders_paid_or_pending.amount,orders_paid_or_pending.periodization, orders_paid_or_pending.periodization_start
				   FROM ( SELECT fm_ecobilagoverf.pmwrkord_code AS order_id, fm_ecobilagoverf.periode, sum(fm_ecobilagoverf.godkjentbelop) AS amount, fm_ecobilagoverf.periodization, fm_ecobilagoverf.periodization_start
						   FROM fm_ecobilagoverf
						   GROUP BY fm_ecobilagoverf.pmwrkord_code, fm_ecobilagoverf.periode, fm_ecobilagoverf.periodization, fm_ecobilagoverf.periodization_start
						UNION ALL 
						  SELECT fm_ecobilag.pmwrkord_code AS order_id, fm_ecobilag.periode, sum(fm_ecobilag.godkjentbelop) AS amount, fm_ecobilag.periodization, fm_ecobilag.periodization_start
						   FROM fm_ecobilag
						   GROUP BY fm_ecobilag.pmwrkord_code, fm_ecobilag.periode, fm_ecobilag.periodization, fm_ecobilag.periodization_start) orders_paid_or_pending ORDER BY orders_paid_or_pending.periode, orders_paid_or_pending.order_id';

				$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);
				break;
			default:
				//do nothing for now
		}

		$sql = 'CREATE OR REPLACE VIEW fm_project_budget_year_from_order_view AS'
 			. ' SELECT DISTINCT fm_workorder.project_id, fm_workorder_budget.year'
 			. ' FROM fm_workorder_budget'
 			. ' JOIN fm_workorder ON fm_workorder.id = fm_workorder_budget.order_id'
 			. ' ORDER BY fm_workorder.project_id';

		$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);


		$sql = 'CREATE OR REPLACE VIEW fm_project_budget_year_view AS' 
 			. ' SELECT DISTINCT fm_project_budget.project_id, fm_project_budget.year'
 			. ' FROM fm_project_budget'
 			. ' ORDER BY fm_project_budget.project_id';

		$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);


		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_ecodimb_role (id, name) VALUES (1, 'Bestiller')",__LINE__,__FILE__);
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_ecodimb_role (id, name) VALUES (2, 'Attestant')",__LINE__,__FILE__);
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_ecodimb_role (id, name) VALUES (3, 'Anviser')",__LINE__,__FILE__);

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tts_priority (id, name) VALUES (1, '1 - Highest')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tts_priority (id, name) VALUES (2, '2')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tts_priority (id, name) VALUES (3, '3 - Lowest')");

