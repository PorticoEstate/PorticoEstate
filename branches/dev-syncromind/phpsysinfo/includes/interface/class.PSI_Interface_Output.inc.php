<?php 
/**
 * Basic Output Functions
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Interfaces
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id$
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * define which methods a output class for phpsysinfo must implement
 * to be recognized and fully work without errors
 *
 * @category  PHP
 * @package   PSI_Interfaces
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
interface PSI_Interface_Output
{
    /**
     * generate the output
     *
     * @return void
     */
    public function run();
}
