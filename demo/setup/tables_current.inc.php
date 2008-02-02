<?php
	/**
	* phpGroupWare - DEMO: A demo application.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package demo
	* @subpackage setup
 	* @version $Id$
	*/

	$phpgw_baseline = array(
		'phpgw_demo_table' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'char_type' => array('type' => 'char','precision' => '10','nullable' => True),
				'time' => array('type' => 'timestamp','nullable' => True,'default' => 'current_timestamp'),
				'name' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'address' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'zip' => array('type' => 'int','precision' => '4','nullable' => True),
				'town' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'remark' => array('type' => 'text','nullable' => True),
				'category' => array('type' => 'int','precision' => '4','nullable' => True),
				'access' => array('type' => 'varchar', 'precision' => '7','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
