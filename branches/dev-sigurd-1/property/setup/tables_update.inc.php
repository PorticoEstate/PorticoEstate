<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
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
					'origin' => array('type' => 'varchar','precision' => '12','nullable' => False),
					'origin_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'destination' => array('type' => 'varchar','precision' => '12','nullable' => False),
					'destination_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
					'entry_date' => array('type' => 'int','precision' => '4','nullable' => True)
				),
				'pk' => array('origin','origin_id','destination','destination_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_request_origin");
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$origin[]=array(
				'origin'	=> $GLOBALS['phpgw_setup']->oProc->f('origin'),
				'origin_id'	=> $GLOBALS['phpgw_setup']->oProc->f('origin_id'),
				'destination'=> 'request',
				'destination_id'	=> $GLOBALS['phpgw_setup']->oProc->f('request_id'),
				'entry_date'	=> $GLOBALS['phpgw_setup']->oProc->f('entry_date'),
			);
		}


		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_project_origin");
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$origin[]=array(
				'origin'	=> $GLOBALS['phpgw_setup']->oProc->f('origin'),
				'origin_id'	=> $GLOBALS['phpgw_setup']->oProc->f('origin_id'),
				'destination'=> 'project',
				'destination_id'	=> $GLOBALS['phpgw_setup']->oProc->f('project_id'),
				'entry_date'	=> $GLOBALS['phpgw_setup']->oProc->f('entry_date'),
			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_entity_origin");
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$origin[]=array(
				'origin'	=> $GLOBALS['phpgw_setup']->oProc->f('origin'),
				'origin_id'	=> $GLOBALS['phpgw_setup']->oProc->f('origin_id'),
				'destination'=> 'entity_' . $GLOBALS['phpgw_setup']->oProc->f('entity_id') . '_' . $GLOBALS['phpgw_setup']->oProc->f('cat_id'),
				'destination_id'	=> $GLOBALS['phpgw_setup']->oProc->f('id'),
				'entry_date'	=> $GLOBALS['phpgw_setup']->oProc->f('entry_date'),
			);
		}

		$rec_count = count($origin);


		for($i=0;$i<$rec_count;$i++)
		{
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_origin(origin,origin_id,destination,destination_id,entry_date) "
				. "VALUES('"
				.$origin[$i]['origin']."','"
				.$origin[$i]['origin_id']."','"
				.$origin[$i]['destination']."','"
				.$origin[$i]['destination_id']."','"
				.$origin[$i]['entry_date']."')");
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
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','descr',array('type' => 'text','nullable' => True));
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
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_acl_location','id',array('type' => 'varchar','precision' => '20','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_acl_location (id, descr) VALUES ('.tenant_claim', 'Tenant claim')");

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_tenant_claim_category', array(
				'fd' => array(
					'id' => array('type' => 'int','precision' => '4','nullable' => False),
					'descr' => array('type' => 'varchar','precision' => '255','nullable' => True)
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
					'id' => array('type' => 'auto','precision' => '4','nullable' => False),
					'project_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'tenant_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'amount' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0','nullable' => True),
					'b_account_id' => array('type' => 'int','precision' => '4','nullable' => True),
					'category' => array('type' => 'int','precision' => '4','nullable' => False),
					'status' => array('type' => 'varchar','precision' => '8','nullable' => True),
					'remark' => array('type' => 'text','nullable' => True),
					'user_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'entry_date' => array('type' => 'int','precision' => '4','nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder','claim_issued',array('type' => 'int','precision' => 2,'nullable' => True));

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
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location_type','pk',array('type' => 'text','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location_type','ix',array('type' => 'text','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location_type','uc',array('type' => 'text','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location_attrib','custom',array('type' => 'int','precision' => 4,'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_attrib SET custom = 1");

		$GLOBALS['phpgw_setup']->oProc->query("SELECT count(*) FROM fm_location_type");
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$locations = $GLOBALS['phpgw_setup']->oProc->f(0);

		for ($location_type=1; $location_type<($locations+1); $location_type++)
		{
			$GLOBALS['phpgw_setup']->oProc->query("SELECT max(id) as id FROM fm_location_attrib WHERE type_id = $location_type");
			$GLOBALS['phpgw_setup']->oProc->next_record();
			$id = $GLOBALS['phpgw_setup']->oProc->f('id');
			$id++;

			$default_attrib['id'][]= $id;
			$default_attrib['column_name'][]= 'location_code';
			$default_attrib['type'][]='V';
			$default_attrib['precision'][] =4*$location_type;
			$default_attrib['nullable'][] ='False';
			$default_attrib['input_text'][] ='dummy';
			$default_attrib['statustext'][] ='dummy';
			$default_attrib['custom'][] ='NULL';
			$id++;

			$default_attrib['id'][]= $id;
			$default_attrib['column_name'][]= 'loc' . $location_type . '_name';
			$default_attrib['type'][]='V';
			$default_attrib['precision'][] =50;
			$default_attrib['nullable'][] ='True';
			$default_attrib['input_text'][] ='dummy';
			$default_attrib['statustext'][] ='dummy';
			$default_attrib['custom'][] ='NULL';
			$id++;

			$default_attrib['id'][]= $id;
			$default_attrib['column_name'][]= 'entry_date';
			$default_attrib['type'][]='I';
			$default_attrib['precision'][] =4;
			$default_attrib['nullable'][] ='True';
			$default_attrib['input_text'][] ='dummy';
			$default_attrib['statustext'][] ='dummy';
			$default_attrib['custom'][] ='NULL';
			$id++;

			$default_attrib['id'][]= $id;
			$default_attrib['column_name'][]= 'category';
			$default_attrib['type'][]='I';
			$default_attrib['precision'][] =4;
			$default_attrib['nullable'][] ='False';
			$default_attrib['input_text'][] ='dummy';
			$default_attrib['statustext'][] ='dummy';
			$default_attrib['custom'][] ='NULL';
			$id++;

			$default_attrib['id'][]= $id;
			$default_attrib['column_name'][]= 'user_id';
			$default_attrib['type'][]='I';
			$default_attrib['precision'][] =4;
			$default_attrib['nullable'][] ='False';
			$default_attrib['input_text'][] ='dummy';
			$default_attrib['statustext'][] ='dummy';
			$default_attrib['custom'][] ='NULL';
			$id++;

			$default_attrib['id'][]= $id;
			$default_attrib['column_name'][]= 'remark';
			$default_attrib['type'][]='T';
			$default_attrib['precision'][] = 'NULL';
			$default_attrib['nullable'][] ='False';
			$default_attrib['input_text'][] ='dummy';
			$default_attrib['statustext'][] ='dummy';
			$default_attrib['custom'][] ='NULL';
			$id++;

			for ($i=1; $i<$location_type+1; $i++)
			{
				$pk[$i-1]= 'loc' . $i;

				$default_attrib['id'][]= $id;
				$default_attrib['column_name'][]= 'loc' . $i;
				$default_attrib['type'][]='V';
				$default_attrib['precision'][] =4;
				$default_attrib['nullable'][] ='False';
				$default_attrib['input_text'][] ='dummy';
				$default_attrib['statustext'][] ='dummy';
				$default_attrib['custom'][] ='NULL';
				$id++;
			}

			if ($location_type==1)
			{
				$default_attrib['id'][]= $id;
				$default_attrib['column_name'][]= 'mva';
				$default_attrib['type'][]='I';
				$default_attrib['precision'][] =4;
				$default_attrib['nullable'][] ='True';
				$default_attrib['input_text'][] ='mva';
				$default_attrib['statustext'][] ='mva';
				$default_attrib['custom'][] = 1;
				$id++;

				$default_attrib['id'][]= $id;
				$default_attrib['column_name'][]= 'kostra_id';
				$default_attrib['type'][]='I';
				$default_attrib['precision'][] =4;
				$default_attrib['nullable'][] ='True';
				$default_attrib['input_text'][] ='kostra_id';
				$default_attrib['statustext'][] ='kostra_id';
				$default_attrib['custom'][] = 1;
				$id++;

				$default_attrib['id'][]= $id;
				$default_attrib['column_name'][]= 'part_of_town_id';
				$default_attrib['type'][]='I';
				$default_attrib['precision'][] =4;
				$default_attrib['nullable'][] ='True';
				$default_attrib['input_text'][] ='dummy';
				$default_attrib['statustext'][] ='dummy';
				$default_attrib['custom'][] ='NULL';
				$id++;

				$default_attrib['id'][]= $id;
				$default_attrib['column_name'][]= 'owner_id';
				$default_attrib['type'][]='I';
				$default_attrib['precision'][] =4;
				$default_attrib['nullable'][] ='True';
				$default_attrib['input_text'][] ='dummy';
				$default_attrib['statustext'][] ='dummy';
				$default_attrib['custom'][] ='NULL';
				$id++;
			}

			if($location_type>1)
			{
				$fk_table='fm_location'. ($location_type-1);

				for ($i=1; $i<$standard['id']; $i++)
				{
					$fk['loc' . $i]	= $fk_table . '.loc' . $i;
				}
			}

			$ix = array('location_code');

			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_type SET "
				. "pk ='" . implode(',',$pk) . "',"
				. "ix ='" . implode(',',$ix) . "' WHERE id = $location_type");


			for ($i=0;$i<count($default_attrib['id']);$i++)
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

		$GLOBALS['phpgw_setup']->oProc->query("SELECT count(*) FROM fm_location_type");
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$locations = $GLOBALS['phpgw_setup']->oProc->f(0);

		for ($location_type=1; $location_type<($locations+1); $location_type++)
		{
			$GLOBALS['phpgw_setup']->oProc->query("SELECT max(attrib_sort) as attrib_sort FROM fm_location_attrib WHERE type_id = $location_type AND column_name = 'remark' AND attrib_sort IS NOT NULL");

			$GLOBALS['phpgw_setup']->oProc->next_record();
			$attrib_sort = $GLOBALS['phpgw_setup']->oProc->f('attrib_sort')+1;


			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_attrib SET attrib_sort = $attrib_sort WHERE type_id = $location_type AND column_name = 'remark'");

			if($location_type==1)
			{
				$attrib_sort++;

				$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_attrib SET attrib_sort = $attrib_sort WHERE type_id = $location_type AND column_name = 'mva'");
				$attrib_sort++;

				$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_attrib SET attrib_sort = $attrib_sort WHERE type_id = $location_type AND column_name = 'kostra_id'");
			}

			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location' . $location_type,'change_type',array('type' => 'int','precision' => 4,'nullable' => True));

			$GLOBALS['phpgw_setup']->oProc->query("SELECT max(id) as attrib_id FROM fm_location_attrib WHERE type_id = $location_type");

			$GLOBALS['phpgw_setup']->oProc->next_record();
			$attrib_id = $GLOBALS['phpgw_setup']->oProc->f('attrib_id')+1;

			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location_attrib (type_id,id,column_name,datatype,precision_,input_text,statustext,nullable,custom)"
					. " VALUES ( $location_type,$attrib_id, 'change_type', 'I', 4, 'change_type','change_type','True',NULL)");

			if($location_type==4)
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

			$metadata = $GLOBALS['phpgw_setup']->db->metadata('fm_location'.$location_type);

			if(isset($GLOBALS['phpgw_setup']->db->adodb))
			{
				$i = 0;
				foreach($metadata as $key => $val)
				{
					$metadata_temp[$i]['name'] = $key;
					$i++;
				}
				$metadata = $metadata_temp;
				unset ($metadata_temp);
			}

			for ($i=0; $i<count($metadata); $i++)
			{
				$sql = "SELECT * FROM fm_location_attrib WHERE type_id=$location_type AND column_name = '" . $metadata[$i]['name'] . "'";

				$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);
				if($GLOBALS['phpgw_setup']->oProc->next_record())
				{
					if(!$precision = $GLOBALS['phpgw_setup']->oProc->f('precision_'))
					{
						$precision = $datatype_precision[$GLOBALS['phpgw_setup']->oProc->f('datatype')];
					}

					if($GLOBALS['phpgw_setup']->oProc->f('nullable')=='True')
					{
						$nullable=True;
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

			$fd['exp_date'] = array('type' => 'timestamp','nullable' => True,'default' => 'current_timestamp');

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
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_wo_hours','category',array('type' => 'int','precision' => 4,'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_wo_hours_category', array(
				'fd' => array(
					'id' => array('type' => 'int','precision' => '4','nullable' => False),
					'descr' => array('type' => 'varchar','precision' => '255','nullable' => False)
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
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','d_safety',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','d_aesthetics',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','d_indoor_climate',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','d_consequential_damage',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','d_user_gratification',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','d_residential_environment',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','p_safety',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','p_aesthetics',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','p_indoor_climate',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','p_consequential_damage',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','p_user_gratification',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','p_residential_environment',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','c_safety',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','c_aesthetics',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','c_indoor_climate',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','c_consequential_damage',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','c_user_gratification',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','c_residential_environment',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','authorities_demands',array('type' => 'int','precision' => '2','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','score',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));

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
					'id' => array('type' => 'int','precision' => '4','nullable' => False),
					'descr' => array('type' => 'varchar','precision' => '50','nullable' => False),
					'priority_key' => array('type' => 'int','precision' => '4','default' => '0','nullable' => True)
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
					'request_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'condition_type' => array('type' => 'int','precision' => '4','nullable' => False),
					'degree' => array('type' => 'int','precision' => '4','default' => '0','nullable' => True),
					'probability' => array('type' => 'int','precision' => '4','default' => '0','nullable' => True),
					'consequence' => array('type' => 'int','precision' => '4','default' => '0','nullable' => True),
					'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
					'entry_date' => array('type' => 'int','precision' => '4','nullable' => True)
				),
				'pk' => array('request_id','condition_type'),
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

		while ($GLOBALS['phpgw_setup']->oProc->next_record())
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

		while (is_array($condition) && list(,$value) = each($condition))
		{
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition (request_id,condition_type,degree,probability,consequence,user_id,entry_date) "
				. "VALUES ('"
				. $value['request_id']. "','"
				. 1 . "',"
				. $value['d_safety']. ","
				. $value['p_safety']. ","
				. $value['c_safety']. ","
				. $value['user_id']. ","
				. $value['entry_date']. ")",__LINE__,__FILE__);

			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition (request_id,condition_type,degree,probability,consequence,user_id,entry_date) "
				. "VALUES ('"
				. $value['request_id']. "','"
				. 2 . "',"
				. $value['d_aesthetics']. ","
				. $value['p_aesthetics']. ","
				. $value['c_aesthetics']. ","
				. $value['user_id']. ","
				. $value['entry_date']. ")",__LINE__,__FILE__);

			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition (request_id,condition_type,degree,probability,consequence,user_id,entry_date) "
				. "VALUES ('"
				. $value['request_id']. "','"
				. 3 . "',"
				. $value['d_indoor_climate']. ","
				. $value['p_indoor_climate']. ","
				. $value['c_indoor_climate']. ","
				. $value['user_id']. ","
				. $value['entry_date']. ")",__LINE__,__FILE__);

			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition (request_id,condition_type,degree,probability,consequence,user_id,entry_date) "
				. "VALUES ('"
				. $value['request_id']. "','"
				. 4 . "',"
				. $value['d_consequential_damage']. ","
				. $value['p_consequential_damage']. ","
				. $value['c_consequential_damage']. ","
				. $value['user_id']. ","
				. $value['entry_date']. ")",__LINE__,__FILE__);

			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition (request_id,condition_type,degree,probability,consequence,user_id,entry_date) "
				. "VALUES ('"
				. $value['request_id']. "','"
				. 5 . "',"
				. $value['d_user_gratification']. ","
				. $value['p_user_gratification']. ","
				. $value['c_user_gratification']. ","
				. $value['user_id']. ","
				. $value['entry_date']. ")",__LINE__,__FILE__);

			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition (request_id,condition_type,degree,probability,consequence,user_id,entry_date) "
				. "VALUES ('"
				. $value['request_id']. "','"
				. 6 . "',"
				. $value['d_residential_environment']. ","
				. $value['p_residential_environment']. ","
				. $value['c_residential_environment']. ","
				. $value['user_id']. ","
				. $value['entry_date']. ")",__LINE__,__FILE__);

			$id = $value['request_id'];



			$sql = "SELECT sum(priority_key * ( degree * probability * ( consequence +1 ))) AS score FROM fm_request_condition"
			 . " JOIN fm_request_condition_type ON (fm_request_condition.condition_type = fm_request_condition_type.id) WHERE request_id = $id";

			$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);

			$GLOBALS['phpgw_setup']->oProc->next_record();
			$score = $GLOBALS['phpgw_setup']->oProc->f('score');
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET score = $score WHERE id = $id",__LINE__,__FILE__);
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
					'acl_location' => array('type' => 'varchar','precision' => '50','nullable' => False),
					'id' => array('type' => 'int','precision' => '4','nullable' => False),
					'descr' => array('type' => 'text','nullable' => True),
					'file_name ' => array('type' => 'varchar','precision' => '50','nullable' => False),
					'active' => array('type' => 'int','precision' => '2','nullable' => True),
					'custom_sort' => array('type' => 'int','precision' => '4','nullable' => True)
				),
				'pk' => array('acl_location','id'),
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
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilag','item_type',array('type' => 'int','precision' => 4,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilag','item_id',array('type' => 'varchar','precision' => 20,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf','item_type',array('type' => 'int','precision' => 4,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf','item_id',array('type' => 'varchar','precision' => 20,'nullable' => True));

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
					'id' => array('type' => 'int','precision' => '4','nullable' => False),
					'name' => array('type' => 'varchar','precision' => '100','nullable' => False),
					'sql_text' => array('type' => 'text','nullable' => False),
					'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
					'user_id' => array('type' => 'int','precision' => '4','nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->m_aTables = $table_def;

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_custom','sql','sql_text');

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

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_attribute','history',array('type' => 'int','precision' => 2,'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_entity_history', array(
				'fd' => array(
					'history_id' => array('type' => 'auto','precision' => '4','nullable' => False),
					'history_record_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'history_appname' => array('type' => 'varchar','precision' => '64','nullable' => False),
					'history_entity_attrib_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'history_owner' => array('type' => 'int','precision' => '4','nullable' => False),
					'history_status' => array('type' => 'char','precision' => '2','nullable' => False),
					'history_new_value' => array('type' => 'text','nullable' => False),
					'history_timestamp' => array('type' => 'timestamp','nullable' => False,'default' => 'current_timestamp')
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
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'customer_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'customer_name' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
					'name' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
					'descr' => array('type' => 'text','nullable' => True),
					'status' => array('type' => 'varchar', 'precision' => 10,'nullable' => True),
					'category' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'member_of' => array('type' => 'text','nullable' => True),
					'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'start_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'end_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'termination_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'actual_cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
					'account_id' => array('type' => 'varchar', 'precision' => 20,'nullable' => True)
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
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'attrib_detail' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => '0'),
					'list' => array('type' => 'int', 'precision' => 2,'nullable' => True),
					'location_form' => array('type' => 'int', 'precision' => 2,'nullable' => True),
					'lookup_form' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'column_name' => array('type' => 'varchar', 'precision' => 20,'nullable' => False),
					'input_text' => array('type' => 'varchar', 'precision' => 50,'nullable' => False),
					'statustext' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
					'size' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'datatype' => array('type' => 'varchar', 'precision' => 10,'nullable' => False),
					'attrib_sort' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'precision_' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'scale' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'default_value' => array('type' => 'varchar', 'precision' => 18,'nullable' => True),
					'nullable' => array('type' => 'varchar', 'precision' => 5,'nullable' => False,'default' => 'True'),
					'search' => array('type' => 'int', 'precision' => 2,'nullable' => True)
				),
				'pk' => array('id','attrib_detail'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_r_agreement_category', array(
				'fd' => array(
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'descr' => array('type' => 'varchar', 'precision' => 50,'nullable' => True)
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
					'attrib_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'value' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
					'attrib_detail' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => '0')
				),
				'pk' => array('attrib_id','id','attrib_detail'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_r_agreement_item', array(
				'fd' => array(
					'agreement_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'location_code' => array('type' => 'varchar', 'precision' => 30,'nullable' => True),
					'address' => array('type' => 'varchar', 'precision' => 100,'nullable' => True),
					'p_num' => array('type' => 'varchar', 'precision' => 15,'nullable' => True),
					'p_entity_id' => array('type' => 'int', 'precision' => 4,'nullable' => True,'default' => '0'),
					'p_cat_id' => array('type' => 'int', 'precision' => 4,'nullable' => True,'default' => '0'),
					'descr' => array('type' => 'text','nullable' => True),
					'unit' => array('type' => 'varchar', 'precision' => 10,'nullable' => True),
					'quantity' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
					'frequency' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'test' => array('type' => 'text','nullable' => True),
					'cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
					'rental_type_id' => array('type' => 'int', 'precision' => 4,'nullable' => True)
				),
				'pk' => array('agreement_id','id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_r_agreement_item_history', array(
				'fd' => array(
					'agreement_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'item_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'current_index' => array('type' => 'int', 'precision' => 2,'nullable' => True),
					'this_index' => array('type' => 'decimal', 'precision' => 20, 'scale' => 4,'nullable' => True),
					'cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
					'index_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'from_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'to_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'tenant_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				),
				'pk' => array('agreement_id','item_id','id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);


		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_r_agreement_common', array(
				'fd' => array(
					'agreement_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'b_account' => array('type' => 'varchar', 'precision' => 30,'nullable' => True),
					'remark' => array('type' => 'text','nullable' => True),
					'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				),
				'pk' => array('agreement_id','id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);
	
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_r_agreement_c_history', array(
				'fd' => array(
					'agreement_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'c_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'from_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'to_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'current_record' => array('type' => 'int', 'precision' => 2,'nullable' => True),
					'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'budget_cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
					'actual_cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
					'fraction' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
					'override_fraction' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
				),
				'pk' => array('agreement_id','c_id','id'),
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
		$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$version = $GLOBALS['phpgw_setup']->oProc->f('app_version');

		if($version =='0.9.17.513')
		{
			$soadmin_location	= CreateObject('property.soadmin_location','property');
		
			for ($i=1; $i<=4; $i++)
			{
				$attrib= array(
					'column_name' => 'rental_area',
					'input_text' => 'Rental area',
					'statustext' => 'Rental area',
					'type_id' => $i,
					'lookup_form' => False,
					'list' => False,
					'column_info' => array('type' =>'N',
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
		
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_wo_hours','cat_per_cent',array('type' => 'int','precision' => 4,'nullable' => True));
		
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
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'year' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'b_group' => array('type' => 'varchar','precision' => '4','nullable' => False),
					'district_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'revision' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'access' => array('type' => 'varchar','precision' => '7','nullable' => True),
					'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'budget_cost' => array('type' => 'int', 'precision' => 4,'default' => '0','nullable' => True),
					'remark' => array('type' => 'text','nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('year','b_group','district_id','revision')
			)
		);
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_budget', array(
				'fd' => array(
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'year' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'b_account_id' => array('type' => 'varchar','precision' => '20','nullable' => False),
					'district_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'revision' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'access' => array('type' => 'varchar','precision' => '7','nullable' => True),
					'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'budget_cost' => array('type' => 'int', 'precision' => 4,'default' => '0','nullable' => True),
					'remark' => array('type' => 'text','nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('year','b_account_id','district_id','revision')
			)
		);
		
		$GLOBALS['phpgw_setup']->oProc->CreateTable(		
			'fm_budget_period', array(
				'fd' => array(
					'year' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'month' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'b_account_id' => array('type' => 'varchar','precision' => '20','nullable' => False),
					'percent' => array('type' => 'int','precision' => 4,'default' => '0','nullable' => True),
					'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'remark' => array('type' => 'text','nullable' => True)
				),
				'pk' => array('year','month','b_account_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);


		$GLOBALS['phpgw_setup']->oProc->CreateTable(		
			'fm_budget_cost', array(
				'fd' => array(
					'id' => array('type' => 'auto','precision' => '4','nullable' => False),
					'year' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'month' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'b_account_id' => array('type' => 'varchar','precision' => '20','nullable' => False),
					'amount' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0','nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('year','month','b_account_id')
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
					'id' => array('type' => 'int','precision' => '4','nullable' => False),
					'descr' => array('type' => 'varchar','precision' => '255','nullable' => False)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_b_account','category',array('type' => 'int','precision' => 4,'nullable' => True));

		$sql = "SELECT id, grouping from fm_b_account";
		$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$grouping[]=array(
				'id' => $GLOBALS['phpgw_setup']->oProc->f('id'),
				'grouping' => $GLOBALS['phpgw_setup']->oProc->f('grouping')
			);
		}
		
		if (is_array($grouping))
		{
			foreach ($grouping as $entry)
			{
				if((int)$entry['grouping']>0)
				{
					$grouping2[]=$entry['grouping'];

					$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_b_account set category = ". (int)$entry['grouping'] . " WHERE id = " . $entry['id'],__LINE__,__FILE__);
				}	
				
			}
			$grouping2 = array_unique($grouping2);
			foreach ($grouping2 as $entry)
			{
					$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_b_account_category (id, descr) VALUES (" . (int)$entry . ",'" . $entry . "')",__LINE__,__FILE__);
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

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_template_hours','entry_date',array('type' => 'int','precision' => 4,'nullable' => True));
		
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

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_request','start_date',array('type' => 'int','precision' => 4,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_request','end_date',array('type' => 'int','precision' => 4,'nullable' => True));
		
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

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_budget_basis','distribute_year',array('type' => 'text','nullable' => True));
		
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

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder','combined_cost', array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'));

		$sql = "SELECT app_version from phpgw_applications WHERE app_name = 'property'";
		$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$version = $GLOBALS['phpgw_setup']->oProc->f('app_version');

		if($version =='0.9.17.521')
		{
			$db2 = clone($GLOBALS['phpgw_setup']->oProc->m_odb);
			$sql = "SELECT id, budget, calculation from fm_workorder";
			$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);
			while($GLOBALS['phpgw_setup']->oProc->next_record())
			{
				if ($GLOBALS['phpgw_setup']->oProc->f('calculation') > 0)
				{
					$combined_cost = ($GLOBALS['phpgw_setup']->oProc->f('calculation') * 1.25); // tax included
				}
				else
				{
					$combined_cost = $GLOBALS['phpgw_setup']->oProc->f('budget');
				}
				
				if($combined_cost > 0)
				{
				
					$db2->query("UPDATE fm_workorder SET combined_cost = '$combined_cost' WHERE id = " . (int)$GLOBALS['phpgw_setup']->oProc->f('id'),__LINE__,__FILE__);
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

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder','paid', array('type' => 'int','precision' => '2','nullable' => True,'default' => '1'));
		
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
			if($GLOBALS['phpgw_setup']->oProc->f('grantor')>0)
			{
				$grantor = $GLOBALS['phpgw_setup']->oProc->f('grantor');
			}
			
			$db2->query("INSERT INTO phpgw_acl (acl_appname, acl_location, acl_account, acl_rights, acl_grantor,acl_type) VALUES ("
			. "'property','" 
			. $GLOBALS['phpgw_setup']->oProc->f('acl_location') . "','"
			. $GLOBALS['phpgw_setup']->oProc->f('acl_account') . "','"
			. $GLOBALS['phpgw_setup']->oProc->f('acl_rights') . "',"
			. $grantor . ",'"
			. (int) $GLOBALS['phpgw_setup']->oProc->f('acl_type') . "')");
			
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
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_tenant_attribute','input_text',array('type' => 'varchar','precision' => '50','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_vendor_attribute','input_text',array('type' => 'varchar','precision' => '50','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location_attrib','input_text',array('type' => 'varchar','precision' => '50','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_owner_attribute','input_text',array('type' => 'varchar','precision' => '50','nullable' => False));
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
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_attribute','disabled', array('type' => 'int','precision' => '4','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_attribute','helpmsg', array('type' => 'text','nullable' => True));

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

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_gab_location','location_code',array('type' => 'varchar','precision' => '20','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_gab_location','loc1',array('type' => 'varchar','precision' => '6','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location1','loc1',array('type' => 'varchar','precision' => '6','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location1_history','loc1',array('type' => 'varchar','precision' => '6','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location2','loc1',array('type' => 'varchar','precision' => '6','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location2_history','loc1',array('type' => 'varchar','precision' => '6','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location3','loc1',array('type' => 'varchar','precision' => '6','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location3_history','loc1',array('type' => 'varchar','precision' => '6','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location4','loc1',array('type' => 'varchar','precision' => '6','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location4_history','loc1',array('type' => 'varchar','precision' => '6','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','loc1',array('type' => 'varchar','precision' => '6','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_tts_tickets','loc1',array('type' => 'varchar','precision' => '6','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_project','loc1',array('type' => 'varchar','precision' => '6','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_investment','loc1',array('type' => 'varchar','precision' => '6','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_document','loc1',array('type' => 'varchar','precision' => '6','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_entity_1_1','loc1',array('type' => 'varchar','precision' => '6','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_entity_1_2','loc1',array('type' => 'varchar','precision' => '6','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_entity_1_3','loc1',array('type' => 'varchar','precision' => '6','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_entity_2_1','loc1',array('type' => 'varchar','precision' => '6','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_entity_2_2','loc1',array('type' => 'varchar','precision' => '6','nullable' => True));

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
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tenant','phpgw_account_lid', array('type' => 'varchar','precision' => '25','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tenant','account_lid', array('type' => 'varchar','precision' => '25','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tenant','account_pwd', array('type' => 'varchar','precision' => '32','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tenant','account_status', array('type' => 'char','precision' => '1','nullable' => True,'default' => 'A'));

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
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'member_of' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'first_name' => array('type' => 'varchar','precision' => '30','nullable' => True),
				'last_name' => array('type' => 'varchar','precision' => '30','nullable' => True),
				'contact_phone' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'category' => array('type' => 'int','precision' => '4','nullable' => True),
				'phpgw_account_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'account_lid' => array('type' => 'varchar','precision' => '25','nullable' => True),
				'account_pwd' => array('type' => 'varchar','precision' => '32','nullable' => True),
				'account_status' => array('type' => 'char','precision' => '1','nullable' => True,'default' => 'A')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		);

		$fm_tenant2 = array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'member_of' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'first_name' => array('type' => 'varchar','precision' => '30','nullable' => True),
				'last_name' => array('type' => 'varchar','precision' => '30','nullable' => True),
				'contact_phone' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'category' => array('type' => 'int','precision' => '4','nullable' => True),
				'phpgw_account_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'account_lid' => array('type' => 'varchar','precision' => '25','nullable' => True),
				'account_pwd' => array('type' => 'varchar','precision' => '32','nullable' => True),
				'account_status' => array('type' => 'int','precision' => '4','nullable' => True,'default' => '1')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		);

		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_tenant',$fm_tenant,'phpgw_account_lid');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_tenant',$fm_tenant2,'account_status');
		unset($fm_tenant);
		unset($fm_tenant2);
				
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tenant','phpgw_account_id', array('type' => 'int','precision' => '4','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tenant','account_status', array('type' => 'int','precision' => '4','nullable' => True,'default' => '1'));		

		$GLOBALS['phpgw_setup']->oProc->query("SELECT max(id) as id, max(attrib_sort) as attrib_sort FROM fm_tenant_attribute");
		
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$id = $GLOBALS['phpgw_setup']->oProc->f('id') + 1;
		$attrib_sort = $GLOBALS['phpgw_setup']->oProc->f('attrib_sort') +1;
		
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

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tenant','owner_id', array('type' => 'int','precision' => '4','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_owner','owner_id', array('type' => 'int','precision' => '4','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_vendor','owner_id', array('type' => 'int','precision' => '4','nullable' => True));

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
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_template_hours','hours_descr',array('type' => 'text','nullable' => True));
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
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location_type','list_info', array('type' => 'varchar','precision' => '255','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location_type','list_address', array('type' => 'int','precision' => '2','nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_type set list_info = '" . 'a:1:{i:1;s:1:"1";}' ."' WHERE id = '1'");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_type set list_info = '" . 'a:2:{i:1;s:1:"1";i:2;s:1:"2";}' ."' WHERE id = '2'");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_type set list_info = '" . 'a:3:{i:1;s:1:"1";i:2;s:1:"2";i:3;s:1:"3";}' ."' WHERE id = '3'");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_type set list_info = '" . 'a:1:{i:1;s:1:"1";}' ."' WHERE id = '4'");		
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
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '25','nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		);

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_dim_d');

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_ecodimd','name','descr');
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_ecodimd','descr',array('type' => 'varchar','precision' => '25','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_ecodimd',$table_def,'description');

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_ecodimb','name','descr');
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_ecodimb','descr',array('type' => 'varchar','precision' => '25','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_ecodimb',$table_def,'description');

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_ecomva','name','descr');
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_ecomva','descr',array('type' => 'varchar','precision' => '25','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_ecomva',$table_def,'description');

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_ecobilagtype','name','descr');
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_ecobilagtype','descr',array('type' => 'varchar','precision' => '25','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_ecobilagtype',$table_def,'description');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('fm_ecobilagtype', 'fm_ecobilag_category');

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_ecoart','name','descr');
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_ecoart','descr',array('type' => 'varchar','precision' => '25','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_ecoart',$table_def,'description');

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

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_project','end_date',array(
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


		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_s_agreement_attribute','history',array('type' => 'int','precision' => 2,'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_s_agreement_history', array(
				'fd' => array(
					'history_id' => array('type' => 'auto','precision' => '4','nullable' => False),
					'history_record_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'history_appname' => array('type' => 'varchar','precision' => '64','nullable' => False),
					'history_detail_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'history_attrib_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'history_owner' => array('type' => 'int','precision' => '4','nullable' => False),
					'history_status' => array('type' => 'char','precision' => '2','nullable' => False),
					'history_new_value' => array('type' => 'text','nullable' => False),
					'history_timestamp' => array('type' => 'timestamp','nullable' => False,'default' => 'current_timestamp')
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

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_entity_history','history_entity_attrib_id','history_attrib_id');

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

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category','start_ticket',array('type' => 'int','precision' => 2,'nullable' => True));

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
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_acl_location where appname = 'property' AND id = '.agreement'");
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_acl_location where appname = 'property' AND id = '.s_agreement'");
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_acl_location where appname = 'property' AND id = '.r_agreement'");
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_acl_location where appname = 'property' AND id = '.tenant'");
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_acl_location where appname = 'property' AND id = '.owner'");
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_acl_location where appname = 'property' AND id = '.vendor'");

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname,id, descr, allow_c_attrib,c_attrib_table) VALUES ('property', '.agreement', 'Agreement',1,'fm_agreement')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname,id, descr, allow_c_attrib,c_attrib_table) VALUES ('property', '.s_agreement', 'Service agreement',1,'fm_s_agreement')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname,id, descr, allow_c_attrib,c_attrib_table) VALUES ('property', '.s_agreement.detail', 'Service agreement detail',1,'fm_s_agreement_detail')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname,id, descr, allow_c_attrib,c_attrib_table) VALUES ('property', '.r_agreement', 'Rental agreement',1,'fm_r_agreement')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname,id, descr, allow_c_attrib,c_attrib_table) VALUES ('property', '.r_agreement.detail', 'Rental agreement detail',1,'fm_r_agreement_detail')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname,id, descr, allow_c_attrib,c_attrib_table) VALUES ('property', '.tenant', 'Tenant',1,'fm_tenant')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname,id, descr, allow_c_attrib,c_attrib_table) VALUES ('property', '.owner', 'Owner',1,'fm_owner')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname,id, descr, allow_c_attrib,c_attrib_table) VALUES ('property', '.vendor', 'Vendor',1,'fm_vendor')");

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_agreement_attribute");
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$attrib[]=array(
					'appname'		=> 'property',
					'location'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_detail') == 1 ? '.agreement':'.agreement.detail',
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
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$attrib[]=array(
					'appname'		=> 'property',
					'location'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_detail') == 1 ? '.r_agreement':'.r_agreement.detail',
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
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$attrib[]=array(
					'appname'		=> 'property',
					'location'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_detail') == 1 ? '.s_agreement':'.s_agreement.detail',
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
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$attrib[]=array(
					'appname'		=> 'property',
					'location'		=> '.owner',
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
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$attrib[]=array(
					'appname'		=> 'property',
					'location'		=> '.tenant',
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
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$attrib[]=array(
					'appname'		=> 'property',
					'location'		=> '.vendor',
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

		foreach ($attrib as $entry)
		{
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_cust_attribute (' . implode(',',array_keys($entry)) . ') VALUES (' . $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($entry)) . ')');
		}
		
		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_agreement_choice"); 
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$choice[]=array(
					'appname'		=> 'property',
					'location'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_detail') == 1 ? '.agreement':'.agreement.detail',
					'attrib_id'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'value'			=> $GLOBALS['phpgw_setup']->oProc->f('value')
			);
		}
			
		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_r_agreement_choice"); 
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$choice[]=array(
					'appname'		=> 'property',
					'location'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_detail') == 1 ? '.r_agreement':'.r_agreement.detail',
					'attrib_id'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'value'			=> $GLOBALS['phpgw_setup']->oProc->f('value')
			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_s_agreement_choice"); 
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$choice[]=array(
					'appname'		=> 'property',
					'location'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_detail') == 1 ? '.s_agreement':'.s_agreement.detail',
					'attrib_id'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'value'			=> $GLOBALS['phpgw_setup']->oProc->f('value')
			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_owner_choice"); 
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$choice[]=array(
					'appname'		=> 'property',
					'location'		=> '.owner',
					'attrib_id'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'value'			=> $GLOBALS['phpgw_setup']->oProc->f('value')
			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_tenant_choice"); 
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$choice[]=array(
					'appname'		=> 'property',
					'location'		=> '.tenant',
					'attrib_id'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'value'			=> $GLOBALS['phpgw_setup']->oProc->f('value')
			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_vendor_choice"); 
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$choice[]=array(
					'appname'		=> 'property',
					'location'		=> '.vendor',
					'attrib_id'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'value'			=> $GLOBALS['phpgw_setup']->oProc->f('value')
			);
		}

		foreach ($choice as $entry)
		{
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_cust_choice (' . implode(',',array_keys($entry)) . ') VALUES (' . $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($entry)) . ')');
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
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$attrib[]=array(
					'appname'		=> 'property',
					'location'		=> '.entity.' . $GLOBALS['phpgw_setup']->oProc->f('entity_id') . '.' . $GLOBALS['phpgw_setup']->oProc->f('cat_id'),
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

		foreach ($attrib as $entry)
		{
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_cust_attribute (' . implode(',',array_keys($entry)) . ') VALUES (' . $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($entry)) . ')');
		}

		$choice = array();
		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_entity_choice"); 
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$choice[]=array(
					'appname'		=> 'property',
					'location'		=> '.entity.' . $GLOBALS['phpgw_setup']->oProc->f('entity_id') . '.' . $GLOBALS['phpgw_setup']->oProc->f('cat_id'),
					'attrib_id'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'value'			=> $GLOBALS['phpgw_setup']->oProc->f('value')
			);
		}

		foreach ($choice as $entry)
		{
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_cust_choice (' . implode(',',array_keys($entry)) . ') VALUES (' . $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($entry)) . ')');
		}

		$location = array();
		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM phpgw_acl_location WHERE appname = 'property' AND id LIKE '.entity.%'");

		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$location[]= $GLOBALS['phpgw_setup']->oProc->f('id');
		}

		foreach ($location as $entry)
		{
			if (strlen($entry)>10)
			{
				$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_acl_location SET allow_c_attrib=1 ,c_attrib_table ='fm" . str_replace('.','_', $entry) ."' WHERE id = '$entry'");
			}
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_entity_attribute');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_entity_choice');

//---------------
//--------------- custom functions
		$custom = array();
		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_custom_function"); 
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$custom[]=array(
					'appname'		=> 'property',
					'location'		=> $GLOBALS['phpgw_setup']->oProc->f('acl_location'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'descr'			=> $GLOBALS['phpgw_setup']->oProc->f('descr'),
					'file_name'		=> $GLOBALS['phpgw_setup']->oProc->f('file_name'),
					'active'		=> $GLOBALS['phpgw_setup']->oProc->f('active'),
					'custom_sort'	=> $GLOBALS['phpgw_setup']->oProc->f('custom_sort')
			);
		}
		foreach ($custom as $entry)
		{
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_cust_function (' . implode(',',array_keys($entry)) . ') VALUES (' . $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($entry)) . ')');
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_custom_function');
//----------------

//--------------- locations

		$attrib = array();
		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_location_attrib");
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$attrib[]=array(
					'appname'		=> 'property',
					'location'		=> '.location.' . $GLOBALS['phpgw_setup']->oProc->f('type_id'),
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

		foreach ($attrib as $entry)
		{
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_cust_attribute (' . implode(',',array_keys($entry)) . ') VALUES (' . $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($entry)) . ')');
		}

		$choice = array();
		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_location_choice"); 
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$choice[]=array(
					'appname'		=> 'property',
					'location'		=> '.location.' . $GLOBALS['phpgw_setup']->oProc->f('type_id'),
					'attrib_id'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'value'			=> $GLOBALS['phpgw_setup']->oProc->f('value')
			);
		}

		foreach ($choice as $entry)
		{
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_cust_choice (' . implode(',',array_keys($entry)) . ') VALUES (' . $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($entry)) . ')');
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

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_budget_period','percent','per_cent');

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
			'/home/entity'				=> '/property/entity',
			'/home/document'			=> '/property/document',
			'/home/fmticket'			=> '/property/fmticket',
			'/home/request'				=> '/property/request',
			'/home/workorder'			=> '/property/workorder',
			'/home/service_agreement'	=> '/property/service_agreement',
			'/home/rental_agreement'	=> '/property/rental_agreement',
			'/home/agreement'			=> '/property/agreement'
		);

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
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$files[]=array(
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

