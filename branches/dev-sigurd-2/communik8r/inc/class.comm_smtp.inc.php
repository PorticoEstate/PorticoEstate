<?php
	/**
	* phpGroupWare - phpmailer wrapper script
	* @author Dave Hall - skwashd at phpgroupware.org
	* @copyright Copyright (C) 2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @version $Id: class.comm_smtp.inc.php,v 1.1.1.1 2005/08/23 05:03:52 skwashd Exp $
	*/

	/**
	*@see phpmailer
	*/
	include_once(PHPGW_APP_INC . '/phpmailer/class.phpmailer.php');
	
	/**
	* class smtp
	*
	* bo class for assembling messages for sending via class send
	* @internal server side attachment storage technique borrowed from Squirrelmail
	*/
	class comm_smtp extends PHPMailer
	{
		/**
		* @constructor
		*/
		function comm_smtp()
		{
			$this->IsSMTP(true);
			$this->Host = $GLOBALS['phpgw_info']['server']['smtp_server'];
			$this->Port = $GLOBALS['phpgw_info']['server']['smtp_port'];
			$this->Version = 'custom - phpGroupWare 1.72';
		}
	}
?>
