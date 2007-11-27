<?php
	/**
	* phpGroupWare - sms
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage core
 	* @version $Id: hook_settings.inc.php 16804 2006-06-17 12:20:12Z sigurdne $
	*/

	create_input_box('Your Cellphone','cellphone','help text','',15);
	create_input_box('Your signature','signature','Signature to be appended to your sms-messages','',15);
