<?php
	/**
	* phpGroupWare EMail - http://phpGroupWare.org
	*
	* @author Angles <angles@phpgroupware.org>
	* @copyright Copyright (C) 2001-2004 Angelo Tony Puglisi
	* @copyright Portions Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @subpackage setup
	* @version $Id$
	* @internal Based on AeroMail by Mark Cushman <mark@cushman.net>
	*/

	$test[] = '0.9.13.002';
	/**
	* Update from 0.9.13.002 to 0.9.13.003
	*
	* @return string New version number
	*/
	function email_upgrade0_9_13_002()
	{
		global $setup_info, $phpgw_setup;

		$phpgw_setup->oProc->CreateTable(
			'phpgw_anglemail', array(
				'fd' => array(
					'account_id' => array('type' => 'varchar', 'precision' => 20, 'nullable' => false),
					'data_key' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False, 'default' => ''),
					'content' => array('type' => 'text', 'nullable' => False, 'default' => ''),
				),
				'pk' => array('account_id', 'data_key'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);
		return $GLOBALS['setup_info']['email']['currentver'] = '0.9.13.003';
	}
	
	$test[] = '0.9.13.003';
	function email_upgrade0_9_13_003()
	{
		return $GLOBALS['setup_info']['email']['currentver']= '0.9.17.500';
	}
?>
