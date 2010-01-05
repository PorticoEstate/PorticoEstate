<?php
/**
 * SMART Plugin Config File
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Plugin_SMART
 * @author    Antoine Bertin <diaoulael@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id$
 * @link      http://phpsysinfo.sourceforge.net
 */

/**
 * Smartctl program
 * If the smartctl program is available we can read S.M.A.R.T informations
 * - define('PSI_PLUGIN_SMART_ACCESS', 'command');  // read data from smartctl program
 */
define('PSI_PLUGIN_SMART_ACCESS', 'command');

/**
 * Smartctl devices to monitor
 * If the smartctl support is enabled, those disks information will be displayed
 * - define('PSI_PLUGIN_SMART_DEVICES', '/dev/hda,/dev/hdb');  // Will display those two disks informations
 */
define('PSI_PLUGIN_SMART_DEVICES', '/dev/sda, /dev/sdb');

/**
 * Smartctl --device option value
 * If the smartctl support is enabled, enter the --device option value for smartctl command
 * - define('PSI_PLUGIN_SMART_DEVICE', false);  // If this option is not needed
 * - define('PSI_PLUGIN_SMART_DEVICE', 'marvell');  // If marvell
 */
define('PSI_PLUGIN_SMART_DEVICE', false);

/**
 * Smartctl ID# and column name from "Vendor Specific SMART Attributes with Thresholds" table
 * If the smartctl support is enabled, enter the ID#-COLUMN_NAME from "Vendor Specific SMART Attributes with Thresholds" table from smartctl output.
 * COLUMN_NAME of this ID# will be displayed in the phpsysinfo S.M.A.R.T table. If you want RAW_VALUE to be displayed for the temperature (ID# 194) enter 194-RAW_VALUE
 * - define('PSI_PLUGIN_SMART_IDS', '194-VALUE,4-VALUE,009-RAW_VALUE'); // ID#-COLUMN_NAME, ID#-COLUMN_NAME, etc...
 */
define('PSI_PLUGIN_SMART_IDS', '194-RAW_VALUE,4-RAW_VALUE,009-RAW_VALUE,012-RAW_VALUE,193-RAW_VALUE,001-RAW_VALUE,007-RAW_VALUE,200-RAW_VALUE');
?>
