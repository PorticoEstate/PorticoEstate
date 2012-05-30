<?php
	/**
	* Notes - Setup
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package notes
	* @subpackage setup
	* @version $Id$
	*/

	/*
		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation; either version 3 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program.  If not, see <http://www.gnu.org/licenses/>.
	*/

	$test[] = '0.9.1';
	
	/**
	 * Upgrade from 0.9.1 to 0.9.2
	 * 
	 * @return string New version number
	 */
	function notes_upgrade0_9_1()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.2';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.2';
	function notes_upgrade0_9_2()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.3pre1';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.3pre1';
	function notes_upgrade0_9_3pre1()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.3pre2';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.3pre2';
	function notes_upgrade0_9_3pre2()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.3pre3';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.3pre3';
	function notes_upgrade0_9_3pre3()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.3pre4';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.3pre4';
	function notes_upgrade0_9_3pre4()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.3pre5';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.3pre5';
	function notes_upgrade0_9_3pre5()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.3pre6';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.3pre6';
	function notes_upgrade0_9_3pre6()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.3pre7';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.3pre7';
	function notes_upgrade0_9_3pre7()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.3pre8';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.3pre8';
	function notes_upgrade0_9_3pre8()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.3pre9';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.3pre9';
	function notes_upgrade0_9_3pre9()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.3pre10';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.3pre10';
	function notes_upgrade0_9_3pre10()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.3';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.3';
	function notes_upgrade0_9_3()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.4pre1';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}

	$test[] = '0.9.4pre1';
	function notes_upgrade0_9_4pre1()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'notes', array(
				'fd' => array(
					'note_id' => array('type' => 'auto', 'nullable' => false),
					'note_owner' => array('type' => 'int', 'precision' => 4),
					'note_date' => array('type' => 'int', 'precision' => 4),
					'note_content' => array('type' => 'text')
				),
				'pk' => array('note_id'),
				'ix' => array(),
				'fk' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.4pre2';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}

	$test[] = '0.9.4pre3';
	function notes_upgrade0_9_4pre3()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.4pre4';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.4pre4';
	function notes_upgrade0_9_4pre4()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.4pre5';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.4pre5';
	function notes_upgrade0_9_4pre5()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.4';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.4';
	function notes_upgrade0_9_4()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.5pre1';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.5pre1';
	function notes_upgrade0_9_5pre1()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.5pre2';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.5pre2';
	function notes_upgrade0_9_5pre2()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.5';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.5';
	function notes_upgrade0_9_5()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.6';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.6';
	function notes_upgrade0_9_6()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.7pre1';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.7pre1';
	function notes_upgrade0_9_7pre1()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.7pre2';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.7pre2';
	function notes_upgrade0_9_7pre2()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.7pre3';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.7pre3';
	function notes_upgrade0_9_7pre3()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.7';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.7';
	function notes_upgrade0_9_7()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.8pre1';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.8pre1';
	function notes_upgrade0_9_8pre1()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.8pre2';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.8pre2';
	function notes_upgrade0_9_8pre2()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.8pre3';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.8pre3';
	function notes_upgrade0_9_8pre3()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.8pre4';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.8pre4';
	function notes_upgrade0_9_8pre4()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.8pre5';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}

	$test[] = '0.9.8pre5';
	function notes_upgrade0_9_8pre5()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre11';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}

	$test[] = '0.9.9pre1';
	function notes_upgrade0_9_9pre1()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.9';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.9';
	function notes_upgrade0_9_9()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre1';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.10pre1';
	function notes_upgrade0_9_10pre1()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre2';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.10pre2';
	function notes_upgrade0_9_10pre2()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre3';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.10pre3';
	function notes_upgrade0_9_10pre3()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre4';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.10pre4';
	function notes_upgrade0_9_10pre4()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre5';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.10pre5';
	function notes_upgrade0_9_10pre5()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre6';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.10pre6';
	function notes_upgrade0_9_10pre6()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre7';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.10pre7';
	function notes_upgrade0_9_10pre7()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre8';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.10pre8';
	function notes_upgrade0_9_10pre8()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre9';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.10pre9';
	function notes_upgrade0_9_10pre9()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre10';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.10pre10';
	function notes_upgrade0_9_10pre10()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre11';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}

	$test[] = '0.9.10pre11';
	function notes_upgrade0_9_10pre11()
	{
		$GLOBALS['phpgw_setup']->oProc->RenameTable('notes','phpgw_notes');
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_notes','note_category',array('type' => 'int', 'precision' => 4));

		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre12';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}

	$test[] = '0.9.10pre12';
	function notes_upgrade0_9_10pre12()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre13';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}

	$test[] = '0.9.10pre13';
	function notes_upgrade0_9_10pre13()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre14';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.10pre14';
	function notes_upgrade0_9_10pre14()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre15';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.10pre15';
	function notes_upgrade0_9_10pre15()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre16';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.10pre16';
	function notes_upgrade0_9_10pre16()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre17';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.10pre17';
	function notes_upgrade0_9_10pre17()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre18';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.10pre18';
	function notes_upgrade0_9_10pre18()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre19';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.10pre19';
	function notes_upgrade0_9_10pre19()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre20';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.10pre20';
	function notes_upgrade0_9_10pre20()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre21';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.10pre21';
	function notes_upgrade0_9_10pre21()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre22';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.10pre22';
	function notes_upgrade0_9_10pre22()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre23';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.10pre23';
	function notes_upgrade0_9_10pre23()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre24';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.10pre24';
	function notes_upgrade0_9_10pre24()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre25';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.10pre25';
	function notes_upgrade0_9_10pre25()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre26';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.10pre26';
	function notes_upgrade0_9_10pre26()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre27';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.10pre27';
	function notes_upgrade0_9_10pre27()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10pre28';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.10pre28';
	function notes_upgrade0_9_10pre28()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.10';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.10';
	function notes_upgrade0_9_10()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.11.001';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.11';
	function notes_upgrade0_9_11()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.11.001';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.11.001';
	function notes_upgrade0_9_11_001()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.11.002';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.11.002';
	function notes_upgrade0_9_11_002()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.11.003';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.11.003';
	function notes_upgrade0_9_11_003()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.11.004';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.11.004';
	function notes_upgrade0_9_11_004()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.11.005';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.11.005';
	function notes_upgrade0_9_11_005()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.11.006';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.11.006';
	function notes_upgrade0_9_11_006()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.11.007';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.11.007';
	function notes_upgrade0_9_11_007()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.11.008';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.11.008';
	function notes_upgrade0_9_11_008()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.11.009';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.11.009';
	function notes_upgrade0_9_11_009()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.11.010';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.11.010';
	function notes_upgrade0_9_11_010()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.11.011';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
	$test[] = '0.9.11.011';
	function notes_upgrade0_9_11_011()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.13.001';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}

	$test[] = '0.9.13.001';
	function notes_upgrade0_9_13_001()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_notes','note_access', array('type' => 'varchar', 'precision' => 7));

		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.13.002';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}

	$test[] = '0.9.13.002';
	function notes_upgrade0_9_13_002()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.15.001';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}

	$test[] = '0.9.14';
	function notes_upgrade0_9_14()
	{
		$GLOBALS['setup_info']['notes']['currentver'] = '0.9.15.001';
		return $GLOBALS['setup_info']['notes']['currentver'];
	}


	$test[] = '0.9.15.001';
	function notes_upgrade0_9_15_001()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_notes','note_lastmod',array(
			'type' => 'int',
			'precision' => '8',
			'nullable' => False
		));

		$GLOBALS['phpgw_setup']->oProc->m_odb->query('UPDATE phpgw_notes SET note_lastmod = note_date');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['notes']['currentver'] = '0.9.15.002';
		}
		return $GLOBALS['setup_info']['notes']['currentver'];
	}
?>
