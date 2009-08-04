<?php
/**
 * Holds the queries inserting default data (not test data):
 * 
 * $oProc->query("sql_statement");
 * 
 */

//Create groups, users, add users to groups and set preferences
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
$oProc->query("DELETE FROM phpgw_preferences WHERE preference_owner = 2003 AND preference_app LIKE 'common'");
$oProc->query("DELETE FROM phpgw_preferences WHERE preference_owner = 2004 AND preference_app LIKE 'common'");
$oProc->query("DELETE FROM phpgw_preferences WHERE preference_owner = 2005 AND preference_app LIKE 'common'");
$oProc->query("INSERT INTO phpgw_preferences VALUES (2003,'common','a:4:{s:9:\"maxmatchs\";s:2:\"50\";s:10:\"dateformat\";s:5:\"d.m.Y\";s:10:\"timeformat\";s:2:\"12\";s:4:\"lang\";s:2:\"no\";}')");
$oProc->query("INSERT INTO phpgw_preferences VALUES (2004,'common','a:4:{s:9:\"maxmatchs\";s:2:\"50\";s:10:\"dateformat\";s:5:\"d.m.Y\";s:10:\"timeformat\";s:2:\"12\";s:4:\"lang\";s:2:\"no\";}')");
$oProc->query("INSERT INTO phpgw_preferences VALUES (2005,'common','a:4:{s:9:\"maxmatchs\";s:2:\"50\";s:10:\"dateformat\";s:5:\"d.m.Y\";s:10:\"timeformat\";s:2:\"12\";s:4:\"lang\";s:2:\"no\";}')");

//Default rental composites
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
	// Vitalitetssenteret
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Vitalitetssenteret','')");
	// Gullstøltunet sykehjem
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Gullstøltunet sykehjem','')");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Gullstøltunet sykehjem - Bosshus/Trafo','')");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Gullstøltunet sykehjem - Pumpehus','')");
	// Bergen Rådhus
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Bergen Rådhus Nye','')");

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
	// Vitalitetssenteret
$oProc->query("INSERT INTO rental_unit VALUES (11,1096,'5807')");
	// Gullstøltunet sykehjem
$oProc->query("INSERT INTO rental_unit VALUES (12,1026,'3409')");
$oProc->query("INSERT INTO rental_unit VALUES (13,1027,'3409')");
$oProc->query("INSERT INTO rental_unit VALUES (14,1028,'3409')");
	// Bergen Rådhus
$oProc->query("INSERT INTO rental_unit VALUES (15,468,'7183')");

$oProc->query("INSERT INTO rental_contract_type (title, description) VALUES ('rental_contract_type_innleie','')");
$oProc->query("INSERT INTO rental_contract_type (title, description) VALUES ('rental_contract_type_internleie','')");
$oProc->query("INSERT INTO rental_contract_type (title, description) VALUES ('rental_contract_type_eksternleie','')");
$oProc->query("INSERT INTO rental_contract_type (title, description) VALUES ('rental_contract_type_investeringskontrakt','')");

$oProc->query("INSERT INTO rental_billing_term (title, runs_a_year) VALUES ('Årlig','1')");
$oProc->query("INSERT INTO rental_billing_term (title, runs_a_year) VALUES ('Halvår','2')");
$oProc->query("INSERT INTO rental_billing_term (title, runs_a_year) VALUES ('Kvartal','4')");
$oProc->query("INSERT INTO rental_billing_term (title, runs_a_year) VALUES ('Månedlig','12')");
$oProc->query("INSERT INTO rental_billing_term (title, runs_a_year) VALUES ('14. dag','24')");

