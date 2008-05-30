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

$app_id = $GLOBALS['phpgw']->applications->name2id('property');

$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_cust_attribute WHERE appname='property'");
$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_cust_choice WHERE appname='property'");

#
#fm_workorder_category
#
//FIXME: consider adding categories to global categories 'property.project'
//$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_workorder_category (id, descr) VALUES (1, 'Preventive')");
//$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_workorder_category (id, descr) VALUES (2, 'Ad Hoc')");

#
#fm_meter_category
#

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

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, list, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable, search) VALUES ('property', '.owner', 1, 1, 'abid', 'Contact', 'Contakt person', NULL, 'AB', 1, 4, NULL, NULL, 'True', NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, list, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable, search) VALUES ('property', '.owner', 2, 1, 'org_name', 'Name', 'The name of the owner', NULL, 'V', 2, 50, NULL, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, list, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable, search) VALUES ('property', '.owner', 3, 1, 'remark', 'remark', 'remark', NULL, 'T', 3, NULL, NULL, NULL, 'True', NULL)");

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

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition_type (id, descr, priority_key) VALUES (1, 'safety', 10)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition_type (id, descr, priority_key) VALUES (2, 'aesthetics', 2)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition_type (id, descr, priority_key) VALUES (3, 'indoor climate', 5)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition_type (id, descr, priority_key) VALUES (4, 'consequential damage', 5)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition_type (id, descr, priority_key) VALUES (5, 'user gratification', 4)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition_type (id, descr, priority_key) VALUES (6, 'residential environment', 6)");


#
# fm_document_category
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_document_category (id, descr) VALUES ('1', 'Picture')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_document_category (id, descr) VALUES ('2', 'Report')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_document_category (id, descr) VALUES ('3', 'Instruction')");

#
# fm_tts_category
#

//FIXME: consider adding categories to global categories 'property.ticket'
//$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tts_category (id, descr) VALUES ('1', 'damage')");
//$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tts_category (id, descr) VALUES ('2', 'user request')");
//$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tts_category (id, descr) VALUES ('3', 'warranty')");


#
# fm_document_status
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_document_status (id, descr) VALUES ('draft', 'Draft')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_document_status (id, descr) VALUES ('final', 'Final')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_document_status (id, descr) VALUES ('obsolete', 'obsolete')");


#
# fm_standard_unit
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_standard_unit (id, descr) VALUES ('m', 'Meter')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_standard_unit (id, descr) VALUES ('m2', 'Square meters')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_standard_unit (id, descr) VALUES ('m3', 'Cubic meters')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_standard_unit (id, descr) VALUES ('km', 'Kilometre')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_standard_unit (id, descr) VALUES ('Stk', 'Stk')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_standard_unit (id, descr) VALUES ('kg', 'Kilogram')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_standard_unit (id, descr) VALUES ('tonn', 'Tonn')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_standard_unit (id, descr) VALUES ('h', 'Hours')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_standard_unit (id, descr) VALUES ('RS', 'Round Sum')");


#
#  fm_agreement_status
#
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_agreement_status (id, descr) VALUES ('closed', 'Closed')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_agreement_status (id, descr) VALUES ('active', 'Active agreement')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_agreement_status (id, descr) VALUES ('planning', 'Planning')");

#
#  phpgw_locations
#
//FIXME
//$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_acl where acl_appname = 'property' AND acl_location !='run' ");
$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_locations where app_id = {$app_id}");

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
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant) VALUES ({$app_id}, '.project', 'Demand -> Workorder', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant) VALUES ({$app_id}, '.ticket', 'Helpdesk', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.ticket.external', 'Helpdesk External user')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.invoice', 'Invoice')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.document', 'Documents')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.drawing', 'Drawing')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant) VALUES ({$app_id}, '.entity.1', 'Equipment', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.entity.1.1', 'Meter', 1, 1, 'fm_entity_1_1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.entity.1.2', 'Elevator', 1, 1, 'fm_entity_1_2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.entity.1.3', 'Fire alarm central', 1, 1, 'fm_entity_1_3')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant) VALUES ({$app_id}, '.entity.2', 'Report', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.entity.2.1', 'Report type 1', 1, 1, 'fm_entity_2_1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.entity.2.2', 'Report type 2', 1, 1, 'fm_entity_2_2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.b_account', 'Budget account')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.tenant_claim', 'Tenant claim')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.budget', 'Budet')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.budget.obligations', 'Obligations')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.ifc', 'ifc integration')");

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.agreement', 'Agreement',1,'fm_agreement')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.s_agreement', 'Service agreement',1,'fm_s_agreement')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.s_agreement.detail', 'Service agreement detail',1,'fm_s_agreement_detail')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.r_agreement', 'Rental agreement',1,'fm_r_agreement')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.r_agreement.detail', 'Rental agreement detail',1,'fm_r_agreement_detail')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.tenant', 'Tenant',1,'fm_tenant')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.owner', 'Owner',1,'fm_owner')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.vendor', 'Vendor',1,'fm_vendor')");


