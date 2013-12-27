<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage custom
 	* @version $Id$
	*/

	/**
	 * Description
	 * usage:
	 * @package property
	 */

	include_class('property', 'cron_parent', 'inc/cron/');

	class update_phpgw extends property_cron_parent
	{
		function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location = lang('Async service');
			$this->function_msg	= 'Update all installed apps of phpgw';

			$this->bocommon		= CreateObject('property.bocommon');
		}

		function execute()
		{
			$this->perform_update_db();
		}

		function perform_update_db()
		{
			$GLOBALS['phpgw_setup'] = CreateObject('phpgwapi.setup', true, true);
			$setup_info = $GLOBALS['phpgw_setup']->detection->get_versions();
			$GLOBALS['phpgw_setup']->db = CreateObject('phpgwapi.db');
			$GLOBALS['phpgw_info']['setup']['stage']['db'] = $GLOBALS['phpgw_setup']->detection->check_db();
			$setup_info = $GLOBALS['phpgw_setup']->detection->get_db_versions($setup_info);
			$setup_info = $GLOBALS['phpgw_setup']->detection->compare_versions($setup_info);
			$setup_info = $GLOBALS['phpgw_setup']->detection->check_depends($setup_info);
			ksort($setup_info);
			$clear_cache = '';
			foreach($setup_info as $app => $appinfo)
			{
				if(isset($appinfo['status']) && $appinfo['status']=='U' && isset($appinfo['currentver']) && $appinfo['currentver'])
				{
					$terror = array();
					$terror[] = $setup_info[$appinfo['name']];
					$GLOBALS['phpgw_setup']->process->upgrade($terror,false);
					$GLOBALS['phpgw_setup']->process->upgrade_langs($terror,false);
					$this->receipt['message'][]=array('msg'=> 'Upgraded application: ' . $appinfo['name']);
					if($appinfo['name']=='property')
					{
						$clear_cache = true;
					}
				}
			}
			if($clear_cache)
			{
				$this->db->query('DELETE FROM fm_cache');
			}
		}
	}
