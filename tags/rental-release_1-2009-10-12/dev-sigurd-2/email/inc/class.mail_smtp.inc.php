<?php
	/**
	* EMail - phpmailer wrapper script
	* 
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) 2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @version $Id: class.mail_smtp.inc.php 17716 2006-12-18 11:02:13Z sigurdne $
	*/

	/**
	* Include phpmailer
	* @see phpmailer
	*/
	include_once(PHPGW_APP_INC . '/phpmailer/class.phpmailer.php');
	
	/**
	* class smtp
	*
	* bo class for assembling messages for sending via class send
	* @package email
	* @internal Server side attachment storage technique borrowed from Squirrelmail
	*/
	class mail_smtp extends PHPMailer
	{
		/**
		* Constructor
		*/
		function mail_smtp()
		{
			$this->IsSMTP(true);
			$this->Host = $GLOBALS['phpgw_info']['server']['smtp_server'];
			$this->Port = (isset($GLOBALS['phpgw_info']['server']['smtp_port'])?$GLOBALS['phpgw_info']['server']['smtp_port']:'');
			$this->Version = 'custom - phpGroupWare 1.72';
		}
	}
?>
