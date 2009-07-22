<?php
/**
 * Holds the queries inserting default data (not test data):
 * 
 * $oProc->query("sql_statement");
 * 
 */
 
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Herdla fuglereservat','Pip pip')");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Fløibanen','Tut tut')");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Perle og Bruse','')");
$oProc->query("INSERT INTO rental_composite (name,description,is_active) VALUES ('Store Lungegårdsvannet','',false)");
$oProc->query("INSERT INTO rental_composite (name,description,address_1,address_2,house_number,postcode,place,has_custom_address) VALUES ('Beddingen','Der Bouvet e','Solheimsgaten','Inngang B','15','5058','BERGEN',true)");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Bystasjonen','')");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Åsane senter','')");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Byporten','')");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Ukjent sted','')");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Lots of levels','A rental composite that consists of areas from all levels.')");
$oProc->query("INSERT INTO rental_unit VALUES (1,1,'2711')");
$oProc->query("INSERT INTO rental_unit VALUES (1,2,'2712')");
$oProc->query("INSERT INTO rental_unit VALUES (1,6,'2717')");
$oProc->query("INSERT INTO rental_unit VALUES (1,10,'2721')");
$oProc->query("INSERT INTO rental_unit VALUES (2,4,'2714')");
$oProc->query("INSERT INTO rental_unit VALUES (2,5,'2716')");
$oProc->query("INSERT INTO rental_unit VALUES (3,6,'2717')");
$oProc->query("INSERT INTO rental_unit VALUES (3,10,'2721')");
$oProc->query("INSERT INTO rental_unit VALUES (4,14,'2726')");
$oProc->query("INSERT INTO rental_unit VALUES (4,16,'2730')");
$oProc->query("INSERT INTO rental_unit VALUES (5,20,'7179')");
$oProc->query("INSERT INTO rental_unit VALUES (5,22,'7183')");
$oProc->query("INSERT INTO rental_unit VALUES (6,515,'2104')"); // Level 2
$oProc->query("INSERT INTO rental_unit VALUES (7,1421,'1101')"); // Level 3
$oProc->query("INSERT INTO rental_unit VALUES (8,1389,'3409')"); // Level 4
$oProc->query("INSERT INTO rental_unit VALUES (9,1391,'3409')"); // Level 5
$oProc->query("INSERT INTO rental_unit VALUES (10,1,'2711')"); // Level 1
$oProc->query("INSERT INTO rental_unit VALUES (10,515,'2104')"); // Level 2
$oProc->query("INSERT INTO rental_unit VALUES (10,1421,'1101')"); // Level 3
$oProc->query("INSERT INTO rental_unit VALUES (10,1389,'3409')"); // Level 4
$oProc->query("INSERT INTO rental_unit VALUES (10,1391,'3409')"); // Level 5
$oProc->query("INSERT INTO rental_contract_type VALUES (1,'rental_contract_type_innleie','')");
$oProc->query("INSERT INTO rental_contract_type VALUES (2,'rental_contract_type_internleie','')");
$oProc->query("INSERT INTO rental_contract_type VALUES (3,'rental_contract_type_eksternleie','')");
$oProc->query("INSERT INTO rental_contract_type VALUES (4,'rental_contract_type_investeringskontrakt','')");
$oProc->query("INSERT INTO rental_billing_term VALUES (1,'Årlig','1')");
$oProc->query("INSERT INTO rental_billing_term VALUES (2,'Månedlig','12')");
$oProc->query("INSERT INTO rental_billing_term VALUES (3,'Halvår','12')");
$oProc->query("INSERT INTO rental_billing_term VALUES (4,'14. dag','12')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account) VALUES ('2009-01-01','2009-09-21','2009-01-15',3,2,'9710.13.8282')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account) VALUES ('2009-01-01','2020-12-12','2009-01-15',2,2,'9710.13.8283')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account) VALUES ('2008-01-01','2028-01-15','2008-01-15',1,2,'9710.13.8284')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account) VALUES ('2009-10-01','2029-10-15','2009-10-15',3,2,'9710.13.8285')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account) VALUES ('2009-09-21','2029-09-15','2009-09-15',4,2,'9710.13.8286')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account) VALUES ('2009-02-03','2029-02-15','2009-02-15',3,2,'9710.13.8287')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account) VALUES ('2009-08-12','2029-08-15','2009-08-15',3,2,'9710.13.8289')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account) VALUES ('2009-06-16','2029-06-15','2009-06-16',3,2,'9710.13.8228')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account) VALUES ('2009-06-01','2029-06-15','2009-06-15',3,2,'9710.13.8282')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account) VALUES ('2004-02-01','2024-02-02','2004-02-15',3,2,'3416.12.23289')");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (1,1)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (2,2)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (3,3)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (4,4)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (5,5)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (6,6)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (7,7)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (8,8)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (9,9)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (10,10)");
$oProc->query("INSERT INTO rental_party (personal_identification_number, first_name, last_name, is_active, address_1, postal_code, place) VALUES ('12345678901','Ola','Nordmann',true,'Bergensgt 5','5050','BERGEN')");
$oProc->query("INSERT INTO rental_party (personal_identification_number, first_name, last_name, is_active, address_1, postal_code, place) VALUES ('23456789012','Kari','Nordmann',true,'Nordnesgt 7','5020','BERGEN')");
$oProc->query("INSERT INTO rental_party (personal_identification_number, first_name, last_name, is_active, address_1, postal_code, place) VALUES ('34567890123','Per','Nordmann',true,'Solheimsviken 13','5008','BERGEN')");
$oProc->query("DELETE FROM phpgw_accounts WHERE account_id = 2000");
$oProc->query("DELETE FROM phpgw_accounts WHERE account_id = 2001");
$oProc->query("DELETE FROM phpgw_accounts WHERE account_id = 2002");
$oProc->query("DELETE FROM phpgw_accounts WHERE account_id = 2003");
$oProc->query("DELETE FROM phpgw_accounts WHERE account_id = 2004");
$oProc->query("DELETE FROM phpgw_accounts WHERE account_id = 2005");
$oProc->query("INSERT INTO phpgw_accounts (account_id,account_lid, account_pwd, account_firstname, account_lastname, account_status, account_expires, account_type,account_quota) VALUES (2000,'rental_backend_read_only','','rental_backend_read_only','Gruppe','A',-1,'g',-1)");
$oProc->query("INSERT INTO phpgw_accounts (account_id,account_lid, account_pwd, account_firstname, account_lastname, account_status, account_expires, account_type,account_quota) VALUES (2001,'rental_backend_write','','rental_backend_write','Gruppe','A',-1,'g',-1)");
$oProc->query("INSERT INTO phpgw_accounts (account_id,account_lid, account_pwd, account_firstname, account_lastname, account_status, account_expires, account_type,account_quota) VALUES (2002,'rental_administrator','','rental_administrator','Gruppe','A',-1,'g',-1)");
$oProc->query("INSERT INTO phpgw_accounts (account_id,account_lid, account_pwd, account_firstname, account_lastname, account_status, account_expires, account_type,account_quota) VALUES (2003,'rental_read','{SSHA}ZbIRWYpt3HmeA0bUWrfV7+2ZEe0Vuw==','Bouvet','Read only backend user','A',-1,'u',-1)");
$oProc->query("INSERT INTO phpgw_accounts (account_id,account_lid, account_pwd, account_firstname, account_lastname, account_status, account_expires, account_type,account_quota) VALUES (2004,'rental_write','{SSHA}jnwiYTpDgKeNcsPLTV5QQ0InqrhPeA==','Bouvet','Rental read and write','A',-1,'u',-1)");
$oProc->query("INSERT INTO phpgw_accounts (account_id,account_lid, account_pwd, account_firstname, account_lastname, account_status, account_expires, account_type,account_quota) VALUES (2005,'rental_admin','{SSHA}mrnNbEnfyIk/1kL9Y70z2B5Zb9UhYA==','Bouvet','Rental administrator','A',-1,'u',-1)");
$oProc->query("DELETE FROM phpgw_group_map WHERE account_id = 2003");
$oProc->query("DELETE FROM phpgw_group_map WHERE account_id = 2004");
$oProc->query("DELETE FROM phpgw_group_map WHERE account_id = 2005");
$oProc->query("INSERT INTO phpgw_group_map (group_id,account_id,arights) VALUES (2000,2003,1)");
$oProc->query("INSERT INTO phpgw_group_map (group_id,account_id,arights) VALUES (2001,2004,1)");
$oProc->query("INSERT INTO phpgw_group_map (group_id,account_id,arights) VALUES (2002,2005,1)");
$oProc->query("DELETE FROM phpgw_acl WHERE acl_account = 2000");
$oProc->query("DELETE FROM phpgw_acl WHERE acl_account = 2001");
$oProc->query("DELETE FROM phpgw_acl WHERE acl_account = 2002");
$oProc->query("INSERT INTO phpgw_acl (acl_account,acl_rights,acl_grantor, acl_type, location_id) VALUES (2000,1,-1,0,(SELECT location_id FROM phpgw_locations WHERE app_id = (SELECT app_id FROM phpgw_applications WHERE app_name LIKE 'rental')))");
$oProc->query("INSERT INTO phpgw_acl (acl_account,acl_rights,acl_grantor, acl_type, location_id) VALUES (2001,1,-1,0,(SELECT location_id FROM phpgw_locations WHERE app_id = (SELECT app_id FROM phpgw_applications WHERE app_name LIKE 'rental')))");
$oProc->query("INSERT INTO phpgw_acl (acl_account,acl_rights,acl_grantor, acl_type, location_id) VALUES (2002,1,-1,0,(SELECT location_id FROM phpgw_locations WHERE app_id = (SELECT app_id FROM phpgw_applications WHERE app_name LIKE 'rental')))");
$oProc->query("DELETE FROM phpgw_preferences WHERE preference_owner = 2003 AND preference_app LIKE 'common'");
$oProc->query("DELETE FROM phpgw_preferences WHERE preference_owner = 2004 AND preference_app LIKE 'common'");
$oProc->query("DELETE FROM phpgw_preferences WHERE preference_owner = 2005 AND preference_app LIKE 'common'");
$oProc->query("INSERT INTO phpgw_preferences VALUES (2003,'common','a:4:{s:9:\"maxmatchs\";s:2:\"50\";s:10:\"dateformat\";s:5:\"d.m.Y\";s:10:\"timeformat\";s:2:\"12\";s:4:\"lang\";s:2:\"no\";}')");
$oProc->query("INSERT INTO phpgw_preferences VALUES (2004,'common','a:4:{s:9:\"maxmatchs\";s:2:\"50\";s:10:\"dateformat\";s:5:\"d.m.Y\";s:10:\"timeformat\";s:2:\"12\";s:4:\"lang\";s:2:\"no\";}')");
$oProc->query("INSERT INTO phpgw_preferences VALUES (2005,'common','a:4:{s:9:\"maxmatchs\";s:2:\"50\";s:10:\"dateformat\";s:5:\"d.m.Y\";s:10:\"timeformat\";s:2:\"12\";s:4:\"lang\";s:2:\"no\";}')");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Fellesareal', '123456789', true, 34.59)");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Administrasjon', '473248234', true, 108.88)");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Parkeringsplass', '124246242', false, 50.00)");