$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account, executive_officer,last_edited, last_edited_by, created, created_by) VALUES ('2009-01-01','2009-09-21','2009-01-15',3,2,'9710.13.8282', 2004,'2009-01-01', 2004, '2009-01-01', 2004)");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account, executive_officer,last_edited, last_edited_by, created, created_by) VALUES ('2009-01-01','2020-12-12','2009-01-15',2,2,'9710.13.8283', 2004,'2009-01-01', 2004, '2009-01-01', 2004)");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account, executive_officer,last_edited, last_edited_by, created, created_by) VALUES ('2008-01-01','2028-01-15','2008-01-15',1,2,'9710.13.8284', 2004,'2009-01-01', 2004, '2009-01-01', 2004)");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account, executive_officer,last_edited, last_edited_by, created, created_by) VALUES ('2009-10-01','2029-10-15','2009-10-15',3,2,'9710.13.8285', 2004,'2009-01-01', 2004, '2009-01-01', 2004)");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account, executive_officer,last_edited, last_edited_by, created, created_by) VALUES ('2009-09-21','2029-09-15','2009-09-15',4,2,'9710.13.8286', 2004,'2009-01-01', 2004, '2009-01-01', 2004)");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account, executive_officer,last_edited, last_edited_by, created, created_by) VALUES ('2009-02-03','2029-02-15','2009-02-15',3,2,'9710.13.8287', 2005,'2009-01-01', 2004, '2009-01-01', 2004)");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account, executive_officer,last_edited, last_edited_by, created, created_by) VALUES ('2009-08-12','2029-08-15','2009-08-15',3,2,'9710.13.8289', 2005,'2009-01-01', 2004, '2009-01-01', 2004)");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account, executive_officer,last_edited, last_edited_by, created, created_by) VALUES ('2009-06-16','2029-06-15','2009-06-16',3,2,'9710.13.8228', 2005,'2009-01-01', 2004, '2009-01-01', 2004)");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account, executive_officer,last_edited, last_edited_by, created, created_by) VALUES ('2009-06-01','2029-06-15','2009-06-15',3,2,'9710.13.8282', 2005,'2009-01-01', 2004, '2009-01-01', 2004)");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account, executive_officer,last_edited, last_edited_by, created, created_by) VALUES ('2004-02-01','2024-02-02','2004-02-15',3,2,'3416.12.23289', 2005,'2009-01-01', 2004, '2009-01-01', 2004)");
	// Vitalitetssenteret
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account, executive_officer,last_edited, last_edited_by, created, created_by, old_contract_id) VALUES ('2003-02-12',NULL,'2005-01-01',2,4,NULL, 2005,'2005-12-06', 2005, '2009-07-27', 2005, 'K00000659')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account, executive_officer,last_edited, last_edited_by, created, created_by, old_contract_id) VALUES ('2003-03-18',NULL,'2005-01-01',2,4,NULL, 2005,'2005-12-06', 2005, '2009-07-27', 2005, 'K00000660')");
	// Gullstøltunet sykehjem
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account, executive_officer,last_edited, last_edited_by, created, created_by, old_contract_id) VALUES ('1999-01-01',NULL,'2005-01-01',2,4,NULL, 2005,'2005-12-20', 2005, '2009-07-28', 2005, ' K00000585')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account, executive_officer,last_edited, last_edited_by, created, created_by, old_contract_id) VALUES ('1999-01-01',NULL,'2005-01-01',2,4,NULL, 2005,'2005-06-27', 2005, '2009-07-28', 2005, ' K00000586')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account, executive_officer,last_edited, last_edited_by, created, created_by, old_contract_id) VALUES ('1999-01-01',NULL,'2005-01-01',2,4,NULL, 2005,'2005-06-27', 2005, '2009-07-28', 2005, ' K00000587')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account, executive_officer,last_edited, last_edited_by, created, created_by, old_contract_id) VALUES ('2006-01-01',NULL,'2006-01-01',2,4,NULL, 2005,'2005-12-20', 2005, '2009-07-28', 2005, ' K00006497')");
	// Bergen Rådhus
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account, executive_officer,last_edited, last_edited_by, created, created_by, old_contract_id) VALUES ('2008-01-01',NULL,'2005-01-01',2,4,NULL, 2005,'2008-11-28', 2005, '2009-07-28', 2005, ' K00000797')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, type_id, term_id, account, executive_officer,last_edited, last_edited_by, created, created_by, old_contract_id) VALUES ('2005-01-01',NULL,'2005-01-01',2,4,NULL, 2005,'2006-06-13', 2005, '2009-07-28', 2005, ' K00000798')");
	
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
	// Vitalitetssenteret
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (11,11)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (12,11)");
	// Gullstøltunet sykehjem
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (13,12)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (14,13)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (15,14)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (16,12)");
	// Bergen Rådhus
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (17,15)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (18,15)");
	
