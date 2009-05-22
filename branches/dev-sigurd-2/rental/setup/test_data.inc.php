<?php
/**
 * Holds the queries inserting test data:
 * 
 * $oProc->query("sql_statement");
 * 
 */
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Herdla fuglereservat','Pip pip')");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Fl¿ibanen','Tut tut')");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Perle og Bruse','')");
$oProc->query("INSERT INTO rental_composite (name,description,is_active) VALUES ('Store LungegŒrdsvannet','',false)");
$oProc->query("INSERT INTO rental_composite (name,description,address_1,address_2,house_number,postcode,place,has_custom_address) VALUES ('Beddingen','Der Bouvet e','Solheimsgaten','Inngang B','15','5058','BERGEN',true)");