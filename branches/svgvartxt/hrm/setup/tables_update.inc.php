<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
	* @subpackage setup
 	* @version $Id$
	*/

	/**
	* Update hrm version from 0.9.17.000 to 0.9.17.001
	*/

	$test[] = '0.9.17.000';
	function hrm_upgrade0_9_17_000()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'hrm_acl_location', array(
				'fd' => array(
					'appname' => array('type' => 'varchar','precision' => '25','nullable' => False),
					'id' => array('type' => 'varchar','precision' => '50','nullable' => False),
					'descr' => array('type' => 'varchar','precision' => '50','nullable' => False),
					'allow_grant' => array('type' => 'int','precision' => '4','nullable' => True)
				),
				'pk' => array('appname','id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO hrm_acl_location (appname,id, descr) VALUES ('hrm', '.', 'Top')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO hrm_acl_location (appname,id, descr, allow_grant) VALUES ('hrm', '.user', 'User',1)");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO hrm_acl_location (appname,id, descr) VALUES ('hrm', '.job', 'Job description')");

		$GLOBALS['setup_info']['hrm']['currentver'] = '0.9.17.001';
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit();
		return $GLOBALS['setup_info']['hrm']['currentver'];
	}

	/**
	* Update hrm version from 0.9.17.001 to 0.9.17.002
	*/

	$test[] = '0.9.17.001';
	function hrm_upgrade0_9_17_001()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->RenameTable('hrm_acl','phpgw_hrm_acl');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('hrm_acl_location','phpgw_hrm_acl_location');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('hrm_org','phpgw_hrm_org');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('hrm_job','phpgw_hrm_job');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('hrm_task','phpgw_hrm_task');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('hrm_task_type','phpgw_hrm_task_type');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('hrm_task_job','phpgw_hrm_task_job');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('hrm_training','phpgw_hrm_training');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('hrm_training_category','phpgw_hrm_training_category');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('hrm_training_place','phpgw_hrm_training_place');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('hrm_training_task','phpgw_hrm_training_task');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('hrm_experience_category','phpgw_hrm_experience_category');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('hrm_skill_level','phpgw_hrm_skill_level');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('hrm_task_category','phpgw_hrm_task_category');

		$GLOBALS['setup_info']['hrm']['currentver'] = '0.9.17.002';
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit();
		return $GLOBALS['setup_info']['hrm']['currentver'];
	}

	/**
	* Update hrm version from 0.9.17.002 to 0.9.17.003
	*/

	$test[] = '0.9.17.002';
	function hrm_upgrade0_9_17_002()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();


		$GLOBALS['phpgw_setup']->oProc->RenameTable('phpgw_hrm_task','phpgw_hrm_quali');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('phpgw_hrm_task_type','phpgw_hrm_quali_type');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('phpgw_hrm_task_job','phpgw_hrm_quali_job');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('phpgw_hrm_training_task','phpgw_hrm_training_quali');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('phpgw_hrm_task_category','phpgw_hrm_quali_category');

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_hrm_quali','task_type_id','quali_type_id');
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_hrm_quali','task_parent','quali_parent');
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_hrm_quali','task_owner','quali_owner');
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_hrm_quali_job','task_id','quali_id');
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_hrm_training_quali','task_id','quali_id');

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_hrm_task', array(
				'fd' => array(
					'id' => array('type' => 'auto','precision' => '4','nullable' => False),
					'job_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'task_parent' => array('type' => 'int','precision' => '4','nullable' => True),
					'task_level' => array('type' => 'int','precision' => '4','nullable' => True),
					'name' => array('type' => 'varchar','precision' => '64','nullable' => True),
					'descr' => array('type' => 'text','nullable' => True),
					'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
					'owner' => array('type' => 'int','precision' => '4','nullable' => True),
					'access' => array('type' => 'varchar','precision' => '7','nullable' => True),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['setup_info']['hrm']['currentver'] = '0.9.17.003';
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit();
		return $GLOBALS['setup_info']['hrm']['currentver'];
	}


	/**
	* Update hrm version from 0.9.17.003 to 0.9.17.004
	*/

	$test[] = '0.9.17.003';
	function hrm_upgrade0_9_17_003()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_hrm_quali','remark',array('type' => 'text', 'nullable' => True));

		$GLOBALS['setup_info']['hrm']['currentver'] = '0.9.17.004';
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit();
		return $GLOBALS['setup_info']['hrm']['currentver'];
	}

	/**
	* Update hrm version from 0.9.17.004 to 0.9.17.005
	*/

	$test[] = '0.9.17.004';
	function hrm_upgrade0_9_17_004()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_hrm_quali','value_sort',array('type' => 'int','precision' => '4', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_hrm_task','value_sort',array('type' => 'int','precision' => '4', 'nullable' => True));
		$GLOBALS['setup_info']['hrm']['currentver'] = '0.9.17.005';
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit();
		return $GLOBALS['setup_info']['hrm']['currentver'];
	}

	/**
	* Update hrm version from 0.9.17.005 to 0.9.17.006
	*/

	$test[] = '0.9.17.005';
	function hrm_upgrade0_9_17_005()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_hrm_training','credits',array('type' => 'int','precision' => '4', 'nullable' => True));
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['hrm']['currentver'] = '0.9.17.006';
			return $GLOBALS['setup_info']['hrm']['currentver'];
		}
	}
