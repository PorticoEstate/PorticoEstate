<?php
/*
 * $Horde: chora/config/cvsroots.php.dist,v 1.1 2001/01/16 19:05:18 avsm Exp $
 *
 * This file contains all the configuration information for the various
 * CVS repositories that you wish to display.  You should a minimum of
 * one entry here!  The following fields are allowed in the description,
 * and those with a [M] are Mandatory, and should not be left out.
 *
 * 'name'     [M] : Short name for the repository
 *
 * 'location' [M} : Location on the filesystem of the CVSROOT
 * 
 * 'title'    [M} : Long title for the repository
 *
 * 'default'      : To make that repository the default one to show
 *
 * 'intro'        : File which contains some introductory text to show
 *                  on the front page of this repository.  This file is
 *                  located in the config/ directory.
 *
 * 'cvsusers'     : A list of all committers with real names and email
 *                  addresses, that normally sits in the CVSROOT/cvsusers
 *                  file.  If it is found, then more useful information
 *                  will be shown.
*/

	$cvsroots = array();

	$GLOBALS['phpgw']->db->query("SELECT * FROM phpgw_chora_sites");
	while ($GLOBALS['phpgw']->db->next_record())
	{
		if ($GLOBALS['phpgw']->db->f('name'))
		{
			$cvsroots[$GLOBALS['phpgw']->db->f('name')] = array(
				'name'     => $GLOBALS['phpgw']->db->f('name'),
				'location' => $GLOBALS['phpgw']->db->f('location'),
				'title'    => $GLOBALS['phpgw']->db->f('title')
			);
			if ($GLOBALS['phpgw']->db->f('intro'))
			{
				$cvsroots[$GLOBALS['phpgw']->db->f('name')]['intro'] = $GLOBALS['phpgw']->db->f('intro');
			}
			if ($GLOBALS['phpgw']->db->f('is_default'))
			{
				$cvsroots[$GLOBALS['phpgw']->db->f('name')]['default'] = True;
			}
		}
	}
?>
