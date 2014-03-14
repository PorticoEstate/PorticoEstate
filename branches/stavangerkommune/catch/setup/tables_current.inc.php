<?php
	/**
	* phpGroupWare - CATCH: An application for importing data from handhelds into property.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2009 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package catch
	* @subpackage catch
 	* @version $Id$
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	$phpgw_baseline = array(
		'fm_catch' => array(
			'fd' => array(
				'location_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'name' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'location_form' => array('type' => 'int','precision' => '4','nullable' => True),
				'documentation' => array('type' => 'int','precision' => '4','nullable' => True),
				'lookup_entity' => array('type' => 'text','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_catch_category' => array(
			'fd' => array(
				'location_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'entity_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'name' => array('type' => 'varchar','precision' => '100','nullable' => True),
				'descr' => array('type' => 'text','nullable' => True),
				'prefix' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'lookup_tenant' => array('type' => 'int','precision' => '4','nullable' => True),
				'tracking' => array('type' => 'int','precision' => '4','nullable' => True),
				'location_level' => array('type' => 'int','precision' => '4','nullable' => True),
				'location_link_level' => array('type' => 'int','precision' => '4','nullable' => True),
				'fileupload' => array('type' => 'int','precision' => '4','nullable' => True),
				'loc_link' => array('type' => 'int','precision' => '4','nullable' => True),
				'start_project' => array('type' => 'int','precision' => '4','nullable' => True),
				'start_ticket' => array('type' => 'int','precision' => '2','nullable' => True),
				'is_eav' => array('type' => 'int','precision' => '2','nullable' => True),
				'enable_bulk' => array('type' => 'int','precision' => '2','nullable' => True),
				'jasperupload' => array('type' => 'int','precision' => 2,'nullable' => True),
				'parent_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'level' => array('type' => 'int','precision' => '4','nullable' => True),
			),
			'pk' => array('entity_id','id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_catch_lookup' => array(
			'fd' => array(
				'entity_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'location' => array('type' => 'varchar','precision' => '15','nullable' => False),
				'type' => array('type' => 'varchar','precision' => '15','nullable' => False)
			),
			'pk' => array('entity_id','location','type'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_catch_history' => array(
			'fd' => array(
				'history_id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'history_record_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'history_appname' => array('type' => 'varchar','precision' => '64','nullable' => False),
				'history_attrib_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'history_owner' => array('type' => 'int','precision' => '4','nullable' => False),
				'history_status' => array('type' => 'char','precision' => '2','nullable' => False),
				'history_new_value' => array('type' => 'text','nullable' => False),
				'history_old_value' => array('type' => 'text','nullable' => true),
				'history_timestamp' => array('type' => 'timestamp','nullable' => False,'default' => 'current_timestamp')
			),
			'pk' => array('history_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_catch_config_type' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => 4,'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 50,'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => 200,'nullable' => true),
				'schema_' => array('type' => 'varchar', 'precision' => 10,'nullable' => false)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_catch_config_attrib' => array(
			'fd' => array(
				'type_id' => array('type' => 'int','precision' => 4,'nullable' => False),
				'id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'input_type' => array('type' => 'varchar', 'precision' => 10,'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 50,'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => 200,'nullable' => true),
				'value' => array('type' => 'varchar', 'precision' => 1000,'nullable' => true)
			),
			'pk' => array('type_id','id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_catch_config_choice' => array(
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
		),
		'fm_catch_1_1' => array(
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
