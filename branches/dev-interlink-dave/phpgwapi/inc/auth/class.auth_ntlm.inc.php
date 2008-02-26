<?php
	/**
	* Authentication based on ntlm auth
	* @author Philipp Kamps <pkamps@probusiness.de>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage accounts
	* @version $Id$
	*/

	/**
	* Authentication based on ntlm auth
	*
	* @package phpgwapi
	* @subpackage accounts
	* @ignore
	*/
	class phpgwapi_auth_ntlm extends phpgwapi_auth_remote_user
	{

		public function __construct()
		{
			parent::__construct();
		}

		function change_password($old_passwd, $new_passwd)
		{
			// not yet supported - this script would change the windows domain password
			return '';
		}

	}
