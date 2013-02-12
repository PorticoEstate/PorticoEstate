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
 	* @version $Id$
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

	$test[] = '0.9.17.502';
	function catch_upgrade0_9_17_502()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_catch_category','name',array('type' => 'varchar','precision' => '100','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_catch_category','descr',array('type' => 'text','nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['catch']['currentver'] = '0.9.17.503';
			return $GLOBALS['setup_info']['catch']['currentver'];
		}
	}

	$test[] = '0.9.17.503';
	function catch_upgrade0_9_17_503()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_catch_config_attrib','value',array('type' => 'varchar','precision' => 1000,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_catch_config_type','schema',array('type' => 'varchar','precision' => 10,'nullable' => false));
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_catch_config_value');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['catch']['currentver'] = '0.9.17.504';
			return $GLOBALS['setup_info']['catch']['currentver'];
		}
	}

	$test[] = '0.9.17.504';
	function catch_upgrade0_9_17_504()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_catch_category");

		$tables_to_drop = array();
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$tables_to_drop[] = 'fm_catch_' . $GLOBALS['phpgw_setup']->oProc->f('entity_id') . '_' . $GLOBALS['phpgw_setup']->oProc->f('id');
		}

		foreach($tables_to_drop as $table)
		{
			$GLOBALS['phpgw_setup']->oProc->DropTable($table);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT app_id FROM phpgw_applications WHERE app_name = 'catch'");
		$GLOBALS['phpgw_setup']->oProc->next_record();

		$app_id = $GLOBALS['phpgw_setup']->oProc->f('app_id');

		$GLOBALS['phpgw_setup']->oProc->query("SELECT location_id FROM phpgw_locations WHERE app_id = {$app_id} AND name != 'run'");

		$locations = array();
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$locations[] = $GLOBALS['phpgw_setup']->oProc->f('location_id');
		}

		if(count($locations))
		{
			$GLOBALS['phpgw_setup']->oProc->query('DELETE FROM phpgw_cust_choice WHERE location_id IN ('. implode (',',$locations) . ')');
			$GLOBALS['phpgw_setup']->oProc->query('DELETE FROM phpgw_cust_attribute WHERE location_id IN ('. implode (',',$locations). ')');
			$GLOBALS['phpgw_setup']->oProc->query('DELETE FROM phpgw_acl  WHERE location_id IN ('. implode (',',$locations) . ')');
		}

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_locations WHERE app_id = {$app_id} AND name != 'run'");
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_catch");
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_catch_category");

		unset($locations);

		#
		#  phpgw_locations
		#

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant) VALUES ({$app_id}, '.', 'Top', 1)");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.admin', 'Admin')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.admin.entity', 'Admin entity')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant) VALUES ({$app_id}, '.catch.1', 'User config', 1)");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.catch.1.1', 'Users and devices', 1, 1, 'fm_catch_1_1')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant) VALUES ({$app_id}, '.catch.2', 'Shema category', 1)");
//		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.catch.2.1', 'Shema type 1', 1, 1, 'fm_catch_2_1')");

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_catch (id, name, descr) VALUES (1, 'Users and devices', 'Users and devices')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_catch (id, name, descr) VALUES (2, 'Shema type 1', 'Shema type 1')");

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_catch_category (entity_id, id, name, descr) VALUES (1, 1, 'Users and devices', 'Users and devices')");
//		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_catch_category (entity_id, id, name, descr, fileupload) VALUES (2, 1, 'Shema type 1', 'Shema type 1', 1)");

		$location_id = $GLOBALS['phpgw']->locations->get_id('catch', '.catch.1.1');
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 1, 'unitid', 'UnitID', 'UnitID for device', 'V', 1, 1, NULL, 50, NULL, NULL, 'False')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 2, 'user_', 'User', 'System user', 'user', 1, 2, NULL, NULL, NULL, NULL, 'False')");

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_catch_1_1',  array(
				'fd' => array(
					'id' => array('type' => 'int','precision' => '4','nullable' => False),
					'num' => array('type' => 'varchar','precision' => '20','nullable' => False),
					'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
					'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
					'unitid' => array('type' => 'varchar','precision' => '50','nullable' => False),
					'user_' => array('type' => 'int','precision' => '4','nullable' => False)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('num')
			)
		);