#
#  fm_ns3420
#
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_ns3420 (id, tekst1, enhet) VALUES ('D00', 'RIGGING, KLARGJØRING', 'RS')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_ns3420 (id, tekst1, enhet,tekst2) VALUES ('D20', 'RIGGING, ANLEGGSTOMT', 'RS','TILFØRSEL- OG FORSYNINGSANLEGG')");

#
# Data-ark for tabell fm_idgenerator
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_idgenerator (name, value, descr) VALUES ('Bilagsnummer', '2003100000', 'Bilagsnummer')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_idgenerator (name, value, descr) VALUES ('Ecobatchid', '1', 'Ecobatchid')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_idgenerator (name, value, descr) VALUES ('project', '1000', 'project')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_idgenerator (name, value, descr) VALUES ('Statuslog', '1', 'Statuslog')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_idgenerator (name, value, descr) VALUES ('workorder', '1000', 'workorder')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_idgenerator (name, value, descr) VALUES ('request', '1000', 'request')");

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

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, list, search, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable) VALUES ('property', '.tenant', 1, 1, 1, 'first_name', 'First name', 'First name', NULL, 'V', 1, 50, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, list, search, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable) VALUES ('property', '.tenant', 2, 1, 1, 'last_name', 'Last name', 'Last name', NULL, 'V', 2, 50, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, list, search, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable) VALUES ('property', '.tenant', 3, 1, 1, 'contact_phone', 'contact phone', 'contact phone', NULL, 'V', 3, 20, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, list, search, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable) VALUES ('property', '.tenant', 4, NULL, NULL, 'phpgw_account_id', 'Mapped User', 'Mapped User', NULL, 'user', 4, 4, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, list, search, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable) VALUES ('property', '.tenant', 5, NULL, NULL, 'account_lid', 'User Name', 'User name for login', NULL, 'V', 5, 25, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, list, search, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable) VALUES ('property', '.tenant', 6, NULL, NULL, 'account_pwd', 'Password', 'Users Password', NULL, 'pwd', 6, 32, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, list, search, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable) VALUES ('property', '.tenant', 7, NULL, NULL, 'account_status', 'account status', 'account status', NULL, 'LB', 7, NULL, NULL, NULL, 'True')");

#
# Dumping data for table fm_tenant_choice
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.tenant', 7, 1, 'Active')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.tenant', 7, 2, 'Banned')");

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

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_entity (id, name, descr, location_form, documentation) VALUES (1, 'Equipment', 'equipment', 1, 1)");
//$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_entity (id, name, descr, location_form, documentation, lookup_entity) VALUES (2, 'Report', 'report', 1, NULL, 'a:1:{i:0;s:1:"1";}')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_entity (id, name, descr, location_form, documentation, lookup_entity) VALUES (2, 'Report', 'report', 1, NULL, '')");

#
# Dumping data for table fm_entity_category
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_entity_category (entity_id, id, name, descr, prefix, lookup_tenant, tracking, location_level) VALUES (1, 1, 'Meter', 'Meter', NULL, NULL, NULL, 3)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_entity_category (entity_id, id, name, descr, prefix, lookup_tenant, tracking, location_level) VALUES (1, 2, 'Elevator', 'Elevator', 'E', NULL, NULL, 3)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_entity_category (entity_id, id, name, descr, prefix, lookup_tenant, tracking, location_level) VALUES (1, 3, 'Fire alarm central', 'Fire alarm central', 'F', NULL, NULL, 3)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_entity_category (entity_id, id, name, descr, prefix, lookup_tenant, tracking, location_level) VALUES (2, 1, 'Report type 1', 'Report type 1', 'RA', 1, 1, 4)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_entity_category (entity_id, id, name, descr, prefix, lookup_tenant, tracking, location_level) VALUES (2, 2, 'Report type 2', 'Report type 2', 'RB', 1, 1, 4)");


