<?php
	/**
	* phpGroupWare - phpmailer wrapper script
	* @author Dave Hall - skwashd at phpgroupware.org
	* @copyright Copyright (C) 2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage communication
	* @version $Id$
	*/

	/**
	* @see phpmailer
	*/
	require_once PHPGW_INCLUDE_ROOT . '/phpgwapi/inc/phpmailer/class.phpmailer.php';
	
	/**
	* Send email messages via SMTP
	*
	* @internal this is really just a phpgw friendly wrapper for phpmailer
	* @package phpgwapi
	* @subpackage communication
	*/
	class phpgwapi_mailer_smtp extends PHPMailer
	{
		/**
		* Constructor
		*/
		public function __construct()
		{
			parent::__construct(true); // enable exceptions
			$this->IsSMTP(true);
			$this->PluginDir = PHPGW_INCLUDE_ROOT . '/phpgwapi/inc/phpmailer/';
			$this->Host = $GLOBALS['phpgw_info']['server']['smtp_server'];
			$this->Port = isset($GLOBALS['phpgw_info']['server']['smtp_port']) ? $GLOBALS['phpgw_info']['server']['smtp_port'] : 25;
			$this->SMTPSecure = isset($GLOBALS['phpgw_info']['server']['smtpSecure']) ? $GLOBALS['phpgw_info']['server']['smtpSecure'] : '';
			$this->Version = 'custom - phpGroupWare 1.73';
			$this->CharSet = 'utf-8';
 
			if ( isset($GLOBALS['phpgw_info']['server']['smtpAuth']) && $GLOBALS['phpgw_info']['server']['smtpAuth'] == 'yes' )
			{
				$this->SMTPAuth	= true;
				$this->Username = isset($GLOBALS['phpgw_info']['server']['smtpUser']) ? $GLOBALS['phpgw_info']['server']['smtpUser'] : '';
				$this->Password =  isset($GLOBALS['phpgw_info']['server']['smtpPassword']) ? $GLOBALS['phpgw_info']['server']['smtpPassword'] : '';
			}
			
		    /**
		     * SMTP class debug output mode.
		     * Options: 0 = off, 1 = commands, 2 = commands and data
		     * @type int
		     */

			if ( isset($GLOBALS['phpgw_info']['server']['SMTPDebug']) )
			{
			    $this->SMTPDebug = (int)$GLOBALS['phpgw_info']['server']['SMTPDebug'];
			}

		    /**
    		 * The function/method to use for debugging output.
     		 * Options: 'echo', 'html' or 'error_log'
    		 * @type string
    		 * @see SMTP::$Debugoutput
    		 */

			if ( isset($GLOBALS['phpgw_info']['server']['Debugoutput']) && $GLOBALS['phpgw_info']['server']['smtpAuth'] != 'echo' )
			{
   				switch ($GLOBALS['phpgw_info']['server']['Debugoutput'])
   				{
   					case 'html':
	    				$this->Debugoutput =  'html';
	    				break;
   					case 'errorlog':
	    				$this->Debugoutput =  'error_log';
	    				break;
	    			default:
   				}
   			}
		}
	}
