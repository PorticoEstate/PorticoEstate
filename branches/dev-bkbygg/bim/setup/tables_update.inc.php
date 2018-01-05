<?php
	/**
	* phpGroupWare - bim: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2009 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package bim
	* @subpackage setup
 	* @version $Id: tables_update.inc.php 6982 2011-02-14 20:01:17Z sigurdne $
	*/
	/**
	* Update bim version from 0.9.17.500 to 0.9.17.501
	*/
	$test[] = '0.9.17.500';

	function bim_upgrade0_9_17_500()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_type', 'location_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_type', 'is_ifc', array('type' => 'int',
			'precision' => 2, 'default' => 1, 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item', 'p_location_id', array(
			'type' => 'int', 'precision' => '4', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item', 'p_id', array('type' => 'int',
			'precision' => '4', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item', 'location_code', array(
			'type' => 'varchar', 'precision' => '20', 'nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item', 'address', array('type' => 'varchar',
			'precision' => '150', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item', 'entry_date', array('type' => 'int',
			'precision' => '4', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item', 'user_id', array('type' => 'int',
			'precision' => '4', 'nullable' => True));
		
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['bim']['currentver'] = '0.9.17.501';
			return $GLOBALS['setup_info']['bim']['currentver'];
		}
	}
	/**
	* Update bim version from 0.9.17.501 to 0.9.17.502
	*/
	$test[] = '0.9.17.501';

	function bim_upgrade0_9_17_501()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_bim_item', 'guid', array('type' => 'varchar',
			'precision' => '50', 'nullable' => False));
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['bim']['currentver'] = '0.9.17.502';
			return $GLOBALS['setup_info']['bim']['currentver'];
		}
	}
	/**
	* Update bim version from 0.9.17.502 to 0.9.17.503
	*/
	$test[] = '0.9.17.502';

	function bim_upgrade0_9_17_502()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item', 'loc1', array('type' => 'varchar',
			'precision' => '6', 'nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->query('ALTER TABLE fm_bim_item DROP CONSTRAINT fm_bim_item_pkey', __LINE__, __FILE__);
		$GLOBALS['phpgw_setup']->oProc->query('ALTER TABLE fm_bim_item ADD CONSTRAINT fm_bim_item_pkey PRIMARY KEY(type,id)', __LINE__, __FILE__);
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['bim']['currentver'] = '0.9.17.503';
			return $GLOBALS['setup_info']['bim']['currentver'];
		}
	}
	/**
	* Update bim version from 0.9.17.503 to 0.9.17.504
	*/
	$test[] = '0.9.17.503';

	function bim_upgrade0_9_17_503()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_bim_type', 'name', array('type' => 'varchar',
			'precision' => '150', 'nullable' => False));
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['bim']['currentver'] = '0.9.17.504';
			return $GLOBALS['setup_info']['bim']['currentver'];
		}
	}
	/**
	* Update bim version from 0.9.17.504 to 0.9.17.505
	*/
	$test[] = '0.9.17.504';

	function bim_upgrade0_9_17_504()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item', 'location_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => true));


		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_bim_type", __LINE__, __FILE__);

		$types = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$types[] = array
			(
				'id'			=> (int)$GLOBALS['phpgw_setup']->oProc->f('id'),
				'location_id'	=> (int)$GLOBALS['phpgw_setup']->oProc->f('location_id'),
				'name' => $GLOBALS['phpgw_setup']->oProc->f('name', true),
				'description' => $GLOBALS['phpgw_setup']->oProc->f('description', true)
			);
		}

		foreach($types as $entry)
		{
			if(!$location_id = $entry['location_id'])
			{
				$location_id = $GLOBALS['phpgw']->locations->add($entry['name'], $entry['description'], 'bim');
			}

			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_bim_item SET location_id = {$location_id} WHERE type = {$entry['id']}", __LINE__, __FILE__);
		}

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_bim_item', 'location_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => false));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['bim']['currentver'] = '0.9.17.505';
			return $GLOBALS['setup_info']['bim']['currentver'];
		}
	}
	/**
	* Update bim version from 0.9.17.505 to 0.9.17.506
	*/
	$test[] = '0.9.17.505';

	function bim_upgrade0_9_17_505()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_bim_item_inventory', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'location_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'item_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'p_location_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'p_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'unit_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'inventory' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'write_off' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'bookable' => array('type' => 'int', 'precision' => 2, 'nullable' => False),
				'active_from' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'active_to' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'created_on' => array('type' => 'int', 'precision' => 8, 'nullable' => False),
				'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'expired_on' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'expired_by' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'remark' => array('type' => 'text', 'nullable' => True)
				),
				'pk' => array('id'),
			'fk' => array(), //'fm_bim_item' => array('location_id' => 'location_id')), 'item_id'=> 'id')),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['bim']['currentver'] = '0.9.17.506';
			return $GLOBALS['setup_info']['bim']['currentver'];
		}
	}
	/**
	* Update bim version from 0.9.17.506 to 0.9.17.507
	*/
	$test[] = '0.9.17.506';

	function bim_upgrade0_9_17_506()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item', 'department_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => true));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['bim']['currentver'] = '0.9.17.507';
			return $GLOBALS['setup_info']['bim']['currentver'];
		}
	}
	/**
	* Update bim version from 0.9.17.507 to 0.9.17.508
	*/
	$test[] = '0.9.17.507';

	function bim_upgrade0_9_17_507()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_bim_item', 'department_id', 'org_unit_id');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['bim']['currentver'] = '0.9.17.508';
			return $GLOBALS['setup_info']['bim']['currentver'];
		}
	}
	/**
	* Update bim version from 0.9.17.508 to 0.9.17.509
	*/
	$test[] = '0.9.17.508';

	function bim_upgrade0_9_17_508()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item', 'entity_group_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => true));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['bim']['currentver'] = '0.9.17.509';
			return $GLOBALS['setup_info']['bim']['currentver'];
		}
	}
	/**
	* Update bim version from 0.9.17.509 to 0.9.17.510
	*/
	$test[] = '0.9.17.509';

	function bim_upgrade0_9_17_509()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item', 'modified_by', array(
			'type' => 'int', 'precision' => 4, 'nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item', 'modified_on', array(
			'type' => 'int', 'precision' => 8, 'nullable' => true));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['bim']['currentver'] = '0.9.17.510';
			return $GLOBALS['setup_info']['bim']['currentver'];
		}
	}
	/**
	* Update bim version from 0.9.17.510 to 0.9.17.511
	*/
	$test[] = '0.9.17.510';

	function bim_upgrade0_9_17_510()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item', 'json_representation', array(
			'type' => 'jsonb', 'nullable' => true));

		$GLOBALS['phpgw_setup']->oProc->query("SELECT id,location_id,xml_representation FROM fm_bim_item", __LINE__, __FILE__);

		$items = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$items[] = array
			(
				'id'			=> (int)$GLOBALS['phpgw_setup']->oProc->f('id'),
				'location_id'	=> (int)$GLOBALS['phpgw_setup']->oProc->f('location_id'),
				'xml_representation' => $GLOBALS['phpgw_setup']->oProc->f('xml_representation', true),
			);
		}

		$xmlparse = CreateObject('property.XmlToArray');
		$xmlparse->setEncoding('UTF-8');
		$xmlparse->setDecodesUTF8Automaticly(false);

		foreach ($items as $item)
		{
			$xmldata = $item['xml_representation'];
			$var_result = $xmlparse->parse($xmldata);

			$jsondata = json_encode($var_result, JSON_HEX_APOS);
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_bim_item SET json_representation = '{$jsondata}'"
			. " WHERE id = {$item['id']} AND location_id = {$item['location_id']}", __LINE__, __FILE__);
		}

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_bim_item', 'json_representation', array(
			'type' => 'jsonb', 'nullable' => False));

		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_bim_item', array(), 'xml_representation');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['bim']['currentver'] = '0.9.17.511';
			return $GLOBALS['setup_info']['bim']['currentver'];
		}
	}
