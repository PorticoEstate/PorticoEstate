<?php
/**************************************************************************\
* phpGroupWare - fax                                                       *
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

/* $Id$ */

# ToDo: check dim
$prefs = array
  (
   'fd'	=>	array
   (
	'faxuser'	=>	array
	(
	 'type'	=>	'varchar',
	 'precision'	=>	255,
	 'nullable'	=>	false
	 ),
	'prefs' => array
	(
	 'type'	=>	'text',
	 'nullable'	=>	false
	 ),
	),
   'pk' => array(),
   'fk' => array(),
   'ix' => array(),
   'uc' => array()
   );



$admin = array
  (
   'fd'	=>	array
   (
	'global_settings'	=>	array
	(
	 'type'	=>	'text',
	 'nullable'	=>	false
	 ),
	),
   'pk' => array(),
   'fk' => array(),
   'ix' => array(),
   'uc' => array()
   );

$phpgw_baseline = array
  (
   'phpgw_fax_prefs'	=>	$prefs,
   'phpgw_fax_admin'	=>	$admin
   );
?>
	