$oProc->query("INSERT INTO rental_party (personal_identification_number, first_name, last_name, is_active, address_1, postal_code, place) VALUES ('12345678901','Ola','Nordmann',true,'Bergensgt 5','5050','BERGEN')");
$oProc->query("INSERT INTO rental_party (personal_identification_number, first_name, last_name, is_active, address_1, postal_code, place) VALUES ('23456789012','Kari','Nordmann',true,'Nordnesgt 7','5020','BERGEN')");
$oProc->query("INSERT INTO rental_party (personal_identification_number, first_name, last_name, is_active, address_1, postal_code, place) VALUES ('34567890123','Per','Nordmann',true,'Solheimsviken 13','5008','BERGEN')");
	// Vitalitetssenteret
$oProc->query("INSERT INTO rental_party (personal_identification_number, first_name, last_name, company_name, department, email, account_number, is_active) VALUES ('R0443','Åge','Nilssen','IDRETT Sentrum sør','Byrådsavdeling for oppvekst','ar564@bergen.kommune.no','R0443',true)");
$oProc->query("INSERT INTO rental_party (personal_identification_number, first_name, last_name, company_name, department, email, account_number, is_active) VALUES ('R0956','Berit','Tande','Bergenhus og Årstad kulturkontor','Byrådsavd. for kultur, næring og idrett','wb902@bergen.kommune.no','R0956',true)");
	// Gullstøltunet sykehjem
$oProc->query("INSERT INTO rental_party (personal_identification_number, first_name, last_name, company_name, department, email, account_number, is_active) VALUES ('R7552','Anna Milde','Thorbjørnsen','Gullstøltunet','Byrådsavd. for helse og omsorg','vk172@bergen.kommune.no','R7552',true)");
$oProc->query("INSERT INTO rental_party (personal_identification_number, first_name, last_name, company_name, address_1, postal_code, place, phone, email, is_active) VALUES ('KF06','Øyvind','Berggreen','Gullstøltunet kjøkken','Øvre Kråkenes 111','5152','Bønes','55929846/48','vm152@bergen.kommune.no',true)");
	// Bergen Rådhus
$oProc->query("INSERT INTO rental_party (personal_identification_number, first_name, last_name, company_name, department, email, is_active) VALUES ('R0401','Anne-Marit','Presterud','Gullstøltunet kjøkken','Byrådsavd. for barnehage og skole','jf684@bergen.kommune.no',true)");
$oProc->query("INSERT INTO rental_party (personal_identification_number, first_name, last_name, company_name, department, email, account_number, is_active) VALUES ('R0300','Jan-Petter','Stoutland','BHO - Kommunaldirektørens stab','Byrådsavd. for helse og omsorg','gs256@bergen.kommune.no','R0300',true)");

	// Vitalitetssenteret
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (11, 4, true)");
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (12, 5, true)");
	// Gullstøltunet sykehjem
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (13, 6, true)");
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (14, 6, true)");
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (15, 6, true)");
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (16, 7, true)");
	// Bergen Rådhus
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (17, 8, true)");
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (18, 9, true)");

