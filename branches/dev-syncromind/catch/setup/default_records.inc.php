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

	/**
	 * Description
	 * @package catch
	 */


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
//$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant, allow_c_attrib,c_attrib_table) VALUES ({$app_id}, '.catch.2.1', 'Shema type 1', 1, 1, 'fm_catch_2_1')");

$location_id = $GLOBALS['phpgw']->locations->get_id('catch', '.catch.1');
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_catch (location_id, id, name, descr) VALUES ({$location_id}, 1, 'Users and devices', 'Users and devices')");
$location_id = $GLOBALS['phpgw']->locations->get_id('catch', '.catch.2');
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_catch (location_id, id, name, descr) VALUES ({$location_id}, 2, 'Shema type 1', 'Shema type 1')");

$location_id = $GLOBALS['phpgw']->locations->get_id('catch', '.catch.1.1');
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_catch_category (location_id, entity_id, id, name, descr) VALUES ({$location_id}, 1, 1, 'Users and devices', 'Users and devices')");

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 1, 'unitid', 'UnitID', 'UnitID for device', 'V', 1, 1, NULL, 50, NULL, NULL, 'False')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_cust_attribute (location_id, id, column_name, input_text, statustext, datatype, list, attrib_sort, size, precision_, scale, default_value, nullable) VALUES ($location_id, 2, 'user_', 'User', 'System user', 'user', 1, 2, NULL, NULL, NULL, NULL, 'False')");
