<?php
/**************************************************************************\
* phpGroupWare - Antispam                                                  *
* http://www.phpgroupware.org                                              *
* This application written by:                                             *
*                             Marco Andriolo-Stagno <stagno@prosa.it>      *
*                             PROSA <http://www.prosa.it>                  *
* -------------------------------------------------------------------------*
* Funding for this program was provided by http://www.seeweb.com           *
* -------------------------------------------------------------------------*
*  This program is free software; you can redistribute it and/or modify it *
*  under the terms of the GNU General Public License as published by the   *
*  Free Software Foundation; either version 2 of the License, or (at your  *
*  option) any later version.                                              *
\**************************************************************************/

 /* $Id: tables_current.inc.php 11580 2002-11-26 17:57:08Z ceb $ */

	$phpgw_baseline = array
	(
		'phpgw_antispam'	=>	array
		(
			'fd'	=>	array
			(
				'username'	=>	array
			 	(
				   	'type'	=>	'varchar',
			   		'precision'	=>	100,
		   		 	'nullable'	=>	false,
	   			 	'key'	=>	true
				 ),
			 
			 	'preference' => array
			 	(
				 	'type'	=>	'varchar',
				 	'precision'	=>	30,
				 	'nullable'	=>	false
				 ),
			 
				'value' => array
			 	(
				 	'type'	=>	'varchar',
				 	'precision'	=>	100,
				 	'nullable'	=>	false
				 ),
			 
				'prefid' => array
			 	(
			  		'type'	=>	'auto',
				 	'nullable'	=>	false
				 ),
			 ),
		 	'pk' => array('prefid'),
		 	'fk' => array(),
		 	'ix' => array('username'), 
		 	'uc' => array()
		 )
	 );
#ToDO: index on username?
?>
