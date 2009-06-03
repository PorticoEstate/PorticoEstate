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
