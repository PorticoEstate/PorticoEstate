<?php 

// phpSysInfo - A PHP System Information Script
// http://phpsysinfo.sourceforge.net/

// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

// $Id: config.php 16243 2005-11-19 06:02:29Z skwashd $

// define the default lng and template here
$default_lng='en';
$default_template='classic';

// hide language and template picklist
// false = display picklist
// true = do not display picklist
$hide_picklist = false;

// define the motherboard monitoring program here
// we support four programs so far
// 1. lmsensors  http://www2.lm-sensors.nu/~lm78/
// 2. healthd    http://healthd.thehousleys.net/
// 3. hwsensors  http://www.openbsd.org/
// 4. mbmon      http://www.nt.phys.kyushu-u.ac.jp/shimizu/download/download.html
// 5. mbm5       http://mbm.livewiredev.com/

// $sensor_program = "lmsensors";
// $sensor_program = "healthd";
// $sensor_program = "hwsensors";
// $sensor_program = "mbmon";
// $sensor_program = "mbm5";
$sensor_program = "";

// show mount point
// true = show mount point
// false = do not show mount point
$show_mount_point = true;

/***************** DO NOT EDIT BELOW THIS POINT ! Please :) *****************/

if ( PHPGROUPWARE ) {
  if ( !isset($GLOBALS['phpgw']->config) || !is_object($GLOBALS['phpgw']->config) ) {
  	$GLOBALS['phpgw']->config = createObject('phpgwapi.config', 'phpsysinfo');
  }
  $GLOBALS['phpgw']->config->appname = 'phpsysinfo';
  $GLOBALS['phpgw']->config->read_repository();
  $default_template =& $GLOBALS['phpgw']->config->config_data['theme'];
  
  $default_lng = strtolower(substr($GLOBALS['phpgw_info']['user']['preferences']['common']['lang'], 0, 2));
  $hide_picklist = true;
}

?>
