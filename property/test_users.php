<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
 	* @version $Id$
	*/

	/**
	 * Test script for SSH2
	 *
	 */

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'currentapp'	=> 'property'
	);

	$GLOBALS['phpgw_info']['flags']['noheader'] = true;
	$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
	$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

	include_once('../header.inc.php');

	$users = array(
	'bertol',
	'karhal',
	'alsbja',
	'karsve',
	'wilkar',
	'svegul',
	'frejul',
	'tomgun',
	'ronsor',
	'ape009',
	'bertol',
	'larott',
	'karsve',
	'wilkar',
	'tomgun',
	'bertol',
	'karhal',
	'alsbja',
	'olejoh',
	'risole',
	'sleter',
	'bertol',
	'larott',
	'olejoh',
	'risole',
	'sleter',
	'bertol',
	'karhal',
	'alsbja',
	'geijor',
	'ronbech',
	'oyvfor',
	'gormyr',
	'kimhau',
	'bertol',
	'karhal',
	'alsbja',
	'geijor',
	'ronbech',
	'rohar',
	'kle2',
	'bertol',
	'karhal',
	'alsbja',
	'geijor',
	'ronbech',
	'svenyg',
	'mat2',
	'bertol',
	'larott',
	'larott',
	'geijor',
	'harjoh',
	'olawes',
	'bertol',
	'larott',
	'larott',
	'geijor',
	'harjoh',
	'olawes',
	'bertol',
	'larott',
	'larott',
	'geijor',
	'harjoh',
	'olawes',
	'bertol',
	'larott',
	'larott',
	'geijor',
	'harjoh',
	'olawes',
	'bertol',
	'larott',
	'larott',
	'geijor',
	'harjoh',
	'olawes',
	'bertol',
	'larott',
	'larhan',
	'lennil',
	'kriosn',
	'geijor',
	'harjoh',
	'olawes',
	'perjak',
	'bertol',
	'larott',
	'larott',
	'geijor',
	'harjoh',
	'olawes',
	'bertol',
	'larott',
	'larott',
	'geijor',
	'harjoh',
	'olawes',
	'bertol',
	'larott',
	'larott',
	'geijor',
	'harjoh',
	'olawes',
	'bertol',
	'kriosn',
	'bra001',
	'bertol',
	'karhal',
	'alsbja',
	'harjoh',
	'oms',
	'jsda',
	'sva001',
	'rujo',
	'andmat',
	'bertol',
	'karhal',
	'alsbja',
	'harjoh',
	'oms',
	'jsda',
	'sva001',
	'rujo',
	'andmat',
	'bertol',
	'karhal',
	'alsbja',
	'olawes',
	'stuarn',
	'areant',
	'signib',
	'thosto',
	'bjøand',
	'stastr',
	'oddmar',
	'paltre',
	'inkr',
	'Asel',
	'tjo019',
	'bertol',
	'karhal',
	'alsbja',
	'olawes',
	'stuarn',
	'areant',
	'signib',
	'thosto',
	'bjøand',
	'stastr',
	'oddmar',
	'paltre',
	'inkr',
	'Asel',
	'tjo019',
	'bertol',
	'larhan',
	'larhan',
	'perjak',
	'lennil',
	'kriosn',
	'geijor',
	'harjoh',
	'olawes'
	);

	foreach ($users as $user_lid)
	{
		$GLOBALS['phpgw']->db->query("select account_id from phpgw_accounts where account_lid = '{$user_lid}'");
		$GLOBALS['phpgw']->db->next_record();
		if(!$GLOBALS['phpgw']->db->f('account_id'))
		{
			_debug_array($user_lid);
		}
	}
	$GLOBALS['phpgw']->common->phpgw_exit();