#
# Dumping data for table fm_entity_attribute
#
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.1.1', 1, 'status', 'Status', 'Status', 'LB', NULL, 1, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.1.1', 2, 'category', 'Category', 'Category statustext', 'LB', NULL, 2, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.1.1', 3, 'ext_system_id', 'Ext system id', 'External system id', 'V', NULL, 3, NULL, 12, NULL, NULL, 'False')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.1.1', 4, 'ext_meter_id', 'Ext meter id', 'External meter id', 'V', NULL, 4, NULL, 12, NULL, NULL, 'False')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.1.1', 5, 'remark', 'Remark', 'Remark status text', 'T', NULL, 5, NULL, NULL, NULL, NULL, 'True')");

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.1.2', 1, 'status', 'Status', 'Status', 'LB', NULL, 1, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.1.2', 2, 'attribute1', 'Attribute 1', 'Attribute 1 statustext', 'V', NULL, 2, NULL, 12, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.1.2', 3, 'attribute2', 'Attribute 2', 'Attribute 2 status text', 'D', NULL, 3, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.1.2', 4, 'attribute3', 'Attribute 3', 'Attribute 3 status text', 'R', NULL, 4, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.1.2', 5, 'attribute4', 'Attribute 4', 'Attribute 4 statustext', 'CH', NULL, 5, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.1.2', 6, 'attribute5', 'Attribute 5', 'Attribute 5 statustext', 'AB', NULL, 6, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.1.3', 1, 'status', 'Status', 'Status', 'LB', NULL, 1, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.1.3', 2, 'attribute1', 'Attribute 1', 'Attribute 1 statustext', 'V', NULL, 2, NULL, 12, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.1.3', 3, 'attribute2', 'Attribute 2', 'Attribute 2 status text', 'D', NULL, 3, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.1.3', 4, 'attribute3', 'Attribute 3', 'Attribute 3 status text', 'R', NULL, 4, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.1.3', 5, 'attribute4', 'Attribute 4', 'Attribute 4 statustext', 'CH', NULL, 5, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.1.3', 6, 'attribute5', 'Attribute 5', 'Attribute 5 statustext', 'AB', NULL, 6, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.2.1', 1, 'status', 'Status', 'Status', 'LB', NULL, 1, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.2.1', 2, 'attribute1', 'Attribute 1', 'Attribute 1 statustext', 'V', NULL, 2, NULL, 12, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.2.1', 3, 'attribute2', 'Attribute 2', 'Attribute 2 status text', 'D', NULL, 3, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.2.1', 4, 'attribute3', 'Attribute 3', 'Attribute 3 status text', 'R', NULL, 4, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.2.1', 5, 'attribute4', 'Attribute 4', 'Attribute 4 statustext', 'CH', NULL, 5, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.2.1', 6, 'attribute5', 'Attribute 5', 'Attribute 5 statustext', 'AB', NULL, 6, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.2.2', 1, 'status', 'Status', 'Status', 'LB', NULL, 1, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.2.2', 2, 'attribute1', 'Attribute 1', 'Attribute 1 statustext', 'V', NULL, 2, NULL, 12, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.2.2', 3, 'attribute2', 'Attribute 2', 'Attribute 2 status text', 'D', NULL, 3, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.2.2', 4, 'attribute3', 'Attribute 3', 'Attribute 3 status text', 'R', NULL, 4, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.2.2', 5, 'attribute4', 'Attribute 4', 'Attribute 4 statustext', 'CH', NULL, 5, NULL, NULL, NULL, NULL, 'True')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ('property', '.entity.2.2', 6, 'attribute5', 'Attribute 5', 'Attribute 5 statustext', 'AB', NULL, 6, NULL, NULL, NULL, NULL, 'True')");

