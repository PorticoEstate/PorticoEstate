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

	$phpgw_baseline = array(
		'phpgw_hrm_org' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'org_parent' => array('type' => 'int','precision' => '4','nullable' => True),
				'job_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'part_time_percent' => array('type' => 'int','precision' => '4','nullable' => True),
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
		),

		'phpgw_hrm_job' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'job_parent' => array('type' => 'int','precision' => '4','nullable' => True),
				'job_level' => array('type' => 'int','precision' => '4','nullable' => True),
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
		),

		'phpgw_hrm_task' => array(
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
				'value_sort' => array('type' => 'int','precision' => '4','nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),


		'phpgw_hrm_quali_type' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'name' => array('type' => 'varchar','precision' => '100','nullable' => True),
				'descr' => array('type' => 'text','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'type_owner' => array('type' => 'int','precision' => '4','nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_hrm_quali' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'job_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'quali_type_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'category' => array('type' => 'int','precision' => '4','nullable' => True),
				'skill_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'experience_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'quali_parent' => array('type' => 'int','precision' => '4','nullable' => True),
				'is_parent' => array('type' => 'int','precision' => '2','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'quali_owner' => array('type' => 'int','precision' => '4','nullable' => True),
				'remark' => array('type' => 'text','nullable' => True),
				'value_sort' => array('type' => 'int','precision' => '4','nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_hrm_quali_job' => array(
			'fd' => array(
				'quali_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'job_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'importance' => array('type' => 'int','precision' => '4','nullable' => True),
				'required_skill' => array('type' => 'int','precision' => '4','nullable' => True)
			),
			'pk' => array('quali_id','job_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_hrm_training' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'title' => array('type' => 'varchar','precision' => '100','nullable' => True),
				'skill' => array('type' => 'int','precision' => '4','nullable' => True),
				'category' => array('type' => 'int','precision' => '4','nullable' => True),
				'start_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'end_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'place_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'reference' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'descr' => array('type' => 'text','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'owner' => array('type' => 'int','precision' => '4','nullable' => True),
				'access' => array('type' => 'varchar','precision' => '7','nullable' => True),
				'credits' => array('type' => 'int','precision' => '4','nullable' => True),
			),
			'pk' => array('id','user_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_hrm_training_category' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '40','nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_hrm_training_place' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'name' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'address' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'zip' => array('type' => 'int','precision' => '4','nullable' => True),
				'town' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'remark' => array('type' => 'text','nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_hrm_training_quali' => array(
			'fd' => array(
				'training_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'quali_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'relevance' => array('type' => 'int','precision' => '4','nullable' => True),
			),
			'pk' => array('training_id','quali_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_hrm_experience_category' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '40','nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_hrm_skill_level' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '40','nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_hrm_quali_category' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '40','nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
