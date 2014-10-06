<?php
	/**
	* Todo - setup
	*
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package todo
	* @subpackage setup
	* @version $Id$
	*/

	/**
	 * Update from 0.9.2 to 0.9.3
	 * 
	 * @param string $table Table name
	 * @param string $field Field name
	 */
	function todo_v0_9_2to0_9_3update_owner($table, $field)
	{
		global $phpgw_setup;

		$phpgw_setup->oProc->query("select distinct($field) from $table");
		if ($phpgw_setup->oProc->num_rows())
		{
			while ($phpgw_setup->oProc->next_record())
			{
				$owner[count($owner)] = $phpgw_setup->oProc->f($field);
			}
			if($phpgw_setup->alessthanb($setup_info['phpgwapi']['currentver'],'0.9.10pre4'))
			{
				$acctstbl = 'accounts';
			}
			else
			{
				$acctstbl = 'phpgw_accounts';
			}
			for($i=0;$i<count($owner);$i++)
			{
				$phpgw_setup->oProc->query("SELECT account_id FROM $acctstbl WHERE account_lid='".$owner[$i]."'");
				$phpgw_setup->oProc->next_record();
				$phpgw_setup->oProc->query("UPDATE $table SET $field=".$phpgw_setup->oProc->f("account_id")." WHERE $field='".$owner[$i]."'");
			}
		}
		$phpgw_setup->oProc->AlterColumn($table, $field, array('type' => 'int', 'precision' => 4, 'nullable' => false, 'default' => 0));
	}

	$test[] = '0.9.1';
	function todo_upgrade0_9_1()
	{
		global $setup_info;

		$setup_info['todo']['currentver'] = '0.9.2';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.2';
	function todo_upgrade0_9_2()
	{
		global $setup_info;

		$setup_info['todo']['currentver'] = '0.9.3pre1';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.3pre1';
	function todo_upgrade0_9_3pre1()
	{
		global $setup_info;

		todo_v0_9_2to0_9_3update_owner('todo','todo_owner');

		$setup_info['todo']['currentver'] = '0.9.3pre2';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.3pre2';
	function todo_upgrade0_9_3pre2()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.3pre3';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.3pre3';
	function todo_upgrade0_9_3pre3()
	{
		global $setup_info, $phpgw_setup;

		$phpgw_setup->oProc->AddColumn("todo", "todo_id_parent", array("type" => "int", "precision" => 4, "nullable" => false, "default" => "0"));

		$setup_info['todo']['currentver'] = '0.9.3pre4';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.3pre4';
	function todo_upgrade0_9_3pre4()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.3pre4';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.3pre5';
	function todo_upgrade0_9_3pre5()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.3pre6';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.3pre6';
	function todo_upgrade0_9_3pre6()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.3pre7';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.3pre7';
	function todo_upgrade0_9_3pre7()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.3pre8';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.3pre8';
	function todo_upgrade0_9_3pre8()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.3pre9';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.3pre9';
	function todo_upgrade0_9_3pre9()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.3pre10';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.3pre10';
	function todo_upgrade0_9_3pre10()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.3';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.3';
	function todo_upgrade0_9_3()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.4pre1';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.4pre1';
	function todo_upgrade0_9_4pre1()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.4pre2';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.4pre2';
	function todo_upgrade0_9_4pre2()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.4pre3';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.4pre3';
	function todo_upgrade0_9_4pre3()
	{
		global $phpgw_info, $phpgw_setup;

		$phpgw_setup->oProc->AddColumn("todo", "todo_startdate", array("type" => "int", "precision" => 4));
		$phpgw_setup->oProc->RenameColumn("todo", "todo_datedue", "todo_enddate");

		$setup_info['todo']['currentver'] = '0.9.4pre4';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.4pre4';
	function todo_upgrade0_9_4pre4()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.4pre5';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.4pre5';
	function todo_upgrade0_9_4pre5()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.4';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.4';
	function todo_upgrade0_9_4()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.5pre1';
		return $setup_info['todo']['currentver'];
	}


	$test[] = '0.9.5pre1';
	function todo_upgrade0_9_5pre1()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.5pre2';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.5pre2';
	function todo_upgrade0_9_5pre2()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.5';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.5';
	function todo_upgrade0_9_5()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.6';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.6';
	function todo_upgrade0_9_6()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.7pre1';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.7pre1';
	function todo_upgrade0_9_7pre1()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.7pre2';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.7pre2';
	function todo_upgrade0_9_7pre2()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.7pre3';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.7pre3';
	function todo_upgrade0_9_7pre3()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.7';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.7';
	function todo_upgrade0_9_7()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.8pre1';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.8pre1';
	function todo_upgrade0_9_8pre1()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.8pre2';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.8pre2';
	function todo_upgrade0_9_8pre2()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.8pre3';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.8pre3';
	function todo_upgrade0_9_8pre3()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.8pre4';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.8pre4';
	function todo_upgrade0_9_8pre4()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.8pre5';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.8pre5';
	function todo_upgrade0_9_8pre5()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.9pre1';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.9pre1';
	function todo_upgrade0_9_9pre1()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.9';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.9';
	function todo_upgrade0_9_9()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10pre1';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre1';
	function todo_upgrade0_9_10pre1()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10pre2';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre2';
	function todo_upgrade0_9_10pre2()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10pre3';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre3';
	function todo_upgrade0_9_10pre3()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10pre4';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre4';
	function todo_upgrade0_9_10pre4()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10pre5';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre5';
	function todo_upgrade0_9_10pre5()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10pre6';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre6';
	function todo_upgrade0_9_10pre6()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10pre7';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre7';
	function todo_upgrade0_9_10pre7()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10pre8';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre8';
	function todo_upgrade0_9_10pre8()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10pre9';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre9';
	function todo_upgrade0_9_10pre9()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10pre10';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre10';
	function todo_upgrade0_9_10pre10()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10pre11';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre11';
	function todo_upgrade0_9_10pre11()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10pre12';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre12';
	function todo_upgrade0_9_10pre12()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10pre13';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre13';
	function todo_upgrade0_9_10pre13()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10pre14';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre14';
	function todo_upgrade0_9_10pre14()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10pre15';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre15';
	function todo_upgrade0_9_10pre15()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10pre16';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre16';
	function todo_upgrade0_9_10pre16()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10pre17';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre17';
	function todo_upgrade0_9_10pre17()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10pre18';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre18';
	function todo_upgrade0_9_10pre18()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10pre19';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre19';
	function todo_upgrade0_9_10pre19()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10pre20';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre20';
	function todo_upgrade0_9_10pre20()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10pre21';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre21';
	function todo_upgrade0_9_10pre21()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10pre22';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre22';
	function todo_upgrade0_9_10pre22()
	{
		global $setup_info, $phpgw_setup;

		$phpgw_setup->oProc->RenameTable('todo','phpgw_todo');

		$setup_info['todo']['currentver'] = '0.9.10pre23';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre23';
	function todo_upgrade0_9_10pre23()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10pre24';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre24';
	function todo_upgrade0_9_10pre24()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10pre25';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre25';
	function todo_upgrade0_9_10pre25()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10pre26';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre26';
	function todo_upgrade0_9_10pre26()
	{
		global $setup_info,$phpgw_setup;

		$phpgw_setup->oProc->AddColumn('phpgw_todo','todo_cat',array('type' => 'int','precision' => 4,'nullable' => True));

		$setup_info['todo']['currentver'] = '0.9.10pre27';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre27';
	function todo_upgrade0_9_10pre27()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10pre28';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10pre28';
	function todo_upgrade0_9_10pre28()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.10';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.10';
	function todo_upgrade0_9_10()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.11.001';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.11';
	function todo_upgrade0_9_11()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.11.001';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.11.001';
	function todo_upgrade0_9_11_001()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.11.002';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.11.003';
	function todo_upgrade0_9_11_003()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.11.004';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.11.004';
	function todo_upgrade0_9_11_004()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.11.005';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.11.005';
	function todo_upgrade0_9_11_005()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.11.006';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.11.006';
	function todo_upgrade0_9_11_006()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.11.007';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.11.007';
	function todo_upgrade0_9_11_007()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.11.008';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.11.008';
	function todo_upgrade0_9_11_008()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.11.009';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.11.009';
	function todo_upgrade0_9_11_009()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.11.010';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.11.010';
	function todo_upgrade0_9_11_010()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.11.011';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.11.011';
	function todo_upgrade0_9_11_011()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.13.001';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.13.001';
	function todo_upgrade0_9_13_001()
	{
		global $setup_info;
		$setup_info['todo']['currentver'] = '0.9.13.002';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.13.002';
	function todo_upgrade0_9_13_002()
	{
		global $setup_info, $phpgw_setup;

		$phpgw_setup->oProc->AddColumn('phpgw_todo','todo_id_main',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));
		$phpgw_setup->oProc->AddColumn('phpgw_todo','todo_level',array('type' => 'int','precision' => 2,'default' => 0,'nullable' => False));
		$phpgw_setup->oProc->AlterColumn('phpgw_todo','todo_id_parent',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));
		$phpgw_setup->oProc->AlterColumn('phpgw_todo','todo_cat',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));
		$phpgw_setup->oProc->AlterColumn('phpgw_todo','todo_enddate',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));

		$db = $phpgw_setup->db;

		$phpgw_setup->oProc->query("select todo_id from phpgw_todo where todo_id_main='0'");

		while ($phpgw_setup->oProc->next_record())
		{
			$db->query("update phpgw_todo set todo_id_main='" . $phpgw_setup->oProc->f('todo_id') . "' "
						. "where todo_id='" . $phpgw_setup->oProc->f('todo_id') . "'");

		}

		$phpgw_setup->oProc->query("select todo_id_parent from phpgw_todo");

		while ($phpgw_setup->oProc->next_record())
		{
			if ($phpgw_setup->oProc->f('todo_id_parent') != 0)
			{
				$db->query("update phpgw_todo set todo_id_main='" . $phpgw_setup->oProc->f('todo_id_parent') . "',"
							. "todo_level='1' where todo_id_parent='" . $phpgw_setup->oProc->f('todo_id_parent') . "'");
			}
		}

		$setup_info['todo']['currentver'] = '0.9.13.003';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.13.003';
	function todo_upgrade0_9_13_003()
	{
		global $setup_info, $phpgw_setup;

		$phpgw_setup->oProc->AddColumn('phpgw_todo','todo_title',array('type' => 'varchar','precision' => 255,'nullable' => False));
		$phpgw_setup->oProc->AlterColumn('phpgw_todo','todo_owner',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));

		$setup_info['todo']['currentver'] = '0.9.13.004';
		return $setup_info['todo']['currentver'];
	}

	$test[] = '0.9.13.004';
	function todo_upgrade0_9_13_004()
	{
		$GLOBALS['setup_info']['todo']['currentver'] = '0.9.15.001';
		return $GLOBALS['setup_info']['todo']['currentver'];

	}

	$test[] = '0.9.14';
	function todo_upgrade0_9_14()
	{
		$GLOBALS['setup_info']['todo']['currentver'] = '0.9.15.001';
		return $GLOBALS['setup_info']['todo']['currentver'];
	}

	$test[] = '0.9.14.500';
	function todo_upgrade0_9_14_500()
	{
		$GLOBALS['setup_info']['todo']['currentver'] = '0.9.15.001';
		return $GLOBALS['setup_info']['todo']['currentver'];
	}

	$test[] = '0.9.15.001';
	function todo_upgrade0_9_15_001()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_todo','todo_assigned',array('type' => 'varchar','precision' => 255,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_todo','assigned_group',array('type' => 'varchar','precision' => 255,'nullable' => False));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['todo']['currentver'] = '0.9.15.002';
			return $GLOBALS['setup_info']['todo']['currentver'];
		}
	}

	$test[] = '0.9.15.002';
	function todo_upgrade0_9_15_002()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_todo','entry_date',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['todo']['currentver'] = '0.9.15.003';
			return $GLOBALS['setup_info']['todo']['currentver'];
		}
	}