$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Fellesareal', '123456789', true, 34.59)");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Administrasjon', 'Y900', true, 23.00)");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Parkeringsplass', '124246242', false, 50.00)");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Forsikring', 'Y901', true, 10.00)");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Kapitalkostnad', 'Y904', true, 700.00)");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Kom.avg. uten renovasjon', 'Y902', true, 32.29)");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Renovasjon', 'Y903', true, 10.94)");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Vedlikehold', 'Y905', true, 98.23)");

	// Vitalitetssenteret
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (2, 11, 'Administrasjon', 1712, 0, 'Y900', true, 23.98, 41053.76, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (4, 11, 'Forsikring', 1712, 0, 'Y901', true, 10.57, 18095.84, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (5, 11, 'Kapitalkostnad', 1712, 0, 'Y904', true, 759.85, 1300863.20, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (6, 11, 'Kom.avg. uten renovasjon', 1712, 0, 'Y902', true, 32.29, 55280.48, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (7, 11, 'Renovasjon', 1712, 0, 'Y903', true, 10.94, 18729.28, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (8, 11, 'Vedlikehold', 1712, 0, 'Y905', true, 98.23, 168169.76, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (2, 12, 'Administrasjon', 1158, 0, 'Y900', true, 23.98, 27768.84, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (4, 12, 'Forsikring', 1158, 0, 'Y901', true, 10.57, 12240.06, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (5, 12, 'Kapitalkostnad', 1158, 0, 'Y904', true, 702.34, 813309.72, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (6, 12, 'Kom.avg. uten renovasjon', 1158, 0, 'Y902', true, 32.29, 37391.82, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (7, 12, 'Renovasjon', 1158, 0, 'Y903', true, 10.94, 12668.52, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (8, 12, 'Vedlikehold', 1158, 0, 'Y905', true, 98.23, 113750.34, '2009-01-01', NULL)");
	// Gullstøltunet sykehjem
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (2, 13, 'Administrasjon', 7039, 0, 'Y900', true, 23.98, 168795.22, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (4, 13, 'Forsikring', 7039, 0, 'Y901', true, 10.57, 74402.23, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (5, 13, 'Kapitalkostnad', 7039, 0, 'Y904', true, 835.69, 5882421.91, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (8, 13, 'Vedlikehold', 7039, 0, 'Y905', true, 98.23, 691440.97, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (2, 14, 'Administrasjon', 53, 0, 'Y900', true, 23.98, 1270.94, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (4, 14, 'Forsikring', 53, 0, 'Y901', true, 10.57, 560.21, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (5, 14, 'Kapitalkostnad', 53, 0, 'Y904', true, 44291.57, 5882421.91, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (8, 14, 'Vedlikehold', 53, 0, 'Y905', true, 98.23, 5206.19, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (2, 15, 'Administrasjon', 13, 0, 'Y900', true, 23.98, 311.74, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (4, 15, 'Forsikring', 13, 0, 'Y901', true, 10.57, 137.41, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (5, 15, 'Kapitalkostnad', 13, 0, 'Y904', true, 10863.97, 5882421.91, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (8, 15, 'Vedlikehold', 13, 0, 'Y905', true, 98.23, 1276.99, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (2, 16, 'Administrasjon', 360, 0, 'Y900', true, 23.98, 8632.80, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (4, 16, 'Forsikring', 360, 0, 'Y901', true, 10.57, 3805.20, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (5, 16, 'Kapitalkostnad', 360, 0, 'Y904', true, 835.69, 300848.40, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (8, 16, 'Vedlikehold', 360, 0, 'Y905', true, 98.23, 35362.80, '2009-01-01', NULL)");
	// Bergen Rådhus
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (2, 17, 'Administrasjon', 792.3, 0, 'Y900', true, 23.27, 18436.82, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (4, 17, 'Forsikring', 792.3, 0, 'Y901', true, 10.25, 8121.08, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (5, 17, 'Kapitalkostnad', 792.3, 0, 'Y904', true, 1042.95, 826329.29, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (6, 17, 'Kom.avg. uten renovasjon', 792.3, 0, 'Y902', true, 32.29, 25583.37, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (7, 17, 'Renovasjon', 792.3, 0, 'Y903', true, 10.94, 8667.76, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (8, 17, 'Vedlikehold', 792.3, 0, 'Y905', true, 95.28, 75490.34, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (2, 18, 'Administrasjon', 1160.4, 0, 'Y900', true, 23.98, 27826.39, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (4, 18, 'Forsikring', 1160.4, 0, 'Y901', true, 10.57, 12265.43, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (5, 18, 'Kapitalkostnad', 1160.4, 0, 'Y904', true, 1075.18, 1247638.87, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (6, 18, 'Kom.avg. uten renovasjon', 1160.4, 0, 'Y902', true, 32.29, 37469.32, '2005-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (7, 18, 'Renovasjon', 1160.4, 0, 'Y903', true, 10.94, 12694.78, '2005-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (8, 18, 'Vedlikehold', 1160.4, 0, 'Y905', true, 98.23, 113986.09, '2009-01-01', NULL)");

$oProc->query("INSERT INTO rental_contract_last_edited VALUES (2,2004,'2009-07-28')");
$oProc->query("INSERT INTO rental_contract_last_edited VALUES (1,2005,'2009-07-28')");
$oProc->query("INSERT INTO rental_contract_last_edited VALUES (3,2004,'2009-07-28')");

$oProc->query("INSERT INTO rental_notification (user_id, contract_id, message, date, dismissed, recurrence) VALUES (2005,11,'Oppdatér leietaker med ny postadresse.','".date('Y-m-d')."',0,0)");
$oProc->query("INSERT INTO rental_notification (user_id, contract_id, message, date, dismissed, recurrence) VALUES (2005,13,'Leietaker tilbake fra ferie. Følg opp e-post sendt ut for to uker siden.','".date('Y-m-d')."',0,0)");
$oProc->query("INSERT INTO rental_notification (user_id, contract_id, message, date, dismissed, recurrence) VALUES (2005,15,'Kontrollér at priselementer er i henhold.','".date('Y-m-d')."',0,0)");
$oProc->query("INSERT INTO rental_notification (user_id, contract_id, message, date, dismissed, recurrence) VALUES (2005,17,'Oppdatér med ny postadresse.','".date('Y-m-d')."',0,0)");
$oProc->query("INSERT INTO rental_notification (user_id, contract_id, message, date, dismissed, recurrence) VALUES (2005,18,'Oppdatér med ny postadresse.','".date('Y-m-d')."',0,0)");

$oProc->query("DELETE FROM phpgw_acl WHERE acl_account = 2000");
$oProc->query("DELETE FROM phpgw_acl WHERE acl_account = 2001");
$oProc->query("DELETE FROM phpgw_acl WHERE acl_account = 2002");

$oProc->query("INSERT INTO phpgw_acl (acl_account,acl_rights,acl_grantor, acl_type, location_id) VALUES (2000,1,-1,0,(SELECT location_id FROM phpgw_locations WHERE app_id = (SELECT app_id FROM phpgw_applications WHERE app_name LIKE 'rental')))");
$oProc->query("INSERT INTO phpgw_acl (acl_account,acl_rights,acl_grantor, acl_type, location_id) VALUES (2001,1,-1,0,(SELECT location_id FROM phpgw_locations WHERE app_id = (SELECT app_id FROM phpgw_applications WHERE app_name LIKE 'rental')))");
$oProc->query("INSERT INTO phpgw_acl (acl_account,acl_rights,acl_grantor, acl_type, location_id) VALUES (2002,1,-1,0,(SELECT location_id FROM phpgw_locations WHERE app_id = (SELECT app_id FROM phpgw_applications WHERE app_name LIKE 'rental')))");
$oProc->query("INSERT INTO phpgw_acl (acl_account,acl_rights,acl_grantor, acl_type, location_id) VALUES (2002,1,-1,0,(SELECT location_id FROM phpgw_locations WHERE app_id = (SELECT app_id FROM phpgw_applications WHERE app_name LIKE 'admin')))");