/*
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_catch_2_1',  array(
				'fd' => array(
					'id' => array('type' => 'int','precision' => '4','nullable' => False),
					'num' => array('type' => 'varchar','precision' => '20','nullable' => False),
					'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
					'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('num')
			)
		);
*/
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['catch']['currentver'] = '0.9.17.505';
			return $GLOBALS['setup_info']['catch']['currentver'];
		}
	}

	$test[] = '0.9.17.505';
	function catch_upgrade0_9_17_505()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_catch_category','jasperupload',array('type' => 'int','precision' => 2,'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['catch']['currentver'] = '0.9.17.506';
			return $GLOBALS['setup_info']['catch']['currentver'];
		}
	}

	$test[] = '0.9.17.506';
	function catch_upgrade0_9_17_506()
	{
		$metadata = $GLOBALS['phpgw_setup']->oProc->m_odb->metadata('fm_catch_2_1');

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		if(!isset($metadata['loc1']))
		{
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_catch_2_1','p_num', array('type' => 'varchar','precision' => '15','nullable' => True));
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_catch_2_1','p_entity_id', array('type' => 'int','precision' => '4','nullable' => True));
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_catch_2_1','p_cat_id', array('type' => 'int','precision' => '4','nullable' => True));
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_catch_2_1','location_code', array('type' => 'varchar','precision' => '25','nullable' => True));
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_catch_2_1','loc1', array('type' => 'varchar','precision' => '4','nullable' => True));
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_catch_2_1','loc2', array('type' => 'varchar','precision' => '4','nullable' => True));
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_catch_2_1','loc3', array('type' => 'varchar','precision' => '4','nullable' => True));
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_catch_2_1','loc4', array('type' => 'varchar','precision' => '4','nullable' => True));
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_catch_2_1','loc5', array('type' => 'varchar','precision' => '4','nullable' => True));
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_catch_2_1','address', array('type' => 'varchar','precision' => '150','nullable' => True));
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['catch']['currentver'] = '0.9.17.507';
			return $GLOBALS['setup_info']['catch']['currentver'];
		}
	}

	/**
	* Update catch version from 0.9.17.507 to 0.9.17.508
	* Add optional hierarchy on entities
	* 
	*/

	$test[] = '0.9.17.507';
	function catch_upgrade0_9_17_507()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_catch_category','parent_id', array('type' => 'int','precision' => '4','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_catch_category','level', array('type' => 'int','precision' => '4','nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['catch']['currentver'] = '0.9.17.508';
			return $GLOBALS['setup_info']['catch']['currentver'];
		}
	}
	/**
	* Update catch version from 0.9.17.508 to 0.9.17.509
	* Rename reserved fieldname to allow MySQL
	* 
	*/

	$test[] = '0.9.17.508';
	function catch_upgrade0_9_17_508()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_catch_config_type','schema','schema_');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['catch']['currentver'] = '0.9.17.509';
			return $GLOBALS['setup_info']['catch']['currentver'];
		}
	}

	/**
	* Update catch version from 0.9.17.509 to 0.9.17.510
	* Add location_link_level
	* 
	*/

	$test[] = '0.9.17.509';
	function catch_upgrade0_9_17_509()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_catch_category','location_link_level', array('type' => 'int','precision' => '4','nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['catch']['currentver'] = '0.9.17.510';
			return $GLOBALS['setup_info']['catch']['currentver'];
		}
	}

	/**
	* Update catch version from 0.9.17.510 to 0.9.17.511
	* Allow value is null
	* 
	*/

	$test[] = '0.9.17.510';
	function catch_upgrade0_9_17_510()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_catch_config_attrib','value',array('type' => 'varchar', 'precision' => 1000,'nullable' => true));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['catch']['currentver'] = '0.9.17.511';
			return $GLOBALS['setup_info']['catch']['currentver'];
		}
	}

	/**
	* Update catch version from 0.9.17.511 to 0.9.17.512
	* Add flag for eav modelling
	*/

	$test[] = '0.9.17.511';
	function catch_upgrade0_9_17_511()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_catch_category','is_eav',array('type' => 'int','precision' => 2,'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['catch']['currentver'] = '0.9.17.512';
			return $GLOBALS['setup_info']['catch']['currentver'];
		}
	}

	/**
	* Update catch version from 0.9.17.512 to 0.9.17.513
	* Add history_old_value
	*/

	$test[] = '0.9.17.512';
	function catch_upgrade0_9_17_512()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_catch_history','history_old_value',array('type' => 'text','nullable' => true));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['catch']['currentver'] = '0.9.17.513';
			return $GLOBALS['setup_info']['catch']['currentver'];
		}
	}

	/**
	* Update catch version from 0.9.17.513 to 0.9.17.514
	* Add location_id to entities
	*/

	$test[] = '0.9.17.513';
	function catch_upgrade0_9_17_513()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_catch','location_id',array(
			'type'		=> 'int',
			'precision'	=> 4,
			'nullable'	=> true
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_catch_category','location_id',array(
			'type'		=> 'int',
			'precision'	=> 4,
			'nullable'	=> true
			)
		);

		$sql = 'SELECT id, entity_id FROM fm_catch_category';
		$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);

		$categories = array();
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$categories[] = array
			(
				'entity_id'	=> $GLOBALS['phpgw_setup']->oProc->f('entity_id'),
				'cat_id'	=> $GLOBALS['phpgw_setup']->oProc->f('id'),
			);
		}


		$sql = 'SELECT id FROM fm_catch';
		$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);

		$entities = array();
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$entities[] = array
			(
				'entity_id'	=> $GLOBALS['phpgw_setup']->oProc->f('id'),
			);
		}

		foreach ($categories as $category)
		{
			$location_id	= $GLOBALS['phpgw']->locations->get_id('catch', ".catch.{$category['entity_id']}.{$category['cat_id']}");
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_catch_category SET location_id = {$location_id} WHERE entity_id = {$category['entity_id']} AND id = {$category['cat_id']}",__LINE__,__FILE__);
		}

		foreach ($entities as $entity)
		{
			$location_id	= $GLOBALS['phpgw']->locations->get_id('catch', ".catch.{$entity['entity_id']}");
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_catch SET location_id = {$location_id} WHERE id = {$entity['entity_id']}",__LINE__,__FILE__);
		}

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_catch','location_id',array(
			'type'		=> 'int',
			'precision'	=> 4,
			'nullable'	=> false
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_catch_category','location_id',array(
			'type'		=> 'int',
			'precision'	=> 4,
			'nullable'	=> false
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['catch']['currentver'] = '0.9.17.514';
			return $GLOBALS['setup_info']['catch']['currentver'];
		}
	}

	/**
	* Update catch version from 0.9.17.514 to 0.9.17.515
	* Add bulk-flag to entities
	*/

	$test[] = '0.9.17.514';
	function catch_upgrade0_9_17_514()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_catch_category','enable_bulk',array('type' => 'int','precision' => 2,'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['catch']['currentver'] = '0.9.17.515';
			return $GLOBALS['setup_info']['catch']['currentver'];
		}
	}

