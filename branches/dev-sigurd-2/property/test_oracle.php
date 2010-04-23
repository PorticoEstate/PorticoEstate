<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2009 Free Software Foundation, Inc. http://www.fsf.org/
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
 	* @version $Id$
	*/

	/**
	 * Test script for generating XML with attributes
	 *
	 */

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'currentapp'	=> 'property'
	);

		$GLOBALS['phpgw_info']['flags']['noheader'] = true;
		$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
		$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

	include_once('../header.inc.php');

// Start-------------------------------------------------

	$db = createObject('phpgwapi.db', null, null, true);

//	$db->Debug = true;

/*
	$db->Host = '10.11.12.40';
	$db->Type = 'oci8';
	$db->Database = 'FELTEST';
	$db->User = 'FELLES1';
	$db->Password = 'enkel';
*/

	$db->Host = '10.16.14.5';
	$db->Type = 'oci8';
	$db->Database = 'INSTEST';
	$db->User = 'fellesdata';
	$db->Password = 'fellesdata';

	$db->connect();

//lister alle tabller og views
	_debug_array($db->table_names(true));

	$table1 = 'V_ORG_ENHET';
	$table2 = 'V_ORG_ENHET_ENDRINGER';
 	$table3 = 'V_ORG_PERSON';
	$table4 = 'V_ORG_PERSON_ENDRINGER';
 	$table5 = 'V_ORG_PERSON_ENHET';
	$table6 = 'V_ORG_PERSON_ENHET_ENDRINGER';
	$table7 = 'V_AGRESSO_KUNDE_ADRESSE';
	
//	$sql = "SELECT * FROM $table7 WHERE 1=1";
	$sql = "SELECT * FROM $table3 WHERE 1=1 ORDER BY ETTERNAVN ASC";// AND org_nivaa = 1";
	
//	$sql = "SELECT * FROM $table5 JOIN $table1 ON $table1.ORG_ENHET_ID = $table5.ORG_ENHET_ID JOIN $table3 ON $table5.ORG_PERSON_ID = $table3.ORG_PERSON_ID WHERE 1=1 AND org_nivaa = 1";
//	$sql = "SELECT $table3.NAVN FROM $table5 JOIN $table1 ON $table1.ORG_ENHET_ID = $table5.ORG_ENHET_ID JOIN $table3 ON $table5.ORG_PERSON_ID = $table3.ORG_PERSON_ID WHERE 1=1 AND $table1.ORG_ENHET_ID = 1";


	$start = 0;

	$limit = false;
//	$limit = true;
	if($limit)
	{
		$db->limit_query($sql,$start,__LINE__,__FILE__);
	}
	else
	{
		$db->query($sql,__LINE__,__FILE__);
	}

//_debug_array($db->num_rows());

	$values = array();
	while ($db->next_record())
	{
		$values[] = $db->Record;
	}

	_debug_array($values);
	
	
//	_debug_array($db->table_names(true)); die();

// Slutt-------------------------------

// last ned til excel (alle andre out-put må kanselleres)
/*
	$name = $descr = array_keys($values[0]);
	$bocommon = createObject('property.bocommon');
	$bocommon->download($values,$name,$descr);
*/
	$GLOBALS['phpgw']->common->phpgw_exit();


