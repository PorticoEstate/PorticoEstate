<?php
	/**
	* phpGroupWare - CATCH: An application for importing data from handhelds into property.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2009 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package catch
	* @subpackage setup
 	* @version $Id: tables_update.inc.php 1956 2008-12-14 14:10:10Z sigurd $
	*/

	/**
	* Update CATCH version from 0.9.17.001 to 0.9.17.502
	*/

	$test[] = '0.9.17.001';
	function catch_upgrade0_9_17_001()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_catch_config_type',array(
				'fd' => array(
					'id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'name' => array('type' => 'varchar', 'precision' => 50,'nullable' => False),
					'descr' => array('type' => 'varchar', 'precision' => 200,'nullable' => true)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_catch_config_attrib',array(
				'fd' => array(
					'type_id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'input_type' => array('type' => 'varchar', 'precision' => 10,'nullable' => False),
					'name' => array('type' => 'varchar', 'precision' => 50,'nullable' => False),
					'descr' => array('type' => 'varchar', 'precision' => 200,'nullable' => true)
				),
				'pk' => array('type_id','id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_catch_config_choice',array(
				'fd' => array(
					'type_id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'attrib_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'value' => array('type' => 'varchar', 'precision' => 50,'nullable' => False)
				),
				'pk' => array('type_id','attrib_id','id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('type_id','attrib_id','value')
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_catch_config_value',array(
				'fd' => array(
					'type_id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'attrib_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'value' => array('type' => 'varchar', 'precision' => 200,'nullable' => False)
				),
				'pk' => array('type_id','attrib_id','id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('type_id','attrib_id','value')
			)
		);


		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['catch']['currentver'] = '0.9.17.502';
			return $GLOBALS['setup_info']['catch']['currentver'];
		}
	}

