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
$oProc->query("INSERT INTO rental_contract_composite VALUES (1,1,1)");
$oProc->query("INSERT INTO rental_contract_composite VALUES (2,2,2)");
$oProc->query("INSERT INTO rental_contract_composite VALUES (3,3,3)");
$oProc->query("INSERT INTO rental_contract_composite VALUES (4,4,4)");
$oProc->query("INSERT INTO rental_contract_composite VALUES (5,5,5)");
$oProc->query("INSERT INTO rental_contract_composite VALUES (6,6,6)");
$oProc->query("INSERT INTO rental_contract_composite VALUES (7,7,7)");
$oProc->query("INSERT INTO rental_contract_composite VALUES (8,8,8)");
$oProc->query("INSERT INTO rental_contract_composite VALUES (9,9,9)");
$oProc->query("INSERT INTO rental_contract_composite VALUES (10,10,10)");
