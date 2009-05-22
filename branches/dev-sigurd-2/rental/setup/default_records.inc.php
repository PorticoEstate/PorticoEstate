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
$oProc->query("INSERT INTO rental_unit VALUES (1,1)");
$oProc->query("INSERT INTO rental_unit VALUES (1,2)");
$oProc->query("INSERT INTO rental_unit VALUES (1,6)");
$oProc->query("INSERT INTO rental_unit VALUES (1,10)");
$oProc->query("INSERT INTO rental_unit VALUES (2,4)");
$oProc->query("INSERT INTO rental_unit VALUES (2,5)");
$oProc->query("INSERT INTO rental_unit VALUES (3,6)");
$oProc->query("INSERT INTO rental_unit VALUES (3,10)");
$oProc->query("INSERT INTO rental_unit VALUES (4,14)");
$oProc->query("INSERT INTO rental_unit VALUES (4,16)");
$oProc->query("INSERT INTO rental_unit VALUES (5,20)");
$oProc->query("INSERT INTO rental_unit VALUES (5,22)");