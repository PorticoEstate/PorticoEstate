<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2009 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage setup
	 * @version $Id$
	*/
	/**
	* Update property version from 0.9.17.500 to 0.9.17.501
	*/
	$test[] = '0.9.17.500';

	function property_upgrade0_9_17_500()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_origin', array(
				'fd' => array(
				'origin' => array('type' => 'varchar', 'precision' => '12', 'nullable' => False),
				'origin_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'destination' => array('type' => 'varchar', 'precision' => '12', 'nullable' => False),
				'destination_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True)
				),
			'pk' => array('origin', 'origin_id', 'destination', 'destination_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_request_origin");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$origin[] = array(
				'origin'	=> $GLOBALS['phpgw_setup']->oProc->f('origin'),
				'origin_id'	=> $GLOBALS['phpgw_setup']->oProc->f('origin_id'),
				'destination' => 'request',
				'destination_id'	=> $GLOBALS['phpgw_setup']->oProc->f('request_id'),
				'entry_date'	=> $GLOBALS['phpgw_setup']->oProc->f('entry_date'),
			);
		}


		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_project_origin");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$origin[] = array(
				'origin'	=> $GLOBALS['phpgw_setup']->oProc->f('origin'),
				'origin_id'	=> $GLOBALS['phpgw_setup']->oProc->f('origin_id'),
				'destination' => 'project',
				'destination_id'	=> $GLOBALS['phpgw_setup']->oProc->f('project_id'),
				'entry_date'	=> $GLOBALS['phpgw_setup']->oProc->f('entry_date'),
			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_entity_origin");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$origin[] = array(
				'origin'	=> $GLOBALS['phpgw_setup']->oProc->f('origin'),
				'origin_id'	=> $GLOBALS['phpgw_setup']->oProc->f('origin_id'),
				'destination' => 'entity_' . $GLOBALS['phpgw_setup']->oProc->f('entity_id') . '_' . $GLOBALS['phpgw_setup']->oProc->f('cat_id'),
				'destination_id'	=> $GLOBALS['phpgw_setup']->oProc->f('id'),
				'entry_date'	=> $GLOBALS['phpgw_setup']->oProc->f('entry_date'),
			);
		}

		$rec_count = count($origin);


		for($i = 0; $i < $rec_count; $i++)
		{
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_origin(origin,origin_id,destination,destination_id,entry_date) "
				. "VALUES('"
			. $origin[$i]['origin'] . "','"
			. $origin[$i]['origin_id'] . "','"
			. $origin[$i]['destination'] . "','"
			. $origin[$i]['destination_id'] . "','"
			. $origin[$i]['entry_date'] . "')");
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_request_origin');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_project_origin');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_entity_origin');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.501';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.501 to 0.9.17.502
	*/
	$test[] = '0.9.17.501';

	function property_upgrade0_9_17_501()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'descr', array('type' => 'text',
			'nullable' => True));
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.502';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.502 to 0.9.17.503
	*/
	$test[] = '0.9.17.502';

	function property_upgrade0_9_17_502()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_acl_location', 'id', array('type' => 'varchar',
			'precision' => '20', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_acl_location (id, descr) VALUES ('.tenant_claim', 'Tenant claim')");

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_tenant_claim_category', array(
				'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tenant_claim_category (id, descr) VALUES (1, 'Type 1')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tenant_claim_category (id, descr) VALUES (2, 'Type 2')");

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_tenant_claim', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'project_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'tenant_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'amount' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'default' => '0',
					'nullable' => True),
				'b_account_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'status' => array('type' => 'varchar', 'precision' => '8', 'nullable' => True),
				'remark' => array('type' => 'text', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder', 'claim_issued', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.503';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.503 to 0.9.17.504
	*/
	$test[] = '0.9.17.503';

	function property_upgrade0_9_17_503()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location_type', 'pk', array('type' => 'text',
			'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location_type', 'ix', array('type' => 'text',
			'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location_type', 'uc', array('type' => 'text',
			'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location_attrib', 'custom', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_attrib SET custom = 1");

		$GLOBALS['phpgw_setup']->oProc->query("SELECT count(*) as cnt FROM fm_location_type");
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$locations = $GLOBALS['phpgw_setup']->oProc->f('cnt');

		for($location_type = 1; $location_type < ($locations + 1); $location_type++)
		{
			$GLOBALS['phpgw_setup']->oProc->query("SELECT max(id) as id FROM fm_location_attrib WHERE type_id = $location_type");
			$GLOBALS['phpgw_setup']->oProc->next_record();
			$id = $GLOBALS['phpgw_setup']->oProc->f('id');
			$id++;

			$default_attrib['id'][] = $id;
			$default_attrib['column_name'][] = 'location_code';
			$default_attrib['type'][] = 'V';
			$default_attrib['precision'][] = 4 * $location_type;
			$default_attrib['nullable'][] = 'False';
			$default_attrib['input_text'][] = 'dummy';
			$default_attrib['statustext'][] = 'dummy';
			$default_attrib['custom'][] = 'NULL';
			$id++;

			$default_attrib['id'][] = $id;
			$default_attrib['column_name'][] = 'loc' . $location_type . '_name';
			$default_attrib['type'][] = 'V';
			$default_attrib['precision'][] = 50;
			$default_attrib['nullable'][] = 'True';
			$default_attrib['input_text'][] = 'dummy';
			$default_attrib['statustext'][] = 'dummy';
			$default_attrib['custom'][] = 'NULL';
			$id++;

			$default_attrib['id'][] = $id;
			$default_attrib['column_name'][] = 'entry_date';
			$default_attrib['type'][] = 'I';
			$default_attrib['precision'][] = 4;
			$default_attrib['nullable'][] = 'True';
			$default_attrib['input_text'][] = 'dummy';
			$default_attrib['statustext'][] = 'dummy';
			$default_attrib['custom'][] = 'NULL';
			$id++;

			$default_attrib['id'][] = $id;
			$default_attrib['column_name'][] = 'category';
			$default_attrib['type'][] = 'I';
			$default_attrib['precision'][] = 4;
			$default_attrib['nullable'][] = 'False';
			$default_attrib['input_text'][] = 'dummy';
			$default_attrib['statustext'][] = 'dummy';
			$default_attrib['custom'][] = 'NULL';
			$id++;

			$default_attrib['id'][] = $id;
			$default_attrib['column_name'][] = 'user_id';
			$default_attrib['type'][] = 'I';
			$default_attrib['precision'][] = 4;
			$default_attrib['nullable'][] = 'False';
			$default_attrib['input_text'][] = 'dummy';
			$default_attrib['statustext'][] = 'dummy';
			$default_attrib['custom'][] = 'NULL';
			$id++;

			$default_attrib['id'][] = $id;
			$default_attrib['column_name'][] = 'remark';
			$default_attrib['type'][] = 'T';
			$default_attrib['precision'][] = 'NULL';
			$default_attrib['nullable'][] = 'False';
			$default_attrib['input_text'][] = 'dummy';
			$default_attrib['statustext'][] = 'dummy';
			$default_attrib['custom'][] = 'NULL';
			$id++;

			for($i = 1; $i < $location_type + 1; $i++)
			{
				$pk[$i - 1] = 'loc' . $i;

				$default_attrib['id'][] = $id;
				$default_attrib['column_name'][] = 'loc' . $i;
				$default_attrib['type'][] = 'V';
				$default_attrib['precision'][] = 4;
				$default_attrib['nullable'][] = 'False';
				$default_attrib['input_text'][] = 'dummy';
				$default_attrib['statustext'][] = 'dummy';
				$default_attrib['custom'][] = 'NULL';
				$id++;
			}

			if($location_type == 1)
			{
				$default_attrib['id'][] = $id;
				$default_attrib['column_name'][] = 'mva';
				$default_attrib['type'][] = 'I';
				$default_attrib['precision'][] = 4;
				$default_attrib['nullable'][] = 'True';
				$default_attrib['input_text'][] = 'mva';
				$default_attrib['statustext'][] = 'mva';
				$default_attrib['custom'][] = 1;
				$id++;

				$default_attrib['id'][] = $id;
				$default_attrib['column_name'][] = 'kostra_id';
				$default_attrib['type'][] = 'I';
				$default_attrib['precision'][] = 4;
				$default_attrib['nullable'][] = 'True';
				$default_attrib['input_text'][] = 'kostra_id';
				$default_attrib['statustext'][] = 'kostra_id';
				$default_attrib['custom'][] = 1;
				$id++;

				$default_attrib['id'][] = $id;
				$default_attrib['column_name'][] = 'part_of_town_id';
				$default_attrib['type'][] = 'I';
				$default_attrib['precision'][] = 4;
				$default_attrib['nullable'][] = 'True';
				$default_attrib['input_text'][] = 'dummy';
				$default_attrib['statustext'][] = 'dummy';
				$default_attrib['custom'][] = 'NULL';
				$id++;

				$default_attrib['id'][] = $id;
				$default_attrib['column_name'][] = 'owner_id';
				$default_attrib['type'][] = 'I';
				$default_attrib['precision'][] = 4;
				$default_attrib['nullable'][] = 'True';
				$default_attrib['input_text'][] = 'dummy';
				$default_attrib['statustext'][] = 'dummy';
				$default_attrib['custom'][] = 'NULL';
				$id++;
			}

			if($location_type > 1)
			{
				$fk_table = 'fm_location' . ($location_type - 1);

				for($i = 1; $i < $standard['id']; $i++)
				{
					$fk['loc' . $i]	= $fk_table . '.loc' . $i;
				}
			}

			$ix = array('location_code');

			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_type SET "
			. "pk ='" . implode(',', $pk) . "',"
			. "ix ='" . implode(',', $ix) . "' WHERE id = $location_type");


			for($i = 0; $i < count($default_attrib['id']); $i++)
			{
				$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location_attrib (type_id,id,column_name,datatype,precision_,input_text,statustext,nullable,custom)"
					. " VALUES ( $location_type,'"
					. $default_attrib['id'][$i] . "','"
					. $default_attrib['column_name'][$i] . "','"
					. $default_attrib['type'][$i] . "',"
					. $default_attrib['precision'][$i] . ",'"
					. $default_attrib['input_text'][$i] . "','"
					. $default_attrib['statustext'][$i] . "','"
					. $default_attrib['nullable'][$i] . "',"
					. $default_attrib['custom'][$i] . ")");
			}

			unset($pk);
			unset($ix);
			unset($default_attrib);
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.504';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.504 to 0.9.17.505
	*/
	$test[] = '0.9.17.504';

	function property_upgrade0_9_17_504()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_attrib SET custom = 1, input_text = 'Remark', statustext='Remark' WHERE column_name = 'remark'");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_attrib SET input_text = column_name, statustext = column_name WHERE custom IS NULL");

		$datatype_precision = array(
			'R' => 4,
			'LB' => 4,
			'AB' => 4,
			'VENDOR' => 4,
			'email' => 64
			);

		$datatype_text = array(
			'V' => 'varchar',
			'I' => 'int',
			'C' => 'char',
			'N' => 'decimal',
			'D' => 'timestamp',
			'T' => 'text',
			'R' => 'int',
			'CH' => 'text',
			'LB' => 'int',
			'AB' => 'int',
			'VENDOR' => 'int',
			'email' => 'varchar'
			);

		$datatype_text[$datatype];

		$GLOBALS['phpgw_setup']->oProc->query("SELECT count(*) as cnt FROM fm_location_type");
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$locations = $GLOBALS['phpgw_setup']->oProc->f('cnt');

		for($location_type = 1; $location_type < ($locations + 1); $location_type++)
		{
			$GLOBALS['phpgw_setup']->oProc->query("SELECT max(attrib_sort) as attrib_sort FROM fm_location_attrib WHERE type_id = $location_type AND column_name = 'remark' AND attrib_sort IS NOT NULL");

			$GLOBALS['phpgw_setup']->oProc->next_record();
			$attrib_sort = $GLOBALS['phpgw_setup']->oProc->f('attrib_sort') + 1;


			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_attrib SET attrib_sort = $attrib_sort WHERE type_id = $location_type AND column_name = 'remark'");

			if($location_type == 1)
			{
				$attrib_sort++;

				$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_attrib SET attrib_sort = $attrib_sort WHERE type_id = $location_type AND column_name = 'mva'");
				$attrib_sort++;

				$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_attrib SET attrib_sort = $attrib_sort WHERE type_id = $location_type AND column_name = 'kostra_id'");
			}

			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location' . $location_type, 'change_type', array(
				'type' => 'int', 'precision' => 4, 'nullable' => True));

			$GLOBALS['phpgw_setup']->oProc->query("SELECT max(id) as attrib_id FROM fm_location_attrib WHERE type_id = $location_type");

			$GLOBALS['phpgw_setup']->oProc->next_record();
			$attrib_id = $GLOBALS['phpgw_setup']->oProc->f('attrib_id') + 1;

			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location_attrib (type_id,id,column_name,datatype,precision_,input_text,statustext,nullable,custom)"
					. " VALUES ( $location_type,$attrib_id, 'change_type', 'I', 4, 'change_type','change_type','True',NULL)");

			if($location_type == 4)
			{
				$attrib_id++;
				$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location_attrib (type_id,id,column_name,datatype,precision_,input_text,statustext,nullable,custom)"
					. " VALUES ( $location_type,$attrib_id, 'street_id', 'I', 4, 'street_id','street_id','True',NULL)");


				$attrib_id++;
				$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location_attrib (type_id,id,column_name,datatype,precision_,input_text,statustext,nullable,custom)"
					. " VALUES ( $location_type,$attrib_id, 'street_number', 'V', 10, 'street_number','street_number','True',NULL)");

				$attrib_id++;
				$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location_attrib (type_id,id,column_name,datatype,precision_,input_text,statustext,nullable,custom)"
					. " VALUES ( $location_type,$attrib_id, 'tenant_id', 'I', 4, 'tenant_id','tenant_id','True',NULL)");
			}

			$metadata = $GLOBALS['phpgw_setup']->db->metadata('fm_location' . $location_type);

			if(isset($GLOBALS['phpgw_setup']->db->adodb))
			{
				$i = 0;
				foreach($metadata as $key => $val)
				{
					$metadata_temp[$i]['name'] = $key;
					$i++;
				}
				$metadata = $metadata_temp;
				unset($metadata_temp);
			}

			for($i = 0; $i < count($metadata); $i++)
			{
				$sql = "SELECT * FROM fm_location_attrib WHERE type_id=$location_type AND column_name = '" . $metadata[$i]['name'] . "'";

				$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
				if($GLOBALS['phpgw_setup']->oProc->next_record())
				{
					if(!$precision = $GLOBALS['phpgw_setup']->oProc->f('precision_'))
					{
						$precision = $datatype_precision[$GLOBALS['phpgw_setup']->oProc->f('datatype')];
					}

					if($GLOBALS['phpgw_setup']->oProc->f('nullable') == 'True')
					{
						$nullable = True;
					}

					$fd[$metadata[$i]['name']] = array(
					 		'type' => $datatype_text[$GLOBALS['phpgw_setup']->oProc->f('datatype')],
					 		'precision' => $precision,
					 		'nullable' => $nullable,
					 		'default' => stripslashes($GLOBALS['phpgw_setup']->oProc->f('default_value')),
					 		'scale' => $GLOBALS['phpgw_setup']->oProc->f('scale')
					 		);
					unset($precision);
					unset($nullable);
				}
			}

			$fd['exp_date'] = array('type' => 'timestamp', 'nullable' => True, 'default' => 'current_timestamp');

			$GLOBALS['phpgw_setup']->oProc->CreateTable(
				'fm_location' . $location_type . '_history', array(
					'fd' => $fd,
					'pk' => array(),
					'fk' => array(),
					'ix' => array(),
					'uc' => array()
				)
			);

			unset($fd);
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.505';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.505 to 0.9.17.506
	*/
	$test[] = '0.9.17.505';

	function property_upgrade0_9_17_505()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_wo_hours', 'category', array('type' => 'int',
			'precision' => 4, 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_wo_hours_category', array(
				'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.506';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.506 to 0.9.17.507
	*/
	$test[] = '0.9.17.506';

	function property_upgrade0_9_17_506()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'd_safety', array('type' => 'int',
			'precision' => '4', 'default' => '0', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'd_aesthetics', array(
			'type' => 'int', 'precision' => '4', 'default' => '0', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'd_indoor_climate', array(
			'type' => 'int', 'precision' => '4', 'default' => '0', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'd_consequential_damage', array(
			'type' => 'int', 'precision' => '4', 'default' => '0', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'd_user_gratification', array(
			'type' => 'int', 'precision' => '4', 'default' => '0', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'd_residential_environment', array(
			'type' => 'int', 'precision' => '4', 'default' => '0', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'p_safety', array('type' => 'int',
			'precision' => '4', 'default' => '0', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'p_aesthetics', array(
			'type' => 'int', 'precision' => '4', 'default' => '0', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'p_indoor_climate', array(
			'type' => 'int', 'precision' => '4', 'default' => '0', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'p_consequential_damage', array(
			'type' => 'int', 'precision' => '4', 'default' => '0', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'p_user_gratification', array(
			'type' => 'int', 'precision' => '4', 'default' => '0', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'p_residential_environment', array(
			'type' => 'int', 'precision' => '4', 'default' => '0', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'c_safety', array('type' => 'int',
			'precision' => '4', 'default' => '0', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'c_aesthetics', array(
			'type' => 'int', 'precision' => '4', 'default' => '0', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'c_indoor_climate', array(
			'type' => 'int', 'precision' => '4', 'default' => '0', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'c_consequential_damage', array(
			'type' => 'int', 'precision' => '4', 'default' => '0', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'c_user_gratification', array(
			'type' => 'int', 'precision' => '4', 'default' => '0', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'c_residential_environment', array(
			'type' => 'int', 'precision' => '4', 'default' => '0', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'authorities_demands', array(
			'type' => 'int', 'precision' => '2', 'default' => '0', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'score', array('type' => 'int',
			'precision' => '4', 'default' => '0', 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET d_safety = 0 WHERE d_safety IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET d_aesthetics = 0 WHERE d_aesthetics IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET d_indoor_climate = 0 WHERE d_indoor_climate IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET d_consequential_damage = 0 WHERE d_consequential_damage IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET d_user_gratification = 0 WHERE d_user_gratification IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET d_residential_environment = 0 WHERE d_residential_environment IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET p_safety = 0 WHERE p_safety IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET p_aesthetics = 0 WHERE p_aesthetics IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET p_indoor_climate = 0 WHERE p_indoor_climate IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET p_consequential_damage = 0 WHERE p_consequential_damage IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET p_user_gratification = 0 WHERE p_user_gratification IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET p_residential_environment = 0 WHERE p_residential_environment IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET c_safety = 0 WHERE c_safety IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET c_aesthetics = 0 WHERE c_aesthetics IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET c_indoor_climate = 0 WHERE c_indoor_climate IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET c_consequential_damage = 0 WHERE c_consequential_damage IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET c_user_gratification = 0 WHERE c_user_gratification IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET c_residential_environment = 0 WHERE c_residential_environment IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET authorities_demands = 0 WHERE authorities_demands IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET score = 0 WHERE score IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_workorder SET act_mtrl_cost = 0 WHERE act_mtrl_cost IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_workorder SET act_vendor_cost = 0 WHERE act_vendor_cost IS NULL ");

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.507';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.507 to 0.9.17.508
	*/
	$test[] = '0.9.17.507';

	function property_upgrade0_9_17_507()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_request_condition_type', array(
				'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'priority_key' => array('type' => 'int', 'precision' => '4', 'default' => '0',
					'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_request_condition', array(
				'fd' => array(
				'request_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'condition_type' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'degree' => array('type' => 'int', 'precision' => '4', 'default' => '0', 'nullable' => True),
				'probability' => array('type' => 'int', 'precision' => '4', 'default' => '0',
					'nullable' => True),
				'consequence' => array('type' => 'int', 'precision' => '4', 'default' => '0',
					'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True)
				),
			'pk' => array('request_id', 'condition_type'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition_type (id, descr, priority_key) VALUES (1, 'safety', 10)");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition_type (id, descr, priority_key) VALUES (2, 'aesthetics', 2)");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition_type (id, descr, priority_key) VALUES (3, 'indoor climate', 5)");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition_type (id, descr, priority_key) VALUES (4, 'consequential damage', 5)");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition_type (id, descr, priority_key) VALUES (5, 'user gratification', 4)");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition_type (id, descr, priority_key) VALUES (6, 'residential environment', 6)");


		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_request");

		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$condition[] = array(
				'request_id' => $GLOBALS['phpgw_setup']->oProc->f('id'),
				'user_id' => (int)$GLOBALS['phpgw_setup']->oProc->f('owner'),
				'entry_date' => (int)$GLOBALS['phpgw_setup']->oProc->f('entry_date'),
				'd_safety' => (int)$GLOBALS['phpgw_setup']->oProc->f('d_safety'),
				'd_aesthetics' => (int)$GLOBALS['phpgw_setup']->oProc->f('d_aesthetics'),
				'd_indoor_climate' => (int)$GLOBALS['phpgw_setup']->oProc->f('d_indoor_climate'),
				'd_consequential_damage' => (int)$GLOBALS['phpgw_setup']->oProc->f('d_consequential_damage'),
				'd_user_gratification' => (int)$GLOBALS['phpgw_setup']->oProc->f('d_user_gratification'),
				'd_residential_environment' => (int)$GLOBALS['phpgw_setup']->oProc->f('d_residential_environment'),
				'p_safety' => (int)$GLOBALS['phpgw_setup']->oProc->f('p_safety'),
				'p_aesthetics' => (int)$GLOBALS['phpgw_setup']->oProc->f('p_aesthetics'),
				'p_indoor_climate' => (int)$GLOBALS['phpgw_setup']->oProc->f('p_indoor_climate'),
				'p_consequential_damage' => (int)$GLOBALS['phpgw_setup']->oProc->f('p_consequential_damage'),
				'p_user_gratification' => (int)$GLOBALS['phpgw_setup']->oProc->f('p_user_gratification'),
				'p_residential_environment' => (int)$GLOBALS['phpgw_setup']->oProc->f('p_residential_environment'),
				'c_safety' => (int)$GLOBALS['phpgw_setup']->oProc->f('c_safety'),
				'c_aesthetics' => (int)$GLOBALS['phpgw_setup']->oProc->f('c_aesthetics'),
				'c_indoor_climate' => (int)$GLOBALS['phpgw_setup']->oProc->f('c_indoor_climate'),
				'c_consequential_damage' => (int)$GLOBALS['phpgw_setup']->oProc->f('c_consequential_damage'),
				'c_user_gratification' => (int)$GLOBALS['phpgw_setup']->oProc->f('c_user_gratification'),
				'c_residential_environment' => (int)$GLOBALS['phpgw_setup']->oProc->f('c_residential_environment')
			);
		}

                if (is_array($condition))
                {
                    foreach($condition as $value)
		{
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition (request_id,condition_type,degree,probability,consequence,user_id,entry_date) "
				. "VALUES ('"
			. $value['request_id'] . "','"
				. 1 . "',"
			. $value['d_safety'] . ","
			. $value['p_safety'] . ","
			. $value['c_safety'] . ","
			. $value['user_id'] . ","
			. $value['entry_date'] . ")", __LINE__, __FILE__);

			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition (request_id,condition_type,degree,probability,consequence,user_id,entry_date) "
				. "VALUES ('"
			. $value['request_id'] . "','"
				. 2 . "',"
			. $value['d_aesthetics'] . ","
			. $value['p_aesthetics'] . ","
			. $value['c_aesthetics'] . ","
			. $value['user_id'] . ","
			. $value['entry_date'] . ")", __LINE__, __FILE__);

			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition (request_id,condition_type,degree,probability,consequence,user_id,entry_date) "
				. "VALUES ('"
			. $value['request_id'] . "','"
				. 3 . "',"
			. $value['d_indoor_climate'] . ","
			. $value['p_indoor_climate'] . ","
			. $value['c_indoor_climate'] . ","
			. $value['user_id'] . ","
			. $value['entry_date'] . ")", __LINE__, __FILE__);

			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition (request_id,condition_type,degree,probability,consequence,user_id,entry_date) "
				. "VALUES ('"
			. $value['request_id'] . "','"
				. 4 . "',"
			. $value['d_consequential_damage'] . ","
			. $value['p_consequential_damage'] . ","
			. $value['c_consequential_damage'] . ","
			. $value['user_id'] . ","
			. $value['entry_date'] . ")", __LINE__, __FILE__);

			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition (request_id,condition_type,degree,probability,consequence,user_id,entry_date) "
				. "VALUES ('"
			. $value['request_id'] . "','"
				. 5 . "',"
			. $value['d_user_gratification'] . ","
			. $value['p_user_gratification'] . ","
			. $value['c_user_gratification'] . ","
			. $value['user_id'] . ","
			. $value['entry_date'] . ")", __LINE__, __FILE__);

			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition (request_id,condition_type,degree,probability,consequence,user_id,entry_date) "
				. "VALUES ('"
			. $value['request_id'] . "','"
				. 6 . "',"
			. $value['d_residential_environment'] . ","
			. $value['p_residential_environment'] . ","
			. $value['c_residential_environment'] . ","
			. $value['user_id'] . ","
			. $value['entry_date'] . ")", __LINE__, __FILE__);

			$id = $value['request_id'];



			$sql = "SELECT sum(priority_key * ( degree * probability * ( consequence +1 ))) AS score FROM fm_request_condition"
			 . " JOIN fm_request_condition_type ON (fm_request_condition.condition_type = fm_request_condition_type.id) WHERE request_id = $id";

			$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);

			$GLOBALS['phpgw_setup']->oProc->next_record();
			$score = $GLOBALS['phpgw_setup']->oProc->f('score');
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET score = $score WHERE id = $id", __LINE__, __FILE__);
		}
                }

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_request_priority_key');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.508';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.508 to 0.9.17.509
	*/
	$test[] = '0.9.17.508';

	function property_upgrade0_9_17_508()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_custom_function', array(
				'fd' => array(
				'acl_location' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'text', 'nullable' => True),
				'file_name ' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'active' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'custom_sort' => array('type' => 'int', 'precision' => '4', 'nullable' => True)
				),
			'pk' => array('acl_location', 'id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.509';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.509 to 0.9.17.510
	*/
	$test[] = '0.9.17.509';

	function property_upgrade0_9_17_509()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilag', 'item_type', array('type' => 'int',
			'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilag', 'item_id', array('type' => 'varchar',
			'precision' => 20, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf', 'item_type', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf', 'item_id', array(
			'type' => 'varchar', 'precision' => 20, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.510';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.510 to 0.9.17.511
	*/
	$test[] = '0.9.17.510';

	function property_upgrade0_9_17_510()
	{
		$table_def = array(
			'fm_custom' => array(
				'fd' => array(
					'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
					'name' => array('type' => 'varchar', 'precision' => '100', 'nullable' => False),
					'sql_text' => array('type' => 'text', 'nullable' => False),
					'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
					'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->m_aTables = $table_def;

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_custom', 'sql', 'sql_text');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.511';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.511 to 0.9.17.512
	*/
	$test[] = '0.9.17.511';

	function property_upgrade0_9_17_511()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_attribute', 'history', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_entity_history', array(
				'fd' => array(
				'history_id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'history_record_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_appname' => array('type' => 'varchar', 'precision' => '64', 'nullable' => False),
				'history_entity_attrib_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_owner' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_status' => array('type' => 'char', 'precision' => '2', 'nullable' => False),
				'history_new_value' => array('type' => 'text', 'nullable' => False),
				'history_timestamp' => array('type' => 'timestamp', 'nullable' => False, 'default' => 'current_timestamp')
				),
				'pk' => array('history_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.512';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.512 to 0.9.17.513
	*/
	$test[] = '0.9.17.512';

	function property_upgrade0_9_17_512()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_r_agreement', array(
				'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False, 'default' => '0'),
				'customer_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'customer_name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
				'name' => array('type' => 'varchar', 'precision' => 100, 'nullable' => False),
				'descr' => array('type' => 'text', 'nullable' => True),
				'status' => array('type' => 'varchar', 'precision' => 10, 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'member_of' => array('type' => 'text', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'start_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'end_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'termination_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'actual_cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,
					'nullable' => True),
				'account_id' => array('type' => 'varchar', 'precision' => 20, 'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_r_agreement_attribute', array(
				'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False, 'default' => '0'),
				'attrib_detail' => array('type' => 'int', 'precision' => 2, 'nullable' => False,
					'default' => '0'),
				'list' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'location_form' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'lookup_form' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'column_name' => array('type' => 'varchar', 'precision' => 20, 'nullable' => False),
				'input_text' => array('type' => 'varchar', 'precision' => 50, 'nullable' => False),
				'statustext' => array('type' => 'varchar', 'precision' => 100, 'nullable' => False),
				'size' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'datatype' => array('type' => 'varchar', 'precision' => 10, 'nullable' => False),
				'attrib_sort' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'precision_' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'scale' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'default_value' => array('type' => 'varchar', 'precision' => 18, 'nullable' => True),
				'nullable' => array('type' => 'varchar', 'precision' => 5, 'nullable' => False,
					'default' => 'True'),
				'search' => array('type' => 'int', 'precision' => 2, 'nullable' => True)
				),
			'pk' => array('id', 'attrib_detail'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_r_agreement_category', array(
				'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False, 'default' => '0'),
				'descr' => array('type' => 'varchar', 'precision' => 50, 'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_r_agreement_choice', array(
				'fd' => array(
				'attrib_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False,
					'default' => '0'),
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False, 'default' => '0'),
				'value' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
				'attrib_detail' => array('type' => 'int', 'precision' => 2, 'nullable' => False,
					'default' => '0')
				),
			'pk' => array('attrib_id', 'id', 'attrib_detail'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_r_agreement_item', array(
				'fd' => array(
				'agreement_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False,
					'default' => '0'),
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False, 'default' => '0'),
				'location_code' => array('type' => 'varchar', 'precision' => 30, 'nullable' => True),
				'address' => array('type' => 'varchar', 'precision' => 100, 'nullable' => True),
				'p_num' => array('type' => 'varchar', 'precision' => 15, 'nullable' => True),
				'p_entity_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True,
					'default' => '0'),
				'p_cat_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True, 'default' => '0'),
				'descr' => array('type' => 'text', 'nullable' => True),
				'unit' => array('type' => 'varchar', 'precision' => 10, 'nullable' => True),
				'quantity' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2, 'nullable' => True),
				'frequency' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'test' => array('type' => 'text', 'nullable' => True),
				'cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2, 'nullable' => True),
				'rental_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
				),
			'pk' => array('agreement_id', 'id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_r_agreement_item_history', array(
				'fd' => array(
				'agreement_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False,
					'default' => '0'),
				'item_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False, 'default' => '0'),
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False, 'default' => '0'),
				'current_index' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'this_index' => array('type' => 'decimal', 'precision' => 20, 'scale' => 4, 'nullable' => True),
				'cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2, 'nullable' => True),
				'index_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'from_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'to_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'tenant_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				),
			'pk' => array('agreement_id', 'item_id', 'id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);


		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_r_agreement_common', array(
				'fd' => array(
				'agreement_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False,
					'default' => '0'),
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False, 'default' => '0'),
				'b_account' => array('type' => 'varchar', 'precision' => 30, 'nullable' => True),
				'remark' => array('type' => 'text', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				),
			'pk' => array('agreement_id', 'id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_r_agreement_c_history', array(
				'fd' => array(
				'agreement_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False,
					'default' => '0'),
				'c_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False, 'default' => '0'),
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False, 'default' => '0'),
				'from_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'to_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'current_record' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'budget_cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,
					'nullable' => True),
				'actual_cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,
					'nullable' => True),
				'fraction' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2, 'nullable' => True),
				'override_fraction' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,
					'nullable' => True),
				),
			'pk' => array('agreement_id', 'c_id', 'id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);


		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_acl_location (id, descr) VALUES ('.r_agreement', 'Rental agreement')");

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.513';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.513 to 0.9.17.514
	*/
	$test[] = '0.9.17.513';

	function property_upgrade0_9_17_513()
	{
		$sql = "SELECT app_version from phpgw_applications WHERE app_name = 'property'";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$version = $GLOBALS['phpgw_setup']->oProc->f('app_version');

		if($version == '0.9.17.513')
		{
			$soadmin_location = CreateObject('property.soadmin_location', 'property');

			for($i = 1; $i <= 4; $i++)
			{
				$attrib = array(
					'column_name' => 'rental_area',
					'input_text' => 'Rental area',
					'statustext' => 'Rental area',
					'type_id' => $i,
					'lookup_form' => False,
					'list' => False,
					'column_info' => array('type' => 'N',
								'precision' => 20,
								'scale' => 2,
								'default' => '0.00',
								'nullable' => 'True')
					);
				$soadmin_location->add_attrib($attrib);
			}
		}

		$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.514';
		return $GLOBALS['setup_info']['property']['currentver'];
	}
	/**
	* Update property version from 0.9.17.514 to 0.9.17.515
	*/
	$test[] = '0.9.17.514';

	function property_upgrade0_9_17_514()
	{
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_owner_attribute (id, list, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable, search) VALUES (1, 1, 'abid', 'Contact', 'Contakt person', NULL, 'AB', 1, 4, NULL, NULL, 'True', NULL)");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_owner_attribute (id, list, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable, search) VALUES (2, 1, 'org_name', 'Name', 'The name of the owner', NULL, 'V', 2, 50, NULL, NULL, 'True', 1)");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_owner_attribute (id, list, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable, search) VALUES (3, 1, 'remark', 'remark', 'remark', NULL, 'T', 3, NULL, NULL, NULL, 'True', NULL)");

		$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.515';
		return $GLOBALS['setup_info']['property']['currentver'];
	}
	/**
	* Update property version from 0.9.17.515 to 0.9.17.516
	*/
	$test[] = '0.9.17.515';

	function property_upgrade0_9_17_515()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_wo_hours', 'cat_per_cent', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.516';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.516 to 0.9.17.517
	*/
	$test[] = '0.9.17.516';

	function property_upgrade0_9_17_516()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_acl_location (id, descr) VALUES ('.budget', 'Budget')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_acl_location (id, descr) VALUES ('.budget.obligations', 'Obligations')");

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_budget_basis', array(
				'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'year' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'b_group' => array('type' => 'varchar', 'precision' => '4', 'nullable' => False),
				'district_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'revision' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'access' => array('type' => 'varchar', 'precision' => '7', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'budget_cost' => array('type' => 'int', 'precision' => 4, 'default' => '0', 'nullable' => True),
				'remark' => array('type' => 'text', 'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
			'uc' => array('year', 'b_group', 'district_id', 'revision')
			)
		);
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_budget', array(
				'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'year' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'b_account_id' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'district_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'revision' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'access' => array('type' => 'varchar', 'precision' => '7', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'budget_cost' => array('type' => 'int', 'precision' => 4, 'default' => '0', 'nullable' => True),
				'remark' => array('type' => 'text', 'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
			'uc' => array('year', 'b_account_id', 'district_id', 'revision')
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_budget_period', array(
				'fd' => array(
				'year' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'month' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'b_account_id' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'percent' => array('type' => 'int', 'precision' => 4, 'default' => '0', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'remark' => array('type' => 'text', 'nullable' => True)
				),
			'pk' => array('year', 'month', 'b_account_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);


		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_budget_cost', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'year' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'month' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'b_account_id' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'amount' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'default' => '0',
					'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
			'uc' => array('year', 'month', 'b_account_id')
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.517';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.517 to 0.9.17.518
	*/
	$test[] = '0.9.17.517';

	function property_upgrade0_9_17_517()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_b_account_category', array(
				'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_b_account', 'category', array('type' => 'int',
			'precision' => 4, 'nullable' => True));

		$sql = "SELECT id, grouping from fm_b_account";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$grouping[] = array(
				'id' => $GLOBALS['phpgw_setup']->oProc->f('id'),
				'grouping' => $GLOBALS['phpgw_setup']->oProc->f('grouping')
			);
		}

		if(is_array($grouping))
		{
			foreach($grouping as $entry)
			{
				if((int)$entry['grouping'] > 0)
				{
					$grouping2[] = $entry['grouping'];

					$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_b_account set category = " . (int)$entry['grouping'] . " WHERE id = " . $entry['id'], __LINE__, __FILE__);
				}
			}
			$grouping2 = array_unique($grouping2);
			foreach($grouping2 as $entry)
			{
				$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_b_account_category (id, descr) VALUES (" . (int)$entry . ",'" . $entry . "')", __LINE__, __FILE__);
			}
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.518';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.518 to 0.9.17.519
	*/
	$test[] = '0.9.17.518';

	function property_upgrade0_9_17_518()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_template_hours', 'entry_date', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.519';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.519 to 0.9.17.520
	*/
	$test[] = '0.9.17.519';

	function property_upgrade0_9_17_519()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_request', 'start_date', array('type' => 'int',
			'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_request', 'end_date', array('type' => 'int',
			'precision' => 4, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.520';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.520 to 0.9.17.521
	*/
	$test[] = '0.9.17.520';

	function property_upgrade0_9_17_520()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_budget_basis', 'distribute_year', array(
			'type' => 'text', 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.521';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.521 to 0.9.17.522
	*/
	$test[] = '0.9.17.521';

	function property_upgrade0_9_17_521()
	{
//		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin(); transaction have problem with nested db-objects

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder', 'combined_cost', array(
			'type' => 'decimal', 'precision' => '20', 'scale' => '2', 'nullable' => True,
			'default' => '0.00'));

		$sql = "SELECT app_version from phpgw_applications WHERE app_name = 'property'";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$version = $GLOBALS['phpgw_setup']->oProc->f('app_version');

		if($version == '0.9.17.521')
		{
			$db2 = clone($GLOBALS['phpgw_setup']->oProc->m_odb);
			$sql = "SELECT id, budget, calculation from fm_workorder";
			$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
			while($GLOBALS['phpgw_setup']->oProc->next_record())
			{
				if($GLOBALS['phpgw_setup']->oProc->f('calculation') > 0)
				{
					$combined_cost = ($GLOBALS['phpgw_setup']->oProc->f('calculation') * 1.25); // tax included
				}
				else
				{
					$combined_cost = $GLOBALS['phpgw_setup']->oProc->f('budget');
				}

				if($combined_cost > 0)
				{

					$db2->query("UPDATE fm_workorder SET combined_cost = '$combined_cost' WHERE id = " . (int)$GLOBALS['phpgw_setup']->oProc->f('id'), __LINE__, __FILE__);
				}
			}
		}

//		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.522';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.522 to 0.9.17.523
	*/
	$test[] = '0.9.17.522';

	function property_upgrade0_9_17_522()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder', 'paid', array('type' => 'int',
			'precision' => '2', 'nullable' => True, 'default' => '1'));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.523';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.523 to 0.9.17.524
	*/
	$test[] = '0.9.17.523';

	function property_upgrade0_9_17_523()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_acl_location (id, descr) VALUES ('.admin', 'Admin')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_acl_location (id, descr) VALUES ('.admin.entity', 'Admin entity')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_acl_location (id, descr) VALUES ('.admin.location', 'Admin location')");
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.524';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.524 to 0.9.17.525
	*/
	$test[] = '0.9.17.524';

	function property_upgrade0_9_17_524()
	{
//		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin(); transaction have problem with nested db-objects

		$GLOBALS['phpgw_setup']->oProc->query("delete from phpgw_acl where acl_appname = 'property' AND acl_location !='run' ");

		$db2 = clone($GLOBALS['phpgw_setup']->oProc->m_odb);
		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_acl_location ");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$db2->query("INSERT INTO phpgw_acl_location (appname,id, descr,allow_grant) VALUES ("
			. " 'property','"
			. $GLOBALS['phpgw_setup']->oProc->f('id') . "','"
			. $GLOBALS['phpgw_setup']->oProc->f('descr') . "',"
			. (int)$GLOBALS['phpgw_setup']->oProc->f('allow_grant') . ")");
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_acl2 ");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$grantor = 'NULL';
			if($GLOBALS['phpgw_setup']->oProc->f('grantor') > 0)
			{
				$grantor = $GLOBALS['phpgw_setup']->oProc->f('grantor');
			}

			$db2->query("INSERT INTO phpgw_acl (acl_appname, acl_location, acl_account, acl_rights, acl_grantor,acl_type) VALUES ("
			. "'property','"
			. $GLOBALS['phpgw_setup']->oProc->f('acl_location') . "','"
			. $GLOBALS['phpgw_setup']->oProc->f('acl_account') . "','"
			. $GLOBALS['phpgw_setup']->oProc->f('acl_rights') . "',"
			. $grantor . ",'"
			. (int)$GLOBALS['phpgw_setup']->oProc->f('acl_type') . "')");

			unset($grantor);
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_acl_location');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_acl2');

//		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.525';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.525 to 0.9.17.526
	*/
	$test[] = '0.9.17.525';

	function property_upgrade0_9_17_525()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_tenant_attribute', 'input_text', array(
			'type' => 'varchar', 'precision' => '50', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_vendor_attribute', 'input_text', array(
			'type' => 'varchar', 'precision' => '50', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location_attrib', 'input_text', array(
			'type' => 'varchar', 'precision' => '50', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_owner_attribute', 'input_text', array(
			'type' => 'varchar', 'precision' => '50', 'nullable' => False));
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.526';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.526 to 0.9.17.527
	*/
	$test[] = '0.9.17.526';

	function property_upgrade0_9_17_526()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_attribute', 'disabled', array(
			'type' => 'int', 'precision' => '4', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_attribute', 'helpmsg', array(
			'type' => 'text', 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.527';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.527 to 0.9.17.528
	*/
	$test[] = '0.9.17.527';

	function property_upgrade0_9_17_527()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_gab_location', 'location_code', array(
			'type' => 'varchar', 'precision' => '20', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_gab_location', 'loc1', array('type' => 'varchar',
			'precision' => '6', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location1', 'loc1', array('type' => 'varchar',
			'precision' => '6', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location1_history', 'loc1', array(
			'type' => 'varchar', 'precision' => '6', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location2', 'loc1', array('type' => 'varchar',
			'precision' => '6', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location2_history', 'loc1', array(
			'type' => 'varchar', 'precision' => '6', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location3', 'loc1', array('type' => 'varchar',
			'precision' => '6', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location3_history', 'loc1', array(
			'type' => 'varchar', 'precision' => '6', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location4', 'loc1', array('type' => 'varchar',
			'precision' => '6', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location4_history', 'loc1', array(
			'type' => 'varchar', 'precision' => '6', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'loc1', array('type' => 'varchar',
			'precision' => '6', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_tts_tickets', 'loc1', array('type' => 'varchar',
			'precision' => '6', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_project', 'loc1', array('type' => 'varchar',
			'precision' => '6', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_investment', 'loc1', array('type' => 'varchar',
			'precision' => '6', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_document', 'loc1', array('type' => 'varchar',
			'precision' => '6', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_entity_1_1', 'loc1', array('type' => 'varchar',
			'precision' => '6', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_entity_1_2', 'loc1', array('type' => 'varchar',
			'precision' => '6', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_entity_1_3', 'loc1', array('type' => 'varchar',
			'precision' => '6', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_entity_2_1', 'loc1', array('type' => 'varchar',
			'precision' => '6', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_entity_2_2', 'loc1', array('type' => 'varchar',
			'precision' => '6', 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_attrib set precision_ = '6' where column_name = 'loc1'");

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.528';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.528 to 0.9.17.529
	*/
	$test[] = '0.9.17.528';

	function property_upgrade0_9_17_528()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_acl_location set id = '.agreement', descr = 'Agreement' where id = '.pricebook' AND appname = 'property'");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_acl set acl_location = '.agreement' where acl_location = '.pricebook' AND acl_appname = 'property'");

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.529';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.529 to 0.9.17.530
	*/
	$test[] = '0.9.17.529';

	function property_upgrade0_9_17_529()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname, id, descr) VALUES ('property', '.ticket.external', 'Helpdesk External user')");
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tenant', 'phpgw_account_lid', array(
			'type' => 'varchar', 'precision' => '25', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tenant', 'account_lid', array('type' => 'varchar',
			'precision' => '25', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tenant', 'account_pwd', array('type' => 'varchar',
			'precision' => '32', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tenant', 'account_status', array(
			'type' => 'char', 'precision' => '1', 'nullable' => True, 'default' => 'A'));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.530';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.530 to 0.9.17.531
	*/
	$test[] = '0.9.17.530';

	function property_upgrade0_9_17_530()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$fm_tenant = array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'member_of' => array('type' => 'varchar', 'precision' => '255', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'first_name' => array('type' => 'varchar', 'precision' => '30', 'nullable' => True),
				'last_name' => array('type' => 'varchar', 'precision' => '30', 'nullable' => True),
				'contact_phone' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'phpgw_account_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'account_lid' => array('type' => 'varchar', 'precision' => '25', 'nullable' => True),
				'account_pwd' => array('type' => 'varchar', 'precision' => '32', 'nullable' => True),
				'account_status' => array('type' => 'char', 'precision' => '1', 'nullable' => True,
					'default' => 'A')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		);

		$fm_tenant2 = array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'member_of' => array('type' => 'varchar', 'precision' => '255', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'first_name' => array('type' => 'varchar', 'precision' => '30', 'nullable' => True),
				'last_name' => array('type' => 'varchar', 'precision' => '30', 'nullable' => True),
				'contact_phone' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'phpgw_account_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'account_lid' => array('type' => 'varchar', 'precision' => '25', 'nullable' => True),
				'account_pwd' => array('type' => 'varchar', 'precision' => '32', 'nullable' => True),
				'account_status' => array('type' => 'int', 'precision' => '4', 'nullable' => True,
					'default' => '1')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		);

		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_tenant', $fm_tenant, 'phpgw_account_lid');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_tenant', $fm_tenant2, 'account_status');
		unset($fm_tenant);
		unset($fm_tenant2);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tenant', 'phpgw_account_id', array(
			'type' => 'int', 'precision' => '4', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tenant', 'account_status', array(
			'type' => 'int', 'precision' => '4', 'nullable' => True, 'default' => '1'));

		$GLOBALS['phpgw_setup']->oProc->query("SELECT max(id) as id, max(attrib_sort) as attrib_sort FROM fm_tenant_attribute");

		$GLOBALS['phpgw_setup']->oProc->next_record();
		$id = $GLOBALS['phpgw_setup']->oProc->f('id') + 1;
		$attrib_sort = $GLOBALS['phpgw_setup']->oProc->f('attrib_sort') + 1;

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tenant_attribute (id, list, search, lookup_form, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable) VALUES ($id, NULL, NULL, NULL, 'phpgw_account_id', 'Mapped User', 'Mapped User', NULL, 'user', $attrib_sort, 4, NULL, NULL, 'True')");
		$id++;
		$attrib_sort++;
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tenant_attribute (id, list, search, lookup_form, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable) VALUES ($id, NULL, NULL, NULL, 'account_lid', 'User Name', 'User name for login', NULL, 'V', $attrib_sort, 25, NULL, NULL, 'True')");
		$id++;
		$attrib_sort++;
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tenant_attribute (id, list, search, lookup_form, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable) VALUES ($id, NULL, NULL, NULL, 'account_pwd', 'Password', 'Users Password', NULL, 'pwd', $attrib_sort, 32, NULL, NULL, 'True')");
		$id++;
		$attrib_sort++;
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tenant_attribute (id, list, search, lookup_form, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable) VALUES ($id, NULL, NULL, NULL, 'account_status', 'account status', 'account status', NULL, 'LB', $attrib_sort, NULL, NULL, NULL, 'True')");

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tenant_choice (attrib_id, id, value) VALUES ($id, 1, 'Active')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tenant_choice (attrib_id, id, value) VALUES ($id, 2, 'Banned')");
		unset($id);
		unset($attrib_sort);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.531';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.531 to 0.9.17.532
	*/
	$test[] = '0.9.17.531';

	function property_upgrade0_9_17_531()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tenant', 'owner_id', array('type' => 'int',
			'precision' => '4', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_owner', 'owner_id', array('type' => 'int',
			'precision' => '4', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_vendor', 'owner_id', array('type' => 'int',
			'precision' => '4', 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_tenant set owner_id = 6");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_owner set owner_id = 6");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_vendor set owner_id = 6");

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_acl WHERE acl_appname = 'property' AND acl_location = '.tenant' AND acl_grantor IS NOT NULL");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl (acl_appname, acl_location, acl_account, acl_rights, acl_grantor, acl_type) VALUES ('property', '.tenant', '1', '1', '6', '0')");
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_acl WHERE acl_appname = 'property' AND acl_location = '.owner' AND acl_grantor IS NOT NULL");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl (acl_appname, acl_location, acl_account, acl_rights, acl_grantor, acl_type) VALUES ('property', '.owner', '1', '1','6', '0')");
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_acl WHERE acl_appname = 'property' AND acl_location = '.vendor' AND acl_grantor IS NOT NULL");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl (acl_appname, acl_location, acl_account, acl_rights, acl_grantor, acl_type) VALUES ('property', '.vendor', '1', '1', '6', '0')");

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.532';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.532 to 0.9.17.533
	*/
	$test[] = '0.9.17.532';

	function property_upgrade0_9_17_532()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_template_hours', 'hours_descr', array(
			'type' => 'text', 'nullable' => True));
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.533';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.533 to 0.9.17.534
	*/
	$test[] = '0.9.17.533';

	function property_upgrade0_9_17_533()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location_type', 'list_info', array(
			'type' => 'varchar', 'precision' => '255', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location_type', 'list_address', array(
			'type' => 'int', 'precision' => '2', 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_type set list_info = '" . 'a:1:{i:1;s:1:"1";}' . "' WHERE id = '1'");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_type set list_info = '" . 'a:2:{i:1;s:1:"1";i:2;s:1:"2";}' . "' WHERE id = '2'");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_type set list_info = '" . 'a:3:{i:1;s:1:"1";i:2;s:1:"2";i:3;s:1:"3";}' . "' WHERE id = '3'");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_type set list_info = '" . 'a:1:{i:1;s:1:"1";}' . "' WHERE id = '4'");
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.534';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.534 to 0.9.17.535
	*/
	$test[] = '0.9.17.534';

	function property_upgrade0_9_17_534()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname, id, descr) VALUES ('property', '.location.1', 'Property')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname, id, descr) VALUES ('property', '.location.2', 'Building')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname, id, descr) VALUES ('property', '.location.3', 'Entrance')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname, id, descr) VALUES ('property', '.location.4', 'Apartment')");
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.535';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.535 to 0.9.17.536
	*/
	$test[] = '0.9.17.535';

	function property_upgrade0_9_17_535()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$table_def = array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '25', 'nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		);

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_dim_d');

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_ecodimd', 'name', 'descr');
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_ecodimd', 'descr', array('type' => 'varchar',
			'precision' => '25', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_ecodimd', $table_def, 'description');

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_ecodimb', 'name', 'descr');
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_ecodimb', 'descr', array('type' => 'varchar',
			'precision' => '25', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_ecodimb', $table_def, 'description');

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_ecomva', 'name', 'descr');
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_ecomva', 'descr', array('type' => 'varchar',
			'precision' => '25', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_ecomva', $table_def, 'description');

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_ecobilagtype', 'name', 'descr');
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_ecobilagtype', 'descr', array(
			'type' => 'varchar', 'precision' => '25', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_ecobilagtype', $table_def, 'description');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('fm_ecobilagtype', 'fm_ecobilag_category');

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_ecoart', 'name', 'descr');
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_ecoart', 'descr', array('type' => 'varchar',
			'precision' => '25', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_ecoart', $table_def, 'description');

		unset($table_def);
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.536';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.536 to 0.9.17.537
	*/
	$test[] = '0.9.17.536';

	function property_upgrade0_9_17_536()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_project', 'end_date', array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => 'True'
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.537';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.537 to 0.9.17.538
	*/
	$test[] = '0.9.17.537';

	function property_upgrade0_9_17_537()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();


		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_s_agreement_attribute', 'history', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_s_agreement_history', array(
				'fd' => array(
				'history_id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'history_record_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_appname' => array('type' => 'varchar', 'precision' => '64', 'nullable' => False),
				'history_detail_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_attrib_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_owner' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_status' => array('type' => 'char', 'precision' => '2', 'nullable' => False),
				'history_new_value' => array('type' => 'text', 'nullable' => False),
				'history_timestamp' => array('type' => 'timestamp', 'nullable' => False, 'default' => 'current_timestamp')
				),
				'pk' => array('history_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.538';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.538 to 0.9.17.539
	*/
	$test[] = '0.9.17.538';

	function property_upgrade0_9_17_538()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_entity_history', 'history_entity_attrib_id', 'history_attrib_id');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.539';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.539 to 0.9.17.540
	*/
	$test[] = '0.9.17.539';

	function property_upgrade0_9_17_539()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category', 'start_ticket', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.540';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.540 to 0.9.17.541
	*/
	$test[] = '0.9.17.540';

	function property_upgrade0_9_17_540()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw']->locations->add('.s_agreement.detail', 'Service agreement detail', 'property', $allow_grant = false, $custom_tbl = 'fm_s_agreement_detail', $c_function = false);
		$GLOBALS['phpgw']->locations->add('.r_agreement.detail', 'Rental agreement detail', 'property', $allow_grant = false, $custom_tbl = 'fm_r_agreement_detail', $c_function = false);

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_agreement_attribute");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$attrib[] = array(
				'location_id' => $GLOBALS['phpgw_setup']->oProc->f('attrib_detail') == 1 ? '.agreement' : '.agreement.detail',
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'column_name'	=> $GLOBALS['phpgw_setup']->oProc->f('column_name'),
					'input_text'	=> $GLOBALS['phpgw_setup']->oProc->f('input_text'),
					'statustext'	=> $GLOBALS['phpgw_setup']->oProc->f('statustext'),
					'datatype'		=> $GLOBALS['phpgw_setup']->oProc->f('datatype'),
					'search'		=> $GLOBALS['phpgw_setup']->oProc->f('search'),
					'history'		=> $GLOBALS['phpgw_setup']->oProc->f('history'),
					'list'			=> $GLOBALS['phpgw_setup']->oProc->f('list'),
					'attrib_sort'	=> $GLOBALS['phpgw_setup']->oProc->f('attrib_sort'),
					'size'			=> $GLOBALS['phpgw_setup']->oProc->f('size'),
					'precision_'	=> $GLOBALS['phpgw_setup']->oProc->f('precision_'),
					'scale'			=> $GLOBALS['phpgw_setup']->oProc->f('scale'),
					'default_value'	=> $GLOBALS['phpgw_setup']->oProc->f('default_value'),
					'nullable'		=> $GLOBALS['phpgw_setup']->oProc->f('nullable'),
					'custom'		=> 1
 			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_r_agreement_attribute");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$attrib[] = array(
				'location_id' => $GLOBALS['phpgw_setup']->oProc->f('attrib_detail') == 1 ? '.r_agreement' : '.r_agreement.detail',
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'column_name'	=> $GLOBALS['phpgw_setup']->oProc->f('column_name'),
					'input_text'	=> $GLOBALS['phpgw_setup']->oProc->f('input_text'),
					'statustext'	=> $GLOBALS['phpgw_setup']->oProc->f('statustext'),
					'datatype'		=> $GLOBALS['phpgw_setup']->oProc->f('datatype'),
					'search'		=> $GLOBALS['phpgw_setup']->oProc->f('search'),
					'history'		=> $GLOBALS['phpgw_setup']->oProc->f('history'),
					'list'			=> $GLOBALS['phpgw_setup']->oProc->f('list'),
					'attrib_sort'	=> $GLOBALS['phpgw_setup']->oProc->f('attrib_sort'),
					'size'			=> $GLOBALS['phpgw_setup']->oProc->f('size'),
					'precision_'	=> $GLOBALS['phpgw_setup']->oProc->f('precision_'),
					'scale'			=> $GLOBALS['phpgw_setup']->oProc->f('scale'),
					'default_value'	=> $GLOBALS['phpgw_setup']->oProc->f('default_value'),
					'nullable'		=> $GLOBALS['phpgw_setup']->oProc->f('nullable'),
					'custom'		=> 1
 			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_s_agreement_attribute");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$attrib[] = array(
				'location_id' => $GLOBALS['phpgw_setup']->oProc->f('attrib_detail') == 1 ? '.s_agreement' : '.s_agreement.detail',
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'column_name'	=> $GLOBALS['phpgw_setup']->oProc->f('column_name'),
					'input_text'	=> $GLOBALS['phpgw_setup']->oProc->f('input_text'),
					'statustext'	=> $GLOBALS['phpgw_setup']->oProc->f('statustext'),
					'datatype'		=> $GLOBALS['phpgw_setup']->oProc->f('datatype'),
					'search'		=> $GLOBALS['phpgw_setup']->oProc->f('search'),
					'history'		=> $GLOBALS['phpgw_setup']->oProc->f('history'),
					'list'			=> $GLOBALS['phpgw_setup']->oProc->f('list'),
					'attrib_sort'	=> $GLOBALS['phpgw_setup']->oProc->f('attrib_sort'),
					'size'			=> $GLOBALS['phpgw_setup']->oProc->f('size'),
					'precision_'	=> $GLOBALS['phpgw_setup']->oProc->f('precision_'),
					'scale'			=> $GLOBALS['phpgw_setup']->oProc->f('scale'),
					'default_value'	=> $GLOBALS['phpgw_setup']->oProc->f('default_value'),
					'nullable'		=> $GLOBALS['phpgw_setup']->oProc->f('nullable'),
					'custom'		=> 1
 			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_owner_attribute");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$attrib[] = array(
					'location_id'	=> '.owner',
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'column_name'	=> $GLOBALS['phpgw_setup']->oProc->f('column_name'),
					'input_text'	=> $GLOBALS['phpgw_setup']->oProc->f('input_text'),
					'statustext'	=> $GLOBALS['phpgw_setup']->oProc->f('statustext'),
					'datatype'		=> $GLOBALS['phpgw_setup']->oProc->f('datatype'),
					'search'		=> $GLOBALS['phpgw_setup']->oProc->f('search'),
					'history'		=> $GLOBALS['phpgw_setup']->oProc->f('history'),
					'list'			=> $GLOBALS['phpgw_setup']->oProc->f('list'),
					'attrib_sort'	=> $GLOBALS['phpgw_setup']->oProc->f('attrib_sort'),
					'size'			=> $GLOBALS['phpgw_setup']->oProc->f('size'),
					'precision_'	=> $GLOBALS['phpgw_setup']->oProc->f('precision_'),
					'scale'			=> $GLOBALS['phpgw_setup']->oProc->f('scale'),
					'default_value'	=> $GLOBALS['phpgw_setup']->oProc->f('default_value'),
					'nullable'		=> $GLOBALS['phpgw_setup']->oProc->f('nullable'),
					'custom'		=> 1
 			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_tenant_attribute");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$attrib[] = array(
					'location_id'	=> '.tenant',
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'column_name'	=> $GLOBALS['phpgw_setup']->oProc->f('column_name'),
					'input_text'	=> $GLOBALS['phpgw_setup']->oProc->f('input_text'),
					'statustext'	=> $GLOBALS['phpgw_setup']->oProc->f('statustext'),
					'datatype'		=> $GLOBALS['phpgw_setup']->oProc->f('datatype'),
					'search'		=> $GLOBALS['phpgw_setup']->oProc->f('search'),
					'history'		=> $GLOBALS['phpgw_setup']->oProc->f('history'),
					'list'			=> $GLOBALS['phpgw_setup']->oProc->f('list'),
					'attrib_sort'	=> $GLOBALS['phpgw_setup']->oProc->f('attrib_sort'),
					'size'			=> $GLOBALS['phpgw_setup']->oProc->f('size'),
					'precision_'	=> $GLOBALS['phpgw_setup']->oProc->f('precision_'),
					'scale'			=> $GLOBALS['phpgw_setup']->oProc->f('scale'),
					'default_value'	=> $GLOBALS['phpgw_setup']->oProc->f('default_value'),
					'nullable'		=> $GLOBALS['phpgw_setup']->oProc->f('nullable'),
					'custom'		=> 1
 			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_vendor_attribute");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$attrib[] = array(
					'location_id'	=> '.vendor',
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'column_name'	=> $GLOBALS['phpgw_setup']->oProc->f('column_name'),
					'input_text'	=> $GLOBALS['phpgw_setup']->oProc->f('input_text'),
					'statustext'	=> $GLOBALS['phpgw_setup']->oProc->f('statustext'),
					'datatype'		=> $GLOBALS['phpgw_setup']->oProc->f('datatype'),
					'search'		=> $GLOBALS['phpgw_setup']->oProc->f('search'),
					'history'		=> $GLOBALS['phpgw_setup']->oProc->f('history'),
					'list'			=> $GLOBALS['phpgw_setup']->oProc->f('list'),
					'attrib_sort'	=> $GLOBALS['phpgw_setup']->oProc->f('attrib_sort'),
					'size'			=> $GLOBALS['phpgw_setup']->oProc->f('size'),
					'precision_'	=> $GLOBALS['phpgw_setup']->oProc->f('precision_'),
					'scale'			=> $GLOBALS['phpgw_setup']->oProc->f('scale'),
					'default_value'	=> $GLOBALS['phpgw_setup']->oProc->f('default_value'),
					'nullable'		=> $GLOBALS['phpgw_setup']->oProc->f('nullable'),
					'custom'		=> 1
 			);
		}

		foreach($attrib as & $entry)
		{
			$entry['location_id'] = $GLOBALS['phpgw']->locations->get_id('property', $entry['location_id']);
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_cust_attribute (' . implode(',', array_keys($entry)) . ') VALUES (' . $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($entry)) . ')');
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_agreement_choice");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$choice[] = array(
				'location_id' => $GLOBALS['phpgw_setup']->oProc->f('attrib_detail') == 1 ? '.agreement' : '.agreement.detail',
					'attrib_id'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'value'			=> $GLOBALS['phpgw_setup']->oProc->f('value')
			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_r_agreement_choice");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$choice[] = array(
				'location_id' => $GLOBALS['phpgw_setup']->oProc->f('attrib_detail') == 1 ? '.r_agreement' : '.r_agreement.detail',
					'attrib_id'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'value'			=> $GLOBALS['phpgw_setup']->oProc->f('value')
			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_s_agreement_choice");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$choice[] = array(
				'location_id' => $GLOBALS['phpgw_setup']->oProc->f('attrib_detail') == 1 ? '.s_agreement' : '.s_agreement.detail',
					'attrib_id'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'value'			=> $GLOBALS['phpgw_setup']->oProc->f('value')
			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_owner_choice");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$choice[] = array(
					'location_id'	=> '.owner',
					'attrib_id'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'value'			=> $GLOBALS['phpgw_setup']->oProc->f('value')
			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_tenant_choice");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$choice[] = array(
					'location_id'	=> '.tenant',
					'attrib_id'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'value'			=> $GLOBALS['phpgw_setup']->oProc->f('value')
			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_vendor_choice");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$choice[] = array(
					'location_id'	=> '.vendor',
					'attrib_id'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'value'			=> $GLOBALS['phpgw_setup']->oProc->f('value')
			);
		}

		foreach($choice as & $entry)
		{
			$entry['location_id'] = $GLOBALS['phpgw']->locations->get_id('property', $entry['location_id']);
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_cust_choice (' . implode(',', array_keys($entry)) . ') VALUES (' . $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($entry)) . ')');
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_agreement_attribute');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_r_agreement_attribute');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_s_agreement_attribute');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_owner_attribute');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_tenant_attribute');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_vendor_attribute');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_agreement_choice');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_r_agreement_choice');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_s_agreement_choice');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_owner_choice');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_tenant_choice');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_vendor_choice');