#
# Dumping data for table fm_entity_choice
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.1.1', 1, 1, 'status 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.1.1', 1, 2, 'status 2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.1.1', 2, 1, 'Tenant power meter')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.1.1', 2, 2, 'Joint power meter')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.1.2', 1, 1, 'status 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.1.2', 1, 2, 'status 2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.1.2', 4, 1, 'choice 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.1.2', 4, 2, 'choice 2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.1.2', 5, 1, 'choice 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.1.2', 5, 2, 'choice 2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.1.3', 1, 1, 'status 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.1.3', 1, 2, 'status 2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.1.3', 4, 1, 'choice 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.1.3', 4, 2, 'choice 2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.1.3', 5, 1, 'choice 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.1.3', 5, 2, 'choice 2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.2.1', 1, 1, 'status 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.2.1', 1, 2, 'status 2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.2.1', 4, 1, 'choice 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.2.1', 4, 2, 'choice 2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.2.1', 5, 1, 'choice 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.2.1', 5, 2, 'choice 2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.2.2', 1, 1, 'status 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.2.2', 1, 2, 'status 2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.2.2', 4, 1, 'choice 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.2.2', 4, 2, 'choice 2')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.2.2', 5, 1, 'choice 1')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.entity.2.2', 5, 2, 'choice 2')");

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

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, list, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable, search) VALUES ('property', '.vendor', 1, 1, 'org_name', 'Name', 'The Name of the vendor', NULL, 'V', 1, 50, NULL, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, list, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable, search) VALUES ('property', '.vendor', 2, 1, 'contact_phone', 'Contact phone', 'Contact phone', NULL, 'V', 2, 20, NULL, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, list, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable, search) VALUES ('property', '.vendor', 3, 1, 'email', 'email', 'email', NULL, 'email', 3, 64, NULL, NULL, 'True', 1)");


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

	for ($i=0;$i<count($default_attrib['id']);$i++)
	{
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id,column_name,datatype,precision_,input_text,statustext,nullable,custom)"
			. " VALUES ("
			. "'property',"
			. "'.location.$location_type',"
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

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ('property', '.location.1', 8, 'status', 'Status', 'Status', 'LB', NULL, 1, NULL, NULL, NULL, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ('property', '.location.1', 9, 'remark', 'Remark', 'Remark', 'T', NULL, 2, NULL, NULL, NULL, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ('property', '.location.1', 10, 'mva', 'mva', 'Status', 'I', NULL, 3, NULL, 4, NULL, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ('property', '.location.1', 11, 'kostra_id', 'kostra_id', 'kostra_id', 'I', NULL, 4, NULL, 4, NULL, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ('property', '.location.1', 12, 'part_of_town_id', 'part_of_town_id', 'part_of_town_id', 'I', NULL, NULL, NULL, 4, NULL, NULL, 'True', NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ('property', '.location.1', 13, 'owner_id', 'owner_id', 'owner_id', 'I', NULL, NULL, NULL, 4, NULL, NULL, 'True', NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ('property', '.location.1', 14, 'change_type', 'change_type', 'change_type', 'I', NULL, NULL, NULL, 4, NULL, NULL, 'True', NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, precision_, scale, default_value, nullable,custom) VALUES ('property', '.location.1', 15, 'rental_area', 'Rental area', 'Rental area', 'N', NULL, 5, 20, 2, NULL, 'True', 1)");

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ('property', '.location.2', 9, 'status', 'Status', 'Status', 'LB', NULL, 1, NULL, NULL, NULL, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ('property', '.location.2', 10, 'remark', 'Remark', 'Remark', 'T', NULL, 2, NULL, NULL, NULL, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ('property', '.location.2', 11, 'change_type', 'change_type', 'change_type', 'I', NULL, NULL, NULL, 4, NULL, NULL, 'True', NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, precision_, scale, default_value, nullable,custom) VALUES ('property', '.location.2', 12, 'rental_area', 'Rental area', 'Rental area', 'N', NULL, 3, 20, 2, NULL, 'True', 1)");

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ('property', '.location.3', 10, 'status', 'Status', 'Status', 'LB', NULL, 1, NULL, NULL, NULL, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ('property', '.location.3', 11, 'remark', 'Remark', 'Remark', 'T', NULL, 2, NULL, NULL, NULL, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ('property', '.location.3', 12, 'change_type', 'change_type', 'change_type', 'I', NULL, NULL, NULL, 4, NULL, NULL, 'True', NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, precision_, scale, default_value, nullable,custom) VALUES ('property', '.location.3', 13, 'rental_area', 'Rental area', 'Rental area', 'N', NULL, 3, 20, 2, NULL, 'True', 1)");

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ('property', '.location.4', 11, 'status', 'Status', 'Status', 'LB', NULL, 1, NULL, NULL, NULL, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ('property', '.location.4', 12, 'remark', 'Remark', 'Remark', 'T', NULL, 2, NULL, NULL, NULL, NULL, 'True', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ('property', '.location.4', 13, 'street_id', 'street_id', 'street_id', 'I', NULL, NULL, NULL, 4, NULL, NULL, 'True', NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ('property', '.location.4', 14, 'street_number', 'street_number', 'street_number', 'I', NULL, NULL, NULL, 4, NULL, NULL, 'True', NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ('property', '.location.4', 15, 'tenant_id', 'tenant_id', 'tenant_id', 'I', NULL, NULL, NULL, 4, NULL, NULL, 'True', NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable,custom) VALUES ('property', '.location.4', 16, 'change_type', 'change_type', 'change_type', 'I', NULL, NULL, NULL, 4, NULL, NULL, 'True', NULL)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (appname, location, id, column_name, input_text, statustext, datatype, list, attrib_sort, precision_, scale, default_value, nullable,custom) VALUES ('property', '.location.4', 17, 'rental_area', 'Rental area', 'Rental area', 'N', NULL, 4, 20, 2, NULL, 'True', 1)");

#
# Dumping data for table fm_location_choice
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.location.1', 8, 1, 'OK')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.location.1', 8, 2, 'Not OK')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.location.2', 9, 1, 'OK')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.location.2', 9, 2, 'Not OK')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.location.3', 10, 1, 'OK')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.location.3', 10, 2, 'Not OK')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.location.4', 11, 1, 'OK')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_choice (appname, location, attrib_id, id, value) VALUES ('property', '.location.4', 11, 2, 'Not OK')");


?>
