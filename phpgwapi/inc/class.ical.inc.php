<?php
	/**
	* phpGroupWare
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2021 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/
	* @package phpgwapi
	* @subpackage utilities
 	* @version $Id$
	*
	*/


	require_once PHPGW_API_INC . '/icalcreator/autoload.php';
	use Kigkonsult\Icalcreator\Vcalendar;

	/**
	* Document me!
	*
	* @package phpgwapi
	* @subpackage utilities
	*/
	class phpgwapi_ical
	{
		public $vcalendar;

		public function __construct($cal_name = "", $cal_desc =  "")
		{
			$timezone	 = !empty($GLOBALS['phpgw_info']['user']['preferences']['common']['timezone']) ? $GLOBALS['phpgw_info']['user']['preferences']['common']['timezone'] : 'UTC';
			$unique_id	 = !empty($GLOBALS['phpgw_info']['server']['site_title']) ? $GLOBALS['phpgw_info']['server']['site_title'] : $GLOBALS['phpgw_info']['server']['system_name'];

			$this->vcalendar = Vcalendar::factory([Vcalendar::UNIQUE_ID => $unique_id,])
				// with calendaring info
				->setMethod(Vcalendar::PUBLISH)
				->setXprop(
				Vcalendar::X_WR_TIMEZONE,
				$timezone
			);
			if($cal_name)
			{
				$this->vcalendar->setXprop(Vcalendar::X_WR_CALNAME, $cal_name);
			}
			if($cal_desc)
			{
				$this->vcalendar->setXprop(Vcalendar::X_WR_CALDESC, $cal_desc);
			}
		}
	}