//---------------entity
		$attrib = array();
		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_entity_attribute");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$attrib[] = array(
					'location_id'	=> '.entity.' . $GLOBALS['phpgw_setup']->oProc->f('entity_id') . '.' . $GLOBALS['phpgw_setup']->oProc->f('cat_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'column_name'	=> $GLOBALS['phpgw_setup']->oProc->f('column_name'),
					'input_text'	=> $GLOBALS['phpgw_setup']->oProc->f('input_text'),
					'statustext'	=> $GLOBALS['phpgw_setup']->oProc->f('statustext'),
					'datatype'		=> $GLOBALS['phpgw_setup']->oProc->f('datatype'),
					'search'		=> $GLOBALS['phpgw_setup']->oProc->f('search'),
					'history'		=> $GLOBALS['phpgw_setup']->oProc->f('history'),
					'list'			=> $GLOBALS['phpgw_setup']->oProc->f('list'),
					'attrib_sort'	=> $GLOBALS['phpgw_setup']->oProc->f('attrib_sort'),
					'size'			=> $GLOBALS['phpgw_setup']->oProc->f('size'),
					'precision_'	=> $GLOBALS['phpgw_setup']->oProc->f('precision_'),
					'scale'			=> $GLOBALS['phpgw_setup']->oProc->f('scale'),
					'default_value'	=> $GLOBALS['phpgw_setup']->oProc->f('default_value'),
					'nullable'		=> $GLOBALS['phpgw_setup']->oProc->f('nullable'),
					'helpmsg'		=> $GLOBALS['phpgw_setup']->oProc->f('helpmsg'),
					'custom'		=> 1
 			);
		}

		foreach($attrib as & $entry)
		{
			$entry['location_id'] = $GLOBALS['phpgw']->locations->get_id('property', $entry['location_id']);
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_cust_attribute (' . implode(',', array_keys($entry)) . ') VALUES (' . $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($entry)) . ')');
		}

		$choice = array();
		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_entity_choice");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$choice[] = array(
					'location_id'	=> '.entity.' . $GLOBALS['phpgw_setup']->oProc->f('entity_id') . '.' . $GLOBALS['phpgw_setup']->oProc->f('cat_id'),
					'attrib_id'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'value'			=> $GLOBALS['phpgw_setup']->oProc->f('value')
			);
		}

		foreach($choice as & $entry)
		{
			$entry['location_id'] = $GLOBALS['phpgw']->locations->get_id('property', $entry['location_id']);
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_cust_choice (' . implode(',', array_keys($entry)) . ') VALUES (' . $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($entry)) . ')');
		}

		$location = array();

		$app_id = $GLOBALS['phpgw']->applications->name2id('property');
		$GLOBALS['phpgw_setup']->oProc->query("SELECT location_id,name FROM phpgw_locations WHERE app_id = {$app_id} AND name LIKE '.entity.%'");

		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$location[] = array
			(
				'location_id'	=> $GLOBALS['phpgw_setup']->oProc->f('location_id'),
				'name'			=> $GLOBALS['phpgw_setup']->oProc->f('name')
			);
		}

		foreach($location as $entry)
		{
			if(strlen($entry['name']) > 10)
			{
				$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_locations SET allow_c_attrib=1 ,c_attrib_table ='fm" . str_replace('.', '_', $entry['name']) . "' WHERE location_id = {$entry['location_id']}");
			}
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_entity_attribute');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_entity_choice');

