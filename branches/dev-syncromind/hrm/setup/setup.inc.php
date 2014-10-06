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

	$setup_info['hrm']['name']      = 'hrm';
	$setup_info['hrm']['version']   = '0.9.17.006';
	$setup_info['hrm']['app_order'] = 20;
	$setup_info['hrm']['enable']    = 1;
	$setup_info['hrm']['globals_checked']    = True;
	$setup_info['hrm']['app_group']	= 'office';

	$setup_info['hrm']['author'] = array
	(
		'name'	=> 'Sigurd Nes',
		'email'	=> 'sigurdne@online.no'
	);

	$setup_info['hrm']['maintainer'] = array
	(
		'name'	=> 'Sigurd Nes',
		'email'	=> 'sigurdne@online.no'
	);

	$setup_info['hrm']['license']  = 'GPL';
	$setup_info['hrm']['description'] =
	'<div align="left">
		<b>HRM</b> human resource competence management system:
		<ol>
			<li>List of education and courses per user.</li>
				<ol>
					<li>The user will maintain the information - which possibly (as option) needs approval from a moderator per record.</li>
				</ol>
			<li>List of type of experience pr categories and period and a rating of skills per user.</li>
			<li>Ability to generate a standardized CV as PDF and Excel (also good for openoffice)</li>
			<li>Job-requirements profile - and matching against relevant users to reveal need for training/education.</li>
			<li>Users wish list for training education.</li>
		</ol>
	</div>';

	$setup_info['hrm']['note'] =
		'Training item is rated and linked to qualifications with degree of relevance. The qualification is rated for importance and grouped into job-descriptions (job-type) - which is linked to an organization layout (hierarchy). <br>
		Training items is categorized as education, courses or work experiences';

	$setup_info['hrm']['tables'] = array(
		'phpgw_hrm_org',
		'phpgw_hrm_job',
		'phpgw_hrm_task',
		'phpgw_hrm_quali',
		'phpgw_hrm_quali_type',
		'phpgw_hrm_quali_job',
		'phpgw_hrm_training',
		'phpgw_hrm_training_category',
		'phpgw_hrm_training_place',
		'phpgw_hrm_training_quali',
		'phpgw_hrm_experience_category',
		'phpgw_hrm_skill_level',
		'phpgw_hrm_quali_category'
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['hrm']['hooks'] = array
	(
//		'add_def_pref',
		'manual',
		'settings',
		'preferences',
		'help',
		'menu'	=> 'hrm.menu.get_menu'
	);

	/* Dependencies for this app to work */
	$setup_info['hrm']['depends'][] = array
	(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.15', '0.9.16', '0.9.17', '0.9.18')
	);

	$setup_info['hrm']['depends'][] = array(
		'appname'  => 'admin',
		'versions' => Array('0.9.13', '0.9.14', '0.9.15', '0.9.16', '0.9.17', '0.9.18')
	);

	$setup_info['hrm']['depends'][] = array(
		'appname'  => 'preferences',
		'versions' => Array('0.9.13', '0.9.14', '0.9.15', '0.9.16', '0.9.17', '0.9.18')
	);