//---------------
//--------------- custom functions
		$custom = array();
		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_custom_function");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$custom[] = array(
					'location_id'	=> $GLOBALS['phpgw_setup']->oProc->f('acl_location'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'descr'			=> $GLOBALS['phpgw_setup']->oProc->f('descr'),
					'file_name'		=> $GLOBALS['phpgw_setup']->oProc->f('file_name'),
					'active'		=> $GLOBALS['phpgw_setup']->oProc->f('active'),
					'custom_sort'	=> $GLOBALS['phpgw_setup']->oProc->f('custom_sort')
			);
		}

		foreach($custom as & $entry)
		{
			$entry['location_id'] = $GLOBALS['phpgw']->locations->get_id('property', $entry['location_id']);
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_cust_function (' . implode(',', array_keys($entry)) . ') VALUES (' . $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($entry)) . ')');
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_custom_function');
//----------------
//--------------- locations

		$attrib = array();
		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_location_attrib");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$attrib[] = array(
					'location_id'		=> '.location.' . $GLOBALS['phpgw_setup']->oProc->f('type_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'column_name'	=> $GLOBALS['phpgw_setup']->oProc->f('column_name'),
					'input_text'	=> $GLOBALS['phpgw_setup']->oProc->f('input_text'),
					'statustext'	=> $GLOBALS['phpgw_setup']->oProc->f('statustext'),
					'datatype'		=> $GLOBALS['phpgw_setup']->oProc->f('datatype'),
					'search'		=> $GLOBALS['phpgw_setup']->oProc->f('search'),
					'history'		=> $GLOBALS['phpgw_setup']->oProc->f('history'),
					'list'			=> $GLOBALS['phpgw_setup']->oProc->f('list'),
					'attrib_sort'	=> $GLOBALS['phpgw_setup']->oProc->f('attrib_sort'),
					'size'			=> $GLOBALS['phpgw_setup']->oProc->f('size'),
					'precision_'	=> $GLOBALS['phpgw_setup']->oProc->f('precision_'),
					'scale'			=> $GLOBALS['phpgw_setup']->oProc->f('scale'),
					'default_value'	=> $GLOBALS['phpgw_setup']->oProc->f('default_value'),
					'nullable'		=> $GLOBALS['phpgw_setup']->oProc->f('nullable'),
					'helpmsg'		=> $GLOBALS['phpgw_setup']->oProc->f('helpmsg'),
					'lookup_form'	=> $GLOBALS['phpgw_setup']->oProc->f('lookup_form'),
					'custom'		=> $GLOBALS['phpgw_setup']->oProc->f('custom'),
 			);
		}

		foreach($attrib as & $entry)
		{
			$entry['location_id'] = $GLOBALS['phpgw']->locations->get_id('property', $entry['location_id']);
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_cust_attribute (' . implode(',', array_keys($entry)) . ') VALUES (' . $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($entry)) . ')');
		}

		$choice = array();
		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_location_choice");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$choice[] = array(
					'location_id'	=> '.location.' . $GLOBALS['phpgw_setup']->oProc->f('type_id'),
					'attrib_id'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'value'			=> $GLOBALS['phpgw_setup']->oProc->f('value')
			);
		}

		foreach($choice as & $entry)
		{
			$entry['location_id'] = $GLOBALS['phpgw']->locations->get_id('property', $entry['location_id']);
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_cust_choice (' . implode(',', array_keys($entry)) . ') VALUES (' . $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($entry)) . ')');
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_location_attrib');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_location_choice');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.541';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.541 to 0.9.17.542
	* 'percent' is reserved for mssql
	*/
	$test[] = '0.9.17.541';

	function property_upgrade0_9_17_541()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_budget_period', 'percent', 'per_cent');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.542';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.542 to 0.9.17.543
	* Move files from 'home' to 'property'
 	*/
	$test[] = '0.9.17.542';

	function property_upgrade0_9_17_542()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$change = array
		(
			'/home/document'			=> '/property/document',
			'/home/fmticket'			=> '/property/fmticket',
			'/home/request'				=> '/property/request',
			'/home/workorder'			=> '/property/workorder',
			'/home/service_agreement'	=> '/property/service_agreement',
			'/home/rental_agreement'	=> '/property/rental_agreement',
			'/home/agreement'			=> '/property/agreement'
		);

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_entity_category");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$entity = "entity_{$GLOBALS['phpgw_setup']->oProc->f('entity_id')}_{$GLOBALS['phpgw_setup']->oProc->f('id')}";
			$change["/home/{$entity}"] = "/property/{$entity}";
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT config_value FROM phpgw_config WHERE config_app = 'phpgwapi' AND config_name = 'files_dir'");
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$files_dir = $GLOBALS['phpgw_setup']->oProc->f('config_value');

		@mkdir($files_dir . '/property', 0770);

		foreach($change as $change_from => $change_to)
		{
			@rename($files_dir . $change_from, $files_dir . $change_to);
		}

		$change_from = array_keys($change);
        $change_to = array_values($change);

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM phpgw_vfs WHERE app = 'property'");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$files[] = array(
				'file_id'	=> $GLOBALS['phpgw_setup']->oProc->f('file_id'),
				'directory'	=> str_ireplace($change_from, $change_to, $GLOBALS['phpgw_setup']->oProc->f('directory')),
			);
		}

		foreach($files as $file)
		{
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_vfs SET directory ='{$file['directory']}' WHERE file_id = {$file['file_id']}");
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.543';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.543 to 0.9.17.544
	* FIXME: Figure out the correct conversion of categories that comply with interlink
 	*/
	$test[] = '0.9.17.543';

	function property_upgrade0_9_17_543()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		// Need account_repository, accounts, acl and hooks to use categories
		$GLOBALS['phpgw_setup']->oProc->query("SELECT config_value FROM phpgw_config WHERE config_app = 'phpgwapi' AND config_name = 'account_repository'");
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$GLOBALS['phpgw_info']['server']['account_repository'] = $GLOBALS['phpgw_setup']->oProc->f('config_value');

		$GLOBALS['phpgw']->accounts		= createObject('phpgwapi.accounts');

		$GLOBALS['phpgw']->db = & $GLOBALS['phpgw_setup']->oProc->m_odb;
		$GLOBALS['phpgw']->acl = CreateObject('phpgwapi.acl');
		$GLOBALS['phpgw']->hooks = CreateObject('phpgwapi.hooks', $GLOBALS['phpgw_setup']->oProc->m_odb);
		$cats = CreateObject('phpgwapi.categories', -1, 'property.ticket');

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_tts_category");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$categories[$GLOBALS['phpgw_setup']->oProc->f('id')] = array(
				'name'	=> $GLOBALS['phpgw_setup']->oProc->f('descr', true),
				'descr'	=> $GLOBALS['phpgw_setup']->oProc->f('descr', true),
				'parent' => 'none',
				'old_parent' => 0,
				'access' => 'public'
			);
		}

		foreach($categories as $old => $values)
		{
			$cat_id = $cats->add($values);
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_tts_tickets SET cat_id = $cat_id WHERE cat_id = $old");
		}

		$cats->set_appname('property.project');

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_workorder_category");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$categories[$GLOBALS['phpgw_setup']->oProc->f('id')] = array(
				'name'	=> $GLOBALS['phpgw_setup']->oProc->f('descr', true),
				'descr'	=> $GLOBALS['phpgw_setup']->oProc->f('descr', true),
				'parent' => 'none',
				'old_parent' => 0,
				'access' => 'public'
			);
		}

		foreach($categories as $old => $values)
		{
			$cat_id = $cats->add($values);
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_project SET category = $cat_id WHERE category = $old");
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET category = $cat_id WHERE category = $old");
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_tts_category');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_workorder_category');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_request_category');

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_tts_tickets', 'status', array(
			'type' => 'varchar', 'precision' => '2', 'nullable' => False));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_responsibility', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
				'active' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'cat_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'created_on' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				),
				'pk' => array('id'),
				'fk' => array(
					'phpgw_categories' => array('cat_id' => 'cat_id')
				),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_responsibility_contact', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'responsibility_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'contact_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'location_code' => array('type' => 'varchar', 'precision' => 20, 'nullable' => True),
				'p_num' => array('type' => 'varchar', 'precision' => 15, 'nullable' => True),
				'p_entity_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True,
					'default' => '0'),
				'p_cat_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True, 'default' => '0'),
				'priority' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'active_from' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'active_to' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'created_on' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'expired_on' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'expired_by' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'remark' => array('type' => 'text', 'nullable' => True),
				),
				'pk' => array('id'),
				'fk' => array(
					'fm_responsibility' => array('responsibility_id' => 'id'),
					'phpgw_contact' => array('contact_id' => 'contact_id')
				),
				'ix' => array('location_code'),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_tts_status', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'color' => array('type' => 'varchar', 'precision' => '10', 'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		unset($GLOBALS['phpgw']->accounts);
		unset($GLOBALS['phpgw']->acl);
		$GLOBALS['phpgw']->hooks->register_all_hooks(); //get the menus
		unset($GLOBALS['phpgw']->hooks);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.544';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.544 to 0.9.17.545
	* Move interlink data from property to API
 	*/
	$test[] = '0.9.17.544';

	function property_upgrade0_9_17_544()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->query('DELETE FROM fm_cache');
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_wo_hours', 'hours_descr', array(
			'type' => 'text', 'nullable' => True));

		$GLOBALS['phpgw']->locations->add('.project.workorder', 'Workorder', 'property', $allow_grant = true, $custom_tbl = null, $c_function = true);
		$GLOBALS['phpgw']->locations->add('.project.request', 'Request', 'property', $allow_grant = true, $custom_tbl = null, $c_function = true);
		$GLOBALS['phpgw_setup']->oProc->query('SELECT * FROM fm_origin');
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$interlink[] = array
			(
				'origin'			=> $GLOBALS['phpgw_setup']->oProc->f('origin'),
				'origin_id'			=> $GLOBALS['phpgw_setup']->oProc->f('origin_id'),
				'destination'		=> $GLOBALS['phpgw_setup']->oProc->f('destination'),
				'destination_id'	=> $GLOBALS['phpgw_setup']->oProc->f('destination_id'),
				'user_id'			=> $GLOBALS['phpgw_setup']->oProc->f('user_id'),
				'entry_date'		=> $GLOBALS['phpgw_setup']->oProc->f('entry_date')
			);
		}

		foreach($interlink as $entry)
		{
			if($entry['origin'] == 'workorder')
			{
				$entry['origin'] = 'project.workorder';
			}
			if($entry['origin'] == 'request')
			{
				$entry['origin'] = 'project.request';
			}
			if($entry['destination'] == 'request')
			{
				$entry['destination'] = 'project.request';
			}
			if($entry['destination'] == 'tenant_claim')
			{
				$entry['destination'] = 'tenant&claim';
			}

			$location1_id = $GLOBALS['phpgw']->locations->get_id('property', '.' . str_replace('_', '.', $entry['origin'] == 'tts' ? 'ticket' : $entry['origin']));
			$location2_id = $GLOBALS['phpgw']->locations->get_id('property', '.' . str_replace(array(
				'_', '&'), array('.', '_'), $entry['destination'] == 'tts' ? 'ticket' : $entry['destination']));
			$account_id = $entry['user_id'] ? $entry['user_id'] : -1;
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_interlink (location1_id,location1_item_id,location2_id,location2_item_id,account_id,entry_date,is_private,start_date,end_date) '
			. 'VALUES('
			. $location1_id . ','
			. $entry['origin_id'] . ','
			. $location2_id . ','
			. $entry['destination_id'] . ','
			. $account_id . ','
			. $entry['entry_date'] . ',-1,-1,-1)');
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_origin');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.545';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.545 to 0.9.17.546
	* Add table for a common unified location-mapping for use with interlink
 	*/
	$test[] = '0.9.17.545';

	function property_upgrade0_9_17_545()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		//old table that may exist
		if($GLOBALS['phpgw_setup']->oProc->m_odb->metadata('fm_location'))
		{
			$GLOBALS['phpgw_setup']->oProc->DropTable('fm_location');
		}

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_locations', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'level' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'location_code' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('location_code')
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query('SELECT max(id) as levels FROM fm_location_type');
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$levels =  $GLOBALS['phpgw_setup']->oProc->f('levels');

		//perform an update on all location_codes on all levels to make sure they are consistent and unique
		$locations = array();
		for($level = 1; $level < ($levels + 1); $level++)
		{
			$sql = "SELECT * from fm_location{$level}";
			$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
			$i = 0;
			while($GLOBALS['phpgw_setup']->oProc->next_record())
			{
				$location_code = array();
				$where = 'WHERE';
				$locations[$level][$i]['condition'] = '';
				for($j = 1; $j < ($level + 1); $j++)
				{
					$loc = $GLOBALS['phpgw_setup']->oProc->f("loc{$j}");
					$location_code[] = $loc;
					$locations[$level][$i]['condition'] .= "$where loc{$j}='{$loc}'";
					$where = 'AND';
				}
				$locations[$level][$i]['new_values']['location_code'] = implode('-', $location_code);
				$i++;
			}
		}

		foreach($locations as $level => $location_at_leve)
		{
			foreach($location_at_leve as $location)
			{
				$sql = "UPDATE fm_location{$level} SET location_code = '{$location['new_values']['location_code']}' {$location['condition']}";
				$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
			}
		}

		$locations = array();
		for($i = 1; $i < ($levels + 1); $i++)
		{
			$GLOBALS['phpgw_setup']->oProc->query("SELECT * from fm_location{$i}");
			while($GLOBALS['phpgw_setup']->oProc->next_record())
			{
				$locations[] = array
				(
					'level' 		=> $i,
					'location_code' => $GLOBALS['phpgw_setup']->oProc->f('location_code')
				);
			}
		}

		foreach($locations as $location)
		{
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_locations (level, location_code) VALUES ({$location['level']}, '{$location['location_code']}')");
		}

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_acl set acl_grantor = -1 WHERE acl_grantor IS NULL", __LINE__, __FILE__);
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_cache_user", __LINE__, __FILE__);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.546';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.546 to 0.9.17.547
	* Udate missing information on table for custom fields for owner, tenant and vendor
 	*/
	$test[] = '0.9.17.546';

	function property_upgrade0_9_17_546()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$location_id	= $GLOBALS['phpgw']->locations->get_id('property', '.owner');
		$sql = "UPDATE phpgw_locations SET allow_c_attrib = 1, c_attrib_table = 'fm_owner' WHERE location_id = {$location_id}";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		$location_id	= $GLOBALS['phpgw']->locations->get_id('property', '.tenant');
		$sql = "UPDATE phpgw_locations SET allow_c_attrib = 1, c_attrib_table = 'fm_tenant' WHERE location_id = {$location_id}";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		$location_id	= $GLOBALS['phpgw']->locations->get_id('property', '.vendor');
		$sql = "UPDATE phpgw_locations SET allow_c_attrib = 1, c_attrib_table = 'fm_vendor' WHERE location_id = {$location_id}";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.547';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.547 to 0.9.17.548
	* Drop some old tables and add custom attribute groups if this was missed during api-upgrade
 	*/
	$test[] = '0.9.17.547';

	function property_upgrade0_9_17_547()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$metadata = $GLOBALS['phpgw_setup']->db->metadata('fm_equipment');
		if($metadata)
		{
			$GLOBALS['phpgw_setup']->oProc->DropTable('fm_equipment');
			$GLOBALS['phpgw_setup']->oProc->DropTable('fm_equipment_attrib');
			$GLOBALS['phpgw_setup']->oProc->DropTable('fm_equipment_status');
			$GLOBALS['phpgw_setup']->oProc->DropTable('fm_equipment_type');
			$GLOBALS['phpgw_setup']->oProc->DropTable('fm_equipment_type_attrib');
			$GLOBALS['phpgw_setup']->oProc->DropTable('fm_equipment_type_choice');
		}

		$metadata = $GLOBALS['phpgw_setup']->db->metadata('fm_meter');
		if($metadata)
		{
			$GLOBALS['phpgw_setup']->oProc->DropTable('fm_meter');
			$GLOBALS['phpgw_setup']->oProc->DropTable('fm_meter_category');
		}


		$GLOBALS['phpgw_setup']->oProc->m_odb->query("SELECT count(*) as found_some FROM phpgw_cust_attribute_group");
		$GLOBALS['phpgw_setup']->oProc->m_odb->next_record();
		if(!$GLOBALS['phpgw_setup']->oProc->f('found_some'))
		{
			$GLOBALS['phpgw_setup']->oProc->m_odb->query("SELECT DISTINCT location_id FROM phpgw_cust_attribute");
			$locations = array();
			while($GLOBALS['phpgw_setup']->oProc->m_odb->next_record())
			{
				$locations[] = $GLOBALS['phpgw_setup']->oProc->f('location_id');
			}

			foreach($locations as $location_id)
			{
				$GLOBALS['phpgw_setup']->oProc->m_odb->query("INSERT INTO phpgw_cust_attribute_group (location_id, id, name, group_sort, descr)"
				. " VALUES ({$location_id}, 1, 'Default group', 1, 'Auto created from db-update')", __LINE__, __FILE__);
			}
		}


		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.548';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.548 to 0.9.17.549
	* Add new table for project_group
 	*/
	$test[] = '0.9.17.548';

	function property_upgrade0_9_17_548()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_project_group', array(
				'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project', 'project_group', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.549';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.549 to 0.9.17.550
	* FIXME: Figure out the correct conversion of categories that comply with interlink
 	*/
	$test[] = '0.9.17.549';

	function property_upgrade0_9_17_549()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		// Need account_repository, accounts, acl and hooks to use categories
		$GLOBALS['phpgw_setup']->oProc->query("SELECT config_value FROM phpgw_config WHERE config_app = 'phpgwapi' AND config_name = 'account_repository'");
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$GLOBALS['phpgw_info']['server']['account_repository'] = $GLOBALS['phpgw_setup']->oProc->f('config_value');

		$GLOBALS['phpgw']->accounts		= createObject('phpgwapi.accounts');

		$GLOBALS['phpgw']->db = & $GLOBALS['phpgw_setup']->oProc->m_odb;
		$GLOBALS['phpgw']->acl = CreateObject('phpgwapi.acl');
		$GLOBALS['phpgw']->hooks = CreateObject('phpgwapi.hooks', $GLOBALS['phpgw_setup']->oProc->m_odb);
		$cats = CreateObject('phpgwapi.categories', -1, 'property.document');

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_document_category");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$categories[$GLOBALS['phpgw_setup']->oProc->f('id')] = array(
				'name'	=> $GLOBALS['phpgw_setup']->oProc->f('descr', true),
				'descr'	=> $GLOBALS['phpgw_setup']->oProc->f('descr', true),
				'parent' => 'none',
				'old_parent' => 0,
				'access' => 'public'
			);
		}

		foreach($categories as $old => $values)
		{
			$cat_id = $cats->add($values);
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_document SET category = $cat_id WHERE category = $old");
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_document_category');

		unset($GLOBALS['phpgw']->accounts);
		unset($GLOBALS['phpgw']->acl);
		unset($GLOBALS['phpgw']->hooks);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.550';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.550 to 0.9.17.551
	*/
	$test[] = '0.9.17.550';

	function property_upgrade0_9_17_550()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_request_history', 'history_old_value', array(
			'type' => 'text', 'nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder_history', 'history_old_value', array(
			'type' => 'text', 'nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project_history', 'history_old_value', array(
			'type' => 'text', 'nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_history', 'history_old_value', array(
			'type' => 'text', 'nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_document_history', 'history_old_value', array(
			'type' => 'text', 'nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_history', 'history_old_value', array(
			'type' => 'text', 'nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_s_agreement_history', 'history_old_value', array(
			'type' => 'text', 'nullable' => true));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.551';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.551 to 0.9.17.552
	* Reorganise documents
	*/
	$test[] = '0.9.17.551';

	function property_upgrade0_9_17_551()
	{
		set_time_limit(1800);
		$next_version = '0.9.17.552';

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_document");
		$files = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$files[] = array
			(
				'document_name'	=> $GLOBALS['phpgw_setup']->oProc->f('document_name'),
				'location_code'	=> $GLOBALS['phpgw_setup']->oProc->f('location_code'),
				'loc1'			=> $GLOBALS['phpgw_setup']->oProc->f('loc1'),
				'category'		=> $GLOBALS['phpgw_setup']->oProc->f('category'),
				'p_num'			=> $GLOBALS['phpgw_setup']->oProc->f('p_num'),
				'p_entity_id'	=> $GLOBALS['phpgw_setup']->oProc->f('p_entity_id'),
				'p_cat_id'		=> $GLOBALS['phpgw_setup']->oProc->f('p_cat_id'),
			);
		}

		$sql = 'SELECT config_name,config_value FROM phpgw_config'
					. " WHERE config_name = 'files_dir'"
					. " OR config_name = 'file_repository'";

		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$GLOBALS['phpgw_info']['server'][$GLOBALS['phpgw_setup']->oProc->f('config_name', true)] = $GLOBALS['phpgw_setup']->oProc->f('config_value', true);
		}
		$GLOBALS['phpgw']->db = & $GLOBALS['phpgw_setup']->oProc->m_odb;
		$acl = CreateObject('phpgwapi.acl');

		$admins = $acl->get_ids_for_location('run', 1, 'admin');
		$GLOBALS['phpgw_info']['user']['account_id'] = $admins[0];

		//used in vfs
		define('PHPGW_ACL_READ', 1);
		define('PHPGW_ACL_ADD', 2);
		define('PHPGW_ACL_EDIT', 4);
		define('PHPGW_ACL_DELETE', 8);

		$GLOBALS['phpgw']->session		= createObject('phpgwapi.sessions');
		$vfs 			= CreateObject('phpgwapi.vfs');
		$vfs->fakebase 	= '/property';
		$vfs->override_acl = 1;


		if(!is_dir("{$vfs->basedir}{$vfs->fakebase}"))
		{
			$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_abort();
			$GLOBALS['setup_info']['property']['currentver'] = $next_version;
			return $GLOBALS['setup_info']['property']['currentver'];
		}


		$to_dir = array();
		foreach($files as $entry)
		{
			 if($entry['p_num'])
			 {
				continue;
			 }
			 else
			 {
			 	$to_dir["{$vfs->basedir}{$vfs->fakebase}/document/{$entry['location_code']}"] = true;
			 	$to_dir["{$vfs->basedir}{$vfs->fakebase}/document/{$entry['location_code']}/{$entry['category']}"] = true;
			 }
		}

		foreach($to_dir as $dir => $dummy)
		{
			if(!is_dir($dir))
			{
				mkdir($dir, 0770);
			}
		}

		reset($files);
		$error = array();
		foreach($files as $entry)
		{
			 if($entry['p_num'])
			 {
				continue;
			 }
			 else
			 {
			 	$from_file = "{$vfs->fakebase}/document/{$entry['loc1']}/{$entry['document_name']}";
			 	$to_file = "{$vfs->fakebase}/document/{$entry['location_code']}/{$entry['category']}/{$entry['document_name']}";
			 }

			if(!$vfs->mv(array(
				'from'		=> $from_file,
				'to'		=> $to_file,
				'relatives' => array(RELATIVE_ALL, RELATIVE_ALL))))
			{
				$error[] = lang('Failed to move file') . " {$from_file}";
			}
		}

		$vfs->override_acl = 0;
		if($error)
		{
			_debug_array($error);
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = $next_version;
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.552 to 0.9.17.553
	*
	*/
	$test[] = '0.9.17.552';

	function property_upgrade0_9_17_552()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw']->locations->add('.invoice.dimb', 'A dimension for accounting', 'property');
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder', 'ecodimb', array('type' => 'int',
			'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_budget', 'ecodimb', array('type' => 'int',
			'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_budget_basis', 'ecodimb', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_budget', 'category', array('type' => 'int',
			'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_budget_basis', 'category', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_entity_category', 'name', array(
			'type' => 'varchar', 'precision' => '100', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_entity_category', 'descr', array(
			'type' => 'text', 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_budget', 'district_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder', 'p_num', array('type' => 'varchar',
			'precision' => 15, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder', 'p_entity_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder', 'p_cat_id', array('type' => 'int',
			'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder', 'location_code', array(
			'type' => 'varchar', 'precision' => 20, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder', 'address', array('type' => 'varchar',
			'precision' => 150, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder', 'tenant_id', array('type' => 'int',
			'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder', 'contact_phone', array(
			'type' => 'varchar', 'precision' => 20, 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project', 'planned_cost', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True, 'default' => '0'));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_s_agreement_budget', array(
				'fd' => array(
				'agreement_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'year' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'budget_account' => array('type' => 'varchar', 'precision' => 15, 'nullable' => False),
				'ecodimb' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'budget' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'nullable' => True,
					'default' => '0.00'),
				'actual_cost' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
				),
			'pk' => array('agreement_id', 'year'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.553';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.553 to 0.9.17.554
	*
	*/
	$test[] = '0.9.17.553';

	function property_upgrade0_9_17_553()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$metadata = $GLOBALS['phpgw_setup']->oProc->m_odb->metadata('fm_workorder');

		if(!isset($metadata['paid_percent']))
		{
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder', 'paid_percent', array(
				'type' => 'int', 'precision' => 4, 'nullable' => True, 'default' => 0));
		}

		if(!isset($metadata['category']))
		{
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder', 'category', array('type' => 'int',
				'precision' => 4, 'nullable' => True));
		}

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project', 'account_id', array('type' => 'varchar',
			'precision' => '20', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project', 'ecodimb', array('type' => 'int',
			'precision' => 4, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.554';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.554 to 0.9.17.555
	*
	*/
	$test[] = '0.9.17.554';

	function property_upgrade0_9_17_554()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->query('DELETE FROM fm_cache');
		$GLOBALS['phpgw_setup']->oProc->query('DELETE FROM phpgw_cache_user');
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_responsibility_contact', 'ecodimb', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.555';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.555 to 0.9.17.556
	* Scheduling capabilities by custom fields and asyncservice
	*
	*/
	$test[] = '0.9.17.555';

	function property_upgrade0_9_17_555()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_event_action', array(
				'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 100, 'nullable' => False),
				'action' => array('type' => 'varchar', 'precision' => 100, 'nullable' => False),
				'data' => array('type' => 'text', 'nullable' => True),
				'descr' => array('type' => 'text', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_event', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'location_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'location_item_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'attrib_id' => array('type' => 'int', 'precision' => 4, 'default' => '0', 'nullable' => true),
				'responsible_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'action_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'descr' => array('type' => 'text', 'nullable' => True),
				'start_date' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'end_date' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'repeat_type' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'repeat_day' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'repeat_interval' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'enabled' => array('type' => 'int', 'precision' => 2, 'nullable' => true),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('location_id', 'location_item_id', 'attrib_id')
			)
		);

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_responsibility');

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_responsibility', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
				'active' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'cat_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'location_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'created_on' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.556';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.556 to 0.9.17.557
	* Scheduling capabilities by custom fields and asyncservice
	*
	*/
	$test[] = '0.9.17.556';

	function property_upgrade0_9_17_556()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_event_exception', array(
				'fd' => array(
				'event_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'exception_time' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'descr' => array('type' => 'text', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
				),
				'pk' => array('event_id', 'exception_time'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.557';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.557 to 0.9.17.558
	* Rename reserved fieldname (mysql)
	*
	*/
	$test[] = '0.9.17.557';

	function property_upgrade0_9_17_557()
	{
		$metadata = $GLOBALS['phpgw_setup']->oProc->m_odb->metadata('fm_event');

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		if(isset($metadata['interval']))
		{
			$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_event', 'interval', 'repeat_interval');
		}
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.558';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.558 to 0.9.17.559
	* change the priority for the helpdest (from 10-1 to 1-3)
	*
	*/
	$test[] = '0.9.17.558';

	function property_upgrade0_9_17_558()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_tts_tickets SET priority = 11 WHERE priority IN (8,9,10)");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_tts_tickets SET priority = 12 WHERE priority IN (4,5,6,7)");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_tts_tickets SET priority = 13 WHERE priority IN (1,2,3)");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_tts_tickets SET priority = 1 WHERE priority = 11");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_tts_tickets SET priority = 2 WHERE priority = 12");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_tts_tickets SET priority = 3 WHERE priority = 13");

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.559';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.559 to 0.9.17.560
	* Add location to the budget.basis
	*
	*/
	$test[] = '0.9.17.559';

	function property_upgrade0_9_17_559()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw']->locations->add('.budget.basis', 'Basis for high level lazy budgeting', 'property');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.560';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.560 to 0.9.17.561
	* Add ability to upload jasper reports
	*
	*/
	$test[] = '0.9.17.560';

	function property_upgrade0_9_17_560()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category', 'jasperupload', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.561';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.561 to 0.9.17.562
	* Add variants of closed-status for tickets
	*
	*/
	$test[] = '0.9.17.561';

	function property_upgrade0_9_17_561()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_status', 'closed', array('type' => 'int',
			'precision' => 2, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.562';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.562 to 0.9.17.563
	* Separate project status from workorder status
	*
	*/
	$test[] = '0.9.17.562';

	function property_upgrade0_9_17_562()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_project_status', array(
				'fd' => array(
				'id' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_workorder_status");
		$status = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$status[] = array
			(
				'id'	=> $GLOBALS['phpgw_setup']->oProc->f('id'),
				'descr'	=> $GLOBALS['phpgw_setup']->oProc->f('descr')
			);
		}

		foreach($status as $entry)
		{
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO fm_project_status (' . implode(',', array_keys($entry)) . ') VALUES (' . $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($entry)) . ')');
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.563';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.563 to 0.9.17.564
	* Add area information as standard fields to each level in the location hierarchy
	*
	*/
	$test[] = '0.9.17.563';

	function property_upgrade0_9_17_563()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$db = & $GLOBALS['phpgw_setup']->oProc->m_odb;

		$db->query('DELETE FROM fm_cache');

		$cust = array
		(
			'datatype'		=> 'N',
			'precision_'	=> 20,
			'scale'			=> 2,
			'default_value'	=> '0.00',
			'nullable'		=> 'True',
			'custom'		=> 1
		);

		$area_fields = array();

		$area_fields[] = array
		(
			'name' => 'area_gross',
			'descr' => 'gross area',
			'statustext' => 'Sum of the areas included within the outside face of the exterior walls of a building.',
			'cust'	=> $cust
		);
		$area_fields[] = array
		(
			'name' => 'area_net',
			'descr' => 'net area',
			'statustext' => 'The wall-to-wall floor area of a room.',
			'cust'	=> $cust
		);
		$area_fields[] = array
		(
			'name' => 'area_usable',
			'descr' => 'usable area',
			'statustext' => 'generally measured from "paint to paint" inside the permanent walls and to the middle of partitions separating rooms',
			'cust'	=> $cust
		);

		$db->query("SELECT count(*) as levels FROM fm_location_type");

		$db->next_record();
		$levels = $db->f('levels');

		for($i = 1; $i < $levels + 1; $i++)
		{
			$metadata = $GLOBALS['phpgw_setup']->db->metadata("fm_location{$i}");
			foreach($area_fields as & $field)
			{
				if(!isset($metadata[$field['name']]))
				{
					$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_location{$i}", $field['name'], array(
						'type' => 'decimal', 'precision' => '20', 'scale' => '2', 'nullable' => True,
						'default' => '0.00'));
					$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_location{$i}_history", $field['name'], array(
						'type' => 'decimal', 'precision' => '20', 'scale' => '2', 'nullable' => True,
						'default' => '0.00'));
				}

				$field['cust']['location_id'] = $GLOBALS['phpgw']->locations->get_id('property', ".location.{$i}");
				$db->query("SELECT max(id) as id FROM phpgw_cust_attribute WHERE location_id = {$field['cust']['location_id']}");
				$db->next_record();
				$id = (int)$db->f('id');
				$db->query("SELECT max(attrib_sort) as attrib_sort FROM phpgw_cust_attribute WHERE id = {$id} AND location_id = {$field['cust']['location_id']}");
				$db->next_record();

				$field['cust']['id']			= $id + 1;
				$field['cust']['attrib_sort'] = $db->f('attrib_sort') + 1;
				$field['cust']['column_name']	= $field['name'];
				$field['cust']['input_text']	= $field['descr'];
				$field['cust']['statustext']	= $field['statustext'];

				$sql = 'INSERT INTO phpgw_cust_attribute(' . implode(',', array_keys($field['cust'])) . ') '
					 . ' VALUES (' . $db->validate_insert($field['cust']) . ')';
				$db->query($sql, __LINE__, __FILE__);
			}
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.564';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.564 to 0.9.17.565
	* alter datatype for spvend_code
	*
	*/
	$test[] = '0.9.17.564';

	function property_upgrade0_9_17_564()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$db = & $GLOBALS['phpgw_setup']->oProc->m_odb;

		$metadata = $GLOBALS['phpgw_setup']->db->metadata('fm_ecobilag');

		if($metadata['spvend_code']->type == 'varchar')
		{
			echo 'oppdaterer..</br>';
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilag', 'spvend_code_tmp', array(
				'type' => 'int', 'precision' => 4, 'nullable' => True));
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf', 'spvend_code_tmp', array(
				'type' => 'int', 'precision' => 4, 'nullable' => True));
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecoavvik', 'spvend_code_tmp', array(
				'type' => 'int', 'precision' => 4, 'nullable' => True));

			$db->query('UPDATE fm_ecobilag SET spvend_code_tmp = CAST ( spvend_code AS integer )', __LINE__, __FILE__);
			$db->query('UPDATE fm_ecobilagoverf SET spvend_code_tmp = CAST ( spvend_code AS integer )', __LINE__, __FILE__);
			$db->query('UPDATE fm_ecoavvik SET spvend_code_tmp = CAST ( spvend_code AS integer )', __LINE__, __FILE__);

			$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_ecobilag', array(), 'spvend_code');
			$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_ecobilagoverf', array(), 'spvend_code');
			$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_ecoavvik', array(), 'spvend_code');

			$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_ecobilag', 'spvend_code_tmp', 'spvend_code');
			$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_ecobilagoverf', 'spvend_code_tmp', 'spvend_code');
			$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_ecoavvik', 'spvend_code_tmp', 'spvend_code');
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.565';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.565 to 0.9.17.566
	* Add field to reference origin of invoices if imported from external system
	*
	*/
	$test[] = '0.9.17.565';

	function property_upgrade0_9_17_565()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilag', 'external_ref', array(
			'type' => 'varchar', 'precision' => '30', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf', 'external_ref', array(
			'type' => 'varchar', 'precision' => '30', 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.566';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.566 to 0.9.17.567
	* Add a general approval scheme for items across the system
	*
	*/
	$test[] = '0.9.17.566';

	function property_upgrade0_9_17_566()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_approval', array(
				'fd' => array(
				'id' => array('type' => 'int', 'precision' => 8, 'nullable' => False),
				'location_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'account_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'requested' => array('type' => 'int', 'precision' => 4, 'nullable' => True), //timestamp
				'approved' => array('type' => 'int', 'precision' => 4, 'nullable' => True), //timestamp
				'reminder' => array('type' => 'int', 'precision' => 4, 'nullable' => True, 'default' => '1'),
				'created_on' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				),
				'pk' => array('id', 'location_id', 'account_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.567';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.567 to 0.9.17.568
	* Extend the approval scheme to include general actions
	*
	*/
	$test[] = '0.9.17.567';

	function property_upgrade0_9_17_567()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_approval');

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_action_pending', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'item_id' => array('type' => 'int', 'precision' => 8, 'nullable' => False),
				'location_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'responsible' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'responsible_type' => array('type' => 'varchar', 'precision' => 20, 'nullable' => False),
				'action_category' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'action_requested' => array('type' => 'int', 'precision' => 4, 'nullable' => True), //timestamp
				'action_deadline' => array('type' => 'int', 'precision' => 4, 'nullable' => True), //timestamp
				'action_performed' => array('type' => 'int', 'precision' => 4, 'nullable' => True), //timestamp
				'reminder' => array('type' => 'int', 'precision' => 4, 'nullable' => True, 'default' => '1'),
				'created_on' => array('type' => 'int', 'precision' => 4, 'nullable' => False), //timestamp
				'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'expired_on' => array('type' => 'int', 'precision' => 4, 'nullable' => True), //timestamp
				'expired_by' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'remark' => array('type' => 'text', 'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_action_pending_category', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'num' => array('type' => 'varchar', 'precision' => 25, 'nullable' => True),
				'name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => True),
				'descr' => array('type' => 'text', 'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('num')
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_action_pending_category (num, name, descr) VALUES ('approval', 'Approval', 'Please approve the item requested')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_action_pending_category (num, name, descr) VALUES ('remind', 'Remind', 'This is a reminder of task assigned')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_action_pending_category (num, name, descr) VALUES ('accept_delivery', 'Accept delivery', 'Please accept delivery on this item')");

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.568';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.568 to 0.9.17.569
	* Add variants of closed and approved-status for projects and workorders
	*
	*/
	$test[] = '0.9.17.568';

	function property_upgrade0_9_17_568()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project_status', 'approved', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project_status', 'closed', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder_status', 'approved', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder_status', 'in_progress', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder_status', 'delivered', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder_status', 'closed', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.569';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.569 to 0.9.17.570
	* Add custom fields to projects, workorders and tickets
	*
	*/
	$test[] = '0.9.17.569';

	function property_upgrade0_9_17_569()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$location_id_project = $GLOBALS['phpgw']->locations->get_id('property', '.project');
		$location_id_workorder = $GLOBALS['phpgw']->locations->get_id('property', '.project.workorder');
		$location_id_ticket = $GLOBALS['phpgw']->locations->get_id('property', '.ticket');

		$sql = "UPDATE phpgw_locations SET allow_c_function = 1, allow_c_attrib = 1, c_attrib_table = 'fm_project' WHERE location_id = {$location_id_project}";
		$GLOBALS['phpgw_setup']->oProc->query($sql);
		$sql = "UPDATE phpgw_locations SET allow_c_function = 1, allow_c_attrib = 1, c_attrib_table = 'fm_workorder' WHERE location_id = {$location_id_workorder}";
		$GLOBALS['phpgw_setup']->oProc->query($sql);
		$sql = "UPDATE phpgw_locations SET allow_c_function = 1, allow_c_attrib = 1, c_attrib_table = 'fm_tts_tickets' WHERE location_id = {$location_id_ticket}";
		$GLOBALS['phpgw_setup']->oProc->query($sql);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.570';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.570 to 0.9.17.571
	* Add custom fields to projects, workorders and tickets
	*
	*/
	$test[] = '0.9.17.570';

	function property_upgrade0_9_17_570()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project', 'contact_id', array('type' => 'int',
			'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'contact_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.571';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.571 to 0.9.17.572
	* Add event workorders
	*
	*/
	$test[] = '0.9.17.571';

	function property_upgrade0_9_17_571()
	{
		$metadata = $GLOBALS['phpgw_setup']->oProc->m_odb->metadata('fm_workorder');
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		if(!isset($metadata['event_id']))
		{
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder', 'event_id', array('type' => 'int',
				'precision' => 4, 'nullable' => True));
		}
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_event', 'attrib_id_', array('type' => 'varchar',
			'precision' => 50, 'default' => '0', 'nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->query('UPDATE fm_event SET attrib_id_ = attrib_id');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_event', array(), 'attrib_id');
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_event', 'attrib_id_', 'attrib_id');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.572';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.572 to 0.9.17.573
	* Add ticket order - an ad hock order without using the project module
	*
	*/
	$test[] = '0.9.17.572';

	function property_upgrade0_9_17_572()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw']->locations->add('.ticket.order', 'Helpdesk ad hock order', 'property');

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'order_id', array(
			'type' => 'int', 'precision' => 8, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'vendor_id', array(
			'type' => 'int', 'precision' => '4', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'order_descr', array(
			'type' => 'text', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'b_account_id', array(
			'type' => 'varchar', 'precision' => '20', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'ecodimb', array('type' => 'int',
			'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'budget', array('type' => 'int',
			'precision' => '4', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'actual_cost', array(
			'type' => 'decimal', 'precision' => '20', 'scale' => '2', 'nullable' => True,
			'default' => '0.00'));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.573';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.573 to 0.9.17.574
	* Alter field definition
	*
	*/
	$test[] = '0.9.17.573';

	function property_upgrade0_9_17_573()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_tts_history', 'history_status', array(
			'type' => 'varchar', 'precision' => '3', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'title', array('type' => 'varchar',
			'precision' => '100', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_document', 'title', array('type' => 'varchar',
			'precision' => '100', 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.574';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.574 to 0.9.17.575
	* Add variants of closed and approved-status for tickets
	*
	*/
	$test[] = '0.9.17.574';

	function property_upgrade0_9_17_574()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_tts_tickets', 'status', array(
			'type' => 'varchar', 'precision' => '3', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_status', 'approved', array('type' => 'int',
			'precision' => 2, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_status', 'in_progress', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_status', 'delivered', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.575';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.575 to 0.9.17.576
	* Add contact_email to tickets
	*
	*/
	$test[] = '0.9.17.575';

	function property_upgrade0_9_17_575()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tenant', 'contact_email', array(
			'type' => 'varchar', 'precision' => '64', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'contact_email', array(
			'type' => 'varchar', 'precision' => '64', 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.576';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.576 to 0.9.17.577
	* Add sorting to ticket status
	*
	*/
	$test[] = '0.9.17.576';

	function property_upgrade0_9_17_576()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_status', 'sorting', array('type' => 'int',
			'precision' => 4, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.577';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.577 to 0.9.17.578
	* Add order categories to ticket ad hoc orders
	*
	*/
	$test[] = '0.9.17.577';

	function property_upgrade0_9_17_577()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'order_cat_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.578';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.578 to 0.9.17.579
	* Add custom dimension for orders
	*
	*/
	$test[] = '0.9.17.578';

	function property_upgrade0_9_17_578()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'building_part', array(
			'type' => 'varchar', 'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'order_dim1', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_order_dim1', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'num' => array('type' => 'varchar', 'precision' => 20, 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False)
				),
				'pk' => array('id'),
				'ix' => array(),
				'fk' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.579';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.579 to 0.9.17.580
	* Add optional publishing flag on ticket notes
	*
	*/
	$test[] = '0.9.17.579';

	function property_upgrade0_9_17_579()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'publish_note', array(
			'type' => 'varchar', 'precision' => 2, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_history', 'publish', array('type' => 'int',
			'precision' => 2, 'nullable' => True));


		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.580';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.580 to 0.9.17.581
	* Add optional hierarchy on entities
	*
	*/
	$test[] = '0.9.17.580';

	function property_upgrade0_9_17_580()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category', 'parent_id', array(
			'type' => 'int', 'precision' => '4', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category', 'level', array(
			'type' => 'int', 'precision' => '4', 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.581';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.581 to 0.9.17.582
	* Add templates to Ad Hoc Orders
	*
	*/
	$test[] = '0.9.17.581';

	function property_upgrade0_9_17_581()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_order_template', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 200, 'nullable' => False),
				'content' => array('type' => 'text', 'nullable' => True),
				'public' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.582';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.582 to 0.9.17.583
	* Grant rights on actors
	*
	*/
	$test[] = '0.9.17.582';

	function property_upgrade0_9_17_582()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$location_id	= $GLOBALS['phpgw']->locations->get_id('property', '.owner');
		$sql = "UPDATE phpgw_locations SET allow_grant = 1 WHERE location_id = {$location_id}";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		$location_id	= $GLOBALS['phpgw']->locations->get_id('property', '.tenant');
		$sql = "UPDATE phpgw_locations SET allow_grant = 1 WHERE location_id = {$location_id}";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		$location_id	= $GLOBALS['phpgw']->locations->get_id('property', '.vendor');
		$sql = "UPDATE phpgw_locations SET allow_grant = 1 WHERE location_id = {$location_id}";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.583';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.583 to 0.9.17.584
	* Add schedule to event
	*
	*/
	$test[] = '0.9.17.583';

	function property_upgrade0_9_17_583()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_event_schedule', array(
				'fd' => array(
				'event_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'schedule_time' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'descr' => array('type' => 'text', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
				),
				'pk' => array('event_id', 'schedule_time'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_event_receipt');

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_event_receipt', array(
				'fd' => array(
				'event_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'receipt_time' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'descr' => array('type' => 'text', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
				),
				'pk' => array('event_id', 'receipt_time'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw']->locations->add('.scheduled_events', 'Scheduled events', 'property');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.584';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.583 to 0.9.17.584
	* Use locations for categories
	*
	*/
	$test[] = '0.9.17.584';

	function property_upgrade0_9_17_584()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$locations = array
		(
			'property.ticket'	=> '.ticket',
			'property.project'	=> '.project',
			'property.document' => '.document',
			'fm_vendor'			=> '.vendor',
			'fm_tenant'			=> '.tenant',
			'fm_owner'			=> '.owner'
		);


		foreach($locations as $dummy => $location)
		{
			$GLOBALS['phpgw']->locations->add("{$location}.category", 'Categories', 'property');
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM phpgw_categories");
		$categories = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			if(in_array($GLOBALS['phpgw_setup']->oProc->f('cat_appname', true), array_keys($locations)))
			{
				$categories[] = array
				(
					'id'		=> $GLOBALS['phpgw_setup']->oProc->f('cat_id'),
					'appname' => $GLOBALS['phpgw_setup']->oProc->f('cat_appname', true),
					'name' => $GLOBALS['phpgw_setup']->oProc->f('cat_name', true)
				);
			}
		}

		foreach($categories as $category)
		{
			$location = $locations[$category['appname']];
			$location_id	= $GLOBALS['phpgw']->locations->get_id('property', $location);
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_categories SET cat_appname = 'property', location_id = {$location_id} WHERE cat_id = {$category['id']}", __LINE__, __FILE__);

			$GLOBALS['phpgw']->locations->add("{$location}.category.{$category['id']}", $category['name'], 'property');
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT file_id, mime_type, name FROM  phpgw_vfs WHERE mime_type != 'Directory' AND mime_type != 'journal' AND mime_type != 'journal-deleted'", __LINE__, __FILE__);

		$mime = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$mime[] = array
			(
				'file_id'		=> $GLOBALS['phpgw_setup']->oProc->f('file_id'),
				'mime_type'		=> $GLOBALS['phpgw_setup']->oProc->f('mime_type'),
				'name'			=> $GLOBALS['phpgw_setup']->oProc->f('name'),
			);
		}

		$mime_magic = createObject('phpgwapi.mime_magic');

		foreach($mime as $entry)
		{
			if(!$entry['mime_type'])
			{
				$mime_type = $mime_magic->filename2mime($entry['name']);
				$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_vfs SET mime_type = '{$mime_type}' WHERE file_id = {$entry['file_id']}", __LINE__, __FILE__);
			}
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.585';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.585 to 0.9.17.586
	* Use budget account groups on project level
	*
	*/
	$test[] = '0.9.17.585';

	function property_upgrade0_9_17_585()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project', 'account_group', array(
			'type' => 'int', 'precision' => '4', 'nullable' => true));
		$sql = "SELECT DISTINCT fm_project.account_id, fm_b_account.category as account_group FROM fm_project JOIN fm_b_account ON fm_project.account_id = fm_b_account.id";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		$accounts = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$accounts[] = array
			(
				'account_id'		=> $GLOBALS['phpgw_setup']->oProc->f('account_id'),
				'account_group'		=> $GLOBALS['phpgw_setup']->oProc->f('account_group'),
			);
		}
		foreach($accounts as $entry)
		{
			$sql = "UPDATE fm_project SET account_group = {$entry['account_group']} WHERE account_id = '{$entry['account_id']}'";

			$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		}

//		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_project',array(),'account_id');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.586';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.586 to 0.9.17.587
	* restore field
	*
	*/
	$test[] = '0.9.17.586';

	function property_upgrade0_9_17_586()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$metadata = $GLOBALS['phpgw_setup']->oProc->m_odb->metadata('fm_project');

		if(!isset($metadata['account_id']))
		{
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project', 'account_id', array('type' => 'varchar',
				'precision' => '20', 'nullable' => True));
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.587';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.587 to 0.9.17.588
	* add billable_hours to workorders
	*
	*/
	$test[] = '0.9.17.587';

	function property_upgrade0_9_17_587()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder', 'billable_hours', array(
			'type' => 'decimal', 'precision' => '20', 'scale' => '2', 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.588';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.588 to 0.9.17.589
	* Better precision to period (month) for payment-info
	*
	*/
	$test[] = '0.9.17.588';

	function property_upgrade0_9_17_588()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_ecobilag', 'periode', array('type' => 'int',
			'precision' => '4', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_ecobilagoverf', 'periode', array(
			'type' => 'int', 'precision' => '4', 'nullable' => True));
//		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf','periode_old',array('type' => 'int','precision' => 4,'nullable' => True));
//		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilag','periode_old',array('type' => 'int','precision' => 4,'nullable' => True));

		$db = & $GLOBALS['phpgw_setup']->oProc->m_odb;

		$tables = array('fm_ecobilag', 'fm_ecobilagoverf');

		foreach($tables as $table)
		{
			//Backup
//			$sql = "UPDATE {$table} SET periode_old = periode";
//			$db->query($sql,__LINE__,__FILE__);

			$sql = 'SELECT count (*), bilagsnr, EXTRACT(YEAR from fakturadato ) as aar ,'
			. ' EXTRACT(MONTH from fakturadato ) as month, periode'
			. " FROM {$table} "
			. ' GROUP BY bilagsnr, EXTRACT(YEAR from fakturadato ), EXTRACT(MONTH from fakturadato ), periode'
			. ' ORDER BY aar, month, periode';

			$db->query($sql, __LINE__, __FILE__);

			$result = array();
			while($db->next_record())
			{
				$aar = $db->f('aar');
				$month = $db->f('month');
				$periode = $db->f('periode');
				$periode_ny = $aar . sprintf("%02d", $periode);
				$periode_old = $aar . sprintf("%02d", $month);

				if($periode_old != $periode_ny && $month == 1)
				{
					$periode_korrigert = ($aar - 1) . sprintf("%02d", $periode);
				}
				else
				{
					$periode_korrigert = $periode_ny;
				}

				$result[] = array
				(
    		   	    'bilagsnr'			=> $db->f('bilagsnr'),
    	//	   	    'aar'				=> $aar,
    	//	   		'month'				=> $month,
    	//	   	    'periode'			=> $periode,
    	//	   	    'periode_ny'		=> $periode_ny,
    		   	    'periode_korrigert'	=> $periode_korrigert
				);
			}

			foreach($result as $entry)
			{
				$sql = "UPDATE {$table} SET periode = {$entry['periode_korrigert']} WHERE bilagsnr = {$entry['bilagsnr']}";
				$db->query($sql, __LINE__, __FILE__);
			}
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.589';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.589 to 0.9.17.590
	* add generic support for JasperReport
	*
	*/
	$test[] = '0.9.17.589';

	function property_upgrade0_9_17_589()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw']->locations->add('.jasper', 'JasperReport', 'property', $allow_grant = true);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_jasper', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
				'location_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'title' => array('type' => 'varchar', 'precision' => 100, 'nullable' => true),
				'descr' => array('type' => 'varchar', 'precision' => 255, 'nullable' => true),
				'formats' => array('type' => 'varchar', 'precision' => 255, 'nullable' => true),
				'version' => array('type' => 'varchar', 'precision' => 10, 'nullable' => true),
				'access' => array('type' => 'varchar', 'precision' => 7, 'nullable' => true),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_jasper_input_type', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
				'name' => array('type' => 'varchar', 'precision' => 20, 'nullable' => false), // i.e: date/ integer
				'descr' => array('type' => 'varchar', 'precision' => 255, 'nullable' => true),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_input_type (name, descr) VALUES ('integer', 'Integer')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_input_type (name, descr) VALUES ('float', 'Float')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_input_type (name, descr) VALUES ('text', 'Text')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_input_type (name, descr) VALUES ('date', 'Date')");

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_jasper_format_type', array(
				'fd' => array(
				'id' => array('type' => 'varchar', 'precision' => 20, 'nullable' => false), // i.e: pdf/xls
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_format_type (id) VALUES ('PDF')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_format_type (id) VALUES ('CSV')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_format_type (id) VALUES ('XLS')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_format_type (id) VALUES ('XHTML')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_format_type (id) VALUES ('DOCX')");

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_jasper_input', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
				'jasper_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'input_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'is_id' => array('type' => 'int', 'precision' => 2, 'nullable' => true),
				'name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
				'descr' => array('type' => 'varchar', 'precision' => 255, 'nullable' => true),
				),
				'pk' => array('id'),
				'fk' => array(
					'fm_jasper_input_type' => array('input_type_id' => 'id'),
					'fm_jasper' => array('jasper_id' => 'id')),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.590';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.590 to 0.9.17.591
	* Add datatypes for user input at JasperReport
	*
	*/
	$test[] = '0.9.17.590';

	function property_upgrade0_9_17_590()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_input_type (name, descr) VALUES ('timestamp', 'timestamp')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_input_type (name, descr) VALUES ('AB', 'Address book')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_input_type (name, descr) VALUES ('VENDOR', 'Vendor')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_input_type (name, descr) VALUES ('user', 'system user')");

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.591';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.591 to 0.9.17.592
	* Add integration settings on entities
	*
	*/
	$test[] = '0.9.17.591';

	function property_upgrade0_9_17_591()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category', 'integration_tab', array(
			'type' => 'varchar', 'precision' => 50, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category', 'integration_url', array(
			'type' => 'varchar', 'precision' => 255, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category', 'integration_paramtres', array(
			'type' => 'text', 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.592';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.592 to 0.9.17.593
	* More on integration settings on entities
	*
	*/
	$test[] = '0.9.17.592';

	function property_upgrade0_9_17_592()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_entity_category', 'integration_paramtres', 'integration_parametres');
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category', 'integration_action', array(
			'type' => 'varchar', 'precision' => 50, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category', 'integration_action_view', array(
			'type' => 'varchar', 'precision' => 50, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category', 'integration_action_edit', array(
			'type' => 'varchar', 'precision' => 50, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.593';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.593 to 0.9.17.594
	* Convert integration settings to generic config on locations
	*
	*/
	$test[] = '0.9.17.593';

	function property_upgrade0_9_17_593()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_entity_category', array(), 'integration_tab');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_entity_category', array(), 'integration_url');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_entity_category', array(), 'integration_parametres');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_entity_category', array(), 'integration_action');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_entity_category', array(), 'integration_action_view');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_entity_category', array(), 'integration_action_edit');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.594';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.594 to 0.9.17.595
	* Add custom dimension for orders
	*
	*/
	$test[] = '0.9.17.594';

	function property_upgrade0_9_17_594()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_request', 'building_part', array(
			'type' => 'varchar', 'precision' => 4, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.595';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.595 to 0.9.17.596
	* Alter datatype
	*
	*/
	$test[] = '0.9.17.595';

	function property_upgrade0_9_17_595()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("SELECT id, b_account_id FROM fm_tenant_claim");
		$claims = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$claims[] = array
			(
				'id'			=> (int)$GLOBALS['phpgw_setup']->oProc->f('id'),
				'b_account_id'	=> $GLOBALS['phpgw_setup']->oProc->f('b_account_id')
			);
		}

		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_tenant_claim', array(), 'b_account_id');
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tenant_claim', 'b_account_id', array(
			'type' => 'varchar', 'precision' => 20, 'nullable' => True));

		foreach($claims as $claim)
		{
			$sql = "UPDATE fm_tenant_claim SET b_account_id = {$claim['b_account_id']} WHERE id = {$claim['id']}";

			$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		}


		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.596';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.596 to 0.9.17.597
	* Add responsibility roles
	*
	*/
	$test[] = '0.9.17.596';

	function property_upgrade0_9_17_596()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_responsibility_role', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 200, 'nullable' => False),
				'remark' => array('type' => 'text', 'nullable' => True),
				'location' => array('type' => 'varchar', 'precision' => 200, 'nullable' => False),
				'responsibility' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				),
				'pk' => array('id'),
				'fk' => array('fm_responsibility' => array('responsibility' => 'id')),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.597';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.597 to 0.9.17.598
	* Rename column
	*
	*/
	$test[] = '0.9.17.597';

	function property_upgrade0_9_17_597()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_responsibility_role', 'responsibility', 'responsibility_id');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.598';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.598 to 0.9.17.599
	* Add columns to fm_b_account
	*
	*/
	$test[] = '0.9.17.598';

	function property_upgrade0_9_17_598()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_b_account', 'active', array('type' => 'int',
			'precision' => '2', 'nullable' => True, 'default' => '0'));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_b_account', 'user_id', array('type' => 'int',
			'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_b_account', 'entry_date', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_b_account', 'modified_date', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->query('UPDATE fm_b_account SET active = 1', __LINE__, __FILE__);

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_r_agreement');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_r_agreement_category');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_r_agreement_item');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_r_agreement_item_history');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_r_agreement_common');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_r_agreement_c_history');

		$GLOBALS['phpgw_setup']->oProc->query('DELETE FROM fm_cache', __LINE__, __FILE__);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilag', 'currency', array('type' => 'varchar',
			'precision' => '3', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf', 'currency', array(
			'type' => 'varchar', 'precision' => '3', 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_ecobilag SET currency = 'NOK'", __LINE__, __FILE__);
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_ecobilagoverf SET currency = 'NOK'", __LINE__, __FILE__);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.599';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.599 to 0.9.17.600
	* Add responsibility roles
	*
	*/
	$test[] = '0.9.17.599';

	function property_upgrade0_9_17_599()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_custom_menu_items', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 200, 'nullable' => False),
				'url' => array('type' => 'text', 'nullable' => True),
				'location' => array('type' => 'varchar', 'precision' => 200, 'nullable' => False),
				'local_files' => array('type' => 'int', 'precision' => 2, 'nullable' => true),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.600';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.600 to 0.9.17.601
	* Add custom fields to request
	*
	*/
	$test[] = '0.9.17.600';

	function property_upgrade0_9_17_600()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$location_id	= $GLOBALS['phpgw']->locations->get_id('property', '.project.request');
		$sql = "UPDATE phpgw_locations SET allow_c_attrib = 1, c_attrib_table = 'fm_request' WHERE location_id = {$location_id}";
		$GLOBALS['phpgw_setup']->oProc->query($sql);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.601';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.601 to 0.9.17.602
	* Add fields voucher handling
	*
	*/
	$test[] = '0.9.17.601';

	function property_upgrade0_9_17_601()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_ecobilag_process_code', array(
				'fd' => array(
				'id' => array('type' => 'varchar', 'precision' => 10, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 200, 'nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilag', 'process_log', array(
			'type' => 'text', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf', 'process_log', array(
			'type' => 'text', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilag', 'process_code', array(
			'type' => 'varchar', 'precision' => '10', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf', 'process_code', array(
			'type' => 'varchar', 'precision' => '10', 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.602';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.602 to 0.9.17.603
	* Add templates to response from helpdesk
	*
	*/
	$test[] = '0.9.17.602';

	function property_upgrade0_9_17_602()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_response_template', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 200, 'nullable' => False),
				'content' => array('type' => 'text', 'nullable' => True),
				'public' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.603';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.603 to 0.9.17.604
	* convert data for datatype CH: from serialized array to comma separated list
	*
	*/
	$test[] = '0.9.17.603';

	function property_upgrade0_9_17_603()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query('SELECT count(*) as cnt FROM fm_location_type');
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$levels = $GLOBALS['phpgw_setup']->oProc->f('cnt');

		for($i = 1; $i < ($levels + 1); $i++)
		{
			$sql = "UPDATE phpgw_locations SET c_attrib_table = 'fm_location{$i}' WHERE name = '.location.{$i}' AND c_attrib_table IS NULL";
			$GLOBALS['phpgw_setup']->oProc->query($sql);
		}


		$sql = "SELECT c_attrib_table, column_name FROM phpgw_cust_attribute JOIN phpgw_locations ON phpgw_cust_attribute.location_id = phpgw_locations.location_id WHERE datatype = 'CH' GROUP BY c_attrib_table, column_name";

		$GLOBALS['phpgw_setup']->oProc->query($sql);

		$attribs = array();

		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$attribs[$GLOBALS['phpgw_setup']->oProc->f('c_attrib_table')][] = $GLOBALS['phpgw_setup']->oProc->f('column_name');
		}

		$value_set = array();
		foreach($attribs as $table => $columns)
		{
			$id_name = 'id';
			if(preg_match('/(^fm_location)/', $table))
			{
				$id_name = 'location_code';
			}

			foreach($columns as $column)
			{
				$sql = "SELECT {$id_name}, {$column} FROM {$table} WHERE {$column} IS NOT NULL";
				$GLOBALS['phpgw_setup']->oProc->query($sql);
				while($GLOBALS['phpgw_setup']->oProc->next_record())
				{
					if($value = $GLOBALS['phpgw_setup']->oProc->f($column))
					{
						if(@unserialize($value))
						{
							$value = ',' . implode(',', unserialize($value)) . ',';
						}
						else
						{
							$value = ",{$value}";
						}

						$value_set[] = array
						(
							'table'		=> $table,
							'id_name'	=> $id_name,
							'id_value'	=> $GLOBALS['phpgw_setup']->oProc->f($id_name),
							'column'	=> $column,
							'value'		=> $value
						);
					}
				}
			}
		}

		foreach($value_set as $update)
		{
			$sql = "UPDATE {$update['table']} SET  {$update['column']} = '{$update['value']}' WHERE {$update['id_name']} = '{$update['id_value']}'";
			$GLOBALS['phpgw_setup']->oProc->query($sql);
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.604';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.604 to 0.9.17.605
	* Add columns to  table fm_tts_tickets and fm_tts_status
	*
	*/
	$test[] = '0.9.17.604';

	function property_upgrade0_9_17_604()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_status', 'actual_cost', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'branch_id', array(
			'type' => 'int', 'precision' => '4', 'nullable' => True));
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.605';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.605 to 0.9.17.606
	* Add authorities demands type to request
	*
	*/
	$test[] = '0.9.17.605';

	function property_upgrade0_9_17_605()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_authorities_demands', array(
				'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 200, 'nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.606';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.606 to 0.9.17.607
	* Add authorities demands type to request
	*
	*/
	$test[] = '0.9.17.606';

	function property_upgrade0_9_17_606()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_b_account_category', 'active', array(
			'type' => 'int', 'precision' => '2', 'nullable' => True, 'default' => '0'));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_b_account_category', 'project_group', array(
			'type' => 'int', 'precision' => '2', 'nullable' => True, 'default' => '0'));
		$GLOBALS['phpgw_setup']->oProc->query('UPDATE fm_b_account_category SET active = 1', __LINE__, __FILE__);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.607';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.607 to 0.9.17.608
	* Add location_link_level
	*
	*/
	$test[] = '0.9.17.607';

	function property_upgrade0_9_17_607()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category', 'location_link_level', array(
			'type' => 'int', 'precision' => '4', 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.608';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.608 to 0.9.17.609
	* Add location_link_level
	*
	*/
	$test[] = '0.9.17.608';

	function property_upgrade0_9_17_608()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_request_status', 'closed', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_request_status', 'in_progress', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_request_status', 'delivered', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_request_status', 'sorting', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.609';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.609 to 0.9.17.610
	* Add location_link_level
	*
	*/
	$test[] = '0.9.17.609';

	function property_upgrade0_9_17_609()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_request', 'closed_date', array('type' => 'int',
			'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_request', 'in_progress_date', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_request', 'delivered_date', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.610';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.610 to 0.9.17.611
	* Add budget to project group
	*
	*/
	$test[] = '0.9.17.610';

	function property_upgrade0_9_17_610()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project_group', 'budget', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.611';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.611 to 0.9.17.612
	* Add contract sum to orders
	*
	*/
	$test[] = '0.9.17.611';

	function property_upgrade0_9_17_611()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder', 'contract_sum', array(
			'type' => 'decimal', 'precision' => '20', 'scale' => '2', 'nullable' => True,
			'default' => '0.00'));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.612';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.612 to 0.9.17.613
	* Add regulations
	*
	*/
	$test[] = '0.9.17.612';

	function property_upgrade0_9_17_612()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_request', 'regulations', array('type' => 'varchar',
			'precision' => 100, 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
		'fm_regulations', array(
				'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
				'descr' => array('type' => 'text', 'nullable' => True),
				'external_ref' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.613';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.613 to 0.9.17.614
	* Add parent to regulations
	*
	*/
	$test[] = '0.9.17.613';

	function property_upgrade0_9_17_613()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_regulations', 'parent_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.614';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.614 to 0.9.17.615
	* Add historical consume to request
	*
	*/
	$test[] = '0.9.17.614';

	function property_upgrade0_9_17_614()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_request_consume', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'request_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'amount' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'date' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'descr' => array('type' => 'text', 'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array('fm_request' => array('request_id' => 'id')),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.615';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.615 to 0.9.17.616
	* Enable hierarchy to custom menu
	*
	*/
	$test[] = '0.9.17.615';

	function property_upgrade0_9_17_615()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_custom_menu_items', 'parent_id', array(
			'type' => 'int', 'precision' => '4', 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.616';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.616 to 0.9.17.617
	* rename field, add customized url-target
	*
	*/
	$test[] = '0.9.17.616';

	function property_upgrade0_9_17_616()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_custom_menu_items', 'name', 'text');
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_custom_menu_items', 'target', array(
			'type' => 'varchar', 'precision' => '15', 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.617';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.616 to 0.9.17.617
	* Sync fm_locations with fm_locationX
	*
	*/
	$test[] = '0.9.17.617';

	function property_upgrade0_9_17_617()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("SELECT max(level) as level FROM fm_locations", __LINE__, __FILE__);
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$level = $GLOBALS['phpgw_setup']->oProc->f('level');
		$ids = array();
		for($i = 1; $i < ($level + 1); $i++)
		{
			$sql = "SELECT id FROM fm_locations LEFT JOIN fm_location{$i} ON fm_locations.location_code = fm_location{$i}.location_code"
					. " WHERE fm_location{$i}.location_code IS NULL AND LEVEL = {$i}";
			$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
			while($GLOBALS['phpgw_setup']->oProc->next_record())
			{
				$ids[] = $GLOBALS['phpgw_setup']->oProc->f('id');
			}
		}

		if($ids)
		{
			$sql = 'DELETE FROM fm_locations WHERE id IN(' . implode(',', $ids) . ')';
			$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.618';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.618 to 0.9.17.619
	*/
	$test[] = '0.9.17.618';

	function property_upgrade0_9_17_618()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location_type', 'list_documents', array(
			'type' => 'int', 'precision' => '2', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_type SET list_documents = 1");

		$GLOBALS['phpgw_setup']->oProc->query("SELECT count(*) as cnt FROM fm_location_type");
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$locations = $GLOBALS['phpgw_setup']->oProc->f('cnt') + 1;
		for($level = 5; $level < $locations; $level++)
		{
			$GLOBALS['phpgw_setup']->oProc->AlterColumn("fm_location{$level}", "loc{$level}_name", array(
				'type' => 'varchar', 'precision' => '50', 'nullable' => True));
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.619';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.619 to 0.9.17.620
	* Add tentative planning to request
	*
	*/
	$test[] = '0.9.17.619';

	function property_upgrade0_9_17_619()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_request_planning', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'request_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'amount' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'date' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'descr' => array('type' => 'text', 'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array('fm_request' => array('request_id' => 'id')),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.620';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.620 to 0.9.17.621
	*
	*/
	$test[] = '0.9.17.620';

	function property_upgrade0_9_17_620()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_request_condition_type', 'descr', 'name');
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_request_condition_type', 'descr', array(
			'type' => 'varchar', 'precision' => '255', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_request_condition', 'reference', array(
			'type' => 'int', 'precision' => '4', 'default' => '0', 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.621';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.621 to 0.9.17.622
	* Add locations missing from clean install
	*
	*/
	$test[] = '0.9.17.621';

	function property_upgrade0_9_17_621()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw']->locations->add('.invoice.dimb', 'A dimension for accounting', 'property');
		$GLOBALS['phpgw']->locations->add('.scheduled_events', 'Scheduled events', 'property');

		$locations = array
		(
			'property.ticket'	=> '.ticket',
			'property.project'	=> '.project',
			'property.document' => '.document',
			'fm_vendor'			=> '.vendor',
			'fm_tenant'			=> '.tenant',
			'fm_owner'			=> '.owner'
		);

		foreach($locations as $dummy => $location)
		{
			$GLOBALS['phpgw']->locations->add("{$location}.category", 'Categories', 'property');
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.622';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.622 to 0.9.17.623
	* Allow filtering of buildingparts depending of type of use
	*
	*/
	$test[] = '0.9.17.622';

	function property_upgrade0_9_17_622()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_building_part', 'id', array('type' => 'varchar',
			'precision' => '5', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_building_part', 'filter_1', array(
			'type' => 'int', 'precision' => '2', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_building_part', 'filter_2', array(
			'type' => 'int', 'precision' => '2', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_building_part', 'filter_3', array(
			'type' => 'int', 'precision' => '2', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_building_part', 'filter_4', array(
			'type' => 'int', 'precision' => '2', 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.623';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.623 to 0.9.17.624
	* Add column missing from fresh install.
	*
	*/
	$test[] = '0.9.17.623';

	function property_upgrade0_9_17_623()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$metadata = $GLOBALS['phpgw_setup']->oProc->m_odb->metadata('fm_project');

		if(!isset($metadata['account_group']))
		{
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project', 'account_group', array(
				'type' => 'int', 'precision' => '4', 'nullable' => True));
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.624';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.624 to 0.9.17.625
	* Add flag for eav modelling
	*/
	$test[] = '0.9.17.624';

	function property_upgrade0_9_17_624()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category', 'is_eav', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.625';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.625 to 0.9.17.626
	* Add periodization to voucher handling
	*/
	$test[] = '0.9.17.625';

	function property_upgrade0_9_17_625()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();




		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_eco_periodization', array(
				'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '64', 'nullable' => False)
				),
				'pk' => array('id'),
				'ix' => array(),
				'fk' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilag', 'periodization', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilag', 'periodization_start', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf', 'periodization', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf', 'periodization_start', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.626';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.626 to 0.9.17.627
	* Add assign voucher id on export from system
	*/
	$test[] = '0.9.17.626';

	function property_upgrade0_9_17_626()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilag', 'bilagsnr_ut', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf', 'bilagsnr_ut', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_idgenerator', array(), 'maxvalue');
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_idgenerator', 'date_from', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True, 'default' => '0'));

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_idgenerator(name,value,descr) "
		. "VALUES('bilagsnr_ut', 0, 'Bilagsnummer utgående')", __LINE__, __FILE__);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.627';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.627 to 0.9.17.628
	* Alter primary key
	*/
	$test[] = '0.9.17.627';

	function property_upgrade0_9_17_627()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_idgenerator', 'date_from', 'start_date');
		$GLOBALS['phpgw_setup']->oProc->query('ALTER TABLE fm_idgenerator DROP CONSTRAINT fm_idgenerator_pkey', __LINE__, __FILE__);
		$GLOBALS['phpgw_setup']->oProc->query('ALTER TABLE fm_idgenerator ADD CONSTRAINT fm_idgenerator_pkey PRIMARY KEY(name,start_date)', __LINE__, __FILE__);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.628';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.628 to 0.9.17.629
	* Add appname to responsibility_role in order to filter by defining app.
	*/
	$test[] = '0.9.17.628';

	function property_upgrade0_9_17_628()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_responsibility_role', 'appname', array(
			'type' => 'varchar', 'precision' => 25, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_responsibility_role SET appname = 'property'", __LINE__, __FILE__);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_responsibility_role', 'appname', array(
			'type' => 'varchar', 'precision' => '25', 'nullable' => false));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.629';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.629 to 0.9.17.630
	* Add convert invoice configuration to separate section
	*/
	$test[] = '0.9.17.629';

	function property_upgrade0_9_17_629()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$config = CreateObject('phpgwapi.config', 'property');
		$config->read();

		$custom_config = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.invoice'));

		// common
		$receipt_section_common = $custom_config->add_section(array
			(
				'name' => 'common',
				'descr' => 'common invoice config'
			)
		);

		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_common['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'host',
				'descr'			=> 'Host',
				'value'			=> $config->config_data['invoice_ftp_host'],
			)
		);
		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_common['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'user',
				'descr'			=> 'User',
				'value'			=> $config->config_data['invoice_ftp_user'],
			)
		);
		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_common['section_id'],
				'input_type'	=> 'password',
				'name'			=> 'password',
				'descr'			=> 'Password',
				'value'			=> $config->config_data['invoice_ftp_password'],
			)
		);
		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_common['section_id'],
				'input_type'	=> 'listbox',
				'name'			=> 'method',
				'descr'			=> 'Export / import method',
			'choice' => array('local', 'ftp', 'ssh'),
				'value'			=> $config->config_data['invoice_export_method'],
			)
		);

		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_common['section_id'],
				'attrib_id'		=> $receipt['attrib_id'],
				'input_type'	=> 'listbox',
				'name'			=> 'invoice_approval',
				'descr'			=> 'Number of persons required to approve for payment',
			'choice' => array(1, 2),
				'value'			=> $config->config_data['invoice_approval'],
			)
		);

		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_common['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'baseurl_invoice',
				'descr'			=> 'baseurl on remote server for image of invoice',
				'value'			=> $config->config_data['baseurl_invoice'],
			)
		);

		// import:
		$receipt_section_import = $custom_config->add_section(array
			(
				'name' => 'import',
				'descr' => 'import invoice config'
			)
		);

		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_import['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'local_path',
				'descr'			=> 'path on local sever to store imported files',
				'value'			=> $config->config_data['import_path'],
			)
		);

		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_import['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'budget_responsible',
				'descr'			=> 'default initials if responsible can not be found',
				'value'			=> 'karhal'
			)
		);

		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_import['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'remote_basedir',
				'descr'			=> 'basedir on remote server to fetch files from',
			)
		);

		//export
		$receipt_section_export = $custom_config->add_section(array
			(
				'name' => 'export',
				'descr' => 'Invoice export'
			)
		);
		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_export['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'cleanup_old',
				'descr'			=> 'Overføre manuelt registrerte fakturaer rett til historikk'
			)
		);
		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_export['section_id'],
				'input_type'	=> 'date',
				'name'			=> 'dato_aarsavslutning',
				'descr'			=> "Dato for årsavslutning: overført pr. desember foregående år"
			)
		);
		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_export['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'path',
				'descr'			=> 'path on local sever to store exported files',
				'value'			=> $config->config_data['export_path'],
			)
		);

		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_export['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'pre_path',
				'descr'			=> 'path on local sever to store exported files for pre approved vouchers',
				'value'			=> $config->config_data['export_pre_path'],
			)
		);

		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_export['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'remote_basedir',
				'descr'			=> 'basedir on remote server to receive files',
			)
		);


		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.630';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.630 to 0.9.17.631
	* keep track of projects with open orders
	*/
	$test[] = '0.9.17.630';

	function property_upgrade0_9_17_630()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$sql = 'CREATE OR REPLACE VIEW fm_open_workorder_view AS'
			. ' SELECT fm_workorder.id, fm_workorder.project_id, fm_workorder_status.descr FROM fm_workorder'
			. ' JOIN fm_workorder_status ON fm_workorder.status = fm_workorder_status.id WHERE fm_workorder_status.delivered IS NULL AND fm_workorder_status.closed IS NULL';

		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.631';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.631 to 0.9.17.632
	* allow manual records of invoice
	*/
	$test[] = '0.9.17.631';

	function property_upgrade0_9_17_631()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf', 'manual_record', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.632';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.632 to 0.9.17.633
	* Add view on fm_ecobilag
	*/
	$test[] = '0.9.17.632';

	function property_upgrade0_9_17_632()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");

		$sql = 'CREATE OR REPLACE VIEW fm_ecobilag_sum_view AS'
			. ' SELECT DISTINCT bilagsnr, sum(godkjentbelop) AS approved_amount, sum(belop) AS amount FROM fm_ecobilag  GROUP BY bilagsnr ORDER BY bilagsnr ASC';

		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.633';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.633 to 0.9.17.634
	* Add project budget per year
	*/
	$test[] = '0.9.17.633';

	function property_upgrade0_9_17_633()
	{
		date_default_timezone_set('UTC');
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_project_budget', array(
				'fd' => array(
				'project_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'year' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'budget' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'nullable' => True,
					'default' => '0.00'),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
				),
			'pk' => array('project_id', 'year'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$sql = "SELECT id, budget, start_date, user_id,entry_date FROM fm_project ORDER BY ID ASC";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		$budget_values = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$budget_values[] = array
			(
				'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
				'budget'		=> $GLOBALS['phpgw_setup']->oProc->f('budget'),
				'start_date'	=> $GLOBALS['phpgw_setup']->oProc->f('start_date'),
				'user_id'		=> $GLOBALS['phpgw_setup']->oProc->f('user_id'),
				'entry_date'	=> $GLOBALS['phpgw_setup']->oProc->f('entry_date')
			);
		}

		foreach($budget_values as $entry)
		{
			if($entry['budget'] && abs($entry['budget']) > 0)
			{
				$value_set = array
				(
					'project_id'		=> $entry['id'],
					'year' => date('Y', $entry['start_date']),
					'budget'			=> $entry['budget'],
					'user_id'			=> $entry['user_id'],
					'entry_date'		=> $entry['entry_date'],
					'modified_date'		=> $entry['entry_date']
				);
				$cols = implode(',', array_keys($value_set));
				$values	= $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($value_set));
				$sql = "INSERT INTO fm_project_budget ({$cols}) VALUES ({$values})";
				$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
			}
		}

		$sql = 'SELECT DISTINCT pmwrkord_code from fm_ecobilagoverf WHERE loc1 IS NULL AND pmwrkord_code IS NOT NULL';
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		$orders = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$orders[] = $GLOBALS['phpgw_setup']->oProc->f('pmwrkord_code');
		}

		foreach($orders as $order)
		{
			$sql = "SELECT loc1 FROM fm_project JOIN fm_workorder ON fm_project.id = fm_workorder.project_id WHERE fm_workorder.id = '{$order}'";
			$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
			$GLOBALS['phpgw_setup']->oProc->next_record();
			if($loc1 = $GLOBALS['phpgw_setup']->oProc->f('loc1'))
			{
				$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_ecobilagoverf SET loc1 = '{$loc1}' WHERE pmwrkord_code = '{$order}'", __LINE__, __FILE__);
			}
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.634';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.633 to 0.9.17.634
	* Add project budget per year
	*/
	$test[] = '0.9.17.634';

	function property_upgrade0_9_17_634()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_responsibility_module', array(
				'fd' => array(
				'responsibility_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'location_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'cat_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'active' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'created_on' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				),
				'pk' => array('responsibility_id', 'location_id', 'cat_id'),
				'fk' => array
						(
							'fm_responsibility' => array('responsibility_id' => 'id'),
							'phpgw_locations' 	=> array('location_id' => 'location_id'),
							'phpgw_categories'	=> array('cat_id' => 'cat_id')
						),
				'ix' => array(),
				'uc' => array()
			)
		);

		$sql = 'SELECT * FROM fm_responsibility';
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		$responsibilities = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			if($cat_id = $GLOBALS['phpgw_setup']->oProc->f('cat_id'))
			{
				$responsibilities[] = array
				(
					'responsibility_id' => $GLOBALS['phpgw_setup']->oProc->f('id'),
					'location_id' => $GLOBALS['phpgw_setup']->oProc->f('location_id'),
					'cat_id' => $cat_id,
					'active' => $GLOBALS['phpgw_setup']->oProc->f('active'),
					'created_on' => $GLOBALS['phpgw_setup']->oProc->f('created_on'),
					'created_by' => $GLOBALS['phpgw_setup']->oProc->f('created_by')
				);
			}
		}

		foreach($responsibilities as $value_set)
		{
			$cols = implode(',', array_keys($value_set));
			$values	= $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($value_set));
			$sql = "INSERT INTO fm_responsibility_module ({$cols}) VALUES ({$values})";
			$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		}

		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_responsibility', array(), 'location_id');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_responsibility', array(), 'cat_id');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_responsibility', array(), 'active');


		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_responsibility_role', 'location', 'location_level');
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_responsibility_role', 'location_level', array(
			'type' => 'varchar', 'precision' => '200', 'nullable' => True));

		$sql = 'SELECT * FROM fm_responsibility_role';
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		$roles = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$roles[] = array
			(
				'id' => $GLOBALS['phpgw_setup']->oProc->f('id'),
				'location_level' => explode(',', ltrim($GLOBALS['phpgw_setup']->oProc->f('location_level'), '.location.'))
			);
		}

		foreach($roles as $role)
		{
			$sql = 'UPDATE fm_responsibility_role SET location_level = ' . implode(',', $role['location_level']) . " WHERE id = {$role['id']}";
			$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		}


		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.635';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.635 to 0.9.17.636
	* Add percent value to tax-code
	*
	*/
	$test[] = '0.9.17.635';

	function property_upgrade0_9_17_635()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecomva', 'percent', array('type' => 'int',
			'precision' => 4, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.636';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.636 to 0.9.17.637
	* Add approve tag and mail recipients to workorders
	*
	*/
	$test[] = '0.9.17.636';

	function property_upgrade0_9_17_636()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder', 'approved', array('type' => 'int',
			'precision' => 2, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder', 'mail_recipients', array(
			'type' => 'varchar', 'precision' => 255, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.637';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.637 to 0.9.17.638
	* Modified timestamp til tickets
	*
	*/
	$test[] = '0.9.17.637';

	function property_upgrade0_9_17_637()
	{
		date_default_timezone_set('UTC');
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'modified_date', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_tts_tickets', 'address', array(
			'type' => 'varchar', 'precision' => '255', 'nullable' => True));

		$sql = 'SELECT id, entry_date FROM fm_tts_tickets';
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);

		$tickets = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$tickets[] = array
			(
				'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
				'entry_date'	=> $GLOBALS['phpgw_setup']->oProc->f('entry_date')
			);
		}

		foreach($tickets as $ticket)
		{
			$sql = "SELECT history_timestamp FROM fm_tts_history WHERE history_record_id = {$ticket['id']} ORDER BY history_timestamp DESC";
			$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
			if($GLOBALS['phpgw_setup']->oProc->next_record())
			{
				$modified_date = (int)strtotime($GLOBALS['phpgw_setup']->oProc->f('history_timestamp'));
			}
			else
			{
				$modified_date = (int)$ticket['entry_date'];
			}
			$sql = "UPDATE fm_tts_tickets SET modified_date = {$modified_date} WHERE id = {$ticket['id']}";
			$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.638';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.638 to 0.9.17.639
	* Add relation contact-location
	*
	*/
	$test[] = '0.9.17.638';

	function property_upgrade0_9_17_638()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_location_contact', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'contact_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'location_code' => array('type' => 'varchar', 'precision' => 20, 'nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => False)
				),
				'pk' => array('id'),
			'fk' => array('fm_locations' => array('location_code' => 'location_code'), 'phpgw_contact' => array(
					'contact_id' => 'contact_id')),
				'ix' => array(),
				'uc' => array('contact_id', 'location_code')
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.639';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.638 to 0.9.17.639
	* Add fm_ecobilag_process_log
	*
	*/
	$test[] = '0.9.17.639';

	function property_upgrade0_9_17_639()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_ecobilag_process_log', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'bilagsnr' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'process_code' => array('type' => 'varchar', 'precision' => 10, 'nullable' => true),
				'process_log' => array('type' => 'text', 'nullable' => true),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$sql = 'SELECT bilagsnr, process_code, process_log FROM fm_ecobilag WHERE process_code IS NOT NULL OR process_log IS NOT NULL';
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);

		$logs = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$logs[] = array
			(
				'bilagsnr'		=> $GLOBALS['phpgw_setup']->oProc->f('bilagsnr'),
				'process_code'	=> $GLOBALS['phpgw_setup']->oProc->f('process_code'),
				'process_log'	=> $GLOBALS['phpgw_setup']->oProc->f('process_log')
			);
		}

		$sql = 'SELECT bilagsnr, process_code, process_log FROM fm_ecobilagoverf WHERE process_code IS NOT NULL OR process_log IS NOT NULL';
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);

		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$logs[] = array
			(
				'bilagsnr'		=> $GLOBALS['phpgw_setup']->oProc->f('bilagsnr'),
				'process_code'	=> $GLOBALS['phpgw_setup']->oProc->f('process_code'),
				'process_log'	=> $GLOBALS['phpgw_setup']->oProc->f('process_log')
			);
		}

		foreach($logs as $log)
		{
			$cols = implode(',', array_keys($log));
			$values	= $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($log));
			$sql = "INSERT INTO fm_ecobilag_process_log ({$cols}) VALUES ({$values})";
			$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		}
		/*
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_ecobilag',array(),'process_code');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_ecobilag',array(),'process_log');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_ecobilagoverf',array(),'process_code');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_ecobilagoverf',array(),'process_log');
		 */
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.640';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	$test[] = '0.9.17.640';

	function property_upgrade0_9_17_640()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$metadata = $GLOBALS['phpgw_setup']->oProc->m_odb->metadata('fm_ecobilag');

		if(!isset($metadata['process_log']))
		{
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilag', 'process_log', array(
				'type' => 'text', 'nullable' => True));
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf', 'process_log', array(
				'type' => 'text', 'nullable' => True));
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilag', 'process_code', array(
				'type' => 'varchar', 'precision' => '10', 'nullable' => True));
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf', 'process_code', array(
				'type' => 'varchar', 'precision' => '10', 'nullable' => True));
		}

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilag', 'line_text', array('type' => 'varchar',
			'precision' => '255', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf', 'line_text', array(
			'type' => 'varchar', 'precision' => '255', 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.641';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	$test[] = '0.9.17.641';

	function property_upgrade0_9_17_641()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_project', 'category', array('type' => 'int',
			'precision' => 4, 'nullable' => True, 'default' => '0'));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilag', 'dime', array('type' => 'int',
			'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf', 'dime', array('type' => 'int',
			'precision' => 4, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.642';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	$test[] = '0.9.17.642';

	function property_upgrade0_9_17_642()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_ecodimb', 'id', array('type' => 'int',
			'precision' => '4', 'nullable' => false));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_ecobilag', 'splitt', array('type' => 'int',
			'precision' => '4', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_ecobilagoverf', 'splitt', array(
			'type' => 'int', 'precision' => '4', 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_ecodimb_role', array(
				'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '25', 'nullable' => False)
				),
				'pk' => array('id'),
				'ix' => array(),
				'fk' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_ecodimb_role (id, name) VALUES (1, 'Bestiller')", __LINE__, __FILE__);
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_ecodimb_role (id, name) VALUES (2, 'Attestant')", __LINE__, __FILE__);
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_ecodimb_role (id, name) VALUES (3, 'Anviser')", __LINE__, __FILE__);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_ecodimb_role_user', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'ecodimb' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'role_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'default_user' => array('type' => 'int', 'precision' => '2', 'nullable' => true,
					'default' => 0),
				'active_from' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'active_to' => array('type' => 'int', 'precision' => 4, 'nullable' => True, 'default' => 0),
				'created_on' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'expired_on' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'expired_by' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				),
				'pk' => array('id'),
				'ix' => array(),
			'fk' => array('fm_ecodimb_role' => array('role_id' => 'id'), 'fm_ecodimb' => array(
					'ecodimb' => 'id'), 'phpgw_accounts' => array('user_id' => 'account_id')),
				'uc' => array()
			)
		);

		$sql = 'SELECT * FROM fm_responsibility_contact JOIN phpgw_accounts ON fm_responsibility_contact.contact_id = phpgw_accounts.person_id WHERE expired_on IS NULL AND ecodimb IS NOT NULL';
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);

		$roles = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$roles[] = array
			(
				'ecodimb'		=> $GLOBALS['phpgw_setup']->oProc->f('ecodimb'),
				'user_id'		=> $GLOBALS['phpgw_setup']->oProc->f('account_id'),
				'role_id'		=> $GLOBALS['phpgw_setup']->oProc->f('responsibility_id') == 2 ? 3 : 2,
				'default_user'	=> $GLOBALS['phpgw_setup']->oProc->f('responsibility_id') == 2 ? 1 : '',
				'active_from'	=> $GLOBALS['phpgw_setup']->oProc->f('active_from'),
				'active_to'		=> $GLOBALS['phpgw_setup']->oProc->f('active_to'),
				'created_on'	=> $GLOBALS['phpgw_setup']->oProc->f('created_on'),
				'created_by'	=> $GLOBALS['phpgw_setup']->oProc->f('created_by')
			);
			$i++;
		}

		foreach($roles as $role)
		{
			$cols = implode(',', array_keys($role));
			$values	= $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($role));
			$sql = "INSERT INTO fm_ecodimb_role_user ({$cols}) VALUES ({$values})";
			$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.643';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.643 to 0.9.17.644
	* Add view on fm_ecobilag
	*/
	$test[] = '0.9.17.643';

	function property_upgrade0_9_17_643()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_workorder SET combined_cost = 0 WHERE combined_cost IS NULL");

		$sql = 'CREATE OR REPLACE VIEW fm_orders_actual_cost_view AS'
			. ' SELECT fm_orders.id as order_id, sum(godkjentbelop) AS actual_cost FROM fm_ecobilagoverf join fm_orders ON fm_ecobilagoverf.pmwrkord_code = fm_orders.id GROUP BY fm_orders.id';

		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.644';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.644 to 0.9.17.645
	* Add view on fm_ecobilag
	*/
	$test[] = '0.9.17.644';

	function property_upgrade0_9_17_644()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder', 'actual_cost', array(
			'type'		=> 'decimal',
			'precision'	=> '20',
			'scale'		=> '2',
			'nullable'	=> true,
			'default'	=> '0.00'
			)
		);

		$sql = 'SELECT order_id, actual_cost FROM fm_orders_actual_cost_view';
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);

		$orders = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$orders[] = array
			(
				'order_id'		=>	$GLOBALS['phpgw_setup']->oProc->f('order_id'),
				'actual_cost'	=>	$GLOBALS['phpgw_setup']->oProc->f('actual_cost')
			);
		}
		foreach($orders as $order)
		{
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_workorder SET actual_cost = '{$order['actual_cost']}' WHERE id = '{$order['order_id']}'", __LINE__, __FILE__);
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.645';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.645 to 0.9.17.646
	* Add optional inheritance of location from project to order
	*/
	$test[] = '0.9.17.645';

	function property_upgrade0_9_17_645()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project', 'inherit_location', array(
			'type'		=> 'int',
			'precision'	=> 2,
			'nullable'	=> true,
			'default'	=> 1
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_project SET inherit_location = 1", __LINE__, __FILE__);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.646';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.646 to 0.9.17.647
	* Update values
	*/
	$test[] = '0.9.17.646';

	function property_upgrade0_9_17_646()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("SELECT project_id, sum(budget) AS sum_budget FROM fm_project_budget GROUP BY project_id", __LINE__, __FILE__);

		$values = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$values[] = array
			(
				'id'			=> (int)$GLOBALS['phpgw_setup']->oProc->f('project_id'),
				'budget'		=> (int)$GLOBALS['phpgw_setup']->oProc->f('sum_budget')
			);
		}

		foreach($values as $entry)
		{
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_project SET budget = {$entry['budget']} WHERE id =  {$entry['id']}", __LINE__, __FILE__);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT id FROM fm_workorder", __LINE__, __FILE__);

		$orders = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$orders[$GLOBALS['phpgw_setup']->oProc->f('id')] = true;
		}

		execMethod('property.soXport.update_actual_cost_from_archive', $orders);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.647';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.647 to 0.9.17.648
	* Implement periodizations at project budgetting
	*/
	$test[] = '0.9.17.647';

	function property_upgrade0_9_17_647()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_department',  array(
				'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'parent_id' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'name' => array('type' => 'varchar', 'precision' => '60', 'nullable' => False),
				'created_on' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'modified_on' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
				),
				'pk' => array('id'),
				'ix' => array(),
				'fk' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_department  (id, name, created_on, created_by) VALUES (1, 'Department'," . time() . ",6 ) ", __LINE__, __FILE__);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecodimb', 'department', array(
			'type'		=> 'int',
			'precision'	=> 4,
			'nullable'	=> true
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_ecodimb SET department = 1", __LINE__, __FILE__);

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_ecodimb', 'department', array(
			'type'		=> 'int',
			'precision'	=> 4,
			'nullable'	=> false
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.648';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.648 to 0.9.17.649
	* Enable periodization of budget at project
	*/
	$test[] = '0.9.17.648';

	function property_upgrade0_9_17_648()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_eco_periodization_outline',  array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'periodization_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'month' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'value' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'nullable' => false,
					'default' => '0.00'),
				'remark' => array('type' => 'varchar', 'precision' => '60', 'nullable' => False),
				),
				'pk' => array('id'),
				'ix' => array(),
			'fk' => array('fm_eco_periodization' => array('periodization_id' => 'id')),
				'uc' => array('periodization_id', 'month')
			)
		);


		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_eco_periodization', 'active', array(
			'type'		=> 'int',
			'precision'	=> 2,
			'nullable'	=> true,
			'default' => 0
			)
		);


		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_eco_periodization SET active = 1", __LINE__, __FILE__);

		$sql = 'SELECT fm_project_budget.* FROM fm_project_budget JOIN fm_project ON fm_project_budget.project_id = fm_project.id';
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);

		$budgets = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$budgets[] = array
			(
				'project_id'	=> $GLOBALS['phpgw_setup']->oProc->f('project_id'),
				'year'			=> $GLOBALS['phpgw_setup']->oProc->f('year'),
				'month'			=> 0,
				'budget'		=> $GLOBALS['phpgw_setup']->oProc->f('budget'),
				'user_id'		=> $GLOBALS['phpgw_setup']->oProc->f('user_id'),
				'entry_date'	=> $GLOBALS['phpgw_setup']->oProc->f('entry_date'),
				'modified_date'	=> $GLOBALS['phpgw_setup']->oProc->f('modified_date'),
			);
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_project_budget');

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_project_budget',  array(
				'fd' => array(
				'project_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'year' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'month' => array('type' => 'int', 'precision' => 2, 'nullable' => False, 'default' => 0),
				'budget' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'nullable' => True,
					'default' => '0.00'),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
				),
			'pk' => array('project_id', 'year', 'month'),
				'fk' => array('fm_project' => array('project_id' => 'id')),
				'ix' => array(),
				'uc' => array()
			)
		);

		foreach($budgets as $budget)
		{
			$cols = implode(',', array_keys($budget));
			$values	= $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($budget));
			$sql = "INSERT INTO fm_project_budget ({$cols}) VALUES ({$values})";
			$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		}


		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.649';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.649 to 0.9.17.650
	* Enable join to locations on loc1
	*/
	$test[] = '0.9.17.649';

	function property_upgrade0_9_17_649()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_locations', 'loc1', array(
			'type'		=> 'varchar',
			'precision'	=> 10,
			'nullable'	=> true
			)
		);


		$sql = 'SELECT id, location_code FROM fm_locations';
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);

		$locations = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$location_arr	= explode('-', $GLOBALS['phpgw_setup']->oProc->f('location_code'));
			$locations[] = array
			(
				'id'		=> $GLOBALS['phpgw_setup']->oProc->f('id'),
				'loc1'		=> $location_arr[0]
			);
		}

		foreach($locations as $location)
		{
			$sql = "UPDATE fm_locations SET loc1 = '{$location['loc1']}' WHERE id = {$location['id']}";
			$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		}

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_locations', 'loc1', array(
			'type'		=> 'varchar',
			'precision'	=> 10,
			'nullable'	=> false
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.650';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.650 to 0.9.17.651
	* Enable to close periode on budget
	*/
	$test[] = '0.9.17.650';

	function property_upgrade0_9_17_650()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project_budget', 'closed', array(
			'type'		=> 'int',
			'precision'	=> 2,
			'nullable'	=> true
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.651';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.651 to 0.9.17.652
	* Enable to close periode on budget
	*/
	$test[] = '0.9.17.651';

	function property_upgrade0_9_17_651()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project', 'periodization_id', array(
			'type'		=> 'int',
			'precision'	=> 4,
			'nullable'	=> true
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.652';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.652 to 0.9.17.653
	* Enable to close periode on budget
	*/
	$test[] = '0.9.17.652';

	function property_upgrade0_9_17_652()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project_budget', 'order_amount', array(
			'type'		=> 'decimal',
			'precision'	=> 20,
			'scale' 	=> 2,
			'nullable'	=> true,
			'default'	=> '0.00'
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_eco_periodization_outline', 'value', array(
			'type'		=> 'decimal',
			'precision'	=> '20',
			'scale'		=> '6',
			'nullable'	=> false,
			'default'	=> '0.000000'
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.653';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.653 to 0.9.17.654
	* Add location_id to entities
	*/
	$test[] = '0.9.17.653';

	function property_upgrade0_9_17_653()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity', 'location_id', array(
			'type'		=> 'int',
			'precision'	=> 4,
			'nullable'	=> true
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category', 'location_id', array(
			'type'		=> 'int',
			'precision'	=> 4,
			'nullable'	=> true
			)
		);

		$sql = 'SELECT id, entity_id FROM fm_entity_category';
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);

		$categories = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$categories[] = array
			(
				'entity_id'	=> $GLOBALS['phpgw_setup']->oProc->f('entity_id'),
				'cat_id'	=> $GLOBALS['phpgw_setup']->oProc->f('id'),
			);
		}


		$sql = 'SELECT id FROM fm_entity';
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);

		$entities = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$entities[] = array
			(
				'entity_id'	=> $GLOBALS['phpgw_setup']->oProc->f('id'),
			);
		}

		foreach($categories as $category)
		{
			$location_id	= $GLOBALS['phpgw']->locations->get_id('property', ".entity.{$category['entity_id']}.{$category['cat_id']}");
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_entity_category SET location_id = {$location_id} WHERE entity_id = {$category['entity_id']} AND id = {$category['cat_id']}", __LINE__, __FILE__);
		}

		foreach($entities as $entity)
		{
			$location_id	= $GLOBALS['phpgw']->locations->get_id('property', ".entity.{$entity['entity_id']}");
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_entity SET location_id = {$location_id} WHERE id = {$entity['entity_id']}", __LINE__, __FILE__);
		}

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_entity', 'location_id', array(
			'type'		=> 'int',
			'precision'	=> 4,
			'nullable'	=> false
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_entity_category', 'location_id', array(
			'type'		=> 'int',
			'precision'	=> 4,
			'nullable'	=> false
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.654';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.654 to 0.9.17.655
	* Add Condition Survey as a referencing level to requests
	*/
	$test[] = '0.9.17.654';

	function property_upgrade0_9_17_654()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_request', 'condition_survey_id', array(
			'type'		=> 'int',
			'precision'	=> 4,
			'nullable'	=> true
			)
		);

		$GLOBALS['phpgw']->locations->add('.project.condition_survey', 'Condition Survey', 'property', true, 'fm_condition_survey', true);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_condition_survey', array(
				'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'title' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False),
				'p_num' => array('type' => 'varchar', 'precision' => '15', 'nullable' => True),
				'p_entity_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'p_cat_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'location_code' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'loc1' => array('type' => 'varchar', 'precision' => '6', 'nullable' => True),
				'loc2' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'loc3' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'loc4' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'descr' => array('type' => 'text', 'nullable' => True),
				'address' => array('type' => 'varchar', 'precision' => '255', 'nullable' => True),
				'status_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'category' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'coordinator_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'vendor_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'report_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("SELECT count(*) as cnt FROM fm_location_type");
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$levels = $GLOBALS['phpgw_setup']->oProc->f('cnt');

		for($level = 5; $level < ($levels + 1); $level++)
		{
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_condition_survey', "loc{$level}", array(
				'type'		=> 'varchar',
				'precision'	=> 4,
				'nullable'	=> true
				)
			);
		}

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_condition_survey_status', array(
				'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False),
				'closed' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'in_progress' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'delivered' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'sorting' => array('type' => 'int', 'precision' => '4', 'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_condition_survey_history', array(
				'fd' => array(
				'history_id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'history_record_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_appname' => array('type' => 'varchar', 'precision' => '64', 'nullable' => False),
				'history_owner' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_status' => array('type' => 'char', 'precision' => '2', 'nullable' => False),
				'history_new_value' => array('type' => 'text', 'nullable' => False),
				'history_old_value' => array('type' => 'text', 'nullable' => true),
				'history_timestamp' => array('type' => 'timestamp', 'nullable' => False, 'default' => 'current_timestamp')
				),
				'pk' => array('history_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);


		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.655';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.655 to 0.9.17.656
	* Add Condition Survey as a referencing level to requests
	*/
	$test[] = '0.9.17.655';

	function property_upgrade0_9_17_655()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'title', array(
			'type'		=> 'varchar',
			'precision'	=> 255,
			'nullable'	=> false
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.656';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.656 to 0.9.17.657
	* Alter planing dates to hold bigint
	*/
	$test[] = '0.9.17.656';

	function property_upgrade0_9_17_656()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_project', 'start_date', array(
			'type'		=> 'int',
			'precision'	=> 8,
			'nullable'	=> false
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_project', 'end_date', array(
			'type'		=> 'int',
			'precision'	=> 8,
			'nullable'	=> true
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_workorder', 'start_date', array(
			'type'		=> 'int',
			'precision'	=> 8,
			'nullable'	=> false
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_workorder', 'end_date', array(
			'type'		=> 'int',
			'precision'	=> 8,
			'nullable'	=> true
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'start_date', array(
			'type'		=> 'int',
			'precision'	=> 8,
			'nullable'	=> true
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'end_date', array(
			'type'		=> 'int',
			'precision'	=> 8,
			'nullable'	=> true
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request_consume', 'date', array(
			'type'		=> 'int',
			'precision'	=> 8,
			'nullable'	=> false
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request_planning', 'date', array(
			'type'		=> 'int',
			'precision'	=> 8,
			'nullable'	=> false
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.657';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.657 to 0.9.17.658
	* Add project types
	*/
	$test[] = '0.9.17.657';

	function property_upgrade0_9_17_657()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project', 'parent_id', array(
			'type'		=> 'int',
			'precision'	=> 4,
			'nullable'	=> true
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project', 'project_type_id', array(
			'type'		=> 'int',
			'precision'	=> 2,
			'nullable'	=> true
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project_budget', 'active', array(
			'type'		=> 'int',
			'precision'	=> 2,
			'nullable'	=> true
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_project_buffer_budget', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'buffer_project_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'amount_in' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'from_project' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'amount_out' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'to_project' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'remark' => array('type' => 'text', 'nullable' => true),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);


		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.658';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.658 to 0.9.17.659
	* Add view on fm_ecobilag
	*/
	$test[] = '0.9.17.658';

	function property_upgrade0_9_17_658()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");

		$sql = 'UPDATE fm_project SET project_type_id = 1 WHERE project_type_id IS NULL';

		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);

		$sql = 'CREATE OR REPLACE VIEW fm_orders_pending_cost_view AS'
			. ' SELECT fm_ecobilag.pmwrkord_code AS order_id, sum(fm_ecobilag.godkjentbelop) AS pending_cost FROM fm_ecobilag GROUP BY fm_ecobilag.pmwrkord_code';

		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);

		$sql = 'CREATE OR REPLACE VIEW fm_orders_actual_cost_view AS'
 			. ' SELECT fm_ecobilagoverf.pmwrkord_code AS order_id, sum(fm_ecobilagoverf.godkjentbelop) AS actual_cost FROM fm_ecobilagoverf  GROUP BY fm_ecobilagoverf.pmwrkord_code';

		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);


		$sql = 'CREATE OR REPLACE VIEW fm_orders_paid_or_pending_view AS
		 SELECT orders_paid_or_pending.order_id, orders_paid_or_pending.periode, orders_paid_or_pending.amount
 			FROM ( SELECT fm_ecobilagoverf.pmwrkord_code AS order_id, fm_ecobilagoverf.periode, sum(fm_ecobilagoverf.godkjentbelop) AS amount
                   FROM fm_ecobilagoverf
                 GROUP BY fm_ecobilagoverf.pmwrkord_code, fm_ecobilagoverf.periode
        		UNION ALL
                 	SELECT fm_ecobilag.pmwrkord_code AS order_id, fm_ecobilag.periode, sum(fm_ecobilag.godkjentbelop) AS amount
                   FROM fm_ecobilag
                 GROUP BY fm_ecobilag.pmwrkord_code, fm_ecobilag.periode) orders_paid_or_pending';

		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);


		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.659';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.659 to 0.9.17.660
	* Add fraction to periodization outline as an alternative
	*/
	$test[] = '0.9.17.659';

	function property_upgrade0_9_17_659()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();


		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_eco_periodization_outline', 'dividend', array(
			'type'		=> 'int',
			'precision'	=> 4,
			'nullable'	=> true
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_eco_periodization_outline', 'divisor', array(
			'type'		=> 'int',
			'precision'	=> 4,
			'nullable'	=> true
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_eco_periodization_outline', 'value', array(
			'type'		=> 'decimal',
			'precision'	=> '20',
			'scale'		=> '6',
			'nullable'	=> true
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.660';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.660 to 0.9.17.661
	* Add year and active-flag to project_buffer_budget
	*/
	$test[] = '0.9.17.660';

	function property_upgrade0_9_17_660()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();


		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project_buffer_budget', 'year', array(
			'type'		=> 'int',
			'precision'	=> 4,
			'nullable'	=> false
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project_buffer_budget', 'month', array(
			'type'		=> 'int',
			'precision'	=> 4,
			'nullable'	=> false,
			'default'	=> 0
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project_buffer_budget', 'active', array(
			'type'		=> 'int',
			'precision'	=> 2,
			'nullable'	=> true
			)
		);


		$GLOBALS['phpgw_setup']->oProc->query('UPDATE fm_project_budget SET active = 1', __LINE__, __FILE__);
		$GLOBALS['phpgw_setup']->oProc->query('UPDATE fm_request set budget = 0 WHERE budget IS NULL', __LINE__, __FILE__);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_workorder_budget',  array(
				'fd' => array(
				'order_id' => array('type' => 'int', 'precision' => 8, 'nullable' => False),
				'year' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'month' => array('type' => 'int', 'precision' => 2, 'nullable' => False, 'default' => 0),
				'budget' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'nullable' => True,
					'default' => '0.00'),
				'combined_cost' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
				),
			'pk' => array('order_id', 'year', 'month'),
				'fk' => array('fm_workorder' => array('order_id' => 'id')),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.661';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.661 to 0.9.17.662
	* Add year and active-flag to project_buffer_budget
	*/
	$test[] = '0.9.17.661';

	function property_upgrade0_9_17_661()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder_budget', 'active', array(
			'type'			=> 'int',
			'precision'		=> 2,
			'nullable'		=> true,
			'default'		=> 1
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder_budget', 'contract_sum', array(
			'type'		=> 'decimal',
			'precision' => '20',
			'scale'		=> '2',
			'nullable'	=> True,
			'default'	=> '0.00'
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query('UPDATE fm_workorder_budget SET active = 1', __LINE__, __FILE__);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.662';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.662 to 0.9.17.663
	* Add continuous-flag to workorder
	*/
	$test[] = '0.9.17.662';

	function property_upgrade0_9_17_662()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder', 'continuous', array(
			'type'		=> 'int',
			'precision'	=> 2,
			'nullable'	=> True
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.663';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.663 to 0.9.17.664
	* Alter id from varchar to ingeter
	*/
	$test[] = '0.9.17.663';

	function property_upgrade0_9_17_663()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");
		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_standard_unit");
		$units = array();
		$i = 1;

		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$name = $GLOBALS['phpgw_setup']->oProc->f('id');

			$units[$name] = array
			(
				'id'	=> $i,
				'name'	=> $name,
				'descr'	=> $GLOBALS['phpgw_setup']->oProc->f('descr')
			);
			$i++;
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_standard_unit');

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_standard_unit',  array(
				'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 20, 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		foreach($units as $_name => $unit)
		{
			$cols = implode(',', array_keys($unit));
			$values	= $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($unit));
			$sql = "INSERT INTO fm_standard_unit ({$cols}) VALUES ({$values})";
			$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		}

		$tables = array('fm_activities', 'fm_wo_hours', 'fm_template_hours', 'fm_s_agreement_detail');
		foreach($tables as $table)
		{
			$metadata = $GLOBALS['phpgw_setup']->oProc->m_odb->metadata($table);
			if(!isset($metadata['unit']))
			{
				continue;
			}

			$GLOBALS['phpgw_setup']->oProc->RenameColumn($table, 'unit', '_unit');
			$GLOBALS['phpgw_setup']->oProc->AddColumn($table, 'unit', array(
					'type'			=> 'int',
					'precision'		=> 4,
					'nullable'		=> true,
				)
			);

			reset($units);
			foreach($units as $_name => $unit)
			{
				$sql = "UPDATE {$table} SET unit = {$unit['id']} WHERE _unit = '{$_name}'";
				$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
			}
			$GLOBALS['phpgw_setup']->oProc->DropColumn($table, array(), '_unit');
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.664';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.664 to 0.9.17.665
	* Add bulk-flag to entities
	*/
	$test[] = '0.9.17.664';

	function property_upgrade0_9_17_664()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category', 'enable_bulk', array(
				'type' =>	'int',
				'precision' => 2,
				'nullable' => true
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.665';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.665 to 0.9.17.666
	* Add bulk-flag to entities
	*/
	$test[] = '0.9.17.665';

	function property_upgrade0_9_17_665()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_request', 'budget', 'amount_investment');

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'amount_investment', array(
				'type' =>	'int',
				'precision' => 4,
				'default' => '0',
				'nullable' => true
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_request', 'amount_operation', array(
				'type' =>	'int',
				'precision' => 4,
				'default' => '0',
				'nullable' => true
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_request', 'amount_potential_grants', array(
				'type' =>	'int',
				'precision' => 4,
				'default' => '0',
				'nullable' => true
			)
		);
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.666';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.666 to 0.9.17.667
	* Add recommended year
	*/
	$test[] = '0.9.17.666';

	function property_upgrade0_9_17_666()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_request', 'recommended_year', array(
				'type' =>	'int',
				'precision' => 4,
				'default' => '0',
				'nullable' => true
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_request', 'responsible_unit', array(
				'type' =>	'int',
				'precision' => 4,
				'nullable' => true
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_request_responsible_unit',  array(
				'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => False),
				'descr' => array('type' => 'text', 'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("SELECT DISTINCT fm_request.category, phpgw_categories.cat_name, phpgw_categories.cat_description FROM fm_request JOIN phpgw_categories ON fm_request.category = phpgw_categories.cat_id");
		$request_cats = array();

		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$request_cats[] = array
			(
				'id'				=> $GLOBALS['phpgw_setup']->oProc->f('category'),
				'cat_name'			=> $GLOBALS['phpgw_setup']->oProc->f('cat_name'),
				'cat_description'	=> $GLOBALS['phpgw_setup']->oProc->f('cat_description'),
			);
		}

		$location_id	= $GLOBALS['phpgw']->locations->get_id('property', '.project.request');

		$i = 1;
		foreach($request_cats as $old_cat)
		{
			$value_set = array
			(
				'cat_main'			=> 0,
				'cat_parent'		=> 0,
				'cat_level'			=> 0,
				'cat_owner'			=> -1,
				'cat_access' => 'public',
				'cat_appname'		=> 'property',
				'cat_name'			=> $old_cat['cat_name'],
				'cat_description'	=> $old_cat['cat_description'] ? $old_cat['cat_description'] : $old_cat['cat_name'],
				'last_mod'			=> time(),
				'location_id'		=> $location_id
			);
			$i++;

			$cols = implode(',', array_keys($value_set));
			$values	= $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($value_set));
			$sql = "INSERT INTO phpgw_categories ({$cols}) VALUES ({$values})";
			$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
			$cat_id = (int)$GLOBALS['phpgw_setup']->oProc->m_odb->get_last_insert_id('phpgw_categories', 'cat_id');
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET category = {$cat_id} WHERE category = {$old_cat['id']}");
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_categories SET cat_main= {$cat_id} WHERE cat_id={$cat_id}", __LINE__, __FILE__);
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.667';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.667 to 0.9.17.668
	* Add check for missing budgets
	*/
	$test[] = '0.9.17.667';

	function property_upgrade0_9_17_667()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");

		$sql = 'CREATE OR REPLACE VIEW fm_project_budget_year_from_order_view AS'
 			. ' SELECT DISTINCT fm_workorder.project_id, fm_workorder_budget.year'
 			. ' FROM fm_workorder_budget'
 			. ' JOIN fm_workorder ON fm_workorder.id = fm_workorder_budget.order_id'
 			. ' ORDER BY fm_workorder.project_id';

		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);


		$sql = 'CREATE OR REPLACE VIEW fm_project_budget_year_view AS'
 			. ' SELECT DISTINCT fm_project_budget.project_id, fm_project_budget.year'
 			. ' FROM fm_project_budget'
 			. ' ORDER BY fm_project_budget.project_id';

		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.668';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.668 to 0.9.17.669
	* Add check for missing budgets
	*/
	$test[] = '0.9.17.668';

	function property_upgrade0_9_17_668()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_eco_period_transition', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'month' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'day' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'hour' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'remark' => array('type' => 'varchar', 'precision' => '60', 'nullable' => true),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
				),
				'pk' => array('id'),
				'ix' => array(),
				'fk' => array(),
				'uc' => array('month')
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_document', 'descr', array(
			'type'		=> 'text',
			'nullable'	=> True
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.669';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.669 to 0.9.17.670
	* Add history for tenant claim
	*/
	$test[] = '0.9.17.669';

	function property_upgrade0_9_17_669()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_tenant_claim_history', array(
				'fd' => array(
				'history_id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'history_record_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_appname' => array('type' => 'varchar', 'precision' => '64', 'nullable' => False),
				'history_owner' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_status' => array('type' => 'char', 'precision' => '2', 'nullable' => False),
				'history_new_value' => array('type' => 'text', 'nullable' => False),
				'history_old_value' => array('type' => 'text', 'nullable' => true),
				'history_timestamp' => array('type' => 'timestamp', 'nullable' => False, 'default' => 'current_timestamp')
				),
				'pk' => array('history_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.670';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.670 to 0.9.17.671
	* Convert p_num values to integer
	*/
	$test[] = '0.9.17.670';

	function property_upgrade0_9_17_670()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_entity_category");

		$categories = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			if($prefix = $GLOBALS['phpgw_setup']->oProc->f('prefix'))
			{
				$categories[] = array
				(
					'prefix'	=> $prefix,
					'entity_id'	=> $GLOBALS['phpgw_setup']->oProc->f('entity_id'),
					'cat_id'	=> $GLOBALS['phpgw_setup']->oProc->f('id')
				);
			}
		}

		$tables = $GLOBALS['phpgw_setup']->oProc->m_odb->table_names();

		foreach($tables as $table)
		{
			if(preg_match('/^fm_/', $table))
			{
				$metadata = $GLOBALS['phpgw_setup']->oProc->m_odb->metadata($table);

				$primary_keys = array();
				foreach($metadata as $key => $info)
				{
					if(isset($info->primary_key) && $info->primary_key)
					{
						$primary_keys[] = $key;
					}
				}

				if(isset($metadata['p_num']))
				{

					foreach($categories as $category)
					{

						$cols = array_merge($primary_keys, array('p_num'));
						$records = array();
						$i = 0;
						$GLOBALS['phpgw_setup']->oProc->query("SELECT " . implode(',', $cols) . " FROM {$table} WHERE p_entity_id = '{$category[entity_id]}' AND p_cat_id = '{$category[cat_id]}'");

						while($GLOBALS['phpgw_setup']->oProc->next_record())
						{
							foreach($cols as $col)
							{
								$records[$i][$col] = $GLOBALS['phpgw_setup']->oProc->f($col);
							}
							$i++;
						}

						foreach($records as $record)
						{
							$p_num = (int)ltrim($record['p_num'], $category['prefix']);
							$condition_arr = array();
							foreach($primary_keys as $primary_key)
							{
								$condition_arr[] = "{$primary_key} = '{$record[$primary_key]}'";
							}

							$sql = "UPDATE {$table} SET p_num = '{$p_num}' WHERE " . implode(' AND ', $condition_arr);

							$GLOBALS['phpgw_setup']->oProc->query($sql);
						}
					}

					$sql = "UPDATE {$table} SET p_num = p_num::integer WHERE p_num IS NOT NULL";
					$GLOBALS['phpgw_setup']->oProc->query($sql);
				}
			}
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.671';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.671 to 0.9.17.672
	* Add external voucher_id for integration with external accounting_system
	*/
	$test[] = '0.9.17.671';

	function property_upgrade0_9_17_671()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilag', 'external_voucher_id', array(
			'type' => 'int', 'precision' => '8', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf', 'external_voucher_id', array(
			'type' => 'int', 'precision' => '8', 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.672';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.672 to 0.9.17.673
	* Add configurable prioriy keys for tickets
	*/
	$test[] = '0.9.17.672';

	function property_upgrade0_9_17_672()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_tts_priority', array(
				'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 100, 'nullable' => true),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("SELECT config_value FROM phpgw_config WHERE config_app = 'property' AND config_name = 'prioritylevels'");
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$prioritylevels = $GLOBALS['phpgw_setup']->oProc->f('config_value');

		$prioritylevels = $prioritylevels ? $prioritylevels : 3;

		$priority_comment = array();
		$priority_comment[$prioritylevels]	= " - Lowest";
		$priority_comment[1]				= " - Highest";

		for($i = 1; $i <= $prioritylevels; $i++)
		{
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tts_priority (id, name) VALUES ({$i}, '{$i}{$priority_comment[$i]}')");
		}

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_config WHERE config_app = 'property' AND config_name = 'prioritylevels'");

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.673';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.673 to 0.9.17.674
	* Add multiplier to condition survey
	*/
	$test[] = '0.9.17.673';

	function property_upgrade0_9_17_673()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_condition_survey', 'multiplier', array
				(
					'type'		=> 'decimal',
					'precision' => '20',
					'scale'		=> '2',
					'default'	=> '1.00',
					'nullable'	=> True
				)
			);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.674';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.674 to 0.9.17.675
	* Add multiplier to condition survey
	*/
	$test[] = '0.9.17.674';

	function property_upgrade0_9_17_674()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_request', 'multiplier', array
				(
					'type'		=> 'decimal',
					'precision' => '20',
					'scale'		=> '2',
					'default'	=> '1.00',
					'nullable'	=> True
				)
			);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.675';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.675 to 0.9.17.676
	* Add multiplier to condition survey
	*/
	$test[] = '0.9.17.675';

	function property_upgrade0_9_17_675()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("ALTER TABLE fm_budget DROP CONSTRAINT fm_budget_year_b_account_id_district_id_revision_key");
		$GLOBALS['phpgw_setup']->oProc->query("ALTER TABLE fm_budget ADD CONSTRAINT fm_budget_year_key UNIQUE(year , b_account_id , district_id , revision, ecodimb ,category)");

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_department', 'name', array('type' => 'varchar',
			'precision' => '200', 'nullable' => False));


		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.676';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.676 to 0.9.17.677
	* Add fields to view
	*/
	$test[] = '0.9.17.676';

	function property_upgrade0_9_17_676()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");

		$sql = 'CREATE OR REPLACE VIEW fm_orders_paid_or_pending_view AS
		 SELECT orders_paid_or_pending.order_id, orders_paid_or_pending.periode,orders_paid_or_pending.amount,orders_paid_or_pending.periodization, orders_paid_or_pending.periodization_start
		   FROM ( SELECT fm_ecobilagoverf.pmwrkord_code AS order_id, fm_ecobilagoverf.periode, sum(fm_ecobilagoverf.godkjentbelop) AS amount, fm_ecobilagoverf.periodization, fm_ecobilagoverf.periodization_start
			       FROM fm_ecobilagoverf
			       GROUP BY fm_ecobilagoverf.pmwrkord_code, fm_ecobilagoverf.periode, fm_ecobilagoverf.periodization, fm_ecobilagoverf.periodization_start
				UNION ALL
			      SELECT fm_ecobilag.pmwrkord_code AS order_id, fm_ecobilag.periode, sum(fm_ecobilag.godkjentbelop) AS amount, fm_ecobilag.periodization, fm_ecobilag.periodization_start
			       FROM fm_ecobilag
			       GROUP BY fm_ecobilag.pmwrkord_code, fm_ecobilag.periode, fm_ecobilag.periodization, fm_ecobilag.periodization_start) orders_paid_or_pending ORDER BY orders_paid_or_pending.periode, orders_paid_or_pending.order_id';

		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.677';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.677 to 0.9.17.678
	* Add fields to view
	*/
	$test[] = '0.9.17.677';

	function property_upgrade0_9_17_677()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder', 'fictive_periodization', array(
			'type'		=> 'int',
			'precision'	=> 2,
			'nullable'	=> True
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.678';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.678 to 0.9.17.679
	* Add department-flag to entities
	*/
	$test[] = '0.9.17.678';

	function property_upgrade0_9_17_678()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category', 'department', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.679';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.679 to 0.9.17.680
	* Add department_id to entity tables
	*/
	$test[] = '0.9.17.679';

	function property_upgrade0_9_17_679()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_entity_category");

		$categories = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$categories[] = array
			(
				'entity_id'	=> $GLOBALS['phpgw_setup']->oProc->f('entity_id'),
				'cat_id'	=> $GLOBALS['phpgw_setup']->oProc->f('id')
			);
		}

		$tables = $GLOBALS['phpgw_setup']->oProc->m_odb->table_names();

		foreach($categories as $category)
		{
			$table = "fm_entity_{$category['entity_id']}_{$category['cat_id']}";
			if(in_array($table, $tables))
			{
				$metadata = $GLOBALS['phpgw_setup']->oProc->m_odb->metadata($table);
				if(!isset($metadata['department_id']))
				{
					$GLOBALS['phpgw_setup']->oProc->AddColumn($table, 'department_id', array('type' => 'int',
						'precision' => 4, 'nullable' => True));
				}
			}
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.680';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.680 to 0.9.17.681
	* Change name from department to org_unit
	*/
	$test[] = '0.9.17.680';

	function property_upgrade0_9_17_680()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");

		$GLOBALS['phpgw_setup']->oProc->RenameTable('fm_department', 'fm_org_unit');
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_entity_category', 'department', 'org_unit');
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_ecodimb', 'department', 'org_unit_id');

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_entity_category");

		$categories = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$categories[] = array
			(
				'entity_id'	=> $GLOBALS['phpgw_setup']->oProc->f('entity_id'),
				'cat_id'	=> $GLOBALS['phpgw_setup']->oProc->f('id')
			);
		}

		$tables = $GLOBALS['phpgw_setup']->oProc->m_odb->table_names();

		foreach($categories as $category)
		{
			$table = "fm_entity_{$category['entity_id']}_{$category['cat_id']}";
			if(in_array($table, $tables))
			{
				$metadata = $GLOBALS['phpgw_setup']->oProc->m_odb->metadata($table);
				if(isset($metadata['department_id']))
				{
					$GLOBALS['phpgw_setup']->oProc->RenameColumn($table, 'department_id', 'org_unit_id');
				}
			}
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.681';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.681 to 0.9.17.682
	* Add one-to-many relation on documents
	*/
	$test[] = '0.9.17.681';

	function property_upgrade0_9_17_681()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_document_relation', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'document_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'location_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'location_item_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				),
				'pk' => array('id'),
				'fk' => array('fm_document' => array('document_id' => 'id')),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.682';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.682 to 0.9.17.683
	* Add actual cost year to tickets
	*/
	$test[] = '0.9.17.682';

	function property_upgrade0_9_17_682()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'actual_cost_year', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.683';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.683 to 0.9.17.684
	* Add tender related dates to workorder
	*/
	$test[] = '0.9.17.683';

	function property_upgrade0_9_17_683()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder', 'tender_deadline', array(
			'type' => 'int', 'precision' => 8, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder', 'tender_received', array(
			'type' => 'int', 'precision' => 8, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder', 'inspection_on_completion', array(
			'type' => 'int', 'precision' => 8, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.684';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.684 to 0.9.17.685
	* Add tender related dates to workorder
	*/
	$test[] = '0.9.17.684';

	function property_upgrade0_9_17_684()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_tts_payments', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'ticket_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'amount' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'default' => '0',
					'nullable' => false),
				'period' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'remark' => array('type' => 'text', 'nullable' => true),
				'created_on' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				),
				'pk' => array('id'),
				'ix' => array(),
				'fk' => array('fm_tts_tickets' => array('ticket_id' => 'id')),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("SELECT id, actual_cost, modified_date FROM fm_tts_tickets WHERE actual_cost != '0.00' ORDER BY modified_date");

		$tickets = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$tickets[] = array
			(
				'id'				=> $GLOBALS['phpgw_setup']->oProc->f('id'),
				'actual_cost'		=> $GLOBALS['phpgw_setup']->oProc->f('actual_cost'),
				'modified_date'		=> $GLOBALS['phpgw_setup']->oProc->f('modified_date')
			);
		}

		foreach($tickets as $ticket)
		{
			$period = date('Ym', $ticket['modified_date']);
			$value_set = array
			(
				'ticket_id'	=> $ticket['id'],
				'amount'	=> $ticket['actual_cost'],
				'period'	=> $period,
				'created_on' => $ticket['modified_date']
			);
			$cols = implode(',', array_keys($value_set));
			$values	= $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($value_set));
			$sql = "INSERT INTO fm_tts_payments ({$cols}) VALUES ({$values})";
			$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.685';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.685 to 0.9.17.686
	* Convert id from character to integer for fm_vendor_category::id
	*/
	$test[] = '0.9.17.685';

	function property_upgrade0_9_17_685()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$metadata = $GLOBALS['phpgw_setup']->oProc->m_odb->metadata('fm_vendor_category');

		if($metadata['id']->type == 'varchar')
		{
			$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_vendor_category");
			$cats = array();
			while($GLOBALS['phpgw_setup']->oProc->next_record())
			{
				$cats[] = array
				(
					'id'		=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'descr' => $GLOBALS['phpgw_setup']->oProc->f('descr', true),
				);
			}

			$GLOBALS['phpgw_setup']->oProc->DropTable('fm_vendor_category');
			$GLOBALS['phpgw_setup']->oProc->CreateTable(
				'fm_vendor_category', array(
					'fd' => array(
					'id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'descr' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
					),
					'pk' => array('id'),
					'fk' => array(),
					'ix' => array(),
					'uc' => array()
				)
			);
			$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_vendor', 'category', '_category');

			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_vendor', 'category', array('type' => 'int',
				'precision' => 4, 'nullable' => True));

			$id = 1;
			foreach($cats as $cat)
			{
				$value_set = array
				(
					'id'	=> $id,
					'descr'	=> $cat['descr']
				);
				$cols = implode(',', array_keys($value_set));
				$values	= $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($value_set));
				$sql = "INSERT INTO fm_vendor_category ({$cols}) VALUES ({$values})";
				$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);

				$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_vendor SET category = {$id} WHERE _category = '{$cat['id']}'", __LINE__, __FILE__);

				$id++;
			}
			$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_vendor', array(), '_category');
		}
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.686';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.686 to 0.9.17.687
	* Convert ns3420 table
	*/
	$test[] = '0.9.17.686';

	function property_upgrade0_9_17_686()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_ns3420 ORDER BY id");

		$ns3420 = array();
		$id = 1;

		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$ns3420[] = array
			(
				'id' => $id,
				'num' => $GLOBALS['phpgw_setup']->oProc->f('id'),
				'enhet' => $GLOBALS['phpgw_setup']->oProc->f('enhet'),
				'tekst1' => $GLOBALS['phpgw_setup']->oProc->f('tekst1'),
				'tekst2' => $GLOBALS['phpgw_setup']->oProc->f('tekst2'),
				'tekst3' => $GLOBALS['phpgw_setup']->oProc->f('tekst3'),
				'tekst4' => $GLOBALS['phpgw_setup']->oProc->f('tekst4'),
				'tekst5' => $GLOBALS['phpgw_setup']->oProc->f('tekst5'),
				'tekst6' => $GLOBALS['phpgw_setup']->oProc->f('tekst6'),
			);
			$id ++;
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_ns3420');

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_ns3420', array(
				'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'num' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'parent_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'enhet' => array('type' => 'varchar', 'precision' => '6', 'nullable' => True),
				'tekst1' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'tekst2' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'tekst3' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'tekst4' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'tekst5' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'tekst6' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'type' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('num')
			)
		);

		foreach($ns3420 as $value_set)
		{
			$cols = implode(',', array_keys($value_set));
			$values	= $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($value_set));
			$sql = "INSERT INTO fm_ns3420 ({$cols}) VALUES ({$values})";
			$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		}


		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.687';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.687 to 0.9.17.688
	* Add controller-flag to entities
	*/
	$test[] = '0.9.17.687';

	function property_upgrade0_9_17_687()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category', 'enable_controller', array(
				'type' =>	'int',
				'precision' => 2,
				'nullable' => true
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.688';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.688 to 0.9.17.689
	* Add generic history
	*/
	$test[] = '0.9.17.688';

	function property_upgrade0_9_17_688()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_generic_history', array(
				'fd' => array(
				'history_id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'history_record_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_owner' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_status' => array('type' => 'char', 'precision' => '2', 'nullable' => False),
				'history_new_value' => array('type' => 'text', 'nullable' => False),
				'history_old_value' => array('type' => 'text', 'nullable' => true),
				'history_timestamp' => array('type' => 'timestamp', 'nullable' => False, 'default' => 'current_timestamp'),
				'history_attrib_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'location_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'app_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				),
				'pk' => array('history_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.689';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.689 to 0.9.17.690
	* Add generic history
	*/
	$test[] = '0.9.17.689';

	function property_upgrade0_9_17_689()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_entity_group', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 100, 'nullable' => False),
				'descr' => array('type' => 'text', 'nullable' => true),
				'active' => array('type' => 'int', 'precision' => '2', 'nullable' => True, 'default' => 0),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => False)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category', 'entity_group_id', array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => True
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_locations', 'name', array(
			'type'		=> 'text',
			'nullable'	=> true
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("SELECT location_code FROM fm_locations");

		$locations = array();

		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$locations[] = $GLOBALS['phpgw_setup']->oProc->f('location_code');
		}

		foreach($locations as $location_code)
		{
			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code,
				'extra' => array('noattrib' => true)));

			$location_arr = explode('-', $location_code);
			$loc_name_arr = array();
			$i = 1;
			foreach($location_arr as $_part)
			{
				$loc_name_arr[] = $location_array["loc{$i}_name"];
				$i++;
			}
			$name	= $GLOBALS['phpgw_setup']->oProc->m_odb->db_addslashes(implode(', ', $loc_name_arr));
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_locations SET name = '{$name}' WHERE location_code = '{$location_code}'");
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.690';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.690 to 0.9.17.691
	* Add modifyinfo to location-register.
	*/
	$test[] = '0.9.17.690';

	function property_upgrade0_9_17_690()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$attributes = array(
			array(
				'column_name'	=> 'modified_by',
				'type'			=> 'user',
				'precision'		=> 4,
				'nullable'		=> 'true',
				'input_text'	=> 'modified_by',
				'statustext'	=> 'modified_by'
			),
			array(
				'column_name'	=> 'modified_on',
				'type'			=> 'DT',
				'precision'		=> 8,
				'nullable'		=> 'true',
				'input_text'	=> 'modified_on',
				'statustext'	=> 'modified_on'
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query('SELECT count(*) as cnt FROM fm_location_type');
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$levels = $GLOBALS['phpgw_setup']->oProc->f('cnt');

		for($i = 1; $i < ($levels + 1); $i++)
		{

			$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_location{$i}", 'modified_by', array(
				'type' => 'int',
				'precision' => 4,
				'nullable' => True
				)
			);
			$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_location{$i}", 'modified_on', array(
				'type' => 'timestamp',
				'nullable' => True,
				'default' => 'current_timestamp'
				)
			);
			$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_location{$i}_history", 'modified_by', array(
				'type' => 'int',
				'precision' => 4,
				'nullable' => True
				)
			);
			$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_location{$i}_history", 'modified_on', array(
				'type' => 'timestamp',
				'nullable' => True,
				'default' => 'current_timestamp'
				)
			);

			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location{$i} SET modified_on = NULL");
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location{$i}_history SET modified_on = NULL");

			$location_id = $GLOBALS['phpgw']->locations->get_id('property', ".location.{$i}");
			$GLOBALS['phpgw_setup']->oProc->query("SELECT max(id) as id FROM phpgw_cust_attribute WHERE location_id = {$location_id}");
			$GLOBALS['phpgw_setup']->oProc->next_record();
			$id = $GLOBALS['phpgw_setup']->oProc->f('id');

			foreach($attributes as $attrib)
			{
				$id ++;

				$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id,column_name,datatype,precision_,input_text,statustext,nullable,custom)"
					. " VALUES ("
				. $location_id . ','
					. $id . ",'"
					. $attrib['column_name'] . "','"
					. $attrib['type'] . "',"
					. $attrib['precision'] . ",'"
					. $attrib['input_text'] . "','"
					. $attrib['statustext'] . "','"
					. $attrib['nullable'] . "',NULL)");
			}
		}

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_cust_attribute SET input_text = column_name WHERE input_text = 'dummy'");

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.691';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.691 to 0.9.17.692
	* Alter field name.
	*/
	$test[] = '0.9.17.691';

	function property_upgrade0_9_17_691()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$MetaForeignKeys = $GLOBALS['phpgw_setup']->oProc->m_odb->MetaForeignKeys('fm_responsibility_contact');

		if(isset($MetaForeignKeys['fm_responsibility']) && $MetaForeignKeys['fm_responsibility'])
		{
			$GLOBALS['phpgw_setup']->oProc->query("ALTER TABLE fm_responsibility_contact DROP CONSTRAINT fm_responsibility_contact_responsibility_id_fkey");
		}

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_responsibility_contact', 'responsibility_id', 'responsibility_role_id');

		$GLOBALS['phpgw_setup']->oProc->query("ALTER TABLE fm_responsibility_contact"
		. " ADD CONSTRAINT fm_responsibility_contact_responsibility_role_id_fkey"
		. " FOREIGN KEY (responsibility_role_id)"
		. " REFERENCES fm_responsibility_role (id) MATCH SIMPLE"
		. " ON UPDATE NO ACTION ON DELETE NO ACTION"
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.692';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.692 to 0.9.17.693
	* Alter field name.
	*/
	$test[] = '0.9.17.692';

	function property_upgrade0_9_17_692()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");

		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_responsibility_contact', array(), 'ecodimb');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.693';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.693 to 0.9.17.694
	* Add modified info.
	*/
	$test[] = '0.9.17.693';

	function property_upgrade0_9_17_693()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_entity_category");

		$categories = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$categories[] = array
			(
				'entity_id'	=> $GLOBALS['phpgw_setup']->oProc->f('entity_id'),
				'cat_id'	=> $GLOBALS['phpgw_setup']->oProc->f('id')
			);
		}

		$tables = $GLOBALS['phpgw_setup']->oProc->m_odb->table_names();

		foreach($categories as $category)
		{
			$table = "fm_entity_{$category['entity_id']}_{$category['cat_id']}";
			if(in_array($table, $tables))
			{
				$GLOBALS['phpgw_setup']->oProc->AddColumn($table, 'modified_by', array('type' => 'int',
					'precision' => 4, 'nullable' => true));
				$GLOBALS['phpgw_setup']->oProc->AddColumn($table, 'modified_on', array('type' => 'int',
					'precision' => 8, 'nullable' => true));
			}
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.694';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.694 to 0.9.17.695
	* differentiate budget.
	*/
	$test[] = '0.9.17.694';

	function property_upgrade0_9_17_694()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_tts_budget', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'ticket_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'amount' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'default' => '0',
					'nullable' => false),
				'period' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'remark' => array('type' => 'text', 'nullable' => true),
				'created_on' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				),
				'pk' => array('id'),
				'ix' => array(),
				'fk' => array('fm_tts_tickets' => array('ticket_id' => 'id')),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("SELECT id, budget, entry_date FROM fm_tts_tickets WHERE budget != 0  ORDER BY entry_date");

		$tickets = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$tickets[] = array
			(
				'id'				=> $GLOBALS['phpgw_setup']->oProc->f('id'),
				'budget'		=> $GLOBALS['phpgw_setup']->oProc->f('budget'),
				'entry_date'		=> $GLOBALS['phpgw_setup']->oProc->f('entry_date')
			);
		}

		foreach($tickets as $ticket)
		{
			$period = date('Ym', $ticket['entry_date']);
			$value_set = array
			(
				'ticket_id'	=> $ticket['id'],
				'amount'	=> $ticket['budget'],
				'period'	=> $period,
				'created_on' => $ticket['entry_date']
			);
			$cols = implode(',', array_keys($value_set));
			$values	= $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($value_set));
			$sql = "INSERT INTO fm_tts_budget ({$cols}) VALUES ({$values})";
			$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.695';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.695 to 0.9.17.696
	* Alter name of part of town id.
	*/
	$test[] = '0.9.17.695';

	function property_upgrade0_9_17_695()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_config SET reference_id = 'id' WHERE column_name = 'part_of_town_id'");

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_part_of_town', 'part_of_town_id', 'id');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.696';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.696 to 0.9.17.697
	* Add parametres for integration with e-commerse platforms
	*/
	$test[] = '0.9.17.696';

	function property_upgrade0_9_17_696()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");

		$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_tts_tickets", 'contract_id', array(
			'type' => 'varchar',
			'precision' => 30,
			'nullable' => True
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_tts_tickets", 'service_id', array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => True
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_eco_service', array(
				'fd' => array(
					'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
					'name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
					'active' => array('type' => 'int', 'precision' => '2', 'nullable' => True, 'default' => 1),
				),
				'pk' => array('id'),
				'ix' => array(),
				'fk' => array(),
				'uc' => array()
			)
		);


		$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_tts_tickets", 'tax_code', array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => True
			)
		);

		$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.ticket.order');

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_locations SET allow_c_attrib = 1, c_attrib_table = 'fm_tts_tickets' WHERE location_id = {$location_id}");

		$metadata = $GLOBALS['phpgw_setup']->oProc->m_odb->metadata('fm_tts_tickets');
		if(isset($metadata['agresso_prosjekt']))
		{
			$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.ticket');
			$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_tts_tickets', 'agresso_prosjekt', 'external_project_id');
			$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_cust_attribute WHERE location_id = {$location_id} AND column_name ='agresso_prosjekt'");
		}
		else
		{
			$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_tts_tickets", 'external_project_id', array(
				'type' => 'varchar',
				'precision' => 10,
				'nullable' => True
				)
			);
		}

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_external_project', array(
				'fd' => array(
					'id' => array('type' => 'varchar', 'precision' => '10', 'nullable' => False),
					'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False),
					'budget' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_unspsc_code', array(
				'fd' => array(
					'id' => array('type' => 'varchar', 'precision' => '15', 'nullable' => False),
					'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_tts_tickets", 'unspsc_code', array(
			'type' => 'varchar',
			'precision' => 15,
			'nullable' => True
			)
		);

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_b_account_category', 'project_group', 'external_project');
		$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_project", 'external_project_id', array(
				'type' => 'varchar',
				'precision' => 10,
				'nullable' => True
				)
			);

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_project SET external_project_id = project_group WHERE project_group IS NOT NULL");

		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_project', array(), 'project_group');

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM  fm_project_group");

		$external_projects = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$external_projects[] = array
			(
				'id'		=> $GLOBALS['phpgw_setup']->oProc->f('id'),
				'name'		=> $GLOBALS['phpgw_setup']->oProc->f('descr'),
				'budget'	=> $GLOBALS['phpgw_setup']->oProc->f('budget')
			);
		}

		foreach($external_projects as $external_project)
		{
			$cols = implode(',', array_keys($external_project));
			$values	= $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($external_project));
			$sql = "INSERT INTO fm_external_project ({$cols}) VALUES ({$values})";
			$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_project_group');

		$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_workorder", 'contract_id', array(
			'type' => 'varchar',
			'precision' => 30,
			'nullable' => True
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_agreement", 'contract_id', array(
			'type' => 'varchar',
			'precision' => 30,
			'nullable' => True
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_workorder", 'service_id', array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => True
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_workorder", 'unspsc_code', array(
			'type' => 'varchar',
			'precision' => 15,
			'nullable' => True
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_workorder", 'tax_code', array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => True
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.697';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.697 to 0.9.17.698
	* Add parametres for integration with e-commerse platforms
	*/
	$test[] = '0.9.17.697';

	function property_upgrade0_9_17_697()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw']->locations->add('.project.workorder.transfer', 'Transfer Workorder', 'property', $allow_grant = null, $custom_tbl = null, $c_function = true);
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.698';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.698 to 0.9.17.699
	* Add parametres for integration with e-commerse platforms
	*/
	$test[] = '0.9.17.698';

	function property_upgrade0_9_17_698()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");

		$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_workorder", 'building_part', array(
			'type' => 'varchar',
			'precision' => 4,
			'nullable' => True
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_workorder", 'order_dim1', array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => True
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_workorder", 'order_sent', array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => True
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_workorder", 'order_received', array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => True
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_tts_tickets", 'order_sent', array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => True
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_tts_tickets", 'order_received', array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => True
			)
		);


		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.699';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.699 to 0.9.17.700
	* Add parametres for integration with e-commerse platforms
	*/
	$test[] = '0.9.17.699';

	function property_upgrade0_9_17_699()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_workorder", 'order_received_percent', array(
			'type' => 'int',
			'precision' => 2,
			'nullable' => True
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_tts_tickets", 'order_received_percent', array(
			'type' => 'int',
			'precision' => 2,
			'nullable' => True
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.700';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.700 to 0.9.17.701
	*
	*/
	$test[] = '0.9.17.700';

	function property_upgrade0_9_17_700()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_external_project", 'active', array(
			'type' => 'int',
			'precision' => 2,
			'nullable' => True,
			'default' => 1
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.701';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.701 to 0.9.17.702
	*
	*/
	$test[] = '0.9.17.701';

	function property_upgrade0_9_17_701()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");

		$GLOBALS['phpgw_setup']->oProc->query("SELECT count(*) as cnt FROM fm_location_type");
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$locations = $GLOBALS['phpgw_setup']->oProc->f('cnt');

		for($location_type = 1; $location_type < ($locations + 1); $location_type++)
		{

			$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_location{$location_type}", 'id', array(
				'type' => 'int',
				'precision' => 4,
				'nullable' => true,
				)
			);

			$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_location{$location_type}_history", 'id', array(
				'type' => 'int',
				'precision' => 4,
				'nullable' => true,
				)
			);

			$location_id = $GLOBALS['phpgw']->locations->get_id('property', ".location.{$location_type}");

			$GLOBALS['phpgw_setup']->oProc->query("SELECT max(id) as id FROM phpgw_cust_attribute WHERE location_id = {$location_id}");
			$GLOBALS['phpgw_setup']->oProc->next_record();
			$attrib_id = $GLOBALS['phpgw_setup']->oProc->f('id') +1;

			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id,column_name,datatype,precision_,input_text,statustext,nullable,custom)"
			. " VALUES ( {$location_id}, {$attrib_id}, 'id', 'I', 4, 'id', 'id', 'true', NULL)");

		}

		execMethod('property.solocation.update_location');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.702';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.702 to 0.9.17.703
	*
	*/
	$test[] = '0.9.17.702';

	function property_upgrade0_9_17_702()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw']->locations->add('.org_unit', 'Org unit', 'property', false, 'fm_org_unit', false, true);

		$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_org_unit", 'active', array(
			'type' => 'int',
			'precision' => 2,
			'nullable' => True,
			'default' => 1
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.703';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.703 to 0.9.17.704
	*
	*/
	$test[] = '0.9.17.703';

	function property_upgrade0_9_17_703()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_project", 'b_account_id', array(
			'type' => 'varchar',
			'precision' => 20,
			'nullable' => True
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("SELECT DISTINCT account_id, project_id FROM fm_workorder WHERE account_id IS NOT NULL AND account_id != '0'");
		$projects = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$projects[] = array(
				'id' => $GLOBALS['phpgw_setup']->oProc->f('project_id'),
				'b_account_id' => $GLOBALS['phpgw_setup']->oProc->f('account_id'),
			);
		}

		foreach ($projects as $project)
		{
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_project SET b_account_id = '{$project['b_account_id']}' WHERE id = {$project['id']}");
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.704';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.704 to 0.9.17.705
	*
	*/
	$test[] = '0.9.17.704';

	function property_upgrade0_9_17_704()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_workorder', array(), 'order_received_percent');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_tts_tickets', array(), 'order_received_percent');


		$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_workorder", 'order_received_amount', array(
			'type' => 'decimal',
			'precision' => '20',
			'scale' => '2',
			'nullable' => True,
			'default' => '0.00'
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_tts_tickets", 'order_received_amount', array(
			'type' => 'decimal',
			'precision' => '20',
			'scale' => '2',
			'nullable' => True,
			'default' => '0.00'
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.705';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.705 to 0.9.17.706
	*
	*/
	$test[] = '0.9.17.705';

	function property_upgrade0_9_17_705()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("SELECT DISTINCT order_id FROM fm_tts_tickets WHERE order_id IS NOT NULL");
		$orders = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$orders[] = $GLOBALS['phpgw_setup']->oProc->f('order_id');
		}

		foreach ($orders as $order_id)
		{
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_orders (id,type) VALUES ({$order_id},'ticket')");
		}

		$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_tts_tickets", 'ordered_by', array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => True
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_tts_tickets SET ordered_by = assignedto WHERE order_id IS NOT NULL");

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.706';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.706 to 0.9.17.707
	*
	*/
	$test[] = '0.9.17.706';

	function property_upgrade0_9_17_706()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_ecodimb', 'id', array('type' => 'int', 'precision' => '4', 'nullable' => False));

		$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_ecodimb", 'active', array(
			'type' => 'int',
			'precision' => 2,
			'nullable' => True,
			'default' => 1
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_eco_service', 'name', array('type' => 'varchar', 'precision' => '255', 'nullable' => False));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.707';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.707 to 0.9.17.708
	*
	*/
	$test[] = '0.9.17.707';

	function property_upgrade0_9_17_707()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();


		$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_vendor", 'active', array(
			'type' => 'int',
			'precision' => 2,
			'nullable' => True,
			'default' => 1
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_vendor', 'entry_date',
				array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp')
			);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.708';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.708 to 0.9.17.709
	*
	*/
	$test[] = '0.9.17.708';

	function property_upgrade0_9_17_708()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'mail_recipients', array(
			'type' => 'varchar',
			'precision' => 255,
			'nullable' => True
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'file_attachments', array(
			'type' => 'varchar',
			'precision' => 255,
			'nullable' => True
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.709';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.709 to 0.9.17.710
	*
	*/
	$test[] = '0.9.17.709';

	function property_upgrade0_9_17_709()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder_status', 'canceled', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.710';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.710 to 0.9.17.711
	*
	*/
	$test[] = '0.9.17.710';

	function property_upgrade0_9_17_710()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw']->locations->add('.report', 'Generic report', 'property', $allow_grant = true);

		$metadata = $GLOBALS['phpgw_setup']->oProc->m_odb->metadata('fm_view_dataset');

		if(!$metadata)
		{
			$GLOBALS['phpgw_setup']->oProc->CreateTable(
				'fm_view_dataset', array(
					'fd' => array(
						'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
						'view_name' => array('type' => 'varchar', 'precision' => 100, 'nullable' => False),
						'dataset_name' => array('type' => 'varchar', 'precision' => 100, 'nullable' => False),
						'owner_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
						'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
					),
					'pk' => array('id'),
					'fk' => array(),
					'ix' => array(),
					'uc' => array()
				)
			);

			$GLOBALS['phpgw_setup']->oProc->CreateTable(
				'fm_view_dataset_report', array(
					'fd' => array(
						'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
						'dataset_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
						'report_name' => array('type' => 'varchar', 'precision' => 100, 'nullable' => False),
						'report_definition' => array('type' => 'jsonb', 'nullable' => true),
						'owner_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
						'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
					),
					'pk' => array('id'),
					'fk' => array('fm_view_dataset' => array('dataset_id' => 'id')),
					'ix' => array(),
					'uc' => array()
				)
			);
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.711';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.711 to 0.9.17.712
	*
	*/
	$test[] = '0.9.17.711';

	function property_upgrade0_9_17_711()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw']->locations->add('.report', 'Generic report', 'property', $allow_grant = true);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_ecodimb_role_user_substitute', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
					'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
					'substitute_user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				),
				'pk' => array('id'),
				'ix' => array(),
				'fk' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.712';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.712 to 0.9.17.713
	*
	*/
	$test[] = '0.9.17.712';

	function property_upgrade0_9_17_712()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_part_of_town', 'name', array('type' => 'varchar', 'precision' => '150', 'nullable' => false));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.713';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.713 to 0.9.17.714
	*
	*/
	$test[] = '0.9.17.713';

	function property_upgrade0_9_17_713()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query('DELETE FROM fm_cache');

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_district', 'delivery_address', array(
			'type' => 'text', 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_part_of_town', 'delivery_address', array(
			'type' => 'text', 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location1', 'delivery_address', array(
			'type' => 'text', 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location1_history', 'delivery_address', array(
			'type' => 'text', 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project', 'delivery_address', array(
			'type' => 'text', 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder', 'delivery_address', array(
			'type' => 'text', 'nullable' => True));


		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'delivery_address', array(
			'type' => 'text', 'nullable' => True));


		$cust = array
		(
			'datatype'		=> 'T',
			'precision_'	=> '',
			'scale'			=> '',
			'default_value'	=> '',
			'nullable'		=> 'True',
			'custom'		=> 1
		);

		$cust_fields = array();

		$cust_fields[] = array
		(
			'name' => 'delivery_address',
			'descr' => 'delivery address',
			'statustext' => 'delivery address',
			'cust'	=> $cust
		);

		$db = & $GLOBALS['phpgw_setup']->oProc->m_odb;

		foreach($cust_fields as & $field)
		{

			$field['cust']['location_id'] = $GLOBALS['phpgw']->locations->get_id('property', ".location.1");
			$db->query("SELECT max(id) as id FROM phpgw_cust_attribute WHERE location_id = {$field['cust']['location_id']}");
			$db->next_record();
			$id = (int)$db->f('id');
			$db->query("SELECT max(attrib_sort) as attrib_sort FROM phpgw_cust_attribute WHERE group_id = 0 AND location_id = {$field['cust']['location_id']}");
			$db->next_record();

			$field['cust']['id']			= $id + 1;
			$field['cust']['attrib_sort']	= (int)$db->f('attrib_sort') + 1;
			$field['cust']['column_name']	= $field['name'];
			$field['cust']['input_text']	= $field['descr'];
			$field['cust']['statustext']	= $field['statustext'];

			$sql = 'INSERT INTO phpgw_cust_attribute(' . implode(',', array_keys($field['cust'])) . ') '
				 . ' VALUES (' . $db->validate_insert($field['cust']) . ')';
			$db->query($sql, __LINE__, __FILE__);
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.714';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.714 to 0.9.17.715
	*
	*/
	$test[] = '0.9.17.714';

	function property_upgrade0_9_17_714()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_location_exception_severity',  array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
				),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
		'fm_location_exception_category',  array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
				'parent_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
		'fm_location_exception_category_text',  array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'category_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'content' => array('type' => 'text', 'nullable' => True),
				),
			'pk' => array('id'),
			'fk' => array('fm_location_exception_category' => array('category_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
		'fm_location_exception',  array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'location_code' => array('type' => 'varchar', 'precision' => 20, 'nullable' => False),
				'severity_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'category_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'descr' => array('type' => 'text', 'nullable' => True),
				'start_date' => array('type' => 'int', 'precision' => 8, 'nullable' => False),
				'end_date' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
				'reference' => array('type' => 'text', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array('fm_location_exception_severity' => array('severity_id' => 'id'),
				'fm_location_exception_category' => array('category_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		));

		$GLOBALS['phpgw']->locations->add('.location.exception', 'location exception', 'property');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.715';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.715 to 0.9.17.716
	*
	*/
	$test[] = '0.9.17.715';

	function property_upgrade0_9_17_715()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location_exception', 'alert_vendor', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.716';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.716 to 0.9.17.717
	*
	*/
	$test[] = '0.9.17.716';

	function property_upgrade0_9_17_716()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location_exception', 'category_text_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => true));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.717';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.717 to 0.9.17.718
	*
	*/
	$test[] = '0.9.17.717';

	function property_upgrade0_9_17_717()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'continuous', array(
			'type' => 'int', 'precision' => 2, 'nullable' => true));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.718';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.718 to 0.9.17.719
	*
	*/
	$test[] = '0.9.17.718';

	function property_upgrade0_9_17_718()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'order_deadline', array(
			'type' => 'int', 'precision' => 8, 'nullable' => true));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.719';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.719 to 0.9.17.720
	*
	*/
	$test[] = '0.9.17.719';

	function property_upgrade0_9_17_719()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'order_deadline2', array(
			'type' => 'int', 'precision' => 8, 'nullable' => true));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.720';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.720 to 0.9.17.721
	*
	*/
	$test[] = '0.9.17.720';

	function property_upgrade0_9_17_720()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request', 'title', array('type' => 'text',
			'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.721';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.721 to 0.9.17.722
	*
	*/
	$test[] = '0.9.17.721';

	function property_upgrade0_9_17_721()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");

		$GLOBALS['phpgw_setup']->oProc->query("DROP VIEW fm_orders_paid_or_pending_view");

		$sql = 'CREATE OR REPLACE VIEW public.fm_orders_paid_or_pending_view AS
			SELECT orders_paid_or_pending.order_id,
			   orders_paid_or_pending.periode,
			   orders_paid_or_pending.amount,
			   orders_paid_or_pending.periodization,
			   orders_paid_or_pending.periodization_start,
			   orders_paid_or_pending.mvakode
			  FROM ( SELECT fm_ecobilagoverf.pmwrkord_code AS order_id,
					   fm_ecobilagoverf.periode,
					   sum(fm_ecobilagoverf.godkjentbelop) AS amount,
					   fm_ecobilagoverf.periodization,
					   fm_ecobilagoverf.periodization_start,
					   fm_ecobilagoverf.mvakode
					  FROM fm_ecobilagoverf
					 GROUP BY fm_ecobilagoverf.pmwrkord_code, fm_ecobilagoverf.periode, fm_ecobilagoverf.periodization, fm_ecobilagoverf.periodization_start,fm_ecobilagoverf.mvakode
				   UNION ALL
					SELECT fm_ecobilag.pmwrkord_code AS order_id,
					   fm_ecobilag.periode,
					   sum(fm_ecobilag.godkjentbelop) AS amount,
					   fm_ecobilag.periodization,
					   fm_ecobilag.periodization_start,
					   fm_ecobilag.mvakode
					  FROM fm_ecobilag
					 GROUP BY fm_ecobilag.pmwrkord_code, fm_ecobilag.periode, fm_ecobilag.periodization, fm_ecobilag.periodization_start, fm_ecobilag.mvakode) orders_paid_or_pending
			 ORDER BY orders_paid_or_pending.periode, orders_paid_or_pending.order_id';

		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.722';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.722 to 0.9.17.723
	*
	*/
	$test[] = '0.9.17.722';

	function property_upgrade0_9_17_722()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder', 'file_attachments', array(
			'type' => 'varchar',
			'precision' => 255,
			'nullable' => True
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.723';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.723 to 0.9.17.724
	*
	*/
	$test[] = '0.9.17.723';

	function property_upgrade0_9_17_723()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location_type', 'enable_controller', array(
				'type' =>	'int',
				'precision' => 2,
				'nullable' => true
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.724';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.724 to 0.9.17.725
	*
	*/
	$test[] = '0.9.17.724';

	function property_upgrade0_9_17_724()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'invoice_remark', array(
				'type' =>	'text',
				'nullable' => true
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.725';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.725 to 0.9.17.726
	*
	*/
	$test[] = '0.9.17.725';

	function property_upgrade0_9_17_725()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw']->locations->add('.admin_booking', 'Administrer booking', 'property');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.726';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.726 to 0.9.17.727
	*
	*/
	$test[] = '0.9.17.726';

	function property_upgrade0_9_17_726()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_b_account_user', array(
				'fd' => array(
					'b_account_id' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
					'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
					'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
					'modified_on' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				),
				'pk' => array('b_account_id', 'user_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.727';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.727 to 0.9.17.728
	*
	*/
	$test[] = '0.9.17.727';

	function property_upgrade0_9_17_727()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf', 'external_updated',array(
			'type' => 'int',
			'precision' => 2,
			'nullable' => true
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf', 'netto_belop',array(
			'type' => 'decimal',
			'precision' => 20,
			'scale' => 2,
			'nullable' => True
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.728';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.728 to 0.9.17.729
	*
	*/
	$test[] = '0.9.17.728';

	function property_upgrade0_9_17_728()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'external_ticket_id',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => true
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.729';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.729 to 0.9.17.730
	*
	*/
	$test[] = '0.9.17.729';

	function property_upgrade0_9_17_729()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecodimb_role_user_substitute', 'start_time',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => true
			)
		);

		$now = time();

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_ecodimb_role_user_substitute SET start_time = {$now}", __LINE__, __FILE__);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.730';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.730 to 0.9.17.731
	*
	*/
	$test[] = '0.9.17.730';

	function property_upgrade0_9_17_730()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw']->locations->add('.ticket.external_communication', 'Helpdesk external communication', 'property', $allow_grant = false, $custom_tbl = false, $c_function = true);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_tts_external_communication', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => False),
					'ticket_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
					'order_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
					'type_id' => array('type' => 'int', 'precision' => 2, 'nullable' => False),
					'vendor_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
					'subject' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
					'mail_recipients' => array('type' => 'text', 'nullable' => True),
					'file_attachments' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
					'deadline' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
					'deadline2' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
					'created_on' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
					'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
					'modified_date' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				),
				'pk' => array('id'),
				'ix' => array(),
				'fk' => array(
					'fm_tts_tickets' => array('ticket_id' => 'id')
					),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.731';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.731 to 0.9.17.732
	*
	*/
	$test[] = '0.9.17.731';

	function property_upgrade0_9_17_731()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_tts_external_communication_type', array(
				'fd' => array(
					'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
					'name' => array('type' => 'varchar', 'precision' => 100, 'nullable' => true),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.732';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.732 to 0.9.17.733
	*
	*/
	$test[] = '0.9.17.732';

	function property_upgrade0_9_17_732()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_tts_external_communication_msg',  array(
				'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => False),
					'excom_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
					'message' => array('type' => 'text', 'nullable' => False),
					'timestamp_sent' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
					'mail_recipients' => array('type' => 'text', 'nullable' => True),
					'file_attachments' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
					'sender_email_address' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
					'created_on' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
					'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				),
				'pk' => array('id'),
				'ix' => array(),
				'fk' => array(
					'fm_tts_external_communication' => array('excom_id' => 'id')
					),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.733';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}


	/**
	* Handyman
	* Update property version from 0.9.17.733 to 0.9.17.734
	*
	*/
	$test[] = '0.9.17.733';
	function property_upgrade0_9_17_733()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'document_required', array(
				'type' =>	'int',
				'precision' => 4,
				'nullable' => True
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'handyman_checklist_id', array(
				'type' =>	'int',
				'precision' => 8,
				'nullable' => true
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets', 'handyman_order_number', array(
				'type' =>	'int',
				'precision' => 8,
				'nullable' => true
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_handyman_documents', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'hs_document_id' => array('type' => 'varchar', 'precision' => 20, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 20, 'nullable' => False),
				'file_path' => array('type' => 'varchar', 'precision' => 20, 'nullable' => False),
				'file_extension' => array('type' => 'varchar', 'precision' => 20, 'nullable' => False),
				'hm_installation_id' => array('type' => 'varchar', 'precision' => 20, 'nullable' => False),
				'created_date' => array('type' => 'timestamp', 'nullable' => True, 'default' => 'current_timestamp'),
				'retrieved_from_handyman' => array('type' => 'int', 'precision' => 2, 'default' => '0'),
				'retrieved_date' => array('type' => 'timestamp', 'nullable' => True),
				'message_id' => array('type' => 'int', 'precision' => 4, 'default' => 0),
				'hs_order_number' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'hs_checklist_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
			),
			'pk' => array('id'),
			'ix' => array(),
			'uc' => array()
		));


		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_handyman_log', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'comment' => array('type' => 'text'),
				'log_date' => array('type' => 'timestamp', 'default' => 'current_timestamp'),
				'success' => array('type' => 'bool', 'nullable' => false, 'default' => 'false'),
				'num_of_messages' => array('type' => 'int', 'precision' => 4)
			),
			'pk' => array('id'),
			'ix' => array(),
			'uc' => array()
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.734';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}